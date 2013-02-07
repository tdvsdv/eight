<?php

abstract class Staff
{

	public static function showComputerName($Login)
		{
		if(in_array($Login, $GLOBALS['ADMIN_LOGINS']) && $GLOBALS['SHOW_COMPUTER_FIELD'])
			return true;
		else
			return false;
		}

	/*public static function makeUserLinkByLogin($Login)
		{
		if($GLOBALS['USE_DISPLAY_NAME'])
			$Attrs=array($GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD'], $GLOBALS['DISPLAY_NAME_FIELD']);
		else
			$Attrs=array($GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD']);
		$Dn_and_name=$GLOBALS['ldap']->getArray($GLOBALS['OU'], "(&(".$GLOBALS['LDAP_USERPRINCIPALNAME_FIELD']."=".$Login."))", $Attrs);

		return self::makeNameUrlFromDn($Dn_and_name[$GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD']][0], $Dn_and_name[$GLOBALS['DISPLAY_NAME_FIELD']][0]);
		}*/

	public static function makeNameUrlFromDn($DN, $Title="")
		{
		if($GLOBALS['USE_DISPLAY_NAME'])
			{
			$DN=preg_replace("/([ёA-zА-я-]+)[\s]{1}([ёA-zА-я-]+[\s]{1}[ёA-zА-я-]+)(CN.*)/u", "<a href=\"newwin.php?menu_marker=si_employeeview&dn=\\3\" data-lightview-type=\"iframe\" data-lightview-options=\"width: '80%', height: '100%', keyboard: {esc: true}, skin: 'light'\" class=\"lightview in_link\"><span class='surname'>\\1</span> \\2</a>", $Title.$DN);
			$DN=preg_replace("/([ёA-zА-я-]+[\s]{1}[ёA-zA-я]{1}.)[\s]{1}([ёA-zА-я-]+)(CN.*)/u", "<a href=\"newwin.php?menu_marker=si_employeeview&dn=\\3\" data-lightview-type=\"iframe\" data-lightview-options=\"width: '80%', height: '100%', keyboard: {esc: true}, skin: 'light'\" class=\"lightview in_link\"><span class='surname'>\\2</span> \\1</a>", $DN);	
			$DN=preg_replace("/([ёA-zA-я0-9№\s-]{1,})(CN.*)/u", "<a href=\"newwin.php?menu_marker=si_employeeview&dn=\\2\" data-lightview-type=\"iframe\" data-lightview-options=\"width: '80%', height: '100%', keyboard: {esc: true}, skin: 'light'\" class=\"lightview in_link\"><span class='surname'> \\1</span></a>", $DN);		
			$DN=preg_replace("/^CN=([ёA-zA-я0-9\s\.-]{1,})(.*)$/u", "<a href=\"newwin.php?menu_marker=si_employeeview&dn=\\0\" data-lightview-type=\"iframe\" data-lightview-options=\"width: '80%', height: '100%', keyboard: {esc: true}, skin: 'light'\" class=\"lightview in_link\"><span class='surname'> \\1</span></a>", $DN);		
			}
		else
			{
			$DN=preg_replace("/^[A-Za-z]+=*([ёА-яA-z0-9\s-.]+),[\S\s]+$/eu", "'<a href=\"newwin.php?menu_marker=si_employeeview&dn='.'\\0'.'\" data-lightview-type=\"iframe\" data-lightview-options=\"width: '80%', height: '100%', keyboard: {esc: true}, skin: 'light'\" class=\"lightview in_link\">___\\1</a>'", $DN);
			$DN=preg_replace("/___([ёA-zА-я-]+)[\s]{1}([ёA-zА-я-]+[\s]{1}[ёA-zА-я-]+)/u", "<span class='surname'>\\1</span> \\2", $DN);
			//Для формата Имя О. Фамилия
			$DN=preg_replace("/___([ёA-zА-я-]+[\s]{1}[ёA-zA-я]{1}.)[\s]{1}([ёA-zА-я-]+)/u", "<span class='surname'>\\2</span> \\1", $DN);	
			}	
		return $DN;
		}
		
