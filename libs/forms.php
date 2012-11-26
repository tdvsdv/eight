<?php
function arrtoupper($arr) 
{
	if (!is_array($arr)) 
	{
		$arr = strtoupper($arr);
	} 
	else 
	{
		foreach ($arr as $key => $val) $arr[$key] = arrtoupper($val);
	}
	return $arr;
}

function arrtolower($arr) 
{
	if (!is_array($arr)) 
	{
		$arr = strtolower($arr);
	} 
	else 
	{
		foreach ($arr as $key => $val) $arr[$key] = arrtolower($val);
	}
	return $arr;
}

class LDAPTable
	{
	private $LC; 
	private $Attributes=array();
	private $PregReplace=array();
	private $LogicReplace=array();
	
	private $Vars=array();	
	public 	$Href;
	public 	$SortColumn;
	public 	$SortType;
	public 	$Head;
	public 	$Numbering;
	public 	$UI;
	public  $StringsOnPage;
	
	function __construct($Server, $User, $Password, $Port="389", $Head=true, $Numbering=true, $UI="")
		{
		$this->UI=$UI;
		$this->Href=$_SERVER['PHP_SELF']."?menu_marker=".$GLOBALS['menu_marker'];
		$this->SortColumn=($_GET[$this->UI.'sortcolumn'])?$_GET[$this->UI.'sortcolumn']:$_POST[$this->UI.'sortcolumn'];
		$this->SortType=($_GET[$this->UI.'sorttype'])?$_GET[$this->UI.'sorttype']:(($_POST[$this->UI.'sorttype'])?$_POST[$this->UI.'sorttype']:"ASC");
		$this->Head=$Head;
		$this->Numbering=$Numbering;
		
		
		$this->LC=ldap_connect($Server);
		ldap_set_option($this->LC, LDAP_OPT_PROTOCOL_VERSION, 3); 
		ldap_set_option($this->LC, LDAP_OPT_REFERRALS, 0); 
		
		$LB=ldap_bind($this->LC, $User, $Password); 
		}
		
	
	private function convertValue($Value)
		{
		$Value1=$Value;
		if($Value1)
			{
			@$Value=iconv($GLOBALS[CHARSET_DATA], $GLOBALS[CHARSET_APP], $Value1);
			if(!$Value)
				$Value=$Value1;
			}							
		else
			$Value="";		
			
		return $Value;
		}
	
	function addColumn($Name, $Title, $Sort=false, $i=0, $NotShow=false, $OrderFormat=false)
		{
		
		if((is_array(@$this->Attributes['title']))?(!in_array($Title, @$this->Attributes['title'])):true)
			{
			$j=sizeof(@$this->Attributes[name]);

			$this->Attributes['title'][$j]=$Title;		
			$this->Attributes['sort'][$j]=$Sort;	
			$this->Attributes['name'][$j]=$Name;
			$this->Attributes['i'][$j]=$i;
			$this->Attributes['notshow'][$j]=$NotShow;
			$this->Attributes['order_format'][$j]=$OrderFormat;
			}
		}
		
	function addVar($Name, $Value)
		{
		@$this->Vars[name][$Name]=$Name;		
		@$this->Vars[value][$Name]=$Value;	
		}	
		
	function addPregReplace($Pattern, $Replacement, $Title, $Limit="-1", $Conditions=false)
		{
		$j=sizeof(@$this->PregReplace[$Title][pattern]);
		
		$this->PregReplace[$Title]['pattern'][$j]=$Pattern;
		$this->PregReplace[$Title]['replacement'][$j]=$Replacement;
		$this->PregReplace[$Title]['limit'][$j]=$Limit;
		$this->PregReplace[$Title]['apply'][$j]=true;
		
		if(is_array($Conditions))
			{
			foreach($Conditions as $key=>$value)
				{
				//echo"$key<br>";
				if((is_array($this->Attributes['name']))?(in_array($key, $this->Attributes['name'])):false)
					{
					foreach($Conditions[$key] as $key1=>$value1)
						{
						//echo"$key1<br><br>";
						$this->PregReplace[$Title]['conditions'][$j][$key][$key1]=$value1;
						}					
					}
				}
			unset($key, $key1, $value, $value1);
			}
		}
				
	
	private function getNameByTitle($Title)
		{
		if($array_keys=array_keys($this->Attributes['title'], $Title))
			return $this->Attributes['name'][$array_keys[0]];
		else
			return false;
		}
		
	private function PregReplace($Title, $Value)
		{
		
		if(is_array(@$this->PregReplace[$Title][pattern]))
			{	
			$AK=array_keys($this->PregReplace[$Title][pattern]);
			$SizeOf=sizeof($AK);
			
			for($i=0; $i<$SizeOf; $i++)
				{
				
				if($this->PregReplace[$Title]['apply'][$i])
					$Value=preg_replace($this->PregReplace[$Title][pattern][$i], $this->PregReplace[$Title][replacement][$i], $Value, $this->PregReplace[$Title][limit][$i]);

				}
				
			}

		return $Value;
		}		
		
	
	//Печатает шапку таблицы
	private function printHead()
		{
		//$ADAttributes=array_values($this->Attributes[name]);
		$SizeOf=sizeof($this->Attributes['name']);

		
		echo"<tr>";
		if($this->Numbering)
			echo"<th><div>№</div></th>";

		for($i=0; $i<$SizeOf; $i++) 
			{ 
			if(!$this->Attributes['notshow'][$i])
				{
				echo"<th>";			
				
				if($this->Attributes['sort'][$i])
					{
					if($this->StringsOnPage) {$Href=$this->Href."&startstring=".$this->StartString;}
					else{$Href=$this->Href;}
					
					if(is_array($this->Vars['name']))
						{
						$AK=array_keys(@$this->Vars[name]);
						$SizeOf1=sizeof($AK);
						for($p=0; $p<$SizeOf1; $p++)
							{
							@$Href=$Href."&".$this->Vars[name][$AK[$p]]."=".$this->Vars[value][$AK[$p]];
							}
						}

					echo"<a class=\"".(($this->SortColumn==@$this->Attributes[title][$i])?$this->SortType:"")."\" href=\"".$Href."&".$this->UI."sortcolumn=".@$this->Attributes[title][$i]."&".$this->UI."sorttype=".(($this->SortType=="ASC")?"DESC":"ASC")."\">";
					echo @$this->Attributes[title][$i];
					echo"</a>";
					}
				else
					{
					echo "<div>".@$this->Attributes[title][$i]."</div>";
					}
				echo"</th>";
				}
			}
			
		echo"</tr>";
		}	
		
	function printTable($BaseDN, $Filter)
		{
		$BaseDN=iconv($GLOBALS['CHARSET_APP'], $GLOBALS['CHARSET_DATA'], $BaseDN);
		$Filter=iconv($GLOBALS['CHARSET_APP'], $GLOBALS['CHARSET_DATA'], $Filter);
		$ADAttributes=array_values($this->Attributes['name']); //Убрать дублирование
		foreach($ADAttributes AS $V)
			{
			if(strpos($V, ","))
				$ADAttributes=array_merge($ADAttributes, str_replace(" ", "", explode(",", $V)));
			}
		
		@$SizeOf=sizeof($this->Attributes[name]);
		
		
		$LS=ldap_search($this->LC, $BaseDN, $Filter, $ADAttributes); 

		//ldap_sort($this->LC, $LS, "name");
		 
		if($Entries=ldap_get_entries($this->LC, $LS)) 
			{ 
			echo"<table class='sqltable' cellpadding='4'>";

			if($Entries['count']&&$this->Head)
				{self::printHead();}
				
			//Сортировка
			//-----------------------------------------------------------------------------
			if($this->SortColumn)
				{
				$SortName=self::getNameByTitle($this->SortColumn);
				$j=array_search($this->SortColumn, $this->Attributes['title']);
				$k=$this->Attributes['i'][$j];
				
				for($i=0; $i<$Entries['count']; $i++) 
					{ 
					if(strpos($SortName, ","))
						{
						$ArrSorted[$i]="";
						foreach(explode(",", $SortName) AS $V)
							{
							@$ArrSorted[$i].=iconv($GLOBALS['CHARSET_DATA'], $GLOBALS['CHARSET_APP'], $Entries[$i][trim($V)][$k]);	
							}
						}
					else	
						{
						//$Value=iconv($GLOBALS['CHARSET_DATA'], $GLOBALS['CHARSET_APP'], $Entries[$i][$SortName][$k]); 
						@$ArrSorted[$i]=iconv($GLOBALS['CHARSET_DATA'], $GLOBALS['CHARSET_APP'], $Entries[$i][$SortName][$k]);	
						}
					//echo $ArrSorted[$i]."<br>";
					
					switch($this->Attributes['order_format'][$j])
						{
						case 'ad_def_full_name':
							$ArrSorted[$i]=preg_replace("/([ёA-zA-я-]+[\s]{1}[ёA-zA-я]{1}.)[\s]{1}([ёA-zA-я-]+)/", "\\2 \\1", $ArrSorted[$i]);
						break;
						
						case 'dd.mm.yyyy':
							if(preg_match("/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/", $ArrSorted[$i])) 
								{
								$e=explode(".", $ArrSorted[$i]);
								$ArrSorted[$i]=mktime(0, 0, 0, $e[1], $e[0], $e[2]);
								$ArrSorted[$i];
								}
						break;		

						case 'mm-dd':
							if(preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $ArrSorted[$i])) 
								{
								$e=explode("-", $ArrSorted[$i]);
								$ArrSorted[$i]=mktime(0, 0, 0, $e[1], $e[2], 2000);
								$ArrSorted[$i];
								}
						break;							

						case 'dd.mm':
							if(preg_match("/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/", $ArrSorted[$i])) 
								{
								$e=explode(".", $ArrSorted[$i]);
								$ArrSorted[$i]=mktime(0, 0, 0, $e[1], $e[0], 2000);
								$ArrSorted[$i];
								}
						break;								
						}
					

					}
				unset($SortName, $k);
				
				if(is_array(@$ArrSorted))
					{
					asort($ArrSorted);
					$AS=array_keys($ArrSorted);
					if($this->SortType=="DESC"){$AS=array_reverse($AS);}
					}
				}			
			//-----------------------------------------------------------------------------
			
	
			
			for($i=0; $i<$Entries['count']; $i++) 
				{ 
				$n=$i+1;
				if($n%2) {$CssClass='even';}
				else{$CssClass='odd';}
				
				echo"<tr class='".$CssClass."'>";
				if($this->Numbering)
					echo"<td>".$n."</td>";
				
				for($j=0; $j<$SizeOf; $j++)
					{
					//-----------------------------------------------------------------------------	

					if($this->SortColumn)
						{		
						if(strpos($this->Attributes['name'][$j], ","))
							{
							$Value="";
							foreach(explode(",", $this->Attributes['name'][$j]) AS $V)
								@$Value.=self::convertValue($Entries[$AS[$i]][trim($V)][$this->Attributes['i'][array_search($ADAttributes[$j], $this->Attributes['name'])]]);													
							}
						else
							@$Value=self::convertValue($Entries[$AS[$i]][$this->Attributes['name'][$j]][$this->Attributes['i'][array_search($ADAttributes[$j], $this->Attributes['name'])]]);
						}
					else
						{
						if(strpos($this->Attributes['name'][$j], ","))
							{
							$Value="";
							foreach(explode(",", $this->Attributes['name'][$j]) AS $V)
								$Value.=self::convertValue($Entries[$i][trim($V)][$this->Attributes['i'][array_search($ADAttributes[$j], $this->Attributes['name'])]]);						
							}
						else					
							$Value=self::convertValue($Entries[$i][$this->Attributes['name'][$j]][$this->Attributes['i'][array_search($ADAttributes[$j], $this->Attributes['name'])]]);
						}

					

					//-----------------------------------------------------------------------------	

					//Проверка применимости замены на рег. выражениях
					//-----------------------------------------------------------------------------				
					if(is_array(@$this->PregReplace[$this->Attributes[title][$j]]['conditions']))
						{						
						foreach(@$this->PregReplace[$this->Attributes[title][$j]]['conditions'] as $key=>$value)
							{
							@$this->PregReplace[$this->Attributes[title][$j]]['apply'][$key]=true;
							//$PreviousFlag=true;
							foreach(@$this->PregReplace[$this->Attributes[title][$j]]['conditions'][$key] as $key1=>$value1) //Цикл по ключам ldap
								{
								foreach(@$this->PregReplace[$this->Attributes[title][$j]]['conditions'][$key][$key1] as $key2=>$value2)
									{
																	
									if($this->SortColumn)
										@$ConditionValue=self::convertValue($Entries[$AS[$i]][$key1][$this->Attributes['i'][array_search($key1, $this->Attributes['name'])]]);
									else
										$ConditionValue=self::convertValue($Entries[$i][$key1][$this->Attributes['i'][array_search($key1, $this->Attributes['name'])]]);									
									switch($key2)
										{
										case "=":
											//echo $ConditionValue."--------".$value2."<br>";
											if($ConditionValue==$value2)
												@$this->PregReplace[$this->Attributes[title][$j]]['apply'][$key]=$this->PregReplace[$this->Attributes[title][$j]]['apply'][$key]&&true;
											else
												@$this->PregReplace[$this->Attributes[title][$j]]['apply'][$key]=$this->PregReplace[$this->Attributes[title][$j]]['apply'][$key]&&false;
										break;
										case "!=":
											if($ConditionValue!=$value2)
												@$this->PregReplace[$this->Attributes[title][$j]]['apply'][$key]=$this->PregReplace[$this->Attributes[title][$j]]['apply'][$key]&&true;
											else
												@$this->PregReplace[$this->Attributes[title][$j]]['apply'][$key]=$this->PregReplace[$this->Attributes[title][$j]]['apply'][$key]&&false;
										break;	

										case "in_range_date":
											if(preg_match("/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}\s-\s[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/", $ConditionValue))
												{												
												$e=explode(" - ", $ConditionValue);
												if((Time::getTimeOfDMYHI($e[1])>=$value2)&&(Time::getTimeOfDMYHI($e[0])<=$value2))
													{
													$this->PregReplace[$this->Attributes[title][$j]]['apply'][$key]=$this->PregReplace[$this->Attributes[title][$j]]['apply'][$key]&&true;
													}
												else
													$this->PregReplace[$this->Attributes[title][$j]]['apply'][$key]=$this->PregReplace[$this->Attributes[title][$j]]['apply'][$key]&&false;
												}
											else
												@$this->PregReplace[$this->Attributes[title][$j]]['apply'][$key]=$this->PregReplace[$this->Attributes[title][$j]]['apply'][$key]&&false;
										break;	
											
										default:
										$this->PregReplace[$this->Attributes[title][$j]]['apply'][$key]=true;	
										}
									}
								}
							}
										
						unset($key, $key1, $key2, $value, $value1, $value2, $ConditionValue);
						}				
					//-----------------------------------------------------------------------------							
						
					@$Value=self::PregReplace($this->Attributes[title][$j], $Value);
					//$Value=self::LogicReplace($this->Attributes[title][$j], $Value);
					
					if(!$this->Attributes['notshow'][$j])
						{
						echo "<td>";
						echo $Value;
						echo "</td>";
						}
					}

				echo"</tr>";
						
				unset($Value);
		 
				} 
			echo"</table>";
			}
		}
	}
	
	
