<?php
/**
 * Copyright 2017 Alin Marcu
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit();

if ( ! class_exists( 'GAPWP_Tracking' ) ) {

	class GAPWP_Tracking {

		private $gapwp;

		public $analytics;

		public $analytics_amp;

		public $tagmanager;

		public function __construct() {
			$this->gapwp = GAPWP();

			$this->init();
		}

		public function tracking_code() { // Removed since 5.0
			GAPWP_Tools::doing_it_wrong( __METHOD__, __( "This method is deprecated, read the documentation!", 'google-analytics-plus-wp' ), '5.0' );
		}

		public static function gapwp_user_optout( $atts, $content = "" ) {
			if ( ! isset( $atts['html_tag'] ) ) {
				$atts['html_tag'] = 'a';
			}
			if ( 'a' == $atts['html_tag'] ) {
				return '<a href="#" class="gapwp_useroptout" onclick="gaOptout()">' . esc_html( $content ) . '</a>';
			} else if ( 'button' == $atts['html_tag'] ) {
				return '<button class="gapwp_useroptout" onclick="gaOptout()">' . esc_html( $content ) . '</button>';
			}
		}

		public function init() {
			// excluded roles
			if ( GAPWP_Tools::check_roles( $this->gapwp->config->options['track_exclude'], true ) || ( $this->gapwp->config->options['superadmin_tracking'] && current_user_can( 'manage_network' ) ) ) {
				return;
			}

			if ( 'universal' == $this->gapwp->config->options['tracking_type'] && ($this->gapwp->config->options['tableid_jail'] || $this->gapwp->config->options['tracking_id']) ) {

				// Analytics
				require_once 'tracking-analytics.php';

				if ( 1 == $this->gapwp->config->options['ga_with_gtag'] ) {
					$this->analytics = new GAPWP_Tracking_GlobalSiteTag();
				} else {
					$this->analytics = new GAPWP_Tracking_Analytics();
				}

				if ( $this->gapwp->config->options['amp_tracking_analytics'] ) {
					$this->analytics_amp = new GAPWP_Tracking_Analytics_AMP();
				}
			}

			if ( 'tagmanager' == $this->gapwp->config->options['tracking_type'] && $this->gapwp->config->options['web_containerid'] ) {

				// Tag Manager
				require_once 'tracking-tagmanager.php';
				$this->tagmanager = new GAPWP_Tracking_TagManager();
			}

			add_shortcode( 'gapwp_useroptout', array( $this, 'gapwp_user_optout' ) );
		}
	}
}
