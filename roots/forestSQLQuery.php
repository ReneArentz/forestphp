<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.5.0 (0x1 0000A)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * class collection for generating one SQL-Query in an object-oriented way
 * this helps for object-oriented access on database-tables
 * for more information please read the documentation
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.0 alpha	renatus		2019-08-04	first build
 * 0.1.1 alpha	renatus		2019-08-10	added trunk and forestDateTime functionality
 * 0.1.5 alpha	renatus		2019-10-10	added forestLookup functionality
 * 0.2.0 beta	renatus		2019-10-15	added data definition language functionality
 */

class forestSQLQuery {
	use forestData;
	
	/* Fields */
	
	const SELECT = 'select';
	const INSERT = 'insert';
	const UPDATE = 'update';
	const DELETE = 'delete';
	const TRUNCATE = 'truncate';
	
	const CREATE = 'create';
	const DROP = 'drop';
	const ALTER = 'alter';
	
	private $BaseGateway;
	private $SqlType;
	private $Table;
	private $Query;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct(string $p_s_baseGateway, string $p_s_type, string $p_s_table) {
		/* take over construct parameters */
		$this->BaseGateway = new forestString($p_s_baseGateway, false);
		$this->SqlType = new forestString($p_s_type, false);
		$this->Table = new forestString($p_s_table, false);
		
		/* check base gateway parameter */
        switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				break;
			default:
				throw new forestException('Invalid BaseGateway[%0]', array($this->BaseGateway->value));
			break;
		}
		
		/* create query object */
		switch ($this->SqlType->value) {
			case self::SELECT:
				$this->Query = new forestObject(new forestSQLSelect($this), false);
			break;
			case self::INSERT:
				$this->Query = new forestObject(new forestSQLInsert($this), false);
			break;
			case self::UPDATE:
				$this->Query = new forestObject(new forestSQLUpdate($this), false);
			break;
			case self::DELETE:
				$this->Query = new forestObject(new forestSQLDelete($this), false);
			break;
			case self::TRUNCATE:
				$this->Query = new forestObject(new forestSQLTruncate($this), false);
			break;
			case self::CREATE:
				$this->Query = new forestObject(new forestSQLCreate($this), false);
			break;
			case self::DROP:
				$this->Query = new forestObject(new forestSQLDrop($this), false);
			break;
			case self::ALTER:
				$this->Query = new forestObject(new forestSQLAlter($this), false);
			break;
			default:
				throw new forestException('Invalid SqlType[%0]', array($this->SqlType->value));
			break;
		}
	}
	
	function __toString() {
		return '' . $this->Query->value;
	}
	
	public static function ColumnTypeAllocation(string $p_s_baseGateway, string $p_s_sqlType, &$p_s_columnType, &$p_i_columnLength, &$p_i_columnDecimalLength) {
		/* check base gateway parameter */
        switch ($p_s_baseGateway) {
			case forestBase::MariaSQL:
				break;
			default:
				throw new forestException('Invalid BaseGateway[%0]', array($p_s_baseGateway));
			break;
		}
		
		/* check sql type parameter */
		 switch ($p_s_sqlType) {
			case 'text [255]':
			case 'text':
			case 'integer [small]':
			case 'integer [int]':
			case 'integer [big]':
			case 'datetime':
			case 'time':
			case 'double':
			case 'decimal':
			case 'bool':
				break;
			default:
				throw new forestException('Invalid SqlType[%0]', array($p_s_sqlType));
			break;
		}
		
		$a_allocation = array();
		
		/* forestBase::MariaSQL */
		$a_allocation[forestBase::MariaSQL]['text [255]']['columnType'] = 'VARCHAR';
		$a_allocation[forestBase::MariaSQL]['text [255]']['columnLength'] = 255;
		$a_allocation[forestBase::MariaSQL]['text [255]']['decimalLength'] = null;
		
		$a_allocation[forestBase::MariaSQL]['text']['columnType'] = 'TEXT';
		$a_allocation[forestBase::MariaSQL]['text']['columnLength'] = null;
		$a_allocation[forestBase::MariaSQL]['text']['decimalLength'] = null;
		
		$a_allocation[forestBase::MariaSQL]['integer [small]']['columnType'] = 'SMALLINT';
		$a_allocation[forestBase::MariaSQL]['integer [small]']['columnLength'] = 6;
		$a_allocation[forestBase::MariaSQL]['integer [small]']['decimalLength'] = null;
		
		$a_allocation[forestBase::MariaSQL]['integer [int]']['columnType'] = 'INT';
		$a_allocation[forestBase::MariaSQL]['integer [int]']['columnLength'] = 10;
		$a_allocation[forestBase::MariaSQL]['integer [int]']['decimalLength'] = null;
		
		$a_allocation[forestBase::MariaSQL]['integer [big]']['columnType'] = 'BIGINT';
		$a_allocation[forestBase::MariaSQL]['integer [big]']['columnLength'] = 20;
		$a_allocation[forestBase::MariaSQL]['integer [big]']['decimalLength'] = null;
		
		$a_allocation[forestBase::MariaSQL]['datetime']['columnType'] = 'TIMESTAMP';
		$a_allocation[forestBase::MariaSQL]['datetime']['columnLength'] = null;
		$a_allocation[forestBase::MariaSQL]['datetime']['decimalLength'] = null;
		
		$a_allocation[forestBase::MariaSQL]['time']['columnType'] = 'TIME';
		$a_allocation[forestBase::MariaSQL]['time']['columnLength'] = null;
		$a_allocation[forestBase::MariaSQL]['time']['decimalLength'] = null;
		
		$a_allocation[forestBase::MariaSQL]['double']['columnType'] = 'DOUBLE';
		$a_allocation[forestBase::MariaSQL]['double']['columnLength'] = null;
		$a_allocation[forestBase::MariaSQL]['double']['decimalLength'] = null;
		
		$a_allocation[forestBase::MariaSQL]['decimal']['columnType'] = 'DECIMAL';
		$a_allocation[forestBase::MariaSQL]['decimal']['columnLength'] = 10;
		$a_allocation[forestBase::MariaSQL]['decimal']['decimalLength'] = 2;
		
		$a_allocation[forestBase::MariaSQL]['bool']['columnType'] = 'BIT';
		$a_allocation[forestBase::MariaSQL]['bool']['columnLength'] = 1;
		$a_allocation[forestBase::MariaSQL]['bool']['decimalLength'] = null;
		
		/* get column properties of allocation matrix */
		$p_s_columnType = $a_allocation[$p_s_baseGateway][$p_s_sqlType]['columnType'];
		$p_i_columnLength = $a_allocation[$p_s_baseGateway][$p_s_sqlType]['columnLength'];
		$p_i_columnDecimalLength = $a_allocation[$p_s_baseGateway][$p_s_sqlType]['decimalLength'];
	}
}

