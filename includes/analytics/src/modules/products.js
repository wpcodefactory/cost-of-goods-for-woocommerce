/**
 * Cost of Goods for WooCommerce - Analytics > Products (WooCommerce Admin) Report.
 *
 */

import {addFilter} from '@wordpress/hooks';
import {__} from '@wordpress/i18n';
import CurrencyFactory from '@woocommerce/currency';

const storeCurrency = CurrencyFactory(wcSettings.currency);

let products = {
	init: function () {
		// Reports table
		addFilter(
			'woocommerce_admin_report_table',
			'cost-of-goods-for-woocommerce',
			(reportTableData) => {
				if (
					reportTableData.endpoint !== 'products' ||
					!reportTableData.items ||
					!reportTableData.items.data ||
					!reportTableData.items.data.length ||
					!alg_wc_cog_analytics_obj.cost_and_profit_columns_enabled_on_products
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
					console.log(item);
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
			'woocommerce_admin_products_report_charts',
			'cost-of-goods-for-woocommerce',
			(charts) => {
				if (alg_wc_cog_analytics_obj.cost_and_profit_totals_enabled_on_products) {
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

