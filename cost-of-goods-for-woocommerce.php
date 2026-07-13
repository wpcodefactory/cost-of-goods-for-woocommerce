<?php
/*
Plugin Name: Cost of Goods: Product Cost & Profit Calculator for WooCommerce
Plugin URI: https://wpfactory.com/item/cost-of-goods-for-woocommerce/
Description: Save product purchase costs (cost of goods) in WooCommerce. Beautifully.
Version: 4.1.8
Author: WPFactory
Author URI: https://wpfactory.com
Text Domain: cost-of-goods-for-woocommerce
Domain Path: /languages
WC tested up to: 10.9
Requires Plugins: woocommerce
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'wpfcogs_is_plugin_active' ) ) {
	/**
	 * wpfcogs_is_plugin_active.
	 *
	 * @version 2.4.5
	 * @since   2.4.5
	 */
	function wpfcogs_is_plugin_active( $plugin ) {
		return ( function_exists( 'is_plugin_active' ) ? is_plugin_active( $plugin ) :
			(
				in_array( $plugin, apply_filters( 'active_plugins', ( array ) get_option( 'active_plugins', array() ) ) ) ||
				( is_multisite() && array_key_exists( $plugin, ( array ) get_site_option( 'active_sitewide_plugins', array() ) ) )
			)
		);
	}
}

// Check for active plugins
if (
	! wpfcogs_is_plugin_active( 'woocommerce/woocommerce.php' ) ||
	( 'cost-of-goods-for-woocommerce.php' === basename( __FILE__ ) && wpfcogs_is_plugin_active( 'cost-of-goods-for-woocommerce-pro/cost-of-goods-for-woocommerce-pro.php' ) )
) {
	if ( function_exists( 'wpfcogs' ) ) {
		$cog = wpfcogs();
		if ( method_exists( $cog, 'set_free_version_filesystem_path' ) ) {
			$cog->set_free_version_filesystem_path( __FILE__ );
		}
	}
	return;
}

require_once( 'includes/class-wpfcogs.php' );

if ( ! function_exists( 'wpfcogs' ) ) {
	/**
	 * Returns the main instance of WPFCOGS to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  WPFCOGS
	 */
	function wpfcogs() {
		return WPFCOGS::instance();
	}
}

// Initializes the plugin.
add_action( 'plugins_loaded', function () {
	$cog = wpfcogs();
	$cog->set_filesystem_path( __FILE__ );
	$cog->init();
} );

// Custom deactivation/activation hooks.
$activation_option = 'alg_wc_cog_on_activation';
$activation_hook = 'wpfcogs_on_activation';
$deactivation_option = 'alg_wc_cog_on_deactivation';
$deactivation_hook = 'wpfcogs_on_deactivation';
register_activation_hook( __FILE__, function () use ( $activation_option ) {
	add_option( $activation_option, 'yes' );
} );
register_deactivation_hook( __FILE__, function () use ( $deactivation_hook ) {
	do_action( $deactivation_hook );
} );
add_action( 'admin_init', function () use ( $activation_option, $activation_hook ) {
	if ( is_admin() && get_option( $activation_option ) === 'yes' ) {
		delete_option( $activation_option );
		do_action( $activation_hook );
	}
} );