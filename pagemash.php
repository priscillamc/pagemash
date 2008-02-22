<?php
/*
Plugin Name: pageMash
Plugin URI: http://joelstarnes.co.uk/pagemash/
Description: pageMash > pageManagement  [WP_Admin > Manage > pageMash]
Author: Joel Starnes
Version: 1.0.0
Author URI: http://joelstarnes.co.uk/

CHANGELOG:
Release:		Date:			Description:
0.1.0			10 Feb 2008		Initial release
0.1.1			12 Feb 2008		Minor fixes > Removed external include
0.1.2			15 Feb 2008		Minor fixes > Fixed CSS&JS headers to only display on pagemash 
1.0.0 beta		19 Feb 2008		Major update > 	Recusive page handles unlimited nested children,
												collapsable list items, interface makeover...

FIXME:
	@fixme with instantUpdateFeature, hide will not send the update
	@fixme ie highlights text as you drag the list item
	
*/
#########CONFIG OPTIONS############################################
$minlevel = 7;  /*[deafult=7]*/
/* Minimum user level to access page order */

$excludePagesFeature = false;  /*[deafult=true]*/
/* Allows you to set pages not to be listed
   Will only work if you have modified the template. */

$instantUpdateFeature = false;  /*[deafult=false]*/
/* Updates the database instantly after a move using ajax 
   otherwise it will wait for update button press.
   nb. this feature has not been optimised and enabling will cause 
   much increased server load */

