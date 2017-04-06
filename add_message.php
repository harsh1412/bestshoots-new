<?php
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

    include_once './include/db.php';
    header("Content-Type: text/html; charset=utf-8");

    $number_text = mb_strlen($_POST['text'], "UTF-8"); //максимальна кількість символів
    if ($number_text > 2048) exit('2');
    if ($number_text < 1) exit('3');

    $dialog_id = min($_SESSION["user_id"], $_POST["to_id"]) . "_" . max($_SESSION["user_id"], $_POST["to_id"]);
    $dialog_id = mysqli_real_escape_string($link, $dialog_id);

    $text = trim($_POST["text"]);
    $text = mysqli_real_escape_string($link, $text);

    // Сохраняем данные в БД
    $insert = "INSERT INTO `tbl_messages` VALUES (NULL, " . (int)$_SESSION["user_id"] . ", " . (int)$_POST["to_id"] . ", NOW(), '$text', 1, 1, 1, '$dialog_id') ";
    mysqli_query($link, $insert);

    $id = mysqli_insert_id($link);

    $sql = "SELECT `col_date` FROM `tbl_messages` WHERE `col_id`=" . $id;
    $result = mysqli_query($link, $sql);
    mysqli_close($link);

    $row = mysqli_fetch_assoc($result);

    $data = "<li class='outcoming'>";
    $data .= nl2br(htmlspecialchars($_POST["text"]));
    $data .= '<time class="date timeago" datetime="' . $row['col_date'] . '"></time>';
    $data .= "</li>";

    exit($data);
} //кінець ajax
exit();