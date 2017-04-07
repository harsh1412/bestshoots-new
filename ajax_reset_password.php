<?php
include_once './include/commonFunctions.php';

if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

    include_once './include/db.php';
    header("Content-Type: text/html; charset=utf-8");

    $password = trim($_POST['password']);
    $password2 = trim($_POST['password2']);

    if (!empty($password) or !empty($password2)) {

        $hash = calculatePasswordHash($password, $data, $password2);

        $email = trim($_POST["email"]);
        $email = mysqli_real_escape_string($link, $email);

        $update = "UPDATE `tbl_users` SET `col_password` = '$hash' WHERE `col_email`= '$email' ";
        $result = mysqli_query($link, $update);

        if ($result) {

            $delete = "DELETE FROM `tbl_lostpass` WHERE `col_email` = '$email' ";
            mysqli_query($link, $delete);

            $sql = "SELECT `col_id`, `col_company_name` FROM `tbl_users` WHERE `col_email` = '$email' ";
            $query = mysqli_query($link, $sql);
            mysqli_close($link);
            $row = mysqli_fetch_assoc($query);

            prepareLoginSession($row, $error);
        }

    } else {
        $data['error'] = "Choose a password between 6 and 20 characters";
        exit(json_encode($data));
    }
} //кінець ajax
exit();