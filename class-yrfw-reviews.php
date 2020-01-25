<?php

/*
	Plugin Name: Yotpo Reviews for WooCommerce
	Description: Yotpo Social Reviews helps Woocommerce store owners generate a ton of reviews for their products. Yotpo is the only solution which makes it easy to share your reviews automatically to your social networks to gain a boost in traffic and an increase in sales.
	Author: Paul Glushak (hxii)
	Version: 2.0.4
	Author URI: https://github.com/hxii/
	Plugin URI: https://github.com/hxii/YRFW
	WC requires at least: 3.1.0
	WC tested up to: 3.9.0
	Text Domain: yrfw
	Domain Path: /languages

	This plugin relies on getting and receiving data to and from Yotpo and associated Yotpo domains (staticw2.yotpo.com, api.yotpo.com etc.).
	More information about Yotpo can be found here:
		https://www.yotpo.com/
		https://www.yotpo.com/terms-of-service/
		https://www.yotpo.com/privacy-policy/
*/

defined( 'ABSPATH' ) || die();

/**
 * Const definitions
 */
define( 'YRFW_PLUGIN_VERSION', '2.0.4' );
define( 'YRFW_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'YRFW_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'YRFW_BASENAME', plugin_basename( __FILE__ ) );
define( 'YRFW_WC_VERSION', '3.1.0' );
define( 'YRFW_PHP_VERSION', '7.2.0' );

/**
 * Load all classes
 */
require_once YRFW_PLUGIN_PATH . 'inc/Base/class-yrfw-activate.php';
require_once YRFW_PLUGIN_PATH . 'inc/Base/class-yrfw-actions.php';
require_once YRFW_PLUGIN_PATH . 'inc/Base/class-yrfw-admin.php';
require_once YRFW_PLUGIN_PATH . 'inc/Base/class-yrfw-messages.php';
require_once YRFW_PLUGIN_PATH . 'inc/Base/class-yrfw-assets.php';
require_once YRFW_PLUGIN_PATH . 'inc/Base/class-yrfw-logger.php';
require_once YRFW_PLUGIN_PATH . 'inc/Base/class-yrfw-exporter.php';
require_once YRFW_PLUGIN_PATH . 'inc/Base/class-yrfw-settings-file.php';
require_once YRFW_PLUGIN_PATH . 'inc/Base/class-yrfw-extensions.php';
require_once YRFW_PLUGIN_PATH . 'inc/Orders/class-yrfw-products.php';
require_once YRFW_PLUGIN_PATH . 'inc/Orders/class-yrfw-orders.php';
require_once YRFW_PLUGIN_PATH . 'inc/Orders/class-yrfw-past-orders.php';
require_once YRFW_PLUGIN_PATH . 'inc/Orders/class-yrfw-scheduler.php';
require_once YRFW_PLUGIN_PATH . 'inc/Widgets/class-yrfw-widgets.php';
require_once YRFW_PLUGIN_PATH . 'inc/Helpers/class-yrfw-image-map.php';
require_once YRFW_PLUGIN_PATH . 'inc/Helpers/class-yrfw-product-cache.php';
require_once YRFW_PLUGIN_PATH . 'inc/Helpers/class-yrfw-api-wrapper.php';
require_once YRFW_PLUGIN_PATH . 'inc/Helpers/class-yrfw-order-column.php';
require_once YRFW_PLUGIN_PATH . 'inc/Helpers/curl.php';

global $yrfw_logger, $settings_instance;
$settings_instance = ( YRFW_Settings_File::get_instance() )->get_settings();
$yrfw_logger       = new Hxii_Logger( YRFW_PLUGIN_PATH . 'yotpo_debug.log', ( isset( $settings_instance['debug_level'] ) ) ? $settings_instance['debug_level'] : 'info' );
if ( $yrfw_logger->err ) {
	new YRFW_Messages( '<strong>Yotpo Reviews for WooCommerce</strong> - Unable to open log file. Please check permissions/ownership on the <code>yotpo_debug.log</code> file. Debugging has been disabled', 'error', false, true );
}

/**
 * Main class
 */
class YRFW_Reviews {

	/**
	 * Is WooCommerce available?
	 */
	public function __construct() {
		global $yrfw_logger;
		if ( defined( 'WC_VERSION' ) ) {
			$this->load_textdomain();
			$this->check_dependecies();
		} else {
			$yrfw_logger->info( 'class \'woocommerce\' doesn\'t exist!' );
		}
	}

	/**
	 * Activation function
	 *
	 * @return void
	 */
	public function activate() {
		$activate = new YRFW_Activate();
		$activate->activate();
	}

	/**
	 * Deactivation function
	 *
	 * @return void
	 */
	public function deactivate() {
		$scheduler = new YRFW_Scheduler();
		$scheduler->clear_scheduler();
	}

	/**
	 * Load the languages
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'yrfw', false, dirname( YRFW_BASENAME ) . '/languages/' );
	}

	/**
	 * Initialize all classes
	 *
	 * @return void
	 */
	private function init() {
		global $yotpo_settings, $yotpo_scheduler, $yotpo_actions, $yotpo_products, $yotpo_widgets, $yotpo_orders;
		define( 'YRFW_CURRENCY', get_woocommerce_currency() );
		new YRFW_Admin();
		new YRFW_Assets();
		new YRFW_Order_Column();
		$yotpo_orders     = new YRFW_Orders();
		$yotpo_widgets    = new YRFW_Widgets();
		$yotpo_products   = new YRFW_Products();
		$yotpo_settings   = YRFW_Settings_File::get_instance();
		$yotpo_scheduler  = new YRFW_Scheduler();
		$yotpo_actions    = new YRFW_Actions();
		$yotpo_extensions = YRFW_Extensions::get_instance();
		$yotpo_extensions->load_extensions();
	}

	/**
	 * Checking dependencies for WooCommerce and PHP
	 *
	 * @return void
	 */
	private function check_dependecies() {
		if ( version_compare( WOOCOMMERCE_VERSION, YRFW_WC_VERSION, '>=' ) && version_compare( phpversion(), YRFW_PHP_VERSION, '>=' ) ) {
			$this->init();
		} else {
			new YRFW_Messages(
				/* translators: 1:current WC version 2:current PHP version 3:required WC version 4:required PHP version */
				sprintf( __( '<strong>Yotpo Reviews for WooCommerce -</strong> You are using WooCommerce %1$s and PHP %2$s. <strong>WooCommerce %3$s and above with PHP %4$s and above are required.</strong>', 'yrfw' ), WOOCOMMERCE_VERSION, phpversion(), YRFW_WC_VERSION, YRFW_PHP_VERSION ),
				'error',
				false,
				true
			);
		}
	}

}
add_action( 'plugins_loaded', 'yrfw_initialize' );

function yrfw_initialize() {
	$yotpo = new YRFW_Reviews();
	register_activation_hook( __FILE__, array( $yotpo, 'activate' ) );
	register_deactivation_hook( __FILE__, array( $yotpo, 'deactivate' ) );
}
