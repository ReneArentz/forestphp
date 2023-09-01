<?php
/**
 * forestData trait declaring general types of data and for reuse in all fphp classes and mods
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestphp.de>
 * @copyright   (c) 2019 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     0.9.0 beta
 * @link        http://www.forestphp.de/
 * @object-id   0x1 00003
 * @since       File available since Release 0.1.0 alpha
 * @deprecated  -
 *
 * @version log Version    	Developer	Date		Comment
 * 		0.1.0 alpha	renatus		2019-08-04	first build
 * 		0.1.5 alpha	renatus		2019-10-04	added forestLookup and forestCombination
 * 		0.9.0 beta	renatus		2020-01-28	changes for namespaces
 */

namespace fPHP\Roots;

use \fPHP\Roots\forestException as forestException;

trait forestData {
	/**
	 * write access to value, calling further __set method
	 *
	 * @param string $p_s_name  name of the property
	 * @param object $p_o_value  value which should be set
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	 public function __set($p_s_name, $p_o_value) {
		/* check write access */
		if ($this->$p_s_name->_b_write) {
			$this->$p_s_name->value = $p_o_value;	
		} else {
			throw new forestException('No write access to property [' . $p_s_name . ']');
		}
	}

	/**
	 * read access to value, calling further __get method
	 *
	 * @param string $p_s_name  name of the property
	 *
	 * @return object  object value which is stored with the property
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
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
	
	/**
	 * unset access to value, calling further __unset method
	 *
	 * @param string $p_s_name  name of the property
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __unset($p_s_name) {
		unset($this->$p_s_name->value);
	}
}

/**
 * string data container
 *
 * @throws forestException if error occurs
 * @access public
 * @static no
 */
class forestString {
	public $_b_read = false;
	public $_b_write = false;
	private $_s_value = 'NULL';
	
	/**
	 * write access to value
	 *
	 * @param string $p_s_name  name of the property
	 * @param string $p_o_value value which should be set
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __set($p_s_name, $p_o_value) {
		/* check if parameter is a string */
		if (is_string($p_o_value)) {
			$this->_s_value = strval($p_o_value);
		} else {
			throw new forestException('Parameter is not a string');
		}
	}

	/**
	 * read access to value
	 *
	 * @param string $p_s_name  name of the property
	 *
	 * @return string  string value which is stored with the property
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function &__get($p_s_name) {
		if (isset($this->_s_value)) {
			return $this->_s_value;
		} else { /* never happens */
			throw new forestException('Property->get is null');
		}
	}
	
	/**
	 * unset access to value
	 *
	 * @param string $p_s_name  name of the property
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __unset($p_s_name) {
		$this->_s_value = 'NULL';
	}

	/**
	 * constructor of forestString container
	 *
	 * @param string $p_o_default_value  default value for property if container is created
	 * @param bool $p_b_write  default setting for write access to property
	 * @param bool $p_b_read  default setting for read access to property
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __construct($p_o_default_value = null, $p_b_write = true, $p_b_read = true) {
		if (!is_null($p_o_default_value)) {
			$this->_s_value = strval($p_o_default_value);
		}
		
		$this->_b_read = $p_b_read;
		$this->_b_write = $p_b_write;
	}
}

/**
 * list data container
 *
 * @throws forestException if error occurs
 * @access public
 * @static no
 */
class forestList {
	public $_b_read = false;
	public $_b_write = false;
	private $_a_list_values = null;
	private $_s_value = 'NULL';
	
