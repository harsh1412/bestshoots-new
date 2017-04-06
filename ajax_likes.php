<?php
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    include_once './include/db.php';
    header("Content-Type: text/html; charset=utf-8");

    if ($_POST["author_id"] == $_SESSION["user_id"]) {
        exit("error");
    }

    $sql_er = "SELECT
	                  `col_id`
			     FROM 
			          `tbl_likes`
			    WHERE 
			          `col_contest_id` = " . (int)$_POST["contest_id"] . " AND `col_author_id` <> " . (int)$_POST["author_id"] . " AND `col_user_id` = " . (int)$_SESSION["user_id"];
    $query_er = mysqli_query($link, $sql_er);

    if (mysqli_num_rows($query_er) > 0) {
        exit("error2");
    }

    $sql = "SELECT
	               `col_id`
			  FROM 
			       `tbl_likes`
			 WHERE 
			       `col_contest_id` = " . (int)$_POST["contest_id"] . " AND `col_author_id` = " . (int)$_POST["author_id"] . " AND `col_user_id` = " . (int)$_SESSION["user_id"];
    $query = mysqli_query($link, $sql);

    if (mysqli_num_rows($query) > 0) {
        $delete = "DELETE FROM 
		                       `tbl_likes`
						 WHERE 
						       `col_contest_id` = " . (int)$_POST["contest_id"] . " AND `col_author_id` = " . (int)$_POST["author_id"] . " AND `col_user_id` = " . (int)$_SESSION["user_id"];
        $query3 = mysqli_query($link, $delete);
    } else {
        $insert = "INSERT INTO 
		                       `tbl_likes` 
					    VALUES (NULL,
								" . (int)$_POST["contest_id"] . ",
								" . (int)$_POST["author_id"] . ",
								" . (int)$_SESSION["user_id"] . ",
								" . (int)$_POST["company_id"] . ",
								NOW()) ";
        $query2 = mysqli_query($link, $insert);
    }

    if ($query2) {
        exit("like");
    }

    if ($query3) {
        exit("notlike");
    }
} //ajax

exit();