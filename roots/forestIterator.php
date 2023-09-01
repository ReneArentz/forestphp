<?php
/**
 * iterator class for forestObjectList-class
 * necessary for iteration of a list-objects in a foreach-loop
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestphp.de>
 * @copyright   (c) 2019 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     0.9.0 stable
 * @link        http://www.forestphp.de/
 * @object-id   0x1 00008
 * @since       File available since Release 0.1.0 alpha
 * @deprecated  -
 *
 * @version log Version		Developer	Date		Comment
 * 		0.1.0 alpha	renatus		2019-08-04	first build
 */

namespace fPHP\Helper;

class forestIterator implements \Iterator {
	/* Fields */
	 
    private $_a_var;
    private $_i_index;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestIterator class
	 *
	 * @param array $p_a_value  array object
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
    public function __construct($p_a_value) {
		$this->_a_var = array();
		$this->_i_index = 0;
		
        if (is_array($p_a_value)) {
            $this->_a_var = $p_a_value;
        }
    }
	
	/**
	 * set index back to zero
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
    public function rewind() {
        $this->_i_index = 0;
    }
	
	/**
	 * get current array object based on internal index value using keys of array
	 *
	 * @return object
	 *
	 * @access public
	 * @static no
	 */
    public function current() {
        $foo = array_keys($this->_a_var);
        return $this->_a_var[$foo[$this->_i_index]];
    }

	/**
	 * get current key based on internal index value
	 *
	 * @return object  key value
	 *
	 * @access public
	 * @static no
	 */
    public function key() {
        $foo = array_keys($this->_a_var);
        return $foo[$this->_i_index];
    }
	
	/**
	 * get next array object based on internal index value using keys of array
	 *
	 * @return object  object value if there is a next object, false if there is no next object in list
	 *
	 * @access public
	 * @static no
	 */
    public function next() {
        $foo = array_keys($this->_a_var);
		
        if (isset($foo[++$this->_i_index])) {
            return $this->_a_var[$foo[$this->_i_index]];
        } else {
            return false;
        }
    }

	/**
	 * check if there is a valid object behind a key
	 *
	 * @return bool  true - object set behind key, false - no object set behind key
	 *
	 * @access public
	 * @static no
	 */
    public function valid() {
        $foo = array_keys($this->_a_var);
        return isset($foo[$this->_i_index]);
    }
}
?>