abstract class forestSQLQueryAbstract {
	use forestData;
	
	/* Fields */
	
	protected $Operators;
	protected $FilterOperators;
	protected $JoinTypes;
	protected $SqlAggregations;
	protected $SqlConstraints;
	protected $SqlIndexConstraints;
	protected $AlterOperations;
	protected $SqlColumnTypes;
	
	protected $BaseGateway;
	protected $SqlType;
	protected $AmountJoins;
	protected $Table;
	
	/* Properties */
		
	/* Methods */
	
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		$this->BaseGateway = new forestString($p_o_sqlQuery->BaseGateway, false);
		$this->SqlType = new forestString($p_o_sqlQuery->SqlType, false);
		$this->AmountJoins = new forestInt;
		$this->Table = new forestString($p_o_sqlQuery->Table);
		
		$this->Operators = new forestArray(array('=', '<', '<=', '>', '>=', '<>', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'IS', 'IS NOT'), false);
		$this->FilterOperators = new forestArray(array('AND', 'OR', 'XOR'), false);
		$this->JoinTypes = new forestArray(array('INNER JOIN', 'NATURAL JOIN', 'CROSS JOIN', 'OUTER JOIN', 'LEFT OUTER JOIN', 'RIGHT OUTER JOIN', 'FULL OUTER JOIN'), false);
		$this->SqlAggregations = new forestArray(array('AVG', 'COUNT', 'MAX', 'MIN', 'SUM'), false);
		$this->SqlConstraints = new forestArray(array('NULL', 'NOT NULL', 'UNIQUE', 'PRIMARY KEY', 'DEFAULT', 'INDEX', 'AUTO_INCREMENT', 'SIGNED', 'UNSIGNED'), false);
		$this->SqlIndexConstraints = new forestArray(array('UNIQUE', 'PRIMARY KEY', 'INDEX'), false);
		$this->AlterOperations = new forestArray(array('ADD', 'CHANGE', 'DROP'), false);
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				$this->SqlColumnTypes = new forestArray(array('VARCHAR', 'TEXT', 'SMALLINT', 'INT', 'BIGINT', 'TIMESTAMP', 'TIME', 'DOUBLE', 'DECIMAL', 'BIT'), false);
			break;
			default:
				throw new forestException('BaseGateway[%0] not implemented', array($this->BaseGateway->value));
			break;
		}
	}
	
	/* important parsing function, pretending SQL-Injection */
	public function ParseValue($p_s_value) {
		$o_glob = forestGlobals::init();
		
		if (is_null($p_s_value)) {
			$p_s_value = '';
		}
		
		if (is_a($p_s_value, 'forestDateTime')) {
			$p_s_value = $p_s_value->ToString();
		}
		
		if (is_a($p_s_value, 'forestLookupData')) {
			$p_s_value = $p_s_value->PrimaryValue;
		}
		
		if (is_string($p_s_value)) {
			if (strlen($p_s_value) == 0) {
				$p_s_value = 'NULL';
			}
			
			if ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == forestBase::MariaSQL) {
				$p_s_value = str_replace('\\', '\\\\', $p_s_value);
			}
			
			/* date conversion for sql query [dd.MM.yyyy] / [dd.MM.yyyy hh:mm:ss] */
			switch ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway) {
				case forestBase::MariaSQL:
					if (preg_match('/^ ((0[1-9])|(1[0-9])|2[0-9]|(3[0-1])) \. ((0[1-9])|(1[0-2])) \. (\d){4} $/x', $p_s_value)) {
						$s_foo = explode('.', $p_s_value);
						$p_s_value = $s_foo[2] . '-' . $s_foo[1] . '-' . $s_foo[0];
					} else if (preg_match('/^ ((0[1-9])|(1[0-9])|2[0-9]|(3[0-1])) \. ((0[1-9])|(1[0-2])) \. (\d){4} \s (0[0-9]|1[0-9]|2[0-3]) : ([0-5][0-9]) : ([0-5][0-9]) $/x', $p_s_value)) {
						$s_foo  = explode(' ', $p_s_value);
						$s_foo2 = explode('.', $s_foo[0]);
						$p_s_value = $s_foo2[2] . '-' . $s_foo2[1] . '-' . $s_foo2[0] . ' ' . $s_foo[1];
					}
				break;
				default:
					throw new forestException('BaseGateway[%0] not implemented', array($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway));
				break;
			}
		}
		
		if ((!is_int($p_s_value)) && (!is_float($p_s_value)) && ($p_s_value != 'NULL')) {
			$p_s_value = addslashes($p_s_value);
			
			/*  when magic_quotes are on, all ' (single-quote), " (double quote), \ (backslash) and NULL's are escaped with a backslash automatically. */
			if (get_magic_quotes_gpc()) {
				/* un-quotes a quoted string */
				$p_s_value = stripslashes($p_s_value);
			}
			
			/* additionally utf8 en/decoding + surround value with single quotes */
			if (!is_null($o_glob->Trunk)) {
				if ($o_glob->Trunk->IncContentUTF8Decode) {
					$p_s_value = "'" . utf8_decode($p_s_value) . "'";
				} else if ($o_glob->Trunk->IncContentUTF8Encode) {
					$p_s_value = "'" . utf8_encode($p_s_value) . "'";
				} else {
					$p_s_value = "'" . $p_s_value . "'";
				}
			} else {
				/* surround value with single quotes */
				$p_s_value = "'" . $p_s_value . "'";
			}
		}
		
		return $p_s_value;
	}
}

