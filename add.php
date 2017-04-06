<?php
include_once './include/db.php';

if ($_SESSION["profile"] == "user") {
    header("Location: profile.php?id=" . $_SESSION["user_id"]);
}

if ($_SESSION["loged"] != "yes") {
    header("Location: sign_up.php");
}

$durations = array(
    "7 DAY" => "1 week",
    "14 DAY" => "2 weeks",
    "21 DAY" => "3 weeks",
    "1 MONTH" => "1 month",
    "2 MONTH" => "2 months",
    "3 MONTH" => "3 months"
);

$prizes = array(
    "1" => "Add prize for rating",
    "2" => "Add prize for random",
    "3" => "Add prize for likes"
);

include_once './include/header.php';
?>
<section id="content">
    <div id="one-less">
        <h1 class="text-effect">Let's create one Contest</h1>
    </div> <!-- END id="one-less" -->


    <div id="create-contest">


        <div class="fs-form fs-input">
            <header class="progress">
                <div class="item-progress">
                    <span class="scale active js-item-arrow" data-value="0">1</span>
                    <span class="text">description contest</span>
                </div>
                <div class="item-progress">
                    <span class="scale js-item-arrow" data-value="1">2</span>
                    <span class="text">prize for rating</span>
                </div>
                <div class="item-progress">
                    <span class="scale js-item-arrow" data-value="2">3</span>
                    <span class="text">prize for random</span>
                </div>
                <div class="item-progress">
                    <span class="scale js-item-arrow" data-value="3">4</span>
                    <span class="text">prize for likes</span>
                </div>
            </header>
            <div id="slider">

                <div class="swSlider">
                    <div class="swPage">
                        <div class="input">
                            <label class="input__label">Title</label>
                            <input class="input__field" id="js-title" type="text">
                        </div>
                        <div class="input">
                            <label class="input__label">About</label>
                            <textarea class="input__field input__text" id="js-about"></textarea>
                        </div>
                        <div class="input">
                            <label class="input__label">The duration of the contest</label>
                            <div class="input__field input__select js-write-modal" data-modal-id="#js-durations-modal">
                                <span class="select-text" id="js-duration" data-value="1 MONTH">1 month</span>
                                <i class="fa fa-caret-down"></i>
                            </div>
                        </div>
                        <div class="input">
                            <form id="upload" method="post" action="/upload.php" enctype="multipart/form-data">

                                <i class="fa fa-cloud-upload add-file js-file" id="js-header-photo"
                                   data-min-width="1920"
                                   data-min-height="535"
                                   data-max-size="4"
                                   data-dir="contests/header_photo"></i>

                                <input type="file" name="files" id="photoupload" class="js-form"/>
                                <div class="file-text">
                                    <h6>Add header photo</h6>
                                    <p>Recommended Image Size: 1920 x 535</p>
                                    <p>Maximum file size: 4 MB</p>
                                </div>
                            </form>
                        </div>
                        <div class="input">

                            <i class="fa fa-cloud-upload add-file js-file" id="js-logo"
                               data-min-width="758"
                               data-min-height="692"
                               data-max-size="4"
                               data-dir="contests/logo"></i>

                            <div class="file-text">
                                <h6>Add logo</h6>
                                <p>Recommended Image Size: 758 x 692</p>
                                <p>Maximum file size: 4 MB</p>
                            </div>
                        </div>

                    </div> <!-- class="swPage" -->
                    <?php
                    $html = '';

                    foreach ($prizes as $key => $value) {
                        $html .= '<div class="swPage">';
                        $html .= '<div class="input">';
                        $html .= '<i class="fa fa-plus add-file js-write-prize-modal" data-modal-id="#js-prize-modal" data-type="' . $key . '" data-title="' . $value . '"></i>';
                        $html .= '<div class="file-text">add prize</div>';
                        $html .= '</div>';
                        $html .= '<ul class="input js-wrap-prizes"></ul>';
                        $html .= '</div>';
                    }

                    echo $html;
                    ?>
                </div> <!-- class="swSlider" -->
            </div> <!-- class="slider" -->

            <div class="action">
                <i class="fa fa-long-arrow-right arrow-right" id="js-arrow" data-value="1"></i>
            </div>

        </div>

    </div> <!-- END id="create-contest" -->

</section> <!-- END id="content" -->
<?php include_once './include/footer.php';

$data = '<div id="js-durations-modal" class="fs-box">';
$data .= '<section>';
$data .= '<ul id="durations-form" class="js-form">';
foreach ($durations as $key => $value) {
    $data .= '<li data-value="' . $key . '">' . $value . '</li>';
}
$data .= '</ul>';
$data .= '</section>';
$data .= '</div>'; //id="js-durations-modal"
echo $data;
?>

<div id="js-prize-modal" class="fs-box">
    <section>
        <div id="prize-form" class="fs-form fs-form2 js-form">
            <header class="header">
                <h3>Add prize for</h3>
                <i class="fa fa-close fs-close js-close" data-modal-id="#js-prize-modal"></i>
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
                    <i class="fa fa-cloud-upload add-file js-file" data-min-width="40" data-min-height="84"
                       data-max-size="1" data-dir="prizes"></i>
                    <div class="file-text">
                        <h6>Add picture prize</h6>
                        <p>Maximum file size: 1 MB</p>
                    </div>
                </div>

                <div class="action">
                    <a href="#" class="button button-small-black" id="prize-send">add</a>
                </div>
            </div> <!-- END class="fs-input" -->
        </div>
    </section>
</div> <!-- END id="js-prize-modal" -->

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
                <input type="hidden" name="x" id="x"/>
                <input type="hidden" name="y" id="y"/>
                <input type="hidden" name="w" id="w"/>
                <input type="hidden" name="h" id="h"/>
                <div class="action">
                    <a href="#" class="button button-small-black" id="upload-send">add</a>
                </div>
            </div>
        </div>
    </section>
</div> <!-- id="js-crop-modal" -->


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="/js/jquery.jcrop.js"></script>
<script src="/js/jquery.ui.widget.js"></script>
<script src="/js/jquery.iframe-transport.js"></script>
<script src="/js/jquery.fileupload.js"></script>
<script src="/js/main.js"></script>
</body>
</html>