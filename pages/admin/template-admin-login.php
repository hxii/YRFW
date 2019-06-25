<div class="container py-5" style="max-width: 500px; margin: auto;">
	<h3><?php esc_html_e( 'Login Form', 'yrfw' ); ?></h3>
	<form method="post" accept-charset="utf-8" id="yotpo_login_form" action="">
		<?php wp_nonce_field( 'login', 'yotpo_login_form' ); ?>
		<input type="hidden" name="action" value="yotpo_login">
		<div class="form-group">
			<label for="appkey" class="sr-only"><?php esc_html_e( 'Appkey', 'yrfw' ); ?></label>
			<input type="text" class="form-control rounded-0 border-bottom-0" id="appkey" name="appkey" placeholder="Your Appkey" value="<?php echo $settings['app_key']; ?>" <?php echo disabled( $settings['authenticated'], true ); ?> required>
			<label for="secret" class="sr-only"><?php esc_html_e( 'Secret', 'yrfw' ); ?></label>
			<input type="text" class="form-control rounded-0 border-top-0" id="secret" name="secret" placeholder="Your Secret" value="<?php echo str_repeat( '*', 6 ); ?>" <?php echo disabled( $settings['authenticated'], true ); ?> required>
		</div>
		<?php if ( ! $settings['authenticated'] ) : ?>
			<button type="submit" class="btn btn-primary" <?php echo disabled( $settings['authenticated'], true ); ?> name="authenticate"><?php esc_html_e( 'Authenticate', 'yrfw' ); ?></button>
			<a href="https://yap.yotpo.com/get-started/#/signup/register" class="btn btn-secondary" <?php echo disabled( $settings['authenticated'], true ); ?> target="_blank"><?php esc_html_e( 'Register', 'yrfw' ); ?></a>
		<?php else : ?>
			<div class="alert alert-success"><?php esc_html_e( 'You have been authenticated successfully, would you like to reset?', 'yrfw' ); ?></div>
			<button type="submit" class="btn btn-danger btn-block" name="reset"><?php esc_html_e( 'Reset', 'yrfw' ); ?></button>
		<?php endif; ?>
	</form>
</div>