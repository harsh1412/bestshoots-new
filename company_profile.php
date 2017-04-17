<?php
$id = $_GET['id'];

if (!preg_match("|^[\d]+$|", $id)) {
    header("Location: error.php");
}

include_once './include/db.php';
include_once './include/commonFunctions.php';

$sql = "SELECT
               `col_company_name`,
			   DATE_FORMAT(`col_date`, '%d %M, %Y') AS `col_date`,
			   `col_about`,
			   `col_header_photo`,
			   `col_avatar`,
			   `col_link`,
			   `col_location`
		  FROM
		       `tbl_users`
		 WHERE
		       `col_id` = " . (int)$id . " AND `col_company_name` <> '' ";
$query = mysqli_query($link, $sql);

$num = mysqli_num_rows($query);
if ($num == 0) {
    header("Location: error.php");
}
$row = mysqli_fetch_assoc($query);

if (!empty($row['col_header_photo'])) {
    $style = "background-image: url('/img/companies/header_photo/" . $row['col_header_photo'] . "')";
}
//***FOLLOW***
$sql_s = "SELECT `col_id` FROM `tbl_subscriptions` WHERE `company_id` = " . (int)$id . " AND `user_id` = " . (int)$_SESSION["user_id"];
$query_s = mysqli_query($link, $sql_s);

if (mysqli_num_rows($query_s) > 0) {
    $follow = "unfollow";
} else {
    $follow = "follow";
}
//***END FOLLOW***

