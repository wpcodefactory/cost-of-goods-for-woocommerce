<?php
/**
 * Cost of Goods for WooCommerce - Analytics Class.
 *
 * @version 4.1.6
 * @since   1.7.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPFCOGS_Analytics' ) ) :

class WPFCOGS_Analytics {

	/**
	 * Orders.
	 *
	 * @since 2.9.4
	 *
	 * @var WPFCOGS_Analytics_Orders
	 */
	public $orders;

	/**
	 * Orders.
	 *
	 * @since 3.6.8
	 *
	 * @var WPFCOGS_Pro_Analytics_Products
	 */
	public $products;

	/**
	 * Constructor.
	 *
	 * @version 4.1.5
	 * @since   1.7.0
	 *
	 */
	function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_script' ) );

		// Free Analytics > Orders.
		$this->orders = require_once('class-wpfcogs-analytics-orders.php');

		// Free WooCommerce > Customers.
		require_once('class-wpfcogs-analytics-customers.php');

		/**
		 * Load pro-only analytics modules.
		 *
		 * @since 4.1.5
		 */
		do_action( 'wpfcogs_analytics_load_modules', $this );
	}

	/**
	 * register_script.
	 *
	 * @version 4.1.6
	 * @since   1.7.0
	 */
	function register_script() {
		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$page = ! empty( $page ) ? $page : ( isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if (
			! function_exists( 'wc_admin_is_registered_page' ) ||
			'wc-admin' !== $page ||
			! apply_filters( 'wpfcogs_create_analytics_orders_validation', true )
		) {
			return;
		}

		wp_register_script(
			'wpfcogs-analytics-report',
			plugins_url( apply_filters( 'wpfcogs_analytics_script_path', '/build/index.js' ), __FILE__ ),
			array(
				'wp-hooks',
				'wp-element',
				'wp-i18n',
				'wc-components',
			),
			wpfcogs()->version,
			true
		);
		wp_enqueue_script( 'wpfcogs-analytics-report' );
		wp_localize_script( 'wpfcogs-analytics-report', 'wpfcogs_analytics_obj',
			apply_filters( 'wpfcogs_analytics_localization_info', array( 'profit_template' => get_option( 'alg_wc_cog_product_profit_html_template', '%profit% (%profit_percent%)' ) ) )
		);
	}

}

endif;

return new WPFCOGS_Analytics();