class forestSQLSelect extends forestSQLQueryAbstract {
	use forestData;

	/* Fields */
	
	private $Distinct;
	private $Columns;
	private $Joins;
	private $Where;
	private $GroupBy;
	private $Having;
	private $OrderBy;
	private $Limit;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->Distinct = new forestBool;
		$this->Columns = new forestObject(new forestObjectList('forestSQLColumn'), false);
		$this->Joins = new forestObject(new forestObjectList('forestSQLJoin'), false);
		$this->Where = new forestObject(new forestObjectList('forestSQLWhere'), false);
		$this->GroupBy = new forestObject(new forestObjectList('forestSQLColumn'), false);
		$this->Having = new forestObject(new forestObjectList('forestSQLWhere'), false);
		$this->OrderBy = new forestObject(new forestSQLOrderBy($p_o_sqlQuery), false);
		$this->Limit = new forestObject(new forestSQLLimit($p_o_sqlQuery), false);
	}
	
	function __toString() {
		$s_foo = '';
		
		if ($this->Columns->value->Count() == 0) {
			throw new forestException('Object list is empty', null, true);
		}
		
		$s_foo = 'SELECT ';
		
		if ($this->Distinct->value) {
			$s_foo .= ' DISTINCT ';
		}
		
		$s_lastKey = $this->Columns->value->LastKey();
		
		foreach ($this->Columns->value as $s_key => $o_column) {
			if ($s_key == $s_lastKey) {
				$s_foo .= $o_column;
			} else {
				$s_foo .= $o_column . ', ';
			}
		}
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				$s_foo .= ' FROM ' . '`' . $this->Table->value . '`';
			break;
			default:
				throw new forestException('BaseGateway[%0] not implemented', array($this->BaseGateway->value));
			break;
		}
		
		if ($this->Joins->value->Count() > 0) {
			foreach ($this->Joins->value as $o_join) {
				$s_foo .= ' ' . $o_join;
			}
		}
		
		if ($this->Where->value->Count() > 0) {
			$s_foo .= ' WHERE ';
		
			foreach ($this->Where->value as $o_where) {
				$s_foo .= $o_where;
			}
		}
		
		if ($this->GroupBy->value->Count() > 0) {
			$s_foo .= ' GROUP BY ';
			$s_lastKey = $this->GroupBy->value->LastKey();
			
			foreach ($this->GroupBy->value as $s_key => $o_groupBy) {
				if ($s_key == $s_lastKey) {
					$s_foo .= $o_groupBy;
				} else {
					$s_foo .= $o_groupBy . ', ';
				}
			}
		}
		
		if ($this->Having->value->Count() > 0) {
			$s_foo .= ' HAVING ';
		
			foreach ($this->Having->value as $o_having) {
				$s_foo .= $o_having;
			}
		}
		
		if ($this->OrderBy->value->Columns->Count() > 0) {
			$s_foo .= $this->OrderBy->value;
		}
		
		if ( $this->Limit->value->Interval != 0) {
			$s_foo .= $this->Limit->value;
		}
		
		return $s_foo;
	}
}

