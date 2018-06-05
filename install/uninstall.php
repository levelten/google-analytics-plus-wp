<?php
/**
 * Copyright 2013 Alin Marcu
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit();

class GAPWP_Uninstall {

	public static function uninstall() {
		global $wpdb;
		if ( is_multisite() ) { // Cleanup Network install
			foreach ( GAPWP_Tools::get_sites( array( 'number' => apply_filters( 'gapwp_sites_limit', 100 ) ) ) as $blog ) {
				switch_to_blog( $blog['blog_id'] );
				$sqlquery = $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'gapwp_cache_%%'" );
				delete_option( 'gapwp_options' );
				restore_current_blog();
			}
			delete_site_option( 'gapwp_network_options' );
		} else { // Cleanup Single install
			$sqlquery = $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'gapwp_cache_%%'" );
			delete_option( 'gapwp_options' );
		}
		GAPWP_Tools::unset_cookie( 'default_metric' );
		GAPWP_Tools::unset_cookie( 'default_dimension' );
		GAPWP_Tools::unset_cookie( 'default_view' );
	}
}
