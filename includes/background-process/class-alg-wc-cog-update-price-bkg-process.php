<?php
/**
 * Cost of Goods for WooCommerce - Background Process - Update Price
 *
 * @version 2.6.3
 * @since   2.6.3
 * @author  WPFactory
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Update_Price_Bkg_Process' ) ) :

	class Alg_WC_Cost_of_Goods_Update_Price_Bkg_Process extends Alg_WC_Cost_of_Goods_Bkg_Process {

		/**
		 * @var string
		 */
		protected $action = 'alg_wc_cog_update_price';

		/**
		 * get_action_label.
		 *
		 * @since   2.6.3
		 * @version 2.6.3
		 *
		 * @return string
		 */
		protected function get_action_label() {
			return __( 'Cost of Goods for WooCommerce - Bulk update prices', 'cost-of-goods-for-woocommerce' );
		}

		/**
		 * task.
		 *
		 * @version 2.6.3
		 * @since   2.6.3
		 *
		 * @param mixed $item
		 * @return bool|mixed
		 */
		protected function task( $item ) {
			parent::task( $item );
			alg_wc_cog()->core->products->update_product_price_by_percentage( $item );
			return false;
		}
	}
endif;