class LDAP
	{
	private $LC; 
	
	function __construct($Server, $User, $Password, $Port="389")
		{
		$this->LC=ldap_connect($Server);
		ldap_set_option($this->LC, LDAP_OPT_PROTOCOL_VERSION, 3); 
		ldap_set_option($this->LC, LDAP_OPT_REFERRALS, 0); 
		
		$this->alphabet=$GLOBALS['Alphabet'];
		$this->SizePageDividerAttr=$GLOBALS['LDAP_SIZE_LIMIT_PAGE_DIVIDER_FIELD'];
		$this->SizeLimitCompatibility=$GLOBALS['LDAP_SIZE_LIMIT_COMPATIBILITY'];

		$LB=ldap_bind($this->LC, $User, $Password); 
		}	
	
	function ldap_modify($DN, $WhatChange, $NotRecode=false)
		{
		if(is_array($WhatChange))
			{
			//echo $DN;
			$DN=iconv($GLOBALS['CHARSET_APP'], $GLOBALS['CHARSET_DATA'], $DN);
			
			foreach($WhatChange as $key=>$value)
				{	
				if(is_array($value))
					{
					foreach($value as $key1=>$value1)
						{
						if($value1=="")
							{
							unset($WhatChange[$key][$key1]);
							//$KeyForDel[]=$key;
							}
						else
							{
							if($NotRecode)
								$WhatChange[$key][$key1]=$value1;
							else
								$WhatChange[$key][$key1]=iconv($GLOBALS['CHARSET_APP'], $GLOBALS['CHARSET_DATA'], $value1);
							}						
						}
					}
				else
					{
					if($WhatChange[$key]=="")
						{
						unset($WhatChange[$key]);
						$KeyForDel[]=$key;
						}
					else
						{
						if($NotRecode)
							$WhatChange[$key]=$value;
						else							
							$WhatChange[$key]=iconv($GLOBALS['CHARSET_APP'], $GLOBALS['CHARSET_DATA'], $value);
						}
					}

				}
			
			if(is_array(@$KeyForDel))
				{
				$LS=ldap_search($this->LC, $DN, "name=*", $KeyForDel); 
				$Entries=ldap_get_entries($this->LC, $LS);
				
				foreach($KeyForDel as $key=>$value)
					{
					if(@$Entries[0][$value][0]!="")
						$WhatDel[$value]=$Entries[0][$value][0];
					}
				if(is_array(@$WhatDel))
					ldap_mod_del($this->LC, $DN, $WhatDel);	
				}
			
			//$LS=ldap_search($this->LC, $DN, $Filter, $Attributes); 
			
			ldap_modify($this->LC, $DN, $WhatChange);

			}
		
		}
	
	function getEmptyFilter()
		{
		return "name=*";
		}

	function getAttrValue($DN, $Attribute, $Filter=false)
		{
		$Attributes=array($Attribute);

		if(!$Filter)
			$Filter=self::getEmptyFilter();	

		if(@$LS=ldap_search($this->LC, $DN, $Filter, $Attributes))
			{
			if($Entries=ldap_get_entries($this->LC, $LS)) 
				{
				unset($Entries[0][$Attribute]['count']);
				return @$Entries[0][$Attribute];
				}
			else
				return false;
			}
		else
			return false;		
		}

	function getValue($DN, $Attribute, $Filter=false, $NotRecode=false) //Устарела. Использовать getAttrValue()
		{
		$Attributes=array($Attribute);
		$Attributes=arrtolower($Attributes);
		
		$DN=iconv($GLOBALS['CHARSET_APP'], $GLOBALS['CHARSET_DATA'], $DN);
		$Filter=iconv($GLOBALS['CHARSET_APP'], $GLOBALS['CHARSET_DATA'], $Filter);
		
		if(!$Filter)
			{
			$Filter=$GLOBALS['LDAP_CN_FIELD']."=*";
			}
		
		if(@$LS=ldap_search($this->LC, $DN, $Filter, $Attributes))
			{
			//for ($Entries=ldap_first_entry($this->LC, $LS); $Entries!=false; $Entries=ldap_next_entry($this->LC,$Entries))
			if($Entries=ldap_get_entries($this->LC, $LS)) 
				{ 	

				if(!$NotRecode)
					return @iconv($GLOBALS['CHARSET_DATA'], $GLOBALS['CHARSET_APP'], $Entries[0][$Attribute][0]); 
				else
					return $Entries[0][$Attribute][0];
				}
			else
				{return false;}
			}
		else
			{return false;}
		}
		
	function getImage($DN, $Attribute, $File=false)	//$File=false
		{		
		$Attributes=array($Attribute);
		$Attributes=arrtolower($Attributes);
		$DN=iconv($GLOBALS['CHARSET_APP'], $GLOBALS['CHARSET_DATA'], $DN);
		$LS=ldap_search($this->LC, $DN, "name=*", $Attributes); 	

		if($Entries=ldap_get_entries($this->LC, $LS)) 
			{ 			
			if(@$Entries[0][$Attribute][0])
				{
				if($File)
					{
					//if (file_exists($strFile)) unlink($strFile);
					$handle = @fopen($File,'wb');
					@fwrite($handle, $Entries[0][$Attribute][0]);
					@fclose($handle);
					return $File;
					}
				else
					return "data:image/jpeg;base64,".base64_encode($Entries[0][$Attribute][0]);
				}
			else
				return false;
			}
		else
			{
			return false;
			}
		}
		

	function getEntriesWithoutSizeLimit($BaseDN, $Filter, $Attributes)
		{
		if($this->SizeLimitCompatibility)
			{
			$Attributes[]='displayname';
			foreach($this->alphabet AS $key=>$value)
				{
				$MofifiedFilter=substr_replace($Filter, "(&(".$this->SizePageDividerAttr."=".$value."*)", 0, 2);
				$LS=ldap_search($this->LC, $BaseDN, $MofifiedFilter, $Attributes);
				if(is_array($Entries))
					{
					$Entries=array_merge($Entries, ldap_get_entries($this->LC, $LS));
					$count+=$Entries['count'];
					}
				else
					{
					$Entries=ldap_get_entries($this->LC, $LS);
					$count=$Entries['count'];
					}
				}

			$Entries['count']=$count;
			}
		else
			{
			$LS=ldap_search($this->LC, $BaseDN, $Filter, $Attributes);
			$Entries=ldap_get_entries($this->LC, $LS);

			}
		return $Entries;
		}
		
	function getArray($BaseDN, $Filter, $ADAttributes, $Sort=array('name'), $SortType="ASC")
		{
		//$BaseDN=iconv($GLOBALS['CHARSET_APP'], $GLOBALS['CHARSET_DATA'], $BaseDN);
		//$Filter=iconv($GLOBALS['CHARSET_APP'], $GLOBALS['CHARSET_DATA'], $Filter);
		$ADAttributes=arrtolower($ADAttributes);
		$SizeOf=sizeof($ADAttributes);
		

		//$LS=ldap_search($this->LC, $BaseDN, $Filter, $ADAttributes); 



		if($Entries=self::getEntriesWithoutSizeLimit($BaseDN, $Filter, $ADAttributes)) 
			{ 	
			//Сортировка
			//-----------------------------------------------------------------------------
			if(is_array($Sort))
				{			
				for($i=0, $d=0; $i<$Entries['count']; $i++) 
					{ 							
					foreach($Sort AS $key=>$val)
						{
						if(is_array($val))
							{
							$d="";
							foreach($val AS $key1=>$val1)
								{
								if(is_array($val1))
									{
									foreach($val1 AS $key2=>$val2)
										{
										
										switch($val2)
											{
											case 'order_replace':
												$d.=$key2;
												$LastVal[$key-1]=str_replace($key1, $d, $LastVal[$key-1]); //!!!! Возможно деяние должно быть другим
											break;										
											}	
										}
									}
									
								switch($val1)
									{
									case 'ad_def_full_name':										
										$LastVal[$key-1]=preg_replace("/([ёA-zA-я-]+[\s]{1}[ёA-zA-я]{1}.)[\s]{1}([ёA-zA-я-]+)/", "\\2 \\1", $LastVal[$key-1]);
									break;	

									case 'order_replace':	
										//$d.=" ";
										//$LastVal[$key-1]=str_replace($key1, $d, $LastVal[$key-1]); //!!!! Возможно деяние должно быть другим
										$LastVal[$key-1]=" ".str_replace($key1, "", $LastVal[$key-1]);
									break;									
									/*default:
										$LastVal[$key-1]=iconv($GLOBALS['CHARSET_DATA'], $GLOBALS['CHARSET_APP'], $LastVal[$key-1]);*/
									}
								}
							}
						else
							$LastVal[$key]=iconv($GLOBALS['CHARSET_DATA'], $GLOBALS['CHARSET_APP'], $Entries[$i][$val][0]);
							
						if($key>0)	
							@$ArrSorted[$i].=" ".$LastVal[$key-1];
						}
					if(!is_array($val))
						@$ArrSorted[$i].=" ".$LastVal[$key];	
					//echo $ArrSorted[$i]."<br>";
					}
					
				
				if(is_array(@$ArrSorted))
					{
					asort($ArrSorted);
					$AS=array_keys($ArrSorted);
					if(strtolower($SortType)=="desc"){$AS=array_reverse($AS);}
					}
				/*foreach($ArrSorted AS $key=>$value)
					{
					echo "".$value."<br/>";
					}				*/
				}			
			//-----------------------------------------------------------------------------
			
			
			for($i=0; $i<@$Entries[count]; $i++) 
				{ 			
				for($j=0; $j<$SizeOf; $j++)
					{
					if(is_array($Sort))
						{					
						@$Value=iconv($GLOBALS['CHARSET_DATA'], $GLOBALS['CHARSET_APP'], $Entries[$AS[$i]][$ADAttributes[$j]][0]);
						//echo $Value."<br>";
						}
					else
						$Value=iconv($GLOBALS['CHARSET_DATA'], $GLOBALS['CHARSET_APP'], $Entries[$i][$ADAttributes[$j]][0]);
																	
					$RA[$ADAttributes[$j]][$i]=$Value;
					}			
				unset($Value);
				//echo "<br>";
		 
				} 
			}
			
		if(@is_array($RA))
			{return $RA;}
		else
			{return false;}			
		}
	

	function removeValues($dn, $Attributes)
		{
		ldap_mod_del($this->LC, $dn, $Attributes); 

		}

	function addValuesToEnd($dn, $Attributes)
		{
		@ldap_mod_add($this->LC, $dn, $Attributes);
		//$LS=ldap_search($this->LC, $dn, "name=*", array_unique(array_keys($Attributes)));
		//$Entries=ldap_get_entries($this->LC, $LS);
		}

	static function escapeFilterValue($Value)
		{
		$Value=str_replace(array('\\', '(', ')'), array('\5c', '\28', '\29'), $Value);
		return $Value;
		}
	}
	


?>