	public static function makeMailUrl($Mail)
		{
		if($Mail)
			return preg_replace("/([A-z0-9_\.\-]{1,20}@[A-z0-9\.\-]{1,20}\.[A-z]{2,4})/u", "<a href='mailto:\\1' class='in_link'>\\1</a>", $Mail);
		else
			return "x";
		}
		

	public static function makeDeputy($DN, $Title='')
		{
		if($GLOBALS['USE_DISPLAY_NAME'])
			return self::makeNameUrlFromDn($DN, $Title);
		else
			return self::makeNameUrlFromDn($DN);
		}

	public static function printDeputyInList($DN, $Title='')
		{
		if($GLOBALS['SHOW_DEPUTY'] && $DN && $GLOBALS['SHOW_DEPUTY_IN_LISTS'])
			echo "<span class=\"unimportant\"> ".$GLOBALS['L']->l("deputy")." </span><span class=\"deputy\">".Staff::makeNameUrlFromDn($DN, $Title)."</span>";
		}

	// Функции форматирования телефонных номеров
	// ===============================================================================================================
	public static function makeInternalPhone($Val, $Link=true)
	{
		$phone_attr=get_phone_attr($Val);
		if (empty($Val)) return 'x';
		if($Link)
		{
			if(@$GLOBALS['FORMAT_INTERNAL_PHONE'])
			{		
				$Val="<a href=\"callto:".$phone_attr['clear_phone']."\" class=\"in_link int_phone\">".$phone_attr['format_phone']."</a>";
			}	
			else
				$Val="<a href=\"callto:".$Val."\" class=\"in_link int_phone\">".$Val."</a>";
		}
		else
			{
			if(@$GLOBALS['FORMAT_INTERNAL_PHONE'])
				$Val="<nobr>".$phone_attr['format_phone']."</nobr>";
			}
		//*********************************************
		return $Val;
	}
	// ---------------------------------------------------------------------------------------------------------------		
	public static function makeCityPhone($Val, $Link=true)
	{
		$phone_attr=get_phone_attr($Val);
		if (empty($Val)) return 'x';
		if($Link)
		{
			if($GLOBALS['FORMAT_CITY_PHONE'])
			{		
				if($GLOBALS['USE_PHONE_CODES_DESCRIPTION'] AND $phone_attr['provider_desc'])
					$phone_title="title=\"".$phone_attr['provider_desc']."\"";
				else
					$phone_title="title=\"\"";
				$Val="<a href=\"callto:".$phone_attr['clear_phone']."\" class=\"in_link cityphone\" ".$phone_title.">".$phone_attr['format_phone']."</a>";
			}
			else
				$Val="<a href=\"callto:".$Val."\" class=\"in_link cityphone\">".$Val."</a>";
		}
		else
			{
			if(@$GLOBALS['FORMAT_CITY_PHONE'])
				$Val="<nobr>".$phone_attr['format_phone']."</nobr>";
			}
		return $Val;
	}	
	// ---------------------------------------------------------------------------------------------------------------	
	public static function makeCellPhone($Val, $Link=true)
	{
		$phone_attr=get_phone_attr($Val);
		if (empty($Val)) return 'x';
		if($Link)
		{
			if($GLOBALS['FORMAT_CELL_PHONE'])
			{
				if($GLOBALS['USE_PHONE_CODES_DESCRIPTION'] AND $phone_attr['provider_desc'])
					$phone_title="title=\"".$phone_attr['provider_desc']."\"";
				@$Val="<a href=\"callto:".$phone_attr['clear_phone']."\" class=\"in_link cell_phone\" ".$phone_title.">".$phone_attr['format_phone']."</a>";
			}
			else
				$Val="<a href=\"callto:".$Val."\" class=\"in_link cell_phone\">".$Val."</a>";
		}
		else
			{
			if(@$GLOBALS['FORMAT_CELL_PHONE'])
				$Val="<nobr>".$phone_attr['format_phone']."</nobr>";
			}
		//*********************************************
		return $Val;
	}	
	// ---------------------------------------------------------------------------------------------------------------	
	public static function makeHomePhone($Val, $Link=true)
	{
		$phone_attr=get_phone_attr($Val);
		if (empty($Val)) return 'x';
		if($Link)
		{
			if($GLOBALS['FORMAT_HOME_PHONE'])
			{
				if($GLOBALS['USE_PHONE_CODES_DESCRIPTION'] AND $phone_attr['provider_desc'])
					$Val="<acronym title =\"".$phone_attr['provider_desc']."\"><a href=\"callto:".$phone_attr['clear_phone']."\" class=\"in_link homephone\">".$phone_attr['format_phone']."</a></acronym>";
				else
					$Val="<a href=\"callto:".$phone_attr['clear_phone']."\" class=\"in_link homephone\">".$phone_attr['format_phone']."</a>";
			}
			else
				$Val="<a href=\"callto:".$Val."\" class=\"in_link homephone\">".$Val."</a>";
		}
		else
			{
			if(@$GLOBALS['FORMAT_HOME_PHONE'])
				$Val="<nobr>".$phone_attr['format_phone']."</nobr>";
			}
		//*********************************************
		return $Val;
	}
	// ===============================================================================================================
		
