<?php
/**
 * Cost of Goods for WooCommerce - Deprecated Functions.
 *
 * Maps old alg_wc_cog_* functions to new wpfcogs_* functions
 * and triggers _deprecated_function() notices.
 *
 * @version 4.1.6
 * @since   4.1.6
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'alg_wc_cog_log' ) ) {
	/**
	 * Deprecated. Use wpfcogs_log() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @param string $message Message to log.
	 */
	function alg_wc_cog_log( $message ) {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_log' );
		return wpfcogs_log( $message );
	}
}

if ( ! function_exists( 'alg_wc_cog_pre_get_posts_order_by_column' ) ) {
	/**
	 * Deprecated. Use wpfcogs_pre_get_posts_order_by_column() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @param WP_Query $query                  The WP_Query instance.
	 * @param string   $post_type              Post type slug.
	 * @param bool     $do_exclude_empty_lines Whether to exclude empty lines.
	 */
	function alg_wc_cog_pre_get_posts_order_by_column( $query, $post_type, $do_exclude_empty_lines ) {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_pre_get_posts_order_by_column' );
		return wpfcogs_pre_get_posts_order_by_column( $query, $post_type, $do_exclude_empty_lines );
	}
}

if ( ! function_exists( 'alg_wc_cog_insert_in_array' ) ) {
	/**
	 * Deprecated. Use wpfcogs_insert_in_array() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @param array  $original_array      Original array.
	 * @param array  $array_to_insert     Array to insert.
	 * @param string $key_to_insert_after Key to insert after.
	 *
	 * @return array
	 */
	function alg_wc_cog_insert_in_array( $original_array, $array_to_insert, $key_to_insert_after ) {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_insert_in_array' );
		return wpfcogs_insert_in_array( $original_array, $array_to_insert, $key_to_insert_after );
	}
}

if ( ! function_exists( 'alg_wc_cog_get_table_html' ) ) {
	/**
	 * Deprecated. Use wpfcogs_get_table_html() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @param array $data Table data.
	 * @param array $args Optional arguments.
	 *
	 * @return string
	 */
	function alg_wc_cog_get_table_html( $data, $args = array() ) {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_get_table_html' );
		return wpfcogs_get_table_html( $data, $args );
	}
}

if ( ! function_exists( 'alg_wc_cog_format_cost' ) ) {
	/**
	 * Deprecated. Use wpfcogs_format_cost() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @param float $cost Raw price.
	 * @param array $args Optional arguments.
	 *
	 * @return string
	 */
	function alg_wc_cog_format_cost( $cost, $args = array() ) {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_format_cost' );
		return wpfcogs_format_cost( $cost, $args );
	}
}

if ( ! function_exists( 'alg_wc_cog_is_user_allowed' ) ) {
	/**
	 * Deprecated. Use wpfcogs_is_user_allowed() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @param WP_User|null $user User object or null.
	 *
	 * @return bool
	 */
	function alg_wc_cog_is_user_allowed( $user = null ) {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_is_user_allowed' );
		return wpfcogs_is_user_allowed( $user );
	}
}

if ( ! function_exists( 'alg_wc_cog_array_to_string' ) ) {
	/**
	 * Deprecated. Use wpfcogs_array_to_string() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @param array $arr  Array.
	 * @param array $args Optional arguments.
	 *
	 * @return string
	 */
	function alg_wc_cog_array_to_string( $arr, $args = array() ) {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_array_to_string' );
		return wpfcogs_array_to_string( $arr, $args );
	}
}

if ( ! function_exists( 'alg_wc_cog_get_blocked_options_message' ) ) {
	/**
	 * Deprecated. Use wpfcogs_get_blocked_options_message() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @return string
	 */
	function alg_wc_cog_get_blocked_options_message() {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_get_blocked_options_message' );
		return wpfcogs_get_blocked_options_message();
	}
}

