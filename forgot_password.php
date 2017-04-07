<?php
include_once './include/db.php';
include_once './include/commonFunctions.php';

setRedirectHeaderToProfile();

include_once './include/header.php';
?>
<section id="content">
    <div id="one-less">
        <h1 class="text-effect">Forgot your password?</h1>
    </div> <!-- END id="one-less" -->

    <div id="two">
        <div class="sign fs-input">
            <div class="info">
                Enter your email address and we will send you instructions to reset your password.
            </div>
            <div class="input last">
                <label class="input__label">E-Mail</label>
                <input type="email" class="input__field" id="email-fp">
            </div>
            <div class="action">
                <a href="#" id="js-email-reset-password" class="button button-green">send</a>
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