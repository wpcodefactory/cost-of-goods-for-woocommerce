<?php
/*
Plugin Name: Cost of Goods: Product Cost & Profit Calculator for WooCommerce
Plugin URI: https://wpfactory.com/item/cost-of-goods-for-woocommerce/
Description: Save product purchase costs (cost of goods) in WooCommerce. Beautifully.
Version: 3.8.1
Author: WPFactory
Author URI: https://wpfactory.com
Text Domain: cost-of-goods-for-woocommerce
Domain Path: /langs
Copyright: © 2025 WPFactory
WC tested up to: 10.0
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'alg_wc_cog_is_plugin_active' ) ) {
	/**
	 * alg_wc_cog_is_plugin_active.
	 *
	 * @version 2.4.5
	 * @since   2.4.5
	 */
	function alg_wc_cog_is_plugin_active( $plugin ) {
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
	! alg_wc_cog_is_plugin_active( 'woocommerce/woocommerce.php' ) ||
	( 'cost-of-goods-for-woocommerce.php' === basename( __FILE__ ) && alg_wc_cog_is_plugin_active( 'cost-of-goods-for-woocommerce-pro/cost-of-goods-for-woocommerce-pro.php' ) )
) {
	if ( function_exists( 'alg_wc_cog' ) ) {
		$cog = alg_wc_cog();
		if ( method_exists( $cog, 'set_free_version_filesystem_path' ) ) {
			$cog->set_free_version_filesystem_path( __FILE__ );
		}
	}
	return;
}

require_once( 'includes/class-alg-wc-cog.php' );

if ( ! function_exists( 'alg_wc_cog' ) ) {
	/**
	 * Returns the main instance of Alg_WC_Cost_of_Goods to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  Alg_WC_Cost_of_Goods
	 */
	function alg_wc_cog() {
		return Alg_WC_Cost_of_Goods::instance();
	}
}

// Initializes the plugin.
add_action( 'plugins_loaded', function () {
	$cog = alg_wc_cog();
	$cog->set_filesystem_path( __FILE__ );
	$cog->init();
} );

// Custom deactivation/activation hooks.
$activation_hook   = 'alg_wc_cog_on_activation';
$deactivation_hook = 'alg_wc_cog_on_deactivation';
register_activation_hook( __FILE__, function () use ( $activation_hook ) {
	add_option( $activation_hook, 'yes' );
} );
register_deactivation_hook( __FILE__, function () use ( $deactivation_hook ) {
	do_action( $deactivation_hook );
} );
add_action( 'admin_init', function () use ( $activation_hook ) {
	if ( is_admin() && get_option( $activation_hook ) === 'yes' ) {
		delete_option( $activation_hook );
		do_action( $activation_hook );
	}
} );