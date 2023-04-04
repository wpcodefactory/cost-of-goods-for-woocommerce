<?php
/**
 * Cost of Goods for WooCommerce - Section Settings.
 *
 * @version 2.9.4
 * @since   1.0.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Settings_Section' ) ) :

class Alg_WC_Cost_of_Goods_Settings_Section {

	/**
	 * ID.
	 *
	 * @since 2.9.4
	 */
	public $id;

	/**
	 * Description.
	 *
	 * @since 2.9.4
	 */
	public $desc;

	/**
	 * Constructor.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function __construct() {
		add_filter( 'woocommerce_get_sections_alg_wc_cost_of_goods',              array( $this, 'settings_section' ) );
		add_filter( 'woocommerce_get_settings_alg_wc_cost_of_goods_' . $this->id, array( $this, 'get_settings' ), PHP_INT_MAX );
	}

	/**
	 * settings_section.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function settings_section( $sections ) {
		$sections[ $this->id ] = $this->desc;
		return $sections;
	}

	/**
	 * get_order_statuses.
	 *
	 * @version 1.3.1
	 * @since   1.3.1
	 */
	function get_order_statuses() {
		$order_statuses = array();
		foreach ( wc_get_order_statuses() as $status_id => $status_title ) {
			$order_statuses[ ( 0 === strpos( $status_id, 'wc-' ) ? substr( $status_id, 3 ) : $status_id ) ] = $status_title;
		}
		return $order_statuses;
	}

}

endif;