include_once './include/header.php';
?>
<section id="content">
    <div id="one" style="<?= $style ?>" class="profile">
        <h1 class="text-effect"><?= $row['col_company_name'] ?></h1>
        <?php
        if ($_SESSION["user_id"] == $id) {
            $html_up = '<a href="#" class="change-photo js-file" data-min-width="1920" data-min-height="535" data-max-size="4" data-dir="companies/header_photo">';
            $html_up .= '<i class="fa fa-camera"></i>';
            $html_up .= '<span>Change your header photo</span>';
            $html_up .= '</a>';

            echo $html_up;
        }
        ?>
    </div> <!-- END id="one" -->
    <div id="project">


        <?php
        /*  CONTEST
        ------------------------------------------------------- */
        $sql_contests = "SELECT 
                        `col_id`,
						`col_title`,
						`col_logo`
		           FROM 
		                `tbl_contests`
			      WHERE 
		                `col_company_id` = " . (int)$id . "
	           ORDER BY 
		                `col_id` DESC 
				  LIMIT 4 ";
        $query_contests = mysqli_query($link, $sql_contests);

        $contests = '<div class="column" id="profile-contests">';


        if (mysqli_num_rows($query_contests) > 0) {

            $contests .= '<div class="container">';
            $contests .= '<h3 class="column-title">Contest</h3>';
            $contests .= '<ul>';

            while ($row_contests = mysqli_fetch_assoc($query_contests)) {

                $contests .= '<li class="item">';
                $contests .= '<a href="/inner_page.php?id=' . $row_contests['col_id'] . '" class="company-logo">';
                $logoUrl = getContestLogoUrl($row_contests['col_logo']);
                $contests .= '<img src="' . $logoUrl . '" alt="' . $row_contests['col_title'] . '">';
                //<span class="mark">1st</span>
                $contests .= '</a>';
                $contests .= '<div class="title">' . $row_contests['col_title'] . '</div>';
                $contests .= '<a href="/company_profile.php?id=' . $id . '" class="company-name">' . $row['col_company_name'] . '</a>';
                $contests .= '</li>';

            } //END WHILE
            $contests .= '</ul>';
            $contests .= '<div class="action">';
            $contests .= '<a href="#" class="button button-big-black js-write-modal" data-modal-id="#js-contests-modal">view all</a>';
            $contests .= '</div>';

            $contests .= '</div>'; //END class="container"

        } else {

            $contests .= '<div class="empty-container">';
            $contests .= '<div>';
            if ($_SESSION["user_id"] == $id) {
                $contests .= '<span>Your Contest list is empty</span>';
                $contests .= '<span class="last-empty"><a href="/add.php" class="link">Let\'s create one Contest</a></span>';
            } else {
                $contests .= 'Contest list is empty';
            }
            $contests .= '</div>';
            $contests .= '</div>'; //END class="empty-container"
        }


        $contests .= '</div>'; //END id="profile-contests"

        echo $contests;
        /*  END CONTEST
        ------------------------------------------------------- */
        ?>
        <div class="column company-about">

            <?php
            $class_logo = 'up';
            $html_img = '';

            if (!empty($row['col_avatar'])) {
                $class_logo = 'up hidden';
                $html_img = '<img src="/img/companies/logo/' . $row['col_avatar'] . '">';
            }

            if ($_SESSION["user_id"] == $id) {
                $html = '<form id="upload" method="post" action="/upload.php" enctype="multipart/form-data">';
                $html .= '<div class="company-logo js-file" id="js-company-logo" data-min-width="132" data-min-height="132" data-max-size="4" data-dir="companies/logo">';
                $html .= $html_img;
                $html .= '<div class="' . $class_logo . '">';
                $html .= '<i class="fa fa-upload"></i>';
                $html .= '<span>Upload Avatar</span>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<input type="file" name="files" id="photoupload" class="js-form" />';
                $html .= '</form>';

                $html .= '<a href="#" class="left-btn js-write-edit-modal" data-modal-id="#js-edit-modal">edit profile</a>';
            } else {
                $html = '<div class="company-logo">';
                $html .= $html_img;
                $html .= '</div>';

                if ($_SESSION["loged"] == "yes") {

                    $html .= '<a href="#" class="left-btn js-send-message" data-to-id="' . $id . '">message</a>';

                    $html .= '<a href="#" class="follow" id="js-follow" 
								data-company-id="' . $id . '"
								data-type="follow"
								data-name="' . $row['col_company_name'] . '"
								data-logo="' . $row['col_avatar'] . '">' . $follow . '</a>';

                } else {
                    $html .= '<a href="/sign_up.php" class="left-btn">message</a>';
                    $html .= '<a href="/sign_up.php" class="follow">follow</a>';
                }
            }
            echo $html;
            ?>


            <h3 class="column-title">About <?= $row['col_company_name'] ?></h3>
            <div class="text" id="js-profile-about"><?= $row['col_about'] ?></div>
            <a href="#" class="read" id="js-write-about">Read More</a>
            <ul class="list">
                <?php
                $class_link = 'item';
                if (empty($row['col_link'])) {
                    $class_link = 'item hidden';
                }
                ?>
                <li class="<?= $class_link ?>" id="js-profile-link"><i class="fa fa-link"></i><a
                            href="<?= $row['col_link'] ?>" class="link"><?= $row['col_link'] ?></a></li>
                <li class="item"><i class="fa fa-calendar-o"></i><span>Join <?= $row['col_date'] ?></span></li>
                <li class="item"><i class="fa fa-map-marker"></i><span><?= $row['col_location'] ?></span></li>
            </ul>


            <?php
            /*  PRIZES
            ------------------------------------------------------- */
            $sql_prizes = "SELECT 
                    p.`col_title`,
					p.`col_img`,
			        p.`col_contest_id`,
					c.`col_title` AS `col_contest_title`
		       FROM 
		            `tbl_prizes` p
		  LEFT JOIN 
	                `tbl_contests` c ON c.`col_id` = p.`col_contest_id`
		      WHERE 
		            p.`col_company_id` = " . (int)$id . "
		   GROUP BY 
				    p.`col_id`
		   ORDER BY 
		            RAND() 
			  LIMIT 21";
            $query_prizes = mysqli_query($link, $sql_prizes);

            $num_prizes = mysqli_num_rows($query_prizes);
            $prizes = '<div id="block-prizes">';

            if ($num_prizes > 0) {

                $class_prizes = "prizes";
                if ($num_prizes < 4) $class_prizes = "prizes no_slider";

                $prizes .= '<h4 class="prizes-title">Prizes</h4>';
                $prizes .= '<div class="' . $class_prizes . '" id="js-slider-profile">';
                $prizes .= '<div class="swSlider">';
                $prizes .= '<div class="swPage">';

                $i = 0;

                while ($row_prizes = mysqli_fetch_assoc($query_prizes)) {
                    if ($i == 3) {
                        $prizes .= '</div><div class="swPage">';
                        $i = 0;
                    }
                    $prizes .= '<div class="prize">';
                    $prizes .= '<img src="/img/prizes/' . $row_prizes['col_img'] . '" alt="' . $row_prizes['col_title'] . '">';
                    $prizes .= '<div class="prize-name">' . $row_prizes['col_title'] . '</div>';
                    $prizes .= '<div class="contest-name">in <a href="/inner_page.php?id=' . $row_prizes['col_contest_id'] . '">' . $row_prizes['col_contest_title'] . '</a></div>';
                    $prizes .= '</div>';

                    $i++;

                } //END WHILE
                $prizes .= '</div>'; //END LAST class="swPage"
                $prizes .= '</div>'; //END class="swSlider"


                $prizes .= '</div>';
                if ($num_prizes > 3) {
                    $prizes .= '<i class="fa fa-chevron-left arrow arrow-left" id="js-arrow-left" data-value="0"></i>';
                    $prizes .= '<i class="fa fa-chevron-right arrow arrow-right" id="js-arrow-right" data-value="1"></i>';
                }

            } else {
                $prizes .= '<h4 class="prizes-title empty">You don\'t have any products</h4>';
                $prizes .= '<div class="prizes empty">Empty</div>';
            }
            $prizes .= '</div>'; //END id="block-prizes"

            echo $prizes;
            /*  END PRIZES
            ------------------------------------------------------- */
            ?>


        </div>
        <?php
        $feed_url = "/img/companies/logo/";
        include_once './include/news_feed.php';
        ?>
    </div> <!-- END id="project" -->


    <?php
    /*  LAST RELEASES
    ------------------------------------------------------- */
    $sql_photo = "SELECT 
                     `col_photo_url`,
					 `col_date`,
					 `col_title`,
					 `col_description`
		        FROM 
		             `tbl_company_photo`
			   WHERE 
		             `col_company_id` = " . (int)$id . " 
	        ORDER BY 	  
				     `col_id` DESC 
			   LIMIT 12";
    $query_photo = mysqli_query($link, $sql_photo);

    if (mysqli_num_rows($query_photo) > 0 || $_SESSION["user_id"] == $id) {
        $photo = '<div id="last-releases">';
        $photo .= '<h2>Last Releases</h2>';
        $photo .= '<ul class="wrap-items" id="js-last-releases">';

        while ($row_photo = mysqli_fetch_assoc($query_photo)) {

            $photo .= '<li class="item effect">';
            $photo .= '<div class="container">';
            $photo .= '<img src="/img/companies/releases/' . $row_photo['col_photo_url'] . '" alt="' . $row_photo['col_title'] . '">';
            $photo .= '<div class="info">';
            $photo .= '<div class="contest-title">' . $row_photo['col_title'] . '</div>';
            $photo .= '<time class="date timeago" datetime="' . $row_photo['col_date'] . '"></time>';
            $photo .= '</div>';

            $photo .= '<div class="hover-block">';
            $photo .= '<div>' . $row_photo['col_title'] . '</div>';
            $photo .= '<div class="description">' . $row_photo['col_description'] . '</div>';
            $photo .= '</div>';

            $photo .= '</div>'; //END class="container"
            $photo .= '</li>';

        } //END WHILE

        if ($_SESSION["user_id"] == $id) {
            $photo .= '<li class="item last">';
            $photo .= '<a href="#" id="js-add-releases" class="container text">';
            $photo .= '<div>What news of the company?</div><div>Add new releases</div>';
            $photo .= '</a>';
            $photo .= '</li>';
        }

        $photo .= '</ul>';
        $photo .= '</div>'; //END id="last-releases"
    } else {
        $photo = '<div id="last-releases-inner-empty" class="inner-page"></div>';
    }

    echo $photo;
    /*  END LAST RELEASES
    ------------------------------------------------------- */


    /*  SUBSCRIPTIONS
    ------------------------------------------------------- */
    $sql_sub = "SELECT 
                   u.`col_id`,
				   u.`col_company_name`,
			       u.`col_avatar`,
				   COUNT(s.`col_id`) AS `col_count`
		      FROM 
		           `tbl_users` u
		 LEFT JOIN
		           `tbl_subscriptions` s ON s.`company_id` = u.`col_id`
		     WHERE 
		           u.`col_company_name` <> ''
		  GROUP BY 
		           u.`col_id`
		  ORDER BY 
		           u.`col_id` DESC
		     LIMIT 6 ";
    $query_sub = mysqli_query($link, $sql_sub);

    $num_sub = mysqli_num_rows($query_sub);

    if ($num_sub > 0) {

        $wrap_sub = "wrap-items";

        if ($num_sub == 6) $wrap_sub = "wrap-items six";

        $sub = '<div id="subscriptions">';
        $sub .= '<h2>Subscriptions</h2>';
        $sub .= '<ul class="' . $wrap_sub . '">';

        while ($row_sub = mysqli_fetch_assoc($query_sub)) {

            $sub .= '<li class="item">';
            $sub .= '<a href="/company_profile.php?id=' . $row_sub['col_id'] . '">';
            $sub .= '<img src="/img/companies/logo/' . $row_sub['col_avatar'] . '">';
            $sub .= '<div class="company-name">' . $row_sub['col_company_name'] . '</div>';
            $sub .= '<div class="followers">' . $row_sub['col_count'] . '</div>';
            $sub .= '</a>';
            $sub .= '</li>';

        } //END WHILE

        $sub .= '</ul>';
        $sub .= '</div>'; //END id="subscriptions"
    } else {
        $sub = '<div id="subscriptions-empty">You have no subscriptions</div>';
    }

    echo $sub;
    /*  END SUBSCRIPTIONS
    ------------------------------------------------------- */
    ?>
