<?php
/**
 * Cost of Goods for WooCommerce - Import Tool Class
 *
 * @version 2.3.4
 * @since   1.1.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Import_Tool' ) ) :

class Alg_WC_Cost_of_Goods_Import_Tool {

	/**
	 * @var Alg_WC_Cost_of_Goods_Import_Tool_Bkg_Process
	 */
	public $import_tool_bkg_process;

	/**
	 * Constructor.
	 *
	 * @version 2.3.0
	 * @since   1.1.0
	 */
	function __construct() {
		add_action( 'admin_menu', array( $this, 'create_import_tool' ) );
		// Bkg Process
		add_action( 'plugins_loaded', array( $this, 'init_bkg_process' ) );
	}

	/**
	 * init_bkg_process.
	 *
	 * @version 2.3.0
	 * @since   2.3.0
	 */
	function init_bkg_process() {
		require_once( alg_wc_cog()->plugin_path() . '/includes/background-process/class-alg-wc-cog-import-tool-bkg-process.php' );
		$this->import_tool_bkg_process = new Alg_WC_Cost_of_Goods_Import_Tool_Bkg_Process();
	}

	/**
	 * create_import_tool.
	 *
	 * @version 2.3.4
	 * @since   1.0.0
	 */
	function create_import_tool() {
		if ( ! apply_filters( 'alg_wc_cog_create_import_tool_validation', true ) ) {
			return;
		}
		add_submenu_page(
			'tools.php',
			__( 'Import Costs', 'cost-of-goods-for-woocommerce' ),
			__( 'Import Costs', 'cost-of-goods-for-woocommerce' ),
			'manage_woocommerce',
			'import-costs',
			array( $this, 'import_tool' )
		);
	}

	/**
	 * copy_product_meta.
	 *
	 * @version 2.3.2
	 * @since   2.3.0
	 *
	 * @param $product_id
	 * @param string $from_key
	 * @param string $to_key
	 */
	function copy_product_meta( $product_id, $from_key = '_wc_cog_cost', $to_key = '_alg_wc_cog_cost' ) {
		if (
			(
				'yes' === get_option( 'alg_wc_cog_import_tool_check_key', 'yes' )
				&& ! metadata_exists( 'post', $product_id, $from_key )
			)
			||
			(
				'yes' === get_option( 'alg_wc_cog_import_tool_check_value', 'yes' )
				&& empty( get_post_meta( $product_id, $from_key, true ) )
			)
		) {
			return;
		}
		$source_cost = get_post_meta( $product_id, $from_key, true );
		update_post_meta( $product_id, $to_key, $source_cost );
	}

	/**
	 * import_tool.
	 *
	 * @version 2.3.4
	 * @since   1.0.0
	 * @todo    [later] use `wc_get_products()`
	 * @todo    [later] better description here and in settings
	 * @todo    [later] notice after import
	 * @todo    [later] add "import from file" option (CSV, XML etc.) (#12169)
	 * @todo    [maybe] import order items meta
	 */
	function import_tool() {
		$perform_import            = ( isset( $_POST['alg_wc_cog_import'] ) );
		$key                       = get_option( 'alg_wc_cog_tool_key', '_wc_cog_cost' );
		$bkg_process_min_amount    = get_option( 'alg_wc_cog_bkg_process_min_amount', 100 );
		$table_data                = array();
		$alg_wc_cog_get_table_html = '';
		$products                  = array();
		$display_table             = 'yes' === get_option( 'alg_wc_cog_import_tool_display_table', 'no' );
		$table_data[]              = array(
			__( 'Product ID', 'cost-of-goods-for-woocommerce' ),
			__( 'Product Title', 'cost-of-goods-for-woocommerce' ),
			sprintf( __( 'Source %s', 'cost-of-goods-for-woocommerce' ), '<code>' . $key . '</code>' ),
			sprintf( __( 'Destination %s', 'cost-of-goods-for-woocommerce' ), '<code>' . '_alg_wc_cog_cost' . '</code>' ),
		);
		$args                      = array(
			'post_type'              => array( 'product', 'product_variation' ),
			'post_status'            => 'any',
			'posts_per_page'         => - 1,
			'orderby'                => 'ID',
			'order'                  => 'ASC',
			'fields'                 => 'ids',
			'no_found_rows'          => false,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'cache_results'          => false
		);

		$loop                = new WP_Query( $args );
		$perform_bkg_process = $perform_import && $loop->post_count >= $bkg_process_min_amount;

		// Bkg Process
		if ( $perform_bkg_process ) {
			$this->import_tool_bkg_process->cancel_process();
			if ( $loop->have_posts() ) {
				foreach ( $loop->posts as $product_id ) {
					$this->import_tool_bkg_process->push_to_queue( array( 'id' => $product_id, 'from_key' => $key, 'to_key' => '_alg_wc_cog_cost' ) );
				}
			}
			$this->import_tool_bkg_process->save()->dispatch();
		}

		if ( $display_table ) {
			if ( $loop->have_posts() ) {
				foreach ( $loop->posts as $product_id ) {
					$source_cost = get_post_meta( $product_id, $key, true );
					if ( $perform_import ) {
						update_post_meta( $product_id, '_alg_wc_cog_cost', $source_cost );
					}
					$destination_cost = alg_wc_cog()->core->products->get_product_cost( $product_id );
					$table_data[]     = array( $product_id, get_the_title( $product_id ), $source_cost, $destination_cost );
				}
			}
			$alg_wc_cog_get_table_html = alg_wc_cog_get_table_html( $table_data, array( 'table_heading_type' => 'horizontal', 'table_class' => 'widefat striped' ) );
		} elseif ( ! $perform_bkg_process ) {
			if ( $loop->have_posts() ) {
				foreach ( $loop->posts as $product_id ) {
					$this->copy_product_meta( $product_id, $key, '_alg_wc_cog_cost' );
				}
			}
		}

		$button_form = '<form method="post" action="">' .
				'<input type="submit" name="alg_wc_cog_import" class="button-primary" value="' . __( 'Import', 'cost-of-goods-for-woocommerce' ) . '"' .
					' onclick="return confirm(\'' . __( 'Are you sure?', 'cost-of-goods-for-woocommerce' ) . '\')">' .
			'</form>';
		$html = '<div class="wrap">' .
			'<h1>' . __( 'Costs Import', 'cost-of-goods-for-woocommerce' ) . '</h1>' .
			'<p>' . __( 'Import products costs to "Cost of Goods for WooCommerce" plugin.', 'cost-of-goods-for-woocommerce' ) . ' ' .
				sprintf( __( 'Tool\'s options can be set in %s.', 'cost-of-goods-for-woocommerce' ),
					'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_cost_of_goods&section=tools' ) . '">' . __( 'plugin settings', 'cost-of-goods-for-woocommerce' ) . '</a>'
				) . '</p>' .
			'<p>' . $button_form . '</p>' .
			'<p>' . $alg_wc_cog_get_table_html . '</p>' .
		'</div>';
		echo $html;
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Import_Tool();
