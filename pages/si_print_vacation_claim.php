<?php
$dn=($_POST['dn'])?$_POST['dn']:$_GET['dn'];
$Sn=$ldap->getValue($dn, $LDAP_SN_FIELD);
$Title=mb_strtolower($ldap->getValue($dn, $LDAP_TITLE_FIELD), 'UTF-8');
$Initials=$ldap->getValue($dn, $LDAP_INITIALS_FIELD);
$V=explode(" - ", $ldap->getValue($dn, $LDAP_VACATION_FIELD));

$DirectorSn=$ldap->getValue($OU, $LDAP_SN_FIELD, "(".$LDAP_TITLE_FIELD."=".$DIRECTOR_FULL_TITLE.")");
$DirectorDn=$ldap->getValue($OU, $LDAP_DISTINGUISHEDNAME_FIELD, "(".$LDAP_TITLE_FIELD."=".$DIRECTOR_FULL_TITLE.")");
$DirectorInitials=$ldap->getValue($OU, $LDAP_INITIALS_FIELD, "(".$LDAP_TITLE_FIELD."=".$DIRECTOR_FULL_TITLE.")");
$DirectorCompany=str_replace("\"", "&laquo;&raquo;", $ldap->getValue($OU, $LDAP_COMPANY_FIELD, "(".$LDAP_TITLE_FIELD."=".$DIRECTOR_FULL_TITLE.")"));

$DirectorData=$ldap->getValue($OU, $LDAP_DATA_FIELD, "(".$LDAP_TITLE_FIELD."=".$DIRECTOR_FULL_TITLE.")", true);

$DD=json_decode($DirectorData, true);

if($DD['who_title'])
	$DirectorTitle=iconv($GLOBALS['CHARSET_DATA'], $GLOBALS['CHARSET_APP'], $DD['who_title']);
else
	$DirectorTitle=$DIRECTOR_FULL_TITLE;
	
if($DD['company'])
	$DirectorCompany=iconv($GLOBALS['CHARSET_DATA'], $GLOBALS['CHARSET_APP'], $DD['company']);
else
	$DirectorCompany=$DirectorCompany;	
	
if($DD['who'])
	$Who=iconv($GLOBALS['CHARSET_DATA'], $GLOBALS['CHARSET_APP'], $DD['who']);
else
	$Who=$DirectorInitials." ".$DirectorSn;	
	
$Data=$ldap->getValue($dn, $LDAP_DATA_FIELD, false, true);
$DD=json_decode($Data, true);

if($DD['from_title'])
	$Title=iconv($GLOBALS['CHARSET_DATA'], $GLOBALS['CHARSET_APP'], $DD['from_title']);

if($DD['from'])
	$From=iconv($GLOBALS['CHARSET_DATA'], $GLOBALS['CHARSET_APP'], $DD['from']);
else
	$From=$Initials." ".$Sn;	
	
if($DD['sign'])
	$Sign=iconv($GLOBALS['CHARSET_DATA'], $GLOBALS['CHARSET_APP'], $DD['sign']);
else
	$Sign=$Initials." ".$Sn;

if($DD['vacation_claim_text'])
	$Text=iconv($GLOBALS['CHARSET_DATA'], $GLOBALS['CHARSET_APP'], $DD['vacation_claim_text']);
	
?>

<div id="claim">
<div id="claim_head">
<div>
<span class="editing"><?php echo $DirectorTitle ?></span> <span class="no_print edit"><a href="javascript:GF();"><Изменить></a><a class="hidden" href="javascript: GF();"><Применить></a><span class="attribute hidden">who_title</span><span class="dn hidden"><?php echo $DirectorDn; ?></span><img class="hidden loader" src="./images/load.gif" width="16" height="16"/></span> <span class="editing"><?php echo $DirectorCompany ?></span> <span class="no_print edit"><a href="javascript: GF();"><Изменить></a><a class="hidden" href="javascript: GF();"><Применить></a><span class="attribute hidden">company</span><span class="dn hidden"><?php echo $DirectorDn; ?></span><img class="hidden loader" src="./images/load.gif" width="16" height="16"/></span><br />
<span class="editing"><?php echo $Who; ?></span> <span class="no_print edit"><a href="javascript: GF();"><Изменить></a><a class="hidden" href="javascript: GF();"><Применить></a><span class="attribute hidden">who</span><span class="dn hidden"><?php echo $DirectorDn; ?></span><img class="hidden loader" src="./images/load.gif" width="16" height="16"/></span> <br />
от <span class="editing"><?php echo $Title; ?></span> <span class="no_print edit"><a href="javascript: GF();"><Изменить></a><a class="hidden" href="javascript: GF();"><Применить></a><span class="attribute hidden">from_title</span><span class="dn hidden"><?php echo $dn; ?></span><img class="hidden loader" src="./images/load.gif" width="16" height="16"/></span><br />
<span class="editing"><?php echo $From; ?></span> <span class="no_print edit"><a href="javascript: GF();"><Изменить></a><a class="hidden" href="javascript: GF();"><Применить></a><span class="attribute hidden">from</span><span class="dn hidden"><?php echo $dn; ?></span><img class="hidden loader" src="./images/load.gif" width="16" height="16"/></span> <br />
</div>
</div>
<br/><br/><br/><br/><br/><br/><br/>
<div id="claim_title">Заявление</div>
<br/><br/>
<div id="claim_body">
<span class="editing">
Прошу предоставить мне часть очередного отпуска с <?php echo $V[0]; ?> по <?php echo $V[1]; ?> включительно.
</span>
<span class="no_print edit textarea"><a href="javascript: GF();"><Изменить></a><a class="hidden" href="javascript: GF();"><Применить></a><span class="attribute hidden">vacation_claim_text</span><span class="dn hidden"><?php echo $dn; ?></span><img class="hidden loader" src="./images/load.gif" width="16" height="16"/></span>
<div class="no_print links">

<!-- Варианты заявлений -->
<a href="javascript: GF();" title="Прошу предоставить мне часть очередного отпуска с <?php echo $V[0]; ?> по <?php echo $V[1]; ?> включительно и оказать материальную помощь к отпуску в соответствии с Положением об оплате."> <С материальной помощью> </a>
<a href="javascript: GF();" title="<?php echo $Text; ?>"> <Прошлое> </a>
&nbsp;
</div>
</div>
<br/><br/>

<div id="claim_foot">
<div id="claim_date"><span class="editing"><?php echo date("d.m.Y") ?></span> <span class="no_print edit"><a href="javascript: GF();"><Изменить></a><a class="hidden" href="javascript: GF();"><Применить></a></span></div>	<div id="claim_sign">________ <span class="editing"><?php echo $Sign; ?></span> <span class="no_print edit"><a href="javascript: GF();"><Изменить></a><a class="hidden" href="javascript: GF();"><Применить></a><span class="attribute hidden">sign</span><span class="dn hidden"><?php echo $dn; ?></span><img class="hidden loader" src="./images/load.gif" width="16" height="16"/></span></div>
</div>

</div>

<button id="print" class="no_print">Распечатать</button>
