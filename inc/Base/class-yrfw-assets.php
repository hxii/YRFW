<?php

/**
 * This class is responsible for loading all front and back-end assets.
 *
 * @package YotpoReviews
 */
class YRFW_Assets {

	/**
	 * Init loading assets.
	 */
	public function __construct() {
		$this->load_assets();
	}

	/**
	 * Loads assets based on current pags is admin or not
	 *
	 * @return void
	 */
	private function load_assets() {
		global $settings_instance;
		if ( ! is_admin() && true === $settings_instance['authenticated'] ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'assets_load_frontend' ), 5 );
		} else {
			add_action( 'admin_enqueue_scripts', array( $this, 'assets_load_admin' ), 5 );
		}
	}

	/**
	 * Loads all admin assets
	 *
	 * @param string $hook page string from WordPress.
	 * @return void
	 */
	public function assets_load_admin( $hook ) {
		if ( strpos( $hook, 'yotpo-reviews' ) !== false ) {
			wp_enqueue_style( 'hxii-log-stylesheet', ( YRFW_PLUGIN_URL . '/assets/css/hxii-log.css' ) );
			wp_enqueue_style( 'bootstrap_css', ( YRFW_PLUGIN_URL . '/assets/css/bootstrap-wrapper.css' ) );
			wp_enqueue_script( 'yotpoSettingsJs', ( YRFW_PLUGIN_URL . '/assets/js/settings.js' ) );
			wp_enqueue_script( 'bootstrap_js', ( YRFW_PLUGIN_URL . '/assets/js/bootstrap.bundle.min.js' ) );
		}
		wp_enqueue_style( 'yotpoSideLogoStylesheet' , ( YRFW_PLUGIN_URL . '/assets/css/side-menu-logo.css' ) );
	}

	/**
	 * Loads all front-end assets as well as preloading and prefetching
	 *
	 * @return void
	 */
	public function assets_load_frontend() {
		global $settings_instance;
		add_action( 'wp_head', array( $this, 'assets_preconnect_yotpo' ), 1 );
		add_action( 'wp_head', array( $this, 'assets_preload_js' ), 2 );
		wp_enqueue_script( 'yotpo_widget', '//staticw2.yotpo.com/' . $settings_instance['app_key'] . '/widget.js', '', null );
		wp_enqueue_style( 'bottomline_css', ( YRFW_PLUGIN_URL . '/assets/css/bottom-line.css' ) );
		add_filter( 'script_loader_tag', array( $this, 'append_async' ), 10, 2 );
	}

	/**
	 * Preload Yotpo widget JS file
	 *
	 * @return void
	 */
	public function assets_preload_js() {
		global $settings_instance;
		$version = $this->get_widget_version();
		if ( ! is_checkout() ) {
			echo '<link rel="preload" href="//staticw2.yotpo.com/' . $settings_instance['app_key'] . '/widget.js" as="script">';
			echo '<link rel="preload" href="//staticw2.yotpo.com/' . $settings_instance['app_key'] . '/widget.css?widget_version=' . $version . '" as="style">';
			echo '<link rel="preload" href="//staticw2.yotpo.com/assets/yotpo-widget-font.woff?version=' . $version . '" as="font" crossorigin="anonymous">';
		}
	}

	/**
	 * Preconnect for all Yotpo domains
	 *
	 * @return void
	 */
	public function assets_preconnect_yotpo() {
		echo '<link rel="preconnect" href="//staticw2.yotpo.com">';
		echo '<link rel="preconnect" href="//api.yotpo.com">';
		echo '<link rel="preconnect" href="//w2.yotpo.com">';
	}

	/**
	 * Append async attribute to Yotpo Widget
	 *
	 * @param string tag    $tag script tag.
	 * @param string handle $handle enqueue handle. We're only looking for 'yotpo_widget'.
	 * @return string filtered tag if handle matches.
	 */
	public function append_async( $tag, $handle ) {
		if ( 'yotpo_widget' === $handle ) {
			return str_replace( ' src', ' async="async" src', $tag );
		} else {
			return $tag;
		}
	}

	/**
	 * Get widget version for prefetch
	 *
	 * @return string widget version
	 */
	private function get_widget_version() {
		$version = get_transient( 'yotpo_widget_version' );
		if ( ! $version ) {
			global $settings_instance;
			if ( empty( $settings_instance['app_key'] ) ) { return; }
			$version = json_decode( wp_remote_retrieve_body( wp_remote_get( 'https://api.yotpo.com/widgetsmanager/v1/' . $settings_instance['app_key'] . '/account_widgets' ) ), true )['account_widgets'][0]['widget_semantic_version'] ?? '';
			set_transient( 'yotpo_widget_version', $version, HOUR_IN_SECONDS );
		}
		return $version;
	}

}