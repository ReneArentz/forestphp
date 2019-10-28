<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.2.0 (0x1 00010)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * class for holding information about sorting columns of current page view
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.2 alpha	renatus		2019-08-21	added to framework	
 */

class forestSort {
	use forestData;
	
	/* Fields */
		
	private $Column;
	private $Direction;
	private $Temp;
	private $ColumnName;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct($p_s_column, $p_b_direction) {
		$this->Column = new forestString($p_s_column);
		$this->Direction = new forestBool($p_b_direction);
		$this->Temp = new forestBool;
		$this->ColumnName = new forestString;
	}
	
	/* returns sort link */
	function __toString() {
		$o_glob = forestGlobals::init();
		
		/* if sort object is not temporary, then its origin is of URL information */
		if (!$this->Temp->value) {
			/* create sort link */
			$s_columnLink = '<a class="btn btn-default" href="' . forestLink::Link($o_glob->URL->Branch, $o_glob->URL->Action, $this->parameterArray($this->Column->value)) . '">' . $this->ColumnName->value . '</a>';
			
			/* calculate direction glyphicon */
			$s_direction = ($this->Direction->value) ? '<span class="glyphicon glyphicon-sort-by-attributes"></span>' : '<span class="glyphicon glyphicon-sort-by-attributes-alt"></span>';
			
			/* create direction glyphicon */
			$s_directionLink = ' <a class="btn btn-default" href="' . forestLink::Link($o_glob->URL->Branch, $o_glob->URL->Action, $this->parameterArray()) . '">' . $s_direction . '</a>';
			
			return '<div class="btn-group">' . $s_columnLink . $s_directionLink . '</div>';
		} else {
			/* temporary sort object, need to create new forestSort before rendering */
			$o_sort = new forestSort($this->Column->value, true);
			$o_sort->ColumnName = $this->ColumnName->value;
			
			return '<div class="btn-group"><a class="btn btn-default" href="' . forestLink::Link($o_glob->URL->Branch, $o_glob->URL->Action, $this->parameterArray($o_sort)) . '">' . $this->ColumnName->value . ' <span class="glyphicon glyphicon-sort"></span></a></div>';
		}
	}
	
	/* create sort columns parameters with opposite direction of current sort column */
	private function parameterArray($p_o_sort = null) {
		$o_glob = forestGlobals::init();
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
	
	/* alternative call of returning sort link with title parameter */
	public function ToString($p_s_columnName) {
		$this->ColumnName->value = $p_s_columnName;
		return strval($this);
	}
}
?>