<?php
/**
 * Cost of Goods for WooCommerce - Analytics - Categories.
 *
 * @version 2.5.5
 * @since   2.5.5
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Analytics_Categories' ) ) :

	class Alg_WC_Cost_of_Goods_Analytics_Categories {

		/**
		 * Constructor.
		 *
		 * @version 2.5.5
		 * @since   2.5.5
		 *
		 */
		function __construct() {
			// Costs.
			add_filter( 'woocommerce_analytics_clauses_join_categories_subquery', array( $this, 'add_costs_to_categories_join_clauses' ) );
			add_filter( 'woocommerce_analytics_clauses_select_categories_subquery', array( $this, 'add_costs_to_select_categories_subquery' ) );
			add_filter( 'alg_wc_cog_analytics_product_cost_totals', array( $this, 'change_product_clause_based_on_categories_totals_option' ) );
			add_filter( 'alg_wc_cog_analytics_product_cost_select', array( $this, 'change_product_clause_based_on_categories_columns_option' ) );
			add_filter( 'alg_wc_cog_analytics_product_cost_select_subquery', array( $this, 'change_product_clause_based_on_categories_columns_option' ) );

			// Profit.
			add_filter( 'woocommerce_analytics_clauses_select_categories_subquery', array( $this, 'add_profit_to_select_categories_subquery' ) );
			add_filter( 'alg_wc_cog_analytics_product_profit_totals', array( $this, 'change_product_clause_based_on_categories_totals_option' ) );
			add_filter( 'alg_wc_cog_analytics_product_cost_join', array( $this, 'change_product_clause_based_on_categories_totals_option' ) );
			add_filter( 'alg_wc_cog_analytics_product_profit_select', array( $this, 'change_product_clause_based_on_categories_columns_option' ) );
			add_filter( 'alg_wc_cog_analytics_product_profit_subquery', array( $this, 'change_product_clause_based_on_categories_columns_option' ) );

			// Script localization info.
			add_filter( 'alg_wc_cog_analytics_localization_info', array( $this, 'add_analytics_localization_info' ) );
		}

		/**
		 * change_product_clause_based_on_categories_totals_option.
		 *
		 * @version 2.5.5
		 * @since   2.5.5
		 *
		 * @param $enabled
		 *
		 * @return bool
		 */
		function change_product_clause_based_on_categories_totals_option( $enabled ) {
			$enabled = 'yes' === get_option( 'alg_wc_cog_cost_and_profit_totals_on_categories_tab', 'no' ) ? true : $enabled;
			return $enabled;
		}

		/**
		 * change_product_clause_based_on_categories_columns_option.
		 *
		 * @version 2.5.5
		 * @since   2.5.5
		 *
		 * @param $enabled
		 *
		 * @return bool
		 */
		function change_product_clause_based_on_categories_columns_option( $enabled ) {
			$enabled = 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_categories_tab', 'no' ) ? true : $enabled;
			return $enabled;
		}

		/**
		 * add_costs_to_categories_join_clauses.
		 *
		 * @version 2.5.5
		 * @since   2.5.5
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_costs_to_categories_join_clauses( $clauses ) {
			if ( 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_categories_tab', 'no' ) ) {
				global $wpdb;
				$clauses[] = "LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta alg_cog_oimc ON {$wpdb->prefix}wc_order_product_lookup.order_item_id = alg_cog_oimc.order_item_id AND alg_cog_oimc.meta_key = '_alg_wc_cog_item_cost'";
			}
			return $clauses;
		}

		/**
		 * add_costs_to_select_categories_subquery.
		 *
		 * @version 2.5.5
		 * @since   2.5.5
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_costs_to_select_categories_subquery( $clauses ) {
			if ( 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_categories_tab', 'no' ) ) {
				$clauses[] = ', SUM(IFNULL(alg_cog_oimc.meta_value * product_qty, 0)) AS cost';
			}
			return $clauses;
		}

		/**
		 * add_profit_to_select_categories_subquery.
		 *
		 * @version 2.5.5
		 * @since   2.5.5
		 *
		 * @param $clauses
		 *
		 * @return array
		 */
		function add_profit_to_select_categories_subquery( $clauses ) {
			if ( 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_categories_tab', 'no' ) ) {
				global $wpdb;
				$clauses[] = ", IFNULL((SUM({$wpdb->prefix}wc_order_product_lookup.product_net_revenue) - SUM(alg_cog_oimc.meta_value * product_qty)), 0) AS profit";
			}
			return $clauses;
		}

		/**
		 * add_analytics_localization_info.
		 *
		 * @version 2.5.5
		 * @since   2.5.5
		 *
		 * @param $info
		 *
		 * @return mixed
		 */
		function add_analytics_localization_info( $info ) {
			$info['cost_and_profit_totals_enabled_on_categories']  = 'yes' === get_option( 'alg_wc_cog_cost_and_profit_totals_on_categories_tab', 'no' );
			$info['cost_and_profit_columns_enabled_on_categories'] = 'yes' === get_option( 'alg_wc_cog_cost_and_profit_column_on_categories_tab', 'no' );
			return $info;
		}
		

	}

endif;

return new Alg_WC_Cost_of_Goods_Analytics_Categories();
