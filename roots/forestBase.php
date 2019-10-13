<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.1.5 (0x1 00009)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * gathering class for all database interfaces an gateways who can be used by the script collection
 * whole database connectivity goes over this class
 * on initialization the developer can decide which gateway he wants to use
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.0 alpha	renatus		2019-08-04	first build
 * 0.1.1 alpha	renatus		2019-08-07	added date conversion and trunk settings
 */

class forestBase {
	use forestData;
	
	/* Fields */
	
	const MariaSQL = 'mariasql';
	
	const ASSOC = PDO::FETCH_ASSOC;
	const NUM = PDO::FETCH_NUM;
	const BOTH = PDO::FETCH_BOTH;
	const OBJ = PDO::FETCH_OBJ;
	const LAZY = PDO::FETCH_LAZY;
	
	private $AmountQueries;
	private $Queries;
	private $BaseGateway;
	private $CurrentConnection;
	private $Query;
	private $CurrentResult;
	
	/* Properties */
	
	/* Methods */
	
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
		
		/* chosse database gateway and create new PDO object with connection settings		 */
		try {
			switch ($this->BaseGateway->value) {
				case forestBase::MariaSQL:
					$this->CurrentConnection->value = new PDO('mysql:dbname=' . $p_s_datasource . ';host=' . $p_s_host, $p_s_temp_user, $p_s_temp_pw, array(PDO::ATTR_PERSISTENT => $p_b_persistentConnection));
				break;
				default:
					throw new forestException('Invalid base-gateway [' . $this->BaseGateway->value . '].');
				break;
			}
			
			/* set exception setting for PDO class */
			$this->CurrentConnection->value->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->CurrentConnection->value->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $o_pdoException) {
			throw new forestException('The connection to the database is not possible; <i>' . $o_pdoException->getCode() . ': ' . $o_pdoException->getMessage() . '</i>');
		}
		
