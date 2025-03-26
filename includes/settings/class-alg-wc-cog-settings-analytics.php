<?php
/**
 * Cost of Goods for WooCommerce - Analytics Section Settings.
 *
 * @version 3.6.8
 * @since   3.4.6
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Settings_Analytics' ) ) :

	class Alg_WC_Cost_of_Goods_Settings_Analytics extends Alg_WC_Cost_of_Goods_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 3.4.6
		 * @since   3.4.6
		 */
		function __construct() {
			$this->id   = 'analytics';
			$this->desc = __( 'Analytics', 'cost-of-goods-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * get_settings.
		 *
		 * @version 3.6.8
		 * @since   3.4.6
		 *
		 * @return array
		 */
		function get_settings() {
			$analytics_settings = array(
				array(
					'title' => __( 'Analytics options', 'cost-of-goods-for-woocommerce' ),
					'desc'  => sprintf( __( 'Options related to <a href="%s">WooCommerce Analytics</a>.', 'cost-of-goods-for-woocommerce' ), admin_url( 'admin.php?page=wc-admin&path=/analytics/overview' ) ) . ' ' .
					           sprintf( __( 'If you can\'t see the values refreshed or have issues with the analytics page, please try <a href="%s">clearing the analytics cache</a>.', 'cost-of-goods-for-woocommerce' ), admin_url( 'admin.php?page=wc-status&tab=tools' ) ),
					'type'  => 'title',
					'id'    => 'alg_wc_cog_analytics_options',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_cog_analytics_options',
				),
			);

			$customers_section_opts = array(
				array(
					'title' => __( 'Customers', 'cost-of-goods-for-woocommerce' ),
					'desc'  => sprintf( __( 'Options for the %s section.', 'cost-of-goods-for-woocommerce' ), '<a href="' . admin_url( 'admin.php?page=wc-admin&path=/customers' ) . '" >' . __( 'WooCommerce > Customers', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
					'type'  => 'title',
					'id'    => 'alg_wc_cog_analytics_orders_options',
				),
				array(
					'title'   => __( 'Cost and profit columns', 'cost-of-goods-for-woocommerce' ),
					'desc'    => __( 'Add "Cost" and "Profit" columns', 'cost-of-goods-for-woocommerce' ),
					'id'      => 'alg_wc_cog_analytics_customers_cost_and_profit_columns',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_cog_analytics_orders_options',
				),
			);

			$orders_section_opts = array(
				array(
					'title' => __( 'Orders', 'cost-of-goods-for-woocommerce' ),
					'desc'  => sprintf( __( 'Options for the %s section.', 'cost-of-goods-for-woocommerce' ), '<a href="' . admin_url( 'admin.php?page=wc-admin&path=/analytics/orders' ) . '" >' . __( 'Analytics > Orders', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
					'type'  => 'title',
					'id'    => 'alg_wc_cog_analytics_orders_options',
				),
				array(
					'title'   => __( 'Cost and profit columns', 'cost-of-goods-for-woocommerce' ),
					'desc'    => __( 'Add "Cost" and "Profit" columns', 'cost-of-goods-for-woocommerce' ),
					'id'      => 'alg_wc_cog_analytics_orders',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'title'             => __( 'Cost and profit totals', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Add "Cost" and "Profit" totals to the report charts', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_analytics_orders_cost_profit_totals',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'             => __( 'Extra costs', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Add columns for individual elements from the costs total', 'cost-of-goods-for-woocommerce' ),
					'desc_tip'          => __( 'Adds items, shipping, gateway, shipping classes and extra costs.', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_analytics_orders_individual_costs',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_cog_analytics_orders_options',
				),
			);

			$products_section_opts = array(
				array(
					'title' => __( 'Products', 'cost-of-goods-for-woocommerce' ),
					'desc'  => sprintf( __( 'Options for the %s section.', 'cost-of-goods-for-woocommerce' ), '<a href="' . admin_url( 'admin.php?page=wc-admin&path=/analytics/products' ) . '" >' . __( 'Analytics > Products', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
					'type'  => 'title',
					'id'    => 'alg_wc_cog_analytics_products_options',
				),
				array(
					'title'             => __( 'Cost and profit columns', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Add "Cost" and "Profit" columns', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_cost_and_profit_column_on_products_tab',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
					'default'           => 'no',
					'type'              => 'checkbox',
				),
				array(
					'title'             => __( 'Cost and profit totals', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Add "Cost" and "Profit" totals to the report charts', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_cost_and_profit_totals_on_products_tab',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_cog_analytics_products_options',
				),
			);

			$variation_section_opts = array(
				array(
					'title' => __( 'Variations', 'cost-of-goods-for-woocommerce' ),
					'desc'  => sprintf( __( 'Options for the %s section.', 'cost-of-goods-for-woocommerce' ), '<a href="' . admin_url( 'admin.php?page=wc-admin&path=/analytics/variations' ) . '" >' . __( 'Analytics > Variations', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
					'type'  => 'title',
					'id'    => 'alg_wc_cog_analytics_variations_options',
				),
				array(
					'title'             => __( 'Cost and profit columns', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Add "Cost" and "Profit" columns', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_cost_and_profit_column_on_variations_tab',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
					'default'           => 'no',
					'type'              => 'checkbox',
				),
				array(
					'title'             => __( 'Cost and profit totals', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Add "Cost" and "Profit" totals to the report charts', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_cost_and_profit_totals_on_variations_tab',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_cog_analytics_variations_options',
				),
			);

			$categories_section_opts = array(
				array(
					'title' => __( 'Categories', 'cost-of-goods-for-woocommerce' ),
					'desc'  => sprintf( __( 'Options for the %s section.', 'cost-of-goods-for-woocommerce' ), '<a href="' . admin_url( 'admin.php?page=wc-admin&path=/analytics/categories' ) . '" >' . __( 'Analytics > Categories', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
					'type'  => 'title',
					'id'    => 'alg_wc_cog_analytics_categories_options',
				),
				array(
					'title'             => __( 'Cost and profit columns', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Add "Cost" and "Profit" columns', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_cost_and_profit_column_on_categories_tab',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
					'default'           => 'no',
					'type'              => 'checkbox',
				),
				array(
					'title'             => __( 'Cost and profit totals', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Add "Cost" and "Profit" totals to the report charts', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_cost_and_profit_totals_on_categories_tab',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_cog_analytics_categories_options',
				),
			);

			$revenue_section_opts = array(
				array(
					'title' => __( 'Revenue', 'cost-of-goods-for-woocommerce' ),
					'desc'  => sprintf( __( 'Options for the %s section.', 'cost-of-goods-for-woocommerce' ), '<a href="' . admin_url( 'admin.php?page=wc-admin&path=/analytics/revenue' ) . '" >' . __( 'Analytics > Revenue', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
					'type'  => 'title',
					'id'    => 'alg_wc_cog_analytics_revenue_options',
				),
				array(
					'title'             => __( 'Cost and profit columns', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Add "Cost" and "Profit" totals columns', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_cost_and_profit_column_on_analytics_revenue',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
					'default'           => 'no',
					'type'              => 'checkbox',
				),
				array(
					'title'             => __( 'Cost and profit totals', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Add "Cost" and "Profit" totals to the report charts', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_cost_and_profit_totals_on_analytics_revenue',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_cog_analytics_revenue_options',
				),
			);

			$stock_section_opts = array(
				array(
					'title' => __( 'Stock', 'cost-of-goods-for-woocommerce' ),
					'desc'  => sprintf( __( 'Options for the %s section.', 'cost-of-goods-for-woocommerce' ), '<a href="' . admin_url( 'admin.php?page=wc-admin&path=/analytics/stock' ) . '" >' . __( 'Analytics > Stock', 'cost-of-goods-for-woocommerce' ) . '</a>' ),
					'type'  => 'title',
					'id'    => 'alg_wc_cog_analytics_stock_options',
				),
				array(
					'title'             => __( 'Cost and profit columns', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Add "Cost" and "Profit" columns', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_cost_and_profit_enabled_on_analytics_stock',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
					'default'           => 'no',
					'type'              => 'checkbox',
				),
				array(
					'title'             => __( 'Consider stock', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Take stock into consideration for cost and profit calculation', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_analytics_stock_considers_stock',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
					'default'           => 'yes',
					'type'              => 'checkbox',
				),
				array(
					'title'             => __( 'Category column', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Add "Category" column', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_category_enabled_on_analytics_stock',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
					'default'           => 'no',
					'type'              => 'checkbox',
				),
				array(
					'title'             => __( 'Filter dropdown', 'cost-of-goods-for-woocommerce' ),
					'desc'              => __( 'Add filter allowing to restrict the query', 'cost-of-goods-for-woocommerce' ),
					'desc_tip'          => __( 'For now, it allows to get only products with costs.', 'cost-of-goods-for-woocommerce' ),
					'id'                => 'alg_wc_cog_filter_enabled_on_analytics_stock',
					'custom_attributes' => apply_filters( 'alg_wc_cog_settings', array( 'disabled' => 'disabled' ) ),
					'default'           => 'no',
					'type'              => 'checkbox',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_cog_analytics_stock_options',
				),
			);

			return array_merge(
				$analytics_settings,
				$customers_section_opts,
				$products_section_opts,
				$revenue_section_opts,
				$orders_section_opts,
				$variation_section_opts,
				$categories_section_opts,
				$stock_section_opts,
				array()
			);
		}
	}
endif;

return new Alg_WC_Cost_of_Goods_Settings_Analytics();