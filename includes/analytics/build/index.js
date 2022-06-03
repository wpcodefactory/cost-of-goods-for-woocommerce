/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@babel/runtime/helpers/arrayLikeToArray.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/arrayLikeToArray.js ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _arrayLikeToArray(arr, len) {
  if (len == null || len > arr.length) len = arr.length;

  for (var i = 0, arr2 = new Array(len); i < len; i++) {
    arr2[i] = arr[i];
  }

  return arr2;
}

module.exports = _arrayLikeToArray;
module.exports["default"] = module.exports, module.exports.__esModule = true;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/arrayWithoutHoles.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/arrayWithoutHoles.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray.js */ "./node_modules/@babel/runtime/helpers/arrayLikeToArray.js");

function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) return arrayLikeToArray(arr);
}

module.exports = _arrayWithoutHoles;
module.exports["default"] = module.exports, module.exports.__esModule = true;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/iterableToArray.js":
/*!****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/iterableToArray.js ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _iterableToArray(iter) {
  if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter);
}

module.exports = _iterableToArray;
module.exports["default"] = module.exports, module.exports.__esModule = true;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/nonIterableSpread.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/nonIterableSpread.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}

module.exports = _nonIterableSpread;
module.exports["default"] = module.exports, module.exports.__esModule = true;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/toConsumableArray.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/toConsumableArray.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var arrayWithoutHoles = __webpack_require__(/*! ./arrayWithoutHoles.js */ "./node_modules/@babel/runtime/helpers/arrayWithoutHoles.js");

var iterableToArray = __webpack_require__(/*! ./iterableToArray.js */ "./node_modules/@babel/runtime/helpers/iterableToArray.js");

var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray.js */ "./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js");

var nonIterableSpread = __webpack_require__(/*! ./nonIterableSpread.js */ "./node_modules/@babel/runtime/helpers/nonIterableSpread.js");

function _toConsumableArray(arr) {
  return arrayWithoutHoles(arr) || iterableToArray(arr) || unsupportedIterableToArray(arr) || nonIterableSpread();
}

module.exports = _toConsumableArray;
module.exports["default"] = module.exports, module.exports.__esModule = true;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js ***!
  \***************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray.js */ "./node_modules/@babel/runtime/helpers/arrayLikeToArray.js");

function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return arrayLikeToArray(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return arrayLikeToArray(o, minLen);
}

module.exports = _unsupportedIterableToArray;
module.exports["default"] = module.exports, module.exports.__esModule = true;

/***/ }),

/***/ "./src/index.js":
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _modules_orders__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./modules/orders */ "./src/modules/orders.js");
/* harmony import */ var _modules_revenue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./modules/revenue */ "./src/modules/revenue.js");
/* harmony import */ var _modules_stock__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./modules/stock */ "./src/modules/stock.js");
/* harmony import */ var _modules_products__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./modules/products */ "./src/modules/products.js");
/* harmony import */ var _modules_categories__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./modules/categories */ "./src/modules/categories.js");
/**
 * Analytics.
 *
 * @version 2.5.5
 * @since   2.4.5
 * @author  WPFactory
 */
// Orders.

_modules_orders__WEBPACK_IMPORTED_MODULE_0__["default"].init(); // Revenue.


_modules_revenue__WEBPACK_IMPORTED_MODULE_1__["default"].init(); // Stock.


_modules_stock__WEBPACK_IMPORTED_MODULE_2__["default"].init(); // Products.


_modules_products__WEBPACK_IMPORTED_MODULE_3__["default"].init(); // Categories.


_modules_categories__WEBPACK_IMPORTED_MODULE_4__["default"].init();

/***/ }),

/***/ "./src/modules/categories.js":
/*!***********************************!*\
  !*** ./src/modules/categories.js ***!
  \***********************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "./node_modules/@babel/runtime/helpers/toConsumableArray.js");
/* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @woocommerce/currency */ "@woocommerce/currency");
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_currency__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _formatting_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./formatting.js */ "./src/modules/formatting.js");


/**
 * Cost of Goods for WooCommerce - Analytics > Categories (WooCommerce Admin) Report.
 *
 */