	public static function makeComputerName($Val)
		{
		if($Val)
			return $Val;
		else
			return "x";	
		}

	public static function makeTitle($Val)
		{
		if($Val)
			return preg_replace('/(?:\"([^\"]+)\")/u', '&laquo;\\1&raquo;', $Val);
		else
			return "x";	
		}
		
	public static function makeDepartment($Val, $MakeAdd=false)
		{
		if($Val)
			{
			$return="<span class=\"dep_name\">".preg_replace('/(?:\"([^\"]+)\")/u', '&laquo;\\1&raquo;', str_replace("\\", " &rarr; ", $Val))."</span>";	
			if($MakeAdd)
				@$return.=$GLOBALS['DEP_ADD'][$Val];			
			return $return;
			}
		else
			return "x";	
		}

	public static function checkInVacation($StDate, $EndDate)
		{
		if($StDate&&$EndDate)
			{
			$time=time();
			if((Time::getTimeOfDMYHI($EndDate, $GLOBALS['VAC_DATE_FORMAT'])>=$time)&&(Time::getTimeOfDMYHI($StDate, $GLOBALS['VAC_DATE_FORMAT'])<=$time))
				return true;
			else
				return false;
			}
		else
			return false;
		}

	public static function getVacationState($StDate, $EndDate)
		{
		if($StDate&&$EndDate)
			{
			$end_time=Time::getTimeOfDMYHI($EndDate, $GLOBALS['VAC_DATE_FORMAT']);
			$start_time=Time::getTimeOfDMYHI($StDate, $GLOBALS['VAC_DATE_FORMAT']);
			$time=time();
			if(($end_time>=$time)&&($start_time<=$time))
				return 0;	// в отпуске
			else 
				{
				if($start_time>$time)
					return 1;	// отпуск еще предстоит
				else
					return -1;	// отпуск закончился
				}

			}
		}

	public static function checkShowVacOnCurrentPage($StDate, $EndDate)
		{
		if($StDate&&$EndDate)
			{
			$VacationState=self::getVacationState($StDate, $EndDate);
			if(
				(
				(($VacationState == 0) && $GLOBALS['SHOW_CURRENT_VAC'][$GLOBALS['menu_marker']]) 
				|| (($VacationState > 0) && $GLOBALS['SHOW_NEXT_VAC'][$GLOBALS['menu_marker']])
				|| (($VacationState < 0) && $GLOBALS['SHOW_PREV_VAC'][$GLOBALS['menu_marker']])
				) && $GLOBALS['VACATION']
			  )
				return true;
			else
				return false;	
			}
		}

