<?php
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    include_once './include/db.php';
    header("Content-Type: text/html; charset=utf-8");

    $email = trim(($_POST["email"]));
    $password = trim(($_POST["password"]));

    if ($email == "") {
        $error["name"] = "email";
        $error["error"] = "You must fill in field";
        exit(json_encode($error));
    }
    if ($password == "") {
        $error["name"] = "password";
        $error["error"] = "You must fill in field";
        exit(json_encode($error));
    }

    // удаляем ip-адреса ошибавшихся при входе пользователей через 15 минут.
    $delete = "DELETE FROM `tbl_error` WHERE UNIX_TIMESTAMP() - UNIX_TIMESTAMP(`col_date`) > 900 ";
    mysqli_query($link, $delete);

    $email = mysqli_real_escape_string($link, $email);

    $sql = "SELECT `col_id`, `col_password`, `col_company_name` FROM `tbl_users` WHERE `col_email` = '" . $email . "' ";
    $query = mysqli_query($link, $sql);

    $row = mysqli_fetch_assoc($query);

    // Если пароли совпали то создами переменные сессии (вход да) и (ID пользователя)
    if (password_verify($password, $row["col_password"])) {
        $_SESSION["loged"] = "yes";
        $_SESSION["user_id"] = $row["col_id"];

        if (empty($row['col_company_name'])) {
            $redirect = "/profile.php?id=" . $row["col_id"];
            $_SESSION["profile"] = "user";
        } else {
            $redirect = "/company_profile.php?id=" . $row["col_id"];
            $_SESSION["profile"] = "company";
        }

        $error["name"] = "signin";
        $error["redirect"] = $redirect;
        exit(json_encode($error));
    } else {
        $ip = getenv("HTTP_X_FORWARDED_FOR");
        if (empty($ip) || $ip == "unknown") $ip = getenv("REMOTE_ADDR");

        // извлекаем из базы колличество неудачных попыток входа за последние 15 минут у пользователя с данным ip
        $sql2 = "SELECT `col_ip`, `col_number` FROM `tbl_error` WHERE `col_ip` = '$ip' ";
        $query2 = mysqli_query($link, $sql2);

        $row2 = mysqli_fetch_assoc($query2);

        if ($ip == $row2["col_ip"]) {
            $col = $row2["col_number"] + 1; //Если есть, то приплюсовываем количесво

            $ip = mysqli_real_escape_string($link, $ip);

            $update = "UPDATE `tbl_error` SET `col_number` = $col, `col_date` = NOW() WHERE `col_ip` = '$ip' ";
            mysqli_query($link, $update);

            /*
            //если таковых попыток больше трех, то выдаем сообщение.
            if ($col > 3) {
                exit("code");
            }
            */
        } else {
            //если за последние 15 минут ошибок не было, то вставляем новую запись в таблицу "error"
            $ip = mysqli_real_escape_string($link, $ip);

            $insert = "INSERT INTO `tbl_error` (`col_ip`, `col_date`, `col_number`) VALUES ('$ip', NOW(), 1) ";
            mysqli_query($link, $insert);
        }
        $error["name"] = "password";
        $error["error"] = "Wrong email or password";
        exit(json_encode($error));
    }
}
exit();