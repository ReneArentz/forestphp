<?php
/**
 * collection of static string helper functions
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestany.net>
 * @copyright   (c) 2024 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.1.0 stable
 * @link        https://forestany.net
 * @object-id   0x1 0001A
 * @since       File available since Release 0.1.0 alpha
 * @deprecated  -
 *
 * @version log Version			Developer	Date		Comment
 * 				0.1.0 alpha		renea		2019-08-04	first build
 * 				0.1.0 alpha		renea		2019-08-04	added conversion for forestDateTime	
 * 				0.7.0 beta		renea		2020-01-03	added IncreaseIdentifier and mondey_format functions
 * 				0.9.0 beta		renea		2020-01-30	changes for TextToDate function for ocisql
 * 				1.0.0 stable	renea		2020-02-10	added os identification in money_format
 * 				1.0.0 stable	renea		2020-02-14	added ParseNameToFilename function
 * 				1.1.0 stable	renea		2023-11-01	fixed sprintf2 so that we can use more than 10 values in 2nd array parameter, also added a reverse option e.g. %15..%1 to avoid conflicts
 * 				1.1.0 stable	renea		2023-11-04	added GetNumericPartOfIdentifier method
 * 				1.1.0 stable	renea		2023-11-09	added ObfuscateString method
 */

namespace fPHP\Helper;

use \fPHP\Roots\forestString as forestString;
use \fPHP\Roots\forestList as forestList;
use \fPHP\Roots\forestNumericString as forestNumericString;
use \fPHP\Roots\forestInt as forestInt;
use \fPHP\Roots\forestFloat as forestFloat;
use \fPHP\Roots\forestBool as forestBool;
use \fPHP\Roots\forestArray as forestArray;
use \fPHP\Roots\forestObject as forestObject;
use \fPHP\Roots\forestLookup as forestLookup;
use \fPHP\Roots\forestException as forestException;

class forestStringLib {
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * replace multiple values in a string where we have numeric placeholders with a preceding character
	 *
	 * @param string $p_s_str  		string value with numeric placeholders
	 * @param array $p_a_vars  		array of values
	 * @param bool $p_b_reverse  	true - reverce replacement, false - usual replacement
	 * @param char $p_s_char  		numeric placeholder char for recognition
	 *
	 * @return string
	 *
	 * @access public
	 * @static yes
	 */
	public static function sprintf2($p_s_str = '', $p_a_vars = array(), $p_b_reverse = false, $p_s_char = '%') {
		if (empty($p_s_str)) {
			return '';
		}
		
		if (count($p_a_vars) > 0) {
			if ($p_b_reverse) {
				for ($i = (count($p_a_vars) - 1); $i >= 0; $i--) {
					$p_s_str = str_replace($p_s_char . $i, $p_a_vars[$i], $p_s_str);
				}
			} else {
				for ($i = 0; $i < count($p_a_vars); $i++) {
					$p_s_str = str_replace($p_s_char . $i, $p_a_vars[$i], $p_s_str);
				}
			}
		}
		
		return $p_s_str;
	}
	
	/**
	 * replace multiple chars in a string which are arranged in two parameter-arrays
	 *
	 * @param string $p_s_str  string value, passed by reference
	 * @param array $p_a_search  array of characters which should be replaced
	 * @param array $p_a_replace  array of characters which are replacing the found characters
	 *
	 * @return null
	 *
	 * @access public
	 * @static yes
	 */
	public static function ReplaceChar(&$p_s_str, array $p_a_search, array $p_a_replace) {
		if (count($p_a_search) != count($p_a_replace)) {
			return $p_s_str;
		}
		
		$i_amount = strlen($p_s_str);
		
		for ($i = 0; $i < count($p_a_search); $i++) {
			if ((strlen($p_a_search[$i]) != 1) || (strlen($p_a_replace[$i]) != 1)) {
				return $p_s_str;
			}
			
			if ($i_amount == 1) {
				if ($p_s_str == $p_a_search[$i]) {
					$p_s_str = $p_a_replace[$i];
				}
			} else {
				for ($j = 0; $j < $i_amount; $j++) {
					if ($p_s_str[$j] == $p_a_search[$i]) {
						if ($j != 0) {
							if ($p_s_str[$j-1] != '\\') {
								$p_s_str[$j] = $p_a_replace[$i];
							}
						} else {
							$p_s_str[$j] = $p_a_replace[$i];
						}
					}
				}
			}
		}
	}
		
