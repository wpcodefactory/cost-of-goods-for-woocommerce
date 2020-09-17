<?php
/**
 * Cost of Goods for WooCommerce - Bulk Edit Tool Class
 *
 * @version 2.1.0
 * @since   1.2.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Bulk_Edit_Tool' ) ) :

class Alg_WC_Cost_of_Goods_Bulk_Edit_Tool {

	/**
	 * Constructor.
	 *
	 * @version 1.4.0
	 * @since   1.2.0
	 */
	function __construct() {
		add_action( 'admin_menu',             array( $this, 'create_tool' ) );
		add_action( 'admin_init',             array( $this, 'save_costs' ) );
		add_filter( 'woocommerce_screen_ids', array( $this, 'add_tool_to_wc_screen_ids' ) );
		add_action( 'admin_enqueue_scripts',  array( $this, 'enqueue_scripts_and_styles' ) );
		if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_post_title'] ) ) {
			if ( 'title' === get_option( 'alg_wc_cog_bulk_edit_tool_search_method', 'title' ) ) {
				add_filter( 'posts_where',    array( $this, 'search_by_post_title' ), PHP_INT_MAX );
			} else {
				add_filter( 'pre_get_posts',  array( $this, 'search_products' ) );
			}
		}
	}

	/**
	 * enqueue_scripts_and_styles.
	 *
	 * @version 2.1.0
	 * @since   1.3.3
	 */
	function enqueue_scripts_and_styles( $hook ) {
		if ( 'tools_page_bulk-edit-costs' != $hook ) {
			return;
		}
		$min_suffix = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ? '' : '.min' );
		wp_enqueue_style( 'alg-wc-cog-bulk-edit-tool-style',
			alg_wc_cog()->plugin_url() . '/includes/css/alg-wc-cog-bulk-edit-tool' . $min_suffix . '.css',
			array(),
			alg_wc_cog()->version
		);
		wp_enqueue_script( 'alg-wc-cog-bulk-edit-tool',
			alg_wc_cog()->plugin_url() . '/includes/js/alg-wc-cog-bulk-edit-tool' . $min_suffix . '.js',
			array( 'jquery' ),
			alg_wc_cog()->version,
			true
		);
	}

	/**
	 * add_tool_to_wc_screen_ids.
	 *
	 * for `wc_input_price` class.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	function add_tool_to_wc_screen_ids( $screen_ids ) {
		$screen_ids[] = 'tools_page_bulk-edit-costs';
		return $screen_ids;
	}

	/**
	 * save_costs.
	 *
	 * @version 1.4.0
	 * @since   1.2.0
	 * @see     https://wordpress.org/support/topic/you-should-add-posibility-to-edit-regular-price-and-sale-price/
	 * @todo    [dev] nonce etc.
	 */
	function save_costs() {

		$do_edit_prices = ( 'yes' === get_option( 'alg_wc_cog_bulk_edit_tool_edit_prices', 'no' ) );
		if ( $do_edit_prices ) {
			$error_sale_price_ids = array();
		}

		if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_save_costs'] ) ) {
			// Costs
			if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_costs'] )&& is_array( $_POST['alg_wc_cog_bulk_edit_tool_costs'] ) ) {
				foreach ( $_POST['alg_wc_cog_bulk_edit_tool_costs'] as $product_id => $cost_value ) {
					update_post_meta( sanitize_key( $product_id ), '_alg_wc_cog_cost', sanitize_text_field( $cost_value ) );
				}
			}
			// Prices
			if ( $do_edit_prices ) {
				if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_regular_price'] ) && is_array( $_POST['alg_wc_cog_bulk_edit_tool_regular_price'] ) ) {
					$regular_prices = wc_clean( $_POST['alg_wc_cog_bulk_edit_tool_regular_price'] );
					foreach ( $regular_prices as $product_id => $regular_price_value ) {
						$product = wc_get_product( $product_id );
						$product->set_regular_price( $regular_price_value );
						$product->save();
					}
				}
				if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_sale_price'] )&& is_array( $_POST['alg_wc_cog_bulk_edit_tool_sale_price'] ) ) {
					$sale_prices = wc_clean( $_POST['alg_wc_cog_bulk_edit_tool_sale_price'] );
					foreach ( $sale_prices as $product_id => $sale_price_value ) {
						$product = wc_get_product( $product_id );
						if ( $sale_price_value <= $product->get_regular_price() ) {
							$product->set_sale_price( $sale_price_value );
							$product->save();
						} else {
							array_push( $error_sale_price_ids, $product_id );
						}
					}
				}
			}
			// Stock
			if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_stock'] )&& is_array( $_POST['alg_wc_cog_bulk_edit_tool_stock'] ) ) {
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
			// Notices
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
		echo '<div class="notice notice-success is-dismissible"><p><strong>' . __( 'Costs have been saved.', 'cost-of-goods-for-woocommerce' ) . '</strong></p></div>';
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

	/**
	 * create_tool.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	function create_tool() {
		add_submenu_page(
			'tools.php',
			__( 'Bulk Edit Costs', 'cost-of-goods-for-woocommerce' ),
			__( 'Bulk Edit Costs', 'cost-of-goods-for-woocommerce' ),
			'manage_woocommerce',
			'bulk-edit-costs',
			array( $this, 'display_tool' )
		);
	}

	/**
	 * search_by_post_title.
	 *
	 * @version 1.3.3
	 * @since   1.3.2
	 */
	function search_by_post_title( $where ) {
		global $wpdb;
		$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like( $_POST['alg_wc_cog_bulk_edit_tool_post_title'] ) ) . '%\'';
		return $where;
	}

	/**
	 * search_products.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function search_products( $query ) {
		$data_store = WC_Data_Store::load( 'product' );
		$ids        = $data_store->search_products( wc_clean( wp_unslash( $_POST['alg_wc_cog_bulk_edit_tool_post_title'] ) ), '', true, true );
		$post_in    = array_merge( $ids, array( 0 ) );
		$query->set( 'post__in', $post_in );
		return $query;
	}

	/**
	 * display_tool.
	 *
	 * @version 2.0.0
	 * @since   1.2.0
	 * @todo    [dev] pagination (same in "Import" tool and "Stock" report)
	 * @todo    [dev] use `wc_get_products()`
	 * @todo    [maybe] better description here and in settings
	 * @todo    [feature] [maybe] bulk edit order items meta
	 */
	function display_tool() {
		$do_manage_stock = ( 'yes' === get_option( 'alg_wc_cog_bulk_edit_tool_manage_stock', 'no' ) );
		$do_edit_prices  = ( 'yes' === get_option( 'alg_wc_cog_bulk_edit_tool_edit_prices', 'no' ) );
		$table_data      = array();
		if ( $do_edit_prices ) {
			$table_data[] = array(
				__( 'Product ID', 'cost-of-goods-for-woocommerce' ),
				__( 'SKU', 'cost-of-goods-for-woocommerce' ),
				__( 'Title', 'cost-of-goods-for-woocommerce' ),
				__( 'Cost', 'cost-of-goods-for-woocommerce' ),
				__( 'Regular price', 'cost-of-goods-for-woocommerce' ),
				__( 'Sale price', 'cost-of-goods-for-woocommerce' ),
				__( 'Stock', 'cost-of-goods-for-woocommerce' ),
			);
		} else {
			$table_data[] = array(
				__( 'Product ID', 'cost-of-goods-for-woocommerce' ),
				__( 'SKU', 'cost-of-goods-for-woocommerce' ),
				__( 'Title', 'cost-of-goods-for-woocommerce' ),
				__( 'Cost', 'cost-of-goods-for-woocommerce' ),
				__( 'Price', 'cost-of-goods-for-woocommerce' ),
				__( 'Stock', 'cost-of-goods-for-woocommerce' ),
			);
		}
		$args = array(
			'post_type'      => array( 'product', 'product_variation' ),
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'fields'         => 'ids',
		);
		$loop = new WP_Query( $args );
		if ( $loop->have_posts() ) {
			foreach ( $loop->posts as $product_id ) {
				$value = get_post_meta( $product_id, '_alg_wc_cog_cost', true );
				if ( $do_edit_prices ) {
					$regular_price = get_post_meta( $product_id, '_regular_price', true );
					$sale_price    = get_post_meta( $product_id, '_sale_price', true );
				}
				$input_field = '<input' .
					' name="alg_wc_cog_bulk_edit_tool_costs[' . $product_id . ']"' .
					' type="text"' .
					' class="alg_wc_cog_bet_input short wc_input_price"' .
					' initial-value="' . $value . '"' .
					' value="'         . $value . '"' . '>';
				if ( $do_edit_prices ) {
					$input_regular_price = '<input' .
						' name="alg_wc_cog_bulk_edit_tool_regular_price[' . $product_id . ']"' .
						' type="text"' .
						' class="alg_wc_cog_bet_input short wc_input_price"' .
						' initial-value="' . $regular_price . '"' .
						' value="'         . $regular_price . '"' . '>';
					$input_sale_price = '<input' .
						' name="alg_wc_cog_bulk_edit_tool_sale_price[' . $product_id . ']"' .
						' type="text"' .
						' class="alg_wc_cog_bet_input short wc_input_price"' .
						' initial-value="' . $sale_price . '"' .
						' value="'         . $sale_price . '"' . '>';
				}
				if ( $do_manage_stock ) {
					$stock_value  = ( '' === ( $stock = get_post_meta( $product_id, '_stock', true ) ) ? '' : floatval( $stock ) );
					$stock_status = ( '' == ( $_stock_status = get_post_meta( $product_id, '_stock_status', true ) ) ? 'N/A' : $_stock_status );
					$input_field_stock = '<input' .
						' name="alg_wc_cog_bulk_edit_tool_stock[' . $product_id . ']"' .
						' type="text"' .
						' class="alg_wc_cog_bet_input short"' .
						' initial-value="' . $stock_value . '"' .
						' value="'         . $stock_value . '"' . '>';
					$input_field_stock .= wc_help_tip( sprintf( __( 'Stock status: %s', 'cost-of-goods-for-woocommerce' ), $stock_status ) );
				}
				if ( $do_edit_prices ) {
					$table_data[] = array(
						'<a tabIndex="-1" target="_blank" href="' . admin_url( 'post.php?post=' . $product_id . '&action=edit' ) . '">' . $product_id . '</a>',
						get_post_meta( $product_id, '_sku', true ),
						'<a tabIndex="-1" target="_blank" href="' . get_the_permalink( $product_id ) . '">' . get_the_title( $product_id ) . '</a>',
						$input_field,
						$input_regular_price,
						$input_sale_price,
						( $do_manage_stock ? $input_field_stock : get_post_meta( $product_id, '_stock', true ) ),
					);
				} else {
					$table_data[] = array(
						'<a tabIndex="-1" target="_blank" href="' . admin_url( 'post.php?post=' . $product_id . '&action=edit' ) . '">' . $product_id . '</a>',
						get_post_meta( $product_id, '_sku', true ),
						'<a tabIndex="-1" target="_blank" href="' . get_the_permalink( $product_id ) . '">' . get_the_title( $product_id ) . '</a>',
						$input_field,
						get_post_meta( $product_id, '_price', true ),
						( $do_manage_stock ? $input_field_stock : get_post_meta( $product_id, '_stock', true ) ),
					);
				}
			}
		}
		$save_button = '<p>' . '<input type="submit" name="alg_wc_cog_bulk_edit_tool_save_costs" class="button-primary" value="' .
			__( 'Save all', 'cost-of-goods-for-woocommerce' ) . '">' . '</p>';
		$search_input_title       = ( 'title' === get_option( 'alg_wc_cog_bulk_edit_tool_search_method', 'title' ) ?
			__( 'Search by product title', 'cost-of-goods-for-woocommerce' ) : __( 'Search products', 'cost-of-goods-for-woocommerce' ) );
		$search_input_placeholder = ( 'title' === get_option( 'alg_wc_cog_bulk_edit_tool_search_method', 'title' ) ?
			__( 'Product title...', 'cost-of-goods-for-woocommerce' ) : __( 'Search...', 'cost-of-goods-for-woocommerce' ) );
		$search_input = '<input style="float:right;min-width:300px;" type="text" name="alg_wc_cog_bulk_edit_tool_post_title"' .
			' id="alg_wc_cog_bulk_edit_tool_post_title" title="' . $search_input_title . '"' .
			' placeholder="' . $search_input_placeholder . '"' .
			' value="' . ( isset( $_POST['alg_wc_cog_bulk_edit_tool_post_title'] ) ? $_POST['alg_wc_cog_bulk_edit_tool_post_title'] : '' ) . '">';
		echo '<div class="wrap">' .
			'<h1>' . __( 'Bulk Edit Costs', 'cost-of-goods-for-woocommerce' ) . '</h1>' .
			'<p>' . __( 'Bulk edit products costs/prices/stock.', 'cost-of-goods-for-woocommerce' ) . ' ' .
				sprintf( __( 'Tool\'s options can be set in "%s" %s.', 'cost-of-goods-for-woocommerce' ),
					__( 'Cost of Goods for WooCommerce', 'cost-of-goods-for-woocommerce' ),
					'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_cost_of_goods&section=tools' ) . '">' . __( 'plugin settings', 'cost-of-goods-for-woocommerce' ) . '</a>'
				) .
			'</p>' .
			'<form method="post" action="">' .
				$search_input .
				$save_button .
				alg_wc_cog_get_table_html( $table_data, array( 'table_heading_type' => 'horizontal', 'table_class' => 'widefat striped' ) ) .
				$save_button .
			'</form>' .
		'</div>';
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Bulk_Edit_Tool();
