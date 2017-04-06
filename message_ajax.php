<?php
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

    include_once './include/db.php';
    header("Content-Type: text/html; charset=utf-8");

    if (empty($_SESSION["user_id"])) exit("1");

    $dialog_id = min((int)$_SESSION["user_id"], $_POST["to_id"]) . "_" . max((int)$_SESSION["user_id"], $_POST["to_id"]);
    $dialog_id = mysqli_real_escape_string($link, $dialog_id);

    $update = "UPDATE `tbl_messages` SET `col_flag_new`= 0 WHERE `col_to_id`=" . (int)$_SESSION["user_id"]
        . " AND `col_dialog_id` = '$dialog_id' ";
    mysqli_query($link, $update);

    $sql = "SELECT 
			   `col_date`,
			   `col_text`,
			   `col_from_id`
		  FROM 
		       `tbl_messages` 
		 WHERE 
		       ((`col_from_id` = " . (int)$_SESSION["user_id"] . " AND `col_flag_from` = 1) OR (`col_to_id` = " . (int)$_SESSION["user_id"] . " AND `col_flag_to` = 1))
			   AND `col_dialog_id` = '$dialog_id' 
	  GROUP BY 
	           `col_id` 
	  ORDER BY 
	           `col_date` ";
    $result = mysqli_query($link, $sql);
    mysqli_close($link);

    if (mysqli_num_rows($result) > 0) {
        $data = '';

        while ($row = mysqli_fetch_assoc($result)) { //виводимо всі повідомлення в циклі

            if ($row["col_from_id"] == $_SESSION["user_id"]) {
                $class = 'outcoming';
            } else {
                $class = 'incoming';
            }

            $data .= '<li class="' . $class . '">';
            $data .= nl2br(htmlspecialchars($row['col_text']));
            $data .= '<time class="date timeago" datetime="' . $row['col_date'] . '"></time>';
            $data .= "</li>";
        }

        exit($data);
    }

} //кінець ajax
exit();