<?php
/**
 * Copyright 2013 Alin Marcu
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit();

if ( ! class_exists( 'GAPWP_Backend_Setup' ) ) {

	final class GAPWP_Backend_Setup {

		private $gapwp;

		public function __construct() {
			$this->gapwp = GAPWP();

			// Styles & Scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ) );
			// Site Menu
			add_action( 'admin_menu', array( $this, 'site_menu' ) );
			// Network Menu
			add_action( 'network_admin_menu', array( $this, 'network_menu' ) );
			// Settings link
			add_filter( "plugin_action_links_" . plugin_basename( GAPWP_DIR . 'gapwp.php' ), array( $this, 'settings_link' ) );
			// Updated admin notice
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		}

		/**
		 * Add Site Menu
		 */
		public function site_menu() {
			global $wp_version;
			if ( current_user_can( 'manage_options' ) ) {
				include ( GAPWP_DIR . 'admin/settings.php' );
				add_menu_page( __( "Google Analytics+", 'google-analytics-plus-wp' ), __( "Google Analytics+", 'google-analytics-plus-wp' ), 'manage_options', 'gapwp_settings', array( 'GAPWP_Settings', 'general_settings' ), version_compare( $wp_version, '3.8.0', '>=' ) ? 'dashicons-chart-area' : GAPWP_URL . 'admin/images/gapwp-icon.png' );
				add_submenu_page( 'gapwp_settings', __( "General Settings", 'google-analytics-plus-wp' ), __( "General Settings", 'google-analytics-plus-wp' ), 'manage_options', 'gapwp_settings', array( 'GAPWP_Settings', 'general_settings' ) );
				add_submenu_page( 'gapwp_settings', __( "Tracking Settings", 'google-analytics-plus-wp' ), __( "Tracking Settings", 'google-analytics-plus-wp' ), 'manage_options', 'gapwp_tracking_settings', array( 'GAPWP_Settings', 'tracking_settings' ) );
				add_submenu_page( 'gapwp_settings', __( "Reporting Settings", 'google-analytics-plus-wp' ), __( "Reporting Settings", 'google-analytics-plus-wp' ), 'manage_options', 'gapwp_report_settings', array( 'GAPWP_Settings', 'reporting_settings' ) );
				add_submenu_page( 'gapwp_settings', __( "Errors & Debug", 'google-analytics-plus-wp' ), __( "Errors & Debug", 'google-analytics-plus-wp' ), 'manage_options', 'gapwp_errors_debugging', array( 'GAPWP_Settings', 'errors_debugging' ) );
				/*
				add_submenu_page( 'gapwp_settings', __( "General Settings", 'google-analytics-plus-wp' ), __( "General Settings", 'google-analytics-plus-wp' ), 'manage_options', 'gapwp_settings', array( 'GAPWP_Settings', 'general_settings' ) );
				add_submenu_page( 'gapwp_settings', __( "Backend Settings", 'google-analytics-plus-wp' ), __( "Backend Settings", 'google-analytics-plus-wp' ), 'manage_options', 'gapwp_backend_settings', array( 'GAPWP_Settings', 'backend_settings' ) );
				add_submenu_page( 'gapwp_settings', __( "Frontend Settings", 'google-analytics-plus-wp' ), __( "Frontend Settings", 'google-analytics-plus-wp' ), 'manage_options', 'gapwp_frontend_settings', array( 'GAPWP_Settings', 'frontend_settings' ) );
				add_submenu_page( 'gapwp_settings', __( "Tracking Settings", 'google-analytics-plus-wp' ), __( "Tracking Code", 'google-analytics-plus-wp' ), 'manage_options', 'gapwp_tracking_settings', array( 'GAPWP_Settings', 'tracking_settings' ) );
				add_submenu_page( 'gapwp_settings', __( "Errors & Debug", 'google-analytics-plus-wp' ), __( "Errors & Debug", 'google-analytics-plus-wp' ), 'manage_options', 'gapwp_errors_debugging', array( 'GAPWP_Settings', 'errors_debugging' ) );
				*/
			}
		}

		/**
		 * Add Network Menu
		 */
		public function network_menu() {
			global $wp_version;
			if ( current_user_can( 'manage_network' ) ) {
				include ( GAPWP_DIR . 'admin/settings.php' );
				add_menu_page( __( "Google Analytics+", 'google-analytics-plus-wp' ), "Google Analytics+", 'manage_network', 'gapwp_settings', array( 'GAPWP_Settings', 'general_settings_network' ), version_compare( $wp_version, '3.8.0', '>=' ) ? 'dashicons-chart-area' : GAPWP_URL . 'admin/images/gapwp-icon.png' );
				add_submenu_page( 'gapwp_settings', __( "General Settings", 'google-analytics-plus-wp' ), __( "General Settings", 'google-analytics-plus-wp' ), 'manage_network', 'gapwp_settings', array( 'GAPWP_Settings', 'general_settings_network' ) );
				add_submenu_page( 'gapwp_settings', __( "Errors & Debug", 'google-analytics-plus-wp' ), __( "Errors & Debug", 'google-analytics-plus-wp' ), 'manage_network', 'gapwp_errors_debugging', array( 'GAPWP_Settings', 'errors_debugging' ) );
			}
		}

		/**
		 * Styles & Scripts conditional loading (based on current URI)
		 *
		 * @param
		 *            $hook
		 */
		public function load_styles_scripts( $hook ) {
			$new_hook = explode( '_page_', $hook );

			if ( isset( $new_hook[1] ) ) {
				$new_hook = '_page_' . $new_hook[1];
			} else {
				$new_hook = $hook;
			}

			/*
			 * GAPWP main stylesheet
			 */
			wp_enqueue_style( 'gapwp', GAPWP_URL . 'admin/css/gapwp.css', null, GAPWP_CURRENT_VERSION );

			/*
			 * GAPWP UI
			 */

			if ( GAPWP_Tools::get_cache( 'gapi_errors' ) ) {
				$ed_bubble = '!';
			} else {
				$ed_bubble = '';
			}

			wp_enqueue_script( 'gapwp-backend-ui', plugins_url( 'js/ui.js', __FILE__ ), array( 'jquery' ), GAPWP_CURRENT_VERSION, true );

			/* @formatter:off */
			wp_localize_script( 'gapwp-backend-ui', 'gapwp_ui_data', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'security' => wp_create_nonce( 'gapwp_dismiss_notices' ),
				'ed_bubble' => $ed_bubble,
			)
			);
			/* @formatter:on */

			if ( $this->gapwp->config->options['switch_profile'] && count( $this->gapwp->config->options['ga_profiles_list'] ) > 1 ) {
				$views = array();
				foreach ( $this->gapwp->config->options['ga_profiles_list'] as $items ) {
					if ( $items[3] ) {
						$views[$items[1]] = esc_js( GAPWP_Tools::strip_protocol( $items[3] ) ); // . ' &#8658; ' . $items[0] );
					}
				}
			} else {
				$views = false;
			}

			/*
			 * Main Dashboard Widgets Styles & Scripts
			 */
			$widgets_hooks = array( 'index.php' );

			if ( in_array( $new_hook, $widgets_hooks ) ) {
				if ( GAPWP_Tools::check_roles( $this->gapwp->config->options['access_back'] ) && $this->gapwp->config->options['dashboard_widget'] ) {

					if ( $this->gapwp->config->options['ga_target_geomap'] ) {
						$country_codes = GAPWP_Tools::get_countrycodes();
						if ( isset( $country_codes[$this->gapwp->config->options['ga_target_geomap']] ) ) {
							$region = $this->gapwp->config->options['ga_target_geomap'];
						} else {
							$region = false;
						}
					} else {
						$region = false;
					}

					wp_enqueue_style( 'gapwp-nprogress', GAPWP_URL . 'common/nprogress/nprogress.css', null, GAPWP_CURRENT_VERSION );

					wp_enqueue_style( 'gapwp-backend-item-reports', GAPWP_URL . 'admin/css/admin-widgets.css', null, GAPWP_CURRENT_VERSION );

					wp_register_style( 'jquery-ui-tooltip-html', GAPWP_URL . 'common/realtime/jquery.ui.tooltip.html.css' );

					wp_enqueue_style( 'jquery-ui-tooltip-html' );

					wp_register_script( 'jquery-ui-tooltip-html', GAPWP_URL . 'common/realtime/jquery.ui.tooltip.html.js' );

					wp_register_script( 'googlecharts', 'https://www.gstatic.com/charts/loader.js', array(), null );

					wp_enqueue_script( 'gapwp-nprogress', GAPWP_URL . 'common/nprogress/nprogress.js', array( 'jquery' ), GAPWP_CURRENT_VERSION );

					wp_enqueue_script( 'gapwp-backend-dashboard-reports', GAPWP_URL . 'common/js/reports5.js', array( 'jquery', 'googlecharts', 'gapwp-nprogress', 'jquery-ui-tooltip', 'jquery-ui-core', 'jquery-ui-position', 'jquery-ui-tooltip-html' ), GAPWP_CURRENT_VERSION, true );

					/* @formatter:off */

					$datelist = array(
						'realtime' => __( "Real-Time", 'google-analytics-plus-wp' ),
						'today' => __( "Today", 'google-analytics-plus-wp' ),
						'yesterday' => __( "Yesterday", 'google-analytics-plus-wp' ),
						'7daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-plus-wp' ), 7 ),
						'14daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-plus-wp' ), 14 ),
						'30daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-plus-wp' ), 30 ),
						'90daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-plus-wp' ), 90 ),
						'365daysAgo' =>  sprintf( _n( "%s Year", "%s Years", 1, 'google-analytics-plus-wp' ), __('One', 'google-analytics-plus-wp') ),
						'1095daysAgo' =>  sprintf( _n( "%s Year", "%s Years", 3, 'google-analytics-plus-wp' ), __('Three', 'google-analytics-plus-wp') ),
					);


					if ( $this->gapwp->config->options['user_api'] && ! $this->gapwp->config->options['backend_realtime_report'] ) {
						array_shift( $datelist );
					}

					wp_localize_script( 'gapwp-backend-dashboard-reports', 'gapwpItemData', array(
						'ajaxurl' => admin_url( 'admin-ajax.php' ),
						'security' => wp_create_nonce( 'gapwp_backend_item_reports' ),
						'dateList' => $datelist,
						'reportList' => array(
							'sessions' => __( "Sessions", 'google-analytics-plus-wp' ),
							'users' => __( "Users", 'google-analytics-plus-wp' ),
							'organicSearches' => __( "Organic", 'google-analytics-plus-wp' ),
							'pageviews' => __( "Page Views", 'google-analytics-plus-wp' ),
							'visitBounceRate' => __( "Bounce Rate", 'google-analytics-plus-wp' ),
							'locations' => __( "Location", 'google-analytics-plus-wp' ),
							'contentpages' =>  __( "Pages", 'google-analytics-plus-wp' ),
							'referrers' => __( "Referrers", 'google-analytics-plus-wp' ),
							'searches' => __( "Searches", 'google-analytics-plus-wp' ),
							'trafficdetails' => __( "Traffic", 'google-analytics-plus-wp' ),
							'technologydetails' => __( "Technology", 'google-analytics-plus-wp' ),
							'404errors' => __( "404 Errors", 'google-analytics-plus-wp' ),
						),
						'i18n' => array(
							__( "A JavaScript Error is blocking plugin resources!", 'google-analytics-plus-wp' ), //0
							__( "Traffic Mediums", 'google-analytics-plus-wp' ),
							__( "Visitor Type", 'google-analytics-plus-wp' ),
							__( "Search Engines", 'google-analytics-plus-wp' ),
							__( "Social Networks", 'google-analytics-plus-wp' ),
							__( "Sessions", 'google-analytics-plus-wp' ),
							__( "Users", 'google-analytics-plus-wp' ),
							__( "Page Views", 'google-analytics-plus-wp' ),
							__( "Bounce Rate", 'google-analytics-plus-wp' ),
							__( "Organic Search", 'google-analytics-plus-wp' ),
							__( "Pages/Session", 'google-analytics-plus-wp' ),
							__( "Invalid response", 'google-analytics-plus-wp' ),
							__( "No Data", 'google-analytics-plus-wp' ),
							__( "This report is unavailable", 'google-analytics-plus-wp' ),
							__( "report generated by", 'google-analytics-plus-wp' ), //14
							__( "This plugin needs an authorization:", 'google-analytics-plus-wp' ) . ' <a href="' . menu_page_url( 'gapwp_settings', false ) . '">' . __( "authorize the plugin", 'google-analytics-plus-wp' ) . '</a>.',
							__( "Browser", 'google-analytics-plus-wp' ), //16
							__( "Operating System", 'google-analytics-plus-wp' ),
							__( "Screen Resolution", 'google-analytics-plus-wp' ),
							__( "Mobile Brand", 'google-analytics-plus-wp' ),
							__( "REFERRALS", 'google-analytics-plus-wp' ), //20
							__( "KEYWORDS", 'google-analytics-plus-wp' ),
							__( "SOCIAL", 'google-analytics-plus-wp' ),
							__( "CAMPAIGN", 'google-analytics-plus-wp' ),
							__( "DIRECT", 'google-analytics-plus-wp' ),
							__( "NEW", 'google-analytics-plus-wp' ), //25
							__( "Time on Page", 'google-analytics-plus-wp' ),
							__( "Page Load Time", 'google-analytics-plus-wp' ),
							__( "Session Duration", 'google-analytics-plus-wp' ),
						),
						'rtLimitPages' => $this->gapwp->config->options['ga_realtime_pages'],
						'colorVariations' => GAPWP_Tools::variations( $this->gapwp->config->options['theme_color'] ),
						'region' => $region,
						'mapsApiKey' => apply_filters( 'gapwp_maps_api_key', $this->gapwp->config->options['maps_api_key'] ),
						'language' => get_bloginfo( 'language' ),
						'viewList' => $views,
						'scope' => 'admin-widgets',
					)

					);
					/* @formatter:on */
				}
			}

			/*
			 * Posts/Pages List Styles & Scripts
			 */
			$contentstats_hooks = array( 'edit.php' );
			if ( in_array( $hook, $contentstats_hooks ) ) {
				if ( GAPWP_Tools::check_roles( $this->gapwp->config->options['access_back'] ) && $this->gapwp->config->options['backend_item_reports'] ) {

					if ( $this->gapwp->config->options['ga_target_geomap'] ) {
						$country_codes = GAPWP_Tools::get_countrycodes();
						if ( isset( $country_codes[$this->gapwp->config->options['ga_target_geomap']] ) ) {
							$region = $this->gapwp->config->options['ga_target_geomap'];
						} else {
							$region = false;
						}
					} else {
						$region = false;
					}

					wp_enqueue_style( 'gapwp-nprogress', GAPWP_URL . 'common/nprogress/nprogress.css', null, GAPWP_CURRENT_VERSION );

					wp_enqueue_style( 'gapwp-backend-item-reports', GAPWP_URL . 'admin/css/item-reports.css', null, GAPWP_CURRENT_VERSION );

					wp_enqueue_style( "wp-jquery-ui-dialog" );

					wp_register_script( 'googlecharts', 'https://www.gstatic.com/charts/loader.js', array(), null );

					wp_enqueue_script( 'gapwp-nprogress', GAPWP_URL . 'common/nprogress/nprogress.js', array( 'jquery' ), GAPWP_CURRENT_VERSION );

					wp_enqueue_script( 'gapwp-backend-item-reports', GAPWP_URL . 'common/js/reports5.js', array( 'gapwp-nprogress', 'googlecharts', 'jquery', 'jquery-ui-dialog' ), GAPWP_CURRENT_VERSION, true );

					/* @formatter:off */
					wp_localize_script( 'gapwp-backend-item-reports', 'gapwpItemData', array(
						'ajaxurl' => admin_url( 'admin-ajax.php' ),
						'security' => wp_create_nonce( 'gapwp_backend_item_reports' ),
						'dateList' => array(
							'today' => __( "Today", 'google-analytics-plus-wp' ),
							'yesterday' => __( "Yesterday", 'google-analytics-plus-wp' ),
							'7daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-plus-wp' ), 7 ),
							'14daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-plus-wp' ), 14 ),
							'30daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-plus-wp' ), 30 ),
							'90daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-plus-wp' ), 90 ),
							'365daysAgo' =>  sprintf( _n( "%s Year", "%s Years", 1, 'google-analytics-plus-wp' ), __('One', 'google-analytics-plus-wp') ),
							'1095daysAgo' =>  sprintf( _n( "%s Year", "%s Years", 3, 'google-analytics-plus-wp' ), __('Three', 'google-analytics-plus-wp') ),
						),
						'reportList' => array(
							'uniquePageviews' => __( "Unique Views", 'google-analytics-plus-wp' ),
							'users' => __( "Users", 'google-analytics-plus-wp' ),
							'organicSearches' => __( "Organic", 'google-analytics-plus-wp' ),
							'pageviews' => __( "Page Views", 'google-analytics-plus-wp' ),
							'visitBounceRate' => __( "Bounce Rate", 'google-analytics-plus-wp' ),
							'locations' => __( "Location", 'google-analytics-plus-wp' ),
							'referrers' => __( "Referrers", 'google-analytics-plus-wp' ),
							'searches' => __( "Searches", 'google-analytics-plus-wp' ),
							'trafficdetails' => __( "Traffic", 'google-analytics-plus-wp' ),
							'technologydetails' => __( "Technology", 'google-analytics-plus-wp' ),
						),
						'i18n' => array(
							__( "A JavaScript Error is blocking plugin resources!", 'google-analytics-plus-wp' ), //0
							__( "Traffic Mediums", 'google-analytics-plus-wp' ),
							__( "Visitor Type", 'google-analytics-plus-wp' ),
							__( "Social Networks", 'google-analytics-plus-wp' ),
							__( "Search Engines", 'google-analytics-plus-wp' ),
							__( "Unique Views", 'google-analytics-plus-wp' ),
							__( "Users", 'google-analytics-plus-wp' ),
							__( "Page Views", 'google-analytics-plus-wp' ),
							__( "Bounce Rate", 'google-analytics-plus-wp' ),
							__( "Organic Search", 'google-analytics-plus-wp' ),
							__( "Pages/Session", 'google-analytics-plus-wp' ),
							__( "Invalid response", 'google-analytics-plus-wp' ),
							__( "No Data", 'google-analytics-plus-wp' ),
							__( "This report is unavailable", 'google-analytics-plus-wp' ),
							__( "report generated by", 'google-analytics-plus-wp' ), //14
							__( "This plugin needs an authorization:", 'google-analytics-plus-wp' ) . ' <a href="' . menu_page_url( 'gapwp_settings', false ) . '">' . __( "authorize the plugin", 'google-analytics-plus-wp' ) . '</a>.',
							__( "Browser", 'google-analytics-plus-wp' ), //16
							__( "Operating System", 'google-analytics-plus-wp' ),
							__( "Screen Resolution", 'google-analytics-plus-wp' ),
							__( "Mobile Brand", 'google-analytics-plus-wp' ), //19
							__( "Future Use", 'google-analytics-plus-wp' ),
							__( "Future Use", 'google-analytics-plus-wp' ),
							__( "Future Use", 'google-analytics-plus-wp' ),
							__( "Future Use", 'google-analytics-plus-wp' ),
							__( "Future Use", 'google-analytics-plus-wp' ),
							__( "Future Use", 'google-analytics-plus-wp' ), //25
							__( "Time on Page", 'google-analytics-plus-wp' ),
							__( "Page Load Time", 'google-analytics-plus-wp' ),
							__( "Exit Rate", 'google-analytics-plus-wp' ),
						),
						'colorVariations' => GAPWP_Tools::variations( $this->gapwp->config->options['theme_color'] ),
						'region' => $region,
						'mapsApiKey' => apply_filters( 'gapwp_maps_api_key', $this->gapwp->config->options['maps_api_key'] ),
						'language' => get_bloginfo( 'language' ),
						'viewList' => false,
						'scope' => 'admin-item',
						)
					);
					/* @formatter:on */
				}
			}

			/*
			 * Settings Styles & Scripts
			 */
			$settings_hooks = array( '_page_gapwp_settings', '_page_gapwp_backend_settings', '_page_gapwp_frontend_settings', '_page_gapwp_tracking_settings', '_page_gapwp_errors_debugging' );

			if ( in_array( $new_hook, $settings_hooks ) ) {
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker-script-handle', plugins_url( 'js/wp-color-picker-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
				wp_enqueue_script( 'gapwp-settings', plugins_url( 'js/settings.js', __FILE__ ), array( 'jquery' ), GAPWP_CURRENT_VERSION, true );
			}
		}

		/**
		 * Add "Settings" link in Plugins List
		 *
		 * @param
		 *            $links
		 * @return array
		 */
		public function settings_link( $links ) {
			$settings_link = '<a href="' . esc_url( get_admin_url( null, 'admin.php?page=gapwp_settings' ) ) . '">' . __( "Settings", 'google-analytics-plus-wp' ) . '</a>';
			array_unshift( $links, $settings_link );
			return $links;
		}

		/**
		 *  Add an admin notice after a manual or atuomatic update
		 */
		function admin_notice() {
			$currentScreen = get_current_screen();

			if ( ! current_user_can( 'manage_options' ) || strpos( $currentScreen->base, '_gapwp_' ) === false ) {
				return;
			}

			if ( get_option( 'gapwp_got_updated' ) ) :
				?>
<div id="gapwp-notice" class="notice is-dismissible">
	<p><?php echo sprintf( __('Google Analytics+ for WP has been updated to version %s.', 'google-analytics-plus-wp' ), GAPWP_CURRENT_VERSION).' '.sprintf( __('For details, check out %1$s.', 'google-analytics-plus-wp' ), sprintf(' <a href="https://intelligencewp.com/google-analytics-plus-wordpress/?utm_source=gapwp_notice&utm_medium=link&utm_content=release_notice&utm_campaign=gapwp">%s</a>', __('the plugin documentation', 'google-analytics-plus-wp') ) ); ?></p>
</div>

			<?php
			endif;
		}
	}
}
