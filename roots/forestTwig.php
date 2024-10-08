<?php
/**
 * abstract class for all twig objects, one twig represents a table in a database
 * all necessary methods to read or manipulate data are implemented in this class
 * and can be used with every twig-object that stays for one table in the database
 *
 * this abstract class also can hold one record at a time
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestany.net>
 * @copyright   (c) 2024 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.1.0 stable
 * @link        https://forestany.net
 * @object-id   0x1 0000C
 * @since       File available since Release 0.1.0 alpha
 * @deprecated  -
 *
 * @version log Version			Developer	Date		Comment
 * 				0.1.0 alpha		renea		2019-08-04	first build
 * 				0.1.1 alpha		renea		2019-08-10	added tablefield caching
 * 				0.1.2 alpha		renea		2019-08-27	added sort and limit
 * 				0.1.5 alpha		renea		2019-10-04	added sub-records
 * 				0.1.5 alpha		renea		2019-10-06	added sub-constraint
 * 				0.1.5 alpha		renea		2019-10-08	added caching
 * 				0.1.5 alpha		renea		2019-10-09	added forestLooukp and forestCombination
 * 				0.4.0 beta		renea		2019-11-22	do not add system table flag protection if you are root user
 * 				0.7.0 beta		renea		2020-01-03	added identifier column as standard like id and uuid
 * 				0.7.0 beta		renea		2020-01-03	added FILEVERSION and FILENAME commands to forestCombination
 * 				0.9.0 beta		renea		2020-01-29	optimized ImplementFilter for search on filename
 * 				1.0.0 stable	renea		2020-02-13	added MongoDB support by breaking up SQL-Join Queries
 * 				1.0.1 stable	renea		2021-04-09	added support comparing forestDateTime object for check uniqueness functionality
 * 				1.1.0 stable	renea		2024-03-05	UpdateRecord - only if Id and Identifier are changed at the same time, it is allowed to change Id primary
 */

namespace fPHP\Twigs;

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

abstract class forestTwig {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	protected $fphp_Table;
	protected $fphp_TableUUID;
	protected $fphp_Primary;
	protected $fphp_Mapping;
	protected $fphp_HasUUID;
	protected $fphp_SystemTable;
	protected $fphp_Unique;
	protected $fphp_SortOrder;
	protected $fphp_Interval;
	protected $fphp_View;
	private $fphp_RecordImage;
	private $fphp_SubRecords;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestTwig class, creating a twig object representing a table with all its information and a table record
	 *
	 * @param array $p_a_record  raw dataset of a record as an array
	 * @param integer $p_i_resultType  standard ASSOC - ASSOC, NUM, BOTH, OBJ, LAZY
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct($p_a_record = array(), $p_i_resultType = \fPHP\Base\forestBase::ASSOC) {
		$this->fphp_Table = new forestString;
		$this->fphp_TableUUID = new forestString;
		$this->fphp_Primary = new forestArray;
		$this->fphp_Mapping = new forestArray;
		$this->fphp_HasUUID = new forestBool(false, false);
		$this->fphp_SystemTable = new forestBool(false, false);
		$this->fphp_Unique = new forestArray;
		$this->fphp_SortOrder = new forestObject(new forestObjectList('stdClass'), false);
		$this->fphp_Interval = new forestInt;
		$this->fphp_View = new forestArray;
		$this->fphp_RecordImage = new forestObject(new forestObjectList('stdClass'), false, false);
		$this->fphp_SubRecords = new forestObject(new forestObjectList('forestTwigList'), false, false);
		
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if (!is_array($p_a_record)) {
			throw new forestException('Parameter record is not an array');
		}
		
		$this->init();
		
		if (!issetStr($this->fphp_Table->value)) {
			throw new forestException('Twig needs a table value');
		}
		
		if ( (\fPHP\Helper\forestStringLib::StartsWith($this->fphp_Table->value, 'sys_fphp_')) && (!$o_glob->Security->RootUser) ) {
			$this->fphp_SystemTable->value = true;
		}
		
		if (count($this->fphp_Primary->value) == 0) {
			throw new forestException('Primary key is missing');
		}
		
		if (count($this->fphp_Mapping->value) == 0) {
			throw new forestException('Mapping values are missing');
		}
		
		foreach ($this->fphp_Primary->value as $s_primary_field) {
			if (!in_array($s_primary_field, $this->fphp_Mapping->value)) {
				throw new forestException('Primary[%0] does not exists in mapping', array($s_primary_field));
			}
		}
		
		foreach ($this->fphp_Unique->value as $s_unique_constraint) {
			/* it is possible that a unique constraint exists of multiple columns, separated by semicolon */
			if (strpos($s_unique_constraint, ';') !== false) {
				$a_columns = explode(';', $s_unique_constraint);
				
				for ($i = 0; $i < count($a_columns); $i++) {
					if (!in_array($a_columns[$i], $this->fphp_Mapping->value)) {
						throw new forestException('Unique constraint[%0] does not exists in mapping', array($a_columns[$i]));
					}
				}
			} else {
				if (!in_array($s_unique_constraint, $this->fphp_Mapping->value)) {
					throw new forestException('Unique constraint[%0] does not exists in mapping', array($s_unique_constraint));
				}
			}
		}
		
		if (array_key_exists($this->fphp_Table->value, $o_glob->Tables)) {
			$this->fphp_TableUUID->value = $o_glob->Tables[$this->fphp_Table->value];
		}
		
		if (!$o_glob->FastProcessing) {
			$this->CacheTableFieldsProperties();
		}
		
		foreach ($this->fphp_SortOrder->value as $s_sortOrder_field => $b_sortOrder_direction) {
			if ( (!in_array($s_sortOrder_field, $this->fphp_Mapping->value)) && (!$o_glob->FastProcessing) ) {
				if (!$o_glob->TablefieldsDictionary->Exists($this->fphp_Table->value . '_' . $s_sortOrder_field)) {
					throw new forestException('SortOrder field[%0] does not exists in mapping', array($s_sortOrder_field));
				}
			}
		}
		
		/* this check is deprecated, but maybe necessary for future changes */
		/*if (count($this->fphp_View->value) == 0) {
			throw new forestException('View values are missing table[%0]', array($this->fphp_Table->value));
		}*/
		
		foreach ($this->fphp_View->value as $s_view_field) {
			if ( (!in_array($s_view_field, $this->fphp_Mapping->value)) && (!$o_glob->FastProcessing) ) {
				if (!$o_glob->TablefieldsDictionary->Exists($this->fphp_Table->value . '_' . $s_view_field)) {
					throw new forestException('View field[%0] does not exists in mapping', array($s_view_field));
				}
			}
		}
		
		/* load record data into class fields */
		/* difference between numeric or named index of record data */
		switch ($p_i_resultType) {
			case \fPHP\Base\forestBase::ASSOC:
			case \fPHP\Base\forestBase::BOTH:
				foreach($p_a_record as $s_key => $s_value) {
					if (!in_array($s_key, $this->fphp_Mapping->value)) {
						/* throw new forestException('Field[%0] does not exists in mapping', array($s_key, implode(',', $this->fphp_Mapping->value))); */
						continue;
					}
					
					if (is_string($s_value)) {
						if (strlen($s_value) > 0) {
							$this->{$s_key} = $s_value;
							$this->fphp_RecordImage->value->Add($s_value, $s_key);
						} else {
							$this->fphp_RecordImage->value->Add($this->{$s_key}, $s_key);
						}
					} else if (!empty($s_value)) {
						$this->{$s_key} = $s_value;
						$this->fphp_RecordImage->value->Add($s_value, $s_key);
					} else {
						$this->fphp_RecordImage->value->Add($this->{$s_key}, $s_key);
					}
				}
			break;
			case \fPHP\Base\forestBase::NUM:
				if (count($p_a_record) != count($this->fphp_Mapping->value)) {
					throw new forestException('Record fields and mapping fields are not of the same amount');
				}
				
				/* use mapping array for field names to call field property methods of the twig, although there is only the numeric index */
				foreach($p_a_record as $s_key => $s_value) {
					$s_key = $this->fphp_Mapping->value[$s_key];
					
					if (is_string($s_value)) {
						if (strlen($s_value) > 0) {
							$this->{$s_key} = $s_value;
							$this->fphp_RecordImage->value->Add($s_value, $s_key);
						} else {
							$this->fphp_RecordImage->value->Add($this->{$s_key}, $s_key);
						}
					} else if (!empty($s_value)) {
						$this->{$s_key} = $s_value;
						$this->fphp_RecordImage->value->Add($s_value, $s_key);
					} else {
						$this->fphp_RecordImage->value->Add($this->{$s_key}, $s_key);
					}
				}
			break;
			default:
				throw new forestException('Result type[%0] not implemented', array($p_i_resultType));
			break;
		}
	}
	
	/* every class which inherits forestTwig must implement init-function */
	abstract protected function init();
	
	/**
	 * cache table fields properties entries in dictionary in forestGlobals
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function CacheTableFieldsProperties() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if (in_array($this->fphp_TableUUID->value, $o_glob->TablesWithTablefieldsCached)) {
			return;
		} else {
			/*foreach ($o_glob->Tables as $key => $table) {
				if ($this->fphp_TableUUID->value == $table) {
					d2c($key);
					d2c((in_array($this->fphp_TableUUID->value, $o_glob->TablesWithTablefields)));
				}
			}*/
			
