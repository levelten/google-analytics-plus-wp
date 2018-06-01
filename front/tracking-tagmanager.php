<?php
/**
 * Author: Alin Marcu
 * Author URI: https://deconf.com
 * Copyright 2017 Alin Marcu
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit();

if ( ! class_exists( 'GAPWP_Tracking_TagManager' ) ) {

	class GAPWP_Tracking_TagManager {

		private $gapwp;

		private $datalayer;

		private $uaid;

		public function __construct() {
			$this->gapwp = GAPWP();

			$profile = GAPWP_Tools::get_selected_profile( $this->gapwp->config->options['ga_profiles_list'], $this->gapwp->config->options['tableid_jail'] );

			$this->uaid = esc_html( $profile[2] );

			if ( $this->gapwp->config->options['trackingcode_infooter'] ) {
				add_action( 'wp_footer', array( $this, 'output' ), 99 );
			} else {
				add_action( 'wp_head', array( $this, 'output' ), 99 );
			}

			if ( $this->gapwp->config->options['amp_tracking_tagmanager'] && $this->gapwp->config->options['amp_containerid'] ) {
				add_filter( 'amp_post_template_data', array( $this, 'amp_add_analytics_script' ) );
				add_action( 'amp_post_template_footer', array( $this, 'amp_output' ) );
			}
		}

		/**
		 * Retrieves the datalayer variables
		 */
		public function get() {
			return $this->datalayer;
		}

		/**
		 * Stores the datalayer variables
		 * @param array $datalayer
		 */
		public function set( $datalayer ) {
			$this->datalayer = $datalayer;
		}

		/**
		 * Adds a variable to the datalayer
		 * @param string $name
		 * @param string $value
		 */
		private function add_var( $name, $value ) {
			$this->datalayer[$name] = $value;
		}

		/**
		 * Builds the datalayer based on user's options
		 */
		private function build_custom_dimensions() {
			global $post;

			if ( $this->gapwp->config->options['tm_author_var'] && ( is_single() || is_page() ) ) {
				global $post;
				$author_id = $post->post_author;
				$author_name = get_the_author_meta( 'display_name', $author_id );
				$this->add_var( 'gapwpAuthor', esc_attr( $author_name ) );
			}

			if ( $this->gapwp->config->options['tm_pubyear_var'] && is_single() ) {
				global $post;
				$date = get_the_date( 'Y', $post->ID );
				$this->add_var( 'gapwpPublicationYear', (int) $date );
			}

			if ( $this->gapwp->config->options['tm_pubyearmonth_var'] && is_single() ) {
				global $post;
				$date = get_the_date( 'Y-m', $post->ID );
				$this->add_var( 'gapwpPublicationYearMonth', esc_attr( $date ) );
			}

			if ( $this->gapwp->config->options['tm_category_var'] && is_category() ) {
				$this->add_var( 'gapwpCategory', esc_attr( single_tag_title( '', false ) ) );
			}
			if ( $this->gapwp->config->options['tm_category_var'] && is_single() ) {
				global $post;
				$categories = get_the_category( $post->ID );
				foreach ( $categories as $category ) {
					$this->add_var( 'gapwpCategory', esc_attr( $category->name ) );
					break;
				}
			}

			if ( $this->gapwp->config->options['tm_tag_var'] && is_single() ) {
				global $post;
				$post_tags_list = '';
				$post_tags_array = get_the_tags( $post->ID );
				if ( $post_tags_array ) {
					foreach ( $post_tags_array as $tag ) {
						$post_tags_list .= $tag->name . ', ';
					}
				}
				$post_tags_list = rtrim( $post_tags_list, ', ' );
				if ( $post_tags_list ) {
					$this->add_var( 'gapwpTag', esc_attr( $post_tags_list ) );
				}
			}

			if ( $this->gapwp->config->options['tm_user_var'] ) {
				$usertype = is_user_logged_in() ? 'registered' : 'guest';
				$this->add_var( 'gapwpUser', $usertype );
			}

			do_action( 'gapwp_tagmanager_datalayer', $this );
		}

		/**
		 * Outputs the Google Tag Manager tracking code
		 */
		public function output() {
			$this->build_custom_dimensions();

			if ( is_array( $this->datalayer ) ) {
				$vars = "{";
				foreach ( $this->datalayer as $var => $value ) {
					$vars .= "'" . $var . "': '" . $value . "', ";
				}
				$vars = rtrim( $vars, ", " );
				$vars .= "}";
			} else {
				$vars = "{}";
			}

			if ( ( $this->gapwp->config->options['tm_optout'] || $this->gapwp->config->options['tm_dnt_optout'] ) && ! empty( $this->uaid ) ) {
				GAPWP_Tools::load_view( 'front/views/analytics-optout-code.php', array( 'uaid' => $this->uaid, 'gaDntOptout' => $this->gapwp->config->options['tm_dnt_optout'], 'gaOptout' => $this->gapwp->config->options['tm_optout'] ) );
			}

			GAPWP_Tools::load_view( 'front/views/tagmanager-code.php', array( 'containerid' => $this->gapwp->config->options['web_containerid'], 'vars' => $vars ) );
		}

		/**
		 * Inserts the Analytics AMP script in the head section
		 */
		public function amp_add_analytics_script( $data ) {
			if ( ! isset( $data['amp_component_scripts'] ) ) {
				$data['amp_component_scripts'] = array();
			}

			$data['amp_component_scripts']['amp-analytics'] = 'https://cdn.ampproject.org/v0/amp-analytics-0.1.js';

			return $data;
		}

		/**
		 * Outputs the Tag Manager code for AMP
		 */
		public function amp_output() {
			$this->build_custom_dimensions();

			$vars = array( 'vars' => $this->datalayer );

			if ( version_compare( phpversion(), '5.4.0', '<' ) ) {
				$json = json_encode( $vars );
			} else {
				$json = json_encode( $vars, JSON_PRETTY_PRINT );
			}

			$amp_containerid = $this->gapwp->config->options['amp_containerid'];

			$json = str_replace( array( '"&#91;', '&#93;"' ), array( '[', ']' ), $json ); // make verticalBoundaries a JavaScript array

			GAPWP_Tools::load_view( 'front/views/tagmanager-amp-code.php', array( 'json' => $json, 'containerid' => $amp_containerid ) );
		}
	}
}
