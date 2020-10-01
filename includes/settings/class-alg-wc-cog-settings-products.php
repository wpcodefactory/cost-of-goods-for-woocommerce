<?php
/**
 * Cost of Goods for WooCommerce - Products Section Settings
 *
 * @version 2.1.1
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
	 * @version 2.1.1
	 * @since   1.7.0
	 * @todo    [later] Cost field label: use in quick and bulk edit
	 * @todo    [later] `alg_wc_cog_products_add_stock`: better description
	 */
	function get_settings() {

		$product_columns_settings = array(
			array(
				'title'    => __( 'Admin Products List Columns', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => sprintf( __( 'This section lets you add custom columns to WooCommerce admin %s.', 'cost-of-goods-for-woocommerce' ),
					'<a href="' . admin_url( 'edit.php?post_type=product' ) . '">' . __( 'products list', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
				'id'       => 'alg_wc_cog_products_columns_options',
			),
			array(
				'title'    => __( 'Product cost', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Add', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_products_columns_cost',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Product profit', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Add', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_products_columns_profit',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_products_columns_options',
			),
		);

		$product_settings = array(
			array(
				'title'    => __( 'General Options', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_products_options',
			),
			array(
				'title'    => __( 'Cost field label', 'cost-of-goods-for-woocommerce' ),
				'desc'     => sprintf( __( 'Available placeholders: %s.', 'cost-of-goods-for-woocommerce' ),
					'<code>' . implode( '</code>, <code>', array( '%currency_symbol%' ) ) . '</code>' ),
				'desc_tip' => __( 'This is used in admin single product edit pages.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_product_cost_field_template',
				'default'  => sprintf( __( 'Cost (excl. tax) (%s)', 'cost-of-goods-for-woocommerce' ), '%currency_symbol%' ),
				'type'     => 'text',
			),
			array(
				'title'    => __( 'Product profit HTML template', 'cost-of-goods-for-woocommerce' ),
				'desc'     => sprintf( __( 'Available placeholders: %s.', 'cost-of-goods-for-woocommerce' ),
					'<code>' . implode( '</code>, <code>', array( '%profit%', '%profit_percent%', '%profit_margin%' ) ) . '</code>' ),
				'desc_tip' => __( 'This is used in admin single product edit pages, and in admin products list "Profit" column.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					__( 'Profit percent is "profit / cost". Margin is "profit / price".', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_product_profit_html_template',
				'default'  => '%profit% (%profit_percent%)',
				'type'     => 'text',
			),
			array(
				'title'    => __( 'Add stock', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Enable', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Adds "%s" meta box to the product edit page.', 'cost-of-goods-for-woocommerce' ),
					__( 'Cost of Goods', 'cost-of-goods-for-woocommerce' ) . ': ' . __( 'Add stock', 'cost-of-goods-for-woocommerce' ) ) . '<br>' .
					__( 'This will automatically calculate new average cost of goods for the product, based on new "Stock" and "Cost" values you enter.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_products_add_stock',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_products_options',
			),
		);

		$product_quick_bulk_edit_settings = array(
			array(
				'title'    => __( 'Quick and Bulk Edit', 'cost-of-goods-for-woocommerce' ),
				'desc'     => apply_filters( 'alg_wc_cog_settings', sprintf( 'You will need %s plugin to enable these options.',
					'<a target="_blank" href="https://wpfactory.com/item/cost-of-goods-for-woocommerce/">' . 'Cost of Goods for WooCommerce Pro' . '</a>' ) ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_products_quick_bulk_edit_options',
			),
			array(
				'title'    => __( 'Quick edit', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Enable', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Adds "Cost" field to product "%s".', 'cost-of-goods-for-woocommerce' ),
					__( 'Quick Edit', 'cost-of-goods-for-woocommerce' ) ),
				'id'       => 'alg_wc_cog_products_quick_edit',
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'Bulk edit', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Enable', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Adds "Cost" field to product "%s".', 'cost-of-goods-for-woocommerce' ),
					__( 'Bulk Actions', 'cost-of-goods-for-woocommerce' ) . ' > ' . __( 'Edit', 'cost-of-goods-for-woocommerce' ) ),
				'id'       => 'alg_wc_cog_products_bulk_edit',
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_products_quick_bulk_edit_options',
			),
		);

		return array_merge(
			$product_columns_settings,
			$product_settings,
			$product_quick_bulk_edit_settings
		);
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Settings_Products();
