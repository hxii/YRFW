<?php

class YRFW_Extensions {

	private static $instance;
	private $extensions;

	private function __construct() {
		// Do nothing
	}

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new static;
		}
		return self::$instance;
	}

	public function load_extensions() {
		try {
			foreach ( glob( YRFW_PLUGIN_PATH . 'extensions/*.php' ) as $file ) {
				require_once $file;
				$class = basename( $file, '.php' );	
				if ( class_exists ( $class ) ) {
					$extension = new $class();
					if ( ! method_exists( $extension, 'register_extension' ) ) {
						throw new Exception( 'Missing method register_extension()' );
					} else {
						$extension->register_extension();
					}
				}
			}
		} catch ( \Exception $e ) {
			global $yrfw_logger;
			$yrfw_logger->warn( "Extension " . basename( $file ) . " failed to load due to {$e->getMessage()}" );
		}

	}

	public function register_extension( $extension_meta ) {
		if ( ! is_array( $extension_meta ) ) {
			throw new Exception("Error Processing Request", 1);
		}
		if (
			! array_key_exists( 'extension_name', $extension_meta ) ||
			! array_key_exists( 'extension_version', $extension_meta ) ||
			! array_key_exists( 'extension_description', $extension_meta ) ||
			! array_key_exists( 'extension_author', $extension_meta )
			) {
				return;
		}
		$this->extensions[ $extension_meta['extension_name'] ] = [
			'extension_version'     => $extension_meta['extension_version'] ?? '1.0',
			'extension_description' => $extension_meta['extension_description'] ?? 'null',
			'extension_author'      => $extension_meta['extension_author'] ?? 'No Name',
			'extension_url'         => $extension_meta['extension_url'] ?? '#',
		];
	}

	public function list_extensions() {
		return $this->extensions;
	}

}