</section> <!-- END id="content" -->
<?php include_once './include/footer.php'; ?>

<div id="js-dialog-modal" class="fs-box">
    <section>
        <div class="fs-form js-form" id="dialog-form">
            <header class="header">
                <h3>Dialog with <a href="/company_profile.php?id=<?= $id ?>"
                                   class="link"><?= $row['col_company_name'] ?></a></h3>
                <i class="fa fa-close fs-close js-close" data-modal-id="#js-dialog-modal"></i>
            </header>
            <div class="container js-scroll">
                <ul id="dialog-list"></ul>
            </div> <!-- END class="about-contest" -->
            <div class="new-message" id="newMessage">
                <div class="content" contenteditable="true">Message...</div>
                <i class="fa fa-paper-plane" id="addMessage" data-to-id="<?= $id ?>"></i>
            </div> <!-- END id="newMessage" -->
        </div>
    </section>
</div> <!-- id="js-dialog-modal" -->


<div id="js-about-modal" class="fs-box">
    <section>
        <div class="fs-form js-form">
            <header class="header">
                <h3>About <?= $row['col_company_name'] ?></h3>
                <i class="fa fa-close fs-close js-close" data-modal-id="#js-about-modal"></i>
            </header>
            <div class="about-contest js-scroll">
                <div class="text js-text"></div>
            </div>
        </div>
    </section>
