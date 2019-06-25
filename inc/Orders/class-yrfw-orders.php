<?php

/**
 * @package YotpoReviews
 */
class YRFW_Orders {
	/**
	 * Submit single order to Yotpo api
	 *
	 * @param  int  $order_id order ID to submit.
	 * @param  bool $retry To prevent going into a loop, we will only try to get a new utoken once.
	 * @return boolean        order submission success
	 */
	public function submit_single_order( int $order_id, bool $retry = false ) {
		global $yrfw_logger, $settings_instance;
		$order        = wc_get_order( $order_id );
		$order_status = "wc-{$order->get_status()}";
		$time         = microtime( true );
		$yrfw_logger->debug( "Order #$order_id changed status to $order_status, should be $settings_instance[yotpo_order_status]" );
		if ( $settings_instance['yotpo_order_status'] === $order_status ) {
			$yrfw_logger->debug( "Starting submission of order #$order_id" );
			if ( ! empty( $settings_instance['app_key'] ) && ! empty( $settings_instance['secret'] ) ) {
				$order_data = $this->get_single_order_data( $order );
				if ( ! is_null( $order_data ) && is_array( $order_data ) ) {
					$curl = YRFW_API_Wrapper::get_instance();
					$curl->init( $settings_instance['app_key'], $settings_instance['secret'] );
					$api_time  = microtime( true );
					$api_token = $this->get_token_from_cache( false );
					$yrfw_logger->debug( 'Getting API token took ' . ( microtime( true ) - $api_time ) . ' seconds to complete.' );
					if ( ! empty( $api_token ) ) {
						$order_data['utoken']            = $api_token;
						$order_data['platform']          = 'woocommerce';
						$order_data['extension_version'] = YRFW_PLUGIN_VERSION;
						try {
							$create_time = microtime( true );
							$response    = $curl->submit_order( $order_data );
							$yrfw_logger->debug( 'Order submission took ' . ( microtime( true ) - $create_time ) . ' seconds to complete.' );
							if ( 200 === $response ) {
								$yrfw_logger->info( "Order #$order_id submitted with response $response" );
								$yrfw_logger->debug( 'The whole process took ' . ( microtime( true ) - $time ) . ' seconds to complete.' );
								$this->update_order_meta( $order_id );
							} elseif ( 401 === $response['code'] && ! retry ) {
								$yrfw_logger->warn( "Order #$order_id failed with response " . ( print_r( $response['code'], true ) ) );
								throw new Exception( 'Access Denied', 401 );
							} else {
								$yrfw_logger->warn( "Order #$order_id failed with response " . ( print_r( $response['code'], true ) ) );
								return false;
							}
						} catch ( \Throwable $th ) {
							if ( 401 === $th->getCode() ) {
								$this->get_token_from_cache( true );
								$this->submit_single_order( $order_id, true );
							}
						}
						unset( $order_data, $response, $api, $api_token );
					}
				}
			}
		}
	}

