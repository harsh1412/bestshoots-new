<?php
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	include_once './include/db.php';
	
	if($_SESSION["profile"] != "company") {
		exit();
	}
	
	header("Content-Type: text/html; charset=utf-8");

	$delete = "DELETE FROM `tbl_contests` WHERE `col_id` = ". (int)$_POST["contest_id"] ." AND `col_company_id`= ". (int)$_SESSION["user_id"];
    $query = mysqli_query($link, $delete);

	if ($query) {
		$delete_pz = "DELETE FROM `tbl_prizes` WHERE `col_contest_id` = ". (int)$_POST["contest_id"] ." AND `col_company_id`= ". (int)$_SESSION["user_id"];
    	$query_pz = mysqli_query($link, $delete_pz);
		mysqli_close($link);
		
		if ($query_pz) {
			exit("del");
		}
	}
}