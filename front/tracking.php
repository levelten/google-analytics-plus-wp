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

if ( ! class_exists( 'OGADWP_Tracking' ) ) {

	class OGADWP_Tracking {

		private $ogadwp;

		public $analytics;

		public $analytics_amp;

		public $tagmanager;

		public function __construct() {
			$this->ogadwp = OGADWP();

			$this->init();
		}

		public function tracking_code() { // Removed since 5.0
			OGADWP_Tools::doing_it_wrong( __METHOD__, __( "This method is deprecated, read the documentation!", 'open-google-analytics-dashboard-for-wp' ), '5.0' );
		}

		public static function ogadwp_user_optout( $atts, $content = "" ) {
			if ( ! isset( $atts['html_tag'] ) ) {
				$atts['html_tag'] = 'a';
			}
			if ( 'a' == $atts['html_tag'] ) {
				return '<a href="#" class="ogadwp_useroptout" onclick="gaOptout()">' . esc_html( $content ) . '</a>';
			} else if ( 'button' == $atts['html_tag'] ) {
				return '<button class="ogadwp_useroptout" onclick="gaOptout()">' . esc_html( $content ) . '</button>';
			}
		}

		public function init() {
			// excluded roles
			if ( OGADWP_Tools::check_roles( $this->ogadwp->config->options['track_exclude'], true ) || ( $this->ogadwp->config->options['superadmin_tracking'] && current_user_can( 'manage_network' ) ) ) {
				return;
			}

			if ( 'universal' == $this->ogadwp->config->options['tracking_type'] && $this->ogadwp->config->options['tableid_jail'] ) {

				// Analytics
				require_once 'tracking-analytics.php';

				if ( 1 == $this->ogadwp->config->options['ga_with_gtag'] ) {
					$this->analytics = new OGADWP_Tracking_GlobalSiteTag();
				} else {
					$this->analytics = new OGADWP_Tracking_Analytics();
				}

				if ( $this->ogadwp->config->options['amp_tracking_analytics'] ) {
					$this->analytics_amp = new OGADWP_Tracking_Analytics_AMP();
				}
			}

			if ( 'tagmanager' == $this->ogadwp->config->options['tracking_type'] && $this->ogadwp->config->options['web_containerid'] ) {

				// Tag Manager
				require_once 'tracking-tagmanager.php';
				$this->tagmanager = new OGADWP_Tracking_TagManager();
			}

			add_shortcode( 'ogadwp_useroptout', array( $this, 'ogadwp_user_optout' ) );
		}
	}
}
