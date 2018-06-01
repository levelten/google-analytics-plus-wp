<?php
/**
 * Author: Alin Marcu
 * Author URI: https://deconf.com
 * Copyright 2013 Alin Marcu
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

final class OGADWP_Settings {

	private static function update_options( $who ) {
		$ogadwp = OGADWP();
		$network_settings = false;
		$options = $ogadwp->config->options; // Get current options
		if ( isset( $_POST['options']['ogadwp_hidden'] ) && isset( $_POST['options'] ) && ( isset( $_POST['ogadwp_security'] ) && wp_verify_nonce( $_POST['ogadwp_security'], 'ogadwp_form' ) ) && 'Reset' != $who ) {
			$new_options = $_POST['options'];
			if ( 'tracking' == $who ) {
				$options['ga_anonymize_ip'] = 0;
				$options['ga_optout'] = 0;
				$options['ga_dnt_optout'] = 0;
				$options['ga_event_tracking'] = 0;
				$options['ga_enhanced_links'] = 0;
				$options['ga_event_precision'] = 0;
				$options['ga_remarketing'] = 0;
				$options['ga_event_bouncerate'] = 0;
				$options['ga_crossdomain_tracking'] = 0;
				$options['ga_aff_tracking'] = 0;
				$options['ga_hash_tracking'] = 0;
				$options['ga_formsubmit_tracking'] = 0;
				$options['ga_force_ssl'] = 0;
				$options['ga_pagescrolldepth_tracking'] = 0;
				$options['tm_pagescrolldepth_tracking'] = 0;
				$options['tm_optout'] = 0;
				$options['tm_dnt_optout'] = 0;
				$options['amp_tracking_analytics'] = 0;
				$options['amp_tracking_clientidapi'] = 0;
				$options['amp_tracking_tagmanager'] = 0;
				$options['optimize_pagehiding'] = 0;
				$options['optimize_tracking'] = 0;
				$options['trackingcode_infooter'] = 0;
				$options['trackingevents_infooter'] = 0;
				$options['ga_with_gtag'] = 0;
				if ( isset( $_POST['options']['ga_tracking_code'] ) ) {
					$new_options['ga_tracking_code'] = trim( $new_options['ga_tracking_code'], "\t" );
				}
				if ( empty( $new_options['track_exclude'] ) ) {
					$new_options['track_exclude'] = array();
				}
			} elseif ( 'backend' == $who ) {
				$options['switch_profile'] = 0;
				$options['backend_item_reports'] = 0;
				$options['dashboard_widget'] = 0;
				$options['backend_realtime_report'] = 0;
				if ( empty( $new_options['access_back'] ) ) {
					$new_options['access_back'][] = 'administrator';
				}
			} elseif ( 'frontend' == $who ) {
				$options['frontend_item_reports'] = 0;
				if ( empty( $new_options['access_front'] ) ) {
					$new_options['access_front'][] = 'administrator';
				}
			} elseif ( 'general' == $who ) {
				$options['user_api'] = 0;
				if ( ! is_multisite() ) {
					$options['automatic_updates_minorversion'] = 0;
				}
			} elseif ( 'network' == $who ) {
				$options['user_api'] = 0;
				$options['network_mode'] = 0;
				$options['superadmin_tracking'] = 0;
				$options['automatic_updates_minorversion'] = 0;
				$network_settings = true;
			}
			$options = array_merge( $options, $new_options );
			$ogadwp->config->options = $options;
			$ogadwp->config->set_plugin_options( $network_settings );
		}
		return $options;
	}

	private static function navigation_tabs( $tabs ) {
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $tabs as $tab => $name ) {
			echo "<a class='nav-tab' id='tab-$tab' href='#top#ogadwp-$tab'>$name</a>";
		}
		echo '</h2>';
	}

	public static function frontend_settings() {
		$ogadwp = OGADWP();
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$options = self::update_options( 'frontend' );
		if ( isset( $_POST['options']['ogadwp_hidden'] ) ) {
			$message = "<div class='updated' id='ogadwp-autodismiss'><p>" . __( "Settings saved.", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
			if ( ! ( isset( $_POST['ogadwp_security'] ) && wp_verify_nonce( $_POST['ogadwp_security'], 'ogadwp_form' ) ) ) {
				$message = "<div class='error' id='ogadwp-autodismiss'><p>" . __( "Cheating Huh?", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
			}
		}
		if ( ! $ogadwp->config->options['tableid_jail'] || ! $ogadwp->config->options['token'] ) {
			$message = sprintf( '<div class="error"><p>%s</p></div>', sprintf( __( 'Something went wrong, check %1$s or %2$s.', 'open-google-analytics-dashboard-for-wp' ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'ogadwp_errors_debugging', false ), __( 'Errors & Debug', 'open-google-analytics-dashboard-for-wp' ) ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'ogadwp_settings', false ), __( 'authorize the plugin', 'open-google-analytics-dashboard-for-wp' ) ) ) );
		}
		?>
<form name="ogadwp_form" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
	<div class="wrap">
	<?php echo "<h2>" . __( "Google Analytics Frontend Settings", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?><hr>
	</div>
	<div id="poststuff" class="ogadwp">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="settings-wrapper">
					<div class="inside">
					<?php if (isset($message)) echo $message; ?>
						<table class="ogadwp-settings-options">
							<tr>
								<td colspan="2"><?php echo "<h2>" . __( "Permissions", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
							</tr>
							<tr>
								<td class="roles ogadwp-settings-title">
									<label for="access_front"><?php _e("Show stats to:", 'open-google-analytics-dashboard-for-wp' ); ?>
									</label>
								</td>
								<td class="ogadwp-settings-roles">
									<table>
										<tr>
										<?php if ( ! isset( $wp_roles ) ) : ?>
											<?php $wp_roles = new WP_Roles(); ?>
										<?php endif; ?>
										<?php $i = 0; ?>
										<?php foreach ( $wp_roles->role_names as $role => $name ) : ?>
											<?php if ( 'subscriber' != $role ) : ?>
												<?php $i++; ?>
												<td>
												<label>
													<input type="checkbox" name="options[access_front][]" value="<?php echo $role; ?>" <?php if ( in_array($role,$options['access_front']) || 'administrator' == $role ) echo 'checked="checked"'; if ( 'administrator' == $role ) echo 'disabled="disabled"';?> /><?php echo $name; ?>
												  </label>
											</td>
											<?php endif; ?>
											<?php if ( 0 == $i % 4 ) : ?>
										 </tr>
										<tr>
											<?php endif; ?>
										<?php endforeach; ?>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="ogadwp-settings-title">
									<div class="button-primary ogadwp-settings-switchoo">
										<input type="checkbox" name="options[frontend_item_reports]" value="1" class="ogadwp-settings-switchoo-checkbox" id="frontend_item_reports" <?php checked( $options['frontend_item_reports'], 1 ); ?>>
										<label class="ogadwp-settings-switchoo-label" for="frontend_item_reports">
											<div class="ogadwp-settings-switchoo-inner"></div>
											<div class="ogadwp-settings-switchoo-switch"></div>
										</label>
									</div>
									<div class="switch-desc"><?php echo " ".__("enable web page reports on frontend", 'open-google-analytics-dashboard-for-wp' );?></div>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<hr>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="submit">
									<input type="submit" name="Submit" class="button button-primary" value="<?php _e('Save Changes', 'open-google-analytics-dashboard-for-wp' ) ?>" />
								</td>
							</tr>
						</table>
						<input type="hidden" name="options[ogadwp_hidden]" value="Y">
						<?php wp_nonce_field('ogadwp_form','ogadwp_security');?>






</form>
<?php
		self::output_sidebar();
	}

	public static function backend_settings() {
		$ogadwp = OGADWP();
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$options = self::update_options( 'backend' );
		if ( isset( $_POST['options']['ogadwp_hidden'] ) ) {
			$message = "<div class='updated' id='ogadwp-autodismiss'><p>" . __( "Settings saved.", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
			if ( ! ( isset( $_POST['ogadwp_security'] ) && wp_verify_nonce( $_POST['ogadwp_security'], 'ogadwp_form' ) ) ) {
				$message = "<div class='error' id='ogadwp-autodismiss'><p>" . __( "Cheating Huh?", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
			}
		}
		if ( ! $ogadwp->config->options['tableid_jail'] || ! $ogadwp->config->options['token'] ) {
			$message = sprintf( '<div class="error"><p>%s</p></div>', sprintf( __( 'Something went wrong, check %1$s or %2$s.', 'open-google-analytics-dashboard-for-wp' ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'ogadwp_errors_debugging', false ), __( 'Errors & Debug', 'open-google-analytics-dashboard-for-wp' ) ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'ogadwp_settings', false ), __( 'authorize the plugin', 'open-google-analytics-dashboard-for-wp' ) ) ) );
		}
		?>
<form name="ogadwp_form" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
	<div class="wrap">
			<?php echo "<h2>" . __( "Google Analytics Backend Settings", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?><hr>
	</div>
	<div id="poststuff" class="ogadwp">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="settings-wrapper">
					<div class="inside">
					<?php if (isset($message)) echo $message; ?>
						<table class="ogadwp-settings-options">
							<tr>
								<td colspan="2"><?php echo "<h2>" . __( "Permissions", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
							</tr>
							<tr>
								<td class="roles ogadwp-settings-title">
									<label for="access_back"><?php _e("Show stats to:", 'open-google-analytics-dashboard-for-wp' ); ?>
									</label>
								</td>
								<td class="ogadwp-settings-roles">
									<table>
										<tr>
										<?php if ( ! isset( $wp_roles ) ) : ?>
											<?php $wp_roles = new WP_Roles(); ?>
										<?php endif; ?>
										<?php $i = 0; ?>
										<?php foreach ( $wp_roles->role_names as $role => $name ) : ?>
											<?php if ( 'subscriber' != $role ) : ?>
												<?php $i++; ?>
											<td>
												<label>
													<input type="checkbox" name="options[access_back][]" value="<?php echo $role; ?>" <?php if ( in_array($role,$options['access_back']) || 'administrator' == $role ) echo 'checked="checked"'; if ( 'administrator' == $role ) echo 'disabled="disabled"';?> /> <?php echo $name; ?>
												</label>
											</td>
											<?php endif; ?>
											<?php if ( 0 == $i % 4 ) : ?>
										</tr>
										<tr>
											<?php endif; ?>
										<?php endforeach; ?>






									</table>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="ogadwp-settings-title">
									<div class="button-primary ogadwp-settings-switchoo">
										<input type="checkbox" name="options[switch_profile]" value="1" class="ogadwp-settings-switchoo-checkbox" id="switch_profile" <?php checked( $options['switch_profile'], 1 ); ?>>
										<label class="ogadwp-settings-switchoo-label" for="switch_profile">
											<div class="ogadwp-settings-switchoo-inner"></div>
											<div class="ogadwp-settings-switchoo-switch"></div>
										</label>
									</div>
									<div class="switch-desc"><?php _e ( "enable Switch View functionality", 'open-google-analytics-dashboard-for-wp' );?></div>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="ogadwp-settings-title">
									<div class="button-primary ogadwp-settings-switchoo">
										<input type="checkbox" name="options[backend_item_reports]" value="1" class="ogadwp-settings-switchoo-checkbox" id="backend_item_reports" <?php checked( $options['backend_item_reports'], 1 ); ?>>
										<label class="ogadwp-settings-switchoo-label" for="backend_item_reports">
											<div class="ogadwp-settings-switchoo-inner"></div>
											<div class="ogadwp-settings-switchoo-switch"></div>
										</label>
									</div>
									<div class="switch-desc"><?php _e ( "enable reports on Posts List and Pages List", 'open-google-analytics-dashboard-for-wp' );?></div>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="ogadwp-settings-title">
									<div class="button-primary ogadwp-settings-switchoo">
										<input type="checkbox" name="options[dashboard_widget]" value="1" class="ogadwp-settings-switchoo-checkbox" id="dashboard_widget" <?php checked( $options['dashboard_widget'], 1 ); ?>>
										<label class="ogadwp-settings-switchoo-label" for="dashboard_widget">
											<div class="ogadwp-settings-switchoo-inner"></div>
											<div class="ogadwp-settings-switchoo-switch"></div>
										</label>
									</div>
									<div class="switch-desc"><?php _e ( "enable the main Dashboard Widget", 'open-google-analytics-dashboard-for-wp' );?></div>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<hr><?php echo "<h2>" . __( "Real-Time Settings", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
							</tr>
							<?php if ( $options['user_api'] ) : ?>
							<tr>
								<td colspan="2" class="ogadwp-settings-title">
									<div class="button-primary ogadwp-settings-switchoo">
										<input type="checkbox" name="options[backend_realtime_report]" value="1" class="ogadwp-settings-switchoo-checkbox" id="backend_realtime_report" <?php checked( $options['backend_realtime_report'], 1 ); ?>>
										<label class="ogadwp-settings-switchoo-label" for="backend_realtime_report">
											<div class="ogadwp-settings-switchoo-inner"></div>
											<div class="ogadwp-settings-switchoo-switch"></div>
										</label>
									</div>
									<div class="switch-desc"><?php _e ( "enable Real-Time report (requires access to Real-Time Reporting API)", 'open-google-analytics-dashboard-for-wp' );?></div>
								</td>
							</tr>
							<?php endif; ?>
							<tr>
								<td colspan="2" class="ogadwp-settings-title"> <?php _e("Maximum number of pages to display on real-time tab:", 'open-google-analytics-dashboard-for-wp'); ?>
									<input type="number" name="options[ga_realtime_pages]" id="ga_realtime_pages" value="<?php echo (int)$options['ga_realtime_pages']; ?>" size="3">
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<hr><?php echo "<h2>" . __( "Location Settings", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
							</tr>
							<tr>
								<td colspan="2" class="ogadwp-settings-title">
									<?php echo __("Target Geo Map to country:", 'open-google-analytics-dashboard-for-wp'); ?>
									<input type="text" style="text-align: center;" name="options[ga_target_geomap]" value="<?php echo esc_attr($options['ga_target_geomap']); ?>" size="3">
								</td>
							</tr>
							<tr>
								<td colspan="2" class="ogadwp-settings-title">
									<?php echo __("Maps API Key:", 'open-google-analytics-dashboard-for-wp'); ?>
									<input type="text" style="text-align: center;" name="options[maps_api_key]" value="<?php echo esc_attr($options['maps_api_key']); ?>" size="50">
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<hr><?php echo "<h2>" . __( "404 Errors Report", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
							</tr>
							<tr>
								<td colspan="2" class="ogadwp-settings-title">
									<?php echo __("404 Page Title contains:", 'open-google-analytics-dashboard-for-wp'); ?>
									<input type="text" style="text-align: center;" name="options[pagetitle_404]" value="<?php echo esc_attr($options['pagetitle_404']); ?>" size="20">
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<hr>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="submit">
									<input type="submit" name="Submit" class="button button-primary" value="<?php _e('Save Changes', 'open-google-analytics-dashboard-for-wp' ) ?>" />
								</td>
							</tr>
						</table>
						<input type="hidden" name="options[ogadwp_hidden]" value="Y">
						<?php wp_nonce_field('ogadwp_form','ogadwp_security'); ?>






</form>
<?php
		self::output_sidebar();
	}

	public static function tracking_settings() {
		$ogadwp = OGADWP();

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$options = self::update_options( 'tracking' );
		if ( isset( $_POST['options']['ogadwp_hidden'] ) ) {
			$message = "<div class='updated' id='ogadwp-autodismiss'><p>" . __( "Settings saved.", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
			if ( ! ( isset( $_POST['ogadwp_security'] ) && wp_verify_nonce( $_POST['ogadwp_security'], 'ogadwp_form' ) ) ) {
				$message = "<div class='error' id='ogadwp-autodismiss'><p>" . __( "Cheating Huh?", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
			}
		}
		if ( ! $ogadwp->config->options['tableid_jail'] ) {
			$message = sprintf( '<div class="error"><p>%s</p></div>', sprintf( __( 'Something went wrong, check %1$s or %2$s.', 'open-google-analytics-dashboard-for-wp' ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'ogadwp_errors_debugging', false ), __( 'Errors & Debug', 'open-google-analytics-dashboard-for-wp' ) ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'ogadwp_settings', false ), __( 'authorize the plugin', 'open-google-analytics-dashboard-for-wp' ) ) ) );
		}
		?>
<form name="ogadwp_form" method="post" action="<?php  esc_url($_SERVER['REQUEST_URI']); ?>">
	<div class="wrap">
			<?php echo "<h2>" . __( "Google Analytics Tracking Code", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?>
	</div>
	<div id="poststuff" class="ogadwp">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="settings-wrapper">
					<div class="inside">
						<?php if ( 'universal' == $options['tracking_type'] ) :?>
						<?php $tabs = array( 'basic' => __( "Basic Settings", 'open-google-analytics-dashboard-for-wp' ), 'events' => __( "Events Tracking", 'open-google-analytics-dashboard-for-wp' ), 'custom' => __( "Custom Definitions", 'open-google-analytics-dashboard-for-wp' ), 'exclude' => __( "Exclude Tracking", 'open-google-analytics-dashboard-for-wp' ), 'advanced' => __( "Advanced Settings", 'open-google-analytics-dashboard-for-wp' ), 'integration' => __( "Integration", 'open-google-analytics-dashboard-for-wp' ) );?>
						<?php elseif ( 'tagmanager' == $options['tracking_type'] ) :?>
						<?php $tabs = array( 'basic' => __( "Basic Settings", 'open-google-analytics-dashboard-for-wp' ), 'tmdatalayervars' => __( "DataLayer Variables", 'open-google-analytics-dashboard-for-wp' ), 'exclude' => __( "Exclude Tracking", 'open-google-analytics-dashboard-for-wp' ), 'tmadvanced' =>  __( "Advanced Settings", 'open-google-analytics-dashboard-for-wp' ), 'tmintegration' => __( "Integration", 'open-google-analytics-dashboard-for-wp' ) );?>
						<?php else :?>
						<?php $tabs = array( 'basic' => __( "Basic Settings", 'open-google-analytics-dashboard-for-wp' ) );?>
						<?php endif; ?>
						<?php self::navigation_tabs( $tabs ); ?>
						<?php if ( isset( $message ) ) : ?>
							<?php echo $message; ?>
						<?php endif; ?>
						<div id="ogadwp-basic">
							<table class="ogadwp-settings-options">
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Tracking Settings", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="tracking_type"><?php _e("Tracking Type:", 'open-google-analytics-dashboard-for-wp' ); ?>
										</label>
									</td>
									<td>
										<select id="tracking_type" name="options[tracking_type]" onchange="this.form.submit()">
											<option value="universal" <?php selected( $options['tracking_type'], 'universal' ); ?>><?php _e("Analytics", 'open-google-analytics-dashboard-for-wp');?></option>
											<option value="tagmanager" <?php selected( $options['tracking_type'], 'tagmanager' ); ?>><?php _e("Tag Manager", 'open-google-analytics-dashboard-for-wp');?></option>
											<option value="disabled" <?php selected( $options['tracking_type'], 'disabled' ); ?>><?php _e("Disabled", 'open-google-analytics-dashboard-for-wp');?></option>
										</select>
									</td>
								</tr>
								<?php if ( 'universal' == $options['tracking_type'] ) : ?>
								<tr>
									<td class="ogadwp-settings-title"></td>
									<td>
										<?php $profile_info = OGADWP_Tools::get_selected_profile($ogadwp->config->options['ga_profiles_list'], $ogadwp->config->options['tableid_jail']); ?>
										<?php echo '<pre>' . __("View Name:", 'open-google-analytics-dashboard-for-wp') . "\t" . esc_html($profile_info[0]) . "<br />" . __("Tracking ID:", 'open-google-analytics-dashboard-for-wp') . "\t" . esc_html($profile_info[2]) . "<br />" . __("Default URL:", 'open-google-analytics-dashboard-for-wp') . "\t" . esc_html($profile_info[3]) . "<br />" . __("Time Zone:", 'open-google-analytics-dashboard-for-wp') . "\t" . esc_html($profile_info[5]) . '</pre>';?>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_with_gtag]" value="1" class="ogadwp-settings-switchoo-checkbox" id="ga_with_gtag" <?php checked( $options['ga_with_gtag'], 1 ); ?>>
											<label class="ogadwp-settings-switchoo-label" for="ga_with_gtag">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("use global site tag gtag.js (not recommended)", 'open-google-analytics-dashboard-for-wp' );?></div>
									</td>
								</tr>
								<?php elseif ( 'tagmanager' == $options['tracking_type'] ) : ?>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="tracking_type"><?php _e("Web Container ID:", 'open-google-analytics-dashboard-for-wp' ); ?>
										</label>
									</td>
									<td>
										<input type="text" name="options[web_containerid]" value="<?php echo esc_attr($options['web_containerid']); ?>" size="15">
									</td>
								</tr>
								<?php endif; ?>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="trackingcode_infooter"><?php _e("Code Placement:", 'open-google-analytics-dashboard-for-wp' ); ?>
										</label>
									</td>
									<td>
										<select id="trackingcode_infooter" name="options[trackingcode_infooter]">
											<option value="0" <?php selected( $options['trackingcode_infooter'], 0 ); ?>><?php _e("HTML Head", 'open-google-analytics-dashboard-for-wp');?></option>
											<option value="1" <?php selected( $options['trackingcode_infooter'], 1 ); ?>><?php _e("HTML Body", 'open-google-analytics-dashboard-for-wp');?></option>
										</select>
									</td>
								</tr>
							</table>
						</div>
						<div id="ogadwp-events">
							<table class="ogadwp-settings-options">
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Events Tracking", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_event_tracking]" value="1" class="ogadwp-settings-switchoo-checkbox" id="ga_event_tracking" <?php checked( $options['ga_event_tracking'], 1 ); ?>>
											<label class="ogadwp-settings-switchoo-label" for="ga_event_tracking">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("track downloads, mailto, telephone and outbound links", 'open-google-analytics-dashboard-for-wp' ); ?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_aff_tracking]" value="1" class="ogadwp-settings-switchoo-checkbox" id="ga_aff_tracking" <?php checked( $options['ga_aff_tracking'], 1 ); ?>>
											<label class="ogadwp-settings-switchoo-label" for="ga_aff_tracking">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("track affiliate links", 'open-google-analytics-dashboard-for-wp' ); ?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_hash_tracking]" value="1" class="ogadwp-settings-switchoo-checkbox" id="ga_hash_tracking" <?php checked( $options['ga_hash_tracking'], 1 ); ?>>
											<label class="ogadwp-settings-switchoo-label" for="ga_hash_tracking">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("track fragment identifiers, hashmarks (#) in URI links", 'open-google-analytics-dashboard-for-wp' ); ?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_formsubmit_tracking]" value="1" class="ogadwp-settings-switchoo-checkbox" id="ga_formsubmit_tracking" <?php checked( $options['ga_formsubmit_tracking'], 1 ); ?>>
											<label class="ogadwp-settings-switchoo-label" for="ga_formsubmit_tracking">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("track form submit actions", 'open-google-analytics-dashboard-for-wp' ); ?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_pagescrolldepth_tracking]" value="1" class="ogadwp-settings-switchoo-checkbox" id="ga_pagescrolldepth_tracking" <?php checked( $options['ga_pagescrolldepth_tracking'], 1 ); ?>>
											<label class="ogadwp-settings-switchoo-label" for="ga_pagescrolldepth_tracking">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("track page scrolling depth", 'open-google-analytics-dashboard-for-wp' ); ?></div>
									</td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="ga_event_downloads"><?php _e("Downloads Regex:", 'open-google-analytics-dashboard-for-wp'); ?>
										</label>
									</td>
									<td>
										<input type="text" id="ga_event_downloads" name="options[ga_event_downloads]" value="<?php echo esc_attr($options['ga_event_downloads']); ?>" size="50">
									</td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="ga_event_affiliates"><?php _e("Affiliates Regex:", 'open-google-analytics-dashboard-for-wp'); ?>
										</label>
									</td>
									<td>
										<input type="text" id="ga_event_affiliates" name="options[ga_event_affiliates]" value="<?php echo esc_attr($options['ga_event_affiliates']); ?>" size="50">
									</td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="trackingevents_infooter"><?php _e("Code Placement:", 'open-google-analytics-dashboard-for-wp' ); ?>
										</label>
									</td>
									<td>
										<select id="trackingevents_infooter" name="options[trackingevents_infooter]">
											<option value="0" <?php selected( $options['trackingevents_infooter'], 0 ); ?>><?php _e("HTML Head", 'open-google-analytics-dashboard-for-wp');?></option>
											<option value="1" <?php selected( $options['trackingevents_infooter'], 1 ); ?>><?php _e("HTML Body", 'open-google-analytics-dashboard-for-wp');?></option>
										</select>
									</td>
								</tr>
							</table>
						</div>
						<div id="ogadwp-custom">
							<table class="ogadwp-settings-options">
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Custom Dimensions", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="ga_author_dimindex"><?php _e("Authors:", 'open-google-analytics-dashboard-for-wp' ); ?>
										</label>
									</td>
									<td>
										<select id="ga_author_dimindex" name="options[ga_author_dimindex]">
										<?php for ($i=0;$i<21;$i++) : ?>
											<option value="<?php echo $i;?>" <?php selected( $options['ga_author_dimindex'], $i ); ?>><?php echo 0 == $i ?'Disabled':'dimension '.$i; ?></option>
										<?php endfor; ?>
										</select>
									</td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="ga_pubyear_dimindex"><?php _e("Publication Year:", 'open-google-analytics-dashboard-for-wp' ); ?>
										</label>
									</td>
									<td>
										<select id="ga_pubyear_dimindex" name="options[ga_pubyear_dimindex]">
										<?php for ($i=0;$i<21;$i++) : ?>
											<option value="<?php echo $i;?>" <?php selected( $options['ga_pubyear_dimindex'], $i ); ?>><?php echo 0 == $i ?'Disabled':'dimension '.$i; ?></option>
										<?php endfor; ?>
										</select>
									</td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="ga_pubyearmonth_dimindex"><?php _e("Publication Month:", 'open-google-analytics-dashboard-for-wp' ); ?>
										</label>
									</td>
									<td>
										<select id="ga_pubyearmonth_dimindex" name="options[ga_pubyearmonth_dimindex]">
										<?php for ($i=0;$i<21;$i++) : ?>
											<option value="<?php echo $i;?>" <?php selected( $options['ga_pubyearmonth_dimindex'], $i ); ?>><?php echo 0 == $i ?'Disabled':'dimension '.$i; ?></option>
										<?php endfor; ?>
										</select>
									</td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="ga_category_dimindex"><?php _e("Categories:", 'open-google-analytics-dashboard-for-wp' ); ?>
										</label>
									</td>
									<td>
										<select id="ga_category_dimindex" name="options[ga_category_dimindex]">
										<?php for ($i=0;$i<21;$i++) : ?>
											<option value="<?php echo $i;?>" <?php selected( $options['ga_category_dimindex'], $i ); ?>><?php echo 0 == $i ? 'Disabled':'dimension '.$i; ?></option>
										<?php endfor; ?>
										</select>
									</td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="ga_user_dimindex"><?php _e("User Type:", 'open-google-analytics-dashboard-for-wp' ); ?>
										</label>
									</td>
									<td>
										<select id="ga_user_dimindex" name="options[ga_user_dimindex]">
										<?php for ($i=0;$i<21;$i++) : ?>
											<option value="<?php echo $i;?>" <?php selected( $options['ga_user_dimindex'], $i ); ?>><?php echo 0 == $i ? 'Disabled':'dimension '.$i; ?></option>
										<?php endfor; ?>
										</select>
									</td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="ga_tag_dimindex"><?php _e("Tags:", 'open-google-analytics-dashboard-for-wp' ); ?>
										</label>
									</td>
									<td>
										<select id="ga_tag_dimindex" name="options[ga_tag_dimindex]">
										<?php for ($i=0;$i<21;$i++) : ?>
										<option value="<?php echo $i;?>" <?php selected( $options['ga_tag_dimindex'], $i ); ?>><?php echo 0 == $i ? 'Disabled':'dimension '.$i; ?></option>
										<?php endfor; ?>
										</select>
									</td>
								</tr>
							</table>
						</div>
						<div id="ogadwp-tmdatalayervars">
							<table class="ogadwp-settings-options">
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Main Variables", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="tm_author_var"><?php _e("Authors:", 'open-google-analytics-dashboard-for-wp' ); ?>
										</label>
									</td>
									<td>
										<select id="tm_author_var" name="options[tm_author_var]">
											<option value="1" <?php selected( $options['tm_author_var'], 1 ); ?>>ogadwpAuthor</option>
											<option value="0" <?php selected( $options['tm_author_var'], 0 ); ?>><?php _e( "Disabled", 'open-google-analytics-dashboard-for-wp' ); ?></option>
										</select>
									</td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="tm_pubyear_var"><?php _e("Publication Year:", 'open-google-analytics-dashboard-for-wp' ); ?>
										</label>
									</td>
									<td>
										<select id="tm_pubyear_var" name="options[tm_pubyear_var]">
											<option value="1" <?php selected( $options['tm_pubyear_var'], 1 ); ?>>ogadwpPublicationYear</option>
											<option value="0" <?php selected( $options['tm_pubyear_var'], 0 ); ?>><?php _e( "Disabled", 'open-google-analytics-dashboard-for-wp' ); ?></option>
										</select>
									</td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="tm_pubyearmonth_var"><?php _e("Publication Month:", 'open-google-analytics-dashboard-for-wp' ); ?>
										</label>
									</td>
									<td>
										<select id="tm_pubyearmonth_var" name="options[tm_pubyearmonth_var]">
											<option value="1" <?php selected( $options['tm_pubyearmonth_var'], 1 ); ?>>ogadwpPublicationYearMonth</option>
											<option value="0" <?php selected( $options['tm_pubyearmonth_var'], 0 ); ?>><?php _e( "Disabled", 'open-google-analytics-dashboard-for-wp' ); ?></option>
										</select>
									</td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="tm_category_var"><?php _e("Categories:", 'open-google-analytics-dashboard-for-wp' ); ?>
										</label>
									</td>
									<td>
										<select id="tm_category_var" name="options[tm_category_var]">
											<option value="1" <?php selected( $options['tm_category_var'], 1 ); ?>>ogadwpCategory</option>
											<option value="0" <?php selected( $options['tm_category_var'], 0 ); ?>><?php _e( "Disabled", 'open-google-analytics-dashboard-for-wp' ); ?></option>
										</select>
									</td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="tm_user_var"><?php _e("User Type:", 'open-google-analytics-dashboard-for-wp' ); ?>
										</label>
									</td>
									<td>
										<select id="tm_user_var" name="options[tm_user_var]">
											<option value="1" <?php selected( $options['tm_user_var'], 1 ); ?>>ogadwpUser</option>
											<option value="0" <?php selected( $options['tm_user_var'], 0 ); ?>><?php _e( "Disabled", 'open-google-analytics-dashboard-for-wp' ); ?></option>
										</select>
									</td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="tm_tag_var"><?php _e("Tags:", 'open-google-analytics-dashboard-for-wp' ); ?>
										</label>
									</td>
									<td>
										<select id="tm_tag_var" name="options[tm_tag_var]">
											<option value="1" <?php selected( $options['tm_tag_var'], 1 ); ?>>ogadwpTag</option>
											<option value="0" <?php selected( $options['tm_tag_var'], 0 ); ?>><?php _e( "Disabled", 'open-google-analytics-dashboard-for-wp' ); ?></option>
										</select>
									</td>
								</tr>
							</table>
						</div>
						<div id="ogadwp-advanced">
							<table class="ogadwp-settings-options">
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Advanced Tracking", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="ga_speed_samplerate"><?php _e("Speed Sample Rate:", 'open-google-analytics-dashboard-for-wp'); ?>
										</label>
									</td>
									<td>
										<input type="number" id="ga_speed_samplerate" name="options[ga_speed_samplerate]" value="<?php echo (int)($options['ga_speed_samplerate']); ?>" max="100" min="1">
										%
									</td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="ga_user_samplerate"><?php _e("User Sample Rate:", 'open-google-analytics-dashboard-for-wp'); ?>
										</label>
									</td>
									<td>
										<input type="number" id="ga_user_samplerate" name="options[ga_user_samplerate]" value="<?php echo (int)($options['ga_user_samplerate']); ?>" max="100" min="1">
										%
									</td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_anonymize_ip]" value="1" class="ogadwp-settings-switchoo-checkbox" id="ga_anonymize_ip" <?php checked( $options['ga_anonymize_ip'], 1 ); ?>>
											<label class="ogadwp-settings-switchoo-label" for="ga_anonymize_ip">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("anonymize IPs while tracking", 'open-google-analytics-dashboard-for-wp' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_optout]" value="1" class="ogadwp-settings-switchoo-checkbox" id="ga_optout" <?php checked( $options['ga_optout'], 1 ); ?>>
											<label class="ogadwp-settings-switchoo-label" for="ga_optout">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable support for user opt-out", 'open-google-analytics-dashboard-for-wp' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_dnt_optout]" value="1" class="ogadwp-settings-switchoo-checkbox" id="ga_dnt_optout" <?php checked( $options['ga_dnt_optout'], 1 ); ?>>
											<label class="ogadwp-settings-switchoo-label" for="ga_dnt_optout">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"> <?php _e( 'exclude tracking for users sending Do Not Track header', 'open-google-analytics-dashboard-for-wp' ); ?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_remarketing]" value="1" class="ogadwp-settings-switchoo-checkbox" id="ga_remarketing" <?php checked( $options['ga_remarketing'], 1 ); ?>>
											<label class="ogadwp-settings-switchoo-label" for="ga_remarketing">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable remarketing, demographics and interests reports", 'open-google-analytics-dashboard-for-wp' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_event_bouncerate]" value="1" class="ogadwp-settings-switchoo-checkbox" id="ga_event_bouncerate" <?php checked( $options['ga_event_bouncerate'], 1 ); ?>>
											<label class="ogadwp-settings-switchoo-label" for="ga_event_bouncerate">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("exclude events from bounce-rate and time on page calculation", 'open-google-analytics-dashboard-for-wp' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_enhanced_links]" value="1" class="ogadwp-settings-switchoo-checkbox" id="ga_enhanced_links" <?php checked( $options['ga_enhanced_links'], 1 ); ?>>
											<label class="ogadwp-settings-switchoo-label" for="ga_enhanced_links">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable enhanced link attribution", 'open-google-analytics-dashboard-for-wp' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_event_precision]" value="1" class="ogadwp-settings-switchoo-checkbox" id="ga_event_precision" <?php checked( $options['ga_event_precision'], 1 ); ?>>
											<label class="ogadwp-settings-switchoo-label" for="ga_event_precision">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("use hitCallback to increase event tracking accuracy", 'open-google-analytics-dashboard-for-wp' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_force_ssl]" value="1" class="ogadwp-settings-switchoo-checkbox" id="ga_force_ssl" <?php checked( $options['ga_force_ssl'] || $options['ga_with_gtag'], 1 ); ?>  <?php disabled( $options['ga_with_gtag'], true );?>>
											<label class="ogadwp-settings-switchoo-label" for="ga_force_ssl">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable Force SSL", 'open-google-analytics-dashboard-for-wp' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Cross-domain Tracking", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_crossdomain_tracking]" value="1" class="ogadwp-settings-switchoo-checkbox" id="ga_crossdomain_tracking" <?php checked( $options['ga_crossdomain_tracking'], 1 ); ?>>
											<label class="ogadwp-settings-switchoo-label" for="ga_crossdomain_tracking">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable cross domain tracking", 'open-google-analytics-dashboard-for-wp' ); ?></div>
									</td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="ga_crossdomain_list"><?php _e("Cross Domains:", 'open-google-analytics-dashboard-for-wp'); ?>
										</label>
									</td>
									<td>
										<input type="text" id="ga_crossdomain_list" name="options[ga_crossdomain_list]" value="<?php echo esc_attr($options['ga_crossdomain_list']); ?>" size="50">
									</td>
								</tr>
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Cookie Customization", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="ga_cookiedomain"><?php _e("Cookie Domain:", 'open-google-analytics-dashboard-for-wp'); ?>
										</label>
									</td>
									<td>
										<input type="text" id="ga_cookiedomain" name="options[ga_cookiedomain]" value="<?php echo esc_attr($options['ga_cookiedomain']); ?>" size="50">
									</td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="ga_cookiename"><?php _e("Cookie Name:", 'open-google-analytics-dashboard-for-wp'); ?>
										</label>
									</td>
									<td>
										<input type="text" id="ga_cookiename" name="options[ga_cookiename]" value="<?php echo esc_attr($options['ga_cookiename']); ?>" size="50">
									</td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="ga_cookieexpires"><?php _e("Cookie Expires:", 'open-google-analytics-dashboard-for-wp'); ?>
										</label>
									</td>
									<td>
										<input type="text" id="ga_cookieexpires" name="options[ga_cookieexpires]" value="<?php echo esc_attr($options['ga_cookieexpires']); ?>" size="10">
										<?php _e("seconds", 'open-google-analytics-dashboard-for-wp' ); ?>
									</td>
								</tr>
							</table>
						</div>
						<div id="ogadwp-integration">
							<table class="ogadwp-settings-options">
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Accelerated Mobile Pages (AMP)", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[amp_tracking_analytics]" value="1" class="ogadwp-settings-switchoo-checkbox" id="amp_tracking_analytics" <?php checked( $options['amp_tracking_analytics'], 1 ); ?>>
											<label class="ogadwp-settings-switchoo-label" for="amp_tracking_analytics">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable tracking for Accelerated Mobile Pages (AMP)", 'open-google-analytics-dashboard-for-wp' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[amp_tracking_clientidapi]" value="1" class="ogadwp-settings-switchoo-checkbox" id="amp_tracking_clientidapi" <?php checked( $options['amp_tracking_clientidapi'] && !$options['ga_with_gtag'], 1 ); ?> <?php disabled( $options['ga_with_gtag'], true );?>>
											<label class="ogadwp-settings-switchoo-label" for="amp_tracking_clientidapi">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable Google AMP Client Id API", 'open-google-analytics-dashboard-for-wp' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Ecommerce", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="tracking_type"><?php _e("Ecommerce Tracking:", 'open-google-analytics-dashboard-for-wp' ); ?>
										</label>
									</td>
									<td>
										<select id="ecommerce_mode" name="options[ecommerce_mode]" <?php disabled( $options['ga_with_gtag'], true );?>>
											<option value="disabled" <?php selected( $options['ecommerce_mode'], 'disabled' ); ?>><?php _e("Disabled", 'open-google-analytics-dashboard-for-wp');?></option>
											<option value="standard" <?php selected( $options['ecommerce_mode'], 'standard' ); ?>><?php _e("Ecommerce Plugin", 'open-google-analytics-dashboard-for-wp');?></option>
											<option value="enhanced" <?php selected( $options['ecommerce_mode'], 'enhanced' ); selected( $options['ga_with_gtag'], true );?>><?php _e("Enhanced Ecommerce Plugin", 'open-google-analytics-dashboard-for-wp');?></option>
										</select>
									</td>
								</tr>
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Optimize", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[optimize_tracking]" value="1" class="ogadwp-settings-switchoo-checkbox" id="optimize_tracking" <?php checked( $options['optimize_tracking'], 1 ); ?>>
											<label class="ogadwp-settings-switchoo-label" for="optimize_tracking">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable Optimize tracking", 'open-google-analytics-dashboard-for-wp' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[optimize_pagehiding]" value="1" class="ogadwp-settings-switchoo-checkbox" id="optimize_pagehiding" <?php checked( $options['optimize_pagehiding'], 1 ); ?>>
											<label class="ogadwp-settings-switchoo-label" for="optimize_pagehiding">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable Page Hiding support", 'open-google-analytics-dashboard-for-wp' );?></div>
									</td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="tracking_type"><?php _e("Container ID:", 'open-google-analytics-dashboard-for-wp' ); ?>
										</label>
									</td>
									<td>
										<input type="text" name="options[optimize_containerid]" value="<?php echo esc_attr($options['optimize_containerid']); ?>" size="15">
									</td>
								</tr>
							</table>
						</div>
						<div id="ogadwp-tmadvanced">
							<table class="ogadwp-settings-options">
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Advanced Tracking", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[tm_optout]" value="1" class="ogadwp-settings-switchoo-checkbox" id="tm_optout" <?php checked( $options['tm_optout'], 1 ); ?>>
											<label class="ogadwp-settings-switchoo-label" for="tm_optout">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable support for user opt-out", 'open-google-analytics-dashboard-for-wp' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[tm_dnt_optout]" value="1" class="ogadwp-settings-switchoo-checkbox" id="tm_dnt_optout" <?php checked( $options['tm_dnt_optout'], 1 ); ?>>
											<label class="ogadwp-settings-switchoo-label" for="tm_dnt_optout">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"> <?php _e( 'exclude tracking for users sending Do Not Track header', 'open-google-analytics-dashboard-for-wp' ); ?></div>
									</td>
								</tr>
							</table>
						</div>
						<div id="ogadwp-tmintegration">
							<table class="ogadwp-settings-options">
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Accelerated Mobile Pages (AMP)", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td colspan="2" class="ogadwp-settings-title">
										<div class="button-primary ogadwp-settings-switchoo">
											<input type="checkbox" name="options[amp_tracking_tagmanager]" value="1" class="ogadwp-settings-switchoo-checkbox" id="amp_tracking_tagmanager" <?php checked( $options['amp_tracking_tagmanager'], 1 ); ?>>
											<label class="ogadwp-settings-switchoo-label" for="amp_tracking_tagmanager">
												<div class="ogadwp-settings-switchoo-inner"></div>
												<div class="ogadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable tracking for Accelerated Mobile Pages (AMP)", 'open-google-analytics-dashboard-for-wp' );?></div>
									</td>
								</tr>
								<tr>
									<td class="ogadwp-settings-title">
										<label for="tracking_type"><?php _e("AMP Container ID:", 'open-google-analytics-dashboard-for-wp' ); ?>
										</label>
									</td>
									<td>
										<input type="text" name="options[amp_containerid]" value="<?php echo esc_attr($options['amp_containerid']); ?>" size="15">
									</td>
								</tr>
							</table>
						</div>
						<div id="ogadwp-exclude">
							<table class="ogadwp-settings-options">
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Exclude Tracking", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td class="roles ogadwp-settings-title">
										<label for="track_exclude"><?php _e("Exclude tracking for:", 'open-google-analytics-dashboard-for-wp' ); ?></label>
									</td>
									<td class="ogadwp-settings-roles">
										<table>
											<tr>
										<?php if ( ! isset( $wp_roles ) ) : ?>
											<?php $wp_roles = new WP_Roles(); ?>
										<?php endif; ?>
										<?php $i = 0; ?>
										<?php foreach ( $wp_roles->role_names as $role => $name ) : ?>
											<?php if ( 'subscriber' != $role ) : ?>
												<?php $i++; ?>
											<td>
													<label>
														<input type="checkbox" name="options[track_exclude][]" value="<?php echo $role; ?>" <?php if (in_array($role,$options['track_exclude'])) echo 'checked="checked"'; ?> /> <?php echo $name; ?>
											</label>
												</td>
											<?php endif; ?>
											<?php if ( 0 == $i % 4 ) : ?>
										 	</tr>
											<tr>
											<?php endif; ?>
										<?php endforeach; ?>
										</table>
									</td>
								</tr>
							</table>
						</div>
						<table class="ogadwp-settings-options">
							<tr>
								<td colspan="2">
									<hr>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="submit">
									<input type="submit" name="Submit" class="button button-primary" value="<?php _e('Save Changes', 'open-google-analytics-dashboard-for-wp' ) ?>" />
								</td>
							</tr>
						</table>
						<input type="hidden" name="options[ogadwp_hidden]" value="Y">
						<?php wp_nonce_field('ogadwp_form','ogadwp_security'); ?>






</form>
<?php
		self::output_sidebar();
	}

	public static function errors_debugging() {

		$ogadwp = OGADWP();

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$anonim = OGADWP_Tools::anonymize_options( $ogadwp->config->options );

		$options = self::update_options( 'frontend' );
		if ( ! $ogadwp->config->options['tableid_jail'] || ! $ogadwp->config->options['token'] ) {
			$message = sprintf( '<div class="error"><p>%s</p></div>', sprintf( __( 'Something went wrong, check %1$s or %2$s.', 'open-google-analytics-dashboard-for-wp' ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'ogadwp_errors_debugging', false ), __( 'Errors & Debug', 'open-google-analytics-dashboard-for-wp' ) ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'ogadwp_settings', false ), __( 'authorize the plugin', 'open-google-analytics-dashboard-for-wp' ) ) ) );
		}
		?>
<div class="wrap">
		<?php echo "<h2>" . __( "Google Analytics Errors & Debugging", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?>
</div>
<div id="poststuff" class="ogadwp">
	<div id="post-body" class="metabox-holder columns-2">
		<div id="post-body-content">
			<div class="settings-wrapper">
				<div class="inside">
						<?php if (isset($message)) echo $message; ?>
						<?php $tabs = array( 'errors' => __( "Errors & Details", 'open-google-analytics-dashboard-for-wp' ), 'config' => __( "Plugin Settings", 'open-google-analytics-dashboard-for-wp' ), 'sysinfo' => __( "System", 'open-google-analytics-dashboard-for-wp' ) ); ?>
						<?php self::navigation_tabs( $tabs ); ?>
						<div id="ogadwp-errors">
						<table class="ogadwp-settings-logdata">
							<tr>
								<td>
									<?php echo "<h2>" . __( "Error Details", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?>
								</td>
							</tr>
							<tr>
								<td>
									<?php $errors_count = OGADWP_Tools::get_cache( 'errors_count' ); ?>
									<pre class="ogadwp-settings-logdata"><?php echo '<span>' . __("Count: ", 'open-google-analytics-dashboard-for-wp') . '</span>' . (int)$errors_count;?></pre>
									<?php $errors = print_r( OGADWP_Tools::get_cache( 'last_error' ), true ) ? esc_html( print_r( OGADWP_Tools::get_cache( 'last_error' ), true ) ) : ''; ?>
									<?php $errors = str_replace( 'Deconf_', 'Google_', $errors); ?>
									<pre class="ogadwp-settings-logdata"><?php echo '<span>' . __("Last Error: ", 'open-google-analytics-dashboard-for-wp') . '</span>' . "\n" . $errors;?></pre>
									<pre class="ogadwp-settings-logdata"><?php echo '<span>' . __("GAPI Error: ", 'open-google-analytics-dashboard-for-wp') . '</span>'; echo "\n" . esc_html( print_r( OGADWP_Tools::get_cache( 'gapi_errors' ), true ) ) ?></pre>
									<br />
									<hr>
								</td>
							</tr>
							<tr>
								<td>
									<?php echo "<h2>" . __( "Sampled Data", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?>
								</td>
							</tr>
							<tr>
								<td>
									<?php $sampling = OGADWP_TOOLS::get_cache( 'sampleddata' ); ?>
									<?php if ( $sampling ) :?>
									<?php printf( __( "Last Detected on %s.", 'open-google-analytics-dashboard-for-wp' ), '<strong>'. $sampling['date'] . '</strong>' );?>
									<br />
									<?php printf( __( "The report was based on %s of sessions.", 'open-google-analytics-dashboard-for-wp' ), '<strong>'. $sampling['percent'] . '</strong>' );?>
									<br />
									<?php printf( __( "Sessions ratio: %s.", 'open-google-analytics-dashboard-for-wp' ), '<strong>'. $sampling['sessions'] . '</strong>' ); ?>
									<?php else :?>
									<?php _e( "None", 'open-google-analytics-dashboard-for-wp' ); ?>
									<?php endif;?>
								</td>
							</tr>
						</table>
					</div>
					<div id="ogadwp-config">
						<table class="ogadwp-settings-options">
							<tr>
								<td><?php echo "<h2>" . __( "Plugin Configuration", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
							</tr>
							<tr>
								<td>
									<pre class="ogadwp-settings-logdata"><?php echo esc_html(print_r($anonim, true));?></pre>
									<br />
									<hr>
								</td>
							</tr>
						</table>
					</div>
					<div id="ogadwp-sysinfo">
						<table class="ogadwp-settings-options">
							<tr>
								<td><?php echo "<h2>" . __( "System Information", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
							</tr>
							<tr>
								<td>
									<pre class="ogadwp-settings-logdata"><?php echo esc_html(OGADWP_Tools::system_info());?></pre>
									<br />
									<hr>
								</td>
							</tr>
						</table>
					</div>
	<?php
		self::output_sidebar();
	}

	public static function general_settings() {
		$ogadwp = OGADWP();
//Intel_Df::watchdog('OGADWP_Settings::general_settings()', '');

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$options = self::update_options( 'general' );
		printf( '<div id="gapi-warning" class="updated"><p>%1$s <a href="https://deconf.com/open-google-analytics-dashboard-wordpress/?utm_source=ogadwp_config&utm_medium=link&utm_content=general_screen&utm_campaign=ogadwp">%2$s</a></p></div>', __( 'Loading the required libraries. If this results in a blank screen or a fatal error, try this solution:', 'open-google-analytics-dashboard-for-wp' ), __( 'Library conflicts between WordPress plugins', 'open-google-analytics-dashboard-for-wp' ) );
		if ( null === $ogadwp->gapi_controller ) {
			$ogadwp->gapi_controller = new OGADWP_GAPI_Controller();
		}
		echo '<script type="text/javascript">jQuery("#gapi-warning").hide()</script>';
		if ( isset( $_POST['ogadwp_access_code'] ) ) {
			if ( 1 == ! stripos( 'x' . $_POST['ogadwp_access_code'], 'UA-', 1 ) && $_POST['ogadwp_access_code'] != get_option( 'ogadwp_redeemed_code' ) ) {
				try {
					$ogadwp_access_code = $_POST['ogadwp_access_code'];
					update_option( 'ogadwp_redeemed_code', $ogadwp_access_code );
					OGADWP_Tools::delete_cache( 'gapi_errors' );
					OGADWP_Tools::delete_cache( 'last_error' );
//Intel_Df::watchdog('OGADWP_Settings::general_settings $_POST', print_r($_POST, 1));
					$ogadwp->gapi_controller->client->authenticate( $_POST['ogadwp_access_code'] );
					$ogadwp->config->options['token'] = $ogadwp->gapi_controller->client->getAccessToken();
					$ogadwp->config->options['automatic_updates_minorversion'] = 1;
					$ogadwp->config->set_plugin_options();
					$options = self::update_options( 'general' );
					$message = "<div class='updated' id='ogadwp-autodismiss'><p>" . __( "Plugin authorization succeeded.", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
					if ( $ogadwp->config->options['token'] && $ogadwp->gapi_controller->client->getAccessToken() ) {
						$profiles = $ogadwp->gapi_controller->refresh_profiles();
						if ( is_array ( $profiles ) && ! empty( $profiles ) ) {
							$ogadwp->config->options['ga_profiles_list'] = $profiles;
							if ( ! $ogadwp->config->options['tableid_jail'] ) {
								$profile = OGADWP_Tools::guess_default_domain( $profiles );
								$ogadwp->config->options['tableid_jail'] = $profile;
							}
							$ogadwp->config->set_plugin_options();
							$options = self::update_options( 'general' );
						}
					}
				} catch ( Deconf_IO_Exception $e ) {
					$timeout = $ogadwp->gapi_controller->get_timeouts( 'midnight' );
					OGADWP_Tools::set_error( $e, $timeout );
				} catch ( Deconf_Service_Exception $e ) {
					$timeout = $ogadwp->gapi_controller->get_timeouts( 'midnight' );
					OGADWP_Tools::set_error( $e, $timeout );
				} catch ( Exception $e ) {
					$timeout = $ogadwp->gapi_controller->get_timeouts( 'midnight' );
					OGADWP_Tools::set_error( $e, $timeout );
					$ogadwp->gapi_controller->reset_token();
				}
			} else {
				if ( 1 == stripos( 'x' . $_POST['ogadwp_access_code'], 'UA-', 1 ) ) {
					$message = "<div class='error' id='ogadwp-autodismiss'><p>" . __( "The access code is <strong>not</strong> your <strong>Tracking ID</strong> (UA-XXXXX-X) <strong>nor</strong> your <strong>email address</strong>!", 'open-google-analytics-dashboard-for-wp' ) . ".</p></div>";
				} else {
					$message = "<div class='error' id='ogadwp-autodismiss'><p>" . __( "You can only use the access code <strong>once</strong>, please generate a <strong>new access</strong> code following the instructions!", 'open-google-analytics-dashboard-for-wp' ) . ".</p></div>";
				}
			}
		}
		if ( isset( $_POST['Clear'] ) ) {
			if ( isset( $_POST['ogadwp_security'] ) && wp_verify_nonce( $_POST['ogadwp_security'], 'ogadwp_form' ) ) {
				OGADWP_Tools::clear_cache();
				$message = "<div class='updated' id='ogadwp-autodismiss'><p>" . __( "Cleared Cache.", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
			} else {
				$message = "<div class='error' id='ogadwp-autodismiss'><p>" . __( "Cheating Huh?", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
			}
		}
		if ( isset( $_POST['Reset'] ) ) {
			if ( isset( $_POST['ogadwp_security'] ) && wp_verify_nonce( $_POST['ogadwp_security'], 'ogadwp_form' ) ) {
				$ogadwp->gapi_controller->reset_token();
				OGADWP_Tools::clear_cache();
				$message = "<div class='updated' id='ogadwp-autodismiss'><p>" . __( "Token Reseted and Revoked.", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
				$options = self::update_options( 'Reset' );
			} else {
				$message = "<div class='error' id='ogadwp-autodismiss'><p>" . __( "Cheating Huh?", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
			}
		}
		if ( isset( $_POST['Reset_Err'] ) ) {
			if ( isset( $_POST['ogadwp_security'] ) && wp_verify_nonce( $_POST['ogadwp_security'], 'ogadwp_form' ) ) {

				if ( OGADWP_Tools::get_cache( 'gapi_errors' ) || OGADWP_Tools::get_cache( 'last_error' ) ) {

					$info = OGADWP_Tools::system_info();
					$info .= 'OGADWP Version: ' . OGADWP_CURRENT_VERSION;

					$sep = "\n---------------------------\n";
					$error_report = OGADWP_Tools::get_cache( 'last_error' );
					$error_report .= $sep . print_r( OGADWP_Tools::get_cache( 'gapi_errors' ), true );
					$error_report .= $sep . OGADWP_Tools::get_cache( 'errors_count' );
					$error_report .= $sep . $info;

					$error_report = urldecode( $error_report );

					$url = OGADWP_ENDPOINT_URL . 'ogadwp-report.php';
					/* @formatter:off */
					$response = wp_remote_post( $url, array(
							'method' => 'POST',
							'timeout' => 45,
							'redirection' => 5,
							'httpversion' => '1.0',
							'blocking' => true,
							'headers' => array(),
							'body' => array( 'error_report' => $error_report ),
							'cookies' => array()
						)
					);
				}

				/* @formatter:on */
				OGADWP_Tools::delete_cache( 'last_error' );
				OGADWP_Tools::delete_cache( 'gapi_errors' );
				delete_option( 'ogadwp_got_updated' );
				$message = "<div class='updated' id='ogadwp-autodismiss'><p>" . __( "All errors reseted.", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
			} else {
				$message = "<div class='error' id='ogadwp-autodismiss'><p>" . __( "Cheating Huh?", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
			}
		}
		if ( isset( $_POST['options']['ogadwp_hidden'] ) && ! isset( $_POST['Clear'] ) && ! isset( $_POST['Reset'] ) && ! isset( $_POST['Reset_Err'] ) ) {
			$message = "<div class='updated' id='ogadwp-autodismiss'><p>" . __( "Settings saved.", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
			if ( ! ( isset( $_POST['ogadwp_security'] ) && wp_verify_nonce( $_POST['ogadwp_security'], 'ogadwp_form' ) ) ) {
				$message = "<div class='error' id='ogadwp-autodismiss'><p>" . __( "Cheating Huh?", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
			}
		}
		if ( isset( $_POST['Hide'] ) ) {
			if ( isset( $_POST['ogadwp_security'] ) && wp_verify_nonce( $_POST['ogadwp_security'], 'ogadwp_form' ) ) {
				$message = "<div class='updated' id='ogadwp-action'><p>" . __( "All other domains/properties were removed.", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
				$lock_profile = OGADWP_Tools::get_selected_profile( $ogadwp->config->options['ga_profiles_list'], $ogadwp->config->options['tableid_jail'] );
				$ogadwp->config->options['ga_profiles_list'] = array( $lock_profile );
				$options = self::update_options( 'general' );
			} else {
				$message = "<div class='error' id='ogadwp-autodismiss'><p>" . __( "Cheating Huh?", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
			}
		}
		?>
	<div class="wrap">
	<?php echo "<h2>" . __( "Google Analytics Settings", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?>
					<hr>
					</div>
					<div id="poststuff" class="ogadwp">
						<div id="post-body" class="metabox-holder columns-2">
							<div id="post-body-content">
								<div class="settings-wrapper">
									<div class="inside">
										<?php if ( $ogadwp->gapi_controller->gapi_errors_handler() || OGADWP_Tools::get_cache( 'last_error' ) ) : ?>
													<?php $message = sprintf( '<div class="error"><p>%s</p></div>', sprintf( __( 'Something went wrong, check %1$s or %2$s.', 'open-google-analytics-dashboard-for-wp' ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'ogadwp_errors_debugging', false ), __( 'Errors & Debug', 'open-google-analytics-dashboard-for-wp' ) ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'ogadwp_settings', false ), __( 'authorize the plugin', 'open-google-analytics-dashboard-for-wp' ) ) ) );?>
										<?php endif;?>
										<?php if ( isset( $_POST['Authorize'] ) ) : ?>
											<?php OGADWP_Tools::clear_cache(); ?>
											<?php $ogadwp->gapi_controller->token_request(); ?>
											<div class="updated">
											<p><?php _e( "Use the red link (see below) to generate and get your access code! You need to generate a new code each time you authorize!", 'open-google-analytics-dashboard-for-wp' )?></p>
										</div>
										<?php else : ?>
										<?php if ( isset( $message ) ) :?>
											<?php echo $message;?>
										<?php endif; ?>
										<form name="ogadwp_form" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
											<input type="hidden" name="options[ogadwp_hidden]" value="Y">
											<?php wp_nonce_field('ogadwp_form','ogadwp_security'); ?>
											<table class="ogadwp-settings-options">
												<tr>
													<td colspan="2">
														<?php echo "<h2>" . __( "Plugin Authorization", 'open-google-analytics-dashboard-for-wp' ) . "</h2>";?>
													</td>
												</tr>
												<tr>
													<td colspan="2" class="ogadwp-settings-info">
														<?php printf(__('You need to create a %1$s and watch this %2$s before proceeding to authorization.', 'open-google-analytics-dashboard-for-wp'), sprintf('<a href="%1$s" target="_blank">%2$s</a>', 'https://deconf.com/creating-a-google-analytics-account/?utm_source=ogadwp_config&utm_medium=link&utm_content=top_tutorial&utm_campaign=ogadwp', __("free analytics account", 'open-google-analytics-dashboard-for-wp')), sprintf('<a href="%1$s" target="_blank">%2$s</a>', 'https://deconf.com/open-google-analytics-dashboard-wordpress/?utm_source=ogadwp_config&utm_medium=link&utm_content=top_video&utm_campaign=ogadwp', __("video tutorial", 'open-google-analytics-dashboard-for-wp')));?>
													</td>
												</tr>
												  <?php if (! $options['token'] || ($options['user_api']  && ! $options['network_mode'])) : ?>
												<tr>
													<td colspan="2" class="ogadwp-settings-info">
														<input name="options[user_api]" type="checkbox" id="user_api" value="1" <?php checked( $options['user_api'], 1 ); ?> onchange="this.form.submit()" <?php echo ($options['network_mode'])?'disabled="disabled"':''; ?> /><?php echo " ".__("developer mode (requires advanced API knowledge)", 'open-google-analytics-dashboard-for-wp' );?>
													</td>
												</tr>
												  <?php endif; ?>
												  <?php if ($options['user_api']  && ! $options['network_mode']) : ?>
												<tr>
													<td class="ogadwp-settings-title">
														<label for="options[client_id]"><?php _e("Client ID:", 'open-google-analytics-dashboard-for-wp'); ?></label>
													</td>
													<td>
														<input type="text" name="options[client_id]" value="<?php echo esc_attr($options['client_id']); ?>" size="40" required="required">
													</td>
												</tr>
												<tr>
													<td class="ogadwp-settings-title">
														<label for="options[client_secret]"><?php _e("Client Secret:", 'open-google-analytics-dashboard-for-wp'); ?></label>
													</td>
													<td>
														<input type="text" name="options[client_secret]" value="<?php echo esc_attr($options['client_secret']); ?>" size="40" required="required">
														<input type="hidden" name="options[ogadwp_hidden]" value="Y">
														<?php wp_nonce_field('ogadwp_form','ogadwp_security'); ?>
													</td>
												</tr>
												  <?php endif; ?>
												  <?php if ( $options['token'] ) : ?>
												<tr>
													<td colspan="2">
														<input type="submit" name="Reset" class="button button-secondary" value="<?php _e( "Clear Authorization", 'open-google-analytics-dashboard-for-wp' ); ?>" <?php echo $options['network_mode']?'disabled="disabled"':''; ?> />
														<input type="submit" name="Clear" class="button button-secondary" value="<?php _e( "Clear Cache", 'open-google-analytics-dashboard-for-wp' ); ?>" />
														<input type="submit" name="Reset_Err" class="button button-secondary" value="<?php _e( "Report & Reset Errors", 'open-google-analytics-dashboard-for-wp' ); ?>" />
													</td>
												</tr>
												<tr>
													<td colspan="2">
														<hr>
													</td>
												</tr>
												<tr>
													<td colspan="2"><?php echo "<h2>" . __( "General Settings", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
												</tr>
												<tr>
													<td class="ogadwp-settings-title">
														<label for="tableid_jail"><?php _e("Select View:", 'open-google-analytics-dashboard-for-wp' ); ?></label>
													</td>
													<td>
														<select id="tableid_jail" <?php disabled(empty($options['ga_profiles_list']) || 1 == count($options['ga_profiles_list']), true); ?> name="options[tableid_jail]">
															<?php if ( ! empty( $options['ga_profiles_list'] ) ) : ?>
																	<?php foreach ( $options['ga_profiles_list'] as $items ) : ?>
																		<?php if ( $items[3] ) : ?>
																			<option value="<?php echo esc_attr( $items[1] ); ?>" <?php selected( $items[1], $options['tableid_jail'] ); ?> title="<?php _e( "View Name:", 'open-google-analytics-dashboard-for-wp' ); ?> <?php echo esc_attr( $items[0] ); ?>">
																				<?php echo esc_html( OGADWP_Tools::strip_protocol( $items[3] ) )?> &#8658; <?php echo esc_attr( $items[0] ); ?>
																			</option>
																		<?php endif; ?>
																	<?php endforeach; ?>
															<?php else : ?>
																	<option value=""><?php _e( "Property not found", 'open-google-analytics-dashboard-for-wp' ); ?></option>
															<?php endif; ?>
														</select>
														<?php if ( count( $options['ga_profiles_list'] ) > 1 ) : ?>
														&nbsp;<input type="submit" name="Hide" class="button button-secondary" value="<?php _e( "Lock Selection", 'open-google-analytics-dashboard-for-wp' ); ?>" />
														<?php endif; ?>
													 </td>
												</tr>
												<?php if ( $options['tableid_jail'] ) :	?>
												<tr>
													<td class="ogadwp-settings-title"></td>
													<td>
													<?php $profile_info = OGADWP_Tools::get_selected_profile( $ogadwp->config->options['ga_profiles_list'], $ogadwp->config->options['tableid_jail'] ); ?>
														<pre><?php echo __( "View Name:", 'open-google-analytics-dashboard-for-wp' ) . "\t" . esc_html( $profile_info[0] ) . "<br />" . __( "Tracking ID:", 'open-google-analytics-dashboard-for-wp' ) . "\t" . esc_html( $profile_info[2] ) . "<br />" . __( "Default URL:", 'open-google-analytics-dashboard-for-wp' ) . "\t" . esc_html( $profile_info[3] ) . "<br />" . __( "Time Zone:", 'open-google-analytics-dashboard-for-wp' ) . "\t" . esc_html( $profile_info[5] );?></pre>
													</td>
												</tr>
												<?php endif; ?>
												 <tr>
													<td class="ogadwp-settings-title">
														<label for="theme_color"><?php _e("Theme Color:", 'open-google-analytics-dashboard-for-wp' ); ?></label>
													</td>
													<td>
														<input type="text" id="theme_color" class="theme_color" name="options[theme_color]" value="<?php echo esc_attr($options['theme_color']); ?>" size="10">
													</td>
												</tr>
												<tr>
													<td colspan="2">
														<hr>
													</td>
												</tr>
												<?php if ( !is_multisite()) :?>
												<tr>
													<td colspan="2"><?php echo "<h2>" . __( "Automatic Updates", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
												</tr>
												<tr>
													<td colspan="2" class="ogadwp-settings-title">
														<div class="button-primary ogadwp-settings-switchoo">
															<input type="checkbox" name="options[automatic_updates_minorversion]" value="1" class="ogadwp-settings-switchoo-checkbox" id="automatic_updates_minorversion" <?php checked( $options['automatic_updates_minorversion'], 1 ); ?>>
															<label class="ogadwp-settings-switchoo-label" for="automatic_updates_minorversion">
																<div class="ogadwp-settings-switchoo-inner"></div>
																<div class="ogadwp-settings-switchoo-switch"></div>
															</label>
														</div>
														<div class="switch-desc"><?php echo " ".__( "automatic updates for minor versions (security and maintenance releases only)", 'open-google-analytics-dashboard-for-wp' );?></div>
													</td>
												</tr>
												<tr>
													<td colspan="2">
														<hr>
													</td>
												</tr>
												<?php endif; ?>
												<tr>
													<td colspan="2" class="submit">
														<input type="submit" name="Submit" class="button button-primary" value="<?php _e('Save Changes', 'open-google-analytics-dashboard-for-wp' ) ?>" />
													</td>
												</tr>
												<?php else : ?>
												<tr>
													<td colspan="2">
														<hr>
													</td>
												</tr>
												<tr>
													<td colspan="2">
														<input type="submit" name="Authorize" class="button button-secondary" id="authorize" value="<?php _e( "Authorize Plugin", 'open-google-analytics-dashboard-for-wp' ); ?>" <?php echo $options['network_mode']?'disabled="disabled"':''; ?> />
														<input type="submit" name="Clear" class="button button-secondary" value="<?php _e( "Clear Cache", 'open-google-analytics-dashboard-for-wp' ); ?>" />
													</td>
												</tr>
												<tr>
													<td colspan="2">
														<hr>
													</td>
												</tr>
											</table>
										</form>
				<?php self::output_sidebar(); ?>
				<?php return; ?>
			<?php endif; ?>
											</table>
										</form>
			<?php endif; ?>
			<?php

		self::output_sidebar();
	}

	// Network Settings
	public static function general_settings_network() {
		$ogadwp = OGADWP();

		if ( ! current_user_can( 'manage_network_options' ) ) {
			return;
		}
		$options = self::update_options( 'network' );
		/*
		 * Include GAPI
		 */
		echo '<div id="gapi-warning" class="updated"><p>' . __( 'Loading the required libraries. If this results in a blank screen or a fatal error, try this solution:', 'open-google-analytics-dashboard-for-wp' ) . ' <a href="https://deconf.com/open-google-analytics-dashboard-wordpress/?utm_source=ogadwp_config&utm_medium=link&utm_content=general_screen&utm_campaign=ogadwp">Library conflicts between WordPress plugins</a></p></div>';

		if ( null === $ogadwp->gapi_controller ) {
			$ogadwp->gapi_controller = new OGADWP_GAPI_Controller();
		}

		echo '<script type="text/javascript">jQuery("#gapi-warning").hide()</script>';
		if ( isset( $_POST['ogadwp_access_code'] ) ) {
			if ( 1 == ! stripos( 'x' . $_POST['ogadwp_access_code'], 'UA-', 1 ) && $_POST['ogadwp_access_code'] != get_option( 'ogadwp_redeemed_code' ) ) {
				try {
					$ogadwp_access_code = $_POST['ogadwp_access_code'];
					update_option( 'ogadwp_redeemed_code', $ogadwp_access_code );
					$ogadwp->gapi_controller->client->authenticate( $_POST['ogadwp_access_code'] );
					$ogadwp->config->options['token'] = $ogadwp->gapi_controller->client->getAccessToken();
					$ogadwp->config->options['automatic_updates_minorversion'] = 1;
					$ogadwp->config->set_plugin_options( true );
					$options = self::update_options( 'network' );
					$message = "<div class='updated' id='ogadwp-action'><p>" . __( "Plugin authorization succeeded.", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
					if ( is_multisite() ) { // Cleanup errors on the entire network
						foreach ( OGADWP_Tools::get_sites( array( 'number' => apply_filters( 'ogadwp_sites_limit', 100 ) ) ) as $blog ) {
							switch_to_blog( $blog['blog_id'] );
							OGADWP_Tools::delete_cache( 'last_error' );
							OGADWP_Tools::delete_cache( 'gapi_errors' );
							restore_current_blog();
						}
					} else {
						OGADWP_Tools::delete_cache( 'last_error' );
						OGADWP_Tools::delete_cache( 'gapi_errors' );
					}
					if ( $ogadwp->config->options['token'] && $ogadwp->gapi_controller->client->getAccessToken() ) {
						$profiles = $ogadwp->gapi_controller->refresh_profiles();
						if ( is_array ( $profiles ) && ! empty( $profiles ) ) {
							$ogadwp->config->options['ga_profiles_list'] = $profiles;
							if ( isset( $ogadwp->config->options['tableid_jail'] ) && ! $ogadwp->config->options['tableid_jail'] ) {
								$profile = OGADWP_Tools::guess_default_domain( $profiles );
								$ogadwp->config->options['tableid_jail'] = $profile;
							}
							$ogadwp->config->set_plugin_options( true );
							$options = self::update_options( 'network' );
						}
					}
				} catch ( Deconf_IO_Exception $e ) {
					$timeout = $ogadwp->gapi_controller->get_timeouts( 'midnight' );
					OGADWP_Tools::set_error( $e, $timeout );
				} catch ( Deconf_Service_Exception $e ) {
					$timeout = $ogadwp->gapi_controller->get_timeouts( 'midnight' );
					OGADWP_Tools::set_error( $e, $timeout );
				} catch ( Exception $e ) {
					$timeout = $ogadwp->gapi_controller->get_timeouts( 'midnight' );
					OGADWP_Tools::set_error( $e, $timeout );
					$ogadwp->gapi_controller->reset_token();
				}
			} else {
				if ( 1 == stripos( 'x' . $_POST['ogadwp_access_code'], 'UA-', 1 ) ) {
					$message = "<div class='error' id='ogadwp-autodismiss'><p>" . __( "The access code is <strong>not</strong> your <strong>Tracking ID</strong> (UA-XXXXX-X) <strong>nor</strong> your <strong>email address</strong>!", 'open-google-analytics-dashboard-for-wp' ) . ".</p></div>";
				} else {
					$message = "<div class='error' id='ogadwp-autodismiss'><p>" . __( "You can only use the access code <strong>once</strong>, please generate a <strong>new access code</strong> using the red link", 'open-google-analytics-dashboard-for-wp' ) . "!</p></div>";
				}
			}
		}
		if ( isset( $_POST['Refresh'] ) ) {
			if ( isset( $_POST['ogadwp_security'] ) && wp_verify_nonce( $_POST['ogadwp_security'], 'ogadwp_form' ) ) {
				$ogadwp->config->options['ga_profiles_list'] = array();
				$message = "<div class='updated' id='ogadwp-autodismiss'><p>" . __( "Properties refreshed.", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
				$options = self::update_options( 'network' );
				if ( $ogadwp->config->options['token'] && $ogadwp->gapi_controller->client->getAccessToken() ) {
					if ( ! empty( $ogadwp->config->options['ga_profiles_list'] ) ) {
						$profiles = $ogadwp->config->options['ga_profiles_list'];
					} else {
						$profiles = $ogadwp->gapi_controller->refresh_profiles();
					}
					if ( $profiles ) {
						$ogadwp->config->options['ga_profiles_list'] = $profiles;
						if ( isset( $ogadwp->config->options['tableid_jail'] ) && ! $ogadwp->config->options['tableid_jail'] ) {
							$profile = OGADWP_Tools::guess_default_domain( $profiles );
							$ogadwp->config->options['tableid_jail'] = $profile;
						}
						$ogadwp->config->set_plugin_options( true );
						$options = self::update_options( 'network' );
					}
				}
			} else {
				$message = "<div class='error' id='ogadwp-autodismiss'><p>" . __( "Cheating Huh?", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
			}
		}
		if ( isset( $_POST['Clear'] ) ) {
			if ( isset( $_POST['ogadwp_security'] ) && wp_verify_nonce( $_POST['ogadwp_security'], 'ogadwp_form' ) ) {
				OGADWP_Tools::clear_cache();
				$message = "<div class='updated' id='ogadwp-autodismiss'><p>" . __( "Cleared Cache.", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
			} else {
				$message = "<div class='error' id='ogadwp-autodismiss'><p>" . __( "Cheating Huh?", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
			}
		}
		if ( isset( $_POST['Reset'] ) ) {
			if ( isset( $_POST['ogadwp_security'] ) && wp_verify_nonce( $_POST['ogadwp_security'], 'ogadwp_form' ) ) {
				$ogadwp->gapi_controller->reset_token();
				OGADWP_Tools::clear_cache();
				$message = "<div class='updated' id='ogadwp-autodismiss'><p>" . __( "Token Reseted and Revoked.", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
				$options = self::update_options( 'Reset' );
			} else {
				$message = "<div class='error' id='ogadwp-autodismiss'><p>" . __( "Cheating Huh?", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
			}
		}
		if ( isset( $_POST['options']['ogadwp_hidden'] ) && ! isset( $_POST['Clear'] ) && ! isset( $_POST['Reset'] ) && ! isset( $_POST['Refresh'] ) ) {
			$message = "<div class='updated' id='ogadwp-autodismiss'><p>" . __( "Settings saved.", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
			if ( ! ( isset( $_POST['ogadwp_security'] ) && wp_verify_nonce( $_POST['ogadwp_security'], 'ogadwp_form' ) ) ) {
				$message = "<div class='error' id='ogadwp-autodismiss'><p>" . __( "Cheating Huh?", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
			}
		}
		if ( isset( $_POST['Hide'] ) ) {
			if ( isset( $_POST['ogadwp_security'] ) && wp_verify_nonce( $_POST['ogadwp_security'], 'ogadwp_form' ) ) {
				$message = "<div class='updated' id='ogadwp-autodismiss'><p>" . __( "All other domains/properties were removed.", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
				$lock_profile = OGADWP_Tools::get_selected_profile( $ogadwp->config->options['ga_profiles_list'], $ogadwp->config->options['tableid_jail'] );
				$ogadwp->config->options['ga_profiles_list'] = array( $lock_profile );
				$options = self::update_options( 'network' );
			} else {
				$message = "<div class='error' id='ogadwp-autodismiss'><p>" . __( "Cheating Huh?", 'open-google-analytics-dashboard-for-wp' ) . "</p></div>";
			}
		}
		?>
<div class="wrap">
											<h2><?php _e( "Google Analytics Settings", 'open-google-analytics-dashboard-for-wp' );?></h2>
											<hr>
										</div>
										<div id="poststuff" class="ogadwp">
											<div id="post-body" class="metabox-holder columns-2">
												<div id="post-body-content">
													<div class="settings-wrapper">
														<div class="inside">
					<?php if ( $ogadwp->gapi_controller->gapi_errors_handler() || OGADWP_Tools::get_cache( 'last_error' ) ) : ?>
						<?php $message = sprintf( '<div class="error"><p>%s</p></div>', sprintf( __( 'Something went wrong, check %1$s or %2$s.', 'open-google-analytics-dashboard-for-wp' ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'ogadwp_errors_debugging', false ), __( 'Errors & Debug', 'open-google-analytics-dashboard-for-wp' ) ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'ogadwp_settings', false ), __( 'authorize the plugin', 'open-google-analytics-dashboard-for-wp' ) ) ) );?>
					<?php endif; ?>
					<?php if ( isset( $_POST['Authorize'] ) ) : ?>
						<?php OGADWP_Tools::clear_cache();?>
						<?php $ogadwp->gapi_controller->token_request();?>
					<div class="updated">
																<p><?php _e( "Use the red link (see below) to generate and get your access code! You need to generate a new code each time you authorize!", 'open-google-analytics-dashboard-for-wp' );?></p>
															</div>
					<?php else : ?>
						<?php if ( isset( $message ) ) : ?>
							<?php echo $message; ?>
						<?php endif; ?>
					<form name="ogadwp_form" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
																<input type="hidden" name="options[ogadwp_hidden]" value="Y">
						<?php wp_nonce_field('ogadwp_form','ogadwp_security'); ?>
						<table class="ogadwp-settings-options">
																	<tr>
																		<td colspan="2">
								<?php echo "<h2>" . __( "Network Setup", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?>
								</td>
																	</tr>
																	<tr>
																		<td colspan="2" class="ogadwp-settings-title">
																			<div class="button-primary ogadwp-settings-switchoo">
																				<input type="checkbox" name="options[network_mode]" value="1" class="ogadwp-settings-switchoo-checkbox" id="network_mode" <?php checked( $options['network_mode'], 1); ?> onchange="this.form.submit()">
																				<label class="ogadwp-settings-switchoo-label" for="network_mode">
																					<div class="ogadwp-settings-switchoo-inner"></div>
																					<div class="ogadwp-settings-switchoo-switch"></div>
																				</label>
																			</div>
																			<div class="switch-desc"><?php echo " ".__("use a single Google Analytics account for the entire network", 'open-google-analytics-dashboard-for-wp' );?></div>
																		</td>
																	</tr>
							<?php if ($options['network_mode']) : ?>
							<tr>
																		<td colspan="2">
																			<hr>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2"><?php echo "<h2>" . __( "Plugin Authorization", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
																	</tr>
																	<tr>
																		<td colspan="2" class="ogadwp-settings-info">
								<?php printf(__('You need to create a %1$s and watch this %2$s before proceeding to authorization.', 'open-google-analytics-dashboard-for-wp'), sprintf('<a href="%1$s" target="_blank">%2$s</a>', 'https://deconf.com/creating-a-google-analytics-account/?utm_source=ogadwp_config&utm_medium=link&utm_content=top_tutorial&utm_campaign=ogadwp', __("free analytics account", 'open-google-analytics-dashboard-for-wp')), sprintf('<a href="%1$s" target="_blank">%2$s</a>', 'https://deconf.com/open-google-analytics-dashboard-wordpress/?utm_source=ogadwp_config&utm_medium=link&utm_content=top_video&utm_campaign=ogadwp', __("video tutorial", 'open-google-analytics-dashboard-for-wp')));?>
								</td>
																	</tr>
								<?php if ( ! $options['token'] || $options['user_api'] ) : ?>
								<tr>
																		<td colspan="2" class="ogadwp-settings-info">
																			<input name="options[user_api]" type="checkbox" id="user_api" value="1" <?php checked( $options['user_api'], 1 ); ?> onchange="this.form.submit()" /><?php echo " ".__("developer mode (requires advanced API knowledge)", 'open-google-analytics-dashboard-for-wp' );?>
								</td>
																	</tr>
								<?php endif; ?>
							<?php if ( $options['user_api'] ) : ?>
							<tr>
																		<td class="ogadwp-settings-title">
																			<label for="options[client_id]"><?php _e("Client ID:", 'open-google-analytics-dashboard-for-wp'); ?>
									</label>
																		</td>
																		<td>
																			<input type="text" name="options[client_id]" value="<?php echo esc_attr($options['client_id']); ?>" size="40" required="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="ogadwp-settings-title">
																			<label for="options[client_secret]"><?php _e("Client Secret:", 'open-google-analytics-dashboard-for-wp'); ?>
									</label>
																		</td>
																		<td>
																			<input type="text" name="options[client_secret]" value="<?php echo esc_attr($options['client_secret']); ?>" size="40" required="required">
																			<input type="hidden" name="options[ogadwp_hidden]" value="Y">
																			<?php wp_nonce_field('ogadwp_form','ogadwp_security'); ?>
								</td>
																	</tr>
							<?php endif; ?>
							<?php if ( $options['token'] ) : ?>
							<tr>
																		<td colspan="2">
																			<input type="submit" name="Reset" class="button button-secondary" value="<?php _e( "Clear Authorization", 'open-google-analytics-dashboard-for-wp' ); ?>" />
																			<input type="submit" name="Clear" class="button button-secondary" value="<?php _e( "Clear Cache", 'open-google-analytics-dashboard-for-wp' ); ?>" />
																			<input type="submit" name="Refresh" class="button button-secondary" value="<?php _e( "Refresh Properties", 'open-google-analytics-dashboard-for-wp' ); ?>" />
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2">
																			<hr>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2">
								<?php echo "<h2>" . __( "Properties/Views Settings", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?>
								</td>
																	</tr>
							<?php if ( isset( $options['network_tableid'] ) ) : ?>
								<?php $options['network_tableid'] = json_decode( json_encode( $options['network_tableid'] ), false ); ?>
							<?php endif; ?>
							<?php foreach ( OGADWP_Tools::get_sites( array( 'number' => apply_filters( 'ogadwp_sites_limit', 100 ) ) ) as $blog ) : ?>
							<tr>
																		<td class="ogadwp-settings-title-s">
																			<label for="network_tableid"><?php echo '<strong>'.$blog['domain'].$blog['path'].'</strong>: ';?></label>
																		</td>
																		<td>
																			<select id="network_tableid" <?php disabled(!empty($options['ga_profiles_list']),false);?> name="options[network_tableid][<?php echo $blog['blog_id'];?>]">
									<?php if ( ! empty( $options['ga_profiles_list'] ) ) : ?>
										<?php foreach ( $options['ga_profiles_list'] as $items ) : ?>
											<?php if ( $items[3] ) : ?>
												<?php $temp_id = $blog['blog_id']; ?>
												<option value="<?php echo esc_attr( $items[1] );?>" <?php selected( $items[1], isset( $options['network_tableid']->$temp_id ) ? $options['network_tableid']->$temp_id : '');?> title="<?php echo __( "View Name:", 'open-google-analytics-dashboard-for-wp' ) . ' ' . esc_attr( $items[0] );?>">
													 <?php echo esc_html( OGADWP_Tools::strip_protocol( $items[3] ) );?> &#8658; <?php echo esc_attr( $items[0] );?>
												</option>
											<?php endif; ?>
										<?php endforeach; ?>
									<?php else : ?>
												<option value="">
													<?php _e( "Property not found", 'open-google-analytics-dashboard-for-wp' );?>
												</option>
									<?php endif; ?>
									</select>
																			<br />
																		</td>
																	</tr>
							<?php endforeach; ?>
							<tr>
																		<td colspan="2">
																			<h2><?php echo _e( "Automatic Updates", 'open-google-analytics-dashboard-for-wp' );?></h2>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2" class="ogadwp-settings-title">
																			<div class="button-primary ogadwp-settings-switchoo">
																				<input type="checkbox" name="options[automatic_updates_minorversion]" value="1" class="ogadwp-settings-switchoo-checkbox" id="automatic_updates_minorversion" <?php checked( $options['automatic_updates_minorversion'], 1 ); ?>>
																				<label class="ogadwp-settings-switchoo-label" for="automatic_updates_minorversion">
																					<div class="ogadwp-settings-switchoo-inner"></div>
																					<div class="ogadwp-settings-switchoo-switch"></div>
																				</label>
																			</div>
																			<div class="switch-desc"><?php echo " ".__( "automatic updates for minor versions (security and maintenance releases only)", 'open-google-analytics-dashboard-for-wp' );?></div>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2">
																			<hr><?php echo "<h2>" . __( "Exclude Tracking", 'open-google-analytics-dashboard-for-wp' ) . "</h2>"; ?></td>
																	</tr>
																	<tr>
																		<td colspan="2" class="ogadwp-settings-title">
																			<div class="button-primary ogadwp-settings-switchoo">
																				<input type="checkbox" name="options[superadmin_tracking]" value="1" class="ogadwp-settings-switchoo-checkbox" id="superadmin_tracking"<?php checked( $options['superadmin_tracking'], 1); ?>">
																				<label class="ogadwp-settings-switchoo-label" for="superadmin_tracking">
																					<div class="ogadwp-settings-switchoo-inner"></div>
																					<div class="ogadwp-settings-switchoo-switch"></div>
																				</label>
																			</div>
																			<div class="switch-desc"><?php echo " ".__("exclude Super Admin tracking for the entire network", 'open-google-analytics-dashboard-for-wp' );?></div>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2">
																			<hr>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2" class="submit">
																			<input type="submit" name="Submit" class="button button-primary" value="<?php _e('Save Changes', 'open-google-analytics-dashboard-for-wp' ) ?>" />
																		</td>
																	</tr>
							<?php else : ?>
							<tr>
																		<td colspan="2">
																			<hr>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2">
																			<input type="submit" name="Authorize" class="button button-secondary" id="authorize" value="<?php _e( "Authorize Plugin", 'open-google-analytics-dashboard-for-wp' ); ?>" />
																			<input type="submit" name="Clear" class="button button-secondary" value="<?php _e( "Clear Cache", 'open-google-analytics-dashboard-for-wp' ); ?>" />
																		</td>
																	</tr>
							<?php endif; ?>
							<tr>
																		<td colspan="2">
																			<hr>
																		</td>
																	</tr>
																</table>
															</form>
		<?php self::output_sidebar(); ?>
				<?php return; ?>
			<?php endif;?>
						</table>
															</form>
		<?php endif; ?>
		<?php

		self::output_sidebar();
	}

	public static function output_sidebar() {
		global $wp_version;

		$ogadwp = OGADWP();
		?>
				</div>
													</div>
												</div>
												<div id="postbox-container-1" class="postbox-container">
													<div class="meta-box-sortables">
														<div class="postbox">
															<h3>
																<span><?php _e("Setup Tutorial & Demo",'open-google-analytics-dashboard-for-wp') ?></span>
															</h3>
															<div class="inside">
																<a href="https://deconf.com/open-google-analytics-dashboard-wordpress/?utm_source=ogadwp_config&utm_medium=link&utm_content=video&utm_campaign=ogadwp" target="_blank"><img src="<?php echo plugins_url( 'images/open-google-analytics-dashboard.png' , __FILE__ );?>" width="100%" alt="" /></a>
															</div>
														</div>
														<div class="postbox">
															<h3>
																<span><?php _e("Stay Updated",'open-google-analytics-dashboard-for-wp')?></span>
															</h3>
															<div class="inside">
																<div class="ogadwp-desc">
																	<div class="g-ytsubscribe" data-channel="TheDeConf" data-layout="default" data-count="default"></div>
																</div>
																<br />
																<div class="ogadwp-desc">
																	<div class="g-follow" data-annotation="bubble" data-height="24" data-href="//plus.google.com/u/0/114149166432576972465" data-rel="publisher"></div>
																	<script src="https://apis.google.com/js/platform.js" async defer></script>
																</div>
																<br />
																<div class="ogadwp-desc">
																	<a href="https://twitter.com/deconfcom" class="twitter-follow-button" data-show-screen-name="false"></a>
																	<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
																</div>
															</div>
														</div>
														<div class="postbox">
															<h3>
																<span><?php _e("Further Reading",'open-google-analytics-dashboard-for-wp')?></span>
															</h3>
															<div class="inside">
																<div class="ogadwp-title">
																	<a href="https://deconf.com/clicky-web-analytics-review/?utm_source=ogadwp_config&utm_medium=link&utm_content=clicky&utm_campaign=ogadwp"><img src="<?php echo plugins_url( 'images/clicky.png' , __FILE__ ); ?>" /></a>
																</div>
																<div class="ogadwp-desc">
																	<?php printf(__('%s service with users tracking at IP level.', 'open-google-analytics-dashboard-for-wp'), sprintf('<a href="https://deconf.com/clicky-web-analytics-review/?utm_source=ogadwp_config&utm_medium=link&utm_content=clicky&utm_campaign=ogadwp">%s</a>', __('Web Analytics', 'open-google-analytics-dashboard-for-wp')));?>
																</div>
																<br />
																<div class="ogadwp-title">
																	<a href="https://deconf.com/move-website-https-ssl/?utm_source=ogadwp_config&utm_medium=link&utm_content=ssl&utm_campaign=ogadwp"><img src="<?php echo plugins_url( 'images/ssl.png' , __FILE__ ); ?>" /></a>
																</div>
																<div class="ogadwp-desc">
																	<?php printf(__('%s by moving your website to HTTPS/SSL.', 'open-google-analytics-dashboard-for-wp'), sprintf('<a href="https://deconf.com/move-website-https-ssl/?utm_source=ogadwp_config&utm_medium=link&utm_content=ssl&utm_campaign=ogadwp">%s</a>', __('Improve search rankings', 'open-google-analytics-dashboard-for-wp')));?>
																</div>
																<br />
																<div class="ogadwp-title">
																	<a href="http://wordpress.org/support/view/plugin-reviews/open-google-analytics-dashboard-for-wp#plugin-info"><img src="<?php echo plugins_url( 'images/star.png' , __FILE__ ); ?>" /></a>
																</div>
																<div class="ogadwp-desc">
																	<?php printf(__('Your feedback and review are both important, %s!', 'open-google-analytics-dashboard-for-wp'), sprintf('<a href="http://wordpress.org/support/view/plugin-reviews/open-google-analytics-dashboard-for-wp#plugin-info">%s</a>', __('rate this plugin', 'open-google-analytics-dashboard-for-wp')));?>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
<?php
		// Dismiss the admin update notice
		if ( version_compare( $wp_version, '4.2', '<' ) && current_user_can( 'manage_options' ) ) {
			delete_option( 'ogadwp_got_updated' );
		}
	}
}
