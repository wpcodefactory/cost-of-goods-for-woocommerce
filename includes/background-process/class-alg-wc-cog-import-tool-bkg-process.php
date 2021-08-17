<?php
/**
 * Cost of Goods for WooCommerce - Background Process - Import
 *
 * @version 2.4.6
 * @since   2.3.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Import_Tool_Bkg_Process' ) ) :

	class Alg_WC_Cost_of_Goods_Import_Tool_Bkg_Process extends Alg_WC_Cost_of_Goods_Bkg_Process {

		/**
		 * @var string
		 */
		protected $action = 'alg_wc_cog_import_tool';

		/**
		 * @version 2.3.0
		 * @since   2.3.0
		 *
		 * @return string
		 */
		protected function get_action_label() {
			return __( 'Cost of Goods for WooCommerce - Import Tool', 'cost-of-goods-for-woocommerce' );
		}

		/**
		 * @version 2.4.6
		 * @since   2.3.0
		 *
		 * @param mixed $item
		 *
		 * @return bool|mixed
		 */
		protected function task( $item ) {
			parent::task( $item );
			alg_wc_cog()->core->import_tool->copy_product_meta( array(
				'product_id' => $item['id'],
				'from_key'   => $item['from_key'],
				'to_key'     => $item['to_key']
			) );
			return false;
		}

	}
endif;