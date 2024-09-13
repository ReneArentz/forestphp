<?php
/**
 * date interval class with match validation of date interval strings
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestany.net>
 * @copyright   (c) 2024 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.0.0 stable
 * @link        https://forestany.net
 * @object-id   0x1 00013
 * @since       File available since Release 0.1.1 alpha
 * @deprecated  -
 *
 * @version log Version			Developer	Date		Comment
 * 				0.1.1 alpha		renea		2019-08-09	added to framework        
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

class forestDateInterval {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $y;
	private $m;
	private $d;
	private $h;
	private $i;
	private $s;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestDateInterval class
	 *
	 * @param string $p_s_dateinterval  date interval string, e.g. P2D, P1Y3M18DT5H16M33S, PT3H30S, ...
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
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
	
	/**
	 * set date interval values with PHP DateInterval object
	 *
	 * @param DateInterval $p_o_dateInterval
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function SetDateInterval(\DateInterval $p_o_dateInterval) {
		$this->y->value = $p_o_dateInterval->y;
		$this->m->value = $p_o_dateInterval->m;
		$this->d->value = $p_o_dateInterval->d;
		$this->h->value = $p_o_dateInterval->h;
		$this->i->value = $p_o_dateInterval->i;
		$this->s->value = $p_o_dateInterval->s;
	}
	
	/**
	 * returns date interval string with translation
	 *
	 * @return string
	 *
	 * @access public
	 * @static no
	 */
	public function __toString() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
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