	/**
	 * remove multiple chars in a string which are set in parameter-array
	 *
	 * @param string $p_s_str  string value
	 * @param array $p_a_search  array of characters which should be removed
	 *
	 * @return string
	 *
	 * @access public
	 * @static yes
	 */
	public static function RemoveChar($p_s_str, array $p_a_search) {
		$i_amount = strlen($p_s_str);
		$p_s_new_str = '';
		
		for ($i = 0; $i < $i_amount; $i++) {
			if (!in_array($p_s_str[$i], $p_a_search)){
				$p_s_new_str .= $p_s_str[$i];
			}
		}
		
		return $p_s_new_str;
	}
	
	/**
	 * converts sql-filter arguments into sql-clauses-array for implementing filter in query
	 * example: =35&<2|>=5&1..5&2*|ab?d
	 *
	 * @param string $p_s_value  string value
	 *
	 * @return array  array of filter clauses
	 *
	 * @access public
	 * @static yes
	 */
	public static function SplitFilter($p_s_value) {
		/* remove whitespaces from beginning and end */
		$p_s_value = trim($p_s_value);
		
		/* remove equation-sign from beginning of the string */
		$s_foo = substr($p_s_value, 0, 1);
		
		if ($s_foo == '=') {
			$p_s_value = substr($p_s_value, 1);
		}
		
		if ((strtolower($p_s_value) == 'ja') || (strtolower($p_s_value) == 'yes')) {
			$p_s_value = '1';
		}
		
		if ((strtolower($p_s_value) == 'nein') || (strtolower($p_s_value) == 'no')) {
			return(array(0 => array('<>', '1')));
		}
		
		if ((strtoupper($p_s_value) == 'NULL') || (empty($p_s_value))) {
			return(array(0 => array('IS', 'NULL')));
		}
		
		/* invalid filter if first character is '&' or '|'  */
		if (in_array($p_s_value[0], array('&','|'))) {
			return array();
		}
		
		/* check if no other operators * = > < .. ? & | are in this string then we can add * wildcards */
		if (
			($p_s_value != '1') &&
			(strpos($p_s_value, '1') === false) &&
			(strpos($p_s_value, '*') === false) &&
			(strpos($p_s_value, '=') === false) &&
			(strpos($p_s_value, '>') === false) &&
			(strpos($p_s_value, '<') === false) &&
			(strpos($p_s_value, '..') === false) &&
			(strpos($p_s_value, '?') === false) &&
			(strpos($p_s_value, '&') === false) &&
			(strpos($p_s_value, '|') === false)
		) {
			$p_s_value = '*' . $p_s_value . '*';
		}
		
		/* get logical filter operators like '&','|' and '..' */
		preg_match_all('(&|\||\.\.)', $p_s_value, $logicalFilterOperators, PREG_PATTERN_ORDER);
		$logicalFilterOperators = $logicalFilterOperators[0];
		
		/* get filter values with their compare operators */
		$filterValues = preg_split('(&|\||\.\.)', $p_s_value);
		
		/* we always need amount of logical filter operators + 1 of amount of filter values, instead invalid filter */
		if ((count($logicalFilterOperators) + 1) != count($filterValues)) {
			return array();
		}
		
		$amount = count($filterValues);
		$filter = array();
		
		/* merge logical filter operators and filter values in one array */
		for ($i = 0; $i < $amount; $i++) {
			if ($i < $amount - 1) { 
				array_push($filter,$filterValues[$i],$logicalFilterOperators[$i]);
			} else {
				array_push($filter,$filterValues[$i]);
			}
		}
		
		$foo = array();
		
		/* separate filter values of their compare filter operators */
		foreach ($filter as $filterValue) {
			if ((in_array($filterValue,array('&','|','..'))) || (empty($filterValue))) {
				array_push($foo, $filterValue);
			} else {
				preg_match('/(<=|>=|<>|<|>|=)?(.*)/', $filterValue, $filterValues);
				
				if ($filterValues[2] == "\'\'") {
					$filterValues[2] = 'NULL';
				}
				
				forestStringLib::ReplaceChar($filterValues[2], array('?','*'), array('_','%'));
				
				if (empty($filterValues[1])) {
					array_push($foo, $filterValues[2]);
				} else {
					array_push($foo, $filterValues[1], $filterValues[2]);
				}
			}
		}
		
		$filter = $foo;
		
		$amount = count($filter);
		$foo = array();
		
		/* change logical filter construct '..' into sql filter construct '>= x', '<= y' */
		for ($i = 0; $i < $amount; $i++) {
			$b_flag = false;
			
			if ($i == 0) {
				if ($amount == 1) {
					if ($filter[$i] != '..') {
						$b_flag = true;
					}
				} else {
					if (($filter[$i] != '..') && ($filter[$i+1] != '..')) {
						$b_flag = true;
					}
				}
			} else if ($i == ($amount - 1)) {
				if (($filter[$i] != '..') && ($filter[$i-1] != '..')) {
					$b_flag = true;
				}
			} else {
				if (($filter[$i] != '..') && ($filter[$i+1] != '..') && ($filter[$i-1] != '..')) {
					$b_flag = true;
				}
			}
			
			if ($b_flag) {
				array_push($foo,$filter[$i]);
			} else if ($filter[$i] == '..') {
				if (!empty($filter[$i-1])) {
					array_push($foo,'>=',$filter[$i-1]);
				}
				if ( (!empty($filter[$i-1])) && (!empty($filter[$i+1])) ) {
					array_push($foo,'&');
				}
				if (!empty($filter[$i+1])) {
					array_push($foo,'<=',$filter[$i+1]);
				}
			}
		}
		
		$filter = $foo;
		
		$foo = array();
		$i = 0;
		$b_operator = false;
		
		/* combine single filter constructs in seperate arrays for easy handling */
		foreach($filter as $filterValues) {
			/* if element is operator set operator flag */
			if (in_array($filterValues, array('<=','>=','<>','<','>','='))) {
				$foo[$i][] = $filterValues;
				$b_operator = true;
			}
			
			/* now we have only the real filter values */
			if ((!in_array($filterValues, array('<=','>=','<>','<','>','='))) && (!in_array($filterValues, array('&','|')))) {
				/* if value has no operator flag but has wildcards we use 'LIKE'	as compare operator */
				if ((!$b_operator) && ((strpos($filterValues, '%') !== false) || (strpos($filterValues, '_') !== false))) {
					$foo[$i][] = 'LIKE';
					$b_operator = true;
				}
				
				/* if value has no operator flag we use '=' as compare operator */
				if (!$b_operator) {
					$foo[$i][] = '=';
				}
				
				/* check if we have no double logical operator conflict or empty filter value */
				if ((preg_match('(<=|>=|<>|<|>|=|LIKE)', $filterValues)) || ($filterValues == '')) {
					return array();
				}
				
				$foo[$i][] = $filterValues;
				$b_operator = false;
			}
			
			/* with a new logical operator our clause-sub-array ends and we increase counter */
			if (in_array($filterValues, array('&','|'))) {
				$foo[$i][] = $filterValues;
				$i++;
			}
		}
		
		$filter = $foo;
		
		$amount = count($filter);
		$i = 0;
		
		/* last check */
		foreach ($filter as $check) {
			if ($i == 0) {
				/* check if first filter construct has at least 2 arguments if amount is 1 */
				if ($amount == 1) {
					if (count($check) != 2) {
						return array();
					}
				} else {
					if (count($check) != 3) {
						return array();
					}
				}
			} else if ($i == ($amount - 1)) {
				/* check if last filter construct has 2 arguments */
				if (count($check) != 2) {
					return array();
				}
			} else {
				/* check if all other filter constructs have at least 3 arguments */
				if (count($check) != 3) {
					return array();
				}
			}
			
			$i++;
		}
		
		/*echo '<pre>';
		print_r($filter);
		echo '</pre>';*/
		
		return $filter;
	}
	
