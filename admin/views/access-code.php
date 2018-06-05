<?php
/**
 * Copyright 2017 Alin Marcu
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
?>
<form name="input" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
	<table class="gapwp-settings-options">
		<tr>
			<td colspan="2" class="gapwp-settings-info">
						<?php echo __( "Use this link to get your <strong>one-time-use</strong> access code:", 'google-analytics-plus-wp' ) . ' <a href="' . $data['authUrl'] . '" id="gapi-access-code" target="_blank">' . __ ( "Get Access Code", 'google-analytics-plus-wp' ) . '</a>.'; ?>
			</td>
		</tr>
		<tr>
			<td class="gapwp-settings-title">
				<label for="gapwp_access_code" title="<?php _e("Use the red link to get your access code! You need to generate a new one each time you authorize!",'google-analytics-plus-wp')?>"><?php echo _e( "Access Code:", 'google-analytics-plus-wp' ); ?></label>
			</td>
			<td>
				<input type="text" id="gapwp_access_code" name="gapwp_access_code" value="" size="61" autocomplete="off" pattern=".\/.{30,}" required="required" title="<?php _e("Use the red link to get your access code! You need to generate a new one each time you authorize!",'google-analytics-plus-wp')?>">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<hr>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" class="button button-secondary" name="gapwp_authorize" value="<?php _e( "Save Access Code", 'google-analytics-plus-wp' ); ?>" />
			</td>
		</tr>
	</table>
</form>
