/**
 * Cost of Goods for WooCommerce - Analytics > Orders (WooCommerce Admin) Report.
 *
 * @see https://github.com/woocommerce/woocommerce-admin/blob/main/client/analytics/report/orders/config.js#L50-L62
 *
 */

import {addFilter} from '@wordpress/hooks';
import {__} from '@wordpress/i18n';
import CurrencyFactory from '@woocommerce/currency';

const storeCurrency = CurrencyFactory(wcSettings.currency);

let orders = {
	init: function () {
		// Reports table
		addFilter(
			'woocommerce_admin_report_table',
			'cost-of-goods-for-woocommerce',
			(reportTableData) => {
				if (
					reportTableData.endpoint !== 'revenue' ||
					!reportTableData.items ||
					!reportTableData.items.data ||
					!reportTableData.items.data.length ||
					!alg_wc_cog_analytics_obj.cost_and_profit_columns_enabled_on_revenue
				) {
					return reportTableData;
				}
				const newHeaders = [
					...reportTableData.headers,
					{
						label: __('Cost', 'cost-of-goods-for-woocommerce'),
						key: 'costs_total',
						isNumeric: true,
						//isSortable: true,
					},
					{
						label: __('Profit', 'cost-of-goods-for-woocommerce'),
						key: 'profit_total',
						isNumeric: true,
						//isSortable: true,
					},
				];
				const newRows = reportTableData.rows.map((row, index) => {
					const item = reportTableData.items.data[index];
					const newRow = [
						...row,
						{
							display: storeCurrency.formatAmount(item.subtotals.costs_total),
							value: item.costs_total,
							type: 'currency'
						},
						{
							display: storeCurrency.formatAmount(item.subtotals.profit_total),
							value: item.profit_total,
							type: 'currency'
						},
					];
					return newRow;
				});
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
			'woocommerce_admin_revenue_report_charts',
			'cost-of-goods-for-woocommerce',
			(charts) => {
				if (alg_wc_cog_analytics_obj.cost_and_profit_totals_enabled_on_revenue) {
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
	}
};
export default orders;

