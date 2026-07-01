<?php
/**
 * Cost of Goods for WooCommerce - Deprecated Shortcodes.
 *
 * Maps old alg_wc_cog_* shortcodes to new wpfcogs_* shortcodes
 * and triggers _deprecated_hook() notices.
 *
 * @version 4.1.6
 * @since   4.1.6
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Deprecated shortcode: [alg_wc_cog_product_cost]
 *
 * @deprecated 4.1.6 Use [wpfcogs_product_cost] instead.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string
 */
function wpfcogs_deprecated_sc_product_cost( $atts ) {
	_deprecated_hook( 'alg_wc_cog_product_cost', '4.1.6', 'wpfcogs_product_cost' );
	return wpfcogs()->core->products->sc_wpfcogs_product_cost( $atts );
}
add_shortcode( 'alg_wc_cog_product_cost', 'wpfcogs_deprecated_sc_product_cost' );

/**
 * Deprecated shortcode: [alg_wc_cog_product_profit]
 *
 * @deprecated 4.1.6 Use [wpfcogs_product_profit] instead.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string
 */
function wpfcogs_deprecated_sc_product_profit( $atts ) {
	_deprecated_hook( 'alg_wc_cog_product_profit', '4.1.6', 'wpfcogs_product_profit' );
	return wpfcogs()->core->products->sc_wpfcogs_product_profit( $atts );
}
add_shortcode( 'alg_wc_cog_product_profit', 'wpfcogs_deprecated_sc_product_profit' );
