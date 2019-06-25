<div class="container py-3">
	<h1>Info</h1>
	<div>Yotpo Plugin Version <span class="badge badge-pill badge-primary"><?= YRFW_PLUGIN_VERSION; ?></span></div>
	<div>WooCommerce Version <span class="badge badge-pill badge-primary"><?= WOOCOMMERCE_VERSION; ?></span></div>
	<div>Wordpress Version <span class="badge badge-pill badge-primary"><?= get_bloginfo( 'version' ); ?></span></div>
	<div>PHP Version <span class="badge badge-pill badge-primary"><?= phpversion(); ?></span></div>
	<div>Logger Version
		<?php
			global $yrfw_logger;
			if ( class_exists( 'Hxii_Logger' ) ) {
				echo '<span class="badge badge-pill badge-primary">'. Hxii_Logger::get_version() .'</span>';
				echo '<span class="badge badge-pill badge-secondary">'. ( $yrfw_logger->loglevel ) .'</span>';
			}
		?>
	</div>
	<div>Scheduled submission <span class="badge badge-pill badge-primary"><?php global $yotpo_scheduler; echo $yotpo_scheduler->get_scheduler(); ?></span></div>
</div>