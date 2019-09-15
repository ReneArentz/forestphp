<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.1.3 (0x1 00008)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * iterator class for forestObjectList-class
 * necessary for iteration of a list-objects in a foreach-loop
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.0 alpha	renatus		2019-08-04	first build
 */

class forestIterator implements Iterator {
	/* Fields */
	 
    private $_a_var;
    private $_i_index;
	
	/* Properties */
	
	/* Methods */
	 
    function __construct($p_a_value) {
		$this->_a_var = array();
		$this->_i_index = 0;
		
        if (is_array($p_a_value)) {
            $this->_a_var = $p_a_value;
        }
    }
	
    public function rewind() {
        $this->_i_index = 0;
    }

    public function current() {
        $foo = array_keys($this->_a_var);
        return $this->_a_var[$foo[$this->_i_index]];
    }

    public function key() {
        $foo = array_keys($this->_a_var);
        return $foo[$this->_i_index];
    }

    public function next() {
        $foo = array_keys($this->_a_var);
		
        if (isset($foo[++$this->_i_index])) {
            return $this->_a_var[$foo[$this->_i_index]];
        } else {
            return false;
        }
    }

    public function valid() {
        $foo = array_keys($this->_a_var);
        return isset($foo[$this->_i_index]);
    }
}
?>