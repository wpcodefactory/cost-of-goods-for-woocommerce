<?php
/**
 * Cost of Goods for WooCommerce - Products - Add Stock.
 *
 * @version 3.3.6
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
		 * @version 3.1.8
		 * @since   2.8.2
		 */
		function __construct() {
			// Add product stock
			add_action( 'add_meta_boxes', array( $this, 'add_product_add_stock_meta_box' ), 10, 2 );
			add_action( 'save_post_product', array( $this, 'save_product_add_stock' ), PHP_INT_MAX, 2 );
			add_action( 'admin_head', array( $this, 'create_add_stock_style' ) );
			add_action( 'wp_ajax_get_add_stock_history_table', array( $this, 'get_add_stock_history_table_ajax' ) );
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
		 * @version 3.3.6
		 * @since   1.7.0
		 */
		function add_product_add_stock_meta_box( $post_type, $post ) {
			if ( ! apply_filters( 'alg_wc_cog_create_product_meta_box_validation', true ) ) {
				return;
			}
			if (
				$post &&
				! is_a( $post, '\Automattic\WooCommerce\Admin\Overrides\Order' ) &&
				is_a( $product = wc_get_product( $post->ID ), 'WC_Product' ) &&
				$this->is_add_stock_enabled() &&
				( $product->is_type( 'simple' ) || $product->is_type( 'variable' ) )
			) {
				$tip = wc_help_tip( __( 'Enter values and "Update" the product.', 'cost-of-goods-for-woocommerce' ) . ' ' .
				                    __( '"Stock" will be added to your inventory, and "Cost" will be used to calculate new average cost of goods for the product.', 'cost-of-goods-for-woocommerce' ) );
				add_meta_box( 'alg-wc-cog-add-stock',
					__( 'Add stock', 'cost-of-goods-for-woocommerce' ) . $tip,
					array( $this, 'product_add_stock_meta_box' ),
					'product',
					'side'
				);
			}
		}

		/**
		 * save_product_add_stock.
		 *
		 * @version 3.3.3
		 * @since   1.7.0
		 * @todo    [next] handle variable products (also unset `$_POST['variable_stock']`)
		 * @todo    [maybe] remove `$this->is_add_stock`
		 */
		function save_product_add_stock( $product_id, $post ) {
			if (
				$this->is_add_stock_enabled() &&
				! empty( $_POST['alg_wc_cog_add_stock'] ) &&
				(
					'do_nothing' !== ( $empty_cost_action = get_option( 'alg_wc_cog_products_add_stock_empty_cost_action', 'do_nothing' ) )
					||
					(
						'do_nothing' === $empty_cost_action
						&& ! empty( $_POST['alg_wc_cog_add_stock_cost'] )
					)
				)
			) {
				if ( isset( $_POST['alg_wc_cog_add_stock_variation_ids'] ) && ! empty( $add_stock_variation_ids = $_POST['alg_wc_cog_add_stock_variation_ids'] ) ) {
					foreach ( $add_stock_variation_ids as $variation_id ) {
						if ( $this->has_variation_manage_stock_enabled( $variation_id ) ) {
							$this->product_add_stock( array(
								'product_id' => (int) $variation_id,
								'stock'      => floatval( $_POST['alg_wc_cog_add_stock'] ),
								'stock_prev' => $this->get_variation_stock_prev_from_post( $variation_id ),
								'cost_prev'  => $this->get_variation_cost_prev_from_post( $variation_id ),
								'cost'       => floatval( $_POST['alg_wc_cog_add_stock_cost'] ),
							) );
							$this->maybe_unset_variable_stock_from_post( $variation_id );
						}
					}
				} else {
					if ( $this->has_parent_product_manage_stock_enabled( $product_id ) ) {
						$this->product_add_stock( array(
							'product_id' => (int) $product_id,
							'stock'      => floatval( $_POST['alg_wc_cog_add_stock'] ),
							'stock_prev' => isset( $_POST['_stock'] ) ? (int) $_POST['_stock'] : '',
							'cost_prev'  => isset( $_POST['_alg_wc_cog_cost'] ) ? alg_wc_cog_sanitize_cost( array( 'number' => $_POST['_alg_wc_cog_cost'] ) ) : '',
							'cost'       => alg_wc_cog_sanitize_cost( array( 'value' => $_POST['alg_wc_cog_add_stock_cost'] ) ),
						) );
						if ( isset( $_POST['_stock'] ) ) {
							unset( $_POST['_stock'] );
						}
					}
				}
			}
		}

		/**
		 * get_variation_stock_prev_from_post.
		 *
		 * @version 3.1.7
		 * @since   3.1.7
		 *
		 * @param $variation_id
		 *
		 * @return mixed|string
		 */
		function get_variation_stock_prev_from_post( $variation_id ) {
			$variation_stock_prev = '';
			$variation_key        = $this->find_variation_key_on_post( $variation_id );
			if (
				false !== $variation_key &&
				isset( $_POST['variable_stock'][ $variation_key ] )
			) {
				$variation_stock_prev = $_POST['variable_stock'][ $variation_key ];
			}

			return $variation_stock_prev;
		}

		/**
		 * get_variation_cost_prev_from_post.
		 *
		 * @version 3.3.3
		 * @since   3.1.7
		 *
		 * @param $variation_id
		 *
		 * @return mixed|string
		 */
		function get_variation_cost_prev_from_post( $variation_id ) {
			$variation_cost_prev = '';
			$variation_key        = $this->find_variation_key_on_post( $variation_id );
			if (
				false !== $variation_key &&
				isset( $_POST['variable_alg_wc_cog_cost'][ $variation_key ] )
			) {
				$variation_cost_prev = $_POST['variable_alg_wc_cog_cost'][ $variation_key ];
			}

			return alg_wc_cog_sanitize_cost( array( 'value' => $variation_cost_prev ) );
		}

		/**
		 * has_parent_product_manage_stock_enabled.
		 *
		 * @version 3.2.5
		 * @since   3.1.7
		 *
		 * @param $product_id
		 *
		 * @return bool
		 */
		function has_parent_product_manage_stock_enabled( $product_id ) {
			if (
				(
					isset( $_POST['_manage_stock'] ) &&
					'yes' === $_POST['_manage_stock']
				) ||
				(
					is_a( $product = wc_get_product( $product_id ), 'WC_Product' ) &&
					$product->get_manage_stock()
				)
			) {
				return true;
			}

			return false;
		}

		/**
		 * maybe_unset_variable_stock_from_post.
		 *
		 * @version 3.1.7
		 * @since   3.1.7
		 *
		 * @param $variation_id
		 *
		 * @return void
		 */
		function maybe_unset_variable_stock_from_post( $variation_id ) {
			$post_variation_key = $this->find_variation_key_on_post( $variation_id );
			if (
				false !== $post_variation_key &&
				isset( $_POST['variable_stock'] ) &&
				isset( $_POST['variable_stock'][ $post_variation_key ] )
			) {
				unset( $_POST['variable_stock'][ $post_variation_key ] );
			}
		}

		/**
		 * find_variation_key_on_post.
		 *
		 * @version 3.1.7
		 * @since   3.1.7
		 *
		 * @param $variation_id
		 *
		 * @return false|int|string
		 */
		function find_variation_key_on_post( $variation_id ) {
			$variation_key = false;
			if ( isset( $_POST['variable_post_id'] ) && ! empty( $_POST['variable_post_id'] ) ) {
				$variation_key = array_search( $variation_id, $_POST['variable_post_id'] );
			}

			return $variation_key;
		}

		/**
		 * has_variation_manage_stock_enabled.
		 *
		 * @version 3.1.7
		 * @since   3.1.7
		 *
		 * @param $variation_id
		 *
		 * @return bool
		 */
		function has_variation_manage_stock_enabled( $variation_id ) {
			$has_variation_manage_stock_enabled = false;
			$variation_key                      = $this->find_variation_key_on_post( $variation_id );
			if (
				(
					false !== $variation_key &&
					isset( $_POST['variable_manage_stock'] ) && ! empty( $variable_manage_stock = $_POST['variable_manage_stock'] ) &&
					isset( $variable_manage_stock[ $variation_key ] ) && 'on' === $variable_manage_stock[ $variation_key ]
				) ||
				(
					is_a( $variation = wc_get_product( $variation_id ), 'WC_Product' ) &&
					$variation->get_manage_stock()
				)
			) {
				$has_variation_manage_stock_enabled = true;
			}

			return $has_variation_manage_stock_enabled;
		}

		/**
		 * create_add_stock_style.
		 *
		 * @version 3.1.7
		 * @since   3.1.7
		 *
		 * @return void
		 */
		function create_add_stock_style() {
			if (
				is_null( $screen = get_current_screen() ) ||
				'product' !== $screen->post_type ||
				'post' !== $screen->base
			) {
				return;
			}
			?>
			<style>
                .alg-wc-cog-variations-box {
                    min-height: 42px;
                    max-height: 200px;
                    overflow: auto;
	                margin-top:5px;
                    padding: 0 0.9em;
                    border: solid 1px #dcdcde;
                    background-color: #fff;
                }
                .alg-wc-cog-variations-box li{
	                margin:0 0 2px 0;
                    padding: 0;
                    line-height: 1.69230769;
                    word-wrap: break-word;
                }
                @media screen and (max-width: 782px){
                    .alg-wc-cog-variations-box li{
                        margin-bottom:15px;
                    }
                }
                .alg-wc-cog-variations-box label{
	                vertical-align: baseline;
                }
                .alg-wc-cog-add-stock-history-table-container table{
                    margin-top:9px;
                }
                .alg-wc-cog-add-stock-variation-history-title .spinner{
                    vertical-align: middle;
                    float: none;
                    margin: 0 0 0 5px;
                }
			</style>
			<?php
		}

		/**
		 * product_add_stock_meta_box.
		 *
		 * @version 3.1.7
		 * @since   1.7.0
		 * @todo    [next] add option to delete all/selected history
		 */
		function product_add_stock_meta_box( $post ) {
			$product = wc_get_product( $post->ID );
			if ( ! is_a( $product, 'WC_Product' ) ) {
				return;
			}

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

			if ( $product->is_type( 'variable' ) ) {
				$html .= $this->get_variations_to_update_html( $product );
				$html .= $this->get_variations_history_html( $product );
			} else {
				$history = get_post_meta( get_the_ID(), '_alg_wc_cog_cost_history', true );
				if ( $history ) {
					$history_rows = '';
					foreach ( $history as $date => $record ) {
						$history_rows .= '<tr><td>' . date( 'Y-m-d', $date ) . '</td><td>' . $record['stock'] . '</td><td>' . alg_wc_cog_format_cost( $record['cost'] ) . '</td></tr>';
					}
					$html .= '' .
					         '<details style="margin-top:5px">' .
					         '<summary style="cursor:pointer">' . __( 'History', 'cost-of-goods-for-woocommerce' ) . '</summary>' .
					         $this->get_add_stock_history_table( get_the_ID() ) .
					         '</details>';
				}
			}

			echo '<div style="margin-top:10px;clear:both"></div>';
			echo $html;
		}

		/**
		 * get_add_stock_history_table.
		 *
		 * @version 3.2.5
		 * @since   3.2.5
		 *
		 * @param $product_id
		 *
		 * @return string
		 */
		function get_add_stock_history_table( $product_id ) {
			$history = get_post_meta( $product_id, '_alg_wc_cog_cost_history', true );
			if ( ! is_array( $history ) ) {
				return '';
			}
			$history_date_order = get_option( 'alg_wc_cog_products_add_stock_history_date_order', 'desc' );
			'asc' === $history_date_order ? ksort( $history ) : krsort( $history );
			$format = get_option( 'alg_wc_cog_products_add_stock_history_date_format', 'Y-m-d' );
			$html   = '';
			if ( $history ) {
				$history_rows = '';
				foreach ( $history as $date => $record ) {
					$history_rows .= '<tr><td>' . date( $format, $date ) . '</td><td>' . $record['stock'] . '</td><td>' . alg_wc_cog_format_cost( $record['cost'] ) . '</td></tr>';
				}
				$html .= '' .
				         '<table class="widefat striped"><tbody>' .
				         '<tr>' .
				         '<th>' . __( 'Date', 'cost-of-goods-for-woocommerce' ) . '</th>' .
				         '<th>' . __( 'Stock', 'cost-of-goods-for-woocommerce' ) . '</th>' .
				         '<th>' . __( 'Cost', 'cost-of-goods-for-woocommerce' ) . '</th>' .
				         '</tr>' .
				         $history_rows .
				         '</tbody></table>';
			}

			return $html;
		}

		/**
		 * get_add_stock_history_table_ajax.
		 *
		 * @version 3.1.7
		 * @since   3.1.7
		 *
		 * @return void
		 */
		function get_add_stock_history_table_ajax() {
			check_ajax_referer( 'add_stock_history_table_nonce', 'security' );
			$variation_id = intval( $_POST['variation_id'] );
			if ( ! empty( $variation_id ) ) {
				$table = $this->get_add_stock_history_table( $variation_id );
				wp_send_json_success( array( 'html' => $table ) );
			}
			die;
		}

		/**
		 * get_variations_history_html.
		 *
		 * @version 3.1.7
		 * @since   3.1.7
		 *
		 * @param $parent_product
		 *
		 * @return string
		 */
		function get_variations_history_html( $parent_product ) {
			$html = '';
			$html .= '<h4 class="alg-wc-cog-add-stock-variation-history-title" style="margin-top:9px;margin-bottom:3px"> ' . __( 'Variation history', 'cost-of-goods-for-woocommerce' ) . '<span class="spinner"></span></h4>';

			$variations               = $parent_product->get_available_variations();
			$variations_dropdown_html = '';

			if ( ! empty( $variations ) ) {
				$variations_dropdown_html .= '<select style="width:100%" class="" name="alg_wc_cog_add_stock_variation_id_history">';
				$variations_dropdown_html .= '<option value="">' . __( 'Select variation', 'cost-of-goods-for-woocommerce' ) . '</option>';

				foreach ( $variations as $variation_data ) {
					$variation_id  = $variation_data['variation_id'];
					$variation_obj = wc_get_product( $variation_id );

					// Variation name.
					$variation_name = $variation_obj->get_formatted_name();
					$variation_name = str_replace( $variation_obj->get_title(), "", $variation_name );
					$variation_name = preg_replace( '/^\s\-\s/', '', $variation_name );
					$variation_name = preg_replace( '/\<span.*\<\/span\>/', '', $variation_name );

					$variations_dropdown_html .= '<option value="' . esc_attr( $variation_id ) . '">' . esc_html( $variation_name ) . '</option>';
				}

				$variations_dropdown_html .= '</select>';
			}

			$html .= $variations_dropdown_html;
            $html.='
            <div class="alg-wc-cog-add-stock-history-table-container"></div>
            ';
			$html .= $this->get_variations_history_script();

			return $html;
		}

		/**
		 * get_variations_history_script.
		 *
		 * @version 3.1.7
		 * @since   3.1.7
		 *
		 * @return false|string
		 */
		function get_variations_history_script() {
			ob_start();
			$ajax_nonce = wp_create_nonce( "add_stock_history_table_nonce" );
			?>
            <script>
                (function () {
                    const dropdown = document.querySelector('select[name="alg_wc_cog_add_stock_variation_id_history"]');
                    dropdown.addEventListener('change', function (event) {
                        jQuery('.alg-wc-cog-add-stock-variation-history-title .spinner').addClass('is-active');
                        let data = {
                            action: 'get_add_stock_history_table',
                            security: '<?php echo $ajax_nonce; ?>',
                            variation_id: event.target.value
                        };
                        jQuery.post(ajaxurl, data, function (response) {
                            jQuery('.alg-wc-cog-add-stock-variation-history-title .spinner').removeClass('is-active');
                            if (response.success) {
                                jQuery('.alg-wc-cog-add-stock-history-table-container').html(response.data.html);
                            } else {
                                jQuery('.alg-wc-cog-add-stock-history-table-container').html('');
                            }
                        });
                    });
                }());
            </script>
			<?php
			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}

		/**
		 * get_variations_to_update_html.
		 *
		 * @version 3.1.7
		 * @since   3.1.7
		 *
		 * @param $parent_product
		 *
		 * @return string
		 */
		function get_variations_to_update_html( $parent_product ) {
			$html = '';
			$tip  = wc_help_tip( __( 'If no variation is selected, the parent product will be updated.', 'cost-of-goods-for-woocommerce' ) );
			$html .= '<h4 style="margin-top:9px;margin-bottom:0px"> ' . __( 'Update variation(s)', 'cost-of-goods-for-woocommerce' ) . $tip . '</h4>';
			$html .= '<div class="alg-wc-cog-variations-box">' .
			         '{variations}' .
			         '</div>';

			$variations      = $parent_product->get_available_variations();
			$variations_html = '';

			// Check if variations exist.
			if ( $variations ) {
				$variations_html .= '<form><ul>';

				foreach ( $variations as $variation_data ) {
					$variation_id  = $variation_data['variation_id'];
					$variation_obj = wc_get_product( $variation_id );

					// Variation name.
					$variation_name = $variation_obj->get_formatted_name();
					$variation_name = str_replace( $variation_obj->get_title(), "", $variation_name );
					$variation_name = preg_replace( '/^\s\-\s/', '', $variation_name );

					// Display variation attributes as checkboxes.
					$variations_html .= '<li>';
					$variations_html .= '<input id="alg_wc_cog_add_stock_variation_' . esc_attr( $variation_id ) . '" type="checkbox" name="alg_wc_cog_add_stock_variation_ids[]" value="' . esc_attr( $variation_id ) . '"/> ';
					$variations_html .= '<label for="alg_wc_cog_add_stock_variation_' . esc_attr( $variation_id ) . '">' . $variation_name . '</label>';
					$variations_html .= '</li>';
				}

				$variations_html .= '</ul></form>';
			}

			$array_from_to = array(
				'{variations}' => $variations_html,
			);

			$html = str_replace( array_keys( $array_from_to ), $array_from_to, $html );

			return $html;
		}

		/**
		 * calculate_add_stock_cost.
		 *
		 * @version 3.3.3
		 * @since   2.4.2
		 *
		 * @param   null  $args
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
			$cost_now = (float) WC_Eval_Math::evaluate( $cost_calculation_template );
			return $cost_now;
		}

		/**
		 * product_add_stock.
		 *
		 * @version 3.3.3
		 * @since   1.7.0
		 * @todo    [next] maybe use `$product = wc_get_product( $product_id )`, i.e. `$product->get_stock_quantity()`, `$product->set_stock_quantity( $stock_now )` and `$product->save()`?
		 * @todo    [maybe] `$cost_now`: round?
		 *
		 * @return bool|mixed
		 */
		function product_add_stock( $args = null  ) {
			$args = wp_parse_args( $args, array(
				'product_id' => '',
				'stock'      => '',
				'stock_prev' => '',
				'cost'       => '',
				'cost_prev'  => '',
				'update_stock' => true
			) );
			$product_id = intval( $args['product_id'] );
			$stock      = intval( $args['stock'] );
			$cost       = floatval( $args['cost'] );
			$update_stock = $args['update_stock'];
			$cost = $this->get_add_stock_cost( array(
				'cost'       => $cost,
				'product_id' => $product_id
			) );
			$stock = (int) $stock;
			$stock_prev = ! empty( $args['stock_prev'] ) ? (float) $args['stock_prev'] : (int) get_post_meta( $product_id, '_stock', true );
			if ( ! $stock_prev || '' === $stock_prev ) {
				$stock_prev = 0;
			}
			$stock_now = (int) ( $stock_prev + $stock );
			if ( 0 != $stock_now && false !== $cost) {
				$cost_prev = ! empty( $args['cost_prev'] ) ? (float) $args['cost_prev'] : (float) alg_wc_cog()->core->products->get_product_cost( $product_id );
				if ( ! $cost_prev ) {
					$cost_prev = 0;
				}
				$cost_now = (float) @$this->calculate_add_stock_cost( array(
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
				if ( $update_stock ) {
					$stock_operation = $this->calculate_update_stock_operation( $product_id, $stock_now );
					wc_update_product_stock( $product_id, abs( $stock ), $stock_operation );
					if ( 'set' === $stock_operation ) {
						update_post_meta( $product_id, '_stock', $stock_now );
					}
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
		 * @param   null  $args
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