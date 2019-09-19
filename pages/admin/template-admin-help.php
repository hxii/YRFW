<div class="container py-3">
	<div class="jumbotron">
		<h3 class="display-3"><?php esc_html_e( 'Need some help?', 'yrfw' ); ?></h2>
		<h2 class="text-muted"><?php esc_html_e( 'No problem! &#128526; Here are some useful resources.', 'yrfw' ); ?></h2>
		<hr class="my-4">
		<ul class="list-group list-group-horizontal">
			<a class="list-group-item list-group-item-action" href="https://support.yotpo.com/" target="_blank"><?php esc_html_e( 'Support Portal', 'yrfw' ); ?></a>
			<a class="list-group-item list-group-item-action" href="https://yap.yotpo.com/#/?modal=contact_support" target="_blank"><?php esc_html_e( 'Contact Support', 'yrfw' ); ?></a>
			<a class="list-group-item list-group-item-action" href="https://apidocs.yotpo.com/reference" target="_blank"><?php esc_html_e( 'API Documentation', 'yrfw' ); ?></a>
			<a class="list-group-item list-group-item-action" href="https://www.yotpo.com/blog/yotpo-gdpr-guide/" target="_blank"><?php esc_html_e( 'GDPR at Yotpo', 'yrfw' ); ?></a>
			<a class="list-group-item list-group-item-action" href="https://www.yotpo.com/release-notes/" target="_blank"><?php esc_html_e( 'Yotpo Release Notes', 'yrfw' ); ?></a>
			<a class="list-group-item list-group-item-action list-group-item-warning" href="http://status.yotpo.com/" target="_blank"><?php esc_html_e( 'Yotpo Service Status', 'yrfw' ); ?></a>
		</ul>
	</div>
	<p>
		<?php
		/* translators: date the order was sent */
			printf( __( 'The last order was sent on <strong>%s</strong>', 'yrfw' ), get_transient( 'yotpo_last_sent_order' ) );
		?>
	</p>
	<p>
		<?php
		/* translators: total number of orders */
			printf( __( 'In total, <strong>%s</strong> orders (excluding past orders) were sent to Yotpo', 'yrfw' ), get_transient( 'yotpo_total_orders' ) );
		?>
	</p>
	<hr>
	<form action="" method="post" accept-charset="utf-8">
		<?php wp_nonce_field( 'export', 'yotpo_export_form' ); ?>
		<label for="export_reviews"><?php esc_html_e( 'Export Reviews', 'yrfw' ); ?></label><span class="dashicons dashicons-editor-help" data-toggle="tooltip" title="Export native WooCommerce reviews in an import-ready format for Yotpo."></span>
		<div class="form-group">
			<button type="submit" name="export_reviews" id="export_reviews" class="btn btn-info" value="true"><?php esc_html_e( 'Export Reviews', 'yrfw' ); ?></button>
			<small class="form-text text-muted"><?php esc_html_e( 'This will export all existing native WooCommerce reviews in an import-ready format for Yotpo.', 'yrfw' ); ?>.</small>
		</div>
	</form>
	<h3>Loaded Extensions</h3>
	<table class="table table-sm">
		<thead class="thead-light"><tr><th>Extension Name</th>
		<th>Extension Author</th>
		<th>Extension Description</th></tr>
		</thead>
		<tbody>
		<?php
		$extension_handler = YRFW_Extensions::get_instance();
		foreach ( $extension_handler->list_extensions() as $extension_name => $extension_data ) {
			?>
			<tr><td><a href="<?php echo $extension_data['extension_url']; ?>" targert="_blank"><?php echo $extension_name . ' ' . $extension_data['extension_version']; ?></a></td>
			<td><?php echo $extension_data['extension_author']; ?></td>
			<td><?php echo $extension_data['extension_description']; ?></td>
			</li></tr>
			<?php
		}
		?>
		</tbody>
	</table>
</div>
