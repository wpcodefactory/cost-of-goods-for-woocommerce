<?php
/**
 * Cost of Goods for WooCommerce - Background Process - Recalculate Orders
 *
 * @version 2.4.9
 * @since   2.3.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Recalculate_Orders_Bkg_Process' ) ) :

	class Alg_WC_Cost_of_Goods_Recalculate_Orders_Bkg_Process extends Alg_WC_Cost_of_Goods_Bkg_Process {

		/**
		 * @var string
		 */
		protected $action = 'alg_wc_cog_recalculate_orders';

		/**
		 * @version 2.3.0
		 * @since   2.3.0
		 *
		 * @return string
		 */
		protected function get_action_label() {
			return __( 'Cost of Goods for WooCommerce - Recalculate orders', 'cost-of-goods-for-woocommerce' );
		}

		/**
		 * @version 2.4.9
		 * @since   2.3.0
		 *
		 * @param mixed $item
		 *
		 * @return bool|mixed
		 */
		protected function task( $item ) {
			parent::task( $item );
			alg_wc_cog()->core->orders->update_order_items_costs( array(
				'order_id'         => $item['id'],
				'is_new_order'     => true,
				'is_no_costs_only' => $item['recalculate_for_orders_with_no_costs'],
			) );
			return false;
		}

	}
endif;