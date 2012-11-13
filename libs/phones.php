<?php
if(is_file("./config/provider_desc.php"))
    {
    require_once ("./config/provider_desc.php");
    require_once ("./config/phone_codes.php");
    }
else
    {
    require_once ("../config/provider_desc.php");
    require_once ("../config/phone_codes.php");
    }

function get_phone_attr($phone = '', $convert = true, $trim = true)
{
    if (empty($phone)) {
        return '';
    }
    // очистка от лишнего мусора с сохранением информации о "плюсе" в начале номера
    $phone=trim($phone);
    $plus = ($phone[0] == '+');
	$OriginalPhone = preg_replace("/[^0-9A-Za-z_-\s]/", "", $phone); /* оригинальное форматирование номера */
    $phone = preg_replace("/[^0-9A-Za-z]/", "", $phone);
    

    // конвертируем буквенный номер в цифровой
    if ($convert == true && !is_numeric($phone)) {
        $replace = array('2'=>array('a','b','c'),
        '3'=>array('d','e','f'),
        '4'=>array('g','h','i'),
        '5'=>array('j','k','l'),
        '6'=>array('m','n','o'),
        '7'=>array('p','q','r','s'),
        '8'=>array('t','u','v'),
        '9'=>array('w','x','y','z'));

        foreach($replace as $digit=>$letters) {
            $phone = str_ireplace($letters, $digit, $phone);
        }
    }

    // заменяем 00 в начале номера на +
    if (substr($phone, 0, 2)=="00")
    {
        $phone = substr($phone, 2, strlen($phone)-2);
        $plus=true;
    }

    // если телефон длиннее 7 символов, начинаем поиск страны
    if (strlen($phone)>7)
    foreach ($GLOBALS['PHONE_CODES'] as $countryCode=>$data)
    {
        $codeLen = strlen($countryCode);
        if (substr($phone, 0, $codeLen)==$countryCode)
        {
            // как только страна обнаружена, урезаем телефон до уровня кода города
            $phone = substr($phone, $codeLen, strlen($phone)-$codeLen);
            $zero=false;
            // проверяем на наличие нулей в коде города
            if ($data['zeroHack'] && $phone[0]=='0')
            {
                $zero=true;
                $phone = substr($phone, 1, strlen($phone)-1);
            }

            $cityCode=NULL;
            // сначала сравниваем с городами-исключениями
            if ($data['exceptions_max']!=0)
            for ($cityCodeLen=$data['exceptions_max']; $cityCodeLen>=$data['exceptions_min']; $cityCodeLen--)
            if (in_array(intval(substr($phone, 0, $cityCodeLen)), $data['exceptions']))
            {
                $cityCode = ($zero ? "0" : "").substr($phone, 0, $cityCodeLen);
                $phone = substr($phone, $cityCodeLen, strlen($phone)-$cityCodeLen);
                break;
            }
            // в случае неудачи с исключениями вырезаем код города в соответствии с длиной по умолчанию
            if (is_null($cityCode))
            {
                $cityCode = substr($phone, 0, $data['cityCodeLength']);
                $phone = substr($phone, $data['cityCodeLength'], strlen($phone)-$data['cityCodeLength']);
            }

            // возвращаем результат
            $PhoneAttr['format_phone']=($plus ? "+" : "").$countryCode.'('.$cityCode.')'.phoneBlocks($phone, $GLOBALS['FORMAT_PHONE_BLOCKLEN']);
            $PhoneAttr['clear_phone']=($plus ? "+" : "").$countryCode.$cityCode.$phone;
			$PhoneAttr['original_phone']=$OriginalPhone;
			if ( array_key_exists($countryCode, $GLOBALS['PROVIDER_DESC']) AND array_key_exists($cityCode, $GLOBALS['PROVIDER_DESC'][$countryCode]) )
				$PhoneAttr['provider_desc']=$GLOBALS['PROVIDER_DESC'][$countryCode][$cityCode];
			else
				$PhoneAttr['provider_desc']='';
            return $PhoneAttr;
        }
    }
    // возвращаем результат без кода страны и города
    $PhoneAttr['format_phone']=($plus ? "+" : "").phoneBlocks($phone, $GLOBALS['FORMAT_PHONE_BLOCKLEN']);
    $PhoneAttr['clear_phone']=($plus ? "+" : "").$phone;
	$PhoneAttr['original_phone']=$OriginalPhone;
	$PhoneAttr['provider_desc']=NULL;
    return $PhoneAttr;
}

// функция превращает любое числов в строку формата XX-XX-... или XXX-XX-XX-... в зависимости от четности кол-ва цифр
//------------------------------------------------------------------------
function phoneBlocks($number, $blocklen)
{
        $add='';
        if (strlen($number)%2)
        {
                $add = $number[0];
                $number = substr($number, 1, strlen($number)-1);
        }
        return $add.strrev(implode("-", str_split(strrev($number), $blocklen)));
}
//------------------------------------------------------------------------
?>