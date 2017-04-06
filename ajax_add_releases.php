<?php
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	include_once './include/db.php';
	header("Content-Type: text/html; charset=utf-8");
	
	$title = trim($_POST["title"]);
	$title = mysqli_real_escape_string($link, $title);
	
	$description = trim($_POST["description"]);
	$description = mysqli_real_escape_string($link, $description);
	
	$img = trim($_POST["img"]);
	$img = mysqli_real_escape_string($link, $img);
	
	$insert = "INSERT INTO `tbl_company_photo` 
		VALUES (
			NULL,
			". (int)$_SESSION["user_id"] .",
			'". $img ."',
			NOW(),
			'". $title ."',
			'". $description ."'
			) ";
	
	mysqli_query($link, $insert);
	$id = mysqli_insert_id($link);
	
	$sql = "SELECT
	                  `col_date`
			     FROM 
			          `tbl_company_photo`
			    WHERE 
			          `col_id` = ". (int)$id ." AND `col_company_id` = ". (int)$_SESSION["user_id"];
	$query = mysqli_query($link, $sql);
	mysqli_close($link);
	
	$row = mysqli_fetch_assoc($query);
	
	exit($row['col_date']);
} //ajax

exit();