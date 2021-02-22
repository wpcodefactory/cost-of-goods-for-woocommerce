<?php
/**
 * Cost of Goods for WooCommerce - Advanced Section Settings
 *
 * @version 2.3.4
 * @since   1.7.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Settings_Advanced' ) ) :

class Alg_WC_Cost_of_Goods_Settings_Advanced extends Alg_WC_Cost_of_Goods_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	function __construct() {
		$this->id   = 'advanced';
		$this->desc = __( 'Advanced', 'cost-of-goods-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_allowed_user_roles_option.
	 *
	 * @version 2.3.4
	 * @since   2.3.4
	 *
	 * @return array
	 */
	function get_allowed_user_roles_option() {
		if ( ! function_exists( 'get_editable_roles' ) ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
		}
		$roles = wp_list_pluck( get_editable_roles(), 'name' );
		return $roles;
	}

	/**
	 * get_settings.
	 *
	 * @version 2.3.4
	 * @since   1.7.0
	 * @todo    [later] "Force costs update on ...": better title and desc (3x)
	 */
	function get_settings() {

		$advanced_settings = array(
			array(
				'title'    => __( 'Restriction', 'cost-of-goods-for-woocommerce' ),
				'desc' => '',
				'type'     => 'title',
				'id'       => 'alg_wc_cog_advanced_restriction_options',
			),
			array(
				'title'    => __( 'Restrict by user role', 'cost-of-goods-for-woocommerce' ),
				'desc'     => empty( $message = apply_filters( 'alg_wc_cog_settings', sprintf( 'You will need %s plugin to enable this option.', '<a target="_blank" href="https://wpfactory.com/item/cost-of-goods-for-woocommerce/">' . 'Cost of Goods for WooCommerce Pro' . '</a>' ) ) ) ? __( 'Allowed user roles.', 'cost-of-goods-for-woocommerce' ) : $message,
				'desc_tip' => __( 'Only the selected user roles will be able to see and edit plugin data.', 'cost-of-goods-for-woocommerce' )
				              .' '. __( 'Leave it empty to show the plugin data for all user roles.', 'cost-of-goods-for-woocommerce' )
				              .'<br />'. __( 'The administrator can\'t be blocked.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_allowed_user_roles',
				'default'  => array(),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => $this->get_allowed_user_roles_option(),
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_advanced_restriction_options',
			),
			array(
				'title'    => __( 'Force costs update', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_advanced_force_costs_update_options',
			),
			array(
				'title'    => __( 'Force costs update on order update', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Force empty order items cost update on each order update.', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Enable', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_force_on_update',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Force costs update on order status change', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Force empty order items cost update on order status change.', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Enable', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_force_on_status',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Force costs update on new order item', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Force empty order items cost update on new order item addition.', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Enable', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_orders_force_on_new_item',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_advanced_force_costs_update_options',
			),
			array(
				'title'    => __( 'Columns sorting', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_advanced_columns_sorting_options',
			),
			array(
				'title'    => __( 'Sortable columns', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Enable', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Makes columns added to admin %s and %s lists <strong>sortable</strong>.', 'cost-of-goods-for-woocommerce' ),
					'<a href="' . admin_url( 'edit.php?post_type=product' )    . '">' . __( 'products', 'cost-of-goods-for-woocommerce' ) . '</a>',
					'<a href="' . admin_url( 'edit.php?post_type=shop_order' ) . '">' . __( 'orders', 'cost-of-goods-for-woocommerce' )   . '</a>' ),
				'id'       => 'alg_wc_cog_columns_sorting',
				'default'  => 'yes',
				'type'     => 'checkbox',
				'checkboxgroup' => 'start',
			),
			array(
				'desc'     => __( 'Exclude empty lines on sorting', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_columns_sorting_exclude_empty_lines',
				'default'  => 'yes',
				'type'     => 'checkbox',
				'checkboxgroup' => 'end',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_advanced_columns_sorting_options',
			),
			array(
				'title'    => __( 'Background processing', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_advanced_bkg_process_options',
			),
			array(
				'title'    => __( 'Minimum amount', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'The minimum amount of results from a query in order to trigger a background processing.', 'cost-of-goods-for-woocommerce' ) . '<br />' . __( 'Only affects "Import Costs" tool and "Order cost and profit Recalculation" for the time being.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_bkg_process_min_amount',
				'default'  => 100,
				'type'     => 'number',
			),
			array(
				'title'    => __( 'Send email', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Enable', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Sends an email when a background processing is complete.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_bkg_process_send_email',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'       => __( 'Email to', 'cost-of-goods-for-woocommerce' ),
				'desc_tip'    => __( 'The email address that is going to receive the email when a background processing task is complete.', 'cost-of-goods-for-woocommerce' ). '<br />' . __( 'Requires the "Send email" option enabled in order to work.', 'cost-of-goods-for-woocommerce' ),
				'id'          => 'alg_wc_cog_bkg_process_email_to',
				'placeholder' => get_option( 'admin_email' ),
				'default'     => get_option( 'admin_email' ),
				'type'        => 'text',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_advanced_bkg_process_options',
			),
			array(
				'title'    => __( 'Compatibility', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_advanced_compatibility',
			),
			array(
				'title'             => __( 'WP All Import', 'cost-of-goods-for-woocommerce' ),
				'desc'              => sprintf( __( 'Enable compatibility with <a target="_blank" href="%s">WP All Import</a>', 'cost-of-goods-for-woocommerce' ), 'https://wordpress.org/plugins/wp-all-import/' ),
				'desc_tip'          => ( $original_desc_tip = __( 'Makes fine adjustments when importing a cost value to <code>_alg_wc_cog_cost</code> meta using the <strong>WP All Import</strong> plugin.', 'cost-of-goods-for-woocommerce' ) )
				                       . empty( $message = apply_filters( 'alg_wc_cog_settings', sprintf( 'You will need %s plugin to enable this option.', '<a target="_blank" href="https://wpfactory.com/item/cost-of-goods-for-woocommerce/">' . 'Cost of Goods for WooCommerce Pro' . '</a>' ) ) ) ? $original_desc_tip . '<br />' . $message : $original_desc_tip,
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
				'desc'              => sprintf( __( 'Enable compatibility with <a target="_blank" href="%s">WPC Product Bundles for WooCommerce</a>', 'cost-of-goods-for-woocommerce' ), 'https://wordpress.org/plugins/woo-product-bundle/' ),
				'desc_tip'          => ( $original_desc_tip = __( 'Excludes Smart bundle product type from stock and orders report.', 'cost-of-goods-for-woocommerce' ) )
				                       . empty( $message = apply_filters( 'alg_wc_cog_settings', sprintf( 'You will need %s plugin to enable this option.', '<a target="_blank" href="https://wpfactory.com/item/cost-of-goods-for-woocommerce/">' . 'Cost of Goods for WooCommerce Pro' . '</a>' ) ) ) ? $original_desc_tip . '<br />' . $message : $original_desc_tip,
				'id'                => 'alg_wc_cog_wpc_product_bundle_for_wc',
				'default'           => 'no',
				'type'              => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_advanced_compatibility',
			),
		);

		return $advanced_settings;
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Settings_Advanced();
