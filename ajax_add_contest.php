<?php
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    include_once './include/db.php';
    include_once './include/commonFunctions.php';
    include_once './include/repository/contestsDao.php';

    header("Content-Type: text/html; charset=utf-8");

    $title = trim($_POST["title"]);
    $title = mysqli_real_escape_string($link, $title);

    $about = trim($_POST["about"]);
    $about = mysqli_real_escape_string($link, $about);

    $header_photo = trim($_POST["header_photo"]);
    $header_photo = mysqli_real_escape_string($link, $header_photo);

    $logo = trim($_POST["logo"]);
    $logo = mysqli_real_escape_string($link, $logo);

    $duration = trim($_POST["duration"]);
    $duration = mysqli_real_escape_string($link, $duration);

    $contestsDao = new contestsDao($link);
    $id_contest = $contestsDao->addContest($title, $about, $header_photo, $logo, $duration);

    //***News Feed***
    $feed_link = '/inner_page.php?id=' . $id_contest;
    $logo = getContestLogoUrl($logo);

    $text = 'Created a new contest <a class="link" href="' . $feed_link . '">' . $title . '</a>';
    $text = mysqli_real_escape_string($link, $text);
    $feed_link = mysqli_real_escape_string($link, $feed_link);
    $logo = mysqli_real_escape_string($link, $logo);

    $insert0 = "INSERT INTO `tbl_feeds` VALUES (NULL, " . (int)$_SESSION["user_id"] . ", NOW(), '$text', '$logo', '$feed_link', 1) ";
    mysqli_query($link, $insert0);
    //***END News Feed***

    mysqli_close($link);
    exit((string)$id_contest);
} //ajax

exit();