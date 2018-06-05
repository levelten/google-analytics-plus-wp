<?php
/**
 * Copyright 2013 Alin Marcu
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit();

if ( ! class_exists( 'GAPWP_Backend_Ajax' ) ) {

	final class GAPWP_Backend_Ajax {

		private $gapwp;

		public function __construct() {
			$this->gapwp = GAPWP();

			if ( GAPWP_Tools::check_roles( $this->gapwp->config->options['access_back'] ) && ( ( 1 == $this->gapwp->config->options['backend_item_reports'] ) || ( 1 == $this->gapwp->config->options['dashboard_widget'] ) ) ) {
				// Items action
				add_action( 'wp_ajax_gapwp_backend_item_reports', array( $this, 'ajax_item_reports' ) );
			}
			if ( current_user_can( 'manage_options' ) ) {
				// Admin Widget action
				add_action( 'wp_ajax_gapwp_dismiss_notices', array( $this, 'ajax_dismiss_notices' ) );
			}
		}

		/**
		 * Ajax handler for Item Reports
		 *
		 * @return json|int
		 */
		public function ajax_item_reports() {
			if ( ! isset( $_POST['gapwp_security_backend_item_reports'] ) || ! wp_verify_nonce( $_POST['gapwp_security_backend_item_reports'], 'gapwp_backend_item_reports' ) ) {
				wp_die( - 30 );
			}
			if ( isset( $_POST['projectId'] ) && $this->gapwp->config->options['switch_profile'] && 'false' !== $_POST['projectId'] ) {
				$projectId = $_POST['projectId'];
			} else {
				$projectId = false;
			}
			$from = $_POST['from'];
			$to = $_POST['to'];
			$query = $_POST['query'];
			if ( isset( $_POST['filter'] ) ) {
				$filter_id = $_POST['filter'];
			} else {
				$filter_id = false;
			}
			if ( isset( $_POST['metric'] ) ) {
				$metric = $_POST['metric'];
			} else {
				$metric = 'sessions';
			}

			if ( $filter_id && $metric == 'sessions' ) { // Sessions metric is not available for item reports
				$metric = 'pageviews';
			}

			if ( ob_get_length() ) {
				ob_clean();
			}

			if ( ! ( GAPWP_Tools::check_roles( $this->gapwp->config->options['access_back'] ) && ( ( 1 == $this->gapwp->config->options['backend_item_reports'] ) || ( 1 == $this->gapwp->config->options['dashboard_widget'] ) ) ) ) {
				wp_die( - 31 );
			}
			if ( $this->gapwp->config->options['token'] && $this->gapwp->config->options['tableid_jail'] && $from && $to ) {
				if ( null === $this->gapwp->gapi_controller ) {
					$this->gapwp->gapi_controller = new GAPWP_GAPI_Controller();
				}
			} else {
				wp_die( - 24 );
			}
			if ( false == $projectId ) {
				$projectId = $this->gapwp->config->options['tableid_jail'];
			}
			$profile_info = GAPWP_Tools::get_selected_profile( $this->gapwp->config->options['ga_profiles_list'], $projectId );
			if ( isset( $profile_info[4] ) ) {
				$this->gapwp->gapi_controller->timeshift = $profile_info[4];
			} else {
				$this->gapwp->gapi_controller->timeshift = (int) current_time( 'timestamp' ) - time();
			}

			if ( $filter_id ) {
				$uri_parts = explode( '/', get_permalink( $filter_id ), 4 );

				if ( isset( $uri_parts[3] ) ) {
					$uri = '/' . $uri_parts[3];
				} else {
					wp_die( - 25 );
				}

				// allow URL correction before sending an API request
				$filter = apply_filters( 'gapwp_backenditem_uri', $uri, $filter_id );

				$lastchar = substr( $filter, - 1 );

				if ( isset( $profile_info[6] ) && $profile_info[6] && '/' == $lastchar ) {
					$filter = $filter . $profile_info[6];
				}

				// Encode URL
				$filter = rawurlencode( rawurldecode( $filter ) );
			} else {
				$filter = false;
			}

			$queries = explode( ',', $query );

			$results = array();

			foreach ( $queries as $value ) {
				$results[] = $this->gapwp->gapi_controller->get( $projectId, $value, $from, $to, $filter, $metric );
			}

			wp_send_json( $results );
		}

		/**
		 * Ajax handler for dismissing Admin notices
		 *
		 * @return json|int
		 */
		public function ajax_dismiss_notices() {
			if ( ! isset( $_POST['gapwp_security_dismiss_notices'] ) || ! wp_verify_nonce( $_POST['gapwp_security_dismiss_notices'], 'gapwp_dismiss_notices' ) ) {
				wp_die( - 30 );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( - 31 );
			}

			delete_option( 'gapwp_got_updated' );

			wp_die();
		}
	}
}
