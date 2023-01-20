<?php
/**
 * Cost of Goods for WooCommerce - Functions.
 *
 * @version 2.8.7
 * @since   1.4.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'alg_wc_cog_log' ) ) {
	/**
	 * alg_wc_cog_log.
	 *
	 * @version 2.1.0
	 * @since   1.6.0
	 */
	function alg_wc_cog_log( $message ) {
		if ( function_exists( 'wc_get_logger' ) && ( $log = wc_get_logger() ) ) {
			$log->log( 'info', $message, array( 'source' => 'alg-wc-cog' ) );
		}
	}
}

if ( ! function_exists( 'alg_wc_cog_pre_get_posts_order_by_column' ) ) {
	/**
	 * alg_wc_cog_pre_get_posts_order_by_column.
	 *
	 * @version 2.6.7
	 * @since   1.7.0
	 */
	function alg_wc_cog_pre_get_posts_order_by_column( $query, $post_type, $do_exclude_empty_lines ) {
		if (
			$query->is_main_query() && ( $orderby = $query->get( 'orderby' ) ) &&
			isset( $query->query['post_type'] ) && $post_type === $query->query['post_type'] &&
			isset( $query->is_admin ) && 1 == $query->is_admin
		) {
			switch ( $orderby ) {
				case '_alg_wc_cog_profit':
					$orderby = '_alg_wc_cog_profit_percent';
					break;
			}
			$key_fragment = '_alg_wc_cog_';
			if ( $key_fragment === substr( $orderby, 0, strlen( $key_fragment ) ) ) {
				if ( $do_exclude_empty_lines ) {
					$query->set( 'meta_key', $orderby );
				} else {
					$query->set( 'meta_query', array(
						'relation' => 'OR',
						array(
							'key'     => $orderby,
							'compare' => 'NOT EXISTS'
						),
						array(
							'key'     => $orderby,
							'compare' => 'EXISTS'
						),
					) );
				}
				$query->set( 'orderby', 'meta_value_num ID' );
			}
		}
	}
}

if ( ! function_exists( 'alg_wc_cog_insert_in_array' ) ) {
	/**
	 * alg_wc_cog_insert_in_array.
	 *
	 * @version 2.1.0
	 * @since   1.7.0
	 */
	function alg_wc_cog_insert_in_array( $original_array, $array_to_insert, $key_to_insert_after ) {
		if ( empty( $array_to_insert ) ) {
			return $original_array;
		}
		$result   = array();
		$is_found = false;
		foreach ( $original_array as $key => $title ) {
			$result[ $key ] = $title;
			if ( $key_to_insert_after === $key ) {
				$result   = array_merge( $result, $array_to_insert );
				$is_found = true;
			}
		}
		return ( $is_found ? $result : array_merge( $result, $array_to_insert ) );
	}
}

