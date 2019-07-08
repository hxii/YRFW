<div class="container py-3">
	<h1>Info</h1>
	<?php
		global $yrfw_logger, $yotpo_scheduler, $yotpo_cache, $debug_info_array;
		$debug_info_array = [
			'Yotpo Plugin Version' => YRFW_PLUGIN_VERSION,
			'WooCommerce Version'  => WOOCOMMERCE_VERSION,
			'WordPress Version'    => get_bloginfo( 'version' ),
			'PHP Version'          => phpversion(),
			'Logger Version'       => Hxii_Logger::get_version(),
			'Logger Level'         => $yrfw_logger->loglevel,
			'Logger File'          => $yrfw_logger->get_filename( false ),
			'Widget Version'       => ( $transient = get_transient( 'yotpo_widget_version' ) ) ? $transient : 'null',
			'Scheduled Submission' => ( $sched     = $yotpo_scheduler->get_scheduler() ) ? $sched : 'null',
			'Products Cache'       => '<a href=" ' . YRFW_PLUGIN_URL . '/products.json">Download</a>(' . ( filesize( YRFW_PLUGIN_PATH . '/products.json' ) / 1024 / 1024 ) . 'MB)',
		];
	?>
	<dl class="row">
	<?php
	foreach ( $debug_info_array as $key => $value ) {
		?>
		<dt class="col-sm-3"><?php echo $key; ?></dt><dd class="col-sm-9"><?php echo $value; ?></dd>
		<?php
	}
	?>
	</dl>
</div>