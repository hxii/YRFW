<?php

/**
 * @package YotpoReviews
 */

class YRFW_Settings {

	private static $instance;
	
	private function __construct() {
		// Do nothing
	}

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new static;
		}
		return self::$instance;
	}

	/**
	 * Get Yotpo settings or return defaults
	 *
	 * @return array settings
	 */
	public static function get_settings() {
		return get_option( 'yotpo_settings', self::default_settings() );
	}

	/**
	 * Update settings with given ones
	 * as well as controling the scheduler cron
	 *
	 * @param  array   $settings settings array to update.
	 * @param  boolean $json     is $settings in JSON format?.
	 * @param  boolean $process  should we process settings before setting them.
	 * @return void
	 */
	public static function update_settings( array $settings, bool $json = false, bool $process = false ) {
		if ( $json ) {
			$settings = json_decode( stripslashes( urldecode( $settings ), true ) );
		}
		if ( $process ) {
			$settings = self::process_post_settings( $settings );
		}
		$current_settings = self::get_settings();
		foreach ( $settings as $key => $value ) {
			$current_settings[ $key ] = $value;
		}
		update_option( 'yotpo_settings', $current_settings );
		if ( 'schedule' === $current_settings['order_submission_method'] ) {
			global $yotpo_scheduler;
			$yotpo_scheduler->set_scheduler();
		} elseif ( 'schedule' !== $current_settings['order_submission_method'] ) {
			global $yotpo_scheduler;
			$yotpo_scheduler->clear_scheduler();
		}
	}

	/**
	 * "upgrading" settings when updating from older versions when settings may be missing
	 *
	 * @return void
	 */
	public function upgrade_settings() { //ToDo: Check this, does this actually append stuff or overwrite?
		$current_settings = self::get_settings();
		$default_settings = $this->default_settings();
		// $upgraded_settings = array_merge( $current_settings, $default_settings );
		// self::update_settings( $upgraded_settings );
		foreach ( $default_settings as $key => $value ) {
			if ( ! array_key_exists( $key, $current_settings ) ) {
				$current_settings[ $key ] = $value;
			}
		}
		self::update_settings( $current_settings );
	}

	/**
	 * Changing settings values from strings to booleans
	 *
	 * @param  array $post_settings the settings to be processed.
	 * @return array                 processed settings.
	 */
	public static function process_post_settings( array $post_settings ){
		foreach ( $post_settings as $key => $value ) {
			if ( 'true' === $value ) {
				$post_settings[ $key ] = true;
			} elseif ( 'false' === $value ) {
				$post_settings[ $key ] = false;
			}
		}
		return $post_settings;
	}

	/**
	 * Reset Yotpo settings (or delete them)
	 *
	 * @param  boolean $delete should we delete the settings.
	 * @return void
	 */
	public static function reset_settings( $delete = false ) {
		if ( $delete ) {
			delete_option( 'yotpo_settings' );
		} else {
			self::update_settings( self::default_settings() );
		}
	}

	/**
	 * Default Yotpo settings
	 *
	 * @return array default settings
	 */
	public static function default_settings() {
		return [
			'app_key'                      => '',
			'secret'                       => '',
			'authenticated'                => false,
			'widget_location'              => 'footer',
			'language_code'                => 'en',
			'widget_tab_name'              => 'Reviews',
			'bottom_line_enabled_product'  => true,
			'qna_enabled_product'          => false,
			'bottom_line_enabled_category' => false,
			'yotpo_language_as_site'       => true,
			'show_submit_past_orders'      => true,
			'yotpo_order_status'           => 'wc-completed',
			'disable_native_review_system' => true,
			'debug_mode'                   => false,
			'debug_level'                  => 'info',
			'main_widget_hook'             => 'woocommerce_after_single_product',
			'main_widget_priority'         => 10,
			'product_bottomline_hook'      => 'woocommerce_single_product_summary',
			'product_bottomline_priority'  => 7,
			'product_qna_hook'             => 'woocommerce_single_product_summary',
			'product_qna_priority'         => 8,
			'category_bottomline_hook'     => 'woocommerce_after_shop_loop_item',
			'category_bottomline_priority' => 7,
			'timeframe_from'               => 90,
			'timeframe_to'                 => 0,
			'order_submission_method'      => 'hook',
			'widget_jsinject_selector'     => 'section#primary',
			'jsinject_selector_rating'     => '',
			'jsinject_selector_qna'        => '',
		];
	}

	/**
	 * Perform authentication check with given appkey and secret, update settings if successful.
	 *
	 * @param  string $appkey Yotpo appkey.
	 * @param  string $secret Yotpo secret.
	 * @return boolean        auth successful true or false
	 */
	public static function authenticate( string $appkey, string $secret ) {
		unset( $settings_instance );
		$api = YRFW_API_Wrapper::get_instance();
		$api->init( $appkey, $secret );
		$response = $api->get_token();
		if ( ! isset( $response ) ) {
			return false;
		}
		$settings = [
			'app_key'       => $appkey,
			'secret'        => $secret,
			'authenticated' => true,
		];
		self::update_settings( $settings, false );
		return true;
	}

	/**
	 * Reset authentication
	 *
	 * @return void
	 */
	public static function reset_authentication() {
		$settings = [
			'app_key'       => '',
			'secret'        => '',
			'authenticated' => false,
		];
		self::update_settings( $settings, false );
	}

	/**
	 * Convert settings to JSON
	 *
	 * @param  array $settings settings array to convert.
	 * @return string           jsonified settings
	 */
	public function jsonify_settings( array $settings ) {
		return wp_json_encode( $settings, JSON_PRETTY_PRINT );
	}
}
