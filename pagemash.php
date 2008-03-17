<?php
/*
Plugin Name: pageMash
Plugin URI: http://joelstarnes.co.uk/pagemash/
Description: pageMash > pageManagement  [WP_Admin > Manage > pageMash]
Author: Joel Starnes
Version: 1.0.2
Author URI: http://joelstarnes.co.uk/

CHANGELOG:
Release:		Date:			Description:
0.1.0			10 Feb 2008		Initial release
0.1.1			12 Feb 2008		Minor fixes > Removed external include
0.1.2			15 Feb 2008		Minor fixes > Fixed CSS&JS headers to only display on pagemash 
1.0.0 beta		19 Feb 2008		Major update > 	Recusive page handles unlimited nested children,
												collapsable list items, interface makeover...
1.0.1 beta		14 Mar 2008		fixed IE > drag selects text
1.0.2			16 Mar 2008		Major code rewrite for exclude pages

FIXME:
	@fixme with instantUpdateFeature hide will not send the update
	
*/
#########CONFIG OPTIONS############################################
$minlevel = 7;  /*[deafult=7]*/
/* Minimum user level to access page order */

$excludePagesFeature = true;  /*[deafult=true]*/
/* Allows you to set pages not to be listed */

$instantUpdateFeature = false;  /*[deafult=false]*/
/* Updates the database instantly after a move using ajax 
   otherwise it will wait for update button press.
   nb. this feature has not been optimised and enabling will cause 
   much increased server load */

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
		echo '<ul ';
		if($post_parent==0) echo 'id="pageMash_pages" '; //add this ID only to root 'ul' element
		echo '>';
   
		foreach ($pageposts as $page): //list pages, [the 'li' ID must be the page ID] ?>
			<li id="pm_<?=$page->ID;?>" <?php if(in_array($page->ID, $excludePagesList)) echo 'class="remove"';//if page is in exclude list, add class remove ?>>
				<span class="title"><?=$page->post_title;?></span>
				<span class="pageMash_pageFunctions">
					id:<?=$page->ID;?>
					[<a href="<?=get_settings('siteurl').'/wp-admin/post.php?action=edit&post='.$page->ID; ?>" title="Edit This Page">edit</a>]
					<?php if($excludePagesFeature): ?>
						[<a href="#" title="Show|Hide" class="excludeLink" onclick="toggleRemove(this); return false">hide</a>]
					<?php endif; ?>
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
	global $instantUpdateFeature, $excludePagesFeature, $excludePagesList;
	$excludePagesList = get_option('exclude_pages');
	if(!is_array($excludePagesList)) $excludePagesList[]=''; //if it's empty set as an empty array
	
	?>
	<div id="debug_list"></div>
	<div id="pageMash" class="wrap">
	<div id="pageMash_checkVersion" style="float:right; font-size:.7em; margin-top:5px;">
	    version [1.0.2]
	</div>
	<h2 style="margin-bottom:0; clear:none;">pageMash - pageManagement</h2>
	<p style="margin-top:4px;">
		Just drag the pages <strong>up</strong> or <strong>down</strong> to change the page order and <strong>left</strong> or <strong>right</strong> to change the page's parent, then hit 'update'.<br />
		The icon to the left of each page shows if it has child pages, <strong>double click</strong> anywhere on that item to toggle <strong>expand|collapse</strong> of it's children.
	</p>
		
	<div>
	    <?php pageMash_getPages(0); //pass 0, as initial parent ?>
	</div>
	
	<p class="submit">
		<div id="update_status" style="float:left; margin-left:40px; opacity:0;"></div>
		<?php if(!$instantUpdateFeature): ?>
			<input type="submit" id="pageMash_submit" tabindex="2" style="font-weight: bold; float:right;" value="Update" name="submit"/>
		<?php endif; ?>
	</p>
	<br style="margin-bottom: .8em;" />
	</div>

	<div class="wrap" style="width:160px; margin-bottom:0; padding:2px; text-align:center;"><a href="#" id="pageMashInfo_toggle" style="text-align:center;">Show|Hide Further Info</a></div>
	<div class="wrap" id="pageMashInfo" style="margin-top:-1px;">
		<h2>How to Use</h2>
		<p>pageMash works with the wp_list_pages function. The easiest way to use it is to put the pages widget in your sidebar [WP admin page > Presentation > Widgets]. Click the configure button on the widget and ensure that 'sort by' is set to 'page order'. Hey presto, you're done.</p>
		<p>You can also use the function anywhere in your theme code. e.g. in your sidebar.php file (but the code in here will not run if you're using any widgets) or your header.php file (somewhere under the body tag, you may want to use the depth=1 parameter to only show top levle pages). The code should look something like the following:</p>
		<p style="margin-bottom:0; font-weight:bold;">Code:</p>
		<code id="pageMash_code">
			<span class="white">&lt;?php</span> <span class="blue">wp_list_pages(</span><span class="orange">'title_li=&lt;h2&gt;Pages&lt;/h2&gt;&amp;depth=0'</span><span class="blue">);</span> <span class="white">?&gt;</span>
		</code>
		<p>You can also hard-code pages to exclude and these will be merged with the pages you set to exclude in your pageMash admin.</p>
		<p>The code here is very simple and flexible, for more information look up <a href="http://codex.wordpress.org/Template_Tags/wp_list_pages" title="wp_list_pages Documentation">wp_list_pages() in the Wordpress Codex</a> as it is very well documented and if you have any further questions or feedback I like getting messages, so <a href="http://joelstarnes.co.uk/contact/" title="email Joel Starnes">drop me an email</a>.</p>
	</div>
	<?php
}

