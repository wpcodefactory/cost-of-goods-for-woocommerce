<?php
/**
 * Cost of Goods for WooCommerce - Shortcode Settings.
 *
 * @version 3.1.6
 * @since   3.1.6
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Settings_Shortcodes' ) ) :

	class Alg_WC_Cost_of_Goods_Settings_Shortcodes extends Alg_WC_Cost_of_Goods_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 3.1.6
		 * @since   3.1.6
		 */
		function __construct() {
			$this->id   = 'shortcodes';
			$this->desc = __( 'Shortcodes', 'cost-of-goods-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * get_allowed_user_roles_option.
		 *
		 * @version 3.1.6
		 * @since   3.1.6
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
		 * @version 3.1.6
		 * @since   3.1.6
		 * @todo    [later] "Force costs update on ...": better title and desc (3x)
		 */
		function get_settings() {

			$product_sc_opts = array(
				array(
					'title' => __( 'Product shortcodes', 'cost-of-goods-for-woocommerce' ),
					'type'  => 'title',
					'id'    => 'alg_wc_cog_product_shortcode_opts',
				),
				array(
					'title'    => '[alg_wc_cog_product_profit]',
					'desc'     => __( 'Displays the product profit', 'cost-of-goods-for-woocommerce' ),
					'desc_tip' => __( 'Params:', 'cost-of-goods-for-woocommerce' ) . '' . '<br />' . alg_wc_cog_array_to_string( array(
							'product_id'      => __( 'Product ID.', 'cost-of-goods-for-woocommerce' ) . ' ' . __( 'If empty, will try to get the current product id.', 'cost-of-goods-for-woocommerce' ),
							'profit_template' => __( 'Profit template.', 'cost-of-goods-for-woocommerce' ) . ' ' . __( 'Default:', 'cost-of-goods-for-woocommerce' ) . ' <code>' . _wp_specialchars( get_option( 'alg_wc_cog_product_profit_html_template', '%profit% (%profit_percent%)' ) ) . '</code>.',
							'html_template'   => __( 'HTML template.', 'cost-of-goods-for-woocommerce' ) . ' ' . __( 'Default:', 'cost-of-goods-for-woocommerce' ) . ' <code>' . _wp_specialchars( '<span class="alg-wc-cog-product-profit">{content}</span>' ) . '</code>.',
						), array( 'item_template' => '<li><code>{key}</code> - {value}', 'glue' => '<br /></li>' ) ),
					'id'       => 'alg_wc_cog_shortcode_product_profit',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'title'    => '[alg_wc_cog_product_cost]',
					'desc'     => __( 'Displays the product cost', 'cost-of-goods-for-woocommerce' ),
					'desc_tip' => __( 'Params:', 'cost-of-goods-for-woocommerce' ) . '' . '<br />' . alg_wc_cog_array_to_string( array(
							'product_id'    => __( 'Product ID.', 'cost-of-goods-for-woocommerce' ) . ' ' . __( 'If empty, will try to get the current product id.', 'cost-of-goods-for-woocommerce' ),
							'html_template' => __( 'HTML template.', 'cost-of-goods-for-woocommerce' ) . ' ' . __( 'Default:', 'cost-of-goods-for-woocommerce' ) . ' <code>' . _wp_specialchars( '<span class="alg-wc-cog-product-cost">{content}</span>' ) . '</code>.',
						), array( 'item_template' => '<li><code>{key}</code> - {value}', 'glue' => '<br /></li>' ) ),
					'id'       => 'alg_wc_cog_shortcode_product_cost',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_cog_product_shortcode_opts',
				),
			);

			return array_merge( $product_sc_opts );
		}

	}

endif;

return new Alg_WC_Cost_of_Goods_Settings_Shortcodes();