var storeCurrency = _woocommerce_currency__WEBPACK_IMPORTED_MODULE_3___default()(wcSettings.currency);
_formatting_js__WEBPACK_IMPORTED_MODULE_4__["default"].setStoreCurrency(storeCurrency);
var categories = {
  init: function init() {
    // Reports table
    Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__["addFilter"])('woocommerce_admin_report_table', 'cost-of-goods-for-woocommerce', function (reportTableData) {
      if (reportTableData.endpoint !== 'categories' || !reportTableData.items || !reportTableData.items.data || !reportTableData.items.data.length || !alg_wc_cog_analytics_obj.cost_and_profit_columns_enabled_on_categories) {
        return reportTableData;
      }

      var newHeaders = [].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(reportTableData.headers), [{
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Cost', 'cost-of-goods-for-woocommerce'),
        key: 'cost',
        isNumeric: true //isSortable: true,

      }, {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Profit', 'cost-of-goods-for-woocommerce'),
        key: 'profit',
        isNumeric: true //isSortable: true,

      }]);
      var newRows = reportTableData.rows.map(function (row, index) {
        var item = reportTableData.items.data[index];
        var newRow = [].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(row), [{
          display: storeCurrency.formatAmount(item.cost),
          value: item.cost,
          type: 'currency'
        }, {
          display: storeCurrency.formatAmount(item.profit),
          value: item.profit,
          type: 'currency'
        }]);
        return newRow;
      });
      var newSummary = [].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(reportTableData.summary), [{
        label: 'Profit',
        value: _formatting_js__WEBPACK_IMPORTED_MODULE_4__["default"].formatProfit(alg_wc_cog_analytics_obj.profit_template, reportTableData.totals.costs_total, reportTableData.totals.profit_total, reportTableData.totals.net_revenue)
      }]);
      reportTableData.summary = newSummary;
      reportTableData.rows = newRows;
      reportTableData.headers = newHeaders;
      return reportTableData;
    }); // Charts

    /**
     * @see https://github.com/woocommerce/woocommerce-admin/blob/main/client/analytics/report/orders/config.js#L50-L62
     */

    Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__["addFilter"])('woocommerce_admin_categories_report_charts', 'cost-of-goods-for-woocommerce', function (charts) {
      if (alg_wc_cog_analytics_obj.cost_and_profit_totals_enabled_on_categories) {
        charts = [].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(charts), [{
          key: 'costs_total',
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Costs total', 'cost-of-goods-for-woocommerce'),
          type: 'currency'
        }, {
          key: 'profit_total',
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Profit total', 'cost-of-goods-for-woocommerce'),
          type: 'currency'
        }]);
      }

      return charts;
    });
  }
};
/* harmony default export */ __webpack_exports__["default"] = (categories);

/***/ }),

/***/ "./src/modules/formatting.js":
/*!***********************************!*\
  !*** ./src/modules/formatting.js ***!
  \***********************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var Formatting = {
  storeCurrency: null,
  formatProfit: function formatProfit(template, cost, profit, price) {
    var placeholders = {
      '%profit%': this.storeCurrency.formatAmount(profit),
      '%profit_percent%': parseFloat(profit / cost * 100).toFixed(2) + "%",
      '%profit_margin%': parseFloat(profit / price * 100).toFixed(2) + "%"
    };
    var regex = new RegExp(Object.keys(placeholders).join('|'), 'gi');
    return template.replace(regex, function (matched) {
      return placeholders[matched];
    });
  },
  setStoreCurrency: function setStoreCurrency(storeCurrency) {
    this.storeCurrency = storeCurrency;
  }
};
/* harmony default export */ __webpack_exports__["default"] = (Formatting);

/***/ }),

/***/ "./src/modules/orders.js":
/*!*******************************!*\
  !*** ./src/modules/orders.js ***!
  \*******************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "./node_modules/@babel/runtime/helpers/toConsumableArray.js");
/* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @woocommerce/currency */ "@woocommerce/currency");
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_currency__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _formatting__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./formatting */ "./src/modules/formatting.js");


/**
 * Cost of Goods for WooCommerce - Analytics > Orders (WooCommerce Admin) Report.
 *
 * @see https://github.com/woocommerce/woocommerce-admin/blob/main/client/analytics/report/orders/config.js#L50-L62
 *
 */




