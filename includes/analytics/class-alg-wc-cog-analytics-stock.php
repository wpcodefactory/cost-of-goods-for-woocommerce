<?php
/**
 * Cost of Goods for WooCommerce - Analytics - Stock.
 *
 * @version 3.2.2
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
		 * @version 3.2.2
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
			// Calculate total cost and profit.
			add_filter( 'woocommerce_analytics_stock_stats_query', array( $this, 'get_total_cost_and_profit' ) );
			// Delete stock cost and profit totals from cache
			add_action( 'woocommerce_update_product', array( $this, 'clear_stock_cost_and_profit_totals_cache' ) );
			add_action( 'woocommerce_new_product', array( $this, 'clear_stock_cost_and_profit_totals_cache' ) );
			add_action( 'update_option_woocommerce_notify_low_stock_amount', array( $this, 'clear_stock_cost_and_profit_totals_cache' ) );
			add_action( 'update_option_woocommerce_notify_no_stock_amount', array( $this, 'clear_stock_cost_and_profit_totals_cache' ) );
			add_action( 'admin_init', array( $this, 'clear_stock_cost_and_profit_totals_cache_on_clear_analytics_cache' ) );
		}

		/**
		 * clear_stock_cost_and_profit_totals_cache_on_clear_analytics_cache.
		 *
		 * @version 3.2.2
		 * @since   3.2.2
		 *
		 * @return void
		 */
		function clear_stock_cost_and_profit_totals_cache_on_clear_analytics_cache() {
			if (
				isset( $_GET['page'] ) && 'wc-status' === $_GET['page'] &&
				isset( $_GET['tab'] ) && 'tools' === $_GET['tab'] &&
				isset( $_GET['action'] ) && 'clear_woocommerce_analytics_cache' === $_GET['action']
			) {
				$this->clear_stock_cost_and_profit_totals_cache();
			}
		}

		/**
		 * clear_stock_cost_and_profit_totals_cache.
		 *
		 * @version 3.2.1
		 * @since   3.2.1
		 *
		 * @return void
		 */
		function clear_stock_cost_and_profit_totals_cache() {
			global $wpdb;
			$prefix           = '_transient_alg_wc_cog_scpt_';
			$meta_key_pattern = $wpdb->esc_like( $prefix ) . '%';
			$query            = $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $meta_key_pattern );
			$wpdb->query( $query );
		}

		/**
		 * get_total_cost_and_profit.
		 *
		 * @version 3.2.1
		 * @since   3.2.1
		 *
		 * @param $query_results
		 *
		 * @return mixed
		 */
		function get_total_cost_and_profit( $query_results ) {
			if ( 'yes' !== get_option( 'alg_wc_cog_cost_and_profit_enabled_on_analytics_stock', 'no' ) ) {
				return $query_results;
			}
			$args = array(
				'type' => isset( $_REQUEST['type'] ) ? $_REQUEST['type'] : ''
			);
			$type = $args['type'];
			if ( ! in_array( $type, array( 'lowstock', 'instock', 'onbackorder', 'outofstock' ) ) ) {
				$type = '';
			}

			$transient_name = 'alg_wc_cog_scpt_' . md5( maybe_serialize( $args ) );
			if ( false === ( $total_cost_and_profit_info = get_transient( $transient_name ) ) ) {
				$filtered_product_ids = array();
				if ( 'lowstock' === $type ) {
					$filtered_product_ids = $this->get_low_stock_product_ids();
				} else {
					$filtered_product_ids = $this->get_product_ids_by_stock_status( $type );
				}
				$total_cost_and_profit_info = $this->get_total_cost_and_profit_from_database( $filtered_product_ids );
				set_transient( $transient_name, $total_cost_and_profit_info );
			}

			if ( ! empty( $total_cost_and_profit_info ) ) {
				$query_results['cost']            = $total_cost_and_profit_info['cost'];
				$query_results['cost_with_qty']   = $total_cost_and_profit_info['cost_with_qty'];
				$query_results['profit']          = $total_cost_and_profit_info['profit'];
				$query_results['profit_with_qty'] = $total_cost_and_profit_info['profit_with_qty'];
			}

			return $query_results;
		}

		/**
		 * get_total_cost_and_profit_from_database.
		 *
		 * @version 3.2.2
		 * @since   3.2.1
		 *
		 * @param $post_ids
		 *
		 * @return array|object|stdClass|null
		 */
		function get_total_cost_and_profit_from_database( $post_ids = array() ) {
			global $wpdb;

			$query = "
			SELECT COUNT(DISTINCT posts.ID) as total_products, SUM(alg_wc_cog_cost_pm.meta_value) AS cost, SUM(alg_wc_cog_cost_pm.meta_value * IF(stock_pm.meta_value = 0 or stock_pm.meta_value IS null, 1, stock_pm.meta_value)) AS cost_with_qty, SUM(alg_wc_cog_profit_pm.meta_value) AS profit, SUM(alg_wc_cog_profit_pm.meta_value * IF(stock_pm.meta_value = 0 or stock_pm.meta_value IS null, 1, stock_pm.meta_value)) AS profit_with_qty, SUM(IF(stock_pm.meta_value = 0 or stock_pm.meta_value IS null, 1, stock_pm.meta_value)) AS stock
			FROM {$wpdb->posts} posts
			LEFT JOIN {$wpdb->postmeta} alg_wc_cog_cost_pm ON posts.ID = alg_wc_cog_cost_pm.post_id and alg_wc_cog_cost_pm.meta_key = '_alg_wc_cog_cost'
			LEFT JOIN {$wpdb->postmeta} alg_wc_cog_profit_pm ON posts.ID = alg_wc_cog_profit_pm.post_id and alg_wc_cog_profit_pm.meta_key = '_alg_wc_cog_profit'
			LEFT JOIN wp_postmeta stock_pm ON posts.ID = stock_pm.post_id AND stock_pm.meta_key = '_stock'
			WHERE posts.post_type IN ( 'product', 'product_variation' )
			AND alg_wc_cog_cost_pm.meta_value NOT IN ('',0) AND alg_wc_cog_profit_pm.meta_value NOT IN ('',0)
			";

			if ( ! empty( $post_ids ) ) {
				$in_str = alg_wc_cog_generate_wpdb_prepare_placeholders_from_array( $post_ids );
				$query  .= "AND posts . ID IN {$in_str}";
			}

			$prepare_args = $post_ids;

			return $wpdb->get_row(
				$wpdb->prepare( $query, $prepare_args ),
				ARRAY_A
			);
		}

		/**
		 * Get count for the passed in stock status.
		 *
		 * @version 3.2.1
		 * @since   3.2.1
		 *
		 * @see Automattic\WooCommerce\Admin\API\Reports\Stock\Stats\DataStore::get_count()
		 *
		 * @param  string $status Status slug.
		 */
		private function get_product_ids_by_stock_status( $status ) {
			global $wpdb;

			return $wpdb->get_col(
				$wpdb->prepare(
					"
				SELECT DISTINCT posts.ID FROM {$wpdb->posts} posts
				LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON posts.ID = wc_product_meta_lookup.product_id
				WHERE posts.post_type IN ( 'product', 'product_variation' )
				AND wc_product_meta_lookup.stock_status = %s
				",
					$status
				)
			);
		}

		/**
		 * Get low stock count (products with stock < low stock amount, but greater than no stock amount).
		 *
		 * @version 3.2.1
		 * @since   3.2.1
		 *
		 * @see Automattic\WooCommerce\Admin\API\Reports\Stock\Stats\DataStore::get_low_stock_count()
		 */
		private function get_low_stock_product_ids() {
			global $wpdb;

			$no_stock_amount  = absint( max( get_option( 'woocommerce_notify_no_stock_amount' ), 0 ) );
			$low_stock_amount = absint( max( get_option( 'woocommerce_notify_low_stock_amount' ), 1 ) );

			return $wpdb->get_col(
				$wpdb->prepare(
					"
				SELECT posts.ID FROM {$wpdb->posts} posts
				LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON posts.ID = wc_product_meta_lookup.product_id
				LEFT JOIN {$wpdb->postmeta} low_stock_amount_meta ON posts.ID = low_stock_amount_meta.post_id AND low_stock_amount_meta.meta_key = '_low_stock_amount'
				WHERE posts.post_type IN ( 'product', 'product_variation' )
				AND wc_product_meta_lookup.stock_quantity IS NOT NULL
				AND wc_product_meta_lookup.stock_status = 'instock'
				AND (
					(
						low_stock_amount_meta.meta_value > ''
						AND wc_product_meta_lookup.stock_quantity <= CAST(low_stock_amount_meta.meta_value AS SIGNED)
						AND wc_product_meta_lookup.stock_quantity > %d
					)
					OR (
						(
							low_stock_amount_meta.meta_value IS NULL OR low_stock_amount_meta.meta_value <= ''
						)
						AND wc_product_meta_lookup.stock_quantity <= %d
						AND wc_product_meta_lookup.stock_quantity > %d
					)
				)
				",
					$no_stock_amount,
					$low_stock_amount,
					$no_stock_amount
				)
			);
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
		 * @version 3.2.1
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
			$info['consider_stock_for_calculation']   = $this->consider_stock_for_calculation();
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
