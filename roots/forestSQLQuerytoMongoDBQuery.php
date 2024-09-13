<?php
/**
 * class with static funtions to transpose SQL-Query to MongoDB-Query
 * this helps for object-oriented access on mongo-databases
 * for more information please read the documentation
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestany.net>
 * @copyright   (c) 2024 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.0.0 stable
 * @link        https://forestany.net
 * @object-id   0x1 00021
 * @since       File available since Release 1.0.0 stable
 * @deprecated  -
 *
 * @version log Version			Developer	Date		Comment
 * 				1.0.0 stable	renea		2020-02-04	added to framework
 */

namespace fPHP\Base;

use \fPHP\Roots\forestString as forestString;
use \fPHP\Roots\forestList as forestList;
use \fPHP\Roots\forestNumericString as forestNumericString;
use \fPHP\Roots\forestInt as forestInt;
use \fPHP\Roots\forestFloat as forestFloat;
use \fPHP\Roots\forestBool as forestBool;
use \fPHP\Roots\forestArray as forestArray;
use \fPHP\Roots\forestObject as forestObject;
use \fPHP\Roots\forestLookup as forestLookup;
use \fPHP\Helper\forestObjectList;
use \fPHP\Roots\forestException as forestException;

class forestSQLQuerytoMongoDBQuery {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	const COMMAND = 'command';
	const QUERY = 'query';
	const BULKWRITE = 'bulkwrite';
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * main transpose function to translate sql query into mongodb commands
	 *
	 * @return array  array of mongodb arguments for query execution
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static yes
	 */
	public static function Transpose(\fPHP\Base\forestSQLQuery $p_o_sqlQuery, $p_s_datasource) {
		$a_return = array(
			'mongodbCollection' => null,
			'mongodbCommandType' => null,
			'mongodbCommand' => null
		);
		
		switch ($p_o_sqlQuery->SqlType) {
			case \fPHP\Base\forestSQLQuery::CREATE:
				$a_return = self::TransposeCreate($p_o_sqlQuery->GetQuery(), $p_s_datasource);
			break;
			case \fPHP\Base\forestSQLQuery::DROP:
				$a_return = self::TransposeDrop($p_o_sqlQuery->GetQuery(), $p_s_datasource);
			break;
			case \fPHP\Base\forestSQLQuery::ALTER:
				$a_return = self::TransposeAlter($p_o_sqlQuery->GetQuery(), $p_s_datasource);
			break;
			case \fPHP\Base\forestSQLQuery::INSERT:
				$a_return = self::TransposeInsert($p_o_sqlQuery->GetQuery(), $p_s_datasource);
			break;
			case \fPHP\Base\forestSQLQuery::UPDATE:
				$a_return = self::TransposeUpdate($p_o_sqlQuery->GetQuery(), $p_s_datasource);
			break;
			case \fPHP\Base\forestSQLQuery::REMOVE:
				$a_return = self::TransposeDelete($p_o_sqlQuery->GetQuery(), $p_s_datasource);
			break;
			case \fPHP\Base\forestSQLQuery::TRUNCATE:
				$a_return['mongodbCollection'] = $p_o_sqlQuery->Table;
				$a_return['mongodbCommandType'] = self::BULKWRITE;
				$o_mongoBulkWrite = new \MongoDB\Driver\BulkWrite;
				$a_return['mongodbCommand'] = [$o_mongoBulkWrite->delete([])];
			break;
			case \fPHP\Base\forestSQLQuery::SELECT:
				$a_return = self::TransposeSelect($p_o_sqlQuery->GetQuery(), $p_s_datasource);
			break;
			default:
				throw new forestException('Invalid SqlType[%0]', array($p_o_sqlQuery->SqlType));
			break;
		}
		
		return $a_return;
	}
	
