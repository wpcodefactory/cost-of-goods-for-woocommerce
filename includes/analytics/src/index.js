/**
 * Cost of Goods for WooCommerce - Analytics (WooCommerce Admin) Report
 *
 * @see https://github.com/woocommerce/woocommerce-admin/blob/main/client/analytics/report/orders/config.js#L50-L62
 *
 */

import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
import CurrencyFactory from '@woocommerce/currency';
const storeCurrency = CurrencyFactory(wcSettings.currency);

// Reports table
addFilter(
	'woocommerce_admin_report_table',
	'cost-of-goods-for-woocommerce',
	( reportTableData ) => {
		if (
			reportTableData.endpoint !== 'orders' ||
			! reportTableData.items ||
			! reportTableData.items.data ||
			! reportTableData.items.data.length ||
			! alg_wc_cog_analytics_obj.cost_and_profit_columns_enabled
		) {
			return reportTableData;
		}
		const newHeaders = [
			...reportTableData.headers,
			{
				label: __( 'Cost', 'cost-of-goods-for-woocommerce' ),
				key: 'order_cost',
				isNumeric: true,
			},
			{
				label: __( 'Profit', 'cost-of-goods-for-woocommerce' ),
				key: 'order_profit',
				isNumeric: true,
			},
		];
		const newRows = reportTableData.rows.map( ( row, index ) => {
			const order = reportTableData.items.data[ index ];
			const newRow = [
				...row,
				{
					display: storeCurrency.formatAmount(order.order_cost),
					value: order.order_cost,
					type: 'currency'
				},
				{
					display: storeCurrency.formatAmount(order.order_profit),
					value: order.order_profit,
					type: 'currency'
				},
			];
			return newRow;
		} );
		reportTableData.headers = newHeaders;
		reportTableData.rows = newRows;
		return reportTableData;
	}
);

// Charts
/**
 * @see https://github.com/woocommerce/woocommerce-admin/blob/main/client/analytics/report/orders/config.js#L50-L62
 */
addFilter(
	'woocommerce_admin_orders_report_charts',
	'cost-of-goods-for-woocommerce',
	(charts) => {
		if (alg_wc_cog_analytics_obj.cost_and_profit_totals_enabled) {
			charts = [...charts,
				{
					key: 'costs_total',
					label: __('Costs total', 'cost-of-goods-for-woocommerce'),
					type: 'currency'
				},
				{
					key: 'profit_total',
					label: __('Profit total', 'cost-of-goods-for-woocommerce'),
					type: 'currency'
				}];
		}
		return charts;
	}
);