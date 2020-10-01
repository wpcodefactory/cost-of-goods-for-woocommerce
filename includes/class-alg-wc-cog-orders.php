<?php
/**
 * Cost of Goods for WooCommerce - Orders Class
 *
 * @version 2.2.0
 * @since   2.1.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Orders' ) ) :

class Alg_WC_Cost_of_Goods_Orders {

	/**
	 * Constructor.
	 *
	 * @version 2.2.0
	 * @since   2.1.0
	 * @todo    [maybe] split into smaller files, e.g. `class-alg-wc-cog-orders-ajax.php`, `class-alg-wc-cog-orders-columns.php` etc.
	 */
	function __construct() {
		$this->get_options();
		$this->add_hooks();
		$this->includes();
	}

	/**
	 * includes.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function includes() {
		require_once( 'class-alg-wc-cog-orders-meta-boxes.php' );
	}

	/**
	 * get_options.
	 *
	 * @version 2.2.0
	 * @since   2.1.0
	 * @todo    [maybe] Fees: From Meta: no `trim`?
	 */
	function get_options() {
		// Fees: Gateways
		$this->is_gateway_costs_enabled               = ( 'yes' === get_option( 'alg_wc_cog_gateway_costs_enabled', 'no' ) );
		if ( $this->is_gateway_costs_enabled ) {
			$this->gateway_costs_fixed                = get_option( 'alg_wc_cog_gateway_costs_fixed',   array() );
			$this->gateway_costs_percent              = get_option( 'alg_wc_cog_gateway_costs_percent', array() );
		}
		// Fees: Shipping
		$this->is_shipping_costs_enabled              = ( 'yes' === get_option( 'alg_wc_cog_shipping_costs_enabled', 'no' ) );
		if ( $this->is_shipping_costs_enabled ) {
			$this->shipping_use_instances             = ( 'yes' === get_option( 'alg_wc_cog_shipping_use_shipping_instance', 'no' ) );
			$this->shipping_costs_fixed               = get_option( 'alg_wc_cog_shipping_costs_fixed',   array() );
			$this->shipping_costs_percent             = get_option( 'alg_wc_cog_shipping_costs_percent', array() );
		}
		// Fees: All Orders
		$this->order_extra_cost_fixed                 = get_option( 'alg_wc_cog_order_extra_cost_fixed',   0 );
		$this->order_extra_cost_percent               = get_option( 'alg_wc_cog_order_extra_cost_percent', 0 );
		// Fees: Per Order
		$this->is_order_extra_cost_per_order          = array(
			'handling' => ( 'yes' === get_option( 'alg_wc_cog_order_extra_cost_per_order_handling_fee', 'no' ) ),
			'shipping' => ( 'yes' === get_option( 'alg_wc_cog_order_extra_cost_per_order_shipping_fee', 'no' ) ),
			'payment'  => ( 'yes' === get_option( 'alg_wc_cog_order_extra_cost_per_order_payment_fee',  'no' ) ),
		);
		// Fees: From Meta
		$this->order_extra_cost_from_meta             = get_option( 'alg_wc_cog_order_extra_cost_from_meta', '' );
		if ( '' != $this->order_extra_cost_from_meta ) {
			$this->order_extra_cost_from_meta         = array_map( 'trim', explode( PHP_EOL, $this->order_extra_cost_from_meta ) );
		}
		// Calculations
		$this->order_extra_cost_percent_total         = get_option( 'alg_wc_cog_order_extra_cost_percent_total', 'subtotal_excl_tax' );
		$this->order_count_empty_costs                = ( 'yes' === get_option( 'alg_wc_cog_order_count_empty_costs', 'no' ) );
		$this->do_add_shipping_to_profit              = ( 'yes' === get_option( 'alg_wc_cog_order_shipping_to_profit', 'no' ) );
		$this->do_add_fees_to_profit                  = ( 'yes' === get_option( 'alg_wc_cog_order_fees_to_profit', 'no' ) );
		$this->delay_calculations_status              = get_option( 'alg_wc_cog_orders_delay_calculations_status', array() );
		// Admin Order Edit
		$this->item_costs_option                      = get_option( 'alg_wc_cog_orders_item_costs', 'yes' );
		$this->is_order_meta_box                      = ( 'yes' === get_option( 'alg_wc_cog_orders_meta_box', 'yes' ) );
		$this->is_admin_notice                        = ( 'yes' === get_option( 'alg_wc_cog_orders_admin_notice', 'no' ) );
		$this->admin_notice_text                      = get_option( 'alg_wc_cog_orders_admin_notice_text', __( 'You are selling below the cost of goods.', 'cost-of-goods-for-woocommerce' ) );
		$this->is_add_item_ajax                       = ( 'yes' === get_option( 'alg_wc_cog_order_prepopulate_in_ajax', 'yes' ) );
		$this->is_save_order_items_ajax               = ( 'yes' === get_option( 'alg_wc_cog_order_save_items_ajax', 'yes' ) );
		$this->recalculate_order_button_ajax          = get_option( 'alg_wc_cog_order_prepopulate_on_recalculate_order', 'no' );
		// Order item costs: Force update
		$this->do_force_on_order_update               = ( 'yes' === get_option( 'alg_wc_cog_orders_force_on_update', 'no' ) );
		$this->do_force_on_status                     = ( 'yes' === get_option( 'alg_wc_cog_orders_force_on_status', 'no' ) );
		$this->do_force_on_new_item                   = ( 'yes' === get_option( 'alg_wc_cog_orders_force_on_new_item', 'no' ) );
		// Admin Orders List Columns
		$this->is_column_cost                         = ( 'yes' === get_option( 'alg_wc_cog_orders_columns_cost', 'no' ) );
		$this->is_column_profit                       = ( 'yes' === get_option( 'alg_wc_cog_orders_columns_profit', 'yes' ) );
		$this->is_column_profit_percent               = ( 'yes' === get_option( 'alg_wc_cog_orders_columns_profit_percent', 'no' ) );
		$this->is_column_profit_margin                = ( 'yes' === get_option( 'alg_wc_cog_orders_columns_profit_margin', 'no' ) );
		$this->is_columns_extra_cost_per_order        = ( 'yes' === get_option( 'alg_wc_cog_order_extra_cost_per_order_columns', 'no' ) );
		$this->column_order_status                    = array(
			'cost'   => get_option( 'alg_wc_cog_orders_columns_cost_order_status',   array() ),
			'profit' => get_option( 'alg_wc_cog_orders_columns_profit_order_status', array() ),
		);
		$this->column_order_status['profit_percent'] = $this->column_order_status['profit'];
		$this->column_order_status['profit_margin']  = $this->column_order_status['profit'];
		// Sorting
		$this->is_columns_sorting                     = ( 'yes' === get_option( 'alg_wc_cog_columns_sorting', 'yes' ) );
		$this->is_columns_sorting_exclude_empty_lines = ( 'yes' === get_option( 'alg_wc_cog_columns_sorting_exclude_empty_lines', 'yes' ) );
	}

	/**
	 * add_hooks.
	 *
	 * @version 2.2.0
	 * @since   2.1.0
	 * @todo    [next] Save order items costs on new order: REST API?
	 * @todo    [next] Save order items costs on new order: `wp_insert_post`?
	 * @todo    [next] Save order items costs on new order: "Point of Sale POS for WooCommerce" plugin (by "BizSwoop a CPF Concepts, LLC Brand")
	 */
	function add_hooks() {
		// Order item costs: Force update
		add_action( 'save_post_shop_order',                             array( $this, 'update_order_items_costs_save_post' ), 10, 3 );
		add_action( 'woocommerce_new_order_item',                       array( $this, 'update_order_items_costs_new_item' ), 10, 3 );
		add_action( 'woocommerce_order_status_changed',                 array( $this, 'update_order_items_costs_order_status_changed' ), 10, 1 );
		// Order item costs on order edit page
		add_action( 'woocommerce_before_order_itemmeta',                array( $this, 'add_cost_input_shop_order' ), PHP_INT_MAX, 3 );
		add_action( 'save_post_shop_order',                             array( $this, 'save_cost_input_shop_order_save_post' ), PHP_INT_MAX, 3 );
		add_filter( 'woocommerce_hidden_order_itemmeta',                array( $this, 'hide_cost_input_meta_shop_order' ), PHP_INT_MAX );
		// Admin new order (AJAX)
		add_action( 'woocommerce_new_order_item',                       array( $this, 'new_order_item_ajax' ), PHP_INT_MAX, 3 );
		// "Recalculate" order button (AJAX)
		add_action( 'woocommerce_saved_order_items',                    array( $this, 'recalculate_order_ajax' ), PHP_INT_MAX, 2 );
		// Save order items (AJAX)
		add_action( 'woocommerce_before_save_order_items',              array( $this, 'save_order_items_ajax' ), PHP_INT_MAX, 2 );
		// Save order items costs on new order
		add_action( 'woocommerce_new_order',                            array( $this, 'save_cost_input_shop_order_new' ), PHP_INT_MAX );
		add_action( 'woocommerce_api_create_order',                     array( $this, 'save_cost_input_shop_order_new' ), PHP_INT_MAX );
		add_action( 'woocommerce_cli_create_order',                     array( $this, 'save_cost_input_shop_order_new' ), PHP_INT_MAX );
		add_action( 'kco_before_confirm_order',                         array( $this, 'save_cost_input_shop_order_new' ), PHP_INT_MAX );
		add_action( 'woocommerce_checkout_order_processed',             array( $this, 'save_cost_input_shop_order_new' ), PHP_INT_MAX );
		add_action( 'wkwcpos_after_creating_order',                     array( $this, 'save_cost_input_shop_order_new_by_order' ), PHP_INT_MAX );
		// Orders columns
		if (
			$this->is_column_cost || ( $this->is_columns_extra_cost_per_order && in_array( true, $this->is_order_extra_cost_per_order ) ) ||
			$this->is_column_profit || $this->is_column_profit_percent || $this->is_column_profit_margin
		) {
			add_filter( 'manage_edit-shop_order_columns',               array( $this, 'add_order_columns' ), PHP_INT_MAX );
			add_action( 'manage_shop_order_posts_custom_column',        array( $this, 'render_order_columns' ), PHP_INT_MAX, 2 );
			// Make columns sortable
			if ( $this->is_columns_sorting ) {
				add_filter( 'manage_edit-shop_order_sortable_columns',  array( $this, 'shop_order_sortable_columns' ) );
				add_action( 'pre_get_posts',                            array( $this, 'shop_order_pre_get_posts_order_by_column' ) );
			}
		}
		// Admin notice
		if ( $this->is_admin_notice ) {
			add_action( 'admin_notices',                                array( $this, 'order_admin_notice' ), PHP_INT_MAX );
		}
		// Delay calculations by order status
		if ( ! empty( $this->delay_calculations_status ) ) {
			foreach ( $this->delay_calculations_status as $status ) {
				add_action( 'woocommerce_order_status_' . $status,      array( $this, 'save_cost_input_shop_order_new' ), PHP_INT_MAX );
			}
		}
		// Compatibility: "WooCommerce PDF Invoices, Packing Slips, Delivery Notes & Shipping Labels" plugin
		add_filter( 'wf_pklist_modify_meta_data',                       array( $this, 'wf_pklist_remove_cog_meta' ), PHP_INT_MAX );
	}

	/**
	 * wf_pklist_remove_cog_meta.
	 *
	 * @version 1.3.4
	 * @since   1.3.4
	 */
	function wf_pklist_remove_cog_meta( $meta_data ) {
		if ( isset( $meta_data['_alg_wc_cog_item_cost'] ) ) {
			unset( $meta_data['_alg_wc_cog_item_cost'] );
		}
		return $meta_data;
	}

	/**
	 * shop_order_sortable_columns.
	 *
	 * @version 2.2.0
	 * @since   1.7.0
	 */
	function shop_order_sortable_columns( $columns ) {
		foreach ( $this->order_columns as $column_id => $column_title ) {
			$columns[ $column_id ] = $this->get_order_column_key( $column_id );
		}
		return $columns;
	}

	/**
	 * shop_order_pre_get_posts_order_by_column.
	 *
	 * @version 2.1.0
	 * @since   1.7.0
	 */
	function shop_order_pre_get_posts_order_by_column( $query ) {
		alg_wc_cog_pre_get_posts_order_by_column( $query, 'shop_order', $this->is_columns_sorting_exclude_empty_lines );
	}

	/**
	 * order_admin_notice.
	 *
	 * @version 2.1.0
	 * @since   1.4.4
	 * @todo    [maybe] simplify "is order edit page" check
	 */
	function order_admin_notice() {
		if (
			function_exists( 'get_current_screen' ) && ( $scr = get_current_screen() ) && isset( $scr->base, $scr->id ) && 'post' === $scr->base && 'shop_order' === $scr->id &&
			isset( $_GET['action'] ) && 'edit' === $_GET['action'] && isset( $_GET['post'] ) && is_numeric( $_GET['post'] ) &&
			( $order_id = get_the_ID() ) && function_exists( 'wc_get_order' ) && ( $order = wc_get_order() ) &&
			( $profit = get_post_meta( $order_id, '_alg_wc_cog_order_profit', true ) ) && $profit < 0
		) {
			$class   = 'notice notice-error is-dismissible';
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $this->admin_notice_text ) );
		}
	}

	/**
	 * add_order_columns.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 * @todo    [next] add more columns (i.e. not only cost, per order fees, profit, profit percent and profit margin)
	 */
	function add_order_columns( $columns ) {
		$this->order_columns = array();
		if ( $this->is_column_cost ) {
			$this->order_columns['cost'] = __( 'Cost', 'cost-of-goods-for-woocommerce' );
		}
		if ( $this->is_columns_extra_cost_per_order && in_array( true, $this->is_order_extra_cost_per_order ) ) {
			foreach ( $this->is_order_extra_cost_per_order as $fee_type => $is_enabled ) {
				if ( $is_enabled ) {
					$this->order_columns[ $fee_type ] = ucfirst( $fee_type );
				}
			}
		}
		if ( $this->is_column_profit ) {
			$this->order_columns['profit']         = __( 'Profit', 'cost-of-goods-for-woocommerce' );
		}
		if ( $this->is_column_profit_percent ) {
			$this->order_columns['profit_percent'] = __( 'Profit percent', 'cost-of-goods-for-woocommerce' );
		}
		if ( $this->is_column_profit_margin ) {
			$this->order_columns['profit_margin']  = __( 'Profit margin', 'cost-of-goods-for-woocommerce' );
		}
		return alg_wc_cog_insert_in_array( $columns, $this->order_columns, 'order_total' );
	}

	/**
	 * render_order_columns.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 * @todo    [later] order status for the fee columns
	 * @todo    [later] forecasted profit `$value = $line_total * $average_profit_margin`
	 * @todo    [maybe] `if ( 0 != ( $cost = wc_get_order_item_meta( $item_id, '_alg_wc_cog_item_cost' ) ) || 0 != ( $cost = alg_wc_cog()->core->products->get_product_cost( $product_id ) ) ) {`
	 * @todo    [maybe] `if ( $order->get_prices_include_tax() ) { $line_total = $item['line_total'] + $item['line_tax']; }`
	 */
	function render_order_columns( $column, $order_id ) {
		if ( in_array( $column, array_keys( $this->order_columns ) ) ) {
			$order_status = ( isset( $this->column_order_status[ $column ] ) ?  $this->column_order_status[ $column ] : array() );
			if ( ! empty( $order_status ) && ( ! ( $order = wc_get_order( $order_id ) ) || ! $order->has_status( $order_status ) ) ) {
				return;
			}
			$key   = $this->get_order_column_key( $column );
			$value = get_post_meta( $order_id, $key, true );
			echo ( '' !== $value ? $this->format_order_column_value( $value, $column ) : '' );
		}
	}

	/**
	 * format_order_column_value.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function format_order_column_value( $value, $column ) {
		return ( in_array( $column, array( 'profit_percent', 'profit_margin' ) ) ? sprintf( '%0.2f%%', $value ) : wc_price( $value ) );
	}

	/**
	 * get_order_column_key.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function get_order_column_key( $column ) {
		return ( in_array( $column, array( 'cost', 'profit', 'profit_percent', 'profit_margin' ) ) ? '_alg_wc_cog_order_' . $column : '_alg_wc_cog_order_' . $column . '_fee' );
	}

	/**
	 * update_order_items_costs_save_post.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 */
	function update_order_items_costs_save_post( $post_id, $post, $update ) {
		if ( $this->do_force_on_order_update && $update ) {
			$this->update_order_items_costs( $post_id, true, true );
		}
	}

	/**
	 * update_order_items_costs_order_status_changed.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 */
	function update_order_items_costs_order_status_changed( $order_id ) {
		if ( $this->do_force_on_status ) {
			$this->update_order_items_costs( $order_id, true, true );
		}
	}

	/**
	 * update_order_items_costs_new_item.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 */
	function update_order_items_costs_new_item( $item_id, $item, $order_id ) {
		if ( $this->do_force_on_new_item ) {
			$this->update_order_items_costs( $order_id, true, true );
		}
	}

	/**
	 * save_cost_input_shop_order_save_post.
	 *
	 * @version 1.7.1
	 * @since   1.1.0
	 */
	function save_cost_input_shop_order_save_post( $post_id, $post, $update ) {
		$this->update_order_items_costs( $post_id, false );
	}

	/**
	 * save_cost_input_shop_order_new.
	 *
	 * @version 1.1.0
	 * @since   1.1.0
	 */
	function save_cost_input_shop_order_new( $post_id ) {
		$this->update_order_items_costs( $post_id, true );
	}

	/**
	 * save_cost_input_shop_order_new_by_order.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 * @todo    [maybe] merge this with `save_cost_input_shop_order_new()`?
	 */
	function save_cost_input_shop_order_new_by_order( $order ) {
		if ( is_a( $order, 'WC_Order' ) ) {
			$this->update_order_items_costs( $order->get_id(), true );
		}
	}

	/**
	 * recalculate_order_ajax.
	 *
	 * @version 2.1.0
	 * @since   1.4.3
	 * @todo    [maybe] save *and* fill in
	 */
	function recalculate_order_ajax( $order_id, $items ) {
		if (
			'no' != $this->recalculate_order_button_ajax &&
			defined( 'DOING_AJAX' ) && DOING_AJAX &&
			check_ajax_referer( 'calc-totals', 'security', false ) &&
			isset( $_POST['action'] ) && 'woocommerce_calc_line_taxes' === $_POST['action'] &&
			current_user_can( 'edit_shop_orders' )
		) {
			switch ( $this->recalculate_order_button_ajax ) {
				case 'save':
					$this->update_order_items_costs( $order_id, false, false, $items );
					break;
				case 'all':
					$this->update_order_items_costs( $order_id, true, false );
					break;
				default: // 'yes'
					$this->update_order_items_costs( $order_id, true, true );
					break;
			}
		}
	}

	/**
	 * save_order_items_ajax.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	function save_order_items_ajax( $order_id, $items ) {
		if (
			$this->is_save_order_items_ajax &&
			defined( 'DOING_AJAX' ) && DOING_AJAX &&
			check_ajax_referer( 'order-item', 'security', false ) &&
			isset( $_POST['action'] ) && 'woocommerce_save_order_items' === $_POST['action'] &&
			current_user_can( 'edit_shop_orders' )
		) {
			$this->update_order_items_costs( $order_id, false, false, $items );
		}
	}

	/**
	 * new_order_item_ajax.
	 *
	 * @version 2.1.0
	 * @since   1.4.2
	 * @todo    [next] costs are reset on "Add item(s)" - fix that...
	 */
	function new_order_item_ajax( $item_id, $item, $order_id ) {
		if (
			$this->is_add_item_ajax &&
			defined( 'DOING_AJAX' ) && DOING_AJAX &&
			'WC_Order_Item_Product' === get_class( $item ) &&
			'' === wc_get_order_item_meta( $item_id, '_alg_wc_cog_item_cost' ) &&
			( $product_id = ( ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'] ) ) &&
			( $cost = alg_wc_cog()->core->products->get_product_cost( $product_id ) )
		) {
			wc_update_order_item_meta( $item_id, '_alg_wc_cog_item_cost', $cost );
		}
	}

	/**
	 * hide_cost_input_meta_shop_order.
	 *
	 * @version 1.4.5
	 * @since   1.1.0
	 */
	function hide_cost_input_meta_shop_order( $meta_keys ) {
		if ( 'meta' != $this->item_costs_option ) {
			$meta_keys[] = '_alg_wc_cog_item_cost';
		}
		return $meta_keys;
	}

	/**
	 * add_cost_input_shop_order.
	 *
	 * @version 2.2.0
	 * @since   1.1.0
	 */
	function add_cost_input_shop_order( $item_id, $item, $product ) {
		if ( in_array( $this->item_costs_option, array( 'yes', 'readonly' ) ) && 'WC_Order_Item_Product' === get_class( $item ) ) {
			$order    = $item->get_order();
			$readonly = ( 'readonly' === $this->item_costs_option ? ' readonly' : '' );
			echo '<p>' .
				'<label for="alg_wc_cog_item_cost_' . $item_id . '">' . __( 'Cost of goods', 'cost-of-goods-for-woocommerce' ) .
					' (' . get_woocommerce_currency_symbol() . ') ' . '</label>' .
				'<input name="alg_wc_cog_item_cost[' . $item_id . ']" id="alg_wc_cog_item_cost_' . $item_id . '" type="text" class="short wc_input_price" value="' .
					wc_get_order_item_meta( $item_id, '_alg_wc_cog_item_cost' ). '"' . $readonly . '>' .
			'</p>';
		}
	}

	/**
	 * get_order_total_for_pecentage_fees.
	 *
	 * @version 2.2.0
	 * @since   1.7.2
	 * @todo    [next] add more options, e.g. `subtotal_incl_tax`
	 * @todo    [next] optionally set different "order total" for different fees, e.g. shipping total for shipping method fees
	 */
	function get_order_total_for_pecentage_fees( $order ) {
		switch ( $this->order_extra_cost_percent_total ) {
			case 'total_excl_tax':
				return apply_filters( 'alg_wc_cog_order_total_for_pecentage_fees', ( $order->get_total() - $order->get_total_tax() ), $order );
			case 'total_incl_tax':
				return apply_filters( 'alg_wc_cog_order_total_for_pecentage_fees', $order->get_total(), $order );
			default: // 'subtotal_excl_tax'
				return apply_filters( 'alg_wc_cog_order_total_for_pecentage_fees', $order->get_subtotal(), $order );
		}
	}

	/**
	 * update_order_items_costs.
	 *
	 * @version 2.2.0
	 * @since   1.1.0
	 * @todo    [maybe] filters: add more?
	 * @todo    [maybe] `$total_price`: customizable calculation method (e.g. `$order->get_subtotal()`) (this will affect `_alg_wc_cog_order_profit_margin`)
	 * @todo    [maybe] split into smaller functions, e.g. `update_order_fees()`
	 * @todo    [maybe] Fees: From Meta: convert value (e.g. `_stripe_currency`)
	 * @todo    [maybe] Fees: From Meta: check for matching `$order_gateway` value (e.g. `stripe`)
	 * @todo    [maybe] all extra costs: **per order item**
	 * @todo    [recheck] `$do_update  = ( 0 != $cost );`
	 */
	function update_order_items_costs( $order_id, $is_new_order, $is_no_costs_only = false, $posted = false ) {
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		// Order items
		$items_cost            = 0;
		// Fees: Extra shipping method costs
		$shipping_cost         = 0;
		$shipping_cost_fixed   = 0;
		$shipping_cost_percent = 0;
		// Fees: Extra payment gateway costs
		$gateway_cost          = 0;
		$gateway_cost_fixed    = 0;
		$gateway_cost_percent  = 0;
		// Fees: Order extra cost: all orders
		$extra_cost            = 0;
		$extra_cost_fixed      = 0;
		$extra_cost_percent    = 0;
		// Fees: Order extra cost: per order
		$per_order_fees        = 0;
		// Fees: Order extra cost: from meta (e.g. PayPal, Stripe etc.)
		$meta_fees             = 0;
		// Totals
		$profit                = 0;
		$total_cost            = 0;
		$fees                  = 0;
		$total_price           = 0;

		// Calculations
		if ( empty( $this->delay_calculations_status ) || $order->has_status( $this->delay_calculations_status ) ) {
			// Order items
			$posted = ( $posted ? $posted : $_POST );
			foreach ( $order->get_items() as $item_id => $item ) {
				if ( $is_new_order ) {
					if ( ! $is_no_costs_only || '' === wc_get_order_item_meta( $item_id, '_alg_wc_cog_item_cost' ) ) {
						$product_id = ( ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'] );
						$cost       = apply_filters( 'alg_wc_cog_order_item_cost',
							alg_wc_cog()->core->products->get_product_cost( $product_id ), $order, $product_id );
						$do_update  = ( 0 != $cost );
					} else {
						$do_update  = false;
					}
				} else {
					$cost       = ( isset( $posted['alg_wc_cog_item_cost'][ $item_id ] ) ? wc_clean( $posted['alg_wc_cog_item_cost'][ $item_id ] ) : false );
					$do_update  = ( isset( $posted['alg_wc_cog_item_cost'][ $item_id ] ) );
				}
				if ( $do_update ) {
					wc_update_order_item_meta( $item_id, '_alg_wc_cog_item_cost', $cost );
				} else {
					$cost = wc_get_order_item_meta( $item_id, '_alg_wc_cog_item_cost' );
				}
				if ( $this->order_count_empty_costs && ! $cost ) {
					$cost = '0';
				}
				if ( '' != $cost ) {
					$cost         = str_replace( ',', '.', $cost );
					$line_cost    = $cost * $item['qty'];
					$line_total   = apply_filters( 'alg_wc_cog_order_line_total', $item['line_total'], $order );
					$profit      += ( $line_total - $line_cost );
					$items_cost  += $line_cost;
					$total_price += $line_total;
				}
			}
			if ( 0 != $items_cost ) {
				$total_cost += $items_cost;
			}
			// Fees: Extra shipping method costs
			if ( $this->is_shipping_costs_enabled && method_exists( $order, 'get_shipping_methods' ) ) {
				$shipping_methods = $order->get_shipping_methods();
				foreach ( $shipping_methods as $shipping_method ) {
					$shipping_method_id = ( $this->shipping_use_instances ? $shipping_method->get_instance_id() : $shipping_method->get_method_id() );
					// Fixed
					if ( ! empty( $this->shipping_costs_fixed[ $shipping_method_id ] ) ) {
						$shipping_cost_fixed += apply_filters( 'alg_wc_cog_order_shipping_cost_fixed',
							$this->shipping_costs_fixed[ $shipping_method_id ], $order, $shipping_method_id );
					}
					// Percent
					if ( ! empty( $this->shipping_costs_percent[ $shipping_method_id ] ) ) {
						if ( ! isset( $order_total ) ) {
							$order_total = $this->get_order_total_for_pecentage_fees( $order );
						}
						$shipping_cost_percent += apply_filters( 'alg_wc_cog_order_shipping_cost_percent',
							( $order_total * ( $this->shipping_costs_percent[ $shipping_method_id ] / 100 ) ), $order, $shipping_method_id );
					}
				}
				$shipping_cost = ( $shipping_cost_fixed + $shipping_cost_percent );
				$profit     -= $shipping_cost;
				$total_cost += $shipping_cost;
				$fees       += $shipping_cost;
			}
			// Fees: Extra payment gateway costs
			if ( $this->is_gateway_costs_enabled && method_exists( $order, 'get_payment_method' ) ) {
				$order_gateway = $order->get_payment_method();
				// Fixed
				if ( ! empty( $this->gateway_costs_fixed[ $order_gateway ] ) ) {
					$gateway_cost_fixed = apply_filters( 'alg_wc_cog_order_gateway_cost_fixed',
						$this->gateway_costs_fixed[ $order_gateway ], $order, $order_gateway );
				}
				// Percent
				if ( ! empty( $this->gateway_costs_percent[ $order_gateway ] ) ) {
					if ( ! isset( $order_total ) ) {
						$order_total = $this->get_order_total_for_pecentage_fees( $order );
					}
					$gateway_cost_percent = apply_filters( 'alg_wc_cog_order_gateway_cost_percent',
						( $order_total * ( $this->gateway_costs_percent[ $order_gateway ] / 100 ) ), $order, $order_gateway );
				}
				$gateway_cost = ( $gateway_cost_fixed + $gateway_cost_percent );
				$profit     -= $gateway_cost;
				$total_cost += $gateway_cost;
				$fees       += $gateway_cost;
			}
			// Fees: Order extra cost: all orders
			if ( 0 != $this->order_extra_cost_fixed || 0 != $this->order_extra_cost_percent ) {
				// Order extra cost: all orders: fixed
				if ( 0 != $this->order_extra_cost_fixed ) {
					$extra_cost_fixed = apply_filters( 'alg_wc_cog_order_extra_cost_fixed',
						$this->order_extra_cost_fixed, $order );
				}
				// Order extra cost: all orders: percent
				if ( 0 != $this->order_extra_cost_percent ) {
					if ( ! isset( $order_total ) ) {
						$order_total = $this->get_order_total_for_pecentage_fees( $order );
					}
					$extra_cost_percent = apply_filters( 'alg_wc_cog_order_extra_cost_percent',
						( $order_total * ( $this->order_extra_cost_percent / 100 ) ), $order );
				}
				$extra_cost = ( $extra_cost_fixed + $extra_cost_percent );
				$profit     -= $extra_cost;
				$total_cost += $extra_cost;
				$fees       += $extra_cost;
			}
			// Fees: Order extra cost: per order
			foreach ( $this->is_order_extra_cost_per_order as $fee_type => $is_enabled ) {
				if ( $is_enabled && 0 != ( $fee = get_post_meta( $order_id, '_alg_wc_cog_order_' . $fee_type . '_fee', true ) ) ) {
					$per_order_fees += $fee;
				}
			}
			if ( 0 != $per_order_fees ) {
				$profit     -= $per_order_fees;
				$total_cost += $per_order_fees;
				$fees       += $per_order_fees;
			}
			// Fees: Order extra cost: from meta (e.g. PayPal, Stripe etc.)
			if ( '' != $this->order_extra_cost_from_meta ) {
				foreach ( $this->order_extra_cost_from_meta as $meta_key ) {
					$fee        = get_post_meta( $order_id, $meta_key, true );
					$meta_fees += apply_filters( 'alg_wc_cog_order_extra_cost_from_meta', floatval( $fee ), $order );
				}
				if ( 0 != $meta_fees ) {
					$profit     -= $meta_fees;
					$total_cost += $meta_fees;
					$fees       += $meta_fees;
				}
			}
			// Profit adjustments: Shipping
			if ( $this->do_add_shipping_to_profit ) {
				$_shipping    = apply_filters( 'alg_wc_cog_order_shipping_total', $order->get_shipping_total(), $order );
				$profit      += $_shipping;
				$total_price += $_shipping;
			}
			// Profit adjustments: Fees
			if ( $this->do_add_fees_to_profit ) {
				$_fees        = apply_filters( 'alg_wc_cog_order_total_fees', $order->get_total_fees(), $order );
				$profit      += $_fees;
				$total_price += $_fees;
			}
		}

		// Order items
		update_post_meta( $order_id, '_alg_wc_cog_order_items_cost',                $items_cost );
		// Fees: Extra shipping method costs
		update_post_meta( $order_id, '_alg_wc_cog_order_shipping_cost',             $shipping_cost );
		update_post_meta( $order_id, '_alg_wc_cog_order_shipping_cost_fixed',       $shipping_cost_fixed );
		update_post_meta( $order_id, '_alg_wc_cog_order_shipping_cost_percent',     $shipping_cost_percent );
		// Fees: Extra payment gateway costs
		update_post_meta( $order_id, '_alg_wc_cog_order_gateway_cost',              $gateway_cost );
		update_post_meta( $order_id, '_alg_wc_cog_order_gateway_cost_fixed',        $gateway_cost_fixed );
		update_post_meta( $order_id, '_alg_wc_cog_order_gateway_cost_percent',      $gateway_cost_percent );
		// Fees: Order extra cost: all orders
		update_post_meta( $order_id, '_alg_wc_cog_order_extra_cost',                $extra_cost );
		update_post_meta( $order_id, '_alg_wc_cog_order_extra_cost_fixed',          $extra_cost_fixed );
		update_post_meta( $order_id, '_alg_wc_cog_order_extra_cost_percent',        $extra_cost_percent );
		// Fees: Order extra cost: per order
		update_post_meta( $order_id, '_alg_wc_cog_order_extra_cost_per_order',      $per_order_fees );
		foreach ( $this->is_order_extra_cost_per_order as $fee_type => $is_enabled ) {
			if ( ! $is_enabled || '' === get_post_meta( $order_id, '_alg_wc_cog_order_' . $fee_type . '_fee', true ) ) {
				update_post_meta( $order_id, '_alg_wc_cog_order_' . $fee_type . '_fee', 0 );
			}
		}
		// Fees: Order extra cost: from meta (e.g. PayPal, Stripe etc.)
		update_post_meta( $order_id, '_alg_wc_cog_order_extra_cost_from_meta',      $meta_fees );
		// Totals
		update_post_meta( $order_id, '_alg_wc_cog_order_profit',                    $profit );
		update_post_meta( $order_id, '_alg_wc_cog_order_profit_percent',            ( 0 != $total_cost  ? ( $profit / $total_cost  * 100 ) : 0 ) );
		update_post_meta( $order_id, '_alg_wc_cog_order_profit_margin',             ( 0 != $total_price ? ( $profit / $total_price * 100 ) : 0 ) );
		update_post_meta( $order_id, '_alg_wc_cog_order_price',                     $total_price );
		update_post_meta( $order_id, '_alg_wc_cog_order_cost',                      $total_cost );
		update_post_meta( $order_id, '_alg_wc_cog_order_fees',                      $fees );

	}

}

endif;

return new Alg_WC_Cost_of_Goods_Orders();
