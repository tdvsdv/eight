
<form class="heads" method="POST" action="<?php echo $_SERVER['PHP_SELF']."?menu_marker=si_stafflist" ?>">
<?php
$time=time();
@$_GET['sortcolumn']=($_GET['sortcolumn'])?$_GET['sortcolumn']:"ФИО";
@$_GET['sorttype']=($_GET['sorttype'])?$_GET['sorttype']:"ASC";

// Определяем какой атрибут будем использовать в качестве формирования ФИО сотрудника
//-------------------------------------------------------------------------------------------------------------
if($USE_DISPLAY_NAME)
	$DisplayName=$DISPLAY_NAME_FIELD;
else
	$DisplayName=$LDAP_NAME_FIELD;
//-------------------------------------------------------------------------------------------------------------

$sort_order=(! empty($_GET['sort_order'])) ? $_GET['sort_order'] : 'asc';
$sort_field=(! empty($_GET['sort_field'])) ? $_GET['sort_field'] : $DisplayName;
?>

<div class="heads">

<?php
if($BLOCK_VIS[$menu_marker]['birthdays'])
	include("./libs/birth.php");
if($BLOCK_VIS[$menu_marker]['search'])	
	include("./libs/search.php");
if($BLOCK_VIS[$menu_marker]['profile'])	
	include("./libs/profile.php");

?>

</div>

</form>

<?php

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
  		$LDAP_GUID_FIELD);


// Делаем фильтр для выборки сотрудников
//-------------------------------------------------------------------------------------------------------------
$CompanyNameLdapFilter=Application::getCompanyNameLdapFilter();
if(! empty($Name))
	$SearchFilter=Application::getSearchFilter($Name, $LdapListAttrs);

//-------------------------------------------------------------------------------------------------------------	
//Получаем правильно отсортированных сотрудников с необходимыми атрибутами LDAP, учитывая настроки сортировки из конфига
$Staff=$ldap->getArray($OU,
 	"(&".$SearchFilter." ".$CompanyNameLdapFilter."(".$LDAP_CN_FIELD."=*)".$DIS_USERS_COND.")",
	$LdapListAttrs,
  	array($sort_field), $sort_order);

