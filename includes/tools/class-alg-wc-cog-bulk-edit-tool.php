<?php
/**
 * Cost of Goods for WooCommerce - Bulk Edit Tool Class
 *
 * @version 2.3.4
 * @since   1.2.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Bulk_Edit_Tool' ) ) :

class Alg_WC_Cost_of_Goods_Bulk_Edit_Tool {

	/**
	 * Constructor.
	 *
	 * @version 2.3.1
	 * @since   1.2.0
	 */
	function __construct() {
		add_action( 'admin_init',             array( $this, 'save_costs' ) );
		add_filter( 'woocommerce_screen_ids', array( $this, 'add_tool_to_wc_screen_ids' ) );
		add_action( 'admin_enqueue_scripts',  array( $this, 'enqueue_scripts_and_styles' ) );
		add_action( 'admin_menu',             array( $this, 'create_wp_list_tool' ) );
		add_action( 'set-screen-option',      array( $this, 'set_screen_option' ), 10, 3 );
	}

	/**
	 * set_screen_option.
	 *
	 * @version 2.3.1
	 * @since   2.3.1
	 *
	 * @param $status
	 * @param $option
	 * @param $value
	 *
	 * @return mixed
	 */
	function set_screen_option( $status, $option, $value ) {
		if ( 'alg_wc_cog_bulk_edit_per_page' === $option ) {
			return $value;
		}
		return $status;
	}

	/**
	 * screen_option.
	 *
	 * @version 2.3.1
	 * @since   2.3.1
	 */
	function screen_option() {
		$option = 'per_page';
		$args   = [
			'label'   => __( 'Items per page', 'cost-of-goods-for-woocommerce' ),
			'default' => 20,
			'option'  => 'alg_wc_cog_bulk_edit_per_page'
		];
		add_screen_option( $option, $args );
		require_once( 'class-alg-wc-cog-wplist-bulk-edit-tool.php' );
		$this->wp_list_bulk_edit_tool = new Alg_WC_Cost_of_Goods_WP_List_Bulk_Edit_Tool();
	}

	/**
	 * create_wp_list_tool.
	 *
	 * @version 2.3.4
	 * @since   2.3.1
	 */
	function create_wp_list_tool() {
		if ( ! apply_filters( 'alg_wc_cog_create_edit_costs_tool_validation', true ) ) {
			return;
		}
		$hook = add_submenu_page(
			'tools.php',
			__( 'Bulk Edit Costs', 'cost-of-goods-for-woocommerce' ),
			__( 'Bulk Edit Costs', 'cost-of-goods-for-woocommerce' ),
			'manage_woocommerce',
			'bulk-edit-costs',
			array( $this, 'display_wp_list_tool' )
		);
		add_action( "load-{$hook}", array( $this, 'screen_option' ) );
	}

	/**
	 * display_wp_list_tool.
	 *
	 * @version 2.3.1
	 * @since   2.3.1
	 */
	function display_wp_list_tool() {
		$this->wp_list_bulk_edit_tool->prepare_items();
		$save_button        = '<input style="position:relative;top:-2px;margin:0 0 0 10px" type="submit" name="alg_wc_cog_bulk_edit_tool_save_costs" class="page-title-action" value="' .
		                      __( 'Save', 'cost-of-goods-for-woocommerce' ) . '">';
		$save_button_style2 = '<input type="submit" name="alg_wc_cog_bulk_edit_tool_save_costs" class="button-primary" value="' .
		                      __( 'Save', 'cost-of-goods-for-woocommerce' ) . '">';
		?>
		<form method="post" action="">
			<div class="wrap">
				<h1 class="wp-heading-inline"><?php _e( 'Bulk Edit Costs', 'cost-of-goods-for-woocommerce' ) ?><?php echo $save_button; ?></h1>
				<?php
				echo '<p>' . __( 'Bulk edit products costs/prices/stock.', 'cost-of-goods-for-woocommerce' ) . ' ' .
				     sprintf( __( 'Tool\'s options can be set in "%s" %s.', 'cost-of-goods-for-woocommerce' ),
					     __( 'Cost of Goods for WooCommerce', 'cost-of-goods-for-woocommerce' ),
					     '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_cost_of_goods&section=tools' ) . '">' .
					     __( 'plugin settings', 'cost-of-goods-for-woocommerce' ) . '</a>');
				?>
				<?php $this->wp_list_bulk_edit_tool->search_box(__('Search','cost-of-goods-for-woocommerce'), 'alg_wc_cog_search'); ?>
				<?php $this->wp_list_bulk_edit_tool->display(); ?>
				<?php echo $save_button_style2; ?>
			</div>
		</form>
		<?php
	}

	/**
	 * enqueue_scripts_and_styles.
	 *
	 * @version 2.1.0
	 * @since   1.3.3
	 */
	function enqueue_scripts_and_styles( $hook ) {
		if ( 'tools_page_bulk-edit-costs' != $hook ) {
			return;
		}
		$min_suffix = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ? '' : '.min' );
		wp_enqueue_style( 'alg-wc-cog-bulk-edit-tool-style',
			alg_wc_cog()->plugin_url() . '/includes/css/alg-wc-cog-bulk-edit-tool' . $min_suffix . '.css',
			array(),
			alg_wc_cog()->version
		);
		wp_enqueue_script( 'alg-wc-cog-bulk-edit-tool',
			alg_wc_cog()->plugin_url() . '/includes/js/alg-wc-cog-bulk-edit-tool' . $min_suffix . '.js',
			array( 'jquery' ),
			alg_wc_cog()->version,
			true
		);
	}

	/**
	 * add_tool_to_wc_screen_ids.
	 *
	 * for `wc_input_price` class.
	 *
	 * @version 2.3.1
	 * @since   1.2.0
	 */
	function add_tool_to_wc_screen_ids( $screen_ids ) {
		$screen_ids[] = 'tools_page_bulk-edit-costs';
		return $screen_ids;
	}

	/**
	 * save_costs.
	 *
	 * @version 2.2.0
	 * @since   1.2.0
	 * @see     https://wordpress.org/support/topic/you-should-add-posibility-to-edit-regular-price-and-sale-price/
	 * @todo    [next] prices: `$do_update_func`
	 * @todo    [maybe] nonce etc.
	 * @todo    [maybe] output some error on ` ! ( $product = wc_get_product( $product_id ) )`?
	 */
	function save_costs() {
		$do_edit_prices = ( 'yes' === get_option( 'alg_wc_cog_bulk_edit_tool_edit_prices', 'no' ) );
		if ( $do_edit_prices ) {
			$error_sale_price_ids = array();
		}
		if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_save_costs'] ) ) {
			// Costs
			if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_costs'] )&& is_array( $_POST['alg_wc_cog_bulk_edit_tool_costs'] ) ) {
				foreach ( $_POST['alg_wc_cog_bulk_edit_tool_costs'] as $product_id => $cost_value ) {
					update_post_meta( sanitize_key( $product_id ), '_alg_wc_cog_cost', sanitize_text_field( $cost_value ) );
				}
			}
			// Prices
			if ( $do_edit_prices ) {
				if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_regular_price'] ) && is_array( $_POST['alg_wc_cog_bulk_edit_tool_regular_price'] ) ) {
					$regular_prices = wc_clean( $_POST['alg_wc_cog_bulk_edit_tool_regular_price'] );
					foreach ( $regular_prices as $product_id => $regular_price_value ) {
						if ( $product = wc_get_product( $product_id ) ) {
							$product->set_regular_price( $regular_price_value );
							$product->save();
						}
					}
				}
				if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_sale_price'] )&& is_array( $_POST['alg_wc_cog_bulk_edit_tool_sale_price'] ) ) {
					$sale_prices = wc_clean( $_POST['alg_wc_cog_bulk_edit_tool_sale_price'] );
					foreach ( $sale_prices as $product_id => $sale_price_value ) {
						if ( $product = wc_get_product( $product_id ) ) {
							if ( $sale_price_value <= $product->get_regular_price() ) {
								$product->set_sale_price( $sale_price_value );
								$product->save();
							} else {
								array_push( $error_sale_price_ids, $product_id );
							}
						}
					}
				}
			}
			// Stock
			if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_stock'] )&& is_array( $_POST['alg_wc_cog_bulk_edit_tool_stock'] ) ) {
				$do_update_func = ( 'func' === get_option( 'alg_wc_cog_bulk_edit_tool_manage_stock_method', 'meta' ) );
				foreach ( $_POST['alg_wc_cog_bulk_edit_tool_stock'] as $product_id => $stock_value ) {
					if ( $do_update_func && ( $product = wc_get_product( $product_id ) ) ) {
						$product->set_stock_quantity( sanitize_text_field( $stock_value ) );
						$product->save();
					} else {
						update_post_meta( sanitize_key( $product_id ), '_stock', sanitize_text_field( $stock_value ) );
					}
				}
			}
			// Notices
			add_action( 'admin_notices', array( $this, 'admin_notice_costs_saved' ) );
			if ( $do_edit_prices && count( $error_sale_price_ids ) ) {
				$this->error_sale_price_ids = $error_sale_price_ids;
				add_action( 'admin_notices', array( $this, 'admin_notice_sale_price_higher' ) );
			}
		}
	}

	/**
	 * admin_notice_costs_saved.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	function admin_notice_costs_saved() {
		echo '<div class="notice notice-success is-dismissible"><p><strong>' . __( 'Costs have been saved.', 'cost-of-goods-for-woocommerce' ) . '</strong></p></div>';
	}

	/**
	 * get_the_title.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function get_the_title( $post_id ) {
		return get_the_title( $post_id ) . ' (#' . $post_id . ')';
	}

	/**
	 * admin_notice_sale_price_higher.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function admin_notice_sale_price_higher() {
		echo '<div class="notice notice-error is-dismissible"><p><strong>' . sprintf( __( 'Sale price is higher than regular price: %s.', 'cost-of-goods-for-woocommerce' ),
			implode( ', ', array_map( array( $this, 'get_the_title' ), $this->error_sale_price_ids ) ) ) . '</strong></p></div>';
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Bulk_Edit_Tool();
