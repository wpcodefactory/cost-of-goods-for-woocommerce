<?php
/**
 * Cost of Goods for WooCommerce - Analytics Class.
 *
 * @version 3.6.8
 * @since   1.7.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Analytics' ) ) :

class Alg_WC_Cost_of_Goods_Analytics {

	/**
	 * Orders.
	 *
	 * @since 2.9.4
	 *
	 * @var Alg_WC_Cost_of_Goods_Analytics_Orders
	 */
	public $orders;

	/**
	 * Orders.
	 *
	 * @since 3.6.8
	 *
	 * @var Alg_WC_Cost_of_Goods_Analytics_Products
	 */
	public $products;

	/**
	 * Constructor.
	 *
	 * @version 3.6.8
	 * @since   1.7.0
	 *
	 */
	function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_script' ) );

		// Analytics > Orders.
		$this->orders = require_once('class-alg-wc-cog-analytics-orders.php');

		// Analytics > Revenue.
		require_once('class-alg-wc-cog-analytics-revenue.php');

		// Analytics > Stock.
		require_once('class-alg-wc-cog-analytics-stock.php');

		// Analytics > Products.
		$this->products = require_once('class-alg-wc-cog-analytics-products.php');

		// Analytics > Products.
		require_once('class-alg-wc-cog-analytics-categories.php');

		// Analytics > Variations.
		require_once('class-alg-wc-cog-analytics-variations.php');

		// WooCommerce > Customers.
		require_once('class-alg-wc-cog-analytics-customers.php');
	}

	/**
	 * register_script.
	 *
	 * @version 2.5.8
	 * @since   1.7.0
	 */
	function register_script() {
		if (
			! function_exists( 'wc_admin_is_registered_page' ) ||
			! ( isset( $_GET['page'] ) && 'wc-admin' === $_GET['page'] ) ||
			! apply_filters( 'alg_wc_cog_create_analytics_orders_validation', true )
		) {
			return;
		}
		wp_register_script(
			'alg-wc-cost-of-goods-analytics-report',
			plugins_url( '/build/index.js', __FILE__ ),
			array(
				'wp-hooks',
				'wp-element',
				'wp-i18n',
				'wc-components',
			),
			alg_wc_cog()->version,
			true
		);
		wp_enqueue_script( 'alg-wc-cost-of-goods-analytics-report' );
		wp_localize_script( 'alg-wc-cost-of-goods-analytics-report', 'alg_wc_cog_analytics_obj',
			apply_filters( 'alg_wc_cog_analytics_localization_info', array( 'profit_template' => get_option( 'alg_wc_cog_product_profit_html_template', '%profit% (%profit_percent%)' ) ) )
		);
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Analytics();
