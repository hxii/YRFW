<div class="container py-3">
		<form action="" method="post" accept-charset="utf-8">
		<?php wp_nonce_field( 'settings', 'yotpo_debug_settings' ) ?>
		<button type="submit" class="btn btn-primary" name="reset_settings">Reset Settings</button>
		<button type="submit" class="btn btn-danger" name="update_settings">Update Settings</button>
		<button type="submit" class="btn btn-secondary" name="reset_widget">Reset Widget Version</button>
	<hr>
	<div class="d-flex flex-wrap">
	<?php
	// $settings = $yotpo_settings->get_settings();
	$settings = ( YRFW_Settings_File::get_instance() )->get_settings();
	foreach ( $settings as $key => $value ) {
		if ( $key === 'secret' ) { continue; }
		echo "<div class='input-group mb-1 col-6'>
				<div class='input-group-prepend'>
				<span class='input-group-text' id='addon-wrapping'>$key</span>
				</div>
				<input type='text' class='form-control' placeholder='$key' aria-label='$key' aria-describedby='addon-wrapping' name='$key' value='$value'>
				</div>";
	}
	?>
</div>
		</form>
</div>