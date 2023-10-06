<?php

define('METHOD_POST', 1);
define('METHOD_GET', 2);

function CheckImageSize($fileName, $maxWidth, $maxHeight)
{
    list($width, $height, $type, $attr) = getimagesize($fileName);
    if (($width > $maxWidth) || ($height > $maxHeight))
        return false;
    else
        return true;
}

function SMGetImageSize($fileName)
{
    list($width, $height, $type, $attr) = getimagesize($fileName);
    return array($width, $height);
}

function FormatExceptionTrace($exception)
{
    return '<pre>'.$exception->getTraceAsString().'</pre>';
}

function ExtractPrimaryKeyValues(&$primaryKeyValues, $method = METHOD_GET)
{
    $paramNumber = 0;
    if ($method == METHOD_GET)
    {
        while(GetApplication()->IsGETValueSet("pk$paramNumber"))
        {
            $primaryKeyValues[] = GetApplication()->GetGETValue("pk$paramNumber");
            $paramNumber++;
        }
    }
    elseif ($method == METHOD_POST)
    {
        while(GetApplication()->IsPOSTValueSet("pk$paramNumber"))
        {
            $primaryKeyValues[] = GetApplication()->GetPOSTValue("pk$paramNumber");
            $paramNumber++;
        }
    }
}

function AddPrimaryKeyParametersToArray(&$targetArray, $primaryKeyValues)
{
    $paramNumber = 0;
    foreach($primaryKeyValues as $primaryKeyValue)
    {
        $targetArray["pk$paramNumber"] = $primaryKeyValue;
        $paramNumber++;
    }
}

function AddPrimaryKeyParameters($linkBuilder, $PrimaryKeyValues)
{
    $KeyValueList = '';
    $KeyValueNumber = 0;
    foreach($PrimaryKeyValues as $PrimaryKeyValue)
    {
        $linkBuilder->AddParameter("pk$KeyValueNumber", $PrimaryKeyValue);
        $KeyValueNumber ++;
    }
    return $KeyValueList;
}

function ReplaceFirst($target, $pattern, $newValue)
{
    /*
    if (strpos($target, $pattern) >= 0)
        return 
            substr($target, 0, strpos($target, $pattern)) . 
            $newValue . 
            substr($target, strpos($target, $pattern) + strlen($pattern), strlen($target) - (strpos($target, $pattern) + strlen($pattern)));
    else
        return $target;      
    */
    return preg_replace("/(\W|\s)$pattern((\W|\s)|$)/i", "\${1}$newValue\${2}", $target);
}

function ConvertTextToEncoding($text, $sourceEncoding, $targetEncoding)
{
    if ($sourceEncoding != '' && $targetEncoding != '' && $targetEncoding != $sourceEncoding) 
    {
        if (function_exists("mb_convert_encoding"))  
        {
            if ($sourceEncoding == null)
                return mb_convert_encoding($text, $targetEncoding);
            else
                return mb_convert_encoding($text, $targetEncoding, $sourceEncoding);
        }
        elseif (function_exists("iconv")) 
            return iconv($sourceEncoding, $targetEncoding, $text);
        else
            return $text;
    } 
    else 
    {
        return $text;
    }
}

function BuildPrimaryKeyLink($PrimaryKeyValues)
{
	$KeyValueList = '';
	$KeyValueNumber = 0;
	foreach($PrimaryKeyValues as $PrimaryKeyValue)
	{
		AddStr($KeyValueList, "pk$KeyValueNumber=$PrimaryKeyValue", '&');
		$KeyValueNumber ++;
	}
	return $KeyValueList;
}

function AddStr(&$AResult, $AString, $ADelimiter = '')
{
    if(isset($AString) && $AString != '')
    {
        if(!($AResult == ''))
            $AResult = $AResult . $ADelimiter;
        $AResult = $AResult . $AString;
    }
}

function Combine($Left, $Right, $Delimiter = ' = ')
{
	return $Left . $Delimiter . $Right;
}

class ParametrizedQuery
{
	private $FSQL;
	private $FRelplaceList;

	function ParametrizedQuery($ASQL)
	{
		$this->FSQL = $ASQL;
		$this->FRelplaceList = array();
	}

	function AssignParam($ParamName, $Value)
	{
		$this->FRelplaceList[$ParamName] = $Value;
	}

	function GetSQL()
	{
		$Result = $this->FSQL;

		foreach($this->FRelplaceList as $ParamName => $Value)
			$Result = str_replace(':' . $ParamName, $Value, $Result);

		return $Result;
	}
}

