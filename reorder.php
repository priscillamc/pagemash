<?php
require('./../../../wp-config.php'); //run config to cennect to database

$order = explode(",", $_GET['order']); //cut up the coma delimited page IDs into array
$position = 1;
foreach ($order as $pageid):
	$postquery = "UPDATE $wpdb->posts SET menu_order='$position' WHERE ID='$pageid'"; //setup db query
	$wpdb->query($postquery); //update pages in db
	$position += 1; //increment position var
endforeach;

$exclude = $_GET['exclude']; 
$postquery = "UPDATE $wpdb->options SET option_value='$exclude' WHERE option_name='exclude_pages'"; //setup db query
$wpdb->query($postquery); //update pages in db

echo "pagemashed";
?>
