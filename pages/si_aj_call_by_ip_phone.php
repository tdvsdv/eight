<?php
require_once("../libs/require_once.php");

if($_COOKIE['dn'])
	{
	/*  Набираемый номер, взять из справочника (по какому номеру нажали). */
	$number=strtolower($_POST['data-phone-for-ip-call']);

	/* Номер звонящего, надо взять из АД из атрибута telephoneNumber */
	$InternalPhone=$ldap->getValue($_COOKIE['dn'], $LDAP_INTERNAL_PHONE_FIELD);
	$strCallerIDNumber=$InternalPhone;

	/* Имя звонящего, надо взять из АД из атрибутов sn + initials и сделать транслитерацию, иначе Инфинити отобразит абра-кадабру. */
	$strCallerIDName=Localization::translit($ldap->getValue($_COOKIE['dn'], $LDAP_SN_FIELD));

	$strHost = $GLOBALS['CALL_VIA_IP_HOST']; 
	$strUser = $GLOBALS['CALL_VIA_IP_USER']; 
	$strSecret = $GLOBALS['CALL_VIA_IP_SECRET'];
	$strChannel = "SIP/".$strCallerIDNumber."@".$GLOBALS['CALL_VIA_IP_CHANEL'];
	$strContext = $GLOBALS['CALL_VIA_IP_CONTEXT'];
	$strWaitTime = $GLOBALS['CALL_VIA_IP_WAIT_TIME'];
	$strPriority = $GLOBALS['CALL_VIA_IP_PRIORITY'];
	$strMaxRetry = $GLOBALS['CALL_VIA_IP_MAX_RETRY'];

$CALL_VIA_IP_HOST = "192.168.200.90";
$CALL_VIA_IP_USER = "phones";
$CALL_VIA_IP_SECRET = "Secret_321";
$CALL_VIA_IP_CHANEL = "SIP/$strCallerIDNumber@Infinity";
$CALL_VIA_IP_CONTEXT = "phone-book";
$CALL_VIA_IP_WAIT_TIME = "30";
$CALL_VIA_IP_PRIORITY = "1";
$CALL_VIA_IP_MAX_RETRY = "0";


	$pos=strpos($number,"local");

	if ($number == null):
	    echo "number is null";
	    exit();
	endif;

	echo "Number is $number </br>";

	echo"pos is $pos </br>";

	if ($pos === false)
	{
	$errno=0;
	$errstr=0;
	$strCallerId = "$strCallerIDName, $number";
	$oSocket = fsockopen($strHost, 5038, $errno, $errstr);
	echo "pos == false</br>";
	}

	if (!$oSocket)
	{
	echo "$errstr ($errno)<br>\n";
	exit();
	}
	else
	{
	echo "sending data...";
	echo "</br></br></br>";

	echo "Action: login</br>";
	echo "Username: $strUser</br>";
	echo "Action: originate</br>";
	echo "Channel: $strChannel</br>";
	echo "CallerId: $strCallerId</br>";
	echo "Exten: $number</br>";
	echo "Context: $strContext</br>";
	echo "</br></br></br>";
	#DEBUG END

	fputs($oSocket, "Action: login\r\n");
	fputs($oSocket, "Events: off\r\n");
	fputs($oSocket, "Username: $strUser\r\n");
	fputs($oSocket, "Secret: $strSecret\r\n\r\n");
	fputs($oSocket, "Action: originate\r\n");
	fputs($oSocket, "Channel: $strChannel\r\n");
	fputs($oSocket, "WaitTime: $strWaitTime\r\n");
	fputs($oSocket, "CallerId: $strCallerId\r\n");
	fputs($oSocket, "Exten: $number\r\n");
	fputs($oSocket, "Context: $strContext\r\n");
	fputs($oSocket, "Priority: $strPriority\r\n\r\n");
	fputs($oSocket, "Action: Logoff\r\n\r\n");
	sleep(2);
	fclose($oSocket);
	}
	echo "Extension $strChannel should be calling $number.</br>";
	echo "BYE!";
	exit();
	}

?>