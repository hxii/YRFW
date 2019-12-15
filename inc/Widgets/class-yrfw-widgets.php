<?php

/**
 * @package YotpoReviews
 */

class YRFW_Widgets {

	/**
	 * Return main widget HTML
	 *
	 * @param  boolean $check omit comments allowed check.
	 * @return string         widget html
	 */
	public static function main_widget( $check = true ) {
		global $product, $yotpo_cache;
		$show_widget = is_product() ? $product->get_reviews_allowed() === true : true;
		if ( $show_widget || ! $check ) {
			$product_data = $yotpo_cache->get_cached_product( $product->get_id() );
			return "<div class='yotpo yotpo-main-widget' data-product-id='$product_data[id]' data-name='$product_data[name]' data-url='$product_data[url]' data-image-url='$product_data[image]' data-description='$product_data[description]' data-lang='$product_data[lang]' data-price='{$product->get_price()}' data-currency='" . YRFW_CURRENCY . "'></div>";
		}
	}

	/**
	 * Return QA rating HTML
	 *
	 * @param  boolean $check omit comments allowed check.
	 * @return string         q&a widget html
	 */
	public static function qa_bottomline( $check = true ) {
		global $product, $yotpo_cache, $settings_instance;
		$show_bottom_line = is_product() ? $product->get_reviews_allowed() === true : true;
		if ( $show_bottom_line || ! $check ) {
			$product_data = $yotpo_cache->get_cached_product( $product->get_id() );
			return "<div class='yotpo QABottomLine' data-appkey='$settings_instance[app_key]' data-product-id='$product_data[id]'></div>";
		}
	}

	/**
	 * Return star rating widget
	 *
	 * @param  boolean $check omit comments allowed check.
	 * @return string         star rating widget html
	 */
	public static function bottomline( $check = true ) {
		global $product, $yotpo_cache;
		$show_bottom_line = is_product() ? $product->get_reviews_allowed() === true : true;
		if ( $show_bottom_line || ! $check ) {
			$product_data = $yotpo_cache->get_cached_product( $product->get_id() );
			return ( ( ! is_product() ) ? "<a href='$product_data[url]'>" : "<a href='#'>" ) . "<div class='yotpo bottomLine' data-product-id='$product_data[id]' data-url='$product_data[url]' data-lang='$product_data[lang]'></div></a>"; // Add this link as a feature?
		}
	}

	/**
	 * Output conversion tracking for Yotpo (both script and pixel)
	 *
	 * @param int $order_id the order id.
	 * @return void
	 */
	public static function conversion_tracking( int $order_id ) {
		global $settings_instance;
		$order             = wc_get_order( $order_id );
		$total             = $order->get_total();
		$currency          = YRFW_CURRENCY;
		$conversion_params = "app_key=$settings_instance[app_key]&order_id=$order_id&order_amount=$total&order_currency=$currency";
		echo "<script>yotpoTrackConversionData = {orderId: '$order_id', orderAmount: $total, orderCurrency: '$currency'}</script>";
		echo "<noscript><img
			src='https://api.yotpo.com/conversion_tracking.gif?$conversion_params'
			width='1'
			height='1'></img></noscript>";
	}

	/**
	 * Get widget HTML and inject via JS
	 *
	 * @return string html and JS injection script
	 */
	public static function js_inject_main_widget() {
		global $settings_instance;
		if ( is_product() ) {
			$selector = ! empty( $settings_instance['widget_jsinject_selector'] ) ? $settings_instance['widget_jsinject_selector'] : '#main';
			$html     = self::main_widget( false );
			return '<script type="text/javascript" id="yotpo_jsinject_widget">
			var e = jQuery("' . $selector . '");
			var d = document.createElement("div"); 
			var j = document.getElementById("yotpo_jsinject_widget");
			d.innerHTML = "' . $html . '";
			jQuery(d).appendTo(e);
			j.parentNode.removeChild(j);
			</script>';
		}
	}

	/**
	 * Get star/QA rating and inject via JS
	 *
	 * @param string $widget 'rating' for star rating and 'qa' for Q&A.
	 * @return string
	 */
	public static function js_inject_rating( string $widget ) {
		global $settings_instance;
		if ( is_product() ) {
			$selector = ! empty( $settings_instance[ "jsinject_selector_$widget" ] ) ? $settings_instance[ "jsinject_selector_$widget" ] : '';
			if ( 'rating' === $widget ) {
				$html = self::bottomline( false );
			} elseif ( 'qna' === $widget ) {
				$html = self::qa_bottomline( false );
			}
			return '<script type="text/javascript" id="yotpo_jsinject_' . $widget . '">
			var e = jQuery("' . $selector . '");
			var d = document.createElement("div");
			d.setAttribute("id", "yotpo_jsinject_' . $widget . '");
			var j = document.getElementById("yotpo_jsinject_' . $widget . '");
			d.innerHTML = "' . $html . '";
			// jQuery(d).appendTo(e);
			// jQuery("' . $selector . ' ").after(d);
			e.after(d);
			j.parentNode.removeChild(j);
			</script>';
		}
	}

}