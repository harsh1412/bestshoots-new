<?php 
include_once './include/constants.php';
include_once './include/db.php';

if($_SESSION["loged"] == "yes") {
	
	if($_SESSION["profile"] == "user") {
		$url = "profile.php?id=". $_SESSION["user_id"];
	} else {
		$url = "company_profile.php?id=". $_SESSION["user_id"];
	}
	
	header("Location: ". $url);
}

$url_facebook = 'https://www.facebook.com/dialog/oauth';
$params_facebook = array(
    'client_id'     => '1105637386249754', // Client ID
    'redirect_uri'  => $host. '/facebookcom.php', // Redirect URIs
    'response_type' => 'code'
);

$href_facebook = $url_facebook .'?'. urldecode(http_build_query($params_facebook));

include_once './include/header.php'; 
?>
        <section id="content">
        	<div id="one-less">
            	<h1 class="text-effect">Sign in.</h1>
			</div> <!-- END id="one-less" -->
            
			<div id="two">
				<div class="sign fs-input">
					<div class="input">
						<label for="email-l" class="input__label">E-Mail</label>
						<input type="email" class="input__field" id="email-l">
					</div>
					<div class="input last">
						<label for="password-l" class="input__label">Password</label>
						<input type="password" class="input__field" id="password-l">
					</div>      
          
                    
					<div class="action">
						<a href="#" id="login" class="button button-green">Sign in</a>
                        <div class="facebook-container">
                        	<a class="facebook" href="<?=$href_facebook?>"><i class="fa fa-facebook-official"></i></a>
                        </div>
                        <div class="forgot-password-container">
                        	<a href="/forgot_password.php" class="forgot-password">Forgot your password?</a>
                        </div>
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
