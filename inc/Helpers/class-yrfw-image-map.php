<?php

/**
 * This class is responsible for building an array of images for all products for faster pulling.
 *
 * @package YotpoReviews
 */
class YRFW_Image_Map {
	private $image_map;

	/**
	 * Get images on creation
	 */
	public function __construct() {
		$this->image_map = $this->retrieve_images();
	}

	/**
	 * Get all product images from WPDB
	 *
	 * @return object images object by product_id as key
	 */
	private function retrieve_images() {
		global $wpdb;
		$prefix = $wpdb->prefix;
		$query  = "SELECT `post_id` as 'product_id', `guid` as 'image_url'
				FROM `" . $prefix . "postmeta`
				INNER JOIN `" . $prefix . "posts` on `ID` = `meta_value`
				WHERE `meta_key` = '_thumbnail_id'";
		return $wpdb->get_results( $query, 'OBJECT_K' );
	}

	/**
	 * Images getter function
	 *
	 * @param int $product_id product ID in case we want to get images for a specific ID.
	 * @return object product images object
	 */
	public function get_images( int $product_id = null ) {
		return ( ! is_null( $product_id ) ) ? $this->image_map[ $product_id ]->image_url : $this->image_map;
	}

}