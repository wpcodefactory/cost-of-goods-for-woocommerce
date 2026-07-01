<?php
/**
 * Cost of Goods for WooCommerce - Background Process - Recalculate Orders
 *
 * @version 3.6.2
 * @since   2.3.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFCOGS_Recalculate_Orders_Bkg_Process' ) ) :

	class WPFCOGS_Recalculate_Orders_Bkg_Process extends WPFCOGS_Bkg_Process {

		/**
		 * @var string
		 */
		protected $action = 'wpfcogs_recalculate_orders';

		/**
		 * get_action_label.
		 *
		 * @version 2.3.0
		 * @since   2.3.0
		 *
		 * @return string
		 */
		protected function get_action_label() {
			return __( 'Cost of Goods for WooCommerce - Recalculate orders', 'cost-of-goods-for-woocommerce' );
		}

		/**
		 * task.
		 *
		 * @version 3.6.2
		 * @since   2.3.0
		 *
		 * @param mixed $item
		 *
		 * @return bool|mixed
		 */
		protected function task( $item ) {
			parent::task( $item );
			wpfcogs()->core->orders->update_order_items_costs( array(
				'order_id'         => $item['id'],
				'is_new_order'     => $item['is_new_order'],
				'is_no_costs_only' => $item['recalculate_for_orders_with_no_costs'],
			) );
			return false;
		}

	}
endif;