	/**
	 * write access to value
	 *
	 * @param string $p_s_name  name of the property
	 * @param string $p_o_value  value which should be set
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __set($p_s_name, $p_o_value) {
		/* check if parameter is a string */
		if (is_string($p_o_value)) {
			if ($p_o_value != 'NULL') {
				if (in_array($p_o_value, $this->_a_list_values)) {
					$this->_s_value = strval($p_o_value);
				} else {
					throw new forestException('Value[' . $p_o_value . '] is not in defined list[' . implode(',', $this->_a_list_values) . ']');
				}
			}
		} else {
			throw new forestException('Parameter is not a string');
		}
	}

	/**
	 * read access to value
	 *
	 * @param string $p_s_name  name of the property
	 *
	 * @return string  string value which is stored with the property
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function &__get($p_s_name) {
		if (isset($this->_s_value)) {
			return $this->_s_value;
		} else { /* never happens */
			throw new forestException('Property->get is null');
		}
	}
	
	/**
	 * unset access to value
	 *
	 * @param string $p_s_name  name of the property
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __unset($p_s_name) {
		$this->_s_value = 'NULL';
	}

	/**
	 * constructor of forestList container
	 *
	 * @param array $p_a_list_values  list values which can be choosen later
	 * @param string $p_o_default_value  default value for list property if container is created
	 * @param bool $p_b_write  default setting for write access to property
	 * @param bool $p_b_read  default setting for read access to property
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __construct(array $p_a_list_values, $p_o_default_value = null, $p_b_write = true, $p_b_read = true) {
		$this->_a_list_values = $p_a_list_values;
		
		if (!is_null($p_o_default_value)) {
			if (in_array($p_o_default_value, $this->_a_list_values)) {
				$this->_s_value = strval($p_o_default_value);
			} else {
				throw new forestException('Value[' . $p_o_default_value . '] is not in defined list[' . implode(',', $this->_a_list_values) . ']');
			}
		}
		
		$this->_b_read = $p_b_read;
		$this->_b_write = $p_b_write;
	}
}

/**
 * numericstring data container
 *
 * @throws forestException if error occurs
 * @access public
 * @static no
 */
class forestNumericString {
	public $_b_read = false;
	public $_b_write = false;
	private $_i_value = 0;

	/**
	 * write access to value
	 *
	 * @param string $p_s_name  name of the property
	 * @param integer $p_o_value  value which should be set
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __set($p_s_name, $p_o_value) {
		/* check if parameter is numeric */
		if (is_numeric($p_o_value)) {
			$this->_i_value = intval($p_o_value);
		} else {
			throw new forestException('Parameter is not a numeric string');
		}	
	}

	/**
	 * read access to value
	 *
	 * @param string $p_s_name  name of the property
	 *
	 * @return integer  integer value which is stored with the property
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function &__get($p_s_name) {
		if (isset($this->_i_value)) {
			return $this->_i_value;
		} else { /* never happens */
			throw new forestException('Property->get is null');
		}
	}
	
	/**
	 * unset access to value
	 *
	 * @param string $p_s_name  name of the property
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __unset($p_s_name) {
		$this->_i_value = 0;
	}
	
	/**
	 * constructor of forestNumericString container
	 *
	 * @param integer $p_o_default_value  default value for property if container is created
	 * @param bool $p_b_write  default setting for write access to property
	 * @param bool $p_b_read  default setting for read access to property
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __construct($p_o_default_value = null, $p_b_write = true, $p_b_read = true) {
		if (!is_null($p_o_default_value)) {
			$this->_i_value = intval($p_o_default_value);
		}
		
		$this->_b_read = $p_b_read;
		$this->_b_write = $p_b_write;
	}
}

/**
 * integer data container
 *
 * @throws forestException if error occurs
 * @access public
 * @static no
 */
class forestInt {
	public $_b_read = false;
	public $_b_write = false;
	private $_i_value = 0;

