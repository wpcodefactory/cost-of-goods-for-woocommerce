<?php
/**
 * Cost of Goods for WooCommerce - Currencies Section Settings
 *
 * @version 2.2.0
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
	 * @version 2.2.0
	 * @since   2.2.0
	 * @todo    [now] [!] `alg_wc_cog_currencies_wmc`: add link?
	 * @todo    [next] exclude `$wc_currency` from `get_woocommerce_currencies()`?
	 * @todo    [maybe] better desc
	 */
	function get_settings() {
		$settings = array(
			array(
				'title'    => __( 'Multicurrency', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Here you can set currency exchange rates for your orders in non-default shop currency, i.e. order cost and profit will be converted to the default shop currency according to these rates.', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_currencies_options',
			),
			array(
				'title'    => __( 'Multicurrency', 'cost-of-goods-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable section', 'cost-of-goods-for-woocommerce' ) . '</strong>',
				'desc_tip' => apply_filters( 'alg_wc_cog_settings', sprintf( 'You will need %s plugin to enable this section.',
					'<a target="_blank" href="https://wpfactory.com/item/cost-of-goods-for-woocommerce/">' . 'Cost of Goods for WooCommerce Pro' . '</a>' ) ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_cog_currencies_enabled',
				'default'  => 'no',
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'Currencies', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Choose currencies you want to set exchange rates for, and "Save changes" - new settings fields will be displayed.', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'id'       => 'alg_wc_cog_currencies',
				'default'  => array(),
				'options'  => get_woocommerce_currencies(),
			),
		);
		$currencies  = get_option( 'alg_wc_cog_currencies', array() );
		$wc_currency = get_option( 'woocommerce_currency' );
		foreach ( $currencies as $currency ) {
			$pair     = $wc_currency . $currency;
			$settings = array_merge( $settings, array(
				array(
					'title'    => $pair,
					'type'     => 'number',
					'id'       => "alg_wc_cog_currencies_rates[{$pair}]",
					'default'  => 0,
					'custom_attributes' => array( 'step' => '0.000001' ),
				),
			) );
		}
		$settings = array_merge( $settings, array(
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_currencies_options',
			),
			array(
				'title'    => __( 'Advanced Options', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_currencies_advanced_options',
			),
			array(
				'title'    => __( '"Multi Currency for WooCommerce" plugin by VillaTheme', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Enable', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'When enabled, the plugin will try to get currency exchange rates from the "Multi Currency for WooCommerce" plugin automatically.', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_cog_currencies_wmc',
				'default'  => 'no',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_currencies_advanced_options',
			),
		) );
		return $settings;
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Settings_Currencies();