	/**
	 * converts date/time/datetime-sql-string into forestDateTime object
	 *
	 * @param string $p_s_value  string value
	 *
	 * @return forestDateTime
	 *
	 * @access public
	 * @static yes
	 */
	public static function TextToDate($p_s_value) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$s_format = null;
		$i_year = 0;
		$i_month = 0;
		$i_day = 0;
		$i_hour = 0;
		$i_minute = 0;
		$i_second = 0;
		
		if (empty($p_s_value)) {
			return 'NULL';
		}
		
		$i_pos = strpos($p_s_value, ' ');
		$i_posT = strpos($p_s_value, 'T');
		
		/* date and time */
		if ($i_pos !== false) {
			$s_format = $o_glob->Trunk->DateTimeFormat;
			$a_temp = explode(' ', $p_s_value);
			
			/* handle date-part later */
			$p_s_value = $a_temp[0];
			
			$a_temp2 = explode(':',$a_temp[1]);
			
			if (count($a_temp2) != 3) {
				if (count($a_temp2) != 2) {
					throw new forestException('Invalid time value [HH:MM(:SS)].');
				} else {
					$i_hour = $a_temp2[0];
					$i_minute = $a_temp2[1];
					$i_second = '00';
				}
			} else {
				$i_hour = $a_temp2[0];
				$i_minute = $a_temp2[1];
				$i_second = $a_temp2[2];
			}
		}
		
