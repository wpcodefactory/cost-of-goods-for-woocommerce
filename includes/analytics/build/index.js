/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/modules/categories.js":
/*!***********************************!*\
  !*** ./src/modules/categories.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @woocommerce/currency */ "@woocommerce/currency");
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_currency__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _formatting_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./formatting.js */ "./src/modules/formatting.js");
/**
 * Cost of Goods for WooCommerce - Analytics > Categories (WooCommerce Admin) Report.
 *
 */





const storeCurrency = _woocommerce_currency__WEBPACK_IMPORTED_MODULE_2___default()(wcSettings.currency);
_formatting_js__WEBPACK_IMPORTED_MODULE_3__["default"].setStoreCurrency(storeCurrency);
let categories = {
  init: function () {
    // Reports table
    (0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__.addFilter)('woocommerce_admin_report_table', 'cost-of-goods-for-woocommerce', reportTableData => {
      if (reportTableData.endpoint !== 'categories' || !reportTableData.items || !reportTableData.items.data || !reportTableData.items.data.length || !alg_wc_cog_analytics_obj.cost_and_profit_columns_enabled_on_categories) {
        return reportTableData;
      }
      const newHeaders = [...reportTableData.headers, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Cost', 'cost-of-goods-for-woocommerce'),
        key: 'cost',
        isNumeric: true
        //isSortable: true,
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Profit', 'cost-of-goods-for-woocommerce'),
        key: 'profit',
        isNumeric: true
        //isSortable: true,
      }];
      const newRows = reportTableData.rows.map((row, index) => {
        const item = reportTableData.items.data[index];
        const newRow = [...row, {
          display: storeCurrency.formatAmount(item.cost),
          value: item.cost,
          type: 'currency'
        }, {
          display: storeCurrency.formatAmount(item.profit),
          value: item.profit,
          type: 'currency'
        }];
        return newRow;
      });
      const newSummary = [...reportTableData.summary, {
        label: 'Profit',
        value: _formatting_js__WEBPACK_IMPORTED_MODULE_3__["default"].formatProfit(alg_wc_cog_analytics_obj.profit_template, reportTableData.totals.costs_total, reportTableData.totals.profit_total, reportTableData.totals.net_revenue)
      }];
      reportTableData.summary = newSummary;
      reportTableData.rows = newRows;
      reportTableData.headers = newHeaders;
      return reportTableData;
    });
    // Charts
    /**
     * @see https://github.com/woocommerce/woocommerce-admin/blob/main/client/analytics/report/orders/config.js#L50-L62
     */
    (0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__.addFilter)('woocommerce_admin_categories_report_charts', 'cost-of-goods-for-woocommerce', charts => {
      if (alg_wc_cog_analytics_obj.cost_and_profit_totals_enabled_on_categories) {
        charts = [...charts, {
          key: 'costs_total',
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Costs total', 'cost-of-goods-for-woocommerce'),
          type: 'currency'
        }, {
          key: 'profit_total',
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Profit total', 'cost-of-goods-for-woocommerce'),
          type: 'currency'
        }];
      }
      return charts;
    });
  }
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (categories);

/***/ }),

/***/ "./src/modules/customers.js":
/*!**********************************!*\
  !*** ./src/modules/customers.js ***!
  \**********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @woocommerce/currency */ "@woocommerce/currency");
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_currency__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _formatting__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./formatting */ "./src/modules/formatting.js");
/**
 * Cost of Goods for WooCommerce - WooCommerce > Customers report.
 *
 */





