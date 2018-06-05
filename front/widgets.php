<?php
/**
 * Copyright 2013 Alin Marcu
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit();

final class GAPWP_Frontend_Widget extends WP_Widget {

	private $gapwp;

	public function __construct() {
		$this->gapwp = GAPWP();

		parent::__construct( 'gapwp-frontwidget-report', __( 'Google Analytics+', 'google-analytics-plus-wp' ), array( 'description' => __( "Will display your google analytics stats in a widget", 'google-analytics-plus-wp' ) ) );
		// Frontend Styles
		if ( is_active_widget( false, false, $this->id_base, true ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'load_styles_scripts' ) );
		}
	}

	public function load_styles_scripts() {
		$lang = get_bloginfo( 'language' );
		$lang = explode( '-', $lang );
		$lang = $lang[0];

		wp_enqueue_style( 'gapwp-front-widget', GAPWP_URL . 'front/css/widgets.css', null, GAPWP_CURRENT_VERSION );
		wp_enqueue_script( 'gapwp-front-widget', GAPWP_URL . 'front/js/widgets.js', array( 'jquery' ), GAPWP_CURRENT_VERSION );
		wp_enqueue_script( 'googlecharts', 'https://www.gstatic.com/charts/loader.js', array(), null );
	}

	public function widget( $args, $instance ) {
		$widget_title = apply_filters( 'widget_title', $instance['title'] );
		$title = __( "Sessions", 'google-analytics-plus-wp' );
		echo "\n<!-- BEGIN GAPWP v" . GAPWP_CURRENT_VERSION . " Widget - https://intelligencewp.com/google-analytics-plus-wordpress/ -->\n";
		echo $args['before_widget'];
		if ( ! empty( $widget_title ) ) {
			echo $args['before_title'] . $widget_title . $args['after_title'];
		}

		if ( isset( $this->gapwp->config->options['theme_color'] ) ) {
			$css = "colors:['" . $this->gapwp->config->options['theme_color'] . "','" . GAPWP_Tools::colourVariator( $this->gapwp->config->options['theme_color'], - 20 ) . "'],";
			$color = $this->gapwp->config->options['theme_color'];
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
				$periodtext = sprintf( __( 'Last %d Days', 'google-analytics-plus-wp' ), 7 );
				break;
			case '14daysAgo' :
				$periodtext = sprintf( __( 'Last %d Days', 'google-analytics-plus-wp' ), 14 );
				break;
			case '30daysAgo' :
				$periodtext = sprintf( __( 'Last %d Days', 'google-analytics-plus-wp' ), 30 );
				break;
			default :
				$periodtext = "";
				break;
		}
		switch ( $instance['display'] ) {
			case '1' :
				echo '<div id="gapwp-widget"><div id="gapwp-widgetchart"></div><div id="gapwp-widgettotals"></div></div>';
				break;
			case '2' :
				echo '<div id="gapwp-widget"><div id="gapwp-widgetchart"></div></div>';
				break;
			case '3' :
				echo '<div id="gapwp-widget"><div id="gapwp-widgettotals"></div></div>';
				break;
		}
		?>
<script type="text/javascript">
	google.charts.load('current', {'packages':['corechart']});
	google.charts.setOnLoadCallback( GAPWPWidgetLoad );
	function GAPWPWidgetLoad (){
		jQuery.post("<?php echo admin_url( 'admin-ajax.php' ); ?>", {action: "ajax_frontwidget_report", gapwp_number: "<?php echo $this->number; ?>", gapwp_optionname: "<?php  echo $this->option_name; ?>" }, function(response){
			if (!jQuery.isNumeric(response) && jQuery.isArray(response)){
				if (jQuery("#gapwp-widgetchart")[0]){
					gapwpFrontWidgetData = response[0];
					gapwp_drawFrontWidgetChart(gapwpFrontWidgetData);
				}
				if (jQuery("#gapwp-widgettotals")[0]){
					gapwp_drawFrontWidgetTotals(response[1]);
				}
			}else{
				jQuery("#gapwp-widgetchart").css({"background-color":"#F7F7F7","height":"auto","padding-top":"50px","padding-bottom":"50px","color":"#000","text-align":"center"});
				jQuery("#gapwp-widgetchart").html("<?php __( "This report is unavailable", 'google-analytics-plus-wp' ); ?> ("+response+")");
			}
		});
	}
	function gapwp_drawFrontWidgetChart(response) {
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
		var chart = new google.visualization.AreaChart(document.getElementById("gapwp-widgetchart"));
		<?php echo $formater; ?>
		chart.draw(data, options);
	}
	function gapwp_drawFrontWidgetTotals(response) {
		if ( null == response ){
			response = 0;
		}
		jQuery("#gapwp-widgettotals").html('<div class="gapwp-left"><?php _e( "Period:", 'google-analytics-plus-wp' ); ?></div> <div class="gapwp-right"><?php echo $periodtext; ?> </div><div class="gapwp-left"><?php _e( "Sessions:", 'google-analytics-plus-wp' ); ?></div> <div class="gapwp-right">'+response+'</div>');
	}
</script>
<?php
		if ( 1 == $instance['give_credits'] ) :
			?>
<div style="text-align: right; width: 100%; font-size: 0.8em; clear: both; margin-right: 5px;"><?php _e( 'generated by', 'google-analytics-plus-wp' ); ?> <a href="https://intelligencewp.com/google-analytics-plus-wordpress/?utm_source=gapwp_report&utm_medium=link&utm_content=front_widget&utm_campaign=gapwp" rel="nofollow" style="text-decoration: none; font-size: 1em;">GAPWP</a>&nbsp;
</div>

		<?php
		endif;
		$widget_content = ob_get_contents();
		if ( ob_get_length() ) {
			ob_end_clean();
		}
		echo $widget_content;
		echo $args['after_widget'];
		echo "\n<!-- END GAPWP Widget -->\n";
	}

	public function form( $instance ) {
		$widget_title = ( isset( $instance['title'] ) ? $instance['title'] : __( "Google Analytics Stats", 'google-analytics-plus-wp' ) );
		$period = ( isset( $instance['period'] ) ? $instance['period'] : '7daysAgo' );
		$display = ( isset( $instance['display'] ) ? $instance['display'] : 1 );
		$give_credits = ( isset( $instance['give_credits'] ) ? $instance['give_credits'] : 1 );
		$anonim = ( isset( $instance['anonim'] ) ? $instance['anonim'] : 0 );
		/* @formatter:off */
