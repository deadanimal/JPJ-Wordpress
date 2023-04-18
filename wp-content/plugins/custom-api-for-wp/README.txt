=== Custom API For WP ===
Contributors: cyberlord92
Tags: custom-endpoints, api, rest api, external api, endpoint, rest, rest route, wp rest api, crud, webhooks, wp automate, rest endpoints
Requires at least: 3.0.1
Requires PHP: 5.4
Tested up to: 6.1
Stable tag: 2.5.1
License: MIT/Expat
License URI: https://docs.miniorange.com/mit-license

Create WordPress REST APIs endpoints to interact with the WordPress database to perform SQL operations and connect to external APIs in WordPress.


== Description ==

[Custom API for WP](https://plugins.miniorange.com/custom-api-for-wordpress) plugin helps you to _create custom endpoints/ Custom REST APIs_ into WordPress directly with an interactive **Graphical User Interface (GUI)** to fetch any type of data from any WordPress database tables like **user roles, groups to featured images, or any custom data** or fields as well. You can also use functions like **GET, POST, PUT, DELETE (Insert, Update, Delete)** data with these created Custom endpoint / Custom REST routes.

This plugin also helps you **connect to your external APIs** and platforms to **fetch the data and display via shortcode** as per your design (custom HTML, CSS and JS), post data on third-party platforms on any WordPress events (user creation, Woocommerce events, form submission, membership purchase etc) via WordPress hooks. 

This plugin also has an interactive and simple UI that will allow you to interact with data by creating Custom REST API endpoints. It means you can easily interact with the WordPress database to perform **CRUD operations on data using the custom endpoints created (generated) using [Custom API for WP](https://plugins.miniorange.com/custom-api-for-wordpress) for WP**.

[Custom API for WP](https://plugins.miniorange.com/custom-api-for-wordpress) for WP takes care of writing the **complex SQL queries** to fetch/update data and provides you with a very simple User Interface to create or generate custom REST endpoints.
This plugin also provides the **filter operations in which you can filter the data** you want to show in the API endpoint response.

[Custom API for WP](https://plugins.miniorange.com/custom-api-for-wordpress) for WP  also adds a **layer of security by providing authentication methods**  to these custom-generated API endpoints. The authentication methods which are compatible with the plugin are: 
== ==
* [API Key Authentication](https://developers.miniorange.com/docs/rest-api-authentication/wordpress/api-key-authentication) 
* [JWT Authentication](https://developers.miniorange.com/docs/rest-api-authentication/wordpress/jwt-authentication)
* [Basic Authentication](https://developers.miniorange.com/docs/rest-api-authentication/wordpress/basic-authentication) 
* [OAuth 2.0 Authentication](https://developers.miniorange.com/docs/rest-api-authentication/wordpress/oauth-authentication)

You will also be able to **control the visibility** and customize the metadata attached to the Custom endpoint response. This plugin also **eliminates the risk of unauthorized access** to these custom endpoints that you created as it only gives access after successful user validation.


== Third-party/External API Integration into WordPress ==

* This plugin allows you to **integrate any external or third-party REST API endpoints into WordPress** very easily with the help of our interactive and simple GUI within seconds. With this function of our plugin, you can fetch data to your WordPress ([WooCommerce](https://woocommerce.com/)) site or you can use it to fetch data and process it according to your needs.

* These integrations can also be done on third-party plugin events like form submission using Elementor, [Wpforms](https://wpforms.com/), [Gravityforms](https://www.gravityforms.com/) etc. and also payment status or subscription status based on transactions done via payment gateways like that provided by [WooCommerce](https://woocommerce.com/), [Wpforms](https://wpforms.com/), [Gravityforms](https://www.gravityforms.com/) or any other services. 

* External API integrations can be done on any event of the WordPress like user registration, user membership level change or any other using plugin hooks. 

* [WooCommerce](https://woocommerce.com/) products data can be synced with any external/third-party API provider(Supplier) on a real-time basis with our plugin. 

* This feature also provides the capabilities to register or login users to third-party platforms by making an API request to the third-party platforms. 

* **Data display on front end using shortcode** fetched from external API endpoints.


== Use Case ==
* Access custom data of the WordPress site into your mobile application or web clients via custom REST API Endpoints.
* Create easy to **Custom REST Routes to Create, Read, Update and Delete (CRUD)** WordPress content from client-side JavaScript or external applications.
* **Interact with any standard database** schema/ table or your custom-built schema/ table to fetch/ update/ delete data using the custom API endpoints.
* **Connect two WordPress sites** or connect your WordPress site with a website built in any framework and Get/Update/Insert/Delete (CRUD) data of one website to another website with the help of Custom API and feasibility of connection with External APIs / Custom Endpoints developed in the external Website.
* **Connect with External Rest API Routes to display data on your website** or process the data received from External Endpoint.
* **[Integrate External/third-party REST API endpoints with third-party plugin's payment gateways](https://plugins.miniorange.com/integrate-external-third-party-rest-api-endpoints-into-wordpress)** like that of [WooCommerce](https://woocommerce.com/), [Wpforms](https://wpforms.com/)or any other custom gateway such that the API can be called automatically based on the payment status.
* **Integrate External/ third-party REST API endpoints with custom/ third-party plugins' forms** like that of [Wpforms](https://wpforms.com/), Elementor, [Gravityforms](https://www.gravityforms.com/) etc such that the external APIs can be called on these forms submission or any related events to perform fetch/update/delete operation based on API endpoints.
* **Sync third-party/external API provider's (Supplier's) API Inventory data into [WooCommerce](https://woocommerce.com/)** and display them in the product feed on a real-time basis. [[More detials]](https://plugins.miniorange.com/woocommerce-api-product-sync-with-woocommerce-rest-apis)
* **Integrate external APIs into WooCommerce** - If you have a Woocommerce store and want to update the data like order creation, order status, and user profile update on a third-party platform in real-time, then a solution can be provided using our plugin.
* **Data display using shortcode** - Fetch data from external API with security and display that on WordPress front end pages using the shortcodes with customized design.



== Add-Ons ==

## WooCommerce Products sync via External API | Woocommerce Product Importer using API
* If you have a [WooCommerce](https://woocommerce.com/) store and want to **sync** (add/update/delete) the products from the external inventory warehouse/  store's platform via APIs then it can be using the CUSTOM API plugin along with our **[Woocommerce products sync from external APIs](https://plugins.miniorange.com/woocommerce-api-product-sync-with-woocommerce-rest-apis)**.
*  _Following are the key features_ - 

  1. **Data can be synced automatically** after a certain specified period. (For example, every 4 hours a day).
  2. All the product details like name, description, price, and stock status can be updated along with other custom attributes as well.
  3. **Sync can be done in the background** such that customers using your WP site are not affected.
  4. **Data syncing can also be done by clicking on the sync button manually** in the User Interface.
  5. **[WooCommerce](https://wordpress.org/plugins/woocommerce/) product images can also be added** or updated with ease based on external API data.
  6. **No extra work to import and export CSV/ TXT files manually**. API Integration will do the job automatically.

## Google Merchant Center

* Sync Products between **WordPress and [Google Merchant Center feed](https://www.google.com/retail/solutions/merchant-center/)** (Integrate Wordpress/ WooCommerce Store with Google Merchant Center)
 
* If you have a WordPress/ WooCommerce store or third-party dropshipping plugins like [Alidropship](https://alidropship.com/plugin/) and want to sync the products between the WordPress site and [Google Merchant Center feed](https://www.google.com/retail/solutions/merchant-center/) feed via APIs then it can be achieved using our solution. 
* _Following are key features_ - 

 1. **Real-time data sync** between the WordPress and Google Merchant centre platforms.
 2. **CSV or TXT files import and export are not required**. Everything will be handled automatically via REST APIs.
 3. Any attribute like Image, pricing, variations, quantity, stock status, and description can be **updated easily**.
 4. Solution can be made **compatible with WordPress as WooCommerce store, [Alidropship](https://alidropship.com/plugin/) store** or any third-party plugin which manages the products in WordPress.

## Connect multiple WordPress/Woocommerce sites

* WooCommerce API Product Sync with Multiple WooCommerce Stores.
* We do provide the solution in which the product data stored in one WooCommerce store can also be synced with other [WooCommerce](https://wordpress.org/plugins/woocommerce/) stores using the REST APIs such that the WC stores will be updated on a real-time basis.
* Sync all the Woocommerce data between multiple stores on updating in any one store in real time.

## Zoho Integration with WordPress

* If you have a WordPress site and want to connect it to your [Zoho](https://www.zoho.com/) applications like [Zoho CRM](https://www.zoho.com/in/crm/), [Zoho connect](https://www.zoho.com/connect/), [Zoho Subscription](https://www.zoho.com/in/subscriptions/), [Zoho Inventory](https://www.zoho.com/in/inventory/) etc to perform operations like sync real-time data between these platforms, which involves user profile sync, operate on WordPress or assign membership to the user based on his Zoho subscription etc. With this integration, any Zoho APIs and webhooks can be integrated to perform real-time sync. 

## Google Sheet Integration

* If you have a WordPress/Woocommerce site and want to connect it to Google Sheets, then our plugin can help you achieve that with our Google sheet integrator. It provides both ways to sync the data. So, if you update or create any row in the google sheet, then that data will be synced in WordPress and similarly, if you perform any operation or event in WordPress, then that data can be synced as per your need in the Google sheet on a real-time basis.

## WordPress Automate using Webhooks 

* This plugin can be made compatible to automate WordPress events with external API and webhook to synchronize data between WordPress and external applications. 
For example - If you have WordPress/Woocommerce site and want to sync the user data, product data, posts, and membership data with external platforms, inventories and CRM like Zoho, Hubspot etc., then this plugin can be extended with an add-on to achieve this.

These solutions can be used additionally along with the plugin. To know more details, contact us at _apisupport@xecurify.com_ and let us know your requirements. 

### Free Version Features

* _**Unlimited Custom REST** APIs (endpoints) can be created for the HTTP GET method._
* _Name Custom Endpoints/Custom REST routes as per our wish and need._
* _**Build custom REST routes for all tables** within WordPress._
* _Build custom REST routes for fetching posts and taxonomies._
* _**Fetch any type of data** available in WordPress via custom REST API endpoints._
* _**Full control of Custom REST API responses** without writing a single line of PHP code._
* _Fetch operation available with single WHERE condition._
* _Integrate with **all types of applications**._
* _Create one API connection based on **simple and advanced SQL queries** on the WordPress database._
* _Create one external API connection for all the standard third-party REST API endpoints to fetch/update between WordPress and 3rd party platforms._


### Premium Version Features

* _Create/ Register custom namespaces and routes._
* _Multiple endpoints allowed per REST route._
* _**Create (generate) Custom API routes for posts** and taxonomy creation, modification, and deletion._
* _Supports **all kinds of HTTP Methods** (GET, PUT, POST, DELETE)._
* _**Filters** included to alter and extend default functionality ._
* _Fetch operation available with **multiple custom conditions**._
* _Limit the number of responses you get as a result of Custom Endpoints (API)._
* _Pass the data in application/JSON or application/x-www-formurlencode for HTTP POST and PUT API endpoints._
* _**Complex queries** formation with an Advance mechanism._
* _**Restrict public access** to all Custom REST API Routes with API KEY Authentication method (default) and some other Authentication methods can also be provided as ADD-ON as per requirement like_
  1. REST API endpoints authentication using **OAuth 2.0**
  2. REST API endpoints authentication using **JWT Tokens**
  3. **Basic Authorization** with Username and Password
  4. Authentication from **external OAuth/OIDC provider's token** for REST API endpoints 
* _Create unlimited API endpoints with custom **SQL-based query** on an easy-to-use GUI without any code._
* _Create one API connection based on **simple and advanced SQL queries** on the WordPress database._
* _Create one external API connection for all the standard third-party REST API endpoints to fetch/update between WordPress and 3rd party platforms._


###Enterprise Version Features

* _**All** Premium Version Features._
* _Create (generate) unlimited Custom API endpoints with **custom SQL Query** to create a custom API with your complex SQL query._
* _**Connect with External REST API/ External Endpoints**, also known as third-party REST API endpoints._
* _**External API integration to fetch data in the WordPress**, update data on the External API provider side._
* _**Supports all kinds of HTTP(GET/POST/PUT/UPDATE) Methods.**_
* _Supports **integration with Custom API / Custom Endpoints** of External Website or Platform._
* _**Dynamic WordPress hooks** for each External API / Endpoint connection to perform operations on external data._
* _**Compatibility with Third-Party Plugin Events** like WooCommerce, WPForms, [Gravityforms](https://www.gravityforms.com/), Membership Plugins, etc._
* _**Support for calling External / Custom Endpoints** on third-party plugin events._
* _**Compatibility with third-party plugin’s payment gateways** provided by WooCommerce, Wpforms, PayPal, Stripe or any custom payment gateway._
* _**Support for connection with Custom API / Custom Endpoints** developed in any framework like Java, PHP, NodeJS, .NET, etc._
* _**Support of Dynamic headers** for the External REST APIs / Custom APIs request_ 
* _**Securely access External Endpoints** by passing the required authentication parameter either in the Header or Body._
* _**Display data fetched from external API using shortcode** as per your styling (custom HTML, CSS and JS).
* Both JSON and XML response formats are supported.
* Supports OAuth 2.0 authentication, Basic Authentication, API Key authentication and Bearer token authentication.
* Supports execution of multiple APIs at once which are interdependent.


Authentication-related information can be sent by any suitable REST client for eg-  You can use CURL calls to send HTTP Requests or even any IDE like PHPSTORM or you can go with POSTMAN to send an authentication key.


####Type of APIs supported
* ‘HTTP GET` (This can be used to retrieve data from your WordPress)
* ‘HTTP POST’ (This can be used to insert data in your WordPress)
* ‘HTTP PUT’ (This can be used to update data in your WordPress)
* ‘HTTP DELETE’ (This can be used to delete data in your WordPress)

### Type of Data which you can retrieve with Custom Endpoints
* WP Users and User Meta.
* WP Roles and Capabilities.
* WP Posts, Pages and custom post types.
* WP Options.
* WP Taxonomy.
* [WooCommerce](https://wordpress.org/plugins/woocommerce/)products, WordPress Membership plugins data.
* Custom data, Custom posts, Custom parameters, Custom fields and many more.
* Any third-party plugins or custom table data can be fetched/updated using these custom API endpoints.

== Installation ==

= From your WordPress dashboard =
1. Visit `Plugins > Add New`
2. Search for `Custom API for WP`. Find and Install the `Custom API for WP` plugin by miniOrange
3. Activate the plugin

= From WordPress.org =
1. Download the `Custom API for WP` plugin
2. Unzip and upload the `custom-api-for-wp` directory to your `/wp-content/plugins/` directory.
3. Activate the miniOrange API plugin from your Plugins page.

= Once Activated =
1. Go to the `Settings-> Custom API` menu
2. Click on the `Create API` button
3. Choose data that you want to retrieve with API and conditions to retrieve data
4. Save the configuration and your API will be ready to use.

== Frequently Asked Questions ==

= I do not see the data which I want to send with API. =
Please email us at info@xecurify.com or submit your query from the plugin support form so that we can provide support to you to achieve what you are looking for.
= Can I write create or generate API endpoints in WordPress using my own complex custom SQL Query? =
Yes, the plugin provides this functionality so that any custom endpoints can be created or generated based on your self-defined custom SQL query with any complexity, so SQL query can be used to perform operations using even multiple WP database tables.
= How to integrate External/third-party side(Non-WordPress) REST API endpoints into WordPress? = 
The plugin provides the Graphical User Interface-based feature to integrate or connect to any external API endpoints easily within WordPress and these connections can be used to fetch/update data via these external API endpoints on any WordPress events on a real-time basis.
= How to create API endpoints in WordPress = 
This plugin is exactly meant to do that in which you can easily create any APIs to interact with the WordPress database and perform any operations like GET, POST, PUT, DELETE within seconds along with security.
= How to import/sync products from an external inventory/supplier to my Woocommerce store?=
The plugin can be with our other add-on ‘Woocommerce product sync from external API’ to sync all the products including adding, updating, and deleting products based on what is available on the inventory/supplier’s end.
= Can this plugin be connected to Zoho for integration with WordPress?=
Yes, the plugin’s Connect to External API feature can be used to connect the Zoho platforms with WordPress and sync data in real time. 

== Screenshots ==
1. List all created APIs
2. Create API UI
3. View API 
4. Response to API calls
5. Create REST API using custom SQL
6. External API Integration


== Changelog ==

= 1.1.1 =
* Initial version

= 1.1.2 =
* Added UI changes and contact form bug fix

= 1.1.3 =
* Added feedback form at deactivation

= 1.1.4 =
* Improved SEO and added compatibility with WP 5.5

= 1.1.5 =
* Showing all premium features and Added customer registration tab

= 1.1.6 =
* Bugs and UI fixes 

= 1.1.7 =
* Bugs and UI fixes 

= 1.1.8 =
* Added compatibility with WordPress v5.6

= 1.1.9 =
* Bugfix - Added support for LIKE condition

= 2.1.0 =
* Bug Fixes, Compatibility with WordPress v5.7 and integration with external APIs

= 2.1.1 =
* UI Updates, Bug Fixes

= 2.1.2 = 
* Bug Fixes, Usability improvements, WordPress 5.8 compatibility

= 2.1.3 =
* Security Fixes, UI improvements, WordPress 5.8.2 compatibility 

= 2.1.4 =
* UI bug Fixes and Security Fixes

= 2.2.0 = 
* Compatibility with WordPress 5.9
* Fix for _ (underscore) issue with custom endpoints
* Minor UI Fixes

= 2.3.0 =
* Major UI Updates and usability improvements
* Feature to View created custom API connections
* Support for Custom SQL APIs and External API Integration feature
* Bug & Security Fixes

= 2.4.0 = 
* Compatibility with WordPress 6.0
* Minor Bug Fixes

= 2.5.0 =
* UI Updates and usability improvements
* Bug Fixes

= 2.5.1 =
* Compatibility with WordPress 6.1

== Upgrade Notice ==

= 1.0.0 =  
* First release of the plugin
* Create REST endpoints in WordPress