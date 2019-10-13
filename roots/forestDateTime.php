<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.1.5 (0x1 00012)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * capsule class for creating DateTime-objects with own rules and easy uses
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.1 alpha	renatus		2019-08-08	added to framework
 */

class forestDateTime {
	use forestData;
	
	/* Fields */
	
	private $DateTime;
	private $Format;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct($p_s_format = null, $p_i_year = 0, $p_i_month = 0 , $p_i_day = 0, $p_i_hour = 0, $p_i_minute = 0, $p_i_second = 0, $p_o_timezone = null) {
		$this->DateTime = new forestObject(new DateTime);
		$this->Format = new forestString;
		
		if (!is_null($p_s_format)) {
			if (is_string($p_s_format)) {
				$this->Format->value = $p_s_format;
			} else {
				throw new forestException('Paramater is not a string');
			}
		}
		
		if (!(($p_i_year == 0) && ($p_i_month == 0) && ($p_i_day == 0))) {
			$this->DateTime->value->setDate($p_i_year, $p_i_month, $p_i_day);
		}
		
		if (!(($p_i_hour == 0) && ($p_i_minute == 0) && ($p_i_second == 0))) {
			$this->DateTime->value->setTime($p_i_hour, $p_i_minute, $p_i_second);
		}
		
		if (!is_null($p_o_timezone)) {
			$this->DateTime->value->setTimezone($p_o_timezone);
		}
	}
	
	function __toString() {
		if (issetStr($this->Format->value)) {
			return $this->DateTime->value->format($this->Format->value);
		} else {
			return $this->DateTime->value->format(DateTime::ATOM);
		}
	}
	
	/* manual ToString-method for returning DateTime in another format than was set on creation of the object */
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
			return $this->DateTime->value->format(DateTime::ATOM);
		}
	}
	
	/* add interval to datetime value */
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
	
	/* sub interval to datetime value */
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

	public static function SecondsToDateTime($p_i_seconds = 0) {
		$o_dateTime = new forestDateTime;
		$o_dateTime->addDateInterval(0, 0, 0, 0, 0, $p_i_seconds);	
		return $o_dateTime;
	}
	
	public static function UnixTimestampToDateTime($p_i_unixTimestmp = 0) {
		$o_dateTime = new forestDateTime;
		$o_dateTime->DateTime->value->setTimestamp($p_i_unixTimestmp);
		return $o_dateTime;
	}
	
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