		/* date and time */
		if ($i_posT !== false) {
			$s_format = $o_glob->Trunk->DateTimeFormat;
			$a_temp = explode('T', $p_s_value);
			
			/* handle date-part later */
			$p_s_value = $a_temp[0];
			
			$a_temp2 = explode(':',$a_temp[1]);
			
			if (count($a_temp2) != 3) {
				if (count($a_temp2) != 2) {
					throw new forestException('Invalid time value [HH:MM(:SS)].');
				} else {
					$i_hour = $a_temp2[0];
					$i_minute = $a_temp2[1];
					$i_second = '00';
				}
			} else {
				$i_hour = $a_temp2[0];
				$i_minute = $a_temp2[1];
				$i_second = $a_temp2[2];
			}
		}
		
		$i_pos = strpos($p_s_value, '-');
		$i_pos2 = strpos($p_s_value, '.');
		$i_pos3 = strpos($p_s_value, ':');
		
		/* special time format from mssql and ocisql with additional zeroes .0000 */
		if (($i_pos2 !== false) && ($i_pos3 !== false)) {
			$a_temp = explode('.', $p_s_value);
			$p_s_value = $a_temp[0];
			$i_pos2 = false;
		}
		
		if ($i_pos !== false) {
			/* date YYYY-MM-DD */
			if (is_null($s_format)) {
				$s_format = $o_glob->Trunk->DateFormat;
			}
			
			$a_temp = explode('-', $p_s_value);
			
			if (count($a_temp) != 3) {
				if (count($a_temp) != 2) {
					throw new forestException('Invalid date value [YYYY-MM-DD].');
				} else {
					$i_year = $a_temp[0];
					$i_month = $a_temp[1];
					$i_day = '01';
				}
			} else {
				$i_year = $a_temp[0];
				$i_month = $a_temp[1];
				$i_day = $a_temp[2];
			}
		} else if ($i_pos2 !== false) {
			/* date DD.MM.YYYY */
			if (is_null($s_format)) {
				$s_format = $o_glob->Trunk->DateFormat;
			}
			
			$a_temp = explode('.', $p_s_value);
			
			if (count($a_temp) != 3) {
				throw new forestException('Invalid date value [DD.MM.YYYY].');
			}

			$i_year = $a_temp[2];
			$i_month = $a_temp[1];
			$i_day = $a_temp[0];
			
			if (strlen($i_year) < 4) {
				$i_year = '20' . $i_year;
			}
		} else if ($i_pos3 !== false) {
			/* time */
			if (is_null($s_format)) {
				$s_format = $o_glob->Trunk->TimeFormat;
			}
			
			$a_temp = explode(':', $p_s_value);
			
			if (count($a_temp) != 3) {
				if (count($a_temp) != 2) {
					throw new forestException('Invalid time value [HH:MM(:SS)].');
				} else {
					$i_hour = $a_temp[0];
					$i_minute = $a_temp[1];
					$i_second = '00';
				}
			} else {
				$i_hour = $a_temp[0];
				$i_minute = $a_temp[1];
				$i_second = $a_temp[2];
			}
		}
		
		/* because of weird ocisql return value for timestamps */
		if (strpos($i_second, ',') !== false) {
			$i_second = explode(',', $i_second)[0];
		}
		
		/* because of weird ocisql return value for timestamps */
		if (strpos($i_second, '.') !== false) {
			$i_second = explode('.', $i_second)[0];
		}
		
		/*echo '<pre>';
		echo $s_format . '<br>';
		echo $i_year . '<br>';
		echo $i_month . '<br>';
		echo $i_day . '<br>';
		echo $i_hour . '<br>';
		echo $i_minute . '<br>';
		echo $i_second . '<br>';
		echo '</pre>';*/
		