// Locale (if localeconv returns empty info)
define("DEFAULT_DECIMAL_POINT", ".", TRUE);
define("DEFAULT_THOUSANDS_SEP", ",", TRUE);
define("DEFAULT_CURRENCY_SYMBOL", "$", TRUE);
define("DEFAULT_MON_DECIMAL_POINT", ".", TRUE);
define("DEFAULT_MON_THOUSANDS_SEP", ",", TRUE);
define("DEFAULT_POSITIVE_SIGN", "", TRUE);
define("DEFAULT_NEGATIVE_SIGN", "-", TRUE);
define("DEFAULT_FRAC_DIGITS", 2, TRUE);
define("DEFAULT_P_CS_PRECEDES", TRUE, TRUE);
define("DEFAULT_P_SEP_BY_SPACE", FALSE, TRUE);
define("DEFAULT_N_CS_PRECEDES", TRUE, TRUE);
define("DEFAULT_N_SEP_BY_SPACE", FALSE, TRUE);
define("DEFAULT_P_SIGN_POSN", 3, TRUE);
define("DEFAULT_N_SIGN_POSN", 3, TRUE);

// FormatCurrency
//ew_FormatCurrency(Expression[,NumDigitsAfterDecimal [,IncludeLeadingDigit
// [,UseParensForNegativeNumbers [,GroupDigits]]]])
//NumDigitsAfterDecimal is the numeric value indicating how many places to the
//right of the decimal are displayed
//-1 Use Default
//The IncludeLeadingDigit, UseParensForNegativeNumbers, and GroupDigits
//arguments have the following settings:
//-1 True
//0 False
//-2 Use Default
function FormatCurrency($amount, $NumDigitsAfterDecimal, $IncludeLeadingDigit = -2, $UseParensForNegativeNumbers = -2, $GroupDigits = -2) {

    // export the values returned by localeconv into the local scope
    extract(localeconv()); // PHP 4 >= 4.0.5

    // set defaults if locale is not set
    if (empty($decimal_point)) $decimal_point = DEFAULT_DECIMAL_POINT;
    if (empty($thousands_sep)) $thousands_sep = DEFAULT_THOUSANDS_SEP;
    if (empty($currency_symbol)) $currency_symbol = DEFAULT_CURRENCY_SYMBOL;
    if (empty($mon_decimal_point)) $mon_decimal_point = DEFAULT_MON_DECIMAL_POINT;
    if (empty($mon_thousands_sep)) $mon_thousands_sep = DEFAULT_MON_THOUSANDS_SEP;
    if (empty($positive_sign)) $positive_sign = DEFAULT_POSITIVE_SIGN;
    if (empty($negative_sign)) $negative_sign = DEFAULT_NEGATIVE_SIGN;
    if (empty($frac_digits) || $frac_digits == CHAR_MAX) $frac_digits = DEFAULT_FRAC_DIGITS;
    if (empty($p_cs_precedes) || $p_cs_precedes == CHAR_MAX) $p_cs_precedes = DEFAULT_P_CS_PRECEDES;
    if (empty($p_sep_by_space) || $p_sep_by_space == CHAR_MAX) $p_sep_by_space = DEFAULT_P_SEP_BY_SPACE;
    if (empty($n_cs_precedes) || $n_cs_precedes == CHAR_MAX) $n_cs_precedes = DEFAULT_N_CS_PRECEDES;
    if (empty($n_sep_by_space) || $n_sep_by_space == CHAR_MAX) $n_sep_by_space = DEFAULT_N_SEP_BY_SPACE;
    if (empty($p_sign_posn) || $p_sign_posn == CHAR_MAX) $p_sign_posn = DEFAULT_P_SIGN_POSN;
    if (empty($n_sign_posn) || $n_sign_posn == CHAR_MAX) $n_sign_posn = DEFAULT_N_SIGN_POSN;

    // check $NumDigitsAfterDecimal
    if ($NumDigitsAfterDecimal > -1)
        $frac_digits = $NumDigitsAfterDecimal;

    // check $UseParensForNegativeNumbers
    if ($UseParensForNegativeNumbers == -1) {
        $n_sign_posn = 0;
        if ($p_sign_posn == 0) {
            if (DEFAULT_P_SIGN_POSN != 0)
                $p_sign_posn = DEFAULT_P_SIGN_POSN;
            else
                $p_sign_posn = 3;
        }
    } elseif ($UseParensForNegativeNumbers == 0) {
        if ($n_sign_posn == 0)
            if (DEFAULT_P_SIGN_POSN != 0)
                $n_sign_posn = DEFAULT_P_SIGN_POSN;
            else
                $n_sign_posn = 3;
    }

    // check $GroupDigits
    if ($GroupDigits == -1) {
        $mon_thousands_sep = DEFAULT_MON_THOUSANDS_SEP;
    } elseif ($GroupDigits == 0) {
        $mon_thousands_sep = "";
    }

    // start by formatting the unsigned number
    $number = number_format(abs($amount),
                            $frac_digits,
                            $mon_decimal_point,
                            $mon_thousands_sep);

    // check $IncludeLeadingDigit
    if ($IncludeLeadingDigit == 0) {
        if (substr($number, 0, 2) == "0.")
            $number = substr($number, 1, strlen($number)-1);
    }
    if ($amount < 0) {
        $sign = $negative_sign;

        // "extracts" the boolean value as an integer
        $n_cs_precedes  = intval($n_cs_precedes  == true);
        $n_sep_by_space = intval($n_sep_by_space == true);
        $key = $n_cs_precedes . $n_sep_by_space . $n_sign_posn;
    } else {
        $sign = $positive_sign;
        $p_cs_precedes  = intval($p_cs_precedes  == true);
        $p_sep_by_space = intval($p_sep_by_space == true);
        $key = $p_cs_precedes . $p_sep_by_space . $p_sign_posn;
    }
    $formats = array(

      // currency symbol is after amount
      // no space between amount and sign

      '000' => '(%s' . $currency_symbol . ')',
      '001' => $sign . '%s ' . $currency_symbol,
      '002' => '%s' . $currency_symbol . $sign,
      '003' => '%s' . $sign . $currency_symbol,
      '004' => '%s' . $sign . $currency_symbol,

      // one space between amount and sign
      '010' => '(%s ' . $currency_symbol . ')',
      '011' => $sign . '%s ' . $currency_symbol,
      '012' => '%s ' . $currency_symbol . $sign,
      '013' => '%s ' . $sign . $currency_symbol,
      '014' => '%s ' . $sign . $currency_symbol,

      // currency symbol is before amount
      // no space between amount and sign

      '100' => '(' . $currency_symbol . '%s)',
      '101' => $sign . $currency_symbol . '%s',
      '102' => $currency_symbol . '%s' . $sign,
      '103' => $sign . $currency_symbol . '%s',
      '104' => $currency_symbol . $sign . '%s',

      // one space between amount and sign
      '110' => '(' . $currency_symbol . ' %s)',
      '111' => $sign . $currency_symbol . ' %s',
      '112' => $currency_symbol . ' %s' . $sign,
      '113' => $sign . $currency_symbol . ' %s',
      '114' => $currency_symbol . ' ' . $sign . '%s');

  // lookup the key in the above array
    return sprintf($formats[$key], $number);
}

