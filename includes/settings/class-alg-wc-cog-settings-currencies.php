<?php
/**
 * Cost of Goods for WooCommerce - Currencies Section Settings
 *
 * @version 2.4.3
 * @since   2.2.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Settings_Currencies' ) ) :

class Alg_WC_Cost_of_Goods_Settings_Currencies extends Alg_WC_Cost_of_Goods_Settings_Section {

	/**
	 * $auto_exchange_cron_output.
	 *
	 * @since   2.4.3
	 *
	 * @var string
	 */
	private static $auto_exchange_cron_output = '';

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
	 * @version 2.4.3
	 * @since   2.2.0
	 * @todo    [next] exclude `$wc_currency` from `get_woocommerce_currencies()`?
	 * @todo    [maybe] `alg_wc_cog_currencies_wmc`: add link to the plugin on wp.org?
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
		$wc_currency = alg_wc_cog()->core->get_default_shop_currency();
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
				'title'    => __( '"Multi Currency for WooCommerce" plugin', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Enable', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'When enabled, the plugin will try to get currency exchange rates from the "Multi Currency for WooCommerce" plugin (by VillaTheme) automatically.', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_cog_currencies_wmc',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Auto currencies rate from ExchangeRate-API', 'cost-of-goods-for-woocommerce' ),
				'desc'     => sprintf( __( 'Get currency exchange rates from <a href="%s" target="_blank">%s</a> automatically', 'cost-of-goods-for-woocommerce' ), 'https://www.exchangerate-api.com/docs/free', 'ExchangeRate-API' ),
				'desc_tip' => __( 'The update will run once a day.', 'cost-of-goods-for-woocommerce' ) . '<span data-wpfactory-desc-hide>'.' ' . $this->get_auto_exchange_rate_cron_info().'</span>',
				'type'     => 'checkbox',
				'id'       => 'alg_wc_cog_auto_currency_rates',
				'default'  => 'no',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_currencies_advanced_options',
			),
		) );
		return $settings;
	}

	/**
	 * get_auto_exchange_rate_cron_info.
	 *
	 * @version 2.4.3
	 * @since   2.4.3
	 *
	 * @return string
	 */
	function get_auto_exchange_rate_cron_info(){
		$auto_exchange_option_enabled = 'yes' === get_option( 'alg_wc_cog_auto_currency_rates', 'no' );
		if ( empty( self::$auto_exchange_cron_output ) ) {
			$output = '';
			if (
				( ! $event_timestamp = wp_next_scheduled( 'alg_wc_cog_currency_rate_update' ) )
				&& isset( $_POST['alg_wc_cog_auto_currency_rates'] )
			) {
				$output .= '<span style="font-weight: bold; color: green;">' . __( 'Please, reload the page to see the next scheduled event info.', 'cost-of-goods-for-woocommerce' ) . '</span>';
			} elseif ( $event_timestamp && $auto_exchange_option_enabled ) {
				$now                 = current_time( 'timestamp', true );
				$pretty_time_missing = human_time_diff( $now, $event_timestamp );
				$output              .= sprintf( __( 'Next event scheduled to %s', 'cost-of-goods-for-woocommerce' ), '<strong>' . get_date_from_gmt( date( 'Y-m-d H:i:s', $event_timestamp ), get_option( 'date_format' ) . ' - ' . get_option( 'time_format' ) ) . '</strong>' );
				$output              .= ' ' . '(' . $pretty_time_missing . ' left)';
			}
			self::$auto_exchange_cron_output = $output;
		} else {
			if ( ! $auto_exchange_option_enabled ) {
				self::$auto_exchange_cron_output = '';
			}
		}
		return self::$auto_exchange_cron_output;
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Settings_Currencies();
