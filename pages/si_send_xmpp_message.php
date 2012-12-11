<form data-ajax="true" action="./pages/si_aj_send_xmpp_message.php">
<div class="L recipients">
<div>
<?php
if(is_array($_COOKIE['xmpp_list']) && $XMPP_MESSAGE_LISTS_ENABLE)
    {
    /*foreach($_COOKIE['xmpp_list'] AS $key=>$value)
        {
        echo "<div>".Staff::makeUserLinkByLogin($value)."</div>";
        }*/
    $Filter = "(&(|(".$GLOBALS['LDAP_USERPRINCIPALNAME_FIELD']."=".implode(")(".$GLOBALS['LDAP_USERPRINCIPALNAME_FIELD']."=", $_COOKIE['xmpp_list']).")))";
    $Recipients = $ldap->getArray($OU,  $Filter, array($GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD'], $GLOBALS['DISPLAY_NAME_FIELD'], $GLOBALS['LDAP_USERPRINCIPALNAME_FIELD']));
    $i=0;
    foreach($Recipients[$LDAP_DISTINGUISHEDNAME_FIELD] AS $key=>$value)
        {
        echo "<div><input type=\"checkbox\" name=\"resipients[".$i."]\" value=\"".$Recipients[$GLOBALS['LDAP_USERPRINCIPALNAME_FIELD']][$key]."\" data-xmpp-item=\"true\" checked=\"checked\"/>".Staff::makeNameUrlFromDn($Recipients[$LDAP_DISTINGUISHEDNAME_FIELD][$key], $Recipients[$DISPLAY_NAME_FIELD][$key])."</div>";
        $i++;
        }
    }

if($XMPP_LDAP_GROUPS_ENABLE)
    {
    $ou = ($XMPP_LDAP_GROUPS_OU) ? ($XMPP_LDAP_GROUPS_OU) : $OU;
    $Filter = ($XMPP_LDAP_GROUPS_SUBSTR) ? "(&(".$LDAP_CN_FIELD."=*$XMPP_LDAP_GROUPS_SUBSTR*))" : "(&(".Staff::getEmptyFilter()."))";
    $Groups = $ldap->getArray($ou,  $Filter, array($LDAP_XMMP_GROUP_TITLE_FIELD, $LDAP_DISTINGUISHEDNAME_FIELD), array($LDAP_XMMP_GROUP_TITLE_FIELD), "ASC", true);

    $i=0;
    foreach($Groups[$LDAP_DISTINGUISHEDNAME_FIELD] AS $key=>$value)
        {
        if($i==0)
            echo"<div class=\"xmpp_groups\">";
        echo "<div><input id=\"group_".$i."\" type=\"checkbox\" name=\"groups[".$i."]\" value=\"".$Groups[$LDAP_DISTINGUISHEDNAME_FIELD][$key]."\"/><label for=\"group_".$i."\">".$Groups[$LDAP_XMMP_GROUP_TITLE_FIELD][$key]."</label></div>";
        $i++;
        }
    if($i>0)
        echo"</div>";
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
 <?php      
    if(is_array($_COOKIE['xmpp_messages_list']))
        {
        echo"
        <select id=\"last_xmpp_messages\">
        <option value=\"\"></option>";

        foreach($_COOKIE['xmpp_messages_list'] AS $key => $value)
            echo "<option value=\"".$value."\">".mb_substr($value, 0, $XMPP_LAST_MESS_NUM_SYM_OF_PRUNING, 'UTF-8')." ...</option>";       


        echo"</select>";
        }
?>
    </div>
    
    <input type="submit" name="" value="<?php echo $L->l('send_message'); ?>" />
</div>
</form>