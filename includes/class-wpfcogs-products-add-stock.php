<?php
/**
 * Cost of Goods for WooCommerce - Products - Add Stock.
 *
 * @version 4.1.6
 * @since   2.8.2
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFCOGS_Products_Add_Stock' ) ) {

	class WPFCOGS_Products_Add_Stock {

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
		 * @version 3.7.0
		 * @since   2.8.2
		 */
		function __construct() {
			// Add product stock.
			add_action( 'add_meta_boxes', array( $this, 'add_product_add_stock_meta_box' ), 10, 2 );
			add_action( 'save_post_product', array( $this, 'save_product_add_stock' ), PHP_INT_MAX, 2 );
			add_action( 'admin_head', array( $this, 'create_add_stock_style' ) );
			add_action( 'wp_ajax_wpfcogs_get_add_stock_history_table', array( $this, 'wpfcogs_get_add_stock_history_table_ajax' ) );

			// Del add stock history date.
			add_action( 'wp_ajax_wpfcogs_del_add_stock_history_date', array( $this, 'wpfcogs_del_add_stock_history_date_ajax' ) );
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
			if ( ! apply_filters( 'wpfcogs_create_product_meta_box_validation', true ) ) {
				return;
			}
			if (
				$post &&
				'product' === $post_type &&
				! is_a( $post, '\Automattic\WooCommerce\Admin\Overrides\Order' ) &&
				is_a( $product = wc_get_product( $post->ID ), 'WC_Product' ) &&
				$this->is_add_stock_enabled() &&
				( $product->is_type( 'simple' ) || $product->is_type( 'variable' ) )
			) {
				$tip = wc_help_tip( __( 'Enter values and "Update" the product.', 'cost-of-goods-for-woocommerce' ) . ' ' .
				                    __( '"Stock" will be added to your inventory, and "Cost" will be used to calculate new average cost of goods for the product.', 'cost-of-goods-for-woocommerce' ) );
				add_meta_box( 'wpfcogs-add-stock',
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
		 * @version 4.1.6
		 * @since   1.7.0
		 * @todo    [next] handle variable products (also unset `$_POST['variable_stock']`)
		 * @todo    [maybe] remove `$this->is_add_stock`
		 */
		function save_product_add_stock( $product_id, $post ) {
			static $already_ran = false;
			$is_valid_request = false;

			if (
				$already_ran
			) {
				return;
			}

			if ( isset( $_POST['woocommerce_meta_nonce'] ) ) {
				$is_valid_request = wp_verify_nonce(
					sanitize_text_field( wp_unslash( $_POST['woocommerce_meta_nonce'] ) ),
					'woocommerce_save_data'
				);
			} elseif ( isset( $_POST['_wpnonce'] ) ) {
				$is_valid_request = wp_verify_nonce(
					sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ),
					'update-post_' . $product_id
				) || wp_verify_nonce(
					sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ),
					'update-product_' . $product_id
				);
			}

			if ( ! $is_valid_request ) {
				return;
			}

			$add_stock_raw      = isset( $_POST['wpfcogs_add_stock'] ) ? sanitize_text_field( wp_unslash( $_POST['wpfcogs_add_stock'] ) ) : '';
			$add_stock_cost_raw = isset( $_POST['wpfcogs_add_stock_cost'] ) ? sanitize_text_field( wp_unslash( $_POST['wpfcogs_add_stock_cost'] ) ) : '';
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- Sanitized via sanitize_text_field() and (int) cast on next line.
			$raw_parent_stock   = isset( $_POST['_stock'] ) ? sanitize_text_field( wp_unslash( $_POST['_stock'] ) ) : '';
			$parent_stock_prev  = (int) $raw_parent_stock;
			$parent_cost_prev   = isset( $_POST['_alg_wc_cog_cost'] ) ? wpfcogs_sanitize_cost( array( 'value' => sanitize_text_field( wp_unslash( $_POST['_alg_wc_cog_cost'] ) ) ) ) : '';
			$manage_stock       = isset( $_POST['_manage_stock'] ) ? sanitize_text_field( wp_unslash( $_POST['_manage_stock'] ) ) : '';
			$variation_post_ids = isset( $_POST['variable_post_id'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['variable_post_id'] ) ) : array();
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- Sanitized via array_map( 'wc_clean', ... ) on next line.
			$raw_variation_stocks = isset( $_POST['variable_stock'] ) ? (array) wp_unslash( $_POST['variable_stock'] ) : array();
			$variation_stocks     = array_map( 'wc_clean', $raw_variation_stocks );
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- Sanitized via array_map( sanitize_variation_cost(), ... ) on next line.
			$raw_variation_costs = isset( $_POST['variable_wpfcogs_cost'] ) ? (array) wp_unslash( $_POST['variable_wpfcogs_cost'] ) : array();
			$variation_costs     = array_map( array( $this, 'sanitize_variation_cost' ), $raw_variation_costs );
			$variation_manage_stock = isset( $_POST['variable_manage_stock'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['variable_manage_stock'] ) ) : array();
			$add_stock          = wpfcogs_sanitize_number( array( 'value' => $add_stock_raw, 'dots_and_commas_operation' => 'comma-to-dot' ) );
			$add_stock_cost     = wpfcogs_sanitize_cost( array( 'value' => $add_stock_cost_raw ) );
			$empty_cost_action  = apply_filters( 'wpfcogs_products_add_stock_empty_cost_action', 'do_nothing' );
			$add_stock_variation_ids = isset( $_POST['wpfcogs_add_stock_variation_ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['wpfcogs_add_stock_variation_ids'] ) ) : array();
			if (
				$this->is_add_stock_enabled() &&
				'' !== (string) $add_stock_raw &&
				(
					'do_nothing' !== $empty_cost_action
					||
					(
						'do_nothing' === $empty_cost_action
						&& '' !== (string) $add_stock_cost_raw
					)
				)
			) {
				if ( ! empty( $add_stock_variation_ids ) ) {
					foreach ( $add_stock_variation_ids as $variation_id ) {
						$variation_key = $this->find_variation_key_on_post( $variation_id, $variation_post_ids );
						if ( $this->has_variation_manage_stock_enabled( $variation_id, $variation_key, $variation_manage_stock ) ) {
							$this->product_add_stock( array(
								'product_id' => (int) $variation_id,
								'stock'      => (float) $add_stock,
								'stock_prev' => $this->get_variation_stock_prev_from_post( $variation_key, $variation_stocks ),
								'cost_prev'  => $this->get_variation_cost_prev_from_post( $variation_key, $variation_costs ),
								'cost'       => (float) $add_stock_cost,
							) );
							if ( false !== $variation_key && isset( $_POST['variable_stock'][ $variation_key ] ) ) {
								unset( $_POST['variable_stock'][ $variation_key ] );
							}
						}
					}
				} else {
					if ( $this->has_parent_product_manage_stock_enabled( $product_id, $manage_stock ) ) {
						$this->product_add_stock( array(
							'product_id' => (int) $product_id,
							'stock'      => (float) $add_stock,
							'stock_prev' => $parent_stock_prev,
							'cost_prev'  => $parent_cost_prev,
							'cost'       => (float) $add_stock_cost,
						) );
						if ( isset( $_POST['_stock'] ) ) {
							unset( $_POST['_stock'] );
						}
					}
				}
			}
			$already_ran = true;
		}

		/**
		 * get_variation_stock_prev_from_post.
		 *
		 * @version 4.1.5
		 * @since   3.1.7
		 *
		 * @param int   $variation_key Variation index.
		 * @param array $variation_stocks Variation stock values.
		 *
		 * @return mixed|string
		 */
		function get_variation_stock_prev_from_post( $variation_key, $variation_stocks ) {
			$variation_stock_prev = '';
			if (
				false !== $variation_key &&
				isset( $variation_stocks[ $variation_key ] )
			) {
				$variation_stock_prev = $variation_stocks[ $variation_key ];
			}

			return $variation_stock_prev;
		}

		/**
		 * get_variation_cost_prev_from_post.
		 *
		 * @version 4.1.5
		 * @since   3.1.7
		 *
		 * @param int   $variation_key Variation index.
		 * @param array $variation_costs Variation cost values.
		 *
		 * @return mixed|string
		 */
		function get_variation_cost_prev_from_post( $variation_key, $variation_costs ) {
			$variation_cost_prev = '';
			if (
				false !== $variation_key &&
				isset( $variation_costs[ $variation_key ] )
			) {
				$variation_cost_prev = $variation_costs[ $variation_key ];
			}

			return wpfcogs_sanitize_cost( array( 'value' => $variation_cost_prev ) );
		}

		/**
		 * has_parent_product_manage_stock_enabled.
		 *
		 * @version 4.1.5
		 * @since   3.1.7
		 *
		 * @param int    $product_id Product ID.
		 * @param string $manage_stock Posted manage stock value.
		 *
		 * @return bool
		 */
		function has_parent_product_manage_stock_enabled( $product_id, $manage_stock = '' ) {
			if (
				(
					'yes' === $manage_stock
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
		 * find_variation_key_on_post.
		 *
		 * @version 4.1.5
		 * @since   3.1.7
		 *
		 * @param int   $variation_id Variation product ID.
		 * @param array $variation_post_ids Variation post IDs.
		 *
		 * @return false|int|string
		 */
		function find_variation_key_on_post( $variation_id, $variation_post_ids = array() ) {
			$variation_key = false;
			if ( ! empty( $variation_post_ids ) ) {
				$variation_key = array_search( (int) $variation_id, $variation_post_ids, true );
			}

			return $variation_key;
		}

		/**
		 * has_variation_manage_stock_enabled.
		 *
		 * @version 4.1.5
		 * @since   3.1.7
		 *
		 * @param int        $variation_id Variation product ID.
		 * @param int|false  $variation_key Variation index.
		 * @param array      $variation_manage_stock Posted manage stock values.
		 *
		 * @return bool
		 */
		function has_variation_manage_stock_enabled( $variation_id, $variation_key = false, $variation_manage_stock = array() ) {
			$has_variation_manage_stock_enabled = false;
			if (
				(
					false !== $variation_key &&
					! empty( $variation_manage_stock ) &&
					isset( $variation_manage_stock[ $variation_key ] ) && 'on' === $variation_manage_stock[ $variation_key ]
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
		 * sanitize_variation_cost.
		 *
		 * @version 4.1.5
		 * @since   4.1.5
		 *
		 * @param string $cost Variation cost value.
		 *
		 * @return string
		 */
		function sanitize_variation_cost( $cost ) {
			return wpfcogs_sanitize_cost( array( 'value' => $cost ) );
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
                .wpfcogs-variations-box {
                    min-height: 42px;
                    max-height: 200px;
                    overflow: auto;
                    margin-top: 5px;
                    padding: 0 0.9em;
                    border: solid 1px #dcdcde;
                    background-color: #fff;
                }

                .wpfcogs-variations-box li {
                    margin: 0 0 2px 0;
                    padding: 0;
                    line-height: 1.69230769;
                    word-wrap: break-word;
                }

                @media screen and (max-width: 782px) {
                    .wpfcogs-variations-box li {
                        margin-bottom: 15px;
                    }
                }

                .wpfcogs-variations-box label {
                    vertical-align: baseline;
                }

                .wpfcogs-add-stock-history-table-container table {
                    margin-top: 9px;
                }

                .wpfcogs-add-stock-variation-history-title .spinner {
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
		 * @version 4.1.6
		 * @since   1.7.0
		 * @todo    [next] add option to delete all/selected history
		 */
		function product_add_stock_meta_box( $post ) {
			$product = wc_get_product( $post->ID );
			if ( ! is_a( $product, 'WC_Product' ) ) {
				return;
			}

			$negative_stock_allowed = 'yes' === get_option( 'alg_wc_cog_products_add_stock_negative_stock', 'no' );
			$add_stock_input_min    = $negative_stock_allowed ? '' : 'min="0"';

			$html = '';
			$html .= '<table class="widefat striped"><tbody>' .
			         '<tr>' .
			         '<th><label for="wpfcogs_add_stock">' . esc_html__( 'Stock', 'cost-of-goods-for-woocommerce' ) . '</label></th>' .
			         '<td><input name="wpfcogs_add_stock" id="wpfcogs_add_stock" class="short" type="number" ' . $add_stock_input_min . '></td>' .
			         '</tr>' .
			         '<tr>' .
			         '<th><label for="wpfcogs_add_stock_cost">' . esc_html__( 'Cost', 'cost-of-goods-for-woocommerce' ) . '</label></th>' .
			         '<td><input name="wpfcogs_add_stock_cost" id="wpfcogs_add_stock_cost" class="short wc_input_price" type="number" step="0.0001" min="0"></td>' .
			         '</tr>' .
			         '</tbody></table>';

			if ( $product->is_type( 'variable' ) ) {
				$html .= $this->get_variations_to_update_html( $product );
				$html .= $this->get_variations_history_html( $product );
			} else {
				$history = get_post_meta( get_the_ID(), '_alg_wc_cog_cost_history', true );
				if ( ! empty( $history ) && is_array( $history ) ) {
					$this->maybe_fix_history( $history );
					$html .= '' .
							 '<details style="margin-top:5px">' .
				'<summary style="cursor:pointer">' . esc_html__( 'History', 'cost-of-goods-for-woocommerce' ) . '</summary>' .
							 $this->wpfcogs_get_add_stock_history_table( get_the_ID() ) .
							 '</details>';
				}
			}

			echo '<div style="margin-top:10px;clear:both"></div>';
			$allowed_html = wp_kses_allowed_html( 'post' );
			$allowed_html['input']    = array( 'type' => true, 'name' => true, 'id' => true, 'class' => true, 'min' => true, 'step' => true );
			$allowed_html['select']   = array( 'style' => true, 'class' => true, 'name' => true );
			$allowed_html['option']   = array( 'value' => true, 'selected' => true );
			$allowed_html['script']   = array( 'type' => true );
			$allowed_html['style']    = array();
			$allowed_html['button']   = array( 'class' => true, 'type' => true );
			echo wp_kses( $html, $allowed_html );
		}

		/**
		 * Fixes invalid _wpfcogs_cost_history data if needed.
		 *
		 * @version 3.9.5
		 * @since   3.9.5
		 *
		 * @param $history
		 *
		 * @return array|mixed
		 */
		private function maybe_fix_history( $history ) {
			if ( ! is_array( $history ) ) {
				return $history;
			}
			$needs_repair = false;
			foreach ( $history as $date => $record ) {
				if ( ! is_array( $record ) ) {
					unset( $history[ $date ] );
					$needs_repair = true;
				}
			}
			if ( $needs_repair ) {
				update_post_meta( get_the_ID(), '_alg_wc_cog_cost_history', $history );
			}

			return $history;
		}

		/**
		 * get_add_stock_history_table.
		 *
		 * @version 4.1.5
		 * @since   3.2.5
		 *
		 * @param $product_id
		 *
		 * @return string
		 */
		function wpfcogs_get_add_stock_history_table( $product_id ) {
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
					$history_rows .= '<tr class="wpfcogs-add-stock-history-row" data-date="' . esc_attr( $date ) . '">' .
									 '<td>' . esc_html( wp_date( $format, (int) $date ) ) . '</td>' .
									 '<td>' . esc_html( $record['stock'] ) . '</td>' .
									 '<td>' . wp_kses_post( wpfcogs_format_cost( $record['cost'] ) ) . '</td>' .
									 '<td>' . '<button class="wpfcogs-del-add-stock-history-date" type="button"><span class="dashicons dashicons-trash"></span></button>' . '</td>' .
									 '</tr>';
				}
				$html .= '' .
						 '<table class="widefat striped"><tbody>' .
						 '<tr>' .
						 '<th>' . esc_html__( 'Date', 'cost-of-goods-for-woocommerce' ) . '</th>' .
						 '<th>' . esc_html__( 'Stock', 'cost-of-goods-for-woocommerce' ) . '</th>' .
						 '<th>' . esc_html__( 'Cost', 'cost-of-goods-for-woocommerce' ) . '</th>' .
						 '<th></th>' .
						 '</tr>' .
						 $history_rows .
						 '</tbody></table>';
			}

			$html .= $this->get_del_history_date_mechanism_js( $product_id );

			$html .= '<style>' .
					 '.wpfcogs-del-add-stock-history-date{cursor:pointer;background:none;border:none}' .
					 '</style>';

			return $html;
		}

		/**
		 * get_del_history_date_mechanism_js.
		 *
		 * @version 3.7.0
		 * @since   3.7.0
		 *
		 * @param $product_id
		 *
		 * @return false|string
		 */
		function get_del_history_date_mechanism_js( $product_id ) {
			ob_start();
			$php_to_js = array(
				'security'   => wp_create_nonce( 'alg-cog-del-add-stock-history-date-nonce' ),
				'action' => 'wpfcogs_del_add_stock_history_date',
				'product_id' => $product_id
			);
			?>
            <script>
                (function ($, window, document) {
                    let dataFromPHP = <?php echo wp_json_encode( $php_to_js );?>;
                    $(document).on('ready', function () {
                        $(document).on('click', '.wpfcogs-del-add-stock-history-date', function (e) {
                            e.preventDefault();
                            let parentRow = jQuery(this).closest('.wpfcogs-add-stock-history-row');
                            if (parentRow.length) {
                                let date = parentRow.data('date');
                                let jsToPhpData = {
                                    action: dataFromPHP.action,
                                    security: dataFromPHP.security,
                                    date: date,
                                    productId: dataFromPHP.product_id,
                                };
                                parentRow.hide(500, function () {
                                    $(this).remove();
                                });
                                $.post(ajaxurl, jsToPhpData, function (response) {
                                });
                            }
                        });
                    });
                })(jQuery, window, document);
            </script>
			<?php
			return ob_get_clean();
		}

		/**
		 * del_add_stock_history_date_ajax.
		 *
		 * @version 4.1.5
		 * @since   3.7.0
		 *
		 * @return void
		 */
		function wpfcogs_del_add_stock_history_date_ajax() {
			check_ajax_referer( 'alg-cog-del-add-stock-history-date-nonce', 'security' );
			if (
				current_user_can( 'edit_products' ) &&
				isset( $_POST['date'] ) && ! empty( $date = sanitize_text_field( wp_unslash( $_POST['date'] ) ) ) &&
				isset( $_POST['productId'] ) && is_a( $product = wc_get_product( absint( wp_unslash( $_POST['productId'] ) ) ), 'WC_Product' )
			) {
				$history_meta = '_alg_wc_cog_cost_history';
				$history      = $product->get_meta( $history_meta, true );
				if ( isset( $history[ $date ] ) ) {
					unset( $history[ $date ] );
					$product->update_meta_data( $history_meta, $history );
					$product->save();
				}
			}
		}

		/**
		 * get_add_stock_history_table_ajax.
		 *
		 * @version 4.1.6
		 * @since   3.1.7
		 *
		 * @return void
		 */
		function get_add_stock_history_table_ajax() {
			check_ajax_referer( 'add_stock_history_table_nonce', 'security' );
			if ( ! current_user_can( 'edit_products' ) ) {
				wp_send_json_error();
			}
			$variation_id = isset( $_POST['variation_id'] ) ? absint( wp_unslash( $_POST['variation_id'] ) ) : 0;
			if ( ! empty( $variation_id ) ) {
				$table = $this->wpfcogs_get_add_stock_history_table( $variation_id );
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
			$html .= '<h4 class="wpfcogs-add-stock-variation-history-title" style="margin-top:9px;margin-bottom:3px"> ' . esc_html__( 'Variation history', 'cost-of-goods-for-woocommerce' ) . '<span class="spinner"></span></h4>';

			$variations               = $parent_product->get_available_variations();
			$variations_dropdown_html = '';

			if ( ! empty( $variations ) ) {
				$variations_dropdown_html .= '<select style="width:100%" class="" name="wpfcogs_add_stock_variation_id_history">';
				$variations_dropdown_html .= '<option value="">' . esc_html__( 'Select variation', 'cost-of-goods-for-woocommerce' ) . '</option>';

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
			$html .= '
            <div class="wpfcogs-add-stock-history-table-container"></div>
            ';
			$html .= $this->get_variations_history_script();

			return $html;
		}

		/**
		 * get_variations_history_script.
		 *
		 * @version 4.1.5
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
                    const dropdown = document.querySelector('select[name="wpfcogs_add_stock_variation_id_history"]');
                    dropdown.addEventListener('change', function (event) {
                        jQuery('.wpfcogs-add-stock-variation-history-title .spinner').addClass('is-active');
                        let data = {
                            action: 'wpfcogs_get_add_stock_history_table',
							security: '<?php echo esc_js( $ajax_nonce ); ?>',
                            variation_id: event.target.value
                        };
                        jQuery.post(ajaxurl, data, function (response) {
                            jQuery('.wpfcogs-add-stock-variation-history-title .spinner').removeClass('is-active');
                            if (response.success) {
                                jQuery('.wpfcogs-add-stock-history-table-container').html(response.data.html);
                            } else {
                                jQuery('.wpfcogs-add-stock-history-table-container').html('');
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
		 * @version 4.1.5
		 * @since   3.1.7
		 *
		 * @param $parent_product
		 *
		 * @return string
		 */
		function get_variations_to_update_html( $parent_product ) {
			$html = '';
			$tip  = wc_help_tip( __( 'If no variation is selected, the parent product will be updated.', 'cost-of-goods-for-woocommerce' ) );
			$html .= '<h4 style="margin-top:9px;margin-bottom:0px"> ' . esc_html__( 'Update variation(s)', 'cost-of-goods-for-woocommerce' ) . $tip . '</h4>';
			$html .= '<div class="wpfcogs-variations-box">' .
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
					$variation_name = preg_replace( '/\<span.*\<\/span\>/', '', $variation_name );
					$variation_name = wp_strip_all_tags( $variation_name );

					// Display variation attributes as checkboxes.
					$variations_html .= '<li>';
					$variations_html .= '<input id="wpfcogs_add_stock_variation_' . esc_attr( $variation_id ) . '" type="checkbox" name="wpfcogs_add_stock_variation_ids[]" value="' . esc_attr( $variation_id ) . '"/> ';
					$variations_html .= '<label for="wpfcogs_add_stock_variation_' . esc_attr( $variation_id ) . '">' . esc_html( $variation_name ) . '</label>';
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
		 * @version 4.1.5
		 * @since   2.4.2
		 *
		 * @param null $args
		 *
		 * @return mixed
		 */
		function calculate_add_stock_cost( $args = null ) {
			$args                      = wp_parse_args( $args, array(
				'product_id'           => '',
				'template_variables'   => array(
					'%stock_prev%' => '',
					'%cost_prev%'  => '',
					'%stock%'      => '',
					'%cost%'       => '',
					'%stock_now%'  => '',
				),
				'calculation_template' => apply_filters( 'wpfcogs_products_add_stock_cost_calculation_template', '( %stock_prev% * %cost_prev% + %stock% * %cost% ) / %stock_now%' )
			) );
			$template_variables        = $args['template_variables'];
			$cost_calculation_template = $args['calculation_template'];
			$cost_calculation_template = $this->sanitize_math_expression( str_replace( array_keys( $template_variables ), $template_variables, $cost_calculation_template ) );
			include_once WC()->plugin_path() . '/includes/libraries/class-wc-eval-math.php';
			$cost_now = (float) WC_Eval_Math::evaluate( $cost_calculation_template );

			if ( 'yes' === wpfcogs_get_option( 'alg_wc_cog_products_add_stock_format_decimals', 'no' ) ) {
				$cost_now = wc_format_decimal( $cost_now, get_option( 'alg_wc_cog_costs_decimals', wc_get_price_decimals() ) );
			}

			return $cost_now;
		}

		/**
		 * product_add_stock.
		 *
		 * @version 3.9.4
		 * @since   1.7.0
		 * @return bool|mixed
		 * @todo    [maybe] `$cost_now`: round?
		 *
		 * @todo    [next] maybe use `$product = wc_get_product( $product_id )`, i.e. `$product->get_stock_quantity()`, `$product->set_stock_quantity( $stock_now )` and `$product->save()`?
		 */
		function product_add_stock( $args = null ) {
			$args         = wp_parse_args( $args, array(
				'product_id'   => '',
				'stock'        => '',
				'stock_prev'   => '',
				'cost'         => '',
				'cost_prev'    => '',
				'update_stock' => true
			) );
			$product_id = intval( $args['product_id'] );
			$stock = intval( $args['stock'] );
			$cost = floatval( $args['cost'] );
			$update_stock = $args['update_stock'];
			$product = wc_get_product( $product_id );
			$cost = $this->get_add_stock_cost( array(
				'cost'       => $cost,
				'product_id' => $product_id
			) );
			$stock        = (int) $stock;
			$stock_prev   = ! empty( $args['stock_prev'] ) ? (float) $args['stock_prev'] : (int) get_post_meta( $product_id, '_stock', true );
			if ( ! $stock_prev || '' === $stock_prev ) {
				$stock_prev = 0;
			}
			$stock_now = (int) ( $stock_prev + $stock );
			if ( 0 != $stock_now && false !== $cost ) {
				$cost_prev = ! empty( $args['cost_prev'] ) ? (float) $args['cost_prev'] : (float) wpfcogs()->core->products->get_product_cost( $product_id );
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
				$product->update_meta_data( '_alg_wc_cog_cost', $cost_now );
				$product->save();

				// Update Stock.
				if ( $update_stock ) {
					$stock_operation = $this->calculate_update_stock_operation( $product_id, $stock_now );
					wc_update_product_stock( $product_id, abs( $stock ), $stock_operation );
					if ( 'set' === $stock_operation ) {
						update_post_meta( $product_id, '_stock', $stock_now );
					}
				}

				// Update History.
				$history = get_post_meta( $product_id, '_alg_wc_cog_cost_history', true );
				if ( ! is_array( $history ) ) {
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
		 * @version 4.1.5
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
				'empty_cost_action' => apply_filters( 'wpfcogs_products_add_stock_empty_cost_action', 'do_nothing' )
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
						$cost = wpfcogs()->core->products->get_product_cost( $args['product_id'] );
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

return new WPFCOGS_Products_Add_Stock();