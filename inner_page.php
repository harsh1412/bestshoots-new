<?php
$id = $_GET['id'];

if (!preg_match("|^[\d]+$|", $id)) {
	header("Location: error.php");
}

include_once './include/db.php';

$sql = "SELECT
               c.`col_title`,
			   c.`col_about`,
			   c.`col_header_photo`,
			   c.`col_logo`,
			   c.`col_company_id`,
			   DATE_FORMAT(c.`col_date_end`, '%Y/%m/%d') AS `col_date_end`,
			   IF(c.`col_date_end` < NOW(), 1, 0) AS `col_end`,
			   c.`col_flag`,
			   u.`col_company_name`,
			   DATE_FORMAT(u.`col_date`, '%d %M, %Y') AS `col_date`,
			   u.`col_avatar`,
			   u.`col_about` AS `col_company_about`,
			   u.`col_link`
		  FROM
		       `tbl_contests` c
	 LEFT JOIN
	           `tbl_users` u ON u.`col_id` = c.`col_company_id`
		 WHERE
		       c.`col_id` = ". (int)$id ."
	  GROUP BY 
				c.`col_id` ";	
$query = mysqli_query($link, $sql);

$num = mysqli_num_rows($query);
if ($num == 0) {
	header("Location: error.php");
}
$row = mysqli_fetch_assoc($query);

//Выбираем победителей конкурса
if ($row['col_flag'] == 0 && $row['col_end'] == 1) {
	include_once './include/wins.php';
}


$style = "background-image: url('/img/contests/header_photo/". $row['col_header_photo'] ."')";
//***FOLLOW***
$sql_s = "SELECT `col_id` FROM `tbl_subscriptions` WHERE `company_id` = ". (int)$row['col_company_id'] ." AND `user_id` = ". (int)$_SESSION["user_id"];
$query_s = mysqli_query($link, $sql_s);
	
if (mysqli_num_rows($query_s) > 0) {
	$follow = "unsubscribe";
} else {
	$follow = "subscribe";
}
//***END FOLLOW***

include_once './include/header.php';
?>
        <section id="content">
        	<div style="<?=$style?>" id="one">
            	<p class="text-brand"><?=$row['col_company_name']?></p>
                <h2 class="text-effect"><?=$row['col_title']?></h2>

                <?php
				if($_SESSION["loged"] == "yes") {
					echo '<a href="#" class="button button-green js-write-prt-modal" data-modal-id="#js-photo-modal" data-contest-id="'. $id .'">participate</a>';
				} else {
					echo '<a href="/sign_up.php" class="button button-green">participate</a>';
				}
                ?>
                
            </div> <!-- END id="one" -->
            
            
            <div id="project">
            	<div class="column about-contest">
                	<div class="container"> 
                        <h3 class="column-title">About Contest</h3>
                        <h4 class="title"><?=$row['col_title']?></h4>
                        <div class="company-name">by <a href="/company_profile.php?id=<?=$row['col_company_id']?>" class="link"><?=$row['col_company_name']?></a></div>
                        <div class="text"><?=$row['col_about']?></div>
                        <div class="action">
                        	<a href="#" class="button button-big-black js-write-modal" data-modal-id="#js-about-modal">explore more</a>
                        </div>
                    </div>
            	</div>
            	
                
                <?php
/*  PRIZES
------------------------------------------------------- */
$sql_prizes = "SELECT 
                    `col_title`,
					`col_description`,
					`col_img`,
			        `col_start_winners`,
					`col_end_winners`,
					`col_type`
		       FROM 
		            `tbl_prizes`
		      WHERE 
		            `col_contest_id` = ". (int)$id ."
		   ORDER BY 
		            `col_type`, `col_start_winners`" ;
$query_prizes = mysqli_query($link, $sql_prizes);

