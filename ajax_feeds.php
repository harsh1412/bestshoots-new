<?php
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	include_once './include/db.php';
	header("Content-Type: text/html; charset=utf-8");
	
	$update = "UPDATE 
				      `tbl_feeds`
				  SET 
					  `col_flag` = IF(`col_flag` = 1, 2, 1)
				WHERE 
					  `col_id` = ". (int)$_POST["feed_id"] ." AND `col_profile_id` = ". (int)$_SESSION["user_id"];
	$query = mysqli_query($link, $update);
	mysqli_close($link);
	
	if ($query) {
		exit("OK");
	}
} //ajax

exit();