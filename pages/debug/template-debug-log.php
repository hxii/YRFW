<div class="container-fluid py-3">
	<?php
		global $yrfw_logger;
		// $set = YRFW_Settings::jsonify_settings();
	?>
		<textarea class="hxii-log" rows=40><?= $yrfw_logger->read_log(); ?></textarea>
	<form action="" method="post" accept-charset="utf-8" id="yotpo_debug_log">
		<?php wp_nonce_field( 'debug', 'debug_log' ); ?>
		<button type="submit" class="btn btn-warning" name="reset_debug_log">Reset Log</button>
		<button type="submit" class="btn btn-secondary" name="download_debug_log">Download Log</button>
	</form>
	
</div>