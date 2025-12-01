<?php
/**
 * Cost of Goods for WooCommerce - Bulk Edit Attribute Filtering Class.
 *
 * @version 4.0.1
 * @since   4.0.1
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Bulk_Edit_Attr_Filtering' ) ) {


	class Alg_WC_Cost_of_Goods_Bulk_Edit_Attr_Filtering {

		/**
		 * Constructor.
		 *
		 * @version 4.0.1
		 * @since   4.0.1
		 */
		public function __construct() {

		}

		/**
		 * Init.
		 *
		 * @version 4.0.1
		 * @since   4.0.1
		 */
		function init() {
			add_filter( 'alg_wc_cog_bulk_edit_get_products_args', array( $this, 'get_products_args' ) );
			add_filter( 'alg_wc_cog_bulk_edit_get_child_products_args', array( $this, 'get_child_products_args' ) );
		}

		/**
		 * get_child_products_args.
		 *
		 * @version 4.0.1
		 * @since   4.0.1
		 *
		 * @param $args
		 *
		 * @return mixed
		 */
		function get_child_products_args( $args ) {
			$filtered_attr = $_POST['alg_wc_cog_bulk_edit_filtered_attr'] ?? [];
			$meta_query    = array();

			foreach ( $filtered_attr as $taxonomy => $term_ids ) {
				$taxonomy    = sanitize_key( $taxonomy );
				$clean_terms = array_filter( array_map( 'intval', (array) $term_ids ) );
				if ( empty( $clean_terms ) ) {
					continue;
				}

				$slugs = array();
				foreach ( $clean_terms as $term_id ) {
					$term = get_term( $term_id, $taxonomy );
					if ( $term && ! is_wp_error( $term ) ) {
						$slugs[] = $term->slug;
					}
				}

				if ( empty( $slugs ) ) {
					continue;
				}

				$meta_key     = 'attribute_' . $taxonomy;
				$meta_query[] = array(
					'key'     => $meta_key,
					'value'   => $slugs,
					'compare' => 'IN',
				);
			}

			if ( ! empty( $meta_query ) ) {
				if ( ! empty( $args['meta_query'] ) ) {
					$meta_query = array_merge( $args['meta_query'], $meta_query );
				}

				$args['meta_query'] = array_merge(
					array( 'relation' => 'AND' ),
					$meta_query
				);
			}

			return $args;
		}

		/**
		 * get_products_args.
		 *
		 * @version 4.0.1
		 * @since   4.0.1
		 *
		 * @param $args
		 *
		 * @return mixed
		 */
		function get_products_args( $args ) {
			$filtered_attr = $_POST['alg_wc_cog_bulk_edit_filtered_attr'] ?? array();
			$tax_query     = array();
			foreach ( $filtered_attr as $taxonomy => $term_ids ) {
				if ( empty( $term_ids ) ) {
					continue;
				}

				$taxonomy    = sanitize_key( $taxonomy );
				$clean_terms = array_map( 'intval', (array) $term_ids );
				$clean_terms = array_filter( $clean_terms );

				if ( empty( $clean_terms ) ) {
					continue;
				}

				$tax_query[] = [
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $clean_terms,
					'operator' => 'IN',
				];
			}

			if ( ! empty( $tax_query ) ) {
				if ( ! empty( $args['tax_query'] ) ) {
					$tax_query = array_merge( $args['tax_query'], $tax_query );
				}

				$args['tax_query'] = array_merge(
					array( 'relation' => 'AND' ),
					$tax_query
				);
			}

			return $args;
		}

		/**
		 * render_attributes.
		 *
		 * @version 4.0.1
		 * @since   4.0.1
		 *
		 * @return void
		 */
		function render_attributes() {
			$disabled = apply_filters( 'alg_wc_cog_settings', 'disabled' );
			$blocked_text = apply_filters( 'alg_wc_cog_settings', alg_wc_cog_get_blocked_options_message() );
			$taxes    = $this->get_selected_taxonomies();
			foreach ( $taxes as $taxonomy ) :

				$label = wc_attribute_label( $taxonomy );
				$id   = 'attr-' . esc_attr( $taxonomy );
				?>

				<tr>
					<th scope="row">
						<label for="<?php echo $id; ?>">
							<?php echo esc_html( sprintf( __( 'Filter by %s', 'cost-of-goods-for-woocommerce' ), $label ) ); ?>
						</label>
					</th>

					<td>
						<select
							data-return_id="id"
							<?php echo esc_attr( $disabled ); ?>
							class="wc-taxonomy-term-search"
							multiple="multiple"
							style="width: 50%;"
							id="<?php echo $id; ?>"
							name="alg_wc_cog_bulk_edit_filtered_attr[<?php echo esc_attr( $taxonomy ); ?>][]"
							data-placeholder="<?php echo esc_attr( sprintf( __( 'Search for a %s…', 'cost-of-goods-for-woocommerce' ), strtolower( $label ) ) ); ?>"
							data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>"
						></select>

						<p class="description">
							<?php echo esc_html( sprintf( __( 'Select the %s terms you want to filter.', 'cost-of-goods-for-woocommerce' ), strtolower( $label ) ) ); ?>
							<?php echo ( ! empty( $blocked_text ) ) ? '<br />' . $blocked_text : ''; ?>
						</p>
					</td>
				</tr>

			<?php endforeach;

		}

		/**
		 * get_selected_taxonomies.
		 *
		 * @version 4.0.1
		 * @since   4.0.1
		 *
		 * @return false|mixed|null
		 */
		function get_selected_taxonomies() {
			return alg_wc_cog_get_option( 'alg_wc_cog_bulk_edit_filterable_taxes', array() );
		}
	}

}