	public static function printVacOnCurrentPage($StDate, $EndDate)
		{
		$VacationState=self::getVacationState($StDate, $EndDate);
		if(self::checkShowVacOnCurrentPage($StDate, $EndDate))
			{
			if($VacationState===0)
				{
				$class='alarm';
				$vac_title=$GLOBALS['L']->l("in_vacation_until");
				$vac_period=Time::getHandyDateOfDMYHI($EndDate, $GLOBALS['BIRTH_DATE_FORMAT']);
				if($GLOBALS['menu_marker']=='si_employeeview')
					{
					$vac_title="<h6 class=\"alarm\">".$GLOBALS['L']->l("in_vacation").":</h6>";
					$vac_period=Time::getHandyDateOfDMYHI($StDate, $GLOBALS['BIRTH_DATE_FORMAT'])." &mdash; ".Time::getHandyDateOfDMYHI($EndDate, $GLOBALS['BIRTH_DATE_FORMAT']);
					}
				}
			if($VacationState>0)
				{
				$class='next_vac';
				$vac_title="Ближайший отпуск: ";
				$vac_period=Time::getHandyDateOfDMYHI($StDate, $GLOBALS['BIRTH_DATE_FORMAT'])." &mdash; ".Time::getHandyDateOfDMYHI($EndDate, $GLOBALS['BIRTH_DATE_FORMAT']);		
				if($GLOBALS['menu_marker']=='si_employeeview')
					$vac_title="<h6 class=\"".$class."\">".$vac_title."</h6>";					
				}
			if($VacationState<0)
				{
				$class='prev_vac';
				$vac_title="Прошедший отпуск: ";
				$vac_period=Time::getHandyDateOfDMYHI($StDate, $GLOBALS['BIRTH_DATE_FORMAT'])." &mdash; ".Time::getHandyDateOfDMYHI($EndDate, $GLOBALS['BIRTH_DATE_FORMAT']);
				if($GLOBALS['menu_marker']=='si_employeeview')
					$vac_title="<h6 class=\"".$class."\">".$vac_title."</h6>";	
				}
						

			if($GLOBALS['menu_marker']=='si_alph_staff_list' || $GLOBALS['menu_marker']=='si_dep_staff_list' || $GLOBALS['menu_marker']=='si_stafflist' )
				echo"<span class=\"".$class."\">".$vac_title.$vac_period."</span>";
			if($GLOBALS['menu_marker']=='si_employeeview')
				echo"<div class=\"birthday\">".$vac_title.$vac_period."</div>";
			}
		}

	public static function makeAvatar($dn)
	{
		if($GLOBALS['DIRECT_PHOTO'])
			$Image=$GLOBALS['ldap']->getImage($dn, $GLOBALS['LDAP_AVATAR_FIELD']);
		else
			$Image=$GLOBALS['ldap']->getImage($dn, $GLOBALS['LDAP_AVATAR_FIELD'], $GLOBALS['PHOTO_DIR']."/avatar_".md5($dn).".jpg");

		if($Image)
			return "<div class=\"avatar\"><img src=\"".$Image."\" height=\"".$GLOBALS['THUMBNAIL_PHOTO_MAX_HEIGHT']."\" width=\"".$GLOBALS['THUMBNAIL_PHOTO_MAX_WIDTH']."\" /></div>";	
		else
			{
			if($GLOBALS['SHOW_EMPTY_AVATAR'])
				return "<div class=\"avatar\"><img src=\"./skins/".$GLOBALS['CURRENT_SKIN']."/images/user_avatar.png\" alt=\"user avatar\" height=\"32\" width=\"32\" /></div>";	
			}
	}


	public static function getNumStaffTableColls()
		{
		$num=5;
		
		if($GLOBALS['menu_marker']=='si_export_pdf_alphabet' || $GLOBALS['menu_marker']=='si_export_pdf_department')
			$num=5;
		if($GLOBALS['menu_marker']=='si_alph_staff_list' || $GLOBALS['menu_marker']=='si_dep_staff_list')
			{
			$num=5;
			if( self::showComputerName($GLOBALS['Login']))
				$num++;		
			if($GLOBALS['FAVOURITE_CONTACTS'] && !empty($_COOKIE['dn']))
				$num++;	

			}

		if(! $GLOBALS['HIDE_CITY_PHONE_FIELD'])
			$num++;
		if(! $GLOBALS['HIDE_CELL_PHONE_FIELD'])
			$num++;

		if(empty($_COOKIE['dn']) && $GLOBALS['ENABLE_DANGEROUS_AUTH'])
			$num++;
		if($GLOBALS['XMPP_ENABLE'] && $GLOBALS['XMPP_MESSAGE_LISTS_ENABLE'] && !empty($_COOKIE['dn']))
			$num++;

		return $num;

		}
	public static function highlightSearchResult($Str, $SearchStr)
		{
		//echo "/((?:<[^>]+>)*[^<]*)(".$SearchStr.")([^<]*(?:<[^>]+>)*)/";
		$Str=preg_replace ("/(>[^>]*)(".$SearchStr.")([^<]*<)/i", "\\1<span class=\"found\">\\2</span>\\3", $Str);
		$Str=preg_replace ("/^([^>]*)(".$SearchStr.")([^<]*)$/i", "\\1<span class=\"found\">\\2</span>\\3", $Str);
		return $Str;
		}