		return new \fPHP\Helper\forestDateTime($s_format, $i_year, $i_month, $i_day, $i_hour, $i_minute, $i_second);
	}
	
	/**
	 * converts hex-value into integer
	 *
	 * @param string $p_s_value  string value
	 *
	 * @return integer
	 *
	 * @access public
	 * @static yes
	 */
	public static function HexToInt($p_s_value) {
		$i_result = 0;
		$i_hexValue = 0;
		$j = (strlen($p_s_value) - 1);
		
		for ($i = 0;$i <= (strlen($p_s_value) - 1);$i++) {
		
			switch ($p_s_value[$i]) {
				case '0': $i_hexValue = 0; break;
				case '1': $i_hexValue = 1; break;
				case '2': $i_hexValue = 2; break;
				case '3': $i_hexValue = 3; break;
				case '4': $i_hexValue = 4; break;
				case '5': $i_hexValue = 5; break;
				case '6': $i_hexValue = 6; break;
				case '7': $i_hexValue = 7; break;
				case '8': $i_hexValue = 8; break;
				case '9': $i_hexValue = 9; break;
				case 'A': $i_hexValue = 10; break;
				case 'B': $i_hexValue = 11; break;
				case 'C': $i_hexValue = 12; break;
				case 'D': $i_hexValue = 13; break;
				case 'E': $i_hexValue = 14; break;
				case 'F': $i_hexValue = 15; break;
			}
			
			$i_result += ($i_hexValue * pow(16, $j));
			$j--;
		}
		
		return $i_result;
	}
	
	/**
	 * converts integer into hex-value
	 *
	 * @param integer $p_i_value  integer value
	 *
	 * @return string
	 *
	 * @access public
	 * @static yes
	 */
	public static function IntToHex($p_i_value) {
		$s_hex = '';
		$i_hexValue = 0;
		$i_rest = 0;
		
		do {
			$i_rest = intval(floor($p_i_value / 16));
			$i_hexValue = intval(($p_i_value - ($i_rest * 16)));
			
			switch ($i_hexValue) {
				case 0: $s_hex .= '0'; break;
				case 1: $s_hex .= '1'; break;
				case 2: $s_hex .= '2'; break;
				case 3: $s_hex .= '3'; break;
				case 4: $s_hex .= '4'; break;
				case 5: $s_hex .= '5'; break;
				case 6: $s_hex .= '6'; break;
				case 7: $s_hex .= '7'; break;
				case 8: $s_hex .= '8'; break;
				case 9: $s_hex .= '9'; break;
				case 10: $s_hex .= 'A'; break;
				case 11: $s_hex .= 'B'; break;
				case 12: $s_hex .= 'C'; break;
				case 13: $s_hex .= 'D'; break;
				case 14: $s_hex .= 'E'; break;
				case 15: $s_hex .= 'F'; break;
			}
			
			$p_i_value = $i_rest;
		} while ($i_rest > 0);
		
		$s_hex = strrev($s_hex);
		return $s_hex;
	}
	
	/**
	 * check if a string starts with a specific order of characters
	 *
	 * @param string $p_s_str  string value
	 * @param string $p_s_search  search for occurence
	 *
	 * @return bool  true - string starts with search value, false - string start not with search value
	 *
	 * @access public
	 * @static yes
	 */
	public static function StartsWith($p_s_str, $p_s_search) {
		/* search backwards starting from haystack length characters from the end */
		return $p_s_search === "" || strrpos($p_s_str, $p_s_search, -strlen($p_s_str)) !== false;
	}

	/**
	 * check if a string ends with a specific order of characters
	 *
	 * @param string $p_s_str  string value
	 * @param string $p_s_search  search for occurence
	 *
	 * @return bool  true - string ends with search value, false - string ends not with search value
	 *
	 * @access public
	 * @static yes
	 */
	public static function EndsWith($p_s_str, $p_s_search) {
		/* search forward starting from end minus needle length characters */
		return $p_s_search === "" || (($temp = strlen($p_s_str) - strlen($p_s_search)) >= 0 && strpos($p_s_str, $p_s_search, $temp) !== false);
	}

	/**
	 * check if a string contains a specific order of characters
	 *
	 * @param string $p_s_str  string value
	 * @param string $p_s_search  search for occurence
	 *
	 * @return bool  true - string contains search value, false - string does not contain search value
	 *
	 * @access public
	 * @static yes
	 */
	public static function Contains($p_s_str, $p_s_search) {
		return $p_s_search !== '' && strpos($p_s_str, $p_s_search) !== false;
	}
	
	/**
	 * close all open html tags of the parameter string
	 *
	 * @param string $p_s_value  string value
	 *
	 * @return string
	 *
	 * @access public
	 * @static yes
	 */
	public static function closeHTMLTags($p_s_value) {
		preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $p_s_value, $a_result);
		$a_htmlTagsOpen = $a_result[1];
		
		preg_match_all('#</([a-z]+)>#iU', $p_s_value, $a_result);
		$a_htmlTagsClose = $a_result[1];
		
		if (count($a_htmlTagsClose) == count($a_htmlTagsOpen)) {
			/* all tags have been closed, nothing to do here */
			return $p_s_value;
		}
		
		$a_htmlTagsOpen = array_reverse($a_htmlTagsOpen);
		
		for ($i=0; $i < count($a_htmlTagsOpen); $i++) {
			if (!in_array($a_htmlTagsOpen[$i], $a_htmlTagsClose)) {
				/* ignore following html tags, because closing these is not necessary */
				if ( ($a_htmlTagsOpen[$i] != 'input') && ($a_htmlTagsOpen[$i] != 'br') ) {
					$p_s_value .= '</'.$a_htmlTagsOpen[$i].'>';
				}
			} else {
				unset($a_htmlTagsClose[array_search($a_htmlTagsOpen[$i], $a_htmlTagsClose)]);
			}
		}
		
		return $p_s_value;
	}
	
	/**
	 * function to remove system prefix of sql table names
	 *
	 * @param string $p_s_table  name of table, passed by reference
	 *
	 * @return null
	 *
	 * @access public
	 * @static yes
	 */
	public static function RemoveTablePrefix(&$p_s_table) {
		/* using sys_fphp prefix for all system-sql-tables, but we do not use it for class-name-declaration */
		if (strstr($p_s_table, 'sys_fphp_') !== false) {
			$p_s_table = str_replace('sys_fphp_', '', $p_s_table);
		}
		
		/* using fphp prefix for all sql-tables, but we do not use it for class-name-declaration */
		if (strstr($p_s_table, 'fphp_') !== false) {
			$p_s_table = str_replace('fphp_', '', $p_s_table);
		}
	}
	
	/**
	 * help function to increase alphanumeric and numeric only identifiers, with leading zeros as well
	 *
	 * @param string $p_s_identifier  value of identifier
	 * @param string $p_i_increment  increment value
	 *
	 * @return string  incremented identifier
	 *
	 * @access public
	 * @static yes
	 */
	public static function IncreaseIdentifier($p_s_identifier, $p_i_increment) {
		preg_match_all('/([a-zA-Z]+)(\d+)/', $p_s_identifier, $a_matches, PREG_OFFSET_CAPTURE);
		
		/* id must be greater than 0, must contain at least one numeric sign, must not consists of characters only */
		if ( (!is_string($p_s_identifier)) || (empty($p_s_identifier)) || (strlen($p_s_identifier) <= 0) || (ctype_alpha($p_s_identifier)) || (ctype_alpha($p_s_identifier[strlen($p_s_identifier) - 1])) ) {
			$p_s_identifier = 'INVALID';
		} else {
			$s_charPart = '';
			$s_numericPart = '';
			
			if ( empty($a_matches[0]) && empty($a_matches[1]) && empty($a_matches[2]) ) {
				/* no characters found in id */
				$s_numericPart = $p_s_identifier;
			} else {
				/* split characters from numeric part */
				$s_charPart = substr($p_s_identifier, 0, $a_matches[2][count($a_matches[2]) - 1][1]);
				$s_numericPart = substr($p_s_identifier, $a_matches[2][count($a_matches[2]) - 1][1]);
			}
			
			$i_numericPartLength = strlen($s_numericPart);
			$s_numericPart = str_pad(intval($s_numericPart) + intval($p_i_increment), strlen($s_numericPart), '0', STR_PAD_LEFT);
			
			if ($i_numericPartLength != strlen($s_numericPart)) {
				$p_s_identifier = 'OVERFLOW';
			} else {
				$p_s_identifier = $s_charPart . $s_numericPart;
			}
		}
		
		return $p_s_identifier;
	}

	/**
	 * help function to get numeric part of identifier
	 *
	 * @param string $p_s_identifier  value of identifier
	 *
	 * @return int	numeric part of identifier
	 *
	 * @access public
	 * @static yes
	 */
	public static function GetNumericPartOfIdentifier($p_s_identifier) {
		preg_match_all('/([a-zA-Z]+)(\d+)/', $p_s_identifier, $a_matches, PREG_OFFSET_CAPTURE);
		
		/* id must be greater than 0, must contain at least one numeric sign, must not consists of characters only */
		if ( (!is_string($p_s_identifier)) || (empty($p_s_identifier)) || (strlen($p_s_identifier) <= 0) || (ctype_alpha($p_s_identifier)) || (ctype_alpha($p_s_identifier[strlen($p_s_identifier) - 1])) ) {
			$p_s_identifier = -1;
		} else {
			$s_numericPart = '';
			
			if ( empty($a_matches[0]) && empty($a_matches[1]) && empty($a_matches[2]) ) {
				/* no characters found in id */
				$s_numericPart = $p_s_identifier;
			} else {
				/* split characters from numeric part */
				$s_numericPart = substr($p_s_identifier, $a_matches[2][count($a_matches[2]) - 1][1]);
			}
			
			$i_numericPartLength = strlen($s_numericPart);
			$s_numericPart = str_pad(intval($s_numericPart), strlen($s_numericPart), '0', STR_PAD_LEFT);
			
			if ($i_numericPartLength != strlen($s_numericPart)) {
				$p_s_identifier = -1;
			} else {
				$p_s_identifier = $s_numericPart;
			}
		}
		
		return intval($p_s_identifier);
	}
	
	/**
	 * replace unicode escape sequence within a string
	 *
	 * @param string $p_s_string  string value
	 *
	 * @return string
	 *
	 * @access public
	 * @static yes
	 */
	public static function ReplaceUnicodeEscapeSequence($p_s_string) {
		return preg_replace_callback(
			'/\\\\u([0-9a-f]{4})/i',
			function($s_match) {
				return mb_convert_encoding(pack('H*', $s_match[1]), 'UTF-8', 'UCS-2BE');
			},
			$p_s_string
		);
	}
	
	/**
	 * parse a string value to a valid filename string
	 *
	 * @param string $p_s_string  string value
	 *
	 * @return string
	 *
	 * @access public
	 * @static yes
	 */
	public static function ParseNameToFilename($p_s_string) {
		$s_foo = '';
		
		$p_s_string = str_replace('ö', 'oe', $p_s_string);
		$p_s_string = str_replace('ä', 'ae', $p_s_string);
		$p_s_string = str_replace('ü', 'ue', $p_s_string);
		$p_s_string = str_replace('Ö', 'Oe', $p_s_string);
		$p_s_string = str_replace('Ä', 'Ae', $p_s_string);
		$p_s_string = str_replace('Ü', 'Ue', $p_s_string);
		$p_s_string = str_replace('ß', 'ss', $p_s_string);
		
		for ($i = 0; $i < strlen($p_s_string); $i++) {
			if ($p_s_string[$i] == ' ') {
				$p_s_string[$i] = '_';
			}
			
			if (in_array($p_s_string[$i], array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0','1','2','3','4','5','6','7','8','9','-','_'))) {
				$s_foo .= $p_s_string[$i];
			}
		}
		
		return $s_foo;
	}

	/**
	 * manual european money_format function
	 *
	 * @param string $format  locale format e.g. en_US, de_DE
	 * @param float $number
	 *
	 * @return string  well formated money string
	 *
	 * @access public
	 * @static yes
	 */
	public static function money_format($format, $number) {
		return number_format($number, 2, ',', '.') . ' &#8364;';
	}

	/**
	 * obfuscate a string by giving each character it's html entity
	 *
	 * @param string $p_s_string  string value
	 * @param string $p_s_key	  unique characters of p_s_string
	 *
	 * @return string
	 *
	 * @access public
	 * @static yes
	 */
	public static function ObfuscateString($p_s_string) {
		$s_foo = '';

		for ($i = 0; $i < strlen($p_s_string); $i++) {
			$s_foo .= '&#' . ord($p_s_string[$i]) . ';';
		}

		return $s_foo;
	}
}
?>