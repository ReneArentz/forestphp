<?php
/**
 * gathering class for all database interfaces an gateways who can be used by the script collection
 * whole database connectivity goes over this class
 * on initialization the developer can decide which gateway he wants to use
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestany.net>
 * @copyright   (c) 2024 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.0.0 stable
 * @link        https://forestany.net
 * @object-id   0x1 00009
 * @since       File available since Release 0.1.0 alpha
 * @deprecated  -
 *
 * @version log Version			Developer	Date		Comment
 * 				0.1.0 alpha		renea		2019-08-04	first build
 * 				0.1.1 alpha		renea		2019-08-07	added date conversion and trunk settings
 * 				1.0.0 stable	renea		2020-02-04	added MSSQL support
 * 				1.0.0 stable	renea		2020-02-05	added PGSQL support
 * 				1.0.0 stable	renea		2020-02-06	added SQLite3 support
 * 				1.0.0 stable	renea		2020-02-10	added OCISQL support
 * 				1.0.0 stable	renea		2020-02-11	added MongoDB support
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
use \fPHP\Roots\forestException as forestException;
use fPHP\Base\forestSQLQuery as forestSQLQuery;

class forestBase {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	const MariaSQL = 'mariasql';
	const MSSQL = 'mssql';
	const PGSQL = 'pgsql';
	const SQLite3 = 'sqlite3';
	const OCISQL = 'ocisql';
	const MongoDB = 'mongodb';
	
	const ASSOC = \PDO::FETCH_ASSOC;
	const NUM = \PDO::FETCH_NUM;
	const BOTH = \PDO::FETCH_BOTH;
	const OBJ = \PDO::FETCH_OBJ;
	const LAZY = \PDO::FETCH_LAZY;
	
	private $AmountQueries;
	private $Queries;
	private $BaseGateway;
	private $CurrentConnection;
	private $Query;
	private $CurrentResult;
	
	private $OCILastId;
	
	private $CurrentConnectionAlt;
	private $CurrentResultAlt;
	private $DatasourceAlt;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestBase class, set connection parameters to destination database
	 *
	 * @param string $p_s_baseGateway  base-gateway constant
	 * @param string $p_s_host  host value of database server
	 * @param string $p_s_datasource  database name
	 * @param string $p_s_user  database user
	 * @param string $p_s_password  database user's password
	 * @param bool $p_b_persistentConnection  standard true, optional
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct($p_s_baseGateway = '', $p_s_host = '', $p_s_datasource = '', $p_s_user = '', $p_s_password = '', $p_b_persistentConnection = true) {
		if (empty($p_s_baseGateway)) {
			throw new forestException('No base-gateway was selected');
		}
		
		if (empty($p_s_host)) {
			throw new forestException('No base-host/base-file was selected');
		}
		
		/* take over construct parameters */
		$this->AmountQueries = new forestInt(null, false);
		$this->Queries = new forestArray(null, false);
		$this->BaseGateway = new forestString;
		$this->CurrentConnection = new forestObject('PDO');
		$this->Query = new forestString;
		$this->CurrentResult = new forestObject('PDOStatement');
		
		$this->OCILastId = new forestInt(null, false);
		
		$this->CurrentConnectionAlt = null;
		$this->CurrentResultAlt = null;
		$this->DatasourceAlt = new forestString;
		
		$p_s_temp_user = null;
		$p_s_temp_pw = null;
			
		$this->BaseGateway->value = $p_s_baseGateway;
		
        /* initiate server connection */
		if ((!empty($p_s_user)) && (!empty($p_s_password))) {
			$p_s_temp_user = $p_s_user;
			$p_s_temp_pw = $p_s_password;
		} else if ((!empty($p_s_user)) && (empty($p_s_password))) {
			$p_s_temp_user = $p_s_user;
			$p_s_temp_pw = null;
		}
		
		/* chosse database gateway and create new PDO object with connection settings */
		try {
			switch ($this->BaseGateway->value) {
				case forestBase::MariaSQL:
					$this->CurrentConnection->value = new \PDO('mysql:dbname=' . $p_s_datasource . ';host=' . $p_s_host, $p_s_temp_user, $p_s_temp_pw, array(\PDO::ATTR_PERSISTENT => $p_b_persistentConnection));
				break;
				case forestBase::MSSQL:
					$this->CurrentConnection->value = new \PDO('sqlsrv:Server=' . $p_s_host . ';Database=' . $p_s_datasource, $p_s_temp_user, $p_s_temp_pw, array(\PDO::ATTR_PERSISTENT => $p_b_persistentConnection));
				break;
				case forestBase::PGSQL:
					$this->CurrentConnection->value = new \PDO('pgsql:host=' . $p_s_host . ';dbname=' . $p_s_datasource, $p_s_temp_user, $p_s_temp_pw, array(\PDO::ATTR_PERSISTENT => $p_b_persistentConnection));
				break;
				case forestBase::SQLite3:
					$this->CurrentConnection->value = new \PDO('sqlite:' . $p_s_host, $p_s_temp_user, $p_s_temp_pw, array(\PDO::ATTR_PERSISTENT => $p_b_persistentConnection));
				break;
				case forestBase::OCISQL:
					$s_datasource = '';
					$s_dataport = '1521';
					$s_servicename = $p_s_datasource;
					$s_charset = '';
					
					if ((strpos($p_s_host, ':') !== false)) {
						$a_datasource = explode(':', $p_s_host);
						$s_datasource = $a_datasource[0];
						$s_port = $a_datasource[1];
					} else {
						$s_datasource = $p_s_host;
					}
					
					if ((strpos($s_servicename, ':') !== false)) {
						$a_servicename = explode(':', $s_servicename);
						$s_servicename = $a_servicename[0];
						$s_charset = $a_servicename[1];
					}
					
					$s_tns = '(DESCRIPTION = (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ' . $s_datasource . ')(PORT = ' . $s_dataport . '))) (CONNECT_DATA = (SERVICE_NAME = ' . $s_servicename . ')))';
					
					if (!empty($s_charset)) {
						$s_tns .= ';charset=' . $s_charset;
					}
					
					$this->CurrentConnection->value = new \PDO('oci:dbname=' . $s_tns, $p_s_temp_user, $p_s_temp_pw, array(\PDO::ATTR_PERSISTENT => $p_b_persistentConnection));
				break;
				case forestBase::MongoDB:
					if ((!empty($p_s_temp_user)) && (!empty($p_s_temp_pw))) {
						$this->CurrentConnectionAlt = new forestObject(new \MongoDB\Driver\Manager('mongodb://' . $p_s_temp_user . ':' . $p_s_temp_pw . '@' . $p_s_host));
					} else if ((!empty($p_s_temp_user)) && (empty($p_s_temp_pw))) {
						$this->CurrentConnectionAlt = new forestObject(new \MongoDB\Driver\Manager('mongodb://' . $p_s_temp_user . '@' . $p_s_host));
					} else {
						$this->CurrentConnectionAlt = new forestObject(new \MongoDB\Driver\Manager('mongodb://' . $p_s_host));
					}
					
					$this->DatasourceAlt->value = $p_s_datasource;
				break;
				default:
					throw new forestException('Invalid base-gateway [' . $this->BaseGateway->value . '].');
				break;
			}
			
			if ($this->BaseGateway->value != forestBase::MongoDB) {
				/* activate auto commit */
				$this->CurrentConnection->value->setAttribute(\PDO::ATTR_AUTOCOMMIT, true);
				/* set exception setting for PDO class */
				$this->CurrentConnection->value->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
				$this->CurrentConnection->value->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			}
		} catch (\PDOException $o_pdoException) {
			throw new forestException('The connection to the database is not possible; <i>' . $o_pdoException->getCode() . ': ' . $o_pdoException->getMessage() . '</i>');
		} catch(\MongoDB\Driver\Exception $o_mongoException) {
			throw new forestException('The connection to the database is not possible; <i>' . $o_mongoException->getMessage() . '</i>');
		}
		
		/* check connection */
		if ($this->BaseGateway->value != forestBase::MongoDB) {
			if (!$this->CurrentConnection->value) {
				throw new forestException('The connection to the database is not possible.');
			}
		} else {
			if (!$this->CurrentConnectionAlt->value) {
				throw new forestException('The connection to the database is not possible.');
			}
		}
	}
	
	/**
	 * destructor of forestBase class, closes current connection
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __destruct() {
		/* close connection if object is being destroyed */
		if ($this->BaseGateway->value != forestBase::MongoDB) {
			unset($this->CurrentConnection->value);
			
			if ($this->CurrentConnection->value != null) {
				throw new forestException('Could not close the database connection; <i>' . $this->CurrentConnection->value->errorCode() . ': ' . $this->CurrentConnection->value->errorInfo()[2] . '</i>');
			}
		} else {
			unset($this->CurrentConnectionAlt->value);
			
			if ($this->CurrentConnectionAlt->value != null) {
				throw new forestException('Could not close the database connection');
			}
		}
	}
	
	/**
	 * function to send a query to the database and get a result set/answer/value
	 *
	 * @param forestSQLQuery $p_o_sqlQuery  sql query
	 * @param bool $p_b_transaction  use query in transaction mode
	 * @param bool $p_b_resultTwigList  convert result set into twig list
	 * @param bool $p_b_freeResult  free result memory
	 * @param integer $p_i_resultType  standard ASSOC - ASSOC, NUM, BOTH, OBJ, LAZY
	 *
	 * @return object  result set/twig list/row count
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function FetchQuery(\fPHP\Base\forestSQLQuery $p_o_sqlQuery, $p_b_transaction = false, $p_b_resultTwigList = true, $p_b_freeResult = false, $p_i_resultType = \fPHP\Base\forestBase::ASSOC) {
		/* call alternative FetchQuery function if we use MongoDB */
		if ($this->BaseGateway->value == forestBase::MongoDB) {
			return $this->FetchQueryAlt($p_o_sqlQuery, $p_b_transaction, $p_b_resultTwigList, $p_b_freeResult, $p_i_resultType);
		}
		
		/* save query statistics */
		$this->AmountQueries->value++;
		$this->Queries->value[] = date('H:i:s');
		$this->Queries->value[] = $p_o_sqlQuery . ';';
		
		/* checking parameter for the chosen result type - obj and lazy not implemented */
		if (($p_i_resultType != forestBase::ASSOC) && ($p_i_resultType != forestBase::NUM) && ($p_i_resultType != forestBase::BOTH)) {
			throw new forestException('Invalid result-type [' . $p_i_resultType . ']. Valid values:[ASSOC, NUM, BOTH]');
		}
		
		/* convert sql query object to string and execute sql query */
		$this->Query->value = strval($p_o_sqlQuery);
		
		global $b_debug_sql_query;
		global $b_debug_no_select_sql_query;
		if ($b_debug_sql_query) {
			echo '<pre>';
			echo $this->Query->value;
			echo '</pre><br>';
		} else if ($b_debug_no_select_sql_query) {
			if ($p_o_sqlQuery->SqlType != forestSQLQuery::SELECT) {
				echo '<pre>';
				echo $this->Query->value;
				echo '</pre><br>';
			}
		}
		
		try {
			/* if transaction flag is set, begin transaction */
			if ($p_b_transaction) {
				$this->CurrentConnection->value->beginTransaction();
			}
			
			$a_ociFieldInformation = array();
			
			/* check if we have multiple queries in one statement */
			$s_queries = explode(';;;', $this->Query->value);
			
			/* execute every query one by one */
			foreach ($s_queries as $s_query) {
				if (!empty($s_query)) {
					/* calculate last-insert-id, because PDO OCI driver does not support lastInsertId */
					if ( ($p_o_sqlQuery->SqlType == forestSQLQuery::INSERT) && ($this->BaseGateway->value == forestBase::OCISQL) ) {
						$this->CurrentResult->value = $this->CurrentConnection->value->prepare('SELECT MAX("Id") AS "LastInsertId" FROM "' . $p_o_sqlQuery->Table . '"');
						$this->CurrentResult->value->execute();
						$o_fetchRow = $this->CurrentResult->value->fetch($p_i_resultType, \PDO::FETCH_ORI_NEXT);
						
						if ($o_fetchRow) {
							$this->OCILastId->value = intval($o_fetchRow['LastInsertId']) + 1;
						}
					}
					
					/* query field information, because PDO OCI driver does not support getColumnMeta */
					if ( ($p_o_sqlQuery->SqlType == forestSQLQuery::SELECT) && ($this->BaseGateway->value == forestBase::OCISQL) ) {
						$this->CurrentResult->value = $this->CurrentConnection->value->prepare('SELECT "COLUMN_ID", "COLUMN_NAME", "DATA_TYPE" FROM "USER_TAB_COLS" WHERE TABLE_NAME = \'' . $p_o_sqlQuery->Table . '\' ORDER BY COLUMN_ID');
						$this->CurrentResult->value->execute();
						
						while ($o_fetchRow = $this->CurrentResult->value->fetch($p_i_resultType, \PDO::FETCH_ORI_NEXT)) {
							if (!$o_fetchRow) {
								throw new forestException('Could not fetch row; <i>' . $this->CurrentConnection->value->errorCode() . ': ' . $this->CurrentConnection->value->errorInfo()[2] . '</i>');
							}
							
							$a_ociFieldInformation[$o_fetchRow['COLUMN_ID']] = array('name' => $o_fetchRow['COLUMN_NAME'], 'native_type' => $o_fetchRow['DATA_TYPE']);
						}
					}
					
					/* execute query */
					$this->CurrentResult->value = $this->CurrentConnection->value->prepare($s_query);
					$this->CurrentResult->value->execute();
				}
			}
		} catch (\PDOException $o_pdoException) {
			/* if transaction flag is set, roll-back transaction */
			if ($p_b_transaction) {
				$this->CurrentConnection->value->rollBack();
			}
			
			throw new forestException('The query could not be executed; <i>' . $o_pdoException->getCode() . ': ' . $o_pdoException->getMessage() . '</i><br />' . $this->Query->value);
		}
		
		/* check if query got a result */
		if (!$this->CurrentResult->value) {
			/* if transaction flag is set, roll-back transaction */
			if ($p_b_transaction) {
				$this->CurrentConnection->value->rollBack();
			}
			
			throw new forestException('The query could not be executed or has no valid result.');
		}
		
		/* if transaction flag is set, commit transaction */
		if ($p_b_transaction) {
			$this->CurrentConnection->value->commit();
		}
		
		/* on SELECT query, prepare to return result rows */
		if ($p_o_sqlQuery->SqlType == forestSQLQuery::SELECT) {
			$b_once = false;
			$a_fieldInformation = array();
			$a_fetchData = array();
			
			/* fetch sql result into data-array */
			while ($o_fetchRow = $this->CurrentResult->value->fetch($p_i_resultType, \PDO::FETCH_ORI_NEXT)) {
				if (!$o_fetchRow) {
					throw new forestException('Could not fetch row; <i>' . $this->CurrentConnection->value->errorCode() . ': ' . $this->CurrentConnection->value->errorInfo()[2] . '</i>');
				}
				
				/*echo '<pre>';
				print_r($o_fetchRow);
				echo '</pre>';*/
				
				/* we only need columns information once after fetched one row */
				if (!$b_once) {
					/* get OCISQL field information queried before */
					if ($this->BaseGateway->value == forestBase::OCISQL) {
						$a_fieldInformation = $a_ociFieldInformation;
						
						/*echo '<pre>';
						print_r($a_fieldInformation);
						echo '</pre>';*/
					} else {
						/* gather field information for conversion handling */
						foreach (range(0, $this->CurrentResult->value->columnCount() - 1) as $i_column_index) {
							$foo = $this->CurrentResult->value->getColumnMeta($i_column_index);
							
							if (empty($foo)) {
								throw new forestException('Could not get column metadata; <i>' . $this->CurrentConnection->value->errorCode() . ': ' . $this->CurrentConnection->value->errorInfo()[2] . '</i>');
							}
							
							$a_fieldInformation[] = $foo;
						}
						
						/*echo '<pre>';
						print_r($a_fieldInformation);
						echo '</pre>';*/
					}
					
					$b_once = true;
				}
				
				$this->SetTypeFields($o_fetchRow, $a_fieldInformation, $p_i_resultType);
				$a_fetchData[] = $o_fetchRow;
			}
			
			/*echo '<pre>';
			print_r($a_fetchData);
			echo '</pre>';*/
			
			/* free result memory if necessary, only if you know that the result takes great amount of memory */
			if ($p_b_freeResult) {
				$b_freeResult = $this->CurrentResult->value->closeCursor();

				if (!$b_freeResult) {
					throw new forestException('Could not free up the connection; <i>' . $this->CurrentConnection->value->errorCode() . ': ' . $this->CurrentConnection->value->errorInfo()[2] . '</i>');
				}
			}
			
			/* return fetched data as a twig list class or raw data as an array */
			if ($p_b_resultTwigList) {
				$ol_twigs = new \fPHP\Twigs\forestTwigList($p_o_sqlQuery->Table, $a_fetchData, $p_i_resultType);
				$foo = $ol_twigs;
			} else {
				$foo = $a_fetchData;
			}
		} else {
			/* returning amount of affected rows by sql query */
			$foo = $this->CurrentResult->value->rowCount();
		}
		
		$this->Queries->value[] = date('H:i:s');
		
		return $foo;
	}
	
	/**
	 * alternative function to send a query to the database and get a result set/answer/value
	 *
	 * @param forestSQLQuery $p_o_sqlQuery  sql query
	 * @param bool $p_b_transaction  use query in transaction mode
	 * @param bool $p_b_resultTwigList  convert result set into twig list
	 * @param bool $p_b_freeResult  free result memory
	 * @param integer $p_i_resultType  standard ASSOC - ASSOC, NUM, BOTH, OBJ, LAZY
	 *
	 * @return object  result set/twig list/row count
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function FetchQueryAlt(\fPHP\Base\forestSQLQuery $p_o_sqlQuery, $p_b_transaction = false, $p_b_resultTwigList = true, $p_b_freeResult = false, $p_i_resultType = \fPHP\Base\forestBase::ASSOC) {
		/* save query statistics */
		$this->AmountQueries->value++;
		$this->Queries->value[] = date('H:i:s');
		$this->Queries->value[] = $p_o_sqlQuery . ';';
		
		$a_mongoCmdSettings = array();
		
		/* checking parameter for the chosen result type - obj and lazy not implemented */
		if (($p_i_resultType != forestBase::ASSOC) && ($p_i_resultType != forestBase::NUM) && ($p_i_resultType != forestBase::BOTH)) {
			throw new forestException('Invalid result-type [' . $p_i_resultType . ']. Valid values:[ASSOC, NUM, BOTH]');
		}
		
		/* convert sql query object to string and execute sql query */
		$this->Query->value = strval($p_o_sqlQuery);
		
		global $b_debug_sql_query;
		global $b_debug_no_select_sql_query;
		if ($b_debug_sql_query) {
			echo '<pre>';
			echo $this->Query->value;
			echo '</pre><br>';
		} else if ($b_debug_no_select_sql_query) {
			if ($p_o_sqlQuery->SqlType != forestSQLQuery::SELECT) {
				echo '<pre>';
				echo $this->Query->value;
				echo '</pre><br>';
			}
		}
		
		try {
			/* if transaction flag is set, begin transaction */
			if ($p_b_transaction) {
				/* not implemented */
			}
			
			/* execute query */
			$a_mongoCmdSettings = \fPHP\Base\forestSQLQuerytoMongoDBQuery::Transpose($p_o_sqlQuery, $this->DatasourceAlt->value);
			
			/*echo '<pre>';
			print_r($a_mongoCmdSettings);
			echo '</pre>';*/
			
			if ($a_mongoCmdSettings['mongodbCommandType'] == \fPHP\Base\forestSQLQuerytoMongoDBQuery::COMMAND) {
				foreach ($a_mongoCmdSettings['mongodbCommand'] as $o_mongoCommand) {
					$this->CurrentResultAlt = new forestObject($this->CurrentConnectionAlt->value->executeCommand($this->DatasourceAlt->value, $o_mongoCommand));
					//echo '<pre>'; print_r(($this->CurrentResultAlt->value->toArray())[0]->ok); echo '</pre>';
				}
			} else if ($a_mongoCmdSettings['mongodbCommandType'] == \fPHP\Base\forestSQLQuerytoMongoDBQuery::QUERY) {
				foreach ($a_mongoCmdSettings['mongodbCommand'] as $o_mongoQuery) {
					$this->CurrentResultAlt = new forestObject($this->CurrentConnectionAlt->value->executeQuery($this->DatasourceAlt->value . '.' . $a_mongoCmdSettings['mongodbCollection'], $o_mongoQuery));
					//echo '<pre>'; print_r($this->CurrentResultAlt->value); echo '</pre>';
				}
			} else if ($a_mongoCmdSettings['mongodbCommandType'] == \fPHP\Base\forestSQLQuerytoMongoDBQuery::BULKWRITE) {
				foreach ($a_mongoCmdSettings['mongodbCommand'] as $o_mongoBulk) {
					$this->CurrentResultAlt = new forestObject($this->CurrentConnectionAlt->value->executeBulkWrite($this->DatasourceAlt->value . '.' . $a_mongoCmdSettings['mongodbCollection'], $o_mongoBulk));
					//echo '<pre>'; print_r($this->CurrentResultAlt->value); echo '</pre>';
				}
			}
		} catch (\MongoDB\Driver\Exception $o_mongoException) {
			/* if transaction flag is set, roll-back transaction */
			if ($p_b_transaction) {
				/* not implemented */
			}
			
			throw new forestException('The query could not be executed; <i>' . $o_mongoException->getMessage() . '</i><br />' . $this->Query->value);
		}
		
		/* check if query got a result */
		if (!$this->CurrentResultAlt->value) {
			/* if transaction flag is set, roll-back transaction */
			if ($p_b_transaction) {
				/* not implemented */
			}
			
			throw new forestException('The query could not be executed or has no valid result.');
		}
		
		/* if transaction flag is set, commit transaction */
		if ($p_b_transaction) {
			/* not implemented */
		}
		
		/* on SELECT query, prepare to return result rows */
		if ($p_o_sqlQuery->SqlType == forestSQLQuery::SELECT) {
			$b_once = false;
			$a_fieldInformation = array();
			$a_fetchData = array();
			
			$o_glob = \fPHP\Roots\forestGlobals::init();
			
			if ($o_glob->Temp->Exists('MongoDBCountAll')) {
				$a_fetchData[] = array( $o_glob->Temp->{'MongoDBCountAll'} => count($this->CurrentResultAlt->value->toArray()) );
				$o_glob->Temp->Del('MongoDBCountAll');
				
				/*echo '<pre>';
				print_r($a_fetchData);
				echo '</pre>';*/
			} else {
				/* fetch sql result into data-array */
				foreach ($this->CurrentResultAlt->value as $o_row) {
					$a_fetchRow = array();
					
					foreach ($o_row as $o_fieldName => $o_fieldValue) {
						$a_fetchRow[$o_fieldName] = $o_fieldValue;
						
						if (!$b_once) {
							$a_fieldInformation[] = array('name' => $o_fieldName);
						}
					}
					
					$this->SetTypeFields($a_fetchRow, $a_fieldInformation, $p_i_resultType);
					$a_fetchData[] = $a_fetchRow;
					$b_once = true;
				}
				
				if ($o_glob->Temp->Exists('MongoDBDistinct')) {
					$a_newFetchData = array();
					$a_temp = array();
					
					foreach ($a_fetchData as $a_fetchRow) {
						if (!in_array($a_fetchRow[$o_glob->Temp->{'MongoDBDistinct'}], $a_temp)) {
							$a_newFetchData[] = $a_fetchRow;
							$a_temp[] = $a_fetchRow[$o_glob->Temp->{'MongoDBDistinct'}];
						}
					}
					
					$a_fetchData = $a_newFetchData;
					$o_glob->Temp->Del('MongoDBDistinct');
				}
				
				/*echo '<pre>';
				print_r($a_fetchData);
				echo '</pre>';*/
				
				/* free result memory if necessary, only if you know that the result takes great amount of memory */
				if ($p_b_freeResult) {
					/* not implemented */
				}
			}
			
			/* return fetched data as a twig list class or raw data as an array */
			if ($p_b_resultTwigList) {
				$ol_twigs = new \fPHP\Twigs\forestTwigList($p_o_sqlQuery->Table, $a_fetchData, $p_i_resultType);
				$foo = $ol_twigs;
			} else {
				$foo = $a_fetchData;
			}
		} else {
			/* returning amount of affected rows by sql query */
			if (array_key_exists('mongodbCommandType', $a_mongoCmdSettings)) {
				if ($a_mongoCmdSettings['mongodbCommandType'] == \fPHP\Base\forestSQLQuerytoMongoDBQuery::COMMAND) {
					$fooo = $this->CurrentResultAlt->value->toArray();
					$foo = $fooo[0]->ok;
				} else if ($a_mongoCmdSettings['mongodbCommandType'] == \fPHP\Base\forestSQLQuerytoMongoDBQuery::BULKWRITE) {
					if (is_a($this->CurrentResultAlt->value, 'MongoDB\Driver\Cursor')) {
						$foo = 1;
					} else if (is_a($this->CurrentResultAlt->value, 'MongoDB\Driver\WriteResult')) {
						$foo = intval($this->CurrentResultAlt->value->getInsertedCount()) + intval($this->CurrentResultAlt->value->getModifiedCount()) + intval($this->CurrentResultAlt->value->getDeletedCount());
					}
				}
			}
		}
		
		$this->Queries->value[] = date('H:i:s');
		
		return $foo;
	}
	
	/**
	 * function to start transaction mode
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function ManualTransaction() {
		if ($this->BaseGateway->value == forestBase::MongoDB) {
			/* not implemented */
		} else {
			/* check connection */
			if (!$this->CurrentConnection->value) {
				throw new forestException('No connection established.');
			}
			
			/* deactivate auto commit */
			$this->CurrentConnection->value->setAttribute(\PDO::ATTR_AUTOCOMMIT, false);

			$this->CurrentConnection->value->beginTransaction();
			//d2c('beginTransaction');
			global $b_transaction_active;
			$b_transaction_active = true;
		}
	}
	
	/**
	 * function to roll back all transactions
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function ManualRollBack() {
		if ($this->BaseGateway->value == forestBase::MongoDB) {
			/* not implemented */
		} else {
			/* check connection */
			if (!$this->CurrentConnection->value) {
				throw new forestException('No connection established.');
			}
			
			$this->CurrentConnection->value->rollBack();
			//d2c('rollBack');
			global $b_transaction_active;
			$b_transaction_active = false;

			/* activate auto commit */
			$this->CurrentConnection->value->setAttribute(\PDO::ATTR_AUTOCOMMIT, true);
	}
	}
	
	/**
	 * function to end transaction mode
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function ManualCommit() {
		if ($this->BaseGateway->value == forestBase::MongoDB) {
			/* not implemented */
		} else {
			/* check connection */
			if (!$this->CurrentConnection->value) {
				throw new forestException('No connection established.');
			}
			
			$this->CurrentConnection->value->commit();
			//d2c('commit');
			global $b_transaction_active;
			$b_transaction_active = false;

			/* activate auto commit */
			$this->CurrentConnection->value->setAttribute(\PDO::ATTR_AUTOCOMMIT, true);
		}
	}
	
	/**
	 * function to get id of last insert with this connection
	 *
	 * @return integer  id of last inserted record
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function LastInsertId() {
		if ($this->BaseGateway->value == forestBase::MongoDB) {
			$o_glob = \fPHP\Roots\forestGlobals::init();
			
			if ($o_glob->Temp->Exists('MongoDBLastInsertId')) {
				$foo = $o_glob->Temp->{'MongoDBLastInsertId'};
				$o_glob->Temp->Del('MongoDBLastInsertId');
			} else {
				$foo = 0;
			}
		} else {
			if ($this->BaseGateway->value == forestBase::OCISQL) {
				$foo = $this->OCILastId->value;
			} else {
				$foo = $this->CurrentConnection->value->lastInsertId();
			}
			
			if (!$foo) {
				throw new forestException('Could not get last insert id; <i>' . $this->CurrentConnection->value->errorCode() . ': ' . $this->CurrentConnection->value->errorInfo()[2] . '</i>');
			}
		}
		
		return $foo;
	}
	
	/**
	 * iterate all result rows with their field-information for conversion
	 *
	 * @param array $p_a_record  record data, passed by reference
	 * @param array $p_a_fieldInformation  field information data
	 * @param integer $p_i_resultType  ASSOC, NUM, BOTH, OBJ, LAZY
	 *
	 * @return null
	 *
	 * @access private
	 * @static no
	 */
	private function SetTypeFields(&$p_a_record, $p_a_fieldInformation, $p_i_resultType) {
		$i = 0;
		
		foreach ($p_a_fieldInformation as $field) {
			if ($p_i_resultType == forestBase::ASSOC) {
				$this->SetTypeValue($field, $p_a_record[$field['name']]);
			} else {
				$this->SetTypeValue($field, $p_a_record[$i]);
			}
			
			$i++;
		}
	}
	
	/**
	 * convert result field value with the help of the field information
	 *
	 * @param object $p_o_field  field data as array
	 * @param string $p_s_value  field's value, passed by reference
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function SetTypeValue($p_o_field, &$p_s_value) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/*echo '<pre>';var_export($p_s_value);echo '<br>';
		print_r($p_o_field);
		echo '</pre>'*/
		
		$s_type = null;
		
		/* define the type by dystinguishing the base gateway */
		switch ($this->BaseGateway->value) {
			case forestBase::MariaSQL:
				if ( ($p_o_field['native_type'] == 'LONGLONG') || ($p_o_field['native_type'] == 'LONG') || ($p_o_field['native_type'] == 'SHORT') || ($p_o_field['native_type'] == 'TINY') ) {
					$s_type = 'int';
				} else if ( ($p_o_field['native_type'] == 'DOUBLE') || ($p_o_field['native_type'] == 'DECIMAL') || ($p_o_field['native_type'] == 'NEWDECIMAL') ) {
					$s_type = 'real';
				} else if ( ($p_o_field['native_type'] == 'TIMESTAMP') || ($p_o_field['native_type'] == 'DATE') || ($p_o_field['native_type'] == 'DATETIME') || ($p_o_field['native_type'] == 'TIME') ) {
					$s_type = 'date';
				} else if ( ($p_o_field['native_type'] == 'VAR_STRING') || ($p_o_field['native_type'] == 'BLOB') ) {
					$s_type = 'string';
				}
			break;
			case forestBase::MSSQL:
				if ( ($p_o_field['sqlsrv:decl_type'] == 'bigint') || (\fPHP\Helper\forestStringLib::StartsWith($p_o_field['sqlsrv:decl_type'], 'int')) || ($p_o_field['sqlsrv:decl_type'] == 'smallint') ) {
					$s_type = 'int';
				} else if ( ($p_o_field['sqlsrv:decl_type'] == 'decimal') || ($p_o_field['sqlsrv:decl_type'] == 'float') ) {
					$s_type = 'real';
				} else if ( ($p_o_field['sqlsrv:decl_type'] == 'datetime') || ($p_o_field['sqlsrv:decl_type'] == 'date') || ($p_o_field['sqlsrv:decl_type'] == 'time') ) {
					$s_type = 'date';
				} else if ( ($p_o_field['sqlsrv:decl_type'] == 'text') || ($p_o_field['sqlsrv:decl_type'] == 'varchar') || ($p_o_field['sqlsrv:decl_type'] == 'nvarchar') ) {
					$s_type = 'string';
				}
			break;
			case forestBase::PGSQL:
				if ( (\fPHP\Helper\forestStringLib::StartsWith($p_o_field['native_type'], 'int')) ) {
					$s_type = 'int';
				} else if ( ($p_o_field['native_type'] == 'float4') || ($p_o_field['native_type'] == 'float8') || ($p_o_field['native_type'] == 'numeric') ) {
					$s_type = 'real';
				} else if ( ($p_o_field['native_type'] == 'timestamp') || ($p_o_field['native_type'] == 'date') || ($p_o_field['native_type'] == 'time') ) {
					$s_type = 'date';
				} else if ( ($p_o_field['native_type'] == 'varchar') || ($p_o_field['native_type'] == 'text') ) {
					$s_type = 'string';
				}
			break;
			case forestBase::SQLite3:
				if (array_key_exists('sqlite:decl_type', $p_o_field)) {
					if ( (\fPHP\Helper\forestStringLib::StartsWith($p_o_field['sqlite:decl_type'], 'int')) || (\fPHP\Helper\forestStringLib::StartsWith($p_o_field['sqlite:decl_type'], 'smallint')) || (\fPHP\Helper\forestStringLib::StartsWith($p_o_field['sqlite:decl_type'], 'bigint')) ) {
						$s_type = 'int';
					} else if ( ($p_o_field['sqlite:decl_type'] == 'double') || (\fPHP\Helper\forestStringLib::StartsWith($p_o_field['sqlite:decl_type'], 'decimal')) ) {
						$s_type = 'real';
					} else if ( ($p_o_field['sqlite:decl_type'] == 'datetime') || ($p_o_field['sqlite:decl_type'] == 'time') ) {
						$s_type = 'date';
					} else if ( ($p_o_field['sqlite:decl_type'] == 'text') || (\fPHP\Helper\forestStringLib::StartsWith($p_o_field['sqlite:decl_type'], 'varchar')) ) {
						$s_type = 'string';
					}
				} else {
					if ( ($p_o_field['native_type'] == 'integer') ) {
						$s_type = 'int';
					} else if ($p_o_field['native_type'] == 'double') {
						$s_type = 'real';
					} else if ($p_o_field['native_type'] == 'string') {
						$s_type = 'string';
					}
				}
			break;
			case forestBase::OCISQL:
				if ( ($p_o_field['native_type'] == 'NUMBER') || ($p_o_field['native_type'] == 'LONG') ) {
					$s_type = 'int';
					
					/* DECIMAL type NUMBER(38,2) */
					if (preg_match("/^\d*[.,]{1}\d*$/i", $p_s_value)) {
						$s_type = 'real';
						$p_s_value = str_replace(',', '.', $p_s_value);
					}
				} else if ($p_o_field['native_type'] == 'FLOAT') {
					$s_type = 'real';
					$p_s_value = str_replace(',', '.', $p_s_value);
				} else if ( (\fPHP\Helper\forestStringLib::StartsWith($p_o_field['native_type'], 'TIMESTAMP')) || (\fPHP\Helper\forestStringLib::StartsWith($p_o_field['native_type'], 'INTERVAL DAY')) ) {
					$s_type = 'date';
				} else if ( (\fPHP\Helper\forestStringLib::StartsWith($p_o_field['native_type'], 'VARCHAR')) || (\fPHP\Helper\forestStringLib::StartsWith($p_o_field['native_type'], 'NVARCHAR')) ) {
					$s_type = 'string';
				} else if ($p_o_field['native_type'] == 'CLOB') {
					if (!empty($p_s_value)) {
						$p_s_value = stream_get_contents($p_s_value, -1);
					}
				}
			break;
			case forestBase::MongoDB:
				$s_type = \fPHP\Base\forestSQLQuerytoMongoDBQuery::DetermineTypeByValue($p_s_value);
			break;
			default:
				throw new forestException('Invalid base-gateway [' . $this->BaseGateway->value . '].');
			break;
		}
		
		/* cast value of the column to expected type */
		if ($s_type == 'int') {
			$p_s_value = intval($p_s_value);
		} else if ($s_type == 'real') {
			$p_s_value = floatval($p_s_value);
		} else if ($s_type == 'date') {
			$p_s_value = \fPHP\Helper\forestStringLib::TextToDate($p_s_value);
		} else if ($s_type == 'string') {
			$p_s_value = strval($p_s_value);
			
			/* additionally utf8 en/decoding */
			if ( (!is_null($p_s_value)) && (!is_null($o_glob->Trunk)) ) {
				if ($o_glob->Trunk->OutContentUTF8Decode) {
					$p_s_value = mb_convert_encoding($p_s_value, 'ISO-8859-1', 'UTF-8');
				} else if ($o_glob->Trunk->OutContentUTF8Encode) {
					$p_s_value = mb_convert_encoding($p_s_value, 'UTF-8', 'ISO-8859-1');
				}
			}
			
			if ($this->BaseGateway->value == forestBase::MongoDB) {
				/* replace two single quotes, with " double quote */
				$p_s_value = str_replace('\'\'', '"', $p_s_value);
				$p_s_value = str_replace('<', '&lt;', $p_s_value);
				$p_s_value = str_replace('>', '&gt;', $p_s_value);
			} else {
				$p_s_value = htmlspecialchars($p_s_value, ( ENT_QUOTES | ENT_HTML5 ));
			}
		} else {
			/* additionally utf8 en/decoding */
			if ( (!is_null($p_s_value)) && (!is_null($o_glob->Trunk)) ) {
				if ($o_glob->Trunk->OutContentUTF8Decode) {
					$p_s_value = mb_convert_encoding($p_s_value, 'ISO-8859-1', 'UTF-8');
				} else if ($o_glob->Trunk->OutContentUTF8Encode) {
					$p_s_value = mb_convert_encoding($p_s_value, 'UTF-8', 'ISO-8859-1');
				}
			}
			
			if ($this->BaseGateway->value == forestBase::MongoDB) {
				/* replace two single quotes, with " double quote */
				$p_s_value = str_replace('\'\'', '"', $p_s_value);
				$p_s_value = str_replace('<', '&lt;', $p_s_value);
				$p_s_value = str_replace('>', '&gt;', $p_s_value);
			} else if (!is_null($p_s_value)) {
				$p_s_value = htmlspecialchars($p_s_value, ( ENT_QUOTES | ENT_HTML5 ));
			}
		}
	}
}
?>