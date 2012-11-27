<?php
@$Name=($_POST['name'])?$_POST['name']:(($_GET['name'])?$_GET['name']:$SEARCH_DEFAULT_VALUE);
$BadSymbols=array("[", "]", "{", "}", "<", ">", ".", ",", ";", ":", "!", "?", "&", "#", "%", "^", "+", "|", "/", "~", "$");
$Name=str_replace($BadSymbols, "", $Name);

$CurrentVars['name']=$Name;
?>

<fieldset class="find">
<legend><?php echo $L->l("employee_search"); ?></legend>

<div id="search">
<input type="text" id="Name" name="name" value="<?php echo $Name ?>"  />
<input type="image" src="./skins/<?php echo $CURRENT_SKIN; ?>/images/find.png"  />
<input type="hidden" name="bookmark_name" value="<?php echo $BOOKMARK_NAME ?>" />
<input type="hidden" name="bookmark_attr" value="<?php echo $bookmark_attr ?>" />
<input type="hidden" name="form_sent" value="1" />
</div>
<?php 

if($ONLY_BOOKMARK_VIS)
	{
	if($only_bookmark) 
		$Checked="checked=\"checked\"";
	else
		{
		if(isset($_POST['form_sent']))
			$Checked="";
			
		else
			{
			if($ONLY_BOOKMARK&&!isset($_GET['only_bookmark']))
				$Checked="checked=\"checked\"";
			else
				$Checked="";
			}
			
		}
	if(sizeof($BOOKMARK_NAMES)>1)
		echo "<input type=\"checkbox\" id=\"only_bookmark\" name=\"only_bookmark\" value=\"1\" ".$Checked."  title=\"Искать только пользователей в закладке\" /><label for=\"only_bookmark\"> &mdash; Искать в закладке</label>";
	}
else
	{
	echo "<input type=\"hidden\" name=\"only_bookmark\" value=\"".$ONLY_BOOKMARK."\" />";
	}
?>
</fieldset>