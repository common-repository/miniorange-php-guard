<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

delete_option( 'mo_php_guard_output_zip_link' );
delete_option( 'mo_php_guard_message' );
delete_option( 'mo_host_name' );

?>