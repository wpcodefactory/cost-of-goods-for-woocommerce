<?php
/**
 * Cost of Goods for WooCommerce - Bulk Edit Tool Class
 *
 * @version 2.3.4
 * @since   1.2.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Bulk_Edit_Tool' ) ) :

	class Alg_WC_Cost_of_Goods_Bulk_Edit_Tool {

		/**
		 * Tool page's slug.
		 *
		 * @var string
		 */
		private $page_slug = 'bulk-edit-costs';

		/**
		 * @var Alg_WC_Cost_of_Goods_Update_Cost_Bkg_Process
		 */
		public $update_cost_bkg_process;

		/**
		 * Constructor.
		 *
		 * @version 2.5.1
		 * @since   1.2.0
		 */
		function __construct() {
			add_action( 'admin_init', array( $this, 'save_costs' ) );
			add_filter( 'woocommerce_screen_ids', array( $this, 'add_tool_to_wc_screen_ids' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) );
			add_action( 'admin_menu', array( $this, 'create_wp_list_tool' ) );
			add_action( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3 );
			add_action( 'wp_ajax_alg_wc_cog_update_cost', array( $this, 'ajax_update_costs' ) );
			// Bkg Process
			add_action( 'plugins_loaded', array( $this, 'init_bkg_process' ) );
		}

		/**
		 * init_bkg_process.
		 *
		 * @version 2.5.1
		 * @since   2.5.1
		 */
		function init_bkg_process() {
			require_once( alg_wc_cog()->plugin_path() . '/includes/background-process/class-alg-wc-cog-update-cost-bkg-process.php' );
			$this->update_cost_bkg_process = new Alg_WC_Cost_of_Goods_Update_Cost_Bkg_Process();
		}

		/**
		 * Update costs on Ajax for bulk edit tools.
		 *
		 * @version 2.5.1
		 * @since   2.5.1
		 */
		function ajax_update_costs() {
			$posted_data = wp_unslash( $_POST );
			$_form_data  = isset( $posted_data['form_data'] ) ? $posted_data['form_data'] : '';
			$update_type = isset( $posted_data['update_type'] ) ? $posted_data['update_type'] : '';
			parse_str( $_form_data, $form_data );
			// Verify nonce
			if ( isset( $form_data["_nonce_{$update_type}_val"] ) && ! wp_verify_nonce( $form_data["_nonce_{$update_type}_val"], "_nonce_{$update_type}_action" ) ) {
				wp_send_json_error( esc_html__( 'Something went wrong! Please try again.', 'cost-of-goods-for-woocommerce' ) );
			}
			$percentage       = isset( $form_data['percentage'] ) ? sanitize_text_field( $form_data['percentage'] ) : '';
			$product_category = isset( $form_data['product_category'] ) ? $form_data['product_category'] : '';
			$product_category = is_array( $product_category ) ? $product_category : array();
			$product_category = array_map( 'esc_attr', $product_category );
			// If empty percentage do not proceed
			if ( empty( $percentage ) || empty( $update_type ) ) {
				wp_send_json_error( esc_html__( 'Invalid percentage or category.', 'cost-of-goods-for-woocommerce' ) );
			}
			$query_args = array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => '-1',
				'fields'         => 'ids',
			);
			if ( ! empty( $product_category ) && is_array( $product_category ) ) {
				$query_args['tax_query'] = array(
					array(
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => $product_category,
						'operator' => 'IN',
					)
				);
			}
			$posts                  = get_posts( $query_args );
			$bkg_process_min_amount = get_option( 'alg_wc_cog_bkg_process_min_amount', 100 );
			$perform_bkg_process    = count( $posts ) >= $bkg_process_min_amount;
			$message                = '';
			if ( $perform_bkg_process ) {
				$message = __( 'Costs are being updated via background processing.', 'cost-of-goods-for-woocommerce' );
				$message .= 'yes' === get_option( 'alg_wc_cog_bkg_process_send_email', 'yes' ) ? ' '.sprintf( __( 'An email is going to be sent to %s when the task is completed.', 'cost-of-goods-for-woocommerce' ), get_option( 'alg_wc_cog_bkg_process_email_to', get_option( 'admin_email' ) ) ) : '';
				$this->update_cost_bkg_process->cancel_process();
				foreach ( $posts as $product_id ) {
					$this->update_cost_bkg_process->push_to_queue(
						array(
							'product_id'  => $product_id,
							'percentage'  => $percentage,
							'update_type' => $update_type,
						)
					);
				}
				$this->update_cost_bkg_process->save()->dispatch();
			} else {
				$message = __( 'Successfully changed product costs.', 'cost-of-goods-for-woocommerce' );
				foreach ( $posts as $product_id ) {
					alg_wc_cog()->core->products->update_product_cost_by_percentage( array(
						'product_id'        => $product_id,
						'percentage'        => $percentage,
						'update_type'       => $update_type,
						'update_variations' => true
					) );
				}
			}
			wp_send_json_success( esc_html( $message ) );
		}

		/**
		 * Display content for Profit Section.
		 *
		 * @version 2.5.1
		 * @since   2.5.1
		 */
		function display_section_profit() {
			$disabled     = apply_filters( 'alg_wc_cog_settings', 'disabled' );
			$blocked_text = apply_filters( 'alg_wc_cog_settings', alg_wc_cog_get_blocked_options_message() );
			?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="profit-percentage"><?php esc_html_e( 'Profit percentage (%)', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <input id="price-percentage" name="percentage" type="number" step="0.01" required placeholder="">
                        <p class="description">
	                        <?php esc_html_e( 'Products costs will be set according to the profit percentage you\'d like to achieve.', 'cost-of-goods-for-woocommerce' ); ?><br />
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="product-category"><?php esc_html_e( 'Filter by category', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <select <?php echo esc_attr( $disabled ); ?> class="wc-category-search" multiple="multiple" style="width: 50%;" id="product-category" name="product_category[]" data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'cost-of-goods-for-woocommerce' ); ?>" data-action="json_search_categories">
							<?php foreach ( get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) ) as $product_cat ) : ?>
                                <option value="<?php echo esc_attr( $product_cat->term_id ); ?>"><?php echo esc_html( $product_cat->name ); ?></option>
							<?php endforeach; ?>
                        </select>
	                    <p class="description">
		                    <?php esc_html_e( 'Select only the categories you want to edit. Leave it empty to update all products.', 'cost-of-goods-for-woocommerce' ); ?>
		                    <?php echo ( ! empty( $blocked_text ) ) ? '<br />' . $blocked_text : ''; ?>
	                    </p>
                    </td>
                </tr>
            </table>
			<?php
		}

		/**
		 * Display content for Price Section.
		 *
		 * @version 2.5.1
		 * @since   2.5.1
		 */
		function display_section_price() {
			$disabled     = apply_filters( 'alg_wc_cog_settings', 'disabled' );
			$blocked_text = apply_filters( 'alg_wc_cog_settings', alg_wc_cog_get_blocked_options_message() );
			?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="price-percentage"><?php esc_html_e( 'Price percentage (%)', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <input id="price-percentage" name="percentage" step="0.01" type="number" required placeholder="">
                        <p class="description">
	                        <?php esc_html_e( 'Product costs will be defined from a percentage of product prices.', 'cost-of-goods-for-woocommerce' ); ?><br />
	                        <?php esc_html_e( 'Most probably you should use a number between 0 and 100.', 'cost-of-goods-for-woocommerce' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="product-category"><?php esc_html_e( 'Filter by category', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <select <?php echo esc_attr( $disabled ); ?> class="wc-category-search" multiple="multiple" style="width: 50%;" id="product-category" name="product_category[]" data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'cost-of-goods-for-woocommerce' ); ?>" data-action="json_search_categories">
							<?php foreach ( get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) ) as $product_cat ) : ?>
                                <option value="<?php echo esc_attr( $product_cat->term_id ); ?>"><?php echo esc_html( $product_cat->name ); ?></option>
							<?php endforeach; ?>
                        </select>
                        <p class="description">
	                        <?php esc_html_e( 'Select only the categories you want to edit. Leave it empty to update all products.', 'cost-of-goods-for-woocommerce' ); ?>
	                        <?php echo ( ! empty( $blocked_text ) ) ? '<br />' . $blocked_text : ''; ?>
                        </p>
                    </td>
                </tr>
            </table>
			<?php
		}

		/**
		 * Display content for Manually Section.
		 *
		 * @version 2.5.1
		 * @since   2.5.1
		 */
		function display_section_manually() {
			$this->wp_list_bulk_edit_tool->prepare_items();
			$this->wp_list_bulk_edit_tool->search_box( __( 'Search', 'cost-of-goods-for-woocommerce' ), 'alg_wc_cog_search' );
			$this->wp_list_bulk_edit_tool->display();
		}

		/**
		 * Display section navs HTML.
		 *
		 * @version 2.5.1
		 * @since   2.5.1
		 */
		function display_section_navs_html() {

			$tabs_nav_html = array();
			$nav_sections  = $this->get_section_nav_items();
			$section       = isset( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : key( $nav_sections );

			foreach ( $nav_sections as $key => $tab ) {

				$label       = isset( $tab['label'] ) ? $tab['label'] : '';
				$is_current  = $section === $key ? 'current' : '';
				$section_url = admin_url( sprintf( 'tools.php?page=%s&section=%s', $this->page_slug, $key ) );

				$tabs_nav_html[] = sprintf( '<li><a href="%s" class="%s">%s</a></li>', $section_url, $is_current, $label );
			}

			printf( '<ul class="subsubsub no-float">%s</ul>', implode( ' | ', $tabs_nav_html ) );
		}

		/**
		 * display_wp_list_tool.
		 *
		 * @version 2.5.1
		 * @since   2.3.1
		 */
		function display_bulk_edit_tools() {

			ob_start();

			// Section heading title
			printf( '<h1 class="wp-heading-inline">%s - %s %s</h1>',
				esc_html__( 'Bulk Edit Costs', 'cost-of-goods-for-woocommerce' ),
				$this->get_current_section( 'label' ),
				$this->get_current_section( 'save_btn_top' )
			);

			// Section navigations
			$this->display_section_navs_html();

			// Section description
			if ( ! empty( $section_desc = $this->get_current_section( 'desc' ) ) ) {
				printf( '<p>%s</p>', $section_desc );
			}

			$section_method = 'display_section_' . $this->get_current_section( 'id' );

			if ( method_exists( $this, $section_method ) && is_callable( array( $this, $section_method ) ) ) {
				call_user_func( array( $this, $section_method ) );
			}

			// Bottom save button
			if ( ! empty( $save_btn_bottom = $this->get_current_section( 'save_btn_bottom' ) ) ) {
				printf( '<div class="form-action">%s%s<span class="spinner"></span></div>',
					wp_nonce_field( "_nonce_{$this->get_current_section( 'id' )}_action", "_nonce_{$this->get_current_section( 'id' )}_val" ),
					$save_btn_bottom
				);
			}

			// Wrap up section content
			printf( '<div class="notice is-dismissible alg_wc_cog_notice"><p></p></div><form method="post" action="" class="bulk-edit-form %s" data-type="%s"><div class="wrap alg_wc_cog_bulk_edit">%s</div></form>',
				$this->get_current_section( 'form_class' ),
				$this->get_current_section( 'id' ),
				ob_get_clean()
			);
		}

		/**
		 * Return current section or any argument value of current section.
		 *
		 * @version 2.5.1
		 * @since 2.5.1
		 *
		 * @param string $arg
		 * @return int|mixed|string|null
		 */
		function get_current_section( $arg = '' ) {

			$nav_sections = $this->get_section_nav_items();
			$section      = isset( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : key( $nav_sections );

			if ( $arg === 'id' ) {
				return $section;
			}

			if ( ! empty( $arg ) ) {
				return isset( $nav_sections[ $section ][ $arg ] ) ? $nav_sections[ $section ][ $arg ] : '';
			}

			return $nav_sections[ $section ];
		}

		/**
		 * Return navigation items.
		 *
		 * @version 2.5.1
		 * @since   2.5.1
		 *
		 * @return mixed|void
		 */
		function get_section_nav_items() {

			$bulk_edit_sections = array(
				'manually' => array(
					'label'           => esc_html__( 'Manually', 'cost-of-goods-for-woocommerce' ),
					'save_btn_top'    => sprintf( '<input style="position:relative;top:-2px;margin:0 0 0 10px" type="submit" name="alg_wc_cog_bulk_edit_tool_save_costs" class="page-title-action" value="%s">',
						esc_html__( 'Save', 'cost-of-goods-for-woocommerce' )
					),
					'save_btn_bottom' => sprintf( '<input type="submit" name="alg_wc_cog_bulk_edit_tool_save_costs" class="button-primary" value="%s">',
						esc_html__( 'Save', 'cost-of-goods-for-woocommerce' )
					),
					'desc'            => sprintf( __( 'Bulk edit products costs/prices/stock. Tools options can be set in "<strong>Cost of Goods for WooCommerce</strong>" <a href="%s">plugin settings</a>', 'cost-of-goods-for-woocommerce' ),
						admin_url( 'admin.php?page=wc-settings&tab=alg_wc_cost_of_goods&section=tools' )
					),
				),
				'price'    => array(
					'label'           => esc_html__( 'By Price', 'cost-of-goods-for-woocommerce' ),
					'desc'            => esc_html__( 'Here you can set the product costs from the product prices.', 'cost-of-goods-for-woocommerce' ) . '<br />' .
					                     esc_html__( 'Variations costs will also be updated accordingly.', 'cost-of-goods-for-woocommerce' ),
					'save_btn_bottom' => sprintf( '<input type="submit" class="button-primary" value="%s">', esc_html__( 'Update Costs', 'cost-of-goods-for-woocommerce' ) ),
					'form_class'      => 'ajax-submission',
				),
				'profit'   => array(
					'label'           => esc_html__( 'By Profit', 'cost-of-goods-for-woocommerce' ),
					'desc'            => esc_html__( 'Here you can set the product costs according to the profit you want to achieve.', 'cost-of-goods-for-woocommerce' ) . '<br />' .
					                     esc_html__( 'Variations costs will also be updated accordingly.', 'cost-of-goods-for-woocommerce' ),
					'save_btn_bottom' => sprintf( '<input type="submit" class="button-primary" value="%s">', esc_html__( 'Update Costs', 'cost-of-goods-for-woocommerce' ) ),
					'form_class'      => 'ajax-submission',
				),
			);

			return apply_filters( 'alg_wc_cog_filters_bulk_edit_sections_nav', $bulk_edit_sections );
		}

		/**
		 * set_screen_option.
		 *
		 * @version 2.3.1
		 * @since   2.3.1
		 *
		 * @param $status
		 * @param $option
		 * @param $value
		 *
		 * @return mixed
		 *
		 */
		function set_screen_option( $status, $option, $value ) {
			if ( 'alg_wc_cog_bulk_edit_per_page' === $option ) {
				return $value;
			}

			return $status;
		}

		/**
		 * screen_option.
		 *
		 * @version 2.3.1
		 * @since   2.3.1
		 */
		function screen_option() {
			$option = 'per_page';
			$args   = [
				'label'   => __( 'Items per page', 'cost-of-goods-for-woocommerce' ),
				'default' => 20,
				'option'  => 'alg_wc_cog_bulk_edit_per_page'
			];
			add_screen_option( $option, $args );
			require_once( 'class-alg-wc-cog-wplist-bulk-edit-tool.php' );
			$this->wp_list_bulk_edit_tool = new Alg_WC_Cost_of_Goods_WP_List_Bulk_Edit_Tool();
		}

		/**
		 * create_wp_list_tool.
		 *
		 * @version 2.3.4
		 * @since   2.3.1
		 */
		function create_wp_list_tool() {
			if ( ! apply_filters( 'alg_wc_cog_create_edit_costs_tool_validation', true ) ) {
				return;
			}
			$hook = add_submenu_page(
				'tools.php',
				__( 'Bulk Edit Costs', 'cost-of-goods-for-woocommerce' ),
				__( 'Bulk Edit Costs', 'cost-of-goods-for-woocommerce' ),
				'manage_woocommerce',
				$this->page_slug,
				array( $this, 'display_bulk_edit_tools' )
			);
			add_action( "load-{$hook}", array( $this, 'screen_option' ) );
		}

		/**
		 * enqueue_scripts_and_styles.
		 *
		 * @version 2.5.0
		 * @since   1.3.3
		 */
		function enqueue_scripts_and_styles( $hook ) {
			if ( 'tools_page_bulk-edit-costs' != $hook ) {
				return;
			}
			$min_suffix = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ? '' : '.min' );
			wp_enqueue_style( 'alg-wc-cog-bulk-edit-tool-style',
				alg_wc_cog()->plugin_url() . '/includes/css/alg-wc-cog-bulk-edit-tool' . $min_suffix . '.css',
				array(),
				alg_wc_cog()->version
			);
			wp_enqueue_script( 'alg-wc-cog-bulk-edit-tool', alg_wc_cog()->plugin_url() . '/includes/js/alg-wc-cog-bulk-edit-tool' . $min_suffix . '.js', array( 'jquery' ), alg_wc_cog()->version, true );
			wp_localize_script( 'alg-wc-cog-bulk-edit-tool', 'algWcCog',
				array(
					'ajaxURL'     => admin_url( 'admin-ajax.php' ),
					'confirmText' => esc_html__( 'Are you really want to update?', 'cost-of-goods-for-woocommerce' )
				)
			);
		}

		/**
		 * add_tool_to_wc_screen_ids.
		 *
		 * for `wc_input_price` class.
		 *
		 * @version 2.3.1
		 * @since   1.2.0
		 */
		function add_tool_to_wc_screen_ids( $screen_ids ) {
			$screen_ids[] = 'tools_page_bulk-edit-costs';

			return $screen_ids;
		}

		/**
		 * save_costs.
		 *
		 * @version 2.2.0
		 * @since   1.2.0
		 * @see     https://wordpress.org/support/topic/you-should-add-posibility-to-edit-regular-price-and-sale-price/
		 * @todo    [next] prices: `$do_update_func`
		 * @todo    [maybe] nonce etc.
		 * @todo    [maybe] output some error on ` ! ( $product = wc_get_product( $product_id ) )`?
		 */
		function save_costs() {
			$do_edit_prices = ( 'yes' === get_option( 'alg_wc_cog_bulk_edit_tool_edit_prices', 'no' ) );
			if ( $do_edit_prices ) {
				$error_sale_price_ids = array();
			}
			if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_save_costs'] ) ) {
				// Costs
				if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_costs'] ) && is_array( $_POST['alg_wc_cog_bulk_edit_tool_costs'] ) ) {
					foreach ( $_POST['alg_wc_cog_bulk_edit_tool_costs'] as $product_id => $cost_value ) {
						update_post_meta( sanitize_key( $product_id ), '_alg_wc_cog_cost', sanitize_text_field( $cost_value ) );
					}
				}
				// Prices
				if ( $do_edit_prices ) {
					if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_regular_price'] ) && is_array( $_POST['alg_wc_cog_bulk_edit_tool_regular_price'] ) ) {
						$regular_prices = wc_clean( $_POST['alg_wc_cog_bulk_edit_tool_regular_price'] );
						foreach ( $regular_prices as $product_id => $regular_price_value ) {
							if ( $product = wc_get_product( $product_id ) ) {
								$product->set_regular_price( $regular_price_value );
								$product->save();
							}
						}
					}
					if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_sale_price'] ) && is_array( $_POST['alg_wc_cog_bulk_edit_tool_sale_price'] ) ) {
						$sale_prices = wc_clean( $_POST['alg_wc_cog_bulk_edit_tool_sale_price'] );
						foreach ( $sale_prices as $product_id => $sale_price_value ) {
							if ( $product = wc_get_product( $product_id ) ) {
								if ( $sale_price_value <= $product->get_regular_price() ) {
									$product->set_sale_price( $sale_price_value );
									$product->save();
								} else {
									array_push( $error_sale_price_ids, $product_id );
								}
							}
						}
					}
				}
				// Stock
				if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_stock'] ) && is_array( $_POST['alg_wc_cog_bulk_edit_tool_stock'] ) ) {
					$do_update_func = ( 'func' === get_option( 'alg_wc_cog_bulk_edit_tool_manage_stock_method', 'meta' ) );
					foreach ( $_POST['alg_wc_cog_bulk_edit_tool_stock'] as $product_id => $stock_value ) {
						if ( $do_update_func && ( $product = wc_get_product( $product_id ) ) ) {
							$product->set_stock_quantity( sanitize_text_field( $stock_value ) );
							$product->save();
						} else {
							update_post_meta( sanitize_key( $product_id ), '_stock', sanitize_text_field( $stock_value ) );
						}
					}
				}
				// Notices
				add_action( 'admin_notices', array( $this, 'admin_notice_costs_saved' ) );
				if ( $do_edit_prices && count( $error_sale_price_ids ) ) {
					$this->error_sale_price_ids = $error_sale_price_ids;
					add_action( 'admin_notices', array( $this, 'admin_notice_sale_price_higher' ) );
				}
			}
		}

		/**
		 * admin_notice_costs_saved.
		 *
		 * @version 1.2.0
		 * @since   1.2.0
		 */
		function admin_notice_costs_saved() {
			echo '<div class="notice notice-success is-dismissible"><p><strong>' . __( 'Costs have been saved.', 'cost-of-goods-for-woocommerce' ) . '</strong></p></div>';
		}

		/**
		 * get_the_title.
		 *
		 * @version 1.4.0
		 * @since   1.4.0
		 */
		function get_the_title( $post_id ) {
			return get_the_title( $post_id ) . ' (#' . $post_id . ')';
		}

		/**
		 * admin_notice_sale_price_higher.
		 *
		 * @version 1.4.0
		 * @since   1.4.0
		 */
		function admin_notice_sale_price_higher() {
			echo '<div class="notice notice-error is-dismissible"><p><strong>' . sprintf( __( 'Sale price is higher than regular price: %s.', 'cost-of-goods-for-woocommerce' ),
					implode( ', ', array_map( array( $this, 'get_the_title' ), $this->error_sale_price_ids ) ) ) . '</strong></p></div>';
		}
	}

endif;

return new Alg_WC_Cost_of_Goods_Bulk_Edit_Tool();


