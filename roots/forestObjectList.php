<?php
/**
 * simple class for using collections
 * creating lists of objects with array, index can be of any type
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestany.net>
 * @copyright   (c) 2024 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.0.0 stable
 * @link        https://forestany.net
 * @object-id   0x1 00007
 * @since       File available since Release 0.1.0 alpha
 * @deprecated  -
 *
 * @version log Version			Developer	Date		Comment
 * 				0.1.0 alpha		renea		2019-08-04	first build
 * 				0.9.0 beta		renea		2020-01-30	changes for namespaces
 */

namespace fPHP\Helper;

use \fPHP\Roots\forestException as forestException;

class forestObjectList implements \IteratorAggregate {
	/* Fields */
	
	private $_a_list;
	private $_s_objectClassName;
	
	/* Properties */
	
	/**
	 * property function count
	 *
	 * @return integer  amount of objects in list
	 *
	 * @access public
	 * @static no
	 */
	public function Count() {
		return count($this->_a_list);
	}
	
	/**
	 * property function firstkey to get the first element by sorting keys with array_keys
	 *
	 * @return object  first object in list
	 *
	 * @access public
	 * @static no
	 */
	public function FirstKey() {
		$foo = array_keys($this->_a_list);
		return reset($foo);
	}
	
	/**
	 * property function lastkey to get the last element by sorting keys with array_keys
	 *
	 * @return object  last object in list
	 *
	 * @access public
	 * @static no
	 */
	public function LastKey() {
		$foo = array_keys($this->_a_list);
		return end($foo);
	}

	/* Methods */
	
	/**
	 * on initializing the object-list, we need the class-name of the object
	 * so we can be sure in our list are only objects of the same type/class later
	 *
	 * @param string $p_s_objectClassName  class name of objects which will be accepted to be added to the list
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct($p_s_objectClassName) {
		$this->_a_list = array();
		
		if (!is_string($p_s_objectClassName)) {
			throw new forestException('Parameter is not a string');
		}
		
		if (strrpos($p_s_objectClassName, '\\') !== false) {
			$p_s_objectClassName = substr($p_s_objectClassName, strrpos($p_s_objectClassName, '\\') + 1);
		}
		
		$this->_s_objectClassName = $p_s_objectClassName;
	}
	
	/**
	 * add an object to array list
	 * controlling the class-name to be sure in array list are only objects of the same type/class
	 *
	 * @param object $p_o_object  object which will be stored in list
	 * @param string $p_s_key  key name for index, optional
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function Add($p_o_object, $p_s_key = null) {
		if ($this->_s_objectClassName != 'stdClass') {		
			if (!is_object($p_o_object)) {
				throw new forestException('Parameter is not an object');
			}
			
			$s_class = get_class($p_o_object);
			
			if (strrpos($s_class, '\\') !== false) {
				$s_class = substr($s_class, strrpos($s_class, '\\') + 1);
			}
			
			if ($s_class != $this->_s_objectClassName) {
				throw new forestException('The object type [%0] for current object list must be of allowed type [%1]', array(get_class($p_o_object), $this->_s_objectClassName));
			}
		}

		if (empty($p_s_key)) {
			array_push($this->_a_list, $p_o_object);
		} else {
			$this->_a_list[$p_s_key] = $p_o_object;
		}
	}
	
	/**
	 * add an object to array list as first element
	 * controlling the class-name to be sure in array list are only objects of the same type/class
	 *
	 * @param object $p_o_object  object which will be stored in list
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function AddFirst($p_o_object) {
		if ($this->_s_objectClassName != 'stdClass') {		
			if (!is_object($p_o_object)) {
				throw new forestException('Parameter is not an object');
			}
			
			$s_class = get_class($p_o_object);
			
			if (strrpos($s_class, '\\') !== false) {
				$s_class = substr($s_class, strrpos($s_class, '\\') + 1);
			}
			
			if ($s_class != $this->_s_objectClassName) {
				throw new forestException('The object type [%0] for current object list must be of allowed type [%1]', array(get_class($p_o_object), $this->_s_objectClassName));
			}
		}

		array_unshift($this->_a_list, $p_o_object);
	}
	
	/**
	 * delete an object in the list
	 * controlling that the element exists in array list
	 *
	 * @param string $p_s_key  key name for index, optional
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function Del($p_s_key = null) {
		if (array_key_exists($p_s_key, $this->_a_list)) {
			unset($this->_a_list[$p_s_key]);
		}
	}
	
	/**
	 * using magic method to get array list object or for iteration in a foreach-loop
	 *
	 * @param string $p_s_key  key name for index, optional
	 *
	 * @return object  object in list found by index key
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __get($p_s_key = null) { 
		if (array_key_exists($p_s_key, $this->_a_list)) {
			return $this->_a_list[$p_s_key];
		} else {
			throw new forestException('The object with the key[%0] is not included in the list', array($p_s_key));
		}
	}
	
	/**
	 * method to find out if object exists by key
	 *
	 * @param string $p_s_key  key name for index
	 *
	 * @return bool  true - object exists in list, false - object does not exists in list
	 *
	 * @access public
	 * @static no
	 */
	public function Exists($p_s_key) {
		if (array_key_exists($p_s_key, $this->_a_list)) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * method to find out if array has element equal to parameter value
	 *
	 * @param object $p_o_value  object for comparsion
	 *
	 * @return bool  true - object exists in list, false - object does not exists in list
	 *
	 * @access public
	 * @static no
	 */
	public function Has($p_o_value) {
		if (in_array($p_o_value, $this->_a_list)) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * abstract necessary method from IteratorAggregate-Interface to create an external Iterator
	 *
	 * @return forestIterator  instance of forestIterator to loop through list
	 *
	 * @access public
	 * @static no
	 */
	public function getIterator() {
        return new \fPHP\Helper\forestIterator($this->_a_list);
    }
	
	/**
	 * method to sort objects in the list, by using an individual function named CompareObjects
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function SortObjects() {
		usort($this->_a_list, array($this->_s_objectClassName, 'CompareObjects'));
	}
}
?>
