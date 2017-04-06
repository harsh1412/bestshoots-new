<?php
include_once './include/db.php';

if ($_SESSION["loged"] == "yes") {

    if ($_SESSION["profile"] == "user") {
        $url = "profile.php?id=" . $_SESSION["user_id"];
    } else {
        $url = "company_profile.php?id=" . $_SESSION["user_id"];
    }

    header("Location: " . $url);
}

if (empty($_GET["code"])) {
    header("Location: error.php");
}

$delete = "DELETE FROM `tbl_lostpass` WHERE `col_date` < NOW() - INTERVAL 1 DAY";
mysqli_query($link, $delete);

$code = mysqli_real_escape_string($link, $_GET["code"]);

$sql = "SELECT 
               `col_email`
		  FROM 
		       `tbl_lostpass` 
		 WHERE 
		       `col_uniq_id` = '$code' ";
$query = mysqli_query($link, $sql);
mysqli_close($link);

if (mysqli_num_rows($query) == 0) {
    header("Location: error.php");
}

$row = mysqli_fetch_assoc($query);

include_once './include/header.php';
?>
<section id="content">
    <div id="one-less">
        <h1 class="text-effect">Password reset.</h1>
    </div> <!-- END id="one-less" -->

    <div id="two">
        <div class="sign fs-input">
            <div class="input">
                <label class="input__label">New password</label>
                <input type="password" class="input__field" id="password-fp">
            </div>
            <div class="input last">
                <label class="input__label">New password again</label>
                <input type="password" class="input__field" id="password2-fp">
            </div>
            <div class="action">
                <a href="#" id="js-reset-password" class="button button-green" data-email="<?= $row['col_email'] ?>">send</a>
            </div>
        </div>
    </div> <!-- END id="two" -->
</section> <!-- END id="content" -->
<?php include_once './include/footer.php'; ?>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="/js/jquery.jcrop.js"></script>
<script src="/js/jquery.ui.widget.js"></script>
<script src="/js/jquery.iframe-transport.js"></script>
<script src="/js/jquery.fileupload.js"></script>
<script src="/js/main.js"></script>
</body>
</html>