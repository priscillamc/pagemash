=== pageMash > pageManagement ===
Contributors: JoelStarnes
Tags: order pages, ajax, re-order, drag-and-drop, admin, manage, page, pages, sidebar, header, hide,
Requires at least: 2.1
Tested up to: 2.5
Stable tag: 1.1.0

Organise page order and manage page structure with this simple drag-and-drop Ajax interface.

== Description ==

Customise the order your pages are listed in and manage the parent structure with this simple Ajax drag-and-drop administrative interface with an option to toggle the page to be hidden from output. Great tool to quickly re-arrange your page menus.

Checkout the example page: http://joelstarnes.co.uk/pagemash/example
Feedback is greatly appreciated: http://joelstarnes.co.uk/contact

== Installation ==

1. Download Plugin
1. Unzip & Upload to `/wp-content/plugins/`
1. Activate in 'Plugins' admin menu
1. Then have fun..

pageMash works with the `wp_list_pages` function. The easiest way to use it is to put the pages widget in your sidebar [WP admin page > Presentation > Widgets]. Click the configure button on the widget and ensure that 'sort by' is set to 'page order'. Hey presto, you're done.

You can also use the function anywhere in your theme code. e.g. in your sidebar.php file (but the code in here will not run if you're using any widgets) or your header.php file (somewhere under the body tag, you may want to use the depth=1 parameter to only show top levle pages). The code should look something like the following:

`<?php wp_list_pages('title_li=<h2>Pages</h2>&depth=0'); ?>`

You can also hard-code pages to exclude and these will be merged with the pages you set to exclude in your pageMash admin.

The code here is very simple and flexible, for more information look up `wp_list_pages()` in the Wordpress Codex: http://codex.wordpress.org/Template_Tags/wp_list_pages


== Frequently Asked Questions ==

If you have any questions or comments, please drop me an email: http://joelstarnes.co.uk/contact

= Do I need any special code in my template =
No. You no longer need to add the pageMash parameter as you did with the previous versions [<1.0.2]. You can leave the code in as it will do no harm, but it's a good idea to keep your template clean of unnecessary code.

= Which browsers are supported =
Any good up-to-date browser should work fine. I test in Firefox, IE7, Safari and Opera. (NB in IE7 you need to use the page name as a drag handle.)

== Screenshots ==

1. Admin Interface

2. Setting up the page widget. [WP-Admin > Presentation > Widgets]


==Change Log==

0.1.0 > Initial Release

0.1.1 > Removed version check since some hosts will not allow external includes.

0.1.2 > Fixed CSS&JS headers to only display on pageMash admin

0.1.3 > Fixed exclude pages feature

1.0.0 beta > Major rebuild > Recusive page handles unlimited nested children, collapsable list items, interface makeover...

1.0.1 beta > fixed IE drag selects text

1.0.2 > Major code rewrite for exclude pages

1.0.3 > Fixed datatype bug causing array problems

1.0.4 > Removed shorthand PHP and updated CSS and JS headers to admin_print_scripts hook.

1.1.0 > Added quick rename, externalised scripts, changed display of edit|hide|rename links, deregisters prototype

== Localization ==

Currently only available in english.