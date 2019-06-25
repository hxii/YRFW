<?php

/**
 * @package YotpoReviews
 */
class YRFW_Past_Orders extends YRFW_Orders {
	public $product_images;

	/**
	 * Class constructor
	 * Instantiate image mapper class and get product images.
	 */
	public function __construct() {
		global $product_images;
		$image_map      = new YRFW_Image_Map();
		$product_images = $image_map->get_images();
	}

	/**
	 * Get all orders for given timeframe
	 *
	 * @param array $timeframe timeframe array represented by 'to' and 'from' keys.
	 * @return array             order ID array
	 */
	public function get_orders_for_timeframe( array $timeframe ) : array {
		global $yrfw_logger;
		if ( isset( $timeframe['to'], $timeframe['from'] ) ) {
			$yrfw_logger->debug( "Timeframe is from $timeframe[from] to $timeframe[to]" );
			$args = array(
				'limit'        => -1,
				'status'       => 'completed', // @ToDo: add custom status support
				'type'         => 'shop_order',
				'date_created' => "$timeframe[to]...$timeframe[from]",
			);
			$query       = new WC_Order_Query( $args );
			$order_array = $query->get_orders();
			$yrfw_logger->debug( 'Got ' . count( $order_array ) . ' orders' );
			return $order_array;
		}
	}

	/**
	 * Get order information and chunk them for given timeframe (def. 90 days)
	 *
	 * @param integer $days how many days to query for.
	 * @return array        orders with info divided by chunks
	 */
	public function get_past_orders( int $days = 90 ) : array {
		global $yrfw_logger, $product_map;
		$timeframe   = [
			'to'   => date( 'Y-m-d', strtotime( '-' . $days . ' days' ) ),
			'from' => date( 'Y-m-d' ), // today.
		];
		$order_array = $this->get_orders_for_timeframe( $timeframe );
		$past_orders = array();
		foreach ( $order_array as $order ) {
			$order_data = parent::get_single_order_data( $order );
			if ( ! is_null( $order_data ) ) {
				$past_orders[] = $order_data;
			}
		}
		unset( $query, $orders );
		if ( count( $past_orders ) > 0 ) {
			$chunks = array_chunk( $past_orders, 300 );
			$result = array();
			foreach ( $chunks as $index => $chunk ) {
				$result[ $index ] = array(
					'orders'            => $chunk,
					'platform'          => 'woocommerce',
					'extension_version' => YRFW_PLUGIN_VERSION,
				);
			}
			unset( $past_orders, $product_map );
		}
		return $result;
	}

	/**
	 * Submits all orders provided to API
	 *
	 * @param  array $past_orders orders to submit by API.
	 * @return void
	 */
	public function submit_past_orders( array $past_orders ) {
		global $yrfw_logger, $settings_instance, $yotpo_products;
		$time = microtime( true );
		$yrfw_logger->title( 'STARTING PAST ORDER SUBMISSION' );
		if ( ! empty( $settings_instance['app_key'] ) && ! empty( $settings_instance['secret'] ) ) {
			if ( is_null( $past_orders ) || empty( $past_orders ) ) { return; }
			$yrfw_logger->debug( 'Got ' . count( $past_orders ) . ' batches, sending...' );
			$api = YRFW_API_Wrapper::get_instance();
			$api->init( $settings_instance['app_key'], $settings_instance['secret'] );
			$api_token = parent::get_token_from_cache( true );
			if ( empty( $api_token ) ) { return; }
			foreach ( $past_orders as $index => $post_bulk ) if ( ! is_null( $post_bulk ) ) {
				$post_bulk['utoken'] = $api_token;
				$response            = $api->submit_orders( $post_bulk );
				if ( 200 !== $response ) {
					$yrfw_logger->warn( 'Batch ' . ( $index + 1 ) . ' failed with response ' . ( print_r( $response, true ) ) );
					$yrfw_logger->debug( wp_json_encode( $post_bulk, JSON_PRETTY_PRINT ) );
					new YRFW_Messages( esc_html__( 'Past orders submission failed!', 'yrfw' ), 'danger' );
					return;
				} else {
					$yrfw_logger->debug( 'Batch ' . ( $index + 1 ) . " sent successfully with response $response" );
				}
				unset( $post_bulk );
			}
			new YRFW_Messages( esc_html__( 'Past orders submitted successfully!', 'yrfw' ), 'success' );
			$yrfw_logger->debug( 'Submitting past orders took ' . ( microtime( true ) - $time ) . ' seconds to complete.' );
			$yrfw_logger->title( 'FINISHING PAST ORDER SUBMISSION' );
			unset( $past_orders, $response, $api, $api_token, $PROD );
		} else {
			$error_appkey = new YRFW_Messages( esc_html__( 'You have to set up your app key and secret!', 'yrfw' ), 'error' );
		}
	}

}