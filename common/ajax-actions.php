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

if ( ! class_exists( 'OGADWP_Common_Ajax' ) ) {

	final class OGADWP_Common_Ajax {

		private $ogadwp;

		public function __construct() {
			$this->ogadwp = OGADWP();

			if ( OGADWP_Tools::check_roles( $this->ogadwp->config->options['access_back'] ) || OGADWP_Tools::check_roles( $this->ogadwp->config->options['access_front'] ) ) {
				add_action( 'wp_ajax_ogadwp_set_error', array( $this, 'ajax_set_error' ) );
			}
		}

		/**
		 * Ajax handler for storing JavaScript Errors
		 *
		 * @return json|int
		 */
		public function ajax_set_error() {
			if ( ! isset( $_POST['ogadwp_security_set_error'] ) || ! ( wp_verify_nonce( $_POST['ogadwp_security_set_error'], 'ogadwp_backend_item_reports' ) || wp_verify_nonce( $_POST['ogadwp_security_set_error'], 'ogadwp_frontend_item_reports' ) ) ) {
				wp_die( - 40 );
			}
			$timeout = 24 * 60 * 60;
			OGADWP_Tools::set_error( $_POST['response'], $timeout );
			wp_die();
		}
	}
}
