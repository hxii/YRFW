<?php

/**
 * Show rich snippet information on product pages.
 * Last updated format July 2nd, 2019
 */
class YRFW_Rich_Snippets {

	/**
	 * Get and prepare LD+JSON data
	 *
	 * @return bool|array false if product doesn't exist, array of data if exists
	 */
	private function get_data() {
		global $product, $yotpo_cache, $settings_instance;
		$product_id   = $product->get_id();
		$product_data = $yotpo_cache->get_cached_product( $product_id );
		$request_url  = "https://api.yotpo.com/products/$settings_instance[app_key]/$product_id/bottomline";
		$result       = ( 'HTTP/1.1 200 OK' === get_headers( $request_url )[0] ) ? file_get_contents( $request_url ) : null;
		if ( ! is_null( $result ) ) {
			$result = json_decode( $result );
		}
		if ( is_null( $result ) && $result->status->code !== '200' ) {
			return false;
		}
		$data = [
			'name'            => $product_data['name'],
			'sku'             => $product_data['specs']['external_sku'],
			'itemCondition'   => 'http://schema.org/NewCondition',
			'description'     => $product_data['description'],
			'offers'          => [
				'@type'           => 'Offer',
				'url'             => $product_data['url'],
				'availability'    => 'https://schema.org/' . ( $product->is_in_stock() ? 'InStock' : 'OutOfStock' ),
				'price'           => $product_data['price'],
				'priceCurrency'   => YRFW_CURRENCY,
				'priceValidUntil' => date( 'Y-12-31', current_time( 'timestamp', true ) + YEAR_IN_SECONDS ),	
			],
			'aggregateRating' => [
				'@type'       => 'AggregateRating',
				'ratingValue' => $result->response->bottomline->average_score ?: 0,
				'ratingCount' => $result->response->bottomline->total_reviews ?: 0,
			],
		];
		return $data;
	}

	/**
	 * Output LD+JSON data
	 *
	 * @return void
	 */
	public function do_richsnippet() {
		?>
		<script type="application/ld+json">
			{
			<?php echo json_encode( $this->get_data(), JSON_PRETTY_PRINT ); ?>
			}
		</script>
		<?php
	}

}