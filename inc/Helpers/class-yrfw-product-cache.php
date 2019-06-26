<?php

/**
 * This class is responsible for exporting products to a JSON file.
 */
class YRFW_Product_Cache {

	private $filename;
	private $product_cache;
	private static $instance;

	/**
	 * Do nothing
	 */
	private function __construct() {
		// Nothing.
	}

	/**
	 * Get instance of cache
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new static;
		}
		return self::$instance;
	}

	/**
	 * Init cache to the given $filename
	 *
	 * @param string $filename the filename to use for the product cache.
	 * @return void
	 */
	public function init( string $filename ) {
		$this->filename = $filename;
		if ( ! file_exists( $this->filename ) ) {
			$this->init_product_cache();
		}
		$this->product_cache = $this->get_product_cache();
		if ( ! isset( $this->product_cache['cache_generated_at'] ) || ! $this->product_cache['cache_generated_at'] >= date( 'd-m-Y h:i:s' ) ) {
			$this->init_product_cache();
			$this->product_cache = $this->get_product_cache();
		}
	}

	/**
	 * Initialize the cache.
	 *
	 * @return bool
	 */
	public function init_product_cache() {
		$products = $this->get_all_products_data();
		$this->save_products( $products );
		return true;
	}

	/**
	 * Append single product to file.
	 *
	 * @param int $product_id missing product id.
	 * @return array the appended product.
	 */
	public function create_missing_product( int $product_id ) {
		global $yotpo_products;
		$missing_product[ $product_id ]          = $yotpo_products->get_product_data( wc_get_product( $product_id ) );
		$missing_product[ $product_id ]['image'] = $yotpo_products->get_product_image( $product_id );
		$this->product_cache                     = $missing_product + $this->product_cache;
		if ( $this->save_products( $this->product_cache ) ) {
			return $missing_product[ $product_id ];
		}
	}

	/**
	 * Load up products from file to private.
	 *
	 * @return array return the cache.
	 */
	public function get_product_cache() {
		$products = json_decode( file_get_contents( $this->filename ), true );
		return $products;
	}

	/**
	 * Main saver function for products, can either rewrite or append depending on the situation.
	 *
	 * @param object $data products to append/write to the file.
	 * @param string $mode 'w' for write, 'a' for append.
	 * @return boolean
	 */
	public function save_products( $data, string $mode = 'w' ) {
		$products                       = $data;
		$products['cache_generated_at'] = date( 'd-m-Y h:i:s' );
		$products                       = wp_json_encode( $products, JSON_PRETTY_PRINT );
		$filesave                       = file_put_contents( $this->filename, $products );
		if ( false !== $filesave || -1 !== $filesave ) {
			return true;
		}
		return false;
	}

	/**
	 * Return cached product
	 *
	 * @param integer $product_id product ID to get information for.
	 * @return array
	 */
	public function get_cached_product( int $product_id ) {
		if ( ! isset( $this->product_cache[ $product_id ] ) ) {
			$missing_product     = $this->create_missing_product( $product_id );
			$this->product_cache = $this->get_product_cache();
			return $missing_product;
		} else {
			return $this->product_cache[ $product_id ];
		}
	}

	/**
	 * Return all available product IDs.
	 *
	 * @return array product ids.
	 */
	private function get_all_product_ids() {
		global $wpdb;
		$ids    = wp_cache_get( 'yotpo_product_cache' );
		$prefix = $wpdb->prefix;
		$query  = 'SELECT `ID`
			FROM `' . $prefix . 'posts`
			WHERE `post_type` = "product"';
		if ( ! $ids ) {
			wp_cache_set( 'yotpo_product_cache', $wpdb->get_results( $query, ARRAY_A ) );
			$ids = wp_cache_get( 'yotpo_product_cache' );
		}
		return $ids;
	}

	/**
	 * Get product data for all available products and return in JSON.
	 *
	 * @return mixed $data json ready product data.
	 */
	private function get_all_products_data() {
		global $yotpo_products;
		$ids      = $this->get_all_product_ids();
		$images   = ( new YRFW_Image_Map() )->get_images();
		$products = array();
		foreach ( $ids as $id ) {
			$products[ $id['ID'] ]          = $yotpo_products->get_product_data( wc_get_product( $id['ID'] ) );
			$products[ $id['ID'] ]['image'] = isset( $images[ $id['ID'] ] ) ? $images[ $id['ID'] ]->image_url : $yotpo_products->get_product_image( $id['ID'] );
		}
		unset( $images, $ids );
		return $products;
	}

}