var storeCurrency = _woocommerce_currency__WEBPACK_IMPORTED_MODULE_3___default()(wcSettings.currency);
_formatting__WEBPACK_IMPORTED_MODULE_4__["default"].setStoreCurrency(storeCurrency);
var orders = {
  init: function init() {
    // Reports table
    Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__["addFilter"])('woocommerce_admin_report_table', 'cost-of-goods-for-woocommerce', function (reportTableData) {
      if (reportTableData.endpoint !== 'orders' || !reportTableData.items || !reportTableData.items.data || !reportTableData.items.data.length || !alg_wc_cog_analytics_obj.cost_and_profit_columns_enabled_on_orders) {
        return reportTableData;
      }

      var newHeaders = [].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(reportTableData.headers), [{
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Cost', 'cost-of-goods-for-woocommerce'),
        key: 'order_cost',
        isNumeric: true //isSortable: true,

      }, {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Profit', 'cost-of-goods-for-woocommerce'),
        key: 'order_profit',
        isNumeric: true //isSortable: true,

      }]);
      var newRows = reportTableData.rows.map(function (row, index) {
        var order = reportTableData.items.data[index];
        var newRow = [].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(row), [{
          display: storeCurrency.formatAmount(order.order_cost),
          value: order.order_cost,
          type: 'currency'
        }, {
          display: storeCurrency.formatAmount(order.order_profit),
          value: order.order_profit,
          type: 'currency'
        }]);
        return newRow;
      });
      var newSummary = [].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(reportTableData.summary), [{
        label: 'Profit',
        value: _formatting__WEBPACK_IMPORTED_MODULE_4__["default"].formatProfit(alg_wc_cog_analytics_obj.profit_template, reportTableData.totals.costs_total, reportTableData.totals.profit_total, reportTableData.totals.net_revenue)
      }]);
      reportTableData.summary = newSummary;
      reportTableData.headers = newHeaders;
      reportTableData.rows = newRows;
      return reportTableData;
    }); // Charts

    /**
     * @see https://github.com/woocommerce/woocommerce-admin/blob/main/client/analytics/report/orders/config.js#L50-L62
     */

    Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__["addFilter"])('woocommerce_admin_orders_report_charts', 'cost-of-goods-for-woocommerce', function (charts) {
      if (alg_wc_cog_analytics_obj.cost_and_profit_totals_enabled_on_orders) {
        charts = [].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(charts), [{
          key: 'costs_total',
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Costs total', 'cost-of-goods-for-woocommerce'),
          type: 'currency'
        }, {
          key: 'profit_total',
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Profit total', 'cost-of-goods-for-woocommerce'),
          type: 'currency'
        }]);
      }

      return charts;
    });
  }
};
/* harmony default export */ __webpack_exports__["default"] = (orders);

/***/ }),

/***/ "./src/modules/products.js":
/*!*********************************!*\
  !*** ./src/modules/products.js ***!
  \*********************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "./node_modules/@babel/runtime/helpers/toConsumableArray.js");
/* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @woocommerce/currency */ "@woocommerce/currency");
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_currency__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _formatting_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./formatting.js */ "./src/modules/formatting.js");


/**
 * Cost of Goods for WooCommerce - Analytics > Products (WooCommerce Admin) Report.
 *
 */




