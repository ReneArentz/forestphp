<?php
/**
 * class collection for generating one SQL-Query in an object-oriented way
 * this helps for object-oriented access on database-tables
 * for more information please read the documentation
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestphp.de>
 * @copyright   (c) 2019 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.0.0 stable
 * @link        http://www.forestphp.de/
 * @object-id   0x1 0000A
 * @since       File available since Release 0.1.0 alpha
 * @deprecated  -
 *
 * @version log Version		Developer	Date		Comment
 * 		0.1.0 alpha	renatus		2019-08-04	first build
 * 		0.1.1 alpha	renatus		2019-08-10	added trunk and forestDateTime functionality
 * 		0.1.5 alpha	renatus		2019-10-10	added forestLookup functionality
 * 		0.2.0 beta	renatus		2019-10-15	added data definition language functionality
 * 		1.0.0 stable	renatus		2020-02-04	added MSSQL support
 * 		1.0.0 stable	renatus		2020-02-05	added PGSQL support
 * 		1.0.0 stable	renatus		2020-02-06	added SQLite3 support
 * 		1.0.0 stable	renatus		2020-02-10	added OCISQL support
 * 		1.0.0 stable	renatus		2020-02-11	added MongoDB support
 * 		1.0.0 stable	renatus		2020-02-11	removed string type definition in function parameters because of E_RECOVERABLE_ERROR
 * 		1.0.0 stable	renatus		2020-02-14	changes constants because of conflict with php system constants
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

class forestSQLQuery {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	const SELECT = 'select';
	const INSERT = 'insert';
	const UPDATE = 'update';
	const REMOVE = 'delete';
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
	
	/**
	 * constructor of forestSQLQuery class, set query parameters like base-gateway, type and table
	 *
	 * @param string $p_s_baseGateway  base-gateway constant
	 * @param string $p_s_type  type of sql query - SELECT, INSERT, UPDATE, DELETE, TRUNCATE, CREATE, DROP, ALTER
	 * @param string $p_s_table  table name
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct($p_s_baseGateway, $p_s_type, $p_s_table) {
		/* take over construct parameters */
		$this->BaseGateway = new forestString($p_s_baseGateway, false);
		$this->SqlType = new forestString($p_s_type, false);
		$this->Table = new forestString($p_s_table, false);
		
		/* check base gateway parameter */
        switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
			case forestBase::MSSQL:
			case forestBase::PGSQL:
			case forestBase::SQLite3:
			case forestBase::OCISQL:
			case forestBase::MongoDB:
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
			case self::REMOVE:
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
	
	/**
	 * __toString function returning created sql query as string value
	 *
	 * @return string
	 *
	 * @access public
	 * @static no
	 */
	public function __toString() {
		return strval($this->Query->value);
	}
	
	/**
	 * function to get sql query object
	 *
	 * @return forestSQLQueryAbstract or forestString
	 *
	 * @access public
	 * @static no
	 */
	public function GetQuery() {
		return $this->Query->value;
	}
	
	/**
	 * function to set sql query as string value manually
	 *
	 * @return string
	 *
	 * @access public
	 * @static no
	 */
	public function SetQuery($p_s_queryString) {
		$this->Query = new forestString($p_s_queryString);
	}
	
	/**
	 * transpose general column type to base-gateway defined column type and further information for query execution
	 *
	 * @param string $p_s_baseGateway  base-gateway constant
	 * @param string $p_s_sqlType  type of sql column, based on values in table sys_fphp_sqltype
	 * @param string $p_s_columnType  transposed column type, passed by reference
	 * @param integer $p_i_columnLength  transposed column length, passed by reference
	 * @param integer $p_i_columnDecimalLength  transposed column decimal length, passed by reference
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static yes
	 */
	public static function ColumnTypeAllocation($p_s_baseGateway, $p_s_sqlType, &$p_s_columnType, &$p_i_columnLength, &$p_i_columnDecimalLength) {
		/* check base gateway parameter */
        switch ($p_s_baseGateway) {
			case forestBase::MariaSQL:
			case forestBase::MSSQL:
			case forestBase::PGSQL:
			case forestBase::SQLite3:
			case forestBase::OCISQL:
			case forestBase::MongoDB:
				break;
			default:
				throw new forestException('Invalid BaseGateway[%0]', array($p_s_baseGateway));
			break;
		}
		
		/* check sql type parameter */
		 switch ($p_s_sqlType) {
			case 'text [36]':
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
		$a_allocation[forestBase::MariaSQL]['text [36]']['columnType'] = 'VARCHAR';
		$a_allocation[forestBase::MariaSQL]['text [36]']['columnLength'] = 36;
		$a_allocation[forestBase::MariaSQL]['text [36]']['decimalLength'] = null;
		
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
		
		/* forestBase::SQLite3 */
		$a_allocation[forestBase::SQLite3]['text [36]']['columnType'] = 'varchar';
		$a_allocation[forestBase::SQLite3]['text [36]']['columnLength'] = 36;
		$a_allocation[forestBase::SQLite3]['text [36]']['decimalLength'] = null;
		
		$a_allocation[forestBase::SQLite3]['text [255]']['columnType'] = 'varchar';
		$a_allocation[forestBase::SQLite3]['text [255]']['columnLength'] = 255;
		$a_allocation[forestBase::SQLite3]['text [255]']['decimalLength'] = null;
		
		$a_allocation[forestBase::SQLite3]['text']['columnType'] = 'text';
		$a_allocation[forestBase::SQLite3]['text']['columnLength'] = null;
		$a_allocation[forestBase::SQLite3]['text']['decimalLength'] = null;
		
		$a_allocation[forestBase::SQLite3]['integer [small]']['columnType'] = 'smallint';
		$a_allocation[forestBase::SQLite3]['integer [small]']['columnLength'] = null;
		$a_allocation[forestBase::SQLite3]['integer [small]']['decimalLength'] = null;
		
		$a_allocation[forestBase::SQLite3]['integer [int]']['columnType'] = 'integer';
		$a_allocation[forestBase::SQLite3]['integer [int]']['columnLength'] = null;
		$a_allocation[forestBase::SQLite3]['integer [int]']['decimalLength'] = null;
		
		$a_allocation[forestBase::SQLite3]['integer [big]']['columnType'] = 'bigint';
		$a_allocation[forestBase::SQLite3]['integer [big]']['columnLength'] = null;
		$a_allocation[forestBase::SQLite3]['integer [big]']['decimalLength'] = null;
		
		$a_allocation[forestBase::SQLite3]['datetime']['columnType'] = 'datetime';
		$a_allocation[forestBase::SQLite3]['datetime']['columnLength'] = null;
		$a_allocation[forestBase::SQLite3]['datetime']['decimalLength'] = null;
		
		$a_allocation[forestBase::SQLite3]['time']['columnType'] = 'time';
		$a_allocation[forestBase::SQLite3]['time']['columnLength'] = null;
		$a_allocation[forestBase::SQLite3]['time']['decimalLength'] = null;
		
		$a_allocation[forestBase::SQLite3]['double']['columnType'] = 'double';
		$a_allocation[forestBase::SQLite3]['double']['columnLength'] = null;
		$a_allocation[forestBase::SQLite3]['double']['decimalLength'] = null;
		
		$a_allocation[forestBase::SQLite3]['decimal']['columnType'] = 'decimal';
		$a_allocation[forestBase::SQLite3]['decimal']['columnLength'] = 10;
		$a_allocation[forestBase::SQLite3]['decimal']['decimalLength'] = 2;
		
		$a_allocation[forestBase::SQLite3]['bool']['columnType'] = 'bit';
		$a_allocation[forestBase::SQLite3]['bool']['columnLength'] = 1;
		$a_allocation[forestBase::SQLite3]['bool']['decimalLength'] = null;
		
		/* forestBase::MSSQL */
		$a_allocation[forestBase::MSSQL]['text [36]']['columnType'] = 'nvarchar';
		$a_allocation[forestBase::MSSQL]['text [36]']['columnLength'] = 36;
		$a_allocation[forestBase::MSSQL]['text [36]']['decimalLength'] = null;
		
		$a_allocation[forestBase::MSSQL]['text [255]']['columnType'] = 'nvarchar';
		$a_allocation[forestBase::MSSQL]['text [255]']['columnLength'] = 255;
		$a_allocation[forestBase::MSSQL]['text [255]']['decimalLength'] = null;
		
		$a_allocation[forestBase::MSSQL]['text']['columnType'] = 'text';
		$a_allocation[forestBase::MSSQL]['text']['columnLength'] = null;
		$a_allocation[forestBase::MSSQL]['text']['decimalLength'] = null;
		
		$a_allocation[forestBase::MSSQL]['integer [small]']['columnType'] = 'smallint';
		$a_allocation[forestBase::MSSQL]['integer [small]']['columnLength'] = null;
		$a_allocation[forestBase::MSSQL]['integer [small]']['decimalLength'] = null;
		
		$a_allocation[forestBase::MSSQL]['integer [int]']['columnType'] = 'int';
		$a_allocation[forestBase::MSSQL]['integer [int]']['columnLength'] = null;
		$a_allocation[forestBase::MSSQL]['integer [int]']['decimalLength'] = null;
		
		$a_allocation[forestBase::MSSQL]['integer [big]']['columnType'] = 'bigint';
		$a_allocation[forestBase::MSSQL]['integer [big]']['columnLength'] = null;
		$a_allocation[forestBase::MSSQL]['integer [big]']['decimalLength'] = null;
		
		$a_allocation[forestBase::MSSQL]['datetime']['columnType'] = 'datetime';
		$a_allocation[forestBase::MSSQL]['datetime']['columnLength'] = null;
		$a_allocation[forestBase::MSSQL]['datetime']['decimalLength'] = null;
		
		$a_allocation[forestBase::MSSQL]['time']['columnType'] = 'time';
		$a_allocation[forestBase::MSSQL]['time']['columnLength'] = null;
		$a_allocation[forestBase::MSSQL]['time']['decimalLength'] = null;
		
		$a_allocation[forestBase::MSSQL]['double']['columnType'] = 'float';
		$a_allocation[forestBase::MSSQL]['double']['columnLength'] = null;
		$a_allocation[forestBase::MSSQL]['double']['decimalLength'] = null;
		
		$a_allocation[forestBase::MSSQL]['decimal']['columnType'] = 'decimal';
		$a_allocation[forestBase::MSSQL]['decimal']['columnLength'] = 18;
		$a_allocation[forestBase::MSSQL]['decimal']['decimalLength'] = 2;
		
		$a_allocation[forestBase::MSSQL]['bool']['columnType'] = 'bit';
		$a_allocation[forestBase::MSSQL]['bool']['columnLength'] = null;
		$a_allocation[forestBase::MSSQL]['bool']['decimalLength'] = null;
		
		/* forestBase::PGSQL */
		$a_allocation[forestBase::PGSQL]['text [36]']['columnType'] = 'varchar';
		$a_allocation[forestBase::PGSQL]['text [36]']['columnLength'] = 36;
		$a_allocation[forestBase::PGSQL]['text [36]']['decimalLength'] = null;
		
		$a_allocation[forestBase::PGSQL]['text [255]']['columnType'] = 'varchar';
		$a_allocation[forestBase::PGSQL]['text [255]']['columnLength'] = 255;
		$a_allocation[forestBase::PGSQL]['text [255]']['decimalLength'] = null;
		
		$a_allocation[forestBase::PGSQL]['text']['columnType'] = 'text';
		$a_allocation[forestBase::PGSQL]['text']['columnLength'] = null;
		$a_allocation[forestBase::PGSQL]['text']['decimalLength'] = null;
		
		$a_allocation[forestBase::PGSQL]['integer [small]']['columnType'] = 'smallint';
		$a_allocation[forestBase::PGSQL]['integer [small]']['columnLength'] = null;
		$a_allocation[forestBase::PGSQL]['integer [small]']['decimalLength'] = null;
		
		$a_allocation[forestBase::PGSQL]['integer [int]']['columnType'] = 'integer';
		$a_allocation[forestBase::PGSQL]['integer [int]']['columnLength'] = null;
		$a_allocation[forestBase::PGSQL]['integer [int]']['decimalLength'] = null;
		
		$a_allocation[forestBase::PGSQL]['integer [big]']['columnType'] = 'bigint';
		$a_allocation[forestBase::PGSQL]['integer [big]']['columnLength'] = null;
		$a_allocation[forestBase::PGSQL]['integer [big]']['decimalLength'] = null;
		
		$a_allocation[forestBase::PGSQL]['datetime']['columnType'] = 'timestamp';
		$a_allocation[forestBase::PGSQL]['datetime']['columnLength'] = null;
		$a_allocation[forestBase::PGSQL]['datetime']['decimalLength'] = null;
		
		$a_allocation[forestBase::PGSQL]['time']['columnType'] = 'time';
		$a_allocation[forestBase::PGSQL]['time']['columnLength'] = null;
		$a_allocation[forestBase::PGSQL]['time']['decimalLength'] = null;
		
		$a_allocation[forestBase::PGSQL]['double']['columnType'] = 'double precision';
		$a_allocation[forestBase::PGSQL]['double']['columnLength'] = null;
		$a_allocation[forestBase::PGSQL]['double']['decimalLength'] = null;
		
		$a_allocation[forestBase::PGSQL]['decimal']['columnType'] = 'decimal';
		$a_allocation[forestBase::PGSQL]['decimal']['columnLength'] = 10;
		$a_allocation[forestBase::PGSQL]['decimal']['decimalLength'] = 2;
		
		$a_allocation[forestBase::PGSQL]['bool']['columnType'] = 'bit';
		$a_allocation[forestBase::PGSQL]['bool']['columnLength'] = null;
		$a_allocation[forestBase::PGSQL]['bool']['decimalLength'] = null;
		
		/* forestBase::OCISQL */
		$a_allocation[forestBase::OCISQL]['text [36]']['columnType'] = 'VARCHAR2';
		$a_allocation[forestBase::OCISQL]['text [36]']['columnLength'] = 36;
		$a_allocation[forestBase::OCISQL]['text [36]']['decimalLength'] = null;
		
		$a_allocation[forestBase::OCISQL]['text [255]']['columnType'] = 'VARCHAR2';
		$a_allocation[forestBase::OCISQL]['text [255]']['columnLength'] = 255;
		$a_allocation[forestBase::OCISQL]['text [255]']['decimalLength'] = null;
		
		$a_allocation[forestBase::OCISQL]['text']['columnType'] = 'CLOB';
		$a_allocation[forestBase::OCISQL]['text']['columnLength'] = null;
		$a_allocation[forestBase::OCISQL]['text']['decimalLength'] = null;
		
		$a_allocation[forestBase::OCISQL]['integer [small]']['columnType'] = 'SMALLINT';
		$a_allocation[forestBase::OCISQL]['integer [small]']['columnLength'] = null;
		$a_allocation[forestBase::OCISQL]['integer [small]']['decimalLength'] = null;
		
		$a_allocation[forestBase::OCISQL]['integer [int]']['columnType'] = 'INTEGER';
		$a_allocation[forestBase::OCISQL]['integer [int]']['columnLength'] = null;
		$a_allocation[forestBase::OCISQL]['integer [int]']['decimalLength'] = null;
		
		$a_allocation[forestBase::OCISQL]['integer [big]']['columnType'] = 'LONG';
		$a_allocation[forestBase::OCISQL]['integer [big]']['columnLength'] = null;
		$a_allocation[forestBase::OCISQL]['integer [big]']['decimalLength'] = null;
		
		$a_allocation[forestBase::OCISQL]['datetime']['columnType'] = 'TIMESTAMP';
		$a_allocation[forestBase::OCISQL]['datetime']['columnLength'] = null;
		$a_allocation[forestBase::OCISQL]['datetime']['decimalLength'] = null;
		
		$a_allocation[forestBase::OCISQL]['time']['columnType'] = 'INTERVAL DAY(0) TO SECOND(0)';
		$a_allocation[forestBase::OCISQL]['time']['columnLength'] = null;
		$a_allocation[forestBase::OCISQL]['time']['decimalLength'] = null;
		
		$a_allocation[forestBase::OCISQL]['double']['columnType'] = 'DOUBLE PRECISION';
		$a_allocation[forestBase::OCISQL]['double']['columnLength'] = null;
		$a_allocation[forestBase::OCISQL]['double']['decimalLength'] = null;
		
		$a_allocation[forestBase::OCISQL]['decimal']['columnType'] = 'NUMBER';
		$a_allocation[forestBase::OCISQL]['decimal']['columnLength'] = 38;
		$a_allocation[forestBase::OCISQL]['decimal']['decimalLength'] = 2;
		
		$a_allocation[forestBase::OCISQL]['bool']['columnType'] = 'CHAR';
		$a_allocation[forestBase::OCISQL]['bool']['columnLength'] = 1;
		$a_allocation[forestBase::OCISQL]['bool']['decimalLength'] = null;
		
		/* forestBase::MongoDB */
		$a_allocation[forestBase::MongoDB]['text [36]']['columnType'] = 'VARCHAR';
		$a_allocation[forestBase::MongoDB]['text [36]']['columnLength'] = null;
		$a_allocation[forestBase::MongoDB]['text [36]']['decimalLength'] = null;
		
		$a_allocation[forestBase::MongoDB]['text [255]']['columnType'] = 'VARCHAR';
		$a_allocation[forestBase::MongoDB]['text [255]']['columnLength'] = null;
		$a_allocation[forestBase::MongoDB]['text [255]']['decimalLength'] = null;
		
		$a_allocation[forestBase::MongoDB]['text']['columnType'] = 'TEXT';
		$a_allocation[forestBase::MongoDB]['text']['columnLength'] = null;
		$a_allocation[forestBase::MongoDB]['text']['decimalLength'] = null;
		
		$a_allocation[forestBase::MongoDB]['integer [small]']['columnType'] = 'SMALLINT';
		$a_allocation[forestBase::MongoDB]['integer [small]']['columnLength'] = null;
		$a_allocation[forestBase::MongoDB]['integer [small]']['decimalLength'] = null;
		
		$a_allocation[forestBase::MongoDB]['integer [int]']['columnType'] = 'INTEGER';
		$a_allocation[forestBase::MongoDB]['integer [int]']['columnLength'] = null;
		$a_allocation[forestBase::MongoDB]['integer [int]']['decimalLength'] = null;
		
		$a_allocation[forestBase::MongoDB]['integer [big]']['columnType'] = 'BIGINT';
		$a_allocation[forestBase::MongoDB]['integer [big]']['columnLength'] = null;
		$a_allocation[forestBase::MongoDB]['integer [big]']['decimalLength'] = null;
		
		$a_allocation[forestBase::MongoDB]['datetime']['columnType'] = 'TIMESTAMP';
		$a_allocation[forestBase::MongoDB]['datetime']['columnLength'] = null;
		$a_allocation[forestBase::MongoDB]['datetime']['decimalLength'] = null;
		
		$a_allocation[forestBase::MongoDB]['time']['columnType'] = 'TIME';
		$a_allocation[forestBase::MongoDB]['time']['columnLength'] = null;
		$a_allocation[forestBase::MongoDB]['time']['decimalLength'] = null;
		
		$a_allocation[forestBase::MongoDB]['double']['columnType'] = 'DOUBLE';
		$a_allocation[forestBase::MongoDB]['double']['columnLength'] = null;
		$a_allocation[forestBase::MongoDB]['double']['decimalLength'] = null;
		
		$a_allocation[forestBase::MongoDB]['decimal']['columnType'] = 'DECIMAL';
		$a_allocation[forestBase::MongoDB]['decimal']['columnLength'] = null;
		$a_allocation[forestBase::MongoDB]['decimal']['decimalLength'] = null;
		
		$a_allocation[forestBase::MongoDB]['bool']['columnType'] = 'BOOL';
		$a_allocation[forestBase::MongoDB]['bool']['columnLength'] = null;
		$a_allocation[forestBase::MongoDB]['bool']['decimalLength'] = null;
		
		/* get column properties of allocation matrix */
		$p_s_columnType = $a_allocation[$p_s_baseGateway][$p_s_sqlType]['columnType'];
		$p_i_columnLength = $a_allocation[$p_s_baseGateway][$p_s_sqlType]['columnLength'];
		$p_i_columnDecimalLength = $a_allocation[$p_s_baseGateway][$p_s_sqlType]['decimalLength'];
	}
	
	/**
	 * transpose general constraint type to base-gateway defined constraint type and further information for query execution
	 *
	 * @param string $p_s_baseGateway  base-gateway constant
	 * @param string $p_s_constraintType  constraint type of sql column
	 * @param string $p_s_outConstraintType  transposed constraint type, passed by reference
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static yes
	 */
	public static function ConstraintTypeAllocation($p_s_baseGateway, $p_s_constraintType, &$p_s_outConstraintType) {
		/* check base gateway parameter */
        switch ($p_s_baseGateway) {
			case forestBase::MariaSQL:
			case forestBase::MSSQL:
			case forestBase::PGSQL:
			case forestBase::SQLite3:
			case forestBase::OCISQL:
			case forestBase::MongoDB:
				break;
			default:
				throw new forestException('Invalid BaseGateway[%0]', array($p_s_baseGateway));
			break;
		}
		
		/* check sql type parameter */
		 switch ($p_s_constraintType) {
			case 'NULL':
			case 'NOT NULL':
			case 'UNIQUE':
			case 'PRIMARY KEY':
			case 'DEFAULT':
			case 'INDEX':
			case 'AUTO_INCREMENT':
				break;
			default:
				throw new forestException('Invalid SqlType[%0]', array($p_s_constraintType));
			break;
		}
		
		$a_allocation = array();
		
		/* forestBase::MariaSQL */
		$a_allocation[forestBase::MariaSQL]['NULL']['constraintType'] = 'NULL';
		$a_allocation[forestBase::MariaSQL]['NOT NULL']['constraintType'] = 'NOT NULL';
		$a_allocation[forestBase::MariaSQL]['UNIQUE']['constraintType'] = 'UNIQUE';
		$a_allocation[forestBase::MariaSQL]['PRIMARY KEY']['constraintType'] = 'PRIMARY KEY';
		$a_allocation[forestBase::MariaSQL]['DEFAULT']['constraintType'] = 'DEFAULT';
		$a_allocation[forestBase::MariaSQL]['INDEX']['constraintType'] = 'INDEX';
		$a_allocation[forestBase::MariaSQL]['AUTO_INCREMENT']['constraintType'] = 'AUTO_INCREMENT';
		
		/* forestBase::SQLite3 */
		$a_allocation[forestBase::SQLite3]['NULL']['constraintType'] = 'NULL';
		$a_allocation[forestBase::SQLite3]['NOT NULL']['constraintType'] = 'NOT NULL';
		$a_allocation[forestBase::SQLite3]['UNIQUE']['constraintType'] = 'UNIQUE';
		$a_allocation[forestBase::SQLite3]['PRIMARY KEY']['constraintType'] = 'PRIMARY KEY';
		$a_allocation[forestBase::SQLite3]['DEFAULT']['constraintType'] = 'DEFAULT';
		$a_allocation[forestBase::SQLite3]['INDEX']['constraintType'] = 'INDEX';
		$a_allocation[forestBase::SQLite3]['AUTO_INCREMENT']['constraintType'] = 'AUTOINCREMENT';
		
		/* forestBase::MSSQL */
		$a_allocation[forestBase::MSSQL]['NULL']['constraintType'] = 'NULL';
		$a_allocation[forestBase::MSSQL]['NOT NULL']['constraintType'] = 'NOT NULL';
		$a_allocation[forestBase::MSSQL]['UNIQUE']['constraintType'] = 'UNIQUE';
		$a_allocation[forestBase::MSSQL]['PRIMARY KEY']['constraintType'] = 'PRIMARY KEY';
		$a_allocation[forestBase::MSSQL]['DEFAULT']['constraintType'] = 'DEFAULT';
		$a_allocation[forestBase::MSSQL]['INDEX']['constraintType'] = 'INDEX';
		$a_allocation[forestBase::MSSQL]['AUTO_INCREMENT']['constraintType'] = 'IDENTITY(1,1)';
		
		/* forestBase::PGSQL */
		$a_allocation[forestBase::PGSQL]['NULL']['constraintType'] = 'NULL';
		$a_allocation[forestBase::PGSQL]['NOT NULL']['constraintType'] = 'NOT NULL';
		$a_allocation[forestBase::PGSQL]['UNIQUE']['constraintType'] = 'UNIQUE';
		$a_allocation[forestBase::PGSQL]['PRIMARY KEY']['constraintType'] = 'PRIMARY KEY';
		$a_allocation[forestBase::PGSQL]['DEFAULT']['constraintType'] = 'DEFAULT';
		$a_allocation[forestBase::PGSQL]['INDEX']['constraintType'] = 'INDEX';
		$a_allocation[forestBase::PGSQL]['AUTO_INCREMENT']['constraintType'] = '';
		
		/* forestBase::OCISQL */
		$a_allocation[forestBase::OCISQL]['NULL']['constraintType'] = 'NULL';
		$a_allocation[forestBase::OCISQL]['NOT NULL']['constraintType'] = 'NOT NULL';
		$a_allocation[forestBase::OCISQL]['UNIQUE']['constraintType'] = 'UNIQUE';
		$a_allocation[forestBase::OCISQL]['PRIMARY KEY']['constraintType'] = 'PRIMARY KEY';
		$a_allocation[forestBase::OCISQL]['DEFAULT']['constraintType'] = 'DEFAULT';
		$a_allocation[forestBase::OCISQL]['INDEX']['constraintType'] = 'INDEX';
		$a_allocation[forestBase::OCISQL]['AUTO_INCREMENT']['constraintType'] = '';
		
		/* forestBase::OCISQL */
		$a_allocation[forestBase::MongoDB]['NULL']['constraintType'] = 'NULL';
		$a_allocation[forestBase::MongoDB]['NOT NULL']['constraintType'] = 'NOT NULL';
		$a_allocation[forestBase::MongoDB]['UNIQUE']['constraintType'] = 'UNIQUE';
		$a_allocation[forestBase::MongoDB]['PRIMARY KEY']['constraintType'] = 'PRIMARY KEY';
		$a_allocation[forestBase::MongoDB]['DEFAULT']['constraintType'] = 'DEFAULT';
		$a_allocation[forestBase::MongoDB]['INDEX']['constraintType'] = 'INDEX';
		$a_allocation[forestBase::MongoDB]['AUTO_INCREMENT']['constraintType'] = 'AUTO_INCREMENT';
		
		/* get constraint type of allocation matrix */
		$p_s_outConstraintType = $a_allocation[$p_s_baseGateway][$p_s_constraintType]['constraintType'];
	}
}

