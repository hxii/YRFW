<?php

/**
 * @package YotpoReviews
 * This class is responsible for registering admin pages and links.
 */
class YRFW_Admin {

	/**
	 * Add plugin page settings link and sidebar links.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_add_sidebar_links' ) );
		$basename = YRFW_BASENAME;
		add_filter( "plugin_action_links_$basename", array( $this, 'admin_add_settings_link' ) );
	}
	/**
	 * Add Yotpo admin and debug links to sidebar
	 */
	public function admin_add_sidebar_links() {
		global $settings_instance;
		add_menu_page( 'Yotpo Reviews', 'Yotpo Reviews', 'manage_options', 'yotpo-reviews', array( $this, 'admin_show_admin_page' ), $this->get_icon(), 100 );
		if ( $settings_instance['debug_mode'] ) {
			add_submenu_page( 'yotpo-reviews', 'Yotpo Debug', 'Debug', 'manage_options', 'yotpo-debug', array( $this, 'admin_show_debug_page' ) );
		}
	}
	/**
	 * Append settings link to links array for plugin in the plugins page in WordPress
	 *
	 * @param array $links links array from WordPress.
	 * @return array
	 */
	public function admin_add_settings_link( $links ) {
		$link = '<a href="admin.php?page=yotpo-reviews">' . esc_html__( 'Settings', 'yrfw' ) . '</a>';
		array_push( $links, $link );
		return $links;
	}
	/**
	 * Get sidebar icon in base64 SVG
	 *
	 * @return string
	 */
	private function get_icon() {
		return 'data:image/svg+xml;base64,' . base64_encode( file_get_contents( YRFW_PLUGIN_PATH . 'assets/images/logo.svg' ) );
	}
	/**
	 * Display admin/settings page
	 *
	 * @return void
	 */
	public function admin_show_admin_page() {
		require_once YRFW_PLUGIN_PATH . 'pages/template-admin.php';
	}

	/**
	 * Display debug page
	 *
	 * @return void
	 */
	public function admin_show_debug_page() {
		require_once YRFW_PLUGIN_PATH . 'pages/template-debug.php';
	}

}
