<?php
    setcookie("xmpp_list[".sizeof($_COOKIE['xmpp_list'])."]", $_GET['login'], false, "/");
?>