class forestSQLInsert extends forestSQLQueryAbstract {
	use forestData;

	/* Fields */
	
	private $ColumnValues;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->ColumnValues = new forestObject(new forestObjectList('forestSQLColumnValue'), false);
	}
	
	function __toString() {
		$s_foo = '';
		
		if ($this->ColumnValues->value->Count() == 0) {
			throw new forestException('Object list is empty', null, true);
		}
		
		$s_lastKey = $this->ColumnValues->value->LastKey();
		
		$s_foo1 = '';
		$s_foo2 = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				$s_foo = 'INSERT INTO ' . '`' . $this->Table->value . '`' . ' (';
				
				foreach ($this->ColumnValues->value as $s_key => $o_columnValue) {
					if (!issetStr($o_columnValue->Table->value)) {
						$s_foo1 .=  '`' . $o_columnValue->Column . '`';
					} else {
						$s_foo1 .=  '`' . $o_columnValue->Table->value . '`' . '.' . '`' . $o_columnValue->Column . '`';
					}
					
					$s_foo2 .= $o_columnValue->Value->scalar;
						
					if ($s_key != $s_lastKey) {
						$s_foo1 .= ', ';
						$s_foo2 .= ', ';
					}
				}
			break;
			default:
				throw new forestException('BaseGateway[%0] not implemented', array($this->BaseGateway->value));
			break;
		}
		
		$s_foo .= $s_foo1;
		$s_foo .= ') VALUES (';
		$s_foo .= $s_foo2;
		$s_foo .= ')';
		
		return $s_foo;
	}
}

class forestSQLUpdate extends forestSQLQueryAbstract {
	use forestData;

	/* Fields */
	
	private $ColumnValues;
	private $Where;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->ColumnValues = new forestObject(new forestObjectList('forestSQLColumnValue'), false);
		$this->Where = new forestObject(new forestObjectList('forestSQLWhere'), false);
	}
	
	function __toString() {
		$s_foo = '';
		
		if ($this->ColumnValues->value->Count() == 0) {
			throw new forestException('Object list is empty', null, true);
		}
		
		if ($this->Where->value->Count() == 0) {
			throw new forestException('Object list is empty', null, true);
		}
		
		$s_lastKey = $this->ColumnValues->value->LastKey();
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				$s_foo = 'UPDATE ' . '`' . $this->Table->value . '`' . ' SET ';
				
				foreach ($this->ColumnValues->value as $s_key => $o_columnValue) {
					if (!issetStr($o_columnValue->Table->value)) {
						$s_foo .=  '`' . $o_columnValue->Column . '`' . ' = ' . $o_columnValue->Value->scalar;
					} else {
						$s_foo .=  '`' . $o_columnValue->Table->value . '`' . '.' . '`' . $o_columnValue->Column . '`' . ' = ' . $o_columnValue->Value->scalar;
					}
				
					if ($s_key != $s_lastKey) {
						$s_foo .= ', ';
					}
				}
			break;
			default:
				throw new forestException('BaseGateway[%0] not implemented', array($this->BaseGateway->value));
			break;
		}
		
		$s_foo .= ' WHERE ';
			
		foreach ($this->Where->value as $o_where) {
			$s_foo .= $o_where;
		}
			
		return $s_foo;
	}
}

