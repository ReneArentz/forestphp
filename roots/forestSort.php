<?php
/**
 * class for holding information about sorting columns of current page view
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestany.net>
 * @copyright   (c) 2024 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.1.0 stable
 * @link        https://forestany.net
 * @object-id   0x1 00010
 * @since       File available since Release 0.1.2 alpha
 * @deprecated  -
 *
 * @version log Version			Developer	Date		Comment
 * 				0.1.2 alpha		renea		2019-08-21	added to framework
 * 				1.1.0 stable	renea		2024-08-10	changes for bootstrap 5
 */

namespace fPHP\Branches;

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

class forestSort {
	use \fPHP\Roots\forestData;
	
	/* Fields */
		
	private $Column;
	private $Direction;
	private $Temp;
	private $ColumnName;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestSort class
	 *
	 * @param string $p_s_column  name of sql column/field
	 * @param bool $p_b_direction  true - ascending, false - descending
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct($p_s_column, $p_b_direction) {
		$this->Column = new forestString($p_s_column);
		$this->Direction = new forestBool($p_b_direction);
		$this->Temp = new forestBool;
		$this->ColumnName = new forestString;
	}
	
	/**
	 * create sort columns parameters with opposite direction of current sort column
	 *
	 * @param object $p_o_sort  column/field name or forestSort object
	 *
	 * @return array  parameters which can be used with forestLink
	 *
	 * @throws forestException if error occurs
	 * @access private 
	 * @static no
	 */
	private function parameterArray($p_o_sort = null) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$a_foo = array();
		
		foreach($o_glob->Sorts as $o_sort) {
			$b_foo2 = true;
			
			if ((!is_null($p_o_sort)) && (is_string($p_o_sort))) {
				/* skip parameter sort object */
				if ($o_sort->Column->value == $p_o_sort) {
					$b_foo2 = false;
				}
			}
			
			if ($b_foo2) {
				/* take over existing sort objects */
				$direction = ($o_sort->Column->value == $this->Column->value) ? !$o_sort->Direction->value : $o_sort->Direction->value;
				$a_foo['_' . $o_sort->Column->value] = ($direction) ? 'true' : 'false';
			}
		}
		
		if ((!is_null($p_o_sort)) && (is_object($p_o_sort))) {
			/* add new sort object */
			$a_foo['_' . $p_o_sort->Column->value] = ($p_o_sort->Direction->value) ? 'true' : 'false';
		}
		
		/* add other parameters which does not belong to sort information */
		foreach ($o_glob->URL->Parameters as $s_column => $s_value) {
			if ( ($s_column[0] != '_') && ($s_column != 'viewKey') && ($s_column != 'editKey') && ($s_column != 'deleteKey') ) {
				$a_foo[$s_column] = $s_value;
			}
		}
		
		return $a_foo;
	}
	
	/**
	 * returns sort link button
	 *
	 * @return string  html sort link button
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __toString() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* if sort object is not temporary, then its origin is of URL information */
		if (!$this->Temp->value) {
			/* create sort link */
			$s_columnLink = '<a class="btn btn-light text-nowrap" href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, $o_glob->URL->Action, $this->parameterArray($this->Column->value)) . '">' . $this->ColumnName->value . '</a>';
			
			/* calculate direction icon */
			$s_direction = ($this->Direction->value) ? '<span class="bi bi-sort-down-alt"></span>' : '<span class="bi bi-sort-down"></span>';
			
			/* create direction icon */
			$s_directionLink = ' <a class="btn btn-light text-nowrap" href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, $o_glob->URL->Action, $this->parameterArray()) . '">' . $s_direction . '</a>';
			
			return '<div class="btn-group">' . $s_columnLink . $s_directionLink . '</div>';
		} else {
			/* temporary sort object, need to create new forestSort before rendering */
			$o_sort = new \fPHP\Branches\forestSort($this->Column->value, true);
			$o_sort->ColumnName = $this->ColumnName->value;
			
			return '<div class="btn-group"><a class="btn btn-light text-nowrap" href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, $o_glob->URL->Action, $this->parameterArray($o_sort)) . '">' . $this->ColumnName->value . ' <span class="bi bi-arrow-down-up"></span></a></div>';
		}
	}
	
	/**
	 * alternative call of returning sort link with title parameter
	 *
	 * @return string  html sort link button
	 *
	 * @access public
	 * @static no
	 */
	public function ToString($p_s_columnName) {
		$this->ColumnName->value = $p_s_columnName;
		return strval($this);
	}
}
?>