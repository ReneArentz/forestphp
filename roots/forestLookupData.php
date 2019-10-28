<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.2.0 (0x1 0000F)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * class to capsulate lookup result data
 * storing these objects in a global dictionary will reduce database record access for one session
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.0 alpha	renatus		2019-10-02	added to framework
 */

class forestLookupData {
	use forestData;
	
	/* Fields */
	
	private $Table;
	private $Primary;
	private $PrimaryValue;
	private $Label;
	private $Filter;
	private $Concat;
	
	/* Properties */
		
	/* Methods */
	
	public function __construct($p_s_table, array $p_a_primary, array $p_a_label, array $p_a_filter = array(), $p_s_concat = ' - ') {
		/* some construct parameters must have a value */
		if (empty($p_s_table)) {
			throw new forestException('Table parameter needed');
		}
		
		if (empty($p_a_primary)) {
			throw new forestException('Primary array parameter needed');
		}
		
		if (empty($p_a_label)) {
			throw new forestException('Label array parameter needed');
		}
		
		/* take over construct parameters */
		$this->Table = new forestString($p_s_table, false);
		$this->Primary = new forestArray($p_a_primary, false);
		$this->PrimaryValue = new forestString;
		$this->Label = new forestArray($p_a_label, false);
		$this->Filter = new forestArray($p_a_filter, false);
		$this->Concat = new forestString($p_s_concat);
	}
	
	public function __call($p_s_functionName, $p_a_arguments) {
		/* function call to overwrite lookup data */
		if ( ($p_s_functionName == 'SetLookupData') && (is_a($p_a_arguments[0], 'forestLookupData')) ) {
			$this->Table->value = $p_a_arguments[0]->Table->value;
			$this->Primary->value = $p_a_arguments[0]->Primary->value;
			$this->Label->value = $p_a_arguments[0]->Label->value;
			$this->Filter->value = $p_a_arguments[0]->Filter->value;
		}
	}
	
	public function __toString() {
		$o_glob = forestGlobals::init();
		
		$s_foo = '';
		
		/* calculate primary value for control use */
		if ( (issetStr($this->Table->value)) && (issetStr($this->PrimaryValue->value)) ) {
			/* look in global dictionary for primary value */
			if ($o_glob->LookupResultsDictionary->Exists($this->Table->value . '_' . implode('_', explode(';', $this->PrimaryValue->value)))) {
				/* global dictionary key exists, we do not have to query the result */
				$s_foo = $o_glob->LookupResultsDictionary->{$this->Table->value . '_' . implode('_', explode(';', $this->PrimaryValue->value))};
			} else {
				/* get table */
				$o_tableTwig = new tableTwig;
				
				/* get table record */
				if (!($o_tableTwig->GetRecordPrimary(array($this->Table->value), array('Name')))) {
					$s_foo = 'table_not_found';
				} else {
					/* create twig object by table record */
					$s_twigName = $o_tableTwig->Name . 'Twig';
					forestStringLib::RemoveTablePrefix($s_twigName);
					$o_twig = new $s_twigName;
					
					/* query record based on twig object and stored primary values */
					if (!$o_twig->GetRecordPrimary(explode(';', $this->PrimaryValue->value), $this->Primary->value)) {
						$s_foo = 'record_not_found_with_primary';
					} else {
						/* get label of queried record to display lookup value; multiple labels possible */
						for ($i = 0; $i < count($this->Label->value); $i++) {
							$s_foo .= $o_twig->{$this->Label->value[$i]};
							
							if ($i < (count($this->Label->value) - 1)) {
								$s_foo .= $this->Concat->value;
							}
						}
						
						/* add result to global dicitionary */
						$o_glob->LookupResultsDictionary->Add($s_foo, $this->Table->value . '_' . implode('_', explode(';', $this->PrimaryValue->value)));
					}
				}
			}
		}
		
		return $s_foo;
	}
	
	/* this function is important to use lookup data with web select control or other option based controls */
	public function CreateOptionsArray() {
		$o_glob = forestGlobals::init();
		
		$a_options = array();
		
		/* get table */
		$o_tableTwig = new tableTwig;
		
		/* get table record */
		if (!($o_tableTwig->GetRecordPrimary(array($this->Table->value), array('Name')))) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		/* create twig object by table record */
		$s_twigName = $o_tableTwig->Name . 'Twig';
		forestStringLib::RemoveTablePrefix($s_twigName);
		$o_twig = new $s_twigName;
		
		/* create filter */
		$a_filter = $this->Filter->value;
		$a_sqlAdditionalFilter = array();
		
		if (count($a_filter) > 0) {
			foreach ($a_filter as $s_field => $s_value) {
				$s_operator = '=';
				$s_filterOperator = 'AND';
				
				if (forestStringLib::StartsWith($s_field, '!')) {
					$s_field = substr($s_field, 2);
					$s_operator = '<>';
					$s_filterOperator = 'AND';
				}
				
				$a_sqlAdditionalFilter[] = array('column' => $s_field, 'value' => $s_value, 'operator' => $s_operator, 'filterOperator' => $s_filterOperator);
			}
		}
		
		/* get all records of configured twig */
		if (count($a_sqlAdditionalFilter) > 0) {
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		}
		
		$o_records = $o_twig->GetAllRecords(true);
		
		if (count($a_sqlAdditionalFilter) > 0) {
			$o_glob->Temp->Del('SQLAdditionalFilter');
		}
		
		if ($o_records->Twigs->Count() > 0) {
			/* store each record primary in array */
			foreach ($o_records->Twigs as $o_record) {
				$s_option_key = '';
				$s_option = '';
				
				if ($o_twig->fphp_HasUUID) {
					$s_option_key = $o_record->UUID;
				} else {
					$s_option_key = $o_record->Id;
				}
				
				/* get label of queried record to display lookup value; multiple labels possible */
				for ($i = 0; $i < count($this->Label->value); $i++) {
					$s_option .= $o_record->{$this->Label->value[$i]};
					
					if ($i < (count($this->Label->value) - 1)) {
						$s_option .= $this->Concat->value;
					}
				}
				
				/* add record label and record primary key to array which will be returned by this function */
				$a_options[$s_option] = $s_option_key;
			}
		}
		
		return $a_options;
	}
}	
?>