<?php
/**
 * Cost of Goods for WooCommerce - Orders Section Settings
 *
 * @version 2.5.4
 * @since   1.7.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Settings_Orders' ) ) :

class Alg_WC_Cost_of_Goods_Settings_Orders extends Alg_WC_Cost_of_Goods_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	function __construct() {
		$this->id   = 'orders';
		$this->desc = __( 'Orders', 'cost-of-goods-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.5.4
	 * @since   1.7.0
	 * @todo    [later] `alg_wc_cog_order_prepopulate_in_ajax`: remove (i.e. always enabled)
	 * @todo    [later] `alg_wc_cog_order_save_items_ajax`: remove (i.e. always enabled)
	 * @todo    [maybe] `alg_wc_cog_order_prepopulate_on_recalculate_order`: default to `yes`
	 * @todo    [docs] "Extra Costs: From Meta": better description
	 * @todo    [docs] "Extra Cost: Per Order": better description?
	 */
	function get_settings() {

		$order_columns_settings = array(
			array(
				'title'    => __( 'Admin Orders List Columns', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => sprintf( __( 'This section lets you add custom columns to the WooCommerce admin %s.', 'cost-of-goods-for-woocommerce' ),
					'<a href="' . admin_url( 'edit.php?post_type=shop_order' ) . '">' . __( 'orders list', 'cost-of-goods-for-woocommerce' ) . '</a>' ) . '<br>' .
					sprintf( __( 'Please note: to display %s and %s for orders created before plugin v2.2.0 was installed, you will need to recalculate orders cost and profit.', 'cost-of-goods-for-woocommerce' ),
						'"' . __( 'Profit percent', 'cost-of-goods-for-woocommerce' ) . '"', '"' . __( 'Profit margin', 'cost-of-goods-for-woocommerce' ) . '"' ),
				'id'       => 'alg_wc_cog_orders_columns_options',
			),
			array(
				'title'    => __( 'Order cost', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Add cost column', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_columns_cost',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'Order statuses', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Select order statuses to show cost.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					__( 'Leave empty to show for all orders.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_columns_cost_order_status',
				'default'  => array(),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => $this->get_order_statuses(),
			),
			array(
				'title'    => __( 'Order profit', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Add profit column', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_columns_profit',
				'default'  => 'yes',
				'type'     => 'checkbox',
				'checkboxgroup' => 'start',
			),
			array(
				'desc'     => __( 'Add profit percent column', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_columns_profit_percent',
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup' => '',
			),
			array(
				'desc'     => __( 'Add profit margin column', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_columns_profit_margin',
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup' => 'end',
			),
			array(
				'desc'     => __( 'Order statuses', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Select order statuses to show profit.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					__( 'Leave empty to show for all orders.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_columns_profit_order_status',
				'default'  => array(),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => $this->get_order_statuses(),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_orders_columns_options',
			),
		);

		$order_edit_settings = array(
			array(
				'title'    => __( 'Admin Order Edit', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Options for the admin order edit pages.', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_order_edit_options',
			),
			array(
				'title'    => __( 'Item costs', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Adds costs inputs for each order item to admin order edit page.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_item_costs',
				'default'  => 'yes',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'yes'      => __( 'Enable', 'cost-of-goods-for-woocommerce' ),
					'readonly' => __( 'Readonly', 'cost-of-goods-for-woocommerce' ),
					'no'       => __( 'Disable', 'cost-of-goods-for-woocommerce' ),
					'meta'     => __( 'Disable but show as standard meta', 'cost-of-goods-for-woocommerce' ),
				),				
			),
			array(
				'title'    => __( 'Item handling fees', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Adds handling fees inputs for each order item to admin order edit page.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_item_handling_fees',
				'default'  => 'no',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'no'       => __( 'Disable', 'cost-of-goods-for-woocommerce' ),
					'yes'      => __( 'Enable', 'cost-of-goods-for-woocommerce' ),
					'readonly' => __( 'Readonly', 'cost-of-goods-for-woocommerce' ),
					'meta'     => __( 'Disable but show as standard meta', 'cost-of-goods-for-woocommerce' ),
				),
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'Meta box', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Add "Cost of Goods" meta box to admin order edit page', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_meta_box',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'Order profit HTML template.', 'cost-of-goods-for-woocommerce' ) . ' ' .
				              sprintf( __( 'Available placeholders: %s.', 'cost-of-goods-for-woocommerce' ),
					              '<code>' . implode( '</code>, <code>', array( '%profit%', '%profit_percent%', '%profit_margin%' ) ) . '</code>' ) . '<br>' .
				              sprintf( __( 'Please note: to display %s and %s for orders created before plugin v2.2.0 was installed, you will need to recalculate orders cost and profit.', 'cost-of-goods-for-woocommerce' ),
					              '<code>%profit_percent%</code>', '<code>%profit_margin%</code>' ),
				'desc_tip' => __( 'This is used in meta box.', 'cost-of-goods-for-woocommerce' ) . ' ' .
				              __( 'Profit percent is "profit / cost". Margin is "profit / price".', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_profit_html_template',
				'default'  => '%profit%',
				'type'     => 'text'
			),
			array(
				'title'    => __( 'Admin notice', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Add notice to admin order edit page in case if order profit is below zero', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_admin_notice',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'Admin notice text', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_admin_notice_text',
				'default'  => __( 'You are selling below the cost of goods.', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'text',
				'css'      => 'width:100%;',
			),
			array(
				'title'    => __( 'Fill in on add items', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Fill in item costs with the default costs when adding new items (i.e. "Add item(s) > Add product(s)")', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_order_prepopulate_in_ajax',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Save on item edit', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Save item costs when editing order items (i.e. "Edit item > Save")', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_order_save_items_ajax',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( '"Recalculate" button', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Select what should be done when admin clicks "Recalculate" order button.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_order_prepopulate_on_recalculate_order',
				'default'  => 'no',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'no'   => __( 'Do nothing', 'cost-of-goods-for-woocommerce' ),
					'yes'  => __( 'Fill in empty item costs with the default costs', 'cost-of-goods-for-woocommerce' ),
					'all'  => __( 'Fill in all item costs with the default costs', 'cost-of-goods-for-woocommerce' ),
					'save' => __( 'Save all item costs', 'cost-of-goods-for-woocommerce' ),
				),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_order_edit_options',
			),
		);

		$order_emails_settings = array(
			array(
				'title'    => __( 'Orders emails', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'COG options regarding orders emails.', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_order_emails_options',
			),
			array(
				'title'             => __( 'Admin new order email', 'cost-of-goods-for-woocommerce' ),
				'desc'              => __( 'Display the order cost and profit on the admin new order email', 'cost-of-goods-for-woocommerce' ),
				'id'                => 'alg_wc_cog_order_admin_new_order_email_profit_and_cost',
				'default'           => 'no',
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				'type'              => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_order_emails_options',
			),
		);

		$order_calculation_settings = array(
			array(
				'title'    => __( 'Calculations', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Here you can set some options for order cost and profit calculations.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					__( "You will need to recalculate order's cost and profit after you change these settings.", 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_order_calculation_options',
			),
			array(
				'title'    => __( 'Count empty cost lines', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Count empty cost items when calculating order cost and profit', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_order_count_empty_costs',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Order total for percentage fees', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Total to calculate all extra costs percentage fees from (%s).', 'cost-of-goods-for-woocommerce' ),
					'"' . implode( '", "', array(
						__( 'All Orders', 'cost-of-goods-for-woocommerce' ),
						__( 'Payment Gateways', 'cost-of-goods-for-woocommerce' ),
						__( 'Shipping Methods', 'cost-of-goods-for-woocommerce' ),
					) ) . '"' ),
				'id'       => 'alg_wc_cog_order_extra_cost_percent_total',
				'default'  => 'subtotal_excl_tax',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'subtotal_excl_tax' => __( 'Order subtotal excl. tax', 'cost-of-goods-for-woocommerce' ),
					'total_excl_tax'    => __( 'Order total excl. tax', 'cost-of-goods-for-woocommerce' ),
					'total_incl_tax'    => __( 'Order total incl. tax', 'cost-of-goods-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Shipping to profit', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Add order shipping cost to the order profit', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_order_shipping_to_profit',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Fees to profit', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Add  order fees to the order profit ', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_order_fees_to_profit',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Taxes to profit', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Will probably make more sense if %s option is <strong>including tax</strong>.', 'cost-of-goods-for-woocommerce' ), '<strong>' . __( 'Products > Get price method', 'cost-of-goods-for-woocommerce' ) . '</strong>' ),
				'desc'     => __( 'Adds order taxes like VAT to the order profit.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_order_taxes_to_profit',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Delay calculations', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Select order statuses to delay all order profit, cost etc. calculations until.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					__( 'All values will be set to zero until the required order status is set.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					__( 'Leave empty to calculate right away on new order.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_delay_calculations_status',
				'default'  => array(),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => $this->get_order_statuses(),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_order_calculation_options',
			),
		);

		$refunds_settings = array(
			array(
				'title' => __( 'Refunds', 'cost-of-goods-for-woocommerce' ),
				'desc'  =>
					sprintf(
						__( "It's necessary to add the %s status on %s option in order to see refunded orders on reports.", 'cost-of-goods-for-woocommerce' ),
						'<strong>' . __( 'Refunded', 'cost-of-goods-for-woocommerce' ) . '</strong>',
						'<strong>' . __( 'Tools & reports > Orders report: Order status', 'cost-of-goods-for-woocommerce' ) . '</strong>'
					) . '<br />' .
					sprintf(
						__( "Enable %s options to automatically calculate refund costs.", 'cost-of-goods-for-woocommerce' ),
						'<strong>' . __( 'Advanced > Force costs update', 'cost-of-goods-for-woocommerce' ) . '</strong>'
					) . '<br />' .
					__( "It's necessary to recalculate order's cost and profit after you change these settings.", 'cost-of-goods-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_wc_cog_refund_options',
			),
			array(
				'title'    => __( 'Item quantity', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Calculate quantity by excluding refunded items', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'This will affect both the profit and the cost.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_calculate_qty_excluding_refunds',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Refund calculation', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_order_refund_calculation_method',
				'default'  => 'ignore_refunds',
				'type'     => 'radio',
				'options'  => array(
					'ignore_refunds'                                 => __( 'Profit ignore refunds', 'cost-of-goods-for-woocommerce' ),
					'profit_based_on_total_refunded'                 => __( 'Subtract total refunded from profit', 'cost-of-goods-for-woocommerce' ),
					'profit_and_price_based_on_item_refunded_amount' => __( 'Subtract each item\'s refund amount from profit', 'cost-of-goods-for-woocommerce' ),
					'profit_by_netpayment_and_cost_difference'       => __( 'Calculate profit by the difference between Net Payment and Cost', 'cost-of-goods-for-woocommerce' ),
				)
			),
			array(
				'title'    => __( 'Net Payment inclusive of tax', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Include tax on Net Payment', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Only works with %s.', 'cost-of-goods-for-woocommerce' ), '<strong>' . __( 'Calculate profit by the difference between Net Payment and Cost', 'cost-of-goods-for-woocommerce' ) . '</strong>' ),
				'id'       => 'alg_wc_cog_net_payment_inclusive_of_tax',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_refund_options',
			),
		);

		$order_extra_cost_settings = array(
			array(
				'title'    => __( 'Extra Costs', 'cost-of-goods-for-woocommerce' ) . ': ' . __( 'All Orders', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Here you can add extra costs for your orders, e.g. handling fees.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					__( "You will need to recalculate order's cost and profit after you change these settings.", 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_order_extra_cost_options',
			),
			array(
				'title'             => __( 'Fixed cost', 'cost-of-goods-for-woocommerce' ),
				'desc_tip'          => sprintf( __( 'In %s.', 'cost-of-goods-for-woocommerce' ), alg_wc_cog()->core->get_default_shop_currency() ),
				'type'              => 'number',
				'id'                => 'alg_wc_cog_order_extra_cost_fixed',
				'default'           => 0,
				'custom_attributes' => array( 'step' => '0.000001' ),
				'wpfse_data'    => array(
					'description' => ''
				)
			),
			array(
				'title'    => __( 'Percent cost', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Percent from order total.', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'number',
				'id'       => 'alg_wc_cog_order_extra_cost_percent',
				'default'  => 0,
				'custom_attributes' => array( 'step' => '0.000001' ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_order_extra_cost_options',
			),
		);

		$order_extra_cost_per_order_settings = array(
			array(
				'title'    => __( 'Extra Costs', 'cost-of-goods-for-woocommerce' ) . ': ' . __( 'Per Order', 'cost-of-goods-for-woocommerce' ),
				'desc'     => sprintf( __( 'Adds "%s" meta box to admin order edit page.', 'cost-of-goods-for-woocommerce' ),
						__( 'Cost of Goods', 'cost-of-goods-for-woocommerce' ) . ': ' . __( 'Extra costs', 'cost-of-goods-for-woocommerce' ) ) . ' ' .
					__( "You will need to recalculate order's cost and profit after you change these settings.", 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_order_extra_cost_per_order_options',
			),
			array(
				'title'    => __( 'Per Order', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Handling fee', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_order_extra_cost_per_order_handling_fee',
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup' => 'start',
			),
			array(
				'title'    => __( 'Per Order', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Shipping fee', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_order_extra_cost_per_order_shipping_fee',
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup' => '',
			),
			array(
				'title'    => __( 'Per Order', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Payment fee', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_order_extra_cost_per_order_payment_fee',
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup' => 'end',
			),
			array(
				'title'    => __( 'Columns', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Add', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Will add "%s" columns to the WooCommerce admin %s.', 'cost-of-goods-for-woocommerce' ),
						__( 'Extra Costs', 'cost-of-goods-for-woocommerce' ) . ': ' . __( 'Per Order', 'cost-of-goods-for-woocommerce' ),
						'<a href="' . admin_url( 'edit.php?post_type=shop_order' ) . '">' . __( 'orders list', 'cost-of-goods-for-woocommerce' ) . '</a>' ) . ' ' .
					__( 'One column per fee.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_order_extra_cost_per_order_columns',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_order_extra_cost_per_order_options',
			),
		);

		$order_extra_cost_from_meta_settings = array(
			array(
				'title' => __( 'Extra Costs', 'cost-of-goods-for-woocommerce' ) . ': ' . __( 'From Meta', 'cost-of-goods-for-woocommerce' ),
				'desc'  => __( 'Adds extra costs from order meta.', 'cost-of-goods-for-woocommerce' ) . ' ' .
				           sprintf( __( 'E.g.: %s.', 'cost-of-goods-for-woocommerce' ),
					           implode( ', ', array( 'Stripe: ' . '<code>_stripe_fee</code>', 'PayPal: ' . '<code>PayPal Transaction Fee</code>' ) ) ) . '<br />' .
				           '- ' . sprintf( __( 'You can also use dots to access serialized array metas. E.g.: Get fees from %s:', 'cost-of-goods-for-woocommerce' ), '<a href="https://woocommerce.com/pt-br/products/woocommerce-paypal-payments/" target="_blank">' . __( 'PayPal Payments', 'cost-of-goods-for-woocommerce' ) . '</a>' ) . ' ' . '<code>_ppcp_paypal_fees.paypal_fee.value</code>.' . '<br />' .
				           '- ' . __( "You will need to recalculate order's cost and profit after you change these settings.", 'cost-of-goods-for-woocommerce' ) . '<br />' .
				           '- ' . sprintf( __( "If you have issues, please try to enable the %s options.", 'cost-of-goods-for-woocommerce' ), '<strong>' . __( 'Advanced > Force costs update', 'cost-of-goods-for-woocommerce' ) . '</strong>' ),
				'type'  => 'title',
				'id'    => 'alg_wc_cog_order_extra_cost_from_meta_options',
			),
			array(
				'title'    => __( 'Meta keys', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Ignored if empty.', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'One meta key per line.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_order_extra_cost_from_meta',
				'default'  => '',
				'type'     => 'textarea',
				'css'      => 'height:100px;',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'alg_wc_cog_order_extra_cost_from_meta_options',
			),
		);

		return array_merge(
			$order_columns_settings,
			$order_edit_settings,
			$order_emails_settings,
			$order_calculation_settings,
			$refunds_settings,
			$order_extra_cost_settings,
			$order_extra_cost_per_order_settings,
			$order_extra_cost_from_meta_settings
		);
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Settings_Orders();
