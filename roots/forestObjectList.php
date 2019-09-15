<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.1.3 (0x1 00007)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * simple class for using collections
 * creating lists of objects with array, index can be of any type
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.0 alpha	renatus		2019-08-04	first build
 */

class forestObjectList implements IteratorAggregate {
	/* Fields */
	
	private $_a_list;
	private $_s_objectClassName;
	
	/* Properties */
	
	public function Count() {
		return count($this->_a_list);
	}
	
	public function LastKey() {
		$foo = array_keys($this->_a_list);
		return end($foo);
	}

	/* Methods */
	
	/* on initializing the object-list, we need the class-name of the object */
	/* so we can be sure in our list are only objects of the same type/class later */
	public function __construct($p_s_objectClassName) {
		$this->_a_list = array();
		
		if (!is_string($p_s_objectClassName)) {
			throw new forestException('Parameter is not a string');
		}
		
		$this->_s_objectClassName = $p_s_objectClassName;
	}
	
	/* add an object to array list */
	/* controlling the class-name to be sure in array list are only objects of the same type/class */
	/* key value is optional */
	public function Add($p_o_object, $p_s_key = null) {
		if ($this->_s_objectClassName != 'stdClass') {		
			if (!is_object($p_o_object)) {
				throw new forestException('Parameter is not an object');
			}
	
			if (!is_a($p_o_object, $this->_s_objectClassName)) {
				throw new forestException('The object type [%0] for current object list must be of allowed type [%1]', array(get_class($p_o_object), $this->_s_objectClassName));
			}
		}

		if (empty($p_s_key)) {
			array_push($this->_a_list, $p_o_object);
		} else {
			$this->_a_list[$p_s_key] = $p_o_object;
		}
	}
	
	/* add an object to array list as first element */
	/* controlling the class-name to be sure in array list are only objects of the same type/class */
	/* key value is optional */
	public function AddFirst($p_o_object) {
		if ($this->_s_objectClassName != 'stdClass') {		
			if (!is_object($p_o_object)) {
				throw new forestException('Parameter is not an object');
			}
	
			if (!is_a($p_o_object, $this->_s_objectClassName)) {
				throw new forestException('The object type [%0] for current object list must be of allowed type [%1]', array(get_class($p_o_object), $this->_s_objectClassName));
			}
		}

		array_unshift($this->_a_list, $p_o_object);
	}
	
	/* delete an object to our list */
	/* controlling that the element exists in array list */
	public function Del($p_s_key = null) {
		if (array_key_exists($p_s_key, $this->_a_list)) {
			unset($this->_a_list[$p_s_key]);
		}
	}
	
	/* using magic method to get array list object or for iteration in a foreach-loop */
	/* key value is optional */
	function __get($p_s_key = null) { 
		if (array_key_exists($p_s_key, $this->_a_list)) {
			return $this->_a_list[$p_s_key];
		} else {
			throw new forestException('The object with the key[%0] is not included in the list', array($p_s_key));
		}
	}
	
	/* method to find out if object exists with key-parameter */
	public function Exists($p_s_key) {
		if (array_key_exists($p_s_key, $this->_a_list)) {
			return true;
		} else {
			return false;
		}
	}
	
	/* method to find out if array has element equal to parameter value */
	public function Has($p_s_value) {
		if (in_array($p_s_value, $this->_a_list)) {
			return true;
		} else {
			return false;
		}
	}
	
	/* abstract necessary method from IteratorAggregate-Interface to create an external Iterator */
	public function getIterator() {
        return new forestIterator($this->_a_list);
    }
	
	/* method to sort objects in the list, by using an individual function named CompareObjects */
	public function SortObjects() {
		usort($this->_a_list, array($this->_s_objectClassName, 'CompareObjects'));
	}
}
?>
