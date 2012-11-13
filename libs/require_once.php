<?php
require_once("../config.php");
require_once("../libs/forms.php");
require_once("../libs/time.php");
require_once("../libs/staff.php");

Application::makeLdapConfigAttrLowercase();

//Database
//----------------------------------------
$ldap=new LDAP($LDAPServer, $LDAP_WRITE_USER, $LDAP_WRITE_PASSWORD);
//----------------------------------------	

//Basic Auth
//----------------------------------------	
include_once("../auth.php");
//----------------------------------------	

?>