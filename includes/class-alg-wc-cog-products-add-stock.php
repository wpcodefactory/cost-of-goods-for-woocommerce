<?php
/**
 * Cost of Goods for WooCommerce - Products - Add Stock.
 *
 * @version 2.8.2
 * @since   2.8.2
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Products_Add_Stock' ) ) {

	class Alg_WC_Cost_of_Goods_Products_Add_Stock {

		/**
		 * is_add_stock_enabled.
		 *
		 * @since 2.8.2
		 *
		 * @var null
		 */
		protected $is_add_stock_enabled = null;

		/**
		 * Constructor.
		 *
		 * @version 2.8.2
		 * @since   2.8.2
		 */
		function __construct() {
			// Add product stock
			add_action( 'add_meta_boxes', array( $this, 'add_product_add_stock_meta_box' ) );
			add_action( 'save_post_product', array( $this, 'save_product_add_stock' ), PHP_INT_MAX, 2 );
		}

		/**
		 * is_add_stock_enabled.
		 *
		 * @version 2.8.2
		 * @since   2.8.2
		 *
		 * @return bool|null
		 */
		function is_add_stock_enabled() {
			if ( is_null( $this->is_add_stock_enabled ) ) {
				$this->is_add_stock_enabled = ( 'yes' === get_option( 'alg_wc_cog_products_add_stock', 'no' ) );
			}
			return $this->is_add_stock_enabled;
		}

		/**
		 * add_product_add_stock_meta_box.
		 *
		 * @version 2.3.4
		 * @since   1.7.0
		 */
		function add_product_add_stock_meta_box() {
			if ( ! apply_filters( 'alg_wc_cog_create_product_meta_box_validation', true ) ) {
				return;
			}
			if ( $this->is_add_stock_enabled() ) {
				if ( ( $product = wc_get_product( get_the_ID() ) ) && $product->is_type( 'simple' ) ) {
					$tip = wc_help_tip( __( 'Enter values and "Update" the product.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					                    __( '"Stock" will be added to your inventory, and "Cost" will be used to calculate new average cost of goods for the product.', 'cost-of-goods-for-woocommerce' ) );
					add_meta_box( 'alg-wc-cog-add-stock',
						//__( 'Cost of Goods', 'cost-of-goods-for-woocommerce' ) . ': ' . __( 'Add stock', 'cost-of-goods-for-woocommerce' ) . $tip,
						__( 'Add stock', 'cost-of-goods-for-woocommerce' ) . $tip,
						array( $this, 'product_add_stock_meta_box' ),
						'product',
						'side'
					);
				}
			}
		}

		/**
		 * save_product_add_stock.
		 *
		 * @version 2.4.2
		 * @since   1.7.0
		 * @todo    [next] handle variable products (also unset `$_POST['variable_stock']`)
		 * @todo    [maybe] remove `$this->is_add_stock`
		 */
		function save_product_add_stock( $product_id, $post ) {
			if (
				$this->is_add_stock_enabled()
				&& ! empty( $_POST['alg_wc_cog_add_stock'] )
				&&
				(
					'do_nothing' !== ( $empty_cost_action = get_option( 'alg_wc_cog_products_add_stock_empty_cost_action', 'do_nothing' ) )
					||
					(
						'do_nothing' === $empty_cost_action
						&& ! empty( $_POST['alg_wc_cog_add_stock_cost'] )
					)
				)
			) {
				$this->product_add_stock( $product_id, floatval( $_POST['alg_wc_cog_add_stock'] ), floatval( $_POST['alg_wc_cog_add_stock_cost'] ) );
				if ( isset( $_POST['_stock'] ) ) {
					unset( $_POST['_stock'] );
				}
			}
		}

		/**
		 * product_add_stock_meta_box.
		 *
		 * @version 2.8.2
		 * @since   1.7.0
		 * @todo    [next] add option to delete all/selected history
		 */
		function product_add_stock_meta_box( $post ) {
			$negative_stock_allowed = 'yes' === get_option( 'alg_wc_cog_products_add_stock_negative_stock', 'no' );
			$add_stock_input_min = $negative_stock_allowed ? '' : 'min="0"';

			$html  = '';
			$html .= '<table class="widefat striped"><tbody>' .
			         '<tr>' .
			         '<th><label for="alg_wc_cog_add_stock">' . __( 'Stock', 'cost-of-goods-for-woocommerce' ) . '</label></th>' .
			         '<td><input name="alg_wc_cog_add_stock" id="alg_wc_cog_add_stock" class="short" type="number" '.$add_stock_input_min.'></td>' .
			         '</tr>' .
			         '<tr>' .
			         '<th><label for="alg_wc_cog_add_stock_cost">' . __( 'Cost', 'cost-of-goods-for-woocommerce' ) . '</label></th>' .
			         '<td><input name="alg_wc_cog_add_stock_cost" id="alg_wc_cog_add_stock_cost" class="short wc_input_price" type="number" step="0.0001" min="0"></td>' .
			         '</tr>' .
			         '</tbody></table>';
			$history = get_post_meta( get_the_ID(), '_alg_wc_cog_cost_history', true );
			if ( $history ) {
				$history_rows = '';
				foreach ( $history as $date => $record ) {
					$history_rows .= '<tr><td>' . date( 'Y-m-d', $date ) . '</td><td>' . $record['stock'] . '</td><td>' . alg_wc_cog_format_cost( $record['cost'] ) . '</td></tr>';
				}
				$html .= '' .
				         '<details style="margin-top:5px">' .
				         '<summary style="cursor:pointer">' . __( 'History', 'cost-of-goods-for-woocommerce' ) . '</summary>' .
				         '<table style="margin-top:2px" class="widefat striped"><tbody>' .
				         '<tr>' .
				         '<th>' . __( 'Date', 'cost-of-goods-for-woocommerce' ) . '</th>' .
				         '<th>' . __( 'Stock', 'cost-of-goods-for-woocommerce' ) . '</th>' .
				         '<th>' . __( 'Cost', 'cost-of-goods-for-woocommerce' ) . '</th>' .
				         '</tr>' .
				         $history_rows .
				         '</tbody></table>' .
				         '</details>';
			}
			echo '<div style="margin-top:10px;clear:both"></div>';
			echo $html;
		}

		/**
		 * calculate_add_stock_cost.
		 *
		 * @version 2.4.2
		 * @since   2.4.2
		 *
		 * @param null $args
		 *
		 * @return mixed
		 */
		function calculate_add_stock_cost( $args = null ) {
			$args = wp_parse_args( $args, array(
				'product_id'           => '',
				'template_variables'   => array(
					'%stock_prev%' => '',
					'%cost_prev%'  => '',
					'%stock%'      => '',
					'%cost%'       => '',
					'%stock_now%'  => '',
				),
				'calculation_template' => get_option( 'alg_wc_cog_products_add_stock_cost_calculation', '( %stock_prev% * %cost_prev% + %stock% * %cost% ) / %stock_now%' )
			) );
			$template_variables = $args['template_variables'];
			$cost_calculation_template = $args['calculation_template'];
			$cost_calculation_template = $this->sanitize_math_expression( str_replace( array_keys( $template_variables ), $template_variables, $cost_calculation_template ) );
			include_once WC()->plugin_path() . '/includes/libraries/class-wc-eval-math.php';
			$cost_now = WC_Eval_Math::evaluate( $cost_calculation_template );
			return $cost_now;
		}

		/**
		 * product_add_stock.
		 *
		 * @version 2.6.0
		 * @since   1.7.0
		 * @todo    [next] maybe use `$product = wc_get_product( $product_id )`, i.e. `$product->get_stock_quantity()`, `$product->set_stock_quantity( $stock_now )` and `$product->save()`?
		 * @todo    [maybe] `$cost_now`: round?
		 *
		 * @param $product_id
		 * @param $stock
		 * @param $cost
		 *
		 * @return bool|mixed
		 */
		function product_add_stock( $product_id, $stock, $cost ) {
			$cost = $this->get_add_stock_cost( array(
				'cost'       => $cost,
				'product_id' => $product_id
			) );
			$stock = (int) $stock;
			$stock_prev = (int) get_post_meta( $product_id, '_stock', true );
			if ( ! $stock_prev || '' === $stock_prev ) {
				$stock_prev = 0;
			}
			$stock_now = (int) ( $stock_prev + $stock );
			if ( 0 != $stock_now && false !== $cost) {
				$cost_prev = alg_wc_cog()->core->products->get_product_cost( $product_id );
				if ( ! $cost_prev ) {
					$cost_prev = 0;
				}
				$cost_now = $this->calculate_add_stock_cost( array(
					'product_id'         => $product_id,
					'template_variables' => array(
						'%stock_prev%' => $stock_prev,
						'%cost_prev%'  => $cost_prev,
						'%stock%'      => $stock,
						'%cost%'       => $cost,
						'%stock_now%'  => $stock_now,
					)
				) );
				// Update Stock.
				update_post_meta( $product_id, '_alg_wc_cog_cost', $cost_now );
				$stock_operation = $this->calculate_update_stock_operation( $product_id, $stock_now );
				wc_update_product_stock( $product_id, $stock, $stock_operation );
				if ( 'set' === $stock_operation ) {
					update_post_meta( $product_id, '_stock', $stock_now );
				}
				// Update History.
				$history = get_post_meta( $product_id, '_alg_wc_cog_cost_history', true );
				if ( ! $history ) {
					$history = array();
				}
				$history[ current_time( 'timestamp' ) ] = array( 'stock' => $stock, 'cost' => $cost );
				update_post_meta( $product_id, '_alg_wc_cog_cost_history', $history );
				return $cost_now;
			}
			return false;
		}

		/**
		 * calculate_update_stock_operation.
		 *
		 * @version 2.6.0
		 * @since   2.6.0
		 *
		 * @param $product_id
		 * @param $new_stock
		 *
		 * @return string
		 */
		function calculate_update_stock_operation( $product_id, $new_stock ) {
			$operation  = 'set';
			$stock_prev = get_post_meta( $product_id, '_stock', true );
			if ( ! $stock_prev || '' === $stock_prev ) {
				$operation = 'set';
			} elseif ( (int) $new_stock > (int) $stock_prev ) {
				$operation = 'increase';
			} elseif ( (int) $new_stock < (int) $stock_prev ) {
				$operation = 'decrease';
			}
			return $operation;
		}

		/**
		 * get_add_stock_cost.
		 *
		 * @version 2.4.2
		 * @since   2.4.2
		 *
		 * @param null $args
		 *
		 * @return bool|float
		 */
		function get_add_stock_cost( $args = null ) {
			$args = wp_parse_args( $args, array(
				'cost'              => '',
				'product_id'        => '',
				'empty_cost_action' => get_option( 'alg_wc_cog_products_add_stock_empty_cost_action', 'do_nothing' )
			) );
			$cost = $args['cost'];
			if ( empty( $cost ) ) {
				switch ( $args['empty_cost_action'] ) {
					case 'do_nothing':
						$cost = false;
						break;
					case 'use_last_cost':
						$history = get_post_meta( $args['product_id'], '_alg_wc_cog_cost_history', true );
						if ( ! $history || ! is_array( $history ) ) {
							$cost = false;
						} else {
							$cost = array_values( array_slice( $history, - 1 ) )[0]['cost'];
						}
						break;
					case 'use_current_cost':
						$cost = alg_wc_cog()->core->products->get_product_cost( $args['product_id'] );
						break;

				}
			}
			return $cost;
		}

		/**
		 * sanitize_math_expression.
		 *
		 * @version 2.4.2
		 * @since   2.4.2
		 *
		 * @param $expression
		 *
		 * @return null|string|string[]
		 */
		function sanitize_math_expression( $expression ) {
			// Remove whitespace from string.
			$expression = preg_replace( '/\s+/', '', $expression );

			// Trim invalid start/end characters.
			$expression = rtrim( ltrim( $expression, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );
			return $expression;
		}
	}
}

return new Alg_WC_Cost_of_Goods_Products_Add_Stock();