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

if ( ! class_exists( 'OGADWP_Frontend_Ajax' ) ) {

	final class OGADWP_Frontend_Ajax {

		private $ogadwp;

		public function __construct() {
			$this->ogadwp = OGADWP();

			if ( OGADWP_Tools::check_roles( $this->ogadwp->config->options['access_front'] ) && $this->ogadwp->config->options['frontend_item_reports'] ) {
				// Item Reports action
				add_action( 'wp_ajax_ogadwp_frontend_item_reports', array( $this, 'ajax_item_reports' ) );
			}

			// Frontend Widget actions
			add_action( 'wp_ajax_ajax_frontwidget_report', array( $this, 'ajax_frontend_widget' ) );
			add_action( 'wp_ajax_nopriv_ajax_frontwidget_report', array( $this, 'ajax_frontend_widget' ) );
		}

		/**
		 * Ajax handler for Item Reports
		 *
		 * @return string|int
		 */
		public function ajax_item_reports() {
			if ( ! isset( $_POST['ogadwp_security_frontend_item_reports'] ) || ! wp_verify_nonce( $_POST['ogadwp_security_frontend_item_reports'], 'ogadwp_frontend_item_reports' ) ) {
				wp_die( - 30 );
			}

			$from = $_POST['from'];
			$to = $_POST['to'];
			$query = $_POST['query'];
			$uri = $_POST['filter'];
			if ( isset( $_POST['metric'] ) ) {
				$metric = $_POST['metric'];
			} else {
				$metric = 'pageviews';
			}

			$query = $_POST['query'];
			if ( ob_get_length() ) {
				ob_clean();
			}

			if ( ! OGADWP_Tools::check_roles( $this->ogadwp->config->options['access_front'] ) || 0 == $this->ogadwp->config->options['frontend_item_reports'] ) {
				wp_die( - 31 );
			}

			if ( $this->ogadwp->config->options['token'] && $this->ogadwp->config->options['tableid_jail'] ) {
				if ( null === $this->ogadwp->gapi_controller ) {
					$this->ogadwp->gapi_controller = new OGADWP_GAPI_Controller();
				}
			} else {
				wp_die( - 24 );
			}

			if ( $this->ogadwp->config->options['tableid_jail'] ) {
				$projectId = $this->ogadwp->config->options['tableid_jail'];
			} else {
				wp_die( - 26 );
			}

			$profile_info = OGADWP_Tools::get_selected_profile( $this->ogadwp->config->options['ga_profiles_list'], $projectId );

			if ( isset( $profile_info[4] ) ) {
				$this->ogadwp->gapi_controller->timeshift = $profile_info[4];
			} else {
				$this->ogadwp->gapi_controller->timeshift = (int) current_time( 'timestamp' ) - time();
			}

			$uri = '/' . ltrim( $uri, '/' );

			// allow URL correction before sending an API request
			$filter = apply_filters( 'ogadwp_frontenditem_uri', $uri );

			$lastchar = substr( $filter, - 1 );

			if ( isset( $profile_info[6] ) && $profile_info[6] && '/' == $lastchar ) {
				$filter = $filter . $profile_info[6];
			}

			// Encode URL
			$filter = rawurlencode( rawurldecode( $filter ) );

			$queries = explode( ',', $query );

			$results = array();

			foreach ( $queries as $value ) {
				$results[] = $this->ogadwp->gapi_controller->get( $projectId, $value, $from, $to, $filter, $metric );
			}

			wp_send_json( $results );
		}

		/**
		 * Ajax handler for getting analytics data for frontend Widget
		 *
		 * @return string|int
		 */
		public function ajax_frontend_widget() {
			if ( ! isset( $_POST['ogadwp_number'] ) || ! isset( $_POST['ogadwp_optionname'] ) || ! is_active_widget( false, false, 'ogadwp-frontwidget-report' ) ) {
				wp_die( - 30 );
			}
			$widget_index = $_POST['ogadwp_number'];
			$option_name = $_POST['ogadwp_optionname'];
			$options = get_option( $option_name );
			if ( isset( $options[$widget_index] ) ) {
				$instance = $options[$widget_index];
			} else {
				wp_die( - 32 );
			}
			switch ( $instance['period'] ) { // make sure we have a valid request
				case '7daysAgo' :
					$period = '7daysAgo';
					break;
				case '14daysAgo' :
					$period = '14daysAgo';
					break;
				default :
					$period = '30daysAgo';
					break;
			}
			if ( ob_get_length() ) {
				ob_clean();
			}
			if ( $this->ogadwp->config->options['token'] && $this->ogadwp->config->options['tableid_jail'] ) {
				if ( null === $this->ogadwp->gapi_controller ) {
					$this->ogadwp->gapi_controller = new OGADWP_GAPI_Controller();
				}
			} else {
				wp_die( - 24 );
			}
			$projectId = $this->ogadwp->config->options['tableid_jail'];
			$profile_info = OGADWP_Tools::get_selected_profile( $this->ogadwp->config->options['ga_profiles_list'], $projectId );
			if ( isset( $profile_info[4] ) ) {
				$this->ogadwp->gapi_controller->timeshift = $profile_info[4];
			} else {
				$this->ogadwp->gapi_controller->timeshift = (int) current_time( 'timestamp' ) - time();
			}
			wp_send_json( $this->ogadwp->gapi_controller->frontend_widget_stats( $projectId, $period, (int) $instance['anonim'] ) );
		}
	}
}
