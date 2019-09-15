<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.1.3 (0x1 0000C)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * abstract class for all twig objects, one twig represents a table in a database
 * all necessary methods to read or manipulate data are implemented in this class
 * and can be used with every twig-object that stays for one table in the database
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.0 alpha	renatus		2019-08-04	first build
 * 0.1.1 alpha	renatus		2019-08-10	added tablefield caching
 * 0.1.2 alpha	renatus		2019-08-27	added sort and limit
 */

abstract class forestTwig {
	use forestData;
	
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
	
	/* Properties */
	
	/* Methods */
	
	public function __construct($p_a_record = array(), $p_s_resultType = forestBase::ASSOC) {
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
		
		$o_glob = forestGlobals::Init();
		
		if (!is_array($p_a_record)) {
			throw new forestException('Parameter record is not an array');
		}
		
		$this->init();
		
		if (!issetStr($this->fphp_Table->value)) {
			throw new forestException('Twig needs a table value');
		}
		
		if (forestStringLib::StartsWith($this->fphp_Table->value, 'sys_fphp_')) {
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
		
		if (count($this->fphp_View->value) == 0) {
			throw new forestException('View values are missing table[%0]', array($this->fphp_Table->value));
		}
		
		foreach ($this->fphp_View->value as $s_view_field) {
			if ( (!in_array($s_view_field, $this->fphp_Mapping->value)) && (!$o_glob->FastProcessing) ) {
				if (!$o_glob->TablefieldsDictionary->Exists($this->fphp_Table->value . '_' . $s_view_field)) {
					throw new forestException('View field[%0] does not exists in mapping', array($s_view_field));
				}
			}
		}
		
		/* load record data into class fields */
		/* difference between numeric or named index of record data */
		switch ($p_s_resultType) {
			case forestBase::ASSOC:
			case forestBase::BOTH:
				foreach($p_a_record as $s_key => $s_value) {
					if (!in_array($s_key, $this->fphp_Mapping->value)) {
						throw new forestException('Field[%0] does not exists in mapping', array($s_key, implode(',', $this->fphp_Mapping->value)));
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
			case forestBase::NUM:
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
				throw new forestException('Result type[%0] not implemented', array($p_s_resultType));
			break;
		}
	}
	
	abstract protected function init();
	
	/* cache table fields properties entries in dictionary in forestGlobals */
	private function CacheTableFieldsProperties() {
		$o_glob = forestGlobals::Init();
		
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
			$o_tablefieldTwig = new tablefieldTwig;
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
							
							$o_tableFieldProperties = new forestTableFieldProperties(
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
							
							$o_tableFieldProperties = new forestTableFieldProperties(
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
	
	/* execute sql query to get field properties */
	public static function QueryFieldProperties($p_s_tableUUID, $p_s_field) {
		$o_glob = forestGlobals::Init();
		
		$o_querySelect = new forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, forestSQLQuery::SELECT, 'sys_fphp_tablefield');
				
			$column_A = new forestSQLColumn($o_querySelect);
				$column_A->Column = 'FieldName';
			
			$column_B = new forestSQLColumn($o_querySelect);
				$column_B->Column = 'UUID';
				$column_B->Name = 'TableFieldUUID';
			
			$column_C = new forestSQLColumn($o_querySelect);
				$column_C->Column = 'TabId';
				$column_C->Name = 'TableFieldTabId';
				
			$column_D = new forestSQLColumn($o_querySelect);
				$column_D->Column = 'JSONEncodedSettings';
				$column_D->Name = 'TableFieldJSONEncodedSettings';
			
			$column_E = new forestSQLColumn($o_querySelect);
				$column_E->Column = 'FooterElement';
				$column_E->Name = 'TableFieldFooterElement';
			
			$column_T = new forestSQLColumn($o_querySelect);
				$column_T->Column = 'SubRecordField';
				$column_T->Name = 'TableFieldSubRecordField';
				
			$column_U = new forestSQLColumn($o_querySelect);
				$column_U->Column = 'SubRecordField';
				$column_U->Name = 'TableFieldOrder';
			
			$column_F = new forestSQLColumn($o_querySelect);
				$column_F->Column = 'FormElementUUID';
				$column_F->Name = 'FormElementUUID';
			
			$column_G = new forestSQLColumn($o_querySelect);
				$column_G->Table = 'sys_fphp_formelement';
				$column_G->Column = 'Name';
				$column_G->Name = 'FormElementName';
			
			$column_H = new forestSQLColumn($o_querySelect);
				$column_H->Table = 'sys_fphp_formelement';
				$column_H->Column = 'JSONEncodedSettings';
				$column_H->Name = 'FormElementJSONEncodedSettings';
				
			$column_I = new forestSQLColumn($o_querySelect);
				$column_I->Column = 'SqlTypeUUID';
				$column_I->Name = 'SqlTypeUUID';
			
			$column_J = new forestSQLColumn($o_querySelect);
				$column_J->Table = 'sys_fphp_sqltype';
				$column_J->Column = 'Name';
				$column_J->Name = 'SqlTypeName';
			
			$column_K = new forestSQLColumn($o_querySelect);
				$column_K->Column = 'ForestDataUUID';
				$column_K->Name = 'ForestDataUUID';
			
			$column_L = new forestSQLColumn($o_querySelect);
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
		$join_A = new forestSQLJoin($o_querySelect);
		$join_A->JoinType = 'INNER JOIN';
		$join_A->Table = 'sys_fphp_formelement';
			
			$relation_A = new forestSQLRelation($o_querySelect);
			
				$column_M = new forestSQLColumn($o_querySelect);
					$column_M->Column = 'FormElementUUID';
				
				$column_N = new forestSQLColumn($o_querySelect);
					$column_N->Table = $join_A->Table;
					$column_N->Column = 'UUID';
			
			$relation_A->ColumnLeft = $column_M;
			$relation_A->ColumnRight = $column_N;
			$relation_A->Operator = '=';
		
		$join_A->Relations->Add($relation_A);
		/* left join with sqltype table */
		$join_B = new forestSQLJoin($o_querySelect);
		$join_B->JoinType = 'LEFT OUTER JOIN';
		$join_B->Table = 'sys_fphp_sqltype';
		
			$relation_B = new forestSQLRelation($o_querySelect);
			
			$column_O = new forestSQLColumn($o_querySelect);
				$column_O->Column = 'SqlTypeUUID';
				
			$column_P = new forestSQLColumn($o_querySelect);
				$column_P->Table = $join_B->Table;
				$column_P->Column = 'UUID';
			
			$relation_B->ColumnLeft = $column_O;
			$relation_B->ColumnRight = $column_P;
			$relation_B->Operator = '=';
		
		$join_B->Relations->Add($relation_B);
		/* left join with forestdata table */
		$join_C = new forestSQLJoin($o_querySelect);
		$join_C->JoinType = 'LEFT OUTER JOIN';
		$join_C->Table = 'sys_fphp_forestdata';
		
			$relation_C = new forestSQLRelation($o_querySelect);
			
			$column_Q = new forestSQLColumn($o_querySelect);
				$column_Q->Column = 'ForestDataUUID';
			
			$column_R = new forestSQLColumn($o_querySelect);
				$column_R->Table = $join_C->Table;
				$column_R->Column = 'UUID';
			
			$relation_C->ColumnLeft = $column_Q;
			$relation_C->ColumnRight = $column_R;
			$relation_C->Operator = '=';
		
		$join_C->Relations->Add($relation_C);
		
		$o_querySelect->Query->Joins->Add($join_A);
		$o_querySelect->Query->Joins->Add($join_B);
		$o_querySelect->Query->Joins->Add($join_C);
		
		$column_S = new forestSQLColumn($o_querySelect);
			$column_S->Column = 'TableUUID';
		
		/* filter by table-uuid and field-name */
		$where_A = new forestSQLWhere($o_querySelect);
			$where_A->Column = $column_S;
			$where_A->Value = $where_A->ParseValue($p_s_tableUUID);
			$where_A->Operator = '=';
			
		$where_B = new forestSQLWhere($o_querySelect);
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
	
	/* fill Mapping array from forestTwig instance */
	protected function fphp_FillMapping(array $p_a_object_vars) {
		foreach ($p_a_object_vars as $s_key => $s_value) {
			/* do not add fphp system fields of forestTwig class */
			if (forestStringLib::StartsWith($s_key, 'fphp_')) {
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
	
	
	/* general method to get property field value, for sub records as well as for combination fields */
	public function GetFieldValue($p_s_name) {
		$o_glob = forestGlobals::init();
		$o_value = null;
		
		foreach ($o_glob->TablefieldsDictionary as $o_tableFieldProperties) {
			/* load json settings to compare name parameter with id setting */
			$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_tableFieldProperties->JSONEncodedSettings);
			$a_settings = json_decode($s_JSONEncodedSettings, true);
			
			if (!empty($a_settings)) {
				if (array_key_exists('Id', $a_settings)) {
					if ($a_settings['Id'] == $this->fphp_Table->value . '_' . $p_s_name) {
						$o_value = $this->{$o_tableFieldProperties->FieldName};
						break;
					}
				}
			}
		}
		
		return $o_value;
	}
	
	/* general method to set property field value, for sub records as well */
	public function SetFieldValue($p_s_name, $p_o_value) {
		$o_glob = forestGlobals::init();
		
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
	
	/* if twig object is not empty, field value will be restored using record image */
	public function RestoreFieldValue($p_s_name) {
		$o_glob = forestGlobals::init();
		
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
	
	/* get record with values of primary key */
	public function GetRecord(array $p_a_primaryValues) {
		$o_glob = forestGlobals::init();
		
		if (count($p_a_primaryValues) != count($this->fphp_Primary->value)) {
			throw new forestException('Primary input values[%0] and primary fields[%1] are not of the same amount', array(count($p_a_primaryValues), count($this->fphp_Primary->value)));
		}
		
		/* create select query */
		$o_querySelect = new forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, forestSQLQuery::SELECT, $this->fphp_Table->value);
			
		$o_column = new forestSQLColumn($o_querySelect);
			$o_column->Column = '*';
			
		$o_querySelect->Query->Columns->Add($o_column);
		
		/* if parameter array only has one value, record structure has column UUID and parameter value pattern matches a UUID */
		if ( (count($p_a_primaryValues) == 1) && ($this->fphp_HasUUID->value) && (preg_match('/^ (([0-9])|([a-f])){8} \- (([0-9])|([a-f])){4} \- (([0-9])|([a-f])){4} \- (([0-9])|([a-f])){4} \- (([0-9])|([a-f])){12} $/x', $p_a_primaryValues[0])) ) {
			$o_column = new forestSQLColumn($o_querySelect);
				$o_column->Column = 'UUID';
			
			if ( ($this->fphp_Primary->value[0] != 'Id') && ($this->fphp_Primary->value[0] != 'UUID') ) {
				$o_column->Column = $this->fphp_Primary->value[0];
			}
			
			$o_where = new forestSQLWhere($o_querySelect);
				$o_where->Column = $o_column;
				$o_where->Value = $o_where->ParseValue($p_a_primaryValues[0]);
				$o_where->Operator = '=';
				
			$o_querySelect->Query->Where->Add($o_where);
		} else {
			/* go with primary key */
			for ($i = 0; $i < count($this->fphp_Primary->value); $i++) {
				$o_column = new forestSQLColumn($o_querySelect);
					$o_column->Column = $this->fphp_Primary->value[$i];
				
				$o_where = new forestSQLWhere($o_querySelect);
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
	
	/* fill fields from other twig object */
	private function fphp_FillFieldsFromOtherTwigObject($p_o_twig) {
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
	
	/* determine if current twig object is empty */
	public function IsEmpty() {
		return ($this->fphp_RecordImage->value->Count() <= 0);
	}
	
	/* gets record with values of temporary other primary key */
	public function GetRecordPrimary(array $p_a_primaryValues, array $p_a_primaryKeys) {
		$a_bkp_primary = $this->fphp_Primary->value;
		$this->fphp_Primary->value = $p_a_primaryKeys;
		
		$b_ret = $this->GetRecord($p_a_primaryValues);
		
		$this->fphp_Primary->value = $a_bkp_primary;
		
		return $b_ret;
	}
	
	/* showing all data fields of current twig object for log purposes */
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
	
	/* get first record */
	public function GetFirstRecord() {
		$o_glob = forestGlobals::init();
		
		/* create select query */
		$o_querySelect = new forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, forestSQLQuery::SELECT, $this->fphp_Table->value);
			
		$o_column = new forestSQLColumn($o_querySelect);
			$o_column->Column = '*';
				
		$o_querySelect->Query->Columns->Add($o_column);
		
		/* add additional sql filter clauses */
		$this->ImplementAdditionalSQLFilter($o_querySelect);
		
		/* set order into select query */
		foreach($this->fphp_SortOrder->value as $s_sortColumn => $b_sortDirection) {
			$o_column = new forestSQLColumn($o_querySelect);
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
	
	/* get last record */
	public function GetLastRecord() {
		$o_glob = forestGlobals::init();
		
		/* create select query */
		$o_querySelect = new forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, forestSQLQuery::SELECT, $this->fphp_Table->value);
			
		$o_column = new forestSQLColumn($o_querySelect);
			$o_column->Column = '*';
				
		$o_querySelect->Query->Columns->Add($o_column);
		
		/* add additional sql filter clauses */
		$this->ImplementAdditionalSQLFilter($o_querySelect);
		
		/* set order into select query with reversed direction */
		foreach($this->fphp_SortOrder->value as $s_sortColumn => $b_sortDirection) {
			$o_column = new forestSQLColumn($o_querySelect);
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
	
	/* get count of another table or same table */
	public function GetCount($p_s_table = null, $p_b_unlimited = false, $p_b_updateLimitAmount = false) {
		$o_glob = forestGlobals::init();
		
		$s_table = $this->fphp_Table->value;
		
		if ($p_s_table != null) {
			$s_table = $p_s_table;
		}
		
		/* create query for amount of records */
		$o_querySelect = new forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, forestSQLQuery::SELECT, $s_table);
			
		$o_column = new forestSQLColumn($o_querySelect);
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
	
	
	/* implement filter values of session parameter array */
	private function ImplementFilter(forestSQLQuery &$p_o_query, $p_b_unlimited) {
		$o_glob = forestGlobals::init();
		$a_session_filter = array();
		
		/* check if we have a select query object as parameter */
		if ($p_o_query->SqlType != forestSQLQuery::SELECT) {
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
							$o_columnFoo = new forestSQLColumn($p_o_query);
								$o_columnFoo->Column = $s_column;
							
							$o_where = new forestSQLWhere($p_o_query);
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
					$a_clauses = forestStringLib::SplitFilter($s_filterValue);
					
					/* create column object for the where clause */
					if (strpos($s_column, '.') !== false) {
						$a_columnInfo = explode('.', $s_column);
						
						$o_columnFoo = new forestSQLColumn($p_o_query);
							$o_columnFoo->Table = $a_columnInfo[0];
							$o_columnFoo->Column = $a_columnInfo[1];
					} else {
						$o_columnFoo = new forestSQLColumn($p_o_query);
							$o_columnFoo->Column = $s_column;
					}
					
					/* generate where clause for each clause we got */
					$s_lastfilterOperator = null;
					
					if ($b_initWhere) {
						$s_lastfilterOperator = 'AND';
					}
					
					foreach($a_clauses as $a_clause) {
						$o_where = new forestSQLWhere($p_o_query);
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
				}
			}
		}
		
		$this->ImplementAdditionalSQLFilter($p_o_query, $b_initWhere);
	}
	
	/* function to implement additional sql filter */
	private function ImplementAdditionalSQLFilter(forestSQLQuery &$p_o_query, &$p_b_initWhere = false) {
		$o_glob = forestGlobals::init();
		
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
					
					$o_columnFoo = new forestSQLColumn($p_o_query);
						$o_columnFoo->Table = $a_columnInfo[0];
						$o_columnFoo->Column = $a_columnInfo[1];
				} else {
					$o_columnFoo = new forestSQLColumn($p_o_query);
						$o_columnFoo->Column = $a_filter['column'];
				}
				
				$o_where = new forestSQLWhere($p_o_query);
					$o_where->Column = $o_columnFoo;
					$o_where->Value = $o_where->ParseValue($a_filter['value']);
					$o_where->Operator = $a_filter['operator'];
					
					$o_where->BracketStart = array_key_exists('bracket_start', $a_filter);
					$o_where->BracketEnd = array_key_exists('bracket_end', $a_filter);
					
					if ($p_b_initWhere) {
						$o_where->FilterOperator = $a_filter['filterOperator'];
					}
				
				$p_o_query->Query->Where->Add($o_where);
				
				$p_b_initWhere = true;
			}
		}
	}
	
	/* get all records, depending on filter and limit parameters */
	public function GetAllRecords($p_b_unlimited = false) {
		$o_glob = forestGlobals::init();
		
		/* calculate amount of records */
		$i_amount_records = $this->GetCount(null, $p_b_unlimited, true);
		
		/* create select query */
		$o_querySelect = new forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, forestSQLQuery::SELECT, $this->fphp_Table->value);
		
		/* adding optional columns for the query if they are set */
		if ($o_glob->Temp->Exists('SQLGetAllAdditionalColumns')) {
			foreach($o_glob->Temp->{'SQLGetAllAdditionalColumns'} as $o_additionalColumn) {
				$o_querySelect->Query->Columns->Add($o_additionalColumn);
			}
		} else {
			/* we want to query all columns */
			$o_column = new forestSQLColumn($o_querySelect);
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
				
				$o_column = new forestSQLColumn($o_querySelect);
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
				
				$o_column = new forestSQLColumn($o_querySelect);
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
				
				$o_column = new forestSQLColumn($o_querySelect);
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
	
	/* get records based on view columns, depending on filter and limit parameters */
	public function GetAllViewRecords($p_b_unlimited = false) {
		$o_glob = forestGlobals::init();
		
		/* create select query */
		$o_querySelect = new forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, forestSQLQuery::SELECT, $this->fphp_Table->value);
		
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
				$o_columnId = new forestSQLColumn($o_querySelect);
					$o_columnId->Column = 'Id';
							
				$a_sqlGetAllAdditionalColumns[] = $o_columnId;
			}
			
			if ($this->fphp_HasUUID->value) {
				$o_columnUUID = new forestSQLColumn($o_querySelect);
						$o_columnUUID->Column = 'UUID';
						
				$a_sqlGetAllAdditionalColumns[] = $o_columnUUID;
			}
			
			foreach ($this->fphp_View->value as $s_view_field) {
				if ( ($s_view_field == 'Id') || ($s_view_field == 'UUID') || (!in_array($s_view_field, $this->fphp_Mapping->value)) ) {
					continue;
				}
				
				$o_columnFoo = new forestSQLColumn($o_querySelect);
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
	
	
	/* insert record into table, primary key fields optional */
	public function InsertRecord($p_b_withPrimary = false) {
		$o_glob = forestGlobals::init();
		
		/* check uniqueness */
		if ($this->CheckUniquenessInsert($p_b_withPrimary) > 0) {
			return -1;
		}
		
		/* create insert query */
		$o_queryInsert = new forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, forestSQLQuery::INSERT, $this->fphp_Table->value);
		
		/* read out twig fields to get values for insert query */
		foreach ($this->fphp_Mapping->value as $s_field) {
			if ((!in_array($s_field, $this->fphp_Primary->value)) || ($p_b_withPrimary)) {
				/* set new UUID automatically */
				if ($s_field == 'UUID') {
					/* if not UUID has been already set */
					if (!issetStr($this->{$s_field})) {
						$this->{$s_field} = $this->GetUUID();
					}
				}
				
				$o_columnValue = new forestSQLColumnValue($o_queryInsert);
					$o_columnValue->Column = $s_field;
					$o_columnValue->Value = $o_columnValue->ParseValue($this->{$s_field});
				
				$o_queryInsert->Query->ColumnValues->Add($o_columnValue);
			}
		}
		
		$i_return = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryInsert);
		
		if ( ($i_return > 0) && (in_array('Id', $this->fphp_Mapping->value)) ) {
			$this->Id = $o_glob->Base->{$o_glob->ActiveBase}->LastInsertId();
		}
		
		return $i_return;
	}
	
	/* check uniqueness of twig object within table for insert query */
	private function CheckUniquenessInsert($p_b_withPrimary = false) {
		$o_glob = forestGlobals::init();
		
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
	
	/* get unused uuid of table for insert query */
	private function GetUUID() {
		$o_glob = forestGlobals::init();
		
		/* generate new uuid */
		$s_uuid = $o_glob->Security->GenUUID();
		
		/* we can deactivate checking for unique uuid, because it can be kind of long depending how many records exists in the table */
		if ($o_glob->Trunk->CheckUniqueUUID) {
			if (!in_array('UUID', $this->fphp_Mapping->value)) {
				throw new forestException('Field UUID does not exists in twig object');
			}
		
			do {
				/* create select query for counting records with generated uuid to see if a record already exists with that uuid */
				$o_querySelect = new forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, forestSQLQuery::SELECT, $this->fphp_Table->value);
					$o_column = new forestSQLColumn($o_querySelect);
						$o_column->Column = 'UUID';
						$o_column->Name = 'UUID';
						$o_column->SqlAggregation = 'COUNT';
					
					$o_querySelect->Query->Columns->Add($o_column);
					
					$o_column = new forestSQLColumn($o_querySelect);
						$o_column->Column = 'UUID';
				
					$o_where = new forestSQLWhere($o_querySelect);
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
	
	
	/* update record in table, you cannot modify primary key, modify unique fields optional */
	public function UpdateRecord($p_b_modifyUnique = true) {
		$o_glob = forestGlobals::init();
		$b_field_has_changed = false;
		$a_columns = array();
		
		if ($o_glob->Temp->Exists('SQLUpdateColumns')) {
			$a_columns = $o_glob->Temp->{'SQLUpdateColumns'};
		} else {
			$a_columns = $this->fphp_Mapping->value;
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
		$o_queryUpdate = new forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, forestSQLQuery::UPDATE, $this->fphp_Table->value);
		
		/* read out twig fields to get values for insert query */
		foreach ($a_columns as $s_field) {
			if ( (!in_array($s_field, $this->fphp_Primary->value)) && ( ($p_b_modifyUnique) || (!in_array($s_field, $this->fphp_Unique->value)) ) ) {
				$o_columnValue = new forestSQLColumnValue($o_queryUpdate);
					$o_columnValue->Column = $s_field;
					$o_columnValue->Value = $o_columnValue->ParseValue($this->{$s_field});
				
				$o_queryUpdate->Query->ColumnValues->Add($o_columnValue);
			}
		}
			
		/* if twig object use uuid, use it as update filter */
		if ($this->fphp_HasUUID->value) {
			$o_column = new forestSQLColumn($o_queryUpdate);
				$o_column->Column = 'UUID';
			
			$o_where = new forestSQLWhere($o_queryUpdate);
				$o_where->Column = $o_column;
				$o_where->Value = $o_where->ParseValue($this->{'UUID'});
				$o_where->Operator = '=';
			
			$o_queryUpdate->Query->Where->Add($o_where);
		} else {
			$i = 0;
			
			/* else take primary key fields for the update filter */
			foreach ($this->fphp_Primary->value as $s_primary) {
				$o_column = new forestSQLColumn($o_queryUpdate);
				$o_column->Column = $s_primary;
			
				$o_where = new forestSQLWhere($o_queryUpdate);
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
	
	/* check uniqueness of twig object within table for update query */
	private function CheckUniquenessUpdate($p_b_withPrimary = false) {
		$o_glob = forestGlobals::init();
		
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
					
					if ($this->{$a_columns[$i]} != $this->fphp_RecordImage->value->{$a_columns[$i]}) {
						$b_uniqueChanged = true;
						$a_unique_constraints[] = $s_unique;
					}
				}
			} else {
				if (!$this->fphp_RecordImage->value->Exists($s_unique)) {
					throw new forestException('Record image does not match with twig object');
				}
				
				if ($this->{$s_unique} != $this->fphp_RecordImage->value->{$s_unique}) {
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
	
	
	/* delete record in table */
	public function DeleteRecord() {
		$o_glob = forestGlobals::init();
		
		/* create delete query */
		$o_queryDelete = new forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, forestSQLQuery::DELETE, $this->fphp_Table->value);
			
		if ($this->fphp_HasUUID->value) {
			/* if twig object use uuid, use it as update filter */
			$o_column = new forestSQLColumn($o_queryDelete);
				$o_column->Column = 'UUID';
			
			$o_where = new forestSQLWhere($o_queryDelete);
				$o_where->Column = $o_column;
				$o_where->Value = $o_where->ParseValue($this->{'UUID'});
				$o_where->Operator = '=';
			
			$o_queryDelete->Query->Where->Add($o_where);
		} else {
			$b_initWhere = false;
			
			/* else take primary key fields for the update filter */
			foreach ($this->fphp_Primary->value as $s_primary) {
				$o_column = new forestSQLColumn($o_queryDelete);
				$o_column->Column = $s_primary;
			
				$o_where = new forestSQLWhere($o_queryDelete);
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
	
	
	/* truncates table */
	public function TruncateTable() {
		$o_glob = forestGlobals::init();
		
		/* create truncate query */
		$o_queryTruncate = new forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, forestSQLQuery::TRUNCATE, $this->fphp_Table->value);
		
		return $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryTruncate);
	}
}
?>