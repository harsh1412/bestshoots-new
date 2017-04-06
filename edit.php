<?php 
include_once './include/db.php';

if($_SESSION["profile"] == "user") {
	header("Location: profile.php?id=". $_SESSION["user_id"]);
}

if($_SESSION["loged"] != "yes") {
	header("Location: sign_up.php");
}

include_once './include/header.php'; 
?>
        <section id="content">
        	<div id="one-less">
            	<h1 class="text-effect edit">Edit Contests</h1>
            </div> <!-- END id="one-less" -->

            <div id="edit-contest">	
                <div class="content">
                	<div class="empty-content" id="js-empty-content"><div>Your Contest list is empty</div></div>
                    <div id="js-holder" class="nav-holder"></div>
                    <div class="item first" id="js-first">
                        <div class="title">Title</div>
                        <div class="about">About</div>
                        <div class="start-date">Start Date</div>
                        <div class="end-date">END Date</div>
                        <div class="action"></div>
                    </div>
                    <ul class="wrap-items js-wrap" id="js-wrap-contests">
                        <?php
                        $sql = "SELECT `col_id`,
                                       `col_title`,
                                       `col_about`,
                                       `col_header_photo`,
                                       `col_logo`,
									   DATE_FORMAT(`col_date_start`, '%Y-%m-%d') AS `col_date_start`,
									   DATE_FORMAT(`col_date_end`, '%Y-%m-%d') AS `col_date_end`
                                  FROM 
                                       `tbl_contests`
                                 WHERE 
                                       `col_company_id` = ". (int)$_SESSION["user_id"] ;
                        $query = mysqli_query($link, $sql);
                        mysqli_close($link);
                        
                        if (mysqli_num_rows($query) > 0) {
                            
                            $li = '';
                            
                            while ($row = mysqli_fetch_assoc($query)) {
                                
                                
                                $li .= '<li class="item" id="contest'. $row['col_id'] .'" 
                                    data-title="'. $row['col_title'] .'"
                                    data-about="'. $row['col_about'] .'">
                                    
                                    <div class="title">'. $row['col_title'] .'</div>
									<div class="about">'. strip_tags($row['col_about']) .'</div>
									<div class="visible">
										<input class="theday" id="theday-start'. $row['col_id'] .'" 
											data-altinput=true
											data-altFormat="d.m.Y"
											data-value="'. $row['col_date_start'] .'"
											data-contest-id="'. $row['col_id'] .'"
											data-pickr-id="#theday-start'. $row['col_id'] .'"
											data-type="start"
											value="'. $row['col_date_start'] .'">
										</div>
									<div class="visible">
										<input class="theday" id="theday-end'. $row['col_id'] .'"
											data-altinput=true
											data-altFormat="d.m.Y"
											data-value="'. $row['col_date_end'] .'"
											data-contest-id="'. $row['col_id'] .'"
											data-pickr-id="#theday-end'. $row['col_id'] .'"
											data-type="end"
											value="'. $row['col_date_end'] .'">
									</div>
									
                                    <div class="action">
                                        <div class="bars icon">
                                            <i class="fa fa-bars"></i>
                                            <div class="navbar js-navbar">
                                                <a href="/edit_prizes.php?id='. $row['col_id'] .'">Prizes</a>
												<a href="#" class="js-write-edit-contest-modal" data-value="'. $row['col_id'] .'">Edit contest</a>
                                                <a href="#" class="js-file" 
													data-contest-id="'. $row['col_id'] .'"
													data-min-width="1920"
													data-min-height="535"
													data-max-size="4"
													data-dir="contests/header_photo">Edit header photo</a>
												<a href="#" class="js-file"
													data-contest-id="'. $row['col_id'] .'"
													data-min-width="758"
													data-min-height="692"
													data-max-size="4"
													data-dir="contests/logo">Edit logo</a>
                                                <a href="#" class="js-delete-contest" data-value="'. $row['col_id'] .'">Delete contest</a>
                                            </div>
                                        </div>
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
	
    <div id="js-edit-contest-modal" class="fs-box">
        <section>
            <div class="fs-form fs-form2 js-form">
                <header class="header">
                    <h3>Editing a contest</h3>
                    <i class="fa fa-close fs-close js-close" data-modal-id="#js-edit-contest-modal"></i>
                </header>
                <div class="fs-input">
                    <div class="input">
                        <label class="input__label">Title</label>
                        <input class="input__field" id="js-title-edit" type="text">
                    </div>
                    <div class="input">
                        <label class="input__label">About</label>
                        <textarea class="input__field input__text" id="js-about-edit"></textarea>
                    </div>
                    <div class="action">
                        <a href="#" class="button button-small-black" id="js-edit-contest">Save</a>
                    </div>
                </div> <!-- END class="fs-input" -->
            </div>
        </section>
    </div> <!-- END id="js-edit-contest-modal" -->
    
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
    </div> <!-- id="js-crop-modal" -->
    
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