</div> <!-- id="js-about-modal" -->


<?php
/*  CONTESTS MODAL
------------------------------------------------------- */
$sql_contests = "SELECT 
                        c.`col_id`,
						c.`col_title`,
						c.`col_logo`,
						p.`col_title` AS `col_prize_title`
		           FROM 
		                `tbl_contests` c
	               JOIN 
	                    `tbl_prizes` p ON p.`col_contest_id` = c.`col_id`
				  WHERE 
		                c.`col_company_id` = " . (int)$id . "
	           ORDER BY 
		                c.`col_id` DESC, p.`col_id` ";
$query_contests = mysqli_query($link, $sql_contests);

include_once './include/modal_contests.php';
/*  END CONTESTS MODAL
------------------------------------------------------- */
?>

<div id="js-feed-modal" class="fs-box">
    <section>
        <div id="feed-form" class="fs-form fs-form2 js-form">
            <header class="header">
                <h3>Are you sure you want to delete?</h3>
                <i class="fa fa-close fs-close js-close" data-modal-id="#js-feed-modal"></i>
            </header>
            <div class="action">
                <a href="#" class="button button-small-green" id="js-feed-del">Yes</a>
                <a href="#" class="button button-small-black js-close" data-modal-id="#js-feed-modal">No</a>
            </div> <!-- END class="action" -->
        </div>
    </section>