	/**
	 * transpose function to translate create sql query
	 *
	 * @return array  array of mongodb arguments for query execution
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static yes
	 */
	private static function TransposeCreate(\fPHP\Base\forestSQLCreate $p_o_sqlQuery, $p_s_datasource) {
		$a_return = array(
			'mongodbCollection' => null,
			'mongodbCommandType' => null,
			'mongodbCommand' => null
		);
		
		$a_return['mongodbCollection'] = $p_o_sqlQuery->Table;
		$a_return['mongodbCommandType'] = self::COMMAND;
		$a_return['mongodbCommand'] = array();
		
		/* create collection */
		$a_return['mongodbCommand'][] = new \MongoDB\Driver\Command(['create' => $a_return['mongodbCollection']]);
		
		foreach ($p_o_sqlQuery->Columns as $o_column) {
			foreach ($o_column->ConstraintList as $s_constraint) {
				if ( ($s_constraint == 'UNIQUE') || ($s_constraint == 'PRIMARY KEY') ) {
					/* add index to collection with create index command */
					$a_return['mongodbCommand'][] = new \MongoDB\Driver\Command([
						'createIndexes' => $a_return['mongodbCollection'],
						'indexes'       => [[
						  'name' => $a_return['mongodbCollection'] . '_' . $o_column->Name . '_puk',
						  'key'  => [$o_column->Name => 1],
						  'ns'   => $p_s_datasource . '.' . $a_return['mongodbCollection'],
						  'unique' => 1
					   ]]
					]);
				}
			}
		}
		
		return $a_return;
	}
	
	/**
	 * transpose function to translate drop sql query
	 *
	 * @return array  array of mongodb arguments for query execution
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static yes
	 */
	private static function TransposeDrop(\fPHP\Base\forestSQLDrop $p_o_sqlQuery, $p_s_datasource) {
		$a_return = array(
			'mongodbCollection' => null,
			'mongodbCommandType' => null,
			'mongodbCommand' => null
		);
		
		$a_return['mongodbCollection'] = $p_o_sqlQuery->Table;
		$a_return['mongodbCommandType'] = self::COMMAND;
		$a_return['mongodbCommand'] = [new \MongoDB\Driver\Command(['drop' => $a_return['mongodbCollection']])];
		
		return $a_return;
	}
	
	/**
	 * transpose function to translate alter sql query
	 *
	 * @return array  array of mongodb arguments for query execution
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static yes
	 */
	private static function TransposeAlter(\fPHP\Base\forestSQLAlter $p_o_sqlQuery, $p_s_datasource) {
		$a_return = array(
			'mongodbCollection' => null,
			'mongodbCommandType' => null,
			'mongodbCommand' => null
		);
		
		$a_return['mongodbCollection'] = $p_o_sqlQuery->Table;
		$a_return['mongodbCommandType'] = self::COMMAND;
		$a_return['mongodbCommand'] = array();
		
		if (issetStr($p_o_sqlQuery->NewTableName)) {
			/* rename collection command */
			$a_return['mongodbCommand'][] = new \MongoDB\Driver\Command([
				'renameCollection' => $p_s_datasource . '.' . $a_return['mongodbCollection'],
				'to' => $p_s_datasource . '.' . $p_o_sqlQuery->NewTableName
			]);
		} else if ($p_o_sqlQuery->Columns->Count() > 0) {
			$a_return['mongodbCommandType'] = self::BULKWRITE;
			
			/* alter operation ADD is not necessary for mongodb */
			foreach ($p_o_sqlQuery->Columns as $s_key => $o_column) {
				if ($o_column->AlterOperation == 'CHANGE') {
					/* only renaming a column is necessary, type is irrelevant */
					if (issetStr($o_column->NewName)) {
						$o_mongoBulkWrite = new \MongoDB\Driver\BulkWrite;
						$o_mongoBulkWrite->update([], ['$rename' => [$o_column->Name => $o_column->NewName]], ['multi' => true]);
						$a_return['mongodbCommand'][] = $o_mongoBulkWrite;
					}
				} else if ($o_column->AlterOperation == 'DROP') {
					/* use $unset command for dropping column for all records/documents [multi:true] */
					$o_mongoBulkWrite = new \MongoDB\Driver\BulkWrite;
					$o_mongoBulkWrite->update([], ['$unset' => [$o_column->Name => 1]], ['multi' => true]);
					$a_return['mongodbCommand'][] = $o_mongoBulkWrite;
				}
			}
		} else if ($p_o_sqlQuery->Constraints->Count() > 0) {
			foreach ($p_o_sqlQuery->Constraints as $s_key => $o_constraint) {
				if ($o_constraint->AlterOperation != 'DROP') {
					if ($o_constraint->Columns->Count() <= 0) {
						throw new forestException('Columns object list is empty');
					}
				}
				
				if ($o_constraint->AlterOperation == 'ADD') {
					$a_keys = array();
					
					foreach ($o_constraint->Columns as $s_key => $o_column) {
						$a_keys[$o_column->value] = 1;
					}
					
					/* create index command */
					$a_return['mongodbCommand'][] = new \MongoDB\Driver\Command([
						'createIndexes' => $a_return['mongodbCollection'],
						'indexes'       => [[
						  'name' => $o_constraint->Name,
						  'key'  => $a_keys,
						  'ns'   => $p_s_datasource . '.' . $a_return['mongodbCollection'],
						  'unique' => ( ( (($o_constraint->Constraint == 'UNIQUE') || $o_constraint->Constraint == 'PRIMARY KEY') ) ? 1 : 0 )
					   ]]
					]);
				} else if ($o_constraint->AlterOperation == 'CHANGE') {
					if (!issetStr($o_constraint->NewName)) {
						throw new forestException('No new name for changing constraint');
					}
					
					/* drop index command */
					$a_return['mongodbCommand'][] = new \MongoDB\Driver\Command([
						'dropIndexes' => $a_return['mongodbCollection'],
						'index'       => $o_constraint->Name
					]);
					
					$a_keys = array();
					
					foreach ($o_constraint->Columns as $s_key => $o_column) {
						$a_keys[$o_column->value] = 1;
					}
					
					/* create index command */
					$a_return['mongodbCommand'][] = new \MongoDB\Driver\Command([
						'createIndexes' => $a_return['mongodbCollection'],
						'indexes'       => [[
						  'name' => $o_constraint->NewName,
						  'key'  => $a_keys,
						  'ns'   => $p_s_datasource . '.' . $a_return['mongodbCollection'],
						  'unique' => ( ( (($o_constraint->Constraint == 'UNIQUE') || $o_constraint->Constraint == 'PRIMARY KEY') ) ? 1 : 0 )
					   ]]
					]);
				} else if ($o_constraint->AlterOperation == 'DROP') {
					/* drop index command */
					$a_return['mongodbCommand'][] = new \MongoDB\Driver\Command([
						'dropIndexes' => $a_return['mongodbCollection'],
						'index'       => $o_constraint->Name
					]);
				}
			}
		}
		
		return $a_return;
	}
	
