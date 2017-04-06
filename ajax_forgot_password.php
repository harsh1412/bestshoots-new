<?php
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    include_once './include/constants.php';
    include_once './include/db.php';
    header("Content-Type: text/html; charset=utf-8");

    $email = trim($_POST["email"]);
    $email = mysqli_real_escape_string($link, $email);

    if ($email == "") {
        $error["error"] = "You must fill in field";
        exit(json_encode($error));
    }

    $sql = "SELECT `col_email` FROM `tbl_users` WHERE `col_email`='$email' ";
    $result = mysqli_query($link, $sql);

    if (mysqli_num_rows($result) == "0") {
        $error["error"] = "This e-mail address is not registered";
        exit(json_encode($error));
    }

    $delete = "DELETE FROM 
		                   `tbl_lostpass`
					 WHERE 
						   `col_email` = '$email' ";
    mysqli_query($link, $delete);

    // если всё удачно то внесем в базу пользователя.
    $uniq_id = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . mktime());
    $uniq_id = mysqli_real_escape_string($link, $uniq_id);

    $insert = "INSERT INTO 
						   `tbl_lostpass` 
				   VALUES( 
						   '$email',
						   '$uniq_id',
						   NOW()
						) ";
    $result_insert = mysqli_query($link, $insert);
    mysqli_close($link);

    if ($result_insert) {

        if (mb_substr(PHP_OS, 0, 3, 'UTF-8') == 'WIN') {
            $n = "\r\n";
        } else {
            $n = "\n";
        }

        $href = $host . '/reset_password.php?code=' . $uniq_id;

        $subject = 'Forgot your password?';
        $headers = 'Content-type: text/html; charset="utf-8"' . $n;
        $headers .= 'From: BestShoots <' . $config['from_mail'] . '>' . $n;
        $headers .= 'MIME-Version: 1.0' . $n;
        $headers .= 'Date: ' . date('D, d M Y h:i:s O') . $n;

        $message = "<html>";
        $message .= "<head></head>";
        $message .= "<body>";
        $message .= "<p>Hello,</p>";
        $message .= '<p>To regain access to your account, go, please visit:<br /><a href="' . $href . '" target="_blank">' . $href . '</a></p>';
        $message .= "<p>If you have received this email in error, simply ignore it.</p>";
        $message .= "</body>";
        $message .= "</html>";

        if (mb_send_mail($email, $subject, $message, $headers, $config['from_mail']) !== FALSE) {
            $data['success'] = "Your password has been changed";
            exit(json_encode($data));
        }
    }
    exit();

} //кінець ajax
