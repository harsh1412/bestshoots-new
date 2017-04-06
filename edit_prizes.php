<?php 
include_once './include/db.php';

$id = $_GET['id'];

if (!preg_match("|^[\d]+$|", $id)) {
	header("Location: error.php");
}

if($_SESSION["profile"] == "user") {
	header("Location: profile.php?id=". $_SESSION["user_id"]);
}

$sql0 = "SELECT
               `col_id`
		  FROM
		       `tbl_contests`
		 WHERE
		       `col_id` = ". (int)$id ." AND `col_company_id` = ". (int)$_SESSION["user_id"] ;
$query0 = mysqli_query($link, $sql0);

$num = mysqli_num_rows($query0);
if ($num == 0) {
	header("Location: error.php");
}

include_once './include/header.php'; 
?>
        <section id="content">
        	<div id="one-less">
            	<h1 class="text-effect edit">Edit Contests</h1>
            </div> <!-- END id="one-less" -->

            <div id="edit-contest" class="edit-prizes">	
				<div class="header">
					<h2><a href="/edit.php" class="link">←</a> Prizes</h2>
					<a href="#" class="button button-small-green js-write-modal" data-modal-id="#js-add-prize-modal">Add a prize</a>
				</div>
                <div class="content">
                	<div class="empty-content" id="js-empty-content"><div>For this contest has no prizes</div></div>
                    <div id="js-holder" class="nav-holder"></div>
                    <div class="item first" id="js-first">
                        <div class="title">Title</div>
                        <div class="description">Description</div>
                        <div class="img">Img</div>
                        <div class="type">Type</div>
                        <div class="winners">Winners</div>
                        <div class="action"></div>
                    </div>
                    <ul class="wrap-items js-wrap" id="js-wrap-prizes">
                        <?php
						$sql = "SELECT 
                                       `col_id`,
									   `col_title`,
									   `col_description`,
									   `col_img`,
									   `col_start_winners`,
									   `col_end_winners`,
									   `col_type`
                                  FROM 
                                       `tbl_prizes`
                                 WHERE 
                                       `col_contest_id` = ". (int)$id ." AND `col_company_id` = ". (int)$_SESSION["user_id"] ."
							  ORDER BY 
		                               `col_type`, `col_start_winners`";
						$query = mysqli_query($link, $sql);
						mysqli_close($link);
                        
                        if (mysqli_num_rows($query) > 0) {
                            
                            $li = '';
							$prizes = array('rating', 'random', 'likes');
                            
                            while ($row = mysqli_fetch_assoc($query)) {
								
								$winners = ordinal_suffix($row['col_start_winners']) .' - '. ordinal_suffix($row['col_end_winners']);
                                
                                $li .= '<li class="item" id="prize'. $row['col_id'] .'"> 
                                    <div class="title">'. $row['col_title'] .'</div>
									<div class="description">'. $row['col_description'] .'</div>
									<div class="img"><img src="/img/prizes/'. $row['col_img'] .'" alt="'. $row['col_title'] .'"></div>
									<div class="type">'. $prizes[$row['col_type']-1] .'</div>
									<div class="winners">'. $winners .'</div>
                                    <div class="action">
                                        <i class="fa fa-trash-o icon delete js-delete-prize" data-value="'. $row['col_id'] .'"></i>
                                    </div>
                                </li>';
                            } //END WHILE
                            
                            echo $li;
                        }
                        ?>
                    </ul>
                    
                </div> <!-- END class="content" -->
            </div> <!-- END id="edit-contest" -->   
        
</section> <!-- END id="content" --> 
<?php include_once './include/footer.php'; ?>
    
	<div id="js-add-prize-modal" class="fs-box">
        <section>
            <div id="prize-form" class="fs-form fs-form2 js-form">
                <header class="header">
                    <h3>Add a prize</h3>
                    <i class="fa fa-close fs-close js-close" data-modal-id="#js-add-prize-modal"></i>
                </header>
                <div class="fs-input">
                	<div class="input">
                        <label class="input__label">Title</label>
                        <input class="input__field" type="text" id="prz-title">
                    </div>
                    
                    <div class="input">
                        <label class="input__label">Description</label>
                        <input class="input__field" type="text" id="prz-description">
                    </div>
                    
                    <div class="input">
                        <label class="input__label">Winners</label>
                        <input class="input__field" type="text" id="prz-winners">
                    </div>
                    <div class="input">
                        <label class="input__label">Type</label>
                        <ul id="js-type">
                        	<li class="option check js-option" data-type="1"><i class="fa fa-check"></i><span>rating</span></li>
                        	<li class="option js-option" data-type="2"><i class="fa fa-check"></i><span>random</span></li>
                        	<li class="option js-option" data-type="3"><i class="fa fa-check"></i><span>likes</span></li>
                        </ul>
                    </div>
                    
                    <div class="input">   
                        <i class="fa fa-cloud-upload add-file js-file" data-min-width="40" data-min-height="84" data-max-size="1" data-dir="prizes"></i>
                        <div class="file-text">
                            <h6>Add picture prize</h6>
                            <p>Maximum file size: 1 MB</p>
                        </div>
                    </div>
                    
                    <div class="action">
                        <a href="#" class="button button-small-black" id="js-add-prize" data-type="1" data-contest-id="<?=$id?>">add</a>
                    </div>
                </div> <!-- END class="fs-input" -->
            </div>
        </section>
    </div> <!-- END id="js-add-prize-modal" -->
    
    <form id="upload" method="post" action="/upload.php" enctype="multipart/form-data">
		<input name="files" id="photoupload" class="js-form" type="file">
	</form>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="/js/jquery.jcrop.js"></script>
	<script src="/js/jquery.ui.widget.js"></script>
	<script src="/js/jquery.iframe-transport.js"></script>
	<script src="/js/jquery.fileupload.js"></script>
    <script src="/js/jPages.js"></script>
    <script src="js/flatpickr.min.js"></script>
    <script src="/js/main.js"></script>
</body>
</html>