const storeCurrency = _woocommerce_currency__WEBPACK_IMPORTED_MODULE_2___default()(wcSettings.currency);
_formatting__WEBPACK_IMPORTED_MODULE_3__["default"].setStoreCurrency(storeCurrency);
let customers = {
  init: function () {
    (0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__.addFilter)('woocommerce_admin_report_table', 'cost-of-goods-for-woocommerce', reportTableData => {
      if (reportTableData.endpoint !== 'customers' || !reportTableData.items || !reportTableData.items.data || !reportTableData.items.data.length || !alg_wc_cog_analytics_obj.cost_and_profit_columns_enabled_on_orders) {
        return reportTableData;
      }
      reportTableData.headers = this.getHeaders(reportTableData);
      reportTableData.rows = this.getRows(reportTableData);
      return reportTableData;
    });
  },
  getHeaders: function (reportTableData) {
    let headers = reportTableData.headers;
    if (alg_wc_cog_analytics_obj.cost_and_profit_columns_enabled_on_customers) {
      const costAndProfitHeaders = [{
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Cost', 'cost-of-goods-for-woocommerce'),
        key: 'costs_total',
        isNumeric: true
        //isSortable: true,
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Profit', 'cost-of-goods-for-woocommerce'),
        key: 'profit_total',
        isNumeric: true
        //isSortable: true,
      }];
      headers = [...reportTableData.headers, ...costAndProfitHeaders];
    }
    return headers;
  },
  getRows: function (reportTableData) {
    const newRows = reportTableData.rows.map((row, index) => {
      const item = reportTableData.items.data[index];
      let costAndProfit = [{
        display: storeCurrency.formatAmount(item.costs_total),
        value: item.costs_total,
        type: 'currency'
      }, {
        display: storeCurrency.formatAmount(item.profit_total),
        value: item.profit_total,
        type: 'currency'
      }];
      let newRow = [...row, ...costAndProfit];
      return newRow;
    });
    return newRows;
  }
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (customers);

/***/ }),

/***/ "./src/modules/formatting.js":
/*!***********************************!*\
  !*** ./src/modules/formatting.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
let Formatting = {
  storeCurrency: null,
  formatProfit: function (template, cost, profit, price) {
    let placeholders = {
      '%profit%': this.storeCurrency.formatAmount(profit),
      '%profit_percent%': parseFloat(profit / cost * 100).toFixed(2) + "%",
      '%profit_margin%': parseFloat(profit / price * 100).toFixed(2) + "%"
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
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Formatting);

/***/ }),

/***/ "./src/modules/orders.js":
/*!*******************************!*\
  !*** ./src/modules/orders.js ***!
  \*******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @woocommerce/currency */ "@woocommerce/currency");
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_currency__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _formatting__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./formatting */ "./src/modules/formatting.js");
/**
 * Cost of Goods for WooCommerce - Analytics > Orders (WooCommerce Admin) Report.
 *
 * @see https://github.com/woocommerce/woocommerce-admin/blob/main/client/analytics/report/orders/config.js#L50-L62
 *
 */





