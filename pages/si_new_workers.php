
<?php
$ldap=new LDAP($LDAPServer, $LDAPUser, $LDAPPassword); //Соединяемся с сервером


// Определяем какой атрибут будем использовать в качестве формирования ФИО сотрудника
//-------------------------------------------------------------------------------------------------------------
if($USE_DISPLAY_NAME)
	$DisplayName=$DISPLAY_NAME_FIELD;
else
	$DisplayName=$LDAP_NAME_FIELD;
//-------------------------------------------------------------------------------------------------------------

// Делаем фильтр для выборки сотрудников нужных компаний
//-------------------------------------------------------------------------------------------------------------
$CompanyNameLdapFilter=Application::getCompanyNameLdapFilter();
//-------------------------------------------------------------------------------------------------------------


$LdapListAttrs = array($LDAP_DISTINGUISHEDNAME_FIELD, $DisplayName,
  		$LDAP_MAIL_FIELD, 
  		$LDAP_INTERNAL_PHONE_FIELD,
  		$LDAP_CITY_PHONE_FIELD,
  		$LDAP_ST_DATE_VACATION_FIELD,
  		$LDAP_END_DATE_VACATION_FIELD,
  		$LDAP_TITLE_FIELD,
  		$LDAP_DEPARTMENT_FIELD,
  		$LDAP_CELL_PHONE_FIELD,
  		$LDAP_MANAGER_FIELD,
  		$LDAP_COMPUTER_FIELD,
  		$LDAP_DEPUTY_FIELD,
  		$LDAP_GUID_FIELD,
  		$LDAP_CREATED_DATE_FIELD,
        $LDAP_USERPRINCIPALNAME_FIELD);

//Получаем правильно отсортированных сотрудников с необходимыми атрибутами LDAP
$Staff=$ldap->getArray($OU,
 "(&".$CompanyNameLdapFilter."(".$LDAP_CREATED_DATE_FIELD.">=".date('Ymd', time()-$NEW_USERS_NUM_DAYS*24*60*60)."000000.0Z)(".$LDAP_CN_FIELD."=*)".$DIS_USERS_COND.")",
 $LdapListAttrs,
 array($LDAP_CREATED_DATE_FIELD), 'DESC');

if(is_array($Staff))
	{
	// Шапка таблицы
	//-------------------------------------------------------------------------------------------------------------
	echo "
		<table class=\"sqltable\" cellpadding=\"4\">
		<th><div>ФИО</div></th>
		<th><div>Должность</div></th>
		<th><div>E-mail</div></th>
		<th><div>".$L->l('intrenal_phone')."</div></th>
		";
	if(!$HIDE_CITY_PHONE_FIELD)
		echo "<th><div>".$L->l('city_phone')."</div></th>";	
	if(!$HIDE_CELL_PHONE_FIELD)
		echo "<th><div>".$L->l('cell_phone')."</div></th>";
	if(Staff::showComputerName($Login)) //Если сотрудник является администратором справочника
		echo "<th><div>Компьютер</div></th>";
    if($GLOBALS['XMPP_ENABLE'] && $GLOBALS['XMPP_MESSAGE_LISTS_ENABLE'] && !empty($_COOKIE['dn']))  
        echo "<th><div></div></th>";            
	if($FAVOURITE_CONTACTS && $_COOKIE['dn'])
		echo "<th><div></div></th>";	
	echo "<th><div></div></th>";
	if(empty($_COOKIE['dn']) && $ENABLE_DANGEROUS_AUTH)
		echo Application::getCollTitle();		
	//-------------------------------------------------------------------------------------------------------------
	
	$FavouriteDNs=$ldap->getAttrValue($_COOKIE['dn'], $LDAP_FAVOURITE_USER_FIELD);


	$row=0;	// переменная, используемая для нумерации строк таблицы

	//Перебираем всех выбраных пользователей
	foreach($Staff[$LDAP_DISTINGUISHEDNAME_FIELD] AS $key=>$value)
	{			
		
		$Vars['row_css']=($row%2) ? "even" : "odd";
		$Vars['current_login']=$Login;
		$Vars['display_name']=$DisplayName;
		$Vars['ldap_conection']=$ldap;
		$Vars['favourite_dns']=$FavouriteDNs;
		$Vars['data_parent_id']=false;
		$Vars['id']=true;
		Staff::printUserTableRow($Staff, $key, $Vars);

		$row++;
	}
	echo"</table>";	
}
?>