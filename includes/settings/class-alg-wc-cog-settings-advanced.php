<?php
/**
 * Cost of Goods for WooCommerce - Advanced Section Settings
 *
 * @version 1.7.0
 * @since   1.7.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Settings_Advanced' ) ) :

class Alg_WC_Cost_of_Goods_Settings_Advanced extends Alg_WC_Cost_of_Goods_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	function __construct() {
		$this->id   = 'advanced';
		$this->desc = __( 'Advanced', 'cost-of-goods-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 * @todo    [dev] "Force costs update on ...": better title and desc (3x)
	 */
	function get_settings() {

		$advanced_settings = array(
			array(
				'title'    => __( 'Force Costs Update', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_advanced_force_costs_update_options',
			),
			array(
				'title'    => __( 'Force costs update on order update', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Force empty order items cost update on each order update.', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Enable', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_force_on_update',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Force costs update on order status change', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Force empty order items cost update on order status change.', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Enable', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_force_on_status',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Force costs update on new order item', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Force empty order items cost update on new order item addition.', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Enable', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_force_on_new_item',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_advanced_force_costs_update_options',
			),
			array(
				'title'    => __( 'Columns Sorting', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_advanced_columns_sorting_options',
			),
			array(
				'title'    => __( 'Sortable columns', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Enable', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Makes columns added to admin %s and %s lists <strong>sortable</strong>.', 'cost-of-goods-for-woocommerce' ),
					'<a href="' . admin_url( 'edit.php?post_type=product' )    . '">' . __( 'products', 'cost-of-goods-for-woocommerce' ) . '</a>',
					'<a href="' . admin_url( 'edit.php?post_type=shop_order' ) . '">' . __( 'orders', 'cost-of-goods-for-woocommerce' )   . '</a>' ),
				'id'       => 'alg_wc_cog_columns_sorting',
				'default'  => 'yes',
				'type'     => 'checkbox',
				'checkboxgroup' => 'start',
			),
			array(
				'desc'     => __( 'Exclude empty lines on sorting', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_columns_sorting_exclude_empty_lines',
				'default'  => 'yes',
				'type'     => 'checkbox',
				'checkboxgroup' => 'end',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_advanced_columns_sorting_options',
			),
		);

		return $advanced_settings;
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Settings_Advanced();