const storeCurrency = _woocommerce_currency__WEBPACK_IMPORTED_MODULE_2___default()(wcSettings.currency);
_formatting__WEBPACK_IMPORTED_MODULE_3__["default"].setStoreCurrency(storeCurrency);
let orders = {
  getHeaders: function (reportTableData) {
    const costAndProfitHeaders = [{
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Cost', 'cost-of-goods-for-woocommerce'),
      key: 'order_cost',
      isNumeric: true
      //isSortable: true,
    }, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Profit', 'cost-of-goods-for-woocommerce'),
      key: 'order_profit',
      isNumeric: true
      //isSortable: true,
    }];
    let headers = [...reportTableData.headers, ...costAndProfitHeaders];
    if (alg_wc_cog_analytics_obj.individual_order_costs_enabled) {
      const individualCostsHeaders = [{
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Items cost', 'cost-of-goods-for-woocommerce'),
        key: 'items_cost',
        isNumeric: true
        //isSortable: true,
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Shipping cost', 'cost-of-goods-for-woocommerce'),
        key: 'shipping_cost',
        isNumeric: true
        //isSortable: true,
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Gateway cost', 'cost-of-goods-for-woocommerce'),
        key: 'gateway_cost',
        isNumeric: true
        //isSortable: true,
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Extra cost', 'cost-of-goods-for-woocommerce'),
        key: 'extra_cost',
        isNumeric: true
        //isSortable: true,
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Shipping classes cost', 'cost-of-goods-for-woocommerce'),
        key: 'shipping_classes_cost',
        isNumeric: true
        //isSortable: true,
      }];
      headers = [...reportTableData.headers, ...individualCostsHeaders, ...costAndProfitHeaders];
    }
    return headers;
  },
  getRows: function (reportTableData) {
    const newRows = reportTableData.rows.map((row, index) => {
      const order = reportTableData.items.data[index];
      let costAndProfit = [{
        display: storeCurrency.formatAmount(order.order_cost),
        value: order.order_cost,
        type: 'currency'
      }, {
        display: storeCurrency.formatAmount(order.order_profit),
        value: order.order_profit,
        type: 'currency'
      }];
      let newRow = [...row, ...costAndProfit];
      if (alg_wc_cog_analytics_obj.individual_order_costs_enabled) {
        let individualCosts = [{
          display: storeCurrency.formatAmount(order.items_cost),
          value: order.items_cost,
          type: 'currency'
        }, {
          display: storeCurrency.formatAmount(order.shipping_cost),
          value: order.shipping_cost,
          type: 'currency'
        }, {
          display: storeCurrency.formatAmount(order.gateway_cost),
          value: order.gateway_cost,
          type: 'currency'
        }, {
          display: storeCurrency.formatAmount(order.extra_cost),
          value: order.extra_cost,
          type: 'currency'
        }, {
          display: storeCurrency.formatAmount(order.shipping_classes_cost),
          value: order.shipping_classes_cost,
          type: 'currency'
        }];
        newRow = [...row, ...individualCosts, ...costAndProfit];
      }
      return newRow;
    });
    return newRows;
  },
  init: function () {
    // Reports table
    (0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__.addFilter)('woocommerce_admin_report_table', 'cost-of-goods-for-woocommerce', reportTableData => {
      if (reportTableData.endpoint !== 'orders' || !reportTableData.items || !reportTableData.items.data || !reportTableData.items.data.length || !alg_wc_cog_analytics_obj.cost_and_profit_columns_enabled_on_orders) {
        return reportTableData;
      }
      const newSummary = [...reportTableData.summary, {
        label: 'Profit',
        value: _formatting__WEBPACK_IMPORTED_MODULE_3__["default"].formatProfit(alg_wc_cog_analytics_obj.profit_template, reportTableData.totals.costs_total, reportTableData.totals.profit_total, reportTableData.totals.net_revenue)
      }];
      reportTableData.summary = newSummary;
      reportTableData.headers = this.getHeaders(reportTableData);
      reportTableData.rows = this.getRows(reportTableData); //newRows;
      return reportTableData;
    });
    // Charts
    /**
     * @see https://github.com/woocommerce/woocommerce-admin/blob/main/client/analytics/report/orders/config.js#L50-L62
     */
    (0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__.addFilter)('woocommerce_admin_orders_report_charts', 'cost-of-goods-for-woocommerce', charts => {
      if (alg_wc_cog_analytics_obj.cost_and_profit_totals_enabled_on_orders) {
        charts = [...charts, {
          key: 'costs_total',
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Costs total', 'cost-of-goods-for-woocommerce'),
          type: 'currency'
        }, {
          key: 'profit_total',
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Profit total', 'cost-of-goods-for-woocommerce'),
          type: 'currency'
        }];
      }
      return charts;
    });
  }
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (orders);

/***/ }),

/***/ "./src/modules/products.js":
/*!*********************************!*\
  !*** ./src/modules/products.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @woocommerce/currency */ "@woocommerce/currency");
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_currency__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _formatting_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./formatting.js */ "./src/modules/formatting.js");
/**
 * Cost of Goods for WooCommerce - Analytics > Products (WooCommerce Admin) Report.
 *
 */





