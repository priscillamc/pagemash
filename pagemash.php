<?php
/*
Plugin Name: pageMash
Plugin URI: http://joelstarnes.co.uk/pagemash/
Description: pageMash > pageManagement  [WP_Admin > Manage > pageMash]
Author: Joel Starnes
Version: 0.1.3
Author URI: http://joelstarnes.co.uk/

CHANGELOG:
Release:		Date:			Description:
0.1.0			10 Feb 2008		Initial release
0.1.1			12 Feb 2008		Fixed Removed external include
0.1.2			15 Feb 2008		Fixed CSS&JS headers to only display on pagemash 
0.1.3			19 Feb 2008		Fixed exclude pages feature

*/
#########CONFIG OPTIONS############################################
$minlevel = 7;  /*[deafult=7]*/
/* Minimum user level to access page order */

$instantUpdateFeature = false;  /*[deafult=false]*/
/* Updates the database instantly after a move using ajax 
   otherwise it will wait for update button press.
   nb. enabling this feature will put strain on the mysql server */

$excludePagesFeature = true;  /*[deafult=true]*/
/* Allows you to set pages not to be listed
   Will only work if you have modified the template. */
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

function pageMash_main(){
	global $wpdb, $wp_version, $instantUpdateFeature, $excludePagesFeature;

	//get pages from database
	if($wp_version >= 2.1){
		$pageposts = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type = 'page' AND post_parent = '0' ORDER BY menu_order");
	}else{
		$pageposts = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_status = 'static' AND post_parent = '0' ORDER BY menu_order");
	}
	
	//get pages set to exclude
	$excludePagesList = '>,'.get_option('exclude_pages');
	//precede with '>,' otherwise the first pageid will return 0 when strpos() is called to find it.
	//the initial coma allows us to search for ',$pageid,' so as to avoid partial matches
	?>
	
	<div id="pageMash" class="wrap">
	<div id="pageMash_checkVersion" style="float:right; font-size:.7em; margin-top:5px;">
	    version [0.1.3]
	</div>
	<h2 style="margin-bottom:0; clear:none;">pageMash - pageManagement</h2>
	<p style="margin-top:4px;">You can use this to organise and manage your pages. </p>

	<ul id="pageMash_pages" style="list-style:none;">

	<?php foreach ($pageposts as $page): //list pages ?>
		<li id="<?=$page->ID;?>" <?php if(strpos($excludePagesList, ','.$page->ID.',')){echo 'class="remove"';}//if page is in exclude list, add class remove ?>>
			<strong><?=$page->post_title;?></strong>
			[<a href="<?=get_option('siteurl').'/wp-admin/post.php?action=edit&post='.$page->ID; ?>" title="Edit This Page">edit</a>]
		</li>
	<?php endforeach; ?>

	</ul>
	<p class="submit">
		<span id="update_status" style="float:left; margin-left:40px; opacity:0;"></span>
		<?php if(!$instantUpdateFeature): ?>
			<input type="submit" id="pageMash_submit" tabindex="2" style="font-weight: bold; float:right;" value="Update" name="submit"/>
		<?php endif; ?>
	</p>
	<br style="margin-bottom: .8em;" />
	</div>

	<div class="wrap" style="width:160px; margin-bottom:0; padding:2px; text-align:center;"><a href="#" id="pageMashInfo_toggle" style="text-align:center;">Show|Hide Further Info</a></div>
	<div class="wrap" id="pageMashInfo" style="margin-top:-1px;">
		<h2>How to</h2>
		<h4 style="margin-bottom:0;">Just drag the pages into the order you like, hit 'update' and enjoy the scrummy ajaxified goodness.</h4>
		<p style="margin-top:0;">
			<?php if($excludePagesFeature): ?>
				Click the little red icon to left of each page to hide that page or press the 'edit' button to edit that page. Sorted.<br />
			<?php endif; ?>
			<small><strong>Note:</strong> This plugin only orders top level pages</small>
		</p>
		<p style="margin-bottom:1px;">To use this plugin you need to use the <strong>wp_list_pages()</strong> function with the parameters as shown below:</p>
		<code id="pageMash_code">
<span class="white">&lt;?php</span> <span class="purple">if(</span><span class="blue">function_exists(</span><span class="orange">'pageMash_exclude_pages'</span><span class="blue">)</span><span class="purple">){</span><span class="yellow">$exclude_pages</span><span class="white">=</span><span class="blue">pageMash_exclude_pages();</span><span class="purple">} else{</span><span class="yellow">$exclude_pages</span><span class="white">=</span><span class="orange">''</span><span class="blue">;</span><span class="purple">}</span><span class="white">?&gt;</span><br />
<span class="white">&lt;?php</span> <span class="blue">wp_list_pages(</span><span class="orange">'title_li=&lt;h2&gt;Pages&lt;/h2&gt;&amp;exclude='</span><span class="green">.</span><span class="yellow">$exclude_pages</span><span class="blue">);</span><span class="white">?&gt;</span>
		</code>
		<p>For more information on the wp_list_pages() function checkout the <a href="http://codex.wordpress.org/Template_Tags/wp_list_pages" title="wp_list_pages Documentation">Wordpress Codex</a> and if you have any further questions, just <a href="http://joelstarnes.co.uk/contact/" title="email Joel Starnes">drop me an email</a>.</p>
	</div>
	<?php
}



