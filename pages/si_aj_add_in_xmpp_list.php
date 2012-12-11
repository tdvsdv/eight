<?php
    require_once("../config.php");
    setcookie("xmpp_list[".(max(array_keys($_COOKIE['xmpp_list']))+1)."]", $_GET['login'], time()+$XMPP_MESSAGE_LISTS_TIME_OF_LIVE, "/");
?>