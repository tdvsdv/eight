<?php

if($Access)
	{
//********************************************************************************************************************************************************	
	echo "<form method=\"POST\" action=\"".$_SERVER['PHP_SELF']."?menu_marker=si_staffedit\" >";
		
	//-------------------------------------------------------------------------------------------------
	@$Name=($_POST['name'])?$_POST['name']:(($_GET['name'])?$_GET['name']:$SEARCH_DEFAULT_VALUE);
	$BadSymbols=array("[", "]", "{", "}", "<", ">", "(", ")", ".", ",", ";", ":", "!", "?", "&", "#", "@", "%", "^", "+", "|", "\\", "/", "~", "$");
	$Name=str_replace($BadSymbols, "", $Name);
	@$dn=($_GET['dn'])?$_GET['dn']:$_POST['dn'];

	@$_GET['sortcolumn']=($_GET['sortcolumn'])?$_GET['sortcolumn']:"ФИО";
	@$_GET['sorttype']=($_GET['sorttype'])?$_GET['sorttype']:"ASC";
	//-------------------------------------------------------------------------------------------------

	//Обработка формы
	//-------------------------------------------------------------------------------------------------
	if(@$_GET['FormSend'])
		{
		$ldap=new LDAP($LDAPServer, $LDAP_WRITE_USER, $LDAP_WRITE_PASSWORD);

		if(($RE_MAIL)&&(!preg_match("/".$RE_MAIL."/", $_POST[$LDAP_MAIL_FIELD])))
			$Errors[$LDAP_MAIL_FIELD]=$_POST[$LDAP_MAIL_FIELD];
		else
			$info[$LDAP_MAIL_FIELD]=$_POST[$LDAP_MAIL_FIELD];		

		if(($RE_OTHER_TELEPHONE)&&(!preg_match("/".$RE_OTHER_TELEPHONE."/", $_POST[$LDAP_INTERNAL_PHONE_FIELD])))
			$Errors[$LDAP_INTERNAL_PHONE_FIELD]=$_POST[$LDAP_INTERNAL_PHONE_FIELD];
		else
			$info[$LDAP_INTERNAL_PHONE_FIELD]=$_POST[$LDAP_INTERNAL_PHONE_FIELD];
		
		if(($RE_TELEPHONE_NUMBER)&&(!preg_match("/".$RE_TELEPHONE_NUMBER."/", $_POST[$LDAP_CITY_PHONE_FIELD])))
			$Errors[$LDAP_CITY_PHONE_FIELD]=$_POST[$LDAP_CITY_PHONE_FIELD];
		else
			$info[$LDAP_CITY_PHONE_FIELD]=$_POST[$LDAP_CITY_PHONE_FIELD];	
		
		
		if(($RE_MOBILE)&&(!preg_match("/".$RE_MOBILE."/", $_POST[$LDAP_CELL_PHONE_FIELD])))
			$Errors[$LDAP_CELL_PHONE_FIELD]=$_POST[$LDAP_CELL_PHONE_FIELD];
		else
			$info[$LDAP_CELL_PHONE_FIELD]=$_POST[$LDAP_CELL_PHONE_FIELD];
			
		if(!preg_match("/^([0-9]{2}\.[0-9]{2}\.[0-9]{4})$/", $_POST['Birthday']))
			$Errors[$LDAP_BIRTH_FIELD]=$_POST['Birthday'];
		else
			{
			switch($BIRTH_DATE_FORMAT)
				{
				case 'yyyy-mm-dd':
					$Date=explode(".", $_POST['Birthday']);
					$info[$LDAP_BIRTH_FIELD]=$Date[2]."-".$Date[1]."-".$Date[0];
				break;
				case 'dd.mm.yyyy':
					$info[$LDAP_BIRTH_FIELD]=$_POST['Birthday'];
				break;
				default:
					$info[$LDAP_BIRTH_FIELD]=$_POST['Birthday'];
				}			
			}
			
		
		if($USE_DISPLAY_NAME)		
			{
				
			if(($RE_FIO)&&(!preg_match("/".$RE_FIO."/", $_POST['FIO'])))
				{
				$Errors['FIO']=$_POST['FIO'];
				}
			else
				{
				$info[$DISPLAY_NAME_FIELD]=$_POST['FIO'];
				}
			}
			
		$info["title"]=$_POST['Title'];

		
		$ldap->ldap_modify($dn, $info);
			
		}
	//-------------------------------------------------------------------------------------------------

	include("./libs/search.php");

	//Кто вы?
	//-------------------------------------------------------------------------------------------------
	if($_COOKIE['dn'])
		{
		if($WhoAreYou=$ldap->getValue($_COOKIE['dn'], "name"))
			{
			echo "<fieldset class=\"whoareyou\">";
			echo"<legend>".$WhoAreYou."</legend>";

			echo"<ul>";
			echo"<li><a href=\"".$_SERVER['PHP_SELF']."?menu_marker=si_stafflist\">Справочник</a></li>";		
			echo"<li><a href=\"newwin.php?menu_marker=si_employeeview&dn=".$_COOKIE['dn']."\" data-lightview-type=\"iframe\" data-lightview-options=\"width: '80%', height: '100%', keyboard: {esc: true}, skin: 'light'\" class=\"lightview\">Профиль</a></li>";					

			if(@!$_SERVER['REMOTE_USER'])
				echo"<li><a href=\"".$_SERVER['PHP_SELF']."?iamnot=1&name=".$Name."\" title=\"Нет! \">Выйти</a></li>";		
			echo"</ul></fieldset>";	
			}
		}
	//-------------------------------------------------------------------------------------------------
	echo "
	</form>
	";
//********************************************************************************************************************************************************	

	if($Name)
		{				
		$table=new LDAPTable($LDAPServer, $LDAPUser, $LDAPPassword);

		$table->addColumn($LDAP_OBJECTCLASS_FIELD, "Тип", true, 3);
		if($USE_DISPLAY_NAME)
			$table->addColumn($DISPLAY_NAME_FIELD.", distinguishedname", "ФИО", true, 0, false, "ad_def_full_name");
		else
			$table->addColumn($LDAP_DISTINGUISHEDNAME_FIELD, "ФИО", true, 0, false, "ad_def_full_name");
		$table->addColumn($LDAP_TITLE_FIELD, "Должность");
		$table->addColumn($LDAP_MAIL_FIELD, "E-mail", true);
		$table->addColumn($LDAP_INTERNAL_PHONE_FIELD, $L->l('intrenal_phone'), true);
		$table->addColumn($LDAP_CITY_PHONE_FIELD, $L->l('city_phone'), true);
		$table->addColumn($LDAP_CELL_PHONE_FIELD, "Мобильный", true);		
		$table->addColumn($LDAP_BIRTH_FIELD, "Д.Р.", true, 0, false, "dd.mm.yyyy");
		$table->addColumn($LDAP_PHOTO_FIELD, "Фото", true);	
		$table->addColumn($LDAP_DISTINGUISHEDNAME_FIELD, "Править");		

		$table->addVar("name", $Name);
		if(@$_GET['form_sent']||@$_POST['form_sent'])
			$table->addVar("form_sent", 1);	
		$table->addVar("only_bookmark", $only_bookmark);		
		$table->addVar("bookmark_name", $BOOKMARK_NAME);
		$table->addVar("bookmark_attr", $bookmark_attr);
		$table->addVar("dn", $dn);

		//$Name=quotemeta($Name);

		$Conditions1[$LDAP_DISTINGUISHEDNAME_FIELD]['!=']=$dn;
		$Conditions2[$LDAP_DISTINGUISHEDNAME_FIELD]['=']=$dn;

		
	//ФИО
	//-------------------------------------------------------------------------------------------------	
	if($USE_DISPLAY_NAME)
		{
		$table->addPregReplace("/([ёA-zА-я-]+)[\s]{1}([ёA-zА-я-]+[\s]{1}[ёA-zА-я-]+)(CN.*)/u", "<a href=\"newwin.php?menu_marker=si_employeeview&dn=\\3\" data-lightview-type=\"iframe\" data-lightview-options=\"width: '80%', height: '100%', keyboard: {esc: true}, skin: 'light'\" class=\"lightview\"><div class='surname'>\\1</div>\\2</a>", "ФИО", 1, $Conditions1);
		$table->addPregReplace("/([ёA-zА-я-]+[\s]{1}[ёA-zA-я]{1}.)[\s]{1}([ёA-zА-я-]+)(CN.*)/u", "<a href=\"newwin.php?menu_marker=si_employeeview&dn=\\3\" data-lightview-type=\"iframe\" data-lightview-options=\"width: '80%', height: '100%', keyboard: {esc: true}, skin: 'light'\" class=\"lightview\"><div class='surname'>\\2</div>\\1</a>", "ФИО", 1, $Conditions1);
		$table->addPregReplace("/([ёA-zA-я0-1\s-]{1,})(CN.*)/u", "<a href=\"newwin.php?menu_marker=si_employeeview&dn=\\2\" data-lightview-type=\"iframe\" data-lightview-options=\"width: '80%', height: '100%', keyboard: {esc: true}, skin: 'light'\" class=\"lightview\"><div class='surname'>\\1</div></a>", "ФИО", 1, $Conditions1);
		$table->addPregReplace("/^(CN.*)$/u", "<a href=\"newwin.php?menu_marker=si_employeeview&dn=\\1\" data-lightview-type=\"iframe\" data-lightview-options=\"width: '80%', height: '100%', keyboard: {esc: true}, skin: 'light'\" class=\"lightview\"><div class='surname'>x</div></a>", "ФИО", 1, $Conditions1);
		
		
		if(@$Errors['FIO'])
			$table->addPregReplace("/([\w\W]{1,})/u", "<form action=\"".$_SERVER['PHP_SELF']."?menu_marker=si_staffedit&dn=".$dn."&FormSend=1&name=".$Name."\" method=\"POST\"><input type=\"hidden\" name=\"bookmark_attr\" value=\"".$bookmark_attr."\" /><input type=\"hidden\" name=\"bookmark_name\" value=\"".$BOOKMARK_NAME."\" />".(($_GET['form_sent']||$_POST['form_sent'])?"<input type=\"hidden\" name=\"form_sent\" value=\"1\" />":"")."<span class=\"title\"><input class=\"error fio\" name=\"FIO\" value=\"".$Errors['FIO']."\"/><em>Новое ФИО не соответствует формату. <i></i></em></span>", "ФИО", 1, $Conditions2);
		else
			{
			@$table->addPregReplace("/(.*)(CN.*)/u", "<form action=\"".$_SERVER['PHP_SELF']."?menu_marker=si_staffedit&dn=".$dn."&FormSend=1&name=".$Name."\" method=\"POST\"><input type=\"hidden\" name=\"bookmark_attr\" value=\"".$bookmark_attr."\" /><input type=\"hidden\" name=\"bookmark_name\" value=\"".$BOOKMARK_NAME."\" />".(($_GET['form_sent']||$_POST['form_sent'])?"<input type=\"hidden\" name=\"form_sent\" value=\"1\" />":"")."<input type=\"hidden\" name=\"only_bookmark\" value=\"".$only_bookmark."\" /><input class=\"text fio\" name=\"FIO\" value=\"\\1\"/>", "ФИО", 1, $Conditions2);
			@$table->addPregReplace("/^(CN.*)$/u", "<form action=\"".$_SERVER['PHP_SELF']."?menu_marker=si_staffedit&dn=".$dn."&FormSend=1&name=".$Name."\" method=\"POST\"><input type=\"hidden\" name=\"bookmark_attr\" value=\"".$bookmark_attr."\" /><input type=\"hidden\" name=\"bookmark_name\" value=\"".$BOOKMARK_NAME."\" />".(($_GET['form_sent']||$_POST['form_sent'])?"<input type=\"hidden\" name=\"form_sent\" value=\"1\" />":"")."<input type=\"hidden\" name=\"only_bookmark\" value=\"".$only_bookmark."\" /><input class=\"text fio\" name=\"FIO\" value=\"\"/>", "ФИО", 1, $Conditions2);
			}
		}
	else
		{
		$table->addPregReplace("/^[A-Za-z]+=*([ёА-яA-z0-1\s-.]+),[\S\s]+$/eu", "'<a href=\"newwin.php?menu_marker=si_employeeview&dn='.'\\0'.'\" data-lightview-type=\"iframe\" data-lightview-options=\"width: '80%', height: '100%', keyboard: {esc: true}, skin: 'light'\" class=\"lightview\">___\\1</a>'", "ФИО");
		$table->addPregReplace("/___([ёA-zА-я-]+)[\s]{1}([ёA-zА-я-]+[\s]{1}[ёA-zА-я-]+)/u", "<div class='surname'>\\1</div>\\2", "ФИО");
		//Для формата Имя О. Фамилия
		$table->addPregReplace("/___([ёA-zА-я-]+[\s]{1}[ёA-zA-я]{1}.)[\s]{1}([ёA-zА-я-]+)/u", "<div class='surname'>\\2</div>\\1", "ФИО");		
		}
	$table->addPregReplace("/([>]{1}[А-я\s.]*)(".strtolower(preg_quote($Name)).")([А-я\s.]*[<]{1})/u", "\\1<u class='found'>\\2</u>\\3", "ФИО", 1, $Conditions1);
	$table->addPregReplace("/([>]{1}[А-я\s.]*)(".ucfirst(preg_quote($Name)).")([А-я\s.]*[<]{1})/u", "\\1<u class='found'>\\2</u>\\3", "ФИО", 1, $Conditions1);		
	$table->addPregReplace("/___/u", "", "ФИО", 1, $Conditions1);
	
	//-------------------------------------------------------------------------------------------------			
	
	//-------------------------------------------------------------------------------------------------	

	//Д.Р.
	//-------------------------------------------------------------------------------------------------	
	switch($BIRTH_DATE_FORMAT)
		{
		case 'yyyy-mm-dd':
			$table->addPregReplace("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/u", "\\3.\\2.\\1", "Д.Р.", 1);		
		break;
		case 'dd.mm.yyyy':
			$table->addPregReplace("/^([0-9]{2}\.[0-9]{2}\.[0-9]{4})$/u", "\\1", "Д.Р.", 1);
		break;
		default:
			$table->addPregReplace("/^([0-9]{2}\.[0-9]{2}\.[0-9]{4})$/u", "\\1", "Д.Р.", 1);
		}		
		
		
		$table->addPregReplace("/^$/u", "x", "Д.Р.", 1);
		if(@$Errors[$LDAP_BIRTH_FIELD])	
			$table->addPregReplace("/([\w\W]{1,})/u", "<span class=\"title\"><input class=\"error telephonenumber\" name=\"Birthday\"  id=\"Birthday\" value=\"".$Errors['birthday']."\"/><em>Новая Д.Р. не соответствует формату. <br/>Действующие значение: <b> \\1 </b><i></i></em></span>", "Д.Р.", 1, $Conditions2);
		else
			$table->addPregReplace("/([\w\W]{1,})/u", "<input class=\"text telephonenumber\" name=\"Birthday\"  id=\"Birthday\" value=\"\\1\"/>", "Д.Р.", 1, $Conditions2);		
		$table->addPregReplace("/value=\"x\"/", "value=\"\"", "Д.Р.", 1, $Conditions2);	
	//-------------------------------------------------------------------------------------------------		
		
	
		
	//E-mail
	//-------------------------------------------------------------------------------------------------		
		$table->addPregReplace("/([A-z0-9_\.\-]{1,20}@[A-z0-9\.\-]{1,20}\.[A-z]{2,4})/u", "<a href='mailto:\\1'>\\1</a>", "E-mail", 1, $Conditions1);
		$table->addPregReplace("/^$/", "x", "E-mail");	
		if(@$Errors[$LDAP_MAIL_FIELD])
			if($USE_DISPLAY_NAME)
				$table->addPregReplace("/([\w\W]{1,})/u", "<span class=\"title\"><input class=\"error mail\" name=\"".$LDAP_MAIL_FIELD."\" value=\"".$Errors[$LDAP_MAIL_FIELD]."\"/><em>Новый e-mail не соответствует формату. <br/>Действующие значение: <b> \\1 </b><i></i></em></span>", "E-mail", 1, $Conditions2);
			else	
				$table->addPregReplace("/([\w\W]{1,})/u", "<form action=\"".$_SERVER['PHP_SELF']."?menu_marker=si_staffedit&dn=".$dn."&FormSend=1&name=".$Name."\" method=\"POST\"><input type=\"hidden\" name=\"bookmark_attr\" value=\"".$bookmark_attr."\" /><input type=\"hidden\" name=\"bookmark_name\" value=\"".$BOOKMARK_NAME."\" />".(($_GET['form_sent']||$_POST['form_sent'])?"<input type=\"hidden\" name=\"form_sent\" value=\"1\" />":"")."<input type=\"hidden\" name=\"only_bookmark\" value=\"".$only_bookmark."\" /><span class=\"title\"><input class=\"error mail\" name=\"".$LDAP_MAIL_FIELD."\" value=\"".$Errors[$LDAP_MAIL_FIELD]."\"/><em>Новый e-mail не соответствует формату. <br/>Действующие значение: <b> \\1 </b><i></i></em></span>", "E-mail", 1, $Conditions2);
		else
			if($USE_DISPLAY_NAME)
				$table->addPregReplace("/([\w\W]{1,})/u", "<input class=\"text mail\" name=\"".$LDAP_MAIL_FIELD."\" value=\"\\1\"/>", "E-mail", 1, $Conditions2);
			else
				$table->addPregReplace("/([\w\W]{1,})/u", "<form action=\"".$_SERVER['PHP_SELF']."?menu_marker=si_staffedit&dn=".$dn."&FormSend=1&name=".$Name."\" method=\"POST\"><input type=\"hidden\" name=\"bookmark_attr\" value=\"".$bookmark_attr."\" /><input type=\"hidden\" name=\"bookmark_name\" value=\"".$BOOKMARK_NAME."\" />".(($_GET['form_sent']||$_POST['form_sent'])?"<input type=\"hidden\" name=\"form_sent\" value=\"1\" />":"")."<input type=\"hidden\" name=\"only_bookmark\" value=\"".$only_bookmark."\" /><input class=\"text mail\" name=\"".$LDAP_MAIL_FIELD."\" value=\"\\1\"/>", "E-mail", 1, $Conditions2);
				
		$table->addPregReplace("/value=\"x\"/u", "value=\"\"", "E-mail", 1, $Conditions2);
	//-------------------------------------------------------------------------------------------------	

	//Внутренний
	//-------------------------------------------------------------------------------------------------		
		$table->addPregReplace("/(".strtolower(preg_quote($Name)).")/u", "<u class='found'>\\1</u>", $L->l('intrenal_phone'), 1, $Conditions1);
		$table->addPregReplace("/^$/u", "x", $L->l('intrenal_phone'));
		if(@$Errors[$LDAP_INTERNAL_PHONE_FIELD])
			$table->addPregReplace("/([\w\W]{1,})/u", "<span class=\"title\"><input class=\"error othertelephone\" name=\"".$LDAP_INTERNAL_PHONE_FIELD."\" value=\"".$Errors[$LDAP_INTERNAL_PHONE_FIELD]."\"/><em>Новый внутренний номер не соответствует формату. <br/>Действующие значение: <b> \\1 </b><i></i></em></span>", $L->l('intrenal_phone'), 1, $Conditions2);
		else
			$table->addPregReplace("/([\w\W]{1,})/u", "<input class=\"text othertelephone\" name=\"".$LDAP_INTERNAL_PHONE_FIELD."\" value=\"\\1\"/>", $L->l('intrenal_phone'), 1, $Conditions2);
		$table->addPregReplace("/value=\"x\"/", "value=\"\"", $L->l('intrenal_phone'), 1, $Conditions2);
	//-------------------------------------------------------------------------------------------------

	//Городской
	//-------------------------------------------------------------------------------------------------		
		$table->addPregReplace("/^([0-9]{3})([0-9]{3})$/u", "\\1-\\2", $L->l('city_phone'), 1, $Conditions1);
		$table->addPregReplace("/(".preg_quote($Name).")/u", "<u class='found'>\\1</u>", $L->l('city_phone'), 1, $Conditions1);
		$table->addPregReplace("/^$/u", "x", $L->l('city_phone'));
		if(@$Errors[$LDAP_CITY_PHONE_FIELD])
			$table->addPregReplace("/([\w\W]{1,})/u", "<span class=\"title\"><input class=\"error telephonenumber\" name=\"".$LDAP_CITY_PHONE_FIELD."\" value=\"".$Errors[$LDAP_CITY_PHONE_FIELD]."\"/><em>Новый городской номер не соответствует формату. <br/>Действующие значение: <b> \\1 </b><i></i></em></span>", $L->l('city_phone'), 1, $Conditions2);
		else
			$table->addPregReplace("/([\w\W]{1,})/u", "<input class=\"text telephonenumber\" name=\"".$LDAP_CITY_PHONE_FIELD."\" value=\"\\1\"/>", $L->l('city_phone'), 1, $Conditions2);	
		$table->addPregReplace("/value=\"x\"/u", "value=\"\"", $L->l('city_phone'), 1, $Conditions2);
	//-------------------------------------------------------------------------------------------------	
		
	//Мобильный
	//-------------------------------------------------------------------------------------------------		
		$table->addPregReplace("/(".strtolower(preg_quote($Name)).")/u", "<u class='found'>\\1</u>", "Мобильный", 1, $Conditions1);
		$table->addPregReplace("/^$/u", "x", "Мобильный");
		if(@$Errors[$LDAP_CELL_PHONE_FIELD])
			$table->addPregReplace("/([\w\W]{1,})/u", "<span class=\"title\"><input class=\"error mobile\" name=\"".$LDAP_CELL_PHONE_FIELD."\" value=\"".$Errors[$LDAP_CELL_PHONE_FIELD]."\"/><em>Новый мобильный номер не соответствует формату. <br/>Действующие значение: <b> \\1 </b><i></i></em></span>", "Мобильный", 1, $Conditions2);
		else
			$table->addPregReplace("/([\w\W]{1,})/u", "<input class=\"text mobile\" name=\"".$LDAP_CELL_PHONE_FIELD."\" value=\"\\1\"/>", "Мобильный", 1, $Conditions2);
		$table->addPregReplace("/value=\"x\"/u", "value=\"\"", "Мобильный", 1, $Conditions2);
	//-------------------------------------------------------------------------------------------------	

	//Должность
	//-------------------------------------------------------------------------------------------------	
		$table->addPregReplace("/^\.\./u", "", "Должность");
		$table->addPregReplace("/^\./u", "", "Должность");
		$table->addPregReplace("/(".strtolower(preg_quote($Name)).")/u", "<u class='found'>\\1</u>", "Должность", 1, $Conditions1);
		$table->addPregReplace("/(".ucfirst(preg_quote($Name)).")/u", "<u class='found'>\\1</u>", "Должность", 1, $Conditions1);
		$table->addPregReplace("/^$/u", "x", "Должность");
		$table->addPregReplace("/([\w\W]{1,})/u", "<textarea class=\"position\" name=\"Title\">\\1</textarea>", "Должность", 1, $Conditions2);
		$table->addPregReplace("/<textarea class=\"position\" name=\"Title\">x<\/textarea>/u", "<textarea class=\"position\" name=\"Title\"></textarea>", "Должность", 1, $Conditions2);
	//-------------------------------------------------------------------------------------------------	


	//Кнопка
	//-------------------------------------------------------------------------------------------------		

		@$table->addPregReplace("/^(.*)$/u", "<a href=\"?menu_marker=si_staffedit&dn=\\1&sortcolumn=".$_GET['sortcolumn']."&sorttype=".$_GET['sorttype']."&name=".$Name."&bookmark_attr=".$bookmark_attr."&bookmark_name=".$BOOKMARK_NAME."&only_bookmark=".$only_bookmark.(($_GET['form_sent']||$_POST['form_sent'])?"&form_sent=1":"")."\"><img border=\"0\" src=\"./skins/".$CURRENT_SKIN."/images/vcard.png\" width=\"48\" height=\"33\" title=\"Редактировать\"/></a>", "Править", 1, $Conditions1);
		$table->addPregReplace("/^(.*)$/u", "<input type=\"image\" src=\"./skins/".$CURRENT_SKIN."/images/vcard_check.png\" width=\"48\" height=\"41\" title=\"Применить изменения\"/><div id=\"IDForScroll\" ></div></form>", "Править", 1, $Conditions2);
	//-------------------------------------------------------------------------------------------------	
		
	//Фото
	//-------------------------------------------------------------------------------------------------		
		$table->addPregReplace("/^([\w\W]{1,}$)/u", "Есть", "Фото", 1);
		$table->addPregReplace("/^$/u", "x", "Фото", 1);	
		$Conditions3[$LDAP_OBJECTCLASS_FIELD]['=']="user";
		$Conditions3[$LDAP_DISTINGUISHEDNAME_FIELD]['=']=$dn;
		$table->addPregReplace("/^Есть$/u", "<iframe allowtransparency=\"true\" src=\"./newwin.php?menu_marker=si_staff_add_photo&ButTitle=Изменить&dn=".$dn."\" frameborder=\"0\" scrolling=\"no\" width=\"70\" height=\"40\"></iframe>", "Фото", 1, $Conditions3);
		$table->addPregReplace("/^x$/u", "<iframe allowtransparency=\"true\" src=\"./newwin.php?menu_marker=si_staff_add_photo&ButTitle=Добавить&dn=".$dn."\" frameborder=\"0\" scrolling=\"no\" width=\"70\" height=\"40\"></iframe>", "Фото", 1, $Conditions3);
	//-------------------------------------------------------------------------------------------------	

		$cn=($Name=="*")?"*":"*".$Name."*";	

	// Делаем фильтр для выборки сотрудников нужных компаний
	//-------------------------------------------------------------------------------------------------------------
		$CompanyNameLdapFilter=Application::getCompanyNameLdapFilter();
	//-------------------------------------------------------------------------------------------------------------	
		
		$table->printTable($OU, "(&".$CompanyNameLdapFilter."(|(".$LDAP_OBJECTCLASS_FIELD."=user)(".$LDAP_OBJECTCLASS_FIELD."=contact))(|(".$LDAP_CN_FIELD."=".$cn.")(".$LDAP_MAIL_FIELD."=".$cn.")(".$LDAP_INTERNAL_PHONE_FIELD."=".$cn.")(".$LDAP_CITY_PHONE_FIELD."=".$cn.")(".$LDAP_CELL_PHONE_FIELD."=".$cn.")(".$LDAP_TITLE_FIELD."=".$cn.")(".$LDAP_DEPARTMENT_FIELD."=".$cn."))".$DIS_USERS_COND.")");
		
		if($dn)
			echo "
		<script type='text/javascript'>
		Calendar.setup({inputField:'Birthday', ifFormat:'%d.%m.%Y', button:'Birthday', firstDay:1, weekNumbers:false, showOthers:true});
		</script>
		";

		}
	}
else
	{
	echo"У вас нет доступа для редактирования справочника.";
	}

?>