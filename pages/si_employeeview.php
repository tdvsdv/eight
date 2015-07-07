<?php
$dn=($_GET['dn'])?$_GET['dn']:$_POST['dn'];
@$fio=($_GET['fio'])?$_GET['fio']:$_POST['fio'];

@$_GET['sortcolumn']=($_GET['sortcolumn'])?$_GET['sortcolumn']:"ФИО";
@$_GET['sorttype']=($_GET['sorttype'])?$_GET['sorttype']:"ASC";

$ldap=new LDAP($LDAPServer, $LDAPUser, $LDAPPassword);

if($fio)
	$dn=$ldap->getValue($OU, $LDAP_DISTINGUISHEDNAME_FIELD, "cn=".$fio);


if($DIRECT_PHOTO) 
	$Image=$ldap->getImage($dn, $GLOBALS['LDAP_PHOTO_FIELD']);
else
	{
	$Image=$GLOBALS['PHOTO_DIR']."/".md5($dn).".jpg";
	$Image=$ldap->getImage($dn, $GLOBALS['LDAP_PHOTO_FIELD'], $Image);
	}

echo"<table class=\"user\">";
echo"<tr>";
echo"<td width=\"1%\">";
if($Image)
	echo"<div class=\"photo\"><img src=\"".$Image."\"></div>";	
else
	echo"<div class=\"photo\"><img src=\"./skins/".$CURRENT_SKIN."/images/ldap/user.png\"></div>";
echo"</td>";
echo"<td>";

if($USE_DISPLAY_NAME)
	$Name=$ldap->getValue($dn, $DISPLAY_NAME_FIELD);
else
	$Name=$ldap->getValue($dn, "name");


$control=$ldap->getValue($dn, "useraccountcontrol");
$LockedCssClass= ((($control & 2)==2)||(($control & 2) == 16))?"locked":"";

$FIO=preg_replace("/^([ёA-zА-я-]+)[\s]{1}([ёA-zА-я-]+[\s]{1}[ёA-zА-я-]+)$/u", "<div class=\"surname_head ".$LockedCssClass."\">$1</div><div class=\"name ".$LockedCssClass."\">$2</div>", $Name);
$FIO=preg_replace("/^([ёA-zА-я-]+[\s]{1}[ёA-zА-я-]{1}.)[\s]{1}([ёA-zА-я-]+)$/u", "<div class=\"surname_head ".$LockedCssClass."\">$2</div><div class=\"name ".$LockedCssClass."\">$1</div>", $FIO);

echo $FIO;

if($SHOW_EVALUATION_PERIOD_MESSAGE && $LDAP_CREATED_DATE_FIELD)
	{
	$Created=$ldap->getValue($dn, $LDAP_CREATED_DATE_FIELD);	
	$CreatedUnixTime=Time::getTimeOfDMYHI($Created, $LDAP_CREATED_DATE_FORMAT);
	$NumWorkDays=round((Time::getOnlyDatePartFromTime(time())-Time::getOnlyDatePartFromTime($CreatedUnixTime))/(24*60*60));
	if($NumWorkDays<=$EVALUATION_PERIOD)
		echo "<h6 class=\"alarm\">Новый сотрудник</h6> &mdash; <small>работает в компании <big>".$L->ending($NumWorkDays, 'день', 'дня', 'дней')."</big></small>";
	}

$Department=$ldap->getValue($dn, $LDAP_DEPARTMENT_FIELD);
$Title= $ldap->getValue($dn, $LDAP_TITLE_FIELD);

if($Department)
	echo "<div class=\"position\"><nobr class=\"department\">".Staff::makeDepartment($Department)."</nobr> <br/><span class=\"position\">".Staff::makeTitle($Title)."</span></div>";

if($VACATION)
	{
	$e[0]=$ldap->getValue($dn, $LDAP_ST_DATE_VACATION_FIELD); $e[1]=$ldap->getValue($dn, $LDAP_END_DATE_VACATION_FIELD);

	if($e[0]&&$e[1])
		{
		$VacationState=Staff::getVacationState($e[0], $e[1]);
		if($VacationState == 0)
			$tag="del";
		else if($VacationState < 0)
			{ $tag="span"; }
		else
			$tag="span";
		}
	else
		$tag="span";
	}
else
	{
	$tag="span";
	}

if(!$HIDE_CITY_PHONE_FIELD)
	echo "<div class=\"phone\"><h6>".$L->l('city_phone').":</h6> <".$tag.">".Staff::makeCityPhone($ldap->getValue($dn, $LDAP_CITY_PHONE_FIELD))."</".$tag."></div>";

echo "<div class=\"otherphone\"><h6>".$L->l('intrenal_phone').":</h6> <".$tag.">".Staff::makeInternalPhone($ldap->getValue($dn, $LDAP_INTERNAL_PHONE_FIELD))."</".$tag."></div>";

