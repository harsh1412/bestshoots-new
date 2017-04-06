<?php
$sql_feed = "SELECT 
                    `col_id`,
					DATE_FORMAT(`col_date`, '%d.%m.%Y') AS `col_date`,
			        `col_text`,
			        `col_img`,
			        `col_link`,
					`col_flag`
		       FROM 
		            `tbl_feeds`
		      WHERE 
		            `col_profile_id` = ". (int)$id ."
		   ORDER BY 
		            `col_id` DESC ";
$query_feed = mysqli_query($link, $sql_feed);

$feed = '<div class="column" id="news-feed">';
	
	if (mysqli_num_rows($query_feed) > 0) {
		$feed .= '<div class="container">';
			$feed .= '<h3 class="column-title">News Feed</h3>';
			$feed .= '<ul class="wrap-items js-scroll">';
		
			while ($row_feed = mysqli_fetch_assoc($query_feed)) {
	
				if (!empty($row_feed['col_img'])) {
					$img_feed = '<a href="'. $row_feed['col_link'] .'" class="avatar"><img src="'. $row_feed['col_img'] .'"></a>';
				} elseif (!empty($row['col_avatar']) && empty($row_feed['col_img'])) {
					$img_feed = '<div class="avatar"><img src="'. $feed_url. $row['col_avatar'] .'"></div>';
				} else {
					$img_feed = '<div class="avatar"><img src="/img/noavatar.png"></div>';
				}
				
				$class_item = "item";
				if ($row_feed['col_flag'] == 2) {
					$class_item = "item hide";
				}
				
				
				
				if ($row_feed['col_flag'] == 1 || $id == $_SESSION["user_id"]) {
					$feed .= '<li class="'. $class_item .'" id="feed'. $row_feed['col_id'] .'">';
						
						
					if ($id == $_SESSION["user_id"]) {	
						
						$feed .= '<a href="#" class="switch js-hide" data-feed-id="'. $row_feed['col_id'] .'" data-flag="'. $row_feed['col_flag'] .'">';
						if ($row_feed['col_flag'] == 1) {
							$feed .= '<i class="fa fa-close"></i>';
							$feed .= '<span>hide</span>';
						} else {
							$feed .= 'show';
						}
						$feed .= '</a>';
					}
						
						$feed .= $img_feed;
						$feed .= '<div class="content">';
							$feed .= '<p>'. $row_feed['col_text'] .'</p>';
							$feed .= '<div class="date">'. $row_feed['col_date'] .'</div>';
						$feed .= '</div>';
					$feed .= '</li>';
				}
				
			} //END WHILE
			$feed .= '</ul>';
		$feed .= '</div>'; //END class="container"
	} else {
		$feed .= '<div class="empty-container">';
			$feed .= '<div>';
			if($_SESSION["user_id"] == $id) {
				$feed .= 'Your News';
			} else {
				$feed .= 'News';
			}
			$feed .= '</div>';
		$feed .= '</div>'; //END class="empty-container"
	}
	
	
$feed .= '</div>'; //END id="news-feed"

echo $feed;