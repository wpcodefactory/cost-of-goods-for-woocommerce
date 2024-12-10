<?php
/**
 * Cost of Goods for WooCommerce - Extra costs labels.
 *
 * @version 3.6.0
 * @since   3.6.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Extra_Costs_Labels' ) ) {

	class Alg_WC_Cost_of_Goods_Extra_Costs_Labels {

		/**
		 * Labels.
		 *
		 * @since 3.6.0
		 *
		 * @var array
		 */
		protected $labels = array();

		/**
		 * Init.
		 *
		 * @version 3.6.0
		 * @since   3.6.0
		 *
		 * @return void
		 */
		function init() {
			add_action( 'init', function () {
				$this->set_labels( array(
					'handling' => array(
						'short' => __( 'handling', 'cost-of-goods-for-woocommerce' ),
						'long'  => __( 'handling fee', 'cost-of-goods-for-woocommerce' )
					),
					'shipping' => array(
						'short' => __( 'shipping', 'cost-of-goods-for-woocommerce' ),
						'long'  => __( 'shipping fee', 'cost-of-goods-for-woocommerce' )
					),
					'payment'  => array(
						'short' => __( 'payment', 'cost-of-goods-for-woocommerce' ),
						'long'  => __( 'payment fee', 'cost-of-goods-for-woocommerce' )
					),
				) );
			} );
		}

		/**
		 * Get label.
		 *
		 * @version 3.6.0
		 * @since   3.6.0
		 *
		 * @param $cost_type
		 * @param $label_type
		 *
		 * @return string
		 */
		function get_label( $cost_type, $label_type = 'short' ) {
			$label = '';
			if ( isset( $this->get_labels()[ $cost_type ] ) && isset( $this->get_labels()[ $cost_type ][ $label_type ] ) ) {
				$label = $this->get_labels()[ $cost_type ][ $label_type ];
			}

			return $label;
		}

		/**
		 * get_labels.
		 *
		 * @version 3.6.0
		 * @since   3.6.0
		 *
		 * @return array
		 */
		public function get_labels(): array {
			return $this->labels;
		}

		/**
		 * set_labels.
		 *
		 * @version 3.6.0
		 * @since   3.6.0
		 *
		 * @param array $labels
		 */
		public function set_labels( array $labels ): void {
			$this->labels = $labels;
		}


	}
}