const storeCurrency = _woocommerce_currency__WEBPACK_IMPORTED_MODULE_2___default()(wcSettings.currency);
_formatting_js__WEBPACK_IMPORTED_MODULE_3__["default"].setStoreCurrency(storeCurrency);
let products = {
  init: function () {
    // Reports table
    (0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__.addFilter)('woocommerce_admin_report_table', 'cost-of-goods-for-woocommerce', reportTableData => {
      const urlParams = new URLSearchParams(window.location.search);
      if (reportTableData.endpoint !== 'products' || !reportTableData.items || !reportTableData.items.data || !reportTableData.items.data.length || !alg_wc_cog_analytics_obj.product_cost_and_profit_columns_enabled && urlParams.get('path') === '/analytics/products') {
        return reportTableData;
      }
      const newHeaders = [...reportTableData.headers, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Cost', 'cost-of-goods-for-woocommerce'),
        key: 'cost',
        isNumeric: true
        //isSortable: true,
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Profit', 'cost-of-goods-for-woocommerce'),
        key: 'profit',
        isNumeric: true
        //isSortable: true,
      }];
      const newRows = reportTableData.rows.map((row, index) => {
        const item = reportTableData.items.data[index];
        const newRow = [...row, {
          display: storeCurrency.formatAmount(item.cost),
          value: item.cost,
          type: 'currency'
        }, {
          display: storeCurrency.formatAmount(item.profit),
          value: item.profit,
          type: 'currency'
        }];
        return newRow;
      });
      const newSummary = [...reportTableData.summary, {
        label: 'Profit',
        value: _formatting_js__WEBPACK_IMPORTED_MODULE_3__["default"].formatProfit(alg_wc_cog_analytics_obj.profit_template, reportTableData.totals.costs_total, reportTableData.totals.profit_total, reportTableData.totals.net_revenue)
      }];
      reportTableData.summary = newSummary;
      reportTableData.rows = newRows;
      reportTableData.headers = newHeaders;
      return reportTableData;
    });
    // Charts
    /**
     * @see https://github.com/woocommerce/woocommerce-admin/blob/main/client/analytics/report/orders/config.js#L50-L62
     */
    (0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__.addFilter)('woocommerce_admin_products_report_charts', 'cost-of-goods-for-woocommerce', charts => {
      if (alg_wc_cog_analytics_obj.product_cost_and_profit_totals_enabled) {
        charts = [...charts, {
          key: 'costs_total',
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Costs total', 'cost-of-goods-for-woocommerce'),
          type: 'currency'
        }, {
          key: 'profit_total',
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Profit total', 'cost-of-goods-for-woocommerce'),
          type: 'currency'
        }];
      }
      return charts;
    });
  }
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (products);

/***/ }),

/***/ "./src/modules/revenue.js":
/*!********************************!*\
  !*** ./src/modules/revenue.js ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @woocommerce/currency */ "@woocommerce/currency");
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_currency__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _formatting_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./formatting.js */ "./src/modules/formatting.js");
/**
 * Cost of Goods for WooCommerce - Analytics > Revenue (WooCommerce Admin) Report.
 *
 */





