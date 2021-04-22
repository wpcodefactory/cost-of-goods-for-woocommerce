<?php
/**
 * Cost of Goods for WooCommerce - Settings
 *
 * @version 2.4.1
 * @since   1.0.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Settings_Cost_of_Goods' ) ) :

class Alg_WC_Settings_Cost_of_Goods extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 2.2.0
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
		require_once( 'class-alg-wc-cog-settings-gateways.php' );
		require_once( 'class-alg-wc-cog-settings-shipping.php' );
		require_once( 'class-alg-wc-cog-settings-currencies.php' );
		require_once( 'class-alg-wc-cog-settings-tools.php' );
		require_once( 'class-alg-wc-cog-settings-advanced.php' );
		// Create notice about pro
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'create_notice_regarding_pro' ) );
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'highlight_premium_notice_on_disabled_setting_click' ) );
	}

	/**
	 * highlight_premium_notice_on_disabled_setting_click.
	 *
	 * @version 2.4.1
	 * @since   2.4.1
	 */
	function highlight_premium_notice_on_disabled_setting_click(){
		if ( '' === apply_filters( 'alg_wc_ev_settings', true ) ) {
			return;
		}
		?>
		<script>
			jQuery(document).ready(function ($) {
				jQuery(document).ready(function ($) {
					let highlighter = {
						targetClass: '.alg-wc-cog-premium-notice',
						highlight: function () {
							window.scrollTo({
								top: 0,
								behavior: 'smooth'
							});
							setTimeout(function () {
								$(highlighter.targetClass).addClass('alg-wc-cog-blink');
							}, 300);
							setTimeout(function () {
								$(highlighter.targetClass).removeClass('alg-wc-cog-blink');
							}, 3000);
						}
					};
					function createDisabledElem(){
						$(".form-table *:disabled,.form-table *[readonly],.form-table .select2-container--disabled").each(function () {
							$(this).parent().css({
								"position": "relative"
							});
							let position = $(this).position();
							position.top = $(this)[0].offsetTop;
							let disabledDiv = $("<div class='alg-wc-cog-disabled alg-wc-cog-highlight-premium-notice'></div>").insertAfter($(this));
							disabledDiv.css({
								"position": "absolute",
								"left": position.left,
								"top": position.top,
								"width": $(this).outerWidth(),
								"height": $(this).outerHeight(),
								"cursor": 'pointer'
							});
						});
					}
					createDisabledElem();
					$("label:has(input:disabled),label:has(input[readonly])").addClass('alg-wc-cog-highlight-premium-notice');
					$(".alg-wc-cog-highlight-premium-notice, .select2-container--disabled").on('click', highlighter.highlight);
				});
			});
		</script>
		<style>
			.alg-wc-cog-blink{
				animation: alg-dtwp-blink 1s;
				animation-iteration-count: 3;
			}
			@keyframes alg-dtwp-blink { 50% { background-color:#ececec ; }  }
		</style>
		<?php
	}

	/**
	 * create_notice_regarding_pro.
	 *
	 * @version 2.4.1
	 * @since   2.4.1
	 */
	function create_notice_regarding_pro() {
		if ( true === apply_filters( 'alg_wc_cog_settings', true ) ) {
			$pro_version_title      = __( 'Cost of Goods for WooCommerce Pro', 'cost-of-goods-for-woocommerce' );
			$pro_version_url        = 'https://wpfactory.com/item/cost-of-goods-for-woocommerce/';
			$plugin_icon_url        = 'https://ps.w.org/cost-of-goods-for-woocommerce/assets/icon-128x128.png?rev=1884298';
			$upgrade_btn_icon_class = 'dashicons-before dashicons-unlock';
			// Message
			$message = sprintf( '<img style="%s" src="%s"/><span style="%s">%s</span>',
				'margin-right:10px;width:38px;vertical-align:middle',
				$plugin_icon_url,
				'vertical-align: middle;margin:0 14px 0 0;',
				sprintf( __( 'Disabled options can be unlocked using <a href="%s" target="_blank">%s</a>', 'cost-of-goods-for-woocommerce' ), $pro_version_url, '<strong>' . $pro_version_title . '</strong>' )
			);
			// Button
			$button = sprintf( '<a style="%s" target="_blank" class="button-primary" href="%s"><i style="%s" class="%s"></i>%s</a>',
				'vertical-align:middle;display:inline-block;margin:0',
				$pro_version_url,
				'position:relative;top:3px;margin:0 2px 0 -2px;',
				$upgrade_btn_icon_class,
				__( 'Upgrade to Pro version', 'cost-of-goods-for-woocommerce' )
			);
			echo '<div id="message" class="alg-wc-cog-premium-notice notice notice-info inline"><p style="margin:5px 0">' . $message . $button . '</p></div>';
		}
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