$prizes = '<div class="column win-products">';
	$prizes .= '<h3 class="column-title">Win Products & Prizes</h3>';
	$prizes .= '<ul class="wrap-column header">';
		$prizes .= '<li class="rating">Rating</li>';
		$prizes .= '<li class="random">Random</li>';
		$prizes .= '<li class="likes">Likes</li>';
	$prizes .= '</ul>';
	
	$prizes .= '<div class="wrap-column">';	
		
		$i_one = 0;
		$i_two = 0;
		$i_three = 0;
		
		$html_one = '';
		$html_two = '';
		$html_three = '';
	
	while ($row_prizes = mysqli_fetch_assoc($query_prizes)) {
		
		$winners = $row_prizes['col_end_winners'] - $row_prizes['col_start_winners'] + 1;
		$mark_prizes = ordinal_suffix($row_prizes['col_start_winners']) .' - '. ordinal_suffix($row_prizes['col_end_winners']);	
		
		if ($i_one < 3 && $row_prizes['col_type'] == 1) {	
			$html_one .= '<li class="item rating">';
				$html_one .= '<img src="/img/prizes/'. $row_prizes['col_img'] .'" alt="'. $row_prizes['col_title'] .'">';
				$html_one .= '<span class="mark">'. $mark_prizes .'</span>';
			 $html_one .= '</li>';
			
			$i_one ++;
		}
		
		if ($i_two < 3 && $row_prizes['col_type'] == 2) {
			$html_two .= '<li class="item random">';
				$html_two .= '<div>'. $row_prizes['col_title'] .' x '. $winners .'</div>';
				$html_two .= '<div>'. $row_prizes['col_description'] .'</div>';
			$html_two .= '</li>';
			
			$i_two ++;
		}
		
		if ($i_three < 3 && $row_prizes['col_type'] == 3) {
			$html_three .= '<li class="item likes">';
				$html_three .= '<div class="text-effect">'. $row_prizes['col_title'] .' x '. $winners .'</div>';
				$html_three .= '<div>'. $row_prizes['col_description'] .'</div>';
			$html_three .= '</li>';
			
			$i_three ++;
		}
				
	} //END WHILE

		$prizes .= '<ul class="column-item">'. $html_one .'</ul>'; //rating
		$prizes .= '<ul class="column-item">'. $html_two .'</ul>'; //random
		$prizes .= '<ul class="column-item">'. $html_three .'</ul>'; //likes
	$prizes .= '</div>'; //END class="wrap-items"
	$prizes .= '<div class="action">';
		
	if ($_SESSION["loged"] == "yes") {
		$prizes .= '<a href="#" class="button button-green js-write-prt-modal" data-modal-id="#js-photo-modal" data-contest-id="'. $id .'">participate</a>';
	} else {
		$prizes .= '<a href="/sign_up.php" class="button button-green">participate</a>';
	}
	
	$prizes .= '</div>';
$prizes .= '</div>'; //END class="win-products"


	
echo $prizes;
/*  END PRIZES
------------------------------------------------------- */
?>
                
                
                
            	<div class="column brand-info">
                	<div class="container">
                    	<h3 class="column-title">Brand Info</h3>
                        <a href="/company_profile.php?id=<?=$row['col_company_id']?>"><img src="/img/companies/logo/<?=$row['col_avatar']?>"></a>
                        <div class="company-name"><?=$row['col_company_name']?></div>
                        <div class="info">
                        	<div class="description">
                            	<a href="/company_profile.php?id=<?=$row['col_company_id']?>" class="tag">@<?=$row['col_company_name']?></a>
                                <div class="about" id="js-brand-info-about"><?=$row['col_company_about']?></div>
                    			<a href="#" class="read js-write-modal" id="js-write-brand-info-about" data-modal-id="#js-brand-about-modal">Read More</a>
                                <?php
								echo '<div class="website">';
								if (!empty($row['col_link'])) {
									echo '<i class="fa fa-link"></i><a href="'. $row['col_link'] .'" class="link">'. $row['col_link'] .'</a>';
								}
								echo '</div>';
								?>
                            	<div class="date"><i class="fa fa-calendar-o"></i><span>Join <?=$row['col_date']?></span></div>
                            </div>
                            
                            <?php
							if($_SESSION["loged"] == "yes") {
								echo '<a href="#" class="button button-big-black left" id="js-follow" 
									data-company-id="'. $row['col_company_id'] .'"
									data-type="subscribe"
									data-name="'. $row['col_company_name'] .'"
									data-logo="'. $row['col_avatar'] .'">'. $follow .'</a>';
							} else {
								echo '<a href="/sign_up.php" class="button button-big-black left">subscribe</a>';					
							} 
                                
							if (!empty($row['col_link'])) {
								echo '<a href="'. $row['col_link'] .'" class="button right">redeem online</a>';
							}
							?>
                        </div>
                    </div>
            	</div>
            </div> <!-- END id="project" -->
            
            
            
            
