<?php
class Localization
	{
	private $LocaleVars;


	function __construct($YmlFile)
		{
		$this->LocaleVars=Spyc::YAMLLoad($YmlFile);
		}


	public function l($Parameter)
		{
		return $this->LocaleVars[$Parameter];
		}

	public function ending($number, $singular, $plural, $zero_plural)
		{
		$LastDigit=substr($number, -1, 1);
		if($LastDigit==2 || $LastDigit==3 || $LastDigit==4)
			return $number." ".$plural;
		if($LastDigit==0 || $LastDigit==5 || $LastDigit==6 || $LastDigit==7 || $LastDigit==8 || $LastDigit==9)
			return  $number." ".$zero_plural;
		if($LastDigit==1)
			return  $number." ".$singular;
		}

	}
?>