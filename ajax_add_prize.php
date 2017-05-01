<?php
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    include_once './include/db.php';
    header("Content-Type: text/html; charset=utf-8");

    $title = trim($_POST["title"]);
    $title = mysqli_real_escape_string($link, $title);

    $description = trim($_POST["description"]);
    $description = mysqli_real_escape_string($link, $description);

    $img = trim($_POST["img"]);
    $img = mysqli_real_escape_string($link, $img);

    $sql = "SELECT `col_end_winners` FROM `tbl_prizes` WHERE `col_contest_id` = " . (int)$_POST["contest_id"] . " 
		AND `col_type` = " . (int)$_POST["type"] . " ORDER BY `col_end_winners` DESC LIMIT 1";
    $query = mysqli_query($link, $sql);

    $num = mysqli_num_rows($query);
    if ($num > 0) {
        $row = mysqli_fetch_assoc($query);
        $start_winners = $row["col_end_winners"] + 1;
        $end_winners = $row["col_end_winners"] + (int)$_POST["winners"];
    } else {
        $start_winners = 1;
        $end_winners = (int)$_POST["winners"];
    }
    $winners = ordinal_suffix($start_winners) . ' - ' . ordinal_suffix($end_winners);

    $insert = "INSERT INTO `tbl_prizes` 
		VALUES (
			NULL, 
			'" . $title . "',
			'" . $description . "',
			'" . $img . "',
			" . (int)$start_winners . ",
			" . (int)$end_winners . ",
			" . (int)$_POST["type"] . ",
			" . (int)$_POST["contest_id"] . ",
			" . (int)$_SESSION["user_id"] . "
			) ";

    include_once './include/log4php/Logger.php';
    Logger::configure('./include/log4php.xml');
    $log = Logger::getLogger('TOP_CONTESTS');

    $log->debug("insert query " . $insert);

    mysqli_query($link, $insert);
    $id_contest = mysqli_insert_id($link);
    mysqli_close($link);

    $data["winners"] = $winners;
    $data["id"] = $id_contest;
    exit(json_encode($data));
} //ajax