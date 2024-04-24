<?php
/**
 * Cost of Goods for WooCommerce - Costs input.
 *
 * @version 3.3.5
 * @since   3.0.3
 * @author  WPFactory
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Cost_of_Goods' ) ) :

	/**
	 * Main Alg_WC_Cost_of_Goods Class
	 *
	 * @class   Alg_WC_Cost_of_Goods
	 * @version 3.3.5
	 * @since   1.0.0
	 */
	final class Alg_WC_Cost_of_Goods {

		/**
		 * Pro.
		 *
		 * @since 2.9.4
		 *
		 * @var Alg_WC_Cost_of_Goods_Pro
		 */
		public $pro;

		/**
		 * Plugin version.
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		public $version = '3.3.6';

		/**
		 * @since 1.0.0
		 * @var   Alg_WC_Cost_of_Goods The single instance of the class
		 */
		protected static $_instance = null;

		/**
		 * $file_system_path.
		 *
		 * @since 3.0.3
		 */
		protected $file_system_path;

		/**
		 * $free_version_file_system_path.
		 *
		 * @since 3.0.3
		 */
		protected $free_version_file_system_path;

		/**
		 * Core.
		 *
		 * @since 2.9.4
		 *
		 * @var Alg_WC_Cost_of_Goods_Core
		 */
		public $core;

		/**
		 * Main Alg_WC_Cost_of_Goods Instance
		 *
		 * Ensures only one instance of Alg_WC_Cost_of_Goods is loaded or can be loaded.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @static
		 * @return  Alg_WC_Cost_of_Goods - Main instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Initializes.
		 *
		 * @version 3.0.3
		 * @since   2.8.1
		 */
		function init() {
			// Localization
			add_action( 'init', array( $this, 'localize' ) );

			// Adds compatibility with HPOS.
			add_action( 'before_woocommerce_init', function () {
				$this->declare_compatibility_with_hpos( $this->get_filesystem_path() );
				if ( ! empty( $this->get_free_version_filesystem_path() ) ) {
					$this->declare_compatibility_with_hpos( $this->get_free_version_filesystem_path() );
				}
			} );

			// Pro
			if ( 'cost-of-goods-for-woocommerce-pro.php' === basename( $this->get_filesystem_path() ) ) {
				$this->pro = require_once( 'pro/class-alg-wc-cog-pro.php' );
			}

			// Include required files
			$this->includes();

			// Admin
			if ( is_admin() ) {
				$this->admin();
			}

			// Generate documentation
			add_filter( 'wpfpdh_documentation_params_' . plugin_basename( $this->get_filesystem_path() ), array(
				$this,
				'handle_documentation_params'
			), 10 );
		}

		/**
		 * Declare compatibility with custom order tables for WooCommerce.
		 *
		 * @version 3.0.3
		 * @since   3.0.2
		 *
		 * @param $filesystem_path
		 *
		 * @return void
		 * @link    https://github.com/woocommerce/woocommerce/wiki/High-Performance-Order-Storage-Upgrade-Recipe-Book#declaring-extension-incompatibility
		 *
		 */
		function declare_compatibility_with_hpos( $filesystem_path ) {
			if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', $filesystem_path, true );
			}
		}

		/**
		 * Handle documentation params managed by the WP Factory
		 *
		 * @version 2.4.3
		 * @since   2.4.3
		 *
		 * @param $params
		 *
		 * @return mixed
		 */
		function handle_documentation_params( $params ) {
			$params['wc_tab_id']           = 'alg_wc_cost_of_goods';
			$params['pro_settings_filter'] = 'alg_wc_cog_settings';

			return $params;
		}

		/**
		 * localize.
		 *
		 * @version 3.0.3
		 * @since   2.3.3
		 *
		 */
		function localize() {
			// Set up localisation
			load_plugin_textdomain( 'cost-of-goods-for-woocommerce', false, dirname( plugin_basename( $this->get_filesystem_path() ) ) . '/langs/' );
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @version 3.0.3
		 * @since   1.0.0
		 */
		function includes() {
			// Functions
			require_once( 'alg-wc-cog-functions.php' );
			// Core
			$this->core = require_once( 'class-alg-wc-cog-core.php' );
		}

		/**
		 * admin.
		 *
		 * @version 3.0.3
		 * @since   1.1.0
		 */
		function admin() {
			// Action links
			add_filter( 'plugin_action_links_' . plugin_basename( $this->get_filesystem_path() ), array(
				$this,
				'action_links'
			) );
			// Settings
			add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
			// Version update
			if ( get_option( 'alg_wc_cog_version', '' ) !== $this->version ) {
				add_action( 'admin_init', array( $this, 'version_updated' ) );
			}
		}

		/**
		 * Show action links on the plugin screen.
		 *
		 * @version 3.0.3
		 * @since   1.0.0
		 *
		 * @param   mixed  $links
		 *
		 * @return  array
		 */
		function action_links( $links ) {
			$custom_links   = array();
			$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_cost_of_goods' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';
			if ( 'cost-of-goods-for-woocommerce.php' === basename( $this->get_filesystem_path() ) ) {
				$custom_links[] = '<a target="_blank" style="font-weight: bold; color: green;" href="https://wpfactory.com/item/cost-of-goods-for-woocommerce/">' .
				                  __( 'Go Pro', 'cost-of-goods-for-woocommerce' ) . '</a>';
			}
			$custom_links[] = '<a href="' . admin_url( 'tools.php?page=bulk-edit-costs' ) . '">' . __( 'Bulk edit costs', 'woocommerce' ) . '</a>';
			$custom_links[] = '<a href="' . admin_url( 'tools.php?page=bulk-edit-prices' ) . '">' . __( 'Bulk edit prices', 'woocommerce' ) . '</a>';

			return array_merge( $custom_links, $links );
		}

		/**
		 * Add Cost of Goods settings tab to WooCommerce settings.
		 *
		 * @version 3.0.3
		 * @since   1.0.0
		 */
		function add_woocommerce_settings_tab( $settings ) {
			if ( ! apply_filters( 'alg_wc_cog_create_wc_settings_tab_validation', true ) ) {
				return $settings;
			}
			$settings[] = require_once( 'settings/class-alg-wc-settings-cog.php' );

			return $settings;
		}

		/**
		 * version_updated.
		 *
		 * @version 1.1.0
		 * @since   1.1.0
		 */
		function version_updated() {
			update_option( 'alg_wc_cog_version', $this->version );
		}

		/**
		 * Get the plugin url.
		 *
		 * @version 3.0.3
		 * @since   1.0.0
		 * @return  string
		 */
		function plugin_url() {
			return untrailingslashit( plugin_dir_url( $this->get_filesystem_path() ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @version 3.0.3
		 * @since   1.0.0
		 * @return  string
		 */
		function plugin_path() {
			return untrailingslashit( plugin_dir_path( $this->get_filesystem_path() ) );
		}

		/**
		 * get_filesystem_path.
		 *
		 * @version 3.0.3
		 * @since   2.4.3
		 *
		 * @return string
		 */
		function get_filesystem_path() {
			return $this->file_system_path;
		}

		/**
		 * set_filesystem_path.
		 *
		 * @version 3.0.3
		 * @since   3.0.3
		 *
		 * @param   mixed  $file_system_path
		 */
		public function set_filesystem_path( $file_system_path ) {
			$this->file_system_path = $file_system_path;
		}

		/**
		 * get_free_version_filesystem_path.
		 *
		 * @version 3.0.3
		 * @since   3.0.3
		 *
		 * @return mixed
		 */
		public function get_free_version_filesystem_path() {
			return $this->free_version_file_system_path;
		}

		/**
		 * set_free_version_filesystem_path.
		 *
		 * @version 3.0.3
		 * @since   3.0.3
		 *
		 * @param   mixed  $free_version_file_system_path
		 */
		public function set_free_version_filesystem_path( $free_version_file_system_path ) {
			$this->free_version_file_system_path = $free_version_file_system_path;
		}

	}

endif;