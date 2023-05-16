/**
 * Cost of Goods for WooCommerce - Analytics > Orders (WooCommerce Admin) Report.
 *
 * @see https://github.com/woocommerce/woocommerce-admin/blob/main/client/analytics/report/orders/config.js#L50-L62
 *
 */

import {addFilter} from '@wordpress/hooks';
import {__} from '@wordpress/i18n';
import CurrencyFactory from '@woocommerce/currency';
import Formatting from "./formatting";

const storeCurrency = CurrencyFactory(wcSettings.currency);
Formatting.setStoreCurrency(storeCurrency);

let orders = {
	getHeaders: function (reportTableData) {
		const costAndProfitHeaders = [
			{
				label: __('Cost', 'cost-of-goods-for-woocommerce'),
				key: 'order_cost',
				isNumeric: true,
				//isSortable: true,
			},
			{
				label: __('Profit', 'cost-of-goods-for-woocommerce'),
				key: 'order_profit',
				isNumeric: true,
				//isSortable: true,
			},
		];
		let headers = [...reportTableData.headers, ...costAndProfitHeaders];
		if (alg_wc_cog_analytics_obj.individual_order_costs_enabled) {
			const individualCostsHeaders = [
				{
					label: __('Items cost', 'cost-of-goods-for-woocommerce'),
					key: 'items_cost',
					isNumeric: true,
					//isSortable: true,
				},
				{
					label: __('Shipping cost', 'cost-of-goods-for-woocommerce'),
					key: 'shipping_cost',
					isNumeric: true,
					//isSortable: true,
				},
				{
					label: __('Gateway cost', 'cost-of-goods-for-woocommerce'),
					key: 'gateway_cost',
					isNumeric: true,
					//isSortable: true,
				},
				{
					label: __('Extra cost', 'cost-of-goods-for-woocommerce'),
					key: 'extra_cost',
					isNumeric: true,
					//isSortable: true,
				},
				{
					label: __('Shipping classes cost', 'cost-of-goods-for-woocommerce'),
					key: 'shipping_classes_cost',
					isNumeric: true,
					//isSortable: true,
				},
			];
			headers = [...reportTableData.headers, ...individualCostsHeaders, ...costAndProfitHeaders];
		}
		return headers;
	},

	getRows:function(reportTableData){
		const newRows = reportTableData.rows.map((row, index) => {
			const order = reportTableData.items.data[index];
			let costAndProfit = [
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
			let newRow = [...row,...costAndProfit];
			if (alg_wc_cog_analytics_obj.individual_order_costs_enabled) {
				let individualCosts = [
					{
						display: storeCurrency.formatAmount(order.items_cost),
						value: order.items_cost,
						type: 'currency'
					},
					{
						display: storeCurrency.formatAmount(order.shipping_cost),
						value: order.shipping_cost,
						type: 'currency'
					},
					{
						display: storeCurrency.formatAmount(order.gateway_cost),
						value: order.gateway_cost,
						type: 'currency'
					},
					{
						display: storeCurrency.formatAmount(order.extra_cost),
						value: order.extra_cost,
						type: 'currency'
					},
					{
						display: storeCurrency.formatAmount(order.shipping_classes_cost),
						value: order.shipping_classes_cost,
						type: 'currency'
					},
				]
				console.log(order)
				newRow = [...row,...individualCosts,...costAndProfit];
			}
			return newRow;
		});
		return newRows;
	},

	init: function () {
		// Reports table
		addFilter(
			'woocommerce_admin_report_table',
			'cost-of-goods-for-woocommerce',
			(reportTableData) => {
				if (
					reportTableData.endpoint !== 'orders' ||
					!reportTableData.items ||
					!reportTableData.items.data ||
					!reportTableData.items.data.length ||
					!alg_wc_cog_analytics_obj.cost_and_profit_columns_enabled_on_orders
				) {
					return reportTableData;
				}
				const newSummary = [
					...reportTableData.summary,
					{
						label: 'Profit',
						value: Formatting.formatProfit(alg_wc_cog_analytics_obj.profit_template,reportTableData.totals.costs_total,reportTableData.totals.profit_total,reportTableData.totals.net_revenue),
					},
				];
				reportTableData.summary = newSummary;
				reportTableData.headers = this.getHeaders(reportTableData);
				reportTableData.rows = this.getRows(reportTableData)//newRows;
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
				if (alg_wc_cog_analytics_obj.cost_and_profit_totals_enabled_on_orders) {
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