const storeCurrency = _woocommerce_currency__WEBPACK_IMPORTED_MODULE_2___default()(wcSettings.currency);
_formatting_js__WEBPACK_IMPORTED_MODULE_3__["default"].setStoreCurrency(storeCurrency);
let orders = {
  init: function () {
    // Reports table
    (0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__.addFilter)('woocommerce_admin_report_table', 'cost-of-goods-for-woocommerce', reportTableData => {
      if (reportTableData.endpoint !== 'revenue' || !reportTableData.items || !reportTableData.items.data || !reportTableData.items.data.length || !alg_wc_cog_analytics_obj.cost_and_profit_columns_enabled_on_revenue) {
        return reportTableData;
      }
      const newHeaders = [...reportTableData.headers, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Cost', 'cost-of-goods-for-woocommerce'),
        key: 'costs_total',
        isNumeric: true
        //isSortable: true,
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Profit', 'cost-of-goods-for-woocommerce'),
        key: 'profit_total',
        isNumeric: true
        //isSortable: true,
      }];
      const newRows = reportTableData.rows.map((row, index) => {
        const item = reportTableData.items.data[index];
        const newRow = [...row, {
          display: storeCurrency.formatAmount(item.subtotals.costs_total),
          value: item.subtotals.costs_total,
          type: 'currency'
        }, {
          display: storeCurrency.formatAmount(item.subtotals.profit_total),
          value: item.subtotals.profit_total,
          type: 'currency'
        }];
        return newRow;
      });
      const newSummary = [...reportTableData.summary, {
        label: 'Profit',
        value: _formatting_js__WEBPACK_IMPORTED_MODULE_3__["default"].formatProfit(alg_wc_cog_analytics_obj.profit_template, reportTableData.totals.costs_total, reportTableData.totals.profit_total, reportTableData.totals.net_revenue)
      }];
      reportTableData.summary = newSummary;
      reportTableData.headers = newHeaders;
      reportTableData.rows = newRows;
      return reportTableData;
    });
    // Charts
    /**
     * @see https://github.com/woocommerce/woocommerce-admin/blob/main/client/analytics/report/orders/config.js#L50-L62
     */
    (0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__.addFilter)('woocommerce_admin_revenue_report_charts', 'cost-of-goods-for-woocommerce', charts => {
      if (alg_wc_cog_analytics_obj.cost_and_profit_totals_enabled_on_revenue) {
        charts = [...charts, {
          key: 'costs_total',
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Costs total', 'cost-of-goods-for-woocommerce'),
          type: 'currency'
        }, {
          key: 'profit_total',
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Profit total', 'cost-of-goods-for-woocommerce'),
          type: 'currency'
        }];
      }
      return charts;
    });
  }
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (orders);

/***/ }),

/***/ "./src/modules/stock.js":
/*!******************************!*\
  !*** ./src/modules/stock.js ***!
  \******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @woocommerce/currency */ "@woocommerce/currency");
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_currency__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _formatting__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./formatting */ "./src/modules/formatting.js");
/**
 * Cost of Goods for WooCommerce - Analytics > Stock (WooCommerce Admin) Report.
 *
 * @see https://github.com/woocommerce/woocommerce-admin/issues/4348.
 * @todo Add cost and profit totals on summary.
 *
 */





