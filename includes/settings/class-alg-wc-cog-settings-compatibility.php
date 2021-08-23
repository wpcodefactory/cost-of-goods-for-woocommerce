<?php
/**
 * Cost of Goods for WooCommerce - Compatibility Settings.
 *
 * @version 2.4.7
 * @since   2.4.6
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Settings_Compatibility' ) ) :

	class Alg_WC_Cost_of_Goods_Settings_Compatibility extends Alg_WC_Cost_of_Goods_Settings_Section {

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
		 * @version 2.4.7
		 * @since   2.4.6
		 */
		function get_settings() {
			$settings = array(
				array(
					'title'    => __( 'Compatibility options', 'cost-of-goods-for-woocommerce' ),
					'desc'  => __( 'Compatibility with third party plugins or solutions.', 'emails-verification-for-woocommerce' ),
					'type'     => 'title',
					'id'       => 'alg_wc_cog_compatibility_options',
				),
				array(
					'title'             => __( 'WP All Import', 'cost-of-goods-for-woocommerce' ),
					'desc'              => sprintf( __( 'Enable compatibility with <a target="_blank" href="%s">WP All Import</a>', 'cost-of-goods-for-woocommerce' ), 'https://wordpress.org/plugins/wp-all-import/' ),
					'desc_tip'          => __( 'Makes fine adjustments when importing a cost value to <code>_alg_wc_cog_cost</code> meta using the <strong>WP All Import</strong> plugin.', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_wp_all_import',
					'default'           => 'no',
					'type'              => 'checkbox',
					'checkboxgroup'     => 'start',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'desc'          => __( 'Convert to float', 'cost-of-goods-for-woocommerce' ),
					'desc_tip'      => __( 'Converts numbers using a comma for the decimal point and a dot as the thousand separator to float, like <code>1.723,07</code> to <code>1723.07</code>.', 'cost-of-goods-for-woocommerce' ),
					'id'            => 'alg_wc_cog_wp_all_import_convert_to_float',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => '',
				),
				array(
					'desc'          => __( 'Sanitize float number', 'cost-of-goods-for-woocommerce' ),
					'desc_tip'      => __( 'Removes all illegal characters from a float number, like currency values for example.', 'cost-of-goods-for-woocommerce' ),
					'id'            => 'alg_wc_cog_wp_all_import_sanitize_float',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'end',
				),
				array(
					'title'             => __( 'WPC Product Bundles for WooCommerce', 'cost-of-goods-for-woocommerce' ),
					'desc'              => sprintf( __( 'Enable compatibility with <a target="_blank" href="%s">%s</a>', 'cost-of-goods-for-woocommerce' ), 'https://wordpress.org/plugins/woo-product-bundle/', __( 'WPC Product Bundles for WooCommerce', 'cost-of-goods-for-woocommerce' ) ),
					'desc_tip'          => __( 'Excludes Smart bundle product type from stock and orders report.', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_wpc_product_bundle_for_wc',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'             => __( 'Openpos - WooCommerce Point Of Sale(POS)', 'cost-of-goods-for-woocommerce' ),
					'desc'              => sprintf(
						__( 'Enable compatibility with <a target="_blank" href="%s">%s</a> by <a href="%s" target="_blank">anhvnit</a> codenayon author', 'cost-of-goods-for-woocommerce' ),
						'https://codecanyon.net/item/openpos-a-complete-pos-plugins-for-woocomerce/22613341', __( 'Openpos - WooCommerce Point Of Sale', 'cost-of-goods-for-woocommerce' ), 'https://codecanyon.net/user/anhvnit'
					),
					'desc_tip'          => __( 'Manages POS orders on orders reports.', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_openpos_anhvnit',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'desc'     => __( 'Order types in reports.', 'cost-of-goods-for-woocommerce' ),
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
					'title'             => __( 'Product Addons', 'cost-of-goods-for-woocommerce' ),
					'desc'              => sprintf(
						__( 'Enable compatibility with <a target="_blank" href="%s">%s</a> by WooCommerce', 'cost-of-goods-for-woocommerce' ),
						'https://woocommerce.com/products/product-add-ons/', __( 'Product Add-Ons', 'cost-of-goods-for-woocommerce' ) ),
					'desc_tip'          => ( $original_desc_tip = __( 'Adds costs fields for the addons and creates an order meta with addons costs.', 'cost-of-goods-for-woocommerce' ) . '<br />' .
					                                              sprintf( __( 'It\'s necessary to add %s on %s option.', 'cost-of-goods-for-woocommerce' ), '<code>' . '_alg_wc_cog_pao_costs' . '</code>', '<strong>' . __( 'Orders > Extra Costs: From Meta', 'cost-of-goods-for-woocommerce' ) . '</strong>' ) . '<br />' .
					                                              __( 'It\'s also necessary that addons do not change names once purchased.', 'cost-of-goods-for-woocommerce' ) ),
					'id'                => 'alg_wc_cog_product_addons',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'             => __( 'Metorik', 'cost-of-goods-for-woocommerce' ),
					'desc'              => sprintf( __( 'Enable compatibility with <a target="_blank" href="%s">%s</a>', 'cost-of-goods-for-woocommerce' ),						'https://metorik.com/', __( 'Metorik', 'cost-of-goods-for-woocommerce' ) ),
					'id'                => 'alg_wc_cog_metorik',
					'default'           => 'no',
					'checkboxgroup'     => 'start',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'desc'              => sprintf( __( 'Sync cost with %s meta', 'cost-of-goods-for-woocommerce' ), '<code>' . '_wc_cog_cost' . '</code>' ),
					'desc_tip'          => sprintf( __( 'Everytime %s meta gets updated its value is copied to %s', 'cost-of-goods-for-woocommerce' ), '<code>' . '_alg_wc_cog_cost' . '</code>', '<code>' . '_wc_cog_cost' . '</code>' ),
					'id'                => 'alg_wc_cog_metorik_sync_cost_with_wc_cog_cost',
					'default'           => 'no',
					'checkboxgroup'     => 'end',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type'     => 'sectionend',
					'id'       => 'alg_wc_cog_compatibility_options',
				),
			);
			return $settings;
		}

	}

endif;

return new Alg_WC_Cost_of_Goods_Settings_Compatibility();