	//Выводит строку таблицы с информацией по определенному сотруднику
	public static function printUserTableRow($Staff, $key, $Vars)
		{
		$StDate=$Staff[$GLOBALS['LDAP_ST_DATE_VACATION_FIELD']][$key]; 
		$EndDate=$Staff[$GLOBALS['LDAP_END_DATE_VACATION_FIELD']][$key];
		$VacationState=self::getVacationState($StDate, $EndDate);	// проверка: в каком состоянии отпуск?
		($VacationState===0) ? $tag="del" : $tag="span";	// в зависимости от этого применяем разные стили
				
		// Строки таблицы
		//-------------------------------------------------------------------------------------------------------------
		$data_parent_id=($Vars['data_parent_id']) ? "data-parent-id=".md5($Staff[$GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD']][$key]) : '';
		$id=($Vars['id']) ? "id=".md5($Staff[$GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD']][$key]) : '';
		echo"<tr class=\"".$Vars['row_css']."\" ".$id." ".$data_parent_id.">";
		echo "<td>";
		self::printVacOnCurrentPage($StDate, $EndDate);		
		if($GLOBALS['THUMBNAIL_PHOTO_VIS'])	
			echo self::makeAvatar($Staff[$GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD']][$key]);
		if( (self::checkInVacation($StDate, $EndDate) && $GLOBALS['BIND_DEPUTY_AND_VACATION']) || !$GLOBALS['BIND_DEPUTY_AND_VACATION'])	//
			self::printDeputyInList($Staff[$GLOBALS['LDAP_DEPUTY_FIELD']][$key], $Vars['ldap_conection']->getValue($Staff[$GLOBALS['LDAP_DEPUTY_FIELD']][$key], $GLOBALS['DISPLAY_NAME_FIELD']));

		if(empty($Vars['search_str'])) //Если не велся поиск, то не подсвечивавем результаты
			echo self::makeNameUrlFromDn($Staff[$GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD']][$key], $Staff[$Vars['display_name']][$key]); //Делаем ссылку на полную информацию о сотруднике
		else
			echo self::highlightSearchResult(self::makeNameUrlFromDn($Staff[$GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD']][$key], $Staff[$Vars['display_name']][$key]), $Vars['search_str']); //Делаем ссылку на полную информацию о сотруднике

		echo "</td>";
		if(empty($Vars['search_str'])) //Если не велся поиск, то не подсвечивавем результаты
			echo "<td>".self::makeTitle($Staff[$GLOBALS['LDAP_TITLE_FIELD']][$key])."</td>"; //Выводим должность
		else
			echo "<td>".self::highlightSearchResult(self::makeTitle($Staff[$GLOBALS['LDAP_TITLE_FIELD']][$key]), $Vars['search_str'])."</td>"; //Выводим должность

		if(empty($Vars['search_str'])) //Если не велся поиск, то не подсвечивавем результаты
			echo "<td>".self::makeMailUrl($Staff[$GLOBALS['LDAP_MAIL_FIELD']][$key])."</td>"; //Выводим почту
		else
			echo "<td>".self::highlightSearchResult(self::makeMailUrl($Staff[$GLOBALS['LDAP_MAIL_FIELD']][$key]), $Vars['search_str'])."</td>"; 


		echo "<td><".$tag.">".self::makeInternalPhone($Staff[$GLOBALS['LDAP_INTERNAL_PHONE_FIELD']][$key])."</".$tag."></td>"; //Выводим внутренний
		if(!$GLOBALS['HIDE_CITY_PHONE_FIELD'])
			{
			echo "<td><".$tag.">".self::makeCityPhone($Staff[$GLOBALS['LDAP_CITY_PHONE_FIELD']][$key])."</".$tag."></td>"; //Выводим городской
			}

		if(!$GLOBALS['HIDE_CELL_PHONE_FIELD'])
			{
			if(empty($Vars['search_str'])) //Если не велся поиск, то не подсвечивавем результаты	
				echo "<td>".self::makeCellPhone($Staff[$GLOBALS['LDAP_CELL_PHONE_FIELD']][$key])."</td>"; //Выводим сотовый
			else
				echo "<td>".self::highlightSearchResult(self::makeCellPhone($Staff[$GLOBALS['LDAP_CELL_PHONE_FIELD']][$key]), $Vars['search_str'])."</td>"; //Делаем ссылку на полную информацию о сотруднике
			}

		if(self::showComputerName($Vars['current_login'])) //Если сотрудник является администратором справочника
			{
			if(empty($Vars['search_str'])) //Если не велся поиск, то не подсвечивавем результаты	
				echo "<td>".self::makeComputerName($Staff[$GLOBALS['LDAP_COMPUTER_FIELD']][$key])."</td>"; //Выводим имя компьютера
			else
				echo "<td>".self::highlightSearchResult(self::makeComputerName($Staff[$GLOBALS['LDAP_COMPUTER_FIELD']][$key]), $Vars['search_str'])."</td>"; //Выводим имя компьютера
			}
		if( @$Staff[$GLOBALS['LDAP_CREATED_DATE_FIELD']][$key] ) 
			echo "<td>".Time::getHandyDateOfDMYHI($Staff[$GLOBALS['LDAP_CREATED_DATE_FIELD']][$key], $GLOBALS['LDAP_CREATED_DATE_FORMAT'])."</td>"; //Выводим дату принятия на работу

		if($GLOBALS['XMPP_ENABLE'] && $GLOBALS['XMPP_MESSAGE_LISTS_ENABLE'] && $_COOKIE['dn'])
			{
			if(is_array($_COOKIE['xmpp_list']) && in_array($Staff[$GLOBALS['LDAP_USERPRINCIPALNAME_FIELD']][$key], $_COOKIE['xmpp_list']))
				$xmpp_link_class="in_xmpp_list";
			else
				$xmpp_link_class='out_xmpp_list';

			echo "<td>
				  <a href=\"#\" class=\"add_xmpp_list ".$xmpp_link_class." in_link\" title=\"".$GLOBALS['L']->l("add_contact_to_xmpp_list")."\" data-login=".$Staff[$GLOBALS['LDAP_USERPRINCIPALNAME_FIELD']][$key]."></a>
				  </td>"; //Выводим иконку добавления сотрудника в группу рассылки
			}
		if($GLOBALS['FAVOURITE_CONTACTS'] && $_COOKIE['dn'])
			{
			if(is_array($Vars['favourite_dns']))
				$favourite_link_class=(in_array($Staff[$GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD']][$key], $Vars['favourite_dns'])) ? 'fav_true' : 'fav_false';
			else
				$favourite_link_class='fav_false';
			echo "<td>
				  <a href=\"javascript: F();\" class=\"favourite ".$favourite_link_class." in_link\" title=\"Добавить контакт в избранные.\"></a>
				  <div class=\"hidden\">
				  <div class=\"favourite_user_dn\">".$Staff[$GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD']][$key]."</div>
				  </div>
				  </td>";
			}

		if(empty($_COOKIE['dn']) && $GLOBALS['ENABLE_DANGEROUS_AUTH'])
			{
			echo "<td><div><a href=\"\" class=\"is_it_you window in_link\">Это я!</a></div><div class=\"window hidden\">".self::getAuthForm(md5($Staff[$GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD']][$key]), $Staff[$GLOBALS['LDAP_DISTINGUISHEDNAME_FIELD']][$key])."</div></td>";
			}

		echo"</tr>";
		//-------------------------------------------------------------------------------------------------------------

		}

		public static function getAuthForm($id, $dn)
			{
			if((! empty($_POST['auth_form_id'])) && $id == $_POST['auth_form_id'])
				{$form_sent_class='auth_form_sent'; $password_class="error";}
			else
				{$form_sent_class=''; $password_class="";}

			$Form="<form method=\"POST\" class=\"".$form_sent_class."\" action=\"".$_SERVER['PHP_SELF']."\">";
			$Form.="<label for=\"password_".$id."\">Введите пароль</label><br/>";
			$Form.="<input type=\"password\" class=\"password ".$password_class."\" id=\"password_".$id."\" name=\"password\"/><br/>";

			$Form.=Application::getHiddenFieldForForm();
			$Form.="<input type=\"hidden\" name=\"dn\" value=\"".$dn."\">";
			$Form.="<input type=\"hidden\" name=\"auth_form_id\" value=\"".$id."\">";

			$Form.="<input type=\"submit\" name=\"BItSMe\" value=\"Докажите\">";
			$Form.="</form>";
			return $Form;
			}
}

abstract class Application
	{

	public static function getHiddenFieldForForm()
		{
		$HiddenFields='';
		foreach($GLOBALS['CurrentVars'] AS $key=>$value)
			{
			if(! empty($value))
				$HiddenFields.="<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\">";
			}
		return $HiddenFields;
		}

	public static function getSearchFilter($SearchStr, $LdapAttr)
		{
		$SearchStr=LDAP::escapeFilterValue($SearchStr);
		$Filter='(|';
		foreach($LdapAttr AS $value)
			{
			$Filter.="(".$value."=*".$SearchStr."*)";
			}
		$Filter.=")";
		return str_replace("***", "*", $Filter);
		}

	public static function getCollTitle($Title='', $Attr=array())
		{
		$th_css_class='';
		$th_content=$Title;


		if(is_array(@$Attr['sort'])) //Если по полю дожна позволятся сортировка
			{
			$th_css_class.=" sort";

			$urls['menu_marker']=$GLOBALS['menu_marker'];
			$urls['sort_field']=$Attr['sort']['field'];

			if($Attr['sort']['field'] == $Attr['sort']['sorted_field'])
				{
				if(empty($Attr['sort']['order']))
					{
					$urls['sort_order']='desc';
					}
				else
					{
					if($Attr['sort']['order'] == 'asc')
						$urls['sort_order']='desc';
					if($Attr['sort']['order'] == 'desc')
						$urls['sort_order']='asc';
					}
				}
			else
				{
				$urls['sort_order']='asc';
				}

			if(is_array($Attr['sort']['url_vars']))
				$urls=array_merge($urls, $Attr['sort']['url_vars']);

			$th_content="<a href=\"".$_SERVER['PHP_SELF']."?".http_build_query($urls)."\" class=\"no_line\"><span>".$Title."</span></a>";
			if((! empty($Attr['sort']['order'])) && $Attr['sort']['field'] == $Attr['sort']['sorted_field'])
				$th_css_class.=" ".$Attr['sort']['order'];
			}

		$th="<th class=\"".$th_css_class."\">";
		$th.=$th_content;
		$th.="</th>";

		return $th;
		}

	public static function getCompanyNameLdapFilter()
		{
			$bookmark_name=LDAP::escapeFilterValue($GLOBALS['BOOKMARK_NAME']);
			$bookmark_attr=$GLOBALS['bookmark_attr'];

			if(($bookmark_name=="*") || ( (@$_POST['form_sent']) && (@!$GLOBALS['only_bookmark']) ) )
				{

				foreach($GLOBALS['BOOKMARK_NAMES'] AS $key=>$value)
					{		
					$bookmark_names=LDAP::escapeFilterValue(array_keys($value));		

					if($GLOBALS['BOOKMARK_NAME_EXACT_FIT'][$bookmark_attr])
						$filters[]="|(".$key."=".implode(")(".$key."=", $bookmark_names).")";
					else
						{
						$filter="|(".$key."=*".implode("*)(".$key."=*", $bookmark_names)."*)";
						$filters[]=str_replace("***", "*", $filter);
						}
					}
				$filter="(&(".implode(")(", $filters)."))";
				}
			else
				{
				if($GLOBALS['BOOKMARK_NAME_EXACT_FIT'][$bookmark_attr])
					$filter="(".$bookmark_attr."=".$bookmark_name.")";
				else
					$filter="(".$bookmark_attr."=*".$bookmark_name."*)";
				}

		//echo $filter;
		return $filter;
		}

	public static function makeLdapConfigAttrLowercase()
		{
		foreach($GLOBALS AS $key => $value)
			{
			if(preg_match("/^LDAP_[A-Z_]{1,}_FIELD$/", $key))
				$GLOBALS[$key]=mb_strtolower($value);
			}		
		}

	public static function makeWindow($Links, $NumPosInCol=3)
		{
		$Window="<div class=\"tab\"><a href=\"\" class=\"in_link window\"></a></div>";
		$Window.="<div class=\"window hidden\">";
		$i=0;
		foreach($Links AS $key => $value)
			{
			if( !($i%$NumPosInCol) && $i!=0)
				$Window.="</ul><ul>";
			if( !($i%$NumPosInCol) && $i==0)
				$Window.="<ul>";			
			$Window.="<li>".$value."</li>";
			$i++;
			}
		$Window.="</ul></div>";
		return $Window;
		}

	//Возвращает массив
	//первый элемент 'bookmark' - массив со ссылками вкладок, которые должны показаться в данной ситуации
	//второй элемент 'window'- массив ссылок для скрытого всплывающего окна
	public static function getBookMarkLinks($bookmark_attr, $class='')
		{
		if ( array_key_exists($bookmark_attr, $GLOBALS['BOOKMARK_MAX_NUM_ITEMS']) )
			$max_items=$GLOBALS['BOOKMARK_MAX_NUM_ITEMS'][$bookmark_attr]; //Сколько вкладок максимум показывать по данному атрибуту
		else 
			$max_items=0;
		$bookmark_names=$GLOBALS['BOOKMARK_NAMES']; // Массив всех вкладок

		$keys=array_keys($bookmark_names[$bookmark_attr]); //Все значения для поиска для данного атрибута
		$sizeof=sizeof($keys);
		$NumBookmaks=sizeof($bookmark_names[$bookmark_attr]);

		$select_index=array_search($GLOBALS['bookmark_name'], $keys); //Порядковый номер выбраной сейчас вкладки

		if(! $max_items) //Если в конфиге не задано числа максимально показываемых вкладок, то показывать все!
			{
			$start=0; $end=$NumBookmaks-1;
			}
		else
			{
			$delta=$select_index-$max_items+1;
			if($select_index===false) //Ессли в данной группе нет выбраной вкладке
				{
				$start=0; $end=$max_items-1;
				}
			else
				{
				if($delta<0)
					{
					$start=0; $end=$select_index+abs($delta);
					}
				else
					{
					$start=$delta; $end=$select_index;
					}
				}	
			$end = ($end>$NumBookmaks-1) ? $NumBookmaks-1 : $end;			
			}
		$BookMarksLinks=array();
		for($i=$start; $i<=$end; $i++)
			{
			if($keys[$i]==$GLOBALS['bookmark_name'])
				$BookMarksLinks[]="<div class=\"sel tab ".$class."\">".$bookmark_names[$bookmark_attr][$keys[$i]]."</div>";
			else
				$BookMarksLinks[]="<div class=\"tab ".$class."\"><a href=\"".$_SERVER['PHP_SELF']."?bookmark_name=".$keys[$i]."&bookmark_attr=".$bookmark_attr."&menu_marker=".$GLOBALS['menu_marker']."\">".$bookmark_names[$bookmark_attr][$keys[$i]]."</a></div>";
			$class='';
			}
		$i=0;
		$WindowsLinks='';
		foreach($bookmark_names[$bookmark_attr] AS $key=>$value)
			{
			if($i<$start || $i>$end)
				$WindowsLinks[]="<a href=\"".$_SERVER['PHP_SELF']."?bookmark_name=".$key."&bookmark_attr=".$bookmark_attr."&menu_marker=".$GLOBALS['menu_marker']."\">".$value."</a>";
			$i++;
			}

		return array('bookmark' => $BookMarksLinks, 'window' => $WindowsLinks);
		}


	}
	
abstract class Alphabet
	{
	public static function printGeneralLetters()
		{
		echo "<fieldset id=\"move_to_letter\">
		<legend>".$GLOBALS['L']->l('fast_move_by_first_letter_of_name')."</legend>";
		$i=0;
		foreach($GLOBALS['Alphabet'] AS $key=>$value)
			{
			if(!($i%$GLOBALS['ALPH_ITEM_IN_LINE'])&&($i!=0))
				echo"<br>";
			echo"<a href=\"#\" class=\"letter in_link\">".mb_strtoupper($value)."</a>";
			$i++;
			}
		echo "</fieldset>";
		}
	}

?>