if ( ! function_exists( 'alg_wc_cog_sanitize_number' ) ) {
	/**
	 * Deprecated. Use wpfcogs_sanitize_number() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @param array|null $args Arguments.
	 *
	 * @return float
	 */
	function alg_wc_cog_sanitize_number( $args = null ) {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_sanitize_number' );
		return wpfcogs_sanitize_number( $args );
	}
}

if ( ! function_exists( 'alg_wc_cog_normalize_price' ) ) {
	/**
	 * Deprecated. Use wpfcogs_normalize_price() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @param string $price Price string.
	 *
	 * @return string
	 */
	function alg_wc_cog_normalize_price( $price ) {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_normalize_price' );
		return wpfcogs_normalize_price( $price );
	}
}

if ( ! function_exists( 'alg_wc_cog_sanitize_cost' ) ) {
	/**
	 * Deprecated. Use wpfcogs_sanitize_cost() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @param array|null $args Arguments.
	 *
	 * @return float
	 */
	function alg_wc_cog_sanitize_cost( $args = null ) {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_sanitize_cost' );
		return wpfcogs_sanitize_cost( $args );
	}
}

if ( ! function_exists( 'alg_wc_cog_need_to_replace_cog_comma_by_dots' ) ) {
	/**
	 * Deprecated. Use wpfcogs_need_to_replace_cog_comma_by_dots() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @return bool
	 */
	function alg_wc_cog_need_to_replace_cog_comma_by_dots() {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_need_to_replace_cog_comma_by_dots' );
		return wpfcogs_need_to_replace_cog_comma_by_dots();
	}
}

if ( ! function_exists( 'alg_wc_cog_need_to_replace_cog_comma_by_dots_default' ) ) {
	/**
	 * Deprecated. Use wpfcogs_need_to_replace_cog_comma_by_dots_default() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @return string
	 */
	function alg_wc_cog_need_to_replace_cog_comma_by_dots_default() {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_need_to_replace_cog_comma_by_dots_default' );
		return wpfcogs_need_to_replace_cog_comma_by_dots_default();
	}
}

if ( ! function_exists( 'alg_wc_cog_get_regular_price' ) ) {
	/**
	 * Deprecated. Use wpfcogs_get_regular_price() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @param WC_Product $product Product object.
	 * @param array|null $args    Optional arguments.
	 *
	 * @return string
	 */
	function alg_wc_cog_get_regular_price( $product, $args = null ) {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_get_regular_price' );
		return wpfcogs_get_regular_price( $product, $args );
	}
}

if ( ! function_exists( 'alg_wc_cog_get_html_table_structure' ) ) {
	/**
	 * Deprecated. Use wpfcogs_get_html_table_structure() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @param array|null $args Arguments.
	 *
	 * @return string
	 */
	function alg_wc_cog_get_html_table_structure( $args = null ) {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_get_html_table_structure' );
		return wpfcogs_get_html_table_structure( $args );
	}
}

if ( ! function_exists( 'alg_wc_cog_get_cost_subtracting_tax_rate' ) ) {
	/**
	 * Deprecated. Use wpfcogs_get_cost_subtracting_tax_rate() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @param array|null $args Arguments.
	 *
	 * @return float|bool
	 */
	function alg_wc_cog_get_cost_subtracting_tax_rate( $args = null ) {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_get_cost_subtracting_tax_rate' );
		return wpfcogs_get_cost_subtracting_tax_rate( $args );
	}
}

if ( ! function_exists( 'alg_wc_cog_generate_wpdb_prepare_placeholders_from_array' ) ) {
	/**
	 * Deprecated. Use wpfcogs_generate_wpdb_prepare_placeholders_from_array() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @param array $array Array of values.
	 *
	 * @return string
	 */
	function alg_wc_cog_generate_wpdb_prepare_placeholders_from_array( $array ) {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_generate_wpdb_prepare_placeholders_from_array' );
		return wpfcogs_generate_wpdb_prepare_placeholders_from_array( $array );
	}
}

