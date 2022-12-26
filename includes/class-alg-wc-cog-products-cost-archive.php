<?php
/**
 * Cost of Goods for WooCommerce - Products - Cost archive.
 *
 * @version 2.8.2
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
		 * @version 2.8.2
		 * @since   2.8.2
		 */
		function __construct() {
			// Cost archive.
			add_action( 'update_post_meta', array( $this, 'on_update_post_meta' ), 10, 4 );
			add_action( 'add_post_meta', array( $this, 'save_cost_archive' ), 10, 3 );
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
						//__( 'Cost of Goods', 'cost-of-goods-for-woocommerce' ) . ': ' . __( 'Cost archive', 'cost-of-goods-for-woocommerce' ),
						__( 'Cost archive', 'cost-of-goods-for-woocommerce' ),
						array( $this, 'display_product_cost_archive_table' ),
						'product',
						'side'
					);
				}
			}
		}

		/**
		 * get_product_cost_archive.
		 *
		 * @version 2.8.2
		 * @since   2.8.2
		 *
		 * @param null $args
		 *
		 * @return array
		 */
		function get_product_cost_archive( $args = null ) {
			$args       = wp_parse_args( $args, array(
				'product_id' => '',
				'order'      => 'DESC',
				'orderby'    => 'update_datetime'
			) );
			$order      = strtoupper( $args['order'] );
			$orderby    = $args['orderby'];
			$product_id = intval( $args['product_id'] );
			global $wpdb;
			$query = "
			SELECT meta_value, FROM_UNIXTIME(REGEXP_SUBSTR(meta_value,'(?<=update_date\";i:).+?(?=;)')) AS update_datetime
			FROM {$wpdb->postmeta}
			WHERE post_id = %d AND meta_key = %s
			";
			if ( ! empty( $orderby ) ) {
				$query .= "ORDER BY {$orderby} {$order}";
			}
			$results          = $wpdb->get_results(
				$wpdb->prepare( $query, $product_id, '_alg_wc_cog_cost_archive' ),
				ARRAY_A
			);
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
		 * @version 2.8.2
		 * @since   2.8.2
		 * @todo    [next] add option to delete all/selected history
		 */
		function display_product_cost_archive_table( $post ) {
			if ( empty( $post ) ) {
				return;
			}
			$product_cost_archive = $this->get_product_cost_archive( array(
				'product_id' => $post->ID
			) );
			if ( empty( $product_cost_archive ) ) {
				echo '<p>'.__( 'There isn\'t a cost archive for this product yet.', 'cost-of-goods-for-woocommerce' ).'</p>';
				echo '<p>' . sprintf( __( 'Please, check if the option %s is enabled and then update the cost.', 'cost-of-goods-for-woocommerce' ), '<strong><a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_cost_of_goods' ) . '">' . __( 'Products > Cost archive > Save cost archive', 'cost-of-goods-for-woocommerce' ) . '</a></strong>' ) . '</p>';
			} else {
				$table_columns = array(
					'update_date'     => __( 'Update date', 'cost-of-goods-for-woocommerce' ),
					'prev_cost_value' => __( 'Previous cost', 'cost-of-goods-for-woocommerce' ),
					'new_cost_value'  => __( 'New cost', 'cost-of-goods-for-woocommerce' ),
				);
				$table_rows    = array();
				foreach ( $product_cost_archive as $cost_info ) {
					$table_rows[] = array(
						'val_by_col' => array(
							wp_date( 'Y-m-d H:i:s', $cost_info['update_date'] ),
							alg_wc_cog_format_cost( $cost_info['prev_cost_value'] ),
							alg_wc_cog_format_cost( $cost_info['new_cost_value'] )
						)
					);
				}
				$table = alg_wc_cog_get_html_table_structure( array(
					'table_classes' => array( 'widefat', 'striped' ),
					'cols'          => $table_columns,
					'rows'          => $table_rows
				) );
				echo '<div style="margin-top:10px;clear:both"></div>';
				echo $table;
			}
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
		 * @version 2.8.2
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
				$archive = array(
					'update_date'     => current_datetime()->getTimestamp(),
					'prev_cost_value' => (float) alg_wc_cog()->core->products->get_product_cost( $post_id ),
					'new_cost_value'  => $meta_value
				);
				add_post_meta( $post_id, '_alg_wc_cog_cost_archive', $archive );
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

	}
}

return new Alg_WC_Cost_of_Goods_Products_Cost_Archive();