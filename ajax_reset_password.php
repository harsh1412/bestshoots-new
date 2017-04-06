<?php
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

	include_once './include/db.php';
	header("Content-Type: text/html; charset=utf-8");
	
	$password = trim($_POST['password']);
	$password2 = trim($_POST['password2']);
	 
	if (!empty($password) or !empty($password2)) {
		
	       if (mb_strlen($password, 'utf-8') < 6 or mb_strlen($password, 'utf-8') > 20) { 
                $data['error'] = "Choose a password between 6 and 20 characters";
				exit(json_encode($data));
			}
			
	        if ($password != $password2) {
	            $data['error'] = "Passwords do not match";
				exit(json_encode($data));
			}
			
			$hash = password_hash($password, PASSWORD_BCRYPT);
			
			$email = trim($_POST["email"]);
			$email = mysqli_real_escape_string($link, $email);
			
			$update = "UPDATE `tbl_users` SET `col_password` = '$hash' WHERE `col_email`= '$email' ";
			$result = mysqli_query($link, $update);
			
			if ($result) {
				
				$delete = "DELETE FROM `tbl_lostpass` WHERE `col_email` = '$email' ";
    			mysqli_query($link, $delete);
				
				$sql = "SELECT `col_id`, `col_company_name` FROM `tbl_users` WHERE `col_email` = '$email' ";
				$query = mysqli_query($link, $sql);
				mysqli_close($link);
				$row = mysqli_fetch_assoc($query);
				
				$_SESSION["loged"] = "yes";
				$_SESSION["user_id"] = $row["col_id"];
						
				if (empty($row['col_company_name'])) {
					$redirect = "/profile.php?id=". $row["col_id"];
					$_SESSION["profile"] = "user";
				} else {
					$redirect = "/company_profile.php?id=". $row["col_id"];
					$_SESSION["profile"] = "company";
				}
		
				$error["name"] = "signin";
				$error["redirect"] = $redirect;
				exit(json_encode($error));				

			}
			
	} else {
		$data['error'] = "Choose a password between 6 and 20 characters";
		exit(json_encode($data));
	}
} //кінець ajax
exit();