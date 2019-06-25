<?php
/**
 * Add the "date sent to Yotpo" column and populate data from meta
 */
class YRFW_Order_Column {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_new_column' ) );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'populate_data' ), 10, 2 );
	}

	/**
	 * Add new column to WooCommerce orders
	 *
	 * @param array $columns existing columns.
	 * @return array
	 */
	public function add_new_column( $columns ) {
		$columns['yotpo_order_sent'] = __( 'Date sent to Yotpo', 'yrfw' );
		return $columns;
	}

	/**
	 * Populate column values for orders with date order was sent.
	 *
	 * @param string $column column name.
	 * @param int    $post_id post id.
	 * @return void
	 */
	public function populate_data( $column, $post_id ) {
		if ( 'yotpo_order_sent' === $column ) {
			echo esc_html( get_post_meta( $post_id, 'yotpo_order_sent', true ) );
		}
	}

}
