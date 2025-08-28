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
							//{label: __('Products with cost', 'cost-of-goods-for-woocommerce'), value: 'with_cost'},
							{
								label: __('Cost of Goods products', 'cost-of-goods-for-woocommerce'),
								value: 'cog_products'
							}
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
				if (alg_wc_cog_analytics_obj.extra_data) {
					newHeaders.push({
						label: __('Price', 'cost-of-goods-for-woocommerce'),
						key: 'product_price',
						isNumeric: true,
						//isSortable: true,
					});
					newHeaders.push({
						label: __('Total Price', 'cost-of-goods-for-woocommerce'),
						key: 'product_price_total',
						isNumeric: true,
						//isSortable: true,
					});
				}
				// Cost and profit
				if (alg_wc_cog_analytics_obj.cost_and_profit_enabled_on_stock) {
					newHeaders.push({
						label: __('Cost', 'cost-of-goods-for-woocommerce'),
						key: 'product_cost',
						isNumeric: true,
						//isSortable: true,
					});
					newHeaders.push({
						label: __('Total Cost', 'cost-of-goods-for-woocommerce'),
						key: 'product_cost_total',
						isNumeric: true,
						//isSortable: true,
					});
					newHeaders.push({
						label: __('Profit', 'cost-of-goods-for-woocommerce'),
						key: 'product_profit',
						isNumeric: true,
						//isSortable: true,
					});
					newHeaders.push({
						label: __('Total Profit', 'cost-of-goods-for-woocommerce'),
						key: 'product_profit_total',
						isNumeric: true,
						//isSortable: true,
					});
				}
				if (alg_wc_cog_analytics_obj.extra_data) {
					// Category.
					newHeaders.push({
						label: __('Category', 'cost-of-goods-for-woocommerce'),
						key: 'product_cat',
						//isSortable: true,
					});
				}

				const newRows = reportTableData.rows.map((row, index) => {
					const product = reportTableData.items.data[index];
					const newRow = [...row];
					if (alg_wc_cog_analytics_obj.extra_data) {
						newRow.push({
							display: storeCurrency.formatAmount(product.product_price),
							value: product.product_price,
							type: 'currency'
						});
						newRow.push({
							display: storeCurrency.formatAmount(product.product_price_total),
							value: product.product_price_total,
							type: 'currency'
						});
					}
					// Cost and profit
					if (alg_wc_cog_analytics_obj.cost_and_profit_enabled_on_stock) {
						newRow.push({
							display: storeCurrency.formatAmount(product.product_cost),
							value: product.product_cost,
							type: 'currency'
						});
						newRow.push({
							display: storeCurrency.formatAmount(product.product_cost_total),
							value: product.product_cost_total,
							type: 'currency'
						});
						newRow.push({
							display: storeCurrency.formatAmount(product.product_profit),
							value: product.product_profit,
							type: 'currency'
						});
						newRow.push({
							display: storeCurrency.formatAmount(product.product_profit_total),
							value: product.product_profit_total,
							type: 'currency'
						});
					}
					if (alg_wc_cog_analytics_obj.extra_data) {
						// Category.
						newRow.push({
							display: product.product_cat,
							value: product.product_cat,
						});
					}

					return newRow;
				});
				if (alg_wc_cog_analytics_obj.cost_and_profit_enabled_on_stock) {
					let cost = reportTableData.totals.cost;
					let profit = reportTableData.totals.profit;
					let costTotals = reportTableData.totals.total_cost;
					let profitTotals = reportTableData.totals.total_profit;
					const newSummary = [
						...reportTableData.summary,
						{
							label: 'Cost',
							value: storeCurrency.formatAmount(cost),
						},
						{
							label: 'Total cost',
							value: storeCurrency.formatAmount(costTotals),
						},
						{
							label: 'Profit',
							value: storeCurrency.formatAmount(profit),
						},
						{
							label: 'Total profit',
							value: storeCurrency.formatAmount(profitTotals),
						},
					];
					reportTableData.summary = newSummary;
				}
				if (alg_wc_cog_analytics_obj.extra_data) {
					let price = reportTableData.totals.price;
					let totalPrice = reportTableData.totals.total_price;
					let totalStock = reportTableData.totals.total_stock;
					let totalProducts = reportTableData.totals.total_products;
					let averageCost = reportTableData.totals.average_cost;
					let averagePrice = reportTableData.totals.average_price;
					let averageProfit = reportTableData.totals.average_profit;
					const newSummaryExtraData = [
						...reportTableData.summary,
						{
							label: 'Price',
							value: storeCurrency.formatAmount(price),
						},
						{
							label: 'Total price',
							value: storeCurrency.formatAmount(totalPrice),
						},
						{
							label: 'Total stock',
							value: totalStock,
						},
						{
							label: 'Total products',
							value: totalProducts,
						},
						{
							label: 'Total average cost',
							value: storeCurrency.formatAmount(averageCost),
						},
						{
							label: 'Total average price',
							value: storeCurrency.formatAmount(averagePrice),
						},
						{
							label: 'Total average profit',
							value: storeCurrency.formatAmount(averageProfit),
						}
					];
					reportTableData.summary = newSummaryExtraData;
				}
				reportTableData.headers = newHeaders;
				reportTableData.rows = newRows;
				return reportTableData;
			}
		);
	}

};
export default stock;

