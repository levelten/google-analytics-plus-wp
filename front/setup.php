<?php
/**
 * Author: Alin Marcu
 * Author URI: https://deconf.com
 * Copyright 2013 Alin Marcu
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit();

if ( ! class_exists( 'OGADWP_Frontend_Setup' ) ) {

	final class OGADWP_Frontend_Setup {

		private $ogadwp;

		public function __construct() {
			$this->ogadwp = OGADWP();

			// Styles & Scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'load_styles_scripts' ) );
		}

		/**
		 * Styles & Scripts conditional loading
		 *
		 * @param
		 *            $hook
		 */
		public function load_styles_scripts() {
			$lang = get_bloginfo( 'language' );
			$lang = explode( '-', $lang );
			$lang = $lang[0];

			/*
			 * Item reports Styles & Scripts
			 */
			if ( OGADWP_Tools::check_roles( $this->ogadwp->config->options['access_front'] ) && $this->ogadwp->config->options['frontend_item_reports'] ) {

				wp_enqueue_style( 'ogadwp-nprogress', OGADWP_URL . 'common/nprogress/nprogress.css', null, OGADWP_CURRENT_VERSION );

				wp_enqueue_style( 'ogadwp-frontend-item-reports', OGADWP_URL . 'front/css/item-reports.css', null, OGADWP_CURRENT_VERSION );

				$country_codes = OGADWP_Tools::get_countrycodes();
				if ( $this->ogadwp->config->options['ga_target_geomap'] && isset( $country_codes[$this->ogadwp->config->options['ga_target_geomap']] ) ) {
					$region = $this->ogadwp->config->options['ga_target_geomap'];
				} else {
					$region = false;
				}

				wp_enqueue_style( "wp-jquery-ui-dialog" );

				wp_register_script( 'googlecharts', 'https://www.gstatic.com/charts/loader.js', array(), null );

				wp_enqueue_script( 'ogadwp-nprogress', OGADWP_URL . 'common/nprogress/nprogress.js', array( 'jquery' ), OGADWP_CURRENT_VERSION );

				wp_enqueue_script( 'ogadwp-frontend-item-reports', OGADWP_URL . 'common/js/reports5.js', array( 'ogadwp-nprogress', 'googlecharts', 'jquery', 'jquery-ui-dialog' ), OGADWP_CURRENT_VERSION, true );

				/* @formatter:off */
				wp_localize_script( 'ogadwp-frontend-item-reports', 'ogadwpItemData', array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'security' => wp_create_nonce( 'ogadwp_frontend_item_reports' ),
					'dateList' => array(
						'today' => __( "Today", 'open-google-analytics-dashboard-for-wp' ),
						'yesterday' => __( "Yesterday", 'open-google-analytics-dashboard-for-wp' ),
						'7daysAgo' => sprintf( __( "Last %d Days", 'open-google-analytics-dashboard-for-wp' ), 7 ),
						'14daysAgo' => sprintf( __( "Last %d Days", 'open-google-analytics-dashboard-for-wp' ), 14 ),
						'30daysAgo' =>  sprintf( __( "Last %d Days", 'open-google-analytics-dashboard-for-wp' ), 30 ),
						'90daysAgo' =>  sprintf( __( "Last %d Days", 'open-google-analytics-dashboard-for-wp' ), 90 ),
						'365daysAgo' =>  sprintf( _n( "%s Year", "%s Years", 1, 'open-google-analytics-dashboard-for-wp' ), __('One', 'open-google-analytics-dashboard-for-wp') ),
						'1095daysAgo' =>  sprintf( _n( "%s Year", "%s Years", 3, 'open-google-analytics-dashboard-for-wp' ), __('Three', 'open-google-analytics-dashboard-for-wp') ),
					),
					'reportList' => array(
						'uniquePageviews' => __( "Unique Views", 'open-google-analytics-dashboard-for-wp' ),
						'users' => __( "Users", 'open-google-analytics-dashboard-for-wp' ),
						'organicSearches' => __( "Organic", 'open-google-analytics-dashboard-for-wp' ),
						'pageviews' => __( "Page Views", 'open-google-analytics-dashboard-for-wp' ),
						'visitBounceRate' => __( "Bounce Rate", 'open-google-analytics-dashboard-for-wp' ),
						'locations' => __( "Location", 'open-google-analytics-dashboard-for-wp' ),
						'referrers' => __( "Referrers", 'open-google-analytics-dashboard-for-wp' ),
						'searches' => __( "Searches", 'open-google-analytics-dashboard-for-wp' ),
						'trafficdetails' => __( "Traffic", 'open-google-analytics-dashboard-for-wp' ),
						'technologydetails' => __( "Technology", 'open-google-analytics-dashboard-for-wp' ),
					),
					'i18n' => array(
							__( "A JavaScript Error is blocking plugin resources!", 'open-google-analytics-dashboard-for-wp' ), //0
							__( "Traffic Mediums", 'open-google-analytics-dashboard-for-wp' ),
							__( "Visitor Type", 'open-google-analytics-dashboard-for-wp' ),
							__( "Search Engines", 'open-google-analytics-dashboard-for-wp' ),
							__( "Social Networks", 'open-google-analytics-dashboard-for-wp' ),
							__( "Unique Views", 'open-google-analytics-dashboard-for-wp' ),
							__( "Users", 'open-google-analytics-dashboard-for-wp' ),
							__( "Page Views", 'open-google-analytics-dashboard-for-wp' ),
							__( "Bounce Rate", 'open-google-analytics-dashboard-for-wp' ),
							__( "Organic Search", 'open-google-analytics-dashboard-for-wp' ),
							__( "Pages/Session", 'open-google-analytics-dashboard-for-wp' ),
							__( "Invalid response", 'open-google-analytics-dashboard-for-wp' ),
							__( "No Data", 'open-google-analytics-dashboard-for-wp' ),
							__( "This report is unavailable", 'open-google-analytics-dashboard-for-wp' ),
							__( "report generated by", 'open-google-analytics-dashboard-for-wp' ), //14
							__( "This plugin needs an authorization:", 'open-google-analytics-dashboard-for-wp' ) . ' <strong>' . __( "authorize the plugin", 'open-google-analytics-dashboard-for-wp' ) . '</strong>!',
							__( "Browser", 'open-google-analytics-dashboard-for-wp' ), //16
							__( "Operating System", 'open-google-analytics-dashboard-for-wp' ),
							__( "Screen Resolution", 'open-google-analytics-dashboard-for-wp' ),
							__( "Mobile Brand", 'open-google-analytics-dashboard-for-wp' ),
							__( "Future Use", 'open-google-analytics-dashboard-for-wp' ),
							__( "Future Use", 'open-google-analytics-dashboard-for-wp' ),
							__( "Future Use", 'open-google-analytics-dashboard-for-wp' ),
							__( "Future Use", 'open-google-analytics-dashboard-for-wp' ),
							__( "Future Use", 'open-google-analytics-dashboard-for-wp' ),
							__( "Future Use", 'open-google-analytics-dashboard-for-wp' ), //25
							__( "Time on Page", 'open-google-analytics-dashboard-for-wp' ),
							__( "Page Load Time", 'open-google-analytics-dashboard-for-wp' ),
							__( "Exit Rate", 'open-google-analytics-dashboard-for-wp' ),
							__( "Precision: ", 'open-google-analytics-dashboard-for-wp' ), //29
					),
					'colorVariations' => OGADWP_Tools::variations( $this->ogadwp->config->options['theme_color'] ),
					'region' => $region,
					'mapsApiKey' => apply_filters( 'ogadwp_maps_api_key', $this->ogadwp->config->options['maps_api_key'] ),
					'language' => get_bloginfo( 'language' ),
					'filter' => $_SERVER["REQUEST_URI"],
					'viewList' => false,
					'scope' => 'front-item',
				 )
				);
				/* @formatter:on */
			}
		}
	}
}
