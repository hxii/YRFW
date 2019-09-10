<?php

class YRFW_API_Wrapper {

	private $curl;
	private $app_key;
	private $secret;
	private static $instance;
	private static $base_uri = 'https://api.yotpo.com/';

	private function __construct() {
		// nothing
	}

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new static;
		}
		return self::$instance;
	}

	public function init( string $app_key, string $secret ) {
		$this->app_key = $app_key;
		$this->secret  = $secret;
		$this->curl    = new dcai\curl();
	}

	public function get_base_uri() {
		return self::$base_uri;
	}

	public function get_curl() {
		return $this->curl;
	}

	public function get_token() {
		$payload = [
			'client_id'     => $this->app_key,
			'client_secret' => $this->secret,
			'grant_type'    => 'client_credentials',
		];
		$response = $this->curl->post( self::$base_uri . 'oauth/token', $payload );
		if ( 200 === $response->statusCode ) {
			return json_decode( $response->text )->access_token;
		}
	}

	public function submit_order( array $order ) {
		$this->curl->appendRequestHeader( 'Content-Type', 'application/json' );
		$response = $this->curl->post( self::$base_uri . "apps/$this->app_key/purchases/", json_encode( $order ) );
		return $response->statusCode;
	}

	public function submit_orders( array $orders ) {
		$this->curl->appendRequestHeader( 'Content-Type', 'application/json' );
		$response = $this->curl->post( self::$base_uri . "apps/$this->app_key/purchases/mass_create", json_encode( $orders ) );
		return $response->statusCode;
	}

	public function get_bottomline( int $product_id ) {
		$this->curl->appendRequestHeader( 'Content-Type', 'application/json' );
		$response = $this->curl->get( self::$base_uri . "products/$this->app_key/$product_id/bottomline" );
		return $response->text;
	}

}