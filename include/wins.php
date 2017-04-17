<?php
include_once './commonFunctions.php';
global $mysql_f; //Запрос для записи фидов в базу
$mysql = "VALUES"; //Запрос для записи победителей в базу

function feeds($f_user_id, $f_text, $f_logo, $f_contest_id, $f_contest_title, $f_first) {
	global $mysql_f; //Запрос для записи фидов в базу
	
	
	$feed_link = '/inner_page.php?id='. $f_contest_id;
	$logo = getContestLogoUrl($f_logo);
	$text = $f_text .' <a class="link" href="'. $feed_link .'">'. $f_contest_title .'</a>';	
	
	
	
	if ($f_first) {
		$mysql_f = "INSERT INTO `tbl_feeds` VALUES (NULL, ". (int)$f_user_id .", NOW(), '$text', '$logo', '$feed_link', 1)";
	} else {
		$mysql_f .= ", (NULL, ". (int)$f_user_id .", NOW(), '$text', '$logo', '$feed_link')";
	}
}


//Выбираем все призы с данного конкурса
$sql_prizes = "SELECT 
					  `col_id`,
					  `col_start_winners`,
					  `col_end_winners`,
					  `col_type`
		         FROM 
		              `tbl_prizes`
			    WHERE 
			          `col_contest_id` = ". $id ;
$query_prizes = mysqli_query($link, $sql_prizes);

while ($row_prizes = mysqli_fetch_assoc($query_prizes)) {	
	$array_prizes[] = $row_prizes;			
} //END WHILE


//Узнаем количество победителей для Rating | Random | Likes
$sql_count = "SELECT 
					 MAX(`col_end_winners`) AS `col_wins`
		        FROM 
		             `tbl_prizes`
			   WHERE 
			         `col_contest_id` = ". $id ."
			GROUP BY 
				     `col_type`
			ORDER BY 	  
					 `col_type` ";
$query_count = mysqli_query($link, $sql_count);

while ($row_count = mysqli_fetch_assoc($query_count)) {
	$array_count[] = $row_count['col_wins'];	
} //END WHILE


//Выбираем победителей для Rating
// 6, 9, 7
$sql_rating = "SELECT 
				      COUNT(`col_id`) AS `col_count`,
				      `col_author_id`
		         FROM 
		              `tbl_likes`
			    WHERE 
			          `col_contest_id` = ". $id ." 
		     GROUP BY 
				      `col_author_id`
		     ORDER BY 	  
				      `col_count` DESC
		        LIMIT ". $array_count[0] ;
$query_rating = mysqli_query($link, $sql_rating);

$i_rating = 1;
while ($row_rating = mysqli_fetch_assoc($query_rating)) {
	
	for($i = 0; $i < count($array_prizes); $i++) {
		if($i_rating >= $array_prizes[$i]['col_start_winners'] && $i_rating <= $array_prizes[$i]['col_end_winners'] && $array_prizes[$i]['col_type'] == 1) {
			
			$prize_id = $array_prizes[$i]['col_id'];
			
			if ($i_rating > 1) {
				$mysql .= ", "; //Запрос для записи данных в базу
				
				feeds($row_rating['col_author_id'], 'Won a prize in the contest', $row['col_logo'], $id, $row['col_title'], false);
			} else {
				feeds($row_rating['col_author_id'], 'Won contest', $row['col_logo'], $id, $row['col_title'], true);
			}
			
			$mysql .= "(NULL, ". $row_rating['col_author_id'] .", ". (int)$id .", ". (int)$prize_id .", ". $i_rating .", 1)";
		}
	}
	
	$array_wins[] = $row_rating['col_author_id']; //Победители конкурса
	$i_rating ++;
				
} //END WHILE


//Выбираем победителей для Random
//8
$sql_random = "SELECT 
				      `col_user_id`
		         FROM 
		              `tbl_photo`
			    WHERE 
			          `col_contest_id` = ". $id ."
			 ORDER BY RAND() ";
$query_random = mysqli_query($link, $sql_random);

$i_random = 1;
while ($row_random = mysqli_fetch_assoc($query_random)) {
	
	if (!in_array($row_random['col_user_id'], $array_wins)) {	
	
		for($i = 0; $i < count($array_prizes); $i++) {
			if($i_random >= $array_prizes[$i]['col_start_winners'] && $i_random <= $array_prizes[$i]['col_end_winners'] && $array_prizes[$i]['col_type'] == 2) {
				$prize_id = $array_prizes[$i]['col_id'];
				$mysql .= ", (NULL, ". $row_random['col_user_id'] .", ". (int)$id .", ". (int)$prize_id .", ". $i_random .", 2)"; //Запрос для записи данных в базу
				
				feeds($row_random['col_user_id'], 'Won a prize in the contest', $row['col_logo'], $id, $row['col_title'], false);
			}
		}
		
		$array_wins[] = $row_random['col_user_id'];
		$i_random ++;
	}
		
} //END WHILE


//Выбираем победителей для Likes
//10
$sql_likes = "SELECT 
					 `col_user_id`
		        FROM 
		             `tbl_likes`
			   WHERE 
			         `col_contest_id` = ". $id ."
			ORDER BY RAND() ";
$query_likes = mysqli_query($link, $sql_likes);

$i_likes = 1;
while ($row_likes = mysqli_fetch_assoc($query_likes)) {
		
	if (!in_array($row_likes['col_user_id'], $array_wins)) {	
		
		for($i = 0; $i < count($array_prizes); $i++) {
			if($i_likes >= $array_prizes[$i]['col_start_winners'] && $i_likes <= $array_prizes[$i]['col_end_winners'] && $array_prizes[$i]['col_type'] == 3) {
				$prize_id = $array_prizes[$i]['col_id'];
				$mysql .= ", (NULL, ". $row_likes['col_user_id'] .", ". (int)$id .", ". (int)$prize_id .", ". $i_likes .", 3)"; //Запрос для записи данных в базу
				
				feeds($row_likes['col_user_id'], 'Won a prize in the contest', $row['col_logo'], $id, $row['col_title'], false);
			}
		}
			
		$i_likes ++;
	}
				
} //END WHILE


//Вставляем победителей в базу
$insert = "INSERT INTO `tbl_wins` ". $mysql;
$add = mysqli_query($link, $insert);

if ($add) {
	$update = "UPDATE 
				      `tbl_contests`
				  SET 
					  `col_flag` = 1
				WHERE 
					  `col_id` = ". $id;
	mysqli_query($link, $update);
}

feeds($row['col_company_id'], 'Complete contest', $row['col_logo'], $id, $row['col_title'], false);
mysqli_query($link, $mysql_f);

