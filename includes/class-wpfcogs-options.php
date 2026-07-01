<?php
/**
 * Cost of Goods for WooCommerce - Options.
 *
 * @version 3.3.7
 * @since   3.3.7
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFCOGS_Options' ) ) {

	class WPFCOGS_Options {

		/**
		 * Options.
		 *
		 * @since 3.3.7
		 *
		 * @var array
		 */
		protected $options = array();

		/**
		 * get_option.
		 *
		 * @version 3.3.7
		 * @since   3.3.7
		 *
		 * @param  $option
		 * @param  $default_value
		 * @param  $get_value_from_cache
		 *
		 * @return false|mixed|null
		 */
		function get_option( $option, $default_value = false, $get_value_from_cache = true ) {
			if (
				! isset( $this->options[ $option ] ) ||
				! $get_value_from_cache
			) {
				$this->options[ $option ] = get_option( $option, $default_value );
			}

			return $this->options[ $option ];
		}

	}

}