	/**
	 * Get single order information
	 *
	 * @param  int $order the order ID.
	 * @return array product data array
	 */
	public function get_single_order_data( WC_Order $order ) {
		global $yrfw_logger, $yotpo_cache, $settings_instance;
		$order_time   = microtime( true );
		$order_data   = array();
		$products_arr = array();
		$order_id     = $order->get_id();
		// if ( ! $order ) {
		// 	return;
		// }
		$order_data['order_date'] = date( 'Y-m-d H:i:s', strtotime( $order->get_date_created() ) );
		$email                    = $order->get_billing_email();
		if (
			! empty( $email )
			&& ! preg_match( '/\d$/', $email )
			&& filter_var( $email, FILTER_VALIDATE_EMAIL )
			&& strlen( substr( $email, strrpos( $email, '.' ) ) ) >= 3
		) {
			$order_data['email'] = $email;
		} else {
			$yrfw_logger->warn( "Order #$order_id Dropped - Invalid Email ($email)" );
			return;
		}
		$name = trim( "{$order->get_billing_first_name()} {$order->get_billing_last_name()}" );
		if ( ! empty( $name ) ) {
			$order_data['customer_name'] = $name;
		} else {
			$yrfw_logger->warn( "Order #$order_id Dropped - Invalid Name ($name)" );
			return;
		}
		$order_data['order_id'] = $order_id;
		$order_data['currency'] = YRFW_CURRENCY;
		$yrfw_logger->debug( "┌ Order #$order_data[order_id] Date: $order_data[order_date] Email: $order_data[email]" );
		$items = $order->get_items();
		if ( empty( $items ) ) {
			$yrfw_logger->warn( "Order #$order_id Dropped - No Products" );
			return;
		}
		foreach ( $items as $item ) {
			if ( '0' === $item['product_id'] ) {
				$yrfw_logger->warn( "Order #$order_id Dropped - Invalid Product (ID of 0)" );
				return;
			}
			$parent_id    = $item->get_product()->get_parent_id();
			$product_id   = ( 0 !== $parent_id ) ? $parent_id : $item['product_id'];
			$variation_id = $item->get_variation_id();
			$quantity     = $item['qty'];
			$product_time = microtime( true );
			$_product     = $yotpo_cache->get_cached_product( $product_id );
			if ( ! $_product ) { return; }
			$yrfw_logger->debug( 'Getting product data took ' . ( microtime( true ) - $product_time ) . ' seconds.' );
			$product_data            =& $_product;
			$product_data['app_key'] = $settings_instance['app_key'];
			$product_data['price']   = ( $product_data['price'] ?: 0 ) * $quantity; // WIP - To be fixed.
			if ( 0 !== $variation_id ) {
				$product_data['custom_properties']['name']  = 'variant';
				$product_data['custom_properties']['value'] = ( wc_get_product( $variation_id ) )->get_name();
			}
			$products_arr[ $item['product_id'] ] = $product_data;
			$yrfw_logger->debug( "├─ Product: $product_data[name], ID: $product_id, Price: $product_data[price] $order_data[currency], Quantity: $item[qty], Image: $product_data[image]" );
		}
		$order_data['products'] = $products_arr;
		unset( $specs, $products_arr, $order, $items );
		$yrfw_logger->debug( 'Preparing order data took ' . ( microtime( true ) - $order_time ) . ' seconds to complete.' );
		return $order_data;
	}

	/**
	 * Returning auth token from cache (transient) or setting it if not available.
	 *
	 * @param boolean $refresh_token should we force refresh the token.
	 * @return string
	 */
	public function get_token_from_cache( bool $refresh_token = false ) {
		global $settings_instance, $yrfw_logger;
		$token = get_transient( 'yotpo_utoken' );
		if ( false === $token || $refresh_token ) { 
			$api = YRFW_API_Wrapper::get_instance();
			$api->init( $settings_instance['app_key'], $settings_instance['secret'] );
			$token = $api->get_token();
			if ( ! empty( $token ) ) {
				set_transient( 'yotpo_utoken', $token, WEEK_IN_SECONDS );
				$yrfw_logger->debug( "Got new token $token." );
			}
		}
		return $token;
	}

	/**
	 * Update transient with last order sent and set flag (post meta) for order ID to prevent increasing order counter.
	 *
	 * @param int $order_id the order ID.
	 * @return void
	 */
	public function update_order_meta( &$order_id ) {
		set_transient( 'yotpo_last_sent_order', date( 'Y-m-d H:i:s' ), 2 * WEEK_IN_SECONDS );
		if ( ! get_post_meta( $order_id, 'yotpo_order_sent' ) ) {
			set_transient( 'yotpo_total_orders', get_transient( 'yotpo_total_orders' ) + 1 );
			add_post_meta( $order_id, 'yotpo_order_sent', date( 'Y-m-d H:i:s' ) );
		}
	}
}