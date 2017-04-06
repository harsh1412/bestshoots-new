<?php
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    include_once './include/db.php';

    if ($_SESSION["profile"] != "company") {
        exit();
    }

    header("Content-Type: text/html; charset=utf-8");

    $date = mysqli_real_escape_string($link, $_POST["date"]);

    if ($_POST['type'] == 'start') {
        $update = "UPDATE `tbl_contests` SET `col_date_start` = CONCAT('$date',' ',TIME(`col_date_start`)) WHERE `col_id` = " . (int)$_POST["contest_id"] . " AND `col_company_id`= " . (int)$_SESSION["user_id"];
    } else {
        $update = "UPDATE `tbl_contests` SET `col_date_end` = CONCAT('$date',' ',TIME(`col_date_end`)) WHERE `col_id` = " . (int)$_POST["contest_id"] . " AND `col_company_id`= " . (int)$_SESSION["user_id"];
    }

    $query = mysqli_query($link, $update);
    $id = mysqli_insert_id($link);
    mysqli_close($link);

    if ($query) {
        exit("update");
    }
}