<?php
/*  ALL PHOTOS FOR CONTESTS
------------------------------------------------------- */
$sql_photo = "SELECT 
                        p.`col_photo_url`,
						u.`col_id`,
						u.`col_username`,
						u.`col_lastname`,
						COUNT(lk.`col_id`) AS `col_count`
		           FROM 
		                `tbl_photo` p
		      LEFT JOIN 
	                    `tbl_users` u ON u.`col_id` = p.`col_user_id`
			  LEFT JOIN 
						`tbl_likes` lk ON lk.`col_author_id` = p.`col_user_id` AND lk.`col_contest_id` = p.`col_contest_id`
			      WHERE 
		                p.`col_contest_id` = ". (int)$id ."
			   GROUP BY 
				        p.`col_id` 
	           ORDER BY 	  
				      `col_count` DESC, p.`col_id` DESC ";						
$query_photo = mysqli_query($link, $sql_photo);

$photo = '<div id="last-releases" class="inner-page">';
	$photo .= '<h2 id="js-end-contest" data-date-end="'. $row['col_date_end'] .'"></h2>';

if (mysqli_num_rows($query_photo) > 0) {
		
		$i = 0;
		$photo .= '<ul class="wrap-items" id="js-last-releases">';
		
			while ($row_photo = mysqli_fetch_assoc($query_photo)) {
				
				$username = $row_photo['col_username'] .' '. $row_photo['col_lastname'];
	
				$photo .= '<li class="item effect">';
					$photo .= '<div class="container">';
						$photo .= '<div class="number">'. ordinal_suffix($i+1) .'</div>';
						$photo .= '<a href="/profile.php?id='. $row_photo['col_id'] .'">';
							$photo .= '<img src="/img/contests/users_photo/'. $row_photo['col_photo_url'] .'" alt="'. $username .'">';
						$photo .= '</a>';
						
						$photo .= '<div class="info">';
							$photo .= '<div class="contest-title">'. $username .'</div>';
							$photo .= '<div class="contest-likes">'. $row_photo['col_count'] .' Likes</div>';
						$photo .= '</div>';

						$photo .= '<div class="hover-block">';
							$photo .= '<div>Any picture made in our restaurant</div>';
							$photo .= '<div class="count">'. $row_photo['col_count'] .' Likes</div>';
							$photo .= '<div class="action">';
								$photo .= '<i class="fa fa-thumbs-o-up button-like js-like"
									data-author-id="'. $row_photo['col_id'] .'"
									data-contest-id="'. $id .'"
									data-company-id="'. $row['col_company_id'] .'"
									data-end="'. $row['col_end'] .'"></i>';
							$photo .= '</div>';
						$photo .= '</div>';
					
					$photo .= '</div>'; //END class="container"
				$photo .= '</li>';
				
				$i ++;
				
			} //END WHILE
			
				$photo .= '<li class="item last">';
					$photo .= '<a href="/all_contests.php" class="container text">';
						$photo .= '<div>Still not interested?</div>';
						$photo .= '<div>Find out the new contests</div>';
					$photo .= '</a>';
				$photo .= '</li>';
			$photo .= '</ul>';		
	}
	$photo .= '</div>'; //END id="last-releases"
	echo $photo;
