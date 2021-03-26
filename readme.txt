=== Cost of Goods for WooCommerce ===
Contributors: wpcodefactory
Tags: woocommerce, cost, cost of goods, cog, cost of goods sold, cogs, woo commerce
Requires at least: 4.4
Tested up to: 5.7
Stable tag: 2.4.0
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Save product purchase costs (cost of goods) in WooCommerce. Beautifully.

== Description ==

**Cost of Goods for WooCommerce** plugin lets you save WooCommerce products purchase costs (i.e. cost of goods sold).

= Main Features =

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
* And more...

= Premium Version =

With [Cost of Goods for WooCommerce Pro](https://wpfactory.com/item/cost-of-goods-for-woocommerce/) you can:

* Add "Cost" input field to product **bulk** and **quick edit**.
* **Recalculate orders cost and profit** (for all orders or only for orders with no costs).
* View graphical [costs/profit](https://wpfactory.com/item/cost-of-goods-for-woocommerce/#orders-report) and [stock](https://wpfactory.com/item/cost-of-goods-for-woocommerce/#stock-report) **reports**.
* Handle **multicurrency**.

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

= 2.4.0 - 25/03/2021 =
* Fix - Advanced - Openpos - Some info doesn't reflect the "Order types" option.
* Fix - Orders - Extra Costs: All Orders - Unsupported operand types.
* Dev - Tools & Reports - Reports - Add "Stock report: Meta query" option.
* Dev - Products - Add "Cost decimals" option.

= 2.3.9 - 15/03/2021 =
* Dev - Advanced - Add compatibility with "Openpos - WooCommerce Point Of Sale" plugin allowing to manage POS orders on orders reports.
* Dev - Products - Add "get price method" option.
* Tested up to: 5.7.

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