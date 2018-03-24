=== LH Archived Post Status ===
Contributors:      shawfactor
Donate link: 	   https://lhero.org/plugins/lh-archived-post-status/
Tags:              admin, posts, pages, status, workflow
Requires at least: 3.0
Tested up to:      4.9
Stable tag:        trunk
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

Allows posts and pages to be archived so you can remove content from the main loop and feed without having to trash it.

== Description ==

This plugin allows you to archive your WordPress content similar to the way you archive your e-mail. Unlike other archiving solutions though this actually does it all and does it properly

* Makes a new post status available in the drop down called Archived
* Hides or removes your content without having to trash the content
* Content can either be hidden entirely from public view  or simply from the main loop and feed and pages, with other solutions you can only hide it from public view.
* Allows you to add a label to the title of those posts/pages etc that are archived
* Allows you to add a message to the top of the post/page etc that the content is no longer up too date
* Allows you to set an archiving date after which content is automatically changed to having an archived status
* Compatible with posts, pages and custom post types

This plugin is ideal for sites where certain kinds of content is not meant to be evergreen

Check out [our documentation][docs] for more information on why and how to use this new status. 

All tickets for the project are being tracked on [GitHub][].


[docs]: http://lhero.org/plugins/lh-archived-post-status/
[GitHub]: https://github.com/shawfactor/lh-archived-post-status


== Frequently Asked Questions ==

= Isn't this the same as using the Draft or Private statuses? =

Actually, no, they are not the same thing.

The Draft status is a "pre-published" status that is reserved for content that is still being worked on. You can still make changes to content marked as Draft, and you can preview your changes.

The Private status is a special kind of published status. It means the content is only available to certain logged in users.

The Archived post status, on the other hand, is meant to be a "post-published" status. Once a post has been set to Archived the content is either hidden entirely from non logged in viewers or removed from the front page and feed (but still available on singular pages). This behaviour is controlled in the settings screen.

= Doesn't this do the same thing as the other archiving plugin in the repository? =

Actually it does more! Unlike the other plugin content archived with this plugin can still be available to non logged in visitors (depends on plugin settings) and just  removed from the front page and xml feeds (with a custom message can also be added to flag to visitors that the content is no longer up too date). Alternately it can be hidden entirely (to non logged in viewers).

= Can't I just trash old content I don't want anymore? =

Yes, there is nothing wrong with trashing old content. However it will be hidden from non logged in viewers.

However, WordPress automatically purges trashed posts every 7 days (by default), so it will be gone.

This is what makes the Archived post status handy. You can unpublish content without having to delete it forever.

= How can I view a listing of my archived content on its own archive pagelisting all archived posts, pages etc?

This not not part of my plugin per se but it is easily done.

The easiest way would be to install a plugin that allows you to query by post_status eg: https://wordpress.org/plugins/display-posts-shortcode/

and input the shortcode with the post_status of archive:, eg [display-posts post_status=”archive”]

If you want to customise the display that shortcode has plenty of arguments. There are also other shortcodes tha can do this (just search the repository).

== Installation ==

1. Upload the entire `lh-archived-post-status` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to Settings->Archiving and set the visibility and archiving message



== Changelog ==

**0.01 February 12, 2015**  
* Initial release

**0.02 February 17, 2015**
* Added public/private option

**1.00 March 26, 2015**
* Added icons

**1.1 April 24, 2015**
* Added nonces

**1.2 June 15, 2015**
* Added settings

**1.30 July 17, 2015**
* Code improvements

**1.31 August 17, 2015**
* Minor code improvements

**1.40 March 10, 2016**
* Simpler codebase

**2.00 October 20, 2016**
* Handle post expiry

**2.10 November 05, 2016**
* Handle bulk actions

**2.11 November 11, 2016**
* Wp editor for archive message

**2.12 November 21, 2016**
* Added check for empty option

**2.13 December 27, 2016**
* Added fix for php warnings

**2.14 January 04, 2017**
* Better documentation

**2.15 January 27, 2017**
* Sticky post bug fix

**2.16 July 25, 2017**
* Don't use on attachments

**2.17 October 04, 2017**
* Various improvements

**2.18 October 08, 2017**
* Bug Fix

**2.19 October 10, 2017**
* Changed uninstall method to static

**2.20 November 27, 2017**
* Added message filter and disquss fix