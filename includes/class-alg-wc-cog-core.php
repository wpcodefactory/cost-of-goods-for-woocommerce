<?php
/**
 * Cost of Goods for WooCommerce - Core Class.
 *
 * @version 3.3.7
 * @since   1.0.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Core' ) ) :

	class Alg_WC_Cost_of_Goods_Core {

		/**
		 * Import tool
		 *
		 * @since 2.9.4
		 *
		 * @var Alg_WC_Cost_of_Goods_Import_Tool
		 */
		public $import_tool;

		/**
		 * Products.
		 *
		 * @since 2.9.4
		 *
		 * @var Alg_WC_Cost_of_Goods_Products
		 */
		public $products;

		/**
		 * Add stock feature.
		 *
		 * @since 2.9.4
		 *
		 * @var Alg_WC_Cost_of_Goods_Products_Add_Stock
		 */
		public $products_add_stock;

		/**
		 * Cost Archive.
		 *
		 * @since 2.9.4
		 *
		 * @var Alg_WC_Cost_of_Goods_Products_Cost_Archive
		 */
		public $products_cost_archive;

		/**
		 * Costs input.
		 *
		 * @since 2.9.4
		 *
		 * @var Alg_WC_Cost_of_Goods_Cost_Inputs
		 */
		public $cost_inputs;

		/**
		 * Orders.
		 *
		 * @since 2.9.4
		 *
		 * @var Alg_WC_Cost_of_Goods_Orders
		 */
		public $orders;

		/**
		 * Bulk edit tool.
		 *
		 * @since 2.9.4
		 *
		 * @var Alg_WC_Cost_of_Goods_Bulk_Edit_Tool;
		 */
		public $bulk_edit_tool;

		/**
		 * Options.
		 *
		 * @since 3.3.7
		 *
		 * @var Alg_WC_Cost_of_Goods_Options
		 */
		public $options;

		/**
		 * Constructor.
		 *
		 * @version 3.3.7
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
			require_once( 'class-alg-wc-cog-options.php' );
			$this->options = new Alg_WC_Cost_of_Goods_Options();
			// Background process.
			$this->init_bkg_process();
			// Analytics.
			require_once( 'analytics/class-alg-wc-cog-analytics.php' );
			// Import tool.
			$this->import_tool = require_once( 'tools/class-alg-wc-cog-import-tool.php' );
			// Products.
			$this->products = require_once( 'class-alg-wc-cog-products.php' );
			// Products - Add Stock.
			$this->products_add_stock = require_once( 'class-alg-wc-cog-products-add-stock.php' );
			// Products - Cost archive.
			$this->products_cost_archive = require_once( 'class-alg-wc-cog-products-cost-archive.php' );
			// Cost inputs.
			$this->cost_inputs = require_once( 'class-alg-wc-cog-cost-inputs.php' );
			// Orders.
			$this->orders = require_once( 'class-alg-wc-cog-orders.php' );
			// Bulk costs tool.
			$this->init_bulk_costs_tool();
			// Core loaded.
			do_action( 'alg_wc_cog_core_loaded', $this );
		}

		/**
		 * get_default_shop_currency.
		 *
		 * @version 2.3.0
		 * @since   2.3.0
		 * @todo    [now] `wc_price()`
		 * @todo    [now] product profit and cost + multicurrency
		 */
		function get_default_shop_currency() {
			return get_option( 'woocommerce_currency' );
		}

		/**
		 * get_default_shop_currency_symbol.
		 *
		 * @version 2.3.0
		 * @since   2.3.0
		 */
		function get_default_shop_currency_symbol() {
			return get_woocommerce_currency_symbol( $this->get_default_shop_currency() );
		}

		/**
		 * init_bkg_process.
		 *
		 * @version 2.5.1
		 * @since   2.3.0
		 */
		function init_bkg_process() {
			require_once( 'background-process/class-alg-wc-cog-bkg-process.php' );
			add_filter( 'alg_wc_cog_bkg_process_email_params', array( $this, 'change_bkg_process_email_params' ) );
			new Alg_WC_Cost_of_Goods_Bkg_Process();
		}

		/**
		 * init_bulk_costs_tool.
		 *
		 * @version 2.5.1
		 * @since   2.5.1
		 */
		function init_bulk_costs_tool(){
			// Bulk Edit tools
			$this->bulk_edit_tool = require_once( 'tools/class-alg-wc-cog-bulk-edit-tool.php' );
		}

		/**
		 * change_bkg_process_email_params.
		 *
		 * @param $email_params
		 *
		 * @return mixed
		 * @version 2.3.0
		 * @since   2.3.0
		 *
		 */
		function change_bkg_process_email_params( $email_params ) {
			$email_params['send_email_on_task_complete'] = 'yes' === get_option( 'alg_wc_cog_bkg_process_send_email', 'yes' );
			$email_params['send_to']                     = get_option( 'alg_wc_cog_bkg_process_email_to', get_option( 'admin_email' ) );

			return $email_params;
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
