<?php

/**
 * @package  YotpoReviews
 */
class YRFW_CSV_Helper {

	private $reviews;
	private $filename;
	private $fh;

	/**
	 * CSV Headers
	 */
	const ROWS = [
		'review_title',
		'review_content',
		'display_name',
		'user_email',
		'review_score',
		'date',
		'sku',
		'product_title',
		'product_description',
		'product_url',
		'product_image_url',
		'user_type',
	];

	/**
	 * Init class with filename and reviews
	 *
	 * @param array $reviews reviews array to export.
	 */
	public function __construct( array $reviews ) {
		$this->reviews  = &$reviews;
		$this->filename = 'review_export_' . date( 'Y-m-d' ) . '.csv';
		$this->fh       = fopen( YRFW_PLUGIN_PATH . $this->filename, 'w' );
	}

	/**
	 * Make sure the file is closed
	 */
	public function __destruct() {
		is_resource( $this->fh ) && fclose( $this->fh );
	}

	/**
	 * Write all reviews to CSV file and return filename
	 *
	 * @return string|bool return filename if successful and false if not
	 */
	public function generate_csv() {
		global $yrfw_logger;
		try {
			if ( $this->prepare_header_row() ) {
				foreach ( $this->reviews as $review ) {
					fputcsv( $this->fh, $review );
				}
			}
			fclose( $this->fh );
			return $this->filename;
		} catch ( Exception $e ) {
			$yrfw_logger->warn( 'Exporting reviews to CSV failed with ' . $e );
			return false;
		}
	}

	/**
	 * Write header to CSV
	 *
	 * @return callable
	 */
	private function prepare_header_row() {
		return fputcsv( $this->fh, self::ROWS );
	}

}
