<?php
/**
 * Cost of Goods for WooCommerce - Analytics - Orders.
 *
 * @version 3.4.6
 * @since   3.4.6
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

use Automattic\WooCommerce\Utilities\OrderUtil;

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Analytics_Customers' ) ) :

	class Alg_WC_Cost_of_Goods_Analytics_Customers {

		/**
		 * Constructor.
		 *
		 * @version 3.4.6
		 * @since   3.4.6
		 */
		function __construct() {
			// Script localization info.
			add_filter( 'alg_wc_cog_analytics_localization_info', array( $this, 'add_analytics_localization_info' ) );

			// Costs.
			add_filter( 'woocommerce_analytics_clauses_join_customers_subquery', array( $this, 'add_costs_join_customers' ) );
			add_filter( 'woocommerce_analytics_clauses_select_customers_subquery', array( $this, 'add_costs_select_orders_stats_total' ) );

			// Profit.
			add_filter( 'woocommerce_analytics_clauses_join_customers_subquery', array( $this, 'add_profit_join_customers' ) );
			add_filter( 'woocommerce_analytics_clauses_select_customers_subquery', array( $this, 'add_profit_select_orders_stats_total' ) );
		}

		/**
		 * add_analytics_localization_info.
		 *
		 * @version 3.4.6
		 * @since   3.4.6
		 *
		 * @param $info
		 *
		 * @return mixed
		 */
		function add_analytics_localization_info( $info ) {
			$info['cost_and_profit_columns_enabled_on_customers'] = 'yes' ===alg_wc_cog_get_option( 'alg_wc_cog_analytics_customers_cost_and_profit_columns', 'no' );
			return $info;
		}

		/**
		 * add_costs_select_orders_stats_total.
		 *
		 * @version 3.4.6
		 * @since   3.4.6
		 *
		 * @param $clauses
		 *
		 * @return mixed
		 */
		function add_costs_select_orders_stats_total( $clauses ) {
			if ( 'yes' === alg_wc_cog_get_option( 'alg_wc_cog_analytics_customers_cost_and_profit_columns', 'no' ) ) {
				$clauses[] = alg_wc_cog()->core->analytics->orders->get_order_costs_total_meta_select_clauses();
			}
			return $clauses;
		}

		/**
		 * add_costs_join_customers.
		 *
		 * @version 3.4.6
		 * @since   3.4.6
		 *
		 * @param $clauses
		 *
		 * @return mixed
		 */
		function add_costs_join_customers( $clauses ) {
			if ( 'yes' === alg_wc_cog_get_option( 'alg_wc_cog_analytics_customers_cost_and_profit_columns', 'no' ) ) {
				$clauses[] = alg_wc_cog()->core->analytics->orders->get_order_cost_meta_join_clauses();
			}

			return $clauses;
		}

		/**
		 * add_profit_join_customers.
		 *
		 * @version 3.4.6
		 * @since   3.4.6
		 *
		 * @param $clauses
		 *
		 * @return mixed
		 */
		function add_profit_join_customers( $clauses ) {
			if ( 'yes' === alg_wc_cog_get_option( 'alg_wc_cog_analytics_customers_cost_and_profit_columns', 'no' ) ) {
				$clauses[] = alg_wc_cog()->core->analytics->orders->get_order_profit_meta_join_clauses();
			}
			return $clauses;
		}

		/**
		 * add_profit_select_orders_stats_total.
		 *
		 * @version 3.4.6
		 * @since   3.4.6
		 *
		 * @param $clauses
		 *
		 * @return mixed
		 */
		function add_profit_select_orders_stats_total( $clauses ) {
			if ( 'yes' === alg_wc_cog_get_option( 'alg_wc_cog_analytics_customers_cost_and_profit_columns', 'no' ) ) {
				$clauses[] = alg_wc_cog()->core->analytics->orders->get_order_profit_total_meta_select_clauses();
			}
			return $clauses;
		}

	}

endif;

return new Alg_WC_Cost_of_Goods_Analytics_Customers();