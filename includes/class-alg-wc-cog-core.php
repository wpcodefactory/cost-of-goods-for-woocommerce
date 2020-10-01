<?php
/**
 * Cost of Goods for WooCommerce - Core Class
 *
 * @version 2.2.0
 * @since   1.0.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Core' ) ) :

class Alg_WC_Cost_of_Goods_Core {

	/**
	 * Constructor.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 * @todo    [next] add "delete all (products and/or orders) meta" tool
	 * @todo    [next] add option to enter costs *with taxes*
	 * @todo    [next] add options to exclude fees, shipping etc. in order profit
	 * @todo    [next] add reports (e.g. `calculate_all_products_profit()` etc.)
	 * @todo    [next] admin: add all fees column(s), e.g. shipping, gateway, meta
	 * @todo    [next] admin: rename file and class
	 * @todo    [next] admin: customizable column position
	 * @todo    [next] admin: customizable column title
	 * @todo    [later] add custom cost fields
	 * @todo    [later] add custom info fields
	 * @todo    [maybe] `force_on_meta`: `woocommerce_process_shop_order_meta`
	 * @todo    [maybe] add XML export
	 * @todo    [maybe] add product profit/cost meta box
	 * @todo    [maybe] add option to change meta keys prefix (i.e. `_alg_wc_cog`)
	 */
	function __construct() {
		// Analytics
		require_once( 'analytics/class-alg-wc-cog-analytics.php' );
		// Bulk Edit tool
		$this->bulk_edit_tool = require_once( 'tools/class-alg-wc-cog-bulk-edit-tool.php' );
		// Import tool
		$this->import_tool    = require_once( 'tools/class-alg-wc-cog-import-tool.php' );
		// Products
		$this->products       = require_once( 'class-alg-wc-cog-products.php' );
		// Orders
		$this->orders         = require_once( 'class-alg-wc-cog-orders.php' );
		// Core loaded
		do_action( 'alg_wc_cog_core_loaded', $this );
	}

	/**
	 * get_product_profit_html.
	 *
	 * For backward compatibility.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function get_product_profit_html( $product_id, $template = '%profit% (%profit_percent%)' ) {
		return $this->products->get_product_profit_html( $product_id, $template );
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Core();
