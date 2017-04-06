<?php
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    include_once './include/db.php';
    header("Content-Type: text/html; charset=utf-8");

    $about = trim($_POST["about"]);
    $about = mysqli_real_escape_string($link, $about);

    $profile_link = trim($_POST["profile_link"]);
    $profile_link = mysqli_real_escape_string($link, $profile_link);

    $sql = "SELECT `col_about`, `col_link` FROM `tbl_users` WHERE `col_id` = " . (int)$_SESSION["user_id"];
    $query0 = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($query0);

    //***News Feed***
    $num = 0;

    if ($row['col_about'] != $about) {
        $num++;
    }
    if ($row['col_link'] != $profile_link) {
        $num++;
    }

    if ($num > 1) {
        $text = "Updated profile";
        $text = mysqli_real_escape_string($link, $text);

        $insert0 = "INSERT INTO `tbl_feeds` VALUES (NULL, " . (int)$_SESSION["user_id"] . ", NOW(), '$text', '', '', 1) ";
        mysqli_query($link, $insert0);
    }
    //***END News Feed***

    $update = "UPDATE 
				      `tbl_users`
				  SET 
					  `col_about` = '$about',
					  `col_link` = '$profile_link'
				WHERE 
					  `col_id` = " . (int)$_SESSION["user_id"];
    $query = mysqli_query($link, $update);
    mysqli_close($link);

    if ($query) {
        exit("edit");
    }
} //ajax

exit();