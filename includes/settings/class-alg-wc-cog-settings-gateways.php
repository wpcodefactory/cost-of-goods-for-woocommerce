<?php
/**
 * Cost of Goods for WooCommerce - Gateways Section Settings
 *
 * @version 3.6.7
 * @since   1.5.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Settings_Gateways' ) ) :

class Alg_WC_Cost_of_Goods_Settings_Gateways extends Alg_WC_Cost_of_Goods_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.7.2
	 * @since   1.5.0
	 */
	function __construct() {
		$this->id   = 'gateways';
		$this->desc = __( 'Payment Gateways', 'cost-of-goods-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 3.6.7
	 * @since   1.5.0
	 * @todo    [maybe] better section desc (same for `$order_extra_cost_settings` and "Shipping"): how to recalculate order's profit/cost (i.e. update order or use tool)
	 */
	function get_settings() {
		$settings = array(
			array(
				'title'    => __( 'Payment Gateways', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( "Add extra costs/profit to the orders based on order's payment gateway.", 'cost-of-goods-for-woocommerce' ) . ' ' .
					__( "You will need to recalculate order's cost and profit after you change these settings.", 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_gateways',
			),
			array(
				'title'    => __( 'Payment Gateways', 'cost-of-goods-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable Payment Gateways section', 'cost-of-goods-for-woocommerce' ) . '</strong>',
				'type'     => 'checkbox',
				'id'       => 'alg_wc_cog_gateways_values_enabled',
				'default'  => alg_wc_cog_get_gateways_option_default(),
				'wpfse_data'    => array(
					'hide' => true
				)
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_gateways',
			),
		);
		$gateways = WC()->payment_gateways->payment_gateways();
		foreach ( $gateways as $key => $gateway ) {
			$settings = array_merge( $settings, array(
				array(
					'title'          => $gateway->title,
					'type'           => 'title',
					'id'             => 'alg_wc_cog_gateway_' . $key,
					'wpfse_data' => array(
						'hide' => true
					)
				),
				array(
					'title'             => __( 'Fixed cost', 'cost-of-goods-for-woocommerce' ),
					'desc_tip'          => sprintf( __( 'In %s.', 'cost-of-goods-for-woocommerce' ), alg_wc_cog()->core->get_default_shop_currency() ),
					'type'              => 'number',
					'id'                => "alg_wc_cog_gateway_costs_fixed[{$key}]",
					'default'           => 0,
					'custom_attributes' => array( 'step' => '0.000001' ),
					'wpfse_data'    => array(
						'hide' => true
					)
				),
				array(
					'title'             => __( 'Percent cost', 'cost-of-goods-for-woocommerce' ),
					'desc_tip'          => __( 'Percent from order total.', 'cost-of-goods-for-woocommerce' ),
					'type'              => 'number',
					'id'                => "alg_wc_cog_gateway_costs_percent[{$key}]",
					'default'           => 0,
					'custom_attributes' => array( 'step' => '0.000001' ),
					'wpfse_data'    => array(
						'hide' => true
					)
				),
				array(
					'desc'              => __( 'Max value.', 'cost-of-goods-for-woocommerce' ),
					'desc_tip'          => __( 'Maximum absolute value returned for the percent calculation.', 'cost-of-goods-for-woocommerce' ),
					'type'              => 'number',
					'id'                => "alg_wc_cog_gateway_costs_percent_max_value[{$key}]",
					'default'           => 0,
					'custom_attributes' => array( 'step' => '0.000001' ),
					'wpfse_data'    => array(
						'hide' => true
					)
				),
				array(
					'title'             => __( 'Fixed profit', 'cost-of-goods-for-woocommerce' ),
					'desc_tip'          => sprintf( __( 'In %s.', 'cost-of-goods-for-woocommerce' ), alg_wc_cog()->core->get_default_shop_currency() ),
					'type'              => 'number',
					'id'                => "alg_wc_cog_gateway_profit_fixed[{$key}]",
					'default'           => 0,
					'custom_attributes' => array( 'step' => '0.000001' ),
					'wpfse_data'    => array(
						'hide' => true
					)
				),
				array(
					'type'           => 'sectionend',
					'id'             => 'alg_wc_cog_gateway_' . $key,
					'wpfse_data' => array(
						'hide' => true
					)
				),
			) );
		}
		return $settings;
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Settings_Gateways();
