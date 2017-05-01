<?php

/**
 * Created by IntelliJ IDEA.
 * User: anton
 * Date: 18.04.17
 * Time: 0:40
 */
class contestsDao extends baseDao
{

    /**
     * @param $title
     * @param $about
     * @param $header_photo
     * @param $logo
     * @param $duration
     * @param $link
     * @return mixed
     */
    function addContest($title, $about, $header_photo, $logo, $duration)
    {
        $insert = "INSERT INTO `tbl_contests` VALUES (
		NULL, 
		'" . $title . "',
		'" . $about . "',
		'" . $header_photo . "',
		'" . $logo . "',
		NOW(),
		NOW() + INTERVAL " . $duration . ",
		" . (int)$_SESSION["user_id"] . ",
		0) ";

        mysqli_query($this->link, $insert);
        $id_contest = mysqli_insert_id($this->link);
        return $id_contest;
    }

}