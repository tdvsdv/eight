<?php
if($Access)
	{
	require_once("./libs/forms.php");
	require_once("./libs/photo.php");

	$ButTitle=($_GET['ButTitle'])?$_GET['ButTitle']:$_POST['ButTitle'];
	$menu_marker=($_GET['menu_marker'])?$_GET['menu_marker']:$_POST['menu_marker'];
	$dn=($_GET['dn'])?$_GET['dn']:$_POST['dn'];

	if (is_uploaded_file(@$_FILES['imagefile']['tmp_name']))
		{
		if($_FILES['imagefile']['size']<=($PHOTO_MAX_SIZE*1024))
			{
			
			if(substr_count(mb_strtolower($PHOTO_EXT), mb_strtolower(end(explode(".", $_FILES['imagefile']['name']))) ))
				{

				$Content=Photo::getGrippedPhotoContent($PHOTO_MAX_WIDTH, $PHOTO_MAX_HEIGHT, $_FILES['imagefile']['tmp_name']);

				$ldap=new LDAP($LDAPServer, $LDAP_WRITE_USER, $LDAP_WRITE_PASSWORD);

				$dn=iconv($GLOBALS['CHARSET_APP'], $GLOBALS['CHARSET_DATA'], $dn);
				
				$info[$LDAP_PHOTO_FIELD]=$Content['content']; 

				$ldap->ldap_modify($dn, $info, true);
				$ButTitle="Изменить";

				if($THUMBNAIL_PHOTO_EDIT)
					{
					$Content=Photo::getGrippedPhotoContent($THUMBNAIL_PHOTO_MAX_WIDTH, $THUMBNAIL_PHOTO_MAX_HEIGHT, $_FILES["imagefile"]['tmp_name']);
					$info[$LDAP_AVATAR_FIELD]=$Content['content'];

					if($THUMBNAIL_PHOTO_MAX_SIZE*1024 >= @$info['size'])
						$ldap->ldap_modify($dn, $info, true); 
					}

				unset($TempName, $Content);
				}
			else
				{echo"<script>alert('Файл должен быть одного из следующих расширений: ".$PHOTO_EXT.".');</script>";}
			}
		else
			{echo"<script>alert('Размер файла превышает ".$PHOTO_MAX_SIZE." Кб.');</script>";}
		}	
	}
?>

<form action="<?php echo $_SERVER['PHP_SELF']."?menu_marker=".$menu_marker."&ButTitle=".$ButTitle."&dn=".$dn; ?>" enctype="multipart/form-data" method="POST">
<input class="hiidenfile" name="imagefile" type="file" onchange="this.form.submit();" />
<input type="button" class="filebutton"  value="<?php echo $ButTitle; ?>" />
</form>