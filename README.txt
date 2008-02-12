=== pageMash > pageManagement ===
Contributors: JoelStarnes
Tags: order pages, ajax, re-order, drag-and-drop, admin,
Requires at least: 2.0
Tested up to: 2.4-bleeding
Stable tag: 0.1.1

Organise your page order with this simple drag-and-drop Ajax interface.

== Description ==

Customise the order your pages are listed in with a simple Ajax drag-and-drop administrative interface with an option to toggle the page visibility. Great tool to quickly re-arrange your menus.

== Installation ==

1. Download Plugin
1. Unzip & Upload to `/wp-content/plugins/`
1. Activate in 'Plugins' admin menu
1. {Edit your Template}

In most cases the plugin should work straight out the box, since most templates will include something similar to: `wp_list_pages('depth=1&title_li=);`

However to achieve full functionality including the 'exclude pages' feature you should replace the `wp_list_pages()` function with the following php code:
 `if(function_exists('pageMash_exclude_pages'))`
 `{$exclude_pages=pageMash_exclude_pages();} else{$exclude_pages='';}`
 `wp_list_pages('depth=1&title_li=&exclude='.$exclude_pages);`

You can place the code wherever you would like your page listings to appear;
usually either the header.php or sidebar.php file found in: `wp-content\themes\theme_name`

== Frequently Asked Questions ==

If you have any questions or comments,
please drop me an email: joel@joelstarnes.co.uk

== Screenshots ==

1. Admin Interface

== Localization ==

Currently only available in english.

== Limitations==

The plugin will currently only handle top level pages.