<?php

/**
 * Created by IntelliJ IDEA.
 * User: anton
 * Date: 18.04.17
 * Time: 0:06
 */
class usersDao
{

    public function getLatestFour(){
        $sql_brands = "SELECT 
`col_id`,
`col_company_name`,
`col_avatar`
FROM `tbl_users`
WHERE `col_company_name` <> '' 
ORDER BY `col_id` DESC 
LIMIT 4";
        $query_brands = mysqli_query($link, $sql_brands);

        $result = array();
        while($row_brands = mysqli_fetch_assoc($query_brands)){
            array_push($result, $row_brands);
        }
        return  $result;
    }

}