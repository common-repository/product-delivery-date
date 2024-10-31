=== Product Delivery Date ===
Contributors: ITCS, freemius
Tags: Delivery, Dates, Order Delivery, Product Delivery, Timeslots
Requires at least: 5.2
Requires PHP: 7.4
Tested up to: 6.5
Stable tag: 1.2.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Product Delivery Date is a plugin that allows you to customize the delivery date of a product. With this plugin, you can add a delivery date field to the checkout page and save the delivery date to the order meta data. It also comes with a settings page where you can configure the minimum and maximum delivery time.

== Description ==

Product Delivery Date is a plugin that allows you to customize the delivery date of a product. With this plugin, you can add a delivery date field to the checkout page and save the delivery date to the order meta data. It also comes with a settings page where you can configure the minimum and maximum delivery time.

The Pro version of this plugin offers even more features, such as the ability to manage delivery dates by product and split orders into multiple orders based on the delivery date. It also allows you to manage product delivery dates in bulk from the WooCommerce product page.

With Product Delivery Date, you can offer your customers the freedom to choose when their product is delivered, and you can manage your orders more efficiently with the advanced features the Pro version offers. So why wait? Get Product Delivery Date today and give your customers the freedom to choose when their product is delivered!


== Free Version Features == 
1. Assign global Order Delivery Dates
2. Display Custom Message on the checkout page Date Picker
3. Orders central management from an interactive table
4. Option to easily modify the order delivery date from a datepicker on the table
5. Option to export in excel the orders with delivery dates 
6. Make Order Delivery Date field required in checkout
7. Option to show the available dates on the checkout page
8. Admin-Defined Timeslots: Set up and customize delivery timeslots directly from the admin panel

== Pro Version Features ==
1. All the free version features 
2. Option to enable delivery date per product
3. Assign a delivery date on the admin product settings page 
4. Assign delivery dates to woocommerce products from the product bulk edit options
5. Split orders option. The orders that contain more than one items with different delivery dates, can be split by delivery dates and create sub-orders. 
6. Email notification option. The customer email notification text can be modified from the plugin admin settings
7. Option to easily modify the product delivery date from a datepicker on the Orders with Delivery Dates table  
8. Streamline your delivery schedule with our enhanced admin settings page, where you can now effortlessly set up and manage excluded dates. Whether it's public holidays, store closures, or personal breaks, you can ensure that these dates are blocked out from the delivery calendar, providing you with greater control over your delivery operations. The user-friendly interface allows for quick additions or removals of dates, making your delivery management more efficient and responsive to your business needs

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/customize-product-delivery-date` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Delivery Date screen to configure the plugin

== Frequently Asked Questions ==

= Does this plugin require any other plugins? =

No, this plugin does not require any other plugins.

= Does this plugin support any other languages? =

No, this plugin does not support any other languages.

== Screenshots ==

1. The plugin main settings page
2. The email settings page
3. The orders with delivery dates table
4. The delivery date column in the woocommerce products page
5. The min and max settings in the woocommerce products bulk actions
6. The min and max settings in the products page
7. The order delivery datepicker for products in the checkout page
8. The order delivery datepicker for orders in the checkout page
9. Timeslots Selection on Checkout
10. Timeslots on Checkout
11. Timeslots - admin settings
12. Timeslots - admin settings
13. New exclude dates admin settings page (Pro Feature)
14. New Menu

== Upgrade Notice ==

= Version 1.2.3 = 
SDK Upgrade

== Changelog ==

= Version 1.2.3 =

SDK Upgrade

= Version 1.2.2 = 

Improved the timeslot exclusion logic to adhere to the WordPress-configured timezone, ensuring accurate 24-hour calculations for timeslot visibility. The system now prioritizes the WordPress timezone setting while using GMT as a fallback.
Optimized script loading: The JavaScript responsible for timeslot handling on the checkout page is now conditionally loaded based on the 'Exclude Timeslots within 24 Hours' setting. This enhancement ensures that scripts are only loaded when necessary, aligning with the admin's configuration and improving site performance.

= Version 1.2.1 =

Enhanced Timeslot Selection Logic for Checkout

In the latest update of our Product Delivery Date plugin, we've introduced a more intuitive timeslot selection process for customers at checkout. Now, when the minimum delivery date is set to the next day, our system smartly excludes any timeslots that fall within the next 24 hours, ensuring realistic delivery scheduling.

Key Enhancements:

Timeslots within 24 hours of the current time will be automatically disabled, aligning with rapid delivery constraints.
The timeslot dropdown remains hidden until a delivery date is selected, streamlining the checkout experience.
This feature is activated only if the corresponding admin option is enabled, optimizing performance and loading behavior.

= Version 1.2.0 =

Added missing jquery library of calendar component.

= Version 1.1.9 =

Improved checkout process with a more modern datepicker.
Enhanced user experience with the exclusion of unavailable dates.
New admin settings page for managing excluded dates (PRO Feature).

= Version 1.1.8 =

Admin-Defined Timeslots: Customize delivery timeslots from the admin panel.
Timeslot Dropdowns on Checkout: Dropdown menu of available delivery timeslots for customers.
Enhanced Order Calendar: Integration of new timeslots in a dropdown format for order management.

= Version 1.1.7 =

SDK Upgrade

= Version 1.1.6 =

Code refactoring to offload external libraries.
SDK Upgrade

= Version 1.1.5 =

Important security fix.

= Version 1.1.1 =

Redesigned Orders with Delivery Dates table with new features.
Export to excel functionality for the Orders with Delivery Dates.
Ability to dynamically change the delivery date of an order or a product.
New sorting, filtering, and dynamic search functionality.
Option to make the delivery date required on the checkout page.
Redesigned Order Delivery Date Settings page.

= Version 1.1.0 =

Introduction of the pro version.
Extra option to show orders' available dates on the checkout page.
Support for delivery date by product (pro feature).
Manage product delivery dates massively from the WooCommerce product page (pro feature).
Split orders functionality (pro feature).
Email notification for order splitting (pro feature).
Customizable email notification message (pro feature).

= Version 1.0.1 =

Added sorting functionality for the "Orders with Delivery Dates" table.
Added notice for past orders too in the "Orders with Delivery Dates" table.
Fixed an issue with the client name in the "Orders with Delivery Dates" table.

= 1.0.0 =

Initial release.


== Roadmap ==

The following list outlines the upcoming features for our plugin:

1. Improved user experience with a new, intuitive user interface
2. Order management system, through which the admin will be able to set a maximum number of deliveries per time slot

We welcome feedback on our roadmap and would love to hear your ideas and suggestions. Please let us know how we can improve our plugin to better suit your needs.

Donate to support Product Delivery Date

If you would like to support the development of Product Delivery Date, please consider making a donation. Your support will help us to continue to improve the plugin and make it better for everyone.