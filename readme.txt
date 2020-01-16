=== Yotpo Reviews for WooCommerce (Unofficial) ===
Contributors: hxii
Tags: yotpo,reviews,woocommerce,yrfw
Donate link: http://paulglushak.com/
Requires at least: 5.0
Tested up to: 5.4
Requires PHP: 7.2
Stable tag: trunk
License: GPL-3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

Collect and display reviews for your WooCommerce website.

== Description ==
This **(unofficial)** plugin started it's life as a collection of fixes and modifications on top of the Yotpo WooCommerce plugin.
Then I've decided that the best way to learn and fix something is to make it from scratch, which is exactly what this plugin is.
This plugin is also on [GitHub](https://github.com/hxii/YRFW).
Some extensions are available here on [GitHub](https://github.com/hxii/YRFW-Extensions).

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
= Extensions? =
This plugin now supports (basic) extensions, yes!
You can find some extensions made by me on GitHub [here](https://github.com/hxii/YRFW-Extensions).
Just place them in the `/extensions` folder.

== Changelog ==
= 2.0.3 =
- Fixed conversion tracking total sum to be string instead of float.
= 2.0.2 =
- Added `defer` attribute to Yotpo Widget via function since WP doesn't have that. Conversion doesn't work otherwise.
- Minor changes to how the product cache is appended. Thanks Eric for that `Unsupported operand types`. ¯\_(ツ)_/¯
- Some cleanup. We don't like messy code.
- Tested with WP 5.4-alpha-47039 and WC 3.9.0-rc.2. Bleeding edge!
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