<?php

/**
 * @package YotpoReviews
 */
class YRFW_Messages {
	private $_message;
	private $_type;
	private $_dismissible;

	/**
	 * Message boxes
	 *
	 * @param string  $message     The message we wish to output.
	 * @param string  $type        Message type e.g. warning, info, success etc.
	 * @param boolean $dismissible Is the message dismissible.
	 * @param boolean $notice      TRUE for WordPress notices or FALSE for BootStrap 4 alerts.
	 */
	public function __construct( $message, $type, $dismissible = true, $notice = false ) {
		$this->_message     = $message;
		$this->_type        = $type;
		$this->_dismissible = $dismissible;
		if ( $notice ) {
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		} else {
			add_action( 'throw_message', array( $this, 'throw_message' ) );
		}
	}

	/**
	 * Throw WordPress notice
	 *
	 * @return void
	 */
	public function admin_notice() {
		?>
			<div class="notice notice-<?php echo $this->_type; ?> <?php if ( $this->_dismissible ) { echo 'is-dismissible'; } ?>">
				<p><?php echo $this->_message; ?></p>
			</div>
		<?php
	}

	/**
	 * Throw BootStrap 4 alert
	 *
	 * @return void
	 */
	public function throw_message() {
		?>
			<div class="alert alert-<?php echo $this->_type; ?> <?php if ( $this->_dismissible ) { echo 'alert-dismissible'; } ?> fade show" role="alert">
			<?php echo $this->_message; ?>
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
			</div>
		<?php
	}
}