/*  ALL PHOTOS FOR CONTESTS
------------------------------------------------------- */
?>
</section> <!-- END id="content" --> 
<?php include_once './include/footer.php'; ?>
    
    
    <div id="js-brand-about-modal" class="fs-box">
        <section>
            <div class="fs-form js-form">
            	<header class="header">
                	<h3>About <?=$row['col_company_name']?></h3>
                    <i class="fa fa-close fs-close js-close" data-modal-id="#js-brand-about-modal"></i>
                </header>
                <div class="about-contest js-scroll">
					<div class="text"><?=$row['col_company_about']?></div>
                </div>
            </div>
        </section>
    </div> <!-- END id="js-brand-about-modal" -->
    
    
    <div id="js-about-modal" class="fs-box">
        <section>
            <div class="fs-form js-form">
            	<header class="header">
                	<h3>About Contest</h3>
                    <i class="fa fa-close fs-close js-close" data-modal-id="#js-about-modal"></i>
                </header>
                <div class="about-contest js-scroll">
                	<h4 class="title"><?=$row['col_title']?></h4>
                        <div class="company-name">by <a href="/company_profile.php?id=<?=$row['col_company_id']?>" class="link"><?=$row['col_company_name']?></a></div>
                        <div class="text"><?=$row['col_about']?></div>              
                </div>
            </div>
        </section>
    </div> <!-- END id="js-about-modal" -->
    
    <div id="js-photo-modal" class="fs-box">
        <section>
            <div class="fs-form fs-form2 js-form">
                <header class="header">
                    <h3>Participate contest</h3>
                    <i class="fa fa-close fs-close js-close" data-modal-id="#js-photo-modal"></i>
                </header>
                <div class="fs-input">
                    <div class="input last">   
                        <form id="upload" method="post" action="/upload.php" enctype="multipart/form-data">
                            
                            <i class="fa fa-cloud-upload add-file js-file"
                                    data-min-width="758"
                                    data-min-height="692"
                                    data-max-size="4"
                                    data-dir="contests/users_photo"
                                    data-contest-title="<?=$row['col_title']?>"
                                    data-contest-logo="<?=$row['col_logo']?>"
                                    data-contest-id="<?=$id?>"></i>
                                    
                            <input type="file" name="files" id="photoupload" class="js-form" />
                            <div class="file-text">
                                <h6>Add photo</h6>
                                <p>Recommended Image Size: 758 x 692</p>
                                <p>Maximum file size: 4 MB</p>
                            </div>
                        </form>
                    </div>
                </div> <!-- END class="fs-input" -->
            </div>
        </section>
    </div> <!-- END id="js-photo-modal" -->
    
    <div id="js-crop-modal" class="fs-box">
        <section>
            <div class="fs-form js-form" id="crop-form">
            	<header class="header">
                	<h3>Add image</h3>
                    <i class="fa fa-close fs-close js-close" data-modal-id="#js-crop-modal"></i>
                </header>
                <div class="content">
                	<div class="wrap-photo">
                    	<img id="photo">
                    </div>
                    <input type="hidden" name="x" id="x" />
                    <input type="hidden" name="y" id="y" />
                    <input type="hidden" name="w" id="w" />
                    <input type="hidden" name="h" id="h" /> 
                    <div class="action">
                    	<a href="#" class="button button-small-black" id="upload-send">add</a>
                    </div>
                </div>
            </div>
        </section>
    </div> <!-- END id="js-crop-modal" -->
    
    
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    
    <script src="/js/jquery.jcrop.js"></script>
	<script src="/js/jquery.ui.widget.js"></script>
	<script src="/js/jquery.iframe-transport.js"></script>
	<script src="/js/jquery.fileupload.js"></script>
    
    <script src="/js/jquery.mCustomScrollbar.min.js"></script>
    <script src="/js/masonry.pkgd.min.js"></script>
    
    <script src="/js/jquery.countdown.min.js"></script>

    <script src="/js/main.js"></script>
</body>
</html>