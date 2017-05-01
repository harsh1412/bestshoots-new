<?php
include_once './include/db.php';
include_once './include/header.php';
include_once './include/commonFunctions.php';
include_once './include/repository/usersDao.php';
?>
<section id="content">
    <div id="one" class="homepage">
        <p class="text-effect">Your photo contests.</p>
        <h2 class="text-effect">Win favourite products and prizes!</h2>
        <a href="/all_contests.php" class="button">Browse all</a>
    </div> <!-- END id="one" -->
    <div id="project">
        <?php
        /*  CONTESTS
        ------------------------------------------------------- */
        $sql_contests = "SELECT 
                        c.`col_id`,
						c.`col_title`,
						c.`col_logo`,
						c.`col_company_id`,
						u.`col_company_name`
		           FROM 
		                `tbl_contests` c
	          LEFT JOIN 
	                    `tbl_users` u ON u.`col_id` = c.`col_company_id` 
			      WHERE 
		                u.`col_company_name` <> ''
			   GROUP BY 
				        c.`col_id` 
	           ORDER BY 
		                c.`col_id` DESC 
				  LIMIT 5";
        $query_contests = mysqli_query($link, $sql_contests);

        $contests = '<div class="column contests">';
        $contests .= '<div class="container">';
        $contests .= '<h3 class="column-title">Contests</h3>';
        $contests .= '<ul class="list">';

        while ($row_contests = mysqli_fetch_assoc($query_contests)) {

            $contests .= '<li class="item">';
            $contests .= '<a href="/inner_page.php?id=' . $row_contests['col_id'] . '" class="company-logo">';
            $logoUrl = getContestLogoUrl($row_contests['col_logo']);
            $contests .= '<img src="' . $logoUrl . '" alt="' . $row_contests['col_title'] . '">';
            $contests .= '</a>';
            $contests .= '<div class="content">';
            $contests .= '<a href="/inner_page.php?id=' . $row_contests['col_id'] . '" class="title">' . $row_contests['col_title'] . '</a>';
            $contests .= '<div class="company-name">by ';
            $contests .= '<a href="/company_profile.php?id=' . $row_contests['col_company_id'] . '" class="link">' . $row_contests['col_company_name'] . '</a>';
            $contests .= '</div>';
            $contests .= '</div>';
            $contests .= '</li>';

        } //END WHILE
        $contests .= '</ul>';
        $contests .= '<a href="/all_contests.php" class="button button-big-black">view all</a>';
        $contests .= '</div>';
        $contests .= '</div>'; //END class="contests"

        echo $contests;
        /*  END CONTESTS
        ------------------------------------------------------- */

        /*  PRIZES
        ------------------------------------------------------- */
        $sql_prizes = "SELECT 