function pageMash_head(){
if(strrpos('>'.$_GET["page"], 'pagemash')){ // only include header stuff on pagemash admin page
	//stylesheet & javascript to go in page header
	global $instantUpdateFeature, $excludePagesFeature;
	?>
<style type="text/css">
	ul#pageMash_pages li { display:block; margin:2px 0 0 0; border-bottom:1px solid #aaa; border-right:1px solid #aaa; border-top:1px solid #ccc; border-left:1px solid #ccc; padding:5px 6px; background-color:#F1F1F1; }
	body { overflow-y:scroll; }
	span#update_status { font-weight:bold; }
	ul#pageMash_pages li.remove { color:grey; border-style:dashed;}
	ul#pageMash_pages li.remove a { color:grey; }
	ul#pageMash_pages li.remove img { opacity:0.2; }
	
	#pageMash_code {display:block; border:solid 3px #858EF4; background-color:#211E1E; padding:7px; margin=10px;}
	#pageMash_code .white{color:#DADADA;}
	#pageMash_code .purple{color:#9B2E4D; font-weight:bold;}
	#pageMash_code .green{color:#00FF00;}
	#pageMash_code .blue{color:#858EF4;}
	#pageMash_code .yellow{color:#C1C144;}
	#pageMash_code .orange{color:#EC9E00;}
</style>
<script type="text/javascript" src="<?=get_option('siteurl')?>/wp-content/plugins/pagemash/mootools-1.11.js"></script>
<script type="text/javascript">

/* Moo extenders */
Ajax = Ajax.extend({
	/* add timeout to Ajax class */
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
var MySortables = Sortables.extend({
	/* clicking links should not start the drag, but fire the link's event */
	start: function(event, element) {
		if (event.target.tagName != 'A' && event.target.tagName != 'IMG') {
			this.parent(event, element); 
		}	
	}
});


window.addEvent('domready', function(){
	var mySort = new MySortables($('pageMash_pages'), {
		cloneOpacity:.2,
		onComplete: function(){
			/* alternate list colour & if($instantUpdateFeature) ajax db update */
			<?php if($instantUpdateFeature): ?>updateOrder( mySort.serialize() );<?php endif; ?>
			mySort.altColor();
		}
	});
	Sortables.implement({
		serialize: function(){
			/* go through each element of list and return array of element IDs */
			var serial = [];
			this.list.getChildren().each(function(el, i){
				serial[i] = el.getProperty('id');
			}, this);
			return serial;
		},
		altColor: function(){
			/* alternate the list colour */
			var odd = 1;
			this.list.getChildren().each(function(element, i){
				if(odd==1){
					odd=0;
					element.setStyle('background-color', '#F1F1F1');
				}else{
					odd=1;
					element.setStyle('background-color', '#FFFFFF');
				}
			});
		}
	});
	
	mySort.altColor();
	<?php if(!$instantUpdateFeature): ?>
		$('pageMash_submit').addEvent('click', function(e){
			e = new Event(e);
			updateOrder( mySort.serialize() );
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
	
	<?php if($excludePagesFeature): ?>
		$ES('li','pageMash_pages').each(function(el) { DeleteButton(el); })
	<?php endif; ?>
}); /* close dom ready */


function updateOrder (serial) {
	<?php if($excludePagesFeature): ?>
		var excludePages = "";
		$$('#pageMash_pages li.remove').each(function(el){
			excludePages += el.getProperty('id') + ',';
		});
	<?php endif; ?>

	new Ajax('<?=get_option('siteurl')?>/wp-content/plugins/pagemash/reorder.php?order='+serial+'&exclude='+excludePages, {
		method: 'get',
		onComplete: function() {
			$('update_status').setText('Database Updated');
			new Fx.Style($('update_status'), 'opacity', {duration: 300}).start(0,1).chain(function() {
				new Fx.Style($('update_status'), 'opacity', {duration: 1400}).start(1,0);
			});
		},
		timeout: 5500, 
		onTimeout: function() {
			$('update_status').setText('Error: Update Timeout');
			new Fx.Style($('update_status'), 'opacity', {duration: 200}).start(0,1);
		}
	}).request();
}
<?php if($excludePagesFeature): ?>
	var DeleteButton = function(el) {
		new Element('img').setProperties({src: '<?=get_option("siteurl")?>/wp-content/plugins/pagemash/hide.png', alt: 'show|hide'}).addEvent('click', toggleRemove).injectTop(el);
	}
	var toggleRemove = function() {
		this.parentNode.toggleClass('remove');
	}
<?php endif; ?>
</script>
	<?php
} //end main if
} //end function


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