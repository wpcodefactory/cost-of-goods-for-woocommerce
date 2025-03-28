<?php
/**
 * Cost of Goods for WooCommerce - Tools Section Settings.
 *
 * @version 3.6.2
 * @since   1.4.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Settings_Tools' ) ) :

class Alg_WC_Cost_of_Goods_Settings_Tools extends Alg_WC_Cost_of_Goods_Settings_Section {

	/**
	 * @version 2.8.1
	 * @since   2.8.1
	 *
	 * @var string
	 */
	private static $run_import_tool_cron_output = '';

	/**
	 * Constructor.
	 *
	 * @version 1.7.0
	 * @since   1.4.0
	 * @see     "WooCommerce > Settings > Cost of Goods > Tools"
	 */
	function __construct() {
		$this->id   = 'tools';
		$this->desc = __( 'Tools', 'cost-of-goods-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_import_tool_cron_info.
	 *
	 * @version 2.8.1
	 * @since   2.8.1
	 *
	 * @return string
	 */
	function get_import_tool_cron_info() {
		$run_import_tool_cron = 'yes' === get_option( 'alg_wc_cog_import_tool_cron', 'no' );
		if ( empty( self::$run_import_tool_cron_output ) ) {
			$output = '';
			if (
				( ! $event_timestamp = wp_next_scheduled( 'alg_wc_cog_run_import_tool' ) ) &&
				isset( $_POST['alg_wc_cog_import_tool_cron'] )
			) {
				$output = '<br />';
				$output .= '<span style="font-weight: bold; color: green;">' . __( 'Please, reload the page to see the next scheduled event info.', 'cost-of-goods-for-woocommerce' ) . '</span>';
			} elseif ( $event_timestamp && $run_import_tool_cron ) {
				$output              = '<br />';
				$now                 = current_time( 'timestamp', true );
				$pretty_time_missing = human_time_diff( $now, $event_timestamp );
				$output              .= sprintf( __( 'Next event scheduled to %s', 'cost-of-goods-for-woocommerce' ), '<strong>' . get_date_from_gmt( date( 'Y-m-d H:i:s', $event_timestamp ), get_option( 'date_format' ) . ' - ' . get_option( 'time_format' ) ) . '</strong>' );
				$output              .= ' ' . '(' . $pretty_time_missing . ' left)';
			}
			self::$run_import_tool_cron_output = $output;
		} else {
			if ( ! $run_import_tool_cron ) {
				self::$run_import_tool_cron_output = '';
			}
		}
		return self::$run_import_tool_cron_output;
	}

	/**
	 * get_settings.
	 *
	 * @version 3.6.2
	 * @since   1.4.0
	 * @todo    [later] better descriptions
	 * @todo    [maybe] add "PHP time limit" option, i.e. `set_time_limit()`
	 * @todo    [maybe] Orders report: Extra data: better description
	 * @todo    [maybe] PHP memory limit: better description
	 */
	function get_settings() {

		$bulk_edit_costs_opts = array(
			array(
				'title'    => __( 'Product Bulk Edit Costs Tool', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => sprintf( __( 'Bulk Edit tool is in %s.', 'cost-of-goods-for-woocommerce' ),
					'<a href="' . admin_url( 'tools.php?page=bulk-edit-costs' ) . '">' . __( 'Tools > Bulk Edit Costs', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
				'id'       => 'alg_wc_cog_bulk_edit_tool_options',
			),
			array(
				'title'    => __( 'Product search', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'id'       => 'alg_wc_cog_bulk_edit_tool_search_method',
				'default'  => 'title',
				'options'  => array(
					'title' => __( 'Search by title', 'cost-of-goods-for-woocommerce' ),
					'all'   => __( 'Search all', 'cost-of-goods-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Product types', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Leave empty to display all product types.', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'id'       => 'alg_wc_cog_bulk_edit_tool_product_types',
				'default'  => array(),
				'options'  => array_merge( wc_get_product_types(), array( 'variation' => __( 'Variations', 'woocommerce' ) ) ),
			),
			array(
				'title'    => __( 'Prices', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Edit prices', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_cog_bulk_edit_tool_edit_prices',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Tags', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Edit tags', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_cog_bulk_edit_tool_edit_tags',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Costs', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Show profit as cost field description', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_cog_bulk_edit_tool_profit_on_cost_desc',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Stock', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Manage stock', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_cog_bulk_edit_tool_manage_stock',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Stock update method', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Ignored unless "Manage stock" checkbox is enabled.', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'select',
				'class'    => 'chosen_select',
				'id'       => 'alg_wc_cog_bulk_edit_tool_manage_stock_method',
				'default'  => 'meta',
				'options'  => array(
					'meta' => __( 'Update product meta', 'cost-of-goods-for-woocommerce' ),
					'func' => __( 'Use product functions', 'cost-of-goods-for-woocommerce' ),
				),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_bulk_edit_tool_options',
			),
		);

		$import_tools_opts = array(
			array(
				'title' => __( 'Product Import Costs Tool', 'cost-of-goods-for-woocommerce' ),
				'type'  => 'title',
				'desc'  => __( 'A tool created with the purpose of importing the cost meta from another plugin by replacing the cost meta.', 'cost-of-goods-for-woocommerce' ) . '<br /><br />' .
				           __( 'If you wish, you can use it on the opposite way by swapping the from and to keys.', 'cost-of-goods-for-woocommerce' ) . ' ' . __( 'You can also use it with any other metas.', 'cost-of-goods-for-woocommerce' ) . '<br /><br />' .
				           sprintf( __( 'You can find the Import tool at %s.', 'cost-of-goods-for-woocommerce' ),
					           '<a href="' . admin_url( 'tools.php?page=import-costs' ) . '">' . __( 'Tools > Import Costs', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
				'id'    => 'alg_wc_cog_import_tool_options',
			),
			array(
				'title'    => __( 'Key to import from', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'The meta key used to replace the cost meta value.', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'text',
				'id'       => 'alg_wc_cog_tool_key',
				'default'  => '_wc_cog_cost',
			),
			array(
				'title'    => __( 'Meta key replaced', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'text',
				'id'       => 'alg_wc_cog_tool_key_to',
				'default'  => '_alg_wc_cog_cost',
			),
			array(
				'title'    => __( 'Check if key exists', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Replace the meta value only if the from key exists', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_import_tool_check_key',
				'default'  => 'yes',
			),
			array(
				'title'    => __( 'Check key value', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Replace the meta value only if the from key value is not empty, null or zero', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_import_tool_check_value',
				'default'  => 'yes',
			),
			array(
				'title'    => __( 'WooCommerce Import', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'checkbox',
				'desc'     => sprintf( __( 'Sync with %s from WooCommerce', 'cost-of-goods-for-woocommerce' ), '<a href="' . admin_url( 'edit.php?post_type=product&page=product_importer' ) . '" target="_blank">' . __( 'Product Importer', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
				'desc_tip' => __( 'If enabled, our tool will run automatically along with the CSV/TXT import.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_import_tool_sync_wc_import',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Import page table', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Display a table at the import page', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'If you have problems accessing the "Import Costs" page try disabling this option.', 'cost-of-goods-for-woocommerce' ),
				'id'       => 'alg_wc_cog_import_tool_display_table',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Run automatically', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Run the import tool automatically as a recurring event', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Runs the tool based on the frequency option below.', 'cost-of-goods-for-woocommerce' ) )
				              . $this->get_import_tool_cron_info(),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_cog_import_tool_cron',
				'default'  => 'no',
			),
			array(
				'desc'     => __( 'Frequency', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'select',
				'desc_tip' => __( 'If the frequency is changed after the Run automatically option is enabled, it will be necessary to disable and enable it again to see the frequency updated.', 'cost-of-goods-for-woocommerce' ),
				'options'  => array(
					'hourly'     => __( 'Hourly', 'cost-of-goods-for-woocommerce' ),
					'daily'      => __( 'Daily', 'cost-of-goods-for-woocommerce' ),
					'twicedaily' => __( 'Twice daily', 'cost-of-goods-for-woocommerce' ),
					'weekly'     => __( 'Weekly', 'cost-of-goods-for-woocommerce' ),
				),
				'id'       => 'alg_wc_cog_import_tool_cron_frequency',
				'default'  => 'daily',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_import_tool_options',
			),
		);

		$order_tools_opts = array(
			array(
				'title'    => __( 'Orders recalculation tool', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_cog_orders_tools_options',
			),
			array(
				'title'    => __( 'Recalculate orders', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Recalculate cost and profit for all orders', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Enable the checkbox and "Save changes" to run the tool.', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_cog_recalculate_orders_cost_and_profit_all',
				'default'  => 'no',
				'checkboxgroup' => 'start',
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'desc'          => __( 'Override item costs on recalculation', 'cost-of-goods-for-woocommerce' ),
				'desc_tip'      => __( 'Disable it if you don\'t want to override the item costs.', 'cost-of-goods-for-woocommerce' ),
				'type'          => 'checkbox',
				'id'            => 'alg_wc_cog_recalculate_orders_override_item_costs',
				'checkboxgroup' => 'end',
				'default'       => 'yes',
			),
			array(
				'title'    => __( 'Recalculate no cost orders', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'Recalculate cost and profit for orders with no costs', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Set items costs in orders that do not have costs set.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					__( 'Enable the checkbox and "Save changes" to run the tool.', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_cog_recalculate_orders_cost_and_profit_no_costs',
				'default'  => 'no',
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => 'Date',
				'desc'     => __( 'After.', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Recalculate cost and profit for orders after a specific date.', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'datetime-local',
				'id'       => 'alg_wc_cog_recalculate_orders_cost_and_profit_after',
				'css'      => 'width:398px;',
				'default'  => '',
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'desc'     => __( 'Before.', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Recalculate cost and profit for orders before a specific date.', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'datetime-local',
				'id'       => 'alg_wc_cog_recalculate_orders_cost_and_profit_before',
				'css'      => 'width:398px;',
				'default'  => '',
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'desc'     => __( 'Date type.', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'select',
				'id'       => 'alg_wc_cog_recalculate_orders_cost_and_profit_date_type',
				'default'  => '',
				'class'    => 'chosen_select',
				'options'  => array(
					'date_created'   => __( 'Date created', 'cost-of-goods-for-woocommerce' ),
					'date_modified'  => __( 'Date modified', 'cost-of-goods-for-woocommerce' ),
					'date_completed' => __( 'Date completed', 'cost-of-goods-for-woocommerce' ),
					'date_paid'      => __( 'Date paid', 'cost-of-goods-for-woocommerce' ),
				),
				'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'PHP memory limit', 'cost-of-goods-for-woocommerce' ),
				'desc'     => __( 'megabytes', 'cost-of-goods-for-woocommerce' ),
				'desc_tip' => __( 'Will set PHP memory limit right before tools are run.', 'cost-of-goods-for-woocommerce' ) . ' ' .
					__( 'Ignored if set to zero.', 'cost-of-goods-for-woocommerce' ),
				'type'     => 'number',
				'id'       => 'alg_wc_cog_recalculate_orders_cost_and_profit_memory_limit',
				'default'  => 0,
				'custom_attributes' => array( 'min' => 0 ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_cog_orders_tools_options',
			),
		);

		return array_merge(
			$bulk_edit_costs_opts,
			$import_tools_opts,
			$order_tools_opts,
		);
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Settings_Tools();