if(!$HIDE_CELL_PHONE_FIELD)
	echo "<div class=\"otherphone\"><h6>".$L->l('cell_phone').":</h6> ".Staff::makeCellPhone($ldap->getValue($dn, $LDAP_CELL_PHONE_FIELD))."</div>";

if($HomePhone=$ldap->getValue($dn, $LDAP_HOMEPHONE_FIELD))
	echo "<div class=\"otherphone\"><h6>".$L->l('home_phone').":</h6> ".Staff::makeHomePhone($HomePhone)."</div>";

if(!$HIDE_ROOM_NUMBER)
	echo "<div class=\"otherphone\"><h6>".$L->l('room_number').":</h6> ".Staff::makePlainText($ldap->getValue($dn, $LDAP_ROOM_NUMBER_FIELD))."</div>";

echo "<div class=\"email\"><h6>E-mail:</h6> ".Staff::makeMailUrl($ldap->getValue($dn, $LDAP_MAIL_FIELD))."</div>";



$StDate=$ldap->getValue($dn, $LDAP_ST_DATE_VACATION_FIELD);
$EndDate=$ldap->getValue($dn, $LDAP_END_DATE_VACATION_FIELD);
Staff::printVacOnCurrentPage($StDate, $EndDate);

$DeputyDN=$ldap->getValue($dn, $LDAP_DEPUTY_FIELD);	
if($DeputyDN && $SHOW_DEPUTY && (Staff::checkInVacation($StDate, $EndDate) && $BIND_DEPUTY_AND_VACATION) || !$BIND_DEPUTY_AND_VACATION)
	{
	echo "<div class=\"employee birthday\">
		<h6>".$L->l('deputy_for_vacation_period').":</h6><br/>";

	echo Staff::makeDeputy($DeputyDN, $ldap->getValue($DeputyDN, $DISPLAY_NAME_FIELD));
	echo "</div>";
	}

$Birth=$ldap->getValue($dn, $LDAP_BIRTH_FIELD);

//День рождения
//-----------------------------------------------------------------------------
if($Birth)
{
	switch($BIRTH_DATE_FORMAT)
	{
		case 'yyyy-mm-dd':
		{
			$Date=explode("-", $Birth);
			$temp=$Date[0]; $Date[0]=$Date[2]; $Date[2]=$temp;
		} break;
		case 'dd.mm.yyyy':
		{
			$Date=explode(".", $Birth);
		} break;
		default: $Date=explode(".", $Birth);
	}

	$Jubilee="";
	if($SHOW_JUBILEE_INFO)
		{	
		if(!((date("Y")-$Date[2])%5)) $Jubilee="<div>".$L->l('round_date')."</div>";
		if(!((date("Y")-$Date[2])%10)) $Jubilee="<div>".$L->l('jubilee')."</div>";
		}
	echo"<div class=\"birthday\"><h6>".$L->l('birthday').":</h6> ".(int) $Date[0]." ".$MONTHS[(int) $Date[1]].". ".@$Jubilee."</div>";	
}
//-----------------------------------------------------------------------------

$ManDN=$ldap->getValue($dn, $LDAP_MANAGER_FIELD);	
if($ManDN)
{
echo "<div class=\"employee\"><h6>".$L->l('immediate_supervisor').":</h6><br>";
	if($USE_DISPLAY_NAME)
	{
		echo Staff::makeNameUrlFromDn($ManDN, $ldap->getValue($ManDN, $DISPLAY_NAME_FIELD));
	}
	else
		echo Staff::makeNameUrlFromDn($ManDN);
echo "</div>";
}

if (isset($Manager))
	echo $Manager;



echo"</td>";
echo"</tr>";

echo"<tr>";
echo"<td colspan='2'>";
echo"<div class=\"staff\" id=\"people\"><h6>Подчиненные:</h6></div>";
$table=new LDAPTable($LDAPServer, $LDAPUser, $LDAPPassword, false, false);

if($USE_DISPLAY_NAME)
	$table->addColumn($DISPLAY_NAME_FIELD.", distinguishedname", "ФИО", true, 0, false, "ad_def_full_name");
else	
	$table->addColumn("distinguishedname", "ФИО", true, 0, false, "ad_def_full_name");
$table->addColumn($LDAP_INTERNAL_PHONE_FIELD, $L->l('intrenal_phone'), true);
$table->addColumn("title", "Должность");

$table->addPregReplace("/^(.*)$/eu", "Staff::makeNameUrlFromDn('\\1')", "ФИО");	

$table->addPregReplace("/^\.\./u", "", "Должность");
$table->addPregReplace("/^\./u", "", "Должность");
$table->addPregReplace("/^(.*)$/eu", "Staff::makeInternalPhone('\\1')", $L->l('intrenal_phone'));

echo"<div id=\"people_table\">";

$table->printTable($OU, "(&(company=*)(manager=".LDAP::escapeFilterValue($dn).")".$DIS_USERS_COND.")");
echo"</div>";
echo"</td>";
echo"</tr>";
echo"</table>";
?>