if(is_array($Staff))
{
	// Шапка таблицы
	//-------------------------------------------------------------------------------------------------------------
	echo "
		<table class=\"sqltable\" cellpadding=\"4\">";

	$url_vars=array('name' => $Name, 'only_bookmark' => $only_bookmark, 'bookmark_attr' => $bookmark_attr, 'bookmark_name' => $bookmark_name);

	echo Application::getCollTitle("ФИО", 
									array(
										'sort' => array(
													    'field' => $DisplayName,
													    'order' => $sort_order,
													    'sorted_field' => $sort_field,
													    'url_vars' => $url_vars
													    ),
										 ) );
	echo Application::getCollTitle("Должность", 
									array(
										'sort' => array(
													    'field' => $LDAP_TITLE_FIELD,
													    'order' => $sort_order,
													    'sorted_field' => $sort_field,
													    'url_vars' => $url_vars
													    ),
										 ) );
	echo Application::getCollTitle("E-mail", 
									array(
										'sort' => array(
													    'field' => $LDAP_MAIL_FIELD,
													    'order' => $sort_order,
													    'sorted_field' => $sort_field,
													    'url_vars' => $url_vars
													    ),
										 ) );	
	echo Application::getCollTitle("Внутренний", 
									array(
										'sort' => array(
													    'field' => $LDAP_INTERNAL_PHONE_FIELD,
													    'order' => $sort_order,
													    'sorted_field' => $sort_field,
													    'url_vars' => $url_vars
													    ),
										 ) );	

	if(!$HIDE_CITY_PHONE_FIELD)
		echo Application::getCollTitle($L->l('city_phone'), 
										array(
											'sort' => array(
														    'field' => $LDAP_CITY_PHONE_FIELD,
														    'order' => $sort_order,
														    'sorted_field' => $sort_field,
														    'url_vars' => $url_vars
														    ),
											 ) );

	echo Application::getCollTitle("Мобильный", 
										array(
											'sort' => array(
														    'field' => $LDAP_CELL_PHONE_FIELD,
														    'order' => $sort_order,
														    'sorted_field' => $sort_field,
														    'url_vars' => $url_vars
														    ),
											 ) );											 		

	if(Staff::showComputerName($Login)) //Если сотрудник является администратором справочника
		echo Application::getCollTitle("Компьютер", 
										array(
											'sort' => array(
														    'field' => $LDAP_COMPUTER_FIELD,
														    'order' => $sort_order,
														    'sorted_field' => $sort_field,
														    'url_vars' => $url_vars
														    ),
											 ) );
	if($FAVOURITE_CONTACTS && $_COOKIE['dn'])
		echo Application::getCollTitle("");

	if(empty($_COOKIE['dn']) && $ENABLE_DANGEROUS_AUTH)
		echo Application::getCollTitle();
	//-------------------------------------------------------------------------------------------------------------
	

	$FavouriteDNs=$ldap->getAttrValue($_COOKIE['dn'], $LDAP_FAVOURITE_USER_FIELD);

	//Выводим пользователей, которые есть в избраном
	if($GLOBALS['FAVOURITE_CONTACTS'] && is_array($FavouriteDNs) && !empty($_COOKIE['dn']))
		{
		$Filter="(&(".$LDAP_CN_FIELD."=*)".$DIS_USERS_COND."(|(".$LDAP_DISTINGUISHEDNAME_FIELD."=".implode(")(".$LDAP_DISTINGUISHEDNAME_FIELD."=", $FavouriteDNs).")))";
		//echo "$Filter";
		$Favourites=$ldap->getArray($OU, $Filter, $LdapListAttrs);

		if(is_array($Favourites))
			{
			$row=0;
			foreach($Favourites[$LDAP_DISTINGUISHEDNAME_FIELD] AS $key=>$value)
				{	
				$Vars['row_css']=($row%2) ? "even favourite" : "odd favourite";
				$Vars['current_login']=$Login;
				$Vars['display_name']=$DisplayName;
				$Vars['ldap_conection']=$ldap;
				$Vars['favourite_dns']=$FavouriteDNs;
				$Vars['data_parent_id']=true;
				$Vars['id']=false;

				Staff::printUserTableRow($Favourites, $key, $Vars);
				$row++;
				}
			}
		}



	$row=0;	// переменная, используемая для нумерации строк таблицы
	foreach($Staff[$LDAP_DISTINGUISHEDNAME_FIELD] AS $key=>$value)
	{
				
		$Vars['row_css']=($row%2) ? "even" : "odd";
		$Vars['current_login']=$Login;
		$Vars['display_name']=$DisplayName;
		$Vars['ldap_conection']=$ldap;
		$Vars['favourite_dns']=$FavouriteDNs;
		$Vars['data_parent_id']=false;
		$Vars['id']=true;
		if($Name!='*')
			$Vars['search_str']=$Name;
		Staff::printUserTableRow($Staff, $key, $Vars);

		$row++;
	}
	echo"</table>";	
}



