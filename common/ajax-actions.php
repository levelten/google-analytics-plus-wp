<?php
/**
 * Copyright 2013 Alin Marcu
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit();

if ( ! class_exists( 'GAPWP_Common_Ajax' ) ) {

	final class GAPWP_Common_Ajax {

		private $gapwp;

		public function __construct() {
			$this->gapwp = GAPWP();

			if ( GAPWP_Tools::check_roles( $this->gapwp->config->options['access_back'] ) || GAPWP_Tools::check_roles( $this->gapwp->config->options['access_front'] ) ) {
				add_action( 'wp_ajax_gapwp_set_error', array( $this, 'ajax_set_error' ) );
			}
		}

		/**
		 * Ajax handler for storing JavaScript Errors
		 *
		 * @return json|int
		 */
		public function ajax_set_error() {
			if ( ! isset( $_POST['gapwp_security_set_error'] ) || ! ( wp_verify_nonce( $_POST['gapwp_security_set_error'], 'gapwp_backend_item_reports' ) || wp_verify_nonce( $_POST['gapwp_security_set_error'], 'gapwp_frontend_item_reports' ) ) ) {
				wp_die( - 40 );
			}
			$timeout = 24 * 60 * 60;
			GAPWP_Tools::set_error( $_POST['response'], $timeout );
			wp_die();
		}
	}
}