class forestSQLDelete extends forestSQLQueryAbstract {
	use forestData;

	/* Fields */
	
	private $Where;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->Where = new forestObject(new forestObjectList('forestSQLWhere'), false);
	}
	
	function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				$s_foo = 'DELETE FROM ' . '`' . $this->Table->value . '`';
			break;
			default:
				throw new forestException('BaseGateway[%0] not implemented', array($this->BaseGateway->value));
			break;
		}
		
		if ($this->Where->value->Count() > 0) {
			$s_foo .= ' WHERE ';
		
			foreach ($this->Where->value as $o_where) {
				$s_foo .= $o_where;
			}
		}
		
		return $s_foo;
	}
}

class forestSQLTruncate extends forestSQLQueryAbstract {
	use forestData;

	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
	}
	
	function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				$s_foo = 'TRUNCATE TABLE ' . '`' . $this->Table->value . '`';
			break;
			default:
				throw new forestException('BaseGateway[%0] not implemented', array($this->BaseGateway->value));
			break;
		}
		
		return $s_foo;
	}
}

class forestSQLCreate extends forestSQLQueryAbstract {
	use forestData;

	/* Fields */
	
	private $Columns;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->Columns = new forestObject(new forestObjectList('forestSQLColumnStructure'), false);
	}
	
	function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				$s_foo = 'CREATE TABLE ' . '`' . $this->Table->value . '`';
				
				if ($this->Columns->value->Count() <= 0) {
					throw new forestException('Columns object list is empty');
				} else {
					$s_foo .= ' (';
					$s_lastKey = $this->Columns->value->LastKey();
		
					foreach ($this->Columns->value as $s_key => $o_column) {
						if ($s_key == $s_lastKey) {
							$s_foo .= $o_column;
						} else {
							$s_foo .= $o_column . ', ';
						}
					}
					
					$s_foo .= ')';
				}
			break;
			default:
				throw new forestException('BaseGateway[%0] not implemented', array($this->BaseGateway->value));
			break;
		}
		
		return $s_foo;
	}
}

class forestSQLDrop extends forestSQLQueryAbstract {
	use forestData;

	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
	}
	
	function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				$s_foo = 'DROP TABLE ' . '`' . $this->Table->value . '`';
			break;
			default:
				throw new forestException('BaseGateway[%0] not implemented', array($this->BaseGateway->value));
			break;
		}
		
		return $s_foo;
	}
}

class forestSQLAlter extends forestSQLQueryAbstract {
	use forestData;

	/* Fields */
	
	private $Columns;
	private $Constraints;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->Columns = new forestObject(new forestObjectList('forestSQLColumnStructure'), false);
		$this->Constraints = new forestObject(new forestObjectList('forestSQLConstraint'), false);
	}
	
	function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				$s_foo = 'ALTER TABLE ' . '`' . $this->Table->value . '` ';
				
				if ( ($this->Columns->value->Count() <= 0) && ($this->Constraints->value->Count() <= 0) ) {
					throw new forestException('Columns and Constraints object lists are empty');
				} else {
					if ($this->Columns->value->Count() > 0) {
						$s_lastKey = $this->Columns->value->LastKey();
			
						foreach ($this->Columns->value as $s_key => $o_column) {
							if ($s_key == $s_lastKey) {
								$s_foo .= $o_column;
							} else {
								$s_foo .= $o_column . ', ';
							}
						}
					} else if ($this->Constraints->value->Count() > 0) {
						$s_lastKey = $this->Constraints->value->LastKey();
			
						foreach ($this->Constraints->value as $s_key => $o_constraint) {
							if ($s_key == $s_lastKey) {
								$s_foo .= $o_constraint;
							} else {
								$s_foo .= $o_constraint . ', ';
							}
						}
					}
				}
			break;
			default:
				throw new forestException('BaseGateway[%0] not implemented', array($this->BaseGateway->value));
			break;
		}
		
		return $s_foo;
	}
}

class forestSQLColumn extends forestSQLQueryAbstract {
	use forestData;

	/* Fields */
	