var storeCurrency = _woocommerce_currency__WEBPACK_IMPORTED_MODULE_3___default()(wcSettings.currency);
_formatting_js__WEBPACK_IMPORTED_MODULE_4__["default"].setStoreCurrency(storeCurrency);
var products = {
  init: function init() {
    // Reports table
    Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__["addFilter"])('woocommerce_admin_report_table', 'cost-of-goods-for-woocommerce', function (reportTableData) {
      var urlParams = new URLSearchParams(window.location.search);

      if (reportTableData.endpoint !== 'products' || !reportTableData.items || !reportTableData.items.data || !reportTableData.items.data.length || !alg_wc_cog_analytics_obj.product_cost_and_profit_columns_enabled && urlParams.get('path') === '/analytics/products') {
        return reportTableData;
      }

      var newHeaders = [].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(reportTableData.headers), [{
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Cost', 'cost-of-goods-for-woocommerce'),
        key: 'cost',
        isNumeric: true //isSortable: true,

      }, {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Profit', 'cost-of-goods-for-woocommerce'),
        key: 'profit',
        isNumeric: true //isSortable: true,

      }]);
      var newRows = reportTableData.rows.map(function (row, index) {
        var item = reportTableData.items.data[index];
        var newRow = [].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(row), [{
          display: storeCurrency.formatAmount(item.cost),
          value: item.cost,
          type: 'currency'
        }, {
          display: storeCurrency.formatAmount(item.profit),
          value: item.profit,
          type: 'currency'
        }]);
        return newRow;
      });
      var newSummary = [].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(reportTableData.summary), [{
        label: 'Profit',
        value: _formatting_js__WEBPACK_IMPORTED_MODULE_4__["default"].formatProfit(alg_wc_cog_analytics_obj.profit_template, reportTableData.totals.costs_total, reportTableData.totals.profit_total, reportTableData.totals.net_revenue)
      }]);
      reportTableData.summary = newSummary;
      reportTableData.rows = newRows;
      reportTableData.headers = newHeaders;
      return reportTableData;
    }); // Charts

    /**
     * @see https://github.com/woocommerce/woocommerce-admin/blob/main/client/analytics/report/orders/config.js#L50-L62
     */

    Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__["addFilter"])('woocommerce_admin_products_report_charts', 'cost-of-goods-for-woocommerce', function (charts) {
      if (alg_wc_cog_analytics_obj.product_cost_and_profit_totals_enabled) {
        charts = [].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(charts), [{
          key: 'costs_total',
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Costs total', 'cost-of-goods-for-woocommerce'),
          type: 'currency'
        }, {
          key: 'profit_total',
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Profit total', 'cost-of-goods-for-woocommerce'),
          type: 'currency'
        }]);
      }

      return charts;
    });
  }
};
/* harmony default export */ __webpack_exports__["default"] = (products);

/***/ }),

/***/ "./src/modules/revenue.js":
/*!********************************!*\
  !*** ./src/modules/revenue.js ***!
  \********************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "./node_modules/@babel/runtime/helpers/toConsumableArray.js");
/* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @woocommerce/currency */ "@woocommerce/currency");
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_currency__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _formatting_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./formatting.js */ "./src/modules/formatting.js");


/**
 * Cost of Goods for WooCommerce - Analytics > Revenue (WooCommerce Admin) Report.
 *
 */




var storeCurrency = _woocommerce_currency__WEBPACK_IMPORTED_MODULE_3___default()(wcSettings.currency);
_formatting_js__WEBPACK_IMPORTED_MODULE_4__["default"].setStoreCurrency(storeCurrency);
var orders = {
  init: function init() {
    // Reports table
    Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__["addFilter"])('woocommerce_admin_report_table', 'cost-of-goods-for-woocommerce', function (reportTableData) {
      if (reportTableData.endpoint !== 'revenue' || !reportTableData.items || !reportTableData.items.data || !reportTableData.items.data.length || !alg_wc_cog_analytics_obj.cost_and_profit_columns_enabled_on_revenue) {
        return reportTableData;
      }

      var newHeaders = [].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(reportTableData.headers), [{
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Cost', 'cost-of-goods-for-woocommerce'),
        key: 'costs_total',
        isNumeric: true //isSortable: true,

      }, {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Profit', 'cost-of-goods-for-woocommerce'),
        key: 'profit_total',
        isNumeric: true //isSortable: true,

      }]);
      var newRows = reportTableData.rows.map(function (row, index) {
        var item = reportTableData.items.data[index];
        var newRow = [].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(row), [{
          display: storeCurrency.formatAmount(item.subtotals.costs_total),
          value: item.costs_total,
          type: 'currency'
        }, {
          display: storeCurrency.formatAmount(item.subtotals.profit_total),
          value: item.profit_total,
          type: 'currency'
        }]);
        return newRow;
      });
      var newSummary = [].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(reportTableData.summary), [{
        label: 'Profit',
        value: _formatting_js__WEBPACK_IMPORTED_MODULE_4__["default"].formatProfit(alg_wc_cog_analytics_obj.profit_template, reportTableData.totals.costs_total, reportTableData.totals.profit_total, reportTableData.totals.net_revenue)
      }]);
      reportTableData.summary = newSummary;
      reportTableData.headers = newHeaders;
      reportTableData.rows = newRows;
      return reportTableData;
    }); // Charts

    /**
     * @see https://github.com/woocommerce/woocommerce-admin/blob/main/client/analytics/report/orders/config.js#L50-L62
     */

    Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__["addFilter"])('woocommerce_admin_revenue_report_charts', 'cost-of-goods-for-woocommerce', function (charts) {
      if (alg_wc_cog_analytics_obj.cost_and_profit_totals_enabled_on_revenue) {
        charts = [].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(charts), [{
          key: 'costs_total',
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Costs total', 'cost-of-goods-for-woocommerce'),
          type: 'currency'
        }, {
          key: 'profit_total',
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Profit total', 'cost-of-goods-for-woocommerce'),
          type: 'currency'
        }]);
      }

      return charts;
    });
  }
};
/* harmony default export */ __webpack_exports__["default"] = (orders);

