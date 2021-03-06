<?php
include_once './include/db.php';
include_once './include/header.php';
include_once './include/commonFunctions.php';
include_once './include/log4php/Logger.php';
Logger::configure('./include/log4php.xml');
$log = Logger::getLogger('TOP_CONTESTS');

?>
    <section id="content">
        <div id="one-less" class="all">
            <h1 class="text-effect">TOP Contests</h1>
            <p>Win Amazing prizes!</p>
        </div> <!-- END id="one-less" -->
<?php
/*  CONTESTS
------------------------------------------------------- */

getAllPrizes($link, $array_prizes);


$sql_cnt_id = "SELECT 
COUNT(`col_id`) AS `col_count`,
`col_contest_id`
FROM `tbl_likes`
GROUP BY `col_contest_id`
ORDER BY `col_count` DESC 
LIMIT 10 ";



$log->debug(safeLogging($sql_cnt_id));

$query_cnt_id = mysqli_query($link, $sql_cnt_id);

$nn = mysqli_num_rows($query_cnt_id);

while ($row_cnt_id = mysqli_fetch_assoc($query_cnt_id)) {
    $arr_contests[] = $row_cnt_id['col_contest_id'];
}

$contests_id = implode(",", $arr_contests);

$sql_contests = "SELECT 
c.`col_id`,
c.`col_title`,
c.`col_logo`,
c.`col_company_id`,
u.`col_company_name`
FROM `tbl_contests` c
JOIN `tbl_users` u ON u.`col_id` = c.`col_company_id`
WHERE c.`col_id` IN($contests_id)
GROUP BY c.`col_id`
ORDER BY FIND_IN_SET(c.`col_id`, '$contests_id') ";

$log->debug(safeLogging($sql_contests));

include_once './include/contests.php';