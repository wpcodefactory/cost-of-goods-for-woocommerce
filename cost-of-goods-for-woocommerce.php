<?php
/*
Plugin Name: Cost of Goods for WooCommerce
Plugin URI: https://wpfactory.com/item/cost-of-goods-for-woocommerce/
Description: Save product purchase costs (cost of goods) in WooCommerce. Beautifully.
Version: 3.0.2
Author: WPFactory
Author URI: https://wpfactory.com
Text Domain: cost-of-goods-for-woocommerce
Domain Path: /langs
Copyright: © 2023 WPFactory
WC tested up to: 7.9
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
	return;
}

// Composer autoload
if ( ! class_exists( 'Alg_WC_Cost_of_Goods' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
}

if ( ! class_exists( 'Alg_WC_Cost_of_Goods' ) ) :

/**
 * Main Alg_WC_Cost_of_Goods Class
 *
 * @class   Alg_WC_Cost_of_Goods
 * @version 2.1.0
 * @since   1.0.0
 */
final class Alg_WC_Cost_of_Goods {

	/**
	 * Pro.
	 *
	 * @since 2.9.4
	 *
	 * @var Alg_WC_Cost_of_Goods_Pro
	 */
	public $pro;

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	public $version = '3.0.2';

	/**
	 * @var   Alg_WC_Cost_of_Goods The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Core.
	 *
	 * @since 2.9.4
	 *
	 * @var Alg_WC_Cost_of_Goods_Core
	 */
	public $core;

	/**
	 * Main Alg_WC_Cost_of_Goods Instance
	 *
	 * Ensures only one instance of Alg_WC_Cost_of_Goods is loaded or can be loaded.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @static
	 * @return  Alg_WC_Cost_of_Goods - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Initializes.
	 *
	 * @version 3.0.2
	 * @since   2.8.1
	 */
	function init(){
		// Localization
		add_action( 'init', array( $this, 'localize' ) );

		// Declare compatibility with custom order tables for WooCommerce
		add_action( 'before_woocommerce_init', array( $this, 'declare_compatibility_with_hpos' ) );

		// Pro
		if ( 'cost-of-goods-for-woocommerce-pro.php' === basename( __FILE__ ) ) {
			$this->pro = require_once( 'includes/pro/class-alg-wc-cog-pro.php' );
		}

		// Include required files
		$this->includes();

		// Admin
		if ( is_admin() ) {
			$this->admin();
		}

		// Generate documentation
		add_filter( 'wpfpdh_documentation_params_' . plugin_basename( $this->get_filesystem_path() ), array( $this, 'handle_documentation_params' ), 10 );
	}

	/**
	 * Handle documentation params managed by the WP Factory
	 *
	 * @version 2.4.3
	 * @since   2.4.3
	 *
	 * @param $params
	 *
	 * @return mixed
	 */
	function handle_documentation_params( $params ) {
		$params['wc_tab_id'] = 'alg_wc_cost_of_goods';
		$params['pro_settings_filter'] = 'alg_wc_cog_settings';
		return $params;
	}

	/**
	 * localize.
	 *
	 * @version 2.3.3
	 * @since   2.3.3
	 *
	 */
	function localize() {
		// Set up localisation
		load_plugin_textdomain( 'cost-of-goods-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );
	}

	/**
	 * declare_compatibility_with_hpos.
	 *
	 * @version 3.0.2
	 * @since   3.0.2
	 *
	 * @see     https://github.com/woocommerce/woocommerce/wiki/High-Performance-Order-Storage-Upgrade-Recipe-Book#declaring-extension-incompatibility
	 */
	function declare_compatibility_with_hpos() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @version 2.1.0
	 * @since   1.0.0
	 */
	function includes() {
		// Functions
		require_once( 'includes/alg-wc-cog-functions.php' );
		// Core
		$this->core = require_once( 'includes/class-alg-wc-cog-core.php' );
	}

	/**
	 * admin.
	 *
	 * @version 1.4.1
	 * @since   1.1.0
	 */
	function admin() {
		// Action links
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		// Settings
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
		// Version update
		if ( get_option( 'alg_wc_cog_version', '' ) !== $this->version ) {
			add_action( 'admin_init', array( $this, 'version_updated' ) );
		}
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @version 2.6.3
	 * @since   1.0.0
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links = array();
		$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_cost_of_goods' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';
		if ( 'cost-of-goods-for-woocommerce.php' === basename( __FILE__ ) ) {
			$custom_links[] = '<a target="_blank" style="font-weight: bold; color: green;" href="https://wpfactory.com/item/cost-of-goods-for-woocommerce/">' .
				__( 'Go Pro', 'cost-of-goods-for-woocommerce' ) . '</a>';
		}
		$custom_links[] = '<a href="' . admin_url( 'tools.php?page=bulk-edit-costs' ) . '">' . __( 'Bulk edit costs', 'woocommerce' ) . '</a>';
		$custom_links[] = '<a href="' . admin_url( 'tools.php?page=bulk-edit-prices' ) . '">' . __( 'Bulk edit prices', 'woocommerce' ) . '</a>';
		return array_merge( $custom_links, $links );
	}

	/**
	 * Add Cost of Goods settings tab to WooCommerce settings.
	 *
	 * @version 2.3.4
	 * @since   1.0.0
	 */
	function add_woocommerce_settings_tab( $settings ) {
		if ( ! apply_filters( 'alg_wc_cog_create_wc_settings_tab_validation', true ) ) {
			return $settings;
		}
		$settings[] = require_once( 'includes/settings/class-alg-wc-settings-cog.php' );
		return $settings;
	}

	/**
	 * version_updated.
	 *
	 * @version 1.1.0
	 * @since   1.1.0
	 */
	function version_updated() {
		update_option( 'alg_wc_cog_version', $this->version );
	}

	/**
	 * Get the plugin url.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	function plugin_url() {
		return untrailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * get_filesystem_path.
	 *
	 * @version 2.4.3
	 * @since   2.4.3
	 *
	 * @return string
	 */
	function get_filesystem_path(){
		return __FILE__;
	}

}

endif;

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