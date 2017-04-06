<?php
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    include_once './include/db.php';
    header("Content-Type: text/html; charset=utf-8");

    $delete = "DELETE FROM 
		                   `tbl_feeds`
					 WHERE 
						   `col_id` = " . (int)$_POST["feed_id"] . " AND `col_profile_id` = " . (int)$_SESSION["user_id"];
    $query = mysqli_query($link, $delete);

    mysqli_close($link);

    if ($query) {
        exit("del");
    }
} //ajax

exit();