	private $Column;
	private $Name;
	private $SqlAggregation;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->Column = new forestString;
		$this->Name = new forestString;
		$this->SqlAggregation = new forestList($this->SqlAggregations->value);
	}
	
	function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				switch ($this->SqlType->value) {
					case forestSQLQuery::SELECT:
						if ($this->Column->value == '*') {
							$s_foo = '*';
							
							if (issetStr($this->SqlAggregation->value)) {
								$s_foo = $this->SqlAggregation->value . '(' . $s_foo . ')';
							}
							
							if (issetStr($this->Name->value)) {
								$s_foo = $s_foo . ' AS' . ' ' . $this->Name->value;
							}
						} else {
							if (issetStr($this->Table->value)) {
								$s_foo = '`' . $this->Table->value . '`' . '.' . '`' . $this->Column->value . '`';
							} else {
								$s_foo = '`' . $this->Column->value . '`';
							}
							
							if (issetStr($this->SqlAggregation->value)) {
								$s_foo = $this->SqlAggregation->value . '(' . $s_foo . ')';
							}
							
							if (issetStr($this->Name->value)) {
								$s_foo = $s_foo . ' AS' . ' ' . $this->Name->value;
							}
						}
					break;
					case forestSQLQuery::INSERT:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
					case forestSQLQuery::UPDATE:
					case forestSQLQuery::DELETE:
						if (issetStr($this->Table->value)) {
							$s_foo = '`' . $this->Table->value . '`' . '.' . '`' . $this->Column->value . '`';
						} else {
							$s_foo = '`' . $this->Column->value . '`';
						}
					break;
					default:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
				}
			break;
			default:
				throw new forestException('BaseGateway[%0] not implemented', array($this->BaseGateway->value));
			break;
		}
		
		return $s_foo;
	}
}

class forestSQLJoin extends forestSQLQueryAbstract {
	use forestData;

	/* Fields */
	
	private $JoinType;
	private $Relations;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->JoinType = new forestList($this->JoinTypes->value);
		$this->Relations = new forestObject(new forestObjectList('forestSQLRelation'), false);
	}
	
	function __toString() {
		$s_foo = '';
		
		if ($this->Relations->value->Count() == 0) {
			throw new forestException('Object list is empty', null, true);
		}
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				switch ($this->SqlType->value) {
					case forestSQLQuery::SELECT:
						$s_foo = $this->JoinType->value . ' ' . '`' . $this->Table->value . '`';
					break;
					case forestSQLQuery::INSERT:
					case forestSQLQuery::UPDATE:
					case forestSQLQuery::DELETE:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
					default:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
				}
			break;
			default:
				throw new forestException('BaseGateway[%0] not implemented', array($this->BaseGateway->value));
			break;
		}
		
		$s_foo .= ' ON ';
						
		foreach ($this->Relations->value as $o_relation) {
			$s_foo .= $o_relation;
		}
		
		return $s_foo;
	}
}

class forestSQLRelation extends forestSQLQueryAbstract {
	use forestData;

	/* Fields */
	
	private $ColumnLeft;
	private $ColumnRight;
	private $Operator;
	private $FilterOperator;
	private $BracketStart;
	private $BracketEnd;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->ColumnLeft = new forestObject('forestSQLColumn');
		$this->ColumnRight = new forestObject('forestSQLColumn');
		$this->Operator = new forestList($this->Operators->value);
		$this->FilterOperator = new forestList($this->FilterOperators->value);
		$this->BracketStart = new forestBool;
		$this->BracketEnd = new forestBool;
	}
	
	function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				switch ($this->SqlType->value) {
					case forestSQLQuery::SELECT:
						if (issetStr($this->FilterOperator->value)) {
							$s_foo = ' ' . $this->FilterOperator->value . ' ';
						}
						
						if ($this->BracketStart->value) {
							$s_foo .= '(';
						}
						
						$s_foo .= $this->ColumnLeft->value . ' ' . $this->Operator->value . ' ' . $this->ColumnRight->value;
						
						if ($this->BracketEnd->value) {
							$s_foo .= ')';
						}
					break;
					case forestSQLQuery::INSERT:
					case forestSQLQuery::UPDATE:
					case forestSQLQuery::DELETE:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
					default:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
				}
			break;
			default:
				throw new forestException('BaseGateway[%0] not implemented', array($this->BaseGateway->value));
			break;
		}
		
		return $s_foo;
	}
}

class forestSQLWhere extends forestSQLQueryAbstract {
	use forestData;

	/* Fields */
	
