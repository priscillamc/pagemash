<?php
error_reporting(E_ALL);

require('myjson.php'); //JSON decode lib
require('./../../../wp-config.php');  //config to connect to database

global $wpdb, $excludePages;
$excludePages = array();

// fetch JSON object from $_POST['m']
$json = new Services_JSON(); 
$aMenu = (array) $json->decode(stripslashes($_POST['m']));

function saveList($parent, $children) {
	global $wpdb, $excludePages;
	
	$parent = (int) $parent;
	$result = array();
	$i = 1;
	foreach ($children as $k => $v) {
		
		//IDs are 'JM_#' so strip first 3 characters
		$id = (int) substr($children[$k]->id, 3); 
		
		//if it had the remove class it is now added to the excludePages array
		if($v->hide=='exclude') $excludePages[] = $id;
		
		//update pages in db
		$postquery = "UPDATE $wpdb->posts SET menu_order='$i', post_parent='$parent' WHERE ID='$id'"; 
		$wpdb->query($postquery);
		
		echo $postquery;
		echo "\n";

		if (isset($v->children[0])) {saveList($id, $v->children);}
	$i++;
	}
}

echo saveList(0, $aMenu);

//update excludePages option in database
update_option("exclude_pages", $excludePages, '', 'yes');
?>