if ( ! function_exists( 'alg_wc_cog_get_admin_orders_page_url' ) ) {
	/**
	 * Deprecated. Use wpfcogs_get_admin_orders_page_url() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @return string
	 */
	function alg_wc_cog_get_admin_orders_page_url() {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_get_admin_orders_page_url' );
		return wpfcogs_get_admin_orders_page_url();
	}
}

if ( ! function_exists( 'alg_wc_cog_get_option' ) ) {
	/**
	 * Deprecated. Use wpfcogs_get_option() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @param string $option               Option name.
	 * @param mixed  $default_value        Default value.
	 * @param bool   $get_value_from_cache Whether to get from cache.
	 *
	 * @return mixed
	 */
	function alg_wc_cog_get_option( $option, $default_value = false, $get_value_from_cache = true ) {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_get_option' );
		return wpfcogs_get_option( $option, $default_value, $get_value_from_cache );
	}
}

if ( ! function_exists( 'alg_wc_cog_get_gateways_option_default' ) ) {
	/**
	 * Deprecated. Use wpfcogs_get_gateways_option_default() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @return string
	 */
	function alg_wc_cog_get_gateways_option_default() {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_get_gateways_option_default' );
		return wpfcogs_get_gateways_option_default();
	}
}

if ( ! function_exists( 'alg_wc_cog_get_ignore_item_refund_amount_default' ) ) {
	/**
	 * Deprecated. Use wpfcogs_get_ignore_item_refund_amount_default() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @return string
	 */
	function alg_wc_cog_get_ignore_item_refund_amount_default() {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_get_ignore_item_refund_amount_default' );
		return wpfcogs_get_ignore_item_refund_amount_default();
	}
}

if ( ! function_exists( 'alg_wc_cog_enqueue_script' ) ) {
	/**
	 * Deprecated. Use wpfcogs_enqueue_script() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @param string $handle Script handle.
	 * @param string $src    Script source URL.
	 * @param array  $deps   Dependencies.
	 * @param mixed  $ver    Version.
	 * @param array  $args   Additional arguments.
	 */
	function alg_wc_cog_enqueue_script( $handle, $src = '', $deps = array(), $ver = false, $args = array() ) {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_enqueue_script' );
		wpfcogs_enqueue_script( $handle, $src, $deps, $ver, $args );
	}
}

if ( ! function_exists( 'alg_wc_cog_enqueue_style' ) ) {
	/**
	 * Deprecated. Use wpfcogs_enqueue_style() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @param string $handle Style handle.
	 * @param string $src    Style source URL.
	 * @param array  $deps   Dependencies.
	 * @param mixed  $ver    Version.
	 * @param string $media  Media type.
	 */
	function alg_wc_cog_enqueue_style( $handle, $src = '', $deps = array(), $ver = false, $media = 'all' ) {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_enqueue_style' );
		wpfcogs_enqueue_style( $handle, $src, $deps, $ver, $media );
	}
}

if ( ! function_exists( 'alg_wc_cog_is_plugin_active' ) ) {
	/**
	 * Deprecated. Use wpfcogs_is_plugin_active() instead.
	 *
	 * @deprecated 4.1.6
	 *
	 * @param string $plugin Plugin path.
	 *
	 * @return bool
	 */
	function alg_wc_cog_is_plugin_active( $plugin ) {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs_is_plugin_active' );
		return wpfcogs_is_plugin_active( $plugin );
	}
}

if ( ! function_exists( 'alg_wc_cog' ) ) {
	/**
	 * Deprecated. Use wpfcogs() instead.
	 *
	 * Returns the main instance of the plugin.
	 *
	 * @deprecated 4.1.6
	 *
	 * @return WPFCOGS
	 */
	function alg_wc_cog() {
		_deprecated_function( __FUNCTION__, '4.1.6', 'wpfcogs' );
		return wpfcogs();
	}
}
