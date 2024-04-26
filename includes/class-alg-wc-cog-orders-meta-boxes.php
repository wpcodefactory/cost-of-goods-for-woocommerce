<?php
/**
 * Cost of Goods for WooCommerce - Orders Meta Boxes Class.
 *
 * @version 3.3.7
 * @since   2.2.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Orders_Meta_Boxes' ) ) :

class Alg_WC_Cost_of_Goods_Orders_Meta_Boxes {

	protected $order_cost_values = array();

	/**
	 * Constructor.
	 *
	 * @version 3.3.6
	 * @since   2.2.0
	 */
	function __construct() {
		// Order meta box
		add_action( 'add_meta_boxes',       array( $this, 'add_order_meta_box' ) );
		// Order extra cost: per order
		add_action( 'add_meta_boxes',       array( $this, 'add_order_extra_cost_meta_box' ) );
		add_action( 'save_post_shop_order', array( $this, 'save_order_extra_cost' ), 8, 2 );
		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save_order_extra_cost' ), 8, 2 );

		// Manual order cost.
		add_filter( 'alg_wc_cog_order_cost', array( $this, 'save_order_cost_manually' ) );
		add_filter( 'alg_wc_cog_order_profit', array( $this, 'calculate_profit_from_manual_cost' ) );
		add_filter( 'alg_wc_cog_order_metabox_cost_value_html', array( $this, 'replace_order_metabox_cost_by_input' ), 10, 2 );
	}

	/**
	 * replace_cost_from_order_metabox_by_input.
	 *
	 * @version 2.8.8
	 * @since   2.8.8
	 *
	 * @param $cost_value_html
	 * @param $cost
	 *
	 * @return string
	 */
	function replace_order_metabox_cost_by_input( $cost_value_html, $cost ) {
		if ( 'yes' === get_option( 'alg_wc_cog_edit_order_cost_manually', 'no' ) ) {
			$cost_value_html = '<input style="color:red;" type="number" step="0.000001" name="alg_wc_cog_order_cost_input" id="" value="' . esc_attr( $cost ) . '"/>';
		}
		return $cost_value_html;
	}

	/**
	 * calculate_profit_from_manual_cost.
	 *
	 * @version 2.8.8
	 * @since   2.8.8
	 *
	 * @param $profit
	 *
	 * @return mixed
	 */
	function calculate_profit_from_manual_cost( $profit ) {
		if (
			'yes' === get_option( 'alg_wc_cog_edit_order_cost_manually', 'no' ) &&
			! empty( $this->order_cost_values ) &&
			isset( $this->order_cost_values['automatic'] ) &&
			$this->order_cost_values['manual']
		) {
			$cost_difference = $this->order_cost_values['automatic'] - $this->order_cost_values['manual'];
			$profit          += $cost_difference;
		}
		return $profit;
	}

	/**
	 * save_order_cost_manually.
	 *
	 * @version 2.8.8
	 * @since   2.8.8
	 *
	 * @param $order_cost
	 *
	 * @return float
	 */
	function save_order_cost_manually( $order_cost ) {
		if (
			'yes' === get_option( 'alg_wc_cog_edit_order_cost_manually', 'no' ) &&
			isset( $_POST['alg_wc_cog_order_cost_input'] ) &&
			'' !== ( $order_cost_input = $_POST['alg_wc_cog_order_cost_input'] ) &&
			(float) $order_cost !== (float) $order_cost_input
		) {
			$this->order_cost_values['automatic'] = $order_cost;
			$this->order_cost_values['manual']    = (float) $order_cost_input;
			$order_cost = (float) $order_cost_input;
		}
		return $order_cost;
	}

	/**
	 * add_order_meta_box.
	 *
	 * @version 3.0.2
	 * @since   1.4.0
	 */
	function add_order_meta_box() {
		if ( ! apply_filters( 'alg_wc_cog_create_order_meta_box_validation', true ) ) {
			return;
		}
		$screen = alg_wc_cog()->core->orders->get_shop_order_screen_id();
		if ( alg_wc_cog()->core->orders->is_order_meta_box ) {
			add_meta_box( 'alg-wc-cog',
				__( 'Cost of Goods', 'cost-of-goods-for-woocommerce' ),
				array( $this, 'render_order_meta_box' ),
				$screen,
				'side',
				'high'
			);
		}
	}

	/**
	 * render_order_meta_box.
	 *
	 * @version 3.3.7
	 * @since   1.4.0
	 * @todo    [maybe] order total
	 */
	function render_order_meta_box( $post ) {
		$order_id = ! empty( $post->ID ) ? $post->ID : get_the_ID();
		$order    = wc_get_order( $order_id );
		if ( ! is_a( $order, 'WC_Order' ) ) {
			return;
		}
		$cost                = $order->get_meta( '_alg_wc_cog_order_' . 'cost', true );
		$cost                = apply_filters( 'alc_wc_cog_order_metabox_value', $cost, array( 'invert' => true, 'order' => $order ) );
		$handling_fee        = $order->get_meta( '_alg_wc_cog_order_' . 'handling_fee', true );
		$profit              = $order->get_meta( '_alg_wc_cog_order_' . 'profit', true );
		$profit              = apply_filters( 'alc_wc_cog_order_metabox_value', $profit, array( 'invert' => true, 'order' => $order ) );
		$profit_percent      = $order->get_meta( '_alg_wc_cog_order_' . 'profit_percent', true );
		$profit_margin       = $order->get_meta( '_alg_wc_cog_order_' . 'profit_margin', true );
		$profit_template     = get_option( 'alg_wc_cog_orders_profit_html_template', '%profit%' );
		$profit_placeholders = array(
			'%profit%'         => alg_wc_cog()->core->orders->format_order_column_value( $profit, 'profit', apply_filters( 'alc_wc_cog_order_metabox_value_format_args', array(), array( 'order' => $order ) ) ),
			'%profit_percent%' => alg_wc_cog()->core->orders->format_order_column_value( $profit_percent, 'profit_percent' ),
			'%profit_margin%'  => alg_wc_cog()->core->orders->format_order_column_value( $profit_margin, 'profit_margin' ),
		);
		$profit_html         = str_replace( array_keys( $profit_placeholders ), $profit_placeholders, $profit_template );
		$table_args          = array( 'table_heading_type' => 'vertical', 'table_class' => 'widefat', 'columns_styles' => array( '', 'text-align:right;' ) );
		$cost_html           = apply_filters( 'alg_wc_cog_order_metabox_cost_value_html', alg_wc_cog_format_cost( $cost, apply_filters( 'alc_wc_cog_order_metabox_value_format_args', array(), array( 'order' => $order ) ) ), $cost, $order_id );
		$table_data          = array(
			array( __( 'Cost', 'cost-of-goods-for-woocommerce' ),   ( '' !== $cost   ? '<span style="color:red;">'   . $cost_html . '</span>' : '' ) ),
			array( __( 'Profit', 'cost-of-goods-for-woocommerce' ), ( '' !== $profit ? '<span style="color:green;">' . $profit_html      . '</span>' : '' ) ),
		);
		echo alg_wc_cog_get_table_html( $table_data, $table_args );
		// Cost details
		$table_data     = array();
		$cost_meta_keys = array(
			'_alg_wc_cog_order_items_cost'                    => __( 'Item costs', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_items_handling_fee'            => __( 'Item handling fees', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_shipping_cost_fixed'           => __( 'Shipping method fee (fixed)', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_shipping_cost_percent'         => __( 'Shipping method fee (percent)', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_shipping_classes_cost_fixed'   => __( 'Shipping class fee (fixed)', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_shipping_classes_cost_percent' => __( 'Shipping class fee (percent)', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_gateway_cost_fixed'            => __( 'Gateway fee (fixed)', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_gateway_cost_percent'          => __( 'Gateway fee (percent)', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_extra_cost_fixed'              => __( 'Order fee (fixed)', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_extra_cost_percent'            => __( 'Order fee (percent)', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_' . 'handling' . '_fee'        => __( 'Handling fee', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_' . 'shipping' . '_fee'        => __( 'Shipping fee', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_' . 'payment' . '_fee'         => __( 'Payment fee', 'cost-of-goods-for-woocommerce' ),
			'_alg_wc_cog_order_extra_cost_from_meta'          => __( 'Meta fees', 'cost-of-goods-for-woocommerce' ),
		);
		$cost_meta_keys = apply_filters( 'alg_wc_cog_cost_meta_keys', $cost_meta_keys );
		foreach ( $cost_meta_keys as $key => $value ) {
			$cost = $order->get_meta( $key, true );
			$cost = apply_filters( 'alc_wc_cog_order_metabox_value', $cost, array( 'invert' => true, 'order' => $order ) );
			if ( ! empty( $cost ) && 0 != $cost ) {
				$table_data[] = array( $value, alg_wc_cog_format_cost( $cost, apply_filters( 'alc_wc_cog_order_metabox_value_format_args', array(), array( 'order' => $order ) ) ) );
			}
		}
		if ( count( $table_data ) > 0 ) {
			echo '<h5>' . __( 'Cost details', 'cost-of-goods-for-woocommerce' ) . '</h5>';
			echo alg_wc_cog_get_table_html( $table_data, $table_args );
		}
		// Extra profit details.
		$table_data             = array();
		$extra_profit_meta_keys = array(); // Example: '_alg_wc_cog_order_shipping_extra_profit'] = __( 'Shipping to profit', 'cost-of-goods-for-woocommerce' );
		$extra_profit_meta_keys = apply_filters( 'alg_wc_cog_extra_profit_meta_keys', $extra_profit_meta_keys );
		foreach ( $extra_profit_meta_keys as $key => $value ) {
			$cost = $order->get_meta( $key, true );
			$cost = apply_filters( 'alc_wc_cog_order_metabox_value', $cost, array( 'invert' => true, 'order' => $order ) );
			if ( 0 != $cost && ! empty( $cost ) ) {
				$table_data[] = array( $value, alg_wc_cog_format_cost( $cost, apply_filters( 'alc_wc_cog_order_metabox_value_format_args', array(), array( 'order' => $order ) ) ) );
			}
		}
		if ( count( $table_data ) > 0 ) {
			echo '<h5>' . __( 'Extra profit details', 'cost-of-goods-for-woocommerce' ) . '</h5>';
			echo alg_wc_cog_get_table_html( $table_data, $table_args );
		}
	}

	/**
	 * add_order_extra_cost_meta_box.
	 *
	 * @version 3.0.2
	 * @since   1.7.0
	 */
	function add_order_extra_cost_meta_box() {
		if ( ! apply_filters( 'alg_wc_cog_create_order_meta_box_validation', true, 'extra_cost' ) ) {
			return;
		}
		if ( in_array( true, alg_wc_cog()->core->orders->is_order_extra_cost_per_order ) ) {
			$screen = alg_wc_cog()->core->orders->get_shop_order_screen_id();
			add_meta_box( 'alg-wc-cog-extra-cost',
				__( 'Cost of Goods', 'cost-of-goods-for-woocommerce' ) . ': ' . __( 'Extra costs', 'cost-of-goods-for-woocommerce' ),
				array( $this, 'render_order_extra_cost_meta_box' ),
				$screen,
				'side',
				'high'
			);
		}
	}

	/**
	 * render_order_extra_cost_meta_box.
	 *
	 * @version 3.3.6
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
				$value = $order->get_meta( '_' . $id, true );
				$rows  .= '<tr><td><label style="font-size:smaller;" for="' . $id . '">' . $title . '</label></td>' .
				          '<td><input name="' . $id . '" id="' . $id . '" type="number" step="0.0001" class="short wc_input_price" value="' . $value . '"></td></tr>';
			}
		}
		echo '<table class="widefat striped"><tbody>' . $rows . '</tbody></table>';
	}

	/**
	 * save_order_extra_cost.
	 *
	 * @version 3.3.6
	 * @since   1.7.0
	 */
	function save_order_extra_cost( $order_id, $post ) {
		if ( in_array( true, alg_wc_cog()->core->orders->is_order_extra_cost_per_order ) ) {
			remove_action( 'woocommerce_process_shop_order_meta', array( $this, 'save_order_extra_cost' ), 8 );
			remove_action( 'save_post_shop_order', array( $this, 'save_order_extra_cost' ), 8 );
			foreach ( alg_wc_cog()->core->orders->is_order_extra_cost_per_order as $fee_type => $is_enabled ) {
				if ( $is_enabled ) {
					$id = 'alg_wc_cog_order_' . $fee_type . '_fee';
					if ( isset( $_POST[ $id ] ) ) {
						$value = floatval( $_POST[ $id ] );
						$order = wc_get_order( $order_id );
						$order->update_meta_data( '_' . $id, $value );
						$order->save();
					}
				}
			}
			add_action( 'save_post_shop_order', array( $this, 'save_order_extra_cost' ), 8, 2 );
			add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save_order_extra_cost' ), 8, 2 );
		}
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Orders_Meta_Boxes();
