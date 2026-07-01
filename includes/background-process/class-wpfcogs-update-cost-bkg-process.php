<?php
/**
 * Cost of Goods for WooCommerce - Background Process - Update Cost.
 *
 * @version 3.3.0
 * @since   2.5.1
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFCOGS_Update_Cost_Bkg_Process' ) ) :

	class WPFCOGS_Update_Cost_Bkg_Process extends WPFCOGS_Bkg_Process {

		/**
		 * @var string
		 */
		protected $action = 'wpfcogs_update_cost';

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
		 * @version 3.3.0
		 * @since   2.5.1
		 *
		 * @param mixed $item
		 * @return bool|mixed
		 */
		protected function task( $item ) {
			parent::task( $item );
			$function = isset( $item['products_function'] ) ? $item['products_function'] : '';
			call_user_func_array( array( wpfcogs()->core->products, $function ), array( $item ) );

			return false;
		}
	}
endif;