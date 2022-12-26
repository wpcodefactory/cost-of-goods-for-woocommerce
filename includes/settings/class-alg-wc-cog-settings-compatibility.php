<?php
/**
 * Cost of Goods for WooCommerce - Compatibility Settings.
 *
 * @version 2.8.2
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
		 * @version 2.8.2
		 * @since   2.4.6
		 */
		function get_settings() {
			$compatibility_opts = array(
				array(
					'title'    => __( 'Compatibility options', 'cost-of-goods-for-woocommerce' ),
					'desc'  => __( 'Compatibility with third party plugins or solutions.', 'cost-of-goods-for-woocommerce' ),
					'type'     => 'title',
					'id'       => 'alg_wc_cog_compatibility_options',
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
					'type'     => 'sectionend',
					'id'       => 'alg_wc_cog_compatibility_options',
				),
			);
			$metorik_opts = array(
				array(
					'title' => __( 'Metorik', 'cost-of-goods-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => sprintf( __( 'Compatibility with %s plugin.', 'cost-of-goods-for-woocommerce' ), '<a href="https://metorik.com/" target="_blank">' . __( 'Metorik', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
					'id'    => 'alg_wc_cog_compatibility_metorik_options',
				),
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
				array(
					'title' => __( 'WP All Import', 'cost-of-goods-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => sprintf( __( 'Compatibility with %s plugin.', 'cost-of-goods-for-woocommerce' ), '<a href="https://wordpress.org/plugins/wp-all-import/" target="_blank">' . __( 'WP All Import', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
					'id'    => 'alg_wc_cog_compatibility_wp_all_import_options',
				),
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
				array(
					'title' => __( 'WPC Product Bundles for WooCommerce', 'cost-of-goods-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => sprintf( __( 'Compatibility with %s plugin.', 'cost-of-goods-for-woocommerce' ), '<a href="https://wordpress.org/plugins/woo-product-bundle/" target="_blank">' . __( 'WPC Product Bundles for WooCommerce', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
					'id'    => 'alg_wc_cog_compatibility_wpcpb_options',
				),
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
				array(
					'title' => __( 'ATUM Inventory Management for WooCommerce', 'cost-of-goods-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => sprintf( __( 'Compatibility with %s plugin.', 'cost-of-goods-for-woocommerce' ), '<a href="https://wordpress.org/plugins/atum-stock-manager-for-woocommerce/" target="_blank">' . __( 'ATUM Inventory Management for WooCommerce', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
					'id'    => 'alg_wc_cog_compatibility_atum_options',
				),
				array(
					'title'             => __( 'Product import costs tool', 'cost-of-goods-for-woocommerce' ),
					'desc'              => sprintf( __( 'Use function from %s plugin to copy the cost meta', 'cost-of-goods-for-woocommerce' ), '<strong>' . __( 'ATUM', 'cost-of-goods-for-woocommerce' ) . '</strong>' ),
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
					'type' => 'sectionend',
					'id'   => 'alg_wc_cog_compatibility_atum_options',
				),
			);
			$wc_food_opts = array(
				array(
					'title' => __( 'WooCommerce Food', 'cost-of-goods-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => sprintf( __( 'Compatibility with %s plugin.', 'cost-of-goods-for-woocommerce' ), '<a href="https://exthemes.net/woocommerce-food/" target="_blank">' . __( 'WooCommerce Food', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
					'id'    => 'alg_wc_cog_compatibility_wc_food_options',
				),
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
			return array_merge( $compatibility_opts, $metorik_opts, $wp_all_import_opts, $wpc_product_bundle_opts, $atum_opts, $wc_food_opts );
		}

	}

endif;

return new Alg_WC_Cost_of_Goods_Settings_Compatibility();
