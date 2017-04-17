<?php
include_once './commonFunctions.php';
$query_contests = mysqli_query($link, $sql_contests);

$num_rows = mysqli_num_rows($query_contests);

$contests = '<ul class="all-contests">';

$num_contest = 0; //ID конкурса
$prize = '';
$num_prize = 1;
$i = 0;

while ($row_contests = mysqli_fetch_assoc($query_contests)) {

    $contests .= '<li class="item">';
    $contests .= '<div class="container">';
    $contests .= '<div class="photo">';
    $logoUrl = getContestLogoUrl($row_contests['col_logo']);
    $contests .= '<img src="' . $logoUrl . '" alt="' . $row_contests['col_title'] . '">';
    $contests .= '<div class="contest-info">';
    $contests .= '<div class="contest-title">Any picture made in our restaurant</div>';
    $contests .= '<div class="company-name">Contest by ';
    $contests .= '<a href="/company_profile.php?id=' . $row_contests['col_company_id'] . '" class="link">' . $row_contests['col_company_name'] . '</a>';
    $contests .= '</div>';
    $contests .= '</div>'; //class="contest-info"
    $contests .= '</div>'; //class="photo"

    $ii = 1;

    for ($i = 0; $i < count($array_prizes); $i++) {

        if ($ii == 4) break;

        if ($array_prizes[$i]['col_contest_id'] == $row_contests['col_id']) {

            if ($ii == 1) {
                $contests .= '<div class="prizes-info">';
                $contests .= '<h5>Prize of this contest</h5>';
                $contests .= '<ul class="wrap-prizes">';
            }

            $contests .= '<li class="prize"><span class="num">' . $ii . '.</span><span class="text">' . $array_prizes[$i]['col_title'] . '</span></li>';
            $ii++;
        }
    } //END for

    if ($ii > 1) {
        $contests .= '</ul>';
        $contests .= '</div>';
    }

    $contests .= '<div class="rating-info">';


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
										         lk.`col_contest_id` = " . $row_contests['col_id'] . "
				                        GROUP BY 
				                                 lk.`col_author_id`
	                                    ORDER BY 
		                                         `col_count` DESC 
				                           LIMIT 3";
    $query_users = mysqli_query($link, $sql_users);
    if (mysqli_num_rows($query_users) > 0) {
        $contests .= '<ul class="wrap-rating-contests">';
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
    } else {
        $contests .= '<div class="empty">no people</div>';
    }


    $contests .= '<div class="action">';
    $contests .= '<a href="/inner_page.php?id=' . $row_contests['col_id'] . '" class="button button-small-black">see details</a>';
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