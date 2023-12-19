<?php
/**
 * Cost of Goods for WooCommerce - Costs input.
 *
 * @version 3.1.9
 * @since   2.6.4
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Cost_Inputs' ) ) :

	class Alg_WC_Cost_of_Goods_Cost_Inputs {

		/**
		 * Cost field template.
		 *
		 * @since 2.9.4
		 *
		 * @var string
		 */
		public $cost_field_template;

		/**
		 *
		 * Alg_WC_Cost_of_Goods_Cost_Inputs constructor.
		 *
		 * @version 2.6.4
		 * @since   2.6.4
		 *
		 */
		public function __construct() {
			$this->get_options();
			$this->add_hooks();
		}

		/**
		 * get_options.
		 *
		 * @version 2.6.4
		 * @since   2.6.4
		 */
		function get_options() {
			$this->cost_field_template = get_option( 'alg_wc_cog_product_cost_field_template', sprintf( __( 'Cost (excl. tax) (%s)', 'cost-of-goods-for-woocommerce' ), '%currency_symbol%' ) );
		}

		/**
		 * add_hooks.
		 *
		 * @version 2.6.5
		 * @since   2.6.4
		 */
		function add_hooks(){
			// Cost input on admin product page (simple product)
			add_action( get_option( 'alg_wc_cog_product_cost_field_position', 'woocommerce_product_options_pricing' ), array( $this, 'add_cost_input' ) );
			add_action( 'woocommerce_bookings_after_display_cost', array( $this, 'add_cost_input' ) );
			add_action( 'save_post_product', array( $this, 'save_cost_input' ), PHP_INT_MAX - 2, 2 );
			// Cost input on admin product page (variable product)
			add_action( 'woocommerce_variation_options_pricing', array( $this, 'add_cost_input_variation' ), 10, 3 );
			add_action( 'woocommerce_save_product_variation', array( $this, 'save_cost_input_variation' ), PHP_INT_MAX, 2 );
			add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_cost_input_variable' ), PHP_INT_MAX );
		}

		/**
		 * add_cost_input.
		 *
		 * @version 3.1.9
		 * @since   1.0.0
		 * @todo    [later] rethink `$product_id` (and search all code for `get_the_ID()`)
		 * @todo    [maybe] min_profit
		 */
		function add_cost_input() {
			if ( ! apply_filters( 'alg_wc_cog_create_product_meta_box_validation', true ) ) {
				return;
			}
			$product_id = get_the_ID();
			if ( apply_filters( 'alg_wc_cog_add_cost_input_validation', true, $product_id ) ) {
				$label_from_to = $this->get_cost_input_label_placeholders( $product_id );
				woocommerce_wp_text_input( array(
					'id'          => '_alg_wc_cog_cost',
					'value'       => wc_format_localized_price( alg_wc_cog()->core->products->get_product_cost( $product_id ) ),
					'data_type'   => 'price',
					'label'       => str_replace( array_keys( $label_from_to ), $label_from_to, $this->cost_field_template ),
					'description' => sprintf( __( 'Profit: %s', 'cost-of-goods-for-woocommerce' ),
						( '' != ( $profit = alg_wc_cog()->core->products->get_product_profit_html( $product_id, alg_wc_cog()->core->products->product_profit_html_template ) ) ? $profit : __( 'N/A', 'cost-of-goods-for-woocommerce' ) ) ),
				) );
			}
			do_action( 'alg_wc_cog_cost_input', $product_id );
		}

		/**
		 * get_cost_input_label_placeholders.
		 *
		 * @version 3.1.9
		 * @since   3.1.9
		 *
		 * @param $product_id
		 *
		 * @return mixed|null
		 */
		function get_cost_input_label_placeholders( $product_id = null ) {
			return apply_filters( 'alg_wc_cog_cost_input_label_placeholders', array(
				'%currency_symbol%' => alg_wc_cog()->core->get_default_shop_currency_symbol()
			), $product_id );
		}

		/**
		 * add_cost_input_variable.
		 *
		 * @version 1.0.1
		 * @since   1.0.0
		 * @todo    [fix] this is not showing when creating *new* variable product
		 * @todo    [maybe] move this to "Inventory" tab
		 */
		function add_cost_input_variable() {
			if ( ( $product = wc_get_product() ) && $product->is_type( 'variable' ) ) {
				echo '<div class="options_group show_if_variable">';
				$this->add_cost_input();
				echo '</div>';
			}
		}

		/**
		 * add_cost_input_variation.
		 *
		 * @version 2.3.4
		 * @since   1.0.0
		 */
		function add_cost_input_variation( $loop, $variation_data, $variation ) {
			if ( ! apply_filters( 'alg_wc_cog_create_product_meta_box_validation', true ) ) {
				return;
			}
			if (
				! isset( $variation_data['_alg_wc_cog_cost'][0] ) ||
				empty( $value = $variation_data['_alg_wc_cog_cost'][0] )
			) {
				$product           = wc_get_product( $variation->ID );
				$parent_product_id = $product->get_parent_id();
				$value             = alg_wc_cog()->core->products->get_product_cost( $parent_product_id, array( 'check_parent_cost' => false ) );
			}
			$hook_data = array(
				'variation_id'   => $variation->ID,
				'value'          => $value,
				'variation_data' => $variation_data,
				'loop'           => $loop,
			);
			if ( apply_filters( 'alg_wc_cog_add_cost_input_variation_validation', true, $hook_data ) ) {
				woocommerce_wp_text_input( array(
					'id'            => "variable_alg_wc_cog_cost_{$loop}",
					'name'          => "variable_alg_wc_cog_cost[{$loop}]",
					'value'         => wc_format_localized_price( $value ),
					'label'         => str_replace( '%currency_symbol%', alg_wc_cog()->core->get_default_shop_currency_symbol(), $this->cost_field_template ),
					'data_type'     => 'price',
					'wrapper_class' => 'form-row form-row-full',
					'description'   => sprintf( __( 'Profit: %s', 'cost-of-goods-for-woocommerce' ),
						( '' != ( $profit = alg_wc_cog()->core->products->get_product_profit_html( $variation->ID, alg_wc_cog()->core->products->product_profit_html_template ) ) ? $profit : __( 'N/A', 'cost-of-goods-for-woocommerce' ) ) ),
				) );
			}
			do_action( 'alg_wc_cog_cost_input_variation', $hook_data );
		}

		/**
		 * save_cost_input.
		 *
		 * @version 1.7.0
		 * @since   1.0.0
		 * @todo    [next] maybe pre-calculate and save `_alg_wc_cog_profit` (same in `save_cost_input_variation()`)
		 */
		function save_cost_input( $product_id, $__post ) {
			if ( isset( $_POST['_alg_wc_cog_cost'] ) ) {
				update_post_meta( $product_id, '_alg_wc_cog_cost', wc_clean( $_POST['_alg_wc_cog_cost'] ) );
			}
		}

		/**
		 * save_cost_input_variation.
		 *
		 * @version 1.1.0
		 * @since   1.0.0
		 */
		function save_cost_input_variation( $variation_id, $i ) {
			if ( isset( $_POST['variable_alg_wc_cog_cost'][ $i ] ) ) {
				update_post_meta( $variation_id, '_alg_wc_cog_cost', wc_clean( $_POST['variable_alg_wc_cog_cost'][ $i ] ) );
			}
		}

	}
endif;

return new Alg_WC_Cost_of_Goods_Cost_Inputs();