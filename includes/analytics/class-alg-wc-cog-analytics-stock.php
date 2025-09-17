<?php
/**
 * Cost of Goods for WooCommerce - Analytics - Stock.
 *
 * @version 3.9.1
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
		 * $generating_download.
		 *
		 * @since 3.9.1
		 *
		 * @var bool
		 */
		protected $generating_download = false;

		/**
		 * Constructor.
		 *
		 * @version 3.9.1
		 * @since   2.4.5
		 *
		 * @todo    Add cost and profit totals on summary.
		 *
		 */
		function __construct() {
			// Script localization info.
			add_filter( 'alg_wc_cog_analytics_localization_info', array( $this, 'add_analytics_localization_info' ) );

			// Get column values.
			add_filter( 'woocommerce_rest_prepare_report_stock', array( $this, 'get_column_values' ), 10, 2 );

			// Detects when download is starting.
			add_filter( 'woocommerce_export_report_data_endpoint', function ( $endpoint, $report_type ) {
				$this->generating_download = true;
				return $endpoint;
			},10,2 );

			// Export.
			add_filter( 'woocommerce_report_stock_export_columns', array( $this, 'export_columns_names' ) );
			add_filter( 'woocommerce_report_stock_prepare_export_item', array( $this, 'export_columns_values' ), 10, 2 );

			// COG filter.
			add_filter( 'posts_clauses', array( $this, 'get_only_cog_products_by_posts_clauses' ), PHP_INT_MAX, 2 );

			// Calculate total cost and profit.
			add_filter( 'woocommerce_analytics_stock_stats_query', array( $this, 'get_total_cost_and_profit' ) );

			// Delete stock cost and profit totals from cache.
			add_action( 'woocommerce_update_product', array( $this, 'clear_stock_cost_and_profit_totals_cache' ) );
			add_action( 'woocommerce_new_product', array( $this, 'clear_stock_cost_and_profit_totals_cache' ) );
			add_action( 'update_option_woocommerce_notify_low_stock_amount', array( $this, 'clear_stock_cost_and_profit_totals_cache' ) );
			add_action( 'update_option_woocommerce_notify_no_stock_amount', array( $this, 'clear_stock_cost_and_profit_totals_cache' ) );
			add_action( 'admin_init', array( $this, 'clear_stock_cost_and_profit_totals_cache_on_clear_analytics_cache' ) );

			// Handle custom queries with wc_get_products.
			add_filter( 'woocommerce_product_data_store_cpt_get_products_query', array( $this, 'handle_not_empty_prices_get_products_query' ), 10, 2 );
			add_filter( 'woocommerce_product_data_store_cpt_get_products_query', array( $this, 'handle_not_empty_costs_get_products_query' ), 10, 2 );
			add_filter( 'woocommerce_product_data_store_cpt_get_products_query', array( $this, 'handle_with_stock_qty_get_products_query' ), 10, 2 );
			add_filter( 'woocommerce_product_data_store_cpt_get_products_query', array( $this, 'handle_low_stock_get_products_query' ), 10, 2 );
		}

		/**
		 * handle_low_stock_get_products_query.
		 *
		 * @version 3.9.1
		 * @since   3.9.1
		 *
		 * @param $query
		 * @param $query_vars
		 *
		 * @return mixed
		 */
		function handle_low_stock_get_products_query( $query, $query_vars ) {
			if (
				! empty( $query_vars['low_stock_status'] )
			) {

				$ids = $this->get_low_stock_product_ids();

				// Restrict query to those IDs.
				$query['post__in'] = empty( $ids ) ? array( 0 ) : $ids;

				unset( $query_vars['stock_status'] );
			}

			return $query;
		}

		/**
		 * handle_with_stock_qty_get_products_query.
		 *
		 * @version 3.9.1
		 * @since   3.9.1
		 *
		 * @param $query
		 * @param $query_vars
		 *
		 * @return array
		 */
		function handle_with_stock_qty_get_products_query( $query, $query_vars ) {
			if ( ! empty( $query_vars['with_stock_qty'] ) ) {
				$query['meta_query'][] = array(
					'key'     => '_stock',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'NUMERIC'
				);
			}

			return $query;
		}

		/**
		 * @version 3.9.1
		 * @since   3.9.1
		 *
		 * @param $query
		 * @param $query_vars
		 *
		 * @return array
		 */
		function handle_not_empty_costs_get_products_query( $query, $query_vars ) {
			if ( ! empty( $query_vars['not_empty_costs'] ) ) {
				$query['meta_query'][] = array(
					'key'     => '_alg_wc_cog_cost',
					'value'   => 0,
					'compare' => '!=',
				);
				$query['meta_query'][] = array(
					'key'     => '_alg_wc_cog_cost',
					'value'   => '',
					'compare' => '!=',
				);
			}

			return $query;
		}

		/**
		 * @version 3.9.1
		 * @since   3.9.1
		 *
		 * @param $query
		 * @param $query_vars
		 *
		 * @return array
		 */
		function handle_not_empty_prices_get_products_query( $query, $query_vars ) {
			if ( ! empty( $query_vars['not_empty_prices'] ) ) {
				$query['meta_query'][] = array(
					'key'     => '_price',
					'value'   => 0,
					'compare' => '!=',
				);
				$query['meta_query'][] = array(
					'key'     => '_price',
					'value'   => '',
					'compare' => '!=',
				);
			}


			return $query;
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
		 * @version 3.9.1
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

			// Validate type param.
			$type = $args['type'];
			if (
				! empty( $type ) &&
				! in_array( $type, array( 'lowstock', 'instock', 'onbackorder', 'outofstock' ) )
			) {
				return $query_results;
			}

			// Validate alg_cog_stock_filter param.
			$alg_cog_stock_filter = $args['alg_cog_stock_filter'];
			if (
				! empty( $alg_cog_stock_filter ) &&
				! in_array( $alg_cog_stock_filter, array( 'cog_products' ) )
			) {
				return $query_results;
			}

			$transient_name = 'alg_wc_cog_scpt_' . md5( maybe_serialize( $args ) );
			if ( false === ( $total_cost_and_profit_info = get_transient( $transient_name ) ) ) {
				$total_cost_and_profit_info = $this->get_total_cost_and_profit_from_database( $args );
				set_transient( $transient_name, $total_cost_and_profit_info );
			}

			if ( ! empty( $total_cost_and_profit_info ) ) {
				$query_results['cost']       = $total_cost_and_profit_info['cost'];
				$query_results['total_cost'] = $total_cost_and_profit_info['cost_total'];
				$query_results['profit']       = $total_cost_and_profit_info['profit_ex_tax'];
				$query_results['total_profit'] = $total_cost_and_profit_info['profit_ex_tax_total'];
				if ( 'wc_get_price_including_tax' === alg_wc_cog_get_option( 'alg_wc_cog_products_get_price_method', 'wc_get_price_excluding_tax' ) ) {
					$query_results['profit']       = $total_cost_and_profit_info['profit_in_tax'];
					$query_results['total_profit'] = $total_cost_and_profit_info['profit_in_tax_total'];
				}

				// Extra data.
				if ( 'yes' === alg_wc_cog_get_option( 'alg_wc_cog_analytics_stock_extra_data', 'no' ) ) {
					$query_results['total_stock']    = $total_cost_and_profit_info['stock_total'];
					$query_results['total_products'] = $total_cost_and_profit_info['products_total'];
					$query_results['price']          = $total_cost_and_profit_info['price_ex_tax'];
					$query_results['total_price']    = $total_cost_and_profit_info['price_ex_tax_total'];
					$query_results['average_cost']   = $total_cost_and_profit_info['average_cost'];
					$query_results['average_price']  = $total_cost_and_profit_info['average_price_ex_tax'];
					if ( 'wc_get_price_including_tax' === alg_wc_cog_get_option( 'alg_wc_cog_products_get_price_method', 'wc_get_price_excluding_tax' ) ) {
						$query_results['price']         = $total_cost_and_profit_info['price_in_tax'];
						$query_results['total_price']   = $total_cost_and_profit_info['price_in_tax_total'];
						$query_results['average_price'] = $total_cost_and_profit_info['average_price_in_tax'];
					}
					$query_results['average_profit'] = $query_results['average_price'] - $query_results['average_cost'];
				}
			}

			return $query_results;
		}

		/**
		 * get_total_cost_and_profit_from_database.
		 *
		 * @version 3.9.1
		 * @since   3.2.1
		 *
		 * @param $args
		 *
		 * @return array
		 */
		function get_total_cost_and_profit_from_database( $args = array() ) {
			// Product types.
			$product_types      = get_terms( array(
				'taxonomy'   => 'product_type',
				'hide_empty' => false,
			) );
			$product_type_slugs = wp_list_pluck( $product_types, 'slug' );

			// Get product args.
			$wc_get_products_args = array(
				'limit'  => - 1,
				'type'   => $product_type_slugs,
				'return' => 'ids'
			);

			// Cost of Goods products.
			if ( 'cog_products' === $args['alg_cog_stock_filter'] ) {
				unset( $wc_get_products_args['stock_status'] );
				$wc_get_products_args['not_empty_prices'] = true;
				$wc_get_products_args['not_empty_costs']  = true;
				$wc_get_products_args['with_stock_qty']   = true;
			} else {
				// Stock status.
				if ( 'lowstock' !== $args['type'] ) {
					$wc_get_products_args['stock_status'] = $args['type'];
				} else {
					$wc_get_products_args['low_stock_status'] = true;
				}
			}

			// Get variation args.
			$wc_get_variation_args = array_merge( $wc_get_products_args, array(
				'type' => 'variation'
			) );

			// Get products combined (including variation and custom product types).
			$products = wc_get_products( $wc_get_products_args );
			$products = array_merge( $products, wc_get_products( $wc_get_variation_args ) );

			// Variables.
			$cost                   = 0;
			$cost_total             = 0;
			$price_in_tax           = 0;
			$price_in_tax_total     = 0;
			$price_ex_tax           = 0;
			$price_ex_tax_total     = 0;
			$profit_ex_tax          = 0;
			$profit_ex_tax_total    = 0;
			$profit_in_tax          = 0;
			$profit_in_tax_total    = 0;
			$stock_total            = 0;
			$stock_total_at_least_1 = 0;

			foreach ( $products as $product_id ) {
				// Product.
				$product = wc_get_product( $product_id );

				// Stock.
				$current_stock          = empty( $product->get_stock_quantity() ) ? 1 : $product->get_stock_quantity();
				$stock_total            += $product->get_stock_quantity();
				$stock_total_at_least_1 += $current_stock;

				// Cost.
				$cost_current       = (float) alg_wc_cog()->core->products->get_product_cost( $product->get_id() );
				$cost_current_total = $cost_current * $current_stock;
				$cost               += $cost_current;
				$cost_total         += $cost_current_total;

				// Price excluding taxes.
				$price_ex_total_current       = alg_wc_cog()->core->products->get_product_price( $product, array( 'method' => 'wc_get_price_excluding_tax' ) );
				$price_ex_total_current_total = $price_ex_total_current * $current_stock;
				$price_ex_tax                 += $price_ex_total_current;
				$price_ex_tax_total           += $price_ex_total_current_total;

				// Price including taxes.
				$price_in_tax_current       = alg_wc_cog()->core->products->get_product_price( $product, array( 'method' => 'wc_get_price_including_tax' ) );
				$price_in_tax_current_total = $price_in_tax_current * $current_stock;
				$price_in_tax               += $price_in_tax_current;
				$price_in_tax_total         += $price_in_tax_current_total;

				// Profit.
				$profit_ex_tax       += $price_ex_total_current - $cost_current;
				$profit_ex_tax_total += $price_ex_total_current_total - $cost_current_total;
				$profit_in_tax       += $price_in_tax_current - $cost_current;
				$profit_in_tax_total += $price_in_tax_current_total - $cost_current_total;
			}

			$average_cost         = ( 0 != $stock_total_at_least_1 ? ( $cost_total / $stock_total_at_least_1 ) : 0 );
			$average_price_in_tax = ( 0 != $stock_total_at_least_1 ? ( $price_in_tax_total / $stock_total_at_least_1 ) : 0 );
			$average_price_ex_tax = ( 0 != $stock_total_at_least_1 ? ( $price_ex_tax_total / $stock_total_at_least_1 ) : 0 );

			$results['cost']                 = $cost;
			$results['cost_total']           = $cost_total;
			$results['average_cost']         = $average_cost;
			$results['average_price_in_tax'] = $average_price_in_tax;
			$results['average_price_ex_tax'] = $average_price_ex_tax;
			$results['price_ex_tax']         = $price_ex_tax;
			$results['price_ex_tax_total']   = $price_ex_tax_total;
			$results['price_in_tax']         = $price_in_tax;
			$results['price_in_tax_total']   = $price_in_tax_total;
			$results['profit_ex_tax']        = $profit_ex_tax;
			$results['profit_ex_tax_total']  = $profit_ex_tax_total;
			$results['profit_in_tax']        = $profit_in_tax;
			$results['profit_in_tax_total']  = $profit_in_tax_total;
			$results['stock_total']          = $stock_total;
			$results['products_total']       = count( $products );

			return $results;
		}

		/**
		 * Get low stock count (products with stock < low stock amount, but greater than no stock amount).
		 *
		 * @version 3.2.1
		 * @since   3.2.1
		 *
		 * @see     Automattic\WooCommerce\Admin\API\Reports\Stock\Stats\DataStore::get_low_stock_count()
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
		 * get_only_cog_products_by_posts_clauses.
		 *
		 * @version 3.9.1
		 * @since   3.8.4
		 *
		 * @param $clauses
		 * @param $query
		 *
		 * @return mixed
		 */
		function get_only_cog_products_by_posts_clauses( $clauses, $query ) {
			if (
				empty( $post_type = $query->get( 'post_type' ) ) ||
				! is_array( $post_type ) ||
				empty( $intersect = array_intersect( $post_type, array( 'product', 'product_variation' ) ) ) ||
				count( $intersect ) != 2
			) {
				return $clauses;
			}

			if (
				(
					! $this->generating_download &&
					isset( $_GET['alg_cog_stock_filter'] ) &&
					'cog_products' === $_GET['alg_cog_stock_filter'] &&
					'yes' === alg_wc_cog_get_option( 'alg_wc_cog_filter_enabled_on_analytics_stock', 'no' )
				) ||
				(
					$this->generating_download &&
					'yes' === alg_wc_cog_get_option( 'alg_wc_cog_analytics_stock_cog_products_download', 'no' )
				)
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
		 * @see     \Automattic\WooCommerce\Admin\API\Reports\Stock\Controller::get_export_columns()
		 *
		 * @param $columns
		 *
		 * @return mixed
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
		 * @see     \Automattic\WooCommerce\Admin\API\Reports\Stock\Controller::prepare_item_for_export()
		 *
		 * @param $item
		 *
		 * @param $export_item
		 *
		 * @return mixed
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
		 * @see     \Automattic\WooCommerce\Admin\API\Reports\Stock\Controller::prepare_item_for_response()
		 *
		 * @param                     $product
		 *
		 * @param   WP_REST_Response  $response
		 *
		 * @return mixed
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
			$info['filter_enabled_on_stock']          = 'yes' === alg_wc_cog_get_option( 'alg_wc_cog_filter_enabled_on_analytics_stock', 'no' );
			$info['extra_data']                       = 'yes' === alg_wc_cog_get_option( 'alg_wc_cog_analytics_stock_extra_data', 'no' );

			return $info;
		}

		/**
		 * Get categories column export value.
		 *
		 * @version 2.4.5
		 * @since   2.4.5
		 *
		 * @param   array  $category_ids  Category IDs from report row.
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
