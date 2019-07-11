<?php
// $settingss = (YRFW_Settings_File::get_instance())->migrate_settings();

global $yotpo_settings, $settings_instance, $yotpo_orders;
$settings = (YRFW_Settings_File::get_instance())->get_settings();

if ( ! current_user_can( 'administrator' ) ) { wp_die(); }

if ( isset( $_POST['reset_settings'] ) && wp_verify_nonce( $_POST['yotpo_debug_settings'], 'settings' ) ) {
	if ( $yotpo_settings->set_settings( $yotpo_settings->get_default_settings(), false, false ) ) {
		new YRFW_Messages( 'Reset successful!', 'success', true, true );
	}
} elseif ( isset( $_POST['update_settings'] ) ) {
	$settings_instance = YRFW_Settings_File::get_instance();
	$settings_instance->set_settings( $_POST, true, true );
} elseif ( isset( $_POST['reset_debug_log'] ) && wp_verify_nonce( $_POST['debug_log'], 'debug' ) ) {
	global $yrfw_logger;
	$yrfw_logger->reset_log();
} elseif ( isset( $_POST['reset_widget'] ) && wp_verify_nonce( $_POST['yotpo_debug_settings'], 'settings' ) ) {
	delete_transient( 'yotpo_widget_version' );
}

?>

<div id="wrap">
	<div class="bootstrap-wrapper">
		<div class="container-fluid">
			<h1>Yotpo Debug</h1>
			<div id="tabs">
				<?php do_action( 'throw_message' ); ?>
				<ul class="nav nav-tabs">
					<li class="nav-item" id="yotpo-debug-log">
						<a class="nav-link active" href="#log" data-toggle="tab">Log</a>
					</li>
					<li class="nav-item" id="yotpo-debug-settings">
						<a class="nav-link" href="#settings" data-toggle="tab">Settings</a>
					</li>
					<li class="nav-item" id="yotpo-debug-info">
						<a class="nav-link" href="#info" data-toggle="tab">Info</a>
					</li>
				</ul>

				<div class="tab-content shadow-lg border border-top-0" style="background: #fff;">
					<div class="tab-pane active" id="log">
						<?php require_once 'debug/template-debug-log.php'; ?>
					</div>
					<div class="tab-pane" id="settings">
						<?php require_once 'debug/template-debug-settings.php'; ?>
						</div>
					<div class="tab-pane" id="info">
						<?php require_once 'debug/template-debug-info.php'; ?>
					</div> <!-- tab-pane widgets -->
				</div> <!-- tab-content -->
			</div> <!-- tabs -->
		</div>
	</div>
</div>