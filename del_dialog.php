<?php
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

    include_once './include/db.php';
    header("Content-Type: text/html; charset=utf-8");

    $dialog_id = mysqli_real_escape_string($link, $_POST["dialog_id"]);

    $update = "UPDATE 
	                  `tbl_messages` 
				  SET 
				      `col_flag_from`= IF(`col_from_id` = " . (int)$_SESSION["user_id"] . ", 0, `col_flag_from`), 
	                  `col_flag_to`= IF(`col_to_id` = " . (int)$_SESSION["user_id"] . ", 0, `col_flag_to`) 
				WHERE 
				      `col_dialog_id` = '$dialog_id' ";
    mysqli_query($link, $update);

    $delete = "DELETE FROM 
	                       `tbl_messages` 
					 WHERE 
					       `col_dialog_id` = '$dialog_id' AND `col_flag_from` = 0 AND `col_flag_to` = 0 ";
    mysqli_query($link, $delete);

    mysqli_close($link);

    exit();
} //кінець ajax