	/**
	 * helper function to query max id of a collection/table
	 *
	 * @return integer  highest id value of a collection, or 0 if no record/documents are found
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static yes
	 */
	private static function GetLastInsertId($p_s_datasource, $p_s_collection) {
		/* read all fields of a database.collection */
		try {
			$o_glob = \fPHP\Roots\forestGlobals::init();
			
			$o_monogQuery = new \MongoDB\Driver\Query([], ['projection' => ['Id' => 1], 'limit' => 1, 'sort' => [ 'Id' => -1 ]]);
			$o_mongoCursor = $o_glob->Base->{$o_glob->ActiveBase}->CurrentConnectionAlt->executeQuery($p_s_datasource . '.' . $p_s_collection, $o_monogQuery);

			foreach ($o_mongoCursor as $o_mongoDocument) {
				return $o_mongoDocument->{'Id'};
			}
			
			return 0;
		} catch(\MongoDB\Driver\Exception $o_exc) {
			return 0;
		}
	}
	
	/**
	 * transpose function to translate insert sql query
	 *
	 * @return array  array of mongodb arguments for query execution
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static yes
	 */
	private static function TransposeInsert(\fPHP\Base\forestSQLInsert $p_o_sqlQuery, $p_s_datasource) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$a_return = array(
			'mongodbCollection' => null,
			'mongodbCommandType' => null,
			'mongodbCommand' => null
		);
		
		$a_return['mongodbCollection'] = $p_o_sqlQuery->Table;
		$a_return['mongodbCommandType'] = self::BULKWRITE;
		$a_return['mongodbCommand'] = array();
		
		$o_mongoBulkWrite = new \MongoDB\Driver\BulkWrite;
		$o_mongoDocument = array();
		
