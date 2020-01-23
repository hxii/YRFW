<?php

/**
 * hxiilog
 * very simple, constantly evolving logger class for stuff and things
 * by Paul Glushak aka hxii
 * v. 0.2
 * http://github.com/hxii/
 */

defined( 'ABSPATH' ) || die();

define( 'HXII_LOGGER_VER', '0.2.1' );

class Hxii_Logger {
	public $filename;
	public $filepath;
	public $loglevel;
	public $logsize;
	public $date_format;
	private $filehandler;
	const LEVELS = array(
		'off'     => 0,
		'info'    => 100,
		'warning' => 200,
		'debug'   => 999,
	);

	public function __construct( string $file = 'debug.log', string $level = 'info', int $size = 1, string $date_format = '' ) {
		$this->filepath = $file;
		$this->filename = basename( $this->filepath );
		$this->loglevel = $level;
		$this->logsize  = ( $size * 1048576 );
		$this->date_format = ( ! empty( $date_format ) ) ? $date_format : 'Y-m-d H:i:s';
		$this->filehandler = fopen( $this->filepath, 'a' ) or $this->loglevel = 'off';
	}

	public function __destruct(){
		if ( $this->filehandler ) {
			fclose( $this->filehandler );
		}
	}

	public function debug( string $string ) {
		$this->write_to_file( $string, 'debug', 'D' );
	}

	public function info( string $string ) {
		$this->write_to_file( $string, 'info', 'I' );
	}

	public function warn( string $string ) {
		$this->write_to_file( $string, 'warning', 'W' );
	}

	public function title( string $string ) {
		$loggerstring = "═╡ $string ╞" . str_repeat( '═', ( 120 - strlen( $string ) ) );
		$this->write_to_file( $loggerstring, 'debug', 'D' );
	}

	public function read_log( int $lines = 50 ) {
		$tail = shell_exec( "tail -n $lines $this->filepath" );
		return $tail;
	}

	public function reset_log() {
		file_put_contents( $this->filepath, "");
	}

	public static function get_version() {
		return 'HXii Logger v.' . HXII_LOGGER_VER;
	}

	public function get_filename( $path ) {
		return ( $path ) ? $this->filename : $this->filepath;
	}

	private function write_to_file( $string, $level, $let ) {
		if ( self::LEVELS[ $level ] <= self::LEVELS[ $this->loglevel ] ) {
			$time = date( $this->date_format );
			$debug_line = print_r( debug_backtrace( 2 )[1]['line'], true );
			$debug_file = str_replace( YRFW_PLUGIN_PATH, '', print_r( debug_backtrace( 2 )[1]['file'], true ) );
			$debug      = ( self::LEVELS[ $this->loglevel ] >= 999 ) ? "[$debug_file:$debug_line]" : '';
			fwrite( $this->filehandler, "$time [$let] $string $debug" . PHP_EOL );
			if ( $this->check_log_size( $this->filepath ) ) {
				$this->rename_old_log();
			}
		}
	}

	private function rename_old_log() {
		if ( file_exists( $this->filepath ) ) {
			$path = pathinfo( $this->filepath );
			$int = '1';
			$newname = "$path[filename].$int";
			while ( file_exists( "$path[dirname]/$newname" ) ) {
				$int++;
				$newname = "$path[filename].$int";
			}
				rename( $this->filepath, $path['dirname'] . '/' . $newname );
		}
	}

	private function check_log_size( $file, $return_size = false ) {
		if ( $return_size ) {
			return array( filesize( $file ), $this->logsize );
		} else {
			return ( filesize( $file ) >= $this->logsize );
		}
	}
}
