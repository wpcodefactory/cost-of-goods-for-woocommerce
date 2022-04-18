/**
 * Cost of Goods for WooCommerce - Analytics > Stock (WooCommerce Admin) Report.
 *
 * @see https://github.com/woocommerce/woocommerce-admin/issues/4348.
 * @todo Add cost and profit totals on summary.
 *
 */

import {addFilter} from '@wordpress/hooks';
import {__} from '@wordpress/i18n';
import CurrencyFactory from '@woocommerce/currency';
import Formatting from "./formatting";

const storeCurrency = CurrencyFactory(wcSettings.currency);
Formatting.setStoreCurrency(storeCurrency);

let stock = {
	init: function () {
		this.addColumns();
		this.addCOGFilter();
	},
	addCOGFilter: function () {
		if (alg_wc_cog_analytics_obj.filter_enabled_on_stock) {
			addFilter(
				'woocommerce_admin_stock_report_filters',
				'cost-of-goods-for-woocommerce',
				(obj) => {
					obj.push({
						label: __('Cost of Goods filter', 'cost-of-goods-for-woocommerce'),
						staticParams: ['paged', 'per_page'],
						param: 'alg_cog_stock_filter',
						showFilters: () => true,
						filters: [
							{label: __('Disabled', 'cost-of-goods-for-woocommerce'), value: 'all'},
							{label: __('Products with cost', 'cost-of-goods-for-woocommerce'), value: 'with_cost'}
						]
					});
					return obj;
				}
			);
		}
	},
	addColumns: function () {
		// Reports table
		addFilter(
			'woocommerce_admin_report_table',
			'cost-of-goods-for-woocommerce',
			(reportTableData) => {
				if (
					reportTableData.endpoint !== 'stock' ||
					!reportTableData.items ||
					!reportTableData.items.data ||
					!reportTableData.items.data.length
				) {
					return reportTableData;
				}
				const newHeaders = [...reportTableData.headers];
				// Cost and profit
				if (alg_wc_cog_analytics_obj.cost_and_profit_enabled_on_stock) {
					newHeaders.push({
						label: __('Cost', 'cost-of-goods-for-woocommerce'),
						key: 'product_cost',
						isNumeric: true,
						//isSortable: true,
					});
					newHeaders.push({
						label: __('Profit', 'cost-of-goods-for-woocommerce'),
						key: 'product_profit',
						isNumeric: true,
						//isSortable: true,
					});
				}
				// Category
				if (alg_wc_cog_analytics_obj.category_enabled_on_stock) {
					newHeaders.push({
						label: __('Category', 'cost-of-goods-for-woocommerce'),
						key: 'product_cat',
						//isSortable: true,
					});
				}
				const newRows = reportTableData.rows.map((row, index) => {
					const product = reportTableData.items.data[index];
					const newRow = [...row];
					// Cost and profit
					if (alg_wc_cog_analytics_obj.cost_and_profit_enabled_on_stock) {
						newRow.push({
							display: storeCurrency.formatAmount(product.product_cost),
							value: product.product_cost,
							type: 'currency'
						});
						newRow.push({
							display: storeCurrency.formatAmount(product.product_profit),
							value: product.product_profit,
							type: 'currency'
						});
					}
					// Category
					if (alg_wc_cog_analytics_obj.category_enabled_on_stock) {
						newRow.push({
							display: product.product_cat,
							value: product.product_cat,
						});
					}
					return newRow;
				});
				/*const costsTotal = reportTableData.items.data.reduce((sum, item) => {
					return sum + item.product_cost;
				}, 0);
				const profitTotal = reportTableData.items.data.reduce((sum, item) => {
					return sum + item.product_profit;
				}, 0);
				const priceTotal = reportTableData.items.data.reduce((sum, item) => {
					return sum + item.product_price;
				}, 0);
				const newSummary = [
					...reportTableData.summary,
					{
						label: 'Cost',
						value: storeCurrency.formatAmount(costsTotal),
					},
					{
						label: 'Profit',
						value: Formatting.formatProfit(alg_wc_cog_analytics_obj.profit_template,costsTotal,profitTotal,priceTotal),
					},
				];
				reportTableData.summary = newSummary;*/
				reportTableData.headers = newHeaders;
				reportTableData.rows = newRows;
				return reportTableData;
			}
		);
	}
};
export default stock;

