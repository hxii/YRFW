<div class="container py-3">
	<h3><?php esc_html_e( 'Widgets', 'yrfw' ); ?></h3>
	<form method="post" accept-charset="utf-8" id="yotpo_widget_settings">
		<?php wp_nonce_field( 'widgets', 'yotpo_widgets_form' ) ?>
		<div class="form-group">
			<label for="yotpo-location"><?php esc_html_e( 'Select a location for the reviews widget', 'yrfw' ); ?></label>
			<select class="form-control col-5" id="yotpo-location" name="widget_location">
				<option value="tab" <?php echo selected( $settings['widget_location'], 'tab', false ); ?>><?php esc_html_e( 'Tab', 'yrfw' ); ?></option>
				<option value="footer" <?php echo selected( $settings['widget_location'], 'footer', false ); ?>><?php esc_html_e( 'After Product', 'yrfw' ); ?></option>
				<option value="other" <?php echo selected( 'other', $settings['widget_location'], false ); ?>><?php esc_html_e( 'Custom', 'yrfw' ); ?></option>
				<option value="jsinject" <?php echo selected( 'jsinject', $settings['widget_location'], false ); ?>>JS Inject (Beta)</option>
			</select>
		</div>
		<div class="form-group" style="display: none;" id="tab_name">
			<label for="widget_tab_name"><?php esc_html_e( 'Please choose a name for the reviews tab', 'yrfw' ); ?></label>
			<input type="text" class="form-control" name="widget_tab_name" placeholder="Tab Name" value="<?php echo $settings['widget_tab_name'] ?: 'Reviews' ?>" id="widget_tab_name">
		</div>
		<div class="form-group" style="display: none;" id="jsinject_selector">
			<label for="widget_tab_name"><?php esc_html_e( 'Please type in the selector to use (jQuery)', 'yrfw' ); ?></label>
			<input type="text" class="form-control" name="widget_jsinject_selector" placeholder="jQuery Selector" value="<?php echo $settings['widget_jsinject_selector'] ?: '#main' ?>" id="widget_jsinject_selector">
		</div>
		<div class="collapse" id="tab_custom_location">
			<div class="card card-body">
				<?php esc_html_e( 'In order to show the main Yotpo widget in a custom location, you can use the following function:', 'yrfw' ); ?>
				<pre>function() { $widgets = new YRFW_Widgets; $widgets->main_widget(); }</pre>
				<?php esc_html_e( 'Take note this is an example, and it is advised to seek the help of a developer.', 'yrfw' ); ?>
			</div>
		</div>
		<div class="form-group custom-control custom-switch">
			<input type="hidden" name="bottom_line_enabled_product" value="false">
			<input type="checkbox" class="custom-control-input" value="true" name="bottom_line_enabled_product" <?php echo checked( true, $settings['bottom_line_enabled_product'], false ); ?> id="bottom_line_enabled_product">
			<label class="custom-control-label" for="bottom_line_enabled_product"><?php esc_html_e( 'Show star rating on product pages?', 'yrfw' ); ?><span class="dashicons dashicons-editor-help" data-toggle="tooltip" data-placement="top" data-html="true" title="Rating in product pages looks like this <img src='http://wcdev.local/wp-content/plugins/yotpo-dev/assets/images/product-bottomline.png' style='display:block;width:150px;' width='150px'>"></span></label>
			<div id="stars_jsinject" style="display: none">
				<input type="checkbox" class="custom-control-input" value="true" name="stars_jsinject_check" id="stars_jsinject_check">
				<label class="custom-control-label" for="stars_jsinject_check">Try injecting with JS?</label>
			</div>
		</div>
		<div class="form-group custom-control custom-switch">
			<input type="hidden" name="qna_enabled_product" value="false">
			<input type="checkbox" class="custom-control-input" value="true" name="qna_enabled_product" <?php echo checked( true, $settings['qna_enabled_product'], false ); ?> id="qna_enabled_product">
			<label class="custom-control-label" for="qna_enabled_product"><?php esc_html_e( 'Show Q&A rating on product pages?', 'yrfw' ); ?><span class="dashicons dashicons-editor-help" data-toggle="tooltip" data-placement="top" data-html="true" title="Q&A rating looks like this"></span></label>
		</div>
		<div class="form-group custom-control custom-switch">
			<input type="hidden" name="bottom_line_enabled_category" value="false">
			<input type="checkbox" class="custom-control-input" value="true" name="bottom_line_enabled_category" <?php echo checked( true, $settings['bottom_line_enabled_category'], false ); ?> id="bottom_line_enabled_category">
			<label class="custom-control-label" for="bottom_line_enabled_category"><?php esc_html_e( 'Show star rating on category pages?', 'yrfw' ); ?></label>
		</div>
		<div class="form-group custom-control custom-switch">
			<input type="hidden" name="disable_native_review_system" value="false">
			<input type="checkbox" class="custom-control-input" value="true" name="disable_native_review_system" <?php echo checked( true, $settings['disable_native_review_system'], false ); ?> id="disable_native_review_system">
			<label class="custom-control-label" for="disable_native_review_system"><?php esc_html_e( 'Disable native reviews system?', 'yrfw' ); ?></label>
		</div>
		<hr>
		<button type="submit" class="btn btn-primary" <?php echo disabled( $settings['authenticated'], false ); ?> name="widgets"><?php esc_html_e( 'Save Settings', 'yrfw' ); ?></button>
	</form>
</div>