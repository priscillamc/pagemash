<?php
	header('Content-Type: text/css');
	require_once('../../../wp-config.php');
	global $instantUpdateFeature, $excludePagesFeature, $pageMash_abs_dir;
?>
/*                       __  __           _     
       WordPress Plugin |  \/  |         | |    
  _ __   __ _  __ _  ___| \  / | __ _ ___| |__  
 | '_ \ / _` |/ _` |/ _ \ |\/| |/ _` / __| '_ \ 
 | |_) | (_| | (_| |  __/ |  | | (_| \__ \ | | |
 | .__/ \__,_|\__, |\___|_|  |_|\__,_|___/_| |_|
 | |           __/ |  Author: Joel Starnes
 |_|          |___/   URL: pagemash.joelstarnes.co.uk
 
 >>CSS styling for pageMash Admin 
*/



body.wp-admin div#wpwrap div#wpcontent ul#pageMash_pages {
	margin:0 0 0 0;
	list-style:none;
}
ul#pageMash_pages li.collapsed ul {	display:none; }
ul#pageMash_pages li.children {
	background-image: url('<?php echo $pageMash_abs_dir; ?>collapse.png'); 
}
ul#pageMash_pages li.collapsed.children {
	background-image: url('<?php echo $pageMash_abs_dir; ?>expand.png'); 
}
ul#pageMash_pages li { 
	display:block; 
	margin:2px 0 0 0; 
	border-bottom:1px solid #aaa; border-right:1px solid #aaa; border-top:1px solid #ccc; border-left:1px solid #ccc;
	padding:4px 6px 4px 24px; 
	background:#F1F1F1 url('<?php echo $pageMash_abs_dir; ?>page.png') no-repeat 4px 4px; 
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
ul#pageMash_pages li span.pageMash_box {
	font-weight:normal;
	font-size: 1em;
	line-height: 110%;
	border:1px solid #bbb;
	background-color: #eee;
	padding: 0px 3px;
	margin: 0;
	opacity:.5; 
	filter:alpha(opacity=50); zoom:1; /* ie hack[has layout] for opacity */
}
ul#pageMash_pages li .pageMash_box:hover {
	opacity:1; 
	filter:alpha(opacity=100); zoom:1; /* ie hack[has layout] for opacity */
}
ul#pageMash_pages li .pageMash_box .pageMash_pageFunctions { display:none; }
ul#pageMash_pages li .pageMash_box:hover .more { display:none; }
ul#pageMash_pages li .pageMash_box:hover .pageMash_pageFunctions { display:inline; }
ul#pageMash_pages li .pageMash_box a { border:0; }
ul#pageMash_pages li.renaming>.pageMash_box { display:none; }
ul#pageMash_pages li.renaming>span.title {
	border:1px solid #aaa;
	background-color: #ccc;
}
ul#pageMash_pages li.renaming li span.title input {
	font-weight:bold;
}

#pageMash_code {display:block; border:solid 3px #858EF4; background-color:#211E1E; padding:7px; margin:0px;}
#pageMash_code .white{color:#DADADA;}
#pageMash_code .purple{color:#9B2E4D; font-weight:bold;}
#pageMash_code .green{color:#00FF00;}
#pageMash_code .blue{color:#858EF4;}
#pageMash_code .yellow{color:#C1C144;}
#pageMash_code .orange{color:#EC9E00;}