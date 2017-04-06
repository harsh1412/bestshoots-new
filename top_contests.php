<?php
include_once './include/db.php';
include_once './include/header.php';
?>
    <section id="content">
        <div id="one-less" class="all">
            <h1 class="text-effect">TOP Contests</h1>
            <p>Win Amazing prizes!</p>
        </div> <!-- END id="one-less" -->
<?php
/*  CONTESTS
------------------------------------------------------- */
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

$sql_cnt_id = "SELECT 
                   COUNT(`col_id`) AS `col_count`,
  				   `col_contest_id`
		      FROM 
		           `tbl_likes`
	      GROUP BY 
			       `col_contest_id`
	      ORDER BY 
		           `col_count` DESC 
		     LIMIT 10 ";
$query_cnt_id = mysqli_query($link, $sql_cnt_id);

$nn = mysqli_num_rows($query_cnt_id);

while ($row_cnt_id = mysqli_fetch_assoc($query_cnt_id)) $arr_contests[] = $row_cnt_id['col_contest_id'];
$contests_id = implode(",", $arr_contests);

$sql_contests = "SELECT 
                        c.`col_id`,
						c.`col_title`,
						c.`col_logo`,
						c.`col_company_id`,
						u.`col_company_name`
		           FROM 
		                `tbl_contests` c
			       JOIN 
	                    `tbl_users` u ON u.`col_id` = c.`col_company_id`
				  WHERE
				        c.`col_id` IN($contests_id)
	           GROUP BY 
					    c.`col_id`
			   ORDER BY 
				        FIND_IN_SET(c.`col_id`, '$contests_id') ";

include_once './include/contests.php';