</div> <!-- END id="js-feed-modal" -->


<div id="js-releases-modal" class="fs-box">
    <section>
        <div id="add-releases-form" class="fs-form fs-form2 js-form">
            <header class="header">
                <h3>Add releases</h3>
                <i class="fa fa-close fs-close js-close" data-modal-id="#js-releases-modal"></i>
            </header>
            <div class="fs-input">
                <div class="input">
                    <label class="input__label">Title</label>
                    <input class="input__field" type="text" id="rls-title">
                </div>

                <div class="input">
                    <label class="input__label">Description</label>
                    <input class="input__field" type="text" id="rls-description">
                </div>

                <div class="input">
                    <i class="fa fa-cloud-upload add-file js-file" id="js-add-photo"
                       data-min-width="758"
                       data-min-height="692"
                       data-max-size="4"
                       data-dir="companies/releases"></i>
                    <div class="file-text">
                        <h6>Add product company</h6>
                        <p>Recommended Image Size: 758 x 692</p>
                        <p>Maximum file size: 4 MB</p>
                    </div>
                </div>

                <div class="action">
                    <a href="#" class="button button-small-black" id="releases-send">add</a>
                </div>
            </div> <!-- END class="fs-input" -->
        </div>
    </section>
</div> <!-- END id="js-releases-modal" -->


<div id="js-edit-modal" class="fs-box">
    <section>
        <div class="fs-form fs-form2 js-form">
            <header class="header">
                <h3>Edit profile</h3>
                <i class="fa fa-close fs-close js-close" data-modal-id="#js-edit-modal"></i>
            </header>
            <div class="fs-input">
                <div class="input">
                    <label class="input__label">About</label>
                    <textarea class="input__field input__text" id="edit-about"></textarea>
                </div>
                <div class="input">
                    <label class="input__label">Link</label>
                    <input class="input__field" type="text" id="edit-link">
                </div>
                <div class="action">
                    <a href="#" class="button button-small-black" id="edit">edit</a>
                </div>
            </div> <!-- END class="fs-input" -->
        </div>
    </section>
</div> <!-- END id="js-edit-modal" -->

<div id="js-crop-modal" class="fs-box">
    <section>
        <div class="fs-form js-form" id="crop-form">
            <header class="header">
                <h3>Photo on your page</h3>
                <i class="fa fa-close fs-close js-close" data-modal-id="#js-crop-modal"></i>
            </header>
            <div class="content">
                <div class="wrap-photo">
                    <img id="photo">
                </div>
                <input type="hidden" name="x" id="x"/>
                <input type="hidden" name="y" id="y"/>
                <input type="hidden" name="w" id="w"/>
                <input type="hidden" name="h" id="h"/>
                <div class="action">
                    <a href="#" class="button button-small-black" id="upload-send">add</a>
                </div>
            </div>
        </div>
    </section>
</div> <!-- id="js-crop-modal" -->


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

<script src="/js/jquery.jcrop.js"></script>
<script src="/js/jquery.ui.widget.js"></script>
<script src="/js/jquery.iframe-transport.js"></script>
<script src="/js/jquery.fileupload.js"></script>

<script src="/js/masonry.pkgd.min.js"></script>
<script src="/js/jquery.timeago.js"></script>
<script src="/js/jquery.mCustomScrollbar.min.js"></script>

<script src="/js/main.js"></script>
</body>
</html>