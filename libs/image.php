<?php
$Fopen = fopen(".$SKIN_FOLDER/images/logo.gif",'r');
$File=fread($Fopen, filesize(".$SKIN_FOLDER/images/logo.gif"));
fclose($Fopen);

Header('Content-type: image/jpeg');
Header('Content-disposition: inline; filename=jpeg_photo.jpg');
echo $File;
?>