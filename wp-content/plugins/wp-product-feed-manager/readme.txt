﻿=== Woocommerce Google Feed Manager ===
Contributors: Wpmarketingrobot, Michel Jongbloed, AukeJomm
Tags: Google Merchant Export, Product feed, woocommerce, Google product feed export, google, shopping, Google Adwords, Google Merchant, wooCommerce export, woocommerce variations, e-commerce, google merchant product feed, product variations, variations export, wp-e-commerce export, wp marketing robot
Requires at least: 4.6
Tested up to: 4.9
Stable tag: 2.0.8
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Extremely Powerful Woocommerce Google Feed Manager,  Optimize your product listings and campaign results and sell more on Google Shopping! 

== Description ==
Woocommerce Google Feed Manager is an extremely powerful and easy to use google shopping feed manager for Woocommerce web shops.
With Woocommerce Google Feed Manager you can easily add products from your woocommerce store to a product feed setup that meets the requirements from Google Shopping. 

We have connected al the required and recommended fields of the feed with the Woocommerce database. So after installation you're ready to submit your product feed to your Google merchant center. But you can go even further and tweak the content of every field in order to maximize your revenue from your products in Google shopping.

You have very advanced and professional options at your disposal to make your products stand out, use titles different from your shop, change your product google categories depending on title names. 

> Sell your products on other selling channels

