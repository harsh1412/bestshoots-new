<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>BestShoots</title>
<link rel="stylesheet" href="/css/style.css" />
<link rel="stylesheet" href="/css/jquery.mCustomScrollbar.css" />
</head>

<body id="page">
	<section id="main">
        <header id="header">
            <div class="container">
                <div class="column">
                    <a href="/" id="logo">BestShoots</a>
                </div>
                <div class="column center-block">
                    <a href="/latest_photos.php" class="link">New</a>
                    <a href="/top_contests.php" class="link">TOP</a>
                </div>
                <div class="column right-block">
                    <?php
					if ($_SESSION["loged"] == "yes") {
						$sql_pr = "SELECT `col_company_name`, `col_username`, `col_avatar` FROM `tbl_users` WHERE `col_id`=". (int)$_SESSION["user_id"];
						$result_pr = mysqli_query($link, $sql_pr);
						$row_pr = mysqli_fetch_assoc($result_pr);
						
						if (empty($row_pr['col_company_name'])) {
							$href = "/profile.php?id=". $_SESSION["user_id"];
							$src = "/img/users/". $row_pr['col_avatar'];
							$alt = $row_pr['col_username'];
						} else {
							$href = "/company_profile.php?id=". $_SESSION["user_id"];
							$src = "/img/companies/logo/". $row_pr['col_avatar'];
							$alt = $row_pr['col_company_name'];
						}
						
						if (empty($row_pr['col_avatar'])) {
							$src = "/img/noavatar.png";
						}
						
						
						$html = '<a id="profile-link" href="#">';
							$html .= '<img src="'. $src .'" alt="'. $alt .'">';
							$html .= '<i class="fa fa-caret-down"></i>';
						$html .= '</a>';
						$html .= '<ul id="profile-menu">';
							$html .= '<li><a href="'. $href .'">My profile</a></li>';
							$html .= '<li><a href="#" id="js-write-new-password">Change password</a></li>';
							
							if ($_SESSION["profile"] == "company") {
								$html .= '<li><a href="/add.php">New contest</a></li>';
								$html .= '<li><a href="/edit.php">Edit contest</a></li>';
							}
							
							$html .= '<li><a href="/message.php">Message</a></li>';
							$html .= '<li><a href="/exit.php">Exit</a></li>';
						$html .= '</ul>';	
					} else {
						$html = '<a href="/sign_in.php" class="link sign-in">Sign in</a>';
						$html .= '<a href="/sign_up.php" class="link">Sign Up</a>';
					}
					echo $html;
					?> 
                </div>
            </div>
        </header> <!-- END id="header" -->