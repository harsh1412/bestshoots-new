<?php
include_once './include/db.php';
include_once './include/header.php';
?>
<section id="content">
    <div id="one-less" class="all">
        <h1 class="text-effect">All Brands</h1>
        <p>Win Amazing prizes!</p>
    </div> <!-- END id="one-less" -->

    <?php
    //Выбираем все призы
    $sql_prizes = "SELECT 
					  `col_title`,
					  `col_type`,
					  `col_company_id`
		         FROM 
		              `tbl_prizes` ";
    $query_prizes = mysqli_query($link, $sql_prizes);

    while ($row_prizes = mysqli_fetch_assoc($query_prizes)) {
        $array_prizes[] = $row_prizes;
    } //END WHILE


    /*  CONTESTS
    ------------------------------------------------------- */
    $sql_brands = "SELECT 
                        u.`col_id`,
						u.`col_company_name`,
						u.`col_avatar`
		           FROM 
		                `tbl_users` u
			  LEFT JOIN 
					    `tbl_contests` c ON c.`col_company_id` = u.`col_id`
			      WHERE 
		                u.`col_company_name` <> ''
			   GROUP BY 
					    u.`col_id`
			   ORDER BY 
		                c.`col_company_id` DESC ";
    $query_brands = mysqli_query($link, $sql_brands);

    $num_rows = mysqli_num_rows($query_brands);

    $contests = '<ul id="js-last-releases" class="all-contests brands">';

    while ($row_brands = mysqli_fetch_assoc($query_brands)) {

        $contests .= '<li class="item">';
        $contests .= '<div class="container">';
        $contests .= '<div class="company-photo">';
        $contests .= '<div class="company-logo">';
        $contests .= '<img src="/img/companies/logo/' . $row_brands['col_avatar'] . '" alt="' . $row_brands['col_company_name'] . '">';
        $contests .= '</div>';
        $contests .= '<div class="company-name">' . $row_brands['col_company_name'] . '</div>';
        $contests .= '</div>';


        $ii = 1;

        for ($i = 0; $i < count($array_prizes); $i++) {

            if ($ii == 4) break;

            if ($array_prizes[$i]['col_company_id'] == $row_brands['col_id']) {

                if ($ii == 1) {
                    $contests .= '<div class="prizes-info">';
                    $contests .= '<h5>Prize of this company</h5>';
                    $contests .= '<ul class="wrap-prizes">';
                }

                $contests .= '<li class="prize"><span class="num">' . $ii . '.</span><span class="text">' . $array_prizes[$i]['col_title'] . '</span></li>';

                $ii++;
            }
        }


        if ($ii > 1) {
            $contests .= '</ul>';
            $contests .= '</div>';
        }

        $contests .= '<div class="rating-info">';
        $contests .= '<ul class="wrap-rating">';

        $sql_users = "SELECT 
                                                 u.`col_id`,
							                     u.`col_username`,
							                     u.`col_lastname`,
								                 u.`col_avatar`,
												 COUNT(lk.`col_id`) AS `col_count`
		                                    FROM 
		                                         `tbl_likes` lk
	                                        JOIN 
	                                             `tbl_users` u ON u.`col_id` = lk.`col_author_id`
					                       WHERE 
										         lk.`col_company_id` = " . $row_brands['col_id'] . "
				                        GROUP BY 
				                                 lk.`col_author_id`
	                                    ORDER BY 
		                                         `col_count` DESC 
				                           LIMIT 3";
        $query_users = mysqli_query($link, $sql_users);

        $num_user = 1;
        while ($row_users = mysqli_fetch_assoc($query_users)) {
            $username = $row_users['col_username'] . ' ' . $row_users['col_lastname'];

            $contests .= '<li class="rating">';
            $contests .= '<a href="/profile.php?id=' . $row_users['col_id'] . '" class="user-photo">';
            $contests .= '<img src="/img/users/' . $row_users['col_avatar'] . '"><span class="mark">' . ordinal_suffix($num_user) . '</span>';
            $contests .= '</a>';
            $contests .= '<a href="/profile.php?id=' . $row_users['col_id'] . '" class="username">' . $username . '</a>';
            $contests .= '</li>';

            $num_user++;
        }

        $contests .= '</ul>';
        $contests .= '<div class="action">';
        $contests .= '<a href="/company_profile.php?id=' . $row_brands['col_id'] . '" class="button button-small-black">see details</a>';
        $contests .= ' </div>';
        $contests .= '</div>';

        $contests .= '</div>';
        $contests .= '</li>';
    } //END WHILE
    //$contests .= '<div class="test">'. $i .'</div>';
    $contests .= '</ul>'; //END id="all-contests"


    echo $contests;

    /*  END CONTESTS
    ------------------------------------------------------- */
    ?>
</section> <!-- END id="content" -->
<?php include_once './include/footer.php'; ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

<script src="/js/jquery.jcrop.js"></script>
<script src="/js/jquery.ui.widget.js"></script>
<script src="/js/jquery.iframe-transport.js"></script>
<script src="/js/jquery.fileupload.js"></script>

<script src="/js/jquery.mCustomScrollbar.min.js"></script>
<script src="/js/masonry.pkgd.min.js"></script>

<script src="/js/main.js"></script>
</body>
</html>