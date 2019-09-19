<?php

require_once YRFW_PLUGIN_PATH . 'inc/Helpers/class-yrfw-csv-helper.php';

/**
 * Review export
 */
class YRFW_Export_Reviews extends YRFW_CSV_Helper {

	public function __construct( $data ) {
		parent::__construct( $data, $this->header_rows );
	}

	public function init() {
		// do nothing.
	}

	protected $header_rows = [
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

}