function parsenumbers($str)
{
	$ret=array();
	$i=0;
	$num=0;
	$pos=0;
	while($i<strlen($str))
	{
		if(is_numeric($str[$i]) && !$num)
		{
			$num=1;
			$pos=$i;
		}
		else if(!is_numeric($str[$i]) && $num)
		{
			$ret[]=(integer)substr($str,$pos,$i-$pos);
			$num=0;
		}
		$i++;
	}
	if($num)
		$ret[]=(integer)substr($str,$pos,$i-$pos);
	return $ret;
}

function localdatetime2db($strdatetime,$format="")
{
	global $locale_info;
	$locale_idate=$locale_info["LOCALE_IDATE"];
	if($format=="dmy")
		$locale_idate=1;
	if($format=="mdy")
		$locale_idate=0;
	if($format=="ymd")
		$locale_idate=2;

//	check if we use 12hours clock
	$use12=0;
	$pos=strpos($locale_info["LOCALE_STIMEFORMAT"],"h".$locale_info["LOCALE_STIME"]);
	if(!($pos===false))
	{
		$use12=1;
//	determine am/pm
		$pm=0;
		$amstr=$locale_info["LOCALE_S1159"];
		$pmstr=$locale_info["LOCALE_S2359"];
		$posam=strpos($strdatetime,$amstr);
		$pospm=strpos($strdatetime,$pmstr);
		if($posam===false && $pospm!==false)
			$pm=1;
		elseif($posam!==false && $pospm===false)
			$pm=0;
		elseif($posam===false && $pospm===false)
			$use12=0;
		else
		{
			if($posam>$pospm)
				$pm=1;
		}
	}
	$numbers=parsenumbers($strdatetime);
	if(!$numbers || count($numbers)<2)
		return "null";
//	add current year if not specified
	if(count($numbers)<3)
	{	
		if($locale_idate!=1)
		{
			$month=$numbers[0];
			$day=$numbers[1];
		}
		else
		{
			$month=$numbers[1];
			$day=$numbers[0];
		}
		$tm=localtime(time(),true);
		$year=1900+$tm["tm_year"];
	}
	else
	{
		if(!$locale_idate)
			list($month,$day,$year)=$numbers;
		else if($locale_idate==1)
			list($day,$month,$year)=$numbers;
		else if($locale_idate==2)
			list($year,$month,$day)=$numbers;
	}		
	if(!$month || !$day)
		return "null";
	while(count($numbers)<6)
		$numbers[]=0;
	$h=$numbers[3];
	$m=$numbers[4];
	$s=$numbers[5];
	if($use12 && $h)
	{
		if(!$pm && $h==12)
			$h=0;
		if($pm && $h<12)
			$h+=12;
	}
	if($year<100)
	{
		if($year<60)
			$year+=2000;
		else
			$year+=1900;
	}
	return sprintf("%04d-%02d-%02d",$year,$month,$day)." ".sprintf("%02d:%02d:%02d",$h,$m,$s);
}


/*
 * Array utils
 */
function GetArrayValueDef($array, $key, $defaultValue = null)
{
    return isset($array[$key]) ? $array[$key] : $defaultValue;
}

?>