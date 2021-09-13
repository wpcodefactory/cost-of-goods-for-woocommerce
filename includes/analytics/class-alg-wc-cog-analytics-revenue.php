<?php
/**
 * Cost of Goods for WooCommerce - Analytics - Revenue.
 *
 * @version 2.4.8
 * @since   2.4.8
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Analytics_Revenue' ) ) :

	class Alg_WC_Cost_of_Goods_Analytics_Revenue {

		/**
		 * Constructor.
		 *
		 * @version 2.4.8
		 * @since   2.4.8
		 *
		 */
		function __construct() {
			// Script localization info.
			add_filter( 'alg_wc_cog_analytics_localization_info', array( $this, 'add_analytics_localization_info' ) );
			// Select costs and profit total columns from the orders report.
			add_filter( 'alg_wc_cog_analytics_orders_costs_total_validation', array( $this, 'add_costs_and_profit_total_column_if_option_is_enabled' ) );
			add_filter( 'alg_wc_cog_analytics_orders_profit_total_validation', array( $this, 'add_costs_and_profit_total_column_if_option_is_enabled' ) );
			// Export.
			add_filter( 'woocommerce_export_admin_revenue_report_row_data', array( $this, 'add_costs_and_profit_row_data_to_export' ), PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_admin_revenue_report_export_column_names', array( $this, 'add_costs_and_profit_columns_names_to_export' ), PHP_INT_MAX, 2 );
		}

		/**
		 * add_costs_and_profit_columns_names_to_export.
		 *
		 * @version 2.4.8
		 * @since   2.4.8
		 *
		 * @param $columns
		 * @param $exporter
		 *
		 * @return mixed
		 */
		function add_costs_and_profit_columns_names_to_export( $columns, $exporter ) {
			if ( 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_analytics_revenue', 'no' ) ) {
				$columns['costs_total']  = __( 'Cost', 'cost-of-goods-for-woocommerce' );
				$columns['profit_total'] = __( 'Profit', 'cost-of-goods-for-woocommerce' );
			}
			return $columns;
		}

		/**
		 * add_costs_and_profit_row_data_to_export.
		 *
		 * @version 2.4.8
		 * @since   2.4.8
		 *
		 * @param $row
		 * @param $item
		 *
		 * @return mixed
		 */
		function add_costs_and_profit_row_data_to_export( $row, $item ) {
			if ( 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_analytics_revenue', 'no' ) ) {
				$row['costs_total']  = property_exists( $item['subtotals'], 'costs_total' ) ? $item['subtotals']->costs_total : '';
				$row['profit_total'] = property_exists( $item['subtotals'], 'profit_total' ) ? $item['subtotals']->profit_total : '';
			}
			return $row;
		}

		/**
		 * add_costs_and_profit_total_column_if_option_is_enabled.
		 *
		 * @version 2.4.8
		 * @since   2.4.8
		 *
		 * @param $validation
		 *
		 * @return bool
		 */
		function add_costs_and_profit_total_column_if_option_is_enabled( $validation ) {
			if (
				'yes' === get_option( 'alg_wc_cog_cost_and_profit_totals_on_analytics_revenue', 'no' ) ||
				'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_analytics_revenue', 'no' )
			) {
				$validation = true;
			}
			return $validation;
		}

		/**
		 * add_analytics_localization_info.
		 *
		 * @version 2.4.8
		 * @since   2.4.8
		 *
		 * @param $info
		 *
		 * @return mixed
		 */
		function add_analytics_localization_info( $info ) {
			$info['cost_and_profit_totals_enabled_on_revenue']  = 'yes' === get_option( 'alg_wc_cog_cost_and_profit_totals_on_analytics_revenue', 'no' );
			$info['cost_and_profit_columns_enabled_on_revenue'] = 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_analytics_revenue', 'no' );
			return $info;
		}

	}

endif;

return new Alg_WC_Cost_of_Goods_Analytics_Revenue();
