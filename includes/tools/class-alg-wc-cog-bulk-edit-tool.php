<?php
/**
 * Cost of Goods for WooCommerce - Bulk Edit Tool Class.
 *
 * @version 2.9.5
 * @since   1.2.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Bulk_Edit_Tool' ) ) :

	class Alg_WC_Cost_of_Goods_Bulk_Edit_Tool {

		/**
		 * Tool costs page's slug.
		 *
		 * @var string
		 */
		private $page_slug_costs = 'bulk-edit-costs';

        /**
		 * Tool prices page's slug.
         *
         * @since 2.9.5
		 *
		 * @var string
		 */
		private $page_slug_prices = 'bulk-edit-prices';

		/**
		 * Update costs bkg process.
		 *
		 * @since 2.9.5
         *
		 * @var Alg_WC_Cost_of_Goods_Update_Cost_Bkg_Process
		 */
		public $update_cost_bkg_process;

        /**
         * Update prices bkg process.
         *
         * @since 2.9.5
         *
		 * @var Alg_WC_Cost_of_Goods_Update_Price_Bkg_Process
		 */
		public $update_price_bkg_process;

		/**
         * Update variation costs bkg process.
         *
         * @since 2.9.5
         *
		 * @var Alg_WC_Cost_of_Goods_Update_Variation_Costs_Bkg_Process
		 */
		public $update_variation_costs_bkg_process;

		/**
		 * Constructor.
		 *
		 * @version 2.8.1
		 * @since   1.2.0
		 */
		function __construct() {
			add_action( 'admin_init', array( $this, 'save_costs' ) );
			add_filter( 'woocommerce_screen_ids', array( $this, 'add_tool_to_wc_screen_ids' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) );
			add_action( 'admin_menu', array( $this, 'create_wp_list_tool' ) );
			add_action( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3 );
			add_action( 'wp_ajax_alg_wc_cog_update_product_data', array( $this, 'ajax_update_product_data' ) );
			// Bkg Process
			$this->init_bkg_process();
			// Remove query args.
			add_action( 'admin_init', array( $this, 'remove_query_args' ) );
			// Json search tags.
			add_action( 'wp_ajax_' . 'alg_wc_cog_json_search_tags', array( $this, 'json_search_tags' ) );
		}

		/**
		 * json_search_tags.
		 *
		 * @see WC_AJAX::json_search_categories()
		 *
		 * @version 2.7.3
		 * @since   2.7.3
		 */
		function json_search_tags() {
			ob_start();
			check_ajax_referer( 'search-categories', 'security' );
			if ( ! current_user_can( 'edit_products' ) ) {
				wp_die( - 1 );
			}
			$search_text = isset( $_GET['term'] ) ? wc_clean( wp_unslash( $_GET['term'] ) ) : '';
			if ( ! $search_text ) {
				wp_die();
			}
			$found_categories = array();
			$args             = array(
				'taxonomy'   => array( 'product_tag' ),
				'orderby'    => 'id',
				'order'      => 'ASC',
				'hide_empty' => true,
				'fields'     => 'all',
				'name__like' => $search_text,
			);
			$terms            = get_terms( $args );
			if ( $terms ) {
				foreach ( $terms as $term ) {
					$term->formatted_name               = '';
					$term->formatted_name               .= $term->name . ' (' . $term->count . ')';
					$found_categories[ $term->term_id ] = $term;
				}
			}
			wp_send_json( apply_filters( 'alg_wc_cog_json_search_found_tags', $found_categories ) );
		}

		/**
		 * remove_query_args.
		 *
		 * @version 2.7.1
		 * @since   2.7.1
		 *
		 */
		function remove_query_args() {
			if (
				isset( $_GET['page'] ) &&
				'bulk-edit-costs' === $_GET['page'] &&
				! empty( $_GET['_wp_http_referer'] )
			) {
				wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
			}
		}

		/**
		 * init_bkg_process.
		 *
		 * @version 2.9.5
		 * @since   2.5.1
		 */
		function init_bkg_process() {
			require_once( alg_wc_cog()->plugin_path() . '/includes/background-process/class-alg-wc-cog-update-cost-bkg-process.php' );
			require_once( alg_wc_cog()->plugin_path() . '/includes/background-process/class-alg-wc-cog-update-price-bkg-process.php' );
			require_once( alg_wc_cog()->plugin_path() . '/includes/background-process/class-alg-wc-cog-update-variation-costs-bkg-process.php' );
			$this->update_cost_bkg_process            = new Alg_WC_Cost_of_Goods_Update_Cost_Bkg_Process();
			$this->update_price_bkg_process           = new Alg_WC_Cost_of_Goods_Update_Price_Bkg_Process();
			$this->update_variation_costs_bkg_process = new Alg_WC_Cost_of_Goods_Update_Variation_Costs_Bkg_Process();
		}

		/**
		 * Update costs on Ajax for bulk edit tools.
		 *
		 * @version 2.9.5
		 * @since   2.5.1
		 */
		function ajax_update_product_data() {
			$posted_data = wp_unslash( $_POST );
			$_form_data  = isset( $posted_data['form_data'] ) ? $posted_data['form_data'] : '';
			$update_type = isset( $posted_data['update_type'] ) ? $posted_data['update_type'] : '';
			$tool_type   = isset( $posted_data['tool_type'] ) ? $posted_data['tool_type'] : '';
			parse_str( $_form_data, $form_data );
			// Verify nonce
			if ( isset( $form_data["_nonce_{$update_type}_val"] ) && ! wp_verify_nonce( $form_data["_nonce_{$update_type}_val"], "_nonce_{$update_type}_action" ) ) {
				wp_send_json_error( esc_html__( 'Something went wrong! Please try again.', 'cost-of-goods-for-woocommerce' ) );
			}
			$percentage             = isset( $form_data['percentage'] ) ? (float) sanitize_text_field( $form_data['percentage'] ) : '';
			$absolute_profit        = isset( $form_data['absolute_profit'] ) ? (float) sanitize_text_field( $form_data['absolute_profit'] ) : '';
			$affected_field         = isset( $form_data['affected_field'] ) ? $form_data['affected_field'] : 'regular_price';
			$rounding               = isset( $form_data['rounding'] ) ? $form_data['rounding'] : '';
			$costs_filter           = sanitize_text_field( isset( $form_data['costs_filter'] ) ? $form_data['costs_filter'] : 'ignore_costs' );
			$update_variation_costs = filter_var( isset( $form_data['update_variation_costs'] ) ? $form_data['update_variation_costs'] : false, FILTER_VALIDATE_BOOLEAN );
			$empty_variation_costs_required = filter_var( isset( $form_data['empty_variation_costs_required'] ) ? $form_data['empty_variation_costs_required'] : false, FILTER_VALIDATE_BOOLEAN );
			// Requirements.
			if ( empty( $update_type ) ) {
				wp_send_json_error( esc_html__( 'Some error has occurred. Please, try again.', 'cost-of-goods-for-woocommerce' ) );
			}
			if ( 'update_costs' === $tool_type ) {
				if ( isset( $form_data['percentage'] ) && empty( $percentage ) ) {
					wp_send_json_error( esc_html__( 'Invalid percentage.', 'cost-of-goods-for-woocommerce' ) );
				}
			} else {
				if (
					( empty( $percentage ) && empty( $absolute_profit ) ) ||
					( ! empty( $percentage ) && ! empty( $absolute_profit ) )
				) {
					wp_send_json_error( esc_html__( 'It\'s necessary to set the profit percentage or the absolute profit.', 'cost-of-goods-for-woocommerce' ) );
				}
			}
			$query_args = array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => '-1',
				'fields'         => 'ids',
				'tax_query'      => array()
			);
			if ( $update_variation_costs ) {
				$query_args['post_type'] = 'product_variation';
				if ( $empty_variation_costs_required ) {
					$query_args['meta_query'] = array(
						'relation' => 'OR',
						array(
							'key'     => '_alg_wc_cog_cost',
							'value'   => 0,
							'compare' => '==',
						),
						array(
							'key'     => '_alg_wc_cog_cost',
							'value'   => '',
							'compare' => '==',
						),
						array(
							'key'     => '_alg_wc_cog_cost',
							'compare' => 'NOT EXISTS',
						),
					);
				}
			}
			// Product Category.
			$product_category = isset( $form_data['product_category'] ) ? $form_data['product_category'] : '';
			$product_category = is_array( $product_category ) ? $product_category : array();
			$product_category = array_map( 'esc_attr', $product_category );
			if ( ! empty( $product_category ) && is_array( $product_category ) ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => $product_category,
					'operator' => 'IN',
				);
			}
			// Product tag.
			$product_tag = isset( $form_data['product_tag'] ) ? $form_data['product_tag'] : '';
			$product_tag = is_array( $product_tag ) ? $product_tag : array();
			$product_tag = array_map( 'esc_attr', $product_tag );
			if ( ! empty( $product_tag ) && is_array( $product_tag ) ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => 'product_tag',
					'field'    => 'term_id',
					'terms'    => $product_tag,
					'operator' => 'IN',
				);
			}
            $posts                  = get_posts( $query_args );
			$bkg_process_min_amount = get_option( 'alg_wc_cog_bkg_process_min_amount', 100 );
			$args                   = array(
				'products'    => $posts,
				'bkg_process' => count( $posts ) >= $bkg_process_min_amount,
				'options'     => array(
					'costs_filter'    => $costs_filter,
					'percentage'      => $percentage,
					'absolute_profit' => $absolute_profit,
					'update_type'     => $update_type,
					'affected_field'  => $affected_field,
					'rounding'        => $rounding,
				),
			);
            // For bulk update - costs
            if( 'update_costs' === $tool_type ) {
	            if ( ! $update_variation_costs ) {
		            $bkg_process_progress_msg = __( 'Product costs are being updated via background processing.', 'cost-of-goods-for-woocommerce' );
		            $bkg_process_progress_msg .= 'yes' === get_option( 'alg_wc_cog_bkg_process_send_email', 'yes' ) ? ' ' . sprintf( __( 'An email is going to be sent to %s when the task is completed.', 'cost-of-goods-for-woocommerce' ), get_option( 'alg_wc_cog_bkg_process_email_to', get_option( 'admin_email' ) ) ) : '';
		            wp_send_json_success( $this->bulk_update_products( $args, array(
			            'bkg_process_obj'          => $this->update_cost_bkg_process,
			            'success_msg'              => __( 'Successfully updated product costs.', 'cost-of-goods-for-woocommerce' ),
			            'bkg_process_progress_msg' => $bkg_process_progress_msg,
			            'no_bkg_process_function'  => 'update_product_cost_by_percentage',
		            ) ) );
	            } else {
		            $bkg_process_progress_msg = __( 'Variation costs are being updated via background processing.', 'cost-of-goods-for-woocommerce' );
		            $bkg_process_progress_msg .= 'yes' === get_option( 'alg_wc_cog_bkg_process_send_email', 'yes' ) ? ' ' . sprintf( __( 'An email is going to be sent to %s when the task is completed.', 'cost-of-goods-for-woocommerce' ), get_option( 'alg_wc_cog_bkg_process_email_to', get_option( 'admin_email' ) ) ) : '';
		            wp_send_json_success( $this->bulk_update_products( $args, array(
			            'bkg_process_obj'          => $this->update_variation_costs_bkg_process,
			            'success_msg'              => __( 'Successfully updated variation costs.', 'cost-of-goods-for-woocommerce' ),
			            'bkg_process_progress_msg' => $bkg_process_progress_msg,
			            'no_bkg_process_function'  => 'update_variation_cost_from_parent',
		            ) ) );
	            }
            }
            // For bulk update - prices
			if( 'update_prices' === $tool_type ) {
				$bkg_process_progress_msg = __( 'Product prices are being updated via background processing.', 'cost-of-goods-for-woocommerce' );
				$bkg_process_progress_msg .= 'yes' === get_option( 'alg_wc_cog_bkg_process_send_email', 'yes' ) ? ' ' . sprintf( __( 'An email is going to be sent to %s when the task is completed.', 'cost-of-goods-for-woocommerce' ), get_option( 'alg_wc_cog_bkg_process_email_to', get_option( 'admin_email' ) ) ) : '';
				wp_send_json_success( $this->bulk_update_products( $args, array(
					'bkg_process_obj'          => $this->update_price_bkg_process,
					'success_msg'              => __( 'Successfully updated product prices.', 'cost-of-goods-for-woocommerce' ),
					'bkg_process_progress_msg' => $bkg_process_progress_msg,
					'no_bkg_process_function'  => 'update_product_cost_by_percentage',
				) ) );
			}
		}

		/**
         * bulk_update_products.
         *
         * @version 2.9.5
         * @since   2.9.5
         *
		 * @param $update_args
		 * @param $function_args
		 *
		 * @return string
		 */
		function bulk_update_products( $update_args = array(), $function_args = array() ) {
			$update_args = wp_parse_args( $update_args, array(
				'products'    => array(),
				'bkg_process' => false,
				'options'     => array(),
			) );
			$products    = $update_args['products'];
			$bkg_process = $update_args['bkg_process'];
			$options     = $update_args['options'];
			$function_args = wp_parse_args( $function_args, array(
				'bkg_process_obj'          => null,
				'success_msg'              => __( 'Successfully updated product prices.', 'cost-of-goods-for-woocommerce' ),
				'bkg_process_progress_msg' => __( 'Product prices are being updated via background processing.', 'cost-of-goods-for-woocommerce' ),
				'no_bkg_process_function'  => 'update_product_price_by_profit',
				'no_bkg_process_obj'       => alg_wc_cog()->core->products
			) );
			$message = $function_args['success_msg'];
            $bkg_process_obj = $function_args['bkg_process_obj'];
            $no_bkg_process_function = $function_args['no_bkg_process_function'];
			$no_bkg_process_obj = $function_args['no_bkg_process_obj'];
			if ( $bkg_process ) {
				$message = $function_args['bkg_process_progress_msg'];
				call_user_func( array( $bkg_process_obj, "cancel_process" ) );
			}
			foreach ( $products as $product_id ) {
				$_options = wp_parse_args( $options, array( 'product_id' => $product_id ) );
				if ( $bkg_process ) {
					call_user_func_array( array( $bkg_process_obj, "push_to_queue" ), array( $_options ) );
				} else {
					call_user_func_array( array( $no_bkg_process_obj, $no_bkg_process_function ), array( $_options ) );
				}
			}
			if ( $bkg_process ) {
				call_user_func( array( $bkg_process_obj, "save" ) );
				call_user_func( array( $bkg_process_obj, "dispatch" ) );
			}
			if ( empty( $products ) ) {
				$message = __( 'It is not necessary to update any products.', 'cost-of-goods-for-woocommerce' );
            }
			return esc_html( $message );
		}

		/**
		 * Display content for Manually Section.
		 *
		 * @version 2.8.7
		 * @since   2.6.1
		 */
		function display_bulk_edit_prices_profit() {
			$disabled     = apply_filters( 'alg_wc_cog_settings', 'disabled' );
			$blocked_text = apply_filters( 'alg_wc_cog_settings', alg_wc_cog_get_blocked_options_message() );
			?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="affected-field"><?php esc_html_e( 'Affected field', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <select id="affected-field" name="affected_field">
                            <option value="regular_price"><?php esc_html_e( 'Regular Price', 'cost-of-goods-for-woocommerce' ); ?></option>
                            <option value="sale_price"><?php esc_html_e( 'Sale Price', 'cost-of-goods-for-woocommerce' ); ?></option>
                        </select>
                        <p class="description">
							<?php esc_html_e( 'The selected field will be updated on all the products.', 'cost-of-goods-for-woocommerce' ); ?>
                        </p>
                    </td>
                </tr>
	            <tr>
		            <th scope="row"><label for="cost_rounding"><?php esc_html_e( 'Rounding', 'cost-of-goods-for-woocommerce' ); ?></label></th>
		            <td>
			            <select id="rounding" name="rounding">
				            <option value=""><?php esc_html_e( 'Do not round', 'cost-of-goods-for-woocommerce' ); ?></option>
				            <option value="round"><?php esc_html_e( 'Round', 'cost-of-goods-for-woocommerce' ); ?></option>
				            <option value="round_up"><?php esc_html_e( 'Round up', 'cost-of-goods-for-woocommerce' ); ?></option>
				            <option value="round_down"><?php esc_html_e( 'Round down', 'cost-of-goods-for-woocommerce' ); ?></option>
			            </select>
		            </td>
	            </tr>
                <tr>
		            <th scope="row"><label for="profit-percentage"><?php esc_html_e( 'Profit percentage (%)', 'cost-of-goods-for-woocommerce' ); ?></label></th>
		            <td>
			            <input id="profit-percentage" name="percentage" step="0.01" min="0" type="number" placeholder="">
			            <p class="description">
				            <?php echo wp_kses_post( __( 'Products prices will be set according to the profit <strong>percentage</strong> you\'d like to achieve based on the current product costs.', 'cost-of-goods-for-woocommerce' ) ); ?><br/>
				            <?php echo wp_kses_post( sprintf( __( 'Example: If set as <code>10</code>, a product costing <code>%s</code> will have its price changed to <code>%s</code>.' ), wc_price( 50 ), wc_price( 55 ), 'cost-of-goods-for-woocommerce' ) ); ?>
			            </p>
		            </td>
	            </tr>
	            <tr>
		            <th scope="row"><label for="absolute-profit"><?php esc_html_e( 'Profit', 'cost-of-goods-for-woocommerce' ); ?></label></th>
		            <td>
			            <input id="absolute-profit" name="absolute_profit" step="0.01" min="0" type="number" placeholder="">
			            <p class="description">
				            <?php echo wp_kses_post( __( 'Products prices will be set according to the <strong>absolute</strong> profit you\'d like to achieve based on the current product costs.', 'cost-of-goods-for-woocommerce' ) ); ?><br/>
				            <?php echo wp_kses_post( sprintf( __( 'Example: If set as <code>10</code>, a product costing <code>%s</code> will have its price changed to <code>%s</code>.' ), wc_price( 50 ), wc_price( 60 ), 'cost-of-goods-for-woocommerce' ) ); ?>
			            </p>
		            </td>
	            </tr>
                <tr>
                    <th scope="row"><label for="product-category"><?php esc_html_e( 'Filter by category', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <select data-return_id="id" <?php echo esc_attr( $disabled ); ?> class="wc-category-search" multiple="multiple" style="width: 50%;" id="product-category" name="product_category[]" data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'cost-of-goods-for-woocommerce' ); ?>" data-action="json_search_categories">
                        </select>
                        <p class="description">
				            <?php esc_html_e( 'Select only the categories you want to edit. Leave it empty to update all products.', 'cost-of-goods-for-woocommerce' ); ?>
				            <?php echo ( ! empty( $blocked_text ) ) ? '<br />' . $blocked_text : ''; ?>
                        </p>
                    </td>
                </tr>
	            <tr>
		            <th scope="row"><label for="product-tag"><?php esc_html_e( 'Filter by tag(s)', 'cost-of-goods-for-woocommerce' ); ?></label></th>
		            <td>
			            <select data-return_id="id" <?php echo esc_attr( $disabled ); ?> class="wc-tag-search" multiple="multiple" style="width: 50%;" id="product-tag" name="product_tag[]" data-placeholder="<?php esc_attr_e( 'Search for a tag&hellip;', 'cost-of-goods-for-woocommerce' ); ?>" data-action="json_search_tags">
			            </select>
			            <p class="description">
				            <?php esc_html_e( 'Select only the tag(s) you want to edit. Leave it empty to update all products.', 'cost-of-goods-for-woocommerce' ); ?>
				            <?php echo ( ! empty( $blocked_text ) ) ? '<br />' . $blocked_text : ''; ?>
			            </p>
		            </td>
	            </tr>
            </table>
			<?php
		}

		/**
         * display_bulk_edit_variations.
         *
		 * @version 2.9.5
		 * @since   2.9.5
         *
		 * @return void
		 */
		function display_bulk_edit_variations() {
			?>

            <input type="hidden" name="update_variation_costs" value="yes">
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="empty_variation_costs_required"><?php esc_html_e( 'Undefined costs', 'cost-of-goods-for-woocommerce' ); ?></label>
                    </th>
                    <td>
                        <label class="description">
                            <input id="price-empty_variation_costs_required" name="empty_variation_costs_required" type="checkbox" checked>
							<?php esc_html_e( 'Only update variations with empty costs or set as zero', 'cost-of-goods-for-woocommerce' ); ?>
                        </label>
                        <p class="description">
		                    <?php esc_html_e( 'If disabled, will update all variations, including the ones with costs already set.', 'cost-of-goods-for-woocommerce' ); ?>
                        </p>
                    </td>
                </tr>
            </table>
			<?php
		}

		/**
		 * Display content for Profit Section.
		 *
		 * @version 2.9.5
		 * @since   2.5.1
		 */
		function display_bulk_edit_costs_profit() {
			$disabled     = apply_filters( 'alg_wc_cog_settings', 'disabled' );
			$blocked_text = apply_filters( 'alg_wc_cog_settings', alg_wc_cog_get_blocked_options_message() );
			?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="profit-percentage"><?php esc_html_e( 'Profit percentage (%)', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <input id="price-percentage" name="percentage" type="number" step="0.01" required placeholder="">
                        <p class="description">
	                        <?php esc_html_e( 'Products costs will be set according to the profit percentage you\'d like to achieve based on the current product prices.', 'cost-of-goods-for-woocommerce' ); ?><br />
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="product-category"><?php esc_html_e( 'Filter by category', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <select data-return_id="id" <?php echo esc_attr( $disabled ); ?> class="wc-category-search" multiple="multiple" style="width: 50%;" id="product-category" name="product_category[]" data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'cost-of-goods-for-woocommerce' ); ?>" data-action="json_search_categories">
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
	            <tr>
		            <th scope="row"><label for="product-tag"><?php esc_html_e( 'Filter by tag(s)', 'cost-of-goods-for-woocommerce' ); ?></label></th>
		            <td>
			            <select data-return_id="id" <?php echo esc_attr( $disabled ); ?> class="wc-tag-search" multiple="multiple" style="width: 50%;" id="product-tag" name="product_tag[]" data-placeholder="<?php esc_attr_e( 'Search for a tag&hellip;', 'cost-of-goods-for-woocommerce' ); ?>" data-action="json_search_tags">
			            </select>
			            <p class="description">
				            <?php esc_html_e( 'Select only the tag(s) you want to edit. Leave it empty to update all products.', 'cost-of-goods-for-woocommerce' ); ?>
				            <?php echo ( ! empty( $blocked_text ) ) ? '<br />' . $blocked_text : ''; ?>
			            </p>
		            </td>
	            </tr>
                <tr>
                    <th scope="row"><label for="costs_filter"><?php esc_html_e( 'Filter by cost', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <select class="wc-enhanced-select" <?php echo esc_attr( $disabled ); ?> data-return_id="id" <?php echo esc_attr( $disabled ); ?> id="costs_filter" name="costs_filter">
                            <option value="ignore_costs"><?php esc_html_e( 'Update products regardless of their current cost', 'cost-of-goods-for-woocommerce' ); ?></option>
                            <option value="products_without_costs"><?php esc_html_e( 'Update products with no costs set, including zero or empty', 'cost-of-goods-for-woocommerce' ); ?></option>
                            <option value="products_with_costs"><?php esc_html_e( 'Update products with costs already set', 'cost-of-goods-for-woocommerce' ); ?></option>
                        </select>
                        <p class="description">
	                        <?php echo ( ! empty( $blocked_text ) ) ? $blocked_text : ''; ?>
                        </p>
                    </td>
                </tr>
            </table>
			<?php
		}

		/**
		 * Display content for Price Section.
		 *
		 * @version 2.9.5
		 * @since   2.5.1
		 */
		function display_bulk_edit_costs_price() {
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
                        <select data-return_id="id" <?php echo esc_attr( $disabled ); ?> class="wc-category-search" multiple="multiple" style="width: 50%;" id="product-category" name="product_category[]" data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'cost-of-goods-for-woocommerce' ); ?>" data-action="json_search_categories">
                        </select>
                        <p class="description">
	                        <?php esc_html_e( 'Select only the categories you want to edit. Leave it empty to update all products.', 'cost-of-goods-for-woocommerce' ); ?>
	                        <?php echo ( ! empty( $blocked_text ) ) ? '<br />' . $blocked_text : ''; ?>
                        </p>
                    </td>
                </tr>
	            <tr>
		            <th scope="row"><label for="product-tag"><?php esc_html_e( 'Filter by tag(s)', 'cost-of-goods-for-woocommerce' ); ?></label></th>
		            <td>
			            <select data-return_id="id" <?php echo esc_attr( $disabled ); ?> class="wc-tag-search" multiple="multiple" style="width: 50%;" id="product-tag" name="product_tag[]" data-placeholder="<?php esc_attr_e( 'Search for a tag&hellip;', 'cost-of-goods-for-woocommerce' ); ?>" data-action="json_search_tags">
			            </select>
			            <p class="description">
				            <?php esc_html_e( 'Select only the tag(s) you want to edit. Leave it empty to update all products.', 'cost-of-goods-for-woocommerce' ); ?>
				            <?php echo ( ! empty( $blocked_text ) ) ? '<br />' . $blocked_text : ''; ?>
			            </p>
		            </td>
	            </tr>
                <tr>
                    <th scope="row"><label for="costs_filter"><?php esc_html_e( 'Filter by cost', 'cost-of-goods-for-woocommerce' ); ?></label></th>
                    <td>
                        <select class="wc-enhanced-select" <?php echo esc_attr( $disabled ); ?> data-return_id="id" <?php echo esc_attr( $disabled ); ?> id="costs_filter" name="costs_filter">
                            <option value="ignore_costs"><?php esc_html_e( 'Update products regardless of their current cost', 'cost-of-goods-for-woocommerce' ); ?></option>
                            <option value="products_without_costs"><?php esc_html_e( 'Update products with no costs set, including zero or empty', 'cost-of-goods-for-woocommerce' ); ?></option>
                            <option value="products_with_costs"><?php esc_html_e( 'Update products with costs already set', 'cost-of-goods-for-woocommerce' ); ?></option>
                        </select>
                        <p class="description">
				            <?php echo ( ! empty( $blocked_text ) ) ? $blocked_text : ''; ?>
                        </p>
                    </td>
                </tr>
            </table>
			<?php
		}

		/**
		 * Display content for Manually Section.
		 *
		 * @version 2.6.1
		 * @since   2.5.1
		 */
		function display_bulk_edit_costs_manually() {
			echo '<form method="get"><input type="hidden" name="page" value="bulk-edit-costs"/>';
			$this->wp_list_bulk_edit_tool->prepare_items();
			$this->wp_list_bulk_edit_tool->search_box( __( 'Search', 'cost-of-goods-for-woocommerce' ), 'alg_wc_cog_search' );
			$this->wp_list_bulk_edit_tool->display();
		}

		/**
		 * Display section navs HTML.
		 *
		 * @version 2.6.1
		 * @since   2.5.1
		 */
		function display_section_navs_html() {

            global $current_screen;

			$tabs_nav_html = array();
			$nav_sections  = $this->get_section_nav_items();
			$section       = isset( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : key( $nav_sections );

			foreach ( $nav_sections as $key => $tab ) {
				$label       = isset( $tab['label'] ) ? $tab['label'] : '';
				$is_current  = $section === $key ? 'current' : '';
				$section_url = admin_url( sprintf( 'tools.php?page=%s&section=%s', str_replace('tools_page_', '', $current_screen->base ), $key ) );

				$tabs_nav_html[] = sprintf( '<li><a href="%s" class="%s">%s</a></li>', $section_url, $is_current, $label );
			}

			printf( '<ul class="subsubsub no-float">%s</ul>', implode( ' | ', $tabs_nav_html ) );
		}

		/**
		 * display_wp_list_tool.
		 *
		 * @version 2.7.4
		 * @since   2.3.1
		 */
		function display_bulk_edit_tools() {

            global $current_screen;

			$page_title     = esc_html__( 'Bulk Edit Costs', 'cost-of-goods-for-woocommerce' );
			$section_method = $this->get_current_section( 'callback' );
            $tool_type      = 'update_costs';

            if( $this->page_slug_prices == str_replace( 'tools_page_', '', $current_screen->base ) ) {
	            $page_title = esc_html__( 'Bulk Edit Prices', 'cost-of-goods-for-woocommerce' );
	            $tool_type  = 'update_prices';
            }

			ob_start();

			// Section heading title
			printf( '<h1 class="wp-heading-inline">%s - %s %s</h1>', $page_title, $this->get_current_section( 'label' ), $this->get_current_section( 'save_btn_top' ) );

			// Section navigations
			$this->display_section_navs_html();

			// Section description
			if ( ! empty( $section_desc = $this->get_current_section( 'desc' ) ) ) {
				printf( '<p>%s</p>', $section_desc );
			}

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

			$container_elem_type = 'form';
			if (
				( isset( $_GET['page'] ) && 'bulk-edit-costs' === $_GET['page'] ) &&
				( ! isset( $_GET['section'] ) || 'costs_manually' === $_GET['section'] )
			) {
				$container_elem_type = 'div';
				echo '</form>';
			}

			// Wrap up section content
			printf( '<div class="notice is-dismissible alg_wc_cog_notice"><p></p></div><'.$container_elem_type.' method="post" action="" class="bulk-edit-form %s" data-type="%s" data-tool-type="%s"><div class="wrap alg_wc_cog_bulk_edit">%s</div></'.$container_elem_type.'>',
				$this->get_current_section( 'form_class' ),
				$this->get_current_section( 'id' ),
                $tool_type,
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
		 * @version 2.7.3
		 * @since   2.5.1
		 *
		 * @return mixed|void
		 */
		function get_section_nav_items() {

            global $current_screen;

			$current_screen_id  = $current_screen ? str_replace( 'tools_page_bulk-edit-', '', $current_screen->base ) : '';
			$bulk_edit_sections = array(
				'costs_manually'    => array(
					'label'           => esc_html__( 'Manually', 'cost-of-goods-for-woocommerce' ),
					'save_btn_top'    => '',
					'save_btn_bottom' => sprintf( '<input type="submit" name="alg_wc_cog_bulk_edit_tool_save_costs" class="button-primary" value="%s">',
						esc_html__( 'Save', 'cost-of-goods-for-woocommerce' )
					),
					'desc'            => sprintf( __( 'Bulk edit products costs/prices/stock. Tools options can be set in "<strong>Cost of Goods for WooCommerce</strong>" <a href="%s">plugin settings</a>', 'cost-of-goods-for-woocommerce' ),
						admin_url( 'admin.php?page=wc-settings&tab=alg_wc_cost_of_goods&section=tools' )
					),
					'form_class'      => 'bulk-edit-costs',
                    'callback'        => 'display_bulk_edit_costs_manually',
				),
				'costs_price'       => array(
					'label'           => esc_html__( 'By Price', 'cost-of-goods-for-woocommerce' ),
					'desc'            => esc_html__( 'Set the product costs from the product prices.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					                     esc_html__( 'Variations costs will also be updated accordingly.', 'cost-of-goods-for-woocommerce' ),
					'save_btn_bottom' => sprintf( '<input type="submit" class="button-primary" value="%s">', esc_html__( 'Update Costs', 'cost-of-goods-for-woocommerce' ) ),
					'form_class'      => 'bulk-edit-costs ajax-submission',
					'callback'        => 'display_bulk_edit_costs_price',
				),
				'costs_profit'      => array(
					'label'           => esc_html__( 'By Profit', 'cost-of-goods-for-woocommerce' ),
					'desc'            => esc_html__( 'Set the product costs according to the profit you want to achieve.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					                     esc_html__( 'Variations costs will also be updated accordingly.', 'cost-of-goods-for-woocommerce' ),
					'save_btn_bottom' => sprintf( '<input type="submit" class="button-primary" value="%s">', esc_html__( 'Update Costs', 'cost-of-goods-for-woocommerce' ) ),
					'form_class'      => 'bulk-edit-costs ajax-submission',
					'callback'        => 'display_bulk_edit_costs_profit',
				),
				'costs_variations'      => array(
					'label'           => esc_html__( 'Variations', 'cost-of-goods-for-woocommerce' ),
					'desc'            => esc_html__( 'Set or update the variations to have the same cost value as their parent products.', 'cost-of-goods-for-woocommerce' ),
					'save_btn_bottom' => sprintf( '<input type="submit" class="button-primary" value="%s">', esc_html__( 'Update Costs', 'cost-of-goods-for-woocommerce' ) ),
					'form_class'      => 'bulk-edit-costs ajax-submission',
					'callback'        => 'display_bulk_edit_variations',
				),
				'prices_profit'   => array(
					'label'           => esc_html__( 'By Profit', 'cost-of-goods-for-woocommerce' ),
					'save_btn_top'    => '',
					'save_btn_bottom' => sprintf( '<input type="submit" name="alg_wc_cog_bulk_edit_tool_save_costs" class="button-primary" value="%s">',
						esc_html__( 'Update prices', 'cost-of-goods-for-woocommerce' )
					),
					'desc'            => esc_html__( 'Set the product prices according to the cost.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					                     esc_html__( 'Variations prices will also be updated accordingly.', 'cost-of-goods-for-woocommerce' ),
					'form_class'      => 'bulk-edit-prices ajax-submission',
					'callback'        => 'display_bulk_edit_prices_profit',
				),
			);

			foreach ( $bulk_edit_sections as $section_key => $section ) {
				if ( strpos( $section_key, $current_screen_id . '_' ) === false ) {
					unset( $bulk_edit_sections[ $section_key ] );
				}
			}

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
				$this->page_slug_costs,
				array( $this, 'display_bulk_edit_tools' )
			);
			add_action( "load-{$hook}", array( $this, 'screen_option' ) );

			add_submenu_page(
				'tools.php',
				__( 'Bulk Edit Prices', 'cost-of-goods-for-woocommerce' ),
				__( 'Bulk Edit Prices', 'cost-of-goods-for-woocommerce' ),
				'manage_woocommerce',
				$this->page_slug_prices,
				array( $this, 'display_bulk_edit_tools' )
			);
		}

		/**
		 * enqueue_scripts_and_styles.
		 *
		 * @version 2.5.0
		 * @since   1.3.3
		 */
		function enqueue_scripts_and_styles( $hook ) {
            if( ! in_array( $hook, array( 'tools_page_bulk-edit-costs', 'tools_page_bulk-edit-prices' ) ) ) {
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
			?>
			<script>

			</script>
			<?php
		}

		/**
		 * add_tool_to_wc_screen_ids.
		 *
		 * for `wc_input_price` class.
		 *
		 * @version 2.6.1
		 * @since   1.2.0
		 */
		function add_tool_to_wc_screen_ids( $screen_ids ) {
			$screen_ids[] = 'tools_page_bulk-edit-costs';
			$screen_ids[] = 'tools_page_bulk-edit-prices';
			return $screen_ids;
		}

		/**
		 * save_costs.
		 *
		 * @version 2.8.8
		 * @since   1.2.0
		 * @see     https://wordpress.org/support/topic/you-should-add-posibility-to-edit-regular-price-and-sale-price/
		 * @todo    [next] prices: `$do_update_func`
		 * @todo    [maybe] nonce etc.
		 * @todo    [maybe] output some error on ` ! ( $product = wc_get_product( $product_id ) )`?
		 */
		function save_costs() {
			if (
				isset( $_POST['alg_wc_cog_bulk_edit_tool_save_costs'] ) &&
				isset( $_POST['_nonce_costs_manually_val'] ) &&
				wp_verify_nonce( $_REQUEST['_nonce_costs_manually_val'], '_nonce_costs_manually_action' ) &&
				current_user_can( 'manage_woocommerce' )
			) {
				// Do edit prices.
				$do_edit_prices = ( 'yes' === get_option( 'alg_wc_cog_bulk_edit_tool_edit_prices', 'no' ) );
				if ( $do_edit_prices ) {
					$error_sale_price_ids = array();
				}
				// Manually.
				if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_costs'] ) && is_array( $_POST['alg_wc_cog_bulk_edit_tool_costs'] ) ) {
					foreach ( $_POST['alg_wc_cog_bulk_edit_tool_costs'] as $product_id => $cost_value ) {
						update_post_meta( sanitize_key( $product_id ), '_alg_wc_cog_cost', sanitize_text_field( $cost_value ) );
					}
				}
				// Prices.
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
				// Stock.
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
				// Tags update.
				if ( isset( $_POST['alg_wc_cog_bulk_edit_tool_product_tag'] ) && is_array( $_POST['alg_wc_cog_bulk_edit_tool_product_tag'] ) ) {
					foreach ( $_POST['alg_wc_cog_bulk_edit_tool_product_tag'] as $product_id => $tag_ids ) {
						$tag_ids = array_map( 'intval', $tag_ids );
						wp_set_post_terms( $product_id, $tag_ids, 'product_tag');
					}
				}
				// Notices.
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
			echo '<div class="notice notice-success is-dismissible"><p><strong>' . __( 'Data have been saved.', 'cost-of-goods-for-woocommerce' ) . '</strong></p></div>';
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


