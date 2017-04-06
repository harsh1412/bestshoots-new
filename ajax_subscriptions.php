<?php
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	include_once './include/db.php';
	header("Content-Type: text/html; charset=utf-8");
	
	$sql = "SELECT `col_id` FROM `tbl_subscriptions` WHERE `company_id` = ". (int)$_POST["company_id"] ." AND `user_id` = ". (int)$_SESSION["user_id"];
	$query = mysqli_query($link, $sql);
	
	if (mysqli_num_rows($query) > 0) {
		$delete = "DELETE FROM `tbl_subscriptions` WHERE `company_id` = ". (int)$_POST["company_id"] ." AND `user_id` = ". (int)$_SESSION["user_id"];
    	$query3 = mysqli_query($link, $delete);
		
		$text = 'Unsubscribed from'; //feed
	} else {
		$insert = "INSERT INTO `tbl_subscriptions` VALUES (NULL, ". (int)$_POST["company_id"] .", ". (int)$_SESSION["user_id"] .") ";
		$query2 = mysqli_query($link, $insert);
		
		$text = 'Subscribed to'; //feed
	}
	
	//***News Feed***
	$feed_link = '/company_profile.php?id='. $_POST["company_id"];
	$logo = '/img/companies/logo/'. $_POST["company_logo"];
	
	$text .= ' <a class="link" href="'. $feed_link .'">'. $_POST["company_name"] .'</a>';	
	$text = mysqli_real_escape_string($link, $text);
	$feed_link = mysqli_real_escape_string($link, $feed_link);
	$logo = mysqli_real_escape_string($link, $logo);
				
	$insert0 = "INSERT INTO `tbl_feeds` VALUES (NULL, ". (int)$_SESSION["user_id"] .", NOW(), '$text', '$logo', '$feed_link', 1) ";
	mysqli_query($link, $insert0);
	//***END News Feed***
	
	if ($query2) {
		exit("subscribe");
	}
	
	if ($query3) {
		exit("unsubscribe");
	}
} //ajax

exit();