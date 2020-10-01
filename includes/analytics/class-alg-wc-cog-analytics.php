<?php
/**
 * Cost of Goods for WooCommerce - Analytics Class
 *
 * @version 2.2.0
 * @since   1.7.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Analytics' ) ) :

class Alg_WC_Cost_of_Goods_Analytics {

	/**
	 * Constructor.
	 *
	 * @version 2.2.0
	 * @since   1.7.0
	 * @see     https://github.com/woocommerce/woocommerce-admin/tree/master/docs/examples/extensions
	 * @see     https://woocommerce.wordpress.com/2020/02/20/extending-wc-admin-reports/
	 * @todo    [next] caching, i.e. `woocommerce_analytics_orders_query_args` and `woocommerce_analytics_orders_stats_query_args`
	 * @todo    [later] columns: exporting (non server)
	 * @todo    [later] columns: sorting
	 * @todo    [later] remove `get_option( 'alg_wc_cog_analytics_orders', 'no' )`?
	 */
	function __construct() {
		if ( 'yes' === get_option( 'alg_wc_cog_analytics_orders', 'no' ) ) {
			add_action( 'admin_enqueue_scripts',                                      array( $this, 'register_script' ) );

			// Join
			add_filter( 'woocommerce_analytics_clauses_join_orders_subquery',         array( $this, 'add_join_subquery' ) );
			add_filter( 'woocommerce_analytics_clauses_join_orders_stats_total',      array( $this, 'add_join_subquery' ) );
			add_filter( 'woocommerce_analytics_clauses_join_orders_stats_interval',   array( $this, 'add_join_subquery' ) );

			// Where
			add_filter( 'woocommerce_analytics_clauses_where_orders_subquery',        array( $this, 'add_where_subquery' ) );
			add_filter( 'woocommerce_analytics_clauses_where_orders_stats_total',     array( $this, 'add_where_subquery' ) );
			add_filter( 'woocommerce_analytics_clauses_where_orders_stats_interval',  array( $this, 'add_where_subquery' ) );

			// Select
			add_filter( 'woocommerce_analytics_clauses_select_orders_subquery',       array( $this, 'add_select_subquery' ) );
			add_filter( 'woocommerce_analytics_clauses_select_orders_stats_total',    array( $this, 'add_select_subquery' ) );
			add_filter( 'woocommerce_analytics_clauses_select_orders_stats_interval', array( $this, 'add_select_subquery' ) );

			// Export
			add_filter( 'woocommerce_export_admin_orders_report_row_data',            array( $this, 'add_to_export_rows' ),    PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_admin_orders_report_export_column_names',        array( $this, 'add_to_export_columns' ), PHP_INT_MAX, 2 );

			// Schema
			add_filter( 'woocommerce_rest_report_orders_schema',                      array( $this, 'add_order_extended_attributes_schema' ) );
		}
	}

	/**
	 * add_order_extended_attributes_schema.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 * @todo    [next] not sure if this really does anything useful?
	 */
	function add_order_extended_attributes_schema( $properties ) {
		$properties['extended_info']['order_cost'] = array(
			'type'        => 'float',
			'readonly'    => true,
			'context'     => array( 'view', 'edit' ),
			'description' => __( 'Order cost.', 'cost-of-goods-for-woocommerce' ),
		);
		$properties['extended_info']['order_profit'] = array(
			'type'        => 'float',
			'readonly'    => true,
			'context'     => array( 'view', 'edit' ),
			'description' => __( 'Order profit.', 'cost-of-goods-for-woocommerce' ),
		);
		return $properties;
	}

	/**
	 * add_to_export_columns.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 * @see     \woocommerce\includes\export\abstract-wc-csv-exporter.php
	 */
	function add_to_export_columns( $columns, $exporter ) {
		$columns['order_cost']   = __( 'Cost', 'cost-of-goods-for-woocommerce' );
		$columns['order_profit'] = __( 'Profit', 'cost-of-goods-for-woocommerce' );
		return $columns;
	}

	/**
	 * add_to_export_rows.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 * @see     \woocommerce\packages\woocommerce-admin\src\ReportCSVExporter.php
	 */
	function add_to_export_rows( $row, $item ) {
		$row['order_cost']   = $item['order_cost'];
		$row['order_profit'] = $item['order_profit'] ;
		return $row;
	}

	/**
	 * add_join_subquery.
	 *
	 * @version 2.2.0
	 * @since   1.7.0
	 */
	function add_join_subquery( $clauses ) {
		global $wpdb;
		$clauses[] = "LEFT JOIN {$wpdb->postmeta} order_profit_postmeta ON {$wpdb->prefix}wc_order_stats.order_id = order_profit_postmeta.post_id AND order_profit_postmeta.meta_key = '_alg_wc_cog_order_profit'";
		$clauses[] = "LEFT JOIN {$wpdb->postmeta} order_cost_postmeta ON {$wpdb->prefix}wc_order_stats.order_id = order_cost_postmeta.post_id AND order_cost_postmeta.meta_key = '_alg_wc_cog_order_cost'";
		$clauses[] = "JOIN {$wpdb->postmeta} currency_postmeta ON {$wpdb->prefix}wc_order_stats.order_id = currency_postmeta.post_id";
		return $clauses;
	}

	/**
	 * add_where_subquery.
	 *
	 * @version 2.2.0
	 * @since   1.7.0
	 */
	function add_where_subquery( $clauses ) {
		$clauses[] = "AND currency_postmeta.meta_key = '_order_currency'";
		return $clauses;
	}

	/**
	 * add_select_subquery.
	 *
	 * @version 2.2.0
	 * @since   1.7.0
	 */
	function add_select_subquery( $clauses ) {
		$clauses[] = ', IFNULL(order_profit_postmeta.meta_value, 0) AS order_profit';
		$clauses[] = ', IFNULL(order_cost_postmeta.meta_value, 0) AS order_cost';
		$clauses[] = ', currency_postmeta.meta_value AS currency';
		return $clauses;
	}

	/**
	 * register_script.
	 *
	 * @version 1.7.1
	 * @since   1.7.0
	 */
	function register_script() {
		if ( ! class_exists( 'Automattic\WooCommerce\Admin\Loader' ) || ! function_exists( 'wc_admin_is_registered_page' ) || ! \Automattic\WooCommerce\Admin\Loader::is_admin_page() ) {
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
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Analytics();
