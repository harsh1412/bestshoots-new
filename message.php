<?php
include_once './include/db.php';

if ($_SESSION["loged"] != "yes") {
    header("Location: sign_up.php");
}

include_once './include/header.php';
?>
<section id="content">
    <div id="one-less">
        <h1 class="text-effect">Messages</h1>
    </div> <!-- END id="one-less" -->

    <div id="message">
        <?php
        $sql = "SELECT 
	                m.`col_date`,
					m.`col_text`,
					m.`col_dialog_id`,
					COUNT(IF(m.`col_to_id` = " . (int)$_SESSION["user_id"] . " AND m.`col_flag_new` = 1, 1, NULL)) as `col_new`, 
					u.`col_id` AS `col_author_id`,
					u.`col_company_name`,
					u.`col_username`,
					u.`col_lastname`,
					u.`col_avatar` 
			   FROM 
			        (SELECT * FROM `tbl_messages` ORDER BY `col_date` DESC) m 
		  LEFT JOIN 
		            `tbl_users` u ON (u.`col_id` = m.`col_from_id` AND u.`col_id` <> " . (int)$_SESSION["user_id"] . ") 
					OR (u.`col_id` = m.`col_to_id` AND u.`col_id`<> " . (int)$_SESSION["user_id"] . ") 		
		      WHERE 
			        (m.`col_from_id` = " . (int)$_SESSION["user_id"] . " AND m.`col_flag_from` = 1) OR (m.`col_to_id` = " . (int)$_SESSION["user_id"] . " AND m.`col_flag_to` = 1) 
		   GROUP BY 
		            m.`col_dialog_id` 
		   ORDER BY 
		            m.`col_date` DESC ";
        $result = mysqli_query($link, $sql);
        mysqli_close($link);

        if (mysqli_num_rows($result) > 0) {

            $data = '<div class="container">';
            $data .= '<ul id="message-list" class="js-scroll">';

            while ($row = mysqli_fetch_assoc($result)) {
                //замінюємо двойні пробіли на одинарні всередині рядка
                $description = preg_replace('#[\s]{2,}#', ' ', htmlspecialchars($row["col_text"]));
                $description = trim($description);

                if (mb_strlen($description) > 130) {
                    $description = mb_substr($description, 0, 130) . "...";
                }

                if (empty($row['col_company_name'])) {
                    $username = $row['col_username'] . ' ' . $row['col_lastname'];
                    $src = "/img/users/" . $row['col_avatar'];
                    $alt = $row['col_username'];
                    $href = "/profile.php?id=" . $row['col_author_id'];
                } else {
                    $username = $row['col_company_name'];
                    $src = "/img/companies/logo/" . $row['col_avatar'];
                    $alt = $row['col_company_name'];
                    $href = "/company_profile.php?id=" . $row['col_author_id'];
                }

                $data .= '<li class="item js-popup-dialog" id="dialog' . $row['col_dialog_id'] . '"
						  data-to-href="' . $href . '"
						  data-to-id="' . $row['col_author_id'] . '"
						  data-to-username="' . $username . '">';
                $data .= '<div class="avatar">';
                $data .= '<img src="' . $src . '" alt="' . $alt . '">';
                $data .= '</div>';
                $data .= '<div class="content">';
                $data .= '<p>' . $description . '</p>';
                $data .= '<span class="username">' . $username . '</span>';
                $data .= '<time class="date timeago" datetime="' . $row['col_date'] . '"></time>';

                if ($row["col_new"]) $data .= "<span class='unread'>" . $row["col_new"] . "</span>";

                $data .= '<i class="fa fa-trash-o delete js-dialog-delete"
						data-link="link"
						data-dialog-id="' . $row["col_dialog_id"] . '"
						data-to-username="' . $username . '"></i>';

                $data .= '</div>';
                $data .= "</li>";
            } //кінець while

            $data .= '</ul>';
            $data .= '</div>'; //END class="container"
        } else {
            $data = '<div class="empty">You have no messages</div>';
        }
        echo $data;
        ?>
    </div> <!-- END id="message" -->
</section> <!-- END id="content" -->
<?php include_once './include/footer.php'; ?>


<div id="js-dialog-modal" class="fs-box">
    <section>
        <div class="fs-form js-form" id="dialog-form">
            <header class="header">
                <h3>Dialog with <a href="#" class="link js-link"></a></h3>
                <i class="fa fa-close fs-close js-close" data-modal-id="#js-dialog-modal"></i>
            </header>
            <div class="container js-scroll">
                <ul id="dialog-list"></ul>
            </div> <!-- END class="about-contest" -->
            <div class="new-message" id="newMessage">
                <div class="content" contenteditable="true">Message...</div>
                <i class="fa fa-paper-plane" id="addMessage"></i>
            </div> <!-- END id="newMessage" -->
        </div>
    </section>
</div> <!-- id="js-dialog-modal" -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="/js/jquery.jcrop.js"></script>
<script src="/js/jquery.ui.widget.js"></script>
<script src="/js/jquery.iframe-transport.js"></script>
<script src="/js/jquery.fileupload.js"></script>
<script src="/js/jquery.timeago.js"></script>
<script src="/js/jquery.mCustomScrollbar.min.js"></script>
<script src="/js/main.js"></script>
</body>
</html>