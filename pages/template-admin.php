<?php
global $yotpo_settings, $yotpo_cache, $yotpo_orders;
$yotpo_orders->get_token_from_cache();
$settings = (YRFW_Settings_File::get_instance())->get_settings();
$sched = new YRFW_Scheduler();
$sched->clear_scheduler();
// Move to Widgets tab if authenticated.
if ( $settings['authenticated'] ) {
	echo( '<script>jQuery(document).ready(function(){jQuery("#yotpo-widgets > a").click();})</script>' ); }
// Login Form Actions.
if ( isset( $_POST['action'] ) && wp_verify_nonce( $_POST['yotpo_login_form'], 'login' ) ) {
	if ( isset( $_POST['authenticate'] ) ) {
		if ( $yotpo_settings->authenticate( $_POST['appkey'], $_POST['secret'] ) ) {
			new YRFW_Messages( esc_html__( 'Authentication successful!', 'yrfw' ), 'success', true, false );
			$settings = $yotpo_settings->get_settings();
		} else {
			new YRFW_Messages( esc_html__( 'Authenticaion failed!', 'yrfw' ), 'danger', true, false );
		}
	} elseif ( isset( $_POST['reset'] ) ) {
		$yotpo_settings->reset_authentication();
		new YRFW_Messages( esc_html__( 'Authentication has been reset!', 'yrfw' ), 'info', true, false );
		$settings = $yotpo_settings->get_settings();
	}
// Widget Form Actions.
} elseif ( isset( $_POST['widgets'] ) && wp_verify_nonce( $_POST['yotpo_widgets_form'], 'widgets' ) ) {
	$yotpo_settings->set_settings( $_POST, true, true );
	$settings = $yotpo_settings->get_settings();
// Settings Form Actions
} elseif ( isset( $_POST['yotpo_settings_form'] ) && wp_verify_nonce( $_POST['yotpo_settings_form'], 'settings' ) ) {
	if ( isset( $_POST['submit_past_orders'] ) && $settings['show_submit_past_orders'] ) {
		$past_orders = new YRFW_Past_Orders();
		$order_array = $past_orders->get_past_orders( $settings['timeframe_from'] );
		$past_orders->submit_past_orders( $order_array );
		$yotpo_settings->set_settings( [ 'show_submit_past_orders' => false ], true, true );
	} elseif ( isset( $_POST['update_settings'] ) ) {
		$yotpo_settings->set_settings( $_POST, true, true );
		// $settings = $yotpo_settings->get_settings();
	}
	$settings = $yotpo_settings->get_settings();
// Help Form.
} elseif ( isset( $_POST['export_reviews'] ) && wp_verify_nonce( $_POST['yotpo_export_form'], 'export' ) ) {
	$exporter    = new YRFW_Review_Exporter();
	$export_file = $exporter->exporter_export_reviews();
	if ( $export_file ) {
		new YRFW_Messages( esc_html__( 'Reviews successfully exported to', 'yrfw' ) . ' <a class="alert-link" href="' . YRFW_PLUGIN_URL . '/' . $export_file . '">' . $export_file . '</a>', 'success' );
	}
}
?>
<div id="bootstrap-wrapper" class="bootstrap-wrapper">
	<div class="container">
		<h1 class="title"><div class="yotpo-logo" height="32px"></div><?php esc_html_e( 'Yotpo Reviews for WooCommerce', 'yrfw' ); ?></h1>
		<div id="tabs">
			<?php do_action( 'throw_message' ); ?>
			<ul class="nav nav-tabs nav-fill">
				<li class="nav-item" id="yotpo-login">
					<a class="nav-link border-bottom-0 active" href="#login" data-toggle="tab"><?php esc_html_e( 'Login', 'yrfw' ); ?></a>
				</li>
				<li class="nav-item" id="yotpo-widgets">
					<a class="nav-link border-bottom-0 <?php echo ( ! $settings['authenticated'] ) ? 'disabled' : ''; ?>" href="#widgets" data-toggle="tab" <?php echo ( ! $settings['authenticated'] ? 'tabindex="-1" aria-disabled="true"' : '' ); ?> ><?php esc_html_e( 'Widgets', 'yrfw' ); ?></a>
				</li>
				<li class="nav-item" id="yotpo-settings">
					<a class="nav-link border-bottom-0 <?php echo ( ! $settings['authenticated'] ) ? 'disabled' : ''; ?>" href="#settings" data-toggle="tab" <?php echo ( ! $settings['authenticated'] ? 'tabindex="-1" aria-disabled="true"' : '' ); ?> ><?php esc_html_e( 'Other Settings', 'yrfw' ); ?></a>
				</li>
				<li class="nav-item" id="yotpo-help">
					<a class="nav-link border-bottom-0 <?php echo ( ! $settings['authenticated'] ) ? 'disabled' : ''; ?>" href="#help" data-toggle="tab" <?php echo ( ! $settings['authenticated'] ? 'tabindex="-1" aria-disabled="true"' : '' ); ?> ><?php esc_html_e( 'Help', 'yrfw' ); ?></a>
				</li>
			</ul>

			<div class="tab-content shadow-lg border border-top-0" style="background: #fff;">
				<div class="tab-pane active" id="login">
					<?php require_once 'admin/template-admin-login.php'; ?>
				</div>
				<div class="tab-pane" id="widgets">
					<?php require_once 'admin/template-admin-widgets.php'; ?>
				</div> <!-- tab-pane widgets -->
				<div class="tab-pane" id="settings">
					<?php require_once 'admin/template-admin-settings.php'; ?>
				</div> <!-- tab-pane settings -->
				<div class="tab-pane" id="help">
					<?php require_once 'admin/template-admin-help.php'; ?>
				</div> <!-- tab-pane help -->
			</div> <!-- tab-content -->
		</div> <!-- tabs -->
		<div id="yrfw-version" class="version badge badge-dark float-right mt-2">Yotpo <?php echo YRFW_PLUGIN_VERSION; ?></div>
		<?php echo ( '02-14' === date( 'm-d' ) ) ? '<div class="float-left" title="It\'s Paul\'s birthday">&#127867;</div>' : ''; ?>
		<pre><?php var_dump($_POST); ?></pre>
	</div>
</div> <!-- wrap -->