abstract class forestSQLQueryAbstract {
	use \fPHP\Roots\forestData;
	
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
	
	/**
	 * constructor of forestSQLQueryAbstract abstract class, set general parameters like information of the main query class and other arrays for query creation
	 *
	 * @param forestSQLQuery $p_o_sqlQuery  query object of forestSQLQuery class, thus you are forced to use this class only over an instance of forestSQLQuery
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		$this->BaseGateway = new forestString($p_o_sqlQuery->BaseGateway, false);
		$this->SqlType = new forestString($p_o_sqlQuery->SqlType, false);
		$this->AmountJoins = new forestInt;
		$this->Table = new forestString($p_o_sqlQuery->Table);
		
		$this->Operators = new forestArray(array('=', '<', '<=', '>', '>=', '<>', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'IS', 'IS NOT'), false);
		$this->FilterOperators = new forestArray(array('AND', 'OR', 'XOR'), false);
		$this->JoinTypes = new forestArray(array('INNER JOIN', 'NATURAL JOIN', 'CROSS JOIN', 'OUTER JOIN', 'LEFT OUTER JOIN', 'RIGHT OUTER JOIN', 'FULL OUTER JOIN'), false);
		$this->SqlAggregations = new forestArray(array('AVG', 'COUNT', 'MAX', 'MIN', 'SUM'), false);
		$this->SqlIndexConstraints = new forestArray(array('UNIQUE', 'PRIMARY KEY', 'INDEX'), false);
		$this->AlterOperations = new forestArray(array('ADD', 'CHANGE', 'DROP'), false);
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				$this->SqlColumnTypes = new forestArray(array('VARCHAR', 'TEXT', 'SMALLINT', 'INT', 'BIGINT', 'TIMESTAMP', 'TIME', 'DOUBLE', 'DECIMAL', 'BIT'), false);
				$this->SqlConstraints = new forestArray(array('NULL', 'NOT NULL', 'UNIQUE', 'PRIMARY KEY', 'DEFAULT', 'INDEX', 'AUTO_INCREMENT', 'SIGNED', 'UNSIGNED'), false);
			break;
			case forestBase::MSSQL:
				$this->SqlColumnTypes = new forestArray(array('nvarchar', 'text', 'smallint', 'int', 'bigint', 'datetime', 'time', 'float', 'decimal', 'bit'), false);
				$this->SqlConstraints = new forestArray(array('NULL', 'NOT NULL', 'UNIQUE', 'PRIMARY KEY', 'DEFAULT', 'INDEX', 'IDENTITY(1,1)'), false);
			break;
			case forestBase::PGSQL:
				$this->SqlColumnTypes = new forestArray(array('varchar', 'text', 'smallint', 'integer', 'bigint', 'timestamp', 'time', 'double precision', 'decimal', 'bit', 'serial'), false);
				$this->SqlConstraints = new forestArray(array('NULL', 'NOT NULL', 'UNIQUE', 'PRIMARY KEY', 'DEFAULT', 'INDEX', ''), false);
			break;
			case forestBase::SQLite3:
				$this->SqlColumnTypes = new forestArray(array('varchar', 'text', 'smallint', 'integer', 'bigint', 'datetime', 'time', 'double', 'decimal', 'bit'), false);
				$this->SqlConstraints = new forestArray(array('NULL', 'NOT NULL', 'UNIQUE', 'PRIMARY KEY', 'DEFAULT', 'INDEX', 'AUTOINCREMENT'), false);
			break;
			case forestBase::OCISQL:
				$this->SqlColumnTypes = new forestArray(array('VARCHAR2', 'CLOB', 'SMALLINT', 'INTEGER', 'LONG', 'TIMESTAMP', 'INTERVAL DAY(0) TO SECOND(0)', 'DOUBLE PRECISION', 'NUMBER', 'CHAR'), false);
				$this->SqlConstraints = new forestArray(array('NULL', 'NOT NULL', 'UNIQUE', 'PRIMARY KEY', 'DEFAULT', 'INDEX', ''), false);
			break;
			case forestBase::MongoDB:
				$this->SqlColumnTypes = new forestArray(array('VARCHAR', 'TEXT', 'SMALLINT', 'INTEGER', 'BIGINT', 'TIMESTAMP', 'TIME', 'DOUBLE', 'DECIMAL', 'BOOL'), false);
				$this->SqlConstraints = new forestArray(array('NULL', 'NOT NULL', 'UNIQUE', 'PRIMARY KEY', 'DEFAULT', 'INDEX', 'AUTO_INCREMENT'), false);
			break;
			default:
				throw new forestException('BaseGateway[%0] not implemented', array($this->BaseGateway->value));
			break;
		}
	}
	
	/**
	 * important parsing function, pretending SQL-Injection, formatting date values, etc
	 *
	 * @param string $p_s_value  value of a sql field/column
	 *
	 * @return string  returning checked and edited sql field/column value
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function ParseValue($p_s_value) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$b_isDateTime = false;
		$b_isEmptyDateTime = false;
		
		if (is_null($p_s_value)) {
			$p_s_value = '';
		}
		
		if (is_a($p_s_value, '\\fPHP\Helper\\forestDateTime')) {
			$b_isDateTime = true;
			
			if ($p_s_value->EmptyDate) {
				$b_isEmptyDateTime = $p_s_value->EmptyDate;
			}
			
			if ( ( ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == forestBase::PGSQL) || ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == forestBase::MongoDB) ) && ($b_isEmptyDateTime) ) {
				$p_s_value = $p_s_value->ToString('H:i:s');
			} else if ( ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == forestBase::OCISQL) && ($b_isEmptyDateTime) ) {
				$p_s_value = '+0 ' . $p_s_value->ToString('H:i:s');
			} else {
				$p_s_value = $p_s_value->ToString();
				
				if ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == forestBase::OCISQL) {
					$p_s_value = str_replace('T', ' ', $p_s_value);
				}
			}
		}
		
		if (is_a($p_s_value, '\\fPHP\Helper\\forestLookupData')) {
			$p_s_value = $p_s_value->PrimaryValue;
		}
		
		if (is_string($p_s_value)) {
			if (strlen($p_s_value) == 0) {
				$p_s_value = 'NULL';
			}
			
			if ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == forestBase::MariaSQL) {
				$p_s_value = str_replace('\\', '\\\\', $p_s_value);
			}
			
			/* date conversion for sql query [dd.MM.yyyy] / [dd.MM.yyyy hh:mm:ss] / [yyyy.MM.dd hh:mm:ss] */
			switch ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway) {
				case forestBase::MariaSQL:
				case forestBase::PGSQL:
				case forestBase::SQLite3:
				case forestBase::OCISQL:
				case forestBase::MongoDB:
					if (preg_match('/^ ((0[1-9])|(1[0-9])|2[0-9]|(3[0-1])) \. ((0[1-9])|(1[0-2])) \. (\d){4} $/x', $p_s_value)) {
						$s_foo = explode('.', $p_s_value);
						$p_s_value = $s_foo[2] . '-' . $s_foo[1] . '-' . $s_foo[0];
					} else if (preg_match('/^ ((0[1-9])|(1[0-9])|2[0-9]|(3[0-1])) \. ((0[1-9])|(1[0-2])) \. (\d){4} (\s|T) (0[0-9]|1[0-9]|2[0-3]) : ([0-5][0-9]) : ([0-5][0-9]) $/x', $p_s_value)) {
						$s_foo  = explode(' ', $p_s_value);
						$s_foo2 = explode('.', $s_foo[0]);
						$p_s_value = $s_foo2[2] . '-' . $s_foo2[1] . '-' . $s_foo2[0] . ' ' . $s_foo[1];
					}
					
					if ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == forestBase::OCISQL) {
						if (preg_match('/^ (\d){4} - ((0[1-9])|(1[0-2])) - ((0[1-9])|(1[0-9])|2[0-9]|(3[0-1])) (\s|T) (0[0-9]|1[0-9]|2[0-3]) : ([0-5][0-9]) : ([0-5][0-9]) $/x', $p_s_value)) {
							$b_isDateTime = true;
						}
					}
				break;
				case forestBase::MSSQL:
					if (preg_match('/^ ((0[1-9])|(1[0-9])|2[0-9]|(3[0-1])) \. ((0[1-9])|(1[0-2])) \. (\d){4} $/x', $p_s_value)) {
						$s_foo = explode('.', $p_s_value);
						$p_s_value = $s_foo[2] . '-' . $s_foo[1] . '-' . $s_foo[0];
					} else if (preg_match('/^ ((0[1-9])|(1[0-9])|2[0-9]|(3[0-1])) \. ((0[1-9])|(1[0-2])) \. (\d){4} (\s) (0[0-9]|1[0-9]|2[0-3]) : ([0-5][0-9]) : ([0-5][0-9]) $/x', $p_s_value)) {
						$s_foo  = explode(' ', $p_s_value);
						$s_foo2 = explode('.', $s_foo[0]);
						$p_s_value = $s_foo2[2] . '-' . $s_foo2[1] . '-' . $s_foo2[0] . 'T' . $s_foo[1];
					} else if (preg_match('/^ (\d){4} - ((0[1-9])|(1[0-2]))  - ((0[1-9])|(1[0-9])|2[0-9]|(3[0-1])) (\s) (0[0-9]|1[0-9]|2[0-3]) : ([0-5][0-9]) : ([0-5][0-9]) $/x', $p_s_value)) {
						$s_foo  = explode(' ', $p_s_value);
						$p_s_value = $s_foo[0] . 'T' . $s_foo[1];
					} else if (preg_match('/^ ((0[1-9])|(1[0-9])|2[0-9]|(3[0-1])) \. ((0[1-9])|(1[0-2])) \. (\d){4} (T) (0[0-9]|1[0-9]|2[0-3]) : ([0-5][0-9]) : ([0-5][0-9]) $/x', $p_s_value)) {
						$s_foo  = explode('T', $p_s_value);
						$s_foo2 = explode('.', $s_foo[0]);
						$p_s_value = $s_foo2[2] . '-' . $s_foo2[1] . '-' . $s_foo2[0] . 'T' . $s_foo[1];
					} else if (preg_match('/^ (\d){4} - ((0[1-9])|(1[0-2]))  - ((0[1-9])|(1[0-9])|2[0-9]|(3[0-1])) (T) (0[0-9]|1[0-9]|2[0-3]) : ([0-5][0-9]) : ([0-5][0-9]) $/x', $p_s_value)) {
						$s_foo  = explode('T', $p_s_value);
						$p_s_value = $s_foo[0] . 'T' . $s_foo[1];
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
			if ( (get_magic_quotes_gpc()) || ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == forestBase::MSSQL) || ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == forestBase::SQLite3) || ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == forestBase::PGSQL) || ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == forestBase::OCISQL) || ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == forestBase::MongoDB) ) {
				/* un-quotes a quoted string */
				$p_s_value = stripslashes($p_s_value);
			}
			
			if ( ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == forestBase::MSSQL) || ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == forestBase::SQLite3) || ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == forestBase::PGSQL) || ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == forestBase::OCISQL) ) {
				/* replace ' single quote, with two single quotes */
				$p_s_value = str_replace("'", "''", $p_s_value);
			}
			
			if ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == forestBase::MongoDB) {
				/* replace " double quote, with two single quotes */
				$p_s_value = str_replace('"', '\'\'', $p_s_value);
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
			
			if ( ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == forestBase::OCISQL) && ($b_isDateTime) && (!$b_isEmptyDateTime) ) {
				$p_s_value = 'timestamp ' . $p_s_value;
			}
			
			if ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == forestBase::MongoDB) {
				/* remove surrounding single quotes, because they are not necessary for mongodb commands */
				$p_s_value = substr($p_s_value, 1, -1);
			}
		}
		
		return $p_s_value;
	}
}