function pageMash_head(){
if(strrpos('>'.$_GET["page"], 'pagemash')): // only include header stuff on pagemash admin page
	//stylesheet & javascript to go in page header
	global $instantUpdateFeature, $excludePagesFeature;
	?>
<style type="text/css">
	ul#pageMash_pages {
		margin:0 0 0 0;
		list-style:none;
	}
	ul#pageMash_pages li.collapsed ul {	display:none; }
	ul#pageMash_pages li.children {
		background-image: url('<?=get_settings("siteurl")?>/wp-content/plugins/pagemash/collapse.png'); 
	}
	ul#pageMash_pages li.collapsed.children {
		background-image: url('<?=get_settings("siteurl")?>/wp-content/plugins/pagemash/expand.png'); 
	}
	ul#pageMash_pages li { 
		display:block; 
		margin:2px 0 0 0; 
		border-bottom:1px solid #aaa; border-right:1px solid #aaa; border-top:1px solid #ccc; border-left:1px solid #ccc;
		padding:4px 6px 4px 24px; 
		background:#F1F1F1 url('<?=get_settings("siteurl")?>/wp-content/plugins/pagemash/page.png') no-repeat 4px 4px; 
		list-style-type:none;
	}
	ul#pageMash_pages li span.title { font-weight: bold; }
	ul#pageMash_pages li.collapsed.children span.title { text-decoration: underline; }
	ul#pageMash_pages li.collapsed.children li span.title { text-decoration: none; }
	#update_status { 
		font-weight:bold; 
		display:block; 
		border:2px solid #AC604C;
		background-color: #DDA37A;
		padding: 2px 6px;
	}
	ul#pageMash_pages li.remove {
		color:grey;
		border-style:dashed; 
		border-color:#aaa; 
		opacity:.5; 
		filter:alpha(opacity=50); zoom:1; /* ie hack[has layout] for opacity */
	}
	ul#pageMash_pages li.remove a { color:grey; }
	ul#pageMash_pages li span.pageMash_pageFunctions {
		border:1px solid #ccc;
		background-color: #eee;
		padding: 1px 3px;
	}
	ul#pageMash_pages li span.pageMash_pageFunctions a { border:0; }
	
	/* Show [page id, 'edit page' link and 'hide' link] function box on hover */
	ul#pageMash_pages li span.pageMash_pageFunctions { display:none; }
	ul#pageMash_pages li:hover span.pageMash_pageFunctions { display:inline; }
	ul#pageMash_pages li:hover li span.pageMash_pageFunctions { display:none; }
	ul#pageMash_pages li:hover li:hover span.pageMash_pageFunctions { display:inline; }
	ul#pageMash_pages li:hover li:hover li span.pageMash_pageFunctions { display:none; }
	ul#pageMash_pages li:hover li:hover li:hover span.pageMash_pageFunctions { display:inline; }
	ul#pageMash_pages li:hover li:hover li:hover li span.pageMash_pageFunctions { display:none; }
	ul#pageMash_pages li:hover li:hover li:hover li:hover span.pageMash_pageFunctions { display:inline; }
	ul#pageMash_pages li:hover li:hover li:hover li:hover li span.pageMash_pageFunctions { display:none; }
	ul#pageMash_pages li:hover li:hover li:hover li:hover li:hover span.pageMash_pageFunctions { display:inline; }
	ul#pageMash_pages li:hover li:hover li:hover li:hover li:hover li span.pageMash_pageFunctions { display:none; }
	ul#pageMash_pages li:hover li:hover li:hover li:hover li:hover li:hover span.pageMash_pageFunctions { display:inline; }
	
	#pageMash_code {display:block; border:solid 3px #858EF4; background-color:#211E1E; padding:7px; margin:0px;}
	#pageMash_code .white{color:#DADADA;}
	#pageMash_code .purple{color:#9B2E4D; font-weight:bold;}
	#pageMash_code .green{color:#00FF00;}
	#pageMash_code .blue{color:#858EF4;}
	#pageMash_code .yellow{color:#C1C144;}
	#pageMash_code .orange{color:#EC9E00;}
</style>
<!-- Current code not compatible with newer releases of moo -->
<script type="text/javascript" src="<?=get_settings('siteurl')?>/wp-content/plugins/pagemash/nest-mootools.v1.11.js"></script>
<script type="text/javascript" src="<?=get_settings('siteurl')?>/wp-content/plugins/pagemash/nested.js"></script>
<script type="text/javascript">