###################################################################
/*
CREDITS:
Valerio Proietti - Mootools JS Framework [http://mootools.net/]
Stefan Lange-Hegermann - Mootools AJAX timeout class extension [http://www.blackmac.de/archives/44-Mootools-AJAX-timeout.html]
vladimir - Mootools Sortables class extension [http://vladimir.akilles.cl/scripts/sortables/]
ShiftThis - WP Page Order Plugin [http://www.shiftthis.net/wordpress-order-pages-plugin/]
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
	global $wpdb, $wp_version, $instantUpdateFeature, $excludePagesFeature, $excludePagesList;
	if($wp_version >= 2.1){ //get pages from database
		$pageposts = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type = 'page' AND post_parent = '$post_parent' ORDER BY menu_order");
	}else{
		$pageposts = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_status = 'static' AND post_parent = '$post_parent' ORDER BY menu_order");
	}
	
	if ($pageposts == true){ //if $pageposts == true then it does have sub-page(s), so list them.
		echo '<ul ';
		if($post_parent==0) echo 'id="pageMash_pages" '; //add this ID only to root 'ul' element
		echo 'style="list-style:none;">';
   
		foreach ($pageposts as $page): //list pages, [the 'li' ID must be the page ID] ?>
			<li id="pm_<?=$page->ID;?>" <?php if(strpos($excludePagesList, ','.$page->ID.',')){echo 'class="remove"';}//if page is in exclude list, add class remove ?>>
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
	global $wpdb, $wp_version, $instantUpdateFeature, $excludePagesFeature, $excludePagesList;

	//get pages-to-hide from database
	$excludePagesObj = $wpdb->get_results("SELECT option_value FROM $wpdb->options WHERE option_name = 'exclude_pages'");
	$excludePagesList = '>,'.$excludePagesObj[0]->option_value; 
		//precede with '>,' otherwise the first pageid will return 0 when strpos() is called to find it.
		//the initial coma allows us to search for ',$pageid,' so as to avoid partial matches
	?>
	<div id="debug_list"></div>
	<div id="pageMash" class="wrap">
	<div id="pageMash_checkVersion" style="float:right; font-size:.7em; margin-top:5px;">
	    version [1.0.0 beta]
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
		<h2>How to Install</h2>
		<p style="font-size:1.1em;">In most cases, to use this plugin you will not need to change anything, however if its not working you will need to either:</p>
		<ol style="list-style-type:upper-alpha;">
		    <li>Check your 'pages' widget in the WP admin panel under the Presentation>Widgets tab and click the little icon on the pages widget and ensure that <strong>sort by</strong> is set to <strong>'page order'</strong>. </li>
		    <li>If you want the pages listed else-where or do not use the widgets or you would like to use the excludePagesFeature, then you need to edit your template:
		    	<ol style="list-style-type:upper-roman;">
		    	    <li style="margin-bottom:0;">To use the code in your sidebar.php file you need to remove all widgets in your WP admin to active the sidebar code and then find the <strong>wp_list_pages()</strong> function and change it to the code below </li>
		    	    <li style="margin-bottom:0;">To insert the pages in your header; modify header.php insert the code anywhere inside the body tag. (You may want to add the depth=1 parameter on the 2nd line if you only want top level pages listed)</li>
		    	</ol>
		    Then to enable the excludePagesFeature find the line $excludePagesFeature = false; near the top of pagemash.php and change the value to true.
		    </li>
		</ol>
		<p style="margin-bottom:0; font-weight:bold;">Code:</p>
		<code>
			<span class="white">&lt;?php</span> <span class="purple">if(</span><span class="blue">function_exists(</span><span class="orange">'pageMash_exclude_pages'</span><span class="blue">)</span><span class="purple">){</span><span class="yellow">$exclude_pages</span><span class="white">=</span><span class="blue">pageMash_exclude_pages();</span><span class="purple">} else{</span><span class="yellow">$exclude_pages</span><span class="white">=</span><span class="orange">''</span><span class="blue">;</span><span class="purple">}</span><span class="white">?&gt;</span><br />
			<span class="white">&lt;?php</span> <span class="blue">wp_list_pages(</span><span class="orange">'title_li=&lt;h2&gt;Pages&lt;/h2&gt;&amp;exclude='</span><span class="green">.</span><span class="yellow">$exclude_pages</span><span class="blue">);</span><span class="white">?&gt;</span>
		</code>
		<p>The plugin code is very simple and flexible, for more information look at the wp_list_pages() function on the <a href="http://codex.wordpress.org/Template_Tags/wp_list_pages" title="wp_list_pages Documentation">Wordpress Codex</a> and if you have any further questions or feedback, just <a href="http://joelstarnes.co.uk/contact/" title="email Joel Starnes">drop me an email</a>.</p>
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
	}
	ul#pageMash_pages li.collapsed ul {
		display:none;
	}
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
	ul#pageMash_pages li span.title {
		font-weight: bold;
	}
	ul#pageMash_pages li.collapsed.children span.title {
		text-decoration: underline;
	}
	#update_status { 
		font-weight:bold; 
		display:block; 
		border:2px solid #AC604C;
		background-color: #DDA37A;
		padding: 2px 6px;
	}
	ul#pageMash_pages li.remove { color:grey; border-style:dashed; opacity:.5;}
	ul#pageMash_pages li.remove a { color:grey; }
	ul#pageMash_pages li span.pageMash_pageFunctions {
		border:1px solid #ccc;
		background-color: #eee;
		padding: 1px 3px;
	}
	ul#pageMash_pages li span.pageMash_pageFunctions a { border:0; }
	
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
	
	code {display:block; border:solid 3px #858EF4; background-color:#211E1E; padding:7px; margin:0px;}
	code .white{color:#DADADA;}
	code .purple{color:#9B2E4D; font-weight:bold;}
	code .green{color:#00FF00;}
	code .blue{color:#858EF4;}
	code .yellow{color:#C1C144;}
	code .orange{color:#EC9E00;}
</style>
<!-- Current JSON ajax code not compatible with newer releases of moo -->
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
		/* update: "debug_list", */
		onComplete: function() {
			$('update_status').setText('Database Updated');
			new Fx.Style($('update_status'), 'opacity', {duration: 500}).start(0,1).chain(function() {
				new Fx.Style($('update_status'), 'opacity', {duration: 1500}).start(1,0);
			});
		},
		timeout: 5500, 
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
	
}); /* close dom ready */
</script>
	<?php
endif; //main function only display head if jmash admin page
}


function pageMash_add_pages(){
	//add link in the management tab
	global $minlevel;
	add_management_page('pageMash page order', 'pageMash', $minlevel, __FILE__, 'pageMash_main');
}
function pageMash_exclude_pages(){
	/* returns coma delimited list of pages to exclude from output
	   this is used as a parameter in the wp_list_pages() function */
	global $wpdb;
	$excludePagesObj = $wpdb->get_results("SELECT option_value FROM $wpdb->options WHERE option_name = 'exclude_pages'");
	return $excludePagesObj[0]->option_value;
}

add_action('admin_menu', 'pageMash_add_pages'); //add admin menu under management tab
add_action('admin_head', 'pageMash_head'); //add css styles and JS code to head


?>