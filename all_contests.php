<?php
include_once './include/db.php';
include_once './include/header.php';
include_once './include/commonFunctions.php';
?>
    <section id="content">
        <div id="one-less" class="all">
            <h1 class="text-effect">All Contests</h1>
            <p>Win Amazing prizes!</p>
        </div> <!-- END id="one-less" -->
<?php
/*  CONTESTS
------------------------------------------------------- */

getAllPrizes($link, $array_prizes);


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
			   GROUP BY 
					    c.`col_id`
			   ORDER BY 
		                c.`col_id` DESC ";

include_once './include/contests.php';