<?php
require_once("./config.php");
require_once("./libs/forms.php");
require_once("./libs/staff.php");
require_once("./libs/phones.php");
require_once("./libs/time.php");
require_once("./libs/localization.php");
require_once("./libs/spyc.php");

Application::makeLdapConfigAttrLowercase();
$L=new Localization("./config/locales/".$LOCALIZATION.".yml");

//Database
//----------------------------------------
$ldap=new LDAP($LDAPServer, $LDAPUser, $LDAPPassword);
//----------------------------------------	

setlocale(LC_CTYPE, "ru_RU.".$GLOBALS['CHARSET_APP']); 

@$menu_marker=($_POST['menu_marker'])?$_POST['menu_marker']:$_GET['menu_marker'];

//Basic Auth
//----------------------------------------	
include_once("auth.php");
//----------------------------------------	
?>
<html>

	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link rel="STYLESHEET" href="./skins/<?php echo $CURRENT_SKIN; ?>/css/newwin.css" type="text/css" />
		<link rel="STYLESHEET" href="./skins/<?php echo $CURRENT_SKIN; ?>/css/staff.css" type="text/css" />
		<link rel="STYLESHEET" href="./skins/<?php echo $CURRENT_SKIN; ?>/css/si_print_vacation_claim.css" type="text/css" />
		<link rel="STYLESHEET" href="./skins/<?php echo $CURRENT_SKIN; ?>/css/general.css" type="text/css" />
		<script type="text/javascript" src="./js/jquery-1.8.2.min.js"></script>
		<script type="text/javascript" src="./js/prototype.js"></script>
		<script type="text/javascript" src="./js/smartform.js"></script>
		<script type="text/javascript" src="./js/staff.js"></script>
		<script type="text/javascript" src="./js/si_print_vacation_claim.js"></script>		
	</head>

	<body>
		<?php	
		if(is_file($PHPPath."/".$menu_marker.".php")) {include($PHPPath."/".$menu_marker.".php");}
		?>
	</body>

</html>