	/**
	 * write access to value
	 *
	 * @param string $p_s_name  name of the property
	 * @param integer $p_o_value  value which should be set
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __set($p_s_name, $p_o_value) {
		/* check if parameter is an integer */
		if (is_int($p_o_value)) {
			$this->_i_value = intval($p_o_value);
		} else { var_dump($p_s_name); var_dump($p_o_value);
			throw new forestException('Parameter is not an integer');
		}	
	}

	/**
	 * read access to value
	 *
	 * @param string $p_s_name  name of the property
	 *
	 * @return integer  integer value which is stored with the property
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function &__get($p_s_name) {
		if (isset($this->_i_value)) {
			return $this->_i_value;
		} else { /* never happens */
			throw new forestException('Property->get is null');
		}
	}
	
	/**
	 * unset access to value
	 *
	 * @param string $p_s_name  name of the property
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __unset($p_s_name) {
		$this->_i_value = 0;
	}

	/**
	 * constructor of forestInt container
	 *
	 * @param integer $p_o_default_value  default value for property if container is created
	 * @param bool $p_b_write  default setting for write access to property
	 * @param bool $p_b_read  default setting for read access to property
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __construct($p_o_default_value = null, $p_b_write = true, $p_b_read = true) {
		if (!is_null($p_o_default_value)) {
			$this->_i_value = intval($p_o_default_value);
		}
		
		$this->_b_read = $p_b_read;
		$this->_b_write = $p_b_write;
	}
}

/**
 * float data container
 *
 * @throws forestException if error occurs
 * @access public
 * @static no
 */
class forestFloat {
	public $_b_read = false;
	public $_b_write = false;
	private $_f_value = 0.0;

	/**
	 * write access to value
	 *
	 * @param string $p_s_name  name of the property
	 * @param float $p_o_value  value which should be set
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __set($p_s_name, $p_o_value) {
		/* check if parameter is a float */
		if (is_float($p_o_value)) {
			$this->_f_value = floatval($p_o_value);
		} else {
			throw new forestException('Parameter is not a float');
		}	
	}

	/**
	 * read access to value
	 *
	 * @param string $p_s_name  name of the property
	 *
	 * @return float  float value which is stored with the property
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function &__get($p_s_name) {
		if (isset($this->_f_value)) {
			return $this->_f_value;
		} else { /* never happens */
			throw new forestException('Property->get is null');
		}
	}
	
	/**
	 * unset access to value
	 *
	 * @param string $p_s_name  name of the property
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __unset($p_s_name) {
		$this->_f_value = 0.0;
	}

	/**
	 * constructor of forestFloat container
	 *
	 * @param float $p_o_default_value  default value for property if container is created
	 * @param bool $p_b_write  default setting for write access to property
	 * @param bool $p_b_read  default setting for read access to property
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __construct($p_o_default_value = null, $p_b_write = true, $p_b_read = true) {
		if (!is_null($p_o_default_value)) {
			$this->_f_value = floatval($p_o_default_value);
		}
		
		$this->_b_read = $p_b_read;
		$this->_b_write = $p_b_write;
	}
}

/**
 * bool data container
 *
 * @throws forestException if error occurs
 * @access public
 * @static no
 */
class forestBool {
	public $_b_read = false;
	public $_b_write = false;
	private $_b_value = 0;

	/**
	 * write access to value
	 *
	 * @param string $p_s_name  name of the property
	 * @param bool $p_o_value  value which should be set
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __set($p_s_name, $p_o_value) {
		/* check if parameter is boolean */
		if (is_bool($p_o_value) || ($p_o_value == 0) || ($p_o_value == 1)) {
			$this->_b_value = boolval($p_o_value);
			
			if ($this->_b_value) {
				$this->_b_value = 1;
			} else {
				$this->_b_value = 0;
			}
		} else { /* never happens */
			throw new forestException('Parameter is not boolean');
		}	
	}

	/**
	 * read access to value
	 *
	 * @param string $p_s_name  name of the property
	 *
	 * @return bool  bool value which is stored with the property
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function &__get($p_s_name) {
		if (isset($this->_b_value)) {
			return $this->_b_value;
		} else { /* never happens */
			throw new forestException('Property->get is null');
		}
	}
	
	/**
	 * unset access to value
	 *
	 * @param string $p_s_name  name of the property
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __unset($p_s_name) {
		$this->_b_value = 0;
	}

	/**
	 * constructor of forestBool container
	 *
	 * @param bool $p_o_default_value  default value for property if container is created
	 * @param bool $p_b_write  default setting for write access to property
	 * @param bool $p_b_read  default setting for read access to property
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __construct($p_o_default_value = null, $p_b_write = true, $p_b_read = true) {
		if (!is_null($p_o_default_value)) {
			$this->_b_value = boolval($p_o_default_value);
		}
		
		if ($this->_b_value) {
			$this->_b_value = 1;
		} else {
			$this->_b_value = 0;
		}
		
		$this->_b_read = $p_b_read;
		$this->_b_write = $p_b_write;
	}
}

/**
 * array data container
 *
 * @throws forestException if error occurs
 * @access public
 * @static no
 */
