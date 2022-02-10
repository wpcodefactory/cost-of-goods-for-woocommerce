<?php
/**
 * Cost of Goods for WooCommerce - Products Class
 *
 * @version 2.5.1
 * @since   2.1.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Products' ) ) :

class Alg_WC_Cost_of_Goods_Products {

	/**
	 * Constructor.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	function __construct() {
		$this->get_options();
		$this->add_hooks();
	}

	/**
	 * get_options.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	function get_options() {
		// Templates
		$this->cost_field_template                    = get_option( 'alg_wc_cog_product_cost_field_template', sprintf( __( 'Cost (excl. tax) (%s)', 'cost-of-goods-for-woocommerce' ), '%currency_symbol%' ) );
		$this->product_profit_html_template           = get_option( 'alg_wc_cog_product_profit_html_template', '%profit% (%profit_percent%)' );
		// Add stock
		$this->is_add_stock                           = ( 'yes' === get_option( 'alg_wc_cog_products_add_stock', 'no' ) );
		// Columns
		$this->is_column_cost                         = ( 'yes' === get_option( 'alg_wc_cog_products_columns_cost', 'no' ) );
		$this->is_column_profit                       = ( 'yes' === get_option( 'alg_wc_cog_products_columns_profit', 'no' ) );
		// Sorting
		$this->is_columns_sorting                     = ( 'yes' === get_option( 'alg_wc_cog_columns_sorting', 'yes' ) );
		$this->is_columns_sorting_exclude_empty_lines = ( 'yes' === get_option( 'alg_wc_cog_columns_sorting_exclude_empty_lines', 'yes' ) );
	}

	/**
	 * add_hooks.
	 *
	 * @version 2.5.1
	 * @since   2.1.0
	 */
	function add_hooks() {
		// Cost input on admin product page (simple product)
		add_action( get_option( 'alg_wc_cog_product_cost_field_position', 'woocommerce_product_options_pricing' ), array( $this, 'add_cost_input' ) );
		add_action( 'woocommerce_bookings_after_display_cost', array( $this, 'add_cost_input' ) );
		add_action( 'save_post_product', array( $this, 'save_cost_input' ), PHP_INT_MAX, 2 );
		// Cost input on admin product page (variable product)
		add_action( 'woocommerce_variation_options_pricing', array( $this, 'add_cost_input_variation' ), 10, 3 );
		add_action( 'woocommerce_save_product_variation', array( $this, 'save_cost_input_variation' ), PHP_INT_MAX, 2 );
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_cost_input_variable' ), PHP_INT_MAX );
		// Products columns
		if ( $this->is_column_profit || $this->is_column_cost ) {
			add_filter( 'manage_edit-product_columns', array( $this, 'add_product_columns' ) );
			add_action( 'manage_product_posts_custom_column', array( $this, 'render_product_columns' ), PHP_INT_MAX, 2 );
			// Make columns sortable
			if ( $this->is_columns_sorting ) {
				add_filter( 'manage_edit-product_sortable_columns', array( $this, 'product_sortable_columns' ) );
				add_action( 'pre_get_posts', array( $this, 'product_pre_get_posts_order_by_column' ) );
			}
			add_action( 'admin_head-edit.php', array( $this, 'handle_product_columns_style' ) );
		}
		// Products > Export (WooCommerce)
		add_filter( 'woocommerce_product_export_column_names', array( $this, 'add_export_column' ) );
		add_filter( 'woocommerce_product_export_product_default_columns', array( $this, 'add_export_column' ) );
		add_filter( 'woocommerce_product_export_product_column_alg_wc_cog_cost', array( $this, 'add_export_data' ), 10, 2 );
		// Products > Import (WooCommerce)
		add_filter( 'woocommerce_csv_product_import_mapping_options', array( $this, 'add_import_mapping_option' ) );
		add_filter( 'woocommerce_csv_product_import_mapping_default_columns', array( $this, 'set_import_mapping_option_default' ) );
		add_filter( 'woocommerce_product_importer_parsed_data', array( $this, 'parse_import_data' ), 10, 2 );
		// Add product stock
		add_action( 'add_meta_boxes', array( $this, 'add_product_add_stock_meta_box' ) );
		add_action( 'save_post_product', array( $this, 'save_product_add_stock' ), PHP_INT_MAX, 2 );
		// Sanitize cog meta (_alg_wc_cog_cost)
		add_filter( 'sanitize_post_meta_' . '_alg_wc_cog_cost', array( $this, 'sanitize_cog_meta' ) );
		// Save profit
		add_action( 'updated_post_meta', array( $this, 'save_profit_on_postmeta' ), 10, 4 );
		add_action( 'added_post_meta', array( $this, 'save_profit_on_postmeta' ), 10, 4 );
		add_action( 'deleted_post_meta', array( $this, 'save_profit_on_postmeta' ), 10, 4 );
	}

	/**
	 * Save profit on post meta everytime the cost or price is updated on product.
	 *
	 * @version 2.5.1
	 * @since   2.5.1
	 *
	 * @param $meta_id
	 * @param $post_id
	 * @param $meta_key
	 * @param $meta_value
	 */
	function save_profit_on_postmeta( $meta_id, $post_id, $meta_key, $meta_value ) {
		if (
			in_array( $meta_key, array(
				'_alg_wc_cog_cost',
				'_price',
				'_sale_price',
			) ) &&
			is_a( $product = wc_get_product( $post_id ), 'WC_Product' )
		) {
			$profit = (float) $this->get_product_profit( $post_id );
			$cost   = (float) $this->get_product_cost( $post_id );
			$price  = (float) $this->get_product_price( $product );
			update_post_meta( $post_id, '_alg_wc_cog_profit', $profit );
			update_post_meta( $post_id, '_alg_wc_cog_profit_percent', ( 0 != $cost ? ( $profit / $cost * 100 ) : 0 ) );
			update_post_meta( $post_id, '_alg_wc_cog_profit_margin', ( 0 != $price ? ( $profit / $price * 100 ) : 0 ) );
		}
	}

	/**
	 * handle_product_columns_style.
	 *
	 * @version 2.4.2
	 * @since   2.4.2
	 */
	function handle_product_columns_style() {
		global $post_type;
		if ( 'product' !== $post_type ) {
			return;
		}
		$width_unit         = get_option( 'alg_wc_cog_products_columns_width_unit', '%' );
		$cost_width_style   = empty( $cost_width = get_option( 'alg_wc_cog_products_columns_cost_width', '10' ) ) ? '' : 'width:' . intval( $cost_width ) . $width_unit;
		$profit_width_style = empty( $profit_width = get_option( 'alg_wc_cog_products_columns_profit_width', '10' ) ) ? '' : 'width:' . intval( $profit_width ) . $width_unit;
		?>
		<style>
			.wp-list-table .column-cost {
			<?php echo $cost_width_style; ?>
			}

			.wp-list-table .column-profit {
			<?php echo $profit_width_style; ?>
			}
		</style>
		<?php
	}

	/**
	 * sanitize_cog_meta.
	 *
	 * @see https://stackoverflow.com/a/4325608/1193038
	 *
	 * @version 2.3.5
	 * @since   2.3.5
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	function sanitize_cog_meta( $value ) {
		if ( 'yes' === get_option( 'alg_wc_cog_products_sanitize_cog_meta', 'no' ) ) {
			$value = str_replace( ',', '.', $value );
		}
		return $value;
	}

	/**
	 * add_product_add_stock_meta_box.
	 *
	 * @version 2.3.4
	 * @since   1.7.0
	 */
	function add_product_add_stock_meta_box() {
		if ( ! apply_filters( 'alg_wc_cog_create_product_meta_box_validation', true ) ) {
			return;
		}
		if ( $this->is_add_stock ) {
			if ( ( $product = wc_get_product( get_the_ID() ) ) && $product->is_type( 'simple' ) ) {
				$tip = wc_help_tip( __( 'Enter values and "Update" the product.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					__( '"Stock" will be added to your inventory, and "Cost" will be used to calculate new average cost of goods for the product.', 'cost-of-goods-for-woocommerce' ) );
				add_meta_box( 'alg-wc-cog-add-stock',
					__( 'Cost of Goods', 'cost-of-goods-for-woocommerce' ) . ': ' . __( 'Add stock', 'cost-of-goods-for-woocommerce' ) . $tip,
					array( $this, 'product_add_stock_meta_box' ),
					'product',
					'side'
				);
			}
		}
	}

	/**
	 * get_add_stock_bulk_and_quick_edit_fields.
	 *
	 * @version 2.4.2
	 * @since   2.3.5
	 *
	 * @todo Try to understand why the tip doesn't work on quick edit but does work on bulk edit.
	 *
	 * @return string
	 */
	function get_add_stock_bulk_and_quick_edit_fields() {
		$negative_stock_allowed = 'yes' === get_option( 'alg_wc_cog_products_add_stock_negative_stock', 'no' );
		$add_stock_input_min = $negative_stock_allowed ? '' : 'min="0"';
		//$tip = wc_help_tip( __( '"Stock" will be added to your inventory, and "Cost" will be used to calculate new average cost of goods for the product.', 'cost-of-goods-for-woocommerce' ) );
		ob_start();
		?>
		<br class="clear"/>
		<h4 class="title" style="margin-bottom: 10px;"><?php echo __( 'Cost of Goods', 'cost-of-goods-for-woocommerce' ) . ': ' . __( 'Add stock', 'cost-of-goods-for-woocommerce' ) ?><?php //echo $tip; ?></h4>
		<label>
			<span class="title"><?php echo __( 'Stock', 'cost-of-goods-for-woocommerce' ) ?></span>
			<span class="input-text-wrap">
				<input name="_alg_wc_cog_add_stock_qb" id="_alg_wc_cog_add_stock_qb" class="short" type="number" <?php echo $add_stock_input_min; ?>>
			</span>
		</label>
		<label>
			<span class="title"><?php echo __( 'Cost', 'cost-of-goods-for-woocommerce' ) ?></span>
			<span class="input-text-wrap">
				<input name="_alg_wc_cog_add_stock_cost_qb" id="_alg_wc_cog_add_stock_cost_qb" class="short wc_input_price" type="number" step="0.0001" min="0">
			</span>
		</label>
		<?php
		return ob_get_clean();
	}

	/**
	 * product_add_stock_meta_box.
	 *
	 * @version 2.4.2
	 * @since   1.7.0
	 * @todo    [next] add option to delete all/selected history
	 */
	function product_add_stock_meta_box( $post ) {
		$negative_stock_allowed = 'yes' === get_option( 'alg_wc_cog_products_add_stock_negative_stock', 'no' );
		$add_stock_input_min = $negative_stock_allowed ? '' : 'min="0"';

		$html  = '';
		$html .= '<table class="widefat striped"><tbody>' .
				'<tr>' .
					'<th><label for="alg_wc_cog_add_stock">' . __( 'Stock', 'cost-of-goods-for-woocommerce' ) . '</label></th>' .
					'<td><input name="alg_wc_cog_add_stock" id="alg_wc_cog_add_stock" class="short" type="number" '.$add_stock_input_min.'></td>' .
				'</tr>' .
				'<tr>' .
					'<th><label for="alg_wc_cog_add_stock">' . __( 'Cost', 'cost-of-goods-for-woocommerce' ) . '</label></th>' .
					'<td><input name="alg_wc_cog_add_stock_cost" id="alg_wc_cog_add_stock_cost" class="short wc_input_price" type="number" step="0.0001" min="0"></td>' .
				'</tr>' .
			'</tbody></table>';
		$history = get_post_meta( get_the_ID(), '_alg_wc_cog_cost_history', true );
		if ( $history ) {
			$history_rows = '';
			foreach ( $history as $date => $record ) {
				$history_rows .= '<tr><td>' . date( 'Y-m-d', $date ) . '</td><td>' . $record['stock'] . '</td><td>' . alg_wc_cog_format_cost( $record['cost'] ) . '</td></tr>';
			}
			$html .= '<hr>' .
				'<details>' .
					'<summary>' . __( 'History', 'cost-of-goods-for-woocommerce' ) . '</summary>' .
					'<table class="widefat striped"><tbody>' .
						'<tr>' .
							'<th>' . __( 'Date', 'cost-of-goods-for-woocommerce' ) . '</th>' .
							'<th>' . __( 'Stock', 'cost-of-goods-for-woocommerce' ) . '</th>' .
							'<th>' . __( 'Cost', 'cost-of-goods-for-woocommerce' ) . '</th>' .
						'</tr>' .
						$history_rows .
					'</tbody></table>' .
				'</details>';
		}
		echo $html;
	}

	/**
	 * save_product_add_stock.
	 *
	 * @version 2.4.2
	 * @since   1.7.0
	 * @todo    [next] handle variable products (also unset `$_POST['variable_stock']`)
	 * @todo    [maybe] remove `$this->is_add_stock`
	 */
	function save_product_add_stock( $product_id, $post ) {
		if (
			$this->is_add_stock
			&& ! empty( $_POST['alg_wc_cog_add_stock'] )
			&&
			(
				'do_nothing' !== ( $empty_cost_action = get_option( 'alg_wc_cog_products_add_stock_empty_cost_action', 'do_nothing' ) )
				||
				(
					'do_nothing' === $empty_cost_action
					&& ! empty( $_POST['alg_wc_cog_add_stock_cost'] )
				)
			)
		) {
			$this->product_add_stock( $product_id, floatval( $_POST['alg_wc_cog_add_stock'] ), floatval( $_POST['alg_wc_cog_add_stock_cost'] ) );
			if ( isset( $_POST['_stock'] ) ) {
				unset( $_POST['_stock'] );
			}
		}
	}

	/**
	 * sanitize_math_expression.
	 *
	 * @version 2.4.2
	 * @since   2.4.2
	 *
	 * @param $expression
	 *
	 * @return null|string|string[]
	 */
	function sanitize_math_expression( $expression ) {
		// Remove whitespace from string.
		$expression = preg_replace( '/\s+/', '', $expression );

		// Trim invalid start/end characters.
		$expression = rtrim( ltrim( $expression, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );
		return $expression;
	}

	/**
	 * get_add_stock_cost.
	 *
	 * @version 2.4.2
	 * @since   2.4.2
	 *
	 * @param null $args
	 *
	 * @return bool|float
	 */
	function get_add_stock_cost( $args = null ) {
		$args = wp_parse_args( $args, array(
			'cost'              => '',
			'product_id'        => '',
			'empty_cost_action' => get_option( 'alg_wc_cog_products_add_stock_empty_cost_action', 'do_nothing' )
		) );
		$cost = $args['cost'];
		if ( empty( $cost ) ) {
			switch ( $args['empty_cost_action'] ) {
				case 'do_nothing':
					$cost = false;
					break;
				case 'use_last_cost':
					$history = get_post_meta( $args['product_id'], '_alg_wc_cog_cost_history', true );
					if ( ! $history || ! is_array( $history ) ) {
						$cost = false;
					} else {
						$cost = array_values( array_slice( $history, - 1 ) )[0]['cost'];
					}
					break;
				case 'use_current_cost':
					$cost = $this->get_product_cost( $args['product_id'] );
					break;

			}
		}
		return $cost;
	}

	/**
	 * calculate_add_stock_cost.
	 *
	 * @version 2.4.2
	 * @since   2.4.2
	 *
	 * @param null $args
	 *
	 * @return mixed
	 */
	function calculate_add_stock_cost( $args = null ) {
		$args = wp_parse_args( $args, array(
			'product_id'           => '',
			'template_variables'   => array(
				'%stock_prev%' => '',
				'%cost_prev%'  => '',
				'%stock%'      => '',
				'%cost%'       => '',
				'%stock_now%'  => '',
			),
			'calculation_template' => get_option( 'alg_wc_cog_products_add_stock_cost_calculation', '( %stock_prev% * %cost_prev% + %stock% * %cost% ) / %stock_now%' )
		) );
		$template_variables = $args['template_variables'];
		$cost_calculation_template = $args['calculation_template'];
		$cost_calculation_template = $this->sanitize_math_expression( str_replace( array_keys( $template_variables ), $template_variables, $cost_calculation_template ) );
		include_once WC()->plugin_path() . '/includes/libraries/class-wc-eval-math.php';
		$cost_now = WC_Eval_Math::evaluate( $cost_calculation_template );
		return $cost_now;
	}

	/**
	 * product_add_stock.
	 *
	 * @version 2.4.2
	 * @since   1.7.0
	 * @todo    [next] maybe use `$product = wc_get_product( $product_id )`, i.e. `$product->get_stock_quantity()`, `$product->set_stock_quantity( $stock_now )` and `$product->save()`?
	 * @todo    [maybe] `$cost_now`: round?
	 */
	function product_add_stock( $product_id, $stock, $cost ) {
		$cost = $this->get_add_stock_cost( array(
			'cost'       => $cost,
			'product_id' => $product_id
		) );
		$stock_prev = get_post_meta( $product_id, '_stock', true );
		if ( ! $stock_prev ) {
			$stock_prev = 0;
		}
		$stock_now  = ( $stock_prev + $stock );
		if ( 0 != $stock_now && false !== $cost) {
			$cost_prev = $this->get_product_cost( $product_id );
			if ( ! $cost_prev ) {
				$cost_prev = 0;
			}
			$cost_now = $this->calculate_add_stock_cost( array(
				'product_id'         => $product_id,
				'template_variables' => array(
					'%stock_prev%' => $stock_prev,
					'%cost_prev%'  => $cost_prev,
					'%stock%'      => $stock,
					'%cost%'       => $cost,
					'%stock_now%'  => $stock_now,
				)
			) );
			update_post_meta( $product_id, '_alg_wc_cog_cost', $cost_now );
			update_post_meta( $product_id, '_stock', $stock_now );
			$history = get_post_meta( $product_id, '_alg_wc_cog_cost_history', true );
			if ( ! $history ) {
				$history = array();
			}
			$history[ current_time( 'timestamp' ) ] = array( 'stock' => $stock, 'cost' => $cost );
			update_post_meta( $product_id, '_alg_wc_cog_cost_history', $history );
			return $cost_now;
		}
		return false;
	}

	/**
	 * parse_import_data.
	 *
	 * @version 1.5.1
	 * @since   1.5.1
	 */
	function parse_import_data( $data, $importer ) {
		if ( isset( $data['alg_wc_cog_cost'] ) ) {
			if ( ! isset( $data['meta_data'] ) ) {
				$data['meta_data'] = array();
			}
			$data['meta_data'][] = array(
				'key'   => '_' . 'alg_wc_cog_cost',
				'value' => $data['alg_wc_cog_cost'],
			);
			unset( $data['alg_wc_cog_cost'] );
		}
		return $data;
	}

	/**
	 * set_import_mapping_option_default.
	 *
	 * @version 1.7.2
	 * @since   1.5.1
	 */
	function set_import_mapping_option_default( $columns ) {
		$columns[ __( 'Cost', 'cost-of-goods-for-woocommerce' ) ] = 'alg_wc_cog_cost';
		return $columns;
	}

	/**
	 * add_import_mapping_option.
	 *
	 * @version 1.5.1
	 * @since   1.5.1
	 */
	function add_import_mapping_option( $options ) {
		$options['alg_wc_cog_cost'] = __( 'Cost', 'cost-of-goods-for-woocommerce' );
		return $options;
	}

	/**
	 * add_export_column.
	 *
	 * @version 1.5.1
	 * @since   1.5.1
	 */
	function add_export_column( $columns ) {
		$columns['alg_wc_cog_cost'] = __( 'Cost', 'cost-of-goods-for-woocommerce' );
		return $columns;
	}

	/**
	 * add_export_data.
	 *
	 * @version 1.5.1
	 * @since   1.5.1
	 */
	function add_export_data( $value, $product ) {
		return $this->get_product_cost( $product->get_id() );
	}

	/**
	 * product_sortable_columns.
	 *
	 * @version 2.3.4
	 * @since   1.7.0
	 * @todo    [next] add `profit` to the sortable columns
	 */
	function product_sortable_columns( $columns ) {
		if ( ! apply_filters( 'alg_wc_cog_create_product_columns_validation', true ) ) {
			return $columns;
		}
		foreach ( $this->product_columns as $column_id => $column_title ) {
			if ( 'profit' != $column_id ) {
				$columns[ $column_id ] = '_alg_wc_cog_' . $column_id;
			}
		}
		return $columns;
	}

	/**
	 * product_pre_get_posts_order_by_column.
	 *
	 * @version 2.1.0
	 * @since   1.7.0
	 */
	function product_pre_get_posts_order_by_column( $query ) {
		alg_wc_cog_pre_get_posts_order_by_column( $query, 'product', $this->is_columns_sorting_exclude_empty_lines );
	}

	/**
	 * add_product_columns.
	 *
	 * @version 2.3.4
	 * @since   1.0.0
	 */
	function add_product_columns( $columns ) {
		if ( ! apply_filters( 'alg_wc_cog_create_product_columns_validation', true ) ) {
			return $columns;
		}
		$this->product_columns = array();
		if ( $this->is_column_cost ) {
			$this->product_columns['cost']   = __( 'Cost', 'cost-of-goods-for-woocommerce' );
		}
		if ( $this->is_column_profit ) {
			$this->product_columns['profit'] = __( 'Profit', 'cost-of-goods-for-woocommerce' );
		}
		return alg_wc_cog_insert_in_array( $columns, $this->product_columns, 'price' );
	}

	/**
	 * render_product_columns.
	 *
	 * @version 2.1.0
	 * @since   1.0.0
	 */
	function render_product_columns( $column, $product_id ) {
		if ( 'profit' === $column || 'cost' === $column ) {
			echo ( 'cost' === $column ?
				$this->get_product_cost_html( $product_id ) :
				$this->get_product_profit_html( $product_id, $this->product_profit_html_template ) );
		}
	}

	/**
	 * get_product_cost.
	 *
	 * @version 2.4.2
	 * @since   1.0.0
	 */
	function get_product_cost( $product_id, $args = null ) {
		$args = wp_parse_args( $args, array(
			'check_parent_cost' => true, // Check parent id cost if cost from product id is empty
		) );
		if (
			'' === ( $cost = get_post_meta( $product_id, '_alg_wc_cog_cost', true ) )
			&& $args['check_parent_cost']
			&& $product_id
			&& ( $product = wc_get_product( $product_id ) )
			&& ( $parent_id = $product->get_parent_id() )
		) {
			$cost = get_post_meta( $parent_id, '_alg_wc_cog_cost', true );
		}
		return apply_filters( 'alg_wc_cog_get_product_cost', (float) str_replace( ',', '.', $cost ), $product_id, isset( $parent_id ) ? $parent_id : null, $args );
	}

	/**
	 * get_product_handling_fee.
	 *
	 * @version 2.4.5
	 * @since   2.4.5
	 */
	function get_product_handling_fee( $product_id, $args = null ) {
		$args = wp_parse_args( $args, array(
			'check_parent_handling_fee' => true, // Check parent id handling_fee if handling_fee from product id is empty
		) );
		if (
			'' === ( $handling_fee = get_post_meta( $product_id, '_alg_wc_cog_handling_fee', true ) )
			&& $args['check_parent_handling_fee']
			&& $product_id
			&& ( $product = wc_get_product( $product_id ) )
			&& ( $parent_id = $product->get_parent_id() )
		) {
			$handling_fee = get_post_meta( $parent_id, '_alg_wc_cog_handling_fee', true );
		}
		return apply_filters( 'alg_wc_cog_get_product_handling_fee', (float) str_replace( ',', '.', $handling_fee ), $product_id, isset( $parent_id ) ? $parent_id : null, $args );
	}

	/**
	 * get_product_price.
	 *
	 * @version 2.4.7
	 * @since   2.3.9
	 *
	 * @param $product
	 * @param null $args
	 *
	 * @return mixed
	 */
	function get_product_price( $product, $args = null ) {
		$args   = wp_parse_args( $args, array(
			'method'               => get_option( 'alg_wc_cog_products_get_price_method', 'wc_get_price_excluding_tax' ),
			'params'               => array(),
			'return_zero_if_empty' => false
		) );
		$params = array_merge( array( $product ), $args['params'] );
		$return = call_user_func_array( $args['method'], $params );
		return $args['return_zero_if_empty'] && empty( $return ) ? 0 : $return;
	}

	/**
	 * get_product_cost_html.
	 *
	 * @version 2.4.0
	 * @since   1.0.0
	 */
	function get_product_cost_html( $product_id ) {
		$product = wc_get_product( $product_id );
		if ( $product->is_type( 'variable' ) ) {
			return $this->get_variable_product_html( $product_id, 'cost', '%cost%' );
		} else {
			return ( '' === ( $cost = $this->get_product_cost( $product_id ) ) ? '' : alg_wc_cog_format_cost( $cost ) );
		}
	}

	/**
	 * get_product_profit.
	 *
	 * @version 2.3.9
	 * @since   1.0.0
	 * @todo    [next] maybe check if `wc_get_price_excluding_tax()` is numeric (e.g. maybe can return range)
	 */
	function get_product_profit( $product_id ) {
		$product = wc_get_product( $product_id );
		return ( '' === ( $cost = $this->get_product_cost( $product_id ) ) || '' === ( $price = $this->get_product_price( $product ) ) ? '' : $price - $cost );
	}

	/**
	 * get_product_profit_html.
	 *
	 * @version 2.3.9
	 * @since   1.0.0
	 */
	function get_product_profit_html( $product_id, $template = '%profit% (%profit_percent%)' ) {
		$product = wc_get_product( $product_id );
		if ( $product->is_type( 'variable' ) ) {
			return $this->get_variable_product_html( $product_id, 'profit', $template );
		} else {
			if ( '' === ( $profit = $this->get_product_profit( $product_id ) ) ) {
				return '';
			} else {
				$placeholders = array(
					'%profit%'         => wc_price( $profit ),
					'%profit_percent%' => sprintf( '%0.2f%%', ( 0 != ( $cost  = $this->get_product_cost( $product_id ) ) ? $profit / $cost  * 100 : '' ) ),
					'%profit_margin%'  => sprintf( '%0.2f%%', ( 0 != ( $price = $this->get_product_price( $product ) ) ? $profit / $price * 100 : '' ) ),
				);
				return str_replace( array_keys( $placeholders ), $placeholders, $template );
			}
		}
	}

	/**
	 * get_variable_product_html.
	 *
	 * @version 2.4.7
	 * @since   1.0.0
	 * @todo    [maybe] use `get_available_variations()` instead of `get_children()`?
	 */
	function get_variable_product_html( $product_id, $profit_or_cost, $template ) {
		$product = wc_get_product( $product_id );
		$data    = array();
		foreach ( $product->get_children() as $variation_id ) {
			$data[ $variation_id ] = ( 'profit' === $profit_or_cost ? $this->get_product_profit( $variation_id ) : $this->get_product_cost( $variation_id ) );
		}
		if ( empty( $data ) ) {
			return '';
		} else {
			asort( $data );
			if ( 'profit' === $profit_or_cost ) {
				$product_ids    = array_keys( $data );
				$product_id_min = current( $product_ids );
				$product_id_max = end(     $product_ids );
			}
			$min = (float) current( $data );
			$max = (float) end( $data );
			$placeholders = array();
			if ( $min !== $max ) {
				$placeholders[ "%{$profit_or_cost}%" ] = wc_format_price_range( $min, $max );
				if ( 'profit' === $profit_or_cost ) {
					$cost_min                         = $this->get_product_cost( $product_id_min );
					$cost_max                         = $this->get_product_cost( $product_id_max );
					$profit_min                       = ( 0 != $cost_min ? $min / $cost_min * 100 : '' );
					$profit_max                       = ( 0 != $cost_max ? $max / $cost_max * 100 : '' );
					$price_min                        = $this->get_product_price( wc_get_product( $product_id_min ), array( 'return_zero_if_empty' => true ) );
					$price_max                        = $this->get_product_price( wc_get_product( $product_id_max ), array( 'return_zero_if_empty' => true ) );
					$margin_min                       = ( 0 != $price_min && '' !== $min ? $min / $price_min * 100 : '' );
					$margin_max                       = ( 0 != $price_max && '' !== $max ? $max / $price_max * 100 : '' );
					$placeholders['%profit_percent%'] = sprintf( '%0.2f%% &ndash; %0.2f%%', $profit_min, $profit_max );
					$placeholders['%profit_margin%']  = sprintf( '%0.2f%% &ndash; %0.2f%%', $margin_min, $margin_max );
				}
			} else {
				$placeholders[ "%{$profit_or_cost}%" ] = wc_price( $min );
				if ( 'profit' === $profit_or_cost ) {
					$cost                             = (float) $this->get_product_cost( $product_id_min );
					$price                            = $this->get_product_price( wc_get_product( $product_id_min ) );
					$placeholders['%profit_percent%'] = sprintf( '%0.2f%%', ( 0 != $cost ? $min / $cost * 100 : '' ) );
					$placeholders['%profit_margin%']  = sprintf( '%0.2f%%', ( 0 != $price && '' !== $min ? $min / $price * 100 : '' ) );
				}
			}
			return str_replace( array_keys( $placeholders ), $placeholders, $template );
		}
	}

	/**
	 * add_cost_input.
	 *
	 * @version 2.3.4
	 * @since   1.0.0
	 * @todo    [later] rethink `$product_id` (and search all code for `get_the_ID()`)
	 * @todo    [maybe] min_profit
	 */
	function add_cost_input() {
		if ( ! apply_filters( 'alg_wc_cog_create_product_meta_box_validation', true ) ) {
			return;
		}
		$product_id = get_the_ID();
		woocommerce_wp_text_input( array(
			'id'          => '_alg_wc_cog_cost',
			'value'       => wc_format_localized_price( $this->get_product_cost( $product_id ) ),
			'data_type'   => 'price',
			'label'       => str_replace( '%currency_symbol%', alg_wc_cog()->core->get_default_shop_currency_symbol(), $this->cost_field_template ),
			'description' => sprintf( __( 'Profit: %s', 'cost-of-goods-for-woocommerce' ),
				( '' != ( $profit = $this->get_product_profit_html( $product_id, $this->product_profit_html_template ) ) ? $profit : __( 'N/A', 'cost-of-goods-for-woocommerce' ) ) ),
		) );
	}

	/**
	 * add_cost_input_variable.
	 *
	 * @version 1.0.1
	 * @since   1.0.0
	 * @todo    [fix] this is not showing when creating *new* variable product
	 * @todo    [maybe] move this to "Inventory" tab
	 */
	function add_cost_input_variable() {
		if ( ( $product = wc_get_product() ) && $product->is_type( 'variable' ) ) {
			echo '<div class="options_group show_if_variable">';
			$this->add_cost_input();
			echo '</div>';
		}
	}

	/**
	 * add_cost_input_variation.
	 *
	 * @version 2.3.4
	 * @since   1.0.0
	 */
	function add_cost_input_variation( $loop, $variation_data, $variation ) {
		if ( ! apply_filters( 'alg_wc_cog_create_product_meta_box_validation', true ) ) {
			return;
		}
		if (
			! isset( $variation_data['_alg_wc_cog_cost'][0] ) ||
			empty( $value = $variation_data['_alg_wc_cog_cost'][0] )
		) {
			$product           = wc_get_product( $variation->ID );
			$parent_product_id = $product->get_parent_id();
			$value             = $this->get_product_cost( $parent_product_id, array( 'check_parent_cost' => false ) );
		}
		woocommerce_wp_text_input( array(
			'id'            => "variable_alg_wc_cog_cost_{$loop}",
			'name'          => "variable_alg_wc_cog_cost[{$loop}]",
			'value'         => wc_format_localized_price( $value ),
			'label'         => str_replace( '%currency_symbol%', alg_wc_cog()->core->get_default_shop_currency_symbol(), $this->cost_field_template ),
			'data_type'     => 'price',
			'wrapper_class' => 'form-row form-row-full',
			'description'   => sprintf( __( 'Profit: %s', 'cost-of-goods-for-woocommerce' ),
				( '' != ( $profit = $this->get_product_profit_html( $variation->ID, $this->product_profit_html_template ) ) ? $profit : __( 'N/A', 'cost-of-goods-for-woocommerce' ) ) ),
		) );
	}

	/**
	 * save_cost_input.
	 *
	 * @version 1.7.0
	 * @since   1.0.0
	 * @todo    [next] maybe pre-calculate and save `_alg_wc_cog_profit` (same in `save_cost_input_variation()`)
	 */
	function save_cost_input( $product_id, $__post ) {
		if ( isset( $_POST['_alg_wc_cog_cost'] ) ) {
			update_post_meta( $product_id, '_alg_wc_cog_cost', wc_clean( $_POST['_alg_wc_cog_cost'] ) );
		}
	}

	/**
	 * save_cost_input_variation.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function save_cost_input_variation( $variation_id, $i ) {
		if ( isset( $_POST['variable_alg_wc_cog_cost'][ $i ] ) ) {
			update_post_meta( $variation_id, '_alg_wc_cog_cost', wc_clean( $_POST['variable_alg_wc_cog_cost'][ $i ] ) );
		}
	}

	/**
	 * update_product_price.
	 *
	 * @version 2.5.1
	 * @since   2.5.1
	 *
	 * @param null $args
	 *
	 * @return bool
	 */
	function update_product_cost_by_percentage( $args = null ) {
		$args              = wp_parse_args( $args, array(
			'product_id'        => '',
			'percentage'        => 100,
			'update_type'       => 'price', // profit | price
			'update_variations' => true
		) );
		$percentage        = $args['percentage'];
		$product_id        = $args['product_id'];
		$product           = wc_get_product( $product_id );
		$update_variations = $args['update_variations'];
		$update_type       = $args['update_type'];
		if ( ! is_a( $product, 'WC_Product' ) ) {
			return false;
		}
		update_post_meta( $product->get_id(), '_alg_wc_cog_cost', $this->calculate_product_cost_by_percentage( $product->get_price(), $percentage, $update_type ) );
		if (
			$update_variations &&
			$product->is_type( 'variable' ) && $product instanceof WC_Product_Variable
		) {
			foreach ( $product->get_available_variations() as $variation ) {
				$variation_id  = isset( $variation['variation_id'] ) ? $variation['variation_id'] : '';
				$display_price = isset( $variation['display_price'] ) ? $variation['display_price'] : '';
				if ( empty( $variation_id ) || empty( $display_price ) ) {
					continue;
				}
				update_post_meta( $variation_id, '_alg_wc_cog_cost', $this->calculate_product_cost_by_percentage( $display_price, $percentage, $update_type ) );
			}
		}
		return true;
	}

	/**
	 * calculate_product_cost.
	 *
	 * @version 2.5.1
	 * @since   2.5.1
	 *
	 * @param $price
	 * @param $percentage
	 * @param string $method price | profit
	 *
	 * @return float|int
	 */
	function calculate_product_cost_by_percentage( $price, $percentage, $method ) {
		if ( empty( $price ) ) {
			return 0;
		}
		if ( 'profit' === $method ) {
			// Profit percent.
			return $price / ( ( 100 + $percentage ) / 100 );
			// Profit marging.
			//return $price - ( ( $price * $percentage ) / 100 );
		}
		return ( $price * $percentage ) / 100;
	}

	
}

endif;

return new Alg_WC_Cost_of_Goods_Products();
