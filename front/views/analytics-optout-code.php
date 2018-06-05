<?php
/**
 * Copyright 2018 Alin Marcu
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
?>
<script>
var gapwpDnt = false;
var gapwpProperty = '<?php echo $data['uaid']?>';
var gapwpDntFollow = <?php echo $data['gaDntOptout'] ? 'true' : 'false'?>;
var gapwpOptout = <?php echo $data['gaOptout'] ? 'true' : 'false'?>;
var disableStr = 'ga-disable-' + gapwpProperty;
if(gapwpDntFollow && (window.doNotTrack === "1" || navigator.doNotTrack === "1" || navigator.doNotTrack === "yes" || navigator.msDoNotTrack === "1")) {
	gapwpDnt = true;
}
if (gapwpDnt || (document.cookie.indexOf(disableStr + '=true') > -1 && gapwpOptout)) {
	window[disableStr] = true;
}
function gaOptout() {
	var expDate = new Date;
	expDate.setFullYear(expDate.getFullYear( ) + 10);
	document.cookie = disableStr + '=true; expires=' + expDate.toGMTString( ) + '; path=/';
	window[disableStr] = true;
}
</script>