if ( ! function_exists( 'alg_wc_cog_get_table_html' ) ) {
	/**
	 * alg_wc_cog_get_table_html.
	 *
	 * @version 2.3.5
	 * @since   1.0.0
	 */
	function alg_wc_cog_get_table_html( $data, $args = array() ) {
		$args = array_merge( array(
			'table_class'        => '',
			'table_style'        => '',
			'row_styles'         => '',
			'table_heading_type' => 'horizontal',
			'columns_classes'    => array(),
			'columns_styles'     => array(),
			'table_attributes'   => array(),
		), $args );
		// Custom attribute handling.
		$table_attributes = array();
		if ( ! empty( $args['table_attributes'] ) ) {
			foreach ( $args['table_attributes'] as $attribute => $attribute_value ) {
				$table_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}
		$html = '';
		$html .= '<table' . ( '' == $args['table_class']  ? '' : ' class="' . $args['table_class'] . '"' ) .
	         ' '.implode( ' ', $table_attributes ).
			( '' == $args['table_style']  ? '' : ' style="' . $args['table_style'] . '"' ) . '>';
		$html .= '<tbody>';
		$row_styles = ( '' == $args['row_styles'] ? '' : ' style="' . $args['row_styles']  . '"' );
		foreach( $data as $row_number => $row ) {
			$html .= '<tr' . $row_styles . '>';
			foreach( $row as $column_number => $value ) {
				$th_or_td     = ( ( 0 === $row_number && 'horizontal' === $args['table_heading_type'] ) || ( 0 === $column_number && 'vertical' === $args['table_heading_type'] ) ?
					'th' : 'td' );
				$column_class = ( isset( $args['columns_classes'][ $column_number ] ) ? ' class="' . $args['columns_classes'][ $column_number ] . '"' : '' );
				$column_style = ( isset( $args['columns_styles'][ $column_number ] )  ? ' style="' . $args['columns_styles'][ $column_number ]  . '"' : '' );
				$html .= '<' . $th_or_td . $column_class . $column_style . '>';
				$html .= $value;
				$html .= '</' . $th_or_td . '>';
			}
			$html .= '</tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}
}

if ( ! function_exists( 'alg_wc_cog_format_cost' ) ) {
	/**
	 * alg_wc_cog_format_cost.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 *
	 * @param float $cost Raw price.
	 * @param array $args
	 *
	 * @return string
	 */
	function alg_wc_cog_format_cost( $cost, $args = array() ) {
		$args = wp_parse_args( $args, array(
			'decimals' => get_option( 'alg_wc_cog_costs_decimals', wc_get_price_decimals() )
		) );
		return wc_price( $cost, $args );
	}
}

if ( ! function_exists( 'alg_wc_cog_is_user_allowed' ) ) {
	/**
	 * alg_wc_cog_is_user_allowed.
	 *
	 * @version 2.3.4
	 * @since   2.3.4
	 */
	function alg_wc_cog_is_user_allowed( $user = null ) {
		$user = ( null != $user ) ? $user : ( is_user_logged_in() ? wp_get_current_user() : null );
		if (
			! $user ||
			in_array( 'administrator', $user->roles ) ||
			empty( $allowed_user_roles = get_option( 'alg_wc_cog_allowed_user_roles', array() ) )
		) {
			return true;
		}
		if ( count( array_intersect( $allowed_user_roles, $user->roles ) ) > 0 ) {
			return true;
		}
		return false;
	}
}

if ( ! function_exists( 'alg_wc_cog_array_to_string' ) ) {
	/**
	 * converts array to string.
	 *
	 * @version 2.4.2
	 * @since   2.4.2
	 *
	 * @param $arr
	 * @param array $args
	 *
	 * @return string
	 */
	function alg_wc_cog_array_to_string( $arr, $args = array() ) {
		$args            = wp_parse_args( $args, array(
			'glue'          => ', ',
			'item_template' => '{value}' //  {key} and {value} allowed
		) );
		$transformed_arr = array_map( function ( $key, $value ) use ( $args ) {
			$item = str_replace( array( '{key}', '{value}' ), array( $key, $value ), $args['item_template'] );
			return $item;
		}, array_keys( $arr ), $arr );
		return implode( $args['glue'], $transformed_arr );
	}
}

if ( ! function_exists( 'alg_wc_cog_get_blocked_options_message' ) ) {
	/**
	 * alg_wc_cog_get_blocked_options_message.
	 *
	 * @version 2.5.1
	 * @since   2.5.1
	 *
	 * @return string
	 */
	function alg_wc_cog_get_blocked_options_message() {
		return sprintf( __( 'Disabled options can be unlocked using <a href="%s" target="_blank"><strong>%s</strong></a>', 'cost-of-goods-for-woocommerce' ), 'https://wpfactory.com/item/cost-of-goods-for-woocommerce/', __( 'Cost of Goods for WooCommerce Pro', 'cost-of-goods-for-woocommerce' ) );
	}
}

if ( ! function_exists( 'alg_wc_cog_get_regular_price' ) ) {
	/**
	 * alg_wc_cog_get_regular_price.
	 *
	 * @version 2.7.8
	 * @since   2.7.8
	 *
	 * @return string
	 */
	function alg_wc_cog_get_regular_price( $product, $args = null ) {
		$regular_price = 0;
		if ( is_a( $product, 'WC_Product' ) ) {
			$regular_price = $product->get_regular_price();
		}
		return $regular_price;
	}
}

if ( ! function_exists( 'alg_wc_cog_get_html_table_structure' ) ) {
	/**
	 * alg_wc_cog_get_html_table_structure.
	 *
	 * @version 2.8.2
	 * @since   2.8.2
	 *
	 * @return string
	 */
	function alg_wc_cog_get_html_table_structure( $args = null ) {
		// Args.
		$args          = wp_parse_args( $args, array(
			'table_classes'    => array(),
			'table_attributes' => array(),
			'cols'             => array(),
			'rows'             => array()
		) );
		$cols          = $args['cols'];
		$rows          = $args['rows'];
		$table_classes = $args['table_classes'];
		// Table classes.
		$table_classes_html = ! empty( $table_classes ) ? ' class="' . implode( " ", $table_classes ) . '"' : '';
		// Table attributes.
		$table_attributes      = $args['table_attributes'];
		$table_attributes_html = array();
		if ( ! empty( $args['table_attributes'] ) ) {
			foreach ( $args['table_attributes'] as $attribute => $attribute_value ) {
				$table_attributes_html[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}
		$html_table = '<table' . $table_classes_html . ' ' . implode( ' ', $table_attributes_html ) . '>';
		// Thead.
		$html_table .= '<thead><tr>';
		foreach ( $cols as $col_id => $col_label ) {
			$html_table .= '<th>' . $col_label . '</th>';
		}
		$html_table .= '</tr></thead>';
		// Tbody.
		$html_table .= '<tbody>';
		foreach ( $rows as $rows_content ) {
			$html_table .= '<tr>';
			foreach ( $rows_content['val_by_col'] as $col_value ) {
				$html_table .= '<td>' . $col_value . '</td>';
			}
			$html_table .= '</tr>';
		}
		$html_table .= '</tbody>';
		$html_table .= '</table>';
		return $html_table;
	}
}

if ( ! function_exists( 'alg_wc_cog_get_cost_subtracting_tax_rate' ) ) {
	/**
	 * get_cost_subtracting_tax_rate.
	 *
	 * @version 2.8.7
	 * @since   2.8.7
	 *
	 * @param null $args
	 *
	 * @return float|bool
	 */
	function alg_wc_cog_get_cost_subtracting_tax_rate( $args = null ) {
		$args       = wp_parse_args( $args, array(
			'product_id' => '',
			'cost'       => '',
		) );
		$cost       = (float) $args['cost'];
		$product_id = intval( $args['product_id'] );
		$product    = wc_get_product( $product_id );
		$tax        = new WC_Tax();
		if ( ! is_a( $product, 'WC_Product' ) ) {
			return false;
		}
		if (
			'none' === $product->get_tax_status() ||
			empty( $rates = $tax->get_rates_for_tax_class( $product->get_tax_class() ) ) ||
			empty( $tax_rate = array_shift( $rates ) ) ||
			! property_exists( $tax_rate, 'tax_rate' )
		) {
			return $cost;
		}
		return $cost / ( 1 + ( $tax_rate->tax_rate / 100 ) );
	}
}