<?php
/**
 * Cost of Goods for WooCommerce - Analytics - Orders.
 *
 * @version 3.4.6
 * @since   2.4.5
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Automattic\WooCommerce\Utilities\OrderUtil;

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Analytics_Orders' ) ) :

	class Alg_WC_Cost_of_Goods_Analytics_Orders {

		/**
		 * Constructor.
		 *
		 * @version 3.0.0
		 * @since   3.0.0
		 *
		 * @see     https://github.com/woocommerce/woocommerce-admin/tree/master/docs/examples/extensions
		 * @see     https://woocommerce.wordpress.com/2020/02/20/extending-wc-admin-reports/
		 * @see     https://github.com/woocommerce/woocommerce-admin/issues/5092
		 *
		 * @todo    [next] caching, i.e. `woocommerce_analytics_orders_query_args` and `woocommerce_analytics_orders_stats_query_args`
		 * @todo    [later] columns: exporting (non server)
		 * @todo    [later] columns: sorting
		 * @todo    [later] remove `get_option( 'alg_wc_cog_analytics_orders', 'no' )`?
		 */
		function __construct() {
			// Script localization info.
			add_filter( 'alg_wc_cog_analytics_localization_info', array( $this, 'add_analytics_localization_info' ) );

			// Costs
			add_filter( 'woocommerce_analytics_clauses_join_orders_subquery', array( $this, 'add_costs_join_orders' ) );
			add_filter( 'woocommerce_analytics_clauses_join_orders_stats_total', array( $this, 'add_costs_join_orders' ) );
			add_filter( 'woocommerce_analytics_clauses_join_orders_stats_interval', array( $this, 'add_costs_join_orders' ) );
			add_filter( 'woocommerce_analytics_clauses_select_orders_subquery', array( $this, 'add_costs_select_orders_subquery' ) );
			add_filter( 'woocommerce_analytics_clauses_select_orders_stats_total', array( $this, 'add_costs_select_orders_stats_total' ) );
			add_filter( 'woocommerce_analytics_clauses_select_orders_stats_interval', array( $this, 'add_costs_select_orders_stats_total' ) );
			add_filter( 'woocommerce_rest_reports_column_types', array( $this, 'add_costs_total_reports_column_types' ), 10 );
			add_filter( 'woocommerce_export_admin_orders_report_row_data', array( $this, 'add_costs_row_data_to_export' ), PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_admin_orders_report_export_column_names', array( $this, 'add_costs_columns_names_to_export' ), PHP_INT_MAX, 2 );
			add_filter( 'alg_wc_cog_analytics_orders_costs_total_validation', array( $this, 'add_costs_total_column_if_option_is_enabled' ) );

			// Profit
			add_filter( 'woocommerce_analytics_clauses_join_orders_subquery', array( $this, 'add_profit_join_orders' ) );
			add_filter( 'woocommerce_analytics_clauses_join_orders_stats_total', array( $this, 'add_profit_join_orders' ) );
			add_filter( 'woocommerce_analytics_clauses_join_orders_stats_interval', array( $this, 'add_profit_join_orders' ) );
			add_filter( 'woocommerce_analytics_clauses_select_orders_subquery', array( $this, 'add_profit_select_orders_subquery' ) );
			add_filter( 'woocommerce_analytics_clauses_select_orders_stats_total', array( $this, 'add_profit_select_orders_stats_total' ) );
			add_filter( 'woocommerce_analytics_clauses_select_orders_stats_interval', array( $this, 'add_profit_select_orders_stats_total' ) );
			add_filter( 'woocommerce_rest_reports_column_types', array( $this, 'add_profit_total_reports_column_types' ), 10 );
			add_filter( 'woocommerce_export_admin_orders_report_row_data', array( $this, 'add_profit_row_data_to_export' ), PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_admin_orders_report_export_column_names', array( $this, 'add_profit_columns_names_to_export' ), PHP_INT_MAX, 2 );
			add_filter( 'alg_wc_cog_analytics_orders_profit_total_validation', array( $this, 'add_profit_total_column_if_option_is_enabled' ) );

			// Individual Costs export
			add_filter( 'woocommerce_export_admin_orders_report_row_data', array( $this, 'add_individual_costs_row_data_to_export' ), PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_admin_orders_report_export_column_names', array( $this, 'add_individual_costs_columns_names_to_export' ), PHP_INT_MAX, 2 );

			// Test, Debug
			// woocommerce_analytics_orders_stats_select_query
			// woocommerce_analytics_orders_stats_query_args
			// woocommerce_analytics_orders_query_args
			// woocommerce_analytics_orders_select_query
			// add_filter( 'woocommerce_analytics_orders_stats_select_query', array( $this, 'debug' ) );

			//add_filter( 'posts_clauses', array( $this, 'posts_clauses' ), 12, 2 );
		}

		/*function posts_clauses( $clauses, $query ) {

			//error_log( print_r( '======', true ) );
			//error_log( print_r( $clauses, true ) );
			//error_log( print_r( $query, true ) );
			return $clauses;
		}*/

		/*function debug( $param ) {
			error_log(print_r($param,true));
			return $param;
		}*/

		/**
		 * add_individual_costs_columns_names_to_export.
		 *
		 * @version 3.0.0
		 * @since   3.0.0
		 *
		 * @param $columns
		 * @param $exporter
		 *
		 * @return mixed
		 */
		function add_individual_costs_columns_names_to_export( $columns, $exporter ) {
			if ( 'yes' === get_option( 'alg_wc_cog_analytics_orders_individual_costs', 'no' ) ) {
				$columns['items_cost']            = __( 'Items cost', 'cost-of-goods-for-woocommerce' );
				$columns['shipping_cost']         = __( 'Shipping cost', 'cost-of-goods-for-woocommerce' );
				$columns['gateway_cost']          = __( 'Gateway cost', 'cost-of-goods-for-woocommerce' );
				$columns['extra_cost']            = __( 'Extra cost', 'cost-of-goods-for-woocommerce' );
				$columns['shipping_classes_cost'] = __( 'Shipping classes cost', 'cost-of-goods-for-woocommerce' );
			}

			return $columns;
		}

		/**
		 * add_individual_costs_row_data_to_export.
		 *
		 * @version 3.0.0
		 * @since   3.0.0
		 *
		 * @param $row
		 * @param $item
		 *
		 * @return mixed
		 */
		function add_individual_costs_row_data_to_export( $row, $item ) {
			if ( 'yes' === get_option( 'alg_wc_cog_analytics_orders_individual_costs', 'no' ) ) {
				$row['items_cost']            = $item['items_cost'];
				$row['shipping_cost']         = $item['shipping_cost'];
				$row['gateway_cost']          = $item['gateway_cost'];
				$row['extra_cost']            = $item['extra_cost'];
				$row['shipping_classes_cost'] = $item['shipping_classes_cost'];
			}

			return $row;
		}

		/**
		 * add_analytics_localization_info.
		 *
		 * @version 2.9.8
		 * @since   2.4.5
		 *
		 * @param $info
		 *
		 * @return mixed
		 */
		function add_analytics_localization_info( $info ) {
			$info['cost_and_profit_totals_enabled_on_orders']  = 'yes' === get_option( 'alg_wc_cog_analytics_orders_cost_profit_totals', 'no' );
			$info['cost_and_profit_columns_enabled_on_orders'] = 'yes' === get_option( 'alg_wc_cog_analytics_orders', 'no' );
			$info['individual_order_costs_enabled']            = 'yes' === get_option( 'alg_wc_cog_analytics_orders_individual_costs', 'no' );

			return $info;
		}

		/**
		 * add_costs_total_reports_column_types.
		 *
		 * @version 2.4.1
		 * @since   2.4.1
		 *
		 * @param $types
		 *
		 * @return mixed
		 */
		function add_costs_total_reports_column_types( $types ) {
			$types['costs_total'] = 'floatval';
			return $types;
		}

		/**
		 * add_costs_select_orders_stats_total.
		 *
		 * @version 3.4.6
		 * @since   2.4.1
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_costs_select_orders_stats_total( $clauses ) {
			if ( apply_filters( 'alg_wc_cog_analytics_orders_costs_total_validation', false ) ) {
				$clauses[] = $this->get_order_costs_total_meta_select_clauses();
			}
			// If we need to convert the currency
			//$clauses[] = ', SUM(order_cost_postmeta.meta_value * COALESCE(NULLIF(REGEXP_REPLACE(REGEXP_SUBSTR(wpo.option_value, CONCAT(\'"\',\'USD\',currency_postmeta.meta_value,\'"\',\';(s|d):.+?:".*?(?=";)\')), CONCAT(\'"\',\'USD\',currency_postmeta.meta_value,\'"\',\';(s|d):.+?:"\'),\'\'),\'\'),1)) as costs_total';

			return $clauses;
		}

		/**
		 * get_order_costs_total_select_clauses.
		 *
		 * @version 3.4.6
		 * @since   3.4.6
		 *
		 * @return string
		 */
		function get_order_costs_total_meta_select_clauses() {
			return ', SUM(order_cost_postmeta.meta_value) AS costs_total';
		}

		/**
		 * get_order_cost_meta_join_clauses.
		 *
		 * @version 3.4.6
		 * @since   3.4.6
		 *
		 * @return string
		 */
		function get_order_cost_meta_join_clauses() {
			global $wpdb;
			if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
				$clauses = "LEFT JOIN {$wpdb->prefix}wc_orders_meta order_cost_postmeta ON {$wpdb->prefix}wc_order_stats.order_id = order_cost_postmeta.order_id AND order_cost_postmeta.meta_key = '_alg_wc_cog_order_cost'";
			} else {
				$clauses = "LEFT JOIN {$wpdb->postmeta} order_cost_postmeta ON {$wpdb->prefix}wc_order_stats.order_id = order_cost_postmeta.post_id AND order_cost_postmeta.meta_key = '_alg_wc_cog_order_cost'";
			}

			return $clauses;
		}

		/**
		 * add_costs_total_column_if_option_is_enabled.
		 *
		 * @version 2.4.8
		 * @since   2.4.8
		 *
		 * @param $validation
		 *
		 * @return bool
		 */
		function add_costs_total_column_if_option_is_enabled( $validation ) {
			if ( 'yes' === get_option( 'alg_wc_cog_analytics_orders_cost_profit_totals', 'no' ) ) {
				$validation = true;
			}
			return $validation;
		}

		/**
		 * add_profit_total_column_if_option_is_enabled.
		 *
		 * @version 2.4.8
		 * @since   2.4.8
		 *
		 * @param $validation
		 *
		 * @return bool
		 */
		function add_profit_total_column_if_option_is_enabled($validation){
			if ( 'yes' === get_option( 'alg_wc_cog_analytics_orders_cost_profit_totals', 'no' ) ) {
				$validation = true;
			}
			return $validation;
		}

		/**
		 * add_costs_select_orders_subquery.
		 *
		 * @version 2.9.8
		 * @since   2.4.1
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_costs_select_orders_subquery( $clauses ) {
			if ( 'yes' === get_option( 'alg_wc_cog_analytics_orders', 'no' ) ) {
				$clauses[] = ', IFNULL(order_cost_postmeta.meta_value, 0) AS order_cost';
			}

			if ( 'yes' === get_option( 'alg_wc_cog_analytics_orders_individual_costs', 'no' ) ) {
				$clauses[] = ', IFNULL(items_cost_pm.meta_value, 0) AS items_cost';
				$clauses[] = ', IFNULL(shipping_cost_pm.meta_value, 0) AS shipping_cost';
				$clauses[] = ', IFNULL(gateway_cost_pm.meta_value, 0) AS gateway_cost';
				$clauses[] = ', IFNULL(extra_cost_per_orders_pm.meta_value, 0) + IFNULL(extra_cost_all_orders_pm.meta_value, 0) + IFNULL(extra_cost_from_meta_pm.meta_value, 0) AS extra_cost';
				$clauses[] = ', IFNULL(shipping_classes_cost_pm.meta_value, 0) AS shipping_classes_cost';
			}
			return $clauses;
		}

		/**
		 * add_costs_join_orders.
		 *
		 * @version 3.4.6
		 * @since   2.4.1
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_costs_join_orders( $clauses ) {
			global $wpdb;
			$clauses[] = $this->get_order_cost_meta_join_clauses();

			if ( 'yes' === get_option( 'alg_wc_cog_analytics_orders_individual_costs', 'no' ) ) {
				if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
					$clauses[] = "LEFT JOIN {$wpdb->prefix}wc_orders_meta items_cost_pm ON {$wpdb->prefix}wc_order_stats.order_id = items_cost_pm.order_id AND items_cost_pm.meta_key = '_alg_wc_cog_order_items_cost'";
					$clauses[] = "LEFT JOIN {$wpdb->prefix}wc_orders_meta shipping_cost_pm ON {$wpdb->prefix}wc_order_stats.order_id = shipping_cost_pm.order_id AND shipping_cost_pm.meta_key = '_alg_wc_cog_order_shipping_cost'";
					$clauses[] = "LEFT JOIN {$wpdb->prefix}wc_orders_meta gateway_cost_pm ON {$wpdb->prefix}wc_order_stats.order_id = gateway_cost_pm.order_id AND gateway_cost_pm.meta_key = '_alg_wc_cog_order_gateway_cost'";
					$clauses[] = "LEFT JOIN {$wpdb->prefix}wc_orders_meta extra_cost_per_orders_pm ON {$wpdb->prefix}wc_order_stats.order_id = extra_cost_per_orders_pm.order_id AND extra_cost_per_orders_pm.meta_key = '_alg_wc_cog_order_extra_cost_per_order'";
					$clauses[] = "LEFT JOIN {$wpdb->prefix}wc_orders_meta extra_cost_all_orders_pm ON {$wpdb->prefix}wc_order_stats.order_id = extra_cost_all_orders_pm.order_id AND extra_cost_all_orders_pm.meta_key = '_alg_wc_cog_order_extra_cost'";
					$clauses[] = "LEFT JOIN {$wpdb->prefix}wc_orders_meta extra_cost_from_meta_pm ON {$wpdb->prefix}wc_order_stats.order_id = extra_cost_from_meta_pm.order_id AND extra_cost_from_meta_pm.meta_key = '_alg_wc_cog_order_extra_cost_from_meta'";
					$clauses[] = "LEFT JOIN {$wpdb->prefix}wc_orders_meta shipping_classes_cost_pm ON {$wpdb->prefix}wc_order_stats.order_id = shipping_classes_cost_pm.order_id AND shipping_classes_cost_pm.meta_key = '_alg_wc_cog_order_shipping_classes_cost'";
				} else {
					$clauses[] = "LEFT JOIN {$wpdb->postmeta} items_cost_pm ON {$wpdb->prefix}wc_order_stats.order_id = items_cost_pm.post_id AND items_cost_pm.meta_key = '_alg_wc_cog_order_items_cost'";
					$clauses[] = "LEFT JOIN {$wpdb->postmeta} shipping_cost_pm ON {$wpdb->prefix}wc_order_stats.order_id = shipping_cost_pm.post_id AND shipping_cost_pm.meta_key = '_alg_wc_cog_order_shipping_cost'";
					$clauses[] = "LEFT JOIN {$wpdb->postmeta} gateway_cost_pm ON {$wpdb->prefix}wc_order_stats.order_id = gateway_cost_pm.post_id AND gateway_cost_pm.meta_key = '_alg_wc_cog_order_gateway_cost'";
					$clauses[] = "LEFT JOIN {$wpdb->postmeta} extra_cost_per_orders_pm ON {$wpdb->prefix}wc_order_stats.order_id = extra_cost_per_orders_pm.post_id AND extra_cost_per_orders_pm.meta_key = '_alg_wc_cog_order_extra_cost_per_order'";
					$clauses[] = "LEFT JOIN {$wpdb->postmeta} extra_cost_all_orders_pm ON {$wpdb->prefix}wc_order_stats.order_id = extra_cost_all_orders_pm.post_id AND extra_cost_all_orders_pm.meta_key = '_alg_wc_cog_order_extra_cost'";
					$clauses[] = "LEFT JOIN {$wpdb->postmeta} extra_cost_from_meta_pm ON {$wpdb->prefix}wc_order_stats.order_id = extra_cost_from_meta_pm.post_id AND extra_cost_from_meta_pm.meta_key = '_alg_wc_cog_order_extra_cost_from_meta'";
					$clauses[] = "LEFT JOIN {$wpdb->postmeta} shipping_classes_cost_pm ON {$wpdb->prefix}wc_order_stats.order_id = shipping_classes_cost_pm.post_id AND shipping_classes_cost_pm.meta_key = '_alg_wc_cog_order_shipping_classes_cost'";
				}
			}
			// If we need to get something fron the options database
			//$clauses[] = "JOIN {$wpdb->options} wpo ON option_name LIKE '%alg_wc_cog_currencies_rates%'";
			return $clauses;
		}

		/**
		 * add_profit_total_reports_column_types.
		 *
		 * @version 2.4.1
		 * @since   2.4.1
		 *
		 * @param $types
		 *
		 * @return mixed
		 */
		function add_profit_total_reports_column_types( $types ) {
			$types['profit_total'] = 'floatval';
			return $types;
		}

		/**
		 * add_profit_select_orders_stats_total.
		 *
		 * @version 3.4.6
		 * @since   2.4.1
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_profit_select_orders_stats_total( $clauses ) {
			if ( apply_filters( 'alg_wc_cog_analytics_orders_profit_total_validation', false ) ) {
				$clauses[] = $this->get_order_profit_total_meta_select_clauses();
			}

			/*if ( 'yes' !== get_option( 'alg_wc_cog_analytics_orders_cost_profit_totals', 'no' ) ) {
				return $clauses;
			}
			$clauses[] = ', SUM(order_profit_postmeta.meta_value) AS profit_total';*/

			// If we need to convert the currency
			//$clauses[] = ', SUM(order_profit_postmeta.meta_value * COALESCE(NULLIF(REGEXP_REPLACE(REGEXP_SUBSTR(wpo.option_value, CONCAT(\'"\',\'USD\',currency_postmeta.meta_value,\'"\',\';(s|d):.+?:".*?(?=";)\')), CONCAT(\'"\',\'USD\',currency_postmeta.meta_value,\'"\',\';(s|d):.+?:"\'),\'\'),\'\'),1)) as profit_total';
			return $clauses;
		}

		/**
		 * add_profit_select_orders_subquery.
		 *
		 * @version 2.4.1
		 * @since   2.4.1
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_profit_select_orders_subquery( $clauses ) {
			if ( 'yes' === get_option( 'alg_wc_cog_analytics_orders', 'no' ) ) {
				$clauses[] = ', IFNULL(order_profit_postmeta.meta_value, 0) AS order_profit';
			}
			return $clauses;
		}

		/**
		 * get_order_costs_total_select_clauses.
		 *
		 * @version 3.4.6
		 * @since   3.4.6
		 *
		 * @return string
		 */
		function get_order_profit_total_meta_select_clauses() {
			return ', SUM(order_profit_postmeta.meta_value) AS profit_total';
		}

		/**
		 * get_order_cost_meta_join_clauses.
		 *
		 * @version 3.4.6
		 * @since   3.4.6
		 *
		 * @return string
		 */
		function get_order_profit_meta_join_clauses() {
			global $wpdb;
			if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
				$clauses = "LEFT JOIN {$wpdb->prefix}wc_orders_meta order_profit_postmeta ON {$wpdb->prefix}wc_order_stats.order_id = order_profit_postmeta.order_id AND order_profit_postmeta.meta_key = '_alg_wc_cog_order_profit'";
			} else {
				$clauses = "LEFT JOIN {$wpdb->postmeta} order_profit_postmeta ON {$wpdb->prefix}wc_order_stats.order_id = order_profit_postmeta.post_id AND order_profit_postmeta.meta_key = '_alg_wc_cog_order_profit'";
			}

			return $clauses;
		}

		/**
		 * add_profit_join_orders.
		 *
		 * @version 3.4.6
		 * @since   2.4.1
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_profit_join_orders( $clauses ) {
			$clauses[] = $this->get_order_profit_meta_join_clauses();

			return $clauses;
		}

		/**
		 * add_costs_columns_names_to_export.
		 *
		 * @version 2.4.1
		 * @since   2.4.1
		 *
		 * @param $columns
		 * @param $exporter
		 *
		 * @return mixed
		 */
		function add_costs_columns_names_to_export( $columns, $exporter ) {
			if ( 'yes' === get_option( 'alg_wc_cog_analytics_orders', 'no' ) ) {
				$columns['order_cost'] = __( 'Cost', 'cost-of-goods-for-woocommerce' );
			}
			return $columns;
		}

		/**
		 * add_costs_row_data_to_export.
		 *
		 * @version 2.4.1
		 * @since   2.4.1
		 *
		 * @param $row
		 * @param $item
		 *
		 * @return mixed
		 */
		function add_costs_row_data_to_export( $row, $item ) {
			if ( 'yes' === get_option( 'alg_wc_cog_analytics_orders', 'no' ) ) {
				$row['order_cost'] = $item['order_cost'];
			}
			return $row;
		}

		/**
		 * add_profit_columns_names_to_export.
		 *
		 * @version 2.4.1
		 * @since   2.4.1
		 *
		 * @param $columns
		 * @param $exporter
		 *
		 * @return mixed
		 */
		function add_profit_columns_names_to_export( $columns, $exporter ) {
			if ( 'yes' === get_option( 'alg_wc_cog_analytics_orders', 'no' ) ) {
				$columns['order_profit'] = __( 'Profit', 'cost-of-goods-for-woocommerce' );
			}
			return $columns;
		}

		/**
		 * add_profit_row_data_to_export.
		 *
		 * @version 2.4.1
		 * @since   2.4.1
		 *
		 * @param $row
		 * @param $item
		 *
		 * @return mixed
		 */
		function add_profit_row_data_to_export( $row, $item ) {
			if ( 'yes' === get_option( 'alg_wc_cog_analytics_orders', 'no' ) ) {
				$row['order_profit'] = $item['order_profit'];
			}
			return $row;
		}

	}

endif;

return new Alg_WC_Cost_of_Goods_Analytics_Orders();
