<?php
/**
 * Cost of Goods for WooCommerce - Background Process - Update Cost
 *
 * @version 2.5.1
 * @since   2.5.1
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Update_Cost_Bkg_Process' ) ) :

	class Alg_WC_Cost_of_Goods_Update_Cost_Bkg_Process extends Alg_WC_Cost_of_Goods_Bkg_Process {

		/**
		 * @var string
		 */
		protected $action = 'alg_wc_cog_update_cost';

		/**
		 * get_action_label.
		 *
		 * @since   2.5.1
		 * @version 2.5.1
		 *
		 * @return string
		 */
		protected function get_action_label() {
			return __( 'Cost of Goods for WooCommerce - Bulk update costs', 'cost-of-goods-for-woocommerce' );
		}

		/**
		 * task.
		 *
		 * @version 2.5.1
		 * @since   2.5.1
		 *
		 * @param mixed $item
		 * @return bool|mixed
		 */
		protected function task( $item ) {
			parent::task( $item );
			$product_id  = isset( $item['product_id'] ) ? $item['product_id'] : '';
			$percentage  = isset( $item['percentage'] ) ? $item['percentage'] : '';
			$update_type = isset( $item['update_type'] ) ? $item['update_type'] : '';
			alg_wc_cog()->core->products->update_product_cost_by_percentage( array(
				'product_id'        => $product_id,
				'percentage'        => $percentage,
				'update_type'       => $update_type, // profit | price
				'update_variations' => true
			) );
			return false;
		}
	}
endif;