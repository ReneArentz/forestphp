<?php
/**
 * capsule class for creating DateTime-objects with own rules and easy uses
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestphp.de>
 * @copyright   (c) 2019 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     0.9.0 beta
 * @link        http://www.forestphp.de/
 * @object-id   0x1 00012
 * @since       File available since Release 0.1.1 alpha
 * @deprecated  -
 *
 * @version log Version		Developer	Date		Comment
 * 		0.1.1 alpha	renatus		2019-08-08	added to framework
 * 		0.9.0 beta	renatus		2020-01-29	added EmptyDate property, and substr -1 on DateTime::ATOM
 */

namespace fPHP\Helper;

use \fPHP\Roots\{forestString, forestList, forestNumericString, forestInt, forestFloat, forestBool, forestArray, forestObject, forestLookup};
use \fPHP\Roots\forestException as forestException;

class forestDateTime {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $DateTime;
	private $Format;
	private $EmptyDate;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestDateTime class
	 *
	 * @param string $p_s_format  format string for DateTime
	 * @param integer $p_i_year
	 * @param integer $p_i_month
	 * @param integer $p_i_day
	 * @param integer $p_i_hour
	 * @param integer $p_i_minute
	 * @param integer $p_i_second
	 * @param string $p_o_timezone  timezone object
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct($p_s_format = null, $p_i_year = 0, $p_i_month = 0 , $p_i_day = 0, $p_i_hour = 0, $p_i_minute = 0, $p_i_second = 0, $p_o_timezone = null) {
		$this->DateTime = new forestObject(new \DateTime);
		$this->Format = new forestString;
		$this->EmptyDate = new forestBool;
		
		if (!is_null($p_s_format)) {
			if (is_string($p_s_format)) {
				$this->Format->value = $p_s_format;
			} else {
				throw new forestException('Paramater is not a string');
			}
		}
		
		if (!(($p_i_year == 0) && ($p_i_month == 0) && ($p_i_day == 0))) {
			$this->DateTime->value->setDate($p_i_year, $p_i_month, $p_i_day);
		} else {
			if (!(($p_i_hour == 0) && ($p_i_minute == 0) && ($p_i_second == 0))) {
				$this->EmptyDate->value = true;
			}
		}
		
		if (!(($p_i_hour == 0) && ($p_i_minute == 0) && ($p_i_second == 0))) {
			$this->DateTime->value->setTime($p_i_hour, $p_i_minute, $p_i_second);
		}
		
		if (!is_null($p_o_timezone)) {
			$this->DateTime->value->setTimezone($p_o_timezone);
		}
	}
	
	/**
	 * returns DateTime as string with format setting or DateTime::ATOM
	 *
	 * @return string  DateTime as string
	 *
	 * @access public
	 * @static no
	 */
	public function __toString() {
		if (issetStr($this->Format->value)) {
			return $this->DateTime->value->format($this->Format->value);
		} else {
			return $this->DateTime->value->format(substr(\DateTime::ATOM, 0, -1));
		}
	}
	
