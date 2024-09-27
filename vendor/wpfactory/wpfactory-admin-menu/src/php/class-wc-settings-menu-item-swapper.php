<?php
/**
 * WPFactory Admin Menu - WooCommerce Settings Menu Item Swapper.
 *
 * @version 1.0.1
 * @since   1.0.1
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Admin_Menu;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPFactory\WPFactory_Admin_Menu\WC_Settings_Menu_Item_Swapper' ) ) {

	/**
	 * WPFactory Admin Menu.
	 *
	 * @version 1.0.1
	 * @since   1.0.1
	 */
	class WC_Settings_Menu_Item_Swapper {

		/**
		 * $args.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		protected $args = array();

		/**
		 * Initialized.
		 *
		 * @since 1.0.0
		 *
		 * @var bool
		 */
		protected $initialized = false;

		function swap( $args = null ) {
			$args = wp_parse_args( $args, array(
				'wc_settings_tab_id'         => '',
				'replacement_menu_item_slug' => '',
				'page_title'                 => ''
			) );
			$this->args[ $args['replacement_menu_item_slug'] ] = $args;
		}

		/**
		 * Initializes.
		 *
		 * @version 1.0.1
		 * @since   1.0.1
		 *
		 * @return void
		 */
		function init() {
			if ( $this->initialized ) {
				return;
			}
			$this->initialized = true;

			// Replaces WC Settings Menu Item.
			add_filter( 'parent_file', array( $this, 'replace_wc_settings_menu_item' ) );

			// Hide Current plugin Settings tab.
			add_action( 'admin_head', array( $this, 'hide_plugin_settings_tab' ) );

			// Hides WC Settings tabs.
			add_action( 'admin_head', array( $this, 'hide_wc_settings_tabs' ) );

			// Add page title.
			add_action( 'all_admin_notices', array( $this, 'add_page_title' ) );
		}

		/**
		 * Adds page title.
		 *
		 * @version 1.0.1
		 * @since   1.0.1
		 *
		 * @return void
		 */
		function add_page_title() {
			if (
				isset( $_GET['page'] ) &&
				'wc-settings' === $_GET['page'] &&
				isset( $_GET['tab'] ) &&
				! empty( $found_items = wp_list_filter( $this->args, array( 'wc_settings_tab_id' => $_GET['tab'] ) ) )
			) {
				$first_item = reset( $found_items );
				$page_title = $first_item['page_title'];
				if ( ! empty( $page_title ) ) {
					echo '<div class="wrap"><h1>' . esc_html( $page_title ) . '</h1></div>';
				}
			}
		}

		/**
		 * replace_wc_settings_menu_item.
		 *
		 * @version 1.0.1
		 * @since   1.0.1
		 *
		 * @param $file
		 *
		 * @return mixed
		 */
		function replace_wc_settings_menu_item( $file ) {
			global $plugin_page;
			if (
				'wc-settings' === $plugin_page &&
				isset( $_GET['tab'] ) &&
				! empty( $found_items = wp_list_filter( $this->args, array( 'wc_settings_tab_id' => $_GET['tab'] ) ) )
			) {
				$first_item                 = reset( $found_items );
				$replacement_menu_item_slug = $first_item['replacement_menu_item_slug'];
				$plugin_page                = $replacement_menu_item_slug;
			}

			return $file;
		}

		/**
		 * hide_plugin_settings_tab.
		 *
		 * @version 1.0.1
		 * @since   1.0.1
		 *
		 * @return void
		 */
		function hide_plugin_settings_tab() {
			global $plugin_page;
			if (
				'wc-settings' === $plugin_page &&
				(
					! isset( $_GET['tab'] ) ||
					( isset( $_GET['tab'] ) && empty( $found_items = wp_list_filter( $this->args, array( 'wc_settings_tab_id' => $_GET['tab'] ) ) ) )
				)
			) {
				$tab_ids          = array_column( $this->args, 'wc_settings_tab_id' );
				$css_selector_arr = array();
				foreach ( $tab_ids as $tab ) {
					$css_selector_arr[] = '.wrap.woocommerce .nav-tab-wrapper a[href*="tab=' . $tab . '"]';
				}

				?>
				<style>
					<?php echo implode(', ', $css_selector_arr)?>
					{
						display: none
					}
				</style>
				<?php
			}
		}

		/**
		 * hide_wc_settings_tabs.
		 *
		 * @version 1.0.1
		 * @since   1.0.1
		 *
		 * @return void
		 */
		function hide_wc_settings_tabs() {
			global $plugin_page;
			if (
				'wc-settings' === $plugin_page &&
				isset( $_GET['tab'] ) &&
				! empty( $found_items = wp_list_filter( $this->args, array( 'wc_settings_tab_id' => $_GET['tab'] ) ) )
			) {
				$tab_ids          = array_column( $this->args, 'wc_settings_tab_id' );
				$css_selector_arr = array();
				foreach ( $tab_ids as $tab ) {
					$css_selector_arr[] = '.wrap.woocommerce .nav-tab-wrapper a[href*="tab=' . $tab . '"]';
				}
				?>
				<style>
					.wrap.woocommerce .nav-tab-wrapper a{
						display:none;
					}
					<?php echo implode(', ', $css_selector_arr)?>
					{
						display: block
					}

					#wpbody {
						margin-top: 0
					}

					.woocommerce-layout {
						display: none
					}
				</style>
				<?php
			}
		}
	}
}