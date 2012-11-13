<?php
//-------------------------------------------------------------------------------------------------
if(@$_SERVER['REMOTE_USER']) //Если есть прозрачно аутентифицированный пользователь. И в серверной переменной хранится его логин
	{
	if($Login=$ldap->getValue($OU, $LDAP_USERPRINCIPALNAME_FIELD, $LDAP_USERPRINCIPALNAME_FIELD."=".$_SERVER['REMOTE_USER']."*")) //Проверяим есть ли юзер, с логином аутентифицированного пользователя в LDAP
		{
		if(in_array($Login, $ADMIN_LOGINS)) //Пользователь является администратором справочника
			{
			$Access=true;
			}
		else //Пользователь НЕ является администратором справочника
			$Access=false;	
		$Valid=true;		
		}
	else
		{
		$Access=false;
		$Valid=false;
		}
	}
else
	{
	if($Login=$ldap->getValue($_COOKIE['dn'], $LDAP_USERPRINCIPALNAME_FIELD)) //Если есть кука и в LDAP есть юзер с DN из этой куки, то пользователь был аутентифицирован не прозрачно ранее.
		{
		if(in_array($Login, $ADMIN_LOGINS)) //Пользователь является администратором справочника
			{
			$Access=true;
			}
		else //Пользователь НЕ является администратором справочника
			$Access=false;
		$Valid=true;
		}
	else
		{
		$Access=false;	
		$Valid=false;
		}
	}
//-------------------------------------------------------------------------------------------------
?>