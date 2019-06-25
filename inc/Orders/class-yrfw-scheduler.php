<?php

/**
 * @package  YotpoReviews
 */
class YRFW_Scheduler {

	/**
	 * Set WP Cron schedule for order submission for given interval (def. twicedaily)
	 *
	 * @param string $interval chosen interval (e.g. daily, twicedaily etc).
	 * @return void
	 */
	public function set_scheduler( $interval = 'twicedaily' ) {
		global $yrfw_logger, $yotpo_settings;
		$settings = $yotpo_settings->get_settings();
		// if ( ! $this->get_scheduler() && 'schedule' === $settings['order_submission_method'] ) {
		if ( 'schedule' === $settings['order_submission_method'] ) {
			$event = wp_schedule_event( ( time() + ( 5 * 60 ) ), $interval, 'yotpo_scheduler_action' );
			$yrfw_logger->debug( 'Scheduling has been set up for ' . $this->get_scheduler() );
			return $event;
		}
	}

	/**
	 * Get next time of schedule
	 *
	 * @return string|boolean time or false
	 */
	public static function get_scheduler() {
		return ( wp_next_scheduled( 'yotpo_scheduler_action' ) ) ? date( 'Y-m-d\TH:i:s\Z', wp_next_scheduled( 'yotpo_scheduler_action' ) ) : false;
	}

	/**
	 * Run scheduled WP Cron task
	 *
	 * @return void|boolean if failed
	 */
	public function do_scheduler() {
		global $yrfw_logger;
		$past_orders = new YRFW_Past_Orders();
		$order_array = $past_orders->get_past_orders( 1 );
		if ( ! is_null( $order_array ) ) {
			$past_orders->submit_past_orders( $order_array );
		} else {
			return false;
		}
	}

	/**
	 * Remove scheduled WP Cron task
	 *
	 * @return void
	 */
	public function clear_scheduler() {
		if ( $this->get_scheduler() ) {
			wp_clear_scheduled_hook( 'yotpo_scheduler_action' );
			// wp_unschedule_event( $this->get_scheduler(), 'yotpo_scheduler_action' );
		}
	}
}