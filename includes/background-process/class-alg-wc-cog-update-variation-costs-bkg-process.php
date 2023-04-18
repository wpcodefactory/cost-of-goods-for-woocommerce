<?php
/**
 * Cost of Goods for WooCommerce - Background Process - Update variation costs.
 *
 * @version 2.9.5
 * @since   2.9.5
 * @author  WPFactory
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Update_Variation_Costs_Bkg_Process' ) ) :

	class Alg_WC_Cost_of_Goods_Update_Variation_Costs_Bkg_Process extends Alg_WC_Cost_of_Goods_Bkg_Process {

		/**
		 * @var string
		 */
		protected $action = 'alg_wc_cog_update_variation_costs_bkg_process';

		/**
		 * get_action_label.
		 *
		 * @since   2.9.5
		 * @version 2.9.5
		 *
		 * @return string
		 */
		protected function get_action_label() {
			return __( 'Cost of Goods for WooCommerce - Bulk update variation costs', 'cost-of-goods-for-woocommerce' );
		}

		/**
		 * task.
		 *
		 * @version 2.9.5
		 * @since   2.9.5
		 *
		 * @param mixed $item
		 * @return bool|mixed
		 */
		protected function task( $item ) {
			parent::task( $item );
			alg_wc_cog()->core->products->update_variation_cost_from_parent( $item );
			return false;
		}
	}
endif;