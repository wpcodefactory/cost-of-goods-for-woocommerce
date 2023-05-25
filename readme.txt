=== Cost of Goods for WooCommerce ===
Contributors: wpcodefactory, karzin, kerbhavik, jaedm97, algoritmika, anbinder, omardabbas, kousikmukherjeeli
Tags: woocommerce, cost, cost of goods, cog, cost of goods sold, cogs, woo commerce
Requires at least: 4.4
Tested up to: 6.2
Stable tag: 2.9.9
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Save product purchase costs (cost of goods) in WooCommerce. Beautifully.

== Description ==

**Cost of Goods for WooCommerce** plugin lets you save WooCommerce products purchase costs (i.e. cost of goods sold).

### &#9989; Main Features: ###

* Set **costs of goods** for your products.
* For **variable products** costs can be saved for each variation separately or for all variations at once.
* Add (sortable) product/order cost/profit **admin columns** to the WooCommerce products and orders lists.
* **Import and export** product costs from and to a **CSV file** with standard WooCommerce Import and Export tools.
* Included **bulk edit costs tool** allows you to bulk edit all products costs, prices and stock from a single page.
* **Import costs tool** is available if you need to import costs from another product metas.
* Optionally add "Cost of Goods" **meta box** to admin order edit page.
* Optionally add **notice** to admin order edit page in case if order **profit is below zero**.
* Optionally set **order extra cost** (all orders or per order), extra **payment gateway costs** and extra **shipping method costs**.
* Use **add stock** tool to automatically calculate average product cost.
* See cost/profit columns on WooCommerce **Analytics > Orders** section.
* And more...

### &#127942; Premium Version ###

