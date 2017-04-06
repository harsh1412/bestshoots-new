<?php 
include_once './include/db.php';

if($_SESSION["loged"] == "yes") {
	
	if($_SESSION["profile"] == "user") {
		$url = "profile.php?id=". $_SESSION["user_id"];
	} else {
		$url = "company_profile.php?id=". $_SESSION["user_id"];
	}
	
	header("Location: ". $url);
}

include_once './include/header.php'; 
?>
        <section id="content">
        	<div id="one-less">
            	<h1 class="text-effect">Sign up now.</h1>
            </div> <!-- END id="one-less" -->
            
            <div id="two">
            	<div class="sign fs-input">
                	<header class="header">
                    	<a href="#" class="link active js-switch" data-value="0">As User</a>
                        <a href="#" class="link js-switch" data-value="1">As Company</a>
                    </header>
                    <div id="slider">
                    	<div class="swSlider">
                        	<div class="swPage">
                                <div class="input first">
                                    <div class="wrap-input">
                                        <label for="username-u" class="input__label">Username</label>
                                        <input type="text" class="input__field" id="username-u">
                                    </div>
                                    <div class="wrap-input">
                                        <label for="lastname-u" class="input__label">Lastname</label>
                                        <input type="text" class="input__field" id="lastname-u">
                                    </div> 
                                </div>
                                <div class="input">
                                    <label for="email-u" class="input__label">E-Mail</label>
                                    <input type="email" class="input__field" id="email-u">
                                </div>
                                <div class="input last">
                                    <label for="password-u" class="input__label">Password</label>
                                    <input type="password" class="input__field" id="password-u">
                                </div>
                                <div class="action">
                                    <a href="#" id="signup-user" class="button button-green">Sign up</a>
                                </div>
                            </div>
                            <div class="swPage">
                                <div class="input">
                                    <label for="name-c" class="input__label">Name</label>
                                    <input type="text" class="input__field" id="name-c">
                                </div>
                                <div class="input">
                                    <label for="email-c" class="input__label">E-Mail</label>
                                    <input type="email" class="input__field" id="email-c">
                                </div>
                                <div class="input last">
                                    <label for="password-c" class="input__label">Password</label>
                                    <input type="password" class="input__field" id="password-c">
                                </div>
                                <div class="action">
                                    <a href="#" id="signup" class="button button-green">Sign up</a>
                                </div>
                            </div>
                        </div> <!-- END class="swSlider" -->
                    </div> <!-- END id="slider" -->
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