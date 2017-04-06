<?php
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

    include_once './include/db.php';
    header("Content-Type: text/html; charset=utf-8");

    $password = trim($_POST['password']);
    $password2 = trim($_POST['password2']);

    if (!empty($password) or !empty($password2)) {

        if (mb_strlen($password, 'utf-8') < 6 or mb_strlen($password, 'utf-8') > 20) {
            $data['error'] = "Choose a password between 6 and 20 characters";
            exit(json_encode($data));
        }

        if ($password != $password2) {
            $data['error'] = "Passwords do not match";
            exit(json_encode($data));
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
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