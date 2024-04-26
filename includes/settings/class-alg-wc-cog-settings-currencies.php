<?php
/**
 * Cost of Goods for WooCommerce - Currencies Section Settings.
 *
 * @version 3.3.7
 * @since   2.2.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Settings_Currencies' ) ) :

class Alg_WC_Cost_of_Goods_Settings_Currencies extends Alg_WC_Cost_of_Goods_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function __construct() {
		$this->id   = 'currencies';
		$this->desc = __( 'Multicurrency', 'cost-of-goods-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 3.3.7
	 * @since   2.2.0
	 * @todo    [next] exclude `$wc_currency` from `get_woocommerce_currencies()`?
	 * @todo    [maybe] `alg_wc_cog_currencies_wmc`: add link to the plugin on wp.org?
	 * @todo    [maybe] better desc
	 */
	function get_settings() {
		// Multicurrency order calculation.
		$multicurrency_order_calculation_opts = array(
			array(
				'title' => __( 'Multi-currency management', 'cost-of-goods-for-woocommerce' ),
				'desc'  => __( 'Some notes:', 'cost-of-goods-for-woocommerce' ) . '<br />' .
				           alg_wc_cog_array_to_string( array(
					           __( 'All COG related metas, such as profit and costs will be saved in the shop base currency.', 'cost-of-goods-for-woocommerce' ),
					           __( 'All costs should be always set with the shop base currency, including cost fields present on orders.', 'cost-of-goods-for-woocommerce' ),
				           ), array( 'item_template' => '<li>{value}</li>', 'glue' => '' ) ),
				'type'  => 'title',
				'id'    => 'alg_wc_cog_currencies_options',
			),
			array(
				'title'             => __( 'Multi-currency management', 'cost-of-goods-for-woocommerce' ),
				'desc'              => sprintf( __( 'Enable %s feature', 'cost-of-goods-for-woocommerce' ), strtolower( __( 'Multi-currency management', 'cost-of-goods-for-woocommerce' ) ) ),
				'type'              => 'checkbox',
				'id'                => 'alg_wc_cog_currencies_enabled',
				'default'           => 'no',
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				'wpfse_data'        => array(
					'hide' => true
				)
			),
			array(
				'title'             => __( 'Order values', 'cost-of-goods-for-woocommerce' ),
				'desc'              => __( 'Display COG related values from the order, such as costs and profit, in the currency of the order', 'cost-of-goods-for-woocommerce' ),
				'desc_tip'          => sprintf(__( 'The affected areas are the %s and the order page on the admin.', 'cost-of-goods-for-woocommerce' ), '<a href="' . alg_wc_cog_get_admin_orders_page_url() . '">' . __( 'order listing page', 'cost-of-goods-for-woocommerce' ) . '</a>'),
				'type'              => 'checkbox',
				'id'                => 'alg_wc_cog_currencies_display_order_currency_values',
				'default'           => 'yes',
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				'wpfse_data'        => array(
					'hide' => true
				)
			),
			array(
				'title'             => __( 'COG order fields currency', 'cost-of-goods-for-woocommerce' ),
				'desc'              => __( 'Force shop base currency on COG order fields', 'cost-of-goods-for-woocommerce' ),
				'type'              => 'checkbox',
				'id'                => 'alg_wc_cog_currencies_force_sb_currency_cog_order_fields',
				'default'           => 'yes',
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				'wpfse_data'        => array(
					'hide' => true
				)
			),
			array(
				'title'      => __( 'Currencies', 'cost-of-goods-for-woocommerce' ),
				'desc_tip'   => __( 'Choose currencies you want to set exchange rates for, and "Save changes" - new settings fields will be displayed.', 'cost-of-goods-for-woocommerce' ),
				'type'       => 'multiselect',
				'class'      => 'chosen_select',
				'id'         => 'alg_wc_cog_currencies',
				'default'    => array(),
				'options'    => get_woocommerce_currencies(),
				'wpfse_data' => array(
					'hide' => true
				)
			),
		);
		$currencies                           = get_option( 'alg_wc_cog_currencies', array() );
		$wc_currency                          = alg_wc_cog()->core->get_default_shop_currency();
		foreach ( $currencies as $currency ) {
			$pair                                 = $wc_currency . $currency;
			$multicurrency_order_calculation_opts = array_merge( $multicurrency_order_calculation_opts, array(
				array(
					'title'             => $pair,
					'type'              => 'number',
					'id'                => "alg_wc_cog_currencies_rates[{$pair}]",
					'default'           => 0,
					'custom_attributes' => array( 'step' => '0.000001' ),
					'wpfse_data'        => array(
						'hide' => true
					)
				),
			) );
		}
		$multicurrency_order_calculation_opts[] = array(
			'type' => 'sectionend',
			'id'   => 'alg_wc_cog_currencies_options',
		);
		// Currency costs.
		$currency_cost_opts = array(
			array(
				'title' => __( 'Currency costs', 'cost-of-goods-for-woocommerce' ),
				'desc'  => __( 'Add extra costs based on the order currency.', 'cost-of-goods-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_wc_cog_currencies_costs_options',
			),
			array(
				'title'             => __( 'Currencies costs', 'cost-of-goods-for-woocommerce' ),
				'desc'              => __( 'Add extra costs based on the order currency', 'cost-of-goods-for-woocommerce' ),
				'type'              => 'checkbox',
				'id'                => 'alg_wc_cog_currencies_costs_enabled',
				'default'           => 'no',
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				'wpfse_data'        => array(
					'hide' => true
				)
			),
			array(
				'title'      => __( 'Currencies', 'cost-of-goods-for-woocommerce' ),
				'desc_tip'   => __( 'Choose currencies you want to add costs for, and "Save changes" - new settings fields will be displayed.', 'cost-of-goods-for-woocommerce' ),
				'type'       => 'multiselect',
				'class'      => 'chosen_select',
				'id'         => 'alg_wc_cog_currencies_costs_currencies',
				'default'    => array(),
				'options'    => get_woocommerce_currencies(),
				'wpfse_data' => array(
					'hide' => true
				)
			),

		);
		$currencies = get_option( 'alg_wc_cog_currencies_costs_currencies', array() );
		foreach ( $currencies as $currency ) {
			$currency_cost_opts = array_merge( $currency_cost_opts, array(
				array(
					'title'             => $currency . __( ' - Fixed cost', 'cost-of-goods-for-woocommerce' ),
					'type'              => 'number',
					'id'                => "alg_wc_cog_currencies_costs_fixed[{$currency}]",
					'default'           => 0,
					'custom_attributes' => array( 'step' => '0.000001' ),
					'wpfse_data'        => array(
						'hide' => true
					)
				),
				array(
					'title'             => $currency . __( ' - Percent cost', 'cost-of-goods-for-woocommerce' ),
					'desc_tip'          => __( 'Percent from order total. E.g.: If you want to add a cost of <code>50%</code> from order total you can set it as 50.', 'cost-of-goods-for-woocommerce' ),
					'type'              => 'number',
					'id'                => "alg_wc_cog_currencies_costs_percent[{$currency}]",
					'default'           => 0,
					'custom_attributes' => array( 'step' => '0.000001' ),
					'wpfse_data'        => array(
						'hide' => true
					)
				),
			) );
		}
		$currency_cost_opts[] = array(
			'type' => 'sectionend',
			'id'   => 'alg_wc_cog_currencies_costs_options',
		);
		return array_merge( $multicurrency_order_calculation_opts, $currency_cost_opts );


	}

}

endif;

return new Alg_WC_Cost_of_Goods_Settings_Currencies();
