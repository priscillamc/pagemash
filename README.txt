=== pageMash > pageManagement ===
Contributors: JoelStarnes
Tags: order pages, ajax, re-order, drag-and-drop, admin,
Requires at least: 2.0
Tested up to: 2.4-bleeding
Stable tag: 0.1.3

Organise your page order with this simple drag-and-drop Ajax interface.

== Description ==

Customise the order your pages are listed in with a simple Ajax drag-and-drop administrative interface with an option to toggle the page visibility. Great tool to quickly re-arrange your menus.

If you want to see an example of the admin page check out: http://joelstarnes.co.uk/pagemash/example



= Development Version 1.0.0 beta =

I have just finished a major rebuild of the plugin to give full recursive suppot for unlimited nested children in a collapsable list which I've decided to release as the first major version 1.0.0. but it'd be really helpful if some people can download the 1.0.0 beta and give me some feedback to iron out any bugs.

http://joelstarnes.co.uk/pagemash/example_v1.0.0

http://wordpress.org/extend/plugins/pagemash/download/pagemash.1.0.0.zip


== Installation ==

1. Download Plugin
1. Unzip & Upload to `/wp-content/plugins/`
1. Activate in 'Plugins' admin menu
1. {Edit your Template}

In most cases the plugin should work straight out the box so give it an install and if then there is further info available in the plugin's admin page under 'Show further info'.


== Frequently Asked Questions ==

If you have any questions or comments,
please drop me an email: joel@joelstarnes.co.uk

= Can I use this with the 'Pages' sidebar widget? =
Yes. Go into the wordpress admin; Presentation > Widgets and drag the pages widget to the sidebar, then go to it's settings by clicking the icon on the right and ensure that 'sort by' value is set to 'page order'.
Note however that the exclude pages feature will not work, so disable this in the top of the pagemash.php file by setting '$excludePagesFeature = false;'.


== Screenshots ==

1. Admin Interface.

2. If you are having problems using the pages widget; goto [Admin > Presentation > Widgets] and check that the 'sort by' value is set to 'page order'.


==Change Log==

0.1.0 > Initial Release

0.1.1 > Removed version check [line72] since some hosts will not allow external includes.

0.1.2 > Fixed CSS&JS headers to only display on pageMash admin

0.1.3 > Fixed exclude pages feature \n
1.0.0 beta > Major rebuild > Recusive page handles unlimited nested children, collapsable list items, interface makeover...


== Localization ==

Currently only available in english.
