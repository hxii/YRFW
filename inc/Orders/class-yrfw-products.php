<?php

/**
 * @package YotpoReviews
 */

class YRFW_Products {

	public function __construct() {
		global $yotpo_settings;
	}

	/**
	 * Get and return product data for given product
	 *
	 * @param  object $_product WC product instace to get data for.
	 * @return array            product data
	 */
	public function get_product_data( WC_Product $_product ): array {
		global $yrfw_logger, $yotpo_settings, $settings_instance;
		$product = new WC_Product( $_product );
		if ( ! is_object( $product ) ) {
			return false;
		} else {
			return [
				'app_key'     => $settings_instance['app_key'],
				'id'          => ( $id = $product->get_id() ),
				'url'         => get_permalink( $id ),
				'lang'        => $settings_instance['yotpo_language_as_site'] ? explode( '-', get_bloginfo( 'language' ) )[0] : $settings_instance['language_code'],
				// 'description' => wp_strip_all_tags( substr( $product->get_short_description(), 0, 255 ) ),
				'description' => '',
				'name'        => $product->get_title(),
				// 'image'       => $this->get_product_image( $id ),
				'price'       => $product->get_price(),
				'specs'       => array_filter( [
					'external_sku' => $product->get_sku(),
					'upc'          => $product->get_attribute( 'upc' ) ?: null,
					'isbn'         => $product->get_attribute( 'isbn' ) ?: null,
					'brand'        => $product->get_attribute( 'brand' ) ?: null,
					'mpn'          => $product->get_attribute( 'mpn' ) ?: null,
				] ),
			];
		}
	}

	/**
	 * Get and return product images
	 *
	 * @param  integer $product_id product ID.
	 * @return string              product image URL
	 */
	public function get_product_image( int $product_id ): string {
		return wp_get_attachment_url( get_post_thumbnail_id( $product_id ) );
	}

}