class forestArray implements \IteratorAggregate {
	public $_b_read = false;
	public $_b_write = false;
	private $_a_value = array();

	/**
	 * write access to value
	 *
	 * @param string $p_s_name  name of the property
	 * @param array $p_o_value  value which should be set
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __set($p_s_name, $p_o_value) {
		/* check if parameter is an array */
		if (is_array($p_o_value)) {
			$this->_a_value = $p_o_value;
		} else {
			throw new forestException('Parameter is not an array');
		}	
	}

	/**
	 * read access to value
	 *
	 * @param string $p_s_name  name of the property
	 *
	 * @return array  array object which is stored with the property
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function &__get($p_s_name) {
		if (isset($this->_a_value)) {
			return $this->_a_value;
		} else { /* never happens */
			throw new forestException('Property->get is null');
		}
	}
	
	/**
	 * unset access to value
	 *
	 * @param string $p_s_name  name of the property
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __unset($p_s_name) {
		$this->_a_value = array();
	}
	
	/**
	 * constructor of forestArray container
	 *
	 * @param array $p_o_default_value  default value for property if container is created
	 * @param bool $p_b_write  default setting for write access to property
	 * @param bool $p_b_read  default setting for read access to property
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __construct($p_o_default_value = null, $p_b_write = true, $p_b_read = true) {
		if (!is_null($p_o_default_value)) {
			if (is_array($p_o_default_value)) {
				$this->_a_value = $p_o_default_value;
			}
		}
		
		$this->_b_read = $p_b_read;
		$this->_b_write = $p_b_write;
	}
	
	/**
	 * abstract necessary method from IteratorAggregate-Interface to create an external Iterator
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function getIterator() {
        return new \fPHP\Helper\forestIterator($this->_a_value);
    }
}

/**
 * object data container
 *
 * @throws forestException if error occurs
 * @access public
 * @static no
 */
class forestObject {
	public $_b_read = false;
	public $_b_write = false;
	private $_s_class = '';
	private $_o_value = null;
	private $_o_std_value = null;

	/**
	 * write access to value
	 *
	 * @param string $p_s_name  name of the property
	 * @param object $p_o_value  value which should be set
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __set($p_s_name, $p_o_value) {
		/* cast value parameter as object if class is stdClass */
		if ($this->_s_class == 'stdClass') {
			$p_o_value = (object)$p_o_value;
		}
		
