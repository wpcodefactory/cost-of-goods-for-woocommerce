<?php
/**
 * Cost of Goods for WooCommerce - Analytics - Stock.
 *
 * @version 3.8.4
 * @since   2.4.5
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

use Automattic\WooCommerce\Utilities\OrderUtil;

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Analytics_Stock' ) ) :

	class Alg_WC_Cost_of_Goods_Analytics_Stock {

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
			add_filter( 'woocommerce_report_stock_prepare_export_item', array(
				$this,
				'export_columns_values'
			), 10, 2 );

			// COG Filter (Filter products with costs).
			add_filter( 'posts_join', array( $this, 'get_only_products_with_costs' ), 10, 2 );

			// COG filter.
			add_filter( 'posts_clauses', array( $this, 'get_only_cog_products_by_posts_clauses' ), PHP_INT_MAX, 2 );

			// Calculate total cost and profit.
			add_filter( 'woocommerce_analytics_stock_stats_query', array( $this, 'get_total_cost_and_profit' ) );

			// Delete stock cost and profit totals from cache.
			add_action( 'woocommerce_update_product', array( $this, 'clear_stock_cost_and_profit_totals_cache' ) );
			add_action( 'woocommerce_new_product', array( $this, 'clear_stock_cost_and_profit_totals_cache' ) );
			add_action( 'update_option_woocommerce_notify_low_stock_amount', array(
				$this,
				'clear_stock_cost_and_profit_totals_cache'
			) );
			add_action( 'update_option_woocommerce_notify_no_stock_amount', array(
				$this,
				'clear_stock_cost_and_profit_totals_cache'
			) );
			add_action( 'admin_init', array(
				$this,
				'clear_stock_cost_and_profit_totals_cache_on_clear_analytics_cache'
			) );
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
		 * @version 3.8.4
		 * @since   3.2.1
		 *
		 * @param $query_results
		 *
		 * @return mixed
		 */
		function get_total_cost_and_profit( $query_results ) {
			if ( 'yes' !== alg_wc_cog_get_option( 'alg_wc_cog_cost_and_profit_enabled_on_analytics_stock', 'no' ) ) {
				return $query_results;
			}
			$args = array(
				'type'                 => sanitize_text_field( $_REQUEST['type'] ?? '' ),
				'alg_cog_stock_filter' => sanitize_text_field( $_REQUEST['alg_cog_stock_filter'] ?? '' ),
				'get_price_method'     => alg_wc_cog_get_option( 'alg_wc_cog_products_get_price_method', 'wc_get_price_excluding_tax' )
			);

			$type                 = $args['type'];
			$alg_cog_stock_filter = $args['alg_cog_stock_filter'];
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

				if ( 'cog_products' === $alg_cog_stock_filter ) {
					$filtered_product_ids = $this->get_cog_product_ids();
				}

				$total_cost_and_profit_info = $this->get_total_cost_and_profit_from_database( $filtered_product_ids );
				set_transient( $transient_name, $total_cost_and_profit_info );
			}

			if ( ! empty( $total_cost_and_profit_info ) ) {
				$query_results['cost']         = $total_cost_and_profit_info['cost'];
				$query_results['total_cost']   = $total_cost_and_profit_info['total_cost'];
				$query_results['profit']       = $total_cost_and_profit_info['profit'];
				$query_results['total_profit'] = $total_cost_and_profit_info['total_profit_ex_tax'];
				if ( 'wc_get_price_including_tax' === alg_wc_cog_get_option( 'alg_wc_cog_products_get_price_method', 'wc_get_price_excluding_tax' ) ) {
					$query_results['total_profit'] = $total_cost_and_profit_info['total_profit'];
				}

				// Extra data.
				if ( 'yes' === alg_wc_cog_get_option( 'alg_wc_cog_analytics_stock_extra_data', 'no' ) ) {
					$total_stock = $total_cost_and_profit_info['total_stock'];
					$total_price = $total_cost_and_profit_info['total_price_ex_tax'];
					$price = $total_cost_and_profit_info['price_ex_tax'];
					if ( 'wc_get_price_including_tax' === alg_wc_cog_get_option( 'alg_wc_cog_products_get_price_method', 'wc_get_price_excluding_tax' ) ) {
						$total_price = $total_cost_and_profit_info['total_price'];
						$price = $total_cost_and_profit_info['price'];
					}
					$total_products                  = $total_cost_and_profit_info['total_products'];
					$total_cost                      = $total_cost_and_profit_info['total_cost'];
					$average_cost                    = ( 0 != $total_stock ? ( $total_cost / $total_stock ) : 0 );
					$average_price                   = ( 0 != $total_stock ? ( $total_price / $total_stock ) : 0 );
					$average_profit                  = ( $average_price - $average_cost );
					$query_results['total_stock']    = $total_stock;
					$query_results['total_products'] = $total_products;
					$query_results['average_cost']   = $average_cost;
					$query_results['average_price']  = $average_price;
					$query_results['average_profit'] = $average_profit;
					$query_results['price']          = $price;
					$query_results['total_price']    = $total_price;
				}
			}

			return $query_results;
		}

		/**
		 * get_total_cost_and_profit_from_database.
		 *
		 * @version 3.8.4
		 * @since   3.2.1
		 *
		 * @param $post_ids
		 *
		 * @return array|object|stdClass|null
		 */
		function get_total_cost_and_profit_from_database( $post_ids = array() ) {
			global $wpdb;

			$sub_query = "
			SELECT DISTINCT posts.ID as product_id,
			IFNULL(alg_wc_cog_price_pm.meta_value, 0) + 0 AS price, 
		    IFNULL(alg_wc_cog_price_pm.meta_value, 0) * IF(alg_wc_cog_stock_pm.meta_value = 0 OR alg_wc_cog_stock_pm.meta_value IS NULL, 1, alg_wc_cog_stock_pm.meta_value + 0) AS total_price,
			IFNULL(alg_wc_cog_cost_pm.meta_value, 0) + IFNULL(alg_wc_cog_profit_pm.meta_value, 0) AS price_ex_tax,
		    (IFNULL(alg_wc_cog_cost_pm.meta_value, 0)*alg_wc_cog_stock_pm.meta_value) + (IFNULL(alg_wc_cog_profit_pm.meta_value, 0)*alg_wc_cog_stock_pm.meta_value) AS total_price_ex_tax,			
			IFNULL(alg_wc_cog_cost_pm.meta_value, 0) + 0 AS cost, 
			IFNULL(alg_wc_cog_cost_pm.meta_value, 0) * IF(alg_wc_cog_stock_pm.meta_value = 0 OR alg_wc_cog_stock_pm.meta_value IS NULL, 1, alg_wc_cog_stock_pm.meta_value + 0) AS total_cost, 
			IFNULL(alg_wc_cog_profit_pm.meta_value, 0) + 0 AS profit_ex_tax, 
			IFNULL(alg_wc_cog_profit_pm.meta_value, 0) * IF(alg_wc_cog_stock_pm.meta_value = 0 OR alg_wc_cog_stock_pm.meta_value IS NULL, 1, alg_wc_cog_stock_pm.meta_value + 0) AS total_profit_ex_tax,
			(alg_wc_cog_price_pm.meta_value + 0) - (alg_wc_cog_cost_pm.meta_value + 0) AS profit, 
			( (alg_wc_cog_price_pm.meta_value + 0) * alg_wc_cog_stock_pm.meta_value) - ( (alg_wc_cog_cost_pm.meta_value+ 0) * alg_wc_cog_stock_pm.meta_value) AS total_profit,
			IF(alg_wc_cog_stock_pm.meta_value = 0 OR alg_wc_cog_stock_pm.meta_value IS NULL, 0, alg_wc_cog_stock_pm.meta_value + 0) AS stock
			FROM {$wpdb->posts} posts			
			INNER JOIN {$wpdb->postmeta} alg_wc_cog_cost_pm ON posts.ID = alg_wc_cog_cost_pm.post_id and alg_wc_cog_cost_pm.meta_key = '_alg_wc_cog_cost'
			INNER JOIN {$wpdb->postmeta} alg_wc_cog_profit_pm ON posts.ID = alg_wc_cog_profit_pm.post_id and alg_wc_cog_profit_pm.meta_key = '_alg_wc_cog_profit'
			INNER JOIN {$wpdb->postmeta} alg_wc_cog_stock_pm ON posts.ID = alg_wc_cog_stock_pm.post_id AND alg_wc_cog_stock_pm.meta_key = '_stock'
			INNER JOIN {$wpdb->postmeta} alg_wc_cog_price_pm ON posts.ID = alg_wc_cog_price_pm.post_id AND alg_wc_cog_price_pm.meta_key = '_price'
			WHERE posts.post_type IN ( 'product', 'product_variation' )
			AND posts.post_status IN ('publish', 'private')
			";

			if ( ! empty( $post_ids ) ) {
				$in_str    = alg_wc_cog_generate_wpdb_prepare_placeholders_from_array( $post_ids );
				$sub_query .= "AND posts . ID IN {$in_str}";
			}

			$main_query = "
			SELECT COUNT(product_id) as total_products, SUM(price) as price, SUM(total_price) as total_price, SUM(price_ex_tax) as price_ex_tax, SUM(total_price_ex_tax) as total_price_ex_tax, SUM(cost) AS cost, SUM(total_cost) AS total_cost, SUM(profit_ex_tax) AS profit_ex_tax, SUM(total_profit_ex_tax) AS total_profit_ex_tax, SUM(profit) AS profit, SUM(total_profit) AS total_profit, SUM(stock) AS total_stock
			FROM (
			    {$sub_query}
			) as alg_wc_cog_main_stock_query
			";

			$prepare_args = $post_ids;

			return $wpdb->get_row(
				$wpdb->prepare( $main_query, $prepare_args ),
				ARRAY_A
			);
		}

		/**
		 * Get count for the passed in stock status.
		 *
		 * @version 3.7.2
		 * @since   3.2.1
		 *
		 * @param string $status Status slug.
		 *
		 * @see Automattic\WooCommerce\Admin\API\Reports\Stock\Stats\DataStore::get_count()
		 *
		 */
		private function get_product_ids_by_stock_status( $status ) {
			global $wpdb;

			$join = '';

			if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
				$join = "INNER JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON posts.ID = wc_product_meta_lookup.product_id AND wc_product_meta_lookup.stock_status = %s";
			} else {
				$join = "INNER JOIN {$wpdb->postmeta} wc_product_meta_lookup ON posts.ID = wc_product_meta_lookup.post_id AND wc_product_meta_lookup.meta_key = '_stock_status' AND wc_product_meta_lookup.meta_value = %s";
			}

			$prepare_text =
				"
				SELECT DISTINCT posts.ID FROM {$wpdb->posts} posts
				{$join}
				WHERE posts.post_type IN ( 'product', 'product_variation' )				
				";

			return $wpdb->get_col(
				$wpdb->prepare( $prepare_text, $status )
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

			$no_stock_amount  = absint( max( alg_wc_cog_get_option( 'woocommerce_notify_no_stock_amount' ), 0 ) );
			$low_stock_amount = absint( max( alg_wc_cog_get_option( 'woocommerce_notify_low_stock_amount' ), 1 ) );

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
		 * get_cog_product_ids.
		 *
		 * @version 3.8.4
		 * @since   3.8.4
		 *
		 * @return int[]|WP_Post[]
		 */
		function get_cog_product_ids() {
			$args = array(
				'post_type'      => array( 'product', 'product_variation' ),
				'post_status'    => array( 'publish', 'private' ),
				'fields'         => 'ids', // only get IDs
				'posts_per_page' => - 1,    // get all
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'     => '_alg_wc_cog_cost',
						'value'   => array( '', '0' ),
						'compare' => 'NOT IN',
					),
					array(
						'key'     => '_price',
						'value'   => array( '', '0' ),
						'compare' => 'NOT IN',
					),
					array(
						'key'     => '_stock',
						'value'   => array( '', '0' ),
						'compare' => 'NOT IN',
					),
					array(
						'key'     => '_stock',
						'value'   => 0,
						'compare' => '>',
						'type'    => 'NUMERIC',
					),
				),
			);

			return get_posts( $args );
		}

		/**
		 * get_only_products_with_costs.
		 *
		 * @version 3.8.4
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
				'yes' === alg_wc_cog_get_option( 'alg_wc_cog_filter_enabled_on_analytics_stock', 'no' ) &&
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
		 * get_only_cog_products_by_posts_clauses.
		 *
		 * @version 3.8.4
		 * @since   3.8.4
		 *
		 * @param $clauses
		 * @param $query
		 *
		 * @return mixed
		 */
		function get_only_cog_products_by_posts_clauses( $clauses, $query ) {
			if (
				isset( $_GET['alg_cog_stock_filter'] ) &&
				'cog_products' === $_GET['alg_cog_stock_filter'] &&
				'yes' === alg_wc_cog_get_option( 'alg_wc_cog_filter_enabled_on_analytics_stock', 'no' ) &&
				! empty( $post_type = $query->get( 'post_type' ) ) &&
				is_array( $post_type ) &&
				! empty( $intersect = array_intersect( $post_type, array( 'product', 'product_variation' ) ) ) &&
				count( $intersect ) == 2
			) {
				global $wpdb;

				$clauses['where'] = " AND ((
					{$wpdb->posts}.post_type = 'product' 
					AND ({$wpdb->posts}.post_status = 'publish' OR {$wpdb->posts}.post_status = 'private')
				) OR (
					{$wpdb->posts}.post_type = 'product_variation' 
					AND ({$wpdb->posts}.post_status = 'publish' OR {$wpdb->posts}.post_status = 'private')
				))";

				// Cost not empty.
				$clauses['join'] .= " JOIN {$wpdb->postmeta} AS alg_wc_cog_cost_pm ON {$wpdb->posts}.ID = alg_wc_cog_cost_pm.post_id AND alg_wc_cog_cost_pm.meta_key = '_alg_wc_cog_cost' AND alg_wc_cog_cost_pm.meta_value != '' AND alg_wc_cog_cost_pm.meta_value != 0";

				// Price not empty.
				$clauses['join'] .= " JOIN {$wpdb->postmeta} AS alg_wc_cog_price_pm ON {$wpdb->posts}.ID = alg_wc_cog_price_pm.post_id AND alg_wc_cog_price_pm.meta_key = '_price' AND alg_wc_cog_price_pm.meta_value != '' AND alg_wc_cog_price_pm.meta_value != 0";

				// Stock not empty and greater than zero.
				$clauses['join'] .= " JOIN {$wpdb->postmeta} AS alg_wc_cog_stock_pm ON {$wpdb->posts}.ID = alg_wc_cog_stock_pm.post_id AND alg_wc_cog_stock_pm.meta_key = '_stock' AND alg_wc_cog_stock_pm.meta_value != '' AND alg_wc_cog_stock_pm.meta_value != 0 AND alg_wc_cog_stock_pm.meta_value > 0 AND alg_wc_cog_stock_pm.meta_value IS NOT NULL";

			}

			return $clauses;
		}

		/**
		 * export_columns_names.
		 *
		 * @version 3.8.4
		 * @since   2.4.5
		 *
		 * @param $columns
		 *
		 * @return mixed
		 * @see \Automattic\WooCommerce\Admin\API\Reports\Stock\Controller::get_export_columns()
		 *
		 */
		function export_columns_names( $columns ) {

			// Cost and profit
			if ( 'yes' === alg_wc_cog_get_option( 'alg_wc_cog_cost_and_profit_enabled_on_analytics_stock', 'no' ) ) {
				$columns['product_cost']         = __( 'Cost', 'cost-of-goods-for-woocommerce' );
				$columns['product_profit']       = __( 'Profit', 'cost-of-goods-for-woocommerce' );
				$columns['product_cost_total']   = __( 'Total Cost', 'cost-of-goods-for-woocommerce' );
				$columns['product_profit_total'] = __( 'Total Profit', 'cost-of-goods-for-woocommerce' );
			}

			if ( 'yes' === alg_wc_cog_get_option( 'alg_wc_cog_analytics_stock_extra_data', 'no' ) ) {
				$columns['product_price']       = __( 'Price', 'cost-of-goods-for-woocommerce' );
				$columns['product_price_total'] = __( 'Total price', 'cost-of-goods-for-woocommerce' );
				$columns['product_cat']         = __( 'Category', 'cost-of-goods-for-woocommerce' );
			}

			return $columns;
		}

		/**
		 * export_columns_values.
		 *
		 * @version 3.8.4
		 * @since   2.4.5
		 *
		 * @param $export_item
		 * @param $item
		 *
		 * @return mixed
		 * @see \Automattic\WooCommerce\Admin\API\Reports\Stock\Controller::prepare_item_for_export()
		 *
		 */
		function export_columns_values( $export_item, $item ) {

			// Cost and profit.
			if ( 'yes' === get_option( 'alg_wc_cog_cost_and_profit_enabled_on_analytics_stock', 'no' ) ) {
				$export_item['product_cost']         = $item['product_cost'];
				$export_item['product_profit']       = $item['product_profit'];
				$export_item['product_cost_total']   = $item['product_cost_total'];
				$export_item['product_profit_total'] = $item['product_profit_total'];
			}

			if ( 'yes' === alg_wc_cog_get_option( 'alg_wc_cog_analytics_stock_extra_data', 'no' ) ) {
				$export_item['product_price']       = $item['product_price'];
				$export_item['product_price_total'] = $item['product_price_total'];
				$export_item['product_cat']         = $item['product_cat'];
			}

			return $export_item;
		}

		/**
		 * get_column_values.
		 *
		 * @version 3.8.4
		 * @since   2.4.5
		 *
		 * @param WP_REST_Response $response
		 * @param $product
		 *
		 * @return mixed
		 * @see \Automattic\WooCommerce\Admin\API\Reports\Stock\Controller::prepare_item_for_response()
		 *
		 */
		function get_column_values( WP_REST_Response $response, $product ) {
			if ( ! is_a( $product, 'WC_Product' ) ) {
				return $response;
			}

			// Stock.
			$stock_qty = $response->data['stock_quantity'] > 0 ? $response->data['stock_quantity'] : 1;

			// Cost and profit.
			if ( 'yes' === get_option( 'alg_wc_cog_cost_and_profit_enabled_on_analytics_stock', 'no' ) ) {
				$response->data['product_cost']         = alg_wc_cog()->core->products->get_product_cost( $product->get_id() );
				$response->data['product_profit']       = alg_wc_cog()->core->products->get_product_profit( array(
					'product'           => $product,
					'get_profit_method' => 'calculation', // meta || calculation || smart
				) );
				$response->data['product_price']        = (float) $product->get_price();
				$response->data['product_cost_total']   = $response->data['product_cost'] * $stock_qty;
				$response->data['product_profit_total'] = $response->data['product_profit'] * $stock_qty;
				$response->data['product_price_total']  = $response->data['product_price'] * $stock_qty;
			}

			// Extra data (price, category).
			if ( 'yes' === alg_wc_cog_get_option( 'alg_wc_cog_analytics_stock_extra_data', 'no' ) ) {
				$response->data['product_price']       = alg_wc_cog()->core->products->get_product_price( $product );
				$response->data['product_price_total'] = $response->data['product_price'] * $stock_qty;

				// Category.
				$product_id = $product->get_id();
				if ( $parent_id = wp_get_post_parent_id( $product_id ) ) {
					$product_id = $parent_id;
				}
				$term_ids                      = wc_get_product_term_ids( $product_id, 'product_cat' );
				$response->data['product_cat'] = ! empty( $term_ids ) ? $this->get_categories( $term_ids ) : array();
			}

			return $response;
		}

		/**
		 * add_analytics_localization_info.
		 *
		 * @version 3.8.4
		 * @since   2.4.5
		 *
		 * @param $info
		 *
		 * @return mixed
		 */
		function add_analytics_localization_info( $info ) {
			$info['cost_and_profit_enabled_on_stock'] = 'yes' === alg_wc_cog_get_option( 'alg_wc_cog_cost_and_profit_enabled_on_analytics_stock', 'no' );
			$info['filter_enabled_on_stock'] = 'yes' === alg_wc_cog_get_option( 'alg_wc_cog_filter_enabled_on_analytics_stock', 'no' );
			$info['extra_data']              = 'yes' === alg_wc_cog_get_option( 'alg_wc_cog_analytics_stock_extra_data', 'no' );

			return $info;
		}

		/**
		 * Get categories column export value.
		 *
		 * @version 2.4.5
		 * @since   2.4.5
		 *
		 * @param array $category_ids Category IDs from report row.
		 *
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
