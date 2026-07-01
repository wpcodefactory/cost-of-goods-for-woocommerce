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

if ( ! class_exists( 'WPFCOGS_Import_Tool_Bkg_Process' ) ) :

	class WPFCOGS_Import_Tool_Bkg_Process extends WPFCOGS_Bkg_Process {

		/**
		 * @var string
		 */
		protected $action = 'wpfcogs_import_tool';

		/**
		 * get_action_label.
		 *
		 * @version 2.3.0
		 * @since   2.3.0
		 *
		 * @return string
		 */
		protected function get_action_label() {
			return __( 'Cost of Goods for WooCommerce - Import Tool', 'cost-of-goods-for-woocommerce' );
		}

		/**
		 * task.
		 *
		 * @version 2.4.6
		 * @since   2.3.0
		 *
		 * @param mixed $item
		 *
		 * @return bool|mixed
		 */
		protected function task( $item ) {
			parent::task( $item );
			wpfcogs()->core->import_tool->copy_product_meta( array(
				'product_id' => $item['id'],
				'from_key'   => $item['from_key'],
				'to_key'     => $item['to_key']
			) );
			return false;
		}

	}
endif;