/* add timeout to Ajax class */
Ajax = Ajax.extend({
	request: function(){
	if (this.options.timeout) {
		this.timeoutTimer=window.setTimeout(this.callTimeout.bindAsEventListener(this), this.options.timeout);
		this.addEvent('onComplete', this.removeTimer);
	}
	this.parent();
	},
	callTimeout: function () {
		this.transport.abort();
		this.onFailure();
		if (this.options.onTimeout) {
			this.options.onTimeout();
		}
	},
	removeTimer: function() {
		window.clearTimeout(this.timeoutTimer);
	}
});
/* function to retrieve list data and send to server in JSON format */
var SaveList = function() {
	var theDump = sortIt.serialize();
	new Ajax('<?=get_settings("siteurl")?>/wp-content/plugins/pagemash/saveList.php', {
		method: 'post',
		postBody: 'm='+Json.toString(theDump), 
		// update: "debug_list", 
		onComplete: function() {
			$('update_status').setText('Database Updated');
			new Fx.Style($('update_status'), 'opacity', {duration: 500}).start(0,1).chain(function() {
				new Fx.Style($('update_status'), 'opacity', {duration: 1500}).start(1,0);
			});
		},
		timeout: 8500, 
		onTimeout: function() {
			$('update_status').setText('Error: Update Timeout');
			new Fx.Style($('update_status'), 'opacity', {duration: 200}).start(0,1);
		}
	}).request();
};
/* toggle the remove class of grandparent */
<?php if($excludePagesFeature): ?>
	var toggleRemove = function(el) {
		el.parentNode.parentNode.toggleClass('remove');
	}
<?php endif; ?>


/* ******** dom ready ******** */
window.addEvent('domready', function(){
	sortIt = new Nested('pageMash_pages', {
		collapse: true,
		onComplete: function(el) {
			el.setStyle('background-color', '#F1F1F1');
			sortIt.altColor();
			<?php if($instantUpdateFeature): ?>SaveList();<?php endif; ?>
			
			$ES('li','pageMash_pages').each(function(el) {
				if( el.getElement('ul') ){
					 el.addClass('children');
				} else {
					el.removeClass('children');
				}
			});
		}
	});	
	Nested.implement({
		/* alternate the colours of top level nodes */
		altColor: function(){
			var odd = 1;
			this.list.getChildren().each(function(element, i){
				if(odd==1){
					odd=0;
					element.setStyle('background-color', '#CFE8A8');
				}else{
					odd=1;
					element.setStyle('background-color', '#D8E8E6');
				}
			});
		}
	});
	sortIt.altColor();
	$('update_status').setStyle('opacity', 0);
		
	<?php if(!$instantUpdateFeature): ?>
		$('pageMash_submit').addEvent('click', function(e){
			e = new Event(e);
			SaveList();
			e.stop();
		});
	<?php endif; ?> 

	var pageMashInfo = new Fx.Slide('pageMashInfo');
	$('pageMashInfo_toggle').addEvent('click', function(e){
		e = new Event(e);
		pageMashInfo.toggle();
		e.stop();
		switch($('pageMashInfo_toggle').getText()) {
			case "Show Further Info":
				$('pageMashInfo_toggle').setText('Hide Further Info');
			  break    
			case "Hide Further Info":
				$('pageMashInfo_toggle').setText('Show Further Info');
			  break
		}
	});
	pageMashInfo.hide();
	$('pageMashInfo_toggle').setText('Show Further Info');
	
	
	/* loop through each page */
	$ES('li','pageMash_pages').each(function(el) {
		/* If the li has a 'ul' child; it has children pages */
		if( el.getElement('ul') ) el.addClass('children');
		
		/* on page dblClick add this event */
		el.addEvent('dblclick', function(e){
			e = new Event(e);
			if(el.hasClass('children')) el.toggleClass('collapsed');
			e.stop();
		});
	});

	//disable drag text-selection for IE
	if (typeof document.body.onselectstart!="undefined")
		document.body.onselectstart=function(){return false}

}); /* close dom ready */
</script>
	<?php
endif; //main function only display head if jmash admin page
}

function pageMash_add_excludes($excludes) {
	//merge array of hardcoded exclude pages with pageMash ones
	$excludes = array_merge( get_option('exclude_pages'), $excludes );
	sort($excludes);
	return $excludes;
}
function pageMash_add_pages(){
	//add link in the management tab
	global $minlevel;
	add_management_page('pageMash page order', 'pageMash', $minlevel, __FILE__, 'pageMash_main');
}

add_action('admin_menu', 'pageMash_add_pages'); //add admin menu under management tab
add_action('admin_head', 'pageMash_head'); //add css styles and JS code to head
add_filter('wp_list_pages_excludes', 'pageMash_add_excludes'); //add exclude pages to wp_list_pages funct


?>