<?php
/**
 * Cost of Goods for WooCommerce - WP_List Bulk Edit Tool Class.
 *
 * @version 2.7.8
 * @since   2.3.1
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_WP_List_Bulk_Edit_Tool' ) ) :

	class Alg_WC_Cost_of_Goods_WP_List_Bulk_Edit_Tool extends \WP_List_Table {

		/**
		 * $need_to_edit_tags.
		 *
		 * @since 2.7.8
		 *
		 * @var null
		 */
		protected $need_to_edit_tags = null;

		/**
		 * prepare_items.
		 *
		 * @version 2.7.3
		 * @since   2.3.1
		 */
		public function prepare_items() {
			if ( ! empty( $this->items ) ) {
				return;
			}
			// Columns.
			$columns               = $this->get_columns();
			$hidden                = get_hidden_columns( $this->screen );
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );
			// Query args.
			$types    = get_option( 'alg_wc_cog_bulk_edit_tool_product_types', array() );
			$per_page = $this->get_items_per_page( 'alg_wc_cog_bulk_edit_per_page', 20 );
			$args     = array(
				'paginate'       => true,
				'tax_query'     => array(),
				'posts_per_page' => $per_page,
				'paged'          => isset( $_GET['paged'] ) ? filter_var( $_GET['paged'], FILTER_SANITIZE_NUMBER_INT ) : 1,
				'orderby'        => 'ID',
				'order'          => isset( $_GET['order'] ) ? strtoupper( sanitize_text_field( $_GET['order'] ) ) : 'ASC',
				'type'           => ( ! empty( $types ) ? $types : array_merge( array_keys( wc_get_product_types() ), array( 'variation' ) ) ),
			);
			// Search.
			if ( isset( $_REQUEST['s'] ) && ! empty( $search_query = $_REQUEST['s'] ) ) {
				if ( 'title' === get_option( 'alg_wc_cog_bulk_edit_tool_search_method', 'title' ) ) {
					$args['s'] = wc_clean( wp_unslash( $search_query ) );
				} else {
					$data_store        = WC_Data_Store::load( 'product' );
					$ids               = $data_store->search_products( wc_clean( wp_unslash( $search_query ) ), '', true, true );
					$post_in           = array_merge( $ids, array( 0 ) );
					$args['include'] = $post_in;
				}
			}
			// Tax query - Product tag.
			if ( isset( $_GET['product_tag'] ) && ! empty( $product_tag = sanitize_text_field( $_GET['product_tag'] ) ) ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'product_tag',
					'terms'    => array( esc_attr( $product_tag ) ),
					'field'    => 'slug',
				);
			}
			// Orderby.
			if (
				isset( $_GET['orderby'] ) &&
				! empty( $orderby = $_GET['orderby'] )
			) {
				switch ( $orderby ) {
					case 'title':
						$args['orderby']  = 'title';
						break;
					case 'id':
						$args['orderby']  = 'ID';
						break;
					case '_sku':
						$args['meta_key'] = '_sku';
						$args['orderby']  = 'meta_value';
						break;
					default:
						$args['meta_key'] = $orderby;
						$args['orderby']  = 'meta_value_num';
						break;
				}
			}
			// Data.
			$products = wc_get_products( $args );
			$this->set_pagination_args( [
				'total_items' => $products->total, //WE have to calculate the total number of items
				'per_page'    => count( $products->products ), //WE have to determine how many items to show on a page
				'total_pages' => $products->max_num_pages,
			] );
			$this->items = $products->products;
		}

		/**
		 * fix_paged_query_string_on_search_change.
		 *
		 * @version 2.7.1
		 * @since   2.7.1
		 */
		function fix_paged_query_string_on_search_change() {
			if ( ! session_id() ) {
				session_start();
			}

			if (
				isset( $_GET['paged'] ) &&
				1 !== (int) $_GET['paged'] &&
				isset( $_GET['s'] ) &&
				! empty( $current_search = $_GET['s'] ) &&
				(
					( ! isset( $_SESSION['alg_wc_cog_bulk_edit_cost_search'] ) || empty( $referer_search = $_SESSION['alg_wc_cog_bulk_edit_cost_search'] ) ) ||
					$referer_search != $current_search
				)
			) {
				wp_safe_redirect( add_query_arg( array(
					'paged' => 1,
				) ) );
				exit;
			}
			$_SESSION['alg_wc_cog_bulk_edit_cost_search'] = isset( $_GET['s'] ) ? $_GET['s'] : '';
		}

		/**
		 * An internal method that sets all the necessary pagination arguments.
		 *
		 * @version 2.7.1
		 * @since   2.7.1
		 *
		 * @param array|string $args
		 */
		protected function set_pagination_args( $args ) {
			parent::set_pagination_args( $args );
			$this->fix_paged_query_string_on_search_change();
		}

		/**
		 * Displays the table.
		 *
		 * @see WP_List_Table::display()
		 *
		 * @version 2.7.2
		 * @since   2.7.1
		 */
		function display() {
			$singular = $this->_args['singular'];

			$this->display_tablenav( 'top' );

			$this->screen->render_screen_reader_content( 'heading_list' );

			?>

			</form><form method="post">
			<table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
				<thead>
				<tr>
					<?php $this->print_column_headers(); ?>
				</tr>
				</thead>

				<tbody id="the-list"
					<?php
					if ( $singular ) {
						echo " data-wp-lists='list:$singular'";
					}
					?>
				>
				<?php $this->display_rows_or_placeholder(); ?>
				</tbody>

				<tfoot>
				<tr>
					<?php $this->print_column_headers( false ); ?>
				</tr>
				</tfoot>

			</table>
			<style>
				.alg-wc-cog-cost-description {
					display: block;
				}
			</style>
			<?php
			$this->display_tablenav( 'bottom' );
		}

		/**
		 * get_tags_edit_field.
		 *
		 * @version 2.7.8
		 * @since   2.7.8
		 *
		 * @param $post_id
		 *
		 * @return false|string
		 */
		function get_tags_edit_field( $post_id ) {
			ob_start();
			echo '<select data-return_id="id" class="wc-tag-search" multiple="multiple" style="width: 50%;" id="alg_wc_cog_bulk_edit_tool_product_tag" name="alg_wc_cog_bulk_edit_tool_product_tag[' . esc_attr( $post_id ) . '][]" data-placeholder="' . esc_attr( 'Search for a tag&hellip;', 'cost-of-goods-for-woocommerce' ) . '" data-action="json_search_tags">';
			$term_list = wp_get_post_terms( $post_id, 'product_tag', array( 'fields' => 'all' ) );
			foreach ( $term_list as $term ) {
				echo '<option value="' . esc_attr( $term->term_id ) . '"' . selected( true, true, false ) . '>' . esc_html( wp_strip_all_tags( $term->name ) ) . '</option>';
			}
			echo '</select>';
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}

		/**
		 * need_to_edit_tags.
		 *
		 * @version 2.7.8
		 * @since   2.7.8
		 *
		 * @return bool|null
		 */
		function need_to_edit_tags() {
			if ( is_null( $this->need_to_edit_tags ) ) {
				$this->need_to_edit_tags = 'yes' === get_option( 'alg_wc_cog_bulk_edit_tool_edit_tags', 'no' );
			}
			return $this->need_to_edit_tags;
		}

		/**
		 * column_default.
		 *
		 * @todo    [maybe] better description here and in settings
		 * @todo    [maybe] bulk edit order items meta
		 *
		 * @version 2.7.8
		 * @since   2.3.1
		 *
		 * @param object $item
		 * @param string $column_name
		 *
		 * @return string|void
		 */
		public function column_default( $item, $column_name ) {
			$result = '';
			switch ( $column_name ) {
				case 'id':
					$product_id = empty( $parent_id = $item->get_parent_id() ) ? $item->get_id() : $parent_id;
					$result     = '<a href="' . get_edit_post_link( $product_id ) . '">' . $item->get_id() . '</a>';
					break;
				case '_sku':
					$result = $item->get_sku();
					break;
				case 'title':
					$result = '<a href="' . $item->get_permalink() . '">' . $item->get_title() . '</a>';
					break;
				case '_price':
					$result = $item->get_price();
					break;
				case '_regular_price':
					$product_id = $item->get_id();
					$regular_price = get_post_meta( $product_id, '_regular_price', true );
					$result='<input' .
					        ' name="alg_wc_cog_bulk_edit_tool_regular_price[' . $product_id . ']"' .
					        ' type="text"' .
					        ' class="alg_wc_cog_bet_input short wc_input_price"' .
					        ' initial-value="' . $regular_price . '"' .
					        ' value="'         . $regular_price . '"' . '>';
					break;
				case '_sale_price':
					$product_id = $item->get_id();
					$sale_price    = get_post_meta( $product_id, '_sale_price', true );
					$result='<input' .
					        ' name="alg_wc_cog_bulk_edit_tool_sale_price[' . $product_id . ']"' .
					        ' type="text"' .
					        ' class="alg_wc_cog_bet_input short wc_input_price"' .
					        ' initial-value="' . $sale_price . '"' .
					        ' value="'         . $sale_price . '"' . '>';
					break;
				case '_alg_wc_cog_cost':
					$show_profit = 'yes' === get_option( 'alg_wc_cog_bulk_edit_tool_profit_on_cost_desc', 'no' );
					$value       = alg_wc_cog()->core->products->get_product_cost( $item->get_id() );
					if ( $show_profit ) {
						$profit_html = sprintf( __( 'Profit: %s', 'cost-of-goods-for-woocommerce' ), ( '' != ( $profit = alg_wc_cog()->core->products->get_product_profit_html( $item->get_id(), alg_wc_cog()->core->products->product_profit_html_template ) ) ? $profit : __( 'N/A', 'cost-of-goods-for-woocommerce' ) ) );
					} else {
						$profit_html = '';
					}
					$result = '<input' .
					          ' name="alg_wc_cog_bulk_edit_tool_costs[' . $item->get_id() . ']"' .
					          ' type="text"' .
					          ' class="alg_wc_cog_bet_input short wc_input_price"' .
					          ' initial-value="' . $value . '"' .
					          ' value="' . $value . '"' . '>'.'<span class="alg-wc-cog-cost-description">'.$profit_html.'</span>';
					break;
				case '_tags':
					add_filter( 'term_link', function ( $termlink, $term, $taxonomy ) {
						if ( 'product_tag' === $taxonomy ) {
							$termlink = admin_url( 'tools.php?page=bulk-edit-costs&section=costs_manually&product_tag=' . $term->slug );
						}
						return $termlink;
					}, 10, 3 );
					if ( $this->need_to_edit_tags() ) {
						echo $this->get_tags_edit_field( $item->get_id() );
					}
					echo '<div class="alg-wc-cog-product-tags">' . wc_get_product_tag_list( $item->get_id() ) . '</div>';
					break;
				case '_stock':
					if ( 'yes' !== get_option( 'alg_wc_cog_bulk_edit_tool_manage_stock', 'no' ) ) {
						$result = $item->get_stock_quantity();
					} else {
						$product_id   = $item->get_id();
						$stock_value  = ( '' === ( $stock = get_post_meta( $product_id, '_stock', true ) ) ? '' : floatval( $stock ) );
						$stock_status = ( '' == ( $_stock_status = get_post_meta( $product_id, '_stock_status', true ) ) ? 'N/A' : $_stock_status );
						$result       = '<input' .
						                ' name="alg_wc_cog_bulk_edit_tool_stock[' . $product_id . ']"' .
						                ' type="text"' .
						                ' class="alg_wc_cog_bet_input short"' .
						                ' initial-value="' . $stock_value . '"' .
						                ' value="' . $stock_value . '"' . '>';
						$result       .= wc_help_tip( sprintf( __( 'Stock status: %s', 'cost-of-goods-for-woocommerce' ), $stock_status ) );
					}
					break;
				default:
					$result = $item->{$column_name};
			}
			return $result;
		}

		/**
		 * get_columns.
		 *
		 * @version 2.7.8
		 * @since   2.3.1
		 *
		 * @return array
		 */
		function get_columns() {
			$do_edit_prices = ( 'yes' === get_option( 'alg_wc_cog_bulk_edit_tool_edit_prices', 'no' ) );
			$columns        = [
				//'cb'      => '<input type="checkbox" />',
				'id'               => __( 'Product ID', 'cost-of-goods-for-woocommerce' ),
				'_sku'             => __( 'SKU', 'cost-of-goods-for-woocommerce' ),
				'title'            => __( 'Title', 'cost-of-goods-for-woocommerce' ),
				'_alg_wc_cog_cost' => __( 'Cost', 'cost-of-goods-for-woocommerce' ),
				'_stock'           => __( 'Stock', 'cost-of-goods-for-woocommerce' ),
				'_tags'            => __( 'Tags', 'cost-of-goods-for-woocommerce' ),
			];
			if ( $do_edit_prices ) {
				$new_cols['_regular_price'] = __( 'Regular price', 'cost-of-goods-for-woocommerce' );
				$new_cols['_sale_price']    = __( 'Sale price', 'cost-of-goods-for-woocommerce' );
			} else {
				$new_cols['_price'] = __( 'Price', 'cost-of-goods-for-woocommerce' );
			}
			$position = array_search( '_alg_wc_cog_cost', array_keys( $columns ) );
			$columns  = array_merge( array_slice( $columns, 0, $position + 1 ), $new_cols, array_slice( $columns, $position + 1 ) );
			return $columns;
		}

		/**
		 * get_sortable_columns.
		 *
		 * @version 2.3.1
		 * @since   2.3.1
		 *
		 * @return array
		 */
		public function get_sortable_columns() {
			$sortable_columns = array(
				'id'               => array( 'id', true ),
				'_sku'             => array( '_sku', true ),
				'title'            => array( 'title', true ),
				'_alg_wc_cog_cost' => array( '_alg_wc_cog_cost', true ),
				'_price'           => array( '_price', true ),
				'_stock'           => array( '_stock', true ),
			);
			$do_edit_prices   = ( 'yes' === get_option( 'alg_wc_cog_bulk_edit_tool_edit_prices', 'no' ) );
			if ( $do_edit_prices ) {
				$sortable_columns['_regular_price'] = array( '_regular_price', true );
				$sortable_columns['_sale_price']    = array( '_sale_price', true );
			}
			return $sortable_columns;
		}

	}

endif;