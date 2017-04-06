<?php
if (!empty($_FILES)) {
    $tempFile = $_FILES['files']['tmp_name'];
    $ext = mb_strtolower(strrchr($_FILES['files']['name'], ".")); //визначаємо розширення файлу

    $allowed = array('.jpg', '.jpeg', '.gif', '.png');

    if (!in_array($ext, $allowed)) {
        $error["code"] = "2";
        $error["target"] = "ERROR";
        exit(json_encode($error));
    }

    $mimetype = array('image/jpeg', 'image/gif', 'image/png');
    $imageinfo = getimagesize($tempFile);

    if (!in_array($imageinfo["mime"], $mimetype)) {
        $error["code"] = "2";
        $error["target"] = "ERROR";
        exit(json_encode($error));
    }

    if ($_FILES['files']['size'] > $_POST['max_size'] * 1000 * 1024) { // не повинно перевищувати $_POST['max_size'] mb
        $error["code"] = "3";
        $error["target"] = "ERROR";
        exit(json_encode($error));
    }

    // размеры изображения
    $width = $imageinfo[0];
    $height = $imageinfo[1];

    if ($width < $_POST['min_width'] or $height < $_POST['min_height']) {
        unlink($tempFile); //удаляем оригинал загруженного изображения.

        $error["code"] = "1";
        $error["target"] = "ERROR";
        exit(json_encode($error));
    }


    // создаём маленькое изображение
    $new = imagecreatetruecolor($width, $height);
    $back = imagecolorallocate($new, 255, 255, 255);
    imagefilledrectangle($new, 0, 0, $width, $height, $back);

    // создаём оригинальное изображение
    switch ($ext) {
        case '.gif':
            $current_image = imagecreatefromgif($tempFile);  //створює нове зображення з файлу або URL
            break;
        case '.jpg':
            $current_image = imagecreatefromjpeg($tempFile);
            break;
        case '.png':
            $current_image = imagecreatefrompng($tempFile);

            break;
    }

    //вырезаем
    imagecopyresampled($new, $current_image, 0, 0, 0, 0, $width, $height, $width, $height);

    $path = 'img/temp/'; //директорія оригіналу
    $unic_name = time() . '_' . rand(0, 1000) . '.jpg';
    $target = $path . $unic_name; //директорія з ім'ям оригіналу

    imagejpeg($new, $target, 100);

    /* ===================================================================================================
    ====================================================================================================*/
    if ($_POST['dir'] == 'prizes') {

        $new_target = 'img/prizes/' . $unic_name;

        if ($height > 100) {
            $new_width = $width * 100;
            $new_height = $new_width / $height;
            $thumb = imagecreatetruecolor($new_height, 100);

            $source = imagecreatefromjpeg($target);
            //вырезаем
            imagecopyresampled($thumb, $source, 0, 0, 0, 0, $new_height, 100, $width, $height);

            // создаём новое изображение
            imagejpeg($thumb, $new_target, 100);

            imagedestroy($thumb);
            imagedestroy($source);
            unlink($target); //удаляем оригинал загруженного изображения.
        } else {
            rename($target, $new_target); //переносим изображение в другой каталог
        }

        $error["code"] = "4";
        $error["target"] = $unic_name;
        exit(json_encode($error));

    } //$_POST['dir'] == 'prizes'
    /* ===================================================================================================
    ====================================================================================================*/

    $error["code"] = "5";
    $error["target"] = $unic_name;
    exit(json_encode($error));
}