> If you want to get your products shown in Price comparison, product aggregators, affiliate networks and selling channels we have our [Premium Woocommerce Product Feed Manager](https://goo.gl/mGFLMm) supporting all shopping channels.
> Increase your Sales, Show your products on worldwide selling channels and affiliate networks!
> We have the most popular and important feed templates already installed like Bing, Facebook, Pricegrabber, Amazon, Nextag, Beslist, Vergelijk.nl, Shopping.com, Connexity and more.

Bring even more Power to your webshop with [Premium Woocommerce Product Feed Manager](https://goo.gl/mGFLMm) and sell your products on many other Channels!

Everything you need to be successful!
You have full controle over your Google feed, how your products are listed and what products you show and which products not.

Your first step: Learn how to setup up your first basic feed in our online manual > [Product feed manager manual](https://goo.gl/pbgepQ)

== Installation ==
Step 1. Download the Woocommerce google feed manager plugin from your wpmarketing account.
Step 2. Upload 'wp-product-feed-manager' to the ’/wp-content/plugins/' directory
Step 3. Activate the plugin through the 'Plugins' menu in WordPress

That's basically it. You're ready to create your first feed and start making money.

Take a look at the video. They lead you through the proces in creating your first product feed.

= Install woocommerce google feed manager =
[youtube https://www.youtube.com/watch?v=bgEFUWAdNOc]

= How to setup a basic feed =
[youtube https://www.youtube.com/watch?v=VDcyo9ifdmk]

== Frequently Asked Questions ==
= Why should i go with your plugin? =
We are very proud of our woocommerce Google feed manager and we think it is the most powerful plugin out there build by Product Feed Marketeers especially for woocommerce powered shops. Our plugin will help you to get the most out of your Google shopping account.

The power of the plugin is that you can add the data from your shop to any feed attribute required, recommended or supported by Google. Next to that you are able to alter the data from you wordpress woocommerce database so you can optimize the data you send to Google. We support custom product fields and product variations making our plugin even stand out more from the competition.

While you grow with your webshop you can start working with our Premium plugin for even more powerful features and even more channel to sell your products on.

= How does the Google feed manager plugin work? =
Basically the plugin will generate a xml product feed valid for the Google product feed guidelines. It will take the data from your woocommerce powered online shop and create a valid Google product feed. The plugin will automatically update the feed to reflect the changes of your products so the xml feed will contain the correct product data. 

The only thing you have to do is add the xml feed url to your Merchant center and start selling in Google.

= Any Issues? Start here =
Start with the next steps when you have any issue with your installation and plugin.

First make sure you have all your plugins, theme and wordpress updated to the latest version. Follow woocommerce requirements as a minimum for the use of our plugin.

PHP 5.6 or greater
MySQL 5.6 or greater
WooCommerce 2.5 requires WordPress 4.1+
WooCommerce 2.6 requires WordPress 4.4+
WP Memory limit of 256 MB or higher for larger shops
If you have all of that sorted please follow the next steps before contacting us.

Deactivate and then activate the plugin. Test the issue.
If that does not resolve the issue, deactivate and reinstall the plugin over the current installation. Than activate it again and test the issue.
If it still does not work, remove the plugin through the plugins page (be aware, this will also delete all existing feeds) and reinstall it. Then test the issue again.

If the problem still exists, contact our support.

= Feed Warning, invalid xml feed =
There can be many reasons why a feed is invalid. We have build the feed in such a maner that it will only happen very sporadic.

In case it does please do submit your feed in your Google Merchant center and check what is going on in your feed in the Feed debugger from Google. It is very powerful and spot on.

[Read about it here](https://goo.gl/CChqGH)

== Screenshots ==
1. Add a new feed
2. Map a category
3. Save and generate your feed

== Changelog ==
= 2.0.8 - 31/03/2018 =
* Added the wppfm_feed_ids_in_queue filter
* Fixed an issue  that prevented the Third Party Attributes setting to be ignored in a backup
* Extended the Disable background process selection so that it also autmatically clears the feed process when selected
* Fixed an error with product variations when the feed settings was to exclude product variations
* Added an extra option on the settings page to switch the background processing off in case the feeds get stuck in the processing
* Fixed an error that prevented attributes of variable products to show up in the feed
* Fixed an error that prevented the image library to work on variable products
* Fixed an error where some items of variable products of type WC_Product_Variable where not correctly implemented

= 2.0.5 - 16/02/2018 =
* Fixed an issue where when the user had not selected the "include variations" option, the non product specific variation data like min_variation_price or max_variation_price would not be included on the main version of the variation product.* Changed the feed processing proces so it can handle feeds with large number of products
* Fixed an error that would cause the feed process to fail with product variations that included sale dates
* Changed the way the variation data was accessed that caused some messages in logging files
* Fixed an issue where third party attributes that start with an underscore would show up as an empty row in the Google Source pulldown list
* Changed the way third party attributes are shown in the source list. They now keep their origional name
* Fixed a few security issues
* Added the WooCommerce version check
* Improved the auto feed update timing
* Added the wppfm_category_mapping_exclude, wppfm_category_mapping_exclude_tree and wppfm_category_mapping_max_categories filters that allow the user to influence the category mapping list
* Fixed an issue where the Stock count would show a wrong number when the actual Stock account was 0

= 1.9.8 - 21/10/2017 =
* Made some improvements in the script loading process
* Fixed a bug that caused the the "update license" message to show up when you just registered the plugin
* Fixed a bug in the automatic feed update time calculation
* Made the plugin accessable for the Shop Manager role
* Made a few fixes to prevent certain error messages
* Made some improvements to the memory usage during feed generation

= 1.9.5 - 23/09/2017 =
* Fixed a bug that prevented some custom values to show up in the attributes list
* Improved error messaging during license activation
* Fixed a bug where an attribute value of "0" would not be placed in an xml feed

= 1.9.4 - 03/09/2017 =
* Fixed an issue where filtering failed when the user would not enter an "or" input
* Improved the backup process
* Improved memory usages during feed generation
* Fixed an issue where the min and max variation regular prices would not be configured according the the WooCommerce money settings
* Fixed an issue with the duplicate function
* Improved the database setup and update process
* Added support for WooCommerce Composite Products
* Improved the error handling messages for licensing activities
* Added the wppfm_feed_item_value filter that allows users to edit the value of any item in a feed using this filter option
* Added support for Google Dynamic Remarketing
* Fixed an issue where the plugin would conflict with the Mandrill plugin
* The output lists are now sorted alphabetically
* Added support for user made taxonomies
* Added a function that removes WordPress Gallery shortcode from the product description
* Added a warning if a user uses prohibited characters in the feed name
* Fixed an error that could cause calculations in a change value to produce a period as a decimal separator even though a comma is set as required decimal separator
* Changed the Min and Max Variation prices that are not supported by WooCommerce anymore
* Fixed an issue where the url of variable products from which some attributes where not set would not be correct
* Prepaired the code to support the WooCommerce Product Feed Manager WPML Support plugin that adds WPML multilingual support the the plugin

= 1.8.1 - 16/06/2017 =
* Fixed an issue where an update of the database to the new specifications would only occur when visiting the feed update form so if an automatic feed update would be done before visiting the feed update form an error would occur as the database was not updated

= 1.8.0 - 14/06/2017 =
* Changed the way the javascript version numbers are build to prevent cashing issues when switching from a free version to a premium version
* Added the option to change the feed title and description of the feed file
* When an auto feed update is set to once every day the feed will now auto update every day on the set update time independant from manual updates
* Fixed an issue that prevented the "select all" checkbox in the Category Mapping table to correctly select or deselect all Shop Categories
* Added the option to only save (changes to) a feeds data, without (re)generating the feed
* Added the option to use third party attributes by setting attribute keywords in the Settings page

= 1.7.3 - 13/05/2017 =
* Fixed an issue where edit value calculations would not give the correct results with value above 1000 and a comma as the thousands separator
* Fixed an issue where External/Affiliate and Grouped products were excluded from the feed
* Fixed an issue that caused some attributes not to show up in the feed
* Fixed an issue where product variations would cause the feed generation to stop
* Fixed loading the wrong google feed function that caused the feed page not to load correctly
* Fixed an issue where the user had to register the license every day

= 1.7.1 - 30/04/2017 =
* Fixed an issue where pretty permalinks would nog show up in automatic feed updates
* Automatic feed updates are now performed at WordPress time reference instead of server time reference
* Added the option to automatically update a feed more than once a day
* Added an option to backup the feeds you made
* Fixed an issue where the feed generation would fail and kept showing the 'still working' symbol
* Improved the way database update version numbers are stored
* Fixed an issue where the category would not stay at the correct level after generating a feed
* Fixed an issue where the WP-Admin > Plugins > Licenses page was expecting a license form from our plugin which caused an error warning
* Fixed an issue where users who use an older version of PHP got several error messages
* Fixed an issue with combining a static field with a source that has an array output like the Image Library
* Moved the Settings page from the WP-Admin Settings to the Feed Manager menu
* Rearanged the Feed Manager Menu structure

= 1.6.5 - 04/02/2017 =
* Fixed a bug where using 'Image Library' as a source caused the feed generation to fail
* Added support for a Custom CSV Feed channel
* Updated the feed generating process so it now can handle large numbers of products
* Added the option to duplicate an existing feed
* Fixed an issue where the "edit values" option could sometimes not be opened in a Chrome browser
* Sorted the WooCommerce source pull-down fields alphabetically
* Added a "Select All" selector to the feed Category Mapping table
* Fixed an issue with a combined source including static fields and a filter
* Fixed the sale price dates to output the dates in the correct format

= 1.5.1 - 02/12/2016 =
* Fixed an issue that caused an error when calculations where done on a combined input field
* Fixed a code error that caused the plugin not to activate on PHP versions 5.3 or lower
* Added a Last Feed Update source that represents the feed update date and time
* Made some changed to the auto feed update that should improve the update process
* Changed the Edit Feed Page so the user cannot change the channel after the feed has been initialized
* Changed the Edit Feed Page so the user can change the Target Country and Default Category during and after the feed has been stored
* Fixed an issue that could cause the license registration form not to show up
* Updated the Google feed specifications to the October 2016 rules
* Changed the code to force feed file names not to have spaces

= 1.4.2 - 24/09/2016 =
* Fixed an issue with product variation urls

= 1.4.1 - 16/09/2016 =
* Fixed an issue that caused attributes from product variations to always show the value of the last variation in line
* Added the Shipping Class source
* Fixed an issue with numeric condition values with non-english/us values
* Added access to another third party attributs set
* Fixed an issue with html special characters in the feed
* Added the item_group_id source to the source list
* Fixed a bug with combined fields in the not required level
* Changed the way the input fields are shown when making a new feed, thus preventing errors from not using the correct order to fill in the fields
* You can now change the name of an existing feed
* Fixed an issue that caused the advised source of the Shipping source to go to undefined in specific situations
* Added a "Convert to child-element" option to the edit values. This allows you to add a child element with a specific key to an xml feed

= 1.3.0 - 08/08/2016 =
* Due to recurring issues with support folder permissions for some users, moved the support folders from the plugins folder to the uploads folder. Existing feed files will retain their old url, only new feeds will be stored in the new support folder
* Changed the file writing procedures to minimize the times the system asks to enter ftp credentials

= 1.2.1 - 27/07/2016 =
* Added the option to include product variations in the feed (Premium versions only)
* Fixed the change values option in such a way that you now can perform recalculations even on combined source fields
* Fixed a bug that prevented the correct recalculation of comma separated financial values
* Several small changes in the styling code
* Fixed the Add Channels functionality as some firewalls prevented downloading a channel (Premium versions only)

= 1.1.0 - 01/06/2016 =
Not published

= 1.0.1 - 17/06/2016 =
* Fixed a bug that caused a critical error with users that had PHP 5.5 or older
* Fixed a bug in the selection of recommended and optional output fields

= 1.0.0 - 16/06/2016 =
* Fixed a few small bugs
* Fixed a bug that prevented the correct output when working with conditions on feed items that have an advised source
* Fixed a bug that slowed down the wp-admin pages in the Premium versions
* Updated the auto-feed update

= 0.31.0 =
* Added the option to filter specific products from a feed (Premium version only)
* Price values are now formatted according to the Woocommerce settings in the Currency Options, except the currency as this can be added manually and not all channels allow a currency in their feed
* When building a new Feed for Google the price is now preset to the current price followed by a space and the Woocommerce Currency
* Fixed a bug preventing the auto-feed update to work

= 0.30.0 =
* Now fixed the Product Category String in such a way that it shows the correct category string even when only a subcategory has been selected for a product
* Price results are now always printed in a money format with two digits after the comma, even if the product has no digit in its price
* Fixed a compatibility issue with jQuery that prevented the Add New Feed form from proceding after a channel was choosen
* Fixed an issue with the Combined Source Fields option where adding a static value didn't always work
* Added the Woocommerce Currency option that can be used in conjunction with the price outputs (use Combined Source Fields)

= 0.29.0 =
* Fixed a bug in the auto-feed-update function
* Fixed a bug that caused an error when installing a paid version over the free version, causing a white screen and error message
* Fixed a bug in the calculation options of the change value selections
* Changed the way that the support folders are made by temporary removing the umask modifier to force the server to make these folder writable
* Changed the way the change value selections are shown
* Again changed the procedures to get the Product Category String and Selected Product Categories because the last procedure still did not work in all situations

= 0.28.0 =
* Added the option to use Product Tags as source
* Added the Custom Fields from the Posts and tha Products as selectable sources
* Changed the procedure to get the Product Category String and Selected Product Categories because the old procedure did not work in all situations
* Fixed a bug where the user could not make a new feed if there is no Custom Attribute defined
* The plugin now works correctly on websites using the https protocol

0.27.0 Changed the Product Categories source and split it in a Product Category String source that contains a string representing the category in which the product is stored in the actual shop, and a Selected Product Categories source that will give you a comma seperated string with all the shop Categories that where selected for this product. Daarnaast de Image Library source toegevoegd waarmee additionele images aan de feed kunnen worden gekoppeld. Also fixed a bug.

0.26.1 Changed the product name reference for the login process.

0.26.0 Added version numbers to the loaded javascripts to make sure the plugin is always using the latest version of the script. Also repaired a few bugs

0.25.0 Added 'Min Variation Price', 'Min Variation Regular Price', 'Min Variation Sale Price', 'Max Variation Price', 'Max Variation Regular Price', 'Max Variation Sale Price' and the 'Post Category' as additional sources. Repared a few bugs.

0.24.0 Improved the javascript conflict management

0.23.0 Changed the Woocommerce Source pulldown selections to match the Woocommerce Products inputs and fixed a few bugs

0.22.0 The javascript files are now disabled when the user leaves the plugin pages, also fixed a few small bugs

0.21.0 Removed all html code from the feed output

0.20.0 Changed the procedure that defined the product url and image url

0.19.0 Added a preset for the Availability tag for a Google feed and AvantLink feed

0.18.1 Several Bug fixes

0.18.0 Added error loggin to a error.log file. Added implemented the EULA restrictions and fixed a few bugs

0.17.0 Optimized the form layout to better fit in smaller screen resolutions and fixed a few bugs

0.16.1 Removed the underscores from the source selectors and fixed a bug

0.16.0 Added the option to make a Custom XML format feed

0.15.0 Moved the channel code once again Placed the channel code in a more distinct folder

0.14.0 Moved the channel code Placed the channel code outside the plugin folder to allow for updating the plugin and the channels separately

0.13.1 Bugs fixed Changed the include path of pluggable.php and used an other methode to unzip files in order to prevent error warnings

0.13.0 Added functionality Management of channels added. You can now install and update channels from the plugin

0.12.0 Bugs Fixed and channels added
Fixed a few bugs that were bugging me and added the Nextag and Connexity channels

0.11.1 Channels Added
Support for Vergelijk.nl and Koopjespakker.nl added

0.11.0 Added functionality
Added Upgrade and Licensing functionality 

0.10.0 Added Amazon support
Solved a few bugs and added support for the Amazon channel

0.9.2 Added eBay support
Added the Shopping Channel

0.9.1 solved bugs Bing Channel
Added bug fixes for the Bing Channel