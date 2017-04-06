<?php
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    include_once './include/db.php';

    if ($_SESSION["profile"] != "company") {
        exit();
    }

    header("Content-Type: text/html; charset=utf-8");

    $delete = "DELETE FROM `tbl_prizes` WHERE `col_id` = " . (int)$_POST["id"] . " AND `col_company_id`= " . (int)$_SESSION["user_id"];
    $query = mysqli_query($link, $delete);
    mysqli_close($link);

    if ($query) {
        exit("del");
    }
}