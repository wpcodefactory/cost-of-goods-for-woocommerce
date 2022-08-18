<?php
/**
 * Cost of Goods for WooCommerce - Shipping classes Settings.
 *
 * @version 2.6.2
 * @since   2.4.3
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Settings_Shipping_Classes' ) ) :

	class Alg_WC_Cost_of_Goods_Settings_Shipping_Classes extends Alg_WC_Cost_of_Goods_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 2.4.3
		 * @since   2.4.3
		 */
		function __construct() {
			$this->id   = 'shipping_classes';
			$this->desc = __( 'Shipping classes', 'cost-of-goods-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * get_settings.
		 *
		 * @version 2.6.2
		 * @since   2.4.3
		 * @todo    [maybe] better section desc (same for `$order_extra_cost_settings` and "Shipping"): how to recalculate order's profit/cost (i.e. update order or use tool)
		 */
		function get_settings() {
			$settings = array(
				array(
					'title'    => __( 'Extra shipping classes costs', 'cost-of-goods-for-woocommerce' ),
					'desc'     => __( "Here you can add extra costs for your orders based on product's shipping classes.", 'cost-of-goods-for-woocommerce' ) . ' ' .
					              __( "You will need to recalculate order's cost and profit after you change these settings.", 'cost-of-goods-for-woocommerce' ),
					'type'     => 'title',
					'id'       => 'alg_wc_cog_shipping_classes',
				),
				array(
					'title'             => __( 'Extra shipping classes costs', 'cost-of-goods-for-woocommerce' ),
					'desc'              => '<strong>' . __( 'Enable section', 'cost-of-goods-for-woocommerce' ) . '</strong>',
					'type'              => 'checkbox',
					'id'                => 'alg_wc_cog_shipping_classes_enabled',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
					'default'           => 'no',
				),
				array(
					'title'             => __( 'Fixed cost calculation', 'cost-of-goods-for-woocommerce' ),
					'desc_tip'          => __( '"Per product" calculates for each different product having a shipping class.', 'cost-of-goods-for-woocommerce' ) . ' ' . __( '"Per shipping class" calculates for each different shipping class.', 'cost-of-goods-for-woocommerce' ),
					'type'              => 'select',
					'class'             => 'chosen_select',
					'id'                => 'alg_wc_cog_shipping_classes_fixed_cost_calculation',
					'default'           => 'per_product',
					'options'           => array(
						'per_product'        => __( 'Per product', 'cost-of-goods-for-woocommerce' ),
						'per_shipping_class' => __( 'Per shipping class', 'cost-of-goods-for-woocommerce' ),
					)
				),
				array(
					'type'     => 'sectionend',
					'id'       => 'alg_wc_cog_shipping_classes',
				),
			);
			$shipping_classes = wp_list_pluck(get_terms( array('taxonomy' => 'product_shipping_class', 'hide_empty' => false ) ),'name','term_id');
			foreach ( $shipping_classes as $key => $shipping_class ) {
				$settings = array_merge( $settings, array(
					array(
						'title'    => $shipping_class,
						'type'     => 'title',
						'id'       => 'alg_wc_cog_shipping_class_' . $key,
					),
					array(
						'title'             => __( 'Fixed cost', 'cost-of-goods-for-woocommerce' ),
						'desc_tip'          => sprintf( __( 'In %s.', 'cost-of-goods-for-woocommerce' ), alg_wc_cog()->core->get_default_shop_currency() ),
						'type'              => 'number',
						'id'                => "alg_wc_cog_shipping_class_costs_fixed[{$key}]",
						'default'           => 0,
						'custom_attributes' => array( 'step' => '0.000001' ),
						'wpfse_data'    => array(
							'description' => ''
						)
					),
					array(
						'title'    => __( 'Percent cost', 'cost-of-goods-for-woocommerce' ),
						'desc_tip' => __( 'Percent from product total. E.g.: If you want <code>50%</code> from product total you can set it as 50.', 'cost-of-goods-for-woocommerce' ),
						'type'     => 'number',
						'id'       => "alg_wc_cog_shipping_class_costs_percent[{$key}]",
						'default'  => 0,
						'custom_attributes' => array( 'step' => '0.000001' ),
					),
					array(
						'type'     => 'sectionend',
						'id'       => 'alg_wc_cog_shipping_class_' . $key,
					),
				) );
			}
			return $settings;
		}

	}

endif;

return new Alg_WC_Cost_of_Goods_Settings_Shipping_Classes();
