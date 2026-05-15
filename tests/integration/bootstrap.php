<?php
/**
 * PHPUnit bootstrap.
 *
 * Runs inside the wp-env tests-cli container. The WordPress test suite is
 * mounted at /wordpress-phpunit, WooCommerce is installed from .wp-env.json,
 * and this plugin is mounted at wp-content/plugins/hey-woo.
 *
 * @package WooCommerce\HeyWoo\Tests
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/wordpress-phpunit';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo 'Could not find ' . esc_html( $_tests_dir ) . '/includes/functions.php — is the wp-env tests environment running?' . PHP_EOL;
	exit( 1 );
}

require_once $_tests_dir . '/includes/functions.php';

/**
 * Load WooCommerce and Hey Woo before WP_UnitTestCase boots.
 *
 * @return void
 */
function hey_woo_tests_load_plugins() {
	$plugin_dir = defined( 'WP_PLUGIN_DIR' ) ? WP_PLUGIN_DIR : ABSPATH . 'wp-content/plugins';

	$wc_candidates = glob( $plugin_dir . '/woocommerce*/woocommerce.php' );
	if ( empty( $wc_candidates ) ) {
		echo 'Could not find WooCommerce under ' . esc_html( $plugin_dir ) . ' — is WooCommerce installed in the tests environment?' . PHP_EOL;
		exit( 1 );
	}

	require_once $wc_candidates[0];
	require_once $plugin_dir . '/hey-woo/hey-woo.php';

	update_option( 'woocommerce_db_version', WC()->version );
}
tests_add_filter( 'muplugins_loaded', 'hey_woo_tests_load_plugins' );

/**
 * Install WooCommerce with HPOS enabled for integration tests.
 *
 * @return void
 */
function hey_woo_tests_install_woocommerce() {
	update_option( 'woocommerce_custom_orders_table_enabled', 'yes' );
	update_option( 'woocommerce_custom_orders_table_data_sync_enabled', 'no' );
	update_option( 'woocommerce_show_feature_enable_notice_custom_order_tables', 'no' );

	delete_option( 'woocommerce_db_version' );

	if ( class_exists( 'WC_Install' ) ) {
		WC_Install::install();
	}

	$GLOBALS['wp_roles'] = null; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	wp_roles();
}
tests_add_filter( 'init', 'hey_woo_tests_install_woocommerce', 0 );

require $_tests_dir . '/includes/bootstrap.php';
