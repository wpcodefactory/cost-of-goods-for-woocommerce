<?php
/**
 * Cost of Goods for WooCommerce - Core Class.
 *
 * @version 3.6.9
 * @since   1.0.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFCOGS_Core' ) ) :

	class WPFCOGS_Core {

		/**
		 * Import tool
		 *
		 * @since 2.9.4
		 *
		 * @var WPFCOGS_Import_Tool
		 */
		public $import_tool;

		/**
		 * Bulk edit attr filtering.
		 *
		 * @since 4.0.1
		 *
		 * @var WPFCOGS_Bulk_Edit_Attr_Filtering
		 */
		public $bulk_edit_attr_filtering;

		/**
		 * Products.
		 *
		 * @since 2.9.4
		 *
		 * @var WPFCOGS_Products
		 */
		public $products;

		/**
		 * Add stock feature.
		 *
		 * @since 2.9.4
		 *
		 * @var WPFCOGS_Products_Add_Stock
		 */
		public $products_add_stock;

		/**
		 * Cost Archive.
		 *
		 * @since 2.9.4
		 *
		 * @var WPFCOGS_Products_Cost_Archive
		 */
		public $products_cost_archive;

		/**
		 * Costs input.
		 *
		 * @since 2.9.4
		 *
		 * @var WPFCOGS_Cost_Inputs
		 */
		public $cost_inputs;

		/**
		 * Orders.
		 *
		 * @since 2.9.4
		 *
		 * @var WPFCOGS_Orders
		 */
		public $orders;

		/**
		 * Bulk edit tool.
		 *
		 * @since 2.9.4
		 *
		 * @var WPFCOGS_Bulk_Edit_Tool;
		 */
		public $bulk_edit_tool;

		/**
		 * Options.
		 *
		 * @since 3.3.7
		 *
		 * @var WPFCOGS_Options
		 */
		public $options;

		/**
		 * Analytics.
		 *
		 * @since 3.4.6
		 *
		 * @var WPFCOGS_Analytics
		 */
		public $analytics;

		/**
		 * $extra_costs_labels.
		 *
		 * @since 3.6.0
		 *
		 * @var WPFCOGS_Extra_Costs_Labels
		 */
		public $extra_costs_labels;

		/**
		 * Constructor.
		 *
		 * @version 4.0.1
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
		 * @todo    [maybe] add option to change meta keys prefix (i.e. `_wpfcogs`)
		 */
		function __construct() {
			// Options class.
			require_once( 'class-wpfcogs-options.php' );
			$this->options = new WPFCOGS_Options();

			// Extra costs labels.
			require_once( 'class-wpfcogs-extra-costs-labels.php' );
			$this->extra_costs_labels = new WPFCOGS_Extra_Costs_Labels();
			$this->extra_costs_labels->init();

			// Background process.
			$this->init_bkg_process();

			// Analytics.
			$this->analytics = require_once( 'analytics/class-wpfcogs-analytics.php' );

			// Import tool.
			require_once( 'tools/class-wpfcogs-bulk-edit-attr-filtering.php' );
			$this->bulk_edit_attr_filtering = new WPFCOGS_Bulk_Edit_Attr_Filtering();
			$this->bulk_edit_attr_filtering->init();

			// Import tool.
			$this->import_tool = require_once( 'tools/class-wpfcogs-import-tool.php' );

			// Products.
			$this->products = require_once( 'class-wpfcogs-products.php' );

			// Products - Add Stock.
			$this->products_add_stock = require_once( 'class-wpfcogs-products-add-stock.php' );

			// Products - Cost archive.
			$this->products_cost_archive = require_once( 'class-wpfcogs-products-cost-archive.php' );

			// Cost inputs.
			$this->cost_inputs = require_once( 'class-wpfcogs-cost-inputs.php' );

			// Orders.
			$this->orders = require_once( 'class-wpfcogs-orders.php' );

			// Bulk costs tool.
			$this->init_bulk_costs_tool();

			// Admin new order emails.
			require_once( 'class-wpfcogs-admin-new-order-emails.php' );
			$admin_new_order_emails = new WPFCOGS_Admin_New_Order_Emails();
			$admin_new_order_emails->init();

			// Core loaded.
			do_action( 'wpfcogs_core_loaded', $this );
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
			require_once( 'background-process/class-wpfcogs-bkg-process.php' );
			add_filter( 'wpfcogs_bkg_process_email_params', array( $this, 'change_bkg_process_email_params' ) );
			new WPFCOGS_Bkg_Process();
		}

		/**
		 * init_bulk_costs_tool.
		 *
		 * @version 2.5.1
		 * @since   2.5.1
		 */
		function init_bulk_costs_tool(){
			// Bulk Edit tools
			$this->bulk_edit_tool = require_once( 'tools/class-wpfcogs-bulk-edit-tool.php' );
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

return new WPFCOGS_Core();
