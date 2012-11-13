<?php
if($_COOKIE['dn'])
	{
	if($WhoAreYou!==false)
		{
		echo "<fieldset class=\"whoareyou\">";
		echo"<legend>".$WhoAreYou."</legend>";

		echo"<ul>";
		if($Login=$ldap->getValue($_COOKIE['dn'], "userprincipalname"))
			{
			if(in_array($Login, $ADMIN_LOGINS))
				echo"<li><a href=\"".$_SERVER['PHP_SELF']."?menu_marker=si_staffedit\">Администрирование</a></li>";
			}
		
		if($vac_from=$ldap->getValue($_COOKIE['dn'], $LDAP_ST_DATE_VACATION_FIELD))
			$vac_from=Time::modifyDateFormat($vac_from, $VAC_DATE_FORMAT, 'dd.mm.yyyy');
		if($vac_to=$ldap->getValue($_COOKIE['dn'], $LDAP_ST_DATE_VACATION_FIELD))
			$vac_to=Time::modifyDateFormat($vac_to, $VAC_DATE_FORMAT, 'dd.mm.yyyy');
		
		echo"<li><a href=\"newwin.php?menu_marker=si_employeeview&dn=".$_COOKIE['dn']."\" data-lightview-type=\"iframe\" data-lightview-options=\"width: '80%', height: '100%', skin: 'light'\" class=\"lightview\">Профиль</a></li>";
		
		if($VACATION)
			{
			echo"<li><span id=\"Vac\">Планируемый отпуск <big><big>&rarr;</big></big> <span class=\"\">
			<input type=\"text\" name=\"vac_from\" id=\"vac_from\" class=\"date\" value=\"".$vac_from."\"/>
			<em><i></i></em></span> &mdash; <span class=\"\">
			<input type=\"text\" name=\"vac_to\" id=\"vac_to\" class=\"date\" value=\"".$vac_to."\" />
			<em><i></i></em></span>
			<img  id=\"vac_apply\" src=\"./skins/".$CURRENT_SKIN."/images/true24.png\" width=\"24\" height=\"24\"/><img id=\"vac_loader\" class=\"hidden\" src=\"./skins/".$CURRENT_SKIN."/images/load.gif\" width=\"16\" height=\"16\"/></span>
			</li>
			<script type='text/javascript'>
			Calendar.setup({inputField:'vac_from', ifFormat:'%d.%m.%Y', button:'vac_from', firstDay:1, weekNumbers:false, showOthers:true});
			Calendar.setup({inputField: 'vac_to', ifFormat: '%d.%m.%Y', button: 'vac_to', firstDay:1, weekNumbers:false, showOthers:true});
			</script>
			";
			
			if($VAC_CLAIM_ALARM&&$vac_from&&$vac_to)
				{
				if(((Time::getTimeOfDMYHI($vac_from)-$VAC_CLAIM_ALARM_DAYES_FROM*24*60*60)<=time())&&((Time::getTimeOfDMYHI($vac_to)-$VAC_CLAIM_ALARM_DAYES_TO*24*60*60)>=time()))
					echo"<li><a href=\"newwin.php?menu_marker=si_print_vacation_claim&dn=".$_COOKIE['dn']."\" title=\":: :: width: 900, height: 700\" class=\"lightview alert\">Заявление на отпуск</a></li>";
				}
			}
			
			

		if(@!$_SERVER['REMOTE_USER'])
			echo"<li><a href=\"".$_SERVER['PHP_SELF']."?iamnot=1\" title=\"Нет! \">Выйти</a></li>";		
		echo"</ul></fieldset>";	
		}
	}
?>