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

class OGADWP_Uninstall {

	public static function uninstall() {
		global $wpdb;
		if ( is_multisite() ) { // Cleanup Network install
			foreach ( OGADWP_Tools::get_sites( array( 'number' => apply_filters( 'ogadwp_sites_limit', 100 ) ) ) as $blog ) {
				switch_to_blog( $blog['blog_id'] );
				$sqlquery = $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'ogadwp_cache_%%'" );
				delete_option( 'ogadwp_options' );
				restore_current_blog();
			}
			delete_site_option( 'ogadwp_network_options' );
		} else { // Cleanup Single install
			$sqlquery = $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'ogadwp_cache_%%'" );
			delete_option( 'ogadwp_options' );
		}
		OGADWP_Tools::unset_cookie( 'default_metric' );
		OGADWP_Tools::unset_cookie( 'default_dimension' );
		OGADWP_Tools::unset_cookie( 'default_view' );
	}
}