const storeCurrency = _woocommerce_currency__WEBPACK_IMPORTED_MODULE_2___default()(wcSettings.currency);
_formatting__WEBPACK_IMPORTED_MODULE_3__["default"].setStoreCurrency(storeCurrency);
let stock = {
  init: function () {
    this.addColumns();
    this.addCOGFilter();
  },
  addCOGFilter: function () {
    if (alg_wc_cog_analytics_obj.filter_enabled_on_stock) {
      (0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__.addFilter)('woocommerce_admin_stock_report_filters', 'cost-of-goods-for-woocommerce', obj => {
        obj.push({
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Cost of Goods filter', 'cost-of-goods-for-woocommerce'),
          staticParams: ['paged', 'per_page'],
          param: 'alg_cog_stock_filter',
          showFilters: () => true,
          filters: [{
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Disabled', 'cost-of-goods-for-woocommerce'),
            value: 'all'
          },
          //{label: __('Products with cost', 'cost-of-goods-for-woocommerce'), value: 'with_cost'},
          {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Cost of Goods products', 'cost-of-goods-for-woocommerce'),
            value: 'cog_products'
          }]
        });
        return obj;
      });
    }
  },
  addColumns: function () {
    // Reports table
    (0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__.addFilter)('woocommerce_admin_report_table', 'cost-of-goods-for-woocommerce', reportTableData => {
      if (reportTableData.endpoint !== 'stock' || !reportTableData.items || !reportTableData.items.data || !reportTableData.items.data.length) {
        return reportTableData;
      }
      const newHeaders = [...reportTableData.headers];
      if (alg_wc_cog_analytics_obj.extra_data) {
        newHeaders.push({
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Price', 'cost-of-goods-for-woocommerce'),
          key: 'product_price',
          isNumeric: true
          //isSortable: true,
        });
        newHeaders.push({
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Total Price', 'cost-of-goods-for-woocommerce'),
          key: 'product_price_total',
          isNumeric: true
          //isSortable: true,
        });
      }
      // Cost and profit
      if (alg_wc_cog_analytics_obj.cost_and_profit_enabled_on_stock) {
        newHeaders.push({
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Cost', 'cost-of-goods-for-woocommerce'),
          key: 'product_cost',
          isNumeric: true
          //isSortable: true,
        });
        newHeaders.push({
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Total Cost', 'cost-of-goods-for-woocommerce'),
          key: 'product_cost_total',
          isNumeric: true
          //isSortable: true,
        });
        newHeaders.push({
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Profit', 'cost-of-goods-for-woocommerce'),
          key: 'product_profit',
          isNumeric: true
          //isSortable: true,
        });
        newHeaders.push({
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Total Profit', 'cost-of-goods-for-woocommerce'),
          key: 'product_profit_total',
          isNumeric: true
          //isSortable: true,
        });
      }
      if (alg_wc_cog_analytics_obj.extra_data) {
        // Category.
        newHeaders.push({
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Category', 'cost-of-goods-for-woocommerce'),
          key: 'product_cat'
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
            value: product.product_cat
          });
        }
        return newRow;
      });
      if (alg_wc_cog_analytics_obj.cost_and_profit_enabled_on_stock) {
        let cost = reportTableData.totals.cost;
        let profit = reportTableData.totals.profit;
        let costTotals = reportTableData.totals.total_cost;
        let profitTotals = reportTableData.totals.total_profit;
        const newSummary = [...reportTableData.summary, {
          label: 'Cost',
          value: storeCurrency.formatAmount(cost)
        }, {
          label: 'Total cost',
          value: storeCurrency.formatAmount(costTotals)
        }, {
          label: 'Profit',
          value: storeCurrency.formatAmount(profit)
        }, {
          label: 'Total profit',
          value: storeCurrency.formatAmount(profitTotals)
        }];
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
        const newSummaryExtraData = [...reportTableData.summary, {
          label: 'Price',
          value: storeCurrency.formatAmount(price)
        }, {
          label: 'Total price',
          value: storeCurrency.formatAmount(totalPrice)
        }, {
          label: 'Total stock',
          value: totalStock
        }, {
          label: 'Total products',
          value: totalProducts
        }, {
          label: 'Total average cost',
          value: storeCurrency.formatAmount(averageCost)
        }, {
          label: 'Total average price',
          value: storeCurrency.formatAmount(averagePrice)
        }, {
          label: 'Total average profit',
          value: storeCurrency.formatAmount(averageProfit)
        }];
        reportTableData.summary = newSummaryExtraData;
      }
      reportTableData.headers = newHeaders;
      reportTableData.rows = newRows;
      return reportTableData;
    });
  }
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (stock);

/***/ }),

/***/ "./src/modules/variations.js":
/*!***********************************!*\
  !*** ./src/modules/variations.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @woocommerce/currency */ "@woocommerce/currency");
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_currency__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _formatting_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./formatting.js */ "./src/modules/formatting.js");
/**
 * Cost of Goods for WooCommerce - Analytics > Variations (WooCommerce Admin) Report.
 *
 */