		if (!is_null($p_o_value)) {
			/* check if parameter matches class */
			$s_class = 'NULL';
			
			if (is_object($p_o_value)) {
				$s_class = get_class($p_o_value);
				
				if (strrpos($s_class, '\\') !== false) {
					$s_class = substr($s_class, strrpos($s_class, '\\') + 1);
				}
			}
			
			if ($s_class == $this->_s_class) {
				$this->_o_value = $p_o_value;
			} else {
				$b_class_found = false;
				
				if (is_object($p_o_value)) {
					$a_classes = class_parents($p_o_value);
					
					if (count($a_classes) > 0) {
						foreach ($a_classes as $s_class) {
							if (strrpos($s_class, '\\') !== false) {
								$s_class = substr($s_class, strrpos($s_class, '\\') + 1);
							}
							
							if ($s_class == $this->_s_class) {
								$b_class_found = true;
							}
						}
					}
				}
				
				if ($b_class_found) {
					$this->_o_value = $p_o_value;
				} else {
					if ($p_o_value == 'NULL') {
						$this->_o_value = $p_o_value;
					} else {
						throw new forestException('Parameter[' . $s_class . '] is not a ' . $this->_s_class);
					}
				}
			}
		}
	}

	/**
	 * read access to value
	 *
	 * @param string $p_s_name  name of the property
	 *
	 * @return object  object which is stored with the property - if not set, return standard object
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function &__get($p_s_name) {
		$o_foo = $this->_o_std_value;
		
		if (isset($this->_o_value)) {
			$o_foo = $this->_o_value;
		}
		
		return $o_foo;
	}

	/**
	 * unset access to value
	 *
	 * @param string $p_s_name  name of the property
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __unset($p_s_name) {
		$this->_o_value = $this->_o_std_value;
	}
	
	/**
	 * constructor of forestObject container
	 *
	 * @param string $p_s_object  default class name for instances which can be stored in this container
	 * @param bool $p_b_write  default setting for write access to property
	 * @param bool $p_b_read  default setting for read access to property
	 * @param object $p_o_std_value  default value for property if container is created
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __construct($p_s_object, $p_b_write = true, $p_b_read = true, $p_o_std_value = null) {
		if (is_string($p_s_object)) {
			$this->_s_class = $p_s_object;
		} else {
			$this->_s_class = get_class($p_s_object);
			$this->_o_value = $p_s_object;
		}
		
		if (strrpos($this->_s_class, '\\') !== false) {
			$this->_s_class = substr($this->_s_class, strrpos($this->_s_class, '\\') + 1);
		}
		
		$this->_o_std_value = $p_o_std_value;
		$this->_b_read = $p_b_read;
		$this->_b_write = $p_b_write;
	}
}

/**
 * lookup data container
 *
 * @throws forestException if error occurs
 * @access public
 * @static no
 */
class forestLookup {
	public $_b_read = false;
	public $_b_write = false;
	private $_s_class = 'forestLookupData';
	private $_o_value = null;
	
	/**
	 * write access to value
	 *
	 * @param string $p_s_name  name of the property
	 * @param string $p_o_value  value which should be set, primary key of lookup record (string or numeric id string)
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __set($p_s_name, $p_o_value) {
		/* check if parameter is a string or numeric */
		if ( (is_string($p_o_value)) || (is_numeric($p_o_value)) ) {
			$this->_o_value->PrimaryValue = $p_o_value;
		} else {
			$s_class = 'NULL';
			
			if (is_object($p_o_value)) {
				$s_class = get_class($p_o_value);
				
				if (strrpos($s_class, '\\') !== false) {
					$s_class = substr($s_class, strrpos($s_class, '\\') + 1);
				}
			}
			
			if ($s_class == $this->_s_class) {
				$this->_o_value->PrimaryValue = $p_o_value->PrimaryValue;
			} else {
				throw new forestException('Parameter is not a string or is not numeric and is not of type forestLookup');
			}
		}
	}

	/**
	 * read access to value
	 *
	 * @param string $p_s_name  name of the property
	 *
	 * @return forestLookupData  forestLookupData object which is stored with the property
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function &__get($p_s_name) {
		return $this->_o_value;
	}

	/**
	 * unset access to value
	 *
	 * @param string $p_s_name  name of the property
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __unset($p_s_name) {
		/* nothing to do */
	}
	
	/**
	 * constructor of forestObject container
	 *
	 * @param forestLookupData $p_o_object  lookup data for container object
	 * @param bool $p_b_write  default setting for write access to property
	 * @param bool $p_b_read  default setting for read access to property
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __construct(\fPHP\Helper\forestLookupData $p_o_object, $p_b_write = true, $p_b_read = true) {
		$s_class = get_class($p_o_object);
			
		if (strrpos($s_class, '\\') !== false) {
			$s_class = substr($s_class, strrpos($s_class, '\\') + 1);
		}
			
		if ($s_class == $this->_s_class) {
			$this->_o_value = $p_o_object;
		}
		
		$this->_b_read = $p_b_read;
		$this->_b_write = $p_b_write;
	}
}
?>