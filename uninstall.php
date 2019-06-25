<?php

defined( 'WP_UNINSTALL_PLUGIN' ) || die();

delete_option( 'yotpo_settings' );
delete_option( 'yotpo_secret' );
delete_transient( 'yotpo_total_orders' );
delete_transient( 'yotpo_last_sent_order' );
delete_post_meta_by_key( 'yotpo_order_sent' );
