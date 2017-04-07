<?php
include_once './include/commonFunctions.php';

if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

    include_once './include/db.php';
    header("Content-Type: text/html; charset=utf-8");

    $password = trim($_POST['password']);
    $password2 = trim($_POST['password2']);

    if (!empty($password) or !empty($password2)) {

        $hash = calculatePasswordHash($password, $data, $password2);

        $update = "UPDATE `tbl_users` SET `col_password` = '$hash' WHERE `col_id`=" . (int)$_SESSION["user_id"];
        $result = mysqli_query($link, $update);
        mysqli_close($link);

        if ($result) {
            $data['success'] = "Your password has been changed";
            exit(json_encode($data));
        }

    } else {
        $data['error'] = "Choose a password between 6 and 20 characters";
        exit(json_encode($data));
    }
} //кінець ajax
exit();