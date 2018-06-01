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

if ( ! class_exists( 'OGADWP_Backend_Widgets' ) ) {

	class OGADWP_Backend_Widgets {

		private $ogadwp;

		public function __construct() {
			$this->ogadwp = OGADWP();
			if ( OGADWP_Tools::check_roles( $this->ogadwp->config->options['access_back'] ) && ( 1 == $this->ogadwp->config->options['dashboard_widget'] ) ) {
				add_action( 'wp_dashboard_setup', array( $this, 'add_widget' ) );
			}
		}

		public function add_widget() {
			wp_add_dashboard_widget( 'ogadwp-widget', __( "Open Google Analytics Dashboard", 'open-google-analytics-dashboard-for-wp' ), array( $this, 'dashboard_widget' ), $control_callback = null );
		}

		public function dashboard_widget() {
			$projectId = 0;
			
			if ( empty( $this->ogadwp->config->options['token'] ) ) {
				echo '<p>' . __( "This plugin needs an authorization:", 'open-google-analytics-dashboard-for-wp' ) . '</p><form action="' . menu_page_url( 'ogadwp_settings', false ) . '" method="POST">' . get_submit_button( __( "Authorize Plugin", 'open-google-analytics-dashboard-for-wp' ), 'secondary' ) . '</form>';
				return;
			}
			
			if ( current_user_can( 'manage_options' ) ) {
				if ( $this->ogadwp->config->options['tableid_jail'] ) {
					$projectId = $this->ogadwp->config->options['tableid_jail'];
				} else {
					echo '<p>' . __( "An admin should asign a default Google Analytics Profile.", 'open-google-analytics-dashboard-for-wp' ) . '</p><form action="' . menu_page_url( 'ogadwp_settings', false ) . '" method="POST">' . get_submit_button( __( "Select Domain", 'open-google-analytics-dashboard-for-wp' ), 'secondary' ) . '</form>';
					return;
				}
			} else {
				if ( $this->ogadwp->config->options['tableid_jail'] ) {
					$projectId = $this->ogadwp->config->options['tableid_jail'];
				} else {
					echo '<p>' . __( "An admin should asign a default Google Analytics Profile.", 'open-google-analytics-dashboard-for-wp' ) . '</p><form action="' . menu_page_url( 'ogadwp_settings', false ) . '" method="POST">' . get_submit_button( __( "Select Domain", 'open-google-analytics-dashboard-for-wp' ), 'secondary' ) . '</form>';
					return;
				}
			}
			
			if ( ! ( $projectId ) ) {
				echo '<p>' . __( "Something went wrong while retrieving property data. You need to create and properly configure a Google Analytics account:", 'open-google-analytics-dashboard-for-wp' ) . '</p> <form action="https://deconf.com/how-to-set-up-google-analytics-on-your-website/" method="POST">' . get_submit_button( __( "Find out more!", 'open-google-analytics-dashboard-for-wp' ), 'secondary' ) . '</form>';
				return;
			}
			
			?>
<div id="ogadwp-window-1"></div>
<?php
		}
	}
}