	private $Column;
	private $Value;
	private $Operator;
	private $FilterOperator;
	private $BracketStart;
	private $BracketEnd;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->Column = new forestObject('forestSQLColumn');
		$this->Value = new forestObject('stdClass');
		$this->Operator = new forestList($this->Operators->value);
		$this->FilterOperator = new forestList($this->FilterOperators->value);
		$this->BracketStart = new forestBool;
		$this->BracketEnd = new forestBool;
	}
	
	function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				switch ($this->SqlType->value) {
					case forestSQLQuery::SELECT:
					case forestSQLQuery::UPDATE:
					case forestSQLQuery::DELETE:
						if (issetStr($this->FilterOperator->value)) {
							$s_foo = ' ' . $this->FilterOperator->value . ' ';
						}
						
						if ($this->BracketStart->value) {
							$s_foo .= '(';
						}
						
						$s_foo .= $this->Column->value . ' ' . $this->Operator->value . ' ' . $this->Value->value->scalar;
						
						if ($this->BracketEnd->value) {
							$s_foo .= ')';
						}
					break;
					case forestSQLQuery::INSERT:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
					default:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
				}
			break;
			default:
				throw new forestException('BaseGateway[%0] not implemented', array($this->BaseGateway->value));
			break;
		}
		
		return $s_foo;
	}
}

class forestSQLOrderBy extends forestSQLQueryAbstract {
	use forestData;

	/* Fields */
	
	private $Columns;
	private $Directions;
	
	/* Properties */
	
	public function AddColumn(forestSQLColumn $p_o_value, $p_b_direction = true) {
		$this->Columns->value->Add($p_o_value);
		$this->Directions->value[] = $p_b_direction;
	}
	
	/* Methods */
	
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->Columns = new forestObject(new forestObjectList('forestSQLColumn'), false);
		$this->Directions = new forestArray;
		
	}
	
	function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				switch ($this->SqlType->value) {
					case forestSQLQuery::SELECT:
						if ($this->Columns->value->Count() == 0) {
							throw new forestException('Object list is empty', null, true);
						}
						
						$s_foo = ' ORDER BY ';
						
						$s_lastKey = $this->Columns->value->LastKey();
						$i = -1;
						
						foreach ($this->Columns->Value as $s_key => $o_column) {
							$s_foo .= $o_column;
								
							if ($this->Directions->value[++$i]) {
								$s_foo .= ' ASC';
							} else {
								$s_foo .= ' DESC';
							}
							
							if ($s_key != $s_lastKey) {
								$s_foo .= ', ';
							}
						}
					break;
					case forestSQLQuery::INSERT:
					case forestSQLQuery::UPDATE:
					case forestSQLQuery::DELETE:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
					default:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
				}
			break;
			default:
				throw new forestException('BaseGateway[%0] not implemented', array($this->BaseGateway->value));
			break;
		}
		
		return $s_foo;
	}
}	

class forestSQLLimit extends forestSQLQueryAbstract {
	use forestData;

	/* Fields */
	
	private $Start;
	private $Interval;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->Start = new forestInt;
		$this->Interval = new forestInt;
	}
	
	function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				switch ($this->SqlType->value) {
					case forestSQLQuery::SELECT:
						$s_foo = ' LIMIT ' . $this->Start->value . ', ' . $this->Interval->value;
					break;
					case forestSQLQuery::INSERT:
					case forestSQLQuery::UPDATE:
					case forestSQLQuery::DELETE:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
					default:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
				}
			break;
			default:
				throw new forestException('BaseGateway[%0] not implemented', array($this->BaseGateway->value));
			break;
		}
		
		return $s_foo;
	}
}

class forestSQLColumnValue extends forestSQLQueryAbstract {
	use forestData;

	/* Fields */
	
	private $Column;
	private $Value;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->Column = new forestString;
		$this->Value = new forestObject('stdClass');
	}
}

class forestSQLColumnStructure extends forestSQLQueryAbstract {
	use forestData;

	/* Fields */
	
