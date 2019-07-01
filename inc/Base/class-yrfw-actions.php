<?php

/**
 * @package YotpoReviews
 * This class is responsible for all actions/filters.
 * e.g. widgets, order fulfillment
 */
class YRFW_Actions {

	/**
	 * Performs all plugin actions
	 */
	public function __construct() {
		global $settings_instance;
		if ( true === $settings_instance['authenticated'] ) {
			add_action( 'init', array( $this, 'action_generate_product_cache' ), 9999 );
			add_action( 'woocommerce_order_status_changed', array( $this, 'action_submit_order' ), 99, 1 );
			add_filter( 'comments_open', array( $this, 'action_disable_reviews' ), null, 2 );
			if ( true === $settings_instance['bottom_line_enabled_product'] ) {
				add_action( 'woocommerce_single_product_summary', array( $this, 'action_show_star_rating_widget' ), 5 ); // add if statment.
			} elseif ( 'jsinject' === $settings_instance['bottom_line_enabled_product'] ) {
				add_action( 'wp_footer', array( $this, 'action_js_inject_star_rating' ) );
			}
			if ( true === $settings_instance['qna_enabled_product'] ) {
				add_action( 'woocommerce_single_product_summary', array( $this, 'action_show_qa_widget' ), 6 );// add if statment.
			} elseif ( 'jsinject' === $settings_instance['qna_enabled_product'] ) {
				add_action( 'wp_footer', array( $this, 'action_js_inject_qa' ) );
			}
			if ( true === $settings_instance['bottom_line_enabled_category'] ) {
				add_action( 'woocommerce_after_shop_loop_item', array( $this, 'action_show_star_rating_widget' ), 5 );
			}
			add_action( 'yotpo_scheduler_action', array( $this, 'action_perform_scheduler' ) );
			add_action( 'wp_dashboard_setup', array( $this, 'action_show_dashboard_widget' ) );
			add_action( 'woocommerce_thankyou', array( $this, 'action_show_conversion_tracking' ), 1, 1 );
			switch ( $settings_instance['widget_location'] ) {
				case 'footer':
					add_action( 'woocommerce_after_single_product', array( $this, 'action_show_main_widget' ) );
					break;
				case 'tab':
					add_action( 'woocommerce_product_tabs', array( $this, 'action_show_main_widget_tab' ) );
					add_filter( 'woocommerce_tab_manager_integration_tab_allowed', function() { return false; } );
					break;
				case 'jsinject': // @TODO: add setting?
					add_action( 'wp_footer', array( $this, 'action_js_inject_widget' ) );
					break;
				default:
					break;
			}
		}
	}

	/**
	 * Submit order once status equals status in settings, if the method is hook (as opposed to a schedule)
	 *
	 * @param  int $order_id order ID to submit.
	 * @return void
	 */
	public function action_submit_order( int $order_id ) {
		global $yotpo_orders, $settings_instance;
		if ( 'hook' === $settings_instance['order_submission_method'] || ! isset( $settings_instance['order_submission_method'] ) ) {
			$yotpo_orders->submit_single_order( $order_id );
		}
	}

	/**
	 * Show main widget at desired location
	 *
	 * @return void
	 */
	public function action_show_main_widget() {
		global $yotpo_widgets;
		echo $yotpo_widgets->main_widget();
	}

	/**
	 * Show star rating at desired location
	 *
	 * @return void
	 */
	public function action_show_star_rating_widget() {
		global $yotpo_widgets;
		echo $yotpo_widgets->bottomline();
	}

	/**
	 * Show Q&A widget at desired location
	 *
	 * @return void
	 */
	public function action_show_qa_widget() {
		global $yotpo_widgets;
		echo $yotpo_widgets->qa_bottomline();
	}

	/**
	 * Show main widget using JS injection
	 *
	 * @return void
	 */
	public function action_js_inject_widget() {
		global $yotpo_widgets;
		echo $yotpo_widgets->js_inject_main_widget();
	}

	/**
	 * Show star rating using JS injection
	 *
	 * @return void
	 */
	public function action_js_inject_star_rating() {
		global $yotpo_widgets;
		echo $yotpo_widgets->js_inject_rating( 'rating' );
	}

	/**
	 * Show star rating using JS injection
	 *
	 * @return void
	 */
	public function action_js_inject_qa() {
		global $yotpo_widgets;
		echo $yotpo_widgets->js_inject_rating( 'qna' );
	}

	/**
	 * Show main widget in a tab
	 *
	 * @param  array $tabs current tabs.
	 * @return array       tabs array with new tab appended.
	 */
	public function action_show_main_widget_tab( $tabs ) {
		global $product, $yotpo_widgets;
		if ( $product->get_reviews_allowed() ) {
			global $settings_instance;
			$tabs['yotpo_widget'] = array(
				'title'    => $settings_instance['widget_tab_name'],
				'priority' => 50,
				'callback' => array( $this, 'action_show_main_widget' ),
			);
		}
		return $tabs;
	}

	/**
	 * Perform scheduled submission of orders
	 *
	 * @return void
	 */
	public function action_perform_scheduler() {
		global $yotpo_scheduler, $yrfw_logger;
		$yrfw_logger->title( 'Starting scheduled order submission.' );
		$yotpo_scheduler->do_scheduler();
		$yrfw_logger->title( 'Finished scheduled order submission.' );
	}

	/**
	 * Show Yotpo widget on WordPress dashboard
	 *
	 * @return callable
	 */
	public function action_show_dashboard_widget() {
		return new YRFW_Dashboard_Widget();
	}

	/**
	 * Start up the product cache.
	 *
	 * @return void
	 */
	public function action_generate_product_cache() {
		global $yotpo_cache;
		$yotpo_cache = YRFW_Product_Cache::get_instance();
		$yotpo_cache->init( YRFW_PLUGIN_PATH . 'products.json' );
	}

	/**
	 * Conversion tracking pixel and script
	 *
	 * @param integer $order_id ther order id from the cart thank-you page.
	 * @return string
	 */
	public function action_show_conversion_tracking( int $order_id ) {
		global $yotpo_widgets;
		return $yotpo_widgets->conversion_tracking( $order_id );
	}

	/**
	 * Disable native reviews system
	 *
	 * @param  bool $open    are comments allowed.
	 * @param  int  $post_id post (product) id.
	 * @return bool          should we show native reviews.
	 */
	public function action_disable_reviews( $open, $post_id ) {
		global $settings_instance;
		if ( $settings_instance['disable_native_review_system'] ) {
			if ( get_post_type( $post_id ) === 'product' ) {
				return false;
			}
			return $open;
		}
	}
}
