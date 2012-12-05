<?php
require_once("../libs/require_once.php");

#Use XMPPHP_Log::LEVEL_VERBOSE to get more logging for error reports
#If this doesn't work, are you running 64-bit PHP with < 5.2.6?
if($XMPP_ENABLE)
    {
    $conn = new XMPPHP_XMPP($XMPP_SERVER, $XMPP_PORT, $XMPP_USER, $XMPP_PASSWORD, 'xmpphp', $XMPP_DOMAIN, $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);

    try {
        if(!$XMPP_ENCRYPTION)
            $conn->useEncryption(false);
        $conn->connect();
        $conn->processUntil('session_start');
        $conn->presence();

        foreach($_POST['resipients'] AS $key => $value)
            {
            $conn->message(current(explode("@", $key))."@".$XMPP_ACCOUNT_END, $_POST['message']);  
            }
        $conn->disconnect();

        if(is_array($_COOKIE['xmpp_messages_list']))
            {
            if(!in_array($_POST['message'], $_COOKIE['xmpp_messages_list']))
                {
                $index = sizeof($_COOKIE['xmpp_messages_list']);
                if($index >= $XMPP_NUM_OF_LAST_MESSAGES_PER_USER)
                    $index = 0;
                setcookie("xmpp_messages_list[".$index."]", $_POST['message'], time()+$XMPP_LAST_MESSAGE_TIME_OF_KEEPING, "/");
                }
            }
        else
            {
            setcookie("xmpp_messages_list[0]", $_POST['message'], time()+$XMPP_LAST_MESSAGE_TIME_OF_KEEPING, "/");
            }
        

        echo "{\"success\": \"true\", \"message\": \"".$L->l('xmpp_message_has_been_sent')."\"}";
        } catch(XMPPHP_Exception $e) {
            echo "{\"success\": \"true\", \"message\": \"".$e->getMessage()."\"}";
            }
    }
?>