p.`col_title`,
p.`col_img`,
p.`col_company_id`,
p.`col_contest_id`,
c.`col_title` AS `col_contest_title`,
u.`col_company_name`
FROM `tbl_prizes` p
LEFT JOIN `tbl_contests` c ON c.`col_id` = p.`col_contest_id`
LEFT JOIN `tbl_users` u ON u.`col_id` = p.`col_company_id`
GROUP BY p.`col_contest_id`
ORDER BY p.`col_id` DESC 
LIMIT 10 ";
        $query_prizes = mysqli_query($link, $sql_prizes);

        $html = '<div class="column products-prizes">';
        $html .= '<h3 class="column-title">Products & Prizes</h3>';
        $html .= '<div id="block-prize-contest">';
        $html .= '<div class="info" id="js-slider-profile">';
        $html .= '<div class="swSlider">';

        $prizes = '<div id="block-prizes">';
        $prizes .= '<div class="prizes home">';

        $i = 0;
        $html_c = '';

        while ($row_prizes = mysqli_fetch_assoc($query_prizes)) {

            if ($i < 3) {
                $prizes .= '<a class="prize" href="/company_profile.php?id=' . $row_prizes['col_company_id'] . '">';
                $prizes .= '<img src="/img/prizes/' . $row_prizes['col_img'] . '" alt="' . $row_prizes['col_title'] . '">';
                $prizes .= '<div class="prize-name">' . $row_prizes['col_title'] . '</div>';
                $prizes .= '<div class="contest-name">' . $row_prizes['col_company_name'] . '</div>';
                $prizes .= '</a>';
            } else {
                if ($i == 3){
                    $contest_id = $row_prizes['col_contest_id'];
                }

                $html_c .= '<div class="swPage" data-contest-id="' . $row_prizes['col_contest_id'] . '">';
                $html_c .= '<a class="left-block" href="/company_profile.php?id=' . $row_prizes['col_company_id'] . '">';
                $html_c .= '<img src="/img/prizes/' . $row_prizes['col_img'] . '" alt="' . $row_prizes['col_title'] . '">';
                $html_c .= '<div class="title">' . $row_prizes['col_title'] . '</div>';
                $html_c .= '<div class="description">' . $row_prizes['col_company_name'] . '</div>';
                $html_c .= '</a>';
                $html_c .= '<div class="right-block">';
                $html_c .= '<div>Contest of the</div>';
                $html_c .= '<div>“' . $row_prizes['col_contest_title'] . '”</div>';
                $html_c .= '</div>';
                $html_c .= '</div>'; //END class="swPage"
            }

            $i++;

        } //END WHILE

        $prizes .= '</div>';
        $prizes .= '</div>'; //END id="block-prizes"

        $html .= $html_c;
        $html .= '</div>'; //END class="swSlider"
        $html .= '</div>'; //END class="info"
        $html .= '<i class="fa fa-chevron-left arrow arrow-left" id="js-arrow-left" data-value="0"></i>';
        $html .= '<i class="fa fa-chevron-right arrow arrow-right" id="js-arrow-right" data-value="1"></i>';
        $html .= '</div>'; //END id="block-prize-contest"

        if ($_SESSION["loged"] == "yes") {
            $html .= '<a href="/inner_page.php?id=' . $contest_id . '" id="js-prt" class="button button-green">participate</a>';
        } else {
            $html .= '<a href="/sign_up.php" class="button button-green">participate</a>';
        }

        $html .= $prizes;
        $html .= '</div>'; //END class="products-prizes"

        echo $html;
        /*  END PRIZES
        ------------------------------------------------------- */

        /*  BRANDS
        ------------------------------------------------------- */

        $brands = '<div class="column brands">';
        $brands .= '<div class="container">';
        $brands .= '<h3 class="column-title">Brands</h3>';
        $brands .= '<ul class="list">';

        $usersDao = new usersDao($link);
        $list = $usersDao->getLatestFour();
        foreach ($list as $row_brands) {

            $brands .= '<li class="item">';
            $brands .= '<a href="/company_profile.php?id=' . $row_brands['col_id'] . '">';
            $brands .= '<img src="/img/companies/logo/' . $row_brands['col_avatar'] . '" alt="' . $row_brands['col_company_name'] . '" class="company-logo">';
            $brands .= '<div class="company-name">' . $row_brands['col_company_name'] . '</div>';
            $brands .= '</a>';
            $brands .= '</li>';

        } //END WHILE
        $brands .= '</ul>';

        $brands .= '<div class="action">';
        $brands .= '<a href="/all_brands.php" class="button button-big-black">view all</a>';
        $brands .= '</div>';
        $brands .= '</div>';
        $brands .= '</div>'; //END class="brands"

        echo $brands;
        /*  END BRANDS
        ------------------------------------------------------- */
        ?>
    </div> <!-- END id="project" -->
    <div id="three">
        <h2 class="text-effect">How our System Works?</h2>
        <p>Lorem Ipsum has been the industry's standard dummy text eversince the 1500s, when an unknown printer took a
            galley of type and scrambled it to make a type specimen book.</p>
        <ul class="list">
            <?php

            //Выбираем ID последнего законченного конкурса
            $sql_cnt = "SELECT `col_id`, `col_title` FROM `tbl_contests` WHERE `col_flag` = 1 ORDER BY `col_date_end` DESC LIMIT 1 ";
            $query_cnt = mysqli_query($link, $sql_cnt);
            $row_cnt = mysqli_fetch_assoc($query_cnt);


            $sql_wins = "SELECT 
                        w.`col_rating`,
						w.`col_type`,
						ph.`col_photo_url`,
						u.`col_id` AS `col_user_id`,
						u.`col_username`,
						u.`col_avatar`,
						lk.`col_date`,
						(SELECT COUNT(`col_id`) FROM `tbl_likes` WHERE `col_author_id` = w.`col_user_id` AND `col_contest_id` = " . (int)$row_cnt['col_id'] . ") AS `col_count`
		           FROM 
		                `tbl_wins` w
		      LEFT JOIN 
	                    `tbl_photo` ph ON ph.`col_user_id` = w.`col_user_id`
			 LEFT JOIN 
	                    `tbl_likes` lk ON lk.`col_user_id` = w.`col_user_id` AND lk.`col_contest_id` = " . (int)$row_cnt['col_id'] . "
			  LEFT JOIN 
	                    `tbl_users` u ON u.`col_id` = w.`col_user_id` 
			      WHERE 
		                w.`col_contest_id` = " . (int)$row_cnt['col_id'] . "
			   GROUP BY 
				        w.`col_id` 
	           ORDER BY 
		                w.`col_type`, w.`col_rating` ";
            $query_wins = mysqli_query($link, $sql_wins);


            $i = 1;
            $html = '';
            $type = 1;

            while ($row_wins = mysqli_fetch_assoc($query_wins)) {

                if ($type != $row_wins['col_type']) {
                    $html .= '</ul>';
                    $html .= '<a href="/inner_page.php?id=' . $row_cnt['col_id'] . '" class="button button-small-black">view all</a>';
                    $html .= '</li>'; //END class="rating"

                    $i = 1;
                }

                if ($i == 1 && $row_wins['col_type'] == 1) {
                    $html .= '<li class="item rating">';
                    $html .= '<header></header>';
                    $html .= '<h3 class="title">Rating</h3>';
                    $html .= '<p class="description">Lorem Ipsum has been the industry\'s standard dummy text eversince the 1500s, when an unknown printer took a galley.</p>';
                    $html .= '<ul class="wrap-contests">';
                }

                if ($i == 1 && $row_wins['col_type'] == 2) {
                    $html .= '<li class="item random">';
                    $html .= '<header></header>';
                    $html .= '<h3 class="title">Random</h3>';
                    $html .= '<p class="description">Lorem Ipsum has been the industry\'s standard dummy text eversince the 1500s, when an unknown printer took a galley.</p>';
                    $html .= '<ul class="wrap-contests">';
                }

                if ($i == 1 && $row_wins['col_type'] == 3) {
                    $html .= '<li class="item likes">';
                    $html .= '<header></header>';
                    $html .= '<h3 class="title">Likes</h3>';
                    $html .= '<p class="description">Lorem Ipsum has been the industry\'s standard dummy text eversince the 1500s, when an unknown printer took a galley.</p>';
                    $html .= '<ul class="wrap-contests">';
                }

                if ($i < 6) {
                    $html .= '<li class="contest-item">';
                    $html .= '<a href="/profile.php?id=' . $row_wins['col_user_id'] . '">';
                    $html .= '<img src="/img/contests/users_photo/' . $row_wins['col_photo_url'] . '" alt="' . $row_cnt['col_title'] . '">';
                    if ($row_wins['col_type'] == 1) {
                        $html .= '<div class="number">' . $row_wins['col_rating'] . '</div>';
                    }
                    if ($row_wins['col_type'] == 3) {
                        $html .= '<time class="date timeago" datetime="' . $row_wins['col_date'] . '"></time>';
                    }
                    $html .= '<div class="info">';
                    $html .= '<div class="contest-title">' . $row_cnt['col_title'] . '</div>';

                    $html .= '<div class="contest-likes">';
                    if ($row_wins['col_type'] != 3) {
                        $html .= $row_wins['col_count'] . ' Likes';
                    } else {
                        $html .= '<img src="/img/users/' . $row_wins['col_avatar'] . '" alt="' . $row_wins['col_username'] . '" class="avatar">';
                        $html .= '<span class="username">' . $row_wins['col_username'] . '</span>';
                        $html .= '<span>liked this Photo</span>';
                    }
                    $html .= '</div>';

                    $html .= '</div>';
                    $html .= '</a>';
                    $html .= '</li>'; //END class="contest-item"
                }


                $i++;
                $type = $row_wins['col_type'];
            } //END WHILE


            $html .= '</ul>';
            $html .= '<a href="/inner_page.php?id=' . $row_cnt['col_id'] . '" class="button button-small-black">view all</a>';
            $html .= '</li>'; //END class="likes"

            echo $html;
            ?>

        </ul>
    </div> <!-- END id="three" -->
</section> <!-- END id="content" -->
<?php include_once './include/footer.php'; ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

<script src="/js/jquery.jcrop.js"></script>
<script src="/js/jquery.ui.widget.js"></script>
<script src="/js/jquery.iframe-transport.js"></script>
<script src="/js/jquery.fileupload.js"></script>

<script src="/js/jquery.mCustomScrollbar.min.js"></script>
<script src="/js/masonry.pkgd.min.js"></script>
<script src="/js/jquery.timeago.js"></script>

<script src="/js/main.js"></script>
</body>
</html>