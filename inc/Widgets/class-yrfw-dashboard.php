<?php

/**
 * @package  YotpoReviews
 */
class YRFW_Dashboard_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->register_widget();
	}

	/**
	 * Register widget for Dashboard
	 *
	 * @return void
	 */
	public function register_widget() {
		wp_add_dashboard_widget( 'yotpo_dashboard_widget', 'Yotpo Reviews', array( $this, 'display_widget' ) );
	}

	/**
	 * Display Yotpo widget in the WordPress dashboard.
	 *
	 * @return void
	 */
	public function display_widget() {
		global $yotpo_settings;
		$settings = $yotpo_settings->get_settings();
		$api      = YRFW_API_Wrapper::get_instance();
		$api->init( $settings['app_key'], $settings['secret'] );
		$token    = $api->get_token();
		?>
		<div class="yotpo-widget-container">
			<script id="yotpo-dashboard" type="text/javascript">
				var y = JSON.stringify({
					"methods": [
					{
						"method": "badge",
						"params": {
							"pid": null
						}
					}
					],
					"app_key": "<?= $settings['app_key']; ?>"
				});
				jQuery.ajax({
					url: 'http://staticw2.yotpo.com/batch',
					dataType: 'json',
					type: 'post',
					contentType: 'application/json',
					data: y,
					success: function( data ){
							var d = data[0]['result'];
							jQuery('<div id="y-resp" style="display:none;">'+d+'</div>').appendTo('body');
							jQuery('#yotpo_dashboard_widget .inside').html( '<div class="yotpo-widget"><div class="widget-content"><h1>'+jQuery('#y-resp .y-badge-reviews')[0].textContent+'</h1><span>reviews</span></div></div>' );
							jQuery('#yotpo_dashboard_widget .inside').append( '<div class="yotpo-widget"><div class="widget-content"><h1>'+(jQuery('#y-resp .y-badge-stars .sr-only')[0].textContent.replace(' star rating',''))+'</h1><span>avg. rating</span></div></div>' );
						}
				});
				var reviews_in_week = jQuery.get('https://api.yotpo.com/v1/apps/<?= $settings['app_key']; ?>/reviews?utoken=<?= $token; ?>&since_date=<?= date( 'Y-m-d', strtotime( '-7 days' ) ); ?>&count=1000', function(data) {
					var reviews_week = ( data['reviews'] ? data['reviews'].length : 0 );
					jQuery('#yotpo_dashboard_widget .inside').append( '<div class="yotpo-widget"><div class="widget-content"><h1>'+reviews_week+'</h1><span>reviews/wk</span></div></div>' );
				});
				var emails_in_week = jQuery.get('https://api.yotpo.com/analytics/v1/emails/<?= $settings['app_key']; ?>/emails_sent?token=<?= $token; ?>&email_type=all_review_emails', function(data) {
					var sum = 0;
					for(var item in data.date_series_points) {
						sum += data.date_series_points[item].values.all_review_emails;
					}
					jQuery('#yotpo_dashboard_widget .inside').append( '<div class="yotpo-widget"><div class="widget-content"><h1>'+sum+'</h1><span>emails/mth</span></div></div>' );
				});
			</script>
		</div>
		<?php
	}
}