?>
<p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( "Title:",'google-analytics-plus-wp' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $widget_title ); ?>">
</p>
<p>
    <label for="<?php echo $this->get_field_id( 'display' ); ?>"><?php _e( "Display:",'google-analytics-plus-wp' ); ?></label> <select id="<?php echo $this->get_field_id('display'); ?>" class="widefat" name="<?php   echo $this->get_field_name( 'display' ); ?>">
        <option value="1" <?php selected( $display, 1 ); ?>><?php _e('Chart & Totals', 'google-analytics-plus-wp');?></option>
        <option value="2" <?php selected( $display, 2 ); ?>><?php _e('Chart', 'google-analytics-plus-wp');?></option>
        <option value="3" <?php selected( $display, 3 ); ?>><?php _e('Totals', 'google-analytics-plus-wp');?></option>
    </select>
</p>
<p>
    <label for="<?php echo $this->get_field_id( 'anonim' ); ?>"><?php _e( "Anonymize stats:",'google-analytics-plus-wp' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'anonim' ); ?>" name="<?php echo $this->get_field_name( 'anonim' ); ?>" type="checkbox" <?php checked( $anonim, 1 ); ?> value="1">
</p>
<p>
    <label for="<?php echo $this->get_field_id( 'period' ); ?>"><?php _e( "Stats for:",'google-analytics-plus-wp' ); ?></label> <select id="<?php echo $this->get_field_id('period'); ?>" class="widefat" name="<?php   echo $this->get_field_name( 'period' ); ?>">
        <option value="7daysAgo" <?php selected( $period, '7daysAgo' ); ?>><?php printf( __('Last %d Days', 'google-analytics-plus-wp'), 7 );?></option>
        <option value="14daysAgo" <?php selected( $period, '14daysAgo' ); ?>><?php printf( __('Last %d Days', 'google-analytics-plus-wp'), 14 );?></option>
        <option value="30daysAgo" <?php selected( $period, '30daysAgo' ); ?>><?php printf( __('Last %d Days', 'google-analytics-plus-wp'), 30 );?></option>
    </select>
</p>
<p>
    <label for="<?php echo $this->get_field_id( 'give_credits' ); ?>"><?php _e( "Give credits:",'google-analytics-plus-wp' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'give_credits' ); ?>" name="<?php echo $this->get_field_name( 'give_credits' ); ?>" type="checkbox" <?php checked( $give_credits, 1 ); ?> value="1">
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
