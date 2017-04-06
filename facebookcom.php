<?php
if (isset($_GET['code'])) {
	include_once './include/constants.php';
	include_once './include/db.php';
	include_once './include/geoipcity.inc';
	
	$result_facebookcom = false;

    $params = array(
        'client_id'     => '1105637386249754', // Client ID
        'redirect_uri'  => $host . '/facebookcom.php', // Redirect URI
        'client_secret' => '4f476198adbf41fc0621adbca19b8264', // Client secret
        'code'          => $_GET['code']
    );

    $url = 'https://graph.facebook.com/oauth/access_token';

    $tokenInfo = null;
    parse_str(file_get_contents($url . '?' . http_build_query($params)), $tokenInfo);

    if (count($tokenInfo) > 0 && isset($tokenInfo['access_token'])) {
       $params = array(
			'fields' => 'id,first_name,last_name,email,location',
			'access_token' => $tokenInfo['access_token']
			);

        $userInfo = json_decode(file_get_contents('https://graph.facebook.com/me' . '?' . urldecode(http_build_query($params))), true);

        if (isset($userInfo['id'])) {
            $result_facebookcom = true;
        }
    }
	
	if ($result_facebookcom) {
		
		$uid = mysqli_real_escape_string($link, $userInfo["id"]);
		
		// проверим в базе есть ли такой пользователь как мы хочем зарегистрировать
	    $sql = "SELECT `col_id`, `col_uid`, `col_company_name` FROM `tbl_users` WHERE `col_uid` = '$uid' ";
	    $result = mysqli_query($link, $sql);
		
	    $row = mysqli_fetch_assoc($result);
	
		if ($row["col_uid"] == $uid) {
				
			$_SESSION["loged"] = "yes";
			$_SESSION["user_id"] = $row["col_id"];
						
			if (empty($row['col_company_name'])) {
				$redirect = "/profile.php?id=". $row["col_id"];
				$_SESSION["profile"] = "user";
			} else {
				$redirect = "/company_profile.php?id=". $row["col_id"];
				$_SESSION["profile"] = "company";
			}
				

	    } else { //if ($row["col_uid"] == $uid) 

			$username = mb_strtoupper_first($userInfo["first_name"]);
			$username = mysqli_real_escape_string($link, $username);
			$lastname = mb_strtoupper_first($userInfo["last_name"]);
			$lastname = mysqli_real_escape_string($link, $lastname);
			
			// IP-адрес
			$visitor_ip = getenv("HTTP_X_FORWARDED_FOR");
			if (empty($visitor_ip) || $visitor_ip == "unknown") $visitor_ip = getenv("REMOTE_ADDR");
			
			// Открыть файл базы
			$gi = GeoIP_open("include/GeoLiteCity.dat", GEOIP_STANDARD);
			// Получить данные из базы
			$record = GeoIP_record_by_addr($gi, $visitor_ip);
			
			$location = $record->country_name;
			
			if (!empty($record->city)) {
				$location .= ", ". $record->city;
			}
			GeoIP_close($gi);
			
			$location = mysqli_real_escape_string($link, $location);
			
			if (!empty($userInfo["email"])) {
				$email = mysqli_real_escape_string($link, $userInfo["email"]);
			}
			
			$insert = "INSERT INTO `tbl_users` 
				VALUES (
					NULL,
					'$email',
					'',
					'',
					'$username',
					'$lastname',
					NOW(),
					'',
					'', 
					'',
					'',
					'$location',
					'$uid'
				) ";
			$query2 = mysqli_query($link, $insert);
			$id = mysqli_insert_id($link);
			
			//***News Feed***
			$text = "Registered in the system";	
			$text = mysqli_real_escape_string($link, $text);
						
			$insert0 = "INSERT INTO `tbl_feeds` VALUES (NULL, ". (int)$id .", NOW(), '$text', '', '', 1) ";
			mysqli_query($link, $insert0);
			//***END News Feed***
			
			mysqli_close($link);
			
			if ($query2) {
				$_SESSION["loged"] = "yes";
				$_SESSION["user_id"] = $id;
				$_SESSION["profile"] = "user";
				$redirect = "/profile.php?id=". $id;
			}			
			/*	
			echo '<pre></pre>';
			echo print_r($userInfo);
			echo '</pre>';
			exit('<p class="test">kk</p>');
			*/
			
	    } //if ($row["col_uid"] == $uid)

		?>
		<script type="text/javascript">
            window.location.href = "<?=$redirect?>"
        </script>
        <?php
		
    } //if ($result_facebookcom)
}
