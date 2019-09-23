<?php

/**
 * @package YotpoReviews
 */

class YRFW_Activate {
	/**
	 * Perform activcation of plugin.
	 *
	 * @return void
	 */
	public function activate() {
		global $yotpo_settings;
		$yotpo_settings->get_settings();
	}
}
