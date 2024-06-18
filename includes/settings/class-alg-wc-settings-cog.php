<?php
/**
 * Cost of Goods for WooCommerce - Settings
 *
 * @version 3.4.6
 * @since   1.0.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Settings_Cost_of_Goods' ) ) :

class Alg_WC_Settings_Cost_of_Goods extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 3.4.6
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id    = 'alg_wc_cost_of_goods';
		$this->label = __( 'Cost of Goods', 'cost-of-goods-for-woocommerce' );
		parent::__construct();
		// Sections
		require_once( 'class-alg-wc-cog-settings-section.php' );
		require_once( 'class-alg-wc-cog-settings-products.php' );
		require_once( 'class-alg-wc-cog-settings-orders.php' );
		require_once( 'class-alg-wc-cog-settings-shortcodes.php' );
		require_once( 'class-alg-wc-cog-settings-gateways.php' );
		require_once( 'class-alg-wc-cog-settings-shipping.php' );
		require_once( 'class-alg-wc-cog-settings-shipping-classes.php' );
		require_once( 'class-alg-wc-cog-settings-currencies.php' );
		require_once( 'class-alg-wc-cog-settings-tools.php' );
		require_once( 'class-alg-wc-cog-settings-analytics.php' );
		require_once( 'class-alg-wc-cog-settings-advanced.php' );
		require_once( 'class-alg-wc-cog-settings-compatibility.php' );
		// Create notice about pro
		add_action( 'admin_init', array( $this, 'add_promoting_notice' ) );
		// Sanitize raw parameter
		add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'sanitize_raw_parameter' ), 10, 3 );
	}

	/**
	 * sanitize_raw_parameter.
	 *
	 * @version 2.5.3
	 * @since   2.5.3
	 *
	 * @param $value
	 * @param $option
	 * @param $raw_value
	 *
	 * @return mixed|string
	 */
	function sanitize_raw_parameter( $value, $option, $raw_value ) {
		if ( ! isset( $option['alg_wc_cog_raw'] ) || empty( $option['alg_wc_cog_raw'] ) ) {
			return $value;
		}
		$new_value = wp_kses_post( trim( $raw_value ) );
		return $new_value;
	}

	/**
	 * add_promoting_notice.
	 *
	 * @version 2.4.3
	 * @since   2.4.3
	 */
	function add_promoting_notice() {
		$promoting_notice = wpfactory_promoting_notice();
		$promoting_notice->set_args( array(
			'url_requirements'              => array(
				'page_filename' => 'admin.php',
				'params'        => array( 'page' => 'wc-settings', 'tab' => $this->id ),
			),
			'enable'                        => true === apply_filters( 'alg_wc_cog_settings', true ),
			'optimize_plugin_icon_contrast' => true,
			'template_variables'            => array(
				'%pro_version_url%'    => 'https://wpfactory.com/item/cost-of-goods-for-woocommerce/',
				'%plugin_icon_url%'    => 'https://ps.w.org/cost-of-goods-for-woocommerce/assets/icon-128x128.png?rev=1884298',
				'%pro_version_title%'  => __( 'Cost of Goods for WooCommerce Pro', 'cost-of-goods-for-woocommerce' ),
				'%main_text%'          => __( 'Disabled options can be unlocked using <a href="%pro_version_url%" target="_blank"><strong>%pro_version_title%</strong></a>', 'cost-of-goods-for-woocommerce' ),
				'%btn_call_to_action%' => __( 'Upgrade to Pro version', 'cost-of-goods-for-woocommerce' ),
			),
		) );
		$promoting_notice->init();
	}

	/**
	 * get_settings.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function get_settings() {
		global $current_section;
		return array_merge( apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ), array(
			array(
				'title'     => __( 'Reset Settings', 'cost-of-goods-for-woocommerce' ),
				'type'      => 'title',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
			array(
				'title'     => __( 'Reset section settings', 'cost-of-goods-for-woocommerce' ),
				'desc'      => '<strong>' . __( 'Reset', 'cost-of-goods-for-woocommerce' ) . '</strong>',
				'desc_tip'  => __( 'Enable the checkbox and save changes to reset the settings.', 'cost-of-goods-for-woocommerce' ),
				'id'        => $this->id . '_' . $current_section . '_reset',
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
		) );
	}

	/**
	 * maybe_reset_settings.
	 *
	 * @version 1.4.7
	 * @since   1.0.0
	 */
	function maybe_reset_settings() {
		global $current_section;
		if ( 'yes' === get_option( $this->id . '_' . $current_section . '_reset', 'no' ) ) {
			foreach ( $this->get_settings() as $value ) {
				if ( isset( $value['id'] ) ) {
					$id = explode( '[', $value['id'] );
					delete_option( $id[0] );
				}
			}
			if ( method_exists( 'WC_Admin_Settings', 'add_message' ) ) {
				WC_Admin_Settings::add_message( __( 'Your settings have been reset.', 'cost-of-goods-for-woocommerce' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'admin_notices_settings_reset_success' ) );
			}
		}
	}

	/**
	 * admin_notices_settings_reset_success.
	 *
	 * @version 1.2.0
	 * @since   1.1.0
	 */
	function admin_notices_settings_reset_success() {
		echo '<div class="notice notice-warning is-dismissible"><p><strong>' .
			__( 'Your settings have been reset.', 'cost-of-goods-for-woocommerce' ) . '</strong></p></div>';
	}


	/**
	 * Save settings.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function save() {
		parent::save();
		$this->maybe_reset_settings();
		global $current_section;
		do_action( 'alg_wc_cog_save_settings', $current_section );
	}

}

endif;

return new Alg_WC_Settings_Cost_of_Goods();