**[Cost of Goods for WooCommerce Pro](https://wpfactory.com/item/cost-of-goods-for-woocommerce/)** features:

* Add "Cost" input field to product **bulk** and **quick edit**.
* Add "Add stock" fields (stock and cost) to product **bulk** and **quick edit**.
* **Recalculate orders cost and profit** (for all orders or only for orders with no costs).
* Handle **multicurrency**.
* Add extra costs for your orders based on order's payment gateway, shipping methods or product's shipping classes.
* Support.
* View graphical [costs/profit](https://wpfactory.com/item/cost-of-goods-for-woocommerce/#orders-report) and [stock](https://wpfactory.com/item/cost-of-goods-for-woocommerce/#stock-report) **reports**.
* Available reports on WooCommerce **Analytics** section:
  *  See cost/profit including charts at **Analytics > Orders** tab.
  *  See cost, profit and category columns at **Analytics > Stock** tab.
  *  See cost/profit including charts at **Analytics > Revenue** tab.
* **Compatibility** options with:
  * [WP All Import](https://wordpress.org/plugins/wp-all-import/) plugin.
  * [WPC Product Bundles for WooCommerce](https://wordpress.org/plugins/woo-product-bundle/) plugin.
  * [Openpos - WooCommerce Point Of Sale](https://codecanyon.net/item/openpos-a-complete-pos-plugins-for-woocomerce/22613341) plugin.
  * [Product Add-Ons](https://woocommerce.com/products/product-add-ons/) plugin.
  * [Metorik](https://metorik.com/).
* And more...

= More =

* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* [Visit plugin site](https://wpfactory.com/item/cost-of-goods-for-woocommerce/).
* If you wish to contribute – please visit [plugin GitHub repository](https://github.com/wpcodefactory/cost-of-goods-for-woocommerce).

== Frequently Asked Questions ==

= What can I do if the cost field is not saving? =
If even after clicking "Update" on the product edit page the cost field is not saving, please try to change this option:
**- Cost field position**

= What is the easiest way to export the full inventory (cost of all goods)? =
Probably the easiest way of doing it would be through the default WooCommerce export, over:
**- Products > All products > Export**

= How do I bulk edit cost of goods for a specific product category? =
First, you have to enable two options:

- Products > Quick and Bulk Edit > Cost field > Add "Cost" field to product "Quick Edit"
- Products > Quick and Bulk Edit > Cost field > Add "Cost" field to product "Bulk Actions > Edit"

And then you can follow these steps:

1. Access your products listing page
2. Filter by the category
3. Select all
4. Click edit on "Bulk actions" dropdown
5. Edit the field you want

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > Cost of Goods".

== Screenshots ==

1. Product cost of goods.
2. Cost of goods: variable product.
3. Cost of goods: variation.
4. Order "Cost of Goods" meta box.
5. Product bulk edit costs tool.
6. Cost of goods columns in "Analytics > Orders" report.

== Changelog ==

= 2.9.9 - 25/05/2023 =
* Update function Alg_WC_Cost_of_Goods_Products() ->get_product_price() with condition if product object exist or not .

= 2.9.8 - 16/05/2023 =
* Fix - Analytics - Update dependencies.
* Dev - Tools - Analytics - Orders - New option: Add columns for individual elements from the costs total.
* WC tested up to: 7.7.

= 2.9.7 - 25/04/2023 =
* Fix - Bulk edit prices - Calculation is incorrect.

= 2.9.6 - 18/04/2023 =
* Fix - `class-alg-wc-cog-products.php:456` - Call to a member function is_type() on bool.
* WC tested up to: 7.6.

= 2.9.5 - 17/04/2023 =
* Fix - Products - Cost archive - New price column doesn't take decimals into account.
* Dev - Tools - Bulk edit costs - New option: Filter by cost, allowing to select only products with no costs or with costs.
* Dev - Tools - Bulk edit costs - New section: Variation - Set or update the variations to have the same cost value as their parent products.

= 2.9.4 - 04/04/2023 =
* Fix - "Creation of dynamic property" PHP warning is being triggered on multiple locations.
* Fix - Unsupported operand type: float / string on line 482 in `class-alg-wc-cog-products.php`.

= 2.9.3 - 29/03/2023 =
* Fix - Reports - Stock - Cost of Goods - Products registered with price/cost starting with comma or dot won't be displayed on report.
* Dev - Products - Cost archive - New option: Use REGEXP_SUBSTR function to get the dates.
* Dev - Products - Cost archive - New option: Date format.
* Dev - Tools - Reports - Extra data - Add shipping to profit.
* Dev - Tools - Reports - Extra data - Add fees to profit.
* Dev - Tools - Reports - Extra data - Add taxes to profit.
* WC tested up to: 7.5.
* Tested up to: 6.2.

= 2.9.2 - 08/03/2023 =
* Dev - Tools - Recalculation - Date - New option: After date.
* Dev - Tools - Recalculation - Date - New option: Before date.
* Dev - Tools - Recalculation - Date - New option: Date type.

= 2.9.1 - 21/02/2023 =
* Dev - Shipping - Percent cost - New option: Percent cost source.
* Dev - Shipping - Percent cost - New option: Shipping total calculation method, regarding taxes.

= 2.9.0 - 16/02/2023 =
* Fix - Bulk update prices - Variations are not getting updated.
* Dev - Orders - Calculations - Display "Shipping", "Fees" and "Taxes to profit" on order meta box as extra profit.
* Dev - New filters: `alg_wc_cog_extra_profit_meta_keys`.
* WC tested up to: 7.4.

= 2.8.9 - 06/02/2023 =
* Add German translation.
* Fix - Compatibility - ATUM - Taxes option is not working well.
* Fix - Bulk edit costs - Variation name is incomplete.

= 2.8.8 - 25/01/2023 =
* Fix - Bulk edit tool - Change access control check from `manage_options` to `manage_woocommerce`.
* Fix - Multicurrency - Currencies costs - Profit does not change.
* Dev - Orders - Calculations - New option: Shipping to profit - Percentage.
* Dev - Orders - Admin order edit - Meta box - New option: Allow editing the total order cost value by adding a cost input.
* Dev - New filters - `alg_wc_cog_order_cost`, `alg_wc_cog_order_profit`, `alg_wc_cog_order_metabox_cost_value_html`.

= 2.8.7 - 20/01/2023 =
* Fix - Implement access control and nonce check on Bulk edit tool.
* Fix - Bulk edit prices - Some translations are not working.
* Fix - Compatibility - WC Foods - PHP warning triggers sometimes.
* Dev - Multicurrency - Currencies cost - New option: Add extra costs based on the order currency.
* Dev - Compatibility - ATUM - Taxes - New option: Subtract taxes from ATUM cost while using the "Import" or "Cost sync" options.
* Dev - New filters: `alg_wc_cog_update_order_values`, `alg_wc_cog_currencies_costs_total`, `alg_wc_cog_currencies_costs_percentage_total`.
* Move Multicurrency advanced section to Compatibility section.

= 2.8.6 - 13/01/2023 =
* Fix - Orders - Profit margin calculated in a wrong way.
* WC tested up to: 7.3.

= 2.8.5 - 10/01/2023 =
* Dev - Products - Cost sanitization - Product export - New option: Convert cost to number.
* Dev - Products - Cost sanitization - Product export - New option: Dots and commas operation.

= 2.8.4 - 03/01/2023 =
* Fix - Tools & Reports - Orders tools - PHP error when trying to recalculate order cost and profit.

= 2.8.3 - 27/12/2022 =
* Fix - Compatibility - WC Foods - Fix php warning.
* Fix - Products - Add stock - Negative stock - Stock may increase even with negative values.

= 2.8.2 - 26/12/2022 =
* Dev - Refactor "Add Stock" feature in a new class.
* Dev - Products - Cost archive - New option: Save archive.
* Dev - Products - Cost archive - New option: Enable a cost archive meta box.
* Dev - Compatibility - ATUM - New option: Change cost of goods every time the purchase price is updated in ATUM.
* Dev - Compatibility - WC Foods - New option: Add fixed costs to food options.
* Dev - New filters: `alg_wc_cog_cost_meta_keys`, `alg_wc_cog_update_order_item_values`, `alg_wc_cog_food_options_fixed_costs_total`.
* Dev - New actions: `alg_wc_cog_update_order_values_action`.

= 2.8.1 - 19/12/2022 =
* Dev - Improve the way of initializing the main class.
* Dev - Tools - Import - New option: Run import tool automatically via cron.
* WC tested up to: 7.2.

= 2.8.0 - 02/12/2022 =
* Dev - Tools - Import - New option: Sync with Product Importer from WooCommerce.

= 2.7.9 - 22/11/2022 =
* Dev - Tools & Reports - Bulk edit prices - New field allowing to set product prices by absolute profit.

= 2.7.8 - 21/11/2022 =
* Dev - Tools & Reports - Bulk edit prices - New option: Edit tags.
* Dev - Products - Get price method - New option: Get regular price.

= 2.7.7 - 16/11/2022 =
* Dev - Tools & Reports - Bulk edit prices - New option: Filter by tags.

= 2.7.6 - 15/11/2022 =
* Fix - Tools - Analytics - SQL syntax error regarding minus character.

= 2.7.5 - 10/11/2022 =
* Fix - Tools - Analytics - Unknown column `wp_wc_order_product_lookup.product_net_revenue`.

= 2.7.4 - 09/11/2022 =
* Fix - Tools & Reports - Bulk edit costs - Fix profit style on cost field description.
* Fix - Tools & Reports - Bulk edit costs - Remove top save button.
* Fix - Tools & Reports - Bulk edit prices - Update prices button does not work.
* WC tested up to: 7.1.

= 2.7.3 - 03/11/2022 =
* Dev - Tools & Reports - Bulk edit costs - Add tags column.
* Dev - Tools & Reports - Bulk edit costs - By price - New option: Filter by tags.
* Dev - Tools & Reports - Bulk edit costs - By profit - New option: Filter by tags.
* Tested up to: 6.1.

= 2.7.2 - 01/11/2022 =
* Dev - Tools & Reports - Bulk edit costs - Costs - New option: Show profit as cost field description.

= 2.7.1 - 24/10/2022 =
* Fix - Bulk edit costs - Paged parameter overrides search change.
* Fix - Bulk edit costs - Search is using post method.

= 2.7.0 - 21/10/2022 =
* Dev - Products - Cost sanitization - New option: Get only the cost number when using the WooCommerce Importer.

= 2.6.9 - 20/10/2022 =
* Fix - Bulk edit costs - Disabled screen options are being displayed on the page after "Apply" button is clicked.
* WC tested up to: 7.0.

= 2.6.8 - 03/10/2022 =
* Fix - Error: Class WP_Background_Process not found.
* WC tested up to: 6.9.

= 2.6.7 - 08/09/2022 =
* Dev - Products - Admin products list columns - Make profit column sortable.

= 2.6.6 - 08/09/2022 =
* Fix - Remove unnecessary folder from free version.

= 2.6.5 - 31/08/2022 =
* Fix - Products - Add stock option does not update cost.

= 2.6.4 - 18/08/2022 =
* Dev - Tools - Bulk edit prices - Add rounding option.
* Reorganize admin products settings.
* WC tested up to: 6.8.

= 2.6.3 - 28/07/2022 =
* Dev - Tools - Add Bulk edit prices page.
* Dev - Advanced - Create "Costs update hooks" option. Remove `woocommerce_new_order` hook to the calculation work with Avatax + Subscription.
* Dev - Shipping classes - Add option: "Fixed cost calculation".

= 2.6.2 - 21/07/2022 =
* Dev - Compatibility - WPC Product Bundles - Exclude Smart bundle cost from order item on `woocommerce_new_order_item` and `save_post_shop_order`.
* WC tested up to: 6.7.

= 2.6.1 - 11/07/2022 =
* Fix - Shipping classes - Prevent possible error: "Call to a member function get_shipping_class() on bool".
* Dev - Compatibility - WPC Product Bundles - Add option to calculate Smart bundle cost from its items.
* Dev - Compatibility - WPC Product Bundles - Add option to exclude Smart bundle cost from order item.
* WC tested up to: 6.6.

= 2.6.0 - 13/06/2022 =
* Fix - Products - Add stock - Stock being saved as float sometimes causes errors in stock changing calculations.

= 2.5.9 - 04/06/2022 =
* Fix - Error: Call to undefined method Automattic\\WooCommerce\\Admin\\PageController::is_admin_page().

= 2.5.8 - 03/06/2022 =
* Fix - Shipping classes costs are not getting calculated.
* Fix - PHP Deprecated: Function is_admin_page.
* Tested up to: 6.0.

= 2.5.7 - 23/05/2022 =
* Dev - Compatibility - ATUM - New option: "Use function from ATUM plugin to copy the cost meta".
* Dev - Tools - Product import costs tool - Create `alg_wc_cog_copy_product_meta_args` filter.
* Dev - Tools - Product import costs tool - Create `alg_wc_cog_can_copy_cost` filter.
* Dev - Tools - Product Import Costs Tool - Improve `copy_product_meta()` function.
* Dev - Move compatibility code to an exclusive class.
* Dev - Add "Bulk edit costs" and "Import costs" to plugin action links.
* WC tested up to: 6.5.

= 2.5.6 - 10/05/2022 =
* Dev - Remove `package-lock.json`.
* Dev - Sync `_alg_wc_cog_cost` meta between different languages while using Polylang/WPML.

= 2.5.5 - 18/04/2022 =
* Fix - Tools - Analytics - Products tab does not take quantity into consideration.
* Fix - Tools - Analytics - Profit total from products tab is just calculating from totals.
* Dev - Tools - Analytics - Add option to add "Cost" and "Profit" columns to categories tab.
* Dev - Tools - Analytics - Add option to add "Cost" and "Profit" totals to the report chart on the categories tab.
* Dev - Tools - Analytics - Add option to consider stock for cost and profit calculation on stock tab.
* Dev - Tools - Analytics - Add profit to summary based on Product profit HTML template option, except on stock tab.
* WC tested up to: 6.4.

= 2.5.4 - 29/03/2022 =
* Fix - Recalculate orders cost and profit - Order ID is not passed when recalculation doesn't run via background processing.
* Dev - Advanced - Force costs update - Create option to auto fill empty order items costs on order meta update.

= 2.5.3 - 14/03/2022 =
* Fix - Orders - Admin new order email - Too few arguments to function `Alg_WC_Cost_of_Goods_Orders::woocommerce_email_order_meta()` when used with the "Woo Custom Emails" plugin.
* Fix - Orders - Admin new order email - Too few arguments to function `Alg_WC_Cost_of_Goods_Orders::woocommerce_email_order_meta()` when used with the "Woo Custom Emails" plugin.
* Fix - Products - Add stock - Improve method used to update stock. From `update_post_meta()` to `wc_update_product_stock()`.
* Dev - Orders - Extra Costs: From Meta - Use dots to access serialized array metas.

= 2.5.2 - 09/03/2022 =
* Fix - Unsupported operand types: float / string in PHP 8 if Product profit HTML template is set as `%profit%`.
* WC tested up to: 6.3.

= 2.5.1 - 10/02/2022 =
* Fix - Reports - Stock - Cost of goods - Products having costs and prices with decimal places after zero don't get displayed on the report.
* Dev - Tools - Bulk edit costs - Add "by price" and "by profit" sections.
* Dev - Tools - Bulk edit costs - By price - Add option to define the costs from a percentage of product prices.
* Dev - Tools - Bulk edit costs - By profit - Add option to define the costs according to a profit percentage.
* Dev - Tools - Analytics - Add option to add "Cost" and "Profit" columns to products tab.
* Dev - Tools - Analytics - Add option to add "Cost" and "Profit" totals to products tab.
* Tested up to: 5.9.

= 2.5.0 - 19/01/2022 =
* WC tested up to: 6.1.

= 2.4.9 - 06/10/2021 =
* Fix - Add cost of goods on orders placed by WooCommerce REST API.
* Dev - Improve `Alg_WC_Cost_of_Goods_Orders::update_order_items_costs()` function args.
* WC tested up to: 5.7.

= 2.4.8 - 13/09/2021 =
* Dev - Tools - Analytics - Add option to add "Cost" and "Profit" totals columns to revenue tab.
* Dev - Tools - Analytics - Add option to add "Cost" and "Profit" totals columns to the report chart from the revenue tab.
* Improve readme.

= 2.4.7 - 23/08/2021 =
* Fix - Unsupported operand types error when there are variations with empty price.
* Fix - Orders - Admin Order Edit - Item costs - Option is mandatory for calculating the order cost.
* Fix - Advanced - Force costs update on order update doesn't work.
* Fix - Advanced - Restrict by user role - Doesn't work for cost and handling fee input on admin order edit page.
* Dev - Compatibility - Metorik - Add compatibility with Metorik.
* Dev - Compatibility - Metorik - Add option to sync cost with `_wc_cog_cost meta`.
* WC tested up to: 5.6.
* Improve readme.
* Add chinese translation.

= 2.4.6 - 17/08/2021 =
* Fix - Uncaught TypeError: Unsupported operand types: int + string in `Alg_WC_Cost_of_Goods_Orders:888`.
* Dev - Tools - Import - Create "Meta key replaced" option.
* Add compatibility admin settings section.

= 2.4.5 - 16/08/2021 =
* Fix - Check if order is from a `\WC_Order` type on `Alg_WC_Cost_of_Goods_Orders::update_order_items_costs()`.
* Dev - Tools - Analytics - Stock - Create option to add cost and profit columns.
* Dev - Tools - Analytics - Stock - Create option to add category column.
* Dev - Tools - Analytics - Stock - Create option to add a cost of goods filter allowing for example to filter only products with costs.
* Dev - Orders - Admin order edit - Create "Item handling fees" option.
* Dev - Replace `is_plugin_active()` function.
* Dev - Add github deploy setup.
* WC tested up to: 5.5.
* Tested up to: 5.8.

= 2.4.4 - 14/06/2021 =
* Fix - Free and pro plugins can't be active at the same time.

= 2.4.3 - 11/06/2021 =
* Fix - Some products are empty on Cost of goods stock reports csv.
* Dev - Orders - Refunds - Create option to calculate quantity by excluding refunded items.
* Dev - Add "Extra costs: Shipping classes" admin section.
* Dev - Add wpfactory promoting notice.
* Dev - Add composer.
* Dev - Multicurrency - Add "Auto Currencies Rate From exchangerate-api.com" option.
* Dev - Advanced - Add compatibility with WooCommerce Product Add-ons.
* Improve admin settings texts.
* WC tested up to: 5.4.

= 2.4.2 - 20/05/2021 =
* Fix - Unsupported operand types: string * int on alg-wc-cog-orders.php:614 on PHP 8.
* Fix - Unsupported operand types: float - string on PHP 8.
* Dev - Products - Admin products list columns - Add "Column width" option for cost and profit.
* Dev - Products - Admin products list columns - Add "Column width unit" option for cost and profit.
* Dev - Products - Add stock - Add "Cost calculation expression" option.
* Dev - Products - Add stock - Add "Empty cost field" option.
* Dev - Products - Add stock - Add "Negative stock" option.
* WC tested up to: 5.3.

= 2.4.1 - 21/04/2021 =
* Fix - Tools & Reports - Analytics > Orders - Format "Cost" and "Profit" columns as currency.
* Dev - Tools & Reports - Analytics > Orders - Add "Cost" and "Profit" totals to the report charts.
* Add notice on settings page regarding pro version.
* WC tested up to: 5.2.

= 2.4.0 - 25/03/2021 =
* Fix - Advanced - Openpos - Some info doesn't reflect the "Order types" option.
* Fix - Orders - Extra Costs: All Orders - Unsupported operand types.
* Dev - Tools & Reports - Reports - Add "Stock report: Meta query" option.
* Dev - Products - Add "Cost decimals" option.

= 2.3.9 - 15/03/2021 =
* Dev - Advanced - Add compatibility with "Openpos - WooCommerce Point Of Sale" plugin allowing to manage POS orders on orders reports.
* Dev - Products - Add "get price method" option.
* Dev - Advanced - Add compatibility with "Openpos - WooCommerce Point Of Sale" plugin allowing to manage POS orders on orders reports.
* Dev - Add `alg_wc_cog_before_update_order_items_costs` hook.

= 2.3.8 - 22/02/2021 =
* Dev - Tools & Reports - Reports - Stock - Add SKU reference.
* Dev - Advanced - Add compatibility with WPC bundle products for WooCommerce.
* Dev - Developers - Create `alg_wc_cog_stock_report_args` filter.
* WC tested up to: 5.0.

= 2.3.7 - 28/01/2021 =
* Fix "Refund calculation" option.

= 2.3.6 - 27/01/2021 =
* Fix - Orders - Refunds - "Net Payment inclusive of tax" option.
* Fix error - Call to undefined method `OrderRefund::get_total_refunded()`.

= 2.3.5 - 25/01/2021 =
* Fix - Orders - Shipping to profit - PHP Warning.
* Fix - Fix possible Uncaught TypeError when saving product costs.
* Fix - Advanced - Compatibility - WP All Import - "Sanitize float number" option.
* Dev - Orders - Refunds - Add "Refund calculation" option.
* Dev - Orders - Refunds - Add "Net Payment inclusive of tax" option.
* Dev - Calculations - Add "Taxes to profit" option.
* Dev - Create `table_attributes` param for `$args` param from `alg_wc_cog_get_table_html()` function.
* Dev - Orders - Create option to display the order cost and profit on the admin new order email.
* Dev - Products - Add "Sanitize cog meta" option.
* Dev - Products - Create "Add stock" fields option for quick and bulk edit actions.
* WC tested up to: 4.9
* Add question regarding cost fields not saving to FAQ.
* Add question regarding to exporting cost of goods.
* Add question regarding how to bulk edit a category.

= 2.3.4 - 14/12/2020 =
* Fix - Display variation cost fields from the parent product in case the variation cost is empty.
* Dev - Advanced - Compatibility - Add compatibility option with WP All Import plugin.
* Dev - Advanced - Compatibility - WP All Import - Add "Convert to float" option.
* Dev - Advanced - Compatibility - WP All Import - Add "Sanitize float number" option.
* Dev - Advanced - Restriction - Add "Restrict by user role" option.
* Dev - Products - Quick edit - Add "Replace all variations" option.
* Dev - Tools & Reports - Add `alg_wc_cog_create_import_tool_validation` filter.
* Dev - Tools & Reports - Add `alg_wc_cog_create_edit_costs_tool_validation` filter.
* Dev - Tools & Reports - Add `alg_wc_cog_create_report_validation` filter.
* Dev - Tools & Reports - Add `alg_wc_cog_create_analytics_orders_validation` filter.
* Dev - Orders - Add `alg_wc_cog_create_orders_columns_validation` filter.
* Dev - Orders - Add `alg_wc_cog_create_order_meta_box_validation` filter.
* Dev - Products - Add `alg_wc_cog_create_product_columns_validation` filter.
* Dev - Products - Add `alg_wc_cog_create_product_meta_box_validation` filter.
* Dev - Add `alg_wc_cog_create_wc_settings_tab_validation` filter.
* Tested up to: 5.6

= 2.3.3 - 20/11/2020 =
* Fix - Localization - Move `load_plugin_textdomain` function to `init` call.
* WC tested up to: 4.7

= 2.3.2 - 16/11/2020 =
* Fix - Admin Products List Columns - Reduce priority on `manage_edit-product_columns` filter allowing third party solutions to change columns positions.
* Fix - Admin Orders List Columns - Reduce priority on `manage_edit-shop_order_columns` filter allowing third party solutions to change columns positions.
* Dev - Tools & Reports - Product Import Costs Tool - Add "Check key value" option.
* Dev - Tools & Reports - Product Import Costs Tool - Add "Check if key exists" option.

= 2.3.1 - 10/11/2020 =
* Fix - Products - Improve "Add stock" description.
* Dev - Products - General - Add "Cost field position" option.
* Dev - Tools & Reports - Reports - Create "Stock report: Get price method" option.
* Dev - Tools & Reports - Bulk Edit Costs Tool - Add pagination and column sorting.
* WC tested up to: 4.6

= 2.3.0 - 08/10/2020 =
* Fix - Products - Using unfiltered (i.e. default shop) currency symbol in product cost input now.
* Fix - Orders - Using unfiltered (i.e. default shop) currency symbol in order item cost input and per order fees meta box now.
* Fix - Tools & Reports - Orders report - Using unfiltered (i.e. default shop) currency symbol in chart now.
* Fix - Tools & Reports - Product Bulk Edit Costs Tool - Product types - "Variations" product type added.
* Dev - Tools & Reports - Product Import Costs Tool - Add background processing.
* Dev - Tools & Reports - Product Import Costs Tool - Create "Display table" option.
* Dev - Tools & Reports - Recalculate orders cost and profit - Add background processing.
* Dev - Tools & Reports - Fix compatibility with "WooCommerce Order Status & Actions Manager" plugin by managing the `woocommerce_reports_order_statuses` filter.
* Dev - Tools & Reports - Analytics - Settings description fixed.
* Dev - Advanced - Background Processing - Background Add "Send email" option.
* Dev - Advanced - Background Processing - Add "Email to" option.
* Dev - Advanced - Background Processing - Add "Minimum amount" option.
* Dev - Advanced - Background processing - Add `alg_wc_cog_bkg_process_email_params` filter.

= 2.2.0 - 01/10/2020 =
* Fix - Tools & Reports - Analytics - If order doesn't have cost/profit set, it's excluded from the report - this is fixed now.
* Fix - Tools & Reports - Product Bulk Edit Costs Tool - Searching was saving product costs as well - this is fixed now.
* Fix - Settings - Description fixed for all "Percent cost" options.
* Dev - "Multicurrency" section added.
* Dev - Orders - Admin Orders List Columns - "Profit percent" and "Profit margin" columns added.
* Dev - Orders - Admin Order Edit - Meta box - "Order profit HTML template" option added.
* Dev - Orders - Displaying all COG data in default shop currency now (i.e. instead of in order currency).
* Dev - Orders - `update_order_items_costs()` - `alg_wc_cog_order_item_cost` - Order variable added to the filter's params.
* Dev - Orders - `update_order_items_costs()` - `alg_wc_cog_order_shipping_cost_fixed`, `alg_wc_cog_order_shipping_cost_percent`, `alg_wc_cog_order_gateway_cost_fixed`, `alg_wc_cog_order_gateway_cost_percent`, `alg_wc_cog_order_extra_cost_fixed`, `alg_wc_cog_order_extra_cost_percent`, `alg_wc_cog_order_total_for_pecentage_fees`, `alg_wc_cog_order_line_total`, `alg_wc_cog_order_extra_cost_from_meta`, `alg_wc_cog_order_shipping_total`, `alg_wc_cog_order_total_fees` filters added.
* Dev - Tools & Reports - Analytics - "Cost" and "Profit" columns added to the CSV *server* export.
* Dev - Tools & Reports - Analytics - "Cost" and "Profit" columns added to REST report orders schema.
* Dev - Tools & Reports - Product Bulk Edit Costs Tool - Code refactoring (now using `wc_get_products()` function etc.).
* Dev - Tools & Reports - Product Bulk Edit Costs Tool - "Product types" option added.
* Dev - Tools & Reports - Product Bulk Edit Costs Tool - "Search" button added.
* Dev - Tools & Reports - Product Bulk Edit Costs Tool - "No products found" message added.
* Dev - Compatibility - "WooCommerce Point of Sale" plugin (by "Webkul") compatibility added.
* Dev - Compatibility - "Multi Currency for WooCommerce" plugin (by "VillaTheme") compatibility added.
* Dev - Settings - Using unfiltered currency now, i.e. `get_option( 'woocommerce_currency' )` vs `get_woocommerce_currency()`.
* Dev - Core - `get_product_profit_html()` function added (for backward compatibility, e.g. for `wc-frontend-manager-ultimate` plugin).
* Dev - Code refactoring.
* Localization - Turkish (`tr_TR`) translation added.

= 2.1.2 - 17/09/2020 =
* Plugin author updated.

= 2.1.1 - 14/09/2020 =
* Dev - Products - Admin settings rearranged.
* Dev - Products - `alg_wc_cog_get_product_cost` filter added.
* Dev - Orders - Calculations - "Fees to profit" option added.
* Dev - Orders - `update_order_items_costs()` - Additional safe checks added.
* WC tested up to: 4.5.

= 2.1.0 - 21/08/2020 =
* Dev - Products - Cost field added to the "Bookable" products ("WooCommerce Bookings" plugin).
* Dev - Products - General - "Cost field label" option added.
* Dev - Orders - Admin settings rearranged: "General Options" subsection removed; "Admin Order Edit" and "Calculations" subsections added.
* Dev - Orders - Admin Order Edit - "Prepopulate in AJAX" option renamed to "Fill in on add items".
* Dev - Orders - Admin Order Edit - "Save on item edit" option added (defaults to `yes`).
* Dev - Orders - Admin Order Edit - "Repopulate on recalculate" option renamed to '"Recalculate" button'. Option type changed from checkbox to select. "Fill in all item costs with the default costs" and "Save all item costs" options added.
* Dev - Orders - Calculations - "Delay calculations" option added.
* Dev - Tools & Reports - Product Bulk Edit Costs Tool - CSS and JS files minified.
* Dev - Major code refactoring.
* WC tested up to: 4.4.
* Tested up to: 5.5.

= 2.0.0 - 07/08/2020 =
* Dev - Products - General - "Product profit HTML template" option added.
* Dev - Orders - "Extra Costs: From Meta" section added.
* Dev - Orders - General - "Shipping to profit" option added.
* Dev - Orders - General - Meta box - Colors added to the profit and cost values.
* Dev - Orders - General - Meta box - "Cost details" table added (displayed only if cost consists of more than one element).
* Dev - Orders - Saving total items cost and fees (shipping, gateway, order, total etc.) in order meta now.
* Dev - Orders - `alg_wc_cog_order_item_cost` filter added.
* Dev - Tools & Reports - Orders Tools - Now deleting shop order transients after recalculation.
* Dev - Tools & Reports - Orders Tools - "PHP memory limit" option added.
* Dev - Tools & Reports - Product Bulk Edit Costs Tool - "SKU" column added.
* Dev - Tools & Reports - Product Bulk Edit Costs Tool - Title - Frontend product link added.
* Dev - Tools & Reports - Reports - Orders report - "Orders report: Extra data" option added.
* Dev - Tools & Reports - Reports - Orders report - Code refactoring.
* Dev - Tools & Reports - Analytics - Reports caching removed.
* Dev - Tools & Reports - Analytics - Admin settings description updated.
* WC tested up to: 4.3.

= 1.7.2 - 11/06/2020 =
* Dev - Orders - General - "Order total for percentage fees" option added.
* Dev - Extra Costs: Shipping Methods - Now checking for `get_shipping_methods()` method to exist before applying the fees.
* Dev - Import - Now using translated column title in `set_import_mapping_option_default()`.
* Dev - Admin settings descriptions updated ("Extra Cost" to "Extra Costs").

= 1.7.1 - 10/06/2020 =
* Fix - Orders - General - Item costs - Now always recalculating order profit and cost, even if "Item costs" is not set to "Enable".
* Dev - Tools & Reports - Analytics - Now checking if `wc_admin_is_registered_page()` function exists before enqueueing the script.
* Screenshot titles added in readme.txt.
* Descriptions in readme.txt updated.

= 1.7.0 - 04/06/2020 =
* Fix - Orders - Always showing correct order currency in meta boxes and columns now.
* Fix - Payment Gateways - "Recalculate orders cost and profit" tools fixed for "refunded" orders.
* Dev - Products - "Add stock" option added.
* Dev - Products - "Quick and Bulk Edit" options added.
* Dev - Orders - "Count empty cost lines" option added.
* Dev - Orders - "Order Extra Cost: Per Order" options (handling fee, shipping fee, payment fee) added.
* Dev - Orders - Admin Orders List Columns - All columns moved right after "Total" column.
* Dev - Tools & Reports - Analytics - "Orders" option added.
* Dev - Advanced - "Columns Sorting" options added (i.e. columns in "Orders" and "Products" admin lists are sortable now).
* Dev - Admin settings restyled - "General" admin settings section split into new sections: "Products", "Orders", "Advanced"; "Tools" section renamed to "Tools & Reports" etc.
* Dev - Sanitizing all input now.
* Dev - Code refactoring.
* WC tested up to: 4.2.

= 1.6.0 - 13/05/2020 =
* Dev - Advanced Options - "Force costs update on order status change" option added.
* Dev - Advanced Options - "Force costs update on new order item" option added.
* Dev - Reports - Orders - "Orders report: Order status" option added.
* Dev - Code refactoring.
* WC tested up to: 4.1.

= 1.5.2 - 04/05/2020 =
* Fix - Reports - Stock - Displaying products with cost and/or price below `1` now (`NUMERIC` replaced with `DECIMAL` in `meta_query`).
* Dev - Reports - Stock - "Print" and "Export" admin actions added.
* Dev - Advanced Options - "Force costs update on order update" option added.

= 1.5.1 - 23/04/2020 =
* Dev - "Cost" column added to WooCommerce "Products > Export".
* Dev - "Cost" column added to WooCommerce "Products > Import".

= 1.5.0 - 08/04/2020 =
* Dev - "Extra Payment Gateway Costs" section added.
* Dev - "Extra Shipping Method Costs" section added.
* Dev - "Order Extra Cost" subsection added.
* Dev - Showing "N/A" (instead of empty string) when profit can not be calculated (i.e. when product cost is empty).

= 1.4.8 - 01/04/2020 =
* Dev - Product profit - Showing profit percent for variable products now.
* Tested up to: 5.4.

= 1.4.7 - 27/03/2020 =
* Fix - Tools - "plugin settings" links fixed.
* Fix - Admin settings notices fixed.
* Dev - Admin settings descriptions updated.
* readme.txt description updated.
* WC tested up to: 4.0.

= 1.4.6 - 09/03/2020 =
* Dev - Admin Orders List Columns and Admin Products List Columns - Getting order/product ID from filter params now (i.e. instead of `get_the_ID()`).

= 1.4.5 - 18/02/2020 =
* Fix - Admin Order Options - Admin notice - Now showing on single order edit page only.
* Dev - Admin Order Options - "Item costs" option added.

= 1.4.4 - 18/02/2020 =
* Fix - Admin Order Options - Repopulate on recalculate - `check_ajax_referer()` bug fixed.
* Dev - Admin Order Options - "Admin notice" options added.
* Dev - Settings - Tools - Descriptions updated.

= 1.4.3 - 03/02/2020 =
* Dev - Admin Order Options - "Repopulate on recalculate" option added (defaults to `no`).

= 1.4.2 - 28/01/2020 =
* Dev - Admin Order Options - "Prepopulate in AJAX" option added (defaults to `yes`).
* Dev - Settings - "Admin Order Meta Box" subsection renamed to "Admin Order Options".
* Dev - Minor code refactoring.
* WC tested up to: 3.9.

= 1.4.1 - 21/01/2020 =
* Dev - Additional safe checks added when getting product cost from parent product.
* Dev - Code refactoring.

= 1.4.0 - 24/12/2019 =
* Dev - "Admin Order Meta Box" option added.
* Dev - Tools - Bulk Edit Costs - "Edit prices" option added.
* Dev - Tools - Bulk Edit Costs - "Search products" option (and "Search all" value) added.
* Dev - Admin settings split into sections ("General" and "Tools").
* Dev - Code refactoring.
* Tested up to: 5.3.
* WC tested up to: 3.8.

= 1.3.6 - 02/10/2019 =
* Dev - Tools - Bulk Edit Costs - Manage stock - "Stock update method" option added.

= 1.3.5 - 23/09/2019 =
* Dev - Reports - Additional safe checks added (to avoid possible PHP warnings on some servers).

= 1.3.4 - 06/09/2019 =
* Dev - Tools - Bulk Edit Costs - Better styling (modified row).
* Dev - "WooCommerce PDF Invoices, Packing Slips, Delivery Notes & Shipping Labels" plugin compatibility.
* WC tested up to: 3.7.

= 1.3.3 - 04/08/2019 =
* Fix - Tools - Bulk Edit Costs - Search by product title - Now searching in any part of the title (not only from the beginning).
* Dev - Tools - Bulk Edit Costs - Better styling (active row).
* Dev - Tools - Bulk Edit Costs - Manage stock - Trailing zeros removed from stock input.

= 1.3.2 - 08/07/2019 =
* Dev - Tools - Bulk Edit Costs - "Search by product title" input added.
* Dev - Tools - Bulk Edit Costs - "Stock" column added. "Manage stock" option added.
* Dev - Tools - Bulk Edit Costs - "Price" column added.
* Dev - Tools - Bulk Edit Costs - Restyling and minor code refactoring.
* Dev - Reports - "Stock > Cost of goods" report added.

= 1.3.1 - 26/06/2019 =
* Dev - Admin Orders List Columns - "Order statuses" options added.

= 1.3.0 - 18/06/2019 =
* Dev - "Cost of goods" report added (to "Reports > Orders").

= 1.2.0 - 17/05/2019 =
* Dev - Tools - "Bulk Edit Costs" tool added.
* Dev - Admin settings descriptions updated etc.
* Dev - Minor code refactoring.
* WC tested up to: 3.6.
* Tested up to: 5.2.

= 1.1.1 - 19/12/2018 =
* Fix - Core - `add_cost_input_shop_order()` - Getting order on AJAX correctly now.

= 1.1.0 - 06/12/2018 =
* Fix - Comma decimal separator bug fixed.
* Dev - Profit in percent added to profit HTML output.
* Dev - Cost meta changed from `_alg_cost` to `_alg_wc_cog_cost`.
* Dev - Forcing cost of goods to be always set excluding taxes.
* Dev - Saving costs as order item meta.
* Dev - Saving total cost and profit as order meta.
* Dev - Import Costs Tool - Code optimized.
* Dev - Major code refactoring.
* Dev - Plugin URI updated.
* Pro - Dev - "Recalculate orders cost and profit for all orders" option added.
* Pro - Dev - "Recalculate orders cost and profit for orders with no costs" option added.

= 1.0.1 - 17/05/2018 =
* Fix - Cost not saved for simple products - bug fixed.
* Fix - Admin settings link fixed.

= 1.0.0 - 10/05/2018 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
* Initial Release.