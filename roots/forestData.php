<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.7.0 (0x1 00003)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * forestData trait declaring general types of data and for reuse in all fphp classes and mods
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.0 alpha	renatus		2019-08-04	first build
 * 0.1.5 alpha	renatus		2019-10-04	added forestLookup and forestCombination
 */

trait forestData {
	/*write access to value */
	public function __set($p_s_name, $p_o_value) {
		// check write access
		if ($this->$p_s_name->_b_write) {
			$this->$p_s_name->value = $p_o_value;	
		} else {
			throw new forestException('No write access to property [' . $p_s_name . ']');
		}
	}

	/* read access to value */
	public function &__get($p_s_name) {
		$o_foo = null;
		
		/* check read access */
		if ($this->$p_s_name->_b_read) {
			$o_foo = $this->$p_s_name->value;
		} else {
			throw new forestException('No read access to property [' . $p_s_name . ']');
		}
		
		return $o_foo;
	}
	
	/* unset access to value */
	public function __unset($p_s_name) {
		unset($this->$p_s_name->value);
	}
}

/* get value of 2-dimensional key array */
function get($p_o_var, $p_s_key) {
    if (array_key_exists($p_s_key, $p_o_var)) {
    	return $p_o_var[$p_s_key];
    }
	
    return null;
}

/*check if forestString is empty - even if 'NULL' is set */
function issetStr($p_s_str) {
	if ( (empty($p_s_str)) || ($p_s_str == 'NULL') ) {
		return false;
	} else {
		return true;
	}
}

function debugArray($p_a_value) {
	echo '<br /><pre>'; print_r($p_a_value); echo '</pre><br />';
}

function getClass($p_o_value) {
	return get_class($p_o_value);
}

/* string data container */
class forestString {
	public $_b_read = false;
	public $_b_write = false;
	private $_s_value = 'NULL';
	
	public function __set($p_s_name, $p_o_value) {
		/* check if parameter is a string */
		if (is_string($p_o_value)) {
			$this->_s_value = strval($p_o_value);
		} else {
			throw new forestException('Parameter is not a string');
		}
	}

	public function &__get($p_s_name) {
		if (isset($this->_s_value)) {
			return $this->_s_value;
		} else { /* never happens */
			throw new forestException('Property->get is null');
		}
	}
	
	public function __unset($p_s_name) {
		$this->_s_value = 'NULL';
	}

	public function __construct($p_o_default_value = null, $p_b_write = true, $p_b_read = true) {
		if (!is_null($p_o_default_value)) {
			$this->_s_value = $p_o_default_value;
		}
		
		$this->_b_read = $p_b_read;
		$this->_b_write = $p_b_write;
	}
}

/* list data container */
class forestList {
	public $_b_read = false;
	public $_b_write = false;
	private $_a_list_values = null;
	private $_s_value = 'NULL';
	
	public function __set($p_s_name, $p_o_value) {
		/* check if parameter is a string */
		if (is_string($p_o_value)) {
			if ($p_o_value != 'NULL') {
				if (in_array($p_o_value, $this->_a_list_values)) {
					$this->_s_value = strval($p_o_value);
				} else {
					throw new forestException('Value is not in defined list');
				}
			}
		} else {
			throw new forestException('Parameter is not a string');
		}
	}

	public function &__get($p_s_name) {
		if (isset($this->_s_value)) {
			return $this->_s_value;
		} else { /* never happens */
			throw new forestException('Property->get is null');
		}
	}
	
	public function __unset($p_s_name) {
		$this->_s_value = 'NULL';
	}

	public function __construct(array $p_a_list_values, $p_o_default_value = null, $p_b_write = true, $p_b_read = true) {
		$this->_a_list_values = $p_a_list_values;
		
		if (!is_null($p_o_default_value)) {
			if (in_array($p_o_default_value, $this->_a_list_values)) {
				$this->_s_value = $p_o_default_value;
			} else {
				throw new forestException('Value is not in defined list');
			}
		}
		
		$this->_b_read = $p_b_read;
		$this->_b_write = $p_b_write;
	}
}

