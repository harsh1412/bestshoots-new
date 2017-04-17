<?php
/**
 * Created by IntelliJ IDEA.
 * User: anton
 * Date: 7.04.17
 * Time: 9:28
 */

/**
 * @param $row
 * @param $error
 */
function prepareLoginSession($row, $error)
{
    $redirect = setSessions($row);

    $error["name"] = "signin";
    $error["redirect"] = $redirect;
    exit(json_encode($error));
}

/**
 * @param $row
 * @return string
 */
function setSessions($row)
{
    $_SESSION["loged"] = "yes";
    $_SESSION["user_id"] = $row["col_id"];

    if (empty($row['col_company_name'])) {
        $redirect = "/profile.php?id=" . $row["col_id"];
        $_SESSION["profile"] = "user";
        return $redirect;
    } else {
        $redirect = "/company_profile.php?id=" . $row["col_id"];
        $_SESSION["profile"] = "company";
        return $redirect;
    }
}


function setRedirectHeaderToProfile()
{
    if ($_SESSION["loged"] == "yes") {

        if ($_SESSION["profile"] == "user") {
            $url = "profile.php?id=" . $_SESSION["user_id"];
        } else {
            $url = "company_profile.php?id=" . $_SESSION["user_id"];
        }

        header("Location: " . $url);
    }
}



/**
 * @param $link
 * @param $array_prizes
 */
function getAllPrizes($link, $array_prizes)
{
//Выбираем все призы
    $sql_prizes = "SELECT 
					  `col_title`,
					  `col_type`,
					  `col_contest_id`
		         FROM 
		              `tbl_prizes` ";
    $query_prizes = mysqli_query($link, $sql_prizes);

    while ($row_prizes = mysqli_fetch_assoc($query_prizes)) {
        $array_prizes[] = $row_prizes;
    } //END WHILE
}


/**
 * @param $password
 * @param $data
 * @param $password2
 * @return mixed
 */
function calculatePasswordHash($password, $data, $password2)
{
    if (mb_strlen($password, 'utf-8') < 6 or mb_strlen($password, 'utf-8') > 20) {
        $data['error'] = "Choose a password between 6 and 20 characters";
        exit(json_encode($data));
    }

    if ($password != $password2) {
        $data['error'] = "Passwords do not match";
        exit(json_encode($data));
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    return $hash;
}


/**
 * @param $logo
 * @return string
 */
function getContestLogoUrl($logo)
{
    if(strlen($logo) > 0) {
        return '/img/contests/logo/' . $logo;
    }

    return '/images/no-image.jpg';
}