		/* execute auto increment for id manually */
		if ( ($o_glob->Temp->Exists('MongoDBIdAutoIncrement')) && ($o_glob->Temp->{'MongoDBIdAutoIncrement'}) ) {
			$o_mongoDocument['Id'] = intval(self::GetLastInsertId($p_s_datasource, $a_return['mongodbCollection'])) + 1;
			/* add new id to global temp for later use */
			$o_glob->Temp->Add($o_mongoDocument['Id'], 'MongoDBLastInsertId');
			$o_glob->Temp->Del('MongoDBIdAutoIncrement');
		}
		
		foreach ($p_o_sqlQuery->ColumnValues as $s_key => $o_columnValue) {
			if ($o_columnValue->Column == 'Id') {
				/* add new id to global temp for later use */
				$o_glob->Temp->Add($o_columnValue->Value->scalar, 'MongoDBLastInsertId');
			}
			
			$o_mongoDocument[$o_columnValue->Column] = $o_columnValue->Value->scalar;
		}
		
		$o_mongoBulkWrite->insert($o_mongoDocument);
		$a_return['mongodbCommand'][] = $o_mongoBulkWrite;
		
		return $a_return;
	}
	
	/**
	 * transpose function to translate sql query where clauses
	 *
	 * @return array  array of mongodb filter arguments
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static yes
	 */
	private static function TransposeWhere($p_ol_where) {
		$o_mongoWhere = array();
		
		/* transpose where element to mongodb filter */
		foreach ($p_ol_where as $o_where) {
			$o_whereValue = array();
			
			switch ($o_where->Operator) {
				case '=':
				case 'IS':
					$o_whereValue['$eq'] = $o_where->Value->scalar;
				break;
				case '<>':
				case 'IS NOT':
					$o_whereValue['$ne'] = $o_where->Value->scalar;
				break;
				case '<':
					$o_whereValue['$lt'] = $o_where->Value->scalar;
				break;
				case '<=':
					$o_whereValue['$lte'] = $o_where->Value->scalar;
				break;
				case '>':
					$o_whereValue['$gt'] = $o_where->Value->scalar;
				break;
				case '>=':
					$o_whereValue['$gte'] = $o_where->Value->scalar;
				break;
				case 'LIKE':
					$foo = str_replace('%', '', $o_where->Value->scalar);
					$o_whereValue = '/.*' . $foo . '.*/';
				break;
				case 'NOT LIKE':
					$foo = str_replace('%', '', $o_where->Value->scalar);
					$o_whereValue['$not'] = '/.*' . $foo . '.*/';
				break;
				case 'IN':
					$o_whereValue['$in'] = [$o_where->Value->scalar];
				break;
				case 'NOT IN':
					$o_whereValue['$nin'] = [$o_where->Value->scalar];
				break;
				default:
					$o_whereValue['$eq'] = $o_where->Value->scalar;
				break;
			}
			
			if (issetStr($o_where->FilterOperator)) {
				if ($o_where->FilterOperator == 'AND') {
					if (!array_key_exists('$and', $o_mongoWhere)) {
						$o_mongoWhere['$and'] = array();
					}
					
					$o_mongoWhere['$and'][][$o_where->Column->Column] = $o_whereValue;
				} else if ($o_where->FilterOperator == 'OR') {
					if (!array_key_exists('$or', $o_mongoWhere)) {
						$o_mongoWhere['$or'] = array();
					}
					
					$o_mongoWhere['$or'][][$o_where->Column->Column] = $o_whereValue;
				} else {
					throw new forestException('Filter operator[' . $o_where->FilterOperator . '] not supported.');
				}
			} else {
				$o_mongoWhere[$o_where->Column->Column] = $o_whereValue;
			}
		}
		
		return $o_mongoWhere;
	}
	
	/**
	 * transpose function to translate update sql query
	 *
	 * @return array  array of mongodb arguments for query execution
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static yes
	 */
	private static function TransposeUpdate(\fPHP\Base\forestSQLUpdate $p_o_sqlQuery, $p_s_datasource) {
		$a_return = array(
			'mongodbCollection' => null,
			'mongodbCommandType' => null,
			'mongodbCommand' => null
		);
		
		$a_return['mongodbCollection'] = $p_o_sqlQuery->Table;
		$a_return['mongodbCommandType'] = self::BULKWRITE;
		$a_return['mongodbCommand'] = array();
		
		$o_mongoBulkWrite = new \MongoDB\Driver\BulkWrite;
		$o_mongoDocument = array();
		
		$o_mongoWhere = self::TransposeWhere($p_o_sqlQuery->Where);
		
		foreach ($p_o_sqlQuery->ColumnValues as $s_key => $o_columnValue) {
			$o_mongoDocument[$o_columnValue->Column] = $o_columnValue->Value->scalar;
		}
		
		$o_mongoBulkWrite->update($o_mongoWhere, ['$set' => $o_mongoDocument]);
		$a_return['mongodbCommand'][] = $o_mongoBulkWrite;
		
		return $a_return;
	}
	
	/**
	 * transpose function to translate delete sql query
	 *
	 * @return array  array of mongodb arguments for query execution
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static yes
	 */
	private static function TransposeDelete(\fPHP\Base\forestSQLDelete $p_o_sqlQuery, $p_s_datasource) {
		$a_return = array(
			'mongodbCollection' => null,
			'mongodbCommandType' => null,
			'mongodbCommand' => null
		);
		
		$a_return['mongodbCollection'] = $p_o_sqlQuery->Table;
		$a_return['mongodbCommandType'] = self::BULKWRITE;
		$a_return['mongodbCommand'] = array();
		
		$o_mongoBulkWrite = new \MongoDB\Driver\BulkWrite;
		$o_mongoWhere = self::TransposeWhere($p_o_sqlQuery->Where);
		$o_mongoBulkWrite->delete($o_mongoWhere);
		$a_return['mongodbCommand'][] = $o_mongoBulkWrite;
		
		return $a_return;
	}
	
	/**
	 * transpose function to translate select sql query
	 *
	 * @return array  array of mongodb arguments for query execution
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static yes
	 */
	private static function TransposeSelect(\fPHP\Base\forestSQLSelect $p_o_sqlQuery, $p_s_datasource) {
		$a_return = array(
			'mongodbCollection' => null,
			'mongodbCommandType' => null,
			'mongodbCommand' => null
		);
		
		$a_return['mongodbCollection'] = $p_o_sqlQuery->Table;
		$a_return['mongodbCommandType'] = self::QUERY;
		$a_return['mongodbCommand'] = array();
		
		$o_mongoQueryFilter = array();
		$o_mongoQueryOptions = array();
		
		/* like within sql query we need at least one column for a select */
		if ($p_o_sqlQuery->Columns->Count() == 0) {
			throw new forestException('Object list is empty', null, true);
		}
		
		if ($p_o_sqlQuery->Distinct) {
			/* sql distrinct is only supported for one column in mongodb */
			if ($p_o_sqlQuery->Columns->Count() > 1) {
				throw new forestException('Distinct with more than one column is not supported.');
			}
			
			$o_glob = \fPHP\Roots\forestGlobals::init();
			$o_glob->Temp->Add($p_o_sqlQuery->Columns->{0}->Column, 'MongoDBDistinct');
		}
		
		$o_mongoQueryOptions['projection'] = array();
		/* do not display _id column of mongodb by default */
		$o_mongoQueryOptions['projection']['_id'] = 0;
		
		foreach ($p_o_sqlQuery->Columns as $s_key => $o_column) {
			if ($o_column->Column == '*') {
				if (issetStr($o_column->SqlAggregation)) {
					/* only accept COUNT as sql aggregation */
					if ($o_column->SqlAggregation == 'COUNT') {
						$o_glob = \fPHP\Roots\forestGlobals::init();
						$o_glob->Temp->Add($o_column->Name, 'MongoDBCountAll');
					} else {
						throw new forestException('SqlAggregation not supported.');
					}
				}
				
				break;
			} else {
				$o_mongoQueryOptions['projection'][$o_column->Column] = 1;
			}
		}
		
		/* sql join is not supported */
		if ($p_o_sqlQuery->Joins->Count() > 0) {
			throw new forestException('Join is not supported.');
		}
		
		/* transpose sql where clauses */
		if ($p_o_sqlQuery->Where->Count() > 0) {
			$o_mongoQueryFilter = self::TransposeWhere($p_o_sqlQuery->Where);
		}
		
		/* sql groupby is not supported */
		if ($p_o_sqlQuery->GroupBy->Count() > 0) {
			throw new forestException('GroupBy is not supported.');
		}
		
		/* sql having is not supported */
		if ($p_o_sqlQuery->Having->Count() > 0) {
			throw new forestException('Having is not supported.');
		}
		
		/* handle sql orderby */
		if ($p_o_sqlQuery->OrderBy->Columns->Count() > 0) {
			$o_mongoQueryOptions['sort'] = array();
			
			$i = -1;
			
			foreach ($p_o_sqlQuery->OrderBy->Columns as $s_key => $o_column) {
				$i_direction = 1;
				
				if (!$p_o_sqlQuery->OrderBy->Directions[++$i]) {
					$i_direction = -1;
				}
				
				$o_mongoQueryOptions['sort'][$o_column->Column] = $i_direction;
			}
		}
		
		/* handle sql limit */
		if ($p_o_sqlQuery->Limit->Interval != 0) {
			$o_mongoQueryOptions['limit'] = $p_o_sqlQuery->Limit->Interval;
			$o_mongoQueryOptions['skip'] = $p_o_sqlQuery->Limit->Start;
		}
		
		$a_return['mongodbCommand'][] = new \MongoDB\Driver\Query($o_mongoQueryFilter, $o_mongoQueryOptions);
		
		return $a_return;
	}

	/**
	 * helper function to determine type of queried value for internal cast
	 *
	 * @return string  general type for internal cast
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static yes
	 */
	public static function DetermineTypeByValue(&$p_o_value) {
		/* check datetime */
		if (
			(preg_match('/^ ((0[1-9])|(1[0-9])|2[0-9]|(3[0-1])) \. ((0[1-9])|(1[0-2])) \. (\d){4} (\s|T) (0[0-9]|1[0-9]|2[0-3]) : ([0-5][0-9]) (: ([0-5][0-9]))? $/x', $p_o_value)) ||
			(preg_match('/^ ((0[1-9])|(1[0-9])|2[0-9]|(3[0-1])) \. ((0[1-9])|(1[0-2])) \. (\d){2} (\s|T) (0[0-9]|1[0-9]|2[0-3]) : ([0-5][0-9]) (: ([0-5][0-9]))? $/x', $p_o_value)) ||
			(preg_match('/^ (\d){4} - ((0[1-9])|(1[0-2])) - ((0[1-9])|(1[0-9])|2[0-9]|(3[0-1])) (\s|T) (0[0-9]|1[0-9]|2[0-3]) : ([0-5][0-9]) (: ([0-5][0-9]))? $/x', $p_o_value))
		) {
			return 'date';
		}
		
		/* check time */
		if (preg_match('/^ (0[0-9]|1[0-9]|2[0-3]) : ([0-5][0-9]) : ([0-5][0-9]) $/x', $p_o_value)) {
			return 'date';
		}
		
		/* check decimal */
		if (preg_match("/^\d*[.,]{1}\d{2}$/i", $p_o_value)) {
			$p_o_value = str_replace(',', '.', $p_o_value);
			return 'real';
		}
		
		/* check double */
		if (preg_match("/^\d*[.,]{1}\d*$/i", $p_o_value)) {
			$p_o_value = str_replace(',', '.', $p_o_value);
			return 'real';
		}
		
		/* check bool */
		if ( ($p_o_value === true) || ($p_o_value === false) ) {
			return 'int';
		}
		
		/* check smallint */
		if ( (preg_match("/^\d*$/i", $p_o_value)) && (intval($p_o_value) <= 32768) ) {
			return 'int';
		}
		
		/* check integer */
		if ( (preg_match("/^\d*$/i", $p_o_value)) && (intval($p_o_value) > 32768) && (intval($p_o_value) <= 2147483648) ) {
			return 'int';
		}
		
		/* check bigint */
		if ( (preg_match("/^\d*$/i", $p_o_value)) && (intval($p_o_value) > 2147483648) ) {
			return 'int';
		}
		
		/* check text */
		if ( (is_string($p_o_value)) && (strlen($p_o_value) > 255) ) {
			return 'string';
		}
		
		/* check text */
		if (is_string($p_o_value)) {
			return 'string';
		}
		
		return null;
	}
}
?>