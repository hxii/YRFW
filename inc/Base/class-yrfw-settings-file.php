<?php

class YRFW_Settings_File {

	/**
	 * The instance
	 *
	 * @var class
	 */
	private static $instance;
	/**
	 * Settings filename
	 *
	 * @var string
	 */
	public static $filename;

	/**
	 * Make sure this cannot be instantiated
	 */
	private function __construct() {
		// donothing.
	}

	/**
	 * Instance
	 *
	 * @param string $filename the settings filename.
	 * @return class
	 */
	public static function get_instance( string $filename = 'settings.json' ) {
		if ( null === self::$instance ) {
			self::$instance = new static();
			self::$filename = YRFW_PLUGIN_PATH . $filename;
		}
		return self::$instance;
	}

	/**
	 * Get settings from file and secret from get_option
	 *
	 * @return array settings array
	 */
	public function get_settings() {
		if ( ! file_exists( self::$filename ) ) {
			if ( get_option( 'yotpo_settings' ) ) {
				$this->migrate_settings();
			} else {
				$this->set_settings( $this->get_default_settings(), false, false );
			}
			$settings = file_get_contents( self::$filename );
		} else {
			$settings = file_get_contents( self::$filename );
			$settings = json_decode( $settings, true );
		}
		$settings['secret'] = get_option( 'yotpo_secret', '' );
		return $settings;
	}

	/**
	 * Set/update settings to file. Updating settings will first get existing settings, then replace
	 * values of existing keys with new values.
	 * Secret is saved via update_option.
	 *
	 * @param array   $settings the settings to save.
	 * @param boolean $process set true/false strings to boolean values.
	 * @param boolean $update update settings on top of existing settings.
	 * @return void
	 */
	public function set_settings( array $settings, bool $process, bool $update ) {
		// Unset non-valid keys and sanitize data.
		$default_settings = $this->get_default_settings();
		foreach ( $settings as $setting => $data ) {
			if ( ! array_key_exists( $setting, $default_settings ) ) {
				unset( $settings[ $setting ] );
			} else {
				$settings[ $setting ] = sanitize_text_field( $data );
			}
		}
		global $yotpo_scheduler;	
		if ( true === $process ) {
			foreach ( $settings as $key => $value ) {
				if ( 'true' === $value || '1' === $value ) {
					$settings[ $key ] = true;
				} elseif ( 'false' === $value ) {
					$settings[ $key ] = false;
				}
			}
		}
		if ( true === $update ) {
			$current_settings = $this->get_settings();
			foreach ( $settings as $key => $value ) {
				if ( isset( $current_settings[ $key ] ) ) {
					$current_settings[ $key ] = $value;
				}
			}
			$settings = $current_settings;
		}
		if ( isset( $settings['secret'] ) ) {
			update_option( 'yotpo_secret', $settings['secret'] );
			unset( $settings['secret'] );
		}
		$sched = $settings['order_submission_method'];
		$settings = wp_json_encode( $settings, JSON_PRETTY_PRINT );
		file_put_contents( self::$filename, $settings );
		if ( 'schedule' === $sched ) {
			$yotpo_scheduler->set_scheduler();
		} elseif ( 'schedule' !== $sched ) {
			if ( ! isset( $yotpo_scheduler ) ) {
				$yotpo_scheduler = new YRFW_Scheduler();
			}
			$yotpo_scheduler->clear_scheduler();
		}
	}

	/**
	 * Default settings
	 *
	 * @return array settings array
	 */
	public function get_default_settings() {
		return [
			'app_key'                      => '',
			'secret'                       => 'secret',
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
	 * Migrate settings from DB to file based
	 *
	 * @return void
	 */
	public function migrate_settings() {
		$settings = get_option( 'yotpo_settings' );
		add_option( 'yotpo_secret', $settings['secret'] );
		delete_option( 'yotpo_settings' );
		$this->set_settings( $this->get_default_settings(), false, false );
		$this->set_settings( $settings, false, true );
	}

	/**
	 * Reset authentication by clearing the flag and removing the app_key and secret
	 *
	 * @return void
	 */
	public function reset_authentication() {
		$settings = [
			'app_key'       => '',
			'secret'        => '',
			'authenticated' => false,
		];
		$this->set_settings( $settings, false, true );
	}

	public function authenticate( string $appkey, string $secret ) {
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
		$this->set_settings( $settings, true, true );
		return true;
	}
}