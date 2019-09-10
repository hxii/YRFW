<?php

/**
 * CSV Exporter class
 */
abstract class YRFW_CSV_Helper {

	/**
	 * Data array to be exported
	 *
	 * @var array
	 */
	private $data;
	/**
	 * Filename to be used
	 *
	 * @var string
	 */
	private $filename;
	/**
	 * File handler
	 *
	 * @var void
	 */
	private $fh;
	/**
	 * CSV Header
	 *
	 * @var array
	 */
	private $header_rows;

	/**
	 * Name of child class
	 *
	 * @var string
	 */
	private $child;

	/**
	 * Constructor
	 *
	 * @param array $data data to be exported.
	 * @param array $rows header row.
	 */
	public function __construct( array $data, array $rows ) {
		$this->data        = &$data;
		$this->header_rows = &$rows;
		$this->child       = get_class( $this );
		$this->filename    = $this->child . '_' . date( 'Y-m-d_G-i' ) . '.csv';
		$this->fh          = fopen( YRFW_PLUGIN_PATH . $this->filename, 'w' );
		$this->init();
	}

	/**
	 * Make sure the file is closed
	 */
	public function __destruct() {
		is_resource( $this->fh ) && fclose( $this->fh );
	}

	/**
	 * Extra function to be run after construction.
	 *
	 * @return void
	 */
	abstract public function init();

	/**
	 * Write all data to CSV file and return filename
	 *
	 * @return string|bool return filename if successful and false if not
	 */
	public function generate_csv() {
		global $yrfw_logger;
		try {
			if ( $this->prepare_header_row() ) {
				foreach ( $this->data as $item ) {
					fputcsv( $this->fh, $item );
				}
			}
			fclose( $this->fh );
			return $this->filename;
		} catch ( Exception $e ) {
			$yrfw_logger->warn( 'Exporting to CSV failed with ' . $e );
			return false;
		}
	}

	/**
	 * Write header to CSV
	 *
	 * @return callable
	 */
	private function prepare_header_row() {
		return fputcsv( $this->fh, $this->header_rows );
	}

}