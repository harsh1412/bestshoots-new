<?php
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    include_once './include/db.php';
    header("Content-Type: text/html; charset=utf-8");

    if ($_SESSION["profile"] == "company") {
        exit("error2");
    }

    $sql = "SELECT `col_user_id` FROM `tbl_photo` WHERE `col_contest_id` = " . (int)$_POST["contest_id"] . " AND `col_user_id` = " . (int)$_SESSION["user_id"];
    $query = mysqli_query($link, $sql);
    mysqli_close($link);

    if (mysqli_num_rows($query) > 0) {
        exit("error");
    }
} //ajax

exit();