<?php
/**
 * Cost of Goods for WooCommerce - Compatibility Class
 *
 * @version 2.1.0
 * @since   2.1.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Compatibility' ) ) :

class Alg_WC_Cost_of_Goods_Compatibility {

	/**
	 * Constructor.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	function __construct() {
		// "WooCommerce PDF Invoices, Packing Slips, Delivery Notes & Shipping Labels" plugin
		add_filter( 'wf_pklist_modify_meta_data', array( $this, 'wf_pklist_remove_cog_meta' ), PHP_INT_MAX );
	}

	/**
	 * wf_pklist_remove_cog_meta.
	 *
	 * @version 1.3.4
	 * @since   1.3.4
	 */
	function wf_pklist_remove_cog_meta( $meta_data ) {
		if ( isset( $meta_data['_alg_wc_cog_item_cost'] ) ) {
			unset( $meta_data['_alg_wc_cog_item_cost'] );
		}
		return $meta_data;
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Compatibility();
