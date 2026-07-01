<?php
/**
 * Cost of Goods for WooCommerce - Import Tool Class.
 *
 * @version 4.1.6
 * @since   1.1.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFCOGS_Import_Tool' ) ) :

	class WPFCOGS_Import_Tool {

		/**
		 * @var WPFCOGS_Import_Tool_Bkg_Process
		 */
		public $import_tool_bkg_process;

		/**
		 * Constructor.
		 *
		 * @version 2.8.1
		 * @since   1.1.0
		 */
		function __construct() {
			add_action( 'admin_menu', array( $this, 'create_import_tool' ) );
			// Bkg Process
			$this->init_bkg_process();
			// Run copy tool on WooCommerce import.
			add_action( 'woocommerce_product_import_inserted_product_object', array( $this, 'run_copy_tool_on_wc_import' ), 10, 2 );
			// Run import tool automatically based on cron.
			add_action( 'update_option_' . 'alg_wc_cog_import_tool_cron', array( $this, 'handle_auto_import_cron_event' ), 10, 2 );
			add_action( 'add_option_' . 'alg_wc_cog_import_tool_cron', array( $this, 'handle_auto_import_cron_event_on_db_option_update' ), 10, 2 );
			add_action( 'wpfcogs_on_activation', array( $this, 'handle_auto_import_cron_event_on_plugin_switch' ) );
			add_action( 'wpfcogs_on_deactivation', array( $this, 'handle_auto_import_cron_event_on_plugin_switch' ) );
			add_action( 'wpfcogs_run_import_tool', array( $this, 'run_import_tool_by_cron' ) );
		}

		/**
		 * run_import_tool_by_cron.
		 *
		 * @version 2.8.1
		 * @since   2.8.1
		 */
		function run_import_tool_by_cron() {
			$this->import_tool( array(
				'perform_import' => true,
				'display_output' => false
			) );
		}

		/**
		 * handle_auto_import_cron_event_on_plugin_switch.
		 *
		 * @version 2.8.1
		 * @since   2.8.1
		 */
		function handle_auto_import_cron_event_on_plugin_switch() {
			if ( false !== strpos( current_filter(), 'deactivation' ) ) {
				$this->handle_auto_import_cron_event( '', 'off' );
			} else {
				$this->handle_auto_import_cron_event( '', get_option( 'alg_wc_cog_import_tool_cron', 'no' ) );
			}
		}

		/**
		 * handle_auto_import_cron_event_on_db_option_update.
		 *
		 * @version 2.8.1
		 * @since   2.8.1
		 *
		 * @param $option_name
		 * @param $option_value
		 */
		function handle_auto_import_cron_event_on_db_option_update( $option_name, $option_value ) {
			$this->handle_auto_import_cron_event( '', $option_value );
		}

		/**
		 * schedule_delete_unverified_users_cron.
		 *
		 * @version 2.8.1
		 * @since   2.8.1
		 */
		function handle_auto_import_cron_event( $old_value, $value ) {
			if ( 'yes' === $value ) {
				if ( ! wp_next_scheduled( 'wpfcogs_run_import_tool' ) ) {
					wp_schedule_event( time(), get_option( 'alg_wc_cog_import_tool_cron_frequency', 'daily' ), 'wpfcogs_run_import_tool' );
				}
			} else {
				if ( $time = wp_next_scheduled( 'wpfcogs_run_import_tool' ) ) {
					wp_unschedule_event( $time, 'wpfcogs_run_import_tool' );
				}
			}
		}

		/**
		 * run_copy_tool_on_wc_import.
		 *
		 * @version 2.8.0
		 * @since   2.8.0
		 *
		 * @param $product
		 * @param $data
		 */
		function run_copy_tool_on_wc_import( $product, $data ) {
			if (
				'yes' === get_option( 'alg_wc_cog_import_tool_sync_wc_import', 'no' ) &&
				is_a( $product, 'WC_Product' )
			) {
				$this->copy_product_meta( array(
					'product_id' => $product->get_id(),
					'from_key'   => get_option( 'alg_wc_cog_tool_key', '_wc_cog_cost' ),
					'to_key'     => get_option( 'alg_wc_cog_tool_key_to', '_alg_wc_cog_cost' ),
				) );
			}
		}

		/**
		 * init_bkg_process.
		 *
		 * @version 2.5.1
		 * @since   2.3.0
		 */
		function init_bkg_process() {
			require_once( wpfcogs()->plugin_path() . '/includes/background-process/class-wpfcogs-import-tool-bkg-process.php' );
			$this->import_tool_bkg_process = new WPFCOGS_Import_Tool_Bkg_Process();
		}


		/**
		 * create_import_tool.
		 *
		 * @version 2.3.4
		 * @since   1.0.0
		 */
		function create_import_tool() {
			if ( ! apply_filters( 'wpfcogs_create_import_tool_validation', true ) ) {
				return;
			}
			add_submenu_page(
				'tools.php',
				__( 'Import Costs', 'cost-of-goods-for-woocommerce' ),
				__( 'Import Costs', 'cost-of-goods-for-woocommerce' ),
				'manage_woocommerce',
				'wpfcogs-import-costs',
				array( $this, 'import_tool' )
			);
		}

		/**
		 * copy_product_meta.
		 *
		 * @version 3.7.2
		 * @since   2.3.0
		 *
		 * @param null $args
		 */
		function copy_product_meta( $args = null ) {
			$args = wp_parse_args( $args, array(
				'product_id'             => '',
				'from_key'               => get_option( 'alg_wc_cog_tool_key', '_wc_cog_cost' ),
				'to_key'                 => get_option( 'alg_wc_cog_tool_key_to', '_alg_wc_cog_cost' ),
				'get_cost_function'      => 'get_post_meta',
				'get_cost_function_args' => null,
				'source_value'           => null
			) );
			$args                   = apply_filters( 'wpfcogs_copy_product_meta_args', $args );
			$product_id             = $args['product_id'];
			$to_key                 = $args['to_key'];
			$from_key               = $args['from_key'];
			$get_cost_function      = $args['get_cost_function'];
			$get_cost_function_args = $args['get_cost_function_args'];
			$source_value           = $args['source_value'];
			if ( is_null( $get_cost_function_args ) ) {
				$get_cost_function_args = array( $product_id, $from_key, true );
			}
			$source_cost = is_null( $source_value ) ? call_user_func_array( $get_cost_function, $get_cost_function_args ) : $source_value;
			if ( $this->can_copy_cost( $source_cost, $args ) ) {
				$prev_to_cost = call_user_func_array( $get_cost_function, array( $product_id, $to_key, true ) );
				update_post_meta( $product_id, $to_key, $source_cost );
				if ( $source_cost === $prev_to_cost ) {
					do_action( "updated_post_meta", null, $product_id, $to_key, $source_cost );
				}
			}
		}

		/**
		 * can_update_cost.
		 *
		 * @version 2.5.7
		 * @since   2.5.7
		 *
		 * @param $source_cost
		 * @param $args
		 *
		 * @return bool
		 */
		protected function can_copy_cost( $source_cost, $args ) {
			$product_id        = $args['product_id'];
			$from_key          = $args['from_key'];
			$get_cost_function = $args['get_cost_function'];
			$can_update        = true;
			if (
				(
					'get_post_meta' === $get_cost_function &&
					'yes' === get_option( 'alg_wc_cog_import_tool_check_key', 'yes' ) &&
					! metadata_exists( 'post', $product_id, $from_key )
				) ||
				(
					'yes' === get_option( 'alg_wc_cog_import_tool_check_value', 'yes' )
					&& empty( $source_cost )
				)
			) {
				$can_update = false;
			}
			$can_update = apply_filters( 'wpfcogs_can_copy_cost', $can_update, $source_cost, $args );
			return $can_update;
		}

		/**
		 * import_tool.
		 *
		 * @version 4.1.6
		 * @since   1.0.0
		 * @todo    [later] use `wc_get_products()`
		 * @todo    [later] better description here and in settings
		 * @todo    [later] notice after import
		 * @todo    [later] add "import from file" option (CSV, XML etc.) (#12169)
		 * @todo    [maybe] import order items meta
		 */
		function import_tool( $args = null ) {
			$import_submit = filter_input( INPUT_POST, 'wpfcogs_import', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$import_submit = ! empty( $import_submit ) ? $import_submit : ( isset( $_POST['wpfcogs_import'] ) ? sanitize_text_field( wp_unslash( $_POST['wpfcogs_import'] ) ) : '' );
			$import_nonce  = filter_input( INPUT_POST, 'wpfcogs_import_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$import_nonce  = ! empty( $import_nonce ) ? $import_nonce : ( isset( $_POST['wpfcogs_import_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wpfcogs_import_nonce'] ) ) : '' );
			$has_valid_import_nonce = ! empty( $import_nonce ) && wp_verify_nonce( $import_nonce, 'wpfcogs_import_action' );

			$args = wp_parse_args( $args, array(
				'perform_import' => ( ! empty( $import_submit ) && $has_valid_import_nonce ),
				'display_output' => true
			) );
			$perform_import            = $args['perform_import'];
			$display_output            = $args['display_output'];
			$key_from                  = get_option( 'alg_wc_cog_tool_key', '_wc_cog_cost' );
			$key_to                    = get_option( 'alg_wc_cog_tool_key_to', '_alg_wc_cog_cost' );
			$bkg_process_min_amount    = get_option( 'alg_wc_cog_bkg_process_min_amount', 100 );
			$table_data                = array();
			$wpfcogs_get_table_html = '';
			$display_table             = 'yes' === get_option( 'alg_wc_cog_import_tool_display_table', 'no' );
			$table_data[]              = array(
				__( 'Product ID', 'cost-of-goods-for-woocommerce' ),
				__( 'Product Title', 'cost-of-goods-for-woocommerce' ),
				/* translators: %s: Source custom field key. */
				sprintf( __( 'Source %s', 'cost-of-goods-for-woocommerce' ), '<code>' . $key_from . '</code>' ),
				/* translators: %s: Destination custom field key. */
				sprintf( __( 'Destination %s', 'cost-of-goods-for-woocommerce' ), '<code>' . $key_to . '</code>' ),
			);
			$args                      = array(
				'post_type'              => array( 'product', 'product_variation' ),
				'post_status'            => 'any',
				'posts_per_page'         => - 1,
				'orderby'                => 'ID',
				'order'                  => 'ASC',
				'fields'                 => 'ids',
				'no_found_rows'          => false,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'cache_results'          => false
			);
			$loop                      = new WP_Query( $args );
			$perform_bkg_process       = $perform_import && $loop->post_count >= $bkg_process_min_amount;

			// Update values.
			if ( $perform_bkg_process ) {
				$this->import_tool_bkg_process->cancel_process();
				if ( $loop->have_posts() ) {
					foreach ( $loop->posts as $product_id ) {
						$this->import_tool_bkg_process->push_to_queue( array( 'id' => $product_id, 'from_key' => $key_from, 'to_key' => $key_to ) );
					}
				}
				$this->import_tool_bkg_process->save()->dispatch();
			} else {
				if ( $perform_import && $loop->have_posts() ) {
					foreach ( $loop->posts as $product_id ) {
						$this->copy_product_meta( array(
							'product_id' => $product_id,
							'from_key'   => $key_from,
							'to_key'     => $key_to
						) );
					}
				}
			}

			// Output.
			if ( $display_output && $display_table ) {
				if ( $loop->have_posts() ) {
					foreach ( $loop->posts as $product_id ) {
						$source_cost = get_post_meta( $product_id, $key_from, true );
						if ( '_alg_wc_cog_cost' === $key_to ) {
							$meta_value_to = wpfcogs()->core->products->get_product_cost( $product_id );
						} else {
							$meta_value_to = get_post_meta( $product_id, $key_to, true );
						}
						$table_data[] = array( $product_id, get_the_title( $product_id ), $source_cost, $meta_value_to );
					}
				}
				$wpfcogs_get_table_html = wpfcogs_get_table_html( $table_data, array( 'table_heading_type' => 'horizontal', 'table_class' => 'widefat striped' ) );
			}
			$import_nonce_field = wp_nonce_field( 'wpfcogs_import_action', 'wpfcogs_import_nonce', true, false );
			?>
			<div class="wrap">
				<h1><?php esc_html_e( 'Costs Import', 'cost-of-goods-for-woocommerce' ); ?></h1>
				<p>
					<?php
					/* translators: 1: Source custom field key, 2: Destination custom field key. */
					echo wp_kses_post( sprintf( __( 'Import products costs to "Cost of Goods for WooCommerce" plugin by copying the meta from %1$s to %2$s.', 'cost-of-goods-for-woocommerce' ), '<code>' . esc_html( get_option( 'alg_wc_cog_tool_key', '_wc_cog_cost' ) ) . '</code>', '<code>' . esc_html( get_option( 'alg_wc_cog_tool_key_to', '_alg_wc_cog_cost' ) ) . '</code>' ) );
					echo ' ';
					/* translators: %s: Link to plugin settings page. */
					echo wp_kses_post( sprintf( __( 'Tool\'s options can be set in %s.', 'cost-of-goods-for-woocommerce' ), '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=alg_wc_cost_of_goods&section=tools' ) ) . '">' . esc_html__( 'plugin settings', 'cost-of-goods-for-woocommerce' ) . '</a>' ) );
					?>
				</p>
				<p>
					<form method="post" action="">
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_nonce_field() outputs trusted nonce markup.
						echo $import_nonce_field;
						?>
						<input type="submit" name="wpfcogs_import" class="button-primary" value="<?php echo esc_attr__( 'Import', 'cost-of-goods-for-woocommerce' ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Are you sure?', 'cost-of-goods-for-woocommerce' ) ); ?>')">
					</form>
				</p>
				<?php if ( ! empty( $wpfcogs_get_table_html ) ) : ?>
					<p>
						<?php echo wp_kses_post( $wpfcogs_get_table_html ); ?>
					</p>
				<?php endif; ?>
			</div>
			<?php
		}

	}

endif;

return new WPFCOGS_Import_Tool();
