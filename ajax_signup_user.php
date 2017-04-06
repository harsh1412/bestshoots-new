<?php
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    include_once './include/db.php';
    include_once './include/geoipcity.inc';
    header("Content-Type: text/html; charset=utf-8");

    $username = trim(htmlspecialchars($_POST["username"]));
    $lastname = trim(htmlspecialchars($_POST["lastname"]));
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $username = mb_strtoupper_first($username);
    $lastname = mb_strtoupper_first($lastname);

    if (mb_strlen($username, 'utf-8') < 3 or mb_strlen($username, 'utf-8') > 30) {
        $error["name"] = "username";
        $error["error"] = "Select the name of a length of 3 to 30 characters. You can use letters, numbers, point(.) underscore (_), hyphens (-) and a space";
        exit(json_encode($error));
    }
    if (!preg_match("/^[а-яА-ЯёЁіІa-zA-Z0-9-_. ]+$/u", $username)) {
        $error["name"] = "username";
        $error["error"] = "Select the name of a length of 3 to 30 characters. You can use letters, numbers, point(.) underscore (_), hyphens (-) and a space";
        exit(json_encode($error));
    }

    if (mb_strlen($lastname, 'utf-8') < 3 or mb_strlen($lastname, 'utf-8') > 30) {
        $error["name"] = "lastname";
        $error["error"] = "Select the name of a length of 3 to 30 characters. You can use letters, numbers, point(.) underscore (_), hyphens (-) and a space";
        exit(json_encode($error));
    }
    if (!preg_match("/^[а-яА-ЯёЁіІa-zA-Z0-9-_. ]+$/u", $lastname)) {
        $error["name"] = "lastname";
        $error["error"] = "Select the name of a length of 3 to 30 characters. You can use letters, numbers, point(.) underscore (_), hyphens (-) and a space";
        exit(json_encode($error));
    }

    if ($email == "") {
        $error["name"] = "email";
        $error["error"] = "You must fill in field";
        exit(json_encode($error));
    }

    // Проверяем e-mail на корректность
    if (!preg_match("/^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,3}$/i", $email)) {
        $error["name"] = "email";
        $error["error"] = "Invalid E-mail address";
        exit(json_encode($error));
    }

    $email = mysqli_real_escape_string($link, $email);

    $sql = "SELECT `col_email` FROM `tbl_users` WHERE `col_email`='" . $email . "' ";
    $query = mysqli_query($link, $sql);

    $email_num = mysqli_num_rows($query);
    if ($email_num == '1') {
        $error["name"] = "email";
        $error["error"] = "This e-mail address is already in use, enter another";
        exit(json_encode($error));
    }

    if (mb_strlen($password, 'utf-8') < 6 or mb_strlen($password, 'utf-8') > 20) {
        $error["name"] = "password";
        $error["error"] = "Choose a password between 6 and 20 characters";
        exit(json_encode($error));
    }

    // если всё удачно то внесем в базу пользователя.
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $username = mysqli_real_escape_string($link, $username);
    $lastname = mysqli_real_escape_string($link, $lastname);

    // IP-адрес
    $visitor_ip = getenv("HTTP_X_FORWARDED_FOR");
    if (empty($visitor_ip) || $visitor_ip == "unknown") $visitor_ip = getenv("REMOTE_ADDR");

    // Открыть файл базы
    $gi = GeoIP_open("include/GeoLiteCity.dat", GEOIP_STANDARD);
    // Получить данные из базы
    $record = GeoIP_record_by_addr($gi, $visitor_ip);

    $location = $record->country_name;

    if (!empty($record->city)) {
        $location .= ", " . $record->city;
    }
    GeoIP_close($gi);

    $location = mysqli_real_escape_string($link, $location);

    $insert = "INSERT INTO `tbl_users` 
		VALUES (
			NULL,
			'$email',
			'$hash',
			'',
			'$username',
			'$lastname',
			NOW(),
			'',
			'', 
			'',
			'',
			'$location',
			''
		) ";
    $query2 = mysqli_query($link, $insert);
    $id = mysqli_insert_id($link);

    //***News Feed***
    $text = "Registered in the system";
    $text = mysqli_real_escape_string($link, $text);

    $insert0 = "INSERT INTO `tbl_feeds` VALUES (NULL, " . (int)$id . ", NOW(), '$text', '', '', 1) ";
    mysqli_query($link, $insert0);
    //***END News Feed***

    mysqli_close($link);

    if ($query2) {
        $_SESSION["loged"] = "yes";
        $_SESSION["user_id"] = $id;
        $_SESSION["profile"] = "user";

        $error["name"] = "signup";
        $error["redirect"] = "/profile.php?id=" . $id;
        exit(json_encode($error));
    }
} //кінець ajax