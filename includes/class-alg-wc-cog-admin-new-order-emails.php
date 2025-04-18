<?php
/**
 * Cost of Goods for WooCommerce - Admin new order emails.
 *
 * @version 3.6.9
 * @since   3.6.9
 *
 * @see woocommerce/templates/emails/email-order-details.php
 * @see woocommerce/templates/emails/admin-new-order.php
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

use \Automattic\WooCommerce\Utilities\FeaturesUtil;

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Admin_New_Order_Emails' ) ) {

	class Alg_WC_Cost_of_Goods_Admin_New_Order_Emails {

		/**
		 * Init.
		 *
		 * @version 3.6.9
		 * @since   3.6.9
		 *
		 * @return void
		 */
		function init() {
			// Create admin new order area.
			add_action( 'woocommerce_email_order_meta', array( $this, 'create_cog_admin_new_order_email_meta' ), 10, 2 );

			// Add new order email COG title.
			add_action( 'alg_cog_admin_new_order_email_meta', array( $this, 'add_new_order_email_cog_title' ), 10, 2 );

			// Add individual item cost and profit.
			add_action( 'alg_cog_admin_new_order_email_meta', array( $this, 'add_individual_item_cost_and_profit' ), 20, 2 );

			// Add total cost and profit.
			add_action( 'alg_cog_admin_new_order_email_meta', array( $this, 'add_total_cost_and_profit' ), 20, 2 );

			// Add margin at the end.
			add_action( 'alg_cog_admin_new_order_email_meta', array( $this, 'add_margin_at_the_end' ), 50, 2 );

			// Enable cog admin new order email meta.
			add_filter( 'alg_cog_admin_new_order_email_meta_enabled', array( $this, 'enable_cog_admin_new_order_email_meta' ) );
		}

		/**
		 * enable_cog_admin_new_order_email_meta.
		 *
		 * @version 3.6.9
		 * @since   3.6.9
		 *
		 * @param $enabled
		 *
		 * @return bool
		 */
		function enable_cog_admin_new_order_email_meta( $enabled ) {
			$options = array(
				array( 'option_id' => 'alg_wc_cog_order_admin_new_order_email_profit_and_cost', 'default' => 'no' ),
				array( 'option_id' => 'alg_wc_cog_order_admin_new_order_email_item_profit_and_cost', 'default' => 'no' ),
			);
			foreach ( $options as $option ) {
				if ( 'yes' === alg_wc_cog_get_option( $option['option_id'], $option['default'] ) ) {
					return true;
					break;
				}
			}

			return $enabled;
		}

		/**
		 * add_new_order_email_cog_title.
		 *
		 * @version 3.6.9
		 * @since   3.6.9
		 *
		 * @param $order_obj
		 * @param $sent_to_admin
		 *
		 * @return void
		 */
		function add_new_order_email_cog_title( $order_obj, $sent_to_admin ) {
			$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );
			$heading_class              = $email_improvements_enabled ? 'email-order-detail-heading' : '';
			?>
            <h2 class="<?php echo esc_attr( $heading_class ); ?>"><?php _e( 'Cost of goods', 'cost-of-goods-for-woocommerce' ) ?></h2>
			<?php
		}

		/**
		 * create_cog_admin_new_order_email_meta.
		 *
		 * @version 3.6.9
		 * @since   3.6.9
		 *
		 * @param $order_obj
		 * @param $sent_to_admin
		 *
		 * @return void
		 */
		function create_cog_admin_new_order_email_meta( $order_obj, $sent_to_admin ) {
			if (
				$sent_to_admin &&
				! empty( $order_id = $order_obj->get_id() ) &&
				is_a( $order_obj, 'WC_Order' ) &&
				apply_filters( 'alg_cog_admin_new_order_email_meta_enabled', false, $order_obj, $sent_to_admin )
			) {
				do_action( 'alg_cog_admin_new_order_email_meta', $order_obj, $sent_to_admin );
			}
		}

		/**
		 * add_individual_item_cost_and_profit.
		 *
		 * @version 3.6.9
		 * @since   3.6.9
		 *
		 * @param $order
		 * @param $sent_to_admin
		 *
		 * @throws Exception
		 * @return void
		 */
		function add_individual_item_cost_and_profit( $order, $sent_to_admin ) {
			if ( 'yes' !== alg_wc_cog_get_option( 'alg_wc_cog_order_admin_new_order_email_item_profit_and_cost', 'no' ) ) {
				return;
			}
			$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );
			$heading_class              = $email_improvements_enabled ? 'email-order-detail-heading' : '';
			$order_table_class          = $email_improvements_enabled ? 'email-order-details' : '';
			$text_align                 = is_rtl() ? 'right' : 'left';
			$show_sku                   = true;
			?>
            <table class="td font-family <?php echo esc_attr( $order_table_class ); ?>" cellspacing="0" cellpadding="6" style="width: 100%;" border="1">
                <thead>
                <tr>
                    <th class="td" scope="col"
                        style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
                    <th class="td" scope="col"
                        style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Cost', 'cost-of-goods-for-woocommerce' ); ?></th>
                    <th class="td" scope="col"
                        style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Profit', 'cost-of-goods-for-woocommerce' ); ?></th>
                </tr>
                </thead>
                <tbody>

				<?php
				$items             = $order->get_items();
				foreach ( $items as $item_id => $item ) :
					$product = $item->get_product();
					$sku           = '';
					$purchase_note = '';

					if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
						continue;
					}

					if ( is_object( $product ) ) {
						$sku           = $product->get_sku();
						$purchase_note = $product->get_purchase_note();

					}
					?>
                    <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">

                        <td class="td font-family text-align-left" style="vertical-align: middle; word-wrap:break-word;">
							<?php
							/**
							 * Order Item Name hook.
							 *
							 * @since 2.1.0
							 *
							 * @param WC_Order_Item_Product $item The item being displayed.
							 * @param string $item_name The item name HTML.
							 */
							echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );
							// SKU.
							if ( $show_sku && $sku ) {
								echo wp_kses_post( ' (#' . $sku . ')' );
							}

							?>
                        </td>
                        <td class="td font-family" style="vertical-align:middle;">
							<?php echo '<span style="color:red;">' . alg_wc_cog_format_cost( wc_get_order_item_meta( $item_id, '_alg_wc_cog_item_cost' ) ) . '</span>'; ?>
                        </td>
                        <td class="td font-family" style="vertical-align:middle;">
							<?php echo '<span style="color:green;">' . alg_wc_cog_format_cost( wc_get_order_item_meta( $item_id, '_alg_wc_cog_item_profit' ) ) . '</span>'; ?>
                        </td>
                    </tr>

				<?php endforeach; ?>

                </tbody>
            </table>
			<?php
		}

		/**
		 * woocommerce_email_order_meta.
		 *
		 * @version 3.6.9
		 * @since   2.3.5
		 *
		 * @param $order
		 * @param $sent_to_admin
		 */
		function add_total_cost_and_profit( $order, $sent_to_admin ) {
			if ( 'yes' !== alg_wc_cog_get_option( 'alg_wc_cog_order_admin_new_order_email_profit_and_cost', 'no' ) ) {
				return;
			}
			$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );
			$heading_class              = $email_improvements_enabled ? 'email-order-detail-heading' : '';
			$order_table_class          = $email_improvements_enabled ? 'email-order-details' : '';
			$text_align                 = is_rtl() ? 'right' : 'left';

			$cost                = $order->get_meta( '_alg_wc_cog_order_' . 'cost', true );
			$handling_fee        = $order->get_meta( '_alg_wc_cog_order_' . 'handling_fee', true );
			$profit              = $order->get_meta( '_alg_wc_cog_order_' . 'profit', true );
			$profit_percent      = $order->get_meta( '_alg_wc_cog_order_' . 'profit_percent', true );
			$profit_margin       = $order->get_meta( '_alg_wc_cog_order_' . 'profit_margin', true );
			$profit_template     = get_option( 'alg_wc_cog_orders_profit_html_template', '%profit%' );
			$profit_placeholders = array(
				'%profit%'         => alg_wc_cog()->core->orders->format_order_column_value( $profit, 'profit' ),
				'%profit_percent%' => alg_wc_cog()->core->orders->format_order_column_value( $profit_percent, 'profit_percent' ),
				'%profit_margin%'  => alg_wc_cog()->core->orders->format_order_column_value( $profit_margin, 'profit_margin' ),
			);
			$profit_html         = str_replace( array_keys( $profit_placeholders ), $profit_placeholders, $profit_template );
			$table_args          = array(
				//'table_style'        => 'width:100%;margin-bottom: 40px',
				'table_style'        => 'width:100%;',
				'table_heading_type' => 'vertical',
				'table_attributes'   => array( 'cellspacing' => 0, 'cellpadding' => 6, 'border' => 1 ),
				'table_class'        => 'td font-family ' . esc_attr( $order_table_class ) . '',
				'columns_styles'     => array( 'text-align' => 'right', 'border-left' => 0, 'border-top' => 0 ),
				'columns_classes'    => array( 'td', 'td' ),
			);
			$table_data          = array(
				array( __( 'Cost', 'cost-of-goods-for-woocommerce' ), ( '' !== $cost ? '<span style="color:red;">' . alg_wc_cog_format_cost( $cost ) . '</span>' : '' ) ),
				array( __( 'Profit', 'cost-of-goods-for-woocommerce' ), ( '' !== $profit ? '<span style="color:green;">' . $profit_html . '</span>' : '' ) ),
			);
			?>

			<?php echo alg_wc_cog_get_table_html( $table_data, $table_args ); ?>
			<?php
		}

		/**
		 * add_margin_at_the_end.
		 *
		 * @version 3.6.9
		 * @since   2.3.5
		 *
		 * @return void
		 */
		function add_margin_at_the_end() {
			$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );
			echo '<div style="margin-bottom: ' . ( $email_improvements_enabled ? '24px' : '40px' ) . '"></div>';
		}
	}
}