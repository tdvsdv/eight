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

        $message = $_POST['message'];

        if($XMPP_MESSAGE_SIGN_ENABLE)
            {
            $UserInfo = $ldap->getArray($_COOKIE['dn'], false, array($DISPLAY_NAME_FIELD, $LDAP_TITLE_FIELD, $LDAP_CELL_PHONE_FIELD, $LDAP_INTERNAL_PHONE_FIELD), false, false, true);
            $Sign =  "\n\n".$L->l("start_of_xmpp_sign");
            $Sign.= "\n".$UserInfo[$DISPLAY_NAME_FIELD][0]." (".$UserInfo[$LDAP_TITLE_FIELD][0].")";
            if($XMPP_USE_INTERNAL_PHONE_IN_SIGN_ENABLE)
              $Sign.= "\n".$L->l("intrenal_phone").": ".$UserInfo[$LDAP_INTERNAL_PHONE_FIELD][0]."";
            if($XMPP_USE_MOBILE_PHONE_IN_SIGN_ENABLE)
              $Sign.= "\n".$L->l("cell_phone").": ".$UserInfo[$LDAP_CELL_PHONE_FIELD][0]."";
            $message.=$Sign;
            }

        if(is_array($_POST['resipients']))
            {
            foreach($_POST['resipients'] AS $key => $value)
                {
                $conn->message(current(explode("@", $value))."@".$XMPP_ACCOUNT_END, $message);  
                }
            }

        if(is_array($_POST['groups']))
            {
            $Filter = "(|(".$LDAP_DISTINGUISHEDNAME_FIELD."=".implode(")(".$LDAP_DISTINGUISHEDNAME_FIELD."=", LDAP::escapeFilterValue($_POST['groups']))."))";
            //echo $Filter;
            $Entries = $ldap->ldap_search($OU, $Filter, array($LDAP_MEMBER_FIELD));

            for($i=0; $i<$Entries['count']; $i++)
                {
                if($i==0)
                    $Filter = "(|";
                for($j=0; $j<$Entries[$i][$LDAP_MEMBER_FIELD]['count']; $j++)
                    {
                    $Filter.="(".$LDAP_DISTINGUISHEDNAME_FIELD."=".LDAP::escapeFilterValue($Entries[$i][$LDAP_MEMBER_FIELD][$j]).")";
                    }
                
                } 
            if($j>0)
                {
                $Filter.=")";
                $Entries = $ldap->ldap_search($OU, $Filter, array($LDAP_USERPRINCIPALNAME_FIELD));
                for($i=0; $i<$Entries['count']; $i++)
                    {       
                    $conn->message(current(explode("@", $Entries[$i][$LDAP_USERPRINCIPALNAME_FIELD][0]))."@".$XMPP_ACCOUNT_END, $message);  
                    }  
                }
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