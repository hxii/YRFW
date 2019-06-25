# Yotpo Reviews for WooCommerce (YRFW) BETA

### So what is this exactly?
This plugin started it's life as a collection of fixes and modifications on top of the Yotpo WooCommerce plugin.
Then I've decided that the best way to learn and fix something is to make it from scratch, which is exactly what this plugin is.
__It is very important to note that this is STILL work in progress, and things MAY change.__

### What are the BIG changes/differences?
- Completely rewritten code (save from the fixes already introduced prior) in OOP.
- New debug and admin pages.
- Faster/more efficient operation.

### How about more detailed changes?
#### Front-end changes
- DNS prefetch of Yotpo assets.
- Preload of `widget.js` and `widget.css` assets.
- New configurable method of main widget injection (via jQuery); star rating and Q&A to be added later.

#### Back-end changes
##### Code changes
- Everything now divided by classes; Each class only does one thing; Everything is documented.
- Using more efficient and/or correct methods and functions.
- The plugin requires PHP 7.0 and above.
- Updated debugging class with configurable levels and file size limit.

##### Design channges
- Admin and Debug pages use Bootstrap 4.
- I18n - all strings are translateable now - have the back-end in your own language!
- New messages class can show both Bootstrap 4 alerts as well as WordPress notices.

##### Order Submission
- In order to speed up submission times (as I cannot control API response), the following are now cached:
  - `utoken` is now stored in a transient for a period of two weeks.
  - Products are stored and fetched from a JSON file instead of the database.
- Now using RESTful cURL Class by [Dongsheng Cai](http://dongsheng.org) (`Helpers\curl.php`) instead of own library along with a wrapper class (`Helpers\class-yrfw-api-wrapper.php`).
- Once an order is submitted, a transient counter is updated with the total of sent orders (`yotpo_total_orders`).
- Each order gets a meta field of `yotpo_order_sent` with the date when it was submitted.
- Past order submission code was rewritten and now accepts a custom timeframe (in case of the plugin, it's used for both past orders and scheduled submission).
- __New__ option to use WP-Cron to submit orders instead of a hook. This resolves issues for people using fulfillment services that change order status programatically. The scheduled task runs twice a day.
- __Variants__ are now sent as metadata as per the [Yotpo API docs](https://apidocs.yotpo.com/reference#create-an-order-within-the-yotpo-system-metadata). The product itself will always be the main product.

##### Others
- You can no longer "break" the plugin by using incorrect appkey and secret values. You can also reset the authentication.
- Debug page is now easily accessible for debugging purposes.
- Testing a mini dashboard with a couple of widgets (`Widgets\class-yrfw-dashboard.php`).
- Native review exporter has been rewritten and should now be much faster.
- Order chunks for past order submission have been increased to 300 orders.
- Product description is no longer sent as it is unused.