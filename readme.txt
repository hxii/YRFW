=== Yotpo Reviews for WooCommerce (Unofficial) ===
Contributors: Paul (hxii) Glushak
Tags: yotpo,reviews,woocommerce,yrfw
Donate link: http://paulglushak.com/
Requires at least: 5.0
Tested up to: 5.2.3
Requires PHP: 7.2
Stable tag: trunk
License: GPL-3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

Collect and display reviews for your WooCommerce website.

== Description ==
Collect and display reviews for your WooCommerce website.
Some extensions are available here on [GitHub](https://github.com/hxii/YRFW-Extensions)

== Installation ==
1. Install the plugin
2. Enter your app key and secret
3. Press Authenticate
4. Configure to your liking
5. Start collecting reviews

== Screenshots ==
1. Login form.
2. Widget placement options.
3. Help and information screen.

== Frequently Asked Questions ==
= Is this the official plugin? =
No, this is a completely rewritten plugin made by Paul (hxii) Glushak.
= I need help! =
- [The page on my website](https://paulglushak.com/yotpo-reviews-for-woocommerce) is often updated with new information.
- Use support section here on wordpress.org
- Get in touch with me by email: paul@glushak.net

== Changelog ==
= 2.0.1 =
- Added support for extenstions, see https://github.com/hxii/YRFW-Extensions for examples.
- Certain things now became extensions (dashboard, debug page, catalog export, rich snippets).
- CSV Helper is now a base, extended by reviews export class.
- Fix secret missing on initial setup.
- Fix WC check firing too early preventing plugin from loading.
- Added two new methods to API wrapper.
- Code cleanup.
- Added two action points for extensions: `yrfw_extensions_settings` (to add settings forms) and `yrfw_extensions_admin_header`.
- Checked with WC 3.7.0 and WP 5.2.3.
= 2.0.0 =
Initial version