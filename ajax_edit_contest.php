<?php
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    include_once './include/db.php';

    if ($_SESSION["profile"] != "company") {
        exit();
    }

    header("Content-Type: text/html; charset=utf-8");

    $title = mysqli_real_escape_string($link, trim($_POST["title"]));
    $about = mysqli_real_escape_string($link, trim($_POST["about"]));

    $update = "UPDATE `tbl_contests` SET `col_title` = '$title', `col_about` = '$about' WHERE `col_id` = " . (int)$_POST["contest_id"] . " AND `col_company_id`= " . (int)$_SESSION["user_id"];
    $query = mysqli_query($link, $update);
    $id = mysqli_insert_id($link);
    mysqli_close($link);

    if ($query) {
        exit("update");
    }
}