<?php
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    header("Content-Type: text/html; charset=utf-8");

    function close()
    {
        unlink($_POST['target']); //удаляем оригинал загруженного изображения.
    } //кінець close()


    function send()
    {
        // координаты x и y оригинального изображение, где мы
        // буем вырезать фрагмент, по данным, берущимся из формы
        $x = $_POST['x'] * 100 / $_POST['ratio'];
        $y = $_POST['y'] * 100 / $_POST['ratio'];
        $w = $_POST['w'] * 100 / $_POST['ratio'];
        $h = $_POST['h'] * 100 / $_POST['ratio'];

        $ext = strrchr($_POST['target'], "."); //визначаємо розширення файлу
        $unic_name = time() . '_' . rand(0, 1000) . $ext; //ім'я нового зображення
        $target = 'img/' . $_POST['dir'] . '/' . $unic_name;

        // финальные размеры изображения
        $crop_width = $_POST['final_width'];
        $crop_height = $_POST['final_height'];
        // создаём маленькое изображение
        $new = imagecreatetruecolor($crop_width, $crop_height);

        // создаём оригинальное изображение
        $current_image = imagecreatefromjpeg($_POST['target']);

        //вырезаем
        imagecopyresampled($new, $current_image, 0, 0, $x, $y, $crop_width, $crop_height, $w, $h);
        imagejpeg($new, $target, 80);

        //unlink($_POST['target']); // видаляємо зображення з папки temp_av.

        /* ===================================================================================================
        ====================================================================================================*/
        if ($_POST['dir'] == "contests/users_photo") {
            include_once './include/db.php';
            $photo_url = mysqli_real_escape_string($link, $unic_name);

            $insert = "INSERT INTO `tbl_photo` VALUES (
				NULL, 
				" . (int)$_POST['contest_id'] . ",
				" . (int)$_SESSION["user_id"] . ",
				'" . $photo_url . "') ";

            mysqli_query($link, $insert);

            //***News Feed***
            $feed_link = '/inner_page.php?id=' . $_POST['contest_id'];
            $logo = '/img/contests/logo/' . $_POST['contest_logo'];
            $text = 'Participated in <a class="link" href="' . $feed_link . '">' . $_POST['contest_title'] . '</a>';

            $text = mysqli_real_escape_string($link, $text);
            $feed_link = mysqli_real_escape_string($link, $feed_link);
            $logo = mysqli_real_escape_string($link, $logo);

            $insert0 = "INSERT INTO `tbl_feeds` VALUES (NULL, " . (int)$_SESSION["user_id"] . ", NOW(), '$text', '$logo', '$feed_link', 1) ";
            mysqli_query($link, $insert0);
            //***END News Feed***

            mysqli_close($link);
        }
        /* ===================================================================================================
        ====================================================================================================*/
        if ($_POST['dir'] == "users" || $_POST['dir'] == "companies/logo") {
            include_once './include/db.php';
            $photo_url = mysqli_real_escape_string($link, $unic_name);

            $update = "UPDATE `tbl_users` SET `col_avatar` = '$photo_url' WHERE `col_id` = " . (int)$_SESSION["user_id"];
            mysqli_query($link, $update);

            //***News Feed***
            if ($_POST['dir'] == "companies/logo") {
                $text = "Updated company logo";
            } else {
                $text = "Updated avatar";
            }

            $text = mysqli_real_escape_string($link, $text);

            $insert0 = "INSERT INTO `tbl_feeds` VALUES (NULL, " . (int)$_SESSION["user_id"] . ", NOW(), '$text', '', '', 1) ";
            mysqli_query($link, $insert0);
            //***END News Feed***

            mysqli_close($link);
        }
        /* ===================================================================================================
        ====================================================================================================*/
        if ($_POST['dir'] == "contests/header_photo" && !empty($_POST['contest_id'])) {
            include_once './include/db.php';
            $photo_url = mysqli_real_escape_string($link, $unic_name);

            $update = "UPDATE `tbl_contests` SET `col_header_photo` = '$photo_url' WHERE `col_id`= " . (int)$_POST['contest_id'] . " AND `col_company_id` = " . (int)$_SESSION["user_id"];
            mysqli_query($link, $update);
            mysqli_close($link);
        }
        /* ===================================================================================================
        ====================================================================================================*/
        if ($_POST['dir'] == "contests/logo" && !empty($_POST['contest_id'])) {
            include_once './include/db.php';
            $photo_url = mysqli_real_escape_string($link, $unic_name);

            $update = "UPDATE `tbl_contests` SET `col_logo` = '$photo_url' WHERE `col_id`= " . (int)$_POST['contest_id'] . " AND `col_company_id` = " . (int)$_SESSION["user_id"];
            mysqli_query($link, $update);
            mysqli_close($link);
        }

        exit($unic_name);
    } //кінець send()


    if (isset($_POST['act'])) {
        // $_POST['act'] - существует
        switch ($_POST['act']) {
            case "close" : // если она равняется close, вызываем функцию close()
                close();
                break;
            case "send" : // если она равняется send, вызываем функцию send()
                send();
                break;
            default : // если ни тому и не другому  - выходим
                exit();
        }
    }


} //кінець ajax