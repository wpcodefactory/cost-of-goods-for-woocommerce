!function(o){var t={};function e(r){if(t[r])return t[r].exports;var c=t[r]={i:r,l:!1,exports:{}};return o[r].call(c.exports,c,c.exports,e),c.l=!0,c.exports}e.m=o,e.c=t,e.d=function(o,t,r){e.o(o,t)||Object.defineProperty(o,t,{enumerable:!0,get:r})},e.r=function(o){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(o,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(o,"__esModule",{value:!0})},e.t=function(o,t){if(1&t&&(o=e(o)),8&t)return o;if(4&t&&"object"==typeof o&&o&&o.__esModule)return o;var r=Object.create(null);if(e.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:o}),2&t&&"string"!=typeof o)for(var c in o)e.d(r,c,function(t){return o[t]}.bind(null,c));return r},e.n=function(o){var t=o&&o.__esModule?function(){return o.default}:function(){return o};return e.d(t,"a",t),t},e.o=function(o,t){return Object.prototype.hasOwnProperty.call(o,t)},e.p="",e(e.s=7)}([function(o,t){o.exports=window.wp.i18n},function(o,t,e){var r=e(4),c=e(5),a=e(6);o.exports=function(o){return r(o)||c(o)||a()}},function(o,t){o.exports=window.wp.hooks},function(o,t){o.exports=window.wc.currency},function(o,t){o.exports=function(o){if(Array.isArray(o)){for(var t=0,e=new Array(o.length);t<o.length;t++)e[t]=o[t];return e}}},function(o,t){o.exports=function(o){if(Symbol.iterator in Object(o)||"[object Arguments]"===Object.prototype.toString.call(o))return Array.from(o)}},function(o,t){o.exports=function(){throw new TypeError("Invalid attempt to spread non-iterable instance")}},function(o,t,e){"use strict";e.r(t);var r=e(1),c=e.n(r),a=e(2),s=e(0),n=e(3),i=e.n(n),_={storeCurrency:null,formatProfit:function(o,t,e,r){var c={"%profit%":this.storeCurrency.formatAmount(e),"%profit_percent%":parseFloat(e/t*100).toFixed(2)+"%","%profit_margin%":parseFloat(e/r*100).toFixed(2)+"%"},a=new RegExp(Object.keys(c).join("|"),"gi");return o.replace(a,(function(o){return c[o]}))},setStoreCurrency:function(o){this.storeCurrency=o}},l=i()(wcSettings.currency);_.setStoreCurrency(l);var u={getHeaders:function(o){var t=[{label:Object(s.__)("Cost","cost-of-goods-for-woocommerce"),key:"order_cost",isNumeric:!0},{label:Object(s.__)("Profit","cost-of-goods-for-woocommerce"),key:"order_profit",isNumeric:!0}],e=[].concat(c()(o.headers),t);if(alg_wc_cog_analytics_obj.individual_order_costs_enabled){var r=[{label:Object(s.__)("Items cost","cost-of-goods-for-woocommerce"),key:"items_cost",isNumeric:!0},{label:Object(s.__)("Shipping cost","cost-of-goods-for-woocommerce"),key:"shipping_cost",isNumeric:!0},{label:Object(s.__)("Gateway cost","cost-of-goods-for-woocommerce"),key:"gateway_cost",isNumeric:!0},{label:Object(s.__)("Extra cost","cost-of-goods-for-woocommerce"),key:"extra_cost",isNumeric:!0},{label:Object(s.__)("Shipping classes cost","cost-of-goods-for-woocommerce"),key:"shipping_classes_cost",isNumeric:!0}];e=[].concat(c()(o.headers),r,t)}return e},getRows:function(o){return o.rows.map((function(t,e){var r=o.items.data[e],a=[{display:l.formatAmount(r.order_cost),value:r.order_cost,type:"currency"},{display:l.formatAmount(r.order_profit),value:r.order_profit,type:"currency"}],s=[].concat(c()(t),a);if(alg_wc_cog_analytics_obj.individual_order_costs_enabled){var n=[{display:l.formatAmount(r.items_cost),value:r.items_cost,type:"currency"},{display:l.formatAmount(r.shipping_cost),value:r.shipping_cost,type:"currency"},{display:l.formatAmount(r.gateway_cost),value:r.gateway_cost,type:"currency"},{display:l.formatAmount(r.extra_cost),value:r.extra_cost,type:"currency"},{display:l.formatAmount(r.shipping_classes_cost),value:r.shipping_classes_cost,type:"currency"}];console.log(r),s=[].concat(c()(t),n,a)}return s}))},init:function(){var o=this;Object(a.addFilter)("woocommerce_admin_report_table","cost-of-goods-for-woocommerce",(function(t){if(!("orders"===t.endpoint&&t.items&&t.items.data&&t.items.data.length&&alg_wc_cog_analytics_obj.cost_and_profit_columns_enabled_on_orders))return t;var e=[].concat(c()(t.summary),[{label:"Profit",value:_.formatProfit(alg_wc_cog_analytics_obj.profit_template,t.totals.costs_total,t.totals.profit_total,t.totals.net_revenue)}]);return t.summary=e,t.headers=o.getHeaders(t),t.rows=o.getRows(t),t})),Object(a.addFilter)("woocommerce_admin_orders_report_charts","cost-of-goods-for-woocommerce",(function(o){return alg_wc_cog_analytics_obj.cost_and_profit_totals_enabled_on_orders&&(o=[].concat(c()(o),[{key:"costs_total",label:Object(s.__)("Costs total","cost-of-goods-for-woocommerce"),type:"currency"},{key:"profit_total",label:Object(s.__)("Profit total","cost-of-goods-for-woocommerce"),type:"currency"}])),o}))}},f=i()(wcSettings.currency);_.setStoreCurrency(f);var m=function(){Object(a.addFilter)("woocommerce_admin_report_table","cost-of-goods-for-woocommerce",(function(o){if(!("revenue"===o.endpoint&&o.items&&o.items.data&&o.items.data.length&&alg_wc_cog_analytics_obj.cost_and_profit_columns_enabled_on_revenue))return o;var t=[].concat(c()(o.headers),[{label:Object(s.__)("Cost","cost-of-goods-for-woocommerce"),key:"costs_total",isNumeric:!0},{label:Object(s.__)("Profit","cost-of-goods-for-woocommerce"),key:"profit_total",isNumeric:!0}]),e=o.rows.map((function(t,e){var r=o.items.data[e];return[].concat(c()(t),[{display:f.formatAmount(r.subtotals.costs_total),value:r.costs_total,type:"currency"},{display:f.formatAmount(r.subtotals.profit_total),value:r.profit_total,type:"currency"}])})),r=[].concat(c()(o.summary),[{label:"Profit",value:_.formatProfit(alg_wc_cog_analytics_obj.profit_template,o.totals.costs_total,o.totals.profit_total,o.totals.net_revenue)}]);return o.summary=r,o.headers=t,o.rows=e,o})),Object(a.addFilter)("woocommerce_admin_revenue_report_charts","cost-of-goods-for-woocommerce",(function(o){return alg_wc_cog_analytics_obj.cost_and_profit_totals_enabled_on_revenue&&(o=[].concat(c()(o),[{key:"costs_total",label:Object(s.__)("Costs total","cost-of-goods-for-woocommerce"),type:"currency"},{key:"profit_total",label:Object(s.__)("Profit total","cost-of-goods-for-woocommerce"),type:"currency"}])),o}))},d=i()(wcSettings.currency);_.setStoreCurrency(d);var p={init:function(){this.addColumns(),this.addCOGFilter()},addCOGFilter:function(){alg_wc_cog_analytics_obj.filter_enabled_on_stock&&Object(a.addFilter)("woocommerce_admin_stock_report_filters","cost-of-goods-for-woocommerce",(function(o){return o.push({label:Object(s.__)("Cost of Goods filter","cost-of-goods-for-woocommerce"),staticParams:["paged","per_page"],param:"alg_cog_stock_filter",showFilters:function(){return!0},filters:[{label:Object(s.__)("Disabled","cost-of-goods-for-woocommerce"),value:"all"},{label:Object(s.__)("Products with cost","cost-of-goods-for-woocommerce"),value:"with_cost"}]}),o}))},addColumns:function(){Object(a.addFilter)("woocommerce_admin_report_table","cost-of-goods-for-woocommerce",(function(o){if("stock"!==o.endpoint||!o.items||!o.items.data||!o.items.data.length)return o;var t=c()(o.headers);alg_wc_cog_analytics_obj.cost_and_profit_enabled_on_stock&&(t.push({label:Object(s.__)("Cost","cost-of-goods-for-woocommerce"),key:"product_cost",isNumeric:!0}),t.push({label:Object(s.__)("Profit","cost-of-goods-for-woocommerce"),key:"product_profit",isNumeric:!0})),alg_wc_cog_analytics_obj.category_enabled_on_stock&&t.push({label:Object(s.__)("Category","cost-of-goods-for-woocommerce"),key:"product_cat"});var e=o.rows.map((function(t,e){var r=o.items.data[e],a=c()(t);return alg_wc_cog_analytics_obj.cost_and_profit_enabled_on_stock&&(a.push({display:d.formatAmount(r.product_cost),value:r.product_cost,type:"currency"}),a.push({display:d.formatAmount(r.product_profit),value:r.product_profit,type:"currency"})),alg_wc_cog_analytics_obj.category_enabled_on_stock&&a.push({display:r.product_cat,value:r.product_cat}),a}));return o.headers=t,o.rows=e,o}))}},y=i()(wcSettings.currency);_.setStoreCurrency(y);var b=function(){Object(a.addFilter)("woocommerce_admin_report_table","cost-of-goods-for-woocommerce",(function(o){var t=new URLSearchParams(window.location.search);if("products"!==o.endpoint||!o.items||!o.items.data||!o.items.data.length||!alg_wc_cog_analytics_obj.product_cost_and_profit_columns_enabled&&"/analytics/products"===t.get("path"))return o;var e=[].concat(c()(o.headers),[{label:Object(s.__)("Cost","cost-of-goods-for-woocommerce"),key:"cost",isNumeric:!0},{label:Object(s.__)("Profit","cost-of-goods-for-woocommerce"),key:"profit",isNumeric:!0}]),r=o.rows.map((function(t,e){var r=o.items.data[e];return[].concat(c()(t),[{display:y.formatAmount(r.cost),value:r.cost,type:"currency"},{display:y.formatAmount(r.profit),value:r.profit,type:"currency"}])})),a=[].concat(c()(o.summary),[{label:"Profit",value:_.formatProfit(alg_wc_cog_analytics_obj.profit_template,o.totals.costs_total,o.totals.profit_total,o.totals.net_revenue)}]);return o.summary=a,o.rows=r,o.headers=e,o})),Object(a.addFilter)("woocommerce_admin_products_report_charts","cost-of-goods-for-woocommerce",(function(o){return alg_wc_cog_analytics_obj.product_cost_and_profit_totals_enabled&&(o=[].concat(c()(o),[{key:"costs_total",label:Object(s.__)("Costs total","cost-of-goods-for-woocommerce"),type:"currency"},{key:"profit_total",label:Object(s.__)("Profit total","cost-of-goods-for-woocommerce"),type:"currency"}])),o}))},g=i()(wcSettings.currency);_.setStoreCurrency(g);var w=function(){Object(a.addFilter)("woocommerce_admin_report_table","cost-of-goods-for-woocommerce",(function(o){if(!("categories"===o.endpoint&&o.items&&o.items.data&&o.items.data.length&&alg_wc_cog_analytics_obj.cost_and_profit_columns_enabled_on_categories))return o;var t=[].concat(c()(o.headers),[{label:Object(s.__)("Cost","cost-of-goods-for-woocommerce"),key:"cost",isNumeric:!0},{label:Object(s.__)("Profit","cost-of-goods-for-woocommerce"),key:"profit",isNumeric:!0}]),e=o.rows.map((function(t,e){var r=o.items.data[e];return[].concat(c()(t),[{display:g.formatAmount(r.cost),value:r.cost,type:"currency"},{display:g.formatAmount(r.profit),value:r.profit,type:"currency"}])})),r=[].concat(c()(o.summary),[{label:"Profit",value:_.formatProfit(alg_wc_cog_analytics_obj.profit_template,o.totals.costs_total,o.totals.profit_total,o.totals.net_revenue)}]);return o.summary=r,o.rows=e,o.headers=t,o})),Object(a.addFilter)("woocommerce_admin_categories_report_charts","cost-of-goods-for-woocommerce",(function(o){return alg_wc_cog_analytics_obj.cost_and_profit_totals_enabled_on_categories&&(o=[].concat(c()(o),[{key:"costs_total",label:Object(s.__)("Costs total","cost-of-goods-for-woocommerce"),type:"currency"},{key:"profit_total",label:Object(s.__)("Profit total","cost-of-goods-for-woocommerce"),type:"currency"}])),o}))};u.init(),m(),p.init(),b(),w()}]);