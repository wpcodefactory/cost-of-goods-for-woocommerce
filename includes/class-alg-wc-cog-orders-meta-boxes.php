<?php
/**
 * Cost of Goods for WooCommerce - Orders Meta Boxes Class
 *
 * @version 2.4.0
 * @since   2.2.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Orders_Meta_Boxes' ) ) :

class Alg_WC_Cost_of_Goods_Orders_Meta_Boxes {

	/**
	 * Constructor.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function __construct() {
		// Order meta box
		add_action( 'add_meta_boxes',       array( $this, 'add_order_meta_box' ) );
		// Order extra cost: per order
		add_action( 'add_meta_boxes',       array( $this, 'add_order_extra_cost_meta_box' ) );
		add_action( 'save_post_shop_order', array( $this, 'save_order_extra_cost' ), 10, 2 );
	}

	/**
	 * add_order_meta_box.
	 *
	 * @version 2.3.4
	 * @since   1.4.0
	 */
	function add_order_meta_box() {
		if ( ! apply_filters( 'alg_wc_cog_create_order_meta_box_validation', true ) ) {
			return;
		}
		if ( alg_wc_cog()->core->orders->is_order_meta_box ) {
			add_meta_box( 'alg-wc-cog',
				__( 'Cost of Goods', 'cost-of-goods-for-woocommerce' ),
				array( $this, 'render_order_meta_box' ),
				'shop_order',
				'side'
			);
		}
	}

	/**
	 * render_order_meta_box.
	 *
	 * @version 2.4.0
	 * @since   1.4.0
	 * @todo    [maybe] order total
	 */
	function render_order_meta_box( $post ) {
		$order_id            = get_the_ID();
		$cost                = get_post_meta( $order_id, '_alg_wc_cog_order_' . 'cost',   true );
		$profit              = get_post_meta( $order_id, '_alg_wc_cog_order_' . 'profit', true );
		$profit_percent      = get_post_meta( $order_id, '_alg_wc_cog_order_' . 'profit_percent', true );
		$profit_margin       = get_post_meta( $order_id, '_alg_wc_cog_order_' . 'profit_margin',  true );
		$profit_template     = get_option( 'alg_wc_cog_orders_profit_html_template', '%profit%' );
		$profit_placeholders = array(
			'%profit%'         => alg_wc_cog()->core->orders->format_order_column_value( $profit,         'profit' ),
			'%profit_percent%' => alg_wc_cog()->core->orders->format_order_column_value( $profit_percent, 'profit_percent' ),
			'%profit_margin%'  => alg_wc_cog()->core->orders->format_order_column_value( $profit_margin,  'profit_margin' ),
		);
		$profit_html         = str_replace( array_keys( $profit_placeholders ), $profit_placeholders, $profit_template );
		$table_args          = array( 'table_heading_type' => 'vertical', 'table_class' => 'widefat', 'columns_styles' => array( '', 'text-align:right;' ) );
		$table_data          = array(
			array( __( 'Cost', 'cost-of-goods-for-woocommerce' ),   ( '' !== $cost   ? '<span style="color:red;">'   . alg_wc_cog_format_cost( $cost ) . '</span>' : '' ) ),
			array( __( 'Profit', 'cost-of-goods-for-woocommerce' ), ( '' !== $profit ? '<span style="color:green;">' . $profit_html      . '</span>' : '' ) ),
		);
		echo alg_wc_cog_get_table_html( $table_data, $table_args );
		// Cost details
		$table_data     = array();
		$cost_meta_keys = array(
			'_alg_wc_cog_order_items_cost'              => __( 'Item costs', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_shipping_cost_fixed'     => __( 'Shipping method fee (fixed)', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_shipping_cost_percent'   => __( 'Shipping method fee (percent)', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_gateway_cost_fixed'      => __( 'Gateway fee (fixed)', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_gateway_cost_percent'    => __( 'Gateway fee (percent)', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_extra_cost_fixed'        => __( 'Order fee (fixed)', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_extra_cost_percent'      => __( 'Order fee (percent)', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_' . 'handling' . '_fee'  => __( 'Handling fee', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_' . 'shipping' . '_fee'  => __( 'Shipping fee', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_' . 'payment' . '_fee'   => __( 'Payment fee', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_extra_cost_from_meta'    => __( 'Meta fees', 'cost-of-goods-for-woocommerce' ),
		);
		foreach ( $cost_meta_keys as $key => $value ) {
			$cost = get_post_meta( $order_id, $key, true );
			if ( 0 != $cost ) {
				$table_data[] = array( $value, alg_wc_cog_format_cost( $cost ) );
			}
		}
		if ( count( $table_data ) > 1 ) {
			echo '<h5>' . __( 'Cost details', 'cost-of-goods-for-woocommerce' ) . '</h5>';
			echo alg_wc_cog_get_table_html( $table_data, $table_args );
		}
	}

	/**
	 * add_order_extra_cost_meta_box.
	 *
	 * @version 2.3.4
	 * @since   1.7.0
	 */
	function add_order_extra_cost_meta_box() {
		if ( ! apply_filters( 'alg_wc_cog_create_order_meta_box_validation', true, 'extra_cost' ) ) {
			return;
		}
		if ( in_array( true, alg_wc_cog()->core->orders->is_order_extra_cost_per_order ) ) {
			add_meta_box( 'alg-wc-cog-extra-cost',
				__( 'Cost of Goods', 'cost-of-goods-for-woocommerce' ) . ': ' . __( 'Extra costs', 'cost-of-goods-for-woocommerce' ),
				array( $this, 'render_order_extra_cost_meta_box' ),
				'shop_order',
				'side'
			);
		}
	}

	/**
	 * render_order_extra_cost_meta_box.
	 *
	 * @version 2.3.0
	 * @since   1.7.0
	 * @todo    [maybe] better `$title`
	 * @todo    [maybe] better styling
	 * @todo    [maybe] better/customizable `step`
	 */
	function render_order_extra_cost_meta_box( $post ) {
		$order = wc_get_order( get_the_ID() );
		$rows  = '';
		foreach ( alg_wc_cog()->core->orders->is_order_extra_cost_per_order as $fee_type => $is_enabled ) {
			if ( $is_enabled ) {
				$id    = 'alg_wc_cog_order_' . $fee_type . '_fee';
				$title = ucfirst( $fee_type ) . ' ' . __( 'fee', 'cost-of-goods-for-woocommerce' ) . ' (' . alg_wc_cog()->core->get_default_shop_currency_symbol() . ')';
				$value = get_post_meta( get_the_ID(), '_' . $id, true );
				$rows .= '<tr><td><label style="font-size:smaller;" for="' . $id . '">' . $title . '</label></td>' .
					'<td><input name="' . $id . '" id="' . $id . '" type="number" step="0.0001" class="short wc_input_price" value="' . $value . '"></td></tr>';
			}
		}
		echo '<table class="widefat striped"><tbody>' . $rows . '</tbody></table>';
	}

	/**
	 * save_order_extra_cost.
	 *
	 * @version 2.2.0
	 * @since   1.7.0
	 */
	function save_order_extra_cost( $order_id, $post ) {
		if ( in_array( true, alg_wc_cog()->core->orders->is_order_extra_cost_per_order ) ) {
			foreach ( alg_wc_cog()->core->orders->is_order_extra_cost_per_order as $fee_type => $is_enabled ) {
				if ( $is_enabled ) {
					$id = 'alg_wc_cog_order_' . $fee_type . '_fee';
					if ( isset( $_POST[ $id ] ) ) {
						$value = floatval( $_POST[ $id ] );
						update_post_meta( $order_id, '_' . $id, $value );
					}
				}
			}
		}
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Orders_Meta_Boxes();
