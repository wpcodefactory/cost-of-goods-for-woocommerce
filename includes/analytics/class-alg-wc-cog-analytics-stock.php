<?php
/**
 * Cost of Goods for WooCommerce - Analytics - Stock.
 *
 * @version 2.5.5
 * @since   2.4.5
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Analytics_Stock' ) ) :

	class Alg_WC_Cost_of_Goods_Analytics_Stock {

		/**
		 * $consider_stock_for_calculation.
		 *
		 * @since 2.5.5
		 *
		 * @var null
		 */
		protected $consider_stock_for_calculation = null;

		/**
		 * Constructor.
		 *
		 * @version 2.4.5
		 * @since   2.4.5
		 *
		 * @todo Add cost and profit totals on summary.
		 *		
		 */
		function __construct() {
			// Script localization info.
			add_filter( 'alg_wc_cog_analytics_localization_info', array( $this, 'add_analytics_localization_info' ) );
			// Get column values.
			add_filter( 'woocommerce_rest_prepare_report_stock', array( $this, 'get_column_values' ), 10, 2 );
			// Export.
			add_filter( 'woocommerce_report_stock_export_columns', array( $this, 'export_columns_names' ) );
			add_filter( 'woocommerce_report_stock_prepare_export_item', array( $this, 'export_columns_values' ), 10, 2 );
			// COG Filter (Filter products with costs).
			add_filter( 'posts_join', array( $this, 'get_only_products_with_costs' ), 10, 2 );
		}

		/**
		 * get_only_products_with_costs.
		 *
		 * @version 2.4.5
		 * @since   2.4.5
		 *
		 * @param $join
		 * @param $query
		 *
		 * @return string
		 */
		function get_only_products_with_costs( $join, $query ) {
			if (
				isset( $_GET['alg_cog_stock_filter'] ) &&
				'with_cost' === $_GET['alg_cog_stock_filter'] &&
				'yes' === get_option( 'alg_wc_cog_filter_enabled_on_analytics_stock', 'no' ) &&
				! empty( $post_type = $query->get( 'post_type' ) ) &&
				is_array( $post_type ) &&
				! empty( $intersect = array_intersect( $post_type, array( 'product', 'product_variation' ) ) ) &&
				count( $intersect ) == 2
			) {
				global $wpdb;
				$join .= " JOIN {$wpdb->postmeta} AS alg_cog_pm ON {$wpdb->posts}.ID = alg_cog_pm.post_id AND alg_cog_pm.meta_key = '_alg_wc_cog_cost' AND alg_cog_pm.meta_value != '' AND alg_cog_pm.meta_value != 0";
			}
			return $join;
		}

		/**
		 * export_columns_names.
		 *
		 * @version 2.4.5
		 * @since   2.4.5
		 *
		 * @see \Automattic\WooCommerce\Admin\API\Reports\Stock\Controller::get_export_columns()
		 *
		 * @param $columns
		 *
		 * @return mixed
		 */
		function export_columns_names( $columns ) {
			// Cost and profit
			if ( 'yes' === get_option( 'alg_wc_cog_cost_and_profit_enabled_on_analytics_stock', 'no' ) ) {
				$columns['product_cost']   = __( 'Cost', 'cost-of-goods-for-woocommerce' );
				$columns['product_profit'] = __( 'Profit', 'cost-of-goods-for-woocommerce' );
			}
			// Category
			if ( 'yes' === get_option( 'alg_wc_cog_category_enabled_on_analytics_stock', 'no' ) ) {
				$columns['product_cat'] = __( 'Category', 'cost-of-goods-for-woocommerce' );
			}
			return $columns;
		}

		/**
		 * export_columns_values.
		 *
		 * @version 2.4.5
		 * @since   2.4.5
		 *
		 * @see \Automattic\WooCommerce\Admin\API\Reports\Stock\Controller::prepare_item_for_export()
		 *
		 * @param $export_item
		 * @param $item
		 *
		 * @return mixed
		 */
		function export_columns_values( $export_item, $item ) {
			// Cost and profit
			if ( 'yes' === get_option( 'alg_wc_cog_cost_and_profit_enabled_on_analytics_stock', 'no' ) ) {
				$export_item['product_cost']   = $item['product_cost'];
				$export_item['product_profit'] = $item['product_profit'];
			}
			// Category
			if ( 'yes' === get_option( 'alg_wc_cog_category_enabled_on_analytics_stock', 'no' ) ) {
				$export_item['product_cat'] = $item['product_cat'];
			}
			return $export_item;
		}

		/**
		 * consider_stock_for_calculation.
		 *
		 * @version 2.5.5
		 * @since   2.5.5
		 *
		 * @return mixed
		 */
		function consider_stock_for_calculation() {
			if ( null === $this->consider_stock_for_calculation ) {
				$this->consider_stock_for_calculation = 'yes' === get_option( 'alg_wc_cog_analytics_stock_considers_stock', 'yes' );
			}
			return $this->consider_stock_for_calculation;
		}

		/**
		 * get_column_values.
		 *
		 * @see \Automattic\WooCommerce\Admin\API\Reports\Stock\Controller::prepare_item_for_response()
		 *
		 * @version 2.5.5
		 * @since   2.4.5
		 *
		 * @param WP_REST_Response $response
		 * @param $product
		 *
		 * @return mixed
		 */
		function get_column_values( WP_REST_Response $response, $product ) {
			// Cost and profit
			if (
				'yes' === get_option( 'alg_wc_cog_cost_and_profit_enabled_on_analytics_stock', 'no' ) &&
				is_a( $product, 'WC_Product' )
			) {
				$response->data['product_cost']   = alg_wc_cog()->core->products->get_product_cost( $product->get_id() );
				$response->data['product_profit'] = alg_wc_cog()->core->products->get_product_profit( $product->get_id() );
				$response->data['product_price']  = (float) $product->get_price();
				if ( $this->consider_stock_for_calculation() ) {
					if ( $response->data['stock_quantity'] > 0 ) {
						$response->data['product_cost']   *= $response->data['stock_quantity'];
						$response->data['product_profit'] *= $response->data['stock_quantity'];
						$response->data['product_price']  *= $response->data['stock_quantity'];
					}
				}
			}
			// Category
			if (
				'yes' === get_option( 'alg_wc_cog_category_enabled_on_analytics_stock', 'no' ) &&
				is_a( $product, 'WC_Product' )
			) {
				$response->data['product_cat'] = $this->get_categories( wc_get_product_term_ids( $product->get_id(), 'product_cat' ) );
			}
			return $response;
		}

		/**
		 * add_analytics_localization_info.
		 *
		 * @version 2.4.5
		 * @since   2.4.5
		 *
		 * @param $info
		 *
		 * @return mixed
		 */
		function add_analytics_localization_info( $info ) {
			$info['cost_and_profit_enabled_on_stock'] = 'yes' === get_option( 'alg_wc_cog_cost_and_profit_enabled_on_analytics_stock', 'no' );
			$info['category_enabled_on_stock']        = 'yes' === get_option( 'alg_wc_cog_category_enabled_on_analytics_stock', 'no' );
			$info['filter_enabled_on_stock']          = 'yes' === get_option( 'alg_wc_cog_filter_enabled_on_analytics_stock', 'no' );
			return $info;
		}

		/**
		 * Get categories column export value.
		 *
		 * @version 2.4.5
		 * @since   2.4.5
		 *
		 * @param array $category_ids Category IDs from report row.
		 * @return string
		 */
		function get_categories( $category_ids ) {
			$category_names = get_terms(
				array(
					'taxonomy' => 'product_cat',
					'include'  => $category_ids,
					'fields'   => 'names',
				)
			);
			return implode( ', ', $category_names );
		}

	}

endif;

return new Alg_WC_Cost_of_Goods_Analytics_Stock();
