<?php
class Time
{
	public static function getTimeOfDMYHI($DMYHI, $DateFormat="dd.mm.yyyy")
		{	
		$DateTime=explode(" ", $DMYHI);
		@$Time=explode(":", $DateTime[1]);

		switch($DateFormat)
			{
			case 'yyyy-mm-dd':
				$Date=explode("-", $DateTime[0]);
				$temp=$Date[0]; $Date[0]=$Date[2]; $Date[2]=$temp;
			break;
			case 'dd.mm.yyyy':
				$Date=explode(".", $DateTime[0]);
			break;
			case 'yyyymmddhhmmss':
				$Date[0]=substr($DMYHI, 6, 2);
				$Date[1]=substr($DMYHI, 4, 2);
				$Date[2]=substr($DMYHI, 0, 4);
				$Time[0]=substr($DMYHI, 8, 2);
				$Time[1]=substr($DMYHI, 10, 2);
				$Time[2]=substr($DMYHI, 12, 2);
			break;			
			default:
				$Date=explode(".", $DateTime[0]);
			}			

		if(sizeof($Time)==1){$Time[0]=0; $Time[1]=0; $Time[2]=0;}
		if(sizeof($Date)!=1)
			{return @mktime($Time[0], $Time[1], $Time[2], $Date[1], $Date[0], $Date[2]);}
		else
			{return false;}
		}

	public static function getOnlyDatePartFromTime($Time)
		{
		return mktime(0, 0, 0, date("n", $Time), date("j", $Time), date("Y", $Time));
		}

	public static function checkDate($Date, $DateFormat="dd.mm.yyyy")
		{
		switch($DateFormat)
			{
			case 'yyyy-mm-dd':
				if(preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $Date))
					return true;
				else
					return false;
			break;
			case 'dd.mm.yyyy':
				if(preg_match("/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/", $Date))
					return true;
				else
					return false;
			break;
			}		
		}
	
	public static function modifyDateFormat($Date, $FromFormat="dd.mm.yyyy", $ToFormat="yyyy-mm-dd")
		{
		$Time=self::getTimeOfDMYHI($Date, $FromFormat);
		switch($ToFormat)
			{
			case 'yyyy-mm-dd':
				return date("Y-m-d", $Time);
			break;
			case 'dd.mm.yyyy':
				return date("d.m.Y", $Time);
			break;
			case 'yyyymmddhhmmss':
				return date("YmdHis", $Time);
			break;		
			}			
		}

	public static function getHandyDateOfDMYHI($DMYHI, $DateFormat="dd.mm.yyyy")
		{
		$DateTime=explode(" ", $DMYHI);
		
		switch($DateFormat)
			{
			case 'yyyy-mm-dd':
				$Date=explode("-", $DateTime[0]);
				$temp=$Date[0]; $Date[0]=$Date[2]; $Date[2]=$temp;
			break;
			case 'dd.mm.yyyy':
				$Date=explode(".", $DateTime[0]);
			break;
			case 'yyyymmddhhmmss':
				$Date[0]=substr($DMYHI, 6, 2);
				$Date[1]=substr($DMYHI, 4, 2);
				$Date[2]=substr($DMYHI, 0, 4);
			break;			
			default:
				$Date=explode(".", $DateTime[0]);
			}		
		if(sizeof($Date)!=1)
			return (int) $Date[0]." ".$GLOBALS['MONTHS'][(int) $Date[1]]." ".$Date[2];
		else
			return false;
		}
		
}
?>