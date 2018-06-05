<?php
/**
 * Copyright 2013 Alin Marcu
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit();

if ( ! class_exists( 'GAPWP_Backend_Item_Reports' ) ) {

	final class GAPWP_Backend_Item_Reports {

		private $gapwp;

		public function __construct() {
			$this->gapwp = GAPWP();

			if ( GAPWP_Tools::check_roles( $this->gapwp->config->options['access_back'] ) && 1 == $this->gapwp->config->options['backend_item_reports'] ) {
				// Add custom column in Posts List
				add_filter( 'manage_posts_columns', array( $this, 'add_columns' ) );

				// Populate custom column in Posts List
				add_action( 'manage_posts_custom_column', array( $this, 'add_icons' ), 10, 2 );

				// Add custom column in Pages List
				add_filter( 'manage_pages_columns', array( $this, 'add_columns' ) );

				// Populate custom column in Pages List
				add_action( 'manage_pages_custom_column', array( $this, 'add_icons' ), 10, 2 );
			}
		}

		public function add_icons( $column, $id ) {
			global $wp_version;

			if ( 'gapwp_stats' != $column ) {
				return;
			}

			if ( version_compare( $wp_version, '3.8.0', '>=' ) ) {
				echo '<a id="gapwp-' . $id . '" title="' . get_the_title( $id ) . '" href="#' . $id . '" class="gapwp-icon dashicons-before dashicons-chart-area">&nbsp;</a>';
			} else {
				echo '<a id="gapwp-' . $id . '" title="' . get_the_title( $id ) . '" href="#' . $id . '"><img class="gapwp-icon-oldwp" src="' . GAPWP_URL . 'admin/images/gapwp-icon.png"</a>';
			}
		}

		public function add_columns( $columns ) {
			return array_merge( $columns, array( 'gapwp_stats' => __( 'Analytics', 'google-analytics-plus-wp' ) ) );
		}
	}
}
