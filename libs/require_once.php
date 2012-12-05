<?php
require_once("../config.php");
require_once("../libs/forms.php");
require_once("../libs/time.php");
require_once("../libs/staff.php");
require_once("../libs/localization.php");
require_once("../libs/spyc.php");
require_once('../libs/XMPPHP/XMPP.php');

Application::makeLdapConfigAttrLowercase();
$L=new Localization("../config/locales/".$LOCALIZATION.".yml");

//Database
//----------------------------------------
$ldap=new LDAP($LDAPServer, $LDAP_WRITE_USER, $LDAP_WRITE_PASSWORD);
//----------------------------------------	

//Basic Auth
//----------------------------------------	
include_once("../auth.php");
//----------------------------------------	

?>