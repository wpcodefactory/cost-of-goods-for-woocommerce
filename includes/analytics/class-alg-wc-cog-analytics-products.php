<?php
/**
 * Cost of Goods for WooCommerce - Analytics - Products.
 *
 * @version 3.9.1
 * @since   2.5.1
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Analytics_Products' ) ) :

	class Alg_WC_Cost_of_Goods_Analytics_Products {

		/**
		 * Constructor.
		 *
		 * @version 3.0.9
		 * @since   2.5.1
		 *
		 */
		function __construct() {
			// Script localization info.
			add_filter( 'alg_wc_cog_analytics_localization_info', array( $this, 'add_analytics_localization_info' ) );

			// Costs.
			add_filter( 'woocommerce_analytics_clauses_join_products_subquery', array( $this, 'add_costs_to_join_products' ) );
			add_filter( 'woocommerce_analytics_clauses_join_products_stats_total', array( $this, 'add_costs_to_join_products' ) );
			add_filter( 'woocommerce_analytics_clauses_join_products_stats_interval', array( $this, 'add_costs_to_join_products' ) );
			add_filter( 'woocommerce_analytics_clauses_select_products', array( $this, 'add_costs_to_select_products' ) );
			add_filter( 'woocommerce_analytics_clauses_select_products_subquery', array( $this, 'add_costs_to_select_products_subquery' ) );
			add_filter( 'woocommerce_export_admin_products_report_row_data', array( $this, 'add_costs_row_data_to_export' ), PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_admin_products_report_export_column_names', array( $this, 'add_costs_columns_names_to_export' ), PHP_INT_MAX, 2 );

			// Costs total.
			add_filter( 'woocommerce_analytics_clauses_select_products_stats_total', array( $this, 'add_costs_total_to_select_products_stats_total' ) );
			add_filter( 'woocommerce_analytics_clauses_select_products_stats_interval', array( $this, 'add_costs_total_to_select_products_stats_total' ) );

			// Profit.
			add_filter( 'woocommerce_analytics_clauses_select_products', array( $this, 'add_profit_to_select_products' ) );
			add_filter( 'woocommerce_analytics_clauses_select_products_subquery', array( $this, 'add_profit_to_select_products_subquery' ) );
			add_filter( 'woocommerce_export_admin_products_report_row_data', array( $this, 'add_profit_row_data_to_export' ), PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_admin_products_report_export_column_names', array( $this, 'add_profit_columns_names_to_export' ), PHP_INT_MAX, 2 );

			// Profit total.
			add_filter( 'woocommerce_analytics_clauses_select_products_stats_total', array( $this, 'add_profit_total_to_select_products_stats_total' ) );
			add_filter( 'woocommerce_analytics_clauses_select_products_stats_interval', array( $this, 'add_profit_total_to_select_products_stats_total' ) );
		}

		/**
		 * add_profit_row_data_to_export.
		 *
		 * @version 3.0.9
		 * @since   3.0.9
		 *
		 * @param $row
		 * @param $item
		 *
		 * @return mixed
		 */
		function add_costs_row_data_to_export( $row, $item ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_profit_select', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_products_tab', 'no' ) ) ) {
				$row['cost'] = $item['cost'];
			}
			return $row;
		}

		/**
		 * add_profit_columns_names_to_export.
		 *
		 * @version 3.0.9
		 * @since   3.0.9
		 *
		 * @param $columns
		 * @param $exporter
		 *
		 * @return mixed
		 */
		function add_costs_columns_names_to_export( $columns, $exporter ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_profit_select', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_products_tab', 'no' ) ) ) {
				$columns['cost'] = __( 'Cost', 'cost-of-goods-for-woocommerce' );
			}
			return $columns;
		}

		/**
		 * add_profit_row_data_to_export.
		 *
		 * @version 3.0.9
		 * @since   3.0.9
		 *
		 * @param $row
		 * @param $item
		 *
		 * @return mixed
		 */
		function add_profit_row_data_to_export( $row, $item ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_profit_select', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_products_tab', 'no' ) ) ) {
				$row['profit'] = $item['profit'];
			}
			return $row;
		}

		/**
		 * add_profit_columns_names_to_export.
		 *
		 * @version 3.0.9
		 * @since   3.0.9
		 *
		 * @param $columns
		 * @param $exporter
		 *
		 * @return mixed
		 */
		function add_profit_columns_names_to_export( $columns, $exporter ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_profit_select', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_products_tab', 'no' ) ) ) {
				$columns['profit'] = __( 'Profit', 'cost-of-goods-for-woocommerce' );
			}
			return $columns;
		}

		/**
		 * add_costs_total_to_select_products_stats_total.
		 *
		 * @version 3.6.8
		 * @since   2.5.1
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_costs_total_to_select_products_stats_total( $clauses ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_cost_totals', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_totals_on_products_tab', 'no' ) ) ) {
				$clauses[] = $this->add_costs_total_to_select_products_stats_total_clauses();
			}
			return $clauses;
		}

		/**
		 * add_costs_total_to_select_products_stats_total_clauses.
		 *
		 * @version 3.6.8
		 * @since   3.6.8
		 *
		 * @return string
		 */
		function add_costs_total_to_select_products_stats_total_clauses() {
			return ', SUM(alg_cog_oimc.meta_value * product_qty) AS costs_total';
		}

		/**
		 * add_profit_total_to_select_products_stats_total.
		 *
		 * @version 3.6.8
		 * @since   2.5.1
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_profit_total_to_select_products_stats_total( $clauses ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_profit_totals', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_totals_on_products_tab', 'no' ) ) ) {
				global $wpdb;
				$clauses[] = $this->add_profit_total_to_select_products_stats_total_clauses();
			}
			return $clauses;
		}

		/**
		 * add_profit_total_to_select_products_stats_total_clauses.
		 *
		 * @version 3.8.9
		 * @since   3.6.8
		 *
		 * @return string
		 */
		function add_profit_total_to_select_products_stats_total_clauses() {
			global $wpdb;

			$tax_operation = '';
			if ( 'wc_get_price_including_tax' === alg_wc_cog_get_option( 'alg_wc_cog_products_get_price_method', 'wc_get_price_excluding_tax' ) ) {
				$tax_operation = "+ {$wpdb->prefix}wc_order_product_lookup.tax_amount";
			}

			$multicurrency_operation = $this->get_multicurrency_operation();

			$product_net_revenue = "({$multicurrency_operation} {$wpdb->prefix}wc_order_product_lookup.product_net_revenue {$tax_operation})";

			return ", SUM($product_net_revenue - alg_cog_oimc.meta_value * product_qty) AS profit_total";
		}

		/**
		 * get_multicurrency_operation.
		 *
		 * @version 3.8.9
		 * @since   3.8.9
		 *
		 * @return string
		 */
		function get_multicurrency_operation() {
			$multicurrency_operation = "";
			if ( 'yes' === alg_wc_cog_get_option( 'alg_wc_cog_currencies_enabled', 'no' ) ) {
				$multicurrency_operation = "
				1 / COALESCE(
					    TRIM(BOTH '\"' FROM SUBSTRING_INDEX(
					        REGEXP_SUBSTR(
					            alg_cog_mcurr_rate.option_value,
					            CONCAT(alg_cog_shop_base_curr.option_value, alg_cog_o.currency,'\";s:[0-9]+:\"([^\"]+)')                        
					        ),
					        '\"',
					        -1
					    )),
					    '1'
					)  *
				";
			}

			return $multicurrency_operation;
		}

		/**
		 * add_profit_to_select_products.
		 *
		 * @version 2.5.1
		 * @since   2.5.1
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_profit_to_select_products( $clauses ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_profit_select', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_products_tab', 'no' ) ) ) {
				$clauses[] = ', profit';
			}
			return $clauses;
		}

		/**
		 * add_profit_to_select_products_subquery.
		 *
		 * @version 3.6.8
		 * @since   2.5.1
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_profit_to_select_products_subquery( $clauses ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_profit_subquery', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_products_tab', 'no' ) ) ) {
				global $wpdb;
				$clauses[] = $this->add_profit_to_select_products_subquery_clauses();
			}

			return $clauses;
		}

		/**
		 * add_profit_to_select_products_subquery_clauses.
		 *
		 * @version 3.8.9
		 * @since   3.6.8
		 *
		 * @return string
		 */
		function add_profit_to_select_products_subquery_clauses() {
			global $wpdb;

			$tax_operation = '';
			if ( 'wc_get_price_including_tax' === alg_wc_cog_get_option( 'alg_wc_cog_products_get_price_method', 'wc_get_price_excluding_tax' ) ) {
				$tax_operation = "+ {$wpdb->prefix}wc_order_product_lookup.tax_amount";
			}

			$multicurrency_operation = $this->get_multicurrency_operation();

			$product_net_revenue = "{$multicurrency_operation} {$wpdb->prefix}wc_order_product_lookup.product_net_revenue {$tax_operation}";

			return ", IFNULL((SUM($product_net_revenue) - SUM(alg_cog_oimc.meta_value * product_qty)), 0) AS profit";
		}

		/**
		 * add_costs_to_join_products.
		 *
		 * @version 3.9.1
		 * @since   2.5.1
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_costs_to_join_products( $clauses ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_cost_join', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_products_tab', 'no' ) ) ) {
				global $wpdb;
				$clauses[] = $this->add_costs_to_join_products_clauses();
				$clauses   = $this->maybe_add_multicurrency_to_join( $clauses );
			}
			return $clauses;
		}

		/**
		 * maybe_add_multicurrency_to_join.
		 *
		 * @version 3.9.1
		 * @since   3.8.9
		 *
		 * @param $clauses
		 *
		 * @return mixed
		 */
		function maybe_add_multicurrency_to_join( $clauses ) {
			if ( 'yes' === alg_wc_cog_get_option( 'alg_wc_cog_currencies_enabled', 'no' ) ) {
				global $wpdb;
				$clauses[] = " INNER JOIN {$wpdb->prefix}wc_orders AS alg_cog_o ON alg_cog_o.id = wp_wc_order_product_lookup.order_id";
				$clauses[] = " LEFT JOIN {$wpdb->options} AS alg_cog_mcurr_rate ON alg_cog_mcurr_rate.option_name = 'alg_wc_cog_currencies_rates'";
				$clauses[] = " INNER JOIN {$wpdb->options} AS alg_cog_shop_base_curr ON alg_cog_shop_base_curr.option_name = 'woocommerce_currency'";
			}

			return $clauses;
		}

		/**
		 * add_costs_to_join_products_clauses.
		 *
		 * @version 3.6.8
		 * @since   3.6.8
		 *
		 * @return string
		 */
		function add_costs_to_join_products_clauses() {
			global $wpdb;
			return " LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta alg_cog_oimc ON {$wpdb->prefix}wc_order_product_lookup.order_item_id = alg_cog_oimc.order_item_id AND alg_cog_oimc.meta_key = '_alg_wc_cog_item_cost'";
		}

		/**
		 * add_costs_to_select_products_subquery.
		 *
		 * @version 3.6.8
		 * @since   2.5.1
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_costs_to_select_products_subquery( $clauses ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_cost_select_subquery', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_products_tab', 'no' ) ) ) {
				$clauses[] = $this->add_costs_to_select_products_subquery_clauses();
			}
			return $clauses;
		}

		/**
		 * add_costs_to_select_products_subquery_clauses.
		 *
		 * @version 3.6.8
		 * @since   3.6.8
		 *
		 * @return string
		 */
		function add_costs_to_select_products_subquery_clauses() {
			global $wpdb;
			return ", SUM(IFNULL(alg_cog_oimc.meta_value * product_qty, 0)) AS cost";
		}

		/**
		 * add_costs_to_select_products.
		 *
		 * @version 2.5.5
		 * @since   2.5.1
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_costs_to_select_products( $clauses ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_cost_select', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_products_tab', 'no' ) ) ) {
				$clauses[] = ', cost';
			}
			return $clauses;
		}

		/**
		 * add_analytics_localization_info.
		 *
		 * @version 2.5.5
		 * @since   2.5.1
		 *
		 * @param $info
		 *
		 * @return mixed
		 */
		function add_analytics_localization_info( $info ) {
			$info['product_cost_and_profit_totals_enabled']  = 'yes' === get_option( 'alg_wc_cog_cost_and_profit_totals_on_products_tab', 'no' );
			$info['product_cost_and_profit_columns_enabled'] = 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_products_tab', 'no' );
			return $info;
		}

	}

endif;

return new Alg_WC_Cost_of_Goods_Analytics_Products();
