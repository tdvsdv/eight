<form data-ajax="true" action="./pages/si_aj_send_xmpp_message.php">
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
    $Recipients = $ldap->getArray($OU,  $Filter, array($GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD'], $GLOBALS['DISPLAY_NAME_FIELD'], $GLOBALS['LDAP_USERPRINCIPALNAME_FIELD']));
    foreach($Recipients[$LDAP_DISTINGUISHEDNAME_FIELD] AS $key=>$value)
        {
        echo "<div><input type=\"checkbox\" name=\"resipients[".$Recipients[$GLOBALS['LDAP_USERPRINCIPALNAME_FIELD']][$key]."]\" value=\"true\" checked=\"checked\"/>".Staff::makeNameUrlFromDn($Recipients[$LDAP_DISTINGUISHEDNAME_FIELD][$key], $Recipients[$DISPLAY_NAME_FIELD][$key])."</div>";
        }
    }
?>
</div>
</div>
<div class="L message">
    <input type="hidden" name="menu_marker" value="<?php echo  $menu_marker ?>" />
    <textarea id="xmpp_messages" name="message" class="auto_resizing" rows="10">
<?php
    if(is_array($_COOKIE['xmpp_messages_list']))
        echo end($_COOKIE['xmpp_messages_list']);
?>
    </textarea>
    <div>
    <select id="last_xmpp_messages">
    <option value=""></option>
<?php
    foreach($_COOKIE['xmpp_messages_list'] AS $key => $value)
        echo "<option value=\"".$value."\">".mb_substr($value, 0, $XMPP_LAST_MESS_NUM_SYM_OF_PRUNING, 'UTF-8')." ...</option>";
              
?>
    </select>
    </div>
    
    <input type="submit" name="" value="<?php echo $L->l('send_message'); ?>" />
</div>
</form>