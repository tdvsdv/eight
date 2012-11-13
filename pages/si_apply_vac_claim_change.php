<?php
//Занесение об отпуске в LDAP
require_once("../libs/require_once.php");
require_once("../libs/json.php");

$dn=iconv($GLOBALS['CHARSET_DATA'], $GLOBALS['CHARSET_APP'], $_POST['dn']);
$attribute=$_POST['attribute'];
$value=$_POST['value'];

if($Access) //Если юзер залогинен
	{
	if($Data=$ldap->getValue($dn, $LDAP_DATA_FIELD, false, true)) //Получаем значение атрибута для хранения данных не входящих в другие атрибуты
		{

		$a=json_decode($Data, true); //Декодируем их из JSON
		if(is_array($a)) //Если данные были, то склеить их с поступившими
			$b=array_merge($a, array($attribute=>$value));
		else
			$b=array($attribute=>$value);
		$ldap->ldap_modify($dn, array($LDAP_DATA_FIELD=>to_json($b)), true);
			echo"{\"success\": \"true\"}";
		}
	else
		{
		$ldap->ldap_modify($dn, array($LDAP_DATA_FIELD=>to_json(array($attribute=>$value))), true);
			echo"{\"success\": \"true\"}";		
		}

	}

?>