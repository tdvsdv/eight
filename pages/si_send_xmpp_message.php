<form action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="L recipients">
<div>
<?php
if(is_array($_COOKIE['xmpp_list']))
    {
    /*foreach($_COOKIE['xmpp_list'] AS $key=>$value)
        {
        echo "<div>".Staff::makeUserLinkByLogin($value)."</div>";
        }*/
    $Filter = "(&(|(".$GLOBALS['LDAP_USERPRINCIPALNAME_FIELD']."=".implode(")(".$GLOBALS['LDAP_USERPRINCIPALNAME_FIELD']."=", $_COOKIE['xmpp_list']).")))";
    $Recipients = $ldap->getArray($OU,  $Filter, array($GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD'], $GLOBALS['DISPLAY_NAME_FIELD']));
    foreach($Recipients[$LDAP_DISTINGUISHEDNAME_FIELD] AS $key=>$value)
        {
        echo "<div><input type=\"checkbox\" name=\"resipients[]\" value=\"true\" checked=\"checked\"/>".Staff::makeNameUrlFromDn($Recipients[$LDAP_DISTINGUISHEDNAME_FIELD][$key], $Recipients[$DISPLAY_NAME_FIELD][$key])."</div>";
        }
    }
?>
</div>
</div>
<div class="L message">

    <input type="hidden" name="menu_marker" value="<?php echo  $menu_marker ?>" />
    <textarea name="message" class="auto_resizing" rows="10"></textarea>
    <input type="submit" name="" value="<?php echo $L->l('send_message'); ?>" />
</div>
</form>