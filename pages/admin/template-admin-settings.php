<div class="container py-3">
	<h3><?php esc_html_e( 'Settings', 'yrfw' ); ?></h3>
	<form method="post" id="yotpo_settings">
		<?php wp_nonce_field( 'settings', 'yotpo_settings_form' ) ?>
		<div class="form-group">
			<label for="yotpo_order_status"><?php esc_html_e( 'Order Status', 'yrfw' ); ?></label><span class="dashicons dashicons-editor-help" data-toggle="tooltip" title="<?php esc_html_e( 'The status which an order must be in order to submit it to Yotpo. Affects both order submission and past order submission.', 'yrfw' ); ?>"></span>
			<select class="form-control" id="yotpo_order_status" name="yotpo_order_status">
				<?php
					$statuses = wc_get_order_statuses();
					foreach ( $statuses as $k => $v ) {
						echo '<option value="' . $k . '"' . selected( $k, $settings['yotpo_order_status'], false ) . '>' . $v . '</option>';
					}
				?>
			</select>
		</div>
		<label for="submission_method"><?php esc_html_e( 'Submission Method', 'yrfw' ); ?></label><span class="dashicons dashicons-editor-help" data-toggle="tooltip" title="<?php esc_html_e( '&quot;Hook&quot; submits the order once it\'s fulfilled. &quot;Schedule&quot; submits all orders for the past day, twice per day.', 'yrfw' ); ?>"></span>
		<div class="form-group">
			<div class="btn-group btn-group-toggle" data-toggle="buttons" id="submission_method">
				<label class="btn btn-primary <?php echo ( 'hook' === $settings['order_submission_method'] ) ? 'active' : ''; ?>">
					<input type="radio" name="order_submission_method" id="hook" autocomplete="off" <?php checked( $settings['order_submission_method'], 'hook', true ); ?> value="hook"><?php esc_html_e( 'Hook', 'yrfw' ); ?>
				</label>
				<label class="btn btn-primary <?php echo ( 'schedule' === $settings['order_submission_method'] ) ? 'active' : ''; ?>">
					<input type="radio" name="order_submission_method" id="schedule" autocomplete="off" <?php checked( $settings['order_submission_method'], 'schedule', true ); ?> value="schedule"><?php esc_html_e( 'Schedule', 'yrfw' ); ?>
				</label>
			</div>
			<?php global $yotpo_scheduler;
				if ( $settings['order_submission_method'] === 'schedule' ): ?>
				<small class="form-text text-muted"><?php esc_html_e( 'The next order submission is scheduled to', 'yrfw' ); ?> <?php echo $yotpo_scheduler->get_scheduler(); ?></small>
			<?php endif; ?>
		</div>
		<div class="form-group">
			<button type="submit" name="submit_past_orders" id="submit_past_orders" class="btn btn-info" <?php disabled( $settings['show_submit_past_orders'], false, true ); ?>><?php esc_html_e( 'Submit past orders', 'yrfw' ); ?></button>
			<small class="form-text text-muted"><?php esc_html_e( 'Submit all past fulfilled orders for the previous 90 days to Yotpo.', 'yrfw' ); ?></small>
		</div>
		<hr>
		<div class="form-group custom-control custom-switch">
			<input type="hidden" name="debug_mode" value=false>
			<input type="checkbox" class="custom-control-input" name="debug_mode" id="debug-enabled" <?php echo checked( $settings['debug_mode'], true, false ); ?> value=true>
			<label class="custom-control-label text-danger" for="debug-enabled"><?php esc_html_e( 'Enable debugging mode?', 'yrfw' ); ?></label>
			<small class="form-text text-muted"><?php esc_html_e( 'Enable logging and debugging for the Yotpo plugin.', 'yrfw' ); ?></small>
		</div>
		<div class="input-group" id="debug_level">
			<select class="custom-select" id="debug_level_choice" name="debug_level" style="-webkit-appearance: none;">
				<option value="info" <?php echo selected( $settings['debug_level'], 'info', false ); ?>>info</option>
				<option value="warning" <?php echo selected( $settings['debug_level'], 'warning', false ); ?>>warning</option>
				<option value="debug" <?php echo selected( $settings['debug_level'], 'debug', false ); ?>>debug</option>
			</select>
			<div class="input-group-append">
				<!-- <button class="btn btn-outline-secondary" type="button"><?php //esc_html_e( 'Download Log', 'yrfw' ); ?></button> -->
				<a href="<?php global $yrfw_logger; echo YRFW_PLUGIN_URL . '/' . $yrfw_logger->get_filename( true ); ?>" class="btn btn-outline-secondary"><?php esc_html_e( 'Download Log', 'yrfw' ); ?></a>
				<a href="<?php echo admin_url( 'admin.php?page=yotpo-debug' ); ?>" class="btn btn-outline-primary"><?php esc_html_e( 'Open debug', 'yrfw' ); ?></a>
			</div>
		</div>
		<hr>
		<button type="submit" name="update_settings" class="btn btn-primary"><?php esc_html_e( 'Save Settings', 'yrfw' ); ?></button>
	</form>
</div>