<?php
/**
 * Cost of Goods for WooCommerce - Compatibility Settings.
 *
 * @version 3.3.3
 * @since   2.4.6
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Settings_Compatibility' ) ) :

	class Alg_WC_Cost_of_Goods_Settings_Compatibility extends Alg_WC_Cost_of_Goods_Settings_Section {

		/** $auto_exchange_cron_output.
		 *
		 * @since   2.4.3
		 *
		 * @var string
		 */
		private static $auto_exchange_cron_output = '';

		/**
		 * Constructor.
		 *
		 * @version 2.4.6
		 * @since   2.4.6
		 */
		function __construct() {
			$this->id   = 'compatibility';
			$this->desc = __( 'Compatibility', 'cost-of-goods-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * get_settings.
		 *
		 * @version 3.5.9
		 * @since   2.4.6
		 */
		function get_settings() {

			$wp_syncsheets_opts = array(
				$this->get_default_compatibility_title_option( array(
					'title' => __( 'WPSyncSheets', 'cost-of-goods-for-woocommerce' ),
					'link'  => 'https://wordpress.org/plugins/wpsyncsheets-woocommerce/',
					'type'  => 'plugin',
					'id'    => 'alg_wc_cog_compatibility_wpsyncsheets_options',
				) ),
				array(
					'title'             => __( 'Order export', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Export order cost to sheet', 'cost-of-goods-for-woocommerce' ),
					'desc_tip'          => sprintf( __( 'It\'s necessary to enable the new "%s" column on <a href="%s">Order Settings</a>.', 'cost-of-goods-for-woocommerce' ), __( 'Cost', 'cost-of-goods-for-woocommerce' ), admin_url( 'admin.php?page=wpsyncsheets-for-woocommerce&tab=order-settings' ) ),
					'type'              => 'checkbox',
					'id'                => 'alg_wc_cog_comp_wpsyncsheets_export_order_cost',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
					'checkboxgroup' => 'start',
					'default'           => 'no',
				),
				array(
					//'title'             => __( 'Order export', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Export order profit to sheet', 'cost-of-goods-for-woocommerce' ),
					'desc_tip' => sprintf( __( 'It\'s necessary to enable the new "%s" column on <a href="%s">Order Settings</a>.', 'cost-of-goods-for-woocommerce' ), __( 'Profit', 'cost-of-goods-for-woocommerce' ), admin_url( 'admin.php?page=wpsyncsheets-for-woocommerce&tab=order-settings' ) ),
					'type'              => 'checkbox',
					'id'                => 'alg_wc_cog_comp_wpsyncsheets_export_order_profit',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
					'checkboxgroup' => 'end',
					'default'           => 'no',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_cog_compatibility_wpsyncsheets_options',
				),
			);

			$curcy_multicurrency_opts = array(
				$this->get_default_compatibility_title_option( array(
					'title' => __( 'CURCY - Multi Currency for WooCommerce', 'cost-of-goods-for-woocommerce' ),
					'link'  => 'https://wordpress.org/plugins/woo-multi-currency/',
					'type'  => 'plugin',
					'id'    => 'alg_wc_cog_compatibility_curcy_options',
				) ),
				array(
					'title'             => __( 'Multicurrency order calculation', 'cost-of-goods-for-woocommerce' ),
					'desc'              => sprintf( __( 'Get currency rates from CURCY plugin instead of the %s option', 'cost-of-goods-for-woocommerce' ), '<a target="_blank" href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_cost_of_goods&section=currencies' ) . '">' . __( 'Multicurrency > Order calculation', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
					'type'              => 'checkbox',
					'id'                => 'alg_wc_cog_currencies_wmc',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
					'default'           => 'no',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_cog_compatibility_curcy_options',
				),
			);

			$exchangerateapi_opts = array(
				$this->get_default_compatibility_title_option( array(
					'title' => __( 'ExchangeRate-API', 'cost-of-goods-for-woocommerce' ),
					'link'  => 'https://www.exchangerate-api.com/docs/free',
					'type'  => 'plugin',
					'id'    => 'alg_wc_cog_compatibility_exchangerateapi_options',
				) ),
				array(
					'title'             => __( 'Multicurrency order calculation', 'cost-of-goods-for-woocommerce' ),
					'desc'              => sprintf( __( 'Get currency rates from %s instead of the %s option', 'cost-of-goods-for-woocommerce' ), '<a href="https://www.exchangerate-api.com/docs/free" target="_blank">' . __( 'ExchangeRate-API', 'cost-of-goods-for-woocommerce' ) . '</a>' , '<a target="_blank" href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_cost_of_goods&section=currencies' ) . '">' . __( 'Multicurrency > Order calculation', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
					'desc_tip'          => __( 'The update will run once a day.', 'cost-of-goods-for-woocommerce' ) . '<span data-wpfactory-desc-hide>'.' ' . $this->get_auto_exchange_rate_cron_info().'</span>',
					'id'                => 'alg_wc_cog_auto_currency_rates',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_cog_compatibility_exchangerateapi_options',
				),
			);

			$metorik_opts = array(
				$this->get_default_compatibility_title_option( array(
					'title' => __( 'Metorik', 'cost-of-goods-for-woocommerce' ),
					'link'  => 'https://metorik.com/',
					'type'  => 'plugin',
					'id'    => 'alg_wc_cog_compatibility_metorik_options',
				) ),
				array(
					'title'             => __( 'Sync cost', 'cost-of-goods-for-woocommerce' ),
					'desc'              => sprintf( __( 'Sync cost with %s meta', 'cost-of-goods-for-woocommerce' ), '<code>' . '_wc_cog_cost' . '</code>' ),
					'desc_tip'          => sprintf( __( 'Everytime %s meta gets updated its value is copied to %s.', 'cost-of-goods-for-woocommerce' ), '<code>' . '_alg_wc_cog_cost' . '</code>', '<code>' . '_wc_cog_cost' . '</code>' ),
					'id'                => 'alg_wc_cog_metorik_sync_cost_with_wc_cog_cost',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_cog_compatibility_metorik_options',
				),
			);

			$wp_all_import_opts = array(
				$this->get_default_compatibility_title_option( array(
					'title' => __( 'WP All Import', 'cost-of-goods-for-woocommerce' ),
					'link'  => 'https://wordpress.org/plugins/wp-all-import/',
					'type'  => 'plugin',
					'id'    => 'alg_wc_cog_compatibility_wp_all_import_options',
				) ),
				array(
					'title'             => __( 'Numbers with commas', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Convert numbers with commas to dots', 'cost-of-goods-for-woocommerce' ),
					'desc_tip'          => __( 'Example: <code>1723,07</code> changes to <code>1723.07</code>.', 'cost-of-goods-for-woocommerce' ),
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
					'id'                => 'alg_wc_cog_wp_all_import_convert_to_float',
					'default'           => 'no',
					'type'              => 'checkbox',
				),
				array(
					'title'             => __( 'Sanitize float number', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Remove all illegal characters from a float number, like currency values for example', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_wp_all_import_sanitize_float',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
					'default'           => 'no',
					'type'              => 'checkbox',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_cog_compatibility_wp_all_import_options',
				),
			);

			$wpc_product_bundle_opts = array(
				$this->get_default_compatibility_title_option( array(
					'title' => __( 'WPC Product Bundles for WooCommerce', 'cost-of-goods-for-woocommerce' ),
					'link'  => 'https://wordpress.org/plugins/woo-product-bundle/',
					'type'  => 'plugin',
					'id'    => 'alg_wc_cog_compatibility_wpcpb_options',
				) ),
				array(
					'title'             => __( 'WooCommerce reports', 'cost-of-goods-for-woocommerce' ),
					'desc'              => sprintf( __( 'Exclude Smart bundle product type from %s and %s report', 'cost-of-goods-for-woocommerce' ), '<a href="'.admin_url('admin.php?page=wc-reports&tab=stock&report=alg_cost_of_goods_stock').'">'.__( 'stock', 'cost-of-goods-for-woocommerce' ).'</a>', '<a href="'.admin_url('admin.php?page=wc-reports&tab=orders&report=alg_cost_of_goods').'">'.__( 'orders', 'cost-of-goods-for-woocommerce' ).'</a>' ),
					'id'                => 'alg_wc_cog_wpc_product_bundle_for_wc',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'             => __( 'Cost calculation', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Calculate Smart bundle cost from its items', 'cost-of-goods-for-woocommerce' ),
					'desc_tip'          => __( 'The cost will be calculated automatically when the bundle product price is updated.', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_wpcpb_calculate_cost_from_bundle_items',
					'default'           => 'no',
					'type'              => 'checkbox',
					'checkboxgroup'     => 'start',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'desc'              => __( 'Exclude Smart bundle cost from order item', 'cost-of-goods-for-woocommerce' ),
					'desc_tip'          => __( 'This option should be probably enabled if the above option is.', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_wpcpb_exclude_bundle_cost_from_order_item',
					'default'           => 'no',
					'type'              => 'checkbox',
					'checkboxgroup'     => 'end',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_cog_compatibility_wpcpb_options',
				),
			);

			$atum_opts = array(
				$this->get_default_compatibility_title_option( array(
					'title' => __( 'ATUM Inventory Management for WooCommerce', 'cost-of-goods-for-woocommerce' ),
					'link'  => 'https://wordpress.org/plugins/atum-stock-manager-for-woocommerce/',
					'type'  => 'plugin',
					'id'    => 'alg_wc_cog_compatibility_atum_options',
				) ),
				array(
					'title'             => __( 'Import costs tool', 'cost-of-goods-for-woocommerce' ),
					'desc'              => sprintf( __( 'Copy cost from %s plugin while using the %s tool', 'cost-of-goods-for-woocommerce' ), '<strong>' . __( 'ATUM', 'cost-of-goods-for-woocommerce' ) . '</strong>', '<a target="_blank" href="' . admin_url( 'tools.php?page=import-costs' ) . '">' . __( 'Import costs', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
					'desc_tip'          => sprintf( __( 'The %s option will be ignored.', 'cost-of-goods-for-woocommerce' ), '<strong>' . __( 'Key to import from', 'cost-of-goods-for-woocommerce' ) . '</strong>' ),
					'id'                => 'alg_wc_cog_comp_atum_get_cost_function_enabled',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'             => __( 'Cost sync', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Change cost of goods every time the purchase price is updated in ATUM', 'cost-of-goods-for-woocommerce' ),
					'desc_tip'          => sprintf( __( 'The %s meta will be changed every time the %s column is updated.', 'cost-of-goods-for-woocommerce' ), '<code>' . __( '_alg_wc_cog_cost', 'cost-of-goods-for-woocommerce' ) . '</code>', '<code>' . __( 'purchase_price', 'cost-of-goods-for-woocommerce' ) . '</code>' ),
					'id'                => 'alg_wc_cog_comp_atum_get_sync_purchase_price_with_cost',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'             => __( 'Add Stock sync', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Update Add Stock feature when the stock is changed from ATUM.', 'cost-of-goods-for-woocommerce' ),
					'desc_tip'          => sprintf(
						__( 'The %s option should probably be set as %s or %s.', 'cost-of-goods-for-woocommerce' ),
						'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_cost_of_goods' ) . '">' . __( 'Products > Add Stock > Empty cost field', 'cost-of-goods-for-woocommerce' ) . '</a>',
						'<code>' . __( 'Uses corrent cost', 'cost-of-goods-for-woocommerce' ) . '</code>',
						'<code>' . __( 'Uses last cost value from "Add stock history"', 'cost-of-goods-for-woocommerce' ) . '</code>'
					),
					'id'                => 'alg_wc_cog_comp_atum_sync_add_stock',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'             => __( 'Taxes', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Subtract taxes from ATUM cost while using the "Import" or "Cost sync" options', 'cost-of-goods-for-woocommerce' ),
					'desc_tip'          => __( 'The highest priority tax rate will be used, and only on taxable products.', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_comp_atum_subtract_atum_taxes',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_cog_compatibility_atum_options',
				),
			);

			$wc_food_opts = array(
				$this->get_default_compatibility_title_option( array(
					'title' => __( 'WooCommerce Food', 'cost-of-goods-for-woocommerce' ),
					'link'  => 'https://exthemes.net/woocommerce-food/',
					'type'  => 'plugin',
					'id'    => 'alg_wc_cog_compatibility_wc_food_options',
				) ),
				array(
					'title'             => __( 'Food costs', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Add fixed costs to food options', 'cost-of-goods-for-woocommerce' ),
					'desc_tip'          => __( 'A metabox is going to be created on the admin products page and on the global food options.', 'cost-of-goods-for-woocommerce' ) . '<br />' .
					                       __( 'For now, all the options costs will be "quantity based" calculated.', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_comp_wc_food_fixed_options_costs',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_cog_compatibility_wc_food_options',
				),
			);

			$wc_measurement_price_calculator_opts = array(
				$this->get_default_compatibility_title_option( array(
					'title' => __( 'WooCommerce Measurement Price Calculator', 'cost-of-goods-for-woocommerce' ),
					'link'  => 'https://woo.com/document/measurement-price-calculator/',
					'type'  => 'plugin',
					'id'    => 'alg_wc_cog_compatibility_wc_measurement_price_calc_options',
				) ),
				array(
					'title'             => __( 'Cost', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Adjust the cost of goods sold according to the product measurement', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_comp_wcmpc_adjust_cost_by_measure',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'             => __( 'Cost field label placeholder', 'cost-of-goods-for-woocommerce' ),
					'desc'              => sprintf(__( 'Add the placeholder %s to the %s option', 'cost-of-goods-for-woocommerce' ), '<code>%measurement_unit%</code>','<a href="'.admin_url('admin.php?page=wc-settings&tab=alg_wc_cost_of_goods').'">'.__('cost field label').'</a>'),
					'id'                => 'alg_wc_cog_comp_wcmpc_add_measurement_unit_placeholder_to_cost_label',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_cog_compatibility_wc_measurement_price_calc_options',
				),
			);

			$openpos_opts = array(
				$this->get_default_compatibility_title_option( array(
					'title' => __( 'Openpos - WooCommerce Point Of Sale(POS)', 'cost-of-goods-for-woocommerce' ),
					'link'  => 'https://codecanyon.net/item/openpos-a-complete-pos-plugins-for-woocomerce/22613341',
					'type'  => 'plugin',
					'id'    => 'alg_wc_cog_openpos_compatibility_options',
				) ),
				array(
					'title'    => __( 'Order types', 'cost-of-goods-for-woocommerce' ),
					'desc'     => __( 'Order types in reports.', 'cost-of-goods-for-woocommerce' ) . ' ' . '<strong>' . __( 'Note:', 'cost-of-goods-for-woocommerce' ) . '</strong>' . ' ' . __( 'Only works with the old WooCommerce reports, and it might not work on recent versions of WooCommerce.', 'cost-of-goods-for-woocommerce' ),
					'desc_tip' => __( 'If empty will show common and openpos orders combined.', 'cost-of-goods-for-woocommerce' ),
					'id'       => 'alg_wc_cog_openpos_anhvnit_report_order_type',
					'default'  => array(),
					'type'     => 'multiselect',
					'options'  => array(
						'common_orders'  => __( 'Common orders', 'cost-of-goods-for-woocommerce' ),
						'openpos_orders' => __( 'Openpos orders', 'cost-of-goods-for-woocommerce' )
					),
					'class'    => 'chosen_select',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_cog_openpos_compatibility_options',
				),
			);

			$product_addons_opts = array(
				$this->get_default_compatibility_title_option( array(
					'title' => __( 'Product Addons', 'cost-of-goods-for-woocommerce' ),
					'link'  => 'https://woocommerce.com/products/product-add-ons/',
					'type'  => 'plugin',
					'id'    => 'alg_wc_cog_product_addons_compatibility_options',
				) ),
				array(
					'title'             => __( 'Addons', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Add costs fields for the addons and create an order meta with addons costs.', 'cost-of-goods-for-woocommerce' ),
					'desc_tip'          => sprintf( __( 'It\'s necessary to add %s on %s option.', 'cost-of-goods-for-woocommerce' ), '<code>' . '_alg_wc_cog_pao_costs' . '</code>', '<strong>' . __( 'Orders > Extra Costs: From Meta', 'cost-of-goods-for-woocommerce' ) . '</strong>' ) . '<br />' .
					                       __( 'It\'s also necessary that addons do not change names once purchased.', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_product_addons',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_cog_product_addons_compatibility_options',
				),
			);

			return array_merge(
				$wp_syncsheets_opts,
				$curcy_multicurrency_opts,
				$exchangerateapi_opts,
				$metorik_opts,
				$wp_all_import_opts,
				$wpc_product_bundle_opts,
				$atum_opts,
				$wc_food_opts,
				$wc_measurement_price_calculator_opts,
				$openpos_opts,
				$product_addons_opts,
			);
		}

		/**
		 * get_default_compatibility_title_option.
		 *
		 * @version 3.2.4
		 * @since   3.2.4
		 *
		 * @param $args
		 *
		 * @return array
		 */
		function get_default_compatibility_title_option( $args = null ) {
			$args = wp_parse_args( $args, array(
				'link'  => '',
				'title' => '',
				'type'  => 'plugin', // plugin | theme
				'id'    => '',
			) );

			$product_type = 'plugin' === $args['type'] ? __( 'plugin', 'cost-of-goods-for-woocommerce' ) : __( 'theme', 'cost-of-goods-for-woocommerce' );

			return array(
				'title' => $args['title'],
				'type'  => 'title',
				'desc'  => sprintf(
					__( 'Compatibility with %s %s.', 'cost-of-goods-for-woocommerce' ),
					'<a href="' . esc_url( $args['link'] ) . '" target="_blank">' . esc_html( $args['title'] ) . '</a>',
					$product_type
				),
				'id'    => $args['id']
			);
		}

		/**
		 * get_auto_exchange_rate_cron_info.
		 *
		 * @version 2.4.3
		 * @since   2.4.3
		 *
		 * @return string
		 */
		function get_auto_exchange_rate_cron_info(){
			$auto_exchange_option_enabled = 'yes' === get_option( 'alg_wc_cog_auto_currency_rates', 'no' );
			if ( empty( self::$auto_exchange_cron_output ) ) {
				$output = '';
				if (
					( ! $event_timestamp = wp_next_scheduled( 'alg_wc_cog_currency_rate_update' ) )
					&& isset( $_POST['alg_wc_cog_auto_currency_rates'] )
				) {
					$output .= '<span style="font-weight: bold; color: green;">' . __( 'Please, reload the page to see the next scheduled event info.', 'cost-of-goods-for-woocommerce' ) . '</span>';
				} elseif ( $event_timestamp && $auto_exchange_option_enabled ) {
					$now                 = current_time( 'timestamp', true );
					$pretty_time_missing = human_time_diff( $now, $event_timestamp );
					$output              .= sprintf( __( 'Next event scheduled to %s', 'cost-of-goods-for-woocommerce' ), '<strong>' . get_date_from_gmt( date( 'Y-m-d H:i:s', $event_timestamp ), get_option( 'date_format' ) . ' - ' . get_option( 'time_format' ) ) . '</strong>' );
					$output              .= ' ' . '(' . $pretty_time_missing . ' left)';
				}
				self::$auto_exchange_cron_output = $output;
			} else {
				if ( ! $auto_exchange_option_enabled ) {
					self::$auto_exchange_cron_output = '';
				}
			}
			return self::$auto_exchange_cron_output;
		}

	}

endif;

return new Alg_WC_Cost_of_Goods_Settings_Compatibility();
