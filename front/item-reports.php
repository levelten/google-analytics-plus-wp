<?php
/**
 * Copyright 2013 Alin Marcu
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit();

if ( ! class_exists( 'GAPWP_Frontend_Item_Reports' ) ) {

	final class GAPWP_Frontend_Item_Reports {

		private $gapwp;

		public function __construct() {
			$this->gapwp = GAPWP();
			
			add_action( 'admin_bar_menu', array( $this, 'custom_adminbar_node' ), 999 );
		}

		function custom_adminbar_node( $wp_admin_bar ) {
			if ( GAPWP_Tools::check_roles( $this->gapwp->config->options['access_front'] ) && $this->gapwp->config->options['frontend_item_reports'] ) {
				/* @formatter:off */
				$args = array( 	'id' => 'gapwp-1',
								'title' => '<span class="ab-icon"></span><span class="">' . __( "Analytics", 'google-analytics-plus-wp' ) . '</span>',
								'href' => '#1',
								);
				/* @formatter:on */
				$wp_admin_bar->add_node( $args );
			}
		}
	}
}