		/* check connection */
		if (!$this->CurrentConnection->value) {
			throw new forestException('The connection to the database is not possible.');
		}
	}
	
	public function __destruct() {
		/* close connection if object is being destroyed */
		unset($this->CurrentConnection->value);
		
		if ($this->CurrentConnection->value != null) {
			throw new forestException('Could not close the database connection; <i>' . $this->CurrentConnection->value->errorCode() . ': ' . $this->CurrentConnection->value->errorInfo()[2] . '</i>');
		}
	}
	
	public function FetchQuery(forestSQLQuery $p_o_sqlQuery, $p_b_transaction = false, $p_b_resultTwigList = true, $p_b_freeResult = false, $p_i_resultType = forestBase::ASSOC) {
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
			
			$this->CurrentResult->value = $this->CurrentConnection->value->prepare($this->Query->value);
			$this->CurrentResult->value->execute();
		} catch (PDOException $o_pdoException) {
			/* if transaction flag is set, roll-back transaction */
			if ($p_b_transaction) {
				$this->CurrentConnection->value->rollBack();
			}
			
			throw new forestException('The query could not be executed; <i>' . $o_pdoException->getCode() . ': ' . $o_pdoException->getMessage() . '</i><br />' . $this->Query->value);
		}
		
		/* check if query got a result */
		if(!$this->CurrentResult->value) {
			/* if transaction flag is set, roll-back transaction */
			if ($p_b_transaction) {
				$this->CurrentConnection->value->rollBack();
			}
			
			throw new forestException('The query could not be executed.');
		}
		
		/* if transaction flag is set, commit transaction */
		if ($p_b_transaction) {
			$this->CurrentConnection->value->commit();
		}
		
		/* on SELECT query, prepare to return result rows */
		if ($p_o_sqlQuery->SqlType == forestSQLQuery::SELECT) {
			$a_fieldInformation = array();
			
			/* gather field information for conversion handling */
			foreach(range(0, $this->CurrentResult->value->columnCount() - 1) as $i_column_index)
			{
				$foo = $this->CurrentResult->value->getColumnMeta($i_column_index);
				
				if (empty($foo)) {
					throw new forestException('Could not get column metadata; <i>' . $this->CurrentConnection->value->errorCode() . ': ' . $this->CurrentConnection->value->errorInfo()[2] . '</i>');
				}
				
				$a_fieldInformation[] = $foo;
			}
			
			/*echo '<pre>';
			print_r($a_fieldInformation);
			echo '</pre>';*/
			
			$a_fetchData = array();
			
			/* fetch sql result into data-array */
			while ($o_fetchRow = $this->CurrentResult->value->fetch($p_i_resultType, PDO::FETCH_ORI_NEXT)) {
				if (!$o_fetchRow) {
					throw new forestException('Could not fetch row; <i>' . $this->CurrentConnection->value->errorCode() . ': ' . $this->CurrentConnection->value->errorInfo()[2] . '</i>');
				}
				
				/*echo '<pre>';
				print_r($o_fetchRow);
				echo '</pre>';*/
				
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
				$ol_twigs = new forestTwigList($p_o_sqlQuery->Table, $a_fetchData, $p_i_resultType);
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
	
	public function ManualTransaction() {
		/* check connection */
		if (!$this->CurrentConnection->value) {
			throw new forestException('No connection established.');
		}
		
		$this->CurrentConnection->value->beginTransaction();
		//d2c('beginTransaction');
		global $b_transaction_active;
		$b_transaction_active = true;
	}
	
	public function ManualRollBack() {
		/* check connection */
		if (!$this->CurrentConnection->value) {
			throw new forestException('No connection established.');
		}
		
		$this->CurrentConnection->value->rollBack();
		//d2c('rollBack');
		global $b_transaction_active;
		$b_transaction_active = false;
	}
	
	public function ManualCommit() {
		/* check connection */
		if (!$this->CurrentConnection->value) {
			throw new forestException('No connection established.');
		}
		
		$this->CurrentConnection->value->commit();
		//d2c('commit');
		global $b_transaction_active;
		$b_transaction_active = false;
	}
	
	public function LastInsertId() {
		$foo = $this->CurrentConnection->value->lastInsertId();
		
		if (!$foo) {
			throw new forestException('Could not get last insert id; <i>' . $this->CurrentConnection->value->errorCode() . ': ' . $this->CurrentConnection->value->errorInfo()[2] . '</i>');
		}
		
		return $foo;
	}
	
	/* iterate all result rows with their field-information for conversion */
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
	
	/* convert result field value with the help of the field information */
	private function SetTypeValue($p_o_field, &$p_s_value) {
		$o_glob = forestGlobals::init();
		
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
			$p_s_value = forestStringLib::TextToDate($p_s_value);
		} else if ($s_type == 'string') {
			$p_s_value = strval($p_s_value);
			
			/* additionally utf8 en/decoding */
			if (!is_null($o_glob->Trunk)) {
				if ($o_glob->Trunk->OutContentUTF8Decode) {
					$p_s_value = utf8_decode($p_s_value);
				} else if ($o_glob->Trunk->OutContentUTF8Encode) {
					$p_s_value = utf8_encode($p_s_value);
				}
			}
			
			$p_s_value = htmlspecialchars($p_s_value, ( ENT_QUOTES | ENT_HTML5 ));
		} else {
			/* additionally utf8 en/decoding */
			if (!is_null($o_glob->Trunk)) {
				if ($o_glob->Trunk->OutContentUTF8Decode) {
					$p_s_value = utf8_decode($p_s_value);
				} else if ($o_glob->Trunk->OutContentUTF8Encode) {
					$p_s_value = utf8_encode($p_s_value);
				}
			}
			
			$p_s_value = htmlspecialchars($p_s_value, ( ENT_QUOTES | ENT_HTML5 ));
		}
	}
}
?>