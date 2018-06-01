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

if ( ! class_exists( 'OGADWP_Frontend_Item_Reports' ) ) {

	final class OGADWP_Frontend_Item_Reports {

		private $ogadwp;

		public function __construct() {
			$this->ogadwp = OGADWP();
			
			add_action( 'admin_bar_menu', array( $this, 'custom_adminbar_node' ), 999 );
		}

		function custom_adminbar_node( $wp_admin_bar ) {
			if ( OGADWP_Tools::check_roles( $this->ogadwp->config->options['access_front'] ) && $this->ogadwp->config->options['frontend_item_reports'] ) {
				/* @formatter:off */
				$args = array( 	'id' => 'ogadwp-1',
								'title' => '<span class="ab-icon"></span><span class="">' . __( "Analytics", 'open-google-analytics-dashboard-for-wp' ) . '</span>',
								'href' => '#1',
								);
				/* @formatter:on */
				$wp_admin_bar->add_node( $args );
			}
		}
	}
}