/* numeric string data container */
class forestNumericString {
	public $_b_read = false;
	public $_b_write = false;
	private $_i_value = 0;

	public function __set($p_s_name, $p_o_value) {
		/* check if parameter is numeric */
		if (is_numeric($p_o_value)) {
			$this->_i_value = intval($p_o_value);
		} else {
			throw new forestException('Parameter is not a numeric string');
		}	
	}

	public function &__get($p_s_name) {
		if (isset($this->_i_value)) {
			return $this->_i_value;
		} else { /* never happens */
			throw new forestException('Property->get is null');
		}
	}

	public function __unset($p_s_name) {
		$this->_i_value = 0;
	}
	
	public function __construct($p_o_default_value = null, $p_b_write = true, $p_b_read = true) {
		if (!is_null($p_o_default_value)) {
			$this->_i_value = $p_o_default_value;
		}
		
		$this->_b_read = $p_b_read;
		$this->_b_write = $p_b_write;
	}
}

/* int data container */
class forestInt {
	public $_b_read = false;
	public $_b_write = false;
	private $_i_value = 0;

	public function __set($p_s_name, $p_o_value) {
		/* check if parameter is an integer */
		if (is_int($p_o_value)) {
			$this->_i_value = intval($p_o_value);
		} else {
			throw new forestException('Parameter is not an integer');
		}	
	}

	public function &__get($p_s_name) {
		if (isset($this->_i_value)) {
			return $this->_i_value;
		} else { /* never happens */
			throw new forestException('Property->get is null');
		}
	}
	
	public function __unset($p_s_name) {
		$this->_i_value = 0;
	}

	public function __construct($p_o_default_value = null, $p_b_write = true, $p_b_read = true) {
		if (!is_null($p_o_default_value)) {
			$this->_i_value = $p_o_default_value;
		}
		
		$this->_b_read = $p_b_read;
		$this->_b_write = $p_b_write;
	}
}

/* float data container */
class forestFloat {
	public $_b_read = false;
	public $_b_write = false;
	private $_f_value = 0.0;

	public function __set($p_s_name, $p_o_value) {
		/* check if parameter is a float */
		if (is_float($p_o_value)) {
			$this->_f_value = floatval($p_o_value);
		} else { var_dump($p_o_value);
			throw new forestException('Parameter is not a float');
		}	
	}

	public function &__get($p_s_name) {
		if (isset($this->_f_value)) {
			return $this->_f_value;
		} else { /* never happens */
			throw new forestException('Property->get is null');
		}
	}
	
	public function __unset($p_s_name) {
		$this->_f_value = 0.0;
	}

	public function __construct($p_o_default_value = null, $p_b_write = true, $p_b_read = true) {
		if (!is_null($p_o_default_value)) {
			$this->_f_value = $p_o_default_value;
		}
		
		$this->_b_read = $p_b_read;
		$this->_b_write = $p_b_write;
	}
}

/* bool data container */
class forestBool {
	public $_b_read = false;
	public $_b_write = false;
	private $_b_value = 0;

	public function __set($p_s_name, $p_o_value) {
		/* check if parameter is boolean */
		if (is_bool($p_o_value) || ($p_o_value == 0) || ($p_o_value == 1)) {
			$this->_b_value = boolval($p_o_value);
		} else { /* never happens */
			throw new forestException('Parameter is not boolean');
		}	
	}

	public function &__get($p_s_name) {
		if (isset($this->_b_value)) {
			return $this->_b_value;
		} else { /* never happens */
			throw new forestException('Property->get is null');
		}
	}
	
	public function __unset($p_s_name) {
		$this->_b_value = 0;
	}

	public function __construct($p_o_default_value = null, $p_b_write = true, $p_b_read = true) {
		if (!is_null($p_o_default_value)) {
			$this->_b_value = $p_o_default_value;
		}
		
		$this->_b_read = $p_b_read;
		$this->_b_write = $p_b_write;
	}
}

/* array data container */
class forestArray implements IteratorAggregate {
	public $_b_read = false;
	public $_b_write = false;
	private $_a_value = array();

