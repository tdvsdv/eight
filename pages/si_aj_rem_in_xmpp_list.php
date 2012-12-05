<?php
    setcookie("xmpp_list[".array_search($_GET['login'], $_COOKIE['xmpp_list'])."]", false, false, "/");
?>