=== Connect to external API - WPGetAPI ===
Contributors: wpgetapi
Tags: api, external api, connect, endpoint, rest
Requires at least: 4.6
Requires PHP: 5.4
Tested up to: 6.1
Stable tag: 1.7.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Connect WordPress to external API's. Send data to API's or recieve and display API data using shortcode or template tag. Connecting to API's has never been easier. 

== Description ==

The easiest way to connect WordPress with external API's. WPGetAPI allows you to easily send data and get data from an unlimited number of 3rd party REST API's. You can then format and display the returned data on your WordPress website using a shortcode or a template tag.

WPGetAPI supports virtually all authentication methods including [OAuth 2.0 authorization](https://wpgetapi.com/downloads/oauth-2-0-authentication/?utm_campaign=OAuth&utm_medium=wporg&utm_source=readme), Bearer token, basic auth, API keys and username/password.

https://www.youtube.com/watch?v=goHkt-t3pok

### Demo

View the live demo and see how we are connecting to several different API's and displaying the data in 3 unique ways.

[LIVE DEMO - Connecting WordPress to external API's](https://wpgetapi.com/demo-connecting-wordpress-to-external-api/?utm_campaign=Demo&utm_medium=wporg&utm_source=readme)

### Major Features

 * Connect your WordPress website to any REST API
 * Unlimited API's & endpoints
 * No coding required
 * Display API data using a template tag or shortcode
 * GET, POST, PUT & DELETE requests
 * Query string, header & body parameters

### Documentation

We have tons of articles and help available. View these articles below to get started:

 * [Quick Start Guide](https://wpgetapi.com/docs/quick-start-guide/?utm_campaign=Docs&utm_medium=wporg&utm_source=readme)
 * [Step by Step Example](https://wpgetapi.com/docs/step-by-step-example/?utm_campaign=Docs&utm_medium=wporg&utm_source=readme)
 * [Will this work with my API?](https://wpgetapi.com/docs/will-this-work-with-my-api/?utm_campaign=OAuth&utm_medium=wporg&utm_source=readme)

### Extending WPGetAPI

= Pro Plugin =
The **[Pro Plugin](https://wpgetapi.com/downloads/pro-plugin/?utm_campaign=Pro&utm_medium=wporg&utm_source=readme)** provides many extra features that allow you to do some more advanced things with your API's. Features of the Pro Plugin include:

 *  Tokens 
 *  Dynamic variables
 *  Cache API calls
 *  Retrieve nested data
 *  Base64 encoded auth
 *  XML format
 *  Output as HTML

= WooCommerce Import =
The **[WooCommerce Import](https://wpgetapi.com/downloads/woocommerce-import/?utm_campaign=Woocommerce&utm_medium=wporg&utm_source=readme)** plugin allows you to import items/products/listings from your API and create WooCommerce products from these items. You can manually import the products or set it to automatically sync with the API.

= Custom Post Import =
The **[Custom Post Import](https://wpgetapi.com/downloads/custom-post-import/?utm_campaign=Custom-Post&utm_medium=wporg&utm_source=readme)** plugin allows you to import items/products/listings from your API and create custom posts from these items. You can manually import the posts or set it to automatically sync with the API. Supports importing as posts, pages or any other custom post type.

= OAuth 2.0 Authorization =
The **[OAuth 2.0 Authorization](https://wpgetapi.com/downloads/oauth-2-0-authentication/?utm_campaign=OAuth&utm_medium=wporg&utm_source=readme)** plugin allows authorization of your API through the OAuth 2.0 method.


### WPGetAPI Integrations

WPGetAPI integrates extremely well with other WordPress plugins, allowing you to do some very cool things with your API. Click the links below for more info on these integrations.

 * [Charts & Tables from API data using wpDataTables](https://wpgetapi.com/docs/using-with-wpdatatables/?utm_campaign=Integrations&utm_medium=wporg&utm_source=readme)
 * [Gravity Forms send data to API](https://wpgetapi.com/docs/using-with-gravity-forms/?utm_campaign=Integrations&utm_medium=wporg&utm_source=readme)
 * [Contact Form 7 send data to API](https://wpgetapi.com/docs/using-with-contact-form-7/?utm_campaign=Integrations&utm_medium=wporg&utm_source=readme)
 * [WPForms send data to API](https://wpgetapi.com/docs/using-with-wpforms/?utm_campaign=Integrations&utm_medium=wporg&utm_source=readme)
 * [Elementor, DIVI & other page builders](https://wpgetapi.com/docs/elementor-divi-other-page-builders/?utm_campaign=Integrations&utm_medium=wporg&utm_source=readme)


### Translating WPGetAPI

You can translate WPGetAPI into your own language on [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/wpgetapi/)

Here is an article to help [get started with translations](https://make.wordpress.org/polyglots/handbook/translating/first-steps/)

== Installation ==
= Requirements =
* WordPress version 4.6 and later
* PHP 5.6, Tested with PHP 8.0
* cURL

= Usage =

1. Go to the `WPGetAPI -> Setup` menu to add your API's.
2. Once your API's are saved, a new tab is created allowing you to add endpoints.
3. Once your endpoints are saved, you can use the template tag or shortcode to connect to your API and view the data.

== Frequently Asked Questions ==


= Is WPGetAPI free? =

Yes, WPGetAPI’s core features are and always will be free.

[Paid extensions](https://wpgetapi.com/downloads/?utm_campaign=Extensions&utm_medium=wporg&utm_source=faq) are available if you are looking to do more with your API.


= Can I connect to any REST API? =

Yes, most likely. WPGetAPI handles all major methods of authorization and authentication. It will depend on the type of authentication your API is using. Please click the link to view the [types of authentication and authorization](https://wpgetapi.com/docs/authentication-authorization/?utm_campaign=Docs&utm_medium=wporg&utm_source=faq) that are available.


= Can I capture form data and send to my API? =

Yes, with our [Pro Plugin](https://wpgetapi.com/downloads/pro-plugin/?utm_campaign=Pro&utm_medium=wporg&utm_source=faq) you can capture data from your forms and send this data to your API.

This is perfect for lead generation forms, contact forms or really any other type form that requires you to send form data to an API.


= Can I use an XML based API? =

Yes, we support XML with our [Pro Plugin](https://wpgetapi.com/downloads/pro-plugin/?utm_campaign=Pro&utm_medium=wporg&utm_source=faq)


= How do I connect WordPress to an API? =

By using this plugin of course! Check out the video at the top of this page or visit our [Quick Start Guide](https://wpgetapi.com/docs/quick-start-guide/?utm_campaign=Quick-Start&utm_medium=wporg&utm_source=faq) to get started with connecting your WordPress website with an API.


= How do I cache API calls? =

We support caching with our [Pro Plugin](https://wpgetapi.com/downloads/pro-plugin/?utm_campaign=Pro&utm_medium=wporg&utm_source=faq). It also recognises dynamic queries and will cache these individually.

A dynamic query might be something like these:

/customer-endpoint/customer?user_id=123
/customer-endpoint/customer?user_id=4567

The Pro plugin recognises that whilst they are the same endpoint, they are different queries that need to be cached separately.

= Where can I find docs? =

All of our [documentation can be found here](https://wpgetapi.com/docs/?utm_campaign=Docs&utm_medium=wporg&utm_source=faq).


= What can I do with the API data? =

The API data can be output as a JSON string, stored in a PHP variable or automatically output as HTML (using Pro plugin extension). You can easily format the data into tables or charts plus many other possibilities.


= Will you help me if I am having trouble? =

Yes! Please [contact us](https://wpgetapi.com/contact/?utm_campaign=Contact&utm_medium=wporg&utm_source=faq) and we will get your API up and running.



== Screenshots ==

1. The Setup screen where you can add your external API's
2. Once an external API has been added, a new page will be created to setup the API endpoints
3. A live demo of the output when debug mode is set to true
4. An example of some raw output from an API
5. Basic example of the output from an API formatted into HTML table


== Changelog ==

= 1.7.8 (2023-01-13) =
- New - add new licensing and updating methods.

= 1.7.7 (2023-01-10) =
- New - add ability to set float and integers within paramater settings using float(number) and integer(number) syntax.
- Fix - fix help link for Body paramters going to wrong page.

= 1.7.6 (2023-01-09) =
- Fix - fix error with DELETE not working.

= 1.7.5 (2023-01-09) =
- New - add DELETE method.

= 1.7.4 (2022-12-06) =
- Fix - small fix to allow new custom field renaming within the Custom Post Import plugin.

= 1.7.3 (2022-12-02) =
- New - add full response into the 'Test Endpoint' section.
- Update - readme updates and 'tested up to' update.
- Update - update 3 screenshots.

= 1.7.2 (2022-11-08) =
- New - add warning when trying to output PHP array data in shortcode.
- New - remove banner for Pro plugin. No one likes ads, do they?

= 1.7.1 (2022-11-02) =
- Enhancement - rework the 'Test Endpoint' section in admin to make it cleaner and easier to read (again).
- Enhancement - styling tweaks.

= 1.7.0 (2022-10-06) =
- Enhancement - rework the 'Test Endpoint' section in admin to make it cleaner and easier to read.
- Enhancement - styling tweaks.
- New - add banner for Pro plugin.

= 1.6.1 (2022-08-25) =
- Fix - modify the way the body is retrieved. Required for OAuth 2.0 Authorization plugin.

= 1.6.0 (2022-08-19) =
- New - add endpoint testing within the admin area.

= 1.5.4 (2022-08-15) =
- Fix - change response code action in version 1.5.2 to a filter.
- New - updated styling for admin area.

= 1.5.3 (2022-07-29) =
- Enhancement - add new request method PUT.

= 1.5.2 (2022-07-06) =
- Enhancement - add new action to get response code. Required for OAuth 2.0 Authorization plugin.

= 1.5.1 (2022-07-06) =
- Enhancement - add new shortcode attributes for formatting HTML in Pro plugin.
- Enhancement - minor styling tweaks.
- Fix - very minor bug fixes.

= 1.5.0 (2022-06-27) =
- Fix - fully internationalize the plugin.

= 1.4.10 (2022-06-22) =
- Fix - add new filter 'wpgetapi_json_response_body_before_decode' in place of removing invalid characters from 1.4.8 as this was stripping out non-english values.

= 1.4.9 (2022-06-22) =
- Enhancement - rewrite some css to make endpoint page a bit nicer and add some more screenshots.

= 1.4.8 (2022-06-07) =
- Enhancement - remove invalid characters from JSON data that was causing a null return.

= 1.4.7 (2022-05-25) =
- Fix - change the redirect after saving to a javascript solution

= 1.4.6 (2022-05-24) =
- Enhancement - add new attribute 'format' within shortcode that allows formatting of a number in the Pro Plugin.

= 1.4.5 (2022-05-18) =
- Fix - error in admin-options file.

= 1.4.4 (2022-05-18) =
- Enhancement - add some better, and clearer help in the admin area. Tidy up some styling.
- Fix - error displaying correct endpoint ID within admin area shortcode and template tag helpers. Happening when multiple endpoints added.

= 1.4.3 (2022-05-15) =
- Fix - body was not being set correctly.

= 1.4.2 (2022-05-13) =
- Enhancement - readme updates and plugin links within plugin page.

= 1.4.1 (2022-05-05) =
- Fix - new tab was not appearing on intitial save on setup page.
- Enhancement - add new filter 'wpgetapi_admin_pages' to allow adding extra tabs.

= 1.4.0 (2022-03-17) =
- Fix - refactor the building of request args. Body was not working correctly.
- Fix - change naming convention from Template Function to Template Tag within admin.
- Enhancement - modify output of debug to show more info and to show whether or not shortcode is used.

= 1.3.4 (2022-03-17) =
- Enhancement - add ability to use headers and body variables in Pro Plugin.

= 1.3.3 (2022-03-03) =
- Enhancement - style the debug output to make it easier to understand and provide links to docs.

= 1.3.2 (2022-02-22) =
- Bug fix - change paramater value fields to textarea. This allows the proper use of JSON strings within these fields.

= 1.3.1 (2022-02-16) =
- Bug fix - error with class property name that was not allowing proper $args to be sent to remote request

= 1.3.0 (2022-02-08) =
- Fix - rewrite headers parameters section

= 1.2.3 (2021-12-14) =
- Enhancement - add ability for query_variables to be used in shortcode with the Pro Plugin

= 1.2.2 (2021-11-09) =
- Enhancement - add args to debug info. Will be useful for endpoint_variables in Pro Plugin

= 1.2.1 (2021-11-05) =
- Bug fixes with encrypting values

= 1.2.0 (2021-11-04) =
- Enhancement - add option to JSON encode body parameters
- Enhancement - allow simple arrays to be sent in body

= 1.1.0 (2021-11-03) =
- Enhancement - reconfigure debug info
- Bug fixes

= 1.0.2 (2021-11-02) =
- Bug fixes

= 1.0.1 (2021-11-02) =
- Bug fixes

= 1.0.0 (2021-10-27) =
- Initial Release
