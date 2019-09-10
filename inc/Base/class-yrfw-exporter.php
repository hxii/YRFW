<?php

/**
 * This class gets a list of customers that bought products and a list of comments that are reviews.
 * Return cross-referenced data in the form of a Yotpo-ready CSV for import.
 */
class YRFW_Review_Exporter {

	/** Stores image array for quick access. @var $image_map */
	public $image_map;

	/**
	 * Get reviews and generate CSV file with native reviews
	 *
	 * @return string|bool returns file name or false if failed
	 */
	public function exporter_export_reviews() {
		global $yrfw_logger;
		$export_time     = microtime( true );
		$this->image_map = new YRFW_Image_Map();
		$reviews         = $this->exporter_prepare_reviews();
		$csv_helper      = new YRFW_Export_Reviews( $reviews );
		$file            = $csv_helper->generate_csv();
		$yrfw_logger->debug( 'Exporting reviews took ' . ( microtime( true ) - $export_time ) . ' seconds.' );
		return $file ?: false;
	}

	/**
	 * Process both results and return export-ready reviews
	 *
	 * @return array processed reviews
	 */
	private function exporter_prepare_reviews() {
		$reviews     = $this->exporter_get_reviews();
		$customers   = $this->exporter_get_customers();
		$all_reviews = array();
		foreach ( $reviews as $review ) {
			$current_review                        = array();
			$review_content                        = $this->exporter_clean_content( $review->review_content );
			$current_review['review_title']        = $this->exporter_make_title( $review_content );
			$current_review['review_content']      = $review_content;
			$current_review['display_name']        = $this->exporter_clean_content( $review->display_name );
			$current_review['user_email']          = $review->user_email;
			$current_review['review_score']        = $review->review_score;
			$current_review['date']                = date( 'Y-m-d', strtotime( $review->date ) );
			$current_review['sku']                 = $review->product_id;
			$current_review['product_title']       = $this->exporter_clean_content( $review->product_title );
			$current_review['product_description'] = $this->exporter_clean_content( get_post( $review->product_id )->post_excerpt );
			$current_review['product_url']         = get_permalink( $review->product_id );
			$current_review['product_image_url']   = $this->image_map->get_images( $review->product_id );
			if ( array_search( $review->user_email, array_column( $customers, 'user_email' ), true ) !== false ) {
				$current_review['user_type'] = 'verified_buyer';
			} else {
				$current_review['user_type'] = 'anonymous';
			}
			$all_reviews[] = $current_review;
		}
		return $all_reviews;
	}

	/**
	 * Query the database for all native reviews
	 *
	 * @return object native reviews
	 */
	private function exporter_get_reviews() {
		global $wpdb;
		$prefix = $wpdb->prefix;
		$query  = '
		SELECT comment_post_ID AS product_id,
		comment_author AS display_name,
		comment_date AS date,
		comment_author_email AS user_email,
		comment_content AS review_content,
		meta_value AS review_score,
		post_content AS product_description,
		post_title AS product_title,
		user_id
		FROM `' . $prefix . 'comments`
		INNER JOIN `' . $prefix . 'posts` ON `' . $prefix . 'posts`.`ID` = `' . $prefix . 'comments`.`comment_post_ID`
		INNER JOIN `' . $prefix . 'commentmeta` ON `' . $prefix . 'commentmeta`.`comment_id` = `' . $prefix . 'comments`.`comment_ID`
		WHERE `post_type` = "product" AND meta_key="rating"';
		return $wpdb->get_results( $query );
	}

	/**
	 * Query the database for all customers' emails to determine
	 * if verified buyer or anonymous for reviews
	 *
	 * @return array customer emails
	 */
	private function exporter_get_customers() {
		global $wpdb;
		$prefix = $wpdb->prefix;
		$query  = '
		SELECT DISTINCT ' . $prefix . 'users.user_email FROM `' . $prefix . 'postmeta`
		INNER JOIN ' . $prefix . 'users on ' . $prefix . 'users.user_email = meta_value
		WHERE meta_key = "_billing_email"
		AND user_email != "";';
		return $wpdb->get_results( $query, ARRAY_A );
	}

	/**
	 * Remove shortcodes and html from review content. Replace breaks with newlines.
	 *
	 * @param  string $content the content to clean up.
	 * @return string          clean content
	 */
	private function exporter_clean_content( string $content ) {
		return wp_strip_all_tags( strip_shortcodes( ( preg_replace( '/<br(\s+)?\/?>/i', "\n", $content ) ) ) );
	}

	/**
	 * Return shorter version of content for title (since WP does not have review titles)
	 *
	 * @param  string $content content to shorten.
	 * @return string          short content as title
	 */
	private function exporter_make_title( $content ) {
		return ( substr( $content, 0, 120 ) );
	}
}