			$a_foo = $o_glob->TablesWithTablefieldsCached;
			$a_foo[] = $this->fphp_TableUUID->value;
			$o_glob->TablesWithTablefieldsCached = $a_foo;
		}
		
		if (in_array($this->fphp_TableUUID->value, $o_glob->TablesWithTablefields)) {
			/* look for tablefields of current twig */
			$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
			$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $this->fphp_TableUUID->value, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->BackupTemp();
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			$o_glob->RestoreTemp();
			
			if ($o_tablefields->Twigs->Count() > 0) {
				foreach ($o_tablefields->Twigs as $o_tablefield) {
					/* add tablefield information to global dictionary */
					if (!$o_glob->TablefieldsDictionary->Exists($this->fphp_Table->value . '_' . $o_tablefield->FieldName)) {
						$o_result = forestTwig::QueryFieldProperties($this->fphp_TableUUID->value, $o_tablefield->FieldName);
						
						if ($o_result != null) {
							/*echo '<pre>';
							print_r($o_result);
							echo '</pre>';*/
							
							$o_tableFieldProperties = new \fPHP\Twigs\forestTableFieldProperties(
								$o_result['TableFieldUUID'],
								$this->fphp_TableUUID->value,
								$o_result['FieldName'],
								$o_result['TableFieldTabId'],
								$o_result['TableFieldJSONEncodedSettings'],
								$o_result['TableFieldFooterElement'],
								$o_result['TableFieldSubRecordField'],
								$o_result['TableFieldOrder'],
								$o_result['FormElementUUID'],
								$o_result['FormElementName'],
								$o_result['FormElementJSONEncodedSettings'],
								$o_result['SqlTypeUUID'],
								$o_result['SqlTypeName'],
								$o_result['ForestDataUUID'],
								$o_result['ForestDataName']
							);
							
							$o_glob->TablefieldsDictionary->Add($o_tableFieldProperties, $this->fphp_Table->value . '_' . $o_tablefield->FieldName);
						}
					}
				}
			}
			
			/* get tablefields of subconstraints */
			if (array_key_exists($this->fphp_TableUUID->value, $o_glob->SubConstraintsDictionary)) {
				foreach ($o_glob->SubConstraintsDictionary[$this->fphp_TableUUID->value] as $o_subconstraint) {
					if (in_array($o_subconstraint->UUID, $o_glob->TablesWithTablefields)) {
						/* look for tablefields of subconstraint */
						$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
						$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $o_subconstraint->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
						$o_glob->BackupTemp();
						$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
						$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
						$o_glob->Temp->Del('SQLAdditionalFilter');
						$o_glob->RestoreTemp();
						
						if ($o_tablefields->Twigs->Count() > 0) {
							$s_joinTable = array_search($o_subconstraint->SubTableUUID->PrimaryValue, $o_glob->Tables);
							
							foreach ($o_tablefields->Twigs as $o_tablefield) {
								/* add tablefield information to global dictionary */
								if (!$o_glob->TablefieldsDictionary->Exists($this->fphp_Table->value . '_' . $s_joinTable . '_' . $o_tablefield->FieldName)) {
									$o_result = forestTwig::QueryFieldProperties($o_subconstraint->UUID, $o_tablefield->FieldName);
									
									if ($o_result != null) {
										/*echo '<pre>';
										print_r($o_result);
										echo '</pre>';*/
										
										$o_tableFieldProperties = new \fPHP\Twigs\forestTableFieldProperties(
											$o_result['TableFieldUUID'],
											$o_subconstraint->UUID,
											$o_result['FieldName'],
											$o_result['TableFieldTabId'],
											$o_result['TableFieldJSONEncodedSettings'],
											$o_result['TableFieldFooterElement'],
											$o_result['TableFieldSubRecordField'],
											$o_result['TableFieldOrder'],
											$o_result['FormElementUUID'],
											$o_result['FormElementName'],
											$o_result['FormElementJSONEncodedSettings'],
											$o_result['SqlTypeUUID'],
											$o_result['SqlTypeName'],
											$o_result['ForestDataUUID'],
											$o_result['ForestDataName']
										);
										
										$o_glob->TablefieldsDictionary->Add($o_tableFieldProperties, $this->fphp_Table->value . '_' . $s_joinTable . '_' . $o_tablefield->FieldName);
									}
								}
							}
						}
					}
				}
			}
			
			/* iterate mapping for fields which may not be in table fields table */
			foreach ($this->fphp_Mapping->value as $s_field) {
				if ( ($s_field != 'Id') && ($s_field != 'UUID') ) {
					/* add tablefield information to global dictionary */
					if (!$o_glob->TablefieldsDictionary->Exists($this->fphp_Table->value . '_' . $s_field)) {
						$o_result = $this->QueryFieldProperties($this->fphp_TableUUID->value, $s_field);
						
						if ($o_result != null) {
							/*echo '<pre>';
							print_r($o_result);
							echo '</pre>';*/
							
							$o_tableFieldProperties = new \fPHP\Twigs\forestTableFieldProperties(
								$o_result['TableFieldUUID'],
								$this->fphp_TableUUID->value,
								$o_result['FieldName'],
								$o_result['TableFieldTabId'],
								$o_result['TableFieldJSONEncodedSettings'],
								$o_result['TableFieldFooterElement'],
								$o_result['TableFieldSubRecordField'],
								$o_result['TableFieldOrder'],
								$o_result['FormElementUUID'],
								$o_result['FormElementName'],
								$o_result['FormElementJSONEncodedSettings'],
								$o_result['SqlTypeUUID'],
								$o_result['SqlTypeName'],
								$o_result['ForestDataUUID'],
								$o_result['ForestDataName']
							);
							
							$o_glob->TablefieldsDictionary->Add($o_tableFieldProperties, $this->fphp_Table->value . '_' . $s_field);
						}
					}
				}
			}
		}
	}
	
	/**
	 * execute sql query to get table field properties
	 *
	 * @param string $p_s_tableUUID  table uuid
	 * @param string $p_s_field  field name
	 *
	 * @return array  raw record data or null if no properties could be found
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static yes
	 */
	public static function QueryFieldProperties($p_s_tableUUID, $p_s_field) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == \fPHP\Base\forestBase::MongoDB) {
			$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, 'sys_fphp_tablefield');
					
				$column_A = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_A->Column = 'FieldName';
				
				$column_B = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_B->Column = 'UUID';
				
				$column_C = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_C->Column = 'TabId';
					
				$column_D = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_D->Column = 'JSONEncodedSettings';
				
				$column_E = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_E->Column = 'FooterElement';
				
				$column_T = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_T->Column = 'SubRecordField';
					
				$column_U = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_U->Column = 'Order';
				
				$column_F = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_F->Column = 'FormElementUUID';
				
				$column_I = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_I->Column = 'SqlTypeUUID';
					$column_I->Name = 'SqlTypeUUID';
				
				$column_K = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_K->Column = 'ForestDataUUID';
					$column_K->Name = 'ForestDataUUID';
				
			$o_querySelect->Query->Columns->Add($column_A);
			$o_querySelect->Query->Columns->Add($column_B);
			$o_querySelect->Query->Columns->Add($column_C);
			$o_querySelect->Query->Columns->Add($column_D);
			$o_querySelect->Query->Columns->Add($column_E);
			$o_querySelect->Query->Columns->Add($column_T);
			$o_querySelect->Query->Columns->Add($column_U);
			$o_querySelect->Query->Columns->Add($column_F);
			$o_querySelect->Query->Columns->Add($column_I);
			$o_querySelect->Query->Columns->Add($column_K);
			
			$column_S = new \fPHP\Base\forestSQLColumn($o_querySelect);
				$column_S->Column = 'TableUUID';
			
			/* filter by table-uuid and field-name */
			$where_A = new \fPHP\Base\forestSQLWhere($o_querySelect);
				$where_A->Column = $column_S;
				$where_A->Value = $where_A->ParseValue($p_s_tableUUID);
				$where_A->Operator = '=';
				
			$where_B = new \fPHP\Base\forestSQLWhere($o_querySelect);
				$where_B->Column = $column_A;
				$where_B->Value = $where_A->ParseValue($p_s_field);
				$where_B->Operator = '=';
				$where_B->FilterOperator = 'AND';
			
			$o_querySelect->Query->Where->Add($where_A);
			$o_querySelect->Query->Where->Add($where_B);
			
			$o_resultTablefield = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect, false, false);
			
			/* we only expect and accept one record as result; any other result is invalid */
			if (count($o_resultTablefield) != 1) {
				return null;
			}
			
			/*echo '<pre>';
			print_r($o_resultTablefield[0]);
			echo '</pre>';*/
			
			$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, 'sys_fphp_formelement');
					
				$column_A = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_A->Column = 'Name';
				
				$column_B = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_B->Column = 'JSONEncodedSettings';
			
			$o_querySelect->Query->Columns->Add($column_A);
			$o_querySelect->Query->Columns->Add($column_B);
				
			$column_C = new \fPHP\Base\forestSQLColumn($o_querySelect);
				$column_C->Column = 'UUID';
			
			/* filter by table-uuid and field-name */
			$where_A = new \fPHP\Base\forestSQLWhere($o_querySelect);
				$where_A->Column = $column_C;
				$where_A->Value = $where_A->ParseValue($o_resultTablefield[0]['FormElementUUID']);
				$where_A->Operator = '=';
			
			$o_querySelect->Query->Where->Add($where_A);
			
			$o_resultFormElement = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect, false, false);
			
			/* we only expect and accept one record as result; any other result is invalid */
			if (count($o_resultFormElement) != 1) {
				return null;
			}
			
			/*echo '<pre>';
			print_r($o_resultFormElement[0]);
			echo '</pre>';*/
			
			$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, 'sys_fphp_sqltype');
					
				$column_A = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_A->Column = 'Name';
			
			$o_querySelect->Query->Columns->Add($column_A);
				
			$column_B = new \fPHP\Base\forestSQLColumn($o_querySelect);
				$column_B->Column = 'UUID';
			
			/* filter by table-uuid and field-name */
			$where_A = new \fPHP\Base\forestSQLWhere($o_querySelect);
				$where_A->Column = $column_B;
				$where_A->Value = $where_A->ParseValue($o_resultTablefield[0]['SqlTypeUUID']);
				$where_A->Operator = '=';
				
			$o_querySelect->Query->Where->Add($where_A);
			
			$o_resultSqlType = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect, false, false);
			
			/* we only expect and accept one record as result; any other result is invalid */
			if (count($o_resultSqlType) != 1) {
				return null;
			}
			
			/*echo '<pre>';
			print_r($o_resultSqlType[0]);
			echo '</pre>';*/
			
			$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, 'sys_fphp_forestdata');
					
				$column_A = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_A->Column = 'Name';
			
			$o_querySelect->Query->Columns->Add($column_A);
				
			$column_B = new \fPHP\Base\forestSQLColumn($o_querySelect);
				$column_B->Column = 'UUID';
			
			/* filter by table-uuid and field-name */
			$where_A = new \fPHP\Base\forestSQLWhere($o_querySelect);
				$where_A->Column = $column_B;
				$where_A->Value = $where_A->ParseValue($o_resultTablefield[0]['ForestDataUUID']);
				$where_A->Operator = '=';
			
			$o_querySelect->Query->Where->Add($where_A);
			
			$o_resultForestData = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect, false, false);
			
			/* we only expect and accept one record as result; any other result is invalid */
			if (count($o_resultForestData) != 1) {
				return null;
			}
			
			/*echo '<pre>';
			print_r($o_resultForestData[0]);
			echo '</pre>';*/
			
			$o_result = array();
			
			$o_result['TableFieldUUID'] = $o_resultTablefield[0]['UUID'];
			$o_result['FieldName'] = $o_resultTablefield[0]['UUID'];
			$o_result['TableFieldTabId'] = $o_resultTablefield[0]['TabId'];
			$o_result['TableFieldJSONEncodedSettings'] = $o_resultTablefield[0]['JSONEncodedSettings'];
			$o_result['TableFieldFooterElement'] = $o_resultTablefield[0]['FooterElement'];
			$o_result['TableFieldSubRecordField'] = $o_resultTablefield[0]['SubRecordField'];
			$o_result['TableFieldOrder'] = $o_resultTablefield[0]['Order'];
			$o_result['FormElementUUID'] = $o_resultTablefield[0]['FormElementUUID'];
			$o_result['FormElementName'] = $o_resultFormElement[0]['Name'];
			$o_result['FormElementJSONEncodedSettings'] = $o_resultFormElement[0]['JSONEncodedSettings'];
			$o_result['SqlTypeUUID'] = $o_resultTablefield[0]['SqlTypeUUID'];
			$o_result['SqlTypeName'] = $o_resultSqlType[0]['Name'];
			$o_result['ForestDataUUID'] = $o_resultTablefield[0]['ForestDataUUID'];
			$o_result['ForestDataName'] = $o_resultForestData[0]['Name'];
			
			return $o_result;
		} else {
			$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, 'sys_fphp_tablefield');
					
				$column_A = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_A->Column = 'FieldName';
				
				$column_B = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_B->Column = 'UUID';
					$column_B->Name = 'TableFieldUUID';
				
				$column_C = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_C->Column = 'TabId';
					$column_C->Name = 'TableFieldTabId';
					
				$column_D = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_D->Column = 'JSONEncodedSettings';
					$column_D->Name = 'TableFieldJSONEncodedSettings';
				
				$column_E = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_E->Column = 'FooterElement';
					$column_E->Name = 'TableFieldFooterElement';
				
				$column_T = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_T->Column = 'SubRecordField';
					$column_T->Name = 'TableFieldSubRecordField';
					
				$column_U = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_U->Column = 'Order';
					$column_U->Name = 'TableFieldOrder';
				
				$column_F = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_F->Column = 'FormElementUUID';
					$column_F->Name = 'FormElementUUID';
				
				$column_G = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_G->Table = 'sys_fphp_formelement';
					$column_G->Column = 'Name';
					$column_G->Name = 'FormElementName';
				
				$column_H = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_H->Table = 'sys_fphp_formelement';
					$column_H->Column = 'JSONEncodedSettings';
					$column_H->Name = 'FormElementJSONEncodedSettings';
					
				$column_I = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_I->Column = 'SqlTypeUUID';
					$column_I->Name = 'SqlTypeUUID';
				
				$column_J = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_J->Table = 'sys_fphp_sqltype';
					$column_J->Column = 'Name';
					$column_J->Name = 'SqlTypeName';
				
				$column_K = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_K->Column = 'ForestDataUUID';
					$column_K->Name = 'ForestDataUUID';
				
				$column_L = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_L->Table = 'sys_fphp_forestdata';
					$column_L->Column = 'Name';
					$column_L->Name = 'ForestDataName';
				
			$o_querySelect->Query->Columns->Add($column_A);
			$o_querySelect->Query->Columns->Add($column_B);
			$o_querySelect->Query->Columns->Add($column_C);
			$o_querySelect->Query->Columns->Add($column_D);
			$o_querySelect->Query->Columns->Add($column_E);
			$o_querySelect->Query->Columns->Add($column_T);
			$o_querySelect->Query->Columns->Add($column_U);
			$o_querySelect->Query->Columns->Add($column_F);
			$o_querySelect->Query->Columns->Add($column_G);
			$o_querySelect->Query->Columns->Add($column_H);
			$o_querySelect->Query->Columns->Add($column_I);
			$o_querySelect->Query->Columns->Add($column_J);
			$o_querySelect->Query->Columns->Add($column_K);
			$o_querySelect->Query->Columns->Add($column_L);
			/* join with form element table */
			$join_A = new \fPHP\Base\forestSQLJoin($o_querySelect);
			$join_A->JoinType = 'INNER JOIN';
			$join_A->Table = 'sys_fphp_formelement';
				
				$relation_A = new \fPHP\Base\forestSQLRelation($o_querySelect);
				
					$column_M = new \fPHP\Base\forestSQLColumn($o_querySelect);
						$column_M->Column = 'FormElementUUID';
					
					$column_N = new \fPHP\Base\forestSQLColumn($o_querySelect);
						$column_N->Table = $join_A->Table;
						$column_N->Column = 'UUID';
				
				$relation_A->ColumnLeft = $column_M;
				$relation_A->ColumnRight = $column_N;
				$relation_A->Operator = '=';
			
			$join_A->Relations->Add($relation_A);
			/* left join with sqltype table */
			$join_B = new \fPHP\Base\forestSQLJoin($o_querySelect);
			$join_B->JoinType = 'LEFT OUTER JOIN';
			$join_B->Table = 'sys_fphp_sqltype';
			
				$relation_B = new \fPHP\Base\forestSQLRelation($o_querySelect);
				
				$column_O = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_O->Column = 'SqlTypeUUID';
					
				$column_P = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_P->Table = $join_B->Table;
					$column_P->Column = 'UUID';
				
				$relation_B->ColumnLeft = $column_O;
				$relation_B->ColumnRight = $column_P;
				$relation_B->Operator = '=';
			
			$join_B->Relations->Add($relation_B);
			/* left join with forestdata table */
			$join_C = new \fPHP\Base\forestSQLJoin($o_querySelect);
			$join_C->JoinType = 'LEFT OUTER JOIN';
			$join_C->Table = 'sys_fphp_forestdata';
			
				$relation_C = new \fPHP\Base\forestSQLRelation($o_querySelect);
				
				$column_Q = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_Q->Column = 'ForestDataUUID';
				
				$column_R = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column_R->Table = $join_C->Table;
					$column_R->Column = 'UUID';
				
				$relation_C->ColumnLeft = $column_Q;
				$relation_C->ColumnRight = $column_R;
				$relation_C->Operator = '=';
			
			$join_C->Relations->Add($relation_C);
			
			$o_querySelect->Query->Joins->Add($join_A);
			$o_querySelect->Query->Joins->Add($join_B);
			$o_querySelect->Query->Joins->Add($join_C);
			
			$column_S = new \fPHP\Base\forestSQLColumn($o_querySelect);
				$column_S->Column = 'TableUUID';
			
			/* filter by table-uuid and field-name */
			$where_A = new \fPHP\Base\forestSQLWhere($o_querySelect);
				$where_A->Column = $column_S;
				$where_A->Value = $where_A->ParseValue($p_s_tableUUID);
				$where_A->Operator = '=';
				
			$where_B = new \fPHP\Base\forestSQLWhere($o_querySelect);
				$where_B->Column = $column_A;
				$where_B->Value = $where_A->ParseValue($p_s_field);
				$where_B->Operator = '=';
				$where_B->FilterOperator = 'AND';
			
			$o_querySelect->Query->Where->Add($where_A);
			$o_querySelect->Query->Where->Add($where_B);
			
			$o_result = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect, false, false);
			
			/* we only expect and accept one record as result; any other result is invalid */
			if (count($o_result) != 1) {
				return null;
			}
			
			return $o_result[0];
		}
	}
	
	/**
	 * query sub records of a sub constraint
	 *
	 * @param subconstraintTwig $p_o_subconstraint  sub constraint record
	 * @param bool $p_b_overwrite  flag to overwrite information in global cache, standard false
	 *
	 * @return forestTwigList  record list of sub records
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function QuerySubRecords(\fPHP\Twigs\subconstraintTwig $p_o_subconstraint, $p_b_overwrite = false) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if (!$p_b_overwrite) {
			/* look for subrecord twig list with sub constraint uuid and return it */
			if ($this->fphp_SubRecords->value->Exists($p_o_subconstraint->UUID)) {
				return $this->fphp_SubRecords->value->{$p_o_subconstraint->UUID};
			}
		}
		
		if ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == \fPHP\Base\forestBase::MongoDB) {
			$o_subrecordsTwig = new \fPHP\Twigs\subrecordsTwig;
			$s_joinTable = array_search($p_o_subconstraint->SubTableUUID->PrimaryValue, $o_glob->Tables);
			
			$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, $o_subrecordsTwig->fphp_Table->value);
			
			/* add all subrecords table columns */
			foreach($o_subrecordsTwig->fphp_Mapping->value as $s_column) {
				$o_column = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$o_column->Column = $s_column;
				
				$o_querySelect->Query->Columns->Add($o_column);
			}
			
			$o_head_uuid = new \fPHP\Base\forestSQLColumn($o_querySelect);
				$o_head_uuid->Column = 'HeadUUID';
			
			$where_A = new \fPHP\Base\forestSQLWhere($o_querySelect);
				$where_A->Column = $o_head_uuid;
				$where_A->Value = $where_A->ParseValue($this->UUID);
				$where_A->Operator = '=';
				$where_A->FilterOperator = 'AND';
			
			$o_querySelect->Query->Where->Add($where_A);
			
			$o_resultSubRecords = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect, false, false);
			
			$s_tempTable = $s_joinTable;
			\fPHP\Helper\forestStringLib::RemoveTablePrefix($s_tempTable);
			$s_foo = '\\fPHP\\Twigs\\' . $s_tempTable . 'Twig';
			$o_tempTwig = new $s_foo;
			
			foreach ($o_resultSubRecords as $o_resultSubRecord) {
				$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, $o_tempTwig->fphp_Table->value);
			
				/* add all subrecords table columns */
				foreach($o_tempTwig->fphp_Mapping->value as $s_column) {
					if ( ($s_column != 'Id') && ($s_column != 'UUID') ) {
						$o_column = new \fPHP\Base\forestSQLColumn($o_querySelect);
							$o_column->Column = $s_column;
						
						$o_querySelect->Query->Columns->Add($o_column);
						
						$o_resultSubRecord[$s_tempTable . '$' . $s_column] = null;
					}
				}
				
				$o_uuid = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$o_uuid->Column = 'UUID';
				
				$where_A = new \fPHP\Base\forestSQLWhere($o_querySelect);
					$where_A->Column = $o_uuid;
					$where_A->Value = $where_A->ParseValue($o_resultSubRecord['JoinUUID']);
					$where_A->Operator = '=';
					$where_A->FilterOperator = 'AND';
				
				$o_querySelect->Query->Where->Add($where_A);
				
				$o_resultJoinRecord = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect, false, false);
				
				if (count($o_resultJoinRecord) == 1) {
					foreach ($o_resultJoinRecord[0] as $s_joinRecordColumn => $o_joinRecordColumnValue) {
						$o_resultSubRecord[$s_tempTable . '$' . $s_joinRecordColumn] = $o_joinRecordColumnValue;
					}
				}
			}
			
			$o_subRecords = new \fPHP\Twigs\forestTwigList($o_subrecordsTwig->fphp_Table->value, $o_resultSubRecords, \fPHP\Base\forestBase::ASSOC);
		} else {
			/* get all subrecords, based on twig uuid - inner join with joinuuid on subtable */
			$o_subrecordsTwig = new \fPHP\Twigs\subrecordsTwig;
			$s_joinTable = array_search($p_o_subconstraint->SubTableUUID->PrimaryValue, $o_glob->Tables);
			$a_sqlAdditionalFilter = array(array('column' => 'HeadUUID', 'value' => $this->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->BackupTemp();
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			
			$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, $o_subrecordsTwig->fphp_Table->value);
			
			/* add join with sub constraint table */
			$join_A = new \fPHP\Base\forestSQLJoin($o_querySelect);
			$join_A->JoinType = 'INNER JOIN';
			$join_A->Table = $s_joinTable;
				
				$relation_A = new \fPHP\Base\forestSQLRelation($o_querySelect);
				
					$column_A = new \fPHP\Base\forestSQLColumn($o_querySelect);
						$column_A->Column = 'JoinUUID';
					
					$column_B = new \fPHP\Base\forestSQLColumn($o_querySelect);
						$column_B->Table = $join_A->Table;
						$column_B->Column = 'UUID';
				
				$relation_A->ColumnLeft = $column_A;
				$relation_A->ColumnRight = $column_B;
				$relation_A->Operator = '=';
			
			$join_A->Relations->Add($relation_A);
			
			$o_glob->Temp->Add($join_A, 'SQLAdditionalJoin');
			
			$s_tempTable = $s_joinTable;
			\fPHP\Helper\forestStringLib::RemoveTablePrefix($s_tempTable);
			$s_foo = '\\fPHP\\Twigs\\' . $s_tempTable . 'Twig';
			$o_tempTwig = new $s_foo;
			$a_additionalColumns = array();
			
			/* add all subrecords table columns */
			foreach($o_subrecordsTwig->fphp_Mapping->value as $s_column) {
				$column = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$column->Column = $s_column;
				
				$a_additionalColumns[] = $column;
			}
			
			/* add columns of sub constraint table */
			foreach($o_tempTwig->fphp_Mapping->value as $s_column) {
				if ( ($s_column != 'Id') && ($s_column != 'UUID') ) {
					$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, $o_tempTwig->fphp_Table->value);
					
					$column = new \fPHP\Base\forestSQLColumn($o_querySelect);
						$column->Column = $s_column;
						$column->Name = $s_tempTable . '$' . $s_column;
					
					$a_additionalColumns[] = $column;
				}
			}
			
			$o_glob->Temp->Add($a_additionalColumns, 'SQLGetAllAdditionalColumns');
			
			/* execute query */
			$o_subRecords = $o_subrecordsTwig->GetAllRecords(true);
			
			$o_glob->Temp->Del('SQLGetAllAdditionalColumns');
			$o_glob->Temp->Del('SQLAdditionalJoin');
			$o_glob->Temp->Del('SQLAdditionalFilter');
			$o_glob->RestoreTemp();
		}
		
		/* save result into sub records field of twig class */
		$this->fphp_SubRecords->value->Add($o_subRecords, $p_o_subconstraint->UUID);
		
		return $o_subRecords;
	}
	
	/**
	 * fill Mapping array from forestTwig instance
	 *
	 * @param array $p_a_object_vars  list of class variables
	 *
	 * @return null
	 *
	 * @access protected
	 * @static no
	 */
	protected function fphp_FillMapping(array $p_a_object_vars) {
		foreach ($p_a_object_vars as $s_key => $s_value) {
			/* do not add fphp system fields of forestTwig class */
			if (\fPHP\Helper\forestStringLib::StartsWith($s_key, 'fphp_')) {
				continue;
			}
			
			/* set UUID flag */
			if ($s_key == 'UUID') {
				$this->fphp_HasUUID->value = true;
			}
			
			/* adding all twig fields to mapping array */
			$this->fphp_Mapping->value[] = $s_key;
		}
	}
	
	
	/**
	 * general method to get property field value, for sub records as well as for combination fields
	 *
	 * @param string $p_s_name  sql column/field name
	 *
	 * @return object  value behind sql column/field name of holded record
	 *
	 * @access public
	 * @static no
	 */
	public function GetFieldValue($p_s_name) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$o_value = null;
		
		foreach ($o_glob->TablefieldsDictionary as $o_tableFieldProperties) {
			/* load json settings to compare name parameter with id setting */
			$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_tableFieldProperties->JSONEncodedSettings);
			$a_settings = json_decode($s_JSONEncodedSettings, true);
			
			if (!empty($a_settings)) {
				if (array_key_exists('Id', $a_settings)) {
					if ($a_settings['Id'] == $this->fphp_Table->value . '_' . $p_s_name) {
						/* check if field is of type forestCombination */
						if ($o_tableFieldProperties->ForestDataName == 'forestCombination') {
							/* if it is of type forestCombination, we load stored settings to calculate value for that field */
							if (array_key_exists('forestCombination', $a_settings)) {
								$o_value = $this->CalculateCombination($a_settings['forestCombination']);
								break;
							}
						} else if (issetStr($o_tableFieldProperties->SubRecordField)) {
							$o_value = $this->{$o_tableFieldProperties->SubRecordField};
							break;
						} else {
							$o_value = $this->{$o_tableFieldProperties->FieldName};
							break;
						}
					}
				}
			}
		}
		
		return $o_value;
	}
	
	/**
	 * general method to set property field value, for sub records as well
	 *
	 * @param string $p_s_name  sql column/field name
	 * @param object $p_o_value  object value for sql column/field
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function SetFieldValue($p_s_name, $p_o_value) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		foreach ($o_glob->TablefieldsDictionary as $o_tableFieldProperties) {
			/* load json settings to compare name parameter with id setting */
			$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_tableFieldProperties->JSONEncodedSettings);
			$a_settings = json_decode($s_JSONEncodedSettings, true);
			
			if (!empty($a_settings)) {
				if (array_key_exists('Id', $a_settings)) {
					if ($a_settings['Id'] == $this->fphp_Table->value . '_' . $p_s_name) {
						if (issetStr($o_tableFieldProperties->SubRecordField)) {
							$this->{$o_tableFieldProperties->SubRecordField} = $p_o_value;
						} else {
							$this->{$o_tableFieldProperties->FieldName} = $p_o_value;
						}
					}
				}
			}
		}
	}
	
	/**
	 * if twig object is not empty, field value will be restored using record image
	 *
	 * @param string $p_s_name  sql column/field name
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function RestoreFieldValue($p_s_name) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if (!$this->IsEmpty()) {
			foreach ($o_glob->TablefieldsDictionary as $o_tableFieldProperties) {
				/* load json settings to compare name parameter with id setting */
				$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_tableFieldProperties->JSONEncodedSettings);
				$a_settings = json_decode($s_JSONEncodedSettings, true);
				
				if (!empty($a_settings)) {
					if (array_key_exists('Id', $a_settings)) {
						if ($a_settings['Id'] == $this->fphp_Table->value . '_' . $p_s_name) {
							if (issetStr($o_tableFieldProperties->SubRecordField)) {
								$this->{$o_tableFieldProperties->SubRecordField} = $this->fphp_RecordImage->value->{$o_tableFieldProperties->SubRecordField};
							} else {
								$this->{$o_tableFieldProperties->FieldName} = $this->fphp_RecordImage->value->{$o_tableFieldProperties->FieldName};
							}
						}
					}
				}
			}
		}
	}
	
	/**
	 * calculate value behind forestCombination field
	 *
	 * @param string $p_s_forestCombination  forestCombination command
	 *
	 * @return object  value behind forestCombination field, usually a string value
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function CalculateCombination($p_s_forestCombination) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$o_result = '';
		
		/* get logical operators like '+','-','*','/' and '.' */
		preg_match_all('(\+|\-|\*|\/|\.)', $p_s_forestCombination, $a_outputOperators, PREG_PATTERN_ORDER);
		$a_operators = $a_outputOperators[0];
		
		/* get values */
		$a_values = preg_split('(\+|\-|\*|\/|\.)', $p_s_forestCombination);

		/* we always need amount of operators + 1 of amount of values, instead invalid combination result */
		if (!((count($a_operators) + 1) != count($a_values))) {
			$i_amount = count($a_values);
			$a_combinations = array();

			/* merge operators and values in one array */
			for ($i = 0; $i < $i_amount; $i++) {
				if ($i < $i_amount - 1) { 
					array_push($a_combinations,$a_values[$i],$a_operators[$i]);
				} else {
					array_push($a_combinations,$a_values[$i]);
				}
			}
			
			$s_operation = null;
			
			foreach ($a_combinations as $s_combination) {
				if (in_array($s_combination, array('+', '-', '*', '/', '.'))) {
					/* save operator in varialbe */
					$s_operation = $s_combination;
				} else {
					/* save value in varialbe */
					$o_field = null;
					$b_countCommand = false;
					
					if (\fPHP\Helper\forestStringLib::StartsWith($s_combination, 'CNT(')) {
						$b_countCommand = true;
					}
					
					if ( (\fPHP\Helper\forestStringLib::StartsWith($s_combination, 'SUM(')) || ($b_countCommand) ) { /* $s_combination starts with SUM( or CNT( */
						$s_combination = substr($s_combination, 4, -1);
						
						/* forestCombination SUM field must start with table declaration, separated with $ from table field */
						if (strpos($s_combination, '$') === false) {
							$s_combination .= '$';
						}
						
						$a_combinationElements = explode('$', $s_combination);
						$s_joinTable = $a_combinationElements[0];
						$s_combination = $a_combinationElements[1];
						
						if (!array_key_exists($s_joinTable, $o_glob->Tables)) {
							return '[wrong_combination_parameter]';
						}
						
						/* get table uuid of join talbe */
						$s_joinTableUUID = $o_glob->Tables[$s_joinTable];
						
						if (array_key_exists($this->fphp_TableUUID->value, $o_glob->SubConstraintsDictionary)) {
							foreach ($o_glob->SubConstraintsDictionary[$this->fphp_TableUUID->value] as $o_subconstraint) {
								/* look for sub constraint which matches table in forestCombination */
								if ($o_subconstraint->SubTableUUID->PrimaryValue == $s_joinTableUUID) {
									/* query all sub records of found sub constraint */
									$o_subRecords = $this->QuerySubRecords($o_subconstraint);
									
									if ($b_countCommand) {
										return strval($o_subRecords->Twigs->Count());
									}
									
									$o_subrecordsTwig = new \fPHP\Twigs\subrecordsTwig;
									$b_found = false;
									$s_forestCombination = null;
									
									/* get sub record field of forestCombination table field */
									foreach ($o_glob->TablefieldsDictionary as $s_key => $o_tableFieldDictionaryObject) {
										if ($o_tableFieldDictionaryObject->FieldName == $s_combination) {
											if ($o_tableFieldDictionaryObject->ForestDataName == 'forestCombination') {
												/* save table field dictionary key if forestCombination table field is another forestCombination */
												$b_found = true;
												$s_forestCombination = $s_key;
											} else if (!in_array($o_tableFieldDictionaryObject->SubRecordField, $o_subrecordsTwig->fphp_Mapping->value)) {
												return '[wrong_combination_parameter]';
											} else {
												/* save sub record field of forestCombination table field */
												$b_found = true;
												$s_combination = $o_tableFieldDictionaryObject->SubRecordField;
											}
										}
									}
									
									if (!$b_found) {
										return '[wrong_combination_parameter]';
									}
									
									if ($o_subRecords->Twigs->Count() > 0) {
										$o_value = 0;
										
										/* sum up all field values of queried sub records into $o_value */
										foreach ($o_subRecords->Twigs as $o_subRecord) {
											if ($s_forestCombination != null) {
												if ($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway == \fPHP\Base\forestBase::MongoDB) {
													$o_glob->TablefieldsDictionary->{$s_forestCombination}->JSONEncodedSettings = htmlspecialchars_decode($o_glob->TablefieldsDictionary->{$s_forestCombination}->JSONEncodedSettings);
												}
												
												/* if we found another forestCombination as table field, we need to calculate it's value as well */
												$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_glob->TablefieldsDictionary->{$s_forestCombination}->JSONEncodedSettings);
												$a_settings = json_decode($s_JSONEncodedSettings, true);
												
												if (!empty($a_settings)) {
													if (array_key_exists('forestCombination', $a_settings)) {
														$o_value += $o_subRecord->CalculateCombination($a_settings['forestCombination']);
													}
												}
											} else {
												$o_value += $o_subRecord->{$s_combination};
											}
										}
										
										return strval($o_value);
									} else {
										return strval(0);
									}
								}
							}
							
							return strval(0);
						} else {
							return '[wrong_combination_parameter]';
						}
					}
					
					if (\fPHP\Helper\forestStringLib::StartsWith($s_combination, 'FILENAME(')) { /* $s_combination starts with FILENAME */
						$s_fileField = substr($s_combination, 9, -1);
						
						$b_found = false;
						
						/* get sub record field of forestCombination table field */
						foreach ($o_glob->TablefieldsDictionary as $s_key => $o_tableFieldDictionaryObject) {
							if ($o_tableFieldDictionaryObject->FieldName == $s_fileField) {
								if ($o_tableFieldDictionaryObject->FormElementName == \fPHP\Forms\forestFormElement::FILEDIALOG) {
									/* check sub record field value */
									if (issetStr($o_tableFieldDictionaryObject->SubRecordField)) {
										/* check if field actually exists in current record */
										if (in_array($o_tableFieldDictionaryObject->SubRecordField, $this->fphp_Mapping->value)) {
											/* save table sub record field name */
											$b_found = true;
											$s_fileField = $o_tableFieldDictionaryObject->SubRecordField;
										}
									} else {
										/* check if field actually exists in current record */
										if (in_array($o_tableFieldDictionaryObject->FieldName, $this->fphp_Mapping->value)) {
											/* save table field name */
											$b_found = true;
											$s_fileField = $o_tableFieldDictionaryObject->FieldName;
										}
									}
								}
							}
						}
						
						if (!$b_found) {
							return '[wrong_combination_parametera]';
						}
						
						/* get file record */
						$o_filesTwig = new \fPHP\Twigs\filesTwig;
						
						if (! ($o_filesTwig->GetRecord(array($this->{$s_fileField}))) ) {
							return '[file not found]';
						}
						
						/* return file display name */
						return $o_filesTwig->DisplayName;
					}
					
					if (\fPHP\Helper\forestStringLib::StartsWith($s_combination, 'FILEVERSION(')) { /* $s_combination starts with FILEVERSION */
						$s_fileField = substr($s_combination, 12, -1);
						
						$b_found = false;
						
						/* get sub record field of forestCombination table field */
						foreach ($o_glob->TablefieldsDictionary as $s_key => $o_tableFieldDictionaryObject) {
							if ($o_tableFieldDictionaryObject->FieldName == $s_fileField) {
								if ($o_tableFieldDictionaryObject->FormElementName == \fPHP\Forms\forestFormElement::FILEDIALOG) {
									/* check sub record field value */
									if (issetStr($o_tableFieldDictionaryObject->SubRecordField)) {
										/* check if field actually exists in current record */
										if (in_array($o_tableFieldDictionaryObject->SubRecordField, $this->fphp_Mapping->value)) {
											/* save table sub record field name */
											$b_found = true;
											$s_fileField = $o_tableFieldDictionaryObject->SubRecordField;
										}
									} else {
										/* check if field actually exists in current record */
										if (in_array($o_tableFieldDictionaryObject->FieldName, $this->fphp_Mapping->value)) {
											/* save table field name */
											$b_found = true;
											$s_fileField = $o_tableFieldDictionaryObject->FieldName;
										}
									}
								}
							}
						}
						
						if (!$b_found) {
							return '[wrong_combination_parametera]';
						}
						
						/* get file record */
						$o_filesTwig = new \fPHP\Twigs\filesTwig;
						
						if (! ($o_filesTwig->GetRecord(array($this->{$s_fileField}))) ) {
							return '[file not found]';
						}
						
						/* return file version */
						return $o_filesTwig->Major . $o_glob->Trunk->VersionDelimiter . $o_filesTwig->Minor;
					}

					if (strpos($s_combination, '$') !== false) { /* $s_combination contains $ */
						/* this notation is used within sub records, if we are combine it with a field value of sub constaint join record */
						$a_combinationElements = explode('$', $s_combination);
						$s_joinTable = $a_combinationElements[0];
						$s_combination = $a_combinationElements[1];
						
						/* get join table of sub constraint to get field value of join record */
						if (!in_array('JoinUUID', $this->fphp_Mapping->value)) {
							return '[wrong_combination_parameter]';
						} else {
							/* create join table twig object */
							\fPHP\Helper\forestStringLib::RemoveTablePrefix($s_joinTable);
							$s_foo = '\\fPHP\\Twigs\\' . $s_joinTable . 'Twig';
							$o_tempTwig = new $s_foo;
							
							if (!in_array($s_combination, $o_tempTwig->fphp_Mapping->value)) {
								return '[wrong_combination_parameter]';
							} else {
								if (! ($o_tempTwig->GetRecord(array($this->{'JoinUUID'}))) ) {
									return '[wrong_combination_parameter]';
								} else {
									/* get field value */
									$o_field = $o_tempTwig->{$s_combination};
								}
							}
						}
					} else if ( (\fPHP\Helper\forestStringLib::StartsWith($s_combination, '#')) && (\fPHP\Helper\forestStringLib::EndsWith($s_combination, '#')) ) { /* check if constant value is part of forestCombination */
						/* it is possible to use constant values within forestCombination syntax */
						$s_combination = substr($s_combination, 1, -1);
						
						if ( (\fPHP\Helper\forestStringLib::StartsWith($s_combination, 'int(')) || (\fPHP\Helper\forestStringLib::StartsWith($s_combination, 'INT(')) ) {
							/* constant value with integer conversion */
							$s_combination = substr($s_combination, 4, -1);
							$s_combination = str_replace(',', '.', $s_combination);
							$o_field = intval($s_combination);
						} else if ( (\fPHP\Helper\forestStringLib::StartsWith($s_combination, 'dbl(')) || (\fPHP\Helper\forestStringLib::StartsWith($s_combination, 'DBL(')) ) {
							/* constant value with float conversion */
							$s_combination = substr($s_combination, 4, -1);
							$s_combination = str_replace(',', '.', $s_combination);
							$o_field = floatval($s_combination);
						} else {
							/* set constant as normal field value within forestCombination */
							$o_field = $s_combination;
						}
					} else if (!in_array($s_combination, $this->fphp_Mapping->value)) {
						/* check if field exists in tablefield dictionary */
						$b_found = false;
						
						foreach ($o_glob->TablefieldsDictionary as $o_tableFieldDictionaryObject) {
							if ($o_tableFieldDictionaryObject->FieldName == $s_combination) {
								if (!in_array($o_tableFieldDictionaryObject->SubRecordField, $this->fphp_Mapping->value)) {
									return '[wrong_combination_parameter]';
								} else {
									$b_found = true;
									
									/* get field value by sub record field property */
									$o_field = $this->{$o_tableFieldDictionaryObject->SubRecordField};
								}
							}
						}
						
						if (!$b_found) {
							return '[wrong_combination_parameter]';
						}
					}
					
					if ($o_field === null) {
						/* just another field of the same queried record in this twig object */
						$o_field = $this->{$s_combination};
					}
					
					/* do calculation operation or simple concat values */
					switch ($s_operation) {
						case '+':
							$o_result += $o_field;
						break;
						case '-':
							$o_result -= $o_field;
						break;
						case '*':
							$o_result *= $o_field;
						break;
						case '/':
							$o_result /= $o_field;
						break;
						case '.':
							$o_result .= $o_field;
						break;
						default:
							$o_result = $o_field;
						break;
					}
				}
			}
		}
		
		return strval($o_result);
	}
	
	
	/**
	 * get record with values of primary key
	 *
	 * @param array $p_a_primaryValues  primary key values for where clause
	 *
	 * @return bool  true - record found and values loaded into fields, false - record could not be found
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function GetRecord(array $p_a_primaryValues) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if (count($p_a_primaryValues) != count($this->fphp_Primary->value)) {
			throw new forestException('Primary input values[%0] and primary fields[%1] are not of the same amount', array(count($p_a_primaryValues), count($this->fphp_Primary->value)));
		}
		
		/* create select query */
		$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, $this->fphp_Table->value);
			
		$o_column = new \fPHP\Base\forestSQLColumn($o_querySelect);
			$o_column->Column = '*';
			
		$o_querySelect->Query->Columns->Add($o_column);
		
		/* if parameter array only has one value, record structure has column UUID and parameter value pattern matches a UUID */
		if ( (count($p_a_primaryValues) == 1) && ($this->fphp_HasUUID->value) && (preg_match('/^ (([0-9])|([a-f])){8} \- (([0-9])|([a-f])){4} \- (([0-9])|([a-f])){4} \- (([0-9])|([a-f])){4} \- (([0-9])|([a-f])){12} $/x', $p_a_primaryValues[0])) ) {
			$o_column = new \fPHP\Base\forestSQLColumn($o_querySelect);
				$o_column->Column = 'UUID';
			
			if ( ($this->fphp_Primary->value[0] != 'Id') && ($this->fphp_Primary->value[0] != 'UUID') ) {
				$o_column->Column = $this->fphp_Primary->value[0];
			}
			
			$o_where = new \fPHP\Base\forestSQLWhere($o_querySelect);
				$o_where->Column = $o_column;
				$o_where->Value = $o_where->ParseValue($p_a_primaryValues[0]);
				$o_where->Operator = '=';
				
			$o_querySelect->Query->Where->Add($o_where);
		} else {
			/* go with primary key */
			for ($i = 0; $i < count($this->fphp_Primary->value); $i++) {
				$o_column = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$o_column->Column = $this->fphp_Primary->value[$i];
				
				$o_where = new \fPHP\Base\forestSQLWhere($o_querySelect);
					$o_where->Column = $o_column;
					$o_where->Value = $o_where->ParseValue($p_a_primaryValues[$i]);
				
					if ($p_a_primaryValues[$i] !== 'NULL') {
						$o_where->Operator = '=';
					} else {
						$o_where->Operator = 'IS';
					}
					
					if ($i != 0) {
						$o_where->FilterOperator = 'AND';
					}
				
				$o_querySelect->Query->Where->Add($o_where);
			}
		}
		
		/* fetch select query */
		$o_result = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect);
		
		if ($o_result->Twigs->Count() == 1) {
			$o_twig = $o_result->Twigs->{0};
		} else {
			return false;
		}
		
		/* take over fields from result object */
		$this->fphp_FillFieldsFromOtherTwigObject($o_twig);
		
		unset($o_twig);
		
		return true;
	}
	
	/**
	 * fill fields from other twig object
	 *
	 * @param forestTwig $p_o_twig  object of forestTwig instance
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function fphp_FillFieldsFromOtherTwigObject(\fPHP\Twigs\forestTwig $p_o_twig) {
		if (!is_object($p_o_twig)) {
			throw new forestException('Parameter is not an object');
		}
		
		if (get_class($p_o_twig) != get_class($this)) {
			throw new forestException('Parameter object is not the same type[%0]', array(get_class($this)));
		}
		
		foreach ($this->fphp_Mapping->value as $s_field) {
			$this->{$s_field} = $p_o_twig->{$s_field};
			$this->fphp_RecordImage->value->Add($p_o_twig->{$s_field}, $s_field);
		}
	}
	
	/**
	 * determine if current twig object is empty
	 *
	 * @return bool  true - record is not empty, false - record is empty
	 *
	 * @access public
	 * @static no
	 */
	public function IsEmpty() {
		return ($this->fphp_RecordImage->value->Count() <= 0);
	}
	
	/**
	 * gets record with values of temporary other primary key
	 *
	 * @param array $p_a_primaryValues  primary key values for where clause
	 * @param array $p_a_primaryKeys  primary key names for where clause
	 *
	 * @return bool  true - record found and values loaded into fields, false - record could not be found
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function GetRecordPrimary(array $p_a_primaryValues, array $p_a_primaryKeys) {
		$a_bkp_primary = $this->fphp_Primary->value;
		$this->fphp_Primary->value = $p_a_primaryKeys;
		
		$b_ret = $this->GetRecord($p_a_primaryValues);
		
		$this->fphp_Primary->value = $a_bkp_primary;
		
		return $b_ret;
	}
	
	/**
	 * showing all data fields of current twig object for log purposes
	 *
	 * @param bool $p_b_showViewFields  true - show fields in view object list, false - show fields in mapping object list
	 * @param bool $p_b_printBreak  true - print html-br-tag within return value, false - do not print html-br-tag within return value
	 *
	 * @return string  record fields with values
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function ShowFields($p_b_showViewFields = true, $p_b_printBreak = false) {
		$s_foo = '';
		$s_break = '';
		
		if ($p_b_printBreak) {
			$s_break = '<br>';
		}
		
		if ( ($p_b_showViewFields) && (count($this->fphp_View->value) > 0) ) {
			if (property_exists($this, 'Id')) {
				$s_foo .= 'Id = ' . $this->{'Id'} . $s_break . "\n";
			}
			
			if ($this->fphp_HasUUID->value) {
				$s_foo .= 'UUID = ' . $this->{'UUID'} . $s_break . "\n";
			}
			
			foreach($this->fphp_View->value as $s_field) {
				if ( ($s_field == 'Id') || ($s_field == 'UUID') ) {
					continue;
				}
				
				$s_foo .= $s_field . ' = ' . $this->{$s_field} . $s_break . "\n";
			}
		} else {
			foreach($this->fphp_Mapping->value as $s_field) {
				$s_foo .= $s_field . ' = ' . $this->{$s_field} . $s_break . "\n";
			}
		}
		
		return $s_foo;
	}
	
	/**
	 * get first record of table
	 *
	 * @return bool  true - record found and values loaded into fields, false - record could not be found
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function GetFirstRecord() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* create select query */
		$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, $this->fphp_Table->value);
			
		$o_column = new \fPHP\Base\forestSQLColumn($o_querySelect);
			$o_column->Column = '*';
				
		$o_querySelect->Query->Columns->Add($o_column);
		
		/* add additional sql filter clauses */
		$this->ImplementAdditionalSQLFilter($o_querySelect);
		
		/* set order into select query */
		foreach($this->fphp_SortOrder->value as $s_sortColumn => $b_sortDirection) {
			$o_column = new \fPHP\Base\forestSQLColumn($o_querySelect);
				$o_column->Column = $s_sortColumn;
				
			$o_querySelect->Query->OrderBy->AddColumn($o_column, $b_sortDirection);
		}
		
		/* set limit into select query */
		$o_querySelect->Query->Limit->Start = 0;
		$o_querySelect->Query->Limit->Interval = 1;
		
		/* fetch select query */
		$o_result = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect);
		
		if ($o_result->Twigs->Count() == 1) {
			$o_twig = $o_result->Twigs->{0};
		} else {
			return false;
		}
		
		/* take over fields from result object */
		$this->fphp_FillFieldsFromOtherTwigObject($o_twig);
		
		unset($o_twig);
		
		return true;
	}
	
	/**
	 * get last record of table
	 *
	 * @return bool  true - record found and values loaded into fields, false - record could not be found
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function GetLastRecord() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* create select query */
		$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, $this->fphp_Table->value);
			
		$o_column = new \fPHP\Base\forestSQLColumn($o_querySelect);
			$o_column->Column = '*';
				
		$o_querySelect->Query->Columns->Add($o_column);
		
		/* add additional sql filter clauses */
		$this->ImplementAdditionalSQLFilter($o_querySelect);
		
		/* set order into select query with reversed direction */
		foreach($this->fphp_SortOrder->value as $s_sortColumn => $b_sortDirection) {
			$o_column = new \fPHP\Base\forestSQLColumn($o_querySelect);
				$o_column->Column = $s_sortColumn;
			
			if ($b_sortDirection) {
				$b_sortDirection = false;
			} else {
				$b_sortDirection = true;
			}
			
			$o_querySelect->Query->OrderBy->AddColumn($o_column, $b_sortDirection);
		}
		
		/* set limit into select query */
		$o_querySelect->Query->Limit->Start = 0;
		$o_querySelect->Query->Limit->Interval = 1;
		
		/* fetch select query */
		$o_result = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect);
		
		if ($o_result->Twigs->Count() == 1) {
			$o_twig = $o_result->Twigs->{0};
		} else {
			return false;
		}
		
		/* take over fields from result object */
		$this->fphp_FillFieldsFromOtherTwigObject($o_twig);
		
		unset($o_twig);
		
		return true;
	}
	
	/**
	 * get amount of records of current table or another table
	 *
	 * @param string $p_s_table  name of table, optional
	 * @param bool $p_b_unlimited  true - use filter and limit settings, false - ignore filter and limit settings
	 * @param bool $p_b_updateLimitAmount  true - update global limit amount value, false - do not update global limit amount value
	 *
	 * @return integer  amount of records
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function GetCount($p_s_table = null, $p_b_unlimited = false, $p_b_updateLimitAmount = false) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$s_table = $this->fphp_Table->value;
		
		if ($p_s_table != null) {
			$s_table = $p_s_table;
		}
		
		/* create query for amount of records */
		$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, $s_table);
			
		$o_column = new \fPHP\Base\forestSQLColumn($o_querySelect);
			$o_column->Column = '*';
			$o_column->SqlAggregation = 'COUNT';
			$o_column->Name = 'AmountRecords';
				
		$o_querySelect->Query->Columns->Add($o_column);
		
		/* we add additional join for the query if it is set */
		if ($o_glob->Temp->Exists('SQLAdditionalJoin')) {
			$o_additionalJoin = $o_glob->Temp->{'SQLAdditionalJoin'};
			$o_querySelect->Query->Joins->Add($o_additionalJoin);
		}
		
		/* implement filter which is saved in session parameter array */
		$this->ImplementFilter($o_querySelect, $p_b_unlimited);
		
		/* get amount of records */
		$o_result = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect, false, false);
		
		$i_amount_records = 0;
		
		/* query if index 'AmountRecords' is available, because of postgresql case-sensitive */
		if (array_key_exists('AmountRecords', $o_result[0])) {
			$i_amount_records = intval($o_result[0]['AmountRecords']);
		} else {
			$i_amount_records = intval($o_result[0]['amountrecords']);
		}
		
		/* set global limit amount */
		if ($p_b_updateLimitAmount) {
			$o_glob->Limit->Amount = $i_amount_records;
		}
		
		return $i_amount_records;
	}
	
	
	/**
	 * implement filter values of session parameter array
	 *
	 * @param forestSQLQuery $p_o_query  sql query for adding filter clauses
	 * @param bool $p_b_unlimited  true - use filter and limit settings, false - ignore filter and limit settings
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function ImplementFilter(\fPHP\Base\forestSQLQuery &$p_o_query, $p_b_unlimited) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$a_session_filter = array();
		
		/* check if we have a select query object as parameter */
		if ($p_o_query->SqlType != \fPHP\Base\forestSQLQuery::SELECT) {
			throw new forestException('Invalid SqlType[%0]. Only SqlType[SELECT] allowed', array($p_o_query->SqlType));
		}
		
		/* get existing filter values out of session */
		if ($o_glob->Security->SessionData->Exists('filter')) {
			$a_session_filter = $o_glob->Security->SessionData->{'filter'};
		}
		
		$b_initWhere = false;
		
		if ((!empty($a_session_filter)) && (!$p_b_unlimited)) {
			foreach ($a_session_filter as $s_column => $s_filterValue) {
				if ( ($s_column == 'FilterAllColumns') && (!$b_initWhere) ) {
					/* check if no other operators * = > < .. ? & | are in this string then we can add * wildcards */
					if (!(
						(strpos($s_filterValue, '*') === false) &&
						(strpos($s_filterValue, '=') === false) &&
						(strpos($s_filterValue, '>') === false) &&
						(strpos($s_filterValue, '<') === false) &&
						(strpos($s_filterValue, '..') === false) &&
						(strpos($s_filterValue, '?') === false) &&
						(strpos($s_filterValue, '&') === false) &&
						(strpos($s_filterValue, '|') === false)
					)) {
						/* invalid value for searching all columns */
						if ($o_glob->Security->SessionData->Exists('filter')) {
							$o_glob->SystemMessages->Add(new forestException(0x10000C00, array(' *, =, &gt;, &lt;, .., ?, &amp;, | ')));
							$o_glob->Security->SessionData->Del('filter');
							break;
						}
					}
					
					/* generate where clause for each column we have */
					foreach ($this->fphp_Mapping->value as $s_column) {
						if ( ($s_column != 'Id') && ($s_column != 'UUID') ) {
							$o_columnFoo = new \fPHP\Base\forestSQLColumn($p_o_query);
								$o_columnFoo->Column = $s_column;
							
							$o_where = new \fPHP\Base\forestSQLWhere($p_o_query);
								$o_where->Column = $o_columnFoo;
								$o_where->Value = $o_where->ParseValue('%' . $s_filterValue . '%');
								$o_where->Operator = 'LIKE';
								
								if ($b_initWhere) {
									$o_where->FilterOperator = 'OR';
								}
								
							$p_o_query->Query->Where->Add($o_where);
							
							$b_initWhere = true;
						}
					}
				} else {
					/* split the filter in its necessary form to implement it in the query */
					$a_clauses = \fPHP\Helper\forestStringLib::SplitFilter($s_filterValue);
					
					/* check if filter column is of type FILE */
					$b_found_file = false;
					$a_file_uuids = array();
					
					if ($o_glob->TablefieldsDictionary->Exists($this->fphp_Table->value . '_' . $s_column)) {
						if ($o_glob->TablefieldsDictionary->{$this->fphp_Table->value . '_' . $s_column}->FormElementName == \fPHP\Forms\forestFormElement::FILEDIALOG) {
							/* select uuid on table files filtered on displayname */
							$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, 'sys_fphp_files');
				
								$column_A = new \fPHP\Base\forestSQLColumn($o_querySelect);
									$column_A->Column = 'UUID';
								
								$column_B = new \fPHP\Base\forestSQLColumn($o_querySelect);
									$column_B->Column = 'DisplayName';
								
								$column_C = new \fPHP\Base\forestSQLColumn($o_querySelect);
									$column_C->Column = 'BranchId';
								
							$o_querySelect->Query->Columns->Add($column_A);
							
							$s_lastfilterOperatorFiles = null;
							
							foreach ($a_clauses as $a_clause) {
								$o_where = new \fPHP\Base\forestSQLWhere($o_querySelect);
									$o_where->Column = $column_B;
									$o_where->Value = $o_where->ParseValue($a_clause[1]);
									$o_where->Operator = $a_clause[0];
									
									$s_filterOperator = $s_lastfilterOperatorFiles;
									
									/* remind last filter operator for next where clause */
									if (array_key_exists(2, $a_clause)) {
										if ($a_clause[2] == '&') {
											$s_lastfilterOperatorFiles = 'AND';
										} else if ($a_clause[2] == '|') {
											$s_lastfilterOperatorFiles = 'OR';
										}
									} else {
										$s_lastfilterOperatorFiles = 'AND';
									}
									
									if (!is_null($s_filterOperator)) {
										$o_where->FilterOperator = $s_filterOperator;
									}
									
								$o_querySelect->Query->Where->Add($o_where);
							}
							
							$o_where = new \fPHP\Base\forestSQLWhere($o_querySelect);
								$o_where->Column = $column_C;
								$o_where->Value = $o_where->ParseValue($o_glob->URL->BranchId);
								$o_where->Operator = '=';
								$o_where->FilterOperator = 'AND';
								
							$o_querySelect->Query->Where->Add($o_where);
							
							$o_result = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect);
							
							if ($o_result->Twigs->Count() > 0) {
								$b_found_file = true;
								
								foreach ($o_result->Twigs as $o_file) {
									$a_file_uuids[] = '\'' .  $o_file->UUID . '\'';
								}
							}
						}
					}
					
					/* create column object for the where clause */
					if (strpos($s_column, '.') !== false) {
						$a_columnInfo = explode('.', $s_column);
						
						$o_columnFoo = new \fPHP\Base\forestSQLColumn($p_o_query);
							$o_columnFoo->Table = $a_columnInfo[0];
							$o_columnFoo->Column = $a_columnInfo[1];
					} else {
						$o_columnFoo = new \fPHP\Base\forestSQLColumn($p_o_query);
							$o_columnFoo->Column = $s_column;
					}
					
					/* generate where clause for each clause we got */
					$s_lastfilterOperator = null;
					
					if ($b_initWhere) {
						$s_lastfilterOperator = 'AND';
					}
					
					if (!$b_found_file) {
						foreach ($a_clauses as $a_clause) {
							$o_where = new \fPHP\Base\forestSQLWhere($p_o_query);
								$o_where->Column = $o_columnFoo;
								$o_where->Value = $o_where->ParseValue($a_clause[1]);
								$o_where->Operator = $a_clause[0];
								
								
								$s_filterOperator = $s_lastfilterOperator;
								
								/* remind last filter operator for next where clause */
								if (array_key_exists(2, $a_clause)) {
									if ($a_clause[2] == '&') {
										$s_lastfilterOperator = 'AND';
									} else if ($a_clause[2] == '|') {
										$s_lastfilterOperator = 'OR';
									}
								} else {
									$s_lastfilterOperator = 'AND';
								}
								
								if (!is_null($s_filterOperator)) {
									$o_where->FilterOperator = $s_filterOperator;
								}
								
							$p_o_query->Query->Where->Add($o_where);
							
							$b_initWhere = true;
						}
					} else {
						$o_where = new \fPHP\Base\forestSQLWhere($p_o_query);
							$o_where->Column = $o_columnFoo;
							$o_where->Value = '(' . implode(',', $a_file_uuids) . ')';
							$o_where->Operator = 'IN';
							
							$s_filterOperator = $s_lastfilterOperator;
							
							/* remind last filter operator for next where clause */
							if (array_key_exists(2, $a_clause)) {
								if ($a_clause[2] == '&') {
									$s_lastfilterOperator = 'AND';
								} else if ($a_clause[2] == '|') {
									$s_lastfilterOperator = 'OR';
								}
							} else {
								$s_lastfilterOperator = 'AND';
							}
							
							if (!is_null($s_filterOperator)) {
								$o_where->FilterOperator = $s_filterOperator;
							}
							
						$p_o_query->Query->Where->Add($o_where);
						
						$b_initWhere = true;
					}
				}
			}
		}
		
		$this->ImplementAdditionalSQLFilter($p_o_query, $b_initWhere);
	}
	
	/**
	 * implement filter values of session parameter array
	 *
	 * @param forestSQLQuery $p_o_query  sql query for adding filter clauses
	 * @param bool $p_b_initWhere  true - where clause already exists in sql query, false - no where clause in sql query
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function ImplementAdditionalSQLFilter(\fPHP\Base\forestSQLQuery &$p_o_query, &$p_b_initWhere = false) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* if we have the special case that we use SQLAdditionalFilter, instead of session filter, */
		/* we call them of global temp and implement them in the query */
		if ($o_glob->Temp->Exists('SQLAdditionalFilter')) {
			foreach ($o_glob->Temp->{'SQLAdditionalFilter'} as $a_filter) {
				/* check completeness of additional sql filter data */
				if (!array_key_exists('column', $a_filter)) {
					throw new forestException('SQLAdditionalFilter element is missing \'column\' data');
				}
				
				if (!array_key_exists('value', $a_filter)) {
					throw new forestException('SQLAdditionalFilter element is missing \'value\' data');
				}
				
				if (!array_key_exists('operator', $a_filter)) {
					throw new forestException('SQLAdditionalFilter element is missing \'operator\' data');
				}
				
				if ($p_b_initWhere) {
					if (!array_key_exists('filterOperator', $a_filter)) {
						throw new forestException('SQLAdditionalFilter element is missing \'filterOperator\' data');
					}
				}
				
				/* add filter data to select query */
				if (strpos($a_filter['column'], '.') !== false) {
					$a_columnInfo = explode('.', $a_filter['column']);
					
					$o_columnFoo = new \fPHP\Base\forestSQLColumn($p_o_query);
						$o_columnFoo->Table = $a_columnInfo[0];
						$o_columnFoo->Column = $a_columnInfo[1];
				} else {
					$o_columnFoo = new \fPHP\Base\forestSQLColumn($p_o_query);
						$o_columnFoo->Column = $a_filter['column'];
				}
				
				$o_where = new \fPHP\Base\forestSQLWhere($p_o_query);
					$o_where->Column = $o_columnFoo;
					$o_where->Value = $o_where->ParseValue($a_filter['value']);
					$o_where->Operator = $a_filter['operator'];
					
					$o_where->BracketStart = array_key_exists('bracket_start', $a_filter);
					$o_where->BracketEnd = array_key_exists('bracket_end', $a_filter);
					
					if ($p_b_initWhere) {
						$o_where->FilterOperator = $a_filter['filterOperator'];
					}
				
				if (array_key_exists('escapeMarkedUnderscore', $a_filter)) {
					$o_where->Value = str_replace('{[(_)]}', '\\_', $o_where->Value->scalar);
				}
				
				$p_o_query->Query->Where->Add($o_where);
				
				$p_b_initWhere = true;
			}
		}
	}
	
	/**
	 * get all records, depending on filter and limit parameters
	 *
	 * @param bool $p_b_unlimited  true - use filter and limit settings, false - ignore filter and limit settings
	 *
	 * @return result of function forestBase->FetchQuery
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function GetAllRecords($p_b_unlimited = false) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* calculate amount of records */
		$i_amount_records = $this->GetCount(null, $p_b_unlimited, true);
		
		/* create select query */
		$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, $this->fphp_Table->value);
		
		/* adding optional columns for the query if they are set */
		if ($o_glob->Temp->Exists('SQLGetAllAdditionalColumns')) {
			foreach($o_glob->Temp->{'SQLGetAllAdditionalColumns'} as $o_additionalColumn) {
				$o_querySelect->Query->Columns->Add($o_additionalColumn);
			}
		} else {
			/* we want to query all columns */
			$o_column = new \fPHP\Base\forestSQLColumn($o_querySelect);
				$o_column->Column = '*';
				
			$o_querySelect->Query->Columns->Add($o_column);
		}
		
		/* we add additional join for the query if it is set */
		if ($o_glob->Temp->Exists('SQLAdditionalJoin')) {
			$o_additionalJoin = $o_glob->Temp->{'SQLAdditionalJoin'};
			$o_querySelect->Query->Joins->Add($o_additionalJoin);
		}
		
		/* implement filter which is saved in session parameter array */
		$this->ImplementFilter($o_querySelect, $p_b_unlimited);
		
		/* use sort information of session parameter array */
		if (($o_glob->Sorts->Count() > 0) && (!$p_b_unlimited)) {
			foreach($o_glob->Sorts as $o_sort) {
				/* skip sort fields which are not in the mapping array of twig object */
				if (!in_array($o_sort->Column, $this->fphp_Mapping->value)) {
					continue;
				}
				
				$o_column = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$o_column->Column = $o_sort->Column;
				
				$o_querySelect->Query->OrderBy->AddColumn($o_column, $o_sort->Direction);
			}
		} else if ($o_glob->Temp->Exists('SQLAdditionalSorts')) {
			/* use sort information of global temp array */
			foreach($o_glob->Temp->{'SQLAdditionalSorts'} as $s_sortColumn => $b_sortDirection) {
				/* skip sort fields which are not in the mapping array of twig object */
				if (!in_array($s_sortColumn, $this->fphp_Mapping->value)) {
					continue;
				}
				
				$o_column = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$o_column->Column = $s_sortColumn;
					
				$o_querySelect->Query->OrderBy->AddColumn($o_column, $b_sortDirection);
			}
		} else {
			/* use standard sort information of twig object */
			foreach($this->fphp_SortOrder->value as $s_sortColumn => $b_sortDirection) {
				/* skip sort fields which are not in the mapping array of twig object */
				if (!in_array($s_sortColumn, $this->fphp_Mapping->value)) {
					continue;
				}
				
				$o_column = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$o_column->Column = $s_sortColumn;
					
				$o_querySelect->Query->OrderBy->AddColumn($o_column, $b_sortDirection);
			}
		}
		
		/* set global limit values */
		$o_glob->Limit->Start = 0;
		$o_glob->Limit->Interval = $this->fphp_Interval->value;
		
		/* add limit values to the query if unlimited parameter is not set as true */
		if (!$p_b_unlimited) {
			if ($o_glob->Limit->Page > 1) {
				$i_pages = intval(ceil(intval($o_glob->Limit->Amount) / intval($o_glob->Limit->Interval)));
				
				if ($o_glob->Limit->Page >= $i_pages) {
					$o_glob->Limit->Page = $i_pages;
				}
				
				$o_glob->Limit->Start = ($o_glob->Limit->Page - 1) * $o_glob->Limit->Interval;
			}
			
			$o_querySelect->Query->Limit->Start = $o_glob->Limit->Start;
			$o_querySelect->Query->Limit->Interval = $o_glob->Limit->Interval;
		}

		return $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect);
	}
	
	/**
	 * get records based on view columns, depending on filter and limit parameters
	 *
	 * @param bool $p_b_unlimited  true - use filter and limit settings, false - ignore filter and limit settings
	 *
	 * @return result of function this->GetAllRecords
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function GetAllViewRecords($p_b_unlimited = false) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* create select query */
		$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, $this->fphp_Table->value);
		
		if ($o_glob->AddHiddenColumns->Count() > 0) {
			foreach ($o_glob->AddHiddenColumns as $s_hidden_field) {
				/* skip Id or UUID field */
				if ( ($s_hidden_field == 'Id') || ($s_hidden_field == 'UUID') ) {
					continue;
				}
				
				/* skip fields which are not in the mapping array of twig object, unless they are in tablefield dictionary */
				if (!in_array($s_hidden_field, $this->fphp_Mapping->value)) {
					if (!$o_glob->TablefieldsDictionary->Exists($this->fphp_Table->value . '_' . $s_hidden_field)) {
						continue;
					}
				}
				
				/* skip fields which are in view array of twig object */
				if (in_array($s_hidden_field, $this->fphp_View->value)) {
					continue;
				}
				
				$a_temp = $this->fphp_View->value;
				$a_temp[] = $s_hidden_field;
				$this->fphp_View->value = $a_temp;
			}
		}
		
		if (count($this->fphp_View->value) > 0) {
			$a_sqlGetAllAdditionalColumns = array();
			
			if (in_array('Id', $this->fphp_Mapping->value)) {
				$o_columnId = new \fPHP\Base\forestSQLColumn($o_querySelect);
					$o_columnId->Column = 'Id';
							
				$a_sqlGetAllAdditionalColumns[] = $o_columnId;
			}
			
			if ($this->fphp_HasUUID->value) {
				$o_columnUUID = new \fPHP\Base\forestSQLColumn($o_querySelect);
						$o_columnUUID->Column = 'UUID';
						
				$a_sqlGetAllAdditionalColumns[] = $o_columnUUID;
			}
			
			/* if identifier is configured */
			if (issetStr($o_glob->TablesInformation[$this->fphp_TableUUID->value]['Identifier']->PrimaryValue)) {
				$o_columnIdentifier = new \fPHP\Base\forestSQLColumn($o_querySelect);
						$o_columnIdentifier->Column = 'Identifier';
						
				$a_sqlGetAllAdditionalColumns[] = $o_columnIdentifier;
			}
			
			foreach ($this->fphp_View->value as $s_view_field) {
				if ( ($s_view_field == 'Id') || ($s_view_field == 'UUID') || ($s_view_field == 'Identifier') || (!in_array($s_view_field, $this->fphp_Mapping->value)) ) {
					continue;
				}
				
				$o_columnFoo = new \fPHP\Base\forestSQLColumn($o_querySelect);
						$o_columnFoo->Column = $s_view_field;
						
				$a_sqlGetAllAdditionalColumns[] = $o_columnFoo;
			}
		}
		
		if (count($this->fphp_View->value) > 0) {
			$o_glob->Temp->Add($a_sqlGetAllAdditionalColumns, 'SQLGetAllAdditionalColumns');
		}
		
		$o_return = $this->GetAllRecords($p_b_unlimited);
		
		if (count($this->fphp_View->value) > 0) {
			$o_glob->Temp->Del('SQLGetAllAdditionalColumns');
		}
		
		return $o_return;
	}
	
	
	/**
	 * insert record into table, with primary key fields optional
	 *
	 * @param bool $p_b_withPrimary  true - assume and set values for primary key, false - keep primary key values unchanged or let them handle by dbms
	 *
	 * @return integer  -1 - unique issue, 0 - could not create record, 1 - record creation successful
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function InsertRecord($p_b_withPrimary = false, $p_b_full = false) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check uniqueness */
		if ($this->CheckUniquenessInsert($p_b_withPrimary) > 0) {
			return -1;
		}
		
		/* create insert query */
		$o_queryInsert = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::INSERT, $this->fphp_Table->value);
		
		/* read out twig fields to get values for insert query */
		foreach ($this->fphp_Mapping->value as $s_field) {
			if ($p_b_full) {
				$o_columnValue = new \fPHP\Base\forestSQLColumnValue($o_queryInsert);
					$o_columnValue->Column = $s_field;
					$o_columnValue->Value = $o_columnValue->ParseValue($this->{$s_field});
					
				$o_queryInsert->Query->ColumnValues->Add($o_columnValue);
			} else if ((!in_array($s_field, $this->fphp_Primary->value)) || ($p_b_withPrimary)) {
				/* set new UUID automatically */
				if ($s_field == 'UUID') {
					/* if not UUID has been already set */
					if (!issetStr($this->{$s_field})) {
						$this->{$s_field} = $this->GetUUID();
					}
				}
				
				$o_columnValue = new \fPHP\Base\forestSQLColumnValue($o_queryInsert);
					$o_columnValue->Column = $s_field;
					$o_columnValue->Value = $o_columnValue->ParseValue($this->{$s_field});
					
				$o_queryInsert->Query->ColumnValues->Add($o_columnValue);
			}
		}
		
		if ( (in_array('Id', $this->fphp_Mapping->value)) && (in_array('Id', $this->fphp_Primary->value)) && (!$p_b_withPrimary) ) {
			$o_glob->Temp->Add(true, 'MongoDBIdAutoIncrement');
		}
		
		$i_return = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryInsert);
		
		if ( ($i_return > 0) && (in_array('Id', $this->fphp_Mapping->value)) ) {
			$this->Id = $o_glob->Base->{$o_glob->ActiveBase}->LastInsertId();
		}
		
		return $i_return;
	}
	
	/**
	 * check uniqueness of twig object within table for insert query
	 *
	 * @param bool $p_b_withPrimary  true - check values for primary key as well, false - ignore primary key values
	 *
	 * @return integer  >0 - unique issue, <=0 - no unique issue
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function CheckUniquenessInsert($p_b_withPrimary = false) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$i_return = 0;
		
		/* check primary columns */
		if ($p_b_withPrimary) {
			$a_sqlAdditionalFilter = array();
			
			foreach ($this->fphp_Primary->value as $s_primary) {
				$a_sqlAdditionalFilter[] = array('column' => $s_primary, 'value' => $this->{$s_primary}, 'operator' => '=', 'filterOperator' => 'AND');
			}
			
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$i_return = $this->GetCount(null, true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			if ($i_return > 0) {
				$s_pri = '';
				$i = 0;
				
				foreach ($this->fphp_Primary->value as $s_primary) {
					$s_pri .= $s_primary;
					
					if(++$i != count($this->fphp_Primary->value)) {
						$s_pri .= ', ';
					}
				}
				
				$o_glob->Temp->Add('UNIQUE ISSUE - primary key[' . $s_pri . '] already exists for [' . $this->fphp_Table->value . ']', 'UniqueIssue');
			}
		}
		
		/* check unique constraints */
		foreach ($this->fphp_Unique->value as $s_unique_constraint) {
			/* primary key collision already detected */
			if ($i_return > 0) {
				break;
			}
			
			/* ignore unique constraints which consists only of column UUID */
			if ($s_unique_constraint == 'UUID') {
				continue;
			}
			
			$a_sqlAdditionalFilter = array();
			
			/* it is possible that a unique constraint exists of multiple columns, separated by semicolon */
			if (strpos($s_unique_constraint, ';') !== false) {
				$a_columns = explode(';', $s_unique_constraint);
				
				for ($i = 0; $i < count($a_columns); $i++) {
					$a_sqlAdditionalFilter[] = array('column' => $a_columns[$i], 'value' => $this->{$a_columns[$i]}, 'operator' => '=', 'filterOperator' => 'AND');
				}
			} else {
				$a_sqlAdditionalFilter[] = array('column' => $s_unique_constraint, 'value' => $this->{$s_unique_constraint}, 'operator' => '=', 'filterOperator' => 'AND');
			}
			
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$i_return = $this->GetCount(null, true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			if ($i_return > 0) {
				$o_glob->Temp->Add('UNIQUE ISSUE - unique constraint invalid for [' . $s_unique_constraint . '] in table [' . $this->fphp_Table->value . ']; unique key already exists', 'UniqueIssue');
			}
		}
		
		return $i_return;
	}
	
	/**
	 * get unused uuid of table for insert query
	 *
	 * @return string  valid uuid
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function GetUUID() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* generate new uuid */
		$s_uuid = $o_glob->Security->GenUUID();
		
		/* we can deactivate checking for unique uuid, because it can be kind of long depending how many records exists in the table */
		if ($o_glob->Trunk->CheckUniqueUUID) {
			if (!in_array('UUID', $this->fphp_Mapping->value)) {
				throw new forestException('Field UUID does not exists in twig object');
			}
		
			do {
				/* create select query for counting records with generated uuid to see if a record already exists with that uuid */
				$o_querySelect = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::SELECT, $this->fphp_Table->value);
					$o_column = new \fPHP\Base\forestSQLColumn($o_querySelect);
						$o_column->Column = 'UUID';
						$o_column->Name = 'UUID';
						$o_column->SqlAggregation = 'COUNT';
					
					$o_querySelect->Query->Columns->Add($o_column);
					
					$o_column = new \fPHP\Base\forestSQLColumn($o_querySelect);
						$o_column->Column = 'UUID';
				
					$o_where = new \fPHP\Base\forestSQLWhere($o_querySelect);
						$o_where->Column = $o_column;
						$o_where->Value = $s_uuid;
						$o_where->Operator = '=';
					
				$o_querySelect->Query->Where->Add($o_where);
				
				$o_result = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect, false, false);
				
				$i_amount_records = 0;
		
				/* query if index 'UUID' is available, because of postgresql case-sensitive */
				if (array_key_exists('UUID', $o_result[0])) {
					$i_amount_records = intval($o_result[0]['UUID']);
				} else {
					$i_amount_records = intval($o_result[0]['uuid']);
				}
				
				/* if amount = 0 we can use the generated uuid */
			} while ($i_amount_records != 0);
		}
		
		return $s_uuid;
	}
	
	
	/**
	 * update record in table, you cannot modify primary key, with modify of unique fields optional
	 *
	 * @param bool $p_b_modifyUnique  true - assume and set values for unique fields, false - keep unique key fields unchanged or let them handle by dbms
	 * @param bool $p_b_modifyIdPrimary  true - allow change of Id primary(only if Id and Identifier are changed at the same time), false - keep Id primary unchanged or let them handle by dbms
	 *
	 * @return integer  -1 - unique issue, 0 - could not update record, 1 - record update successful
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function UpdateRecord($p_b_modifyUnique = true, $p_b_modifyIdPrimary = false) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$b_field_has_changed = false;
		$a_columns = array();
		
		if ($o_glob->Temp->Exists('SQLUpdateColumns')) {
			$a_columns = $o_glob->Temp->{'SQLUpdateColumns'};
		} else {
			$a_columns = $this->fphp_Mapping->value;
		}

		/* only if Id and Identifier are changed at the same time, it is allowed to change Id primary */
		if ( ($p_b_modifyIdPrimary) && (!( (in_array('Id', $a_columns)) && (count($a_columns) == 2) && (in_array('Identifier', $a_columns)) )) ) {
			throw new forestException('UpdateRecord does not allow executing query changing \'Id\' primary field');
		}

		/* check if any field has changed compared to the record image */
		foreach ($a_columns as $s_field) {
			if (!$this->fphp_RecordImage->value->Exists($s_field)) {
				throw new forestException('Record image does not match with twig object. Field[%0] does not exists in record image', array($s_field));
			}
			
			if ($this->{$s_field} != $this->fphp_RecordImage->value->{$s_field}) {
				$b_field_has_changed = true;
			}
		}
		
		/* there is nothing to change */
		if (!$b_field_has_changed) {
			/*echo '<h1>There is nothing to change</h1>';
			$s_recordFields = $this->ShowFields();
			echo $s_recordFields;
			echo '<hr />';*/
			return 0;
		}
		
		/* check uniqueness */
		if ($this->CheckUniquenessUpdate() > 0) {
			return -1;
		}
		
		/* create update query */
		$o_queryUpdate = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::UPDATE, $this->fphp_Table->value);
		
		/* read out twig fields to get values for insert query */
		foreach ($a_columns as $s_field) {
			if ( ( ($p_b_modifyIdPrimary) || (!in_array($s_field, $this->fphp_Primary->value)) ) && ( ($p_b_modifyUnique) || (!in_array($s_field, $this->fphp_Unique->value)) ) ) {
				$o_columnValue = new \fPHP\Base\forestSQLColumnValue($o_queryUpdate);
					$o_columnValue->Column = $s_field;
					$o_columnValue->Value = $o_columnValue->ParseValue($this->{$s_field});
				
				$o_queryUpdate->Query->ColumnValues->Add($o_columnValue);
			}
		}
			
		/* if twig object use uuid, use it as update filter */
		if ($this->fphp_HasUUID->value) {
			$o_column = new \fPHP\Base\forestSQLColumn($o_queryUpdate);
				$o_column->Column = 'UUID';
			
			$o_where = new \fPHP\Base\forestSQLWhere($o_queryUpdate);
				$o_where->Column = $o_column;
				$o_where->Value = $o_where->ParseValue($this->{'UUID'});
				$o_where->Operator = '=';
			
			$o_queryUpdate->Query->Where->Add($o_where);
		} else {
			$i = 0;
			
			/* else take primary key fields for the update filter */
			foreach ($this->fphp_Primary->value as $s_primary) {
				$o_column = new \fPHP\Base\forestSQLColumn($o_queryUpdate);
				$o_column->Column = $s_primary;
			
				$o_where = new \fPHP\Base\forestSQLWhere($o_queryUpdate);
					$o_where->Column = $o_column;
					$o_where->Value = $o_where->ParseValue($this->{$s_primary});
					$o_where->Operator = '=';
					
					if ($i != 0) {
						$o_where->FilterOperator = 'AND';
					}
				
				$o_queryUpdate->Query->Where->Add($o_where);
				
				$i++;
			}
		}
		
		return $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryUpdate);
	}
	
	/**
	 * check uniqueness of twig object within table for update query, with detection if primary key has changed
	 *
	 * @return integer  >0 - unique issue, <=0 - no unique issue
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function CheckUniquenessUpdate() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$i_return = 0;
		$b_primaryKeyChanged = false;
		$a_unique_constraints = array();
		$b_uuidChanged = false;
		$b_uniqueChanged = false;
		
		/* detect if primary key has changed */
		foreach ($this->fphp_Primary->value as $s_primary) {
			if (!$this->fphp_RecordImage->value->Exists($s_primary)) {
				throw new forestException('Record image does not match with twig object');
			}
			
			if ($this->{$s_primary} != $this->fphp_RecordImage->value->{$s_primary}) {
				$b_primaryKeyChanged = true;
			}
		}
		
		/* check primary key */
		if ($b_primaryKeyChanged) {
			$a_sqlAdditionalFilter = array();
			
			foreach ($this->fphp_Primary->value as $s_primary) {
				$a_sqlAdditionalFilter[] = array('column' => $s_primary, 'value' => $this->{$s_primary}, 'operator' => '=', 'filterOperator' => 'AND');
			}
			
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$i_return = $this->GetCount(null, true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			if ($i_return > 0) {
				$s_pri = '';
				$i = 0;
				
				foreach ($this->fphp_Primary->value as $s_primary) {
					$s_pri .= $s_primary;
					
					if(++$i != count($this->fphp_Primary->value)) {
						$s_pri .= ', ';
					}
				}
				
				$o_glob->Temp->Add('UNIQUE ISSUE - primary key[' . $s_pri . '] already exists for [' . $this->fphp_Table->value . ']', 'UniqueIssue');
			}
		}
		
		if ($this->fphp_HasUUID->value) {
			/* detect if UUID has changed */
			if (!$this->fphp_RecordImage->value->Exists('UUID')) {
				throw new forestException('Record image does not match with twig object');
			}
			
			if ($this->{'UUID'} != $this->fphp_RecordImage->value->{'UUID'}) {
				$b_uuidChanged = true;
			}
			
			/* check UUID */
			if ($b_uuidChanged) {
				$a_sqlAdditionalFilter = array();
				
				$a_sqlAdditionalFilter[] = array('column' => 'UUID', 'value' => $this->{'UUID'}, 'operator' => '=', 'filterOperator' => 'AND');
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$i_return = $this->GetCount(null, true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				
				if ($i_return > 0) {
					$o_glob->Temp->Add('UNIQUE ISSUE - unique key[UUID] already exists for [' . $this->fphp_Table->value . ']', 'UniqueIssue');
				}
			}
		}
		
		/* detect if unique has changed */
		foreach ($this->fphp_Unique->value as $s_unique) {
			if (strpos($s_unique, ';') !== false) {
				$a_columns = explode(';', $s_unique);
				
				for ($i = 0; $i < count($a_columns); $i++) {
					if (!$this->fphp_RecordImage->value->Exists($a_columns[$i])) {
						throw new forestException('Record image does not match with twig object');
					}
					
					$o_left = $this->{$a_columns[$i]};
					$o_right = $this->fphp_RecordImage->value->{$a_columns[$i]};
					
					$b_compareDate = false;
					$b_compareDateTime = false;
					$b_compareMonth = false;
					$b_compareTime = false;
					$b_compareLookup = false;
					
					/* check if tablefield is in dictionary */
					if ($o_glob->TablefieldsDictionary->Exists($this->fphp_Table->value . '_' . $a_columns[$i])) {
						$s_sqlType = $o_glob->TablefieldsDictionary->{$this->fphp_Table->value . '_' . $a_columns[$i]}->SqlTypeName;
						$s_forestData = $o_glob->TablefieldsDictionary->{$this->fphp_Table->value . '_' . $a_columns[$i]}->ForestDataName;
						$s_formElement = $o_glob->TablefieldsDictionary->{$this->fphp_Table->value . '_' . $a_columns[$i]}->FormElementName;
						
						/* check if we have a sql datetime column and forestDateTime Object within fphp */
						if ( ($s_sqlType == 'datetime') && ($s_forestData == 'forestObject(&apos;forestDateTime&apos;)') ) {
							if ($s_formElement == 'date') {
								$b_compareDate = true;
							} else if ($s_formElement == 'datetime-local') {
								$b_compareDateTime = true;
							} else if ($s_formElement == 'month') {
								$b_compareMonth = true;
							} else if ($s_formElement == 'time') {
								$b_compareTime = true;
							}
						}
						
						if ($s_forestData == 'forestLookup') {
							$b_compareLookup = true;
						}
					} else {
						/* this one exception, because 'TableUUID' tablefield does not exist for table 'sys_fphp_tablefield' */
						if ( ($this->fphp_Table->value == 'sys_fphp_tablefield') && ($a_columns[$i] == 'TableUUID') ) {
							$b_compareLookup = true;
						}
					}
					
					if ( ($o_left != null) && ($o_right != null) && ($o_left != 'NULL') && ($o_right != 'NULL') ) {
						/* compare not on object level if we have date, datetime, month or time */
						if ($b_compareDate) {
							$o_left = $this->{$a_columns[$i]}->ToString($o_glob->Trunk->DateFormat);
							$o_right = $this->fphp_RecordImage->value->{$a_columns[$i]}->ToString($o_glob->Trunk->DateFormat);
						} else if ($b_compareDateTime) {
							$o_left = $this->{$a_columns[$i]}->ToString($o_glob->Trunk->DateTimeFormat);
							$o_right = $this->fphp_RecordImage->value->{$a_columns[$i]}->ToString($o_glob->Trunk->DateTimeFormat);
						} else if ($b_compareMonth) {
							$o_left = $this->{$a_columns[$i]}->ToString('m');
							$o_right = $this->fphp_RecordImage->value->{$a_columns[$i]}->ToString('m');
						} else if ($b_compareTime) {
							$o_left = $this->{$a_columns[$i]}->ToString($o_glob->Trunk->TimeFormat);
							$o_right = $this->fphp_RecordImage->value->{$a_columns[$i]}->ToString($o_glob->Trunk->TimeFormat);
						} else if ($b_compareLookup) {
							if (! (preg_match('/^ (([0-9])|([a-f])){8} \- (([0-9])|([a-f])){4} \- (([0-9])|([a-f])){4} \- (([0-9])|([a-f])){4} \- (([0-9])|([a-f])){12} $/x', $o_left)) ) {
								$o_left = $this->{$a_columns[$i]}->PrimaryValue;
							}
							
							if (! (preg_match('/^ (([0-9])|([a-f])){8} \- (([0-9])|([a-f])){4} \- (([0-9])|([a-f])){4} \- (([0-9])|([a-f])){4} \- (([0-9])|([a-f])){12} $/x', $o_right)) ) {
								$o_right = $this->fphp_RecordImage->value->{$a_columns[$i]}->PrimaryValue;
							}
						}
					}
					
					if ($o_left != $o_right) {
						$b_uniqueChanged = true;
						$a_unique_constraints[] = $s_unique;
					}
				}
			} else {
				if (!$this->fphp_RecordImage->value->Exists($s_unique)) {
					throw new forestException('Record image does not match with twig object');
				}
				
				$o_left = $this->{$s_unique};
				$o_right = $this->fphp_RecordImage->value->{$s_unique};
				
				$b_compareDate = false;
				$b_compareDateTime = false;
				$b_compareMonth = false;
				$b_compareTime = false;
				
				/* check if tablefield is in dictionary */
				if ($o_glob->TablefieldsDictionary->Exists($this->fphp_Table->value . '_' . $s_unique)) {
					$s_sqlType = $o_glob->TablefieldsDictionary->{$this->fphp_Table->value . '_' . $s_unique}->SqlTypeName;
					$s_forestData = $o_glob->TablefieldsDictionary->{$this->fphp_Table->value . '_' . $s_unique}->ForestDataName;
					$s_formElement = $o_glob->TablefieldsDictionary->{$this->fphp_Table->value . '_' . $s_unique}->FormElementName;
					
					/* check if we have a sql datetime column and forestDateTime object as table field */
					if ( ($s_sqlType == 'datetime') && ($s_forestData == 'forestObject(&apos;forestDateTime&apos;)') ) {
						if ($s_formElement == 'date') {
							$b_compareDate = true;
						} else if ($s_formElement == 'datetime-local') {
							$b_compareDateTime = true;
						} else if ($s_formElement == 'month') {
							$b_compareMonth = true;
						} else if ($s_formElement == 'time') {
							$b_compareTime = true;
						}
					}
				}
				
				/* compare not on object level if we have date, datetime, month or time */
				if ($b_compareDate) {
					$o_left = $this->{$s_unique}->ToString($o_glob->Trunk->DateFormat);
					$o_right = $this->fphp_RecordImage->value->{$s_unique}->ToString($o_glob->Trunk->DateFormat);
				} else if ($b_compareDateTime) {
					$o_left = $this->{$s_unique}->ToString($o_glob->Trunk->DateTimeFormat);
					$o_right = $this->fphp_RecordImage->value->{$s_unique}->ToString($o_glob->Trunk->DateTimeFormat);
				} else if ($b_compareMonth) {
					$o_left = $this->{$s_unique}->ToString('m');
					$o_right = $this->fphp_RecordImage->value->{$s_unique}->ToString('m');
				} else if ($b_compareTime) {
					$o_left = $this->{$s_unique}->ToString($o_glob->Trunk->TimeFormat);
					$o_right = $this->fphp_RecordImage->value->{$s_unique}->ToString($o_glob->Trunk->TimeFormat);
				}
				
				if ($o_left != $o_right) {
					$b_uniqueChanged = true;
					$a_unique_constraints[] = $s_unique;
				}
			}
		}
		
		$a_unique_constraints = array_unique($a_unique_constraints);
		
		/* check unique constraints */
		if ($b_uniqueChanged) {
			foreach ($a_unique_constraints as $s_unique_constraint) {
				/* primary key collision already detected */
				if ($i_return > 0) {
					break;
				}
				
				/* ignore unique constraints which consists only of column UUID */
				if ($s_unique_constraint == 'UUID') {
					continue;
				}
				
				$a_sqlAdditionalFilter = array();
				
				/* it is possible that a unique constraint exists of multiple columns, separated by semicolon */
				if (strpos($s_unique_constraint, ';') !== false) {
					$a_columns = explode(';', $s_unique_constraint);
					
					for ($i = 0; $i < count($a_columns); $i++) {
						$a_sqlAdditionalFilter[] = array('column' => $a_columns[$i], 'value' => $this->{$a_columns[$i]}, 'operator' => '=', 'filterOperator' => 'AND');
					}
				} else {
					$a_sqlAdditionalFilter[] = array('column' => $s_unique_constraint, 'value' => $this->{$s_unique_constraint}, 'operator' => '=', 'filterOperator' => 'AND');
				}
				
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$i_return = $this->GetCount(null, true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				
				if ($i_return > 0) {
					$o_glob->Temp->Add('UNIQUE ISSUE - unique constraint invalid for [' . $s_unique_constraint . '] in table [' . $this->fphp_Table->value . ']; unique key already exists', 'UniqueIssue');
				}
			}
		}
		
		return $i_return;
	}
	
	
	/**
	 * delete record in table
	 *
	 * @return integer  0 - could not delete record, 1 - record deletion successful
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function DeleteRecord() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* create delete query */
		$o_queryDelete = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::REMOVE, $this->fphp_Table->value);
		
		if ($this->fphp_HasUUID->value) {
			/* if twig object use uuid, use it as update filter */
			$o_column = new \fPHP\Base\forestSQLColumn($o_queryDelete);
				$o_column->Column = 'UUID';
			
			$o_where = new \fPHP\Base\forestSQLWhere($o_queryDelete);
				$o_where->Column = $o_column;
				$o_where->Value = $o_where->ParseValue($this->{'UUID'});
				$o_where->Operator = '=';
			
			$o_queryDelete->Query->Where->Add($o_where);
		} else {
			$b_initWhere = false;
			
			/* else take primary key fields for the update filter */
			foreach ($this->fphp_Primary->value as $s_primary) {
				$o_column = new \fPHP\Base\forestSQLColumn($o_queryDelete);
				$o_column->Column = $s_primary;
			
				$o_where = new \fPHP\Base\forestSQLWhere($o_queryDelete);
					$o_where->Column = $o_column;
					$o_where->Value = $o_where->ParseValue($this->{$s_primary});
					$o_where->Operator = '=';
					
					if ($b_initWhere) {
						$o_where->FilterOperator = 'AND';
					} else {
						$b_initWhere = true;
					}
				
				$o_queryDelete->Query->Where->Add($o_where);
			}
		}
		
		return $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryDelete);
	}
	
	
	/**
	 * truncate table
	 *
	 * @return integer  0 - could not truncate table, 1 - truncate of table successful
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function TruncateTable() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* create truncate query */
		$o_queryTruncate = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::TRUNCATE, $this->fphp_Table->value);
		
		return $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryTruncate);
	}
}
?>