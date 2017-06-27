=== Fast Velocity Minify ===
Contributors: Alignak
Tags: merge, combine, concatenate, PHP Minify, YUI Compressor, CSS, javascript, JS, minification, minify, optimization, optimize, stylesheet, aggregate, cache, CSS, html, minimize, pagespeed, performance, speed, GTmetrix, pingdom
Requires at least: 4.5
Stable tag: 2.1.1
Tested up to: 4.8
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Improve your speed score on GTmetrix, Pingdom Tools and Google PageSpeed Insights by merging and minifying CSS, JavaScript and HTML. 
 

== Description ==

This plugin reduces HTTP requests by merging CSS & Javascript files into groups of files, while attempting to use the least amount of files as possible. It minifies CSS and JS files with PHP Minify (no extra requirements).

Minification is done in real time and done on the frontend only during the first uncached request. Once the first request is processed, any other pages that require the same set of CSS and JavaScript will be served that same static cache file.

This plugin includes options for developers and advanced users, however the default settings should work just fine for most sites.

= Aditional Optimization =

I can offer you aditional `custom made` optimization on top of this plugin. If you would like to hire me, please visit my profile links for further information.


= Features =

*	Merge JS and CSS files into groups to reduce the number of HTTP requests
*	Google Fonts merging and optimization
*	Handles scripts loaded both in the header & footer separately
*	Keeps the order of the scripts even if you exclude some files from minification
*	Supports localized scripts (https://codex.wordpress.org/Function_Reference/wp_localize_script)
*	Minifies CSS and JS with PHP Minify only, no third party software or libraries needed.
*	Option to use YUI Compressor rather than PHP Minify (you you have "exec" and "java" available on your system).
*	Option to defer JavaScript and CSS files.
*	Stores the cache files in the uploads directory.
*	View the status and logs on the WordPress admin page.
*	Option to Minify HTML for further improvements.
*	Ability to turn off minification
*	Ability to turn off CSS or JS optimization seperatly (by disabling either css or js processing)
*	Ability to manually ignore scripts or css
*	Support for conditional scripts and styles
*	Support for multisite installations
*	Support for gzip_static on Nginx
*	Support for the CDN Enabler plugin
*	Auto purging of cache files on W3 Total Cache, WP Supercache, WP Rocket, Wp Fastest Cache, Cachify, Comet Cache, Zen Cache, LiteSpeed Cache, SG Optimizer and Godaddy Managed WordPress Hosting (read the FAQs)
*	Support for preconnect and preload headers
*	and some more...


= Notes =
*	The JavaScript minification is by [PHP Minify](https://github.com/matthiasmullie/minify)
*	The alternative JavaScript minification is by [YUI Compressor](http://yui.github.io/yuicompressor/)
*	Compatible with Nginx, HHVM and PHP 7. 
*	Minimum requirements are PHP 5.5 and WP 4.4, from version 1.4.0 onwards


== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory or upload the zip within WordPress
2. Activate the plugin through the `Plugins` menu in WordPress
3. Configure the options under: `Settings > Fast Velocity Minify` and that's it.


== Screenshots ==

1. You can view the logs and purge the cache files.
2. The settings page.


== Frequently Asked Questions ==

= How can I exclude certain assets by wildcard? =

By default, each line on the ignore list will try to match a substring against all css or js urls, for example: `//yoursite.com/wp-content/plugins/some-plugin/js/` will ignore all files inside that directory. You can also shorten the url like `/some-plugin/js/` and then it will match any css or js url that has `/some-plugin/js/` on the path. Obviously, doing `/js/` would match any files inside any "/js/" directory and in any location, so to avoid unexpected situations please always use the longest, most specific path you can use. 

...

= Why is the ignore list not working? =

The ignore list is working but you need to remove query vars from static urls (ex: ?ver=) and use partial (see wildcard help above) and use relative urls only. 

...


= Why are there several or a lot's of js and css files listed on the status page? =

Those files are created whenever a new set of javascript or css files are found on your front end and it's due to your plugins and themes needing different js and css files per page, post, category, tag, homepage or even custom post types. If you always load the exact same css and javascript in every page on your site, you won't see as many files. Likewise, if you have some dynamic url for css or js that always changes in each pageview, you should add it to the ignore list.

...

= Can I update other plugins and themes? =

Yes, but it's recommended that you purge the cached files (from the plugin status page) in order for the merging and minification cache files to be regenerated. The plugin will try to automatically purge some popular cache plugins. We still recommend, however, that you purge all caches on your cache plugin (whatever you use) "after" purging Fast Velocity Minify cache.

...

= Is it compatible with other caching plugins? =

The plugin will try to automatically purge several popular cache plugins, however we still recommend you to purge all caches (on whatever you use) if you also  manually purge the cache on the plugin settings for some reason.
The automatic purge is active for the following plugins and hosting: W3 Total Cache, WP Supercache, WP Rocket, Wp Fastest Cache, Cachify, Comet Cache, Zen Cache, LiteSpeed Cache, SG Optimizer and Godaddy Managed WordPress Hosting

...

= Is it resource intensive, or will it use too much CPU on my shared hosting plan? =

No it's not. The generation of the minified files is done only once per group of CSS or JS files (and only if needed). All pages that request the same group of CSS or JS files will also make use of that cache file. The cache file will be served from the uploads directory as a static file and there is no PHP involved.

...

= Is it compatible with multisites? =

Yes, it generates a new cache file for every different set of JS and CSS requirements it finds. 

...

= Is it compatible with Adsense and other ad networks? =

The plugin is compatible with any add network but also depends on how you're loading the ads into the site. We only merge and minify css and javascript files enqueued in the header and footer which would exclude any ads. If you're using a plugin that uses JS to insert the ads on the page, there could be issues. Please report on the support forum if you found such case.

...

= After installing, why did my site became slow? =

Please note that the cache regeration happen's once per page or if the requested CSS + JS files change. If you need the same set of CSS and JS files in every page, the cache file will only be generated once and reused for all other pages. If you have a CSS or JS that uses a different name on every pageview, try to add it to the ignore list by using wildcards.

...

= How do I use the precompressed files with gzip_static on Nginx? =

When we merge and minify the css and js files, we also create a `.gz` file to be used with `gzip_static` on Nginx. You need to enable this feature on your Nginx configuration file if you want to make use of it. If you're upgrading from 1.2.3 or earlier, you need to clear the plugin cache.

...

= Where is the YUI Compressor option gone to? =

This functionality depends on wheter you have exec and java available on your system. It will be visible on the basic Settings page under the JavaScript Options section and it only applies to JS files.

...

= After installing, why is my design or layout broken or some images and sliders are not showing? =

Kindly review the "Why are some of the CSS and JS files not being merged or why is my layout broken ?" question below for better insights. 
Additionally, the Advanced Otions should only be used by advanced users or developers that understand what the settings mean and do, especially the "defer JS" options. Having said that, this is how you can solve most issues on the settings tab when not using any options on the advanced tab:

* `Disabling CSS processing but keeping JS processing enabled:` This will leave CSS files alone and it's useful to determine if the problem is CSS or JS related. If isabling CSS processing fixed the layout problems, now you know it's related to CSS... 
Likewise, you can keep CSS processing and disable JS processing to find out if the problem is on JS processing. 
If you determine it's a CSS issue, check the log file on the status page for possible css file urls that should not be there (such as Internet Explorer only files) and add them to the ignore list. 
Also kindly report those files on the support forum so they can be blacklisted for future releases. 
Sometimes there are JS files than conflict with each other when merged so they may need to be excluded too.

* `JS Defer:` If you have a theme that heavily relies on javascript and jQuery (usually with sliders, paralax animations, etc) most probably, you cannot `defer JavaScript` for all JS files, but you could try to add the jQuery library as well as the specific JS files that need to be render blocking to the ignore list. 

* `Developers only:` Note that if you defer jQuery, the library will not run until after the html DOM page loads. That means that whatever jQuery code is inlined on the HTML will not work and trigger an "undefined" error on the browser console log. 
On Google Chrome you can look at the console by pressing CTRL + SHIFT + J on your keyboard and refreshing the page. If there are errors you need to track down which JS file is causing trouble and add it to the ignore list. 
If you have no idea which files to add, I recommend checking the log file on the status page, adding them all to the ignore list and then one by one trying to delete each url until you find the one causing trouble. 
Also beware of any cache plugin in use (and cloudflare) when testing, because that cache needs to be off or purged when configuring things around.

...

= Why are some of the CSS and JS files not being merged or why is my layout broken ? =

There are thousands of plugins and themes out there and not every developer follows the standard way of enqueueing their files on WordPress.

For example, some choose to "print" the html tag directly into the header or footer, rather than to use the official method outlined here: https://developer.wordpress.org/themes/basics/including-css-javascript/

Some developers enqueue all files properly but still "print" conditional tags, such as IE only comments around some CSS or JS files , while they should be following the example as explained on the codex: https://developer.wordpress.org/reference/functions/wp_script_add_data/

Because "printing CSS and JS tags on the header and footer" is evil (seriously), we cannot capture those files and merge them together.
There are also files meant to be loaded for IE users only, mobile users, desktop users, printers, so the enqueing of all the files needs to follow the official method of enqueing data and files. 

This can also cause layout issues because some files that should be for IE only can be merged together with other users, thus overwriting the usual layout (please check if any of those got merged together and add it to the ignore list). Also, to avoid some of these, we have implemented an IE only blacklist of "known" file names that are "always" added to the ignore list behind the scenes.

Please feel free to open a support topic if you found some more JS or CSS files on your theme or plugins that "must always" also blacklisted and why.

The default ignore list can be found here: 
https://fastvelocity.com/api/fvm/ignore.txt

The IE only blacklist (mostly IE only files or that cause trouble most of the times when merged with others), can be found here: https://fastvelocity.com/api/fvm/ie_blacklist.txt

These files are downloaded directly by the plugin, once every 24 hours from our cdn provider.

...

= Why is it that even though I have disabled or removed the plugin, the design is still broken? =

While this is rare, it can happen if you have some sort of cache enabled on your site or server. A cache means that the site or server makes a static copy of your page and serves it for a while (until it's deleted or expires) instead of loading wordpress directly to the users. Some hosting providers such as Godaddy (and their derivates) enforce their own cache plugin to be installed and creates a new menu which allows you to purge the cache. 

If you don't see any option anywhere to clear your cache, you can contact your hosting provider or developer to clear the cache for you or ask them how you can do it in the future.

...

= Why is my frontend editor not working ? =

Some plugins and themes need to edit the layout and styles on the frontend. When they need to do that, they enqueue several extra js and css files that are caught by this plugin and get merged together, thus sometimes, it breaks things. If you encounter such issue of your page editor not working on the frontend, kindly enable the "Fix Page Editors" on the Troubleshooting page.

...

= What is the "Fix Page Editors" on the Troubleshooting page ? =

This hides all optimization from editors and administrators, as long as they are logged in. This also means that you will see the site exactly as it was before installing the plugin and it's meant to fix compatibility with frontend page editors, or plugins that edit things in preview mode using the frontend.

...

= How should I use the "Preload Images" and what is it for? =

Certain themes and plugins, either load large images or sliders on the homepage. Most of them will also load "above the fold" causing the "Prioritize visible content" or the "Eliminate render-blocking JavaScript and CSS in above-the-fold content" message on pagespeed insights (see the previous faq question above).

How you can use the "Preload Images" is by adding the url of the first relevant images that load above the fold, such as the first background image (and the first image only) of the slider. Any big or large enough image that is above the fold should be added here, however note that the images you add here "must" actually exist on the page, else it will trigger a warning on the browser console such as "The resource [...] was preloaded using link preload but not used within a few seconds from the window's load event. Please make sure it wasn't preloaded for nothing." which is not good practice. 

Don't put too many resources here as those are downloaded in high priority and it will slow down the page load on mobile or slower connections (because the browser won't process the rest until it finishes downloading all of those big "preload" images).

...

= What are the recommended cloudflare settings for this plugin? =

On the "Speed" tab, deselect the Auto Minify for JavaScript, CSS and HTML as well as the Rocket Loader option. There is no benefit of using them with our plugin (we already minify things). Those options sometimes can also break the design due to double minification or the fact that the Rocket Loader is still experimental (you can read about that on the "Help" link under each selected option on cloudflare).

...

= How to undo all changes done by the plugin? =

The plugin itself doesn't do any "changes" to your site and all original files are untouched. It intercepts the enqueued CSS and JS files, processes and hides them, while enqueuing the newly optimized cached version of those files. As with any plugin, simply disable or uninstall the plugin, purge all caches you may have in use (plugins, server, cloudflare, etc) and the site will go back to what it was before installing it. The plugin doesn't delete anything from the database or modify any of your files.

...

= I have a complaint or I need support right now. Why haven't you replied to my topic on the support forum yet? =

Before getting angry because you have no answer within a few hours (even with paid plugins, sometimes it takes weeks...), please be informed about how wordpress.org and the plugins directory work. 

The plugins directory is an open source, free service where developers and programmers contribute (on their free time) with plugins that can be downloaded and installed by anyone "at their own risk" and are all released under the GPL license.

While all plugins have to be approved and reviewed by the wordpress team before being published ( for dangerous code, spam, etc ) this doesn't change the license or add any warranty. All plugins are provided as they are, free of charge and should be used at your own risk (so you should make backups before installing any plugin or performing updates) and it's your sole responsability if you break your site after installing a plugin from the plugins directory.

Support is provided by plugin authors on their free time and without warranty of a reply, so you can experience different levels of support level from plugin to plugin. As the author of this plugin I strive to provide support on a daily basis and I can take a look and help you with some issues related with my plugin, but please note that this is done out of my goodwill and in no way I have any legal or moral obligation for doing this. I'm also available for hiring if you need custom made speed optimizations (check my profile links).

For a full version of the license, please read: https://wordpress.org/about/gpl/

...

= Where can I get support or report bugs? =

You can get support on the official wordpress plugin page at: https://wordpress.org/support/plugin/fast-velocity-minify 
You can also see my profile and check my links if you wish to hire me for custom speed optimization on wordpress or extra features. 

...

= How can I donate to the plugin author? =

If you would like to donate any amount to the plugin author (thank you in advance), you can do it via Paypal at https://goo.gl/vpLrSV

...


== Upgrade Notice ==

= 2.0.0 =
Note: The Inline all CSS option is now divided into header and footer.


== Changelog ==

= 2.1.1 [2017.06.24] =
* fixed an error notice

= 2.1.0 [2017.06.21] =
* some performance improvements

= 2.0.9 [2017.06.01] =
* several bug and compatibility fixes

= 2.0.8 [2017.05.28] =
* fixed a notice alert on php for undefined function

= 2.0.7 [2017.05.28] =
* added support for auto purging of LiteSpeed Cache 
* added support for auto purging on Godaddy Managed WordPress Hosting
* added the ie only blacklist, wich doesn't split merged files anymore, like the ignore list does
* added auto updates for the default ignore list and blacklist from our api once every 24 hours
* added cdn rewrite support for generated css and js files only
* removed url protocol rewrites and set default to dynamic "//" protocols
* updated the faqs

= 2.0.6 [2017.05.22] =
* added a "Troubleshooting" option to fix frontend editors for admin and editor level users
* updated the faqs

= 2.0.5 [2017.05.15] =
* fixed preserving the SVG namespace definition "http://www.w3.org/2000/svg" used on Bootstrap 4
* added some exclusions for Thrive and Visual Composer frontend preview and editors

= 2.0.4 [2017.05.15] =
* improved compatibility with Windows operating systems

= 2.0.3 [2017.05.15] =
* fixed an "undefined" notice

= 2.0.2 [2017.05.14] =
* improved compatibility on JS merging and minification

= 2.0.1 [2017.05.11] =
* fixed missing file that caused some errors on new installs 

= 2.0.0 [2017.05.11] =
* moved the css and js merging base code back to 1.4.3 because it was better for compatibility
* removed the font awesome optimization tweaks because people have multiple versions and requirements (but duplicate css and js files are always removed)
* added all usable improvements and features up to 1.5.2, except for the "Defer CSS" and "Critical Path" features (will consider for the future)
* added info to the FAQ's about our internal blacklist for known CSS or JS files that are always ignored by the plugin 
* changed the way CSS and JS files are fetched and merged to make use of the new improvements that were supposed to be on 1.4.4+
* changed the advanced settings tab back to the settings page for quicker selection of options by the advanced users
* changed the cache purging option to also delete our plugin transients via the API, rather than just let them expire
* changed the "Inline all CSS" option into header and footer separately

= 1.5.3 [2017.05.10] =
* quick rollback to 1.5.1 due to some css issues found, another update will come in a few hours

= 1.5.2 [2017.05.10] =
* added debug information to the CSS logs
* added back the option to remove the "Print" related stylesheets
* added some performance tweaks to the CSS processing
* added support to purge cache automatically for Wp Fastest Cache, Cachify, Comet Cache, Zen Cache and SG Optimizer (read the FAQs)
* changed the CSS processing engine to merge all INLINE and CSS files in order
* fixed the loading of conditional CSS files

= 1.5.1 [2017.05.06] =
* fixed a bug with the CSS defer checkbox

= 1.5.0 [2017.05.05] =
* fixed some javascript errors introduced on 1.4.9

= 1.4.9 [2017.05.05] =
* changed some of the design and colors
* fixed a bug with the JS defer option

= 1.4.8 [2017.05.04] =
* added back the YUI Compressor as an option (if java and exec is available)
* added a tab for checking the server information
* added support to purge cache automatically for W3 Total Cache, WP Rocket and WP Supercache (read the FAQs)
* changed how css files are fetched, it will first try to open locally and then fallback to remote urls
* fixed CSS bugs introduced on 1.4.4

= 1.4.7 [2017.05.03] =
* changed css files of media="all" to load first (so it can be overriden by other mediatypes)

= 1.4.6 [2017.05.03] =
* fixed some bugs introduced on 1.4.4

= 1.4.5 [2017.05.02] =
* added a new advanced settings tab
* added preconnect and prefetch support
* updated the readme file and blacklist

= 1.4.4 [2017.04.19] =
* changed css merging logic to be more efficient and have as less files as possible
* changed css merging engine to fetch urls instead of opening them by file path
* changed google fonts optimization so they are now inlined in the CSS files (IE9+ only)
* changed to remove the "Print" related stylesheets by default
* changed the font awesome optimization method for better compatibility
* added option to defer css with LoadCSS and rel="preload" (make sure you know how to use this before trying it)
* added an internal blacklist for JS and CSS files that must always be ignored (for example, IE only files)
* fixed some minor performance issues
* fixed a compatibility issue with the thrive leads plugin

= 1.4.3 [2017.03.10] =
* changed minimum requirements to PHP 5.4 for older clients

= 1.4.2 [2017.02.26] =
* fixed another warning when debug mode is active on wordpress

= 1.4.1 [2017.02.26] =
* fixed a warning when debug mode is active on wordpress

= 1.4.0 [2017.02.24] =
* added option to force HTTP or HTTPS protocol to the generated files, so it can work with the CDN Enabler plugin
* fixed compatibility for sites where home_url() and site_url() differ (some files could not be find by the plugin)
* fixed removing valid code from .svg images when "removal of query strings for Web Fonts" was on the default settings 
* changed the "defer of JS for Pagespeed Insights" option so it ignores scripts that already have the async or defer attribute
* changed the "defer of JS for Pagespeed Insights" so it doesn't defer external resources https://www.chromestatus.com/feature/5718547946799104
* changed so that Font Awesome now loads asynchronously in the footer, unless "Disable Font Awesome optimization" is selected
* changed minimum requirements to PHP 5.5 and WP 4.4 for compatibility reasons.
* removed YUI and Google Closure for simplicity thus making JS minification default to PHP MINIFY 
* updated the faq questions

= 1.3.9 [2017.01.15] =
* stop removing some important CSS comments during HTML minification

= 1.3.8 [2017.01.11] =
* added support to strip html comments during HTML minification
* added wildcard support to the ignore list (url's must end in *), ex: http://yourdomain.com/wp-content/plugins/some-plugin/dynamic-css-*
* changed mrclay HTML library to an older version (same as WP Rocket 2.91) due to unecessary line breaks on HTML minification
* changed some code on mrclay HTML library in order to properly minify some inline css styles
* changed absolute urls during minification to protocol-relative URLs for better https compatibility
* changed the plugin cache directory to the uploads directory for better compatibility with Google App Engine and Amazon Elastic Beanstalk (beta)

= 1.3.7 [2016.12.03] =
* stop removing html comments from html minification for better compatibility with some plugins

= 1.3.6 [2016.11.29] =
* fixed a bug where sometimes the paths and urls have double slashes in the middle leading to those files not being found
* fixed a bug that was stripping the conditional IE tags for javascript and some css files

= 1.3.5 [2016.11.25] =
* added option to automatically exclude dynamically generated css and js files

= 1.3.4 [2016.11.25] =
* added option to defer all JS files for Pagespeed Insights tests only
* fontawesome improvements

= 1.3.3 [2016.11.16] =
* fixed a bug where the ignore list was not being completely ignored when defer javascript is enabled
* added some options to exclude jquery from other sections

= 1.3.2 [2016.11.16] =
* added option for inlining all CSS
* added option for inlining google fonts as woff (modern browsers only)
* added option for optimizing fontawesome
* bug fixes

= 1.3.1 [2016.10.31] =
* fixed some other reported notices that are visible when debug mode is enabled

= 1.3.0 [2016.10.23] =
* fixed a few notices that are visible when debug mode is enabled
* fixed keeping of CSS handles with empty src for better dependency management (JS processing already does this)

= 1.2.9 [2016.10.22] =
* added merging of "screen" and "all" CSS mediatypes
* added auto reordering of CSS files by mediatype
* added support to keep order of CSS for better compatibility
* fixed a bug with CSS where "print" mediatypes were being merged together with "all", breaking some designs
* added an option to remove Print Style Sheets (CSS files of mediatype "print" for printers)
* changed the defer JS files logic in order to skip files that are on the ignore list
* added option to force JS files defer even if they are on the ignore list
* improved some descriptions for some options in the settings page
* added option to remove emojis support

= 1.2.8 [2016.10.21] =
* added font awesome optimization and cdn delivery (if used by your theme)
* load only one font awesome css file, even when your theme or plugins enqueue multiple files
* replaced the HTML minification engine with mrclay minify (same as the autoptimize plugin, w3 total cache and a few other popular plugins)

= 1.2.7 [2016.10.18] =
* fixed CSS minification not working on some cases after the latest update

= 1.2.6 [2016.10.16] =
* improved html minification speed and compatibility
* fixed a PHP 7 compatibility issue on PHP Minify JS minify script
* fixed the JS defer option for scripts that already have defer or async, such as the AMP plugin

= 1.2.5 [2016.10.03] =
* reverted back the PHP Minify library due to a bug

= 1.2.4 [2016.10.03] =
* added support for `gzip_static` on nginx for cached files
* updated PHP Minify with today's release date
* added donation link to the FAQ's section

= 1.2.3 [2016.10.02] =
* google fonts related bugfixes
* improved help section

= 1.2.2 [2016.09.22] =
* bugfixes

= 1.2.1 [2016.09.22] =
* more improvements on multisite installations

= 1.2.0 [2016.09.22] =
* improved compatibility with multisite instalations

= 1.1.9 [2016.09.21] =
* fixed a fatal error on versions older than PHP 5.5 (note that our recommended PHP version is still PHP 5.6+)

= 1.1.8 [2016.09.20] =
* fixed support for custom directory names on wordpress (wp-content, plugins, etc)
* bug fixes

= 1.1.7 [2016.08.26] =
* fixed a compatibility issue with SunOS and Solaris systems

= 1.1.6 [2016.08.24] =
* changed the CSS minification to PHP Minify for better compatibility with the calc() expression and others.
* fixed a bug for when wp-content has been renamed to something else

= 1.1.5 [2016.08.21] =
* better support for third party cache plugins
* bug fixes

= 1.1.4 [2016.08.20] =
* added logic for when wp-content has been renamed to something else
* small improvements

= 1.1.3 [2016.08.05] =
* minor bug fix on the defer javascript option

= 1.1.2 [2016.08.02] =
* added option to force the use of PHP Minify for JS minification instead of YUI or Google Closure

= 1.1.1 [2016.08.01] =
* added PHP Minify [2016.08.01] as fallback for JS files again
* PHP Minify issues (white screen on PHP 7) might have been fixed
* other small bug fixes

= 1.1.0 [2016.07.11] =
* improved compatibility for PHP 7
* new location for cache and temporary files
* removed PHP Minify as fallback due to incompatibility (white screen) with some PHP 7 configurations
* Fallback method for JS files updated to merge only

= 1.0.9 [2016.07.10] =
* added new logic to group handling for better compatibility
* added an intermediate minification cache for faster performance
* added PHP Minify as fallback option for JS files
* added a local Google Closure alternative to YUI Compressor
* added help page to the plugin
* removed the Google Closure API because their rate limit can lead to incomplete minified files

= 1.0.8 [2016.07.02] =
* disabled error reporting messages
* added some extra code checks

= 1.0.7 [2016.07.01] =
* bug fixes related to warnings being displayed at the admin area

= 1.0.6 [2016.06.30] =
* fixed some header and footer scripts not being enqueued on the right place
* added a better dependency check before merging and minifying of JS files
* added more logic to keep the order of js files, when one or several of them are excluded from minification
* performance improvements and some code simplification

= 1.0.5 [2016.06.24] =
* bug fixes on the admin page log viewer

= 1.0.4 [2016.06.23] =
* added YUI Compressor for local JS minification with java (if available)
* JS minification fallback to the Google Closure API if java not available or YUI Compressor fails
* added individual cache for already minified files, so they are minified again only if that cache is older than the original files to minify

= 1.0.3 [2016.06.23] =
* removed JSrink and added back the Google Closure API for compatibility with PHP 7

= 1.0.2 [2016.06.23] =
* Fixed Google Fonts optimization
* Replaced Google Closure API with JSrink PHP library
* Reorganized inline CSS code dependencies
* New (safer) HTML minification

= 1.0.1 [2016.06.22] =
* Javascript minification fixes

= 1.0 [2016.06.19] =
* Initial Release