/***/ }),

/***/ "./src/modules/stock.js":
/*!******************************!*\
  !*** ./src/modules/stock.js ***!
  \******************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "./node_modules/@babel/runtime/helpers/toConsumableArray.js");
/* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @woocommerce/currency */ "@woocommerce/currency");
/* harmony import */ var _woocommerce_currency__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_currency__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _formatting__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./formatting */ "./src/modules/formatting.js");


/**
 * Cost of Goods for WooCommerce - Analytics > Stock (WooCommerce Admin) Report.
 *
 * @see https://github.com/woocommerce/woocommerce-admin/issues/4348.
 * @todo Add cost and profit totals on summary.
 *
 */




var storeCurrency = _woocommerce_currency__WEBPACK_IMPORTED_MODULE_3___default()(wcSettings.currency);
_formatting__WEBPACK_IMPORTED_MODULE_4__["default"].setStoreCurrency(storeCurrency);
var stock = {
  init: function init() {
    this.addColumns();
    this.addCOGFilter();
  },
  addCOGFilter: function addCOGFilter() {
    if (alg_wc_cog_analytics_obj.filter_enabled_on_stock) {
      Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__["addFilter"])('woocommerce_admin_stock_report_filters', 'cost-of-goods-for-woocommerce', function (obj) {
        obj.push({
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Cost of Goods filter', 'cost-of-goods-for-woocommerce'),
          staticParams: ['paged', 'per_page'],
          param: 'alg_cog_stock_filter',
          showFilters: function showFilters() {
            return true;
          },
          filters: [{
            label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Disabled', 'cost-of-goods-for-woocommerce'),
            value: 'all'
          }, {
            label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Products with cost', 'cost-of-goods-for-woocommerce'),
            value: 'with_cost'
          }]
        });
        return obj;
      });
    }
  },
  addColumns: function addColumns() {
    // Reports table
    Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__["addFilter"])('woocommerce_admin_report_table', 'cost-of-goods-for-woocommerce', function (reportTableData) {
      if (reportTableData.endpoint !== 'stock' || !reportTableData.items || !reportTableData.items.data || !reportTableData.items.data.length) {
        return reportTableData;
      }

      var newHeaders = _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(reportTableData.headers); // Cost and profit


      if (alg_wc_cog_analytics_obj.cost_and_profit_enabled_on_stock) {
        newHeaders.push({
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Cost', 'cost-of-goods-for-woocommerce'),
          key: 'product_cost',
          isNumeric: true //isSortable: true,

        });
        newHeaders.push({
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Profit', 'cost-of-goods-for-woocommerce'),
          key: 'product_profit',
          isNumeric: true //isSortable: true,

        });
      } // Category


      if (alg_wc_cog_analytics_obj.category_enabled_on_stock) {
        newHeaders.push({
          label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Category', 'cost-of-goods-for-woocommerce'),
          key: 'product_cat' //isSortable: true,

        });
      }

      var newRows = reportTableData.rows.map(function (row, index) {
        var product = reportTableData.items.data[index];

        var newRow = _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(row); // Cost and profit


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
        } // Category


        if (alg_wc_cog_analytics_obj.category_enabled_on_stock) {
          newRow.push({
            display: product.product_cat,
            value: product.product_cat
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
    });
  }
};
/* harmony default export */ __webpack_exports__["default"] = (stock);

/***/ }),

/***/ "@woocommerce/currency":
/*!**********************************!*\
  !*** external ["wc","currency"] ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wc"]["currency"]; }());

/***/ }),

/***/ "@wordpress/hooks":
/*!*******************************!*\
  !*** external ["wp","hooks"] ***!
  \*******************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["hooks"]; }());

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["i18n"]; }());

/***/ })

/******/ });
//# sourceMappingURL=index.js.map