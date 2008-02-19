<?php
require('./../../../wp-config.php'); //run config to connect to database

$order = explode(",", $_GET['order']); //cut up the coma delimited page IDs into array
$position = 1;
foreach ($order as $pageid):
	$postquery = "UPDATE $wpdb->posts SET menu_order='$position' WHERE ID='$pageid'"; //setup db query
	$wpdb->query($postquery); //update pages in db
	$position += 1; //increment position var
endforeach;

update_option("exclude_pages", $_GET['exclude'], '', 'yes');

echo "pagemashed";
?>
