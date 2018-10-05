=== WooCommerce Prune Orders ===
Contributors: seanconklin
Donate link: https://codedcommerce.com/donate
Tags: woocommerce, administrator, tool, trash, prune, trim, clean, performance
Requires at least: 4.7
Tested up to: 4.9.8
Requires PHP: 5.6
Stable tag: 1.1
License: GPLv2 or later

Adds tools to the WP Admin > WooCommerce > Status > Tools page to move all orders of the selected status and cutoff date into the trash, where they can then be permanently deleted to improve site performance.

Greatly improve the performance of a WooCommerce site bogged down by tens of thousands of historic orders. Back orders up using your favorite Order Exports plugin, or rely upon integrated accounting software to keep history beyond the currently active orders in processing.

If you empty the WP Admin > WooCommerce > Orders > Trash with hundreds of orders inside, you may receive a timeout error of one form or another. Usually the trash will continue to clear. If not, simply return to the Trash later to clear out the remaining orders.

== Screenshots ==
 
1. Displays the tools added into WP Admin > WooCommerce > Status > Tools section.
2. Displays a prompt for the date to trim orders up to.
3. Displays one of the tools after being ran with some orders.
4. Displays the orders just moved into the trash.

== Changelog ==

= 1.1 on 2018-10-04 =
* Added: feature to set date to prune up to
* Added: plugin metadata for WooCommerce
* Updated: cleaned PHP array instances to PHP5.4 standard
* Fixed: singular/plural response message

= 1.0 on 2018-07-09 =
* Initial commit
