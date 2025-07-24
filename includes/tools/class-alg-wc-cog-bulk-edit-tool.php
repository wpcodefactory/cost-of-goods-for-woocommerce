<?php
/**
 * Cost of Goods for WooCommerce - Bulk Edit Tool Class.
 *
 * @version 3.7.8
 * @since   1.2.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Bulk_Edit_Tool' ) ) :

	class Alg_WC_Cost_of_Goods_Bulk_Edit_Tool {

		/**
		 * Tool costs page's slug.
		 *
		 * @var string
		 */
		private $page_slug_costs = 'bulk-edit-costs';

		/**
		 * Tool prices page's slug.
		 *
		 * @since 2.9.5
		 *
		 * @var string
		 */
		private $page_slug_prices = 'bulk-edit-prices';

		/**
		 * Update costs bkg process.
		 *
		 * @since 2.9.5
		 *
		 * @var Alg_WC_Cost_of_Goods_Update_Cost_Bkg_Process
		 */
		public $update_cost_bkg_process;

		/**
		 * Update prices bkg process.
		 *
		 * @since 2.9.5
		 *
		 * @var Alg_WC_Cost_of_Goods_Update_Price_Bkg_Process
		 */
		public $update_price_bkg_process;

		/**
		 * $wp_list_bulk_edit_tool.
		 *
		 * @since 3.2.9
		 *
		 * @var
		 */
		public $wp_list_bulk_edit_tool;

		/**
		 * $notices.
		 *
		 * @since 3.3.0
		 *
		 * @var array
		 */
		protected $notices = array();

		/**
		 * Constructor.
		 *
		 * @version 3.3.0
		 * @since   1.2.0
		 */
		function __construct() {
			// Bkg Process
			$this->init_bkg_process();
			add_action( 'admin_init', array( $this, 'save_costs' ) );
			add_filter( 'woocommerce_screen_ids', array( $this, 'add_tool_to_wc_screen_ids' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) );
			add_action( 'admin_menu', array( $this, 'create_wp_list_tool' ) );
			add_action( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3 );
			// Update costs.
			add_action( 'admin_init', array( $this, 'update_costs' ) );
			// Update prices.
			add_action( 'admin_init', array( $this, 'update_prices' ) );
			// Remove query args.
			add_action( 'admin_init', array( $this, 'remove_query_args' ) );
			// Json search tags.
			add_action( 'wp_ajax_' . 'alg_wc_cog_json_search_tags', array( $this, 'json_search_tags' ) );
			// Filter products by costs.
			add_filter( 'woocommerce_product_data_store_cpt_get_products_query', array( $this, 'filter_products_query_by_costs' ), 10, 2 );
			// Display notices
			add_action( 'alg_wc_cog_tools_after', array( $this, 'display_notices' ), 10 );
			// Disable screen options on Automatically tab.
			add_filter( 'screen_options_show_screen', array( $this, 'disable_screen_option_on_automatically_tab' ), 10, 2 );
		}

		/**
		 * disable_screen_option_on_automatically_tab.
		 *
		 * @version 3.3.0
		 * @since   3.3.0
		 *
		 * @param $show
		 * @param $screen
		 *
		 * @return false|mixed
		 */
		function disable_screen_option_on_automatically_tab( $show, $screen ) {
			if (
				isset( $_GET['tab'] ) &&
				'costs_automatically' === $_GET['tab'] &&
				isset( $_GET['page'] ) &&
				'bulk-edit-costs' === $_GET['page']
			) {
				$show = false;
			}

			return $show;
		}

		/**
		 * add_notice.
		 *
		 * @version 3.3.0
		 * @since   3.3.0
		 *
		 * @param $notice_type
		 * @param $notice_html
		 * @param $html_template
		 *
		 * @return void
		 */
		function add_notice( $notice_type, $notice_html, $html_template = '<p>{content}</p>' ) {
			$this->notices[] = array(
				'type'             => $notice_type,
				'content'          => $notice_html,
				'content_template' => $html_template
			);
		}

		/**
		 * display_notices.
		 *
		 * @version 3.3.0
		 * @since   3.3.0
		 *
		 * @return void
		 */
		function display_notices() {
			foreach ( $this->notices as $notice_data ) {
				$class         = "notice notice-{$notice_data['type']}";
				$array_from_to = array(
					'{content}' => '%2$s',
				);
				$content       = str_replace( array_keys( $array_from_to ), $array_from_to, $notice_data['content_template'] );
				printf( '<div class="%1$s">' . $content . '</div>', esc_attr( $class ), wp_kses_post( $notice_data['content'] ) );
			}
		}

		/**
		 * handle_costs_filter_query.
		 *
		 * @version 3.3.0
		 * @since   3.3.0
		 *
		 * @param $query
		 * @param $costs_filter
		 *
		 * @return array
		 */
		function handle_costs_filter_query( $query, $costs_filter ) {
			switch ( $costs_filter ) {
				case 'products_without_costs':
					$query['meta_query'][] = array(
						'relation' => 'OR',
						array(
							'key'     => '_alg_wc_cog_cost',
							'value'   => 0,
							'compare' => '==',
						),
						array(
							'key'     => '_alg_wc_cog_cost',
							'value'   => '',
							'compare' => '==',
						),
						array(
							'key'     => '_alg_wc_cog_cost',
							'compare' => 'NOT EXISTS',
						),
					);
					break;
				case 'products_with_costs':
					$query['meta_query'][] = array(
						'relation' => 'OR',
						array(
							'key'     => '_alg_wc_cog_cost',
							'value'   => 0,
							'compare' => '!=',
						),
						array(
							'key'     => '_alg_wc_cog_cost',
							'value'   => '',
							'compare' => '!=',
						),
						array(
							'key'     => '_alg_wc_cog_cost',
							'compare' => 'EXISTS',
						),
					);
					break;
			}

			return $query;
		}

		/**
		 * handle_stock_status_filter_query.
		 *
		 * @version 3.4.0
		 * @since   3.4.0
		 *
		 * @param $query
		 * @param $stock_status
		 *
		 * @return array
		 */
		function handle_stock_status_filter_query( $query, $stock_status = '' ) {
			if ( ! empty( $stock_status ) ) {
				$query['meta_query'][] = array(
					'relation' => 'OR',
					array(
						'key'   => '_stock_status',
						'value' => $stock_status
					),
				);
			}

			return $query;
		}

		/**
		 * filter_products_query_by_costs.
		 *
		 * @version 3.3.0
		 * @since   3.3.0
		 *
		 * @param $query
		 * @param $query_vars
		 *
		 * @return array
		 */
		function filter_products_query_by_costs( $query, $query_vars ) {
			if ( isset( $query_vars['alg_wc_cog_costs_filter'] ) && ! empty( $query_vars['alg_wc_cog_costs_filter'] ) ) {
				$query = $this->handle_costs_filter_query( $query, $query_vars['alg_wc_cog_costs_filter'] );
			}

			return $query;
		}

		/**
		 * json_search_tags.
		 *
		 * @see WC_AJAX::json_search_categories()
		 *
		 * @version 2.7.3
		 * @since   2.7.3
		 */
		function json_search_tags() {
			ob_start();
			check_ajax_referer( 'search-categories', 'security' );
			if ( ! current_user_can( 'edit_products' ) ) {
				wp_die( - 1 );
			}
			$search_text = isset( $_GET['term'] ) ? wc_clean( wp_unslash( $_GET['term'] ) ) : '';
			if ( ! $search_text ) {
				wp_die();
			}
			$found_categories = array();
			$args             = array(
				'taxonomy'   => array( 'product_tag' ),
				'orderby'    => 'id',
				'order'      => 'ASC',
				'hide_empty' => true,
				'fields'     => 'all',
				'name__like' => $search_text,
			);
			$terms            = get_terms( $args );
			if ( $terms ) {
				foreach ( $terms as $term ) {
					$term->formatted_name               = '';
					$term->formatted_name               .= $term->name . ' (' . $term->count . ')';
					$found_categories[ $term->term_id ] = $term;
				}
			}
			wp_send_json( apply_filters( 'alg_wc_cog_json_search_found_tags', $found_categories ) );
		}

		/**
		 * remove_query_args.
		 *
		 * @version 2.7.1
		 * @since   2.7.1
		 *
		 */
		function remove_query_args() {
			if (
				isset( $_GET['page'] ) &&
				'bulk-edit-costs' === $_GET['page'] &&
				! empty( $_GET['_wp_http_referer'] )
			) {
				wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
			}
		}

		/**
		 * init_bkg_process.
		 *
		 * @version 3.3.0
		 * @since   2.5.1
		 */
		function init_bkg_process() {
			require_once( alg_wc_cog()->plugin_path() . '/includes/background-process/class-alg-wc-cog-update-cost-bkg-process.php' );
			require_once( alg_wc_cog()->plugin_path() . '/includes/background-process/class-alg-wc-cog-update-price-bkg-process.php' );
			$this->update_cost_bkg_process  = new Alg_WC_Cost_of_Goods_Update_Cost_Bkg_Process();
			$this->update_price_bkg_process = new Alg_WC_Cost_of_Goods_Update_Price_Bkg_Process();
		}

		/**
		 * get_products_to_be_updated.
		 *
		 * @version 3.4.0
		 * @since   3.3.0
		 *
		 * @param $args
		 *
		 * @return array|stdClass
		 */
		function get_products_to_be_updated( $args ) {
			$args = wp_parse_args( $args, array(
				'product_category' => array(),
				'product_tag'      => array(),
				'cost_filter'      => '',
				'update_method'    => '',
				'stock_status'     => ''
			) );
			$product_categories = $args['product_category'];
			$update_method = $args['update_method'];
			$product_tags = $args['product_tag'];
			$cost_filter = $args['cost_filter'];
			$stock_status_filter = $args['stock_status'];

			// Product args.
			$products_args            = array(
				'type'   => array_merge( array_keys( wc_get_product_types() ) ),
				'status' => array( 'publish' ),
				'limit'  => '-1',
				'return' => 'ids',
			);
			$products_args            = $this->handle_category_filter_wc_get_products_args( $product_categories, $products_args );
			$products_args            = $this->handle_tags_filter_wc_get_products_args( $product_tags, $products_args );
			$products_from_taxes_args = $products_args;
			if ( ! empty( $cost_filter ) ) {
				$products_args = $this->handle_costs_filter_wc_get_products_args( $cost_filter, $products_args );
			}
			$products_args['stock_status'] = $stock_status_filter;

			// Child products query args.
			$child_products_query_args = array(
				'post_type'      => array( 'product_variation' ),
				'posts_per_page' => - 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,

			);
			if ( ! empty( $cost_filter ) ) {
				$child_products_query_args = $this->handle_costs_filter_query( $child_products_query_args, $cost_filter );
			}
			if ( ! empty( $stock_status_filter ) ) {
				$child_products_query_args = $this->handle_stock_status_filter_query( $child_products_query_args, $stock_status_filter );
			}

			// Get products.
			$products_from_taxes = empty( $product_tags ) && empty( $product_categories ) ? array() : wc_get_products( $products_from_taxes_args );
			$products            = wc_get_products( $products_args );

			switch ( $update_method ) {
				case 'set_variation_costs_from_parents':
					$child_products_query_args['post_parent__in']     = $products_from_taxes;
					$child_products_query_args['post_parent__not_in'] = array( 0 );
					$child_products                                   = new WP_Query( $child_products_query_args );
					if ( $child_products->have_posts() ) {
						$products = $child_products->posts;
					} else {
						$products = array();
					}
					break;
				default:
					$child_products_query_args['post_parent__in'] = $products_from_taxes;
					$child_products                               = new WP_Query( $child_products_query_args );
					if ( $child_products->have_posts() ) {
						$products = array_merge( $products, $child_products->posts );
					}
					break;
			}

			return $products;
		}

		/**
		 * update_costs.
		 *
		 * @version 3.4.0
		 * @since   3.3.0
		 *
		 * @return void
		 */
		function update_costs() {
			$args = wp_parse_args( $_POST, array(
				'bulk_edit_costs_method'         => 'edit_costs_by_price_percentage',
				'cost_edit_value'                => '',
				'costs_filter'                   => '',
				'_nonce_costs_automatically_val' => '',
				'product_category'               => array(),
				'product_tag'                    => array(),
				'product_stock_status'           => '',
			) );

			if (
				! isset( $_POST['_nonce_costs_automatically_val'] ) ||
				! wp_verify_nonce( $args["_nonce_costs_automatically_val"], "_nonce_costs_automatically_action" ) ||
				! in_array( $bulk_edit_costs_method = sanitize_text_field( $args['bulk_edit_costs_method'] ), wp_list_pluck( $this->get_bulk_edit_costs_methods(), 'id' ) )
			) {
				return;
			}

			// Sanitize args.
			$cost_edit_value     = floatval( $args['cost_edit_value'] );
			$product_categories  = empty( $args['product_category'] ) ? array() : array_map( 'intval', $args['product_category'] );
			$product_tags        = empty( $args['product_tag'] ) ? array() : array_map( 'intval', $args['product_tag'] );
			$cost_filter         = in_array( $filter = sanitize_text_field( $args['costs_filter'] ), wp_list_pluck( $this->get_cost_filter_options(), 'id' ) ) ? $filter : '';
			$stock_status_filter = sanitize_text_field( $args['product_stock_status'] );

			// Get products to be updated.
			$products = $this->get_products_to_be_updated( array(
				'product_category'    => $product_categories,
				'product_tag'         => $product_tags,
				'cost_filter'         => $cost_filter,
				'update_method'       => $bulk_edit_costs_method,
				'stock_status'        => $stock_status_filter
			) );

			// Check if background process is needed.
			$bkg_process_min_amount = get_option( 'alg_wc_cog_bkg_process_min_amount', 100 );
			$need_bkg_process       = count( $products ) >= $bkg_process_min_amount;

			// Notices.
			$this->handle_notices_from_update( array(
				'products'         => $products,
				'need_bkg_process' => $need_bkg_process,
				'dynamic_word'     => __( 'cost', 'cost-of-goods-for-woocommerce' )
			) );

			// Default bulk update params.
			$bulk_update_params = array(
				'products'         => $products,
				'need_bkg_process' => $need_bkg_process,
				'bkg_process_obj'  => $this->update_cost_bkg_process,
			);

			// Bulk edit products.
			switch ( $bulk_edit_costs_method ) {
				case 'edit_costs_by_price_percentage':
					$this->bulk_update_products( array_merge( $bulk_update_params, array(
						'products_function' => 'update_product_cost_by_price_percentage',
						'function_params'   => array(
							'percentage' => $cost_edit_value
						)
					) ) );
					break;
				case 'edit_costs_by_profit_percentage':
					$this->bulk_update_products( array_merge( $bulk_update_params, array(
						'products_function' => 'update_product_cost_by_profit_percentage',
						'function_params'   => array(
							'percentage' => $cost_edit_value
						)
					) ) );
					break;
				case 'set_variation_costs_from_parents':
					$this->bulk_update_products( array_merge( $bulk_update_params, array(
						'products_function' => 'update_variation_cost_from_parent',
					) ) );
					break;
				case 'increase_costs_by_percentage':
					$this->bulk_update_products( array_merge( $bulk_update_params, array(
						'products_function' => 'increase_product_cost_by_percentage',
						'function_params'   => array(
							'percentage' => $cost_edit_value
						)
					) ) );
					break;
				case 'decrease_costs_by_percentage':
					$this->bulk_update_products( array_merge( $bulk_update_params, array(
						'products_function' => 'decrease_product_cost_by_percentage',
						'function_params'   => array(
							'percentage' => $cost_edit_value
						)
					) ) );
					break;
			}
		}

		/**
		 * update_prices.
		 *
		 * @version 3.4.0
		 * @since   3.3.0
		 *
		 * @return void
		 */
		function update_prices() {
			$args = wp_parse_args( $_POST, array(
				'bulk_edit_prices_method'         => 'edit_prices_by_profit_percentage',
				'price_edit_value'                => '',
				'price_type'                      => '',
				'price_rounding'                  => '',
				'_nonce_prices_automatically_val' => '',
				'product_category'                => array(),
				'product_tag'                     => array(),
				'product_stock_status'            => '',
			) );

			if (
				! isset( $_POST['_nonce_prices_automatically_val'] ) ||
				! wp_verify_nonce( $args["_nonce_prices_automatically_val"], "_nonce_prices_automatically_action" ) ||
				! in_array( $bulk_edit_prices_method = sanitize_text_field( $args['bulk_edit_prices_method'] ), wp_list_pluck( $this->get_bulk_edit_prices_methods(), 'id' ) )
			) {
				return;
			}

			// Sanitize args.
			$price_edit_value    = floatval( $args['price_edit_value'] );
			$product_categories  = empty( $args['product_category'] ) ? array() : array_map( 'intval', $args['product_category'] );
			$product_tags        = empty( $args['product_tag'] ) ? array() : array_map( 'intval', $args['product_tag'] );
			$price_type          = sanitize_text_field( $args['price_type'] );
			$price_rounding      = sanitize_text_field( $args['price_rounding'] );
			$stock_status_filter = sanitize_text_field( $args['product_stock_status'] );

			// Get products to be updated.
			$products = $this->get_products_to_be_updated( array(
				'product_category' => $product_categories,
				'product_tag'      => $product_tags,
				'update_method'    => $bulk_edit_prices_method,
				'stock_status'     => $stock_status_filter
			) );

			// Check if background process is needed.
			$bkg_process_min_amount = get_option( 'alg_wc_cog_bkg_process_min_amount', 100 );
			$need_bkg_process       = count( $products ) >= $bkg_process_min_amount;

			// Notices.
			$this->handle_notices_from_update( array(
				'products'         => $products,
				'need_bkg_process' => $need_bkg_process,
				'dynamic_word'     => 'price'
			) );

			// Default bulk update params.
			$bulk_update_params = array(
				'products'         => $products,
				'need_bkg_process' => $need_bkg_process,
				'bkg_process_obj'  => $this->update_price_bkg_process,
			);

			// Bulk update products.
			switch ( $bulk_edit_prices_method ) {
				case 'edit_prices_by_profit_percentage':
					$this->bulk_update_products( array_merge( $bulk_update_params, array(
						'products_function' => 'update_product_price_by_profit',
						'function_params'   => array(
							'percentage' => $price_edit_value,
							'rounding'   => $price_rounding,
							'price_type' => $price_type,
						)
					) ) );
					break;
				case 'edit_prices_by_absolute_profit':
					$this->bulk_update_products( array_merge( $bulk_update_params, array(
						'products_function' => 'update_product_price_by_profit',
						'function_params'   => array(
							'absolute_profit' => $price_edit_value,
							'rounding'        => $price_rounding,
							'price_type'      => $price_type,
						)
					) ) );
					break;
			}
		}

		/**
		 * handle_notices_from_update.
		 *
		 * @version 3.3.0
		 * @since   3.3.0
		 *
		 * @param $args
		 *
		 * @return void
		 */
		function handle_notices_from_update( $args = null ) {
			$args             = wp_parse_args( $args, array(
				'products'         => '',
				'need_bkg_process' => false,
				'dynamic_word'     => 'cost'
			) );
			$products         = $args['products'];
			$need_bkg_process = $args['need_bkg_process'];
			$dynamic_word     = $args['dynamic_word'];

			if ( ! empty( $products ) ) {
				if ( $need_bkg_process ) {
					$notice_msg = sprintf( __( 'The %s of %d products are being updated via background processing.', 'cost-of-goods-for-woocommerce' ), $dynamic_word, count( $products ) );
					$notice_msg .= 'yes' === get_option( 'alg_wc_cog_bkg_process_send_email', 'yes' ) ? ' ' . sprintf( __( 'An email is going to be sent to %s when the task is completed.', 'cost-of-goods-for-woocommerce' ), '<code>' . get_option( 'alg_wc_cog_bkg_process_email_to', get_option( 'admin_email' ) ) . '</code>' ) : '';
				} else {
					$notice_msg = sprintf( __( 'Successfully updated the %s of %d products.', 'cost-of-goods-for-woocommerce' ), $dynamic_word, count( $products ) );
				}
				$this->add_notice( 'success', $notice_msg );
			} else {
				$notice_msg = __( 'There are no products to be updated.', 'cost-of-goods-for-woocommerce' );
				$this->add_notice( 'warning', $notice_msg );
			}
		}

		/**
		 * handle_costs_filter_query_args.
		 *
		 * @version 3.3.0
		 * @since   3.3.0
		 *
		 * @param $costs_filter
		 * @param $query_args
		 *
		 * @return array|mixed
		 */
		function handle_costs_filter_wc_get_products_args( $costs_filter, $query_args = array() ) {
			if ( ! empty( $costs_filter ) ) {
				$query_args['alg_wc_cog_costs_filter'] = $costs_filter;
			}

			return $query_args;
		}

		/**
		 * handle_category_filter_query_args.
		 *
		 * @version 3.3.0
		 * @since   3.3.0
		 *
		 * @param $product_categories
		 * @param $query_args
		 *
		 * @return array|mixed
		 */
		function handle_category_filter_wc_get_products_args( $product_categories, $query_args = array() ) {
			if ( ! empty( $product_categories ) && is_array( $product_categories ) ) {
				$query_args['product_category_id'] = isset( $query_args['product_category_id'] ) ? array_merge( $query_args['product_category_id'], $product_categories ) : $product_categories;
			}

			return $query_args;
		}

		/**
		 * handle_tags_filter_query_args.
		 *
		 * @version 3.3.0
		 * @since   3.3.0
		 *
		 * @param $product_tags
		 * @param $query_args
		 *
		 * @return array|mixed
		 */
		function handle_tags_filter_wc_get_products_args( $product_tags, $query_args = array() ) {
			if ( ! empty( $product_tags ) && is_array( $product_tags ) ) {
				$query_args['product_tag_id'] = isset( $query_args['product_tag_id'] ) ? array_merge( $query_args['product_tag_id'], $product_tags ) : $product_tags;
			}

			return $query_args;
		}

		/**
		 * bulk_update_products.
		 *
		 * @version 3.3.0
		 * @since   2.9.5
		 *
		 * @param $args
		 *
		 * @return void
		 */
		function bulk_update_products( $args = array() ) {
			$args              = wp_parse_args( $args, array(
				'products'          => array(),
				'function_params'   => array(),
				'need_bkg_process'  => false,
				'bkg_process_obj'   => null,
				'products_function' => '',
			) );
			$function_params   = $args['function_params'];
			$products_function = $args['products_function'];
			$products          = $args['products'];
			$need_bkg_process  = $args['need_bkg_process'];
			$bkg_process_obj   = $args['bkg_process_obj'];
			if ( $need_bkg_process ) {
				call_user_func( array( $bkg_process_obj, "cancel_process" ) );
			}
			foreach ( $products as $product_id ) {
				$function_params['product_id'] = $product_id;
				if ( $need_bkg_process ) {
					$function_params['products_function'] = $products_function;
					call_user_func_array( array( $bkg_process_obj, "push_to_queue" ),
						array(
							$function_params
						)
					);
				} else {
					call_user_func_array( array( alg_wc_cog()->core->products, $products_function ), array( $function_params ) );
				}
			}
			if ( $need_bkg_process ) {
				call_user_func( array( $bkg_process_obj, "save" ) );
				call_user_func( array( $bkg_process_obj, "dispatch" ) );
			}
		}

		/**
		 * get_bulk_edit_costs_methods.
		 *
		 * @version 3.3.0
		 * @since   3.3.0
		 *
		 * @return array[]
		 */
		function get_bulk_edit_costs_methods() {
			return array(
				array(
					'id'    => 'edit_costs_by_price_percentage',
					'label' => __( 'Edit costs by price percentage', 'cost-of-goods-for-woocommerce' ),
					'desc'  => __( 'Product costs will be defined from a percentage of product prices.', 'cost-of-goods-for-woocommerce' ),
				),
				array(
					'id'    => 'edit_costs_by_profit_percentage',
					'label' => __( 'Edit costs by profit percentage', 'cost-of-goods-for-woocommerce' ),
					'desc'  => __( 'Products costs will be set according to the profit percentage you\'d like to achieve based on the current product prices.', 'cost-of-goods-for-woocommerce' ),
				),
				array(
					'id'    => 'increase_costs_by_percentage',
					'label' => __( 'Increase costs by percentage', 'cost-of-goods-for-woocommerce' ),
					'desc'  => sprintf( __( 'Product costs will be increased by %s.', 'cost-of-goods-for-woocommerce' ), '<code>x%</code>' ),
				),
				array(
					'id'    => 'decrease_costs_by_percentage',
					'label' => __( 'Decrease costs by percentage', 'cost-of-goods-for-woocommerce' ),
					'desc'  => sprintf( __( 'Product costs will be decreased by %s.', 'cost-of-goods-for-woocommerce' ), '<code>x%</code>' ),
				),
				array(
					'id'    => 'set_variation_costs_from_parents',
					'label' => __( 'Set variation costs from parents', 'cost-of-goods-for-woocommerce' ),
					'desc'  => __( 'Update variations to match the cost value of their parent products.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					           sprintf(
						           __( 'Example: If a variable product is set with a cost of %s, all its variations will also cost %s.', 'cost-of-goods-for-woocommerce' ),
						           '<code>' . wc_price( '150' ) . '</code>',
						           '<code>' . wc_price( '150' ) . '</code>'
					           ),
				),
			);
		}

		/**
		 * get_bulk_edit_prices_methods.
		 *
		 * @version 3.3.0
		 * @since   3.3.0
		 *
		 * @return array[]
		 */
		function get_bulk_edit_prices_methods() {
			return array(
				array(
					'id'    => 'edit_prices_by_profit_percentage',
					'label' => __( 'Edit prices by profit percentage', 'cost-of-goods-for-woocommerce' ),
					'desc'  => __( 'Products prices will be set according to the profit percentage you\'d like to achieve based on the current product costs.', 'cost-of-goods-for-woocommerce' ),
				),
				array(
					'id'    => 'edit_prices_by_absolute_profit',
					'label' => __( 'Edit prices by absolute profit', 'cost-of-goods-for-woocommerce' ),
					'desc'  => __( 'Products prices will be set according to the absolute profit you\'d like to achieve based on the current product costs.', 'cost-of-goods-for-woocommerce' ),
				),
			);
		}

		/**
		 * get_cost_filter_options.
		 *
		 * @version 3.3.0
		 * @since   3.3.0
		 *
		 * @return array[]
		 */
		function get_cost_filter_options() {
			return array(
				array(
					'id'    => 'ignore_costs',
					'label' => __( 'Ignore current cost', 'cost-of-goods-for-woocommerce' ),
					'desc'  => __( '', 'cost-of-goods-for-woocommerce' ),
				),
				array(
					'id'    => 'products_without_costs',
					'label' => __( 'Only update products with no costs set, including zero or empty', 'cost-of-goods-for-woocommerce' ),
					'desc'  => __( '', 'cost-of-goods-for-woocommerce' ),
				),
				array(
					'id'    => 'products_with_costs',
					'label' => __( 'Only update products with costs already set', 'cost-of-goods-for-woocommerce' ),
					'desc'  => __( '', 'cost-of-goods-for-woocommerce' ),
				),
			);
		}

		/**
		 * display_bulk_edit_prices.
		 *
		 * @version 3.4.0
		 * @since   2.6.1
		 */
		function display_bulk_edit_prices() {
			$disabled     = apply_filters( 'alg_wc_cog_settings', 'disabled' );
			$blocked_text = apply_filters( 'alg_wc_cog_settings', alg_wc_cog_get_blocked_options_message() );
			$methods = $this->get_bulk_edit_prices_methods();
			$stock_statuses = wc_get_product_stock_status_options();
			?>
            <table class="form-table bulk-edit-auto" role="presentation">
                <tr>
                    <th scope="row"><label for="bulk_edit_prices_method"><?php esc_html_e( 'Update method', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <select data-dropdown_with_desc="true" data-desc_target=".bulk-edit-prices-method-desc" class="wc-enhanced-select" data-return_id="id" id="bulk_edit_prices_method" name="bulk_edit_prices_method">
				            <?php foreach ( $methods as $method ): ?>
                                <option data-desc="<?php echo esc_attr( $method['desc'] ) ?>"
                                        value="<?php echo esc_attr( $method['id'] ) ?>"><?php echo esc_html( $method['label'] ) ?>
                                </option>
				            <?php endforeach; ?>
                        </select>
                        <p class="bulk-edit-prices-method-desc description hidden dropdown-description" style="margin-top:7px">
                        </p>
                    </td>
                </tr>
                <tr data-depends_on='{"sourceSelector":"#bulk_edit_prices_method","optionSelected":"edit_prices_by_profit_percentage"}'>
                    <th scope="row"><label for="profit_percentage"><?php esc_html_e( 'Profit percentage (%)', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <input id="profit_percentage" name="price_edit_value" step="0.01" min="0" type="number" placeholder="">
                        <p class="description">
	                        <?php echo wp_kses_post( sprintf(
		                        __( 'Example: If set as %s, a product costing %s will have its price changed to %s, resulting in a %s profit percentage.', 'cost-of-goods-for-woocommerce' ),
		                        '<code>10</code>',
		                        '<code>' . wc_price( 50 ) . '</code>',
		                        '<code>' . wc_price( 55 ) . '</code>',
		                        '<code>10%</code>'
	                        ) );
                            ?>
                        </p>
                    </td>
                </tr>
                <tr data-depends_on='{"sourceSelector":"#bulk_edit_prices_method","optionSelected":"edit_prices_by_absolute_profit"}'>
                    <th scope="row"><label for="absolute_profit"><?php esc_html_e( 'Absolute profit', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <input id="absolute_profit" name="price_edit_value" step="0.01" min="0" type="number" placeholder="">
                        <p class="description">
                            <?php echo wp_kses_post( sprintf(
		                        __( 'Example: If set as %s, a product costing %s will have its price changed to %s, resulting in an absolute profit of %s.', 'cost-of-goods-for-woocommerce' ),
		                        '<code>10</code>',
		                        '<code>' . wc_price( 50 ) . '</code>',
		                        '<code>' . wc_price( 60 ) . '</code>',
								'<code>' . wc_price( 10 ) . '</code>'
	                        ) ); ?>

                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="affected-field"><?php esc_html_e( 'Price type', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <select class="wc-enhanced-select" id="affected-field" name="price_type">
                            <option value="regular_price"><?php esc_html_e( 'Regular Price', 'cost-of-goods-for-woocommerce' ); ?></option>
                            <option value="sale_price"><?php esc_html_e( 'Sale Price', 'cost-of-goods-for-woocommerce' ); ?></option>
                        </select>
                        <p class="description dropdown-description">
	                        <?php
	                        esc_html_e( 'Type of price affected by the update.', 'cost-of-goods-for-woocommerce' );
	                        echo ' ';
	                        esc_html_e( 'Note: If the update results in a regular price lower than the sale price, the product won\'t have its price changed.', 'cost-of-goods-for-woocommerce' );
	                        ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="price_rounding"><?php esc_html_e( 'Rounding', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <select class="wc-enhanced-select" id="price_rounding" name="price_rounding">
                            <option value=""><?php esc_html_e( 'Do not round', 'cost-of-goods-for-woocommerce' ); ?></option>
                            <option value="round"><?php esc_html_e( 'Round', 'cost-of-goods-for-woocommerce' ); ?></option>
                            <option value="round_up"><?php esc_html_e( 'Round up', 'cost-of-goods-for-woocommerce' ); ?></option>
                            <option value="round_down"><?php esc_html_e( 'Round down', 'cost-of-goods-for-woocommerce' ); ?></option>
                        </select>
                        <p class="description dropdown-description">
		                    <?php esc_html_e( 'Price rounding after the calculation.', 'cost-of-goods-for-woocommerce' ); ?>
                        </p>
                    </td>
                </tr>
            </table>

            <h2><?php esc_html_e( 'Filters', 'cost-of-goods-for-woocommerce' ); ?></h2>

            <table class="form-table bulk-edit-auto" role="presentation">
                <tr>
                    <th scope="row"><label for="product-category"><?php esc_html_e( 'Filter by category', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <select data-return_id="id" <?php echo esc_attr( $disabled ); ?> class="wc-category-search" multiple="multiple" style="width: 50%;" id="product-category" name="product_category[]" data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'cost-of-goods-for-woocommerce' ); ?>" data-action="json_search_categories">
                        </select>
                        <p class="description">
							<?php esc_html_e( 'Select only the categories you want to edit. Leave it empty to update all products.', 'cost-of-goods-for-woocommerce' ); ?>
							<?php echo ( ! empty( $blocked_text ) ) ? '<br />' . $blocked_text : ''; ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="product-tag"><?php esc_html_e( 'Filter by tag(s)', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <select data-return_id="id" <?php echo esc_attr( $disabled ); ?> class="wc-taxonomy-term-search" multiple="multiple" style="width: 50%;" id="product-tag" name="product_tag[]" data-placeholder="<?php esc_attr_e( 'Search for a tag&hellip;', 'cost-of-goods-for-woocommerce' ); ?>" data-taxonomy="product_tag">
                        </select>
                        <p class="description">
							<?php esc_html_e( 'Select only the tag(s) you want to edit. Leave it empty to update all products.', 'cost-of-goods-for-woocommerce' ); ?>
							<?php echo ( ! empty( $blocked_text ) ) ? '<br />' . $blocked_text : ''; ?>
                        </p>
                    </td>
                </tr>
				<tr>
					<th scope="row"><label for="product-stock-status"><?php esc_html_e( 'Filter by stock status', 'cost-of-goods-for-woocommerce' ); ?></label></th>
					<td>
						<select class="wc-enhanced-select" id="product-stock-status" name="product_stock_status">
							<option value=""><?php echo esc_html__( 'Ignore stock status', 'woocommerce' ) ?></option>
							<?php foreach ( $stock_statuses as $status => $label ) : ?>
								<option value="<?php echo esc_attr( $status ) ?> "><?php echo esc_html( $label ) ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
            </table>
			<?php
		}

		/**
		 * Display content for Bulk edit costs automatically.
		 *
		 * @version 3.4.0
		 * @since   2.5.1
		 */
		function display_bulk_edit_costs_automatically() {
			$disabled     = apply_filters( 'alg_wc_cog_settings', 'disabled' );
			$blocked_text = apply_filters( 'alg_wc_cog_settings', alg_wc_cog_get_blocked_options_message() );
			$methods = $this->get_bulk_edit_costs_methods();
            $cost_filter_options = $this->get_cost_filter_options();
			$stock_statuses = wc_get_product_stock_status_options();
			?>
            <table class="form-table bulk-edit-auto" role="presentation">
                <tr>
                    <th scope="row"><label for="bulk_edit_costs_method"><?php esc_html_e( 'Update method', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <select data-dropdown_with_desc="true" data-desc_target=".bulk-edit-costs-method-desc" class="wc-enhanced-select" data-return_id="id" id="bulk_edit_costs_method" name="bulk_edit_costs_method">
	                        <?php foreach ( $methods as $method ): ?>
                                <option data-desc="<?php echo esc_attr( $method['desc'] ) ?>"
                                        value="<?php echo esc_attr( $method['id'] ) ?>"><?php echo esc_html( $method['label'] ) ?>
                                </option>
	                        <?php endforeach; ?>
                        </select>
                        <p class="bulk-edit-costs-method-desc description hidden dropdown-description">
                        </p>
                    </td>
                </tr>
                <tr data-depends_on='{"sourceSelector":"#bulk_edit_costs_method","optionSelected":"edit_costs_by_price_percentage"}'>
                    <th scope="row"><label for="price_percentage"><?php esc_html_e( 'Price percentage (%)', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <input style="" id="price_percentage" name="cost_edit_value" step="0.01" type="number" required placeholder="">
                        <p class="description">
	                        <?php esc_html_e( 'Most probably you should use a number between 0 and 100.', 'cost-of-goods-for-woocommerce' ); ?>
	                        <?php echo wp_kses_post( sprintf(
		                        __( 'Example: If set as %s, a product priced at %s will have its cost set to %s, representing a %s margin based on the product\'s price.', 'cost-of-goods-for-woocommerce' ),
		                        '<code>' . '10' . '</code>',
		                        '<code>' . wc_price( '150' ) . '</code>',
		                        '<code>' . wc_price( '15' ) . '</code>',
		                        '<code>' . '10%' . '</code>'
	                        ) ); ?>
                        </p>
                    </td>
                </tr>
                <tr data-depends_on='{"sourceSelector":"#bulk_edit_costs_method","optionSelected":"edit_costs_by_profit_percentage"}'>
                    <th scope="row"><label for="profit_percentage"><?php esc_html_e( 'Profit percentage (%)', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <input style="" id="profit_percentage" name="cost_edit_value" step="0.01" type="number" required placeholder="">
                        <p class="description">
	                        <?php echo wp_kses_post( sprintf(
		                        __( 'Example: If set as %s, a product priced at %s will have its cost set to %s, resulting in a %s profit percentage.', 'cost-of-goods-for-woocommerce' ),
		                        '<code>' . '10' . '</code>',
		                        '<code>' . wc_price( '150' ) . '</code>',
		                        '<code>' . wc_price( '136.36' ) . '</code>',
		                        '<code>' . '10%' . '</code>'
	                        ) ); ?>
                        </p>
                    </td>
                </tr>
                <tr data-depends_on='{"sourceSelector":"#bulk_edit_costs_method","optionSelected":"increase_costs_by_percentage"}'>
                    <th scope="row"><label for="percentage_increase"><?php esc_html_e( 'Percentage increase (%)', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <input style="" id="percentage_increase" name="cost_edit_value" step="0.01" type="number" required placeholder="">
                        <p class="description">
				            <?php echo wp_kses_post( sprintf(
					            __( 'Example: If set as %s, a product costing %s will have its cost set to %s, resulting in a %s cost percentage increase.', 'cost-of-goods-for-woocommerce' ),
					            '<code>' . '10' . '</code>',
					            '<code>' . wc_price( '150' ) . '</code>',
					            '<code>' . wc_price( '165' ) . '</code>',
					            '<code>' . '10%' . '</code>'
				            ) ); ?>
                        </p>
                    </td>
                </tr>
                <tr data-depends_on='{"sourceSelector":"#bulk_edit_costs_method","optionSelected":"decrease_costs_by_percentage"}'>
                    <th scope="row"><label for="percentage_decrease"><?php esc_html_e( 'Percentage decrease (%)', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <input style="" id="percentage_decrease" name="cost_edit_value" step="0.01" type="number" required placeholder="">
                        <p class="description">
				            <?php echo wp_kses_post( sprintf(
					            __( 'Example: If set as %s, a product costing %s will have its cost set to %s, resulting in a %s cost percentage decrease.', 'cost-of-goods-for-woocommerce' ),
					            '<code>' . '10' . '</code>',
					            '<code>' . wc_price( '150' ) . '</code>',
					            '<code>' . wc_price( '135' ) . '</code>',
					            '<code>' . '10%' . '</code>'
				            ) ); ?>
                        </p>
                    </td>
                </tr>
            </table>

			<h2><?php esc_html_e( 'Filters', 'cost-of-goods-for-woocommerce' ); ?></h2>

            <table class="form-table bulk-edit-auto" role="presentation">
                <tr>
                    <th scope="row"><label for="product-category"><?php esc_html_e( 'Filter by category', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <select data-return_id="id" <?php echo esc_attr( $disabled ); ?> class="wc-category-search" multiple="multiple" style="width: 50%;" id="product-category" name="product_category[]" data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'cost-of-goods-for-woocommerce' ); ?>" data-action="json_search_categories">
                        </select>
                        <p class="description">
	                        <?php esc_html_e( 'Select only the categories you want to edit. Leave it empty to update all products.', 'cost-of-goods-for-woocommerce' ); ?>
	                        <?php echo ( ! empty( $blocked_text ) ) ? '<br />' . $blocked_text : ''; ?>
                        </p>
                    </td>
                </tr>
	            <tr>
		            <th scope="row"><label for="product-tag"><?php esc_html_e( 'Filter by tag(s)', 'cost-of-goods-for-woocommerce' ); ?></label></th>
		            <td>
			            <select data-return_id="id" <?php echo esc_attr( $disabled ); ?> class="wc-taxonomy-term-search" multiple="multiple" style="width: 50%;" id="product-tag" name="product_tag[]" data-placeholder="<?php esc_attr_e( 'Search for a tag&hellip;', 'cost-of-goods-for-woocommerce' ); ?>" data-taxonomy="product_tag">
			            </select>
			            <p class="description">
				            <?php esc_html_e( 'Select only the tag(s) you want to edit. Leave it empty to update all products.', 'cost-of-goods-for-woocommerce' ); ?>
				            <?php echo ( ! empty( $blocked_text ) ) ? '<br />' . $blocked_text : ''; ?>
			            </p>
		            </td>
	            </tr>
                <tr>
                    <th scope="row"><label for="costs_filter"><?php esc_html_e( 'Filter by cost', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <select class="wc-enhanced-select" <?php echo esc_attr( $disabled ); ?> data-return_id="id" <?php echo esc_attr( $disabled ); ?> id="costs_filter" name="costs_filter">
	                        <?php foreach ( $cost_filter_options as $option ): ?>
                                <option data-desc="<?php echo esc_attr( $option['desc'] ) ?>"
                                        value="<?php echo esc_attr( $option['id'] ) ?>"><?php echo esc_html( $option['label'] ) ?>
                                </option>
	                        <?php endforeach; ?>
                        </select>
                        <p class="description dropdown-description">
				            <?php echo ( ! empty( $blocked_text ) ) ? $blocked_text : ''; ?>
                        </p>
                    </td>
                </tr>
				<tr>
					<th scope="row"><label for="product-stock-status"><?php esc_html_e( 'Filter by stock status', 'cost-of-goods-for-woocommerce' ); ?></label></th>
					<td>
						<select class="wc-enhanced-select" id="product-stock-status" name="product_stock_status">
							<option value=""><?php echo esc_html__( 'Ignore stock status', 'woocommerce' ) ?></option>
							<?php foreach ( $stock_statuses as $status => $label ) : ?>
								<option value="<?php echo esc_attr( $status ) ?> "><?php echo esc_html( $label ) ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
            </table>
			<?php
		}

		/**
		 * Display content for Manually Section.
		 *
		 * @version 3.3.0
		 * @since   2.5.1
		 */
		function display_bulk_edit_costs_manually() {
			echo '<form method="get" style="margin-top:30px"><input type="hidden" name="page" value="bulk-edit-costs"/>';
			$this->wp_list_bulk_edit_tool->prepare_items();
			$this->wp_list_bulk_edit_tool->search_box( __( 'Search', 'cost-of-goods-for-woocommerce' ), 'alg_wc_cog_search' );
			$this->wp_list_bulk_edit_tool->display();
		}

		/**
		 * display_tabs_navs_html.
		 *
		 * @version 3.3.0
		 * @since   3.3.0
		 *
		 * @return void
		 */
		function display_tabs_navs_html() {
			global $current_screen;

			$tabs_nav_html = array();
			$nav_tabs      = $this->get_tab_nav_items();
			$tab           = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : key( $nav_tabs );

			if ( count( $nav_tabs ) > 1 ) {
				foreach ( $nav_tabs as $key => $tab_value ) {
					$label          = isset( $tab_value['label'] ) ? $tab_value['label'] : '';
					$tab_item_class = $tab === $key ? 'nav-tab nav-tab-active' : 'nav-tab';
					$section_url    = admin_url( sprintf( 'tools.php?page=%s&tab=%s', str_replace( 'tools_page_', '', $current_screen->base ), $key ) );

					$tabs_nav_html[] = sprintf( '<a href="%s" class="%s">%s</a>', esc_url( $section_url ), $tab_item_class, $label );
				}

				//printf( '<nav class="nav-tab-wrapper woo-nav-tab-wrapper">%s</nav>', implode( ' | ', $tabs_nav_html ) );
				printf( '<nav class="nav-tab-wrapper woo-nav-tab-wrapper">%s</nav>', implode( '', $tabs_nav_html ) );
			}
		}

		/**
		 * display_wp_list_tool.
		 *
		 * @version 3.3.0
		 * @since   2.3.1
		 */
		function display_bulk_edit_tools() {
			global $current_screen;
			$page_title = esc_html__( 'Bulk Edit Costs', 'cost-of-goods-for-woocommerce' );
			$tab_method = $this->get_current_tab( 'callback' );
			$tool_type  = 'update_costs';

			if ( $this->page_slug_prices == str_replace( 'tools_page_', '', $current_screen->base ) ) {
				$page_title = esc_html__( 'Bulk Edit Prices', 'cost-of-goods-for-woocommerce' );
				$tool_type  = 'update_prices';
			}

			ob_start();

			// Section heading title.
			printf( '<h1 class="wp-heading-inline">%s</h1>', $page_title, $this->get_current_tab( 'label' ), $this->get_current_tab( 'save_btn_top' ) );

			// Section navigations.
			$this->display_tabs_navs_html();

			do_action( 'alg_wc_cog_tools_after' );

			// Tab description.
			if ( ! empty( $tab_desc = $this->get_current_tab( 'desc' ) ) ) {
				printf( '<p>%s</p>', $tab_desc );
			}


			if ( method_exists( $this, $tab_method ) && is_callable( array( $this, $tab_method ) ) ) {
				call_user_func( array( $this, $tab_method ) );
			}

			// Bottom save button.
			if ( ! empty( $save_btn_bottom = $this->get_current_tab( 'save_btn_bottom' ) ) ) {
				printf( '<div class="form-action">%s%s<span class="spinner"></span></div>',
					wp_nonce_field( "_nonce_{$this->get_current_tab( 'id' )}_action", "_nonce_{$this->get_current_tab( 'id' )}_val" ),
					$save_btn_bottom
				);
			}

			$container_elem_type = 'form';
			if (
				( isset( $_GET['page'] ) && 'bulk-edit-costs' === $_GET['page'] ) &&
				( ! isset( $_GET['tab'] ) || 'costs_manually' === $_GET['tab'] )
			) {
				$container_elem_type = 'div';
				echo '</form>';
			}

			// Wrap up section content.
			printf( '<div class="notice is-dismissible alg_wc_cog_notice"><p></p></div><' . $container_elem_type . ' method="post" action="" class="bulk-edit-form %s" data-tool-type="%s"><div class="wrap alg_wc_cog_bulk_edit">%s</div></' . $container_elem_type . '>',
				esc_attr( $this->get_current_tab( 'form_class' ) ),
				esc_attr( $tool_type ),
				ob_get_clean()
			);
		}

		/**
		 * get_current_tab.
		 *
		 * @version 3.3.0
		 * @since   3.3.0
		 *
		 * @param $arg
		 *
		 * @return int|mixed|string|null
		 */
		function get_current_tab( $arg = '' ) {
			$nav_sections = $this->get_tab_nav_items();
			$tab = isset( $_GET['tab'] ) ? rawurlencode( sanitize_text_field( $_GET['tab'] ) ) : key( $nav_sections );

			if ( $arg === 'id' ) {
				return $tab;
			}

			if ( ! empty( $arg ) ) {
				return isset( $nav_sections[ $tab ][ $arg ] ) ? $nav_sections[ $tab ][ $arg ] : '';
			}

			return $nav_sections[ $tab ];
		}

		/**
		 * get_tab_nav_items.
		 *
		 * @version 3.3.0
		 * @since   3.3.0
		 *
		 * @return mixed|null
		 */
		function get_tab_nav_items() {
			global $current_screen;

			$current_screen_id = $current_screen ? str_replace( 'tools_page_bulk-edit-', '', $current_screen->base ) : '';
			$bulk_edit_tabs    = array(
				'costs_manually'       => array(
					'label'           => esc_html__( 'Manually', 'cost-of-goods-for-woocommerce' ),
					'save_btn_top'    => '',
					'save_btn_bottom' => sprintf( '<input type="submit" name="alg_wc_cog_bulk_edit_tool_save_costs" class="button-primary" value="%s">',
						esc_html__( 'Save', 'cost-of-goods-for-woocommerce' )
					),
					'desc'            => sprintf( __( 'Bulk edit products costs/prices/stock manually. Tools options can be set in "<strong>Cost of Goods for WooCommerce</strong>" <a href="%s">plugin settings</a>.', 'cost-of-goods-for-woocommerce' ),
						admin_url( 'admin.php?page=wc-settings&tab=alg_wc_cost_of_goods&section=tools' )
					),
					'form_class'      => 'bulk-edit-costs',
					'callback'        => 'display_bulk_edit_costs_manually',
				),
				'costs_automatically'  => array(
					'label'           => esc_html__( 'Automatically', 'cost-of-goods-for-woocommerce' ),
					'desc'            => esc_html__( 'Set the product costs automatically.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					                     esc_html__( 'Variation costs will also be updated accordingly.', 'cost-of-goods-for-woocommerce' ),
					'save_btn_bottom' => sprintf( '<p class="submit"><input type="submit" class="button-primary" value="%s">', esc_html__( 'Update Costs', 'cost-of-goods-for-woocommerce' ) ) . '</p>',
					'form_class'      => 'bulk-edit-costs ajax-submission',
					'callback'        => 'display_bulk_edit_costs_automatically',
				),
				'prices_automatically' => array(
					'label'           => esc_html__( 'Automatically', 'cost-of-goods-for-woocommerce' ),
					'save_btn_top'    => '',
					'save_btn_bottom' => sprintf( '<p class="submit"><input type="submit" name="alg_wc_cog_bulk_edit_tool_save_costs" class="button-primary" value="%s"></p>',
						esc_html__( 'Update prices', 'cost-of-goods-for-woocommerce' )
					),
					'desc'            => esc_html__( 'Set the product prices according to the cost.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					                     esc_html__( 'Variation prices will also be updated accordingly.', 'cost-of-goods-for-woocommerce' ),
					'form_class'      => 'bulk-edit-prices ajax-submission',
					'callback'        => 'display_bulk_edit_prices',
				),

			);

			foreach ( $bulk_edit_tabs as $section_key => $section ) {
				if ( strpos( $section_key, $current_screen_id . '_' ) === false ) {
					unset( $bulk_edit_tabs[ $section_key ] );
				}
			}

			return apply_filters( 'alg_wc_cog_filters_bulk_edit_tabs_nav', $bulk_edit_tabs );
		}

		/**
		 * set_screen_option.
		 *
		 * @version 2.3.1
		 * @since   2.3.1
		 *
		 * @param $status
		 * @param $option
		 * @param $value
		 *
		 * @return mixed
		 *
		 */
		function set_screen_option( $status, $option, $value ) {
			if ( 'alg_wc_cog_bulk_edit_per_page' === $option ) {
				return $value;
			}

			return $status;
		}

		/**
		 * screen_option.
		 *
		 * @version 2.3.1
		 * @since   2.3.1
		 */
		function screen_option() {
			$option = 'per_page';
			$args   = [
				'label'   => __( 'Items per page', 'cost-of-goods-for-woocommerce' ),
				'default' => 20,
				'option'  => 'alg_wc_cog_bulk_edit_per_page'
			];
			add_screen_option( $option, $args );
			require_once( 'class-alg-wc-cog-wplist-bulk-edit-tool.php' );
			$this->wp_list_bulk_edit_tool = new Alg_WC_Cost_of_Goods_WP_List_Bulk_Edit_Tool();
		}

		/**
		 * create_wp_list_tool.
		 *
		 * @version 2.3.4
		 * @since   2.3.1
		 */
		function create_wp_list_tool() {
			if ( ! apply_filters( 'alg_wc_cog_create_edit_costs_tool_validation', true ) ) {
				return;
			}
			$hook = add_submenu_page(
				'tools.php',
				__( 'Bulk Edit Costs', 'cost-of-goods-for-woocommerce' ),
				__( 'Bulk Edit Costs', 'cost-of-goods-for-woocommerce' ),
				'manage_woocommerce',
				$this->page_slug_costs,
				array( $this, 'display_bulk_edit_tools' )
			);
			add_action( "load-{$hook}", array( $this, 'screen_option' ) );

			add_submenu_page(
				'tools.php',
				__( 'Bulk Edit Prices', 'cost-of-goods-for-woocommerce' ),
				__( 'Bulk Edit Prices', 'cost-of-goods-for-woocommerce' ),
				'manage_woocommerce',
				$this->page_slug_prices,
				array( $this, 'display_bulk_edit_tools' )
			);
		}

		/**
		 * enqueue_scripts_and_styles.
		 *
		 * @version 3.7.8
		 * @since   1.3.3
		 */
		function enqueue_scripts_and_styles( $hook ) {
            if( ! in_array( $hook, array( 'tools_page_bulk-edit-costs', 'tools_page_bulk-edit-prices' ) ) ) {
				return;
			}
			alg_wc_cog_enqueue_style( 'alg-wc-cog-bulk-edit-tool-style',
				alg_wc_cog()->plugin_url() . '/includes/css/alg-wc-cog-bulk-edit-tool.css',
				array(),
				alg_wc_cog()->version
			);
			alg_wc_cog_enqueue_script( 'alg-wc-cog-bulk-edit-tool', alg_wc_cog()->plugin_url() . '/includes/js/alg-wc-cog-bulk-edit-tool.js', array( 'jquery' ), alg_wc_cog()->version, true );
			wp_localize_script( 'alg-wc-cog-bulk-edit-tool', 'algWcCog',
				array(
					'ajaxURL'     => admin_url( 'admin-ajax.php' ),
					'confirmText' => esc_html__( 'Are you really want to update?', 'cost-of-goods-for-woocommerce' )
				)
			);
		}

		/**
		 * add_tool_to_wc_screen_ids.
		 *
		 * for `wc_input_price` class.
		 *
		 * @version 2.6.1
		 * @since   1.2.0
		 */
		function add_tool_to_wc_screen_ids( $screen_ids ) {
			$screen_ids[] = 'tools_page_bulk-edit-costs';
			$screen_ids[] = 'tools_page_bulk-edit-prices';
			return $screen_ids;
		}

		/**
		 * save_costs.
		 *
		 * @version 2.8.8
		 * @since   1.2.0
		 * @see     https://wordpress.org/support/topic/you-should-add-posibility-to-edit-regular-price-and-sale-price/
		 * @todo    [next] prices: `$do_update_func`
		 * @todo    [maybe] nonce etc.
		 * @todo    [maybe] output some error on ` ! ( $product = wc_get_product( $product_id ) )`?
		 */
		function save_costs() {
			if (
				isset( $_POST['alg_wc_cog_bulk_edit_tool_save_costs'] ) &&
				isset( $_POST['_nonce_costs_manually_val'] ) &&
				wp_verify_nonce( $_REQUEST['_nonce_costs_manually_val'], '_nonce_costs_manually_action' ) &&
				current_user_can( 'manage_woocommerce' )
			) {
				// Do edit prices.
				$do_edit_prices = ( 'yes' === get_option( 'alg_wc_cog_bulk_edit_tool_edit_prices', 'no' ) );
				if ( $do_edit_prices ) {
					$error_sale_price_ids = array();
				}
				// Manually.
				if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_costs'] ) && is_array( $_POST['alg_wc_cog_bulk_edit_tool_costs'] ) ) {
					foreach ( $_POST['alg_wc_cog_bulk_edit_tool_costs'] as $product_id => $cost_value ) {
						update_post_meta( sanitize_key( $product_id ), '_alg_wc_cog_cost', sanitize_text_field( $cost_value ) );
					}
				}
				// Prices.
				if ( $do_edit_prices ) {
					if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_regular_price'] ) && is_array( $_POST['alg_wc_cog_bulk_edit_tool_regular_price'] ) ) {
						$regular_prices = wc_clean( $_POST['alg_wc_cog_bulk_edit_tool_regular_price'] );
						foreach ( $regular_prices as $product_id => $regular_price_value ) {
							if ( $product = wc_get_product( $product_id ) ) {
								$product->set_regular_price( $regular_price_value );
								$product->save();
							}
						}
					}
					if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_sale_price'] ) && is_array( $_POST['alg_wc_cog_bulk_edit_tool_sale_price'] ) ) {
						$sale_prices = wc_clean( $_POST['alg_wc_cog_bulk_edit_tool_sale_price'] );
						foreach ( $sale_prices as $product_id => $sale_price_value ) {
							if ( $product = wc_get_product( $product_id ) ) {
								if ( $sale_price_value <= $product->get_regular_price() ) {
									$product->set_sale_price( $sale_price_value );
									$product->save();
								} else {
									array_push( $error_sale_price_ids, $product_id );
								}
							}
						}
					}
				}
				// Stock.
				if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_stock'] ) && is_array( $_POST['alg_wc_cog_bulk_edit_tool_stock'] ) ) {
					$do_update_func = ( 'func' === get_option( 'alg_wc_cog_bulk_edit_tool_manage_stock_method', 'meta' ) );
					foreach ( $_POST['alg_wc_cog_bulk_edit_tool_stock'] as $product_id => $stock_value ) {
						if ( $do_update_func && ( $product = wc_get_product( $product_id ) ) ) {
							$product->set_stock_quantity( sanitize_text_field( $stock_value ) );
							$product->save();
						} else {
							update_post_meta( sanitize_key( $product_id ), '_stock', sanitize_text_field( $stock_value ) );
						}
					}
				}
				// Tags update.
				if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_product_tag'] ) && is_array( $_POST['alg_wc_cog_bulk_edit_tool_product_tag'] ) ) {
					foreach ( $_POST['alg_wc_cog_bulk_edit_tool_product_tag'] as $product_id => $tag_ids ) {
						$tag_ids = array_map( 'intval', $tag_ids );
						wp_set_post_terms( $product_id, $tag_ids, 'product_tag' );
					}
				}
				// Notices.
				add_action( 'admin_notices', array( $this, 'admin_notice_costs_saved' ) );
				if ( $do_edit_prices && count( $error_sale_price_ids ) ) {
					$this->error_sale_price_ids = $error_sale_price_ids;
					add_action( 'admin_notices', array( $this, 'admin_notice_sale_price_higher' ) );
				}
			}
		}

		/**
		 * admin_notice_costs_saved.
		 *
		 * @version 1.2.0
		 * @since   1.2.0
		 */
		function admin_notice_costs_saved() {
			echo '<div class="notice notice-success is-dismissible"><p><strong>' . __( 'Data have been saved.', 'cost-of-goods-for-woocommerce' ) . '</strong></p></div>';
		}

		/**
		 * get_the_title.
		 *
		 * @version 1.4.0
		 * @since   1.4.0
		 */
		function get_the_title( $post_id ) {
			return get_the_title( $post_id ) . ' (#' . $post_id . ')';
		}

		/**
		 * admin_notice_sale_price_higher.
		 *
		 * @version 1.4.0
		 * @since   1.4.0
		 */
		function admin_notice_sale_price_higher() {
			echo '<div class="notice notice-error is-dismissible"><p><strong>' . sprintf( __( 'Sale price is higher than regular price: %s.', 'cost-of-goods-for-woocommerce' ),
					implode( ', ', array_map( array( $this, 'get_the_title' ), $this->error_sale_price_ids ) ) ) . '</strong></p></div>';
		}
	}

endif;

return new Alg_WC_Cost_of_Goods_Bulk_Edit_Tool();


