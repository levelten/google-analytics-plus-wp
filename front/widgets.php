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

final class OGADWP_Frontend_Widget extends WP_Widget {

	private $ogadwp;

	public function __construct() {
		$this->ogadwp = OGADWP();

		parent::__construct( 'ogadwp-frontwidget-report', __( 'Open Google Analytics Dashboard', 'open-google-analytics-dashboard-for-wp' ), array( 'description' => __( "Will display your google analytics stats in a widget", 'open-google-analytics-dashboard-for-wp' ) ) );
		// Frontend Styles
		if ( is_active_widget( false, false, $this->id_base, true ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'load_styles_scripts' ) );
		}
	}

	public function load_styles_scripts() {
		$lang = get_bloginfo( 'language' );
		$lang = explode( '-', $lang );
		$lang = $lang[0];

		wp_enqueue_style( 'ogadwp-front-widget', OGADWP_URL . 'front/css/widgets.css', null, OGADWP_CURRENT_VERSION );
		wp_enqueue_script( 'ogadwp-front-widget', OGADWP_URL . 'front/js/widgets.js', array( 'jquery' ), OGADWP_CURRENT_VERSION );
		wp_enqueue_script( 'googlecharts', 'https://www.gstatic.com/charts/loader.js', array(), null );
	}

	public function widget( $args, $instance ) {
		$widget_title = apply_filters( 'widget_title', $instance['title'] );
		$title = __( "Sessions", 'open-google-analytics-dashboard-for-wp' );
		echo "\n<!-- BEGIN OGADWP v" . OGADWP_CURRENT_VERSION . " Widget - https://deconf.com/open-google-analytics-dashboard-wordpress/ -->\n";
		echo $args['before_widget'];
		if ( ! empty( $widget_title ) ) {
			echo $args['before_title'] . $widget_title . $args['after_title'];
		}

		if ( isset( $this->ogadwp->config->options['theme_color'] ) ) {
			$css = "colors:['" . $this->ogadwp->config->options['theme_color'] . "','" . OGADWP_Tools::colourVariator( $this->ogadwp->config->options['theme_color'], - 20 ) . "'],";
			$color = $this->ogadwp->config->options['theme_color'];
		} else {
			$css = "";
			$color = "#3366CC";
		}
		ob_start();
		if ( $instance['anonim'] ) {
			$formater = "var formatter = new google.visualization.NumberFormat({
					  suffix: '%',
					  fractionDigits: 2
					});

					formatter.format(data, 1);";
		} else {
			$formater = '';
		}
		$periodtext = "";
		switch ( $instance['period'] ) {
			case '7daysAgo' :
				$periodtext = sprintf( __( 'Last %d Days', 'open-google-analytics-dashboard-for-wp' ), 7 );
				break;
			case '14daysAgo' :
				$periodtext = sprintf( __( 'Last %d Days', 'open-google-analytics-dashboard-for-wp' ), 14 );
				break;
			case '30daysAgo' :
				$periodtext = sprintf( __( 'Last %d Days', 'open-google-analytics-dashboard-for-wp' ), 30 );
				break;
			default :
				$periodtext = "";
				break;
		}
		switch ( $instance['display'] ) {
			case '1' :
				echo '<div id="ogadwp-widget"><div id="ogadwp-widgetchart"></div><div id="ogadwp-widgettotals"></div></div>';
				break;
			case '2' :
				echo '<div id="ogadwp-widget"><div id="ogadwp-widgetchart"></div></div>';
				break;
			case '3' :
				echo '<div id="ogadwp-widget"><div id="ogadwp-widgettotals"></div></div>';
				break;
		}
		?>
<script type="text/javascript">
	google.charts.load('current', {'packages':['corechart']});
	google.charts.setOnLoadCallback( OGADWPWidgetLoad );
	function OGADWPWidgetLoad (){
		jQuery.post("<?php echo admin_url( 'admin-ajax.php' ); ?>", {action: "ajax_frontwidget_report", ogadwp_number: "<?php echo $this->number; ?>", ogadwp_optionname: "<?php  echo $this->option_name; ?>" }, function(response){
			if (!jQuery.isNumeric(response) && jQuery.isArray(response)){
				if (jQuery("#ogadwp-widgetchart")[0]){
					ogadwpFrontWidgetData = response[0];
					ogadwp_drawFrontWidgetChart(ogadwpFrontWidgetData);
				}
				if (jQuery("#ogadwp-widgettotals")[0]){
					ogadwp_drawFrontWidgetTotals(response[1]);
				}
			}else{
				jQuery("#ogadwp-widgetchart").css({"background-color":"#F7F7F7","height":"auto","padding-top":"50px","padding-bottom":"50px","color":"#000","text-align":"center"});
				jQuery("#ogadwp-widgetchart").html("<?php __( "This report is unavailable", 'open-google-analytics-dashboard-for-wp' ); ?> ("+response+")");
			}
		});
	}
	function ogadwp_drawFrontWidgetChart(response) {
		var data = google.visualization.arrayToDataTable(response);
		var options = {
			legend: { position: "none" },
			pointSize: "3",
			<?php echo $css; ?>
			title: "<?php echo $title; ?>",
			titlePosition: "in",
			chartArea: { width: "95%", height: "75%" },
			hAxis: { textPosition: "none"},
			vAxis: { textPosition: "none", minValue: 0, gridlines: { color: "transparent" }, baselineColor: "transparent"}
		}
		var chart = new google.visualization.AreaChart(document.getElementById("ogadwp-widgetchart"));
		<?php echo $formater; ?>
		chart.draw(data, options);
	}
	function ogadwp_drawFrontWidgetTotals(response) {
		if ( null == response ){
			response = 0;
		}
		jQuery("#ogadwp-widgettotals").html('<div class="ogadwp-left"><?php _e( "Period:", 'open-google-analytics-dashboard-for-wp' ); ?></div> <div class="ogadwp-right"><?php echo $periodtext; ?> </div><div class="ogadwp-left"><?php _e( "Sessions:", 'open-google-analytics-dashboard-for-wp' ); ?></div> <div class="ogadwp-right">'+response+'</div>');
	}
