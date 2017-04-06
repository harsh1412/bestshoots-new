<?php 
include_once './include/db.php';
include_once './include/header.php'; 
?>
        <section id="content">
        	<div id="one-less" class="all">
            	<h1 class="text-effect">Latest photos</h1>
                <p>Win Amazing prizes!</p>
            </div> <!-- END id="one-less" -->
              
<?php
/*  ALL PHOTOS FOR CONTESTS
------------------------------------------------------- */
$sql_photo = "SELECT 
                        p.`col_photo_url`,
						p.`col_contest_id`,
						u.`col_id`,
						u.`col_username`,
						u.`col_lastname`,
						COUNT(lk.`col_id`) AS `col_count`,
						IF(c.`col_date_end` < NOW(), 1, 0) AS `col_end`,
						c.`col_company_id`
		           FROM 
		                `tbl_photo` p
		      LEFT JOIN 
	                    `tbl_users` u ON u.`col_id` = p.`col_user_id`
			  LEFT JOIN 
						`tbl_likes` lk ON lk.`col_author_id` = p.`col_user_id` AND lk.`col_contest_id` = p.`col_contest_id`
			  LEFT JOIN 
						`tbl_contests` c ON c.`col_id` = p.`col_contest_id`
			   GROUP BY 
				        p.`col_id` 
	           ORDER BY 	  
				        p.`col_id` DESC 
			      LIMIT 20";						
$query_photo = mysqli_query($link, $sql_photo);
		
			$photo = '<ul class="last-photos">';
			
			$photo .= '<div id="js-last-releases">';
		
			while ($row_photo = mysqli_fetch_assoc($query_photo)) {
				
				$username = $row_photo['col_username'] .' '. $row_photo['col_lastname'];
	
				$photo .= '<li class="item effect">';
					$photo .= '<div class="container">';
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
									data-contest-id="'. $row_photo['col_contest_id'] .'"
									data-company-id="'. $row_photo['col_company_id'] .'"
									data-end="'. $row_photo['col_end'] .'"></i>';
							$photo .= '</div>';
						$photo .= '</div>';
					$photo .= '</div>'; //END class="container"
				$photo .= '</li>';
				
				
			} //END WHILE
			
			$photo .= '</div>';	
			$photo .= '</ul>';	
	
	echo $photo;
/*  ALL PHOTOS FOR CONTESTS
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