	/**
	 * manual ToString-method for returning DateTime in another format than was set on creation of the object or DateTime::ATOM
	 *
	 * @param string $p_s_format  format string for DateTime
	 *
	 * @return string  DateTime as string
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function ToString($p_s_format = null) {
		if (!is_null($p_s_format)) {
			/* return DateTime with parameter format */
			if (is_string($p_s_format)) {
				return $this->DateTime->value->format($p_s_format);
			} else {
				throw new forestException('Paramater is not a string');
			}
		} else if (issetStr($this->Format->value)) {
			/* return DateTime with stored format property */
			return $this->DateTime->value->format($this->Format->value);
		} else {
			/* return DateTime with standard DateTime::ATOM format */
			return $this->DateTime->value->format(substr(\DateTime::ATOM, 0, -1));
		}
	}
	
	/**
	 * add interval to DateTime value
	 *
	 * @param integer $p_i_years
	 * @param integer $p_i_months
	 * @param integer $p_i_days
	 * @param integer $p_i_hours
	 * @param integer $p_i_minutes
	 * @param integer $p_i_seconds
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function AddDateInterval($p_i_years = 0, $p_i_months = 0 , $p_i_days = 0, $p_i_hours = 0, $p_i_minutes = 0, $p_i_seconds = 0) {
		if ($p_i_years != 0) {
			$this->DateTime->value->modify('+' . $p_i_years . ' years');
		}
		
		if ($p_i_months != 0) {
			$this->DateTime->value->modify('+' . $p_i_months . ' months');
		}
		
		if ($p_i_days != 0) {
			$this->DateTime->value->modify('+' . $p_i_days . ' days');
		}
		
		if ($p_i_hours != 0) {
			$this->DateTime->value->modify('+' . $p_i_hours . ' hours');
		}
		
		if ($p_i_minutes != 0) {
			$this->DateTime->value->modify('+' . $p_i_minutes . ' minutes');
		}
		
		if ($p_i_seconds != 0) {
			$this->DateTime->value->modify('+' . $p_i_seconds . ' seconds');
		}
	}
	
	/**
	 * sub interval from DateTime value
	 *
	 * @param integer $p_i_years
	 * @param integer $p_i_months
	 * @param integer $p_i_days
	 * @param integer $p_i_hours
	 * @param integer $p_i_minutes
	 * @param integer $p_i_seconds
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function SubDateInterval($p_i_years = 0, $p_i_months = 0 , $p_i_days = 0, $p_i_hours = 0, $p_i_minutes = 0, $p_i_seconds = 0) {
		if ($p_i_years != 0) {
			$this->DateTime->value->modify('-' . $p_i_years . ' years');
		}
		
		if ($p_i_months != 0) {
			$this->DateTime->value->modify('-' . $p_i_months . ' months');
		}
		
		if ($p_i_days != 0) {
			$this->DateTime->value->modify('-' . $p_i_days . ' days');
		}
		
		if ($p_i_hours != 0) {
			$this->DateTime->value->modify('-' . $p_i_hours . ' hours');
		}
		
		if ($p_i_minutes != 0) {
			$this->DateTime->value->modify('-' . $p_i_minutes . ' minutes');
		}
		
		if ($p_i_seconds != 0) {
			$this->DateTime->value->modify('-' . $p_i_seconds . ' seconds');
		}
	}

	/**
	 * convert integer seconds to DateTime value
	 *
	 * @param integer $p_i_seconds
	 *
	 * @return forestDateTime
	 *
	 * @access public
	 * @static yes
	 */
	public static function SecondsToDateTime($p_i_seconds = 0) {
		$o_dateTime = new forestDateTime;
		$o_dateTime->addDateInterval(0, 0, 0, 0, 0, $p_i_seconds);	
		return $o_dateTime;
	}
	
	/**
	 * convert unix timestamp to DateTime value
	 *
	 * @param integer $p_i_unixTimestmp
	 *
	 * @return forestDateTime
	 *
	 * @access public
	 * @static yes
	 */
	public static function UnixTimestampToDateTime($p_i_unixTimestmp = 0) {
		$o_dateTime = new forestDateTime;
		$o_dateTime->DateTime->value->setTimestamp($p_i_unixTimestmp);
		return $o_dateTime;
	}
	
	/**
	 * get amount of days of a month, considering leap year too
	 *
	 * @param integer $p_i_month
	 * @param integer $p_i_year
	 *
	 * @return integer
	 *
	 * @access public
	 * @static yes
	 */
	public static function AmountDaysMonth($p_i_month = 0, $p_i_year = 0) {
		$a_daysMonth = array(0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		
		if ($p_i_month == 2) {
			if (forestDateTime::LeapYear($p_i_year)) {
				return 29;
			} else {
				return $a_daysMonth[$p_i_month];
			}
		}

		if (($p_i_month >= 1) && ($p_i_month <= 12)) {
			return $a_daysMonth[$p_i_month];
		} else {
			return $a_daysMonth[0];
		}
	}
	
	/**
	 * check if year is a leap year
	 *
	 * @param integer $p_i_year
	 *
	 * @return bool  true - is leap year, false - is not leap year
	 *
	 * @access public
	 * @static yes
	 */
	public static function LeapYear($p_i_year = 0) {
		if (($p_i_year % 400) == 0) {
			return true;
		} else if ( ( ($p_i_year % 4) == 0) && ( ($p_i_year % 100) != 0) ) {
			return true;
		} else {
			return false;
		}
	}
}
?>