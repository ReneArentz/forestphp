<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.1.2 (0x1 00013)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * date interval class with match validation of date interval strings
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.1 alpha	renatus		2019-08-09	added to framework
 */

class forestDateInterval {
	use forestData;
	
	/* Fields */
	
	private $y;
	private $m;
	private $d;
	private $h;
	private $i;
	private $s;
	
	/* Properties */
		
	/* Methods */
	
	public function __construct($p_s_dateinterval = null) {
		$this->y = new forestInt;
		$this->m = new forestInt;
		$this->d = new forestInt;
		$this->h = new forestInt;
		$this->i = new forestInt;
		$this->s = new forestInt;
		
		if ($p_s_dateinterval != null) {
			if ( (issetStr($p_s_dateinterval)) && ($p_s_dateinterval != '-') ) {
				if (!is_string($p_s_dateinterval)) {
					throw new forestException('Parameter is not a string');
				}
				
				if (!preg_match('/^ ( ( P ( ((\d)+ Y (\d)+ M ((\d)+ (W|D))?) | ((\d)+ (Y|M) (\d)+ (W|D)) | ((\d)+ (Y|M|W|D)) ) T ( ((\d)+ H (\d)+ M (\d)+ S) | ((\d)+ H (\d)+ (M|S)) | ((\d)+ M (\d)+ S) | ((\d)+ (H|M|S)) ) ) | ( PT ( ((\d)+ H (\d)+ M (\d)+ S) | ((\d)+ H (\d)+ (M|S)) | ((\d)+ M (\d)+ S) | ((\d)+ (H|M|S)) ) ) | ( P ( ((\d)+ Y (\d)+ M ((\d)+ (W|D))?) | ((\d)+ (Y|M) (\d)+ (W|D)) | ((\d)+ (Y|M|W|D)) ) ) ) $/x', $p_s_dateinterval)) {
					throw new forestException('Parameter[%0] does not match date interval format', array($p_s_dateinterval));
				}
				
				$a_info = preg_split('/[[\d]*/', $p_s_dateinterval, -1, PREG_SPLIT_NO_EMPTY);
				$a_values = preg_split('/[[A-Za-z]{1}/', $p_s_dateinterval, -1, PREG_SPLIT_NO_EMPTY);
				
				$i = 0;
				$s_mode = '';
				
				foreach($a_info as $s_char) {
					switch ($s_char) {
						case 'P':
							$s_mode = 'date';
							break;
						case 'T':
							$s_mode = 'time';
							break;
						
						case 'Y':
							$this->y->value = intval($a_values[$i]);
							$i++;
							break;
						case 'D':
							$this->d->value = intval($a_values[$i]);
							$i++;
							break;
						
						case 'H':
							$this->h->value = intval($a_values[$i]);
							$i++;
							break;
						case 'S':
							$this->s->value = intval($a_values[$i]);
							$i++;
							break;
					}
					
					if ($s_char == 'M') {
						if ($s_mode == 'date') {
							$this->m->value = intval($a_values[$i]);
							$i++;
						} else if ($s_mode == 'time') {
							$this->i->value = intval($a_values[$i]);
							$i++;
						}
					}
				}
			}
		}
	}
	
	public function SetDateInterval(DateInterval $p_o_dateInterval) {
		$this->y->value = $p_o_dateInterval->y;
		$this->m->value = $p_o_dateInterval->m;
		$this->d->value = $p_o_dateInterval->d;
		$this->h->value = $p_o_dateInterval->h;
		$this->i->value = $p_o_dateInterval->i;
		$this->s->value = $p_o_dateInterval->s;
	}

	function __toString() {
		$o_glob = forestGlobals::init();
		$s_foo = '';
		
		if ($this->y->value != 0) {
			$s_foo .= $this->y->value . ' ' . $o_glob->GetTranslation('dateIntervalYear', 1) . ' ';
		}
		
		if ($this->m->value != 0) {
			$s_foo .= $this->m->value . ' ' . $o_glob->GetTranslation('dateIntervalMonth', 1) . ' ';
		}
		
		if ($this->d->value != 0) {
			$s_foo .= $this->d->value . ' ' . $o_glob->GetTranslation('dateIntervalDay', 1) . ' ';
		}
		
		if ($this->h->value != 0) {
			$s_foo .= $this->h->value . ' ' . $o_glob->GetTranslation('dateIntervalHour', 1) . ' ';
		}
		
		if ($this->i->value != 0) {
			$s_foo .= $this->i->value . ' ' . $o_glob->GetTranslation('dateIntervalMinute', 1) . ' ';
		}
		
		if ($this->s->value != 0) {
			$s_foo .= $this->s->value . ' ' . $o_glob->GetTranslation('dateIntervalSecond', 1) . ' ';
		}
		
		return trim($s_foo);
	}
}	
?>