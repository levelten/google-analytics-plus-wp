<?php
/**
 * Author: Alin Marcu
 * Copyright 2017 Alin Marcu
 * Author URI: https://deconf.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
?>
<form name="input" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
	<table class="ogadwp-settings-options">
		<tr>
			<td colspan="2" class="ogadwp-settings-info">
						<?php echo __( "Use this link to get your <strong>one-time-use</strong> access code:", 'open-google-analytics-dashboard-for-wp' ) . ' <a href="' . $data['authUrl'] . '" id="gapi-access-code" target="_blank">' . __ ( "Get Access Code", 'open-google-analytics-dashboard-for-wp' ) . '</a>.'; ?>
			</td>
		</tr>
		<tr>
			<td class="ogadwp-settings-title">
				<label for="ogadwp_access_code" title="<?php _e("Use the red link to get your access code! You need to generate a new one each time you authorize!",'open-google-analytics-dashboard-for-wp')?>"><?php echo _e( "Access Code:", 'open-google-analytics-dashboard-for-wp' ); ?></label>
			</td>
			<td>
				<input type="text" id="ogadwp_access_code" name="ogadwp_access_code" value="" size="61" autocomplete="off" pattern=".\/.{30,}" required="required" title="<?php _e("Use the red link to get your access code! You need to generate a new one each time you authorize!",'open-google-analytics-dashboard-for-wp')?>">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<hr>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" class="button button-secondary" name="ogadwp_authorize" value="<?php _e( "Save Access Code", 'open-google-analytics-dashboard-for-wp' ); ?>" />
			</td>
		</tr>
	</table>
</form>