const storeCurrency = _woocommerce_currency__WEBPACK_IMPORTED_MODULE_2___default()(wcSettings.currency);
_formatting_js__WEBPACK_IMPORTED_MODULE_3__["default"].setStoreCurrency(storeCurrency);
let products = {
  init: function () {
    // Reports table
    (0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__.addFilter)('woocommerce_admin_report_table', 'cost-of-goods-for-woocommerce', reportTableData => {
      const urlParams = new URLSearchParams(window.location.search);
      if (reportTableData.endpoint !== 'variations' || !reportTableData.items || !reportTableData.items.data || !reportTableData.items.data.length || !alg_wc_cog_analytics_obj.variation_cost_and_profit_columns_enabled && urlParams.get('path') === '/analytics/variations') {
        return reportTableData;
      }
      const newHeaders = [...reportTableData.headers, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Cost', 'cost-of-goods-for-woocommerce'),
        key: 'cost',
        isNumeric: true
        //isSortable: true,
      }, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Profit', 'cost-of-goods-for-woocommerce'),
        key: 'profit',
        isNumeric: true
        //isSortable: true,
      }];
      const newRows = reportTableData.rows.map((row, index) => {
        const item = reportTableData.items.data[index];
        const newRow = [...row, {
          display: storeCurrency.formatAmount(item.cost),
          value: item.cost,
          type: 'currency'
        }, {
          display: storeCurrency.formatAmount(item.profit),
          value: item.profit,
          type: 'currency'
        }];
        return newRow;
      });
      const newSummary = [...reportTableData.summary, {
        label: 'Profit',
        value: _formatting_js__WEBPACK_IMPORTED_MODULE_3__["default"].formatProfit(alg_wc_cog_analytics_obj.profit_template, reportTableData.totals.costs_total, reportTableData.totals.profit_total, reportTableData.totals.net_revenue)
      }];
      reportTableData.summary = newSummary;
      reportTableData.rows = newRows;
      reportTableData.headers = newHeaders;
      return reportTableData;
    });
    // Charts
    /**
     * @see https://github.com/woocommerce/woocommerce-admin/blob/main/client/analytics/report/orders/config.js#L50-L62
     */
    (0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__.addFilter)('woocommerce_admin_variations_report_charts', 'cost-of-goods-for-woocommerce', charts => {
      if (alg_wc_cog_analytics_obj.variation_cost_and_profit_totals_enabled) {
        charts = [...charts, {
          key: 'costs_total',
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Costs total', 'cost-of-goods-for-woocommerce'),
          type: 'currency'
        }, {
          key: 'profit_total',
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Profit total', 'cost-of-goods-for-woocommerce'),
          type: 'currency'
        }];
      }
      return charts;
    });
  }
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (products);

/***/ }),

/***/ "@woocommerce/currency":
/*!**********************************!*\
  !*** external ["wc","currency"] ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["wc"]["currency"];

/***/ }),

/***/ "@wordpress/hooks":
/*!*******************************!*\
  !*** external ["wp","hooks"] ***!
  \*******************************/
/***/ ((module) => {

module.exports = window["wp"]["hooks"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["i18n"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _modules_orders__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./modules/orders */ "./src/modules/orders.js");
/* harmony import */ var _modules_revenue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./modules/revenue */ "./src/modules/revenue.js");
/* harmony import */ var _modules_stock__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./modules/stock */ "./src/modules/stock.js");
/* harmony import */ var _modules_products__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./modules/products */ "./src/modules/products.js");
/* harmony import */ var _modules_variations__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./modules/variations */ "./src/modules/variations.js");
/* harmony import */ var _modules_categories__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./modules/categories */ "./src/modules/categories.js");
/* harmony import */ var _modules_customers__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./modules/customers */ "./src/modules/customers.js");
/**
 * Analytics.
 *
 * @version 3.4.6
 * @since   2.4.5
 * @author  WPFactory
 */

// Orders.

_modules_orders__WEBPACK_IMPORTED_MODULE_0__["default"].init();

// Revenue.

_modules_revenue__WEBPACK_IMPORTED_MODULE_1__["default"].init();

// Stock.

_modules_stock__WEBPACK_IMPORTED_MODULE_2__["default"].init();

// Products.

_modules_products__WEBPACK_IMPORTED_MODULE_3__["default"].init();

// Variations.

_modules_variations__WEBPACK_IMPORTED_MODULE_4__["default"].init();

// Categories.

_modules_categories__WEBPACK_IMPORTED_MODULE_5__["default"].init();

// Customers.

_modules_customers__WEBPACK_IMPORTED_MODULE_6__["default"].init();
})();

/******/ })()
;
//# sourceMappingURL=index.js.map