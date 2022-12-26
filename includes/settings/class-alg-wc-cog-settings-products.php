<?php
/**
 * Cost of Goods for WooCommerce - Products Section Settings.
 *
 * @version 2.8.2
 * @since   1.7.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Settings_Products' ) ) :

class Alg_WC_Cost_of_Goods_Settings_Products extends Alg_WC_Cost_of_Goods_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	function __construct() {
		$this->id   = '';
		$this->desc = __( 'Products', 'cost-of-goods-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.8.2
	 * @since   1.7.0
	 * @todo    [later] Cost field label: use in quick and bulk edit
	 * @todo    [later] `alg_wc_cog_products_add_stock`: better description
	 */
	function get_settings() {
		$product_settings = array(
			array(
				'title'    => __( 'General product options', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_products_options',
			),
			array(
				'title'          => __( 'Profit HTML template', 'cost-of-goods-for-woocommerce' ),
				'desc'           => sprintf( __( 'Available placeholders: %s.', 'cost-of-goods-for-woocommerce' ),
					'<code>' . implode( '</code>, <code>', array( '%profit%', '%profit_percent%', '%profit_margin%' ) ) . '</code>' ),
				'desc_tip'       => __( 'This is used in admin single product edit pages, and in admin products list "Profit" column.', 'cost-of-goods-for-woocommerce' ) . ' ' .
				                    __( 'Profit percent is "profit / cost". Margin is "profit / price".', 'cost-of-goods-for-woocommerce' ),
				'id'             => 'alg_wc_cog_product_profit_html_template',
				'default'        => '%profit% (%profit_percent%)',
				'type'           => 'text',
				'wpfse_data' => array(
					'description' => __( 'Customizes how the profit will be displayed.', 'cost-of-goods-for-woocommerce' ) . ' ' . '{desc}'
				)
			),
			array(
				'title'    => __( 'Get price method', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'When using this option including tax, it will make sense to enable the option %s.', 'cost-of-goods-for-woocommerce' ), '"' . __( 'Orders > Calculations > Taxes to profit', 'cost-of-goods-for-woocommerce' ) . '"' ),
				'id'       => 'alg_wc_cog_products_get_price_method',
				'default'  => 'wc_get_price_excluding_tax',
				'type'     => 'select',
				'options'  => array(
					'wc_get_price_excluding_tax'   => __( 'Get price excluding tax', 'cost-of-goods-for-woocommerce' ),
					'wc_get_price_including_tax'   => __( 'Get price including tax', 'cost-of-goods-for-woocommerce' ),
					'alg_wc_cog_get_regular_price' => __( 'Get regular price', 'cost-of-goods-for-woocommerce' ),
				),
				'wpfse_data' => array(
					'description' => __( 'Get price excluding or including tax.', 'cost-of-goods-for-woocommerce' ) . ' ' . '{desc}'
				),
				'class'    => 'chosen_select',
			),
			array(
				'title'    => __( 'Cost decimals', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Number of decimal points shown in displayed costs.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_costs_decimals',
				'default'  => wc_get_price_decimals(),
				'type'     => 'number',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_products_options',
			),
		);

		$cost_input_settings = array(
			array(
				'title' => __( 'Cost field options', 'cost-of-goods-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_wc_cog_cost_field_options',
			),
			array(
				'title'          => __( 'Cost field label', 'cost-of-goods-for-woocommerce' ),
				'desc'           => sprintf( __( 'Available placeholders: %s.', 'cost-of-goods-for-woocommerce' ),
					'<code>' . implode( '</code>, <code>', array( '%currency_symbol%' ) ) . '</code>' ),
				'desc_tip'       => __( 'Customizes the cost field input label added to admin product pages.', 'cost-of-goods-for-woocommerce' ),
				'id'             => 'alg_wc_cog_product_cost_field_template',
				'default'        => sprintf( __( 'Cost (excl. tax) (%s)', 'cost-of-goods-for-woocommerce' ), '%currency_symbol%' ),
				'type'           => 'text',
				'wpfse_data' => array(
					'description' => '{desc_tip}'
				),
				'wpfse_data' => array(
					'desc' => '{{desc_tip}}'
				)
			),
			array(
				'title'    => __( 'Cost field position', 'ean-for-woocommerce' ),
				'desc_tip' => __( 'Manages where the Cost field will be displayed on the product edit page.', 'ean-for-woocommerce' ),
				'id'       => 'alg_wc_cog_product_cost_field_position',
				'default'  => 'woocommerce_product_options_pricing',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'woocommerce_product_options_pricing'                => __( 'General > Pricing', 'ean-for-woocommerce' ),
					'woocommerce_product_options_general_product_data'   => __( 'General', 'ean-for-woocommerce' ),
					'woocommerce_product_options_inventory_product_data' => __( 'Inventory', 'ean-for-woocommerce' ),
					'woocommerce_product_options_sku'                    => __( 'Inventory > SKU', 'ean-for-woocommerce' ),
					'woocommerce_product_options_advanced'               => __( 'Advanced', 'ean-for-woocommerce' ),
				),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_cost_field_options',
			),
		);

		$product_columns_settings = array(
			array(
				'title'    => __( 'Admin products list columns', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => sprintf( __( 'This section lets you add custom columns to WooCommerce admin %s.', 'cost-of-goods-for-woocommerce' ),
					'<a href="' . admin_url( 'edit.php?post_type=product' ) . '">' . __( 'products list', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
				'id'       => 'alg_wc_cog_products_columns_options',
			),
			array(
				'title'    => __( 'Product cost', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Add product cost column', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_products_columns_cost',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => sprintf( __( 'Column width (%s)', 'cost-of-goods-for-woocommerce' ), get_option( 'alg_wc_cog_products_columns_width_unit', '%' ) ),
				'desc_tip' => __( 'Zero or empty values will disable width.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_products_columns_cost_width',
				'default'  => '10',
				'type'     => 'number',
				'wpfse_data' => array(
					'description' => __( 'Customize the product cost column width', 'cost-of-goods-for-woocommerce' )
				),
			),
			array(
				'title'    => __( 'Product profit', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Add product profit column', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_products_columns_profit',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => sprintf( __( 'Column width (%s)', 'cost-of-goods-for-woocommerce' ), get_option( 'alg_wc_cog_products_columns_width_unit', '%' ) ),
				'desc_tip' => __( 'Zero or empty values will disable width.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_products_columns_profit_width',
				'default'  => '11',
				'type'     => 'number',
				'wpfse_data' => array(
					'description' => __( 'Customize the product profit column width', 'cost-of-goods-for-woocommerce' )
				),
			),
			array(
				'title'    => __( 'Width unit', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Any CSS unit can be used, like px, %, ch, and so on...', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_products_columns_width_unit',
				'default'  => '%',
				'type'     => 'text',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_products_columns_options',
			),
		);

		$cost_archive_opts = array(
			array(
				'title' => __( 'Cost archive', 'cost-of-goods-for-woocommerce' ),
				'desc'  => __( 'Save a history of product costs each time they get updated.', 'cost-of-goods-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_wc_cog_cost_archive_options',
			),
			array(
				'title'          => __( 'Cost archive', 'cost-of-goods-for-woocommerce' ),
				'desc'           => __( 'Save cost archive', 'cost-of-goods-for-woocommerce' ),
				'id'             => 'alg_wc_cog_save_cost_archive',
				'default'        => 'no',
				'type'           => 'checkbox',
			),
			array(
				'title'          => __( 'Meta box', 'cost-of-goods-for-woocommerce' ),
				'desc'           => __( 'Enable a cost archive meta box on admin product page', 'cost-of-goods-for-woocommerce' ),
				'id'             => 'alg_wc_cog_cost_archive_metabox',
				'default'        => 'no',
				'type'           => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_cost_archive_options',
			),
		);

		$add_stock_settings = array(
			array(
				'title' => __( 'Add stock', 'cost-of-goods-for-woocommerce' ),
				'desc'  => __( 'This will automatically calculate new average cost of goods for the product, based on new "Stock" and "Cost" values you enter.', 'cost-of-goods-for-woocommerce' ) . '<br />' .
				           __( '"Stock" will be added to your inventory, and "Cost" will be used to calculate new average cost of goods for the product.', 'cost-of-goods-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_wc_cog_add_stock_options',
			),
			array(
				'title'    => __( 'Add stock', 'cost-of-goods-for-woocommerce' ),
				'desc'     => sprintf( __( 'Add "%s" meta box to the product edit page', 'cost-of-goods-for-woocommerce' ),
					__( 'Cost of Goods', 'cost-of-goods-for-woocommerce' ) . ': ' . __( 'Add stock', 'cost-of-goods-for-woocommerce' ) ),
				'id'       => 'alg_wc_cog_products_add_stock',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'             => __( 'Cost calculation expression', 'cost-of-goods-for-woocommerce' ),
				'desc'              => __( 'Available placeholders: ', 'cost-of-goods-for-woocommerce' ) .
				                       alg_wc_cog_array_to_string( array(
					                       'stock_prev',
					                       'cost_prev',
					                       'stock',
					                       'cost',
					                       'stock_now'
				                       ), array( 'item_template' => '<code>%{value}%</code>' ) ),
				'desc_tip'          => __( 'The expression used to calculate the new average cost of the product.', 'cost-of-goods-for-woocommerce' ),
				'id'                => 'alg_wc_cog_products_add_stock_cost_calculation',
				'default'           => '( %stock_prev% * %cost_prev% + %stock% * %cost% ) / %stock_now%',
				'type'              => 'text',
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				'wpfse_data'    => array(
					'description' => '{desc_tip}'
				)
			),
			array(
				'title'             => __( 'Empty cost field', 'cost-of-goods-for-woocommerce' ),
				'desc'              => __( 'The cost value considered when the field is empty', 'cost-of-goods-for-woocommerce' ),
				'desc_tip'          => __( 'Use this option if you want to Add stock without worrying about filling the cost value.', 'cost-of-goods-for-woocommerce' ),
				'id'                => 'alg_wc_cog_products_add_stock_empty_cost_action',
				'default'           => 'do_nothing',
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				'type'              => 'select',
				'class'             => 'chosen_select',
				'options' => array(
					'do_nothing'       => __( 'Prevents calculation', 'cost-of-goods-for-woocommerce' ),
					'use_last_cost'    => __( 'Uses last cost value from "Add stock" history', 'cost-of-goods-for-woocommerce' ),
					'use_current_cost' => __( 'Uses current cost', 'cost-of-goods-for-woocommerce' ),
				)
			),
			array(
				'title'    => __( 'Negative stock', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Allow negative stock values', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'If enabled, the stock may also be reduced.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_products_add_stock_negative_stock',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'alg_wc_cog_add_stock_options',
			),
		);

		$product_quick_bulk_edit_settings = array(
			array(
				'title'    => __( 'Quick and Bulk Edit', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_products_quick_bulk_edit_options',
			),
			array(
				'title'    => __( 'Cost field', 'cost-of-goods-for-woocommerce' ),
				'desc' => sprintf( __( 'Add "Cost" field to product "%s"', 'cost-of-goods-for-woocommerce' ),
					__( 'Quick Edit', 'cost-of-goods-for-woocommerce' ) ),
				'id'       => 'alg_wc_cog_products_quick_edit',
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup'     => 'start',
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'desc' => sprintf( __( 'Add "Cost" field to product "%s"', 'cost-of-goods-for-woocommerce' ),
					__( 'Bulk Actions', 'cost-of-goods-for-woocommerce' ) . ' > ' . __( 'Edit', 'cost-of-goods-for-woocommerce' ) ),
				'id'       => 'alg_wc_cog_products_bulk_edit',
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup'     => '',
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'desc'          => __( 'Replace all variations from the main variable product', 'cost-of-goods-for-woocommerce' ),
				'desc_tip'      => __( 'The cost field from the main variable product will replace the cost of all variations.', 'cost-of-goods-for-woocommerce' ),
				'id'            => 'alg_wc_cog_products_quick_edit_replace_variations',
				'default'       => 'no',
				'checkboxgroup' => 'end',
				'type'          => 'checkbox'
			),
			array(
				'title'    => __( '"Add stock" fields', 'cost-of-goods-for-woocommerce' ),
				'desc' => sprintf( __( 'Add "Add stock" fields to product "%s"', 'cost-of-goods-for-woocommerce' ),
					__( 'Quick Edit', 'cost-of-goods-for-woocommerce' ) ),
				'id'       => 'alg_wc_cog_products_add_stock_quick_edit',
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup'     => 'start',
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'desc' => sprintf( __( 'Add "Add stock" fields to product "%s"', 'cost-of-goods-for-woocommerce' ),
					__( 'Bulk Actions', 'cost-of-goods-for-woocommerce' ) . ' > ' . __( 'Edit', 'cost-of-goods-for-woocommerce' ) ),
				'id'       => 'alg_wc_cog_products_add_stock_bulk_edit',
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup'     => '',
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_products_quick_bulk_edit_options',
			),
		);

		$cost_sanitization_opts = array(
			array(
				'title' => __( 'Cost sanitization', 'cost-of-goods-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_wc_cog_cost_sanitization_options',
			),
			array(
				'title'    => __( 'Cost update', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Replace comma by dots when updating cost meta', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_products_sanitize_cog_meta',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Cost import', 'cost-of-goods-for-woocommerce' ),
				'desc'     => sprintf( __( 'Get only the cost number when using the %s', 'cost-of-goods-for-woocommerce' ), '<a href="' . admin_url( 'edit.php?post_type=product&page=product_importer' ) . '">' . __( 'WooCommerce Importer', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
				'desc_tip' => __( 'Useful if you want to ignore price symbols from the CSV when importing.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_import_csv_get_only_cost_number',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'alg_wc_cog_cost_sanitization_options',
			),
		);

		return array_merge(
			$product_settings,
			$cost_input_settings,
			$product_columns_settings,
			$cost_archive_opts,
			$add_stock_settings,
			$product_quick_bulk_edit_settings,
			$cost_sanitization_opts
		);
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Settings_Products();
