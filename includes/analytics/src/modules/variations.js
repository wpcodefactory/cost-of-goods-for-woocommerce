/**
 * Cost of Goods for WooCommerce - Analytics > Variations (WooCommerce Admin) Report.
 *
 */

import {addFilter} from '@wordpress/hooks';
import {__} from '@wordpress/i18n';
import CurrencyFactory from '@woocommerce/currency';
import Formatting from './formatting.js';

const storeCurrency = CurrencyFactory(wcSettings.currency);
Formatting.setStoreCurrency(storeCurrency);

let products = {
	init: function () {
		// Reports table
		addFilter(
			'woocommerce_admin_report_table',
			'cost-of-goods-for-woocommerce',
			(reportTableData) => {
				const urlParams = new URLSearchParams(window.location.search);
				if (
					reportTableData.endpoint !== 'variations' ||
					!reportTableData.items ||
					!reportTableData.items.data ||
					!reportTableData.items.data.length ||
					(!alg_wc_cog_analytics_obj.variation_cost_and_profit_columns_enabled && urlParams.get('path') === '/analytics/variations')
				) {
					return reportTableData;
				}
				const newHeaders = [
					...reportTableData.headers,
					{
						label: __('Cost', 'cost-of-goods-for-woocommerce'),
						key: 'cost',
						isNumeric: true,
						//isSortable: true,
					},
					{
						label: __('Profit', 'cost-of-goods-for-woocommerce'),
						key: 'profit',
						isNumeric: true,
						//isSortable: true,
					},
				];
				const newRows = reportTableData.rows.map((row, index) => {
					const item = reportTableData.items.data[index];
					const newRow = [
						...row,
						{
							display: storeCurrency.formatAmount(item.cost),
							value: item.cost,
							type: 'currency'
						},
						{
							display: storeCurrency.formatAmount(item.profit),
							value: item.profit,
							type: 'currency'
						},
					];
					return newRow;
				});
				const newSummary = [
					...reportTableData.summary,
					{
						label: 'Profit',
						value: Formatting.formatProfit(alg_wc_cog_analytics_obj.profit_template,reportTableData.totals.costs_total,reportTableData.totals.profit_total,reportTableData.totals.net_revenue),
					},
				];
				reportTableData.summary = newSummary;
				reportTableData.rows = newRows;
				reportTableData.headers = newHeaders;

				return reportTableData;
			}
		);
		// Charts
		/**
		 * @see https://github.com/woocommerce/woocommerce-admin/blob/main/client/analytics/report/orders/config.js#L50-L62
		 */
		addFilter(
			'woocommerce_admin_variations_report_charts',
			'cost-of-goods-for-woocommerce',
			(charts) => {
				if (alg_wc_cog_analytics_obj.variation_cost_and_profit_totals_enabled) {
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
export default products;

