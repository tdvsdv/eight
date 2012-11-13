<?php
abstract Class PDF
{
	function get_pdf_head()
	{
		return "
		<table id=\"header\">
		<tr>
		<td rowspan=\"3\"><img src=\"../skins/".$GLOBALS['CURRENT_SKIN']."/images/pdf/logo.png\" width=\"\" height=\"\"></td>
		<td id=\"title\">".$GLOBALS['PDF_TITLE']." (".$GLOBALS['BOOKMARK_NAMES'][$GLOBALS['bookmark_attr']][$GLOBALS['BOOKMARK_NAME']].")</td>
		</tr>
		<tr>
		<td id=\"second_life\">".$GLOBALS['PDF_SECOND_LINE']."</td>
		</tr>
		<tr>
		<td id=\"create_date\">Справочник создан: ".date("d.m.Y")."</td>
		</tr>
		</table>";
	}
}
?>