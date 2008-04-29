<?php
/*
Plugin Name: pageMash
Plugin URI: http://joelstarnes.co.uk/pagemash/
Description: pageMash > pageManagement  [WP_Admin > Manage > pageMash]
Author: Joel Starnes
Version: 1.1.1
Author URI: http://joelstarnes.co.uk/

CHANGELOG:
Release:		Date:			Description:
0.1.0			10 Feb 2008		Initial release
0.1.1			12 Feb 2008		Minor fixes > Removed external include
0.1.2			15 Feb 2008		Minor fixes > Fixed CSS&JS headers to only display on pagemash 
1.0.0 beta		19 Feb 2008		Major update > 	Recusive page handles unlimited nested children, collapsable list items, interface makeover...
1.0.1 beta		14 Mar 2008		Fixed IE > drag selects text
1.0.2			16 Mar 2008		Major code rewrite for exclude pages, funct hooks onto wp_list_pages
1.0.3			18 Mar 2008		Fixed datatype bug causing array problems
1.0.4			11 Apr 2008		removed shorthand PHP and updated CSS and JS headers to admin_print_scripts hook.
1.1.0			24 Apr 2008		Added quick rename, externalised scripts, changed display of edit|hide|rename links, deregisters prototype
1.11			29 Apr 2008		Fix a bug with console.log for safari, removed php code from js&css scripts to fix error
	
*/
#########CONFIG OPTIONS############################################
$minlevel = 7;  /*[deafult=7]*/
/* Minimum user level to access page order */

$excludePagesFeature = true;  /*[deafult=true]*/
/* Allows you to set pages not to be listed */

###################################################################
/*
INSPIRATIONS/CREDITS:
Valerio Proietti - Mootools JS Framework [http://mootools.net/]
Stefan Lange-Hegermann - Mootools AJAX timeout class extension [http://www.blackmac.de/archives/44-Mootools-AJAX-timeout.html]
vladimir - Mootools Sortables class extension [http://vladimir.akilles.cl/scripts/sortables/]
ShiftThis - WP Page Order Plugin [http://www.shiftthis.net/wordpress-order-pages-plugin/]
Garrett Murphey - Page Link Manager [http://gmurphey.com/2006/10/05/wordpress-plugin-page-link-manager/]
*/

