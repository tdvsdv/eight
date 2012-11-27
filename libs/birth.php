<?php
//Выввод ближайших дней рождений
$time=time();
if($NEAR_BIRTHDAYS) 
	{	
	switch($BIRTH_DATE_FORMAT) //Определяем шаблоны для поиска ближайших дней рождения в зависимости от формат хранения даты
		{
		case 'yyyy-mm-dd':
			$DateFormat="m-d";
			$SortType="mm-dd";
		break;
		case 'dd.mm.yyyy':
			$DateFormat="d.m";
			$SortType="dd.mm";
		break;
		default:
			$DateFormat="d.m";
			$SortType="dd.mm";
		}	
	
	function getBD($d) //Функция для регулярного выражения, заменяющее значение даты раждения в LDAP на удобно читаемое. Это конечно форменный пи..цц
		{
		$e=explode("q", $d);
		return (int) $e[1]." ".$GLOBALS['MONTHS'][(int) $e[2]];
		}
	
	echo"<div class=\"heads\">
	<fieldset class=\"birthdays\">
	<legend>".$L->l('nearest')." ".$NUM_ALARM_DAYES." ".$L->l('they_have_birthdays').":</legend>";	
	
	@$_GET['birthdayssortcolumn']=($_GET['birthdayssortcolumn'])?$_GET['birthdayssortcolumn']:"Дата";
	@$_GET['birthdayssorttype']=($_GET['birthdayssorttype'])?$_GET['birthdayssorttype']:"ASC";
	
	$B=new LDAPTable($LDAPServer, $LDAPUser, $LDAPPassword, 389, false, false, "birthdays"); //Создаем LDAP таблицу берущую данные из БД
	
	//Добавляем колонку с ФИО
	if($USE_DISPLAY_NAME)
		$B->addColumn($DISPLAY_NAME_FIELD.", ".$LDAP_DISTINGUISHEDNAME_FIELD, "ФИО", false);	
	else	
		$B->addColumn($LDAP_DISTINGUISHEDNAME_FIELD, "ФИО", false);
		
	//Добавляем колонку с датой рождения
	$B->addColumn($LDAP_BIRTH_FIELD, "Дата", false, 0, false, $SortType);
	
	//Преобразуем колонку с ФИО в ссылку на полную инфу о сотруднике
	$B->addPregReplace("/^(.*)$/e", "Staff::makeNameUrlFromDn('\\1')", "ФИО");	

	//В зависимости от формата хранения даты преобразуем дату дня рождения для последующего преобразования в удобно читаемый формат
	switch($BIRTH_DATE_FORMAT)
		{
		case 'yyyy-mm-dd':
			$B->addPregReplace("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", 'q\\3q\\2q\\1', "Дата");
		break;
		case 'dd.mm.yyyy':
			$B->addPregReplace("/^([0-9]{2})\.([0-9]{2})\.([0-9]{4})$/", 'q\\1q\\2q\\3', "Дата");
		break;
		default:
			$B->addPregReplace("/^([0-9]{2})\.([0-9]{2})\.([0-9]{4})$/", 'q\\1q\\2q\\3', "Дата");
		}
	
	//Преобразуем дату в удобно читаемый формат
	$B->addPregReplace("/([q]{1}[0-9]{2})([q]{1}[0-9]{2})([q]{1}[0-9]{4})/e", 'getBD(\\1\\2)', "Дата");


	//Делаем фильтр необходимый для вывода ближайших дней рождения
	for($i=0; $i<$NUM_ALARM_DAYES; $i++)
		{
		@$Dates.="(".$LDAP_BIRTH_FIELD."=*".date($DateFormat, $time+$i*24*60*60)."*)";
		}
			
	//Добавляем в фильтр условия, что бы показывались сотрудники у которых соответствует компания
	if($Dates)
		{
		$CompanyNameLdapFilter=Application::getCompanyNameLdapFilter();
		
		$B->printTable($OU, "(&".$CompanyNameLdapFilter."(|".$Dates.")".$DIS_USERS_COND.")");
		}

	//Если необходимо ограничивать вывод строк с ближайшими днями рождения, то пишим в тело html-документа соответствующую информацию
	if($BIRTH_VIS_ROW_NUM)
		echo"<div id=\"birth_vis_row_num\" class=\"hidden\">".$BIRTH_VIS_ROW_NUM."</div><a id=\"show_all_birth\" href=\"#\">&darr;</a>";
		
	echo"</fieldset></div>";

	}


?>