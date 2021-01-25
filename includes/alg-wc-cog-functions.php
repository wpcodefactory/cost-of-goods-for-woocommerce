<?php
/**
 * Cost of Goods for WooCommerce - Functions
 *
 * @version 2.3.5
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
	 * @version 2.1.0
	 * @since   1.7.0
	 */
	function alg_wc_cog_pre_get_posts_order_by_column( $query, $post_type, $do_exclude_empty_lines ) {
		if (
			$query->is_main_query() && ( $orderby = $query->get( 'orderby' ) ) &&
			isset( $query->query['post_type'] ) && $post_type === $query->query['post_type'] &&
			isset( $query->is_admin ) && 1 == $query->is_admin
		) {
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
