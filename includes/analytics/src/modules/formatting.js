let Formatting = {
	storeCurrency: null,
	formatProfit: function (template, cost, profit, price) {
		let placeholders = {
			'%profit%': this.storeCurrency.formatAmount(profit),
			'%profit_percent%': parseFloat(profit / cost * 100).toFixed(2) + "%",
			'%profit_margin%': parseFloat(profit / price * 100).toFixed(2) + "%",
		};
		let regex = new RegExp(Object.keys(placeholders).join('|'), 'gi');
		return template.replace(regex, function (matched) {
			return placeholders[matched];
		});
	},
	setStoreCurrency: function (storeCurrency) {
		this.storeCurrency = storeCurrency;
	}
};
export default Formatting;