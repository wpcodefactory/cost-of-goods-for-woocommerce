<?php
/**
 * Cost of Goods for WooCommerce - Tools Section Settings
 *
 * @version 2.3.1
 * @since   1.4.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Settings_Tools' ) ) :

class Alg_WC_Cost_of_Goods_Settings_Tools extends Alg_WC_Cost_of_Goods_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.7.0
	 * @since   1.4.0
	 * @see     "WooCommerce > Settings > Cost of Goods > Tools & Reports"
	 */
	function __construct() {
		$this->id   = 'tools';
		$this->desc = __( 'Tools & Reports', 'cost-of-goods-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.3.1
	 * @since   1.4.0
	 * @todo    [later] better descriptions
	 * @todo    [maybe] add "PHP time limit" option, i.e. `set_time_limit()`
	 * @todo    [maybe] Orders report: Extra data: better description
	 * @todo    [maybe] PHP memory limit: better description
	 */
	function get_settings() {

		$tools_settings = array(
			array(
				'title'    => __( 'Product Bulk Edit Costs Tool', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => sprintf( __( 'Bulk Edit tool is in %s.', 'cost-of-goods-for-woocommerce' ),
					'<a href="' . admin_url( 'tools.php?page=bulk-edit-costs' ) . '">' . __( 'Tools > Bulk Edit Costs', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
				'id'       => 'alg_wc_cog_bulk_edit_tool_options',
			),
			array(
				'title'    => __( 'Search products', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'id'       => 'alg_wc_cog_bulk_edit_tool_search_method',
				'default'  => 'title',
				'options'  => array(
					'title' => __( 'Search by title', 'cost-of-goods-for-woocommerce' ),
					'all'   => __( 'Search all', 'cost-of-goods-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Edit prices', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Enable', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_cog_bulk_edit_tool_edit_prices',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Manage stock', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Enable', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_cog_bulk_edit_tool_manage_stock',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Stock update method', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Ignored unless "Manage stock" checkbox is enabled above.', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'id'       => 'alg_wc_cog_bulk_edit_tool_manage_stock_method',
				'default'  => 'meta',
				'options'  => array(
					'meta' => __( 'Update product meta', 'cost-of-goods-for-woocommerce' ),
					'func' => __( 'Use product functions', 'cost-of-goods-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Product types', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Leave empty to display all product types.', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'id'       => 'alg_wc_cog_bulk_edit_tool_product_types',
				'default'  => array(),
				'options'  => array_merge( wc_get_product_types(), array( 'variation' => __( 'Variations', 'woocommerce' ) ) ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_bulk_edit_tool_options',
			),
			array(
				'title'    => __( 'Product Import Costs Tool', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => sprintf( __( 'Import tool is in %s.', 'cost-of-goods-for-woocommerce' ),
					'<a href="' . admin_url( 'tools.php?page=import-costs' ) . '">' . __( 'Tools > Import Costs', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
				'id'       => 'alg_wc_cog_import_tool_options',
			),
			array(
				'title'    => __( 'Key to import from', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'text',
				'id'       => 'alg_wc_cog_tool_key',
				'default'  => '_wc_cog_cost',
			),
			array(
				'title'    => __( 'Display table', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Enable', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'If you have problems accessing the "Import Costs" page try to disable this option.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_import_tool_display_table',
				'default'  => 'no',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_import_tool_options',
			),
			array(
				'title'    => __( 'Orders Tools', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_orders_tools_options',
			),
			array(
				'title'    => __( 'Recalculate orders cost and profit', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Recalculate for all orders', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Set items costs in all orders (overriding previous costs).', 'cost-of-goods-for-woocommerce' ) . ' ' .
					__( 'Enable the checkbox and "Save changes" to run the tool.', 'cost-of-goods-for-woocommerce' ) .
					apply_filters( 'alg_wc_cog_settings', '<br>' . sprintf( 'You will need %s plugin to use this tool.',
						'<a target="_blank" href="https://wpfactory.com/item/cost-of-goods-for-woocommerce/">' .
							'Cost of Goods for WooCommerce Pro' . '</a>' ) ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_cog_recalculate_orders_cost_and_profit_all',
				'default'  => 'no',
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'desc'     => __( 'Recalculate for orders with no costs', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Set items costs in orders that do not have costs set.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					__( 'Enable the checkbox and "Save changes" to run the tool.', 'cost-of-goods-for-woocommerce' ) .
					apply_filters( 'alg_wc_cog_settings', '<br>' . sprintf( 'You will need %s plugin to use this tool.',
						'<a target="_blank" href="https://wpfactory.com/item/cost-of-goods-for-woocommerce/">' .
							'Cost of Goods for WooCommerce Pro' . '</a>' ) ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_cog_recalculate_orders_cost_and_profit_no_costs',
				'default'  => 'no',
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'PHP memory limit', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'megabytes', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Will set PHP memory limit right before tools are run.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					__( 'Ignored if set to zero.', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'number',
				'id'       => 'alg_wc_cog_recalculate_orders_cost_and_profit_memory_limit',
				'default'  => 0,
				'custom_attributes' => array( 'min' => 0 ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_orders_tools_options',
			),
		);

		$reports_settings = array(
			array(
				'title'    => __( 'Reports', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_reports_options',
				'desc'     => apply_filters( 'alg_wc_cog_settings', '<em>' . sprintf( 'You will need %s plugin to add the reports to "%s" and "%s".',
						'<a target="_blank" href="https://wpfactory.com/item/cost-of-goods-for-woocommerce/">' .
							'Cost of Goods for WooCommerce Pro' . '</a>',
						__( 'Reports > Orders > Cost of Goods', 'cost-of-goods-for-woocommerce' ),
						__( 'Reports > Stock > Cost of Goods', 'cost-of-goods-for-woocommerce' )
					) . '</em>', 'report' ),
			),
			array(
				'title'    => __( 'Orders report', 'cost-of-goods-for-woocommerce' ) . ': ' . __( 'Order status', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Select order statuses for the "Orders > Cost of Goods" report.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					__( '"Refunded" status is added automatically where applicable.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					__( 'If left empty then default value ("Completed", "Processing", "On hold") is used.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_report_orders_order_status',
				'default'  => array( 'completed', 'processing', 'on-hold' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => array_diff_key( $this->get_order_statuses(), array_flip( array( 'refunded', 'cancelled', 'failed' ) ) ),
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'Orders report', 'cost-of-goods-for-woocommerce' ) . ': ' . __( 'Extra data', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( "To display data gathered before the plugin v2.0.0, you will need to recalculate orders cost and profit.", 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_report_orders_extra_fields',
				'default'  => array(),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => array(
					'_alg_wc_cog_order_items_cost'             => __( 'Item costs (excluding fees)', 'cost-of-goods-for-woocommerce' ),
					'_alg_wc_cog_order_fees'                   => __( 'Fees (all)', 'cost-of-goods-for-woocommerce' ),
					'_alg_wc_cog_order_shipping_cost'          => __( 'Shipping method fees (all)', 'cost-of-goods-for-woocommerce' ),
					'_alg_wc_cog_order_shipping_cost_fixed'    => __( 'Shipping method fees (fixed)', 'cost-of-goods-for-woocommerce' ),
					'_alg_wc_cog_order_shipping_cost_percent'  => __( 'Shipping method fees (percent)', 'cost-of-goods-for-woocommerce' ),
					'_alg_wc_cog_order_gateway_cost'           => __( 'Gateway fees (all)', 'cost-of-goods-for-woocommerce' ),
					'_alg_wc_cog_order_gateway_cost_fixed'     => __( 'Gateway fees (fixed)', 'cost-of-goods-for-woocommerce' ),
					'_alg_wc_cog_order_gateway_cost_percent'   => __( 'Gateway fees (percent)', 'cost-of-goods-for-woocommerce' ),
					'_alg_wc_cog_order_extra_cost'             => __( 'Order fees (all)', 'cost-of-goods-for-woocommerce' ),
					'_alg_wc_cog_order_extra_cost_fixed'       => __( 'Order fees (fixed)', 'cost-of-goods-for-woocommerce' ),
					'_alg_wc_cog_order_extra_cost_percent'     => __( 'Order fees (percent)', 'cost-of-goods-for-woocommerce' ),
					'_alg_wc_cog_order_extra_cost_per_order'   => __( 'Per order fees (all)', 'cost-of-goods-for-woocommerce' ),
					'_alg_wc_cog_order_' . 'handling' . '_fee' => __( 'Per order fees: Handling', 'cost-of-goods-for-woocommerce' ),
					'_alg_wc_cog_order_' . 'shipping' . '_fee' => __( 'Per order fees: Shipping', 'cost-of-goods-for-woocommerce' ),
					'_alg_wc_cog_order_' . 'payment' . '_fee'  => __( 'Per order fees: Payment', 'cost-of-goods-for-woocommerce' ),
					'_alg_wc_cog_order_extra_cost_from_meta'   => __( 'Meta fees (all)', 'cost-of-goods-for-woocommerce' ),
				),
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'Stock report', 'cost-of-goods-for-woocommerce' ) . ': ' . __( 'Get price method', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'The mechanism used to get the product price.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_report_stock_price_method',
				'default'  => array( 'default' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'default'                          => __( 'Function', 'cost-of-goods-for-woocommerce' ),
					'excluding_tax_with_price_from_db' => __( 'Function with meta', 'cost-of-goods-for-woocommerce' ),
				),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_reports_options',
			),
		);

		$analytics_settings = array(
			array(
				'title'    => __( 'Analytics', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_analytics_options',
			),
			array(
				'title'    => __( 'Orders', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Enable', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Will add "Cost" and "Profit" columns to the "Analytics > Orders" report.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_analytics_orders',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_analytics_options',
			),
		);

		return array_merge(
			$tools_settings,
			$reports_settings,
			$analytics_settings
		);
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Settings_Tools();
