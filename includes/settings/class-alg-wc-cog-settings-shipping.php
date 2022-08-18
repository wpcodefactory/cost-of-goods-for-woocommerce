<?php
/**
 * Cost of Goods for WooCommerce - Shipping Section Settings
 *
 * @version 2.4.3
 * @since   1.5.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Settings_Shipping' ) ) :

class Alg_WC_Cost_of_Goods_Settings_Shipping extends Alg_WC_Cost_of_Goods_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.7.2
	 * @since   1.5.0
	 */
	function __construct() {
		$this->id   = 'shipping';
		$this->desc = __( 'Shipping Methods', 'cost-of-goods-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_shipping_methods.
	 *
	 * @version 1.5.0
	 * @since   1.5.0
	 */
	function get_shipping_methods() {
		$shipping_methods  = array();
		$_shipping_methods = WC()->shipping() ? WC()->shipping()->load_shipping_methods() : array();
		foreach ( $_shipping_methods as $method ) {
			$shipping_methods[ $method->id ] = $method->get_method_title();
		}
		return $shipping_methods;
	}

	/**
	 * get_shipping_zones.
	 *
	 * @version 1.5.0
	 * @since   1.5.0
	 */
	function get_shipping_zones( $include_empty_zone = true ) {
		$zones = WC_Shipping_Zones::get_zones();
		if ( $include_empty_zone ) {
			$zone                                                = new WC_Shipping_Zone( 0 );
			$zones[ $zone->get_id() ]                            = $zone->get_data();
			$zones[ $zone->get_id() ]['zone_id']                 = $zone->get_id();
			$zones[ $zone->get_id() ]['formatted_zone_location'] = $zone->get_formatted_location();
			$zones[ $zone->get_id() ]['shipping_methods']        = $zone->get_shipping_methods();
		}
		return $zones;
	}

	/**
	 * get_shipping_methods_instances.
	 *
	 * @version 1.5.0
	 * @since   1.5.0
	 */
	function get_shipping_methods_instances( $full_data = false ) {
		$shipping_methods = array();
		foreach ( $this->get_shipping_zones() as $zone_id => $zone_data ) {
			foreach ( $zone_data['shipping_methods'] as $shipping_method ) {
				if ( $full_data ) {
					$shipping_methods[ $shipping_method->instance_id ] = array(
						'zone_id'                     => $zone_id,
						'zone_name'                   => $zone_data['zone_name'],
						'formatted_zone_location'     => $zone_data['formatted_zone_location'],
						'shipping_method_title'       => $shipping_method->title,
						'shipping_method_id'          => $shipping_method->id,
						'shipping_method_instance_id' => $shipping_method->instance_id,
					);
				} else {
					$shipping_methods[ $shipping_method->instance_id ] = $zone_data['zone_name'] . ': ' . $shipping_method->title;
				}
			}
		}
		return $shipping_methods;
	}

	/**
	 * get_settings.
	 *
	 * @version 2.3.0
	 * @since   1.5.0
	 * @todo    [maybe] output "No available shipping methods." on empty `$shipping_methods`
	 */
	function get_settings() {
		$settings = array(
			array(
				'title'    => __( 'Extra Shipping Method Costs', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( "Here you can add extra costs for your orders based on order's shipping method.", 'cost-of-goods-for-woocommerce' ) . ' ' .
					__( "You will need to recalculate order's cost and profit after you change these settings.", 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_shipping',
			),
			array(
				'title'    => __( 'Extra shipping costs', 'cost-of-goods-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable section', 'cost-of-goods-for-woocommerce' ) . '</strong>',
				'type'     => 'checkbox',
				'id'       => 'alg_wc_cog_shipping_costs_enabled',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Use shipping instances', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Enable', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Enable this if you want to use shipping methods instances (with shipping zones) instead of shipping methods.',
					'cost-of-goods-for-woocommerce' ) . ' ' . __( 'Save changes after enabling this option.', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_cog_shipping_use_shipping_instance',
				'default'  => 'no',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_shipping',
			),
		);
		$use_shipping_instance = ( 'yes' === get_option( 'alg_wc_cog_shipping_use_shipping_instance', 'no' ) );
		$shipping_methods      = ( $use_shipping_instance ? $this->get_shipping_methods_instances() : $this->get_shipping_methods() );
		foreach ( $shipping_methods as $key => $title ) {
			$settings = array_merge( $settings, array(
				array(
					'title'    => $title,
					'type'     => 'title',
					'id'       => 'alg_wc_cog_shipping_' . $key,
					'wpfse_data'      => array(
						'hide' => true
					)
				),
				array(
					'title'    => __( 'Fixed cost', 'cost-of-goods-for-woocommerce' ),
					'desc_tip' => sprintf( __( 'In %s.', 'cost-of-goods-for-woocommerce' ), alg_wc_cog()->core->get_default_shop_currency() ),
					'type'     => 'number',
					'id'       => "alg_wc_cog_shipping_costs_fixed[{$key}]",
					'default'  => 0,
					'custom_attributes' => array( 'step' => '0.000001' ),
					'wpfse_data'      => array(
						'hide' => true
					)
				),
				array(
					'title'             => __( 'Percent cost', 'cost-of-goods-for-woocommerce' ),
					'desc_tip'          => __( 'Percent from order total.', 'cost-of-goods-for-woocommerce' ),
					'type'              => 'number',
					'id'                => "alg_wc_cog_shipping_costs_percent[{$key}]",
					'default'           => 0,
					'custom_attributes' => array( 'step' => '0.000001' ),
					'wpfse_data'    => array(
						'hide' => true
					)
				),
				array(
					'type'           => 'sectionend',
					'id'             => 'alg_wc_cog_shipping_' . $key,
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

return new Alg_WC_Cost_of_Goods_Settings_Shipping();