//-------------------------------------------------------------------------------------------------
/*
if($Name)
	{
	$table=new LDAPTable($LDAPServer, $LDAPUser, $LDAPPassword);
	if($USE_DISPLAY_NAME)
		$table->addColumn($DISPLAY_NAME_FIELD.", ".$LDAP_DISTINGUISHEDNAME_FIELD, "ФИО", true, 0, false, "ad_def_full_name");
	else	
		$table->addColumn($LDAP_DISTINGUISHEDNAME_FIELD, "ФИО", true, 0, false, "ad_def_full_name");
	$table->addColumn($LDAP_TITLE_FIELD, "Должность");
	$table->addColumn($LDAP_MAIL_FIELD, "E-mail", true);
	$table->addColumn($LDAP_INTERNAL_PHONE_FIELD, "Внутренний", true);
	if(!$HIDE_CITY_PHONE_FIELD)
		{
		$table->addColumn($LDAP_CITY_PHONE_FIELD, $L->l('city_phone'), true);
		$table->addColumn($LDAP_SN_FIELD, $L->l('city_phone'), true);
		}
	$table->addColumn($LDAP_CELL_PHONE_FIELD, "Мобильный", true);	
	$table->addColumn($LDAP_OBJECTCLASS_FIELD, "Тип", true, 3, true);
	if (isset($LDAP_VACATION_FIELD))
		$table->addColumn($LDAP_VACATION_FIELD, $LDAP_VACATION_FIELD, false, 0, true);
	if(in_array($Login, $ADMIN_LOGINS))
		$table->addColumn($LDAP_COMPUTER_FIELD, "Компьютер", true);
	if(!@$WhoAreYou)
		$table->addColumn($LDAP_DISTINGUISHEDNAME_FIELD, "Это вы?");

	$table->addVar("Name", $Name);
	if(@$_GET['form_sent']||@$_POST['form_sent'])
		$table->addVar("form_sent", 1);	
	$table->addVar("only_bookmark", $only_bookmark);			
	$table->addVar("bookmark_name", $BOOKMARK_NAME);
	$table->addVar("bookmark_attr", $bookmark_attr);
	
	$Name=quotemeta($Name);
	
	$table->addPregReplace("/^(.*)$/e", "Staff::makeNameUrlFromDn('\\1')", "ФИО");			
	$table->addPregReplace("/([>]{1}[А-я\s.]*)(".strtolower($Name).")([А-я\s.]*[<]{1})/", "\\1<u class='found'>\\2</u>\\3", "ФИО");
	$table->addPregReplace("/([>]{1}[А-я\s.]*)(".ucfirst($Name).")([А-я\s.]*[<]{1})/", "\\1<u class='found'>\\2</u>\\3", "ФИО");		
	$table->addPregReplace("/___/", "", "ФИО");
		

	//$table->addPregReplace("/([A-z0-9_\.\-]{1,20}@[A-z0-9\.\-]{1,20}\.[A-z]{2,4})/", "<a href='mailto:\\1'>\\1</a>", "E-mail");
	$table->addPregReplace("/^(.*)$/e", "Staff::makeMailUrl('\\1')", "E-mail");
	
	$table->addPregReplace("/(".strtolower($Name).")/", "<u class='found'>\\1</u>", "Внутренний");
	if($VACATION)
		{
		@$Conditions4[$LDAP_VACATION_FIELD]['in_range_date']=$time;	
		$table->addPregReplace("/^(.*)$/", "<del title=\"В отпуске\">\\1</del>", "Внутренний", 1, $Conditions4);
		}
	$table->addPregReplace("/^(.*)$/e", "Staff::makeInternalPhone('\\1')", "Внутренний");


	if(!$HIDE_CITY_PHONE_FIELD)
		{
		$table->addPregReplace("/^(.*)$/e", "Staff::makeCityPhone('\\1')", $L->l('city_phone'));
		$table->addPregReplace("/(".$Name.")/", "<u class='found'>\\1</u>", $L->l('city_phone'));
		if($VACATION)
			$table->addPregReplace("/^(.*)$/", "<del title=\"В отпуске\">\\1</del>", $L->l('city_phone'), 1, $Conditions4);
		$table->addPregReplace("/^$/", "x", $L->l('city_phone'));
		}

	$table->addPregReplace("/(".strtolower($Name).")/", "<u class='found'>\\1</u>", "Мобильный");
	$table->addPregReplace("/^(.*)$/e", "Staff::makeCellPhone('\\1')", "Мобильный");

	$table->addPregReplace("/^\.\./", "", "Должность");
	$table->addPregReplace("/^\./", "", "Должность");
	$table->addPregReplace("/(".strtolower($Name).")/", "<u class='found'>\\1</u>", "Должность");
	$table->addPregReplace("/(".ucfirst($Name).")/", "<u class='found'>\\1</u>", "Должность");

	if(!@$WhoAreYou)
		{
		$Conditions1[$LDAP_DISTINGUISHEDNAME_FIELD]['!=']=$dn;
		$Conditions1[$LDAP_OBJECTCLASS_FIELD]['=']="user";
		$Conditions2[$LDAP_DISTINGUISHEDNAME_FIELD]['=']=$dn;
		$Conditions2[$LDAP_OBJECTCLASS_FIELD]['=']="user";
		$Conditions3[$LDAP_DISTINGUISHEDNAME_FIELD]['!=']=$dn;
		$Conditions3[$LDAP_OBJECTCLASS_FIELD]['!=']="user";		
		
		$table->addPregReplace("/^([\w\W]{1,})$/", "<a href=\"?menu_marker=si_stafflist&dn=\\0&sortcolumn=".@$_GET['sortcolumn']."&sorttype=".@$_GET['sorttype']."&Name=".$Name."&bookmark_attr=".$bookmark_attr."&bookmark_name=".$BOOKMARK_NAME."&only_bookmark=".$only_bookmark.((@$_GET['form_sent']||@$_POST['form_sent'])?"&form_sent=1":"")."\"><img border=\"0\" src=\"./skins/".$CURRENT_SKIN."/images/true.png\" title=\"Да! Это Я!\"></a>", "Это вы?", 1, $Conditions1);
		if(@$Error['password'])
			$table->addPregReplace("/^([\w\W]{1,})$/", "<form method=\"POST\" action=\"".@$_SERVER['PHP_SELF']."?dn=\\0&Name=".$Name."\"><input type=\"hidden\" name=\"bookmark_attr\" value=\"".$bookmark_attr."\" /><input type=\"hidden\" name=\"bookmark_name\" value=\"".$BOOKMARK_NAME."\" />".((@$_GET['form_sent']||@$_POST['form_sent'])?"<input type=\"hidden\" name=\"form_sent\" value=\"1\" />":"")."<input type=\"hidden\" name=\"only_bookmark\" value=\"".$only_bookmark."\" /> Введите пароль <span class=\"title\"><input type=\"password\" class=\"error password\" id=\"password\" name=\"password\"/><em>Неверный пароль <i></i></em></span><img border=\"0\" src=\"./skins/".$CURRENT_SKIN."/images/blank.gif\" onload=\"password.focus(); \" /> и <input type=\"submit\" onload=\"alert('fsdfsdf');\" name=\"BItSMe\" value=\"Докажите\"> </form>", "Это вы?", 1, $Conditions2);
		else
			$table->addPregReplace("/^([\w\W]{1,})$/", "<form method=\"POST\" action=\"".@$_SERVER['PHP_SELF']."?dn=\\0&Name=".$Name."\"><input type=\"hidden\" name=\"bookmark_attr\" value=\"".$bookmark_attr."\" /><input type=\"hidden\" name=\"bookmark_name\" value=\"".$BOOKMARK_NAME."\" />".((@$_GET['form_sent']||@$_POST['form_sent'])?"<input type=\"hidden\" name=\"form_sent\" value=\"1\" />":"")."<input type=\"hidden\" name=\"only_bookmark\" value=\"".$only_bookmark."\" /> Введите пароль <input type=\"password\" class=\"password\" id=\"password\" name=\"password\"/><img border=\"0\" src=\"./skins/".$CURRENT_SKIN."/images/blank.gif\" onload=\"password.focus(); \" /> и <input type=\"submit\" onload=\"alert('fsdfsdf');\" name=\"BItSMe\" value=\"Докажите\"><div id=\"IDForScroll\"></div> </form>", "Это вы?", 1, $Conditions2);	
		$table->addPregReplace("/^([\w\W]{1,})$/", "", "Это вы?", 1, $Conditions3);
		}
	
	$cn=($Name=="\*")?"*":"*".$Name."*";	
	

// Делаем фильтр для выборки сотрудников нужных компаний
//-------------------------------------------------------------------------------------------------------------
	$CompanyNameLdapFilter=Application::getCompanyNameLdapFilter();
//-------------------------------------------------------------------------------------------------------------	
		
	$table->printTable($OU, "(&".$CompanyNameLdapFilter."(|(".$LDAP_CN_FIELD."=".$cn.")(".$LDAP_MAIL_FIELD."=".$cn.")(".$LDAP_INTERNAL_PHONE_FIELD."=".$cn.")(".$LDAP_CITY_PHONE_FIELD."=".$cn.")(".$LDAP_CELL_PHONE_FIELD."=".$cn.")(".$LDAP_TITLE_FIELD."=".$cn.")(".$LDAP_DEPARTMENT_FIELD."=".$cn."))".$DIS_USERS_COND.")");
	}
	*/

?>