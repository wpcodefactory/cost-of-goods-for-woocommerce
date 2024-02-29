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
		 * @version 3.3.0
		 * @since   2.5.1
		 *
		 * @param mixed $item
		 * @return bool|mixed
		 */
		protected function task( $item ) {
			parent::task( $item );
			$function = isset( $item['products_function'] ) ? $item['products_function'] : '';
			call_user_func_array( array( alg_wc_cog()->core->products, $function ), array( $item ) );

			return false;
		}
	}
endif;