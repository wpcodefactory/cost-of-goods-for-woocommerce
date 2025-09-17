<?php
/**
 * Cost of Goods for WooCommerce - Analytics - Variations.
 *
 * @version 3.9.1
 * @since   3.6.8
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Analytics_Variations' ) ) :

	class Alg_WC_Cost_of_Goods_Analytics_Variations {

		/**
		 * Constructor.
		 *
		 * @version 3.6.8
		 * @since   3.6.8
		 *
		 */
		function __construct() {
			// Script localization info.
			add_filter( 'alg_wc_cog_analytics_localization_info', array( $this, 'add_analytics_localization_info' ) );

			// Costs.
			add_filter( 'woocommerce_analytics_clauses_join_variations_subquery', array( $this, 'add_costs_to_join_variations' ) );
			add_filter( 'woocommerce_analytics_clauses_join_variations_stats_total', array( $this, 'add_costs_to_join_variations' ) );
			add_filter( 'woocommerce_analytics_clauses_join_variations_stats_interval', array( $this, 'add_costs_to_join_variations' ) );
			add_filter( 'woocommerce_analytics_clauses_select_variations', array( $this, 'add_costs_to_select_variations' ) );
			add_filter( 'woocommerce_analytics_clauses_select_variations_subquery', array( $this, 'add_costs_to_select_variations_subquery' ) );
			add_filter( 'woocommerce_export_admin_variations_report_row_data', array( $this, 'add_costs_row_data_to_export' ), PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_admin_variations_report_export_column_names', array( $this, 'add_costs_columns_names_to_export' ), PHP_INT_MAX, 2 );

			// Costs total.
			add_filter( 'woocommerce_analytics_clauses_select_variations_stats_total', array( $this, 'add_costs_total_to_select_variations_stats_total' ) );
			add_filter( 'woocommerce_analytics_clauses_select_variations_stats_interval', array( $this, 'add_costs_total_to_select_variations_stats_total' ) );

			// Profit.
			add_filter( 'woocommerce_analytics_clauses_select_variations', array( $this, 'add_profit_to_select_variations' ) );
			add_filter( 'woocommerce_analytics_clauses_select_variations_subquery', array( $this, 'add_profit_to_select_variations_subquery' ) );
			add_filter( 'woocommerce_export_admin_variations_report_row_data', array( $this, 'add_profit_row_data_to_export' ), PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_admin_variations_report_export_column_names', array( $this, 'add_profit_columns_names_to_export' ), PHP_INT_MAX, 2 );

			// Profit total.
			add_filter( 'woocommerce_analytics_clauses_select_variations_stats_total', array( $this, 'add_profit_total_to_select_variations_stats_total' ) );
			add_filter( 'woocommerce_analytics_clauses_select_variations_stats_interval', array( $this, 'add_profit_total_to_select_variations_stats_total' ) );
		}

		/**
		 * add_profit_row_data_to_export.
		 *
		 * @version 3.6.8
		 * @since   3.6.8
		 *
		 * @param $row
		 * @param $item
		 *
		 * @return mixed
		 */
		function add_costs_row_data_to_export( $row, $item ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_profit_select', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_variations_tab', 'no' ) ) ) {
				$row['cost'] = $item['cost'];
			}
			return $row;
		}

		/**
		 * add_profit_columns_names_to_export.
		 *
		 * @version 3.6.8
		 * @since   3.6.8
		 *
		 * @param $columns
		 * @param $exporter
		 *
		 * @return mixed
		 */
		function add_costs_columns_names_to_export( $columns, $exporter ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_profit_select', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_variations_tab', 'no' ) ) ) {
				$columns['cost'] = __( 'Cost', 'cost-of-goods-for-woocommerce' );
			}
			return $columns;
		}

		/**
		 * add_profit_row_data_to_export.
		 *
		 * @version 3.6.8
		 * @since   3.6.8
		 *
		 * @param $row
		 * @param $item
		 *
		 * @return mixed
		 */
		function add_profit_row_data_to_export( $row, $item ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_profit_select', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_variations_tab', 'no' ) ) ) {
				$row['profit'] = $item['profit'];
			}
			return $row;
		}

		/**
		 * add_profit_columns_names_to_export.
		 *
		 * @version 3.6.8
		 * @since   3.6.8
		 *
		 * @param $columns
		 * @param $exporter
		 *
		 * @return mixed
		 */
		function add_profit_columns_names_to_export( $columns, $exporter ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_profit_select', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_variations_tab', 'no' ) ) ) {
				$columns['profit'] = __( 'Profit', 'cost-of-goods-for-woocommerce' );
			}
			return $columns;
		}

		/**
		 * add_costs_total_to_select_variations_stats_total.
		 *
		 * @version 3.6.8
		 * @since   3.6.8
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_costs_total_to_select_variations_stats_total( $clauses ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_cost_totals', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_totals_on_variations_tab', 'no' ) ) ) {
				$clauses[] = alg_wc_cog()->core->analytics->products->add_costs_total_to_select_products_stats_total_clauses();
			}
			return $clauses;
		}

		/**
		 * add_profit_total_to_select_variations_stats_total.
		 *
		 * @version 3.6.8
		 * @since   3.6.8
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_profit_total_to_select_variations_stats_total( $clauses ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_profit_totals', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_totals_on_variations_tab', 'no' ) ) ) {
				global $wpdb;
				$clauses[] = alg_wc_cog()->core->analytics->products->add_profit_total_to_select_products_stats_total_clauses();
			}
			return $clauses;
		}

		/**
		 * add_profit_to_select_variations.
		 *
		 * @version 3.6.8
		 * @since   3.6.8
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_profit_to_select_variations( $clauses ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_profit_select', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_variations_tab', 'no' ) ) ) {
				$clauses[] = ', profit';
			}
			return $clauses;
		}

		/**
		 * add_profit_to_select_variations_subquery.
		 *
		 * @version 3.6.8
		 * @since   3.6.8
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_profit_to_select_variations_subquery( $clauses ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_profit_subquery', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_variations_tab', 'no' ) ) ) {
				global $wpdb;
				$clauses[] = alg_wc_cog()->core->analytics->products->add_profit_to_select_products_subquery_clauses();
			}
			return $clauses;
		}

		/**
		 * add_costs_to_join_variations.
		 *
		 * @version 3.9.1
		 * @since   3.6.8
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_costs_to_join_variations( $clauses ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_cost_join', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_variations_tab', 'no' ) ) ) {
				global $wpdb;
				$clauses[] = alg_wc_cog()->core->analytics->products->add_costs_to_join_products_clauses();
				$clauses   =  alg_wc_cog()->core->analytics->products->maybe_add_multicurrency_to_join( $clauses );
			}
			return $clauses;
		}

		/**
		 * add_costs_to_select_variations_subquery.
		 *
		 * @version 3.6.8
		 * @since   3.6.8
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_costs_to_select_variations_subquery( $clauses ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_cost_select_subquery', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_variations_tab', 'no' ) ) ) {
				$clauses[] = alg_wc_cog()->core->analytics->products->add_costs_to_select_products_subquery_clauses();
			}
			return $clauses;
		}

		/**
		 * add_costs_to_select_variations.
		 *
		 * @version 3.6.8
		 * @since   3.6.8
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_costs_to_select_variations( $clauses ) {
			if ( apply_filters( 'alg_wc_cog_analytics_product_cost_select', 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_variations_tab', 'no' ) ) ) {
				$clauses[] = ', cost';
			}
			return $clauses;
		}

		/**
		 * add_analytics_localization_info.
		 *
		 * @version 3.6.8
		 * @since   3.6.8
		 *
		 * @param $info
		 *
		 * @return mixed
		 */
		function add_analytics_localization_info( $info ) {
			$info['variation_cost_and_profit_columns_enabled'] = 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_variations_tab', 'no' );
			$info['variation_cost_and_profit_totals_enabled']  = 'yes' === get_option( 'alg_wc_cog_cost_and_profit_totals_on_variations_tab', 'no' );
			return $info;
		}

	}

endif;

return new Alg_WC_Cost_of_Goods_Analytics_Variations();
