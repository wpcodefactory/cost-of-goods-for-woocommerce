<?php
/**
 * Cost of Goods for WooCommerce - Products - Cost archive.
 *
 * @version 3.9.8
 * @since   2.8.2
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Products_Cost_Archive' ) ) {

	class Alg_WC_Cost_of_Goods_Products_Cost_Archive {

		/**
		 * is_cost_archive_metabox_enabled.
		 *
		 * @since 2.8.2
		 *
		 * @var null
		 */
		protected $is_cost_archive_metabox_enabled = null;

		/**
		 * Constructor.
		 *
		 * @version 3.1.7
		 * @since   2.8.2
		 */
		function __construct() {
			// Cost archive.
			add_action( 'update_post_meta', array( $this, 'on_update_post_meta' ), 10, 4 );
			add_action( 'add_post_meta', array( $this, 'save_cost_archive' ), 10, 3 );
			add_action( 'wp_ajax_get_cost_archive_table', array( $this, 'get_cost_archive_table_ajax' ) );
			// Meta box.
			add_action( 'add_meta_boxes', array( $this, 'add_cost_archive_meta_box' ) );
		}

		/**
		 * add_cost_archive_meta_box.
		 *
		 * @version 2.8.2
		 * @since   1.8.2
		 */
		function add_cost_archive_meta_box() {
			if ( ! apply_filters( 'alg_wc_cog_create_product_meta_box_validation', true ) ) {
				return;
			}
			if ( $this->is_cost_archive_metabox_enabled() ) {
				if ( ( $product = wc_get_product( get_the_ID() ) ) && is_a( $product, 'WC_Product' ) ) {
					add_meta_box( 'alg-wc-cog-cost-archive',
						__( 'Cost archive', 'cost-of-goods-for-woocommerce' ),
						array( $this, 'display_product_cost_archive_metabox' ),
						'product',
						'side'
					);
				}
			}
		}

		/**
		 * get_product_cost_last_update_date.
		 *
		 * @version 3.9.8
		 * @since   3.9.7
		 *
		 * @param   null  $args
		 *
		 * @throws Exception
		 * @return void
		 */
		function get_product_cost_last_update_date( $args = null ) {
			if ( 'yes' !== alg_wc_cog_get_option( 'alg_wc_cog_save_cost_archive', 'no' ) ) {
				return null;
			}

			// Args.
			$args = wp_parse_args( $args, array(
				'product_id'        => '',
				'return_method'     => 'date', // 'date','template',
				'maybe_create_meta' => true
			) );
			$update_date_meta = '_alg_wc_cog_last_update_date';
			$product_id = $args['product_id'];
			$product = wc_get_product( $product_id );
			$return_method = $args['return_method'];
			$maybe_create_meta = filter_var( $args['maybe_create_meta'], FILTER_VALIDATE_BOOLEAN );

			// Archive.
			if ( empty( $date = $product->get_meta( $update_date_meta, true ) ) ) {
				$archive = $this->get_product_cost_archive( array(
					'product_id' => $product_id,
					'order'      => 'desc',
					'orderby'    => 'update_datetime'
				) );
				if ( ! empty( $archive ) ) {
					$date = $archive[0]['update_date'];
					if ( $maybe_create_meta ) {
						$product->update_meta_data( $update_date_meta, $date );
						$product->save();
					}
				}
			}

			if ( ! empty( $date ) ) {
				$date_formatted = wp_date( get_option( 'alg_wc_cog_save_cost_archive_date_format', 'Y-m-d' ), $date );
				switch ( $return_method ) {
					case 'date':
						return $date;
						break;
					case 'template':
						$template = alg_wc_cog_get_option( 'alg_wc_cog_last_update_date_template', __( 'Last update date: %last_update_date%', 'cost-of-goods-for-woocommerce' ) );
						$data     = array(
							'%last_update_date%' => $date_formatted,
						);

						return '<span class="alg-wc-cog-last-cost-update-date">' . str_replace( array_keys( $data ), array_values( $data ), $template ) . '</span>';
						break;
				}
			} else {
				return null;
			}
		}

		/**
		 * get_product_cost_archive.
		 *
		 * @version 2.9.3
		 * @since   2.8.2
		 *
		 * @param $args
		 *
		 * @return array
		 * @throws Exception
		 */
		function get_product_cost_archive( $args = null ) {
			$args = wp_parse_args( $args, array(
				'product_id' => '',
				'order'      => strtoupper( get_option( 'alg_wc_cog_cost_archive_date_order', 'desc' ) ),
				'orderby'    => 'update_datetime'
			) );
			$order      = strtoupper( $args['order'] );
			$orderby    = $args['orderby'];
			$product_id = intval( $args['product_id'] );
			$use_mysql_regexp_substr = 'yes' === get_option( 'alg_wc_cog_save_cost_archive_mysql_regexp_substr', 'yes' );
			global $wpdb;
			if ( $use_mysql_regexp_substr ) {
				$query = "
					SELECT meta_value, FROM_UNIXTIME(REGEXP_SUBSTR(meta_value,'(?<=update_date\";i:).+?(?=;)')) AS update_datetime
					FROM {$wpdb->postmeta}
					WHERE post_id = %d AND meta_key = %s
				";
				if ( ! empty( $orderby ) ) {
					$query .= "ORDER BY {$orderby} {$order}";
				}
			} else {
				$query = "
					SELECT meta_value
					FROM {$wpdb->postmeta}
					WHERE post_id = %d AND meta_key = %s
				";
			}
			$results          = $wpdb->get_results(
				$wpdb->prepare( $query, $product_id, '_alg_wc_cog_cost_archive' ),
				ARRAY_A
			);
			if ( ! $use_mysql_regexp_substr ) {
				foreach ( $results as $key => $result ) {
					$results[ $key ]['update_datetime'] = wp_date( "Y-m-d H:i:s", unserialize( $result['meta_value'] )['update_date'] );
				}
				if ( 'update_datetime' === $orderby && 'desc' === strtolower( $order ) ) {
					usort( $results, function ( $a, $b ) {
						return new DateTime( $b['update_datetime'] ) <=> new DateTime( $a['update_datetime'] );
					} );
				}
			}
			$filtered_results = array();
			foreach ( $results as $result ) {
				$arr                    = unserialize( $result['meta_value'] );
				$arr['update_datetime'] = $result['update_datetime'];
				$filtered_results[]     = $arr;
			}
			return $filtered_results;
		}

		/**
		 * product_add_stock_meta_box.
		 *
		 * @version 3.1.7
		 * @since   2.8.2
		 * @todo    [next] add option to delete all/selected history
		 */
		function display_product_cost_archive_metabox( $post ) {
			$product = wc_get_product( $post->ID );
			if ( ! is_a( $product, 'WC_Product' ) ) {
				return;
			}
			if ( $product->is_type( 'variable' ) ) {
				echo $this->get_variations_archive_html( $product );
			} else {
				echo $this->get_product_cost_archive_table( $product );
			}
		}

		/**
		 * get_cost_archive_table_ajax.
		 *
		 * @version 3.1.7
		 * @since   3.1.7
		 *
		 * @return void
		 * @throws Exception
		 */
		function get_cost_archive_table_ajax() {
			check_ajax_referer( 'cost_archive_table_nonce', 'security' );
			$variation_id = intval( $_POST['variation_id'] );
			if ( ! empty( $variation_id ) ) {
				$table = $this->get_product_cost_archive_table( wc_get_product( $variation_id ) );
				wp_send_json_success( array( 'html' => $table ) );
			}
			die;
		}

		/**
		 * get_product_cost_archive_table.
		 *
		 * @version 3.3.3
		 * @since   3.1.7
		 *
		 * @param $product
		 *
		 * @return string
		 * @throws Exception
		 */
		function get_product_cost_archive_table( $product ) {
			$product_cost_archive = $this->get_product_cost_archive( array(
				'product_id' => $product->get_id()
			) );
			$html='';
			if ( empty( $product_cost_archive ) ) {
				$html.= '<p>'.__( 'There isn\'t a cost archive for this product yet.', 'cost-of-goods-for-woocommerce' ).'</p>';
				$html.= '<p>' . sprintf( __( 'Please, check if the option %s is enabled and then update the cost.', 'cost-of-goods-for-woocommerce' ), '<strong><a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_cost_of_goods' ) . '">' . __( 'Products > Cost archive > Save cost archive', 'cost-of-goods-for-woocommerce' ) . '</a></strong>' ) . '</p>';
			} else {
				$table_columns = array(
					'update_date'     => __( 'Update date', 'cost-of-goods-for-woocommerce' ),
					'prev_cost_value' => __( 'Previous cost', 'cost-of-goods-for-woocommerce' ),
					'new_cost_value'  => __( 'New cost', 'cost-of-goods-for-woocommerce' ),
				);
				$table_rows    = array();
				$dots_and_commas_operation = 'comma-to-dot';
				foreach ( $product_cost_archive as $cost_info ) {
					$prev_cost = alg_wc_cog_sanitize_number( array(
						'value'                    => $cost_info['prev_cost_value'],
						'dots_and_commas_operation' => $dots_and_commas_operation
					) );
					$new_cost_value = alg_wc_cog_sanitize_number( array(
						'value'                    => $cost_info['new_cost_value'],
						'dots_and_commas_operation' => $dots_and_commas_operation
					) );
					$table_rows[] = array(
						'val_by_col' => array(
							wp_date( get_option( 'alg_wc_cog_save_cost_archive_date_format', 'Y-m-d' ), $cost_info['update_date'] ),
							alg_wc_cog_format_cost( $prev_cost ),
							alg_wc_cog_format_cost( $new_cost_value )
						)
					);
				}
				$table = alg_wc_cog_get_html_table_structure( array(
					'table_classes' => array( 'widefat', 'striped' ),
					'cols'          => $table_columns,
					'rows'          => $table_rows
				) );
				$html.= '<div style="margin-top:10px;clear:both"></div>';
				$html.= $table;
			}
			return $html;
		}

		/**
		 * on_update_post_meta.
		 *
		 * @version 2.8.2
		 * @since   2.8.2
		 *
		 * @param $meta_id
		 * @param $post_id
		 * @param $meta_key
		 * @param $meta_value
		 */
		function on_update_post_meta( $meta_id, $post_id, $meta_key, $meta_value ) {
			$this->save_cost_archive( $post_id, $meta_key, $meta_value );
		}

		/**
		 * save_cost_history_on_cost_update
		 *
		 * @version 3.9.8
		 * @since   2.8.2
		 *
		 * @param $post_id
		 * @param $meta_key
		 * @param $meta_value
		 */
		function save_cost_archive( $post_id, $meta_key, $meta_value ) {
			if (
				in_array( $meta_key, array(
					'_alg_wc_cog_cost',
				) ) &&
				'yes' === get_option( 'alg_wc_cog_save_cost_archive', 'no' ) &&
				is_a( $product = wc_get_product( $post_id ), 'WC_Product' )
			) {
				$prev_cost_value = (float) alg_wc_cog()->core->products->get_product_cost( $post_id );
				if ( $prev_cost_value !== (float) $meta_value ) {
					$update_date = current_datetime()->getTimestamp();
					$archive = array(
						'update_date'     => $update_date,
						'prev_cost_value' => $prev_cost_value,
						'new_cost_value'  => $meta_value
					);
					$product->add_meta_data( '_alg_wc_cog_cost_archive', $archive, false );
					$product->update_meta_data( '_alg_wc_cog_last_update_date', $update_date );
					$product->save();
				}
			}
		}

		/**
		 * is_add_stock_enabled.
		 *
		 * @version 2.8.2
		 * @since   2.8.2
		 *
		 * @return bool|null
		 */
		function is_cost_archive_metabox_enabled() {
			if ( is_null( $this->is_cost_archive_metabox_enabled ) ) {
				$this->is_cost_archive_metabox_enabled = ( 'yes' === get_option( 'alg_wc_cog_cost_archive_metabox', 'no' ) );
			}
			return $this->is_cost_archive_metabox_enabled;
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
		function get_variations_archive_html( $parent_product ) {
			$html = '';
			$html .= '<h4 class="alg-wc-cog-cost-archive-variation-title" style="margin-top:9px;margin-bottom:3px"> ' . __( 'Variation archive', 'cost-of-goods-for-woocommerce' ) . '<span class="spinner"></span></h4>';

			$variations               = $parent_product->get_available_variations();
			$variations_dropdown_html = '';

			if ( ! empty( $variations ) ) {
				$variations_dropdown_html .= '<select style="width:100%" class="" name="alg_wc_cog_cost_archive_variation_id">';
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
            <div class="alg-wc-cog-cost-archive-table-container"></div>
            ';
			$html .= $this->get_variations_archive_script();

			return $html;
		}

		/**
		 * get_variations_history_script.
		 *
		 * @version 3.9.7
		 * @since   3.1.7
		 *
		 * @return false|string
		 */
		function get_variations_archive_script() {
			ob_start();
			$ajax_nonce = wp_create_nonce( "cost_archive_table_nonce" );
			?>
			<script>
				( function () {
					const dropdown = document.querySelector( 'select[name="alg_wc_cog_cost_archive_variation_id"]' );
					dropdown.addEventListener( 'change', function ( event ) {
						jQuery( '.alg-wc-cog-cost-archive-variation-title .spinner' ).addClass( 'is-active' );
						let data = {
							action: 'get_cost_archive_table',
							security: '<?php echo $ajax_nonce; ?>',
							variation_id: event.target.value
						};
						jQuery.post( ajaxurl, data, function ( response ) {
							jQuery( '.alg-wc-cog-cost-archive-variation-title .spinner' ).removeClass( 'is-active' );
							if ( response.success ) {
								jQuery( '.alg-wc-cog-cost-archive-table-container' ).html( response.data.html );
							} else {
								jQuery( '.alg-wc-cog-cost-archive-table-container' ).html( '' );
							}
						} );
					} );
				}() );
			</script>
			<?php
			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}

	}
}

return new Alg_WC_Cost_of_Goods_Products_Cost_Archive();