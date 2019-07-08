<div class="container-fluid py-3">
	<?php
	global $yrfw_logger;
	?>
	<textarea class="hxii-log" rows=50 readonly><?php echo $yrfw_logger->read_log( 50 ); ?></textarea>
	<form action="" method="post" accept-charset="utf-8" id="yotpo_debug_log">
		<?php wp_nonce_field( 'debug', 'debug_log' ); ?>
		<button type="submit" class="btn btn-warning" name="reset_debug_log">Reset Log</button>
	</form>
</div>