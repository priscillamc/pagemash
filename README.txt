=== pageMash > pageManagement ===
Contributors: JoelStarnes
Tags: order pages, ajax, re-order, drag-and-drop,
Requires at least: 2.2
Tested up to: 2.4-bleeding
Stable tag: 1.7.5

Organise your page order with this simple drag-and-drop Ajax interface.

== Description ==

Customise the order your pages are listed in with a simple Ajax drag-and-drop administrative interface with an option to toggle the page visibility. Great tool to quickly re-arrange your menus.

== Installation ==

1. Download Plugin
1. Unzip & Upload to /wp-content/plugins/
1. Edit your Template
1. Activate in 'Plugins' admin menu


Simply include the following in your template where you would like your pages to be listed:
'<?php if(function_exists('pageMash_exclude_pages')){$exclude_pages=pageMash_exclude_pages();} else{$exclude_pages='';}?>'
'<?php wp_list_pages('depth=1&title_li=&exclude='.$exclude_pages);?>'

If you would only like to order pages you can set $excludePagesFeature to false at the top of pagemash.php
And then just use the standard:
'<?php wp_list_pages('depth=1&title_li=);?>'

== Frequently Asked Questions ==

If you have questions,
please drop me an email.

== Screenshots ==

1. screenshot_1.png

== Localization ==

Currently only available in english.