	public function __set($p_s_name, $p_o_value) {
		/* check if parameter is an array */
		if (is_array($p_o_value)) {
			$this->_a_value = $p_o_value;
		} else {
			throw new forestException('Parameter is not an array');
		}	
	}

	public function &__get($p_s_name) {
		if (isset($this->_a_value)) {
			return $this->_a_value;
		} else { /* never happens */
			throw new forestException('Property->get is null');
		}
	}
	
	public function __unset($p_s_name) {
		$this->_a_value = array();
	}
	
	public function __construct($p_o_default_value = null, $p_b_write = true, $p_b_read = true) {
		if (!is_null($p_o_default_value)) {
			if (is_array($p_o_default_value)) {
				$this->_a_value = $p_o_default_value;
			}
		}
		
		$this->_b_read = $p_b_read;
		$this->_b_write = $p_b_write;
	}
	
	/* abstract necessary method from IteratorAggregate-Interface to create an external Iterator */
	public function getIterator() {
        return new forestIterator($this->_a_value);
    }
}

/* object data container */
class forestObject {
	public $_b_read = false;
	public $_b_write = false;
	private $_s_class = '';
	private $_o_value = null;
	private $_o_std_value = null;

	public function __set($p_s_name, $p_o_value) {
		/* cast value parameter as object if class is stdClass */
		if ($this->_s_class == 'stdClass') {
			$p_o_value = (object)$p_o_value;
		}
		
		if (!is_null($p_o_value)) {
			/* check if parameter matches class */
			if (is_a($p_o_value, $this->_s_class)) {
				$this->_o_value = $p_o_value;
			} else {
				if ($p_o_value == 'NULL') {
					$this->_o_value = $p_o_value;
				} else {
					throw new forestException('Parameter is not a ' . $this->_s_class);
				}
			}
		}
	}

	public function &__get($p_s_name) {
		$o_foo = $this->_o_std_value;
		
		if (isset($this->_o_value)) {
			$o_foo = $this->_o_value;
		}
		
		return $o_foo;
	}

	public function __unset($p_s_name) {
		$this->_o_value = $this->_o_std_value;
	}
	
	public function __construct($p_s_object, $p_b_write = true, $p_b_read = true, $p_o_std_value = null) {
		if (is_string($p_s_object)) {
			$this->_s_class = $p_s_object;
		} else {
			$this->_s_class = get_class($p_s_object);
			$this->_o_value = $p_s_object;
		}
		
		$this->_o_std_value = $p_o_std_value;
		$this->_b_read = $p_b_read;
		$this->_b_write = $p_b_write;
	}
}

/* lookup data container */
class forestLookup {
	public $_b_read = false;
	public $_b_write = false;
	private $_s_class = 'forestLookupData';
	private $_o_value = null;
	
	public function __set($p_s_name, $p_o_value) {
		/* check if parameter is a string or numeric */
		if ( (is_string($p_o_value)) || (is_numeric($p_o_value)) ) {
			$this->_o_value->PrimaryValue = $p_o_value;
		} else {
			if (is_a($p_o_value, $this->_s_class)) {
				$this->_o_value->PrimaryValue = $p_o_value->PrimaryValue;
			} else {
				throw new forestException('Parameter is not a string or is not numeric and is not of type forestLookup');
			}
		}
	}

	public function &__get($p_s_name) {
		return $this->_o_value;
	}

	public function __unset($p_s_name) {
		
	}
	
	public function __construct(forestLookupData $p_o_object, $p_b_write = true, $p_b_read = true) {
		if (is_a($p_o_object, $this->_s_class)) {
			$this->_o_value = $p_o_object;
		}
		
		$this->_b_read = $p_b_read;
		$this->_b_write = $p_b_write;
	}
	
	/*public function SetLookupData(forestLookupData $p_o_object) {
		if ($this->$p_s_name->_b_write) {
			if (is_a($p_o_object, $this->_s_class)) {
				$this->_o_value = $p_o_object;
			} else {
				throw new forestException('Parameter is not a ' . $this->_s_class);
			}
		} else {
			throw new forestException('No write access to property [forestLookup]');
		}
	}*/
}

/* combination data container, same as forestString */
class forestCombination extends forestString {
	
}
?>