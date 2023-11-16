<?php
/**
 * Cost of Goods for WooCommerce - Advanced Section Settings.
 *
 * @version 3.1.3
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
	 * @version 3.1.3
	 * @since   1.7.0
	 * @todo    [later] "Force costs update on ...": better title and desc (3x)
	 */
	function get_settings() {

		$general_opts = array(
			array(
				'title' => __( 'Advanced options', 'cost-of-goods-for-woocommerce' ),
				'desc'  => '',
				'type'  => 'title',
				'id'    => 'alg_wc_cog_advanced_options',
			),
			array(
				'title'             => __( 'Restrict by user role', 'cost-of-goods-for-woocommerce' ),
				'desc'              => __( 'Allowed user roles.', 'cost-of-goods-for-woocommerce' ),
				'desc_tip'          => __( 'Only the selected user roles will be able to see and edit plugin data.', 'cost-of-goods-for-woocommerce' )
				                       . ' ' . __( 'Leave it empty to show the plugin data for all user roles.', 'cost-of-goods-for-woocommerce' )
				                       . '<br />' . __( 'The administrator can\'t be blocked.', 'cost-of-goods-for-woocommerce' ),
				'id'                => 'alg_wc_cog_allowed_user_roles',
				'default'           => array(),
				'type'              => 'multiselect',
				'class'             => 'chosen_select',
				'options'           => $this->get_allowed_user_roles_option(),
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'         => __( 'Force costs update', 'cost-of-goods-for-woocommerce' ),
				'desc'          => __( 'Auto fill empty order items costs on order update', 'cost-of-goods-for-woocommerce' ),
				'id'            => 'alg_wc_cog_orders_force_on_update',
				'default'       => 'no',
				'checkboxgroup' => 'start',
				'type'          => 'checkbox',
			),
			array(
				'desc'          => __( 'Auto fill empty order items costs on order status change', 'cost-of-goods-for-woocommerce' ),
				'id'            => 'alg_wc_cog_orders_force_on_status',
				'default'       => 'no',
				'checkboxgroup' => '',
				'type'          => 'checkbox',
			),
			array(
				'desc'          => __( 'Auto fill empty order items costs on new order item addition', 'cost-of-goods-for-woocommerce' ),
				'id'            => 'alg_wc_cog_orders_force_on_new_item',
				'default'       => 'no',
				'checkboxgroup' => '',
				'type'          => 'checkbox',
			),
			array(
				'desc'          => __( 'Auto fill empty order items costs on order meta update', 'cost-of-goods-for-woocommerce' ),
				'desc_tip'      => __( 'Only triggers with meta not starting with <code>_alg_wc_cog</code>.', 'cost-of-goods-for-woocommerce' ),
				'id'            => 'alg_wc_cog_orders_force_on_order_meta_update',
				'default'       => 'no',
				'checkboxgroup' => 'end',
				'type'          => 'checkbox',
			),
			array(
				'title'   => __( 'Costs update hooks', 'cost-of-goods-for-woocommerce' ),
				'desc'    => __( 'Hooks from new orders that will trigger cost update.', 'cost-of-goods-for-woocommerce' ),
				'id'      => 'alg_wc_cog_new_order_hooks_for_cost_update',
				'type'    => 'multiselect',
				'class'   => 'chosen_select',
				'default' => array_keys( alg_wc_cog()->core->orders->get_new_order_hooks_for_cost_updating() ),
				'options' => alg_wc_cog()->core->orders->get_new_order_hooks_for_cost_updating()
			),
			array(
				'title'         => __( 'Meta data', 'cost-of-goods-for-woocommerce' ),
				'desc'          => __( 'Avoid empty order meta data from being saved to database', 'cost-of-goods-for-woocommerce' ),
				'id'            => 'alg_wc_cog_avoid_empty_order_metadata_saving',
				'default'       => 'yes',
				'type'          => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_advanced_options',
			),
		);

		$cols_sorting_opts = array(
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
		);

		$bkg_process_opts = array(
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
				'desc'     => __( 'Send an email when a background processing is complete', 'cost-of-goods-for-woocommerce' ),
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
		);

		return array_merge( $general_opts, $cols_sorting_opts, $bkg_process_opts );
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Settings_Advanced();