/*  Copyright 2008  Joel Starnes  (email : joel@joelstarnes.co.uk)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//Global Vars
$pageMash_rel_dir = 'wp-content/plugins/pagemash/';
$pageMash_abs_dir = get_bloginfo('wpurl').'/'.$pageMash_rel_dir;

function pageMash_getPages($post_parent){
	//this is a recurrsive function which calls itself to produce a nested list of elements
	//$post_parent should be 0 for root pages, or contain a pageID to return it's sub-pages
	global $wpdb, $wp_version, $excludePagesFeature, $excludePagesList;
	if($wp_version >= 2.1){ //get pages from database
		$pageposts = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type = 'page' AND post_parent = '$post_parent' ORDER BY menu_order");
	}else{
		$pageposts = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_status = 'static' AND post_parent = '$post_parent' ORDER BY menu_order");
	}
	
	if ($pageposts == true){ //if $pageposts == true then it does have sub-page(s), so list them.
		echo (0 === $post_parent) ? '<ul id="pageMash_pages">' : '<ul>'; //add this ID only to root 'ul' element
		foreach ($pageposts as $page): //list pages, [the 'li' ID must be pm_'page ID'] ?>
			<li id="pm_<?php echo $page->ID;?>" <?php if(get_option('exclude_pages')){ if(in_array($page->ID, $excludePagesList)) echo 'class="remove"'; }//if page is in exclude list, add class remove ?>>
				<span class="title"><?php echo $page->post_title;?></span>
				<span class="pageMash_box">
					<span class="pageMash_more">&raquo;</span>
					<span class="pageMash_pageFunctions">
						id:<?php echo $page->ID;?>
						[<a href="<?php echo get_bloginfo('wpurl').'/wp-admin/post.php?action=edit&post='.$page->ID; ?>" title="Edit This Page">edit</a>]
						<?php if($excludePagesFeature): ?>
							[<a href="#" title="Show|Hide" class="excludeLink" onclick="toggleRemove(this); return false">hide</a>]
						<?php endif; ?>
						[<a href="#" title="Rename Page" class="rename">Rename</a>]
					</span>
				</span>
				<?php pageMash_getPages($page->ID)  //call this function to list any sub-pages (passing it the pageID) ?>
			</li>
		<?php endforeach;
		echo '</ul>';
		return true;
	} else {
		return false;
	}
}

function pageMash_main(){
	global $excludePagesFeature, $excludePagesList;
	if(!is_array(get_option('exclude_pages'))) $excludePagesList=array(); else $excludePagesList = get_option('exclude_pages'); //if it's empty set as an empty array
	?>
	<div id="debug_list"></div>
	<div id="pageMash" class="wrap">
		<div id="pageMash_checkVersion" style="float:right; font-size:.7em; margin-top:5px;">
		    version [1.1.1]
		</div>
		<h2 style="margin-bottom:0; clear:none;">pageMash - pageManagement</h2>
		<p style="margin-top:4px;">
			Just drag the pages <strong>up</strong> or <strong>down</strong> to change the page order and <strong>left</strong> or <strong>right</strong> to change the page's parent, then hit 'update'.<br />
			The icon to the left of each page shows if it has child pages, <strong>double click</strong> anywhere on that item to toggle <strong>expand|collapse</strong> of it's children.
		</p>
		
		<?php pageMash_getPages(0); //pass 0, as initial parent ?>
		
		<p class="submit">
			<div id="update_status" style="float:left; margin-left:40px; opacity:0;"></div>
				<input type="submit" id="pageMash_submit" tabindex="2" style="font-weight: bold; float:right;" value="Update" name="submit"/>
		</p>
		<br style="margin-bottom: .8em;" />
	</div>

	<div class="wrap" style="width:160px; margin-bottom:0; padding:2px; text-align:center;"><a href="#" id="pageMashInfo_toggle" style="text-align:center;">Show|Hide Further Info</a></div>
	<div class="wrap" id="pageMashInfo" style="margin-top:-1px;">
		<h2>How to Use</h2>
		<p>pageMash works with the wp_list_pages function. The easiest way to use it is to put the pages widget in your sidebar [WP admin page > Presentation > Widgets]. Click the configure button on the widget and ensure that 'sort by' is set to 'page order'. Hey presto, you're done.</p>
		<p>You can also use the function anywhere in your theme code. e.g. in your sidebar.php file (but the code in here will not run if you're using any widgets) or your header.php file (somewhere under the body tag, you may want to use the depth=1 parameter to only show top level pages). The code should look something like the following:</p>
		<p style="margin-bottom:0; font-weight:bold;">Code:</p>
		<code id="pageMash_code">
			<span class="white">&lt;?php</span> <span class="blue">wp_list_pages(</span><span class="orange">'title_li=&lt;h2&gt;Pages&lt;/h2&gt;&amp;depth=0'</span><span class="blue">);</span> <span class="white">?&gt;</span>
		</code>
		<p>You can also hard-code pages to exclude and these will be merged with the pages you set to exclude in your pageMash admin.</p>
		<p>The code here is very simple and flexible, for more information look up <a href="http://codex.wordpress.org/Template_Tags/wp_list_pages" title="wp_list_pages Documentation">wp_list_pages() in the Wordpress Codex</a> as it is very well documented and if you have any further questions or feedback I like getting messages, so <a href="http://joelstarnes.co.uk/contact/" title="email Joel Starnes">drop me an email</a>.</p>
		<br />
	</div>
	<?php
}

function pageMash_head(){
	//stylesheet & javascript to go in page header
	global $pageMash_rel_dir;
	
	wp_deregister_script('prototype');//remove prototype since it is incompatible  with mootools
	wp_enqueue_script('pagemash_mootools', '/'.$pageMash_rel_dir.'nest-mootools.v1.11.js', false, false); //code is not compatible with other releases of moo
	wp_enqueue_script('pagemash_nested', '/'.$pageMash_rel_dir.'nested.js', array('pagemash_mootools'), false);
	wp_enqueue_script('pagemash_inlineEdit', '/'.$pageMash_rel_dir.'inlineEdit.v1.2.js', array('pagemash_mootools'), false);
	wp_enqueue_script('pagemash', '/'.$pageMash_rel_dir.'pagemash.js', array('pagemash_mootools'), false);
	add_action('admin_head', 'pageMash_add_css', 1);

}

function pageMash_add_css(){
	global $pageMash_abs_dir;
	?>
<link rel="stylesheet" type="text/css" href="<?php echo $pageMash_abs_dir ?>pagemash.css" />
<!--                     __  __           _     
       WordPress Plugin |  \/  |         | |    
  _ __   __ _  __ _  ___| \  / | __ _ ___| |__  
 | '_ \ / _` |/ _` |/ _ \ |\/| |/ _` / __| '_ \ 
 | |_) | (_| | (_| |  __/ |  | | (_| \__ \ | | |
 | .__/ \__,_|\__, |\___|_|  |_|\__,_|___/_| |_|
 | |           __/ |  Author: Joel Starnes
 |_|          |___/   URL: pagemash.joelstarnes.co.uk
 
 >>pageMash Admin Page
-->
	<?php
}

function pageMash_add_excludes($excludes){
	//merge array of hardcoded exclude pages with pageMash ones
	if(is_array(get_option('exclude_pages'))){
		$excludes = array_merge( get_option('exclude_pages'), $excludes );
	}
	sort($excludes);
	return $excludes;
}

function pageMash_add_pages(){
	//add link in the management tab
	global $minlevel;
	$page = add_management_page('pageMash page order', 'pageMash', $minlevel, __FILE__, 'pageMash_main');
	add_action("admin_print_scripts-$page", 'pageMash_head'); //add css styles and JS code to head
}

add_action('admin_menu', 'pageMash_add_pages'); //add admin menu under management tab
add_filter('wp_list_pages_excludes', 'pageMash_add_excludes'); //add exclude pages to wp_list_pages funct


?>