	private $Name;
	private $NewName;
	private $ConstraintList;
	private $ConstraintDefaultValue;
	private $ColumnType;
	private $ColumnTypeLength;
	private $ColumnTypeDecimalLength;
	private $AlterOperation;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->Name = new forestString;
		$this->NewName = new forestString;
		$this->ConstraintList = new forestObject(new forestObjectList('stdClass'), false);
		$this->ConstraintDefaultValue = new forestObject('stdClass');
		$this->ColumnType = new forestList($this->SqlColumnTypes->value);
		$this->ColumnTypeLength = new forestInt;
		$this->ColumnTypeDecimalLength = new forestInt;
		$this->AlterOperation = new forestList($this->AlterOperations->value);
	}
	
	function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				switch ($this->SqlType->value) {
					case forestSQLQuery::CREATE:
					case forestSQLQuery::DROP:
					case forestSQLQuery::ALTER:
						if ($this->AlterOperation->value == 'DROP') {
							$s_foo .= 'DROP `' . $this->Name->value . '`';
						} else {
							if ($this->AlterOperation->value == 'ADD') {
								$this->NewName->value = $this->Name->value;
								$s_foo .= 'ADD ';
							} else if ($this->AlterOperation->value == 'CHANGE') {
								$s_foo .= 'CHANGE `' . $this->Name->value . '` ';
							} else {
								$this->NewName->value = $this->Name->value;
							}
						
							$s_foo .= '`' . $this->NewName->value . '`';
							
							if (!issetStr($this->ColumnType->value)) {
								throw new forestException('ColumnType not set for sql column');
							}
							
							$s_foo .= ' ' . $this->ColumnType->value;
							
							if ($this->ColumnTypeLength->value > 0) {
								$s_foo .= '(' . $this->ColumnTypeLength->value;
								
								if ($this->ColumnTypeDecimalLength->value > 0) {
									$s_foo .= ',' . $this->ColumnTypeDecimalLength->value;
								}
								
								$s_foo .= ')';
							}
							
							if ($this->ConstraintList->value->Count() > 0) {
								foreach ($this->ConstraintList->value as $s_constraint) {
									if (!in_array($s_constraint, $this->SqlConstraints->value)) {
										throw new forestException('Constraint[%0] is not a valid constraint for that base gateway [%1]', array($s_constraint, implode(',', $this->SqlConstraints->value)));
									}
									
									$s_foo .= ' ' . $s_constraint;
									
									if ($s_constraint == 'DEFAULT') {
										if ($this->ConstraintDefaultValue->value == null) {
											throw new forestException('No value for constraint DEFAULT');
										}
										
										if (is_string($this->ConstraintDefaultValue->value->scalar)) {
											$s_foo .= ' \'' . $this->ConstraintDefaultValue->value->scalar . '\'';
										} else {
											$s_foo .= ' ' . $this->ConstraintDefaultValue->value->scalar;
										}
									}
								}
							}
						}
					break;
					default:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
				}
			break;
			default:
				throw new forestException('BaseGateway[%0] not implemented', array($this->BaseGateway->value));
			break;
		}
		
		return $s_foo;
	}
}

class forestSQLConstraint extends forestSQLQueryAbstract {
	use forestData;

	/* Fields */
	
	private $Constraint;
	private $Name;
	private $NewName;
	private $AlterOperation;
	private $Columns;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->Constraint = new forestList($this->SqlIndexConstraints->value);
		$this->Name = new forestString;
		$this->NewName = new forestString;
		$this->AlterOperation = new forestList($this->AlterOperations->value, 'ADD');
		$this->Columns = new forestObject(new forestObjectList('forestString'), false);
	}
	
	function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				switch ($this->SqlType->value) {
					case forestSQLQuery::CREATE:
					case forestSQLQuery::DROP:
					case forestSQLQuery::ALTER:
						if ($this->AlterOperation->value != 'DROP') {
							if ($this->Columns->value->Count() <= 0) {
								throw new forestException('Columns object list is empty');
							}
						}
						
						if ($this->AlterOperation->value == 'ADD') {
							$s_foo = 'ADD ' . $this->Constraint->value . ' `' . $this->Name->value . '` (';
							
							$s_lastKey = $this->Columns->value->LastKey();
					
							foreach ($this->Columns->value as $s_key => $o_column) {
								if ($s_key == $s_lastKey) {
									$s_foo .= '`' . $o_column->value . '`';
								} else {
									$s_foo .= '`' . $o_column->value . '`' . ', ';
								}
							}
							
							$s_foo .= ')';
						} else if ($this->AlterOperation->value == 'CHANGE') {
							if (!issetStr($this->NewName->value)) {
								throw new forestException('No new name for changing constraint');
							}
							
							$s_foo = 'ADD ' . $this->Constraint->value . ' `' . $this->NewName->value . '` (';
							
							$s_lastKey = $this->Columns->value->LastKey();
					
							foreach ($this->Columns->value as $s_key => $o_column) {
								if ($s_key == $s_lastKey) {
									$s_foo .= '`' . $o_column->value . '`';
								} else {
									$s_foo .= '`' . $o_column->value . '`' . ', ';
								}
							}
							
							$s_foo .= ')';
							$s_foo .= ', DROP INDEX `' . $this->Name->value . '`';
						} else if ($this->AlterOperation->value == 'DROP') {
							$s_foo = 'DROP INDEX `' . $this->Name->value . '`';
						}
					break;
					default:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
				}
			break;
			default:
				throw new forestException('BaseGateway[%0] not implemented', array($this->BaseGateway->value));
			break;
		}
		
		return $s_foo;
	}
}
?>