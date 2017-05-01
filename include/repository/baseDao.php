<?php

/**
 * Created by IntelliJ IDEA.
 * User: anton
 * Date: 18.04.17
 * Time: 0:40
 */
class baseDao
{
    var $link;

    function __construct($link) {
        $this->link = $link;
    }
}