class forestSQLSelect extends forestSQLQueryAbstract {
	use \fPHP\Roots\forestData;

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
	
	/**
	 * constructor of forestSQLSelect class
	 *
	 * @param forestSQLQuery $p_o_sqlQuery  query object of forestSQLQuery class, thus you are forced to use this class only over an instance of forestSQLQuery
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
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
	
	/**
	 * generates the sql query as string value
	 *
	 * @return string  sql query
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __toString() {
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
			case forestBase::MSSQL:
				$s_foo .= ' FROM ' . '[' . $this->Table->value . ']';
			break;
			case forestBase::PGSQL:
			case forestBase::OCISQL:
			case forestBase::MongoDB:
				$s_foo .= ' FROM ' . '"' . $this->Table->value . '"';
			break;
			case forestBase::SQLite3:
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
	use \fPHP\Roots\forestData;

	/* Fields */
	
	private $ColumnValues;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestSQLInsert class
	 *
	 * @param forestSQLQuery $p_o_sqlQuery  query object of forestSQLQuery class, thus you are forced to use this class only over an instance of forestSQLQuery
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->ColumnValues = new forestObject(new forestObjectList('forestSQLColumnValue'), false);
	}
	
	/**
	 * generates the sql query as string value
	 *
	 * @return string  sql query
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __toString() {
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
			case forestBase::MSSQL:
				$s_foo = 'INSERT INTO ' . '[' .  $this->Table->value . ']' . ' (';
				
				foreach ($this->ColumnValues->value as $s_key => $o_columnValue) {
					if (!issetStr($o_columnValue->Table->value)) {
						$s_foo1 .=  '[' . $o_columnValue->Column . ']';
					} else {
						$s_foo1 .=  '[' . $o_columnValue->Table->value . ']' . '.' . '[' . $o_columnValue->Column . ']';
					}
					
					$s_foo2 .= $o_columnValue->Value->scalar;
					
					if ($s_key != $s_lastKey) {
						$s_foo1 .= ', ';
						$s_foo2 .= ', ';
					}
				}
			break;
			case forestBase::PGSQL:
			case forestBase::OCISQL:
			case forestBase::MongoDB:
				$s_foo = 'INSERT INTO ' . '"' . $this->Table->value . '"' . ' (';
				
				foreach ($this->ColumnValues->value as $s_key => $o_columnValue) {
					$s_foo1 .=  '"' . $o_columnValue->Column . '"';
					
					$s_foo2 .= $o_columnValue->Value->scalar;
					
					if ($s_key != $s_lastKey) {
						$s_foo1 .= ', ';
						$s_foo2 .= ', ';
					}
				}
			break;
			case forestBase::SQLite3:
				$s_foo = 'INSERT INTO ' . '`' . $this->Table->value . '`' . ' (';
				
				foreach ($this->ColumnValues->value as $s_key => $o_columnValue) {
					$s_foo1 .=  '`' . $o_columnValue->Column . '`';
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
		
		if ($this->BaseGateway->value == forestBase::OCISQL) {
			//$s_foo .= ' RETURNING "Id" INTO :PrimId';
		}
		
		return $s_foo;
	}
}

class forestSQLUpdate extends forestSQLQueryAbstract {
	use \fPHP\Roots\forestData;

	/* Fields */
	
	private $ColumnValues;
	private $Where;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestSQLUpdate class
	 *
	 * @param forestSQLQuery $p_o_sqlQuery  query object of forestSQLQuery class, thus you are forced to use this class only over an instance of forestSQLQuery
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->ColumnValues = new forestObject(new forestObjectList('forestSQLColumnValue'), false);
		$this->Where = new forestObject(new forestObjectList('forestSQLWhere'), false);
	}
	
	/**
	 * generates the sql query as string value
	 *
	 * @return string  sql query
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __toString() {
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
			case forestBase::MSSQL:
				$s_foo = 'UPDATE ' . '[' . $this->Table->value . ']' . ' SET ';
				
				foreach ($this->ColumnValues->value as $s_key => $o_columnValue) {
					if (!issetStr($o_columnValue->Table->value)) {
						$s_foo .=  '[' . $o_columnValue->Column . ']' . ' = ' . $o_columnValue->Value->scalar;
					} else {
						$s_foo .=  '[' . $o_columnValue->Table->value . ']' . '.' . '[' . $o_columnValue->Column . ']' . ' = ' . $o_columnValue->Value->scalar;
					}
				
					if ($s_key != $s_lastKey) {
						$s_foo .= ', ';
					}
				}
			break;
			case forestBase::PGSQL:
			case forestBase::OCISQL:
			case forestBase::MongoDB:
				$s_foo = 'UPDATE ' . '"' . $this->Table->value . '"' . ' SET ';
				
				foreach ($this->ColumnValues->value as $s_key => $o_columnValue) {
					$s_foo .=  '"' . $o_columnValue->Column . '"' . ' = ' . $o_columnValue->Value->scalar;
					
					if ($s_key != $s_lastKey) {
						$s_foo .= ', ';
					}
				}
			break;
			case forestBase::SQLite3:
				$s_foo = 'UPDATE ' . '`' . $this->Table->value . '`' . ' SET ';
				
				foreach ($this->ColumnValues->value as $s_key => $o_columnValue) {
					$s_foo .=  '`' . $o_columnValue->Column . '`' . ' = ' . $o_columnValue->Value->scalar;
					
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
	use \fPHP\Roots\forestData;

	/* Fields */
	
	private $Where;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestSQLDelete class
	 *
	 * @param forestSQLQuery $p_o_sqlQuery  query object of forestSQLQuery class, thus you are forced to use this class only over an instance of forestSQLQuery
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->Where = new forestObject(new forestObjectList('forestSQLWhere'), false);
	}
	
	/**
	 * generates the sql query as string value
	 *
	 * @return string  sql query
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				$s_foo = 'DELETE FROM ' . '`' . $this->Table->value . '`';
			break;
			case forestBase::MSSQL:
				$s_foo = 'DELETE FROM ' . '[' . $this->Table->value . ']';
			break;
			case forestBase::PGSQL:
			case forestBase::OCISQL:
			case forestBase::MongoDB:
				$s_foo = 'DELETE FROM ' . '"' . $this->Table->value . '"';
			break;
			case forestBase::SQLite3:
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
	use \fPHP\Roots\forestData;

	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestSQLTruncate class
	 *
	 * @param forestSQLQuery $p_o_sqlQuery  query object of forestSQLQuery class, thus you are forced to use this class only over an instance of forestSQLQuery
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
	}
	
	/**
	 * generates the sql query as string value
	 *
	 * @return string  sql query
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				$s_foo = 'TRUNCATE TABLE ' . '`' . $this->Table->value . '`';
			break;
			case forestBase::MSSQL:
				$s_foo = 'TRUNCATE TABLE ' . '[' . $this->Table->value . ']';
			break;
			case forestBase::PGSQL:
			case forestBase::OCISQL:
			case forestBase::MongoDB:
				$s_foo = 'TRUNCATE TABLE ' . '"' . $this->Table->value . '"';
			break;
			case forestBase::SQLite3:
				$s_foo = 'DELETE FROM ' . '`' . $this->Table->value . '`';
			break;
			default:
				throw new forestException('BaseGateway[%0] not implemented', array($this->BaseGateway->value));
			break;
		}
		
		return $s_foo;
	}
}

class forestSQLCreate extends forestSQLQueryAbstract {
	use \fPHP\Roots\forestData;

	/* Fields */
	
	private $Columns;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestSQLCreate class
	 *
	 * @param forestSQLQuery $p_o_sqlQuery  query object of forestSQLQuery class, thus you are forced to use this class only over an instance of forestSQLQuery
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->Columns = new forestObject(new forestObjectList('forestSQLColumnStructure'), false);
	}
	
	/**
	 * generates the sql query as string value
	 *
	 * @return string  sql query
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
			case forestBase::SQLite3:
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
			case forestBase::MSSQL:
				$s_foo = 'CREATE TABLE ' . '[' . $this->Table->value . ']';
				
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
			case forestBase::PGSQL:
			case forestBase::OCISQL:
			case forestBase::MongoDB:
				$s_foo = 'CREATE TABLE ' . '"' . $this->Table->value . '"';
				
				if ($this->Columns->value->Count() <= 0) {
					if ($this->BaseGateway->value != forestBase::MongoDB) {
						throw new forestException('Columns object list is empty');
					}
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
	use \fPHP\Roots\forestData;

	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestSQLDrop class
	 *
	 * @param forestSQLQuery $p_o_sqlQuery  query object of forestSQLQuery class, thus you are forced to use this class only over an instance of forestSQLQuery
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
	}
	
	/**
	 * generates the sql query as string value
	 *
	 * @return string  sql query
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				$s_foo = 'DROP TABLE ' . '`' . $this->Table->value . '`';
			break;
			case forestBase::MSSQL:
				$s_foo = 'DROP TABLE ' . '[' . $this->Table->value . ']';
			break;
			case forestBase::PGSQL:
			case forestBase::OCISQL:
			case forestBase::MongoDB:
				$s_foo = 'DROP TABLE ' . '"' . $this->Table->value . '"';
			break;
			case forestBase::SQLite3:
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
	use \fPHP\Roots\forestData;

	/* Fields */
	
	private $Columns;
	private $Constraints;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestSQLAlter class
	 *
	 * @param forestSQLQuery $p_o_sqlQuery  query object of forestSQLQuery class, thus you are forced to use this class only over an instance of forestSQLQuery
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->Columns = new forestObject(new forestObjectList('forestSQLColumnStructure'), false);
		$this->Constraints = new forestObject(new forestObjectList('forestSQLConstraint'), false);
	}
	
	/**
	 * generates the sql query as string value
	 *
	 * @return string  sql query
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				$s_foo = 'ALTER TABLE ' . '`' . $this->Table->value . '`' . ' ';
				
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
			case forestBase::SQLite3:
				if (!( ($this->Columns->value->Count()) xor ($this->Constraints->value->Count()) )) {
					throw new forestException('Columns and Constraints object lists are both empty or both set');
				} else {
					if ($this->Columns->value->Count() >= 1) {
						$s_lastKey = $this->Columns->value->LastKey();
						$o_changeColumn = null;
						$a_deleteColumns = null;
						
						foreach ($this->Columns->value as $s_key => $o_column) {
							$s_foo .= 'ALTER TABLE ' . '`' . $this->Table->value . '`' . ' ';
							
							if ($o_column->AlterOperation == 'CHANGE') {
								if ($this->Columns->value->Count() > 1) {
									throw new forestException('Columns object lists must contain only one item for CHANGE operation');
								}
								
								$o_changeColumn = $o_column;
								break;
							}
							
							if ($o_column->AlterOperation == 'DROP') {
								if ($a_deleteColumns == null) {
									$a_deleteColumns = array();
								}
								
								$a_deleteColumns[] = $o_column;
							}
							
							if ($o_column->AlterOperation == 'ADD') {
								$s_foo .= 'ADD ';
							}
							
							if ($s_key == $s_lastKey) {
								$s_foo .= $o_column;
							} else {
								$s_foo .= $o_column . ';;;';
							}
						}
						
						if ( ($o_changeColumn != null) || ($a_deleteColumns != null) ) {
							$s_foo = '';
							
							/* get twig object of current table */
							$s_tempTable = $this->Table->value;
							\fPHP\Helper\forestStringLib::RemoveTablePrefix($s_tempTable);
							$s_fooTwig = '\\fPHP\\Twigs\\' . $s_tempTable . 'Twig';
							$o_tempTwig = new $s_fooTwig;
							
							/* get all tablefields of current table */
							$o_glob = \fPHP\Roots\forestGlobals::init();
							$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
							
							$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $o_tempTwig->fphp_TableUUID, 'operator' => '=', 'filterOperator' => 'AND'), array('column' => 'SqlTypeUUID', 'value' => 'NULL', 'operator' => 'IS NOT', 'filterOperator' => 'AND'));
							$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
							$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
							$o_glob->Temp->Del('SQLAdditionalFilter');
							
							$a_columns = array(
								'Id' => array(
									'columnType' => 'integer [int]',
									'constraints' => array('NOT NULL', 'PRIMARY KEY', 'AUTO_INCREMENT')
								),
								'UUID' => array(
									'columnType' => 'text [36]',
									'constraints' => array('NOT NULL', 'UNIQUE')
								)
							);
							
							$a_columnsNew = array('Id', 'UUID');
							$a_columnsOld = array('Id', 'UUID');
							
							/* check identifier column */
							if (in_array('Identifier', $o_tempTwig->fphp_Mapping)) {
								$a_columns['Identifier'] = array(
									'columnType' => 'text [255]',
									'constraints' => array('NOT NULL', 'UNIQUE')
								);
								
								$a_columnsNew[] = 'Identifier';
								$a_columnsOld[] = 'Identifier';
							}
							
							foreach ($o_tablefields->Twigs as $o_tablefield) {
								/* ignore forestCombination, dropzone and form field */
								if ((strval($o_tablefield->ForestDataUUID) == 'forestCombination') || (strval($o_tablefield->FormElementUUID) == \fPHP\Forms\forestFormElement::DROPZONE) || (strval($o_tablefield->FormElementUUID) == \fPHP\Forms\forestFormElement::FORM)) {
									continue;
								}
								
								if ( ($a_deleteColumns != null) && (in_array($o_tablefield->FieldName, $a_deleteColumns)) ) {
									continue;
								} else if ( ($o_changeColumn != null) && ($o_tablefield->FieldName == $o_changeColumn->Name) ) {
									$a_columns[$o_changeColumn->NewName] = array('columnType' => strval($o_tablefield->SqlTypeUUID), 'constraints' => array('NULL'));
									$a_columnsNew[] = $o_changeColumn->NewName;
									$a_columnsOld[] = $o_changeColumn->Name;
								} else {
									$a_columns[$o_tablefield->FieldName] = array('columnType' => strval($o_tablefield->SqlTypeUUID), 'constraints' => array('NULL'));
									$a_columnsNew[] = $o_tablefield->FieldName;
									$a_columnsOld[] = $o_tablefield->FieldName;
								}
							}
							
							/* check created column */
							if ( (in_array('Created', $o_tempTwig->fphp_Mapping)) && (!in_array('Created', $a_deleteColumns)) ) {
								$a_columns['Created'] = array(
									'columnType' => 'datetime',
									'constraints' => array('NULL')
								);
								
								$a_columnsNew[] = 'Created';
								$a_columnsOld[] = 'Created';
							}
							
							/* check createdby column */
							if ( (in_array('CreatedBy', $o_tempTwig->fphp_Mapping)) && (!in_array('CreatedBy', $a_deleteColumns)) ) {
								$a_columns['CreatedBy'] = array(
									'columnType' => 'text [36]',
									'constraints' => array('NULL')
								);
								
								$a_columnsNew[] = 'CreatedBy';
								$a_columnsOld[] = 'CreatedBy';
							}
							
							/* check modified column */
							if ( (in_array('Modified', $o_tempTwig->fphp_Mapping)) && (!in_array('Modified', $a_deleteColumns)) ) {
								$a_columns['Modified'] = array(
									'columnType' => 'datetime',
									'constraints' => array('NULL')
								);
								
								$a_columnsNew[] = 'Modified';
								$a_columnsOld[] = 'Modified';
							}
							
							/* check modifiedby column */
							if ( (in_array('ModifiedBy', $o_tempTwig->fphp_Mapping)) && (!in_array('ModifiedBy', $a_deleteColumns)) ) {
								$a_columns['ModifiedBy'] = array(
									'columnType' => 'text [36]',
									'constraints' => array('NULL')
								);
								
								$a_columnsNew[] = 'ModifiedBy';
								$a_columnsOld[] = 'ModifiedBy';
							}
							
							/* CREATE TABLE forestphp_'table' (all columns with column new name) */
							$o_queryNew = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::CREATE, 'forestphp_' . $this->Table->value);
				
							foreach ($a_columns as $s_name => $a_info) {
								$o_column = new \fPHP\Base\forestSQLColumnStructure($o_queryNew);
									$o_column->Name = $s_name;
									
									$s_columnType = null;
									$i_columnLength = null;
									$i_columnDecimalLength = null;
									\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, strval($a_info['columnType']), $s_columnType, $i_columnLength, $i_columnDecimalLength);
									
									$o_column->ColumnType = $s_columnType;
									if ($i_columnLength != null) { $o_column->ColumnTypeLength = $i_columnLength; }
									if ($i_columnDecimalLength != null) { $o_column->ColumnTypeDecimalLength = $i_columnDecimalLength; }
									$o_column->AlterOperation = 'ADD';
									
									if (array_key_exists('constraints', $a_info)) {
										foreach ($a_info['constraints'] as $s_constraint) {
											$s_constraintType = null;
											\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, strval($s_constraint), $s_constraintType);
											
											$o_column->ConstraintList->Add($s_constraintType);
											
											if ( ($s_constraint == 'DEFAULT') && (array_key_exists('constraintDefaultValue', $a_info)) ) {
												$o_column->ConstraintDefaultValue = $a_info['constraintDefaultValue'];
											}
										}
									}
									
								$o_queryNew->Query->Columns->Add($o_column);
							}				
							
							/* Create table does not return a value */
							//$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryNew, false, false);
							$s_foo .= strval($o_queryNew) . ';;;';
							
							/* INSERT INTO forestphp_'table' (all columns with column new name) SELECT (all columns with column old name) FROM 'table' */
							$o_queryInsert = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::INSERT, 'forestphp_' . $this->Table->value);
							$s_query = 'INSERT INTO `forestphp_' . $this->Table->value . '` (`' . implode('`,`', $a_columnsNew) . '`) SELECT `' . implode('`,`', $a_columnsOld) . '` FROM `' . $this->Table->value . '`';
							$o_queryInsert->SetQuery($s_query);
							//$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryInsert, false, false);
							$s_foo .= $s_query . ';;;';
							
							/* DROP 'table' */
							$o_queryDrop = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::DROP, $this->Table->value);
							//$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryDrop, false, false);
							$s_foo .= strval($o_queryDrop) . ';;;';
							
							/* ALTER TABLE forestphp_'table' RENAME TO 'table' */
							$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, 'forestphp_' . $this->Table->value);
							$s_query = 'ALTER TABLE `forestphp_' . $this->Table->value . '` RENAME TO `' . $this->Table->value . '`';
							$o_queryAlter->SetQuery($s_query);
							//$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false);
							$s_foo .= $s_query;
						}
					} else if ($this->Constraints->value->Count() == 1) {
						$s_lastKey = $this->Constraints->value->LastKey();
			
						foreach ($this->Constraints->value as $s_key => $o_constraint) {
							if ($s_key == $s_lastKey) {
								$s_foo .= $o_constraint;
							} else {
								$s_foo .= $o_constraint . ', ';
							}
						}
					} else {
						throw new forestException('Constraints object lists must contain only one item');
					}
				}
			break;
			case forestBase::MSSQL:
				$s_foo = 'ALTER TABLE ' . '[' . $this->Table->value . ']' . ' ';
				
				if ( ($this->Columns->value->Count() <= 0) && ($this->Constraints->value->Count() <= 0) ) {
					throw new forestException('Columns and Constraints object lists are empty');
				} else {
					if ($this->Columns->value->Count() > 0) {
						$b_once = false;
						$s_lastKey = $this->Columns->value->LastKey();
			
						foreach ($this->Columns->value as $s_key => $o_column) {
							if ($o_column->AlterOperation == 'ADD') {
								if (!$b_once) {
									$s_foo .= 'ADD ';
									$b_once = true;
								}
							} else if ($o_column->AlterOperation == 'CHANGE') {
								$s_foo = '';
								
								if (issetStr($o_column->NewName)) {
									$s_foo .= 'EXEC sp_rename \'[' . $this->Table->value . '].[' . $o_column->Name . ']\', \'' . $o_column->NewName . '\', \'COLUMN\';;;';
								}
								
								$s_foo .= 'ALTER TABLE ' . '[' . $this->Table->value . ']' . ' ALTER ';
							} else if ($o_column->AlterOperation == 'DROP') {
								if (!$b_once) {
									$s_foo .= 'DROP ';
									$b_once = true;
								}
							}
							
							if ($s_key == $s_lastKey) {
								$s_foo .= $o_column;
							} else {
								if ($o_column->AlterOperation == 'CHANGE') {
									$s_foo .= $o_column . ';;;';
								} else {
									$s_foo .= $o_column . ', ';
								}
							}
						}
					} else if ($this->Constraints->value->Count() > 0) {
						$s_foo = '';
						
						$s_lastKey = $this->Constraints->value->LastKey();
			
						foreach ($this->Constraints->value as $s_key => $o_constraint) {
							if ($s_key == $s_lastKey) {
								$s_foo .= $o_constraint;
							} else {
								$s_foo .= $o_constraint . ';;;';
							}
						}
					}
				}
			break;
			case forestBase::PGSQL:
				$s_foo = 'ALTER TABLE ' . '"' . $this->Table->value . '"' . ' ';
				
				if ( ($this->Columns->value->Count() <= 0) && ($this->Constraints->value->Count() <= 0) ) {
					throw new forestException('Columns and Constraints object lists are empty');
				} else {
					if ($this->Columns->value->Count() > 0) {
						$s_lastKey = $this->Columns->value->LastKey();
						
						if ($this->Columns->value->{0}->AlterOperation == 'CHANGE') {
							if (issetStr($this->Columns->value->{0}->NewName)) {
								$s_foo = 'ALTER TABLE ' . '"' . $this->Table->value . '"' . ' RENAME COLUMN "' . $this->Columns->value->{0}->Name . '" TO "' . $this->Columns->value->{0}->NewName . '";;;' . $s_foo;
							}
						}
						
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
							if ($o_constraint->Constraint == 'INDEX') {
								$s_foo = '';
							}
							
							if ($s_key == $s_lastKey) {
								$s_foo .= $o_constraint;
							} else {
								if ($o_constraint->Constraint == 'INDEX') {
									$s_foo .= $o_constraint . ';;;';
								} else {
									$s_foo .= $o_constraint . ', ';
								}
							}
						}
					}
				}
			break;
			case forestBase::OCISQL:
			case forestBase::MongoDB:
				$s_foo = 'ALTER TABLE ' . '"' . $this->Table->value . '"' . ' ';
				
				if ( ($this->Columns->value->Count() <= 0) && ($this->Constraints->value->Count() <= 0) ) {
					throw new forestException('Columns and Constraints object lists are empty');
				} else {
					if ($this->Columns->value->Count() > 0) {
						$b_closeAdd = false;
						$b_closeModify = false;
						$b_closeDrop = false;
						
						if ($this->Columns->value->{0}->AlterOperation == 'ADD') {
							$s_foo .= 'ADD (';
							$b_closeAdd = true;
						}
						
						if ($this->Columns->value->{0}->AlterOperation == 'CHANGE') {
							$s_foo .= 'MODIFY (';
							$b_closeModify = true;
							
							if (issetStr($this->Columns->value->{0}->NewName)) {
								$s_foo = 'ALTER TABLE ' . '"' . $this->Table->value . '"' . ' RENAME COLUMN "' . $this->Columns->value->{0}->Name . '" TO "' . $this->Columns->value->{0}->NewName . '";;;' . $s_foo;
							}
						}
						
						if ($this->Columns->value->{0}->AlterOperation == 'DROP') {
							$s_foo .= 'DROP (';
							$b_closeDrop = true;
						}
						
						$s_lastKey = $this->Columns->value->LastKey();
						
						
						foreach ($this->Columns->value as $s_key => $o_column) {
							if ($s_key == $s_lastKey) {
								$s_foo .= $o_column;
							} else {
								$s_foo .= $o_column . ', ';
							}
						}
						
						if ( ($b_closeAdd) || ($b_closeModify) || ($b_closeDrop) ) {
							$s_foo .= ')';
						}
					} else if ($this->Constraints->value->Count() > 0) {
						$s_lastKey = $this->Constraints->value->LastKey();
			
						foreach ($this->Constraints->value as $s_key => $o_constraint) {
							if ($o_constraint->Constraint == 'INDEX') {
								$s_foo = '';
							}
							
							if ($s_key == $s_lastKey) {
								$s_foo .= $o_constraint;
							} else {
								if ($o_constraint->Constraint == 'INDEX') {
									$s_foo .= $o_constraint . ';;;';
								} else {
									$s_foo .= $o_constraint . ', ';
								}
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
	use \fPHP\Roots\forestData;

	/* Fields */
	
	private $Column;
	private $Name;
	private $SqlAggregation;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestSQLColumn class
	 *
	 * @param forestSQLQuery $p_o_sqlQuery  query object of forestSQLQuery class, thus you are forced to use this class only over an instance of forestSQLQuery
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->Column = new forestString;
		$this->Name = new forestString;
		$this->SqlAggregation = new forestList($this->SqlAggregations->value);
	}
	
	/**
	 * generates the sql query column as string value
	 *
	 * @return string  sql query column
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __toString() {
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
					case forestSQLQuery::REMOVE:
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
			case forestBase::MSSQL:
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
								$s_foo = '[' . $this->Table->value . ']' . '.' . '[' . $this->Column->value . ']';
							} else {
								$s_foo = '[' . $this->Column->value . ']';
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
					case forestSQLQuery::REMOVE:
						if (issetStr($this->Table->value)) {
							$s_foo = '[' . $this->Table->value . ']' . '.' . '[' . $this->Column->value . ']';
						} else {
							$s_foo = '[' . $this->Column->value . ']';
						}
					break;
					default:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
				}
			break;
			case forestBase::PGSQL:
				switch ($this->SqlType->value) {
					case forestSQLQuery::SELECT:
						if ($this->Column->value == '*') {
							$s_foo = '*';
							
							if (issetStr($this->SqlAggregation->value)) {
								$s_foo = $this->SqlAggregation->value . '(' . $s_foo . ')';
							}
							
							if (issetStr($this->Name->value)) {
								$s_foo = $s_foo . ' AS' . ' ' . '"' . $this->Name->value . '"';
							}
						} else {
							if (issetStr($this->Table->value)) {
								$s_foo = '"' . $this->Table->value . '"' . '.' . '"' . $this->Column->value . '"';
							} else {
								$s_foo = '"' . $this->Column->value . '"';
							}
							
							if (issetStr($this->SqlAggregation->value)) {
								$s_foo = $this->SqlAggregation->value . '(' . $s_foo . ')';
							}
							
							if (issetStr($this->Name->value)) {
								$s_foo = $s_foo . ' AS' . ' ' . '"' . $this->Name->value . '"';
							}
						}
					break;
					case forestSQLQuery::INSERT:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
					case forestSQLQuery::UPDATE:
					case forestSQLQuery::REMOVE:
						if (issetStr($this->Table->value)) {
							$s_foo = '"' . $this->Table->value . '"' . '.' . '"' . $this->Column->value . '"';
						} else {
							$s_foo = '"' . $this->Column->value . '"';
						}
					break;
					default:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
				}
			break;
			case forestBase::SQLite3:
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
					case forestSQLQuery::REMOVE:
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
			case forestBase::OCISQL:
			case forestBase::MongoDB:
				switch ($this->SqlType->value) {
					case forestSQLQuery::SELECT:
						if ($this->Column->value == '*') {
							$s_foo = '*';
							
							if (issetStr($this->SqlAggregation->value)) {
								$s_foo = $this->SqlAggregation->value . '(' . $s_foo . ')';
							}
							
							if (issetStr($this->Name->value)) {
								$s_foo = $s_foo . ' AS' . ' ' . '"' . $this->Name->value . '"';
							}
						} else {
							if (issetStr($this->Table->value)) {
								$s_foo = '"' . $this->Table->value . '"' . '.' . '"' . $this->Column->value . '"';
							} else {
								$s_foo = '"' . $this->Column->value . '"';
							}
							
							if (issetStr($this->SqlAggregation->value)) {
								$s_foo = $this->SqlAggregation->value . '(' . $s_foo . ')';
							}
							
							if (issetStr($this->Name->value)) {
								$s_foo = $s_foo . ' AS' . ' ' . '"' . $this->Name->value . '"';
							}
						}
					break;
					case forestSQLQuery::INSERT:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
					case forestSQLQuery::UPDATE:
					case forestSQLQuery::REMOVE:
						if (issetStr($this->Table->value)) {
							$s_foo = '"' . $this->Table->value . '"' . '.' . '"' . $this->Column->value . '"';
						} else {
							$s_foo = '"' . $this->Column->value . '"';
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
	use \fPHP\Roots\forestData;

	/* Fields */
	
	private $JoinType;
	private $Relations;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestSQLJoin class
	 *
	 * @param forestSQLQuery $p_o_sqlQuery  query object of forestSQLQuery class, thus you are forced to use this class only over an instance of forestSQLQuery
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->JoinType = new forestList($this->JoinTypes->value);
		$this->Relations = new forestObject(new forestObjectList('forestSQLRelation'), false);
	}
	
	/**
	 * generates the sql query join clause as string value
	 *
	 * @return string  sql query join clause
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __toString() {
		$s_foo = '';
		
		if ($this->Relations->value->Count() == 0) {
			throw new forestException('Object list is empty', null, true);
		}
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
			case forestBase::SQLite3:
				switch ($this->SqlType->value) {
					case forestSQLQuery::SELECT:
						$s_foo = $this->JoinType->value . ' ' . '`' . $this->Table->value . '`';
					break;
					case forestSQLQuery::INSERT:
					case forestSQLQuery::UPDATE:
					case forestSQLQuery::REMOVE:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
					default:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
				}
			break;
			case forestBase::MSSQL:
				switch ($this->SqlType->value) {
					case forestSQLQuery::SELECT:
						$s_foo = $this->JoinType->value . ' ' . '[' . $this->Table->value . ']';
					break;
					case forestSQLQuery::INSERT:
					case forestSQLQuery::UPDATE:
					case forestSQLQuery::REMOVE:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
					default:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
				}
			break;
			case forestBase::PGSQL:
			case forestBase::OCISQL:
			case forestBase::MongoDB:
				switch ($this->SqlType->value) {
					case forestSQLQuery::SELECT:
						$s_foo = $this->JoinType->value . ' ' . '"' . $this->Table->value . '"';
					break;
					case forestSQLQuery::INSERT:
					case forestSQLQuery::UPDATE:
					case forestSQLQuery::REMOVE:
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
	use \fPHP\Roots\forestData;

	/* Fields */
	
	private $ColumnLeft;
	private $ColumnRight;
	private $Operator;
	private $FilterOperator;
	private $BracketStart;
	private $BracketEnd;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestSQLRelation class
	 *
	 * @param forestSQLQuery $p_o_sqlQuery  query object of forestSQLQuery class, thus you are forced to use this class only over an instance of forestSQLQuery
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->ColumnLeft = new forestObject('forestSQLColumn');
		$this->ColumnRight = new forestObject('forestSQLColumn');
		$this->Operator = new forestList($this->Operators->value);
		$this->FilterOperator = new forestList($this->FilterOperators->value);
		$this->BracketStart = new forestBool;
		$this->BracketEnd = new forestBool;
	}
	
	/**
	 * generates the sql query relation clause as string value
	 *
	 * @return string  sql query relation clause
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
			case forestBase::MSSQL:
			case forestBase::PGSQL:
			case forestBase::SQLite3:
			case forestBase::OCISQL:
			case forestBase::MongoDB:
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
					case forestSQLQuery::REMOVE:
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
	use \fPHP\Roots\forestData;

	/* Fields */
	
	private $Column;
	private $Value;
	private $Operator;
	private $FilterOperator;
	private $BracketStart;
	private $BracketEnd;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestSQLWhere class
	 *
	 * @param forestSQLQuery $p_o_sqlQuery  query object of forestSQLQuery class, thus you are forced to use this class only over an instance of forestSQLQuery
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->Column = new forestObject('forestSQLColumn');
		$this->Value = new forestObject('stdClass');
		$this->Operator = new forestList($this->Operators->value);
		$this->FilterOperator = new forestList($this->FilterOperators->value);
		$this->BracketStart = new forestBool;
		$this->BracketEnd = new forestBool;
	}
	
	/**
	 * generates the sql query where clause as string value
	 *
	 * @return string  sql query where clause
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
			case forestBase::MSSQL:
			case forestBase::PGSQL:
			case forestBase::SQLite3:
			case forestBase::OCISQL:
			case forestBase::MongoDB:
				switch ($this->SqlType->value) {
					case forestSQLQuery::SELECT:
					case forestSQLQuery::UPDATE:
					case forestSQLQuery::REMOVE:
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
	use \fPHP\Roots\forestData;

	/* Fields */
	
	private $Columns;
	private $Directions;
	
	/* Properties */
	
	/**
	 * function to add a sql column to order clause
	 *
	 * @param forestSQLColumn $p_o_value  instace of forestSQLColumn
	 * @param bool $p_b_direction  true - ascending, false - descending
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function AddColumn(forestSQLColumn $p_o_value, $p_b_direction = true) {
		$this->Columns->value->Add($p_o_value);
		$this->Directions->value[] = $p_b_direction;
	}
	
	/* Methods */
	
	/**
	 * constructor of forestSQLOrderBy class
	 *
	 * @param forestSQLQuery $p_o_sqlQuery  query object of forestSQLQuery class, thus you are forced to use this class only over an instance of forestSQLQuery
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->Columns = new forestObject(new forestObjectList('forestSQLColumn'), false);
		$this->Directions = new forestArray;
		
	}
	
	/**
	 * generates the sql query order by clause as string value
	 *
	 * @return string  sql query order by clause
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
			case forestBase::MSSQL:
			case forestBase::PGSQL:
			case forestBase::SQLite3:
			case forestBase::OCISQL:
			case forestBase::MongoDB:
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
					case forestSQLQuery::REMOVE:
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
	use \fPHP\Roots\forestData;

	/* Fields */
	
	private $Start;
	private $Interval;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestSQLLimit class
	 *
	 * @param forestSQLQuery $p_o_sqlQuery  query object of forestSQLQuery class, thus you are forced to use this class only over an instance of forestSQLQuery
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->Start = new forestInt;
		$this->Interval = new forestInt;
	}
	
	/**
	 * generates the sql query limit clause as string value
	 *
	 * @return string  sql query limit clause
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
			case forestBase::SQLite3:
				switch ($this->SqlType->value) {
					case forestSQLQuery::SELECT:
						$s_foo = ' LIMIT ' . $this->Start->value . ', ' . $this->Interval->value;
					break;
					case forestSQLQuery::INSERT:
					case forestSQLQuery::UPDATE:
					case forestSQLQuery::REMOVE:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
					default:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
				}
			break;
			case forestBase::MSSQL:
			case forestBase::OCISQL:
				switch ($this->SqlType->value) {
					case forestSQLQuery::SELECT:
						$s_foo = ' OFFSET ' . $this->Start->value . ' ROWS FETCH NEXT ' . $this->Interval->value . ' ROWS ONLY';
					break;
					case forestSQLQuery::INSERT:
					case forestSQLQuery::UPDATE:
					case forestSQLQuery::REMOVE:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
					default:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
				}
			break;
			case forestBase::PGSQL:
			case forestBase::MongoDB:
				switch ($this->SqlType->value) {
					case forestSQLQuery::SELECT:
						$s_foo = ' LIMIT ' . $this->Interval->value . ' OFFSET ' . $this->Start->value;
					break;
					case forestSQLQuery::INSERT:
					case forestSQLQuery::UPDATE:
					case forestSQLQuery::REMOVE:
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
	use \fPHP\Roots\forestData;

	/* Fields */
	
	private $Column;
	private $Value;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestSQLColumnValue class
	 *
	 * @param forestSQLQuery $p_o_sqlQuery  query object of forestSQLQuery class, thus you are forced to use this class only over an instance of forestSQLQuery
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->Column = new forestString;
		$this->Value = new forestObject('stdClass');
	}
}

class forestSQLColumnStructure extends forestSQLQueryAbstract {
	use \fPHP\Roots\forestData;

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
	
	/**
	 * constructor of forestSQLColumnStructure class
	 *
	 * @param forestSQLQuery $p_o_sqlQuery  query object of forestSQLQuery class, thus you are forced to use this class only over an instance of forestSQLQuery
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
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
	
	/**
	 * generates the sql query column structure as string value
	 *
	 * @return string  sql query column structure
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __toString() {
		$s_foo = '';
		
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				switch ($this->SqlType->value) {
					case forestSQLQuery::CREATE:
					case forestSQLQuery::DROP:
					case forestSQLQuery::ALTER:
						if ($this->AlterOperation->value == 'DROP') {
							$s_foo .= 'DROP ' . '`' . $this->Name->value . '`';
						} else {
							if ($this->AlterOperation->value == 'ADD') {
								$this->NewName->value = $this->Name->value;
								
								if ($this->SqlType->value == forestSQLQuery::ALTER) {
									$s_foo .= 'ADD ';
								}
							} else if ($this->AlterOperation->value == 'CHANGE') {
								$s_foo .= 'CHANGE ' . '`' . $this->Name->value . '`' . ' ';
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
			case forestBase::SQLite3:
				switch ($this->SqlType->value) {
					case forestSQLQuery::CREATE:
					case forestSQLQuery::DROP:
					case forestSQLQuery::ALTER:
						$this->NewName->value = $this->Name->value;
						
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
					break;
					default:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
				}
			break;
			case forestBase::MSSQL:
				switch ($this->SqlType->value) {
					case forestSQLQuery::CREATE:
					case forestSQLQuery::DROP:
					case forestSQLQuery::ALTER:
						if ($this->AlterOperation->value == 'DROP') {
							$s_foo .= 'COLUMN ' . '[' . $this->Name->value . ']';
						} else {
							if ($this->AlterOperation->value == 'ADD') {
								$this->NewName->value = $this->Name->value;
							} else if ($this->AlterOperation->value == 'CHANGE') {
								$s_foo .= 'COLUMN ';
								
								if (!issetStr($this->NewName->value)) {
									$this->NewName->value = $this->Name->value;
								}
							} else {
								$this->NewName->value = $this->Name->value;
							}
							
							$s_foo .= '[' . $this->NewName->value . ']';
							
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
									
									/* changing default on column is not supported */
									if ( ($this->AlterOperation->value == 'CHANGE') && ($s_constraint == 'DEFAULT') ) {
										continue;
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
			case forestBase::PGSQL:
				switch ($this->SqlType->value) {
					case forestSQLQuery::CREATE:
					case forestSQLQuery::DROP:
					case forestSQLQuery::ALTER:
						if ($this->AlterOperation->value == 'DROP') {
							$s_foo .= 'DROP ' . '"' . $this->Name->value . '"';
						} else {
							if ($this->AlterOperation->value == 'ADD') {
								if ($this->SqlType->value == forestSQLQuery::ALTER) {
									$s_foo .= 'ADD ';
								}
							} else if ($this->AlterOperation->value == 'CHANGE') {
								if ($this->Name->value == 'Id') {
									throw new forestException('Cannot change settings for sql column "Id"');
								}
								
								$s_foo .= 'ALTER COLUMN ';
							}
						
							$s_name = $this->Name->value;
						
							if ( ($this->AlterOperation->value == 'CHANGE') && (issetStr($this->NewName->value)) ) {
								$s_name = $this->NewName->value;
							}
							
							$s_foo .= '"' . $s_name . '"';
							
							if (!issetStr($this->ColumnType->value)) {
								throw new forestException('ColumnType not set for sql column');
							}
							
							if ($this->AlterOperation->value == 'CHANGE') {
								$s_foo .= ' TYPE';
							}
							
							if ($this->ColumnType->value == 'bit') {
								$s_foo .= ' smallint DEFAULT 0 CHECK ("' . $s_name . '" >= 0 AND "' . $s_name . '" <= 1)';
							} else {
								$s_foo .= ' ' . $this->ColumnType->value;
							}
							
							if ($this->ColumnTypeLength->value > 0) {
								$s_foo .= '(' . $this->ColumnTypeLength->value;
								
								if ($this->ColumnTypeDecimalLength->value > 0) {
									$s_foo .= ',' . $this->ColumnTypeDecimalLength->value;
								}
								
								$s_foo .= ')';
							}
							
							$b_setNotNull = false;
							$b_setDefault = false;
							
							if ( ($this->ConstraintList->value->Count() > 0) && ($this->ColumnType->value != 'bit') ) {
								foreach ($this->ConstraintList->value as $s_constraint) {
									if (!in_array($s_constraint, $this->SqlConstraints->value)) {
										throw new forestException('Constraint[%0] is not a valid constraint for that base gateway [%1]', array($s_constraint, implode(',', $this->SqlConstraints->value)));
									}
									
									if ($this->AlterOperation->value == 'CHANGE') {
										if ($s_constraint == 'NOT NULL') {
											$s_foo .= ', ALTER COLUMN "' . $s_name . '" SET NOT NULL';
											
											$b_setNotNull = true;
										} else if ($s_constraint == 'DEFAULT') {
											$s_foo .= ', ALTER COLUMN "' . $s_name . '" SET DEFAULT ';
											
											if ($this->ConstraintDefaultValue->value == null) {
												throw new forestException('No value for constraint DEFAULT');
											}
											
											if (is_string($this->ConstraintDefaultValue->value->scalar)) {
												$s_foo .= ' \'' . $this->ConstraintDefaultValue->value->scalar . '\'';
											} else {
												$s_foo .= ' ' . $this->ConstraintDefaultValue->value->scalar;
											}
											
											$b_setDefault = true;
										}
									} else {
										if ($s_constraint == 'PRIMARY KEY') {
											$s_foo = '';
											
											if ($this->AlterOperation->value == 'ADD') {
												if ($this->SqlType->value == forestSQLQuery::ALTER) {
													$s_foo .= 'ADD ';
												}
											}
											
											$s_foo .= '"' . $s_name . '" serial '. $s_constraint;
											break;
										} else {
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
							}
							
							if ( ($this->AlterOperation->value == 'CHANGE') && ($this->ColumnType->value != 'bit') ) {
								if (!$b_setNotNull) {
									$s_foo .= ', ALTER COLUMN "' . $s_name . '" DROP NOT NULL';
								}
								
								if (!$b_setDefault) {
									$s_foo .= ', ALTER COLUMN "' . $s_name . '" DROP DEFAULT';
								}
							}
						}
					break;
					default:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
				}
			break;
			case forestBase::MongoDB:
			case forestBase::OCISQL:
				switch ($this->SqlType->value) {
					case forestSQLQuery::CREATE:
					case forestSQLQuery::DROP:
					case forestSQLQuery::ALTER:
						if ($this->AlterOperation->value == 'DROP') {
							$s_foo .= '"' . $this->Name->value . '"';
						} else {
							$s_name = $this->Name->value;
							
							if ( ($this->AlterOperation->value == 'CHANGE') && (issetStr($this->NewName->value)) ) {
								$s_name = $this->NewName->value;
							}
							
							$s_foo .= '"' . $s_name . '"';
							
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
									
									if ($s_constraint == 'PRIMARY KEY') {
										$s_foo = '"' . $s_name . '" NUMBER GENERATED by default on null as IDENTITY '. $s_constraint;
										break;
									} else if ($s_constraint == 'DEFAULT') {
										$s_foo .= ' ' . $s_constraint;
										
										if ($this->ConstraintDefaultValue->value == null) {
											throw new forestException('No value for constraint DEFAULT');
										}
										
										if (is_string($this->ConstraintDefaultValue->value->scalar)) {
											if ($this->ColumnType->value == 'TIME') {	
												$s_foo .= ' \'+0 ' . $this->ConstraintDefaultValue->value->scalar . '\'';
											} else {
												if ($this->ColumnType->value == 'TIMESTAMP') {
													$s_foo .= ' timestamp';
												}
												
												$s_foo .= ' \'' . $this->ConstraintDefaultValue->value->scalar . '\'';
											}
										} else {
											$s_foo .= ' ' . $this->ConstraintDefaultValue->value->scalar;
										}
										
										if ($this->ConstraintList->value->Has('NULL')) {
											$s_foo .= ' NULL';
										} else if ($this->ConstraintList->value->Has('NOT NULL')) {
											$s_foo .= ' NOT NULL';
										}
									} else {
										if ($s_constraint == 'DEFAULT') {
											continue;
										}
										
										if ( ( ($s_constraint == 'NULL') || ($s_constraint == 'NOT NULL') ) && ($this->ConstraintList->value->Has('DEFAULT')) ) {
											continue;
										}
										
										$s_foo .= ' ' . $s_constraint;
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
	use \fPHP\Roots\forestData;

	/* Fields */
	
	private $Constraint;
	private $Name;
	private $NewName;
	private $AlterOperation;
	private $Columns;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestSQLConstraint class
	 *
	 * @param forestSQLQuery $p_o_sqlQuery  query object of forestSQLQuery class, thus you are forced to use this class only over an instance of forestSQLQuery
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct(forestSQLQuery $p_o_sqlQuery) {
		parent::__construct($p_o_sqlQuery);
		
		$this->Constraint = new forestList($this->SqlIndexConstraints->value);
		$this->Name = new forestString;
		$this->NewName = new forestString;
		$this->AlterOperation = new forestList($this->AlterOperations->value, 'ADD');
		$this->Columns = new forestObject(new forestObjectList('forestString'), false);
	}
	
	/**
	 * generates the sql query constraint as string value
	 *
	 * @return string  sql query constraint
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __toString() {
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
			case forestBase::SQLite3:
				switch ($this->SqlType->value) {
					case forestSQLQuery::CREATE:
					case forestSQLQuery::DROP:
					case forestSQLQuery::ALTER:
						if ($this->AlterOperation->value != 'DROP') {
							if ($this->Columns->value->Count() <= 0) {
								throw new forestException('Columns object list is empty');
							}
						}
						
						$s_constraint = $this->Constraint->value;
						
						if ($s_constraint == 'UNIQUE') {
							$s_constraint .= ' INDEX';
						}
						
						if ($this->AlterOperation->value == 'ADD') {
							$s_foo = 'CREATE ' . $s_constraint . ' `' . $this->Name->value . '` ON `' . $this->Table->value . '` (';
							
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
							$s_foo = 'DROP INDEX `' . $this->Name->value . '`;;;';
							
							if (!issetStr($this->NewName->value)) {
								throw new forestException('No new name for changing constraint');
							}
							
							$s_foo .= 'CREATE ' . $s_constraint . ' `' . $this->NewName->value . '` ON `' . $this->Table->value . '` (';
							
							$s_lastKey = $this->Columns->value->LastKey();
					
							foreach ($this->Columns->value as $s_key => $o_column) {
								if ($s_key == $s_lastKey) {
									$s_foo .= '`' . $o_column->value . '`';
								} else {
									$s_foo .= '`' . $o_column->value . '`' . ', ';
								}
							}
							
							$s_foo .= ')';
						} else if ($this->AlterOperation->value == 'DROP') {
							$s_foo = 'DROP INDEX `' . $this->Name->value . '`';
						}
					break;
					default:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
				}
			break;
			case forestBase::MSSQL:
				switch ($this->SqlType->value) {
					case forestSQLQuery::CREATE:
					case forestSQLQuery::DROP:
					case forestSQLQuery::ALTER:
						if ($this->AlterOperation->value != 'DROP') {
							if ($this->Columns->value->Count() <= 0) {
								throw new forestException('Columns object list is empty');
							}
						}
						
						$s_constraint = $this->Constraint->value;
						
						if ($s_constraint == 'UNIQUE') {
							$s_constraint .= ' INDEX';
						}
						
						if ($this->AlterOperation->value == 'ADD') {
							$s_foo = 'CREATE ' . $s_constraint . ' [' . $this->Name->value . '] ON [' . $this->Table->value . '] (';
							
							$s_lastKey = $this->Columns->value->LastKey();
					
							foreach ($this->Columns->value as $s_key => $o_column) {
								if ($s_key == $s_lastKey) {
									$s_foo .= '[' . $o_column->value . ']';
								} else {
									$s_foo .= '[' . $o_column->value . ']' . ', ';
								}
							}
							
							$s_foo .= ')';
						} else if ($this->AlterOperation->value == 'CHANGE') {
							$s_foo = 'DROP INDEX [' . $this->Name->value . '] ON [' . $this->Table->value . '];;;';
							
							if (!issetStr($this->NewName->value)) {
								throw new forestException('No new name for changing constraint');
							}
							
							$s_foo .= 'CREATE ' . $s_constraint . ' [' . $this->NewName->value . '] ON [' . $this->Table->value . '] (';
							
							$s_lastKey = $this->Columns->value->LastKey();
					
							foreach ($this->Columns->value as $s_key => $o_column) {
								if ($s_key == $s_lastKey) {
									$s_foo .= '[' . $o_column->value . ']';
								} else {
									$s_foo .= '[' . $o_column->value . ']' . ', ';
								}
							}
							
							$s_foo .= ')';
						} else if ($this->AlterOperation->value == 'DROP') {
							$s_foo = 'DROP INDEX [' . $this->Name->value . '] ON [' . $this->Table->value . ']';
						}
					break;
					default:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
				}
			break;
			case forestBase::PGSQL:
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
							if ($this->Constraint->value == 'INDEX') {
								$s_foo = 'CREATE INDEX "' . $this->Name->value . '" ON "' . $this->Table->value . '" (';
							} else {
								$s_foo = 'ADD CONSTRAINT "' . $this->Name->value . '" ' . $this->Constraint->value . ' (';
							}
							
							$s_lastKey = $this->Columns->value->LastKey();
					
							foreach ($this->Columns->value as $s_key => $o_column) {
								if ($s_key == $s_lastKey) {
									$s_foo .= '"' . $o_column->value . '"';
								} else {
									$s_foo .= '"' . $o_column->value . '"' . ', ';
								}
							}
							
							$s_foo .= ')';
						} else if ($this->AlterOperation->value == 'CHANGE') {
							if (!issetStr($this->NewName->value)) {
								throw new forestException('No new name for changing constraint');
							}
							
							if ($this->Constraint->value == 'INDEX') {
								$s_foo = 'CREATE INDEX "' . $this->NewName->value . '" ON "' . $this->Table->value . '" (';
							} else {
								$s_foo = 'ADD CONSTRAINT "' . $this->NewName->value . '" ' . $this->Constraint->value . ' (';
							}
							
							$s_lastKey = $this->Columns->value->LastKey();
					
							foreach ($this->Columns->value as $s_key => $o_column) {
								if ($s_key == $s_lastKey) {
									$s_foo .= '"' . $o_column->value . '"';
								} else {
									$s_foo .= '"' . $o_column->value . '"' . ', ';
								}
							}
							
							$s_foo .= ')';
							
							if ($this->Constraint->value == 'INDEX') {
								$s_foo = ';;;DROP INDEX "' . $this->Name->value . '"';
							} else {
								$s_foo .= ', DROP CONSTRAINT "' . $this->Name->value . '"';
							}
						} else if ($this->AlterOperation->value == 'DROP') {
							if ($this->Constraint->value == 'INDEX') {
								$s_foo = 'DROP INDEX "' . $this->Name->value . '"';
							} else {
								$s_foo = 'DROP CONSTRAINT "' . $this->Name->value . '"';
							}
						}
					break;
					default:
						throw new forestException('SqlType[%0] not implemented', array($this->SqlType->value));
					break;
				}
			break;
			case forestBase::MongoDB:
			case forestBase::OCISQL:
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
							if ($this->Constraint->value == 'INDEX') {
								$s_foo = 'CREATE INDEX "' . $this->Name->value . '" ON "' . $this->Table->value . '" (';
							} else {
								$s_foo = 'ADD CONSTRAINT "' . $this->Name->value . '" ' . $this->Constraint->value . ' (';
							}
							
							$s_lastKey = $this->Columns->value->LastKey();
					
							foreach ($this->Columns->value as $s_key => $o_column) {
								if ($s_key == $s_lastKey) {
									$s_foo .= '"' . $o_column->value . '"';
								} else {
									$s_foo .= '"' . $o_column->value . '"' . ', ';
								}
							}
							
							$s_foo .= ')';
						} else if ($this->AlterOperation->value == 'CHANGE') {
							if (!issetStr($this->NewName->value)) {
								throw new forestException('No new name for changing constraint');
							}
							
							if ($this->Constraint->value == 'INDEX') {
								$s_foo = 'DROP INDEX "' . $this->Name->value . '";;;';
							} else {
								$s_foo = 'DROP CONSTRAINT "' . $this->Name->value . '";;;ALTER TABLE "' . $this->Table->value . '" ';
							}
							
							if ($this->Constraint->value == 'INDEX') {
								$s_foo .= 'CREATE INDEX "' . $this->NewName->value . '" ON "' . $this->Table->value . '" (';
							} else {
								$s_foo .= 'ADD CONSTRAINT "' . $this->NewName->value . '" ' . $this->Constraint->value . ' (';
							}
							
							$s_lastKey = $this->Columns->value->LastKey();
					
							foreach ($this->Columns->value as $s_key => $o_column) {
								if ($s_key == $s_lastKey) {
									$s_foo .= '"' . $o_column->value . '"';
								} else {
									$s_foo .= '"' . $o_column->value . '"' . ', ';
								}
							}
							
							$s_foo .= ')';
						} else if ($this->AlterOperation->value == 'DROP') {
							if ($this->Constraint->value == 'INDEX') {
								$s_foo = 'DROP INDEX "' . $this->Name->value . '"';
							} else {
								$s_foo = 'DROP CONSTRAINT "' . $this->Name->value . '"';
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
?>