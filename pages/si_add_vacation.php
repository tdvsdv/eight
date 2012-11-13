<?php
require_once("../libs/require_once.php");
if($Valid)
	{
	if($_POST['vac_from'])
		{
		if($_POST['vac_to'])
			{
			if(Time::checkDate($_POST['vac_from']))
				{
				if(Time::checkDate($_POST['vac_to']))
					{
					if(Time::getTimeOfDMYHI($_POST['vac_from'])>=time())
						{
						if(Time::getTimeOfDMYHI($_POST['vac_from'])<=Time::getTimeOfDMYHI($_POST['vac_to']))
							{
							$ldap->ldap_modify($_COOKIE['dn'], array($LDAP_ST_DATE_VACATION_FIELD => Time::modifyDateFormat($_POST['vac_from'], "dd.mm.yyyy", $VAC_DATE_FORMAT) ));
							$ldap->ldap_modify($_COOKIE['dn'], array($LDAP_END_DATE_VACATION_FIELD => Time::modifyDateFormat($_POST['vac_to'], "dd.mm.yyyy", $VAC_DATE_FORMAT)));
							echo"{\"success\": \"true\", \"dn\": \"".Time::getTimeOfDMYHI($_POST['vac_from'])."\"}";
							}
						else
							echo"{\"success\": \"false\", \"field\": \"vac_to\", \"answer\": \"Дата окончания отпуска должна быть позже даты начала.\"}";
						}
					else
						echo"{\"success\": \"false\", \"field\": \"vac_from\", \"answer\": \"Дата начала планируемого отпуска должна быть позже текущей\"}";	
					}
				else
					echo"{\"success\": \"false\", \"field\": \"vac_to\", \"answer\": \"Дата не соответствует формату\"}";					
				}
			else
				echo"{\"success\": \"false\", \"field\": \"vac_from\", \"answer\": \"Дата не соответствует формату\"}";
			}
		else
			echo"{\"success\": \"false\", \"field\": \"vac_to\", \"answer\": \"Не заполнена дата окончания отпуска\"}";			
		}
	else
		echo"{\"success\": \"false\", \"field\": \"vac_from\", \"answer\": \"Не заполнена дата начала отпуска \"}";
		
	}

?>