<?php
/**
 * Cost of Goods for WooCommerce - Deprecated Hooks.
 *
 * Maps old alg_wc_cog_* action/filter hooks to new wpfcogs_* hooks
 * and triggers _deprecated_hook() notices.
 *
 * @version 4.1.6
 * @since   4.1.6
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ============ Register deprecated action hooks ============
// Hooks into the NEW action name and forwards to the OLD name if any callbacks exist.

$wpfcogs_deprecated_actions = array(
	// Activation / deactivation.
	'alg_wc_cog_on_activation'                    => 'wpfcogs_on_activation',
	'alg_wc_cog_on_deactivation'                  => 'wpfcogs_on_deactivation',

	// Core.
	'alg_wc_cog_core_loaded'                      => 'wpfcogs_core_loaded',

	// Cost inputs.
	'alg_wc_cog_cost_input'                       => 'wpfcogs_cost_input',
	'alg_wc_cog_cost_input_variation'             => 'wpfcogs_cost_input_variation',

	// Orders.
	'alg_wc_cog_before_update_order_items_costs'  => 'wpfcogs_before_update_order_items_costs',
	'alg_wc_cog_update_order_values_action'       => 'wpfcogs_update_order_values_action',

	// Import tool.
	'alg_wc_cog_run_import_tool'                  => 'wpfcogs_run_import_tool',

	// Settings.
	'alg_wc_cog_save_settings'                    => 'wpfcogs_save_settings',
	'alg_wc_cog_on_update'                        => 'wpfcogs_on_update',

	// Tools.
	'alg_wc_cog_tools_after'                      => 'wpfcogs_tools_after',

	// Analytics.
	'alg_wc_cog_analytics_load_modules'           => 'wpfcogs_analytics_load_modules',

	// AJAX actions (wp_ajax_*).
	'wp_ajax_get_cost_archive_table'              => 'wp_ajax_wpfcogs_get_cost_archive_table',
	'wp_ajax_get_add_stock_history_table'         => 'wp_ajax_wpfcogs_get_add_stock_history_table',
	'wp_ajax_del_add_stock_history_date'          => 'wp_ajax_wpfcogs_del_add_stock_history_date',
	'wp_ajax_alg_wc_cog_get_quick_edit_field_value' => 'wp_ajax_wpfcogs_get_quick_edit_field_value',

	// Admin new order emails.
	'alg_cog_admin_new_order_email_meta'          => 'wpfcogs_admin_new_order_email_meta',
);

foreach ( $wpfcogs_deprecated_actions as $old_action => $new_action ) {
	add_action( $new_action, function () use ( $old_action, $new_action ) {
		$args = func_get_args();
		if ( has_action( $old_action ) ) {
			_deprecated_hook( esc_html( $old_action ), '4.1.6', esc_html( $new_action ) );
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
			call_user_func_array( 'do_action', array_merge( array( $old_action ), $args ) );
		}
	}, 5, PHP_INT_MAX );
}

// ============ Register deprecated filter hooks ============
// Hooks into the NEW filter name and forwards to the OLD name if any callbacks exist.

$wpfcogs_deprecated_filters = array(
	// Orders.
	'alg_wc_cog_order_admin_new_order_email_total_enabled' => 'wpfcogs_order_admin_new_order_email_total_enabled',
	'alg_wc_cog_order_admin_new_order_email_item_enabled'   => 'wpfcogs_order_admin_new_order_email_item_enabled',
	'alg_cog_admin_new_order_email_meta_enabled'            => 'wpfcogs_admin_new_order_email_meta_enabled',
	'alg_wc_cog_orders_item_handling_fees'                  => 'wpfcogs_orders_item_handling_fees',
	'alg_wc_cog_update_order_values'                        => 'wpfcogs_update_order_values',
	'alg_wc_cog_update_order_item_values'                   => 'wpfcogs_update_order_item_values',
	'alg_wc_cog_order_item_cost'                            => 'wpfcogs_order_item_cost',
	'alg_wc_cog_order_line_total'                           => 'wpfcogs_order_line_total',
	'alg_wc_cog_order_cost'                                 => 'wpfcogs_order_cost',
	'alg_wc_cog_order_profit'                               => 'wpfcogs_order_profit',
	'alg_wc_cog_order_total_taxes'                          => 'wpfcogs_order_total_taxes',
	'alg_wc_cog_order_total_fees'                           => 'wpfcogs_order_total_fees',
	'alg_wc_cog_order_shipping_total'                       => 'wpfcogs_order_shipping_total',
	'alg_wc_cog_order_net_payment'                          => 'wpfcogs_order_net_payment',
	'alg_wc_cog_order_total_refunded'                       => 'wpfcogs_order_total_refunded',
	'alg_wc_cog_order_total_for_pecentage_fees'             => 'wpfcogs_order_total_for_pecentage_fees',
	'alg_wc_cog_shipping_total_for_pecentage_fees'          => 'wpfcogs_shipping_total_for_pecentage_fees',
	'alg_wc_cog_order_extra_cost_from_meta'                 => 'wpfcogs_order_extra_cost_from_meta',
	'alg_wc_cog_order_shipping_class_cost_fixed'            => 'wpfcogs_order_shipping_class_cost_fixed',
	'alg_wc_cog_order_shipping_class_cost_percent'          => 'wpfcogs_order_shipping_class_cost_percent',
	'alg_wc_cog_order_metabox_cost_value_html'              => 'wpfcogs_order_metabox_cost_value_html',
	'alg_wc_cog_shipping_classes_enabled'                   => 'wpfcogs_shipping_classes_enabled',
	'alg_wc_cog_cost_meta_keys'                             => 'wpfcogs_cost_meta_keys',
	'alg_wc_cog_extra_profit_meta_keys'                     => 'wpfcogs_extra_profit_meta_keys',
	'alc_wc_cog_order_metabox_value_format_args'            => 'wpfcogs_order_metabox_value_format_args',
	'alc_wc_cog_order_metabox_value'                        => 'wpfcogs_order_metabox_value',
	'alc_wc_cog_order_admin_column_value_format_args'       => 'wpfcogs_order_admin_column_value_format_args',
	'alc_wc_cog_order_admin_column_value'                  => 'wpfcogs_order_admin_column_value',

	// Products.
	'alg_wc_cog_get_product_cost'                           => 'wpfcogs_get_product_cost',
	'alg_wc_cog_get_product_handling_fee'                   => 'wpfcogs_get_product_handling_fee',
	'alg_wc_cog_add_cost_input_validation'                  => 'wpfcogs_add_cost_input_validation',
	'alg_wc_cog_add_cost_input_variation_validation'        => 'wpfcogs_add_cost_input_variation_validation',
	'alg_wc_cog_add_handling_fee_input_validation'          => 'wpfcogs_add_handling_fee_input_validation',
	'alg_wc_cog_cost_input_description'                     => 'wpfcogs_cost_input_description',
	'alg_wc_cog_cost_input_label_placeholders'              => 'wpfcogs_cost_input_label_placeholders',
	'alg_wc_cog_products_add_stock_empty_cost_action'       => 'wpfcogs_products_add_stock_empty_cost_action',
	'alg_wc_cog_products_add_stock_cost_calculation_template' => 'wpfcogs_products_add_stock_cost_calculation_template',

	// Validation filters.
	'alg_wc_cog_create_product_meta_box_validation'         => 'wpfcogs_create_product_meta_box_validation',
	'alg_wc_cog_create_product_columns_validation'          => 'wpfcogs_create_product_columns_validation',
	'alg_wc_cog_create_orders_columns_validation'           => 'wpfcogs_create_orders_columns_validation',
	'alg_wc_cog_create_order_meta_box_validation'           => 'wpfcogs_create_order_meta_box_validation',
	'alg_wc_cog_create_wc_settings_tab_validation'          => 'wpfcogs_create_wc_settings_tab_validation',
	'alg_wc_cog_create_import_tool_validation'              => 'wpfcogs_create_import_tool_validation',
	'alg_wc_cog_create_edit_costs_tool_validation'          => 'wpfcogs_create_edit_costs_tool_validation',
	'alg_wc_cog_update_order_items_costs_validation'        => 'wpfcogs_update_order_items_costs_validation',

	// Import tool.
	'alg_wc_cog_copy_product_meta_args'                     => 'wpfcogs_copy_product_meta_args',
	'alg_wc_cog_can_copy_cost'                              => 'wpfcogs_can_copy_cost',

	// Bulk edit.
	'alg_wc_cog_bulk_edit_get_products_args'                => 'wpfcogs_bulk_edit_get_products_args',
	'alg_wc_cog_bulk_edit_get_child_products_args'          => 'wpfcogs_bulk_edit_get_child_products_args',
	'alg_wc_cog_json_search_found_tags'                     => 'wpfcogs_json_search_found_tags',
	'alg_wc_cog_filters_bulk_edit_tabs_nav'                 => 'wpfcogs_filters_bulk_edit_tabs_nav',

	// Background process.
	'alg_wc_cog_bkg_process_email_params'                   => 'wpfcogs_bkg_process_email_params',

	// Core / functions.
	'alg_wc_cog_is_user_allowed'                            => 'wpfcogs_is_user_allowed',
	'alg_wc_cog_is_user_allowed_roles'                      => 'wpfcogs_is_user_allowed_roles',
	'alg_wc_cog_sanitize_number_args'                       => 'wpfcogs_sanitize_number_args',

	// Settings.
	'alg_wc_cog_settings'                                   => 'wpfcogs_settings',

	// Analytics.
	'alg_wc_cog_analytics_localization_info'                 => 'wpfcogs_analytics_localization_info',
	'alg_wc_cog_analytics_orders_costs_total_validation'     => 'wpfcogs_analytics_orders_costs_total_validation',
	'alg_wc_cog_analytics_orders_profit_total_validation'    => 'wpfcogs_analytics_orders_profit_total_validation',
	'alg_wc_cog_analytics_orders_cost_profit_totals_enabled' => 'wpfcogs_analytics_orders_cost_profit_totals_enabled',
	'alg_wc_cog_analytics_orders_individual_costs_enabled'   => 'wpfcogs_analytics_orders_individual_costs_enabled',
	'alg_wc_cog_analytics_product_cost_totals'               => 'wpfcogs_analytics_product_cost_totals',
	'alg_wc_cog_analytics_product_cost_select'               => 'wpfcogs_analytics_product_cost_select',
	'alg_wc_cog_analytics_product_cost_select_subquery'      => 'wpfcogs_analytics_product_cost_select_subquery',
	'alg_wc_cog_analytics_product_cost_join'                 => 'wpfcogs_analytics_product_cost_join',
	'alg_wc_cog_analytics_product_profit_totals'             => 'wpfcogs_analytics_product_profit_totals',
	'alg_wc_cog_analytics_product_profit_select'             => 'wpfcogs_analytics_product_profit_select',
	'alg_wc_cog_analytics_product_profit_subquery'           => 'wpfcogs_analytics_product_profit_subquery',

	// Compatibility.
	'alg_wc_cog_food_options_fixed_costs_total'              => 'wpfcogs_food_options_fixed_costs_total',
);

foreach ( $wpfcogs_deprecated_filters as $old_filter => $new_filter ) {
	add_filter( $new_filter, function ( $value ) use ( $old_filter, $new_filter ) {
		$args = func_get_args();
		if ( has_filter( $old_filter ) ) {
			_deprecated_hook( esc_html( $old_filter ), '4.1.6', esc_html( $new_filter ) );
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
			return call_user_func_array( 'apply_filters', array_merge( array( $old_filter ), $args ) );
		}
		return $value;
	}, 5, PHP_INT_MAX );
}
