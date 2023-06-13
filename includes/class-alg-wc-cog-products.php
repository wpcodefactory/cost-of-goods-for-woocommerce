<?php
/**
 * Cost of Goods for WooCommerce - Products Class.
 *
 * @version 2.9.6
 * @since   2.1.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Products' ) ) :

class Alg_WC_Cost_of_Goods_Products {

	/**
	 * Product profit html template.
	 *
	 * @since 2.9.4
	 */
	public $product_profit_html_template;

	/**
	 * Is column cost.
	 *
	 * @since 2.9.4
	 */
	public $is_column_cost;

	/**
	 * Is column profit.
	 *
	 * @since 2.9.4
	 */
	public $is_column_profit;

	/**
	 * Is columns sorting.
	 *
	 * @since 2.9.4
	 */
	public $is_columns_sorting;

	/**
	 * Is column sorting exclude empty lines.
	 *
	 * @since 2.9.4
	 */
	public $is_columns_sorting_exclude_empty_lines;

	/**
	 * Products columns.
	 *
	 * @since 2.9.4
	 */
	public $product_columns;

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
		$this->product_profit_html_template           = get_option( 'alg_wc_cog_product_profit_html_template', '%profit% (%profit_percent%)' );
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
	 * @version 2.8.2
	 * @since   2.1.0
	 */
	function add_hooks() {

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
		// Sanitize cog meta (_alg_wc_cog_cost)
		add_filter( 'sanitize_post_meta_' . '_alg_wc_cog_cost', array( $this, 'sanitize_cog_meta' ) );
		// Save profit.
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
	 * @version 2.9.5
	 * @since   2.3.5
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	function sanitize_cog_meta( $value ) {
		if ( 'yes' === get_option( 'alg_wc_cog_products_sanitize_cog_meta', 'no' ) ) {
			$value = alg_wc_cog_sanitize_number( array(
				'number'                    => $value,
				'dots_and_commas_operation' => 'comma-to-dot'
			) );
		}

		return $value;
	}

	/**
	 * parse_import_data.
	 *
	 * @version 2.7.0
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
		if ( isset( $data['meta_data'] ) && is_array( $data['meta_data'] ) && ! empty( $data['meta_data'] ) ) {
			foreach ( $data['meta_data'] as $key => $value ) {
				if ( '_alg_wc_cog_cost' === $value['key'] ) {
					$data['meta_data'][ $key ]['value'] = 'yes' === get_option( 'alg_wc_cog_import_csv_get_only_cost_number', 'no' ) ? $this->get_only_number( $value['value'] ) : $value['value'];
				}
			}
		}
		return $data;
	}

	/**
	 * get_only_digits_points_and_commas.
	 *
	 * @version 2.7.0
	 * @since   2.7.0
	 *
	 * @param $str
	 *
	 * @return mixed
	 */
	function get_only_number( $str ) {
		return preg_match( '/[\d.,]+/', $str, $output_array ) ? $output_array[0] : $str;
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
	 * @version 2.8.5
	 * @since   1.5.1
	 */
	function add_export_data( $value, $product ) {
		$cost = $this->get_product_cost( $product->get_id(), array(
			'convert_to_number'         => 'yes' === $convert_cost_to_number = get_option( 'alg_wc_cog_product_export_csv_convert_cost_to_number', 'yes' ),
			'dots_and_commas_operation' => get_option( 'alg_wc_cog_product_export_csv_dots_and_commas_operation', 'comma-to-dot' )
		) );
		$cost = $convert_cost_to_number ? $cost : (string) $cost;
		return $cost;
	}

	/**
	 * product_sortable_columns.
	 *
	 * @version 2.6.7
	 * @since   1.7.0
	 */
	function product_sortable_columns( $columns ) {
		if ( ! apply_filters( 'alg_wc_cog_create_product_columns_validation', true ) ) {
			return $columns;
		}
		foreach ( $this->product_columns as $column_id => $column_title ) {
			$columns[ $column_id ] = '_alg_wc_cog_' . $column_id;
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
	 * @version 2.9.5
	 * @since   1.0.0
	 */
	function get_product_cost( $product_id, $args = null ) {
		$args = wp_parse_args( $args, array(
			'check_parent_cost'         => true, // Check parent id cost if cost from product id is empty
			'dots_and_commas_operation' => 'comma-to-dot', // comma-to-dot | dot-to-comma | none
			'convert_to_number'         => true
		) );
		$dots_and_commas_operation = $args['dots_and_commas_operation'];
		$convert_to_number = $args['convert_to_number'];
		if (
			'' === ( $cost = get_post_meta( $product_id, '_alg_wc_cog_cost', true ) )
			&& $args['check_parent_cost']
			&& $product_id
			&& ( $product = wc_get_product( $product_id ) )
			&& ( $parent_id = $product->get_parent_id() )
		) {
			$cost = get_post_meta( $parent_id, '_alg_wc_cog_cost', true );
		}
		$cost = alg_wc_cog_sanitize_number( array(
			'number'                    => $cost,
			'dots_and_commas_operation' => $dots_and_commas_operation
		) );
		$cost = $convert_to_number ? (float) $cost : $cost;
		return apply_filters( 'alg_wc_cog_get_product_cost', $cost, $product_id, isset( $parent_id ) ? $parent_id : null, $args );
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
	 * @version 2.9.9
	 * @since   2.3.9
	 *
	 * @param $product
	 * @param null $args
	 *
	 * @return mixed
	 */
	function get_product_price( $product, $args = null ) {

		// Check if $product is a valid product object. If not, try loading it.
		if ( !( $product instanceof WC_Product ) ) {
			$product_id = $product;
			$product = wc_get_product( $product_id );
			
			// If it's still not a valid product object, log an error and return false
			if ( !( $product instanceof WC_Product ) ) {
				error_log( "Invalid product ID: $product_id" );
				return false; // or you might return a default value instead
			}
		}

		$args   = wp_parse_args( $args, array(
			'method'               => get_option( 'alg_wc_cog_products_get_price_method', 'wc_get_price_excluding_tax' ),
			'params'               => array(),
			'return_zero_if_empty' => false
		) );
		$params = array_merge( array( $product ), $args['params'] );
		$return = call_user_func_array( $args['method'], $params );
		return $args['return_zero_if_empty'] && empty( $return ) ? 0 : (float) $return;

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
	 * @version 2.9.6
	 * @since   1.0.0
	 */
	function get_product_profit_html( $product_id, $template = '%profit% (%profit_percent%)' ) {
		$product = wc_get_product( $product_id );
		if ( is_a( $product, 'WC_Product' ) ) {
			if ( $product->is_type( 'variable' ) ) {
				return $this->get_variable_product_html( $product_id, 'profit', $template );
			} else {
				if ( '' === ( $profit = $this->get_product_profit( $product_id ) ) ) {
					return '';
				} else {
					$placeholders = array(
						'%profit%'         => wc_price( $profit ),
						'%profit_percent%' => sprintf( '%0.2f%%', ( 0 != ( $cost = $this->get_product_cost( $product_id ) ) ? $profit / $cost * 100 : '' ) ),
						'%profit_margin%'  => sprintf( '%0.2f%%', ( 0 != ( $price = $this->get_product_price( $product ) ) ? $profit / $price * 100 : '' ) ),
					);

					return str_replace( array_keys( $placeholders ), $placeholders, $template );
				}
			}
		} else {
			return '';
		}
	}

	/**
	 * get_variable_product_html.
	 *
	 * @version 2.9.4
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
					$cost_min                         = (float) $this->get_product_cost( $product_id_min );
					$cost_max                         = (float) $this->get_product_cost( $product_id_max );
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
	 * Update product price by product cost.
	 *
	 * @version 2.9.0
	 * @since   2.6.3
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	function update_product_price_by_profit( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'product_id'        => '',
			'percentage'        => '',
			'absolute_profit'   => '',
			'rounding'          => '',
			'affected_field'    => 'regular_price',
			'update_variations' => true
		) );
		$product_id        = $args['product_id'];
		$percentage        = $args['percentage'];
		$absolute_profit   = $args['absolute_profit'];
		$rounding          = $args['rounding'];
		$affected_field    = $args['affected_field'];
		$update_variations = $args['update_variations'];
		$product           = wc_get_product( $product_id );
		$product_cost      = alg_wc_cog()->core->products->get_product_cost( $product->get_id() );
		$new_price         = 0;
		// Update variations recursively.
		if (
			$update_variations &&
			$product->is_type( 'variable' ) && $product instanceof WC_Product_Variable
		) {
			foreach ( $product->get_available_variations() as $variation ) {
				if ( ! empty( $variation_id = isset( $variation['variation_id'] ) ? $variation['variation_id'] : '' ) ) {
					$new_args               = $args;
					$new_args['product_id'] = $variation_id;
					$this->update_product_price_by_profit( $new_args );
				}
			}
		}
		// If invalid product or product cost then return false.
		if ( empty( $product_cost ) || 0 == $product_cost ) {
			return false;
		}
		// Calculate price by cost.
		if ( ! empty( $percentage ) ) {
			$new_price = $product_cost + ( $product_cost * ( $percentage / 100 ) );
		} elseif ( ! empty( $absolute_profit ) ) {
			$new_price = $product_cost + $absolute_profit;
		}
		// If no new price, then return false.
		if ( 0 >= $new_price ) {
			return false;
		}
		// Rounding.
		if ( ! empty( $rounding ) ) {
			$new_price = 'round' === $rounding ? round( $new_price ) : ( 'round_up' === $rounding ? ceil( $new_price ) : floor( $new_price ) );
		}
		if ( 'sale_price' == $affected_field ) {
			$product->set_sale_price( $new_price );
		} else {
			$product->set_regular_price( $new_price );
		}
		$product->save();
		return true;
	}

	/**
     * update_variation_cost_from_parent.
     *
	 * @version 2.9.5
	 * @since   2.9.5
     *
	 * @param $args
	 *
	 * @return void
	 */
	function update_variation_cost_from_parent( $args = null ) {
		$args       = wp_parse_args( $args, array(
			'product_id' => '',
		) );
		$product_id = $args['product_id'];
		$product    = wc_get_product( $product_id );
		$parent_id  = $product->get_parent_id();
		update_post_meta( $product_id, '_alg_wc_cog_cost', $this->get_product_cost( $parent_id, array(
			'check_parent_cost' => false
		) ) );
	}

	/**
	 * update_product_price.
	 *
	 * @version 2.9.5
	 * @since   2.5.1
	 *
	 * @param null $args
	 *
	 * @return bool
	 */
	function update_product_cost_by_percentage( $args = null ) {
		$args              = wp_parse_args( $args, array(
			'product_id'        => '',
			'costs_filter'      => 'ignore_costs', // ignore_costs, products_without_costs, products_with_costs
			'percentage'        => 100,
			'update_type'       => 'costs_price', // costs_profit | costs_price
			'update_variations' => true
		) );
		$percentage        = $args['percentage'];
		$product_id        = $args['product_id'];
		$product           = wc_get_product( $product_id );
		$update_variations = $args['update_variations'];
		$update_type       = $args['update_type'];
        $costs_filter      = $args['costs_filter'];
		if ( ! is_a( $product, 'WC_Product' ) ) {
			return false;
		}
		if ( $this->can_update_product_cost_based_on_costs_filter( $product_id, $costs_filter ) ) {
			update_post_meta( $product->get_id(), '_alg_wc_cog_cost', $this->calculate_product_cost_by_percentage( $product->get_price(), $percentage, $update_type ) );
		}
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
				if ( $this->can_update_product_cost_based_on_costs_filter( $variation_id, $costs_filter ) ) {
					update_post_meta( $variation_id, '_alg_wc_cog_cost', $this->calculate_product_cost_by_percentage( $display_price, $percentage, $update_type ) );
				}
			}
		}
		return true;
	}

	/**
	 * can_update_product_cost.
	 *
	 * @version 2.9.5
	 * @since   2.9.5
     *
	 * @param $product_id
	 * @param $costs_filter
	 *
	 * @return bool
	 */
	function can_update_product_cost_based_on_costs_filter( $product_id, $costs_filter ): bool {
		$can_update   = false;
		if (
			( 'products_without_costs' === $costs_filter && empty( $this->get_product_cost( $product_id, array( 'check_parent_cost' => false ) ) ) ) ||
			( 'products_with_costs' === $costs_filter && ! empty( $this->get_product_cost( $product_id, array( 'check_parent_cost' => false ) ) ) ) ||
			( 'ignore_costs' === $costs_filter )
		) {
			$can_update = true;
		}

		return $can_update;
	}

	/**
	 * calculate_product_cost.
	 *
	 * @version 2.6.3
	 * @since   2.5.1
	 *
	 * @param $price
	 * @param $percentage
	 * @param string $method costs_price | costs_profit
	 *
	 * @return float|int
	 */
	function calculate_product_cost_by_percentage( $price, $percentage, $method ) {
		if ( empty( $price ) ) {
			return 0;
		}
		if ( 'costs_profit' === $method ) {
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