</script>
<?php
		if ( 1 == $instance['give_credits'] ) :
			?>
<div style="text-align: right; width: 100%; font-size: 0.8em; clear: both; margin-right: 5px;"><?php _e( 'generated by', 'open-google-analytics-dashboard-for-wp' ); ?> <a href="https://deconf.com/open-google-analytics-dashboard-wordpress/?utm_source=ogadwp_report&utm_medium=link&utm_content=front_widget&utm_campaign=ogadwp" rel="nofollow" style="text-decoration: none; font-size: 1em;">OGADWP</a>&nbsp;
</div>

		<?php
		endif;
		$widget_content = ob_get_contents();
		if ( ob_get_length() ) {
			ob_end_clean();
		}
		echo $widget_content;
		echo $args['after_widget'];
		echo "\n<!-- END OGADWP Widget -->\n";
	}

	public function form( $instance ) {
		$widget_title = ( isset( $instance['title'] ) ? $instance['title'] : __( "Google Analytics Stats", 'open-google-analytics-dashboard-for-wp' ) );
		$period = ( isset( $instance['period'] ) ? $instance['period'] : '7daysAgo' );
		$display = ( isset( $instance['display'] ) ? $instance['display'] : 1 );
		$give_credits = ( isset( $instance['give_credits'] ) ? $instance['give_credits'] : 1 );
		$anonim = ( isset( $instance['anonim'] ) ? $instance['anonim'] : 0 );
		/* @formatter:off */
?>
<p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( "Title:",'open-google-analytics-dashboard-for-wp' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $widget_title ); ?>">
</p>
<p>
    <label for="<?php echo $this->get_field_id( 'display' ); ?>"><?php _e( "Display:",'open-google-analytics-dashboard-for-wp' ); ?></label> <select id="<?php echo $this->get_field_id('display'); ?>" class="widefat" name="<?php   echo $this->get_field_name( 'display' ); ?>">
        <option value="1" <?php selected( $display, 1 ); ?>><?php _e('Chart & Totals', 'open-google-analytics-dashboard-for-wp');?></option>
        <option value="2" <?php selected( $display, 2 ); ?>><?php _e('Chart', 'open-google-analytics-dashboard-for-wp');?></option>
        <option value="3" <?php selected( $display, 3 ); ?>><?php _e('Totals', 'open-google-analytics-dashboard-for-wp');?></option>
    </select>
</p>
<p>
    <label for="<?php echo $this->get_field_id( 'anonim' ); ?>"><?php _e( "Anonymize stats:",'open-google-analytics-dashboard-for-wp' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'anonim' ); ?>" name="<?php echo $this->get_field_name( 'anonim' ); ?>" type="checkbox" <?php checked( $anonim, 1 ); ?> value="1">
</p>
<p>
    <label for="<?php echo $this->get_field_id( 'period' ); ?>"><?php _e( "Stats for:",'open-google-analytics-dashboard-for-wp' ); ?></label> <select id="<?php echo $this->get_field_id('period'); ?>" class="widefat" name="<?php   echo $this->get_field_name( 'period' ); ?>">
        <option value="7daysAgo" <?php selected( $period, '7daysAgo' ); ?>><?php printf( __('Last %d Days', 'open-google-analytics-dashboard-for-wp'), 7 );?></option>
        <option value="14daysAgo" <?php selected( $period, '14daysAgo' ); ?>><?php printf( __('Last %d Days', 'open-google-analytics-dashboard-for-wp'), 14 );?></option>
        <option value="30daysAgo" <?php selected( $period, '30daysAgo' ); ?>><?php printf( __('Last %d Days', 'open-google-analytics-dashboard-for-wp'), 30 );?></option>
    </select>
</p>
<p>
    <label for="<?php echo $this->get_field_id( 'give_credits' ); ?>"><?php _e( "Give credits:",'open-google-analytics-dashboard-for-wp' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'give_credits' ); ?>" name="<?php echo $this->get_field_name( 'give_credits' ); ?>" type="checkbox" <?php checked( $give_credits, 1 ); ?> value="1">
</p>
<?php
		/* @formatter:on */
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : 'Analytics Stats';
		$instance['period'] = ( ! empty( $new_instance['period'] ) ) ? strip_tags( $new_instance['period'] ) : '7daysAgo';
		$instance['display'] = ( ! empty( $new_instance['display'] ) ) ? strip_tags( $new_instance['display'] ) : 1;
		$instance['give_credits'] = ( ! empty( $new_instance['give_credits'] ) ) ? strip_tags( $new_instance['give_credits'] ) : 0;
		$instance['anonim'] = ( ! empty( $new_instance['anonim'] ) ) ? strip_tags( $new_instance['anonim'] ) : 0;
		return $instance;
	}
}
