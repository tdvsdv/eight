<?php
class Photo
	{

	function grip($K, $OldImageFile, $NewImageFile)
		{
		$WidthAndHeight=getimagesize($OldImageFile);

		$Width=$WidthAndHeight[0];
		$Height=$WidthAndHeight[1];

		$NewWidth=ceil($Width*$K);
		$NewHeight=ceil($Height*$K);

		$src=ImageCreateFromJpeg($OldImageFile);
		$dst=imagecreatetruecolor($NewWidth, $NewHeight);

		ImageCopyResampled($dst, $src, 0, 0, 0, 0, $NewWidth, $NewHeight, $Width, $Height);
		ImageJpeg($dst, $NewImageFile, 80);

		ImageDestroy($src);
		ImageDestroy($dst);
		}

	function getGrippedPhotoContent($MaxWidth, $MaxHeight, $PhotoFile)
		{
		$WidthAndHeight=getimagesize($PhotoFile);

		$KW=$MaxWidth/$WidthAndHeight[0];
		$KH=$MaxHeight/$WidthAndHeight[1];
		$K=($KH<$KW)?$KH:$KW;

		$TempName=tempnam(@$GLOBALS['TEMP_DIR'], "UUF");
		self::grip($K, $PhotoFile, $TempName);		

		$fopen = fopen($TempName, "r");
		$File['content']=fread($fopen, filesize($TempName));		
		$File['size']=filesize($TempName);		
		fclose($fopen);		
		return $File;
		}
	}
?>