=== Email Templates Customizer and Designer for WordPress and WooCommerce ===
Contributors: wpexpertsio
Tags: Email templates, email designer, email customizer, email, woocommerce email
Requires at least: 7.4
Tested up to: 6.1.1
Stable tag: 1.4.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Design and send custom emails with Email Templates plugin for WordPress and WooCommerce
== Description ==

Email Templates is a WordPress plugin that allows you to customize your WooCommerce and WordPress website’s default transactional email designs and templates. 

The easy-to-use editor lets you choose a template style, and you can customize it by adding a logo or text, changing colors, and editing the footer.

= Why use Email templates for your WordPress/WooCommerce website? =
* Live preview your WordPress and WooCommerce emails
* Customize emails to match your brand’s color theme
* Customize your email’s heading, subtitle, and body text (including header and footer text)
* Choose from a variety of template styles
* Configure settings like the sender’s name and email address
* Send test emails to the administrator’s email address
* View your WooCommerce order emails or choose to view a mockup template.
* Select email type from a dropdown list - New order, canceled order, customer processing order, customer completed order, customer refunded order, customer on-hold order, customer invoice, failed order, new account, customer note, reset password.
* Each email type has a default email template and template settings
* Import/export custom style settings
* Send preview email after importing custom style settings

= Compatible with Post SMTP Mailer/Email Log – Best Mail SMTP For WP =
[PostSMTP](https://wordpress.org/plugins/post-smtp/) is a next-generation WP Mail SMTP plugin that assists and improves the email deliverability process of your WordPress website.

**Easy-to-use and reliable** – 300,000+ customers trust Post SMTP Mailer to send their daily WordPress emails to millions of users worldwide.

== Email Templates Features ==

= WordPress Email Template - General Settings =
* Choose the size of your email template (boxed or full-width)
* Add custom CSS to your email template
* Choose the background color for your email template
* Resize the body of the email box using a slider

= WordPress Email Template - Header Settings =
* Add an image to your email template’s header
* Add text and color to your email header.
* Choose an alignment for the email’s header (Left, Center, Right)
* Increase or decrease the size of the text in the header.

= WordPress Email Template - Email Body Settings =
* Set a background color for the email body
* Set a text color for links in the email body

= WordPress Email Template - Footer Settings =
* Edit the text on the email footer
* Choose an alignment for the email footer (Left, Center, Right)
* Set a background color for the email footer
* Resize the footer text using a slider
* Set the color of the text on the footer
* Enable/disable the link to the plugin page (Powered by)

= WooCommerce Email Template Settings for Email Types =
* Add the recipient’s email address
* Add the subject line, heading, subtitle and additional text
* Add the text for the email body
* Add heading and subject line for Full Refund (Customer refunded order template)
* Add heading and subject line for Partial Refund (Customer refunded order template)
* Switch off template for unpaid customers (Customer invoice template) 
* Switch off template for a partial refund (Customer refunded order template)
* Display/hide a payment button in the email template (Customer invoice template)
* Display/hide the account section in the email template (New account template)
* Display/hide a link to the customer’s account (New account template)
* Display/hide the link to password reset in the email template (Reset password template)

= WooCommerce Email Template - Container Settings =
* Set the background color for the container
* Change the content width inside the container
* Enable Fluid Width and set the content inner max width
* Change the border color, radius, and width 
* Change the padding of the container

= WooCommerce Email Template - Header Settings =
* Add a header image to the template
* Link the header image to a website
* Set the header image’s placement (Outside or Insider the container)
* Change the alignment of the image (Left, Center, Right)
* Change the max width of the header image
* Change the background color of the header image
* Change the padding, and style (background color, Alignment, Padding) of the header image
* Change the font, font style, font size, and font weight of the subtitle and heading
* Change the line height of the heading and subtitle
* Set the subtitle placement (Above or Below the heading)

= WooCommerce Email Template - Content Settings =
* Change the background color of the content container
* Change the padding of the content container (top, middle, left, right)
* Change the font size, line height, font family, weight, link color, and text color of the content.
* Change the heading style for the content container (font size, line height, padding [top, right, left, bottom], font family, font style, font weight)
* Set the text transform to None, Uppercase, Lowercase, and Capitalize
* Set the color and text alignment (left, center, right, justify)
* Add a separator and set its color, height, and style (Solid, Double, Groove, Dotted, Dashed, and Ridge)
* Set the order table’s style to light or normal
* Display/hide the product image and set its size/resolution
* Set the order table’s background color (applies on odd rows)
* Change the border width, color, style, padding size, and style of the order table
* Change the background color, padding, and border (width, color, style, text color, alignment) of the address box
* Change the button text color, font size, font family, font weight, background color, padding (top, right, left, bottom), border width, border radius, border color, and alignment of the buttons.

= WooCommerce Email Template - Footer Settings =
* Set the background placement of the footer (inside or outside the body container)
* Change the background color of the footer
* Change the padding of the footer (top, bottom, left, right)
* Display/hide the social section in the footer
* Add multiple social options in the footer
* Set social image source as bundle icon or upload custom icon(s)
* Set color, title, and link for the social icon(s)
* Set color, font size, font family, padding, style, and border width of the footer
* Set text, alignment, font size, font family, font weight, text color, and padding of the footer credit content
* Write custom CSS for footer style


= Minimum Requirements = 
WordPress 4.0.0

= Help with translations =

Send your translations to [Transifex](https://www.transifex.com/projects/p/wp-email-templates/)

= Currently Available in: =
*   English
*   Spanish
*   French
*   Chinese
*   Portuguese
*   Dutch
*   Persian
*   Russian
*   German

= Collaborate in Github = - [https://github.com/wpexpertsio/wordpress-email-templates](https://github.com/wpexpertsio/wordpress-email-templates)

== Installation ==

1. Upload the plugin in /wp-admin/plugin-install.php
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click on Email Templates -> "WordPress Email" to start editing
4. Click on Email Templates -> "WooCommerce Email" to start editing

== Frequently Asked Questions ==

= How to add a custom template? =

Copy the templates folder into your theme, then in functions.php add the following:

`add_filter('mailtpl/customizer_template', function(){
    return get_stylesheet_directory() . '/admin/templates/default.php';
});`

== Screenshots ==

1. Email Templates - Settings
2. Boxed layout
3. Full-width layout
4. Email Notification
5. Email Type
6. Background Color
7. Bundle Icon
8. Send Preview Email
9. Email Type and Text
10. Font Family
11. WooCommerce Emails

== Changelog ==

= 1.4.2 =
* Added toggle switch for users who don't want to use the default WooCommerce template.

= 1.4.1 =
* Fixed WooCommerce required error.

= 1.4 =
* NEW - Design/Customize WooCommerce emails.
* NEW - Added Live preview your WooCommerce emails.

= 1.3.2.1 =
* Emails not being send when multiple emails where being sent at the same time

= 1.3.2 =
* Support for multiple templates PR #29
* Fix Increase priority for the preview template

= 1.3.1.2 =
* Fixed css width
* new filter for default message

= 1.3.1.1 =
* Only filter non html messages
* Fixed bug introduced on 1.3.1

= 1.3.1 =
* Security fix to prevent html injection
* Filter attributes for images

= 1.3 =
* Instead of multiple filters we now just modify wp_mail to make plugin more compatible
with transactional mail plugins

= 1.2.2.3 =
* Fixed issue with maxwith not working on certain installs.

= 1.2.2.2 =
* Fixed issue with boxed layout

= 1.2.2.1 =
* Text domain update

= 1.2.2 =
* Added image support in header text
* Fixed issue with spaces on gravity forms ( gravity plugin needs to be >= 2.2.1.5 )

= 1.2.1 =
* Added shortcode support in header/footer
* Header text now it's used for alt image when using images
* Fixed bug where image was not responsive on mobile devices

= 1.2 =
* Added custom css support on template section
* Added link color in body section
* Updated templates with changes above
* Mailgun / sengrid integration

= 1.1.4 =

* Added body size to template section
* Leaving emtpty from name & from email will let you use other plugins settings now
* Logo alt text is now site description by default
* Removed other panels showing on email templates customizer
* Removed email templates panel from normal customizer

= 1.1.3.1 =
* Fixed woocommerce preview link

= 1.1.3 =
* Fixed bug with some links missing or not clickable
* Added more languages and updated some
* Added more action hooks for devs

= 1.1.2.1 =
* Remove "powered by" by default
* Updated languages

= 1.1.2 =
* Fixed bug with powered by still showing on some mail clients
* Added new languages

= 1.1.1 =
* Added Postman SMTP compatibility
* Added WP SMTP compatibility
* Added Easy WP SMTP compatibility
* Added Easy SMTP Mail compatibility

= 1.1 =
* Fixed bug with wpmandrill
* Added chinese, spanish and portuguese languages
* Added new font size control
* WooCommerce Integration
* Easy Digital Downloads Integration
* Added Email body settings

= 1.0.2 =

* Fixed email link on retrieve password emails from WP

= 1.0.1 =

* Bug - Template is cached to avoid issues when sending multiple emails
* Added fallback text email for non html email clients and to improve inbox hits
* Added site url to the logo/text logo in header
* Fixed some typos in descriptions
* Added Emails templates menu using add_submenu_page


= 1.0 =
* First release