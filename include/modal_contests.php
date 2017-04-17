<?php
include_once 'commonFunctions.php';
$num_rows = mysqli_num_rows($query_contests);

if ($num_rows > 0) {

    $contests = '<div id="js-contests-modal" class="fs-box">';
    $contests .= '<section>';
    $contests .= '<div class="fs-form contests-form js-form">';
    $contests .= '<header class="header">';
    $contests .= '<i class="fa fa-close fs-close js-close" data-modal-id="#js-contests-modal"></i>';
    $contests .= '</header>';
    $contests .= '<ul class="wrap-items company-contests js-scroll">';


    $num_contest = 0; //ID конкурса
    $prize = '';
    $num_prize = 1;
    $i = 0;

    while ($row_contests = mysqli_fetch_assoc($query_contests)) {

        $i++;

        if ($num_prize < 4) {
            $prize .= '<li class="prize"><span class="num">' . $num_prize . '.</span><span class="text">' . $row_contests['col_prize_title'] . '</span></li>';

            $item_contest = '<li class="item">';
            $item_contest .= '<h4 class="title">' . $row_contests['col_title'] . '</h4>';
            $logoUrl = getContestLogoUrl($row_contests['col_logo']);
            $item_contest .= '<img src="' . $logoUrl . '" alt="' . $row_contests['col_title'] . '" class="photo">';
            $item_contest .= '<div class="info">';
            $item_contest .= '<h5>Prize of this contest</h5>';
            $item_contest .= '<ul class="wrap-prizes">' . $prize . '</ul>';
            $item_contest .= '<div class="action">';
            $item_contest .= '<a href="/inner_page.php?id=' . $row_contests['col_id'] . '" class="button button-big-black button-read">Read more</a>';
            $item_contest .= ' </div>';
            $item_contest .= '</div>'; //END class="info"
            $item_contest .= '</li>';


            $num_prize++;
        }

        if (($row_contests['col_id'] != $num_contest && $num_contest != 0) || $num_rows == $i) {
            $contests .= $item_contest;

            $num_prize = 1;
            $prize = '<li class="prize"><span class="num">' . $num_prize . '.</span><span class="text">' . $row_contests['col_prize_title'] . '</span></li>';
            $num_prize++;
        }

        $num_contest = $row_contests['col_id']; //ID конкурса

    } //END WHILE
    //$contests .= '<div class="test">'. $i .'</div>';

    $contests .= '</ul>';
    $contests .= '</div>';
    $contests .= '</section>';
    $contests .= '</div>'; //END id="js-contests-modal"


    echo $contests;

}