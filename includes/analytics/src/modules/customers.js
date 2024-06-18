/**
 * Cost of Goods for WooCommerce - WooCommerce > Customers report.
 *
 */

import {addFilter} from '@wordpress/hooks';
import {__} from '@wordpress/i18n';
import CurrencyFactory from '@woocommerce/currency';
import Formatting from "./formatting";

const storeCurrency = CurrencyFactory(wcSettings.currency);
Formatting.setStoreCurrency(storeCurrency);

let customers = {
	init: function () {
		addFilter(
			'woocommerce_admin_report_table',
			'cost-of-goods-for-woocommerce',
			(reportTableData) => {
				if (
					reportTableData.endpoint !== 'customers' ||
					!reportTableData.items ||
					!reportTableData.items.data ||
					!reportTableData.items.data.length ||
					!alg_wc_cog_analytics_obj.cost_and_profit_columns_enabled_on_orders
				) {
					return reportTableData;
				}
				reportTableData.headers = this.getHeaders(reportTableData);
				reportTableData.rows = this.getRows(reportTableData)
				return reportTableData;
			}
		);
	},

	getHeaders: function (reportTableData) {
		let headers = reportTableData.headers;
		if (alg_wc_cog_analytics_obj.cost_and_profit_columns_enabled_on_customers) {
			const costAndProfitHeaders = [
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
			headers = [...reportTableData.headers, ...costAndProfitHeaders];
		}
		return headers;
	},

	getRows: function (reportTableData) {
		const newRows = reportTableData.rows.map((row, index) => {
			const item = reportTableData.items.data[index];
			let costAndProfit = [
				{
					display: storeCurrency.formatAmount(item.costs_total),
					value: item.costs_total,
					type: 'currency'
				},
				{
					display: storeCurrency.formatAmount(item.profit_total),
					value: item.profit_total,
					type: 'currency'
				},
			];
			let newRow = [...row, ...costAndProfit];
			return newRow;
		});
		return newRows;
	},
}
export default customers;
