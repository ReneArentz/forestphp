<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.5.0 (0x1 00015)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * class-collection for all necessary form data and settings
 * multiple tabs are possible for rendering, also modal form view is supported
 * most settings are based on json encoded strings stored in configuration files or in database records
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.1 alpha	renatus		2019-08-09	added to framework
 * 0.1.3 alpha	renatus		2019-09-06	added formkey and validationrules
 * 0.1.4 alpha	renatus		2019-09-23	added dropzone and richtext
 * 0.1.5 alpha	renatus		2019-10-04	added forestLookup
 * 0.1.5 alpha	renatus		2019-10-05	added forestCombination and Captcha
 * 0.5.0 beta	renatus		2019-12-02	added honeypot fields functionality
 * 0.5.0 beta	renatus		2019-12-04	added auto checkin question
 */

class forestForm {
	use forestData;
	
	/* Fields */
	
	private $Automatic;
	private $Readonly;
	private $PrintFooter;
	private $FormObject;
	private $FormElements;
	private $FormModalConfiguration;
	private $FormTabConfiguration;
	private $FormTabs;
	private $FormModalSubForm;
	private $FormFooterElements;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct(forestTwig $p_o_twig, $p_b_automatic = false, $p_b_readonly = false, $p_b_printFooter = true) {
		$this->Automatic = new forestBool($p_b_automatic);
		$this->Readonly = new forestBool($p_b_readonly);
		$this->PrintFooter = new forestBool($p_b_printFooter);
		$this->FormObject = new forestObject(new forestFormElement(forestFormElement::FORM), false);
		$this->FormElements = new forestObject(new forestObjectList('forestFormElement'), false);
		$this->FormModalConfiguration = new forestObject(new forestModalConfiguration, false);
		$this->FormTabConfiguration = new forestObject(new forestTabConfiguration, false);
		$this->FormTabs = new forestObject(new forestObjectList('forestFormTab'), false);
		$this->FormModalSubForm = new forestString;
		$this->FormFooterElements = new forestObject(new forestObjectList('forestFormElement'), false);
		
		$o_glob = forestGlobals::init();
		
		if ($p_b_automatic) {
			/* get table */
			$o_tableTwig = new tableTwig;
			
			/* query table record */
			if (!($o_tableTwig->GetRecordPrimary(array($p_o_twig->fphp_Table), array('Name')))) {
				throw new forestException(0x10001401, array($p_o_twig->fphp_Table));
			}
			
			/* get formobject element */
			$o_formelementTwig = new formelementTwig;
			
			/* look in tablefields for formobject, if not get the standard by formelementuuid */
			if (!($o_formelementTwig->GetRecordPrimary(array(forestFormElement::FORM), array('Name')))) {
				throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
			}
		
			$o_tablefieldTwig = new tablefieldTwig;
			
			if (!($o_tablefieldTwig->GetRecordPrimary(array($o_tableTwig->UUID, $o_formelementTwig->UUID), array('TableUUID', 'FormElementUUID')))) {
				/* no tablefield for table, take standard */
				$s_formObjectJSONsettings = $o_formelementTwig->JSONEncodedSettings;
			} else {
				$s_formObjectJSONsettings = $o_tablefieldTwig->JSONEncodedSettings;
			}
			
			/*$s_json = '
			{
				"FormTabConfiguration": {
					"Tab" : true,
					"TabMenuClass" : "nav nav-tabs",
					"TabActiveClass" : "active",
					"TabToggle" : "tab",
					"TabContentClass" : "tab-content",
					"TabFooterClass" : "tab-footer",
					"TabElementClass" : "tab-pane fade",
					"TabElementActiveClass" : "tab-pane fade in active",
					"TabsInfo" : [
						{"TabId" : "general", "TabTitle" : "General"}
					]
				},
				
				"FormModalConfiguration" : {
					"Modal" : true,
					"ModalClass" : "modal fade",
					"ModalId" : "myModal",
					"ModalTitle" : "<h4>Modal Form HTML Validation</h4>",
					"ModalRole" : "dialog",
					"ModalDialogClass" : "modal-dialog modal-lg",
					"ModalDialogContentClass" : "modal-content",
					"ModalHeaderClass" : "modal-header",
					"ModalHeaderCloseClass" : "close",
					"ModalHeaderDismissClass" : "modal",
					"ModalHeaderCloseContent" : "&times;",
					"ModalBodyClass" : "modal-body",
					"ModalFooterClass" : "modal-footer"
				},
				
				"Class" : "form-horizontal",
				"FormGroupClass" : "form-group",
				"LabelClass" : "col-sm-3 control-label",
				"FormElementClass" : "col-sm-9",
				"ClassAll" : "form-control",
				"RadioClass" : "radio",
				"CheckboxClass" : "checkbox"
			}
			';*/
			
			/* create formobject(check $this->Readonly->value), modal object and maybe tab objects */
			$this->FormObject->value->loadJSON($s_formObjectJSONsettings);
			$this->FormObject->value->ReadonlyAll = $this->Readonly->value;
			
			$this->FormModalConfiguration->value->loadJSON($s_formObjectJSONsettings);
			
			if ($this->FormModalConfiguration->value->Modal) {
				$this->FormModalConfiguration->value->ModalId = $this->FormObject->value->Id . 'Modal';
				$this->FormModalConfiguration->value->ModalTitle = $o_glob->URL->BranchTitle . ' ' . $o_glob->GetTranslation('BranchTitleRecord', 1);
			}
			
			$this->FormTabConfiguration->value->loadJSON($s_formObjectJSONsettings);
			
			/* get TabsInfo and create tab array */
			forestFormElement::JSONSettingsMultilanguage($s_formObjectJSONsettings);
			$a_jsonSettings = json_decode($s_formObjectJSONsettings, true);
			$a_tabsInfo = array();
			$o_firstTab = null;
			$o_lastTab = null;
			
			/* if we find FormTabConfiguration with Tab-key and TabsInfo-Key */
			if (array_key_exists('FormTabConfiguration', $a_jsonSettings)) {
				if (array_key_exists('Tab', $a_jsonSettings['FormTabConfiguration'])) {
					if ($a_jsonSettings['FormTabConfiguration']['Tab']) {
						if (array_key_exists('TabsInfo', $a_jsonSettings['FormTabConfiguration'])) {
							foreach ($a_jsonSettings['FormTabConfiguration']['TabsInfo'] as $a_tab) {
								$a_tabsInfo[$a_tab['TabId']] = new forestFormTab($a_tab['TabId'], $a_tab['TabTitle']);
								$o_lastTab = $a_tabsInfo[$a_tab['TabId']];
								
								if ($o_firstTab == null) {
									$o_firstTab = $a_tabsInfo[$a_tab['TabId']];
								}
							}
						}
					}
				}
			}
			
			/* get tablefields and iterate them */
			$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $o_tableTwig->UUID, 'operator' => '=', 'filterOperator' => 'AND'), array('column' => 'FieldName', 'value' => forestFormElement::FORM, 'operator' => '<>', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_tableFields = $o_tablefieldTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			foreach ($o_tableFields->Twigs as $o_tableField) {
				$s_forestdataName = '';
				
				/* query forestdata name, if UUID is set */
				if (issetStr($o_tableField->ForestDataUUID->PrimaryValue)) {
					$o_forestDataTwig = new forestdataTwig;
					
					if (! ($o_forestDataTwig->GetRecord(array($o_tableField->ForestDataUUID->PrimaryValue))) ) {
						throw new forestException(0x10001401, array($o_forestDataTwig->fphp_Table));
					}
					
					$s_forestdataName = $o_forestDataTwig->Name;
				}
				
				/* check read only mode */
				if ($this->FormObject->value->ReadonlyAll) {
					/* skip element if we have no table field information in global dictionary, except forestCombination */
					if ( (!$o_glob->TablefieldsDictionary->Exists($p_o_twig->fphp_Table . '_' . $o_tableField->FieldName)) && ($s_forestdataName != 'forestCombination') ) {
						continue;
					}
					
					if ($s_forestdataName != 'forestCombination') {
						/* skip element if it is of type FILE PASSWORD DROPZONE, except forestCombination */
						if ( ($o_glob->TablefieldsDictionary->{$p_o_twig->fphp_Table . '_' . $o_tableField->FieldName}->FormElementName == forestformElement::FILE) || ($o_glob->TablefieldsDictionary->{$p_o_twig->fphp_Table . '_' . $o_tableField->FieldName}->FormElementName == forestformElement::PASSWORD) || ($o_glob->TablefieldsDictionary->{$p_o_twig->fphp_Table . '_' . $o_tableField->FieldName}->FormElementName == forestformElement::DROPZONE) ) {
							continue;
						}
					}
				} else {
					/* if not read only, skip forestCombination fields */
					if ($s_forestdataName == 'forestCombination') {
						continue;
					}
				}
				
				$s_formElementJSONSettings = '';
				
				/* look for settings of tablefield, if not found look for standard with formelementuuid */
				if (!issetStr($o_tableField->JSONEncodedSettings)) {
					if (!($o_formelementTwig->GetRecord(array($o_tableField->FormElementUUID->PrimaryValue)))) {
						continue;
					} else {
						if (!issetStr($o_formelementTwig->JSONEncodedSettings)) {
							continue;
						} else {
							$s_formElementJSONSettings = $o_formelementTwig->JSONEncodedSettings;
						}
					}
				} else {
					$s_formElementJSONSettings = $o_tableField->JSONEncodedSettings;
				}
				
				/* create formelement object */
				if (!($o_formelementTwig->GetRecord(array($o_tableField->FormElementUUID->PrimaryValue)))) {
					continue;
				} else {
					$o_formElement = new forestFormElement($o_formelementTwig->Name);
					$o_formElement->loadJSON($s_formElementJSONSettings);
					
					/* add _readonly string to $formElement->Id, because of conflict with modal forms in detail view */
					if ($this->FormObject->value->ReadonlyAll) {
						$o_formElement->Id = 'readonly_' . $o_formElement->Id;
						
						if ( (property_exists($o_formElement->getFormElement(), 'Placeholder')) && (issetStr($o_formElement->Placeholder)) ) { 
							$o_formElement->Placeholder = '';
						}
					}
					
					/* set form id, uploader and deleter for dropzone element */
					if ($o_formElement->getType() == forestformElement::DROPZONE) {
						$o_formElement->FormId = $this->FormObject->value->Id;
						$o_formElement->URIFileUploader = forestLink::Link($o_glob->URL->Branch, 'fphp_upload');
						$o_formElement->URIFileDeleter = forestLink::Link($o_glob->URL->Branch, 'fphp_upload_delete');
					}
					
					if (property_exists($p_o_twig, $o_tableField->FieldName)) {
						/* create options array for lookup field */
						if (is_object($p_o_twig->{$o_tableField->FieldName})) {
							if (is_a($p_o_twig->{$o_tableField->FieldName}, 'forestLookupData')) {
								$o_formElement->Options = $p_o_twig->{$o_tableField->FieldName}->CreateOptionsArray();
							}
						}
					}
					
					/* adopt standard value of json encoded settings */
					if (issetStr($o_formElement->Value)) {
						$s_value = $o_formElement->Value;
					} else {
						$s_value = '';
					}
					
					/* get value for form element, based on parameter twig record */
					if ((!$p_o_twig->IsEmpty()) && (property_exists($p_o_twig, $o_tableField->FieldName))) {
						/* maybe other casts necessary depending on sqltype info */
						if (is_a($p_o_twig->{$o_tableField->FieldName}, 'forestDateTime')) {
							if ($o_glob->TablefieldsDictionary->{$p_o_twig->fphp_Table . '_' . $o_tableField->FieldName}->FormElementName == forestFormElement::DATETIMELOCAL) {
								$s_value = $p_o_twig->{$o_tableField->FieldName}->ToString('Y-m-d\TH:i:s');
							} else if ($o_glob->TablefieldsDictionary->{$p_o_twig->fphp_Table . '_' . $o_tableField->FieldName}->FormElementName == forestFormElement::DATE) {
								$s_value = $p_o_twig->{$o_tableField->FieldName}->ToString('Y-m-d');
							} else if ($o_glob->TablefieldsDictionary->{$p_o_twig->fphp_Table . '_' . $o_tableField->FieldName}->FormElementName == forestFormElement::MONTH) {
								$s_value = $p_o_twig->{$o_tableField->FieldName}->ToString('Y-m');
							} else if ($o_glob->TablefieldsDictionary->{$p_o_twig->fphp_Table . '_' . $o_tableField->FieldName}->FormElementName == forestFormElement::TIME) {
								$s_value = $p_o_twig->{$o_tableField->FieldName}->ToString('H:i:s');
							} else if ($o_glob->TablefieldsDictionary->{$p_o_twig->fphp_Table . '_' . $o_tableField->FieldName}->FormElementName == forestFormElement::WEEK) {
								$s_value = $p_o_twig->{$o_tableField->FieldName}->ToString('Y-\WW');
							} else {
								$s_value = $p_o_twig->{$o_tableField->FieldName}->ToString();
							}
						} else if (is_a($p_o_twig->{$o_tableField->FieldName}, 'forestLookupData')) {
							$s_value = $p_o_twig->{$o_tableField->FieldName}->PrimaryValue;
						} else {
							$s_value = strval($p_o_twig->{$o_tableField->FieldName});
							
							if ($this->FormObject->value->ReadonlyAll) {
								$s_JSONEncodedSettings = str_replace('&quot;', '"', $s_formElementJSONSettings);
								$a_settings = json_decode($s_JSONEncodedSettings, true);
								/* check if we want to render value as date interval value */
								if (array_key_exists('DateIntervalFormat', $a_settings)) {
									if ($a_settings['DateIntervalFormat']) {
										$s_value = strval(new forestDateInterval($s_value));
									}
								}
							}
						}
					}
					
					/* get value for forestCombination field */
					if ( (!$p_o_twig->IsEmpty()) && ($s_forestdataName == 'forestCombination') ) {
						$s_JSONEncodedSettings = str_replace('&quot;', '"', $s_formElementJSONSettings);
						$a_settings = json_decode($s_JSONEncodedSettings, true);
						
						if (array_key_exists('forestCombination', $a_settings)) {
							$s_value = $p_o_twig->CalculateCombination($a_settings['forestCombination']);
							
							/* check if we want to render value as date interval value */
							if ( (array_key_exists('DateIntervalFormat', $a_settings)) && ($this->FormObject->value->ReadonlyAll) ) {
								if ($a_settings['DateIntervalFormat']) {
									$s_value = strval(new forestDateInterval($s_value));
								}
							}
						}
					}
					
					$o_formElement->Value = $s_value;
					$b_addTab = false;
					
					/* if TabId isset, add form element to tab, else add form element to object list */
					if (issetStr($o_tableField->TabId)) {
						if (array_key_exists($o_tableField->TabId, $a_tabsInfo)) {
							$a_tabsInfo[$o_tableField->TabId]->FormElements->Add($o_formElement);
							$b_addTab = true;
						}
					}
					
					/* if form element should not be added to tab, it can be added to footer or normal to form elements object list */
					if (!$b_addTab) {
						if ($o_tableField->FooterElement) {
							$this->FormFooterElements->value->Add($o_formElement);
						} else {
							$this->FormElements->value->Add($o_formElement);
						}
					}
					
					/* if we have not read only mode */
					if (!$this->FormObject->value->ReadonlyAll) {
						/* get validation rules of tablefield and iterate them */
						$o_querySelect = new forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, forestSQLQuery::SELECT, 'sys_fphp_tablefield_validationrule');
						
						$column_A = new forestSQLColumn($o_querySelect);
							$column_A->Column = '*';
						
						$o_querySelect->Query->Columns->Add($column_A);
						
						$join_A = new forestSQLJoin($o_querySelect);
							$join_A->JoinType = 'INNER JOIN';
							$join_A->Table = 'sys_fphp_validationrule';

						$relation_A = new forestSQLRelation($o_querySelect);
						
						$column_B = new forestSQLColumn($o_querySelect);
							$column_B->Column = 'ValidationruleUUID';
							
						$column_C = new forestSQLColumn($o_querySelect);
							$column_C->Column = 'UUID';
							$column_C->Table = $join_A->Table;
						
						$relation_A->ColumnLeft = $column_B;
						$relation_A->ColumnRight = $column_C;
						$relation_A->Operator = '=';
						
						$join_A->Relations->Add($relation_A);
							
						$o_querySelect->Query->Joins->Add($join_A);
						
						$column_D = new forestSQLColumn($o_querySelect);
							$column_D->Column = 'TablefieldUUID';
						
						$where_A = new forestSQLWhere($o_querySelect);
							$where_A->Column = $column_D;
							$where_A->Value = $where_A->ParseValue($o_tableField->UUID);
							$where_A->Operator = '=';
						
						$o_querySelect->Query->Where->Add($where_A);
						
						$o_result = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect, false, false);
						
						foreach ($o_result as $o_row) {
							/* render validation rules */
							$this->FormObject->value->ValRequiredMessage = $o_glob->GetTranslation('ValRequiredMessage', 1);
							
							$s_param01 = ( ((empty($o_row['ValidationRuleParam01'])) || ($o_row['ValidationRuleParam01'] == 'NULL')) ? null : $o_row['ValidationRuleParam01'] );
							$s_param02 = ( ((empty($o_row['ValidationRuleParam02'])) || ($o_row['ValidationRuleParam02'] == 'NULL')) ? null : $o_row['ValidationRuleParam02'] );
							$s_autoRequired = ( (($o_row['ValidationRuleRequired'] == 1)) ? 'true' : 'false' );
							
							$this->FormObject->value->ValRules->Add(new forestFormValidationRule($p_o_twig->fphp_Table . '_' . $o_tableField->FieldName, $o_row['Name'], $s_param01, $s_param02, $s_autoRequired));
						}
					}
				}
			}
			
			/* add auto checkin form element if current record is checked out */
			if ( ($p_o_twig->fphp_HasUUID) && (!$p_o_twig->IsEmpty()) && (($o_checkoutTwig = new checkoutTwig)->GetRecordPrimary(array($p_o_twig->UUID), array('ForeignUUID'))) && (!$this->FormObject->value->ReadonlyAll) ) {
				/* query auto checkin form element */
				if (!($o_formelementTwig->GetRecordPrimary(array(forestFormElement::AUTOCHECKIN), array('Name')))) {
					throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
				}
				
				/* create captcha form element and adjust settings */
				$o_formElement = new forestFormElement(forestFormElement::AUTOCHECKIN);
				$o_formElement->loadJSON($o_formelementTwig->JSONEncodedSettings);
				$o_formElement->Id = $p_o_twig->fphp_Table . '_AutocheckinStandard';
				
				/* usually it will be added to the last tab or to form element object list */
				if ($o_lastTab != null) {
					$o_lastTab->FormElements->Add($o_formElement);
				} else {
					$this->FormElements->value->Add($o_formElement);
				}
			}
			
			/* if we are using a captcha element and we have not read only mode */
			if ( ($this->FormObject->value->UseCaptcha) && (!$this->FormObject->value->ReadonlyAll) ) {
				/* query captcha form element */
				if (!($o_formelementTwig->GetRecordPrimary(array(forestFormElement::CAPTCHA), array('Name')))) {
					throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
				}
				
				/* create captcha form element and adjust settings */
				$o_formElement = new forestFormElement(forestFormElement::CAPTCHA);
				$o_formElement->loadJSON($o_formelementTwig->JSONEncodedSettings);
				$o_formElement->Id = $p_o_twig->fphp_Table . '_Captcha';
				$this->FormObject->value->ValRules->Add(new forestFormValidationRule($p_o_twig->fphp_Table . '_Captcha', 'required', 'true', 'NULL', 'false'));
				
				/* usually it will be added to the last tab or to form element object list */
				if ($o_lastTab != null) {
					$o_lastTab->FormElements->Add($o_formElement);
				} else {
					$this->FormElements->value->Add($o_formElement);
				}
			}
			
			/* print footer flag */
			if ($this->PrintFooter->value) {
				/* if modal, add standard cancel to footer */
				if ($this->FormModalConfiguration->value->Modal) {
					/* query cancel form element */
					if (!($o_formelementTwig->GetRecordPrimary(array('cancel'), array('Name')))) {
						throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
					}
					
					/* create cancel form element and adjust settings */
					$o_cancel = new forestFormElement(forestFormElement::BUTTON);
					$o_cancel->loadJSON($o_formelementTwig->JSONEncodedSettings);
					$o_cancel->ButtonText = htmlspecialchars_decode($o_cancel->ButtonText, ( ENT_QUOTES | ENT_HTML5 ));
					$o_cancel->Id = $o_cancel->Id . '_' . substr($o_glob->Security->GenRandomHash(), 0, 4);
					
					$this->FormFooterElements->value->Add($o_cancel);
					
					if (!$this->FormObject->value->ReadonlyAll) {
						/* query and add standard submit to footer, if we do not have readonly all flag */
						if (!($o_formelementTwig->GetRecordPrimary(array('submit'), array('Name')))) {
							throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
						}
						
						/* create submit form element and adjust settings */
						$o_submit = new forestFormElement(forestFormElement::BUTTON);
						$o_submit->loadJSON($o_formelementTwig->JSONEncodedSettings);
						$o_submit->ButtonText = htmlspecialchars_decode($o_submit->ButtonText);
						$o_submit->Id = $o_submit->Id . '_' . substr($o_glob->Security->GenRandomHash(), 0, 4);
						
						$this->FormFooterElements->value->Add($o_submit);
					}
				}
			}
			
			/* add form key as hash as hidden field in form footer */
			$this->AddFormKey();
			
			/* use honeypot fields if it is activated and configured in fphp-trunk */
			$this->AddHoneypotFields($p_o_twig);
			
			/* add form tabs if we have any */
			foreach ($a_tabsInfo as $o_tab) {
				if ($this->FormObject->value->ReadonlyAll) {
					$o_tab->ReadOnly = true;
				}
				
				$this->FormTabs->value->Add($o_tab);
			}
			
			/*$s_json_general = '
			{
				"FormGroupClass" : "NULL",
				"Label" : "NULL",
				"LabelClass" : "NULL",
				"LabelFor" : "NULL",
				"FormElementClass" : "NULL",
				
				"Class" : "NULL",
				"Description" : "NULL",
				"DescriptionClass" : "NULL",
				"Disabled" : false,
				"Id" : "NULL",
				"Name" : "NULL",
				"AutoFocus" : false,
				"Required" : false,
				"Style" : "NULL",
				"Value" : "NULL",
				"ValMessage" : "NULL"
			}
			';*/
			
			/*$s_json_input = '
			{
				"FormGroupClass" : "NULL",
				"Label" : "NULL",
				"LabelClass" : "NULL",
				"LabelFor" : "NULL",
				"FormElementClass" : "NULL",
				
				"Class" : "NULL",
				"Description" : "NULL",
				"DescriptionClass" : "NULL",
				"Disabled" : false,
				"Id" : "NULL",
				"Name" : "NULL",
				"AutoFocus" : false,
				"Required" : false,
				"Style" : "NULL",
				"Value" : "NULL",
				"ValMessage" : "NULL",
				
				"Accept" : "NULL",
				"AutoComplete" : true,
				"Capture" : "NULL",
				"Dirname" : "NULL",
				"List" : "NULL",
				"Max" : "NULL",
				"Min" : "NULL",
				"Multiple" : false,
				"Options" : {},
				"Pattern" : "NULL",
				"PatternTitle" : "NULL",
				"Placeholder" : "NULL",
				"Readonly" : false,
				"Size" : 0,
				"Step" : 0,
				
				"Form" : "NULL",
				"FormAction" : "NULL",
				"FormEnctype" : "NULL",
				"FormMethod" : "NULL",
				"FormTarget" : "NULL",
				"FormNoValidate" : false
			}
			';*/
			
			/*$s_json_radio = '
			{
				"FormGroupClass" : "NULL",
				"Label" : "NULL",
				"LabelClass" : "NULL",
				"LabelFor" : "NULL",
				"FormElementClass" : "NULL",
				
				"Class" : "NULL",
				"Description" : "NULL",
				"DescriptionClass" : "NULL",
				"Disabled" : false,
				"Id" : "NULL",
				"Name" : "NULL",
				"AutoFocus" : false,
				"Required" : false,
				"Style" : "NULL",
				"Value" : "NULL",
				"ValMessage" : "NULL",
				
				"Accept" : "NULL",
				"AutoComplete" : true,
				"Capture" : "NULL",
				"Dirname" : "NULL",
				"List" : "NULL",
				"Max" : "NULL",
				"Min" : "NULL",
				"Multiple" : false,
				"Options" : {},
				"Pattern" : "NULL",
				"PatternTitle" : "NULL",
				"Placeholder" : "NULL",
				"Readonly" : false,
				"Size" : 0,
				"Step" : 0,
				
				"Form" : "NULL",
				"FormAction" : "NULL",
				"FormEnctype" : "NULL",
				"FormMethod" : "NULL",
				"FormTarget" : "NULL",
				"FormNoValidate" : false,
				
				"Break" : true,
				"RadioClass" : "NULL"
			}
			';*/
			
			/*$s_json_checkbox = '
			{
				"FormGroupClass" : "NULL",
				"Label" : "NULL",
				"LabelClass" : "NULL",
				"LabelFor" : "NULL",
				"FormElementClass" : "NULL",
				
				"Class" : "NULL",
				"Description" : "NULL",
				"DescriptionClass" : "NULL",
				"Disabled" : false,
				"Id" : "NULL",
				"Name" : "NULL",
				"AutoFocus" : false,
				"Required" : false,
				"Style" : "NULL",
				"Value" : "NULL",
				"ValMessage" : "NULL",
				
				"Accept" : "NULL",
				"AutoComplete" : true,
				"Capture" : "NULL",
				"Dirname" : "NULL",
				"List" : "NULL",
				"Max" : "NULL",
				"Min" : "NULL",
				"Multiple" : false,
				"Options" : {},
				"Pattern" : "NULL",
				"PatternTitle" : "NULL",
				"Placeholder" : "NULL",
				"Readonly" : false,
				"Size" : 0,
				"Step" : 0,
				
				"Form" : "NULL",
				"FormAction" : "NULL",
				"FormEnctype" : "NULL",
				"FormMethod" : "NULL",
				"FormTarget" : "NULL",
				"FormNoValidate" : false,
				
				"Break" : true,
				"CheckboxClass" : "NULL"
			}
			';*/
			
			/*$s_json_textarea = '
			{
				"FormGroupClass" : "NULL",
				"Label" : "NULL",
				"LabelClass" : "NULL",
				"LabelFor" : "NULL",
				"FormElementClass" : "NULL",
				
				"Class" : "NULL",
				"Description" : "NULL",
				"DescriptionClass" : "NULL",
				"Disabled" : false,
				"Id" : "NULL",
				"Name" : "NULL",
				"AutoFocus" : false,
				"Required" : false,
				"Style" : "NULL",
				"Value" : "NULL",
				"ValMessage" : "NULL",
				
				"Rows" : 0,
				"Cols" : 0,
				"Dirname" : "NULL",
				"Placeholder" : "NULL",
				"Readonly" : false,
				"Wrap" : false
			}
			';*/
			
			/*$s_json_select = '
			{
				"FormGroupClass" : "NULL",
				"Label" : "NULL",
				"LabelClass" : "NULL",
				"LabelFor" : "NULL",
				"FormElementClass" : "NULL",
				
				"Class" : "NULL",
				"Description" : "NULL",
				"DescriptionClass" : "NULL",
				"Disabled" : false,
				"Id" : "NULL",
				"Name" : "NULL",
				"AutoFocus" : false,
				"Required" : false,
				"Style" : "NULL",
				"Value" : "NULL",
				"ValMessage" : "NULL",
				
				"Multiple : false,	
				"Options : {},
				"Size : 1,
				"Data : "NULL"
			}
			';*/
			
			/*$s_json_button = '
			{
				"FormGroupClass" : "NULL",
				"Label" : "NULL",
				"LabelClass" : "NULL",
				"LabelFor" : "NULL",
				"FormElementClass" : "NULL",
				
				"Class" : "NULL",
				"Description" : "NULL",
				"DescriptionClass" : "NULL",
				"Disabled" : false,
				"Id" : "NULL",
				"Name" : "NULL",
				"AutoFocus" : false,
				"Required" : false,
				"Style" : "NULL",
				"Value" : "NULL",
				"ValMessage" : "NULL",
				
				"Accept" : "NULL",
				"AutoComplete" : true,
				"Capture" : "NULL",
				"Dirname" : "NULL",
				"List" : "NULL",
				"Max" : "NULL",
				"Min" : "NULL",
				"Multiple" : false,
				"Options" : {},
				"Pattern" : "NULL",
				"PatternTitle" : "NULL",
				"Placeholder" : "NULL",
				"Readonly" : false,
				"Size" : 0,
				"Step" : 0,
				
				"Form" : "NULL",
				"FormAction" : "NULL",
				"FormEnctype" : "NULL",
				"FormMethod" : "NULL",
				"FormTarget" : "NULL",
				"FormNoValidate" : false,
				
				"Type" : "NULL",
				"Data" : "NULL",
				"ButtonText" : "NULL",
				"NoFormGroup" : "NULL",
				"WrapSpanClass" : "NULL"
			}
			';*/
		}
	}

	public function __toString() {
		/* check if we have elements of type FILE or DROPZONE */
		if ($this->CheckUploadElementConfigured()) {
			$this->FormObject->value->Enctype = 'multipart/form-data';
		}
		
		$s_foo = '';
		
		/* render modal */
		if ($this->FormModalConfiguration->value->Modal) {
			$this->FormModalConfiguration->value->CheckIsset();
			
			$s_foo .= '<!-- Modal with tabs -->' . "\n";
			$s_foo .= '	<div class="' . $this->FormModalConfiguration->value->ModalClass . '" id="' . $this->FormModalConfiguration->value->ModalId . '" role="' . $this->FormModalConfiguration->value->ModalRole . '">' . "\n";
			$s_foo .= '		<div class="' . $this->FormModalConfiguration->value->ModalDialogClass . '">' . "\n";

			$s_foo .= '			<!-- Modal content-->' . "\n";
			$s_foo .= '			<div class="' . $this->FormModalConfiguration->value->ModalDialogContentClass . '">' . "\n";
		}
		
		/* render form object */
		$s_foo .= strval($this->FormObject->value);
		
		if ($this->FormModalConfiguration->value->Modal) {
			$s_foo .= '<div class="' . $this->FormModalConfiguration->value->ModalHeaderClass . '">' . "\n";
			$s_foo .= '	<button type="button" class="' . $this->FormModalConfiguration->value->ModalHeaderCloseClass . '" data-dismiss="' . $this->FormModalConfiguration->value->ModalHeaderDismissClass . '">' . $this->FormModalConfiguration->value->ModalHeaderCloseContent . '</button>' . "\n";
			$s_foo .= '	' . $this->FormModalConfiguration->value->ModalTitle . '' . "\n";
			$s_foo .= '</div>' . "\n";
			$s_foo .= '<div class="' . $this->FormModalConfiguration->value->ModalBodyClass . '">' . "\n";
		}
		
		/* render tabs */
		if ($this->FormTabConfiguration->value->Tab) {
			$this->FormTabConfiguration->value->CheckIsset();
			
			if ($this->FormTabs->value->Count() > 0) {
				/* render form elements in tab */
				$this->PrintTabElements($this->FormTabs->value, $s_foo);
				
				if (!$this->FormModalConfiguration->value->Modal) {
					$s_foo .= '<div class="' . $this->FormTabConfiguration->value->TabFooterClass . '">' . "\n";
						$this->PrintFormElements($this->FormFooterElements->value, $s_foo);
					$s_foo .= '</div>' . "\n";
				}
			}
		} else {
			/* render form elements */
			$this->PrintFormElements($this->FormElements->value, $s_foo);
		}
		
		if ($this->FormModalConfiguration->value->Modal) {
			if (issetStr($this->FormModalSubForm->value)) {
				$s_foo .= '<div>' . $this->FormModalSubForm->value . '</div>' . "\n";
			}
			
			$s_foo .= '</div>' . "\n"; // end of modal body
			
			if ($this->FormFooterElements->value->Count() > 0) {
				$s_foo .= '<div class="' . $this->FormModalConfiguration->value->ModalFooterClass . '">' . "\n";
					$this->PrintFormElements($this->FormFooterElements->value, $s_foo);
				$s_foo .= '</div>' . "\n";
			}
		}
		
		/* render validation rules */
		$this->PrintValRules($s_foo);
		
		return forestStringLib::closeHTMLTags($s_foo);
	}
	
	public function CreateModalForm(forestTwig $p_o_twig, $p_s_title, $p_b_showSubmit = true) {
		$o_glob = forestGlobals::init();
		
		/* get table */
		$o_tableTwig = new tableTwig;
		
		/* query table record */
		if (!($o_tableTwig->GetRecordPrimary(array($p_o_twig->fphp_Table), array('Name')))) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$o_formelementTwig = new formelementTwig;
		
		/* look in tablefields for formobject, if not get the standard by formelementuuid */
		if (!($o_formelementTwig->GetRecordPrimary(array('form'), array('Name')))) {
			throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		}
		
		$o_tablefieldTwig = new tablefieldTwig;
		$s_formObjectJSONsettings = '';
		
		if (!($o_tablefieldTwig->GetRecordPrimary(array($o_tableTwig->UUID, $o_formelementTwig->UUID), array('TableUUID', 'FormElementUUID')))) {
			/* no tablefield for table, take standard */
			$s_formObjectJSONsettings = $o_formelementTwig->JSONEncodedSettings;
		} else {
			$s_formObjectJSONsettings = $o_tablefieldTwig->JSONEncodedSettings;
		}
		
		/* create formobject(check $this->Readonly->value), modal object and maybe tab objects */
		$this->FormObject->value->loadJSON($s_formObjectJSONsettings);
		$this->FormModalConfiguration->value->loadJSON($s_formObjectJSONsettings);
		$this->FormTabConfiguration->value->loadJSON($s_formObjectJSONsettings);
		
		$this->FormModalConfiguration->value->ModalId = $this->FormObject->value->Id . 'Modal';
		$this->FormModalConfiguration->value->ModalTitle = $p_s_title;
		$this->FormTabConfiguration->value->Tab = false;
		
		/* create standard cancel button to modal footer */
		if (!($o_formelementTwig->GetRecordPrimary(array('cancel'), array('Name')))) {
			throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		}
		
		$o_cancel = new forestFormElement(forestFormElement::BUTTON);
		$o_cancel->loadJSON($o_formelementTwig->JSONEncodedSettings);
		$o_cancel->ButtonText = htmlspecialchars_decode($o_cancel->ButtonText);
		$this->FormFooterElements->value->Add($o_cancel);
		
		/* create standard submit button to modal footer */
		if ($p_b_showSubmit) {
			if (!($o_formelementTwig->GetRecordPrimary(array('submit'), array('Name')))) {
				throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
			}
			
			$o_button = new forestFormElement(forestFormElement::BUTTON);
			$o_button->loadJSON($o_formelementTwig->JSONEncodedSettings);
			$o_button->ButtonText = htmlspecialchars_decode($o_button->ButtonText);
			$this->FormFooterElements->value->Add($o_button);
		}
		
		/* add form key as hash as hidden field in form footer */
		$this->AddFormKey();
		
		/* use honeypot fields if it is activated and configured in fphp-trunk */
		$this->AddHoneypotFields($p_o_twig);
		
		/* automatic display of modal form */
		$this->Automatic->value = true;
	}
	
	public function CreateDeleteModalForm(forestTwig $p_o_twig, $p_s_title, $p_s_description) {
		$o_glob = forestGlobals::init();
		
		/* get table */
		$o_tableTwig = new tableTwig;
		
		/* query table record */
		if (!($o_tableTwig->GetRecordPrimary(array($p_o_twig->fphp_Table), array('Name')))) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$o_formelementTwig = new formelementTwig;
		
		/* look in tablefields for formobject, if not get the standard by formelementuuid */
		if (!($o_formelementTwig->GetRecordPrimary(array('form'), array('Name')))) {
			throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		}
		
		$o_tablefieldTwig = new tablefieldTwig;
		$s_formObjectJSONsettings = '';
		
		if (!($o_tablefieldTwig->GetRecordPrimary(array($o_tableTwig->UUID, $o_formelementTwig->UUID), array('TableUUID', 'FormElementUUID')))) {
			/* no tablefield for table, take standard */
			$s_formObjectJSONsettings = $o_formelementTwig->JSONEncodedSettings;
		} else {
			$s_formObjectJSONsettings = $o_tablefieldTwig->JSONEncodedSettings;
		}
		
		/* create formobject(check $this->Readonly->value), modal object and maybe tab objects */
		$this->FormObject->value->loadJSON($s_formObjectJSONsettings);
		$this->FormModalConfiguration->value->loadJSON($s_formObjectJSONsettings);
		$this->FormTabConfiguration->value->loadJSON($s_formObjectJSONsettings);
		
		$this->FormModalConfiguration->value->ModalId = $this->FormObject->value->Id . 'Modal';
		$this->FormModalConfiguration->value->ModalTitle = $p_s_title;
		$this->FormTabConfiguration->value->Tab = false;
		
		/* create and add description to modal form */
		$o_description = new forestFormElement(forestFormElement::DESCRIPTION);
		$o_description->Description = $p_s_description;
		$o_description->NoFormGroup = true;
		
		$this->FormElements->value->Add($o_description);
		
		/* create standard no button to modal footer */
		if (!($o_formelementTwig->GetRecordPrimary(array('no'), array('Name')))) {
			throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		}
		
		$o_cancel = new forestFormElement(forestFormElement::BUTTON);
		$o_cancel->loadJSON($o_formelementTwig->JSONEncodedSettings);
		$o_cancel->ButtonText = htmlspecialchars_decode($o_cancel->ButtonText);
		$this->FormFooterElements->value->Add($o_cancel);
		
		/* create standard yes button to modal footer */
		if (!($o_formelementTwig->GetRecordPrimary(array('yes'), array('Name')))) {
			throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		}
		
		$o_button = new forestFormElement(forestFormElement::BUTTON);
		$o_button->loadJSON($o_formelementTwig->JSONEncodedSettings);
		$o_button->ButtonText = htmlspecialchars_decode($o_button->ButtonText);
		$this->FormFooterElements->value->Add($o_button);
		
		/* add form key as hash as hidden field in form footer */
		$this->AddFormKey();
		
		/* use honeypot fields if it is activated and configured in fphp-trunk */
		$this->AddHoneypotFields($p_o_twig);
		
		/* automatic display of modal form */
		$this->Automatic->value = true;
	}
	
	public function AddFormKey() {
		$o_glob = forestGlobals::init();
		
		/* add form key as hash as hidden field in form footer */
		if ( ($o_glob->Trunk->FormKey) && ($o_glob->Security->SessionData->Exists('formkey')) ) {
			$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
			$o_hidden->Id = 'sys_fphp_formkeyHash';
			$o_hidden->Value = password_hash($o_glob->Security->SessionData->{'formkey'}, PASSWORD_DEFAULT);
			
			$this->FormFooterElements->value->Add($o_hidden);
		}
	}

	public function AddHoneypotFields(forestTwig $p_o_twig) {
		$o_glob = forestGlobals::init();
		
		if ( ($o_glob->Trunk->HoneypotFields) && ($o_glob->Trunk->MaxAmountHoneypot > 0) ) {
			$a_memory = array();
			$a_randomNames = array('buzz','rex','bo','hamm','slink','potato','woody','sarge','etch','lenny','squeeze','wheezy','jessie','stretch','buster','bullseye','bookworm','sid');

			$i_amount = mt_rand(1, $o_glob->Trunk->MaxAmountHoneypot);

			for ($i = 0; $i < $i_amount; $i++) {
				$s_hiddenId = null;
				
				do {
					$j = mt_rand(0, (count($a_randomNames) - 1));
					$k = mt_rand(1, 999);
					$s_hiddenId = $p_o_twig->fphp_Table . '_' . $a_randomNames[$j] . $k;
				} while (in_array($s_hiddenId, $a_memory));
				
				$a_memory[] = $s_hiddenId;
				
				$o_hiddenText = new forestFormElement(forestFormElement::TEXT);
				$o_hiddenText->Id = $s_hiddenId;
				$o_hiddenText->NoDisplay = true;
				
				$this->FormFooterElements->value->Add($o_hiddenText);
			}
			
			/* insert hidden text ids into session */
			$o_glob->Security->SessionData->Add(implode(';', $a_memory), 'sys_fphp_honeypotfields');
		}
	}

	public function GetFormElementByFormId($p_s_formId) {
		$o_return = null;
		
		foreach ($this->FormElements->value as $s_key => $o_formElement) {
			if ($o_formElement->Id == $p_s_formId) {
				$o_return = $o_formElement;
				break;
			}
		}
		
		if ($o_return == null) {
			foreach ($this->FormFooterElements->value as $s_key => $o_formElement) {
				if ($o_formElement->Id == $p_s_formId) {
					$o_return = $o_formElement;
					break;
				}
			}
			
			if ($o_return == null) {
				foreach ($this->FormTabs->value as $o_tabElement) {
					$o_return = $o_tabElement->GetFormElementByFormId($p_s_formId);
					
					if ($o_return != null) {
						break;
					}
				}
			}
		}
		
		return $o_return;
	}

	public function DeleteFormElementByFormId($p_s_formId) {
		$b_return = false;
		$s_listKey = null;
		
		foreach ($this->FormElements->value as $s_key => $o_formElement) {
			if ($o_formElement->Id == $p_s_formId) {
				$s_listKey = $s_key;
				$b_return = true;
				break;
			}
		}
		
		if ($b_return) {
			$this->FormElements->value->Del($s_key);
		} else {
			foreach ($this->FormFooterElements->value as $s_key => $o_formElement) {
				if ($o_formElement->Id == $p_s_formId) {
					$s_listKey = $s_key;
					$b_return = true;
					break;
				}
			}
			
			if ($b_return) {
				$this->FormElements->value->Del($s_key);
			} else {
				foreach ($this->FormTabs->value as $o_tabElement) {
					if ($o_tabElement->DeleteFormElementByFormId($p_s_formId)) {
						$b_return = true;
						break;
					}
				}
			}
		}
		
		return $b_return;
	}
	
	public function AddFormElement(forestFormElement $p_o_formElement, $p_s_tabId = 'general', $p_b_first = false) {
		$b_return = false;
		
		if ($p_s_tabId == null) {
			if ($p_b_first) {
				$this->FormElements->value->AddFirst($p_o_formElement);
			} else {
				$this->FormElements->value->Add($p_o_formElement);
			}
			
			$b_return = true;
		} else {
			foreach ($this->FormTabs->value as $o_tabElement) {
				if ($o_tabElement->TabId == $p_s_tabId) {
					$b_return = $o_tabElement->AddFormElement($p_o_formElement, $p_b_first);
				}
			}
		}
		
		return $b_return;
	}
	
	private function CheckUploadElementConfigured() {
		if ($this->FormTabConfiguration->value->Tab) {
			if ($this->FormTabs->value->Count() > 0) {
				foreach ($this->FormTabs->value as $o_tabElement) {
					foreach ($o_tabElement->FormElements as $o_formElement) {
						if ( ($o_formElement->getType() == forestFormElement::FILE) || ($o_formElement->getType() == forestFormElement::DROPZONE) ) {
							return true;
						}
					}
				}
			}
		}
		
		foreach ($this->FormElements->value as $o_formElement) {
			if ( ($o_formElement->getType() == forestFormElement::FILE) || ($o_formElement->getType() == forestFormElement::DROPZONE) ) {
				return true;
			}
		}
		
		return false;
	}
	
	private function PrintTabElements($p_ol_tabElements, &$p_s_foo) {
		$b_first = true;
		$p_s_foo .= '<ul class="' . $this->FormTabConfiguration->value->TabMenuClass . '">' . "\n";
		
		$s_tabIdPrefix = '';
		
		if ($this->FormObject->value->ReadonlyAll) {
			$s_tabIdPrefix = 'readonly_';
		}
		
		/* render tab buttons */
		foreach ($p_ol_tabElements as $o_tabElement) {
			if ( (issetStr($o_tabElement->TabId)) && (issetStr($o_tabElement->TabTitle)) ) {
				$o_tabElement->CheckIsset();
				
				if ($b_first) {
					$p_s_foo .= '<li class="' . $this->FormTabConfiguration->value->TabActiveClass . '"><a data-toggle="' . $this->FormTabConfiguration->value->TabToggle . '" href="#' . $s_tabIdPrefix . $o_tabElement->TabId . '">' . $o_tabElement->TabTitle . '</a></li>' . "\n";
					$b_first = false;
				} else {
					$p_s_foo .= '<li><a data-toggle="' . $this->FormTabConfiguration->value->TabToggle . '" href="#' . $s_tabIdPrefix . $o_tabElement->TabId . '">' . $o_tabElement->TabTitle . '</a></li>' . "\n";
				}
			}
		}
		
		$p_s_foo .= '</ul>' . "\n";
		$p_s_foo .= '<div class="' . $this->FormTabConfiguration->value->TabContentClass . '">' . "\n";
		$b_first = true;
		
		/* render tab form elements */
		foreach ($p_ol_tabElements as $o_tabElement) {
			if ( (issetStr($o_tabElement->TabId)) && (issetStr($o_tabElement->TabTitle)) ) {
				if ($b_first) {
					$o_tabElement->Active = true;
					$b_first = false;
				}
				
				$o_tabElement->TempFormObject = $this->FormObject->value;
				$o_tabElement->TabClass = $this->FormTabConfiguration->value->TabElementClass;
				$o_tabElement->TabActiveClass = $this->FormTabConfiguration->value->TabElementActiveClass;
				
				$o_tabElement->CheckIsset(true);
				
				$p_s_foo .= strval($o_tabElement);
			}
		}
		
		$p_s_foo .= '</div>' . "\n";
	}
	
	private function PrintFormElements($p_ol_formElements, &$p_s_foo) {
		foreach ($p_ol_formElements as $o_formElement) {
			/* overwrite Form Group Class if it is not set */
			if (issetStr($this->FormObject->value->FormGroupClass)) {
				if (!issetStr($o_formElement->FormGroupClass)) {
					$o_formElement->FormGroupClass = $this->FormObject->value->FormGroupClass;
				}
			}
			
			/* overwrite Label Class if it is not set */
			if (issetStr($this->FormObject->value->LabelClass)) {
				if (!issetStr($o_formElement->LabelClass)) {
					$o_formElement->LabelClass = $this->FormObject->value->LabelClass;
				}
			}
			
			/* overwrite Form Element Class if it is not set */
			if (issetStr($this->FormObject->value->FormElementClass)) {
				if (!issetStr($o_formElement->FormElementClass)) {
					$o_formElement->FormElementClass = $this->FormObject->value->FormElementClass;
				}
			}
			
			/* overwrite Class if it is not set */
			if ( ($o_formElement->getType() != forestFormElement::RADIO) && ($o_formElement->getType() != forestFormElement::CHECKBOX) && ($o_formElement->getType() != forestFormElement::DESCRIPTION) ) {
				if (issetStr($this->FormObject->value->ClassAll)) {
					if (!issetStr($o_formElement->Class)) {
						$o_formElement->Class = $this->FormObject->value->ClassAll;
					}
				}
			}
			
			/* set required flag for all form elements */
			if ($this->FormObject->value->RequiredAll) {
				if ($o_formElement->getType() != forestFormElement::CHECKBOX) {
					$o_formElement->Required = true;
				}
			}
			
			/* set readonly flag for all form elements */
			if ($this->FormObject->value->ReadonlyAll) {
				if ( ($o_formElement->getType() != forestFormElement::SELECT) && ($o_formElement->getType() != forestFormElement::LOOKUP) && ($o_formElement->getType() != forestFormElement::COLOR) && ($o_formElement->getType() != forestFormElement::DROPZONE) && ($o_formElement->getType() != forestFormElement::RICHTEXT) && ($o_formElement->getType() != forestFormElement::DESCRIPTION) ) {
					$o_formElement->Readonly = true;
				}
				
				/* other elements do not have readonly flag, instead we are using disabled flag */
				if ( ($o_formElement->getType() == forestFormElement::RICHTEXT) || ($o_formElement->getType() == forestFormElement::RADIO) || ($o_formElement->getType() == forestFormElement::CHECKBOX) || ($o_formElement->getType() == forestFormElement::SELECT) || ($o_formElement->getType() == forestFormElement::LOOKUP) || ($o_formElement->getType() == forestFormElement::COLOR) || ( ($o_formElement->getType() == forestFormElement::BUTTON) && (!$o_formElement->NoFormGroup) ) ) {
					$o_formElement->Disabled = true;
				}
			}
			
			/* overwrite Radio Class if it is not set */
			if ($o_formElement->getType() == forestFormElement::RADIO) {
				if (issetStr($this->FormObject->value->RadioClass)) {
					if (!issetStr($o_formElement->RadioClass)) {
						$o_formElement->RadioClass = $this->FormObject->value->RadioClass;
					}
				}
			}
			
			/* overwrite Checkbox Class if it is not set */
			if ($o_formElement->getType() == forestFormElement::CHECKBOX) {
				if (issetStr($this->FormObject->value->CheckboxClass)) {
					if (!issetStr($o_formElement->CheckboxClass)) {
						$o_formElement->CheckboxClass = $this->FormObject->value->CheckboxClass;
					}
				}
			}
			
			$p_s_foo .= strval($o_formElement);
		}
	}
	
	private function PrintValRules(&$p_s_foo) {
		$i_valRules = $this->FormObject->value->ValRules->Count();
		
		if ($i_valRules > 0) {
			/* render standard required message */
			$p_s_foo .= '<div class="fphp_data_validator">
				{
					"s_formId" : "#' . $this->FormObject->value->Id . '",
					"s_requiredDefaultMessage" : "' . $this->FormObject->value->ValRequiredMessage . '",
					"a_rules" : [' . "\n";
			
			$i = 0;
			
			/* render each rule */
			foreach ($this->FormObject->value->ValRules as $o_validationRule){
				if (! ( ($o_validationRule->RuleParam01 == 'true') || ($o_validationRule->RuleParam01 == 'false') || (is_numeric($o_validationRule->RuleParam01)) ) ) {
					$o_validationRule->RuleParam01 = '"' . $o_validationRule->RuleParam01 . '"';
				}
				
				if (! ( ($o_validationRule->RuleParam02 == 'true') || ($o_validationRule->RuleParam02 == 'false') || (is_numeric($o_validationRule->RuleParam02)) ) ) {
					$o_validationRule->RuleParam02 = '"' . $o_validationRule->RuleParam02 . '"';
				}
				
				if (! ( ($o_validationRule->AutoRequired == 'true') || ($o_validationRule->AutoRequired == 'false') || (is_numeric($o_validationRule->AutoRequired)) ) ) {
					$o_validationRule->AutoRequired = '"' . $o_validationRule->AutoRequired . '"';
				}
				
				$p_s_foo .= '{ "s_formElementId" : "#' . $o_validationRule->FormElementId . '", "s_rule" : "' . $o_validationRule->Rule . '", "s_ruleParam01" : ' . $o_validationRule->RuleParam01 . ', "s_ruleParam02" : ' . $o_validationRule->RuleParam02 . ', "s_ruleAutoRequired" : ' . $o_validationRule->AutoRequired . ' }';
				
				if ($i < ($i_valRules - 1)) {
					$p_s_foo .= ',' . "\n";
				}
				
				$i++;
			}
			
			$p_s_foo .= '		]
				}
			</div>' . "\n";
		}
	}
}

class forestFormTab {
	use forestData;
	
	/* Fields */
	
	private $TabId;
	private $TabTitle;
	private $FormElements;
	private $Active;
	private $TempFormObject;
	private $TabClass;
	private $TabActiveClass;
	private $ReadOnly;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct($p_s_tabId = null, $p_s_tabTitle = null, $p_s_tabClass = null, $p_s_tabActiveClass = null) {
		$this->TabId = new forestString;
		$this->TabTitle = new forestString;
		$this->FormElements = new forestObject(new forestObjectList('forestFormElement'), false);
		$this->Active = new forestBool;
		$this->TempFormObject = new forestObject(new forestFormElement(forestFormElement::FORM));
		$this->TabClass = new forestString;
		$this->TabActiveClass = new forestString;
		$this->ReadOnly = new forestBool;
		
		if ($p_s_tabId != null) {
			$this->TabId->value = $p_s_tabId;
		}
		
		if ($p_s_tabTitle != null) {
			$this->TabTitle->value = $p_s_tabTitle;
		}
		
		if ($p_s_tabClass != null) {
			$this->TabClass->value = $p_s_tabClass;
		}
		
		if ($p_s_tabActiveClass != null) {
			$this->TabActiveClass->value = $p_s_tabActiveClass;
		}
	}
	
	public function __toString() {
		$s_tabId = $this->TabId->value;
		
		if ($this->ReadOnly->value) {
			$s_tabId = 'readonly_' . $s_tabId;
		}
		
		/* render tab container */
		if ($this->Active->value) {
			$s_foo = '<div id="'  . $s_tabId . '" class="' . $this->TabActiveClass->value . '">' . "\n";
		} else {
			$s_foo = '<div id="'  . $s_tabId . '" class="' . $this->TabClass->value . '">' . "\n";
		}
		
		/* render tab form elements */
		$this->PrintFormElements($s_foo);
		
		$s_foo .= '</div>' . "\n";
		
		return $s_foo;
	}
	
	private function PrintFormElements(&$p_s_foo) {
		foreach ($this->FormElements->value as $o_formElement) {
			/* overwrite Form Group Class if it is not set */
			if (issetStr($this->TempFormObject->value->FormGroupClass)) {
				if (!issetStr($o_formElement->FormGroupClass)) {
					$o_formElement->FormGroupClass = $this->TempFormObject->value->FormGroupClass;
				}
			}
			
			/* overwrite Label Class if it is not set */
			if (issetStr($this->TempFormObject->value->LabelClass)) {
				if (!issetStr($o_formElement->LabelClass)) {
					$o_formElement->LabelClass = $this->TempFormObject->value->LabelClass;
				}
			}
			
			/* overwrite Form Element Class if it is not set */
			if (issetStr($this->TempFormObject->value->FormElementClass)) {
				if (!issetStr($o_formElement->FormElementClass)) {
					$o_formElement->FormElementClass = $this->TempFormObject->value->FormElementClass;
				}
			}
			
			/* overwrite Class if it is not set */
			if ( ($o_formElement->getType() != forestFormElement::RADIO) && ($o_formElement->getType() != forestFormElement::CHECKBOX) && ($o_formElement->getType() != forestFormElement::DESCRIPTION) ) {
				if (issetStr($this->TempFormObject->value->ClassAll)) {
					if (!issetStr($o_formElement->Class)) {
						$o_formElement->Class = $this->TempFormObject->value->ClassAll;
					}
				}
			}
			
			/* set required flag for all form elements */
			if ($this->TempFormObject->value->RequiredAll) {
				if ($o_formElement->getType() != forestFormElement::CHECKBOX) {
					$o_formElement->Required = true;
				}
			}
			
			/* set readonly flag for all form elements */
			if ($this->TempFormObject->value->ReadonlyAll) {
				if ( ($o_formElement->getType() != forestFormElement::SELECT) && ($o_formElement->getType() != forestFormElement::LOOKUP) && ($o_formElement->getType() != forestFormElement::COLOR) && ($o_formElement->getType() != forestFormElement::DROPZONE) && ($o_formElement->getType() != forestFormElement::RICHTEXT) && ($o_formElement->getType() != forestFormElement::DESCRIPTION) ) {
					$o_formElement->Readonly = true;
				}
				
				/* other elements do not have readonly flag, instead we are using disabled flag */
				if ( ($o_formElement->getType() == forestFormElement::RICHTEXT) || ($o_formElement->getType() == forestFormElement::RADIO) || ($o_formElement->getType() == forestFormElement::CHECKBOX) || ($o_formElement->getType() == forestFormElement::SELECT) || ($o_formElement->getType() == forestFormElement::LOOKUP) || ($o_formElement->getType() == forestFormElement::COLOR) || ( ($o_formElement->getType() == forestFormElement::BUTTON)  && (!$o_formElement->NoFormGroup) ) ) {
					$o_formElement->Disabled = true;
				}
			}
			
			/* overwrite Radio Class if it is not set */
			if ($o_formElement->getType() == forestFormElement::RADIO) {
				if (issetStr($this->TempFormObject->value->RadioClass)) {
					if (!issetStr($o_formElement->RadioClass)) {
						$o_formElement->RadioClass = $this->TempFormObject->value->RadioClass;
					}
				}
			}
			
			/* overwrite Checkbox Class if it is not set */
			if ($o_formElement->getType() == forestFormElement::CHECKBOX) {
				if (issetStr($this->TempFormObject->value->CheckboxClass)) {
					if (!issetStr($o_formElement->CheckboxClass)) {
						$o_formElement->CheckboxClass = $this->TempFormObject->value->CheckboxClass;
					}
				}
			}
			
			$p_s_foo .= strval($o_formElement);
		}
	}
	
	public function loadJSON($p_s_jsonDataSettings) {
		forestFormElement::JSONSettingsMultilanguage($p_s_jsonDataSettings);
		$a_settings = json_decode($p_s_jsonDataSettings, true);
		
		if ($a_settings != null) {
			foreach (get_object_vars($this) as $s_key => $s_value) {
				if (array_key_exists($s_key, $a_settings)) {
					$this->{$s_key}->value = $a_settings[$s_key];
				}
			}
		} else {
			throw new forestException('Cannot decode json data. Please check json data settings for correct syntax.');
		}
	}
	
	public function CheckIsset($p_b_extended = false) {
		if (!( issetStr($this->TabId->value) && issetStr($this->TabTitle->value) )) {
			throw new forestException('Not all necessary FormTab settings are set.');
		}
		
		if ($p_b_extended) {
			if (!( issetStr($this->TabClass->value) && issetStr($this->TabActiveClass->value) )) {
				throw new forestException('Not all necessary FormTab settings are set.');
			}
		}
	}
	
	public function GetFormElementByFormId($p_s_formId) {
		$o_return = null;
		
		foreach ($this->FormElements->value as $s_key => $o_formElement) {
			if ($o_formElement->Id == $p_s_formId) {
				$o_return = $o_formElement;
				break;
			}
		}
		
		return $o_return;
	}

	public function DeleteFormElementByFormId($p_s_formId) {
		$b_return = false;
		$s_listKey = null;
		
		foreach ($this->FormElements->value as $s_key => $o_formElement) {
			if ($o_formElement->Id == $p_s_formId) {
				$s_listKey = $s_key;
				$b_return = true;
				break;
			}
		}
		
		if ($b_return) {
			$this->FormElements->value->Del($s_key);
		}
		
		return $b_return;
	}
	
	public function AddFormElement(forestFormElement $p_o_formElement, $p_b_first = false) {
		if ($p_b_first) {
			$this->FormElements->value->AddFirst($p_o_formElement);
		} else {
			$this->FormElements->value->Add($p_o_formElement);
		}
		
		return true;
	}
}

class forestTabConfiguration {
	use forestData;
	
	/* Fields */
	
	private $Tab;
	private $TabMenuClass;
	private $TabActiveClass;
	private $TabToggle;
	private $TabContentClass;
	private $TabFooterClass;
	private $TabElementClass;
	private $TabElementActiveClass;
	
	/* Properties */
	
	/* Methods */
	 
	public function __construct(
		$p_b_tab = false,
		$p_s_tabMenuClass = null,
		$p_s_tabActiveClass = null,
		$p_s_tabToggle = null,
		$p_s_tabContentClass = null,
		$p_s_tabFooterClass = null,
		$p_s_tabElementClass = null,
		$p_s_tabElementActiveClass = null
	) {
		$this->Tab = new forestBool($p_b_tab);
		$this->TabMenuClass = new forestString;
		$this->TabActiveClass = new forestString;
		$this->TabToggle = new forestString;
		$this->TabContentClass = new forestString;
		$this->TabFooterClass = new forestString;
		$this->TabElementClass = new forestString;
		$this->TabElementActiveClass = new forestString;
		
		if ($p_s_tabMenuClass != null) {
			$this->TabMenuClass->value = $p_s_tabMenuClass;
		}
		
		if ($p_s_tabActiveClass != null) {
			$this->TabActiveClass->value = $p_s_tabActiveClass;
		}
		
		if ($p_s_tabToggle != null) {
			$this->TabToggle->value = $p_s_tabToggle;
		}
		
		if ($p_s_tabContentClass != null) {
			$this->TabContentClass->value = $p_s_tabContentClass;
		}
		
		if ($p_s_tabFooterClass != null) {
			$this->TabFooterClass->value = $p_s_tabFooterClass;
		}
		
		if ($p_s_tabElementClass != null) {
			$this->TabElementClass->value = $p_s_tabElementClass;
		}
		
		if ($p_s_tabElementActiveClass != null) {
			$this->TabElementActiveClass->value = $p_s_tabElementActiveClass;
		}
	}
	
	public function loadJSON($p_s_jsonDataSettings) {
		forestFormElement::JSONSettingsMultilanguage($p_s_jsonDataSettings);
		$a_settings = json_decode($p_s_jsonDataSettings, true);
		
		if ($a_settings != null) {
			foreach (get_object_vars($this) as $s_key => $s_value) {
				if (array_key_exists($s_key, $a_settings['FormTabConfiguration'])) {
					$this->{$s_key}->value = $a_settings['FormTabConfiguration'][$s_key];
				}
			}
		} else {
			throw new forestException('Cannot decode json data. Please check json data settings for correct syntax.');
		}
	}

	public function CheckIsset() {
		if (!( issetStr($this->TabMenuClass->value) && issetStr($this->TabActiveClass->value) && issetStr($this->TabToggle->value) && issetStr($this->TabContentClass->value) && issetStr($this->TabFooterClass->value) && issetStr($this->TabElementClass->value) && issetStr($this->TabElementActiveClass->value) )) {
			throw new forestException('Not all necessary TabConfiguration settings are set.');
		}
	}
}

class forestModalConfiguration {
	use forestData;
	
	/* Fields */
	
	private $Modal;
	private $ModalClass;
	private $ModalId;
	private $ModalTitle;
	private $ModalRole;
	private $ModalDialogClass;
	private $ModalDialogContentClass;
	private $ModalHeaderClass;
	private $ModalHeaderCloseClass;
	private $ModalHeaderDismissClass;
	private $ModalHeaderCloseContent;
	private $ModalBodyClass;
	private $ModalFooterClass;
	
	/* Properties */
	
	/* Methods */
	 
	public function __construct(
		$p_b_modal = false,
		$p_s_modalClass = null,
		$p_s_modalId = null,
		$p_s_modalTitle = null,
		$p_s_modalRole = null,
		$p_s_modalDialogClass = null,
		$p_s_modalDialogContentClass = null,
		$p_s_modalHeaderClass = null,
		$p_s_modalHeaderCloseClass = null,
		$p_s_modalHeaderDismissClass = null,
		$p_s_modalHeaderCloseContent = null,
		$p_s_modalBodyClass = null,
		$p_s_modalFooterClass = null
	) {
		$this->Modal = new forestBool($p_b_modal);
		$this->ModalClass = new forestString;
		$this->ModalId = new forestString;
		$this->ModalTitle = new forestString;
		$this->ModalRole = new forestString;
		$this->ModalDialogClass = new forestString;
		$this->ModalDialogContentClass = new forestString;
		$this->ModalHeaderClass = new forestString;
		$this->ModalHeaderCloseClass = new forestString;
		$this->ModalHeaderDismissClass = new forestString;
		$this->ModalHeaderCloseContent = new forestString;
		$this->ModalBodyClass = new forestString;
		$this->ModalFooterClass = new forestString;
		
		if ($p_s_modalClass != null) {
			$this->ModalClass->value = $p_s_modalClass;
		}
		
		if ($p_s_modalId != null) {
			$this->ModalId->value = $p_s_modalId;
		}
		
		if ($p_s_modalTitle != null) {
			$this->ModalTitle->value = $p_s_modalTitle;
		}
		
		if ($p_s_modalRole != null) {
			$this->ModalRole->value = $p_s_modalRole;
		}
		
		if ($p_s_modalDialogClass != null) {
			$this->ModalDialogClass->value = $p_s_modalDialogClass;
		}
		
		if ($p_s_modalDialogContentClass != null) {
			$this->ModalDialogContentClass->value = $p_s_modalDialogContentClass;
		}
		
		if ($p_s_modalHeaderClass != null) {
			$this->ModalHeaderClass->value = $p_s_modalHeaderClass;
		}
		
		if ($p_s_modalHeaderCloseClass != null) {
			$this->ModalHeaderCloseClass->value = $p_s_modalHeaderCloseClass;
		}
		
		if ($p_s_modalHeaderDismissClass != null) {
			$this->ModalHeaderDismissClass->value = $p_s_modalHeaderDismissClass;
		}
		
		if ($p_s_modalHeaderCloseContent != null) {
			$this->ModalHeaderCloseContent->value = $p_s_modalHeaderCloseContent;
		}
		
		if ($p_s_modalBodyClass != null) {
			$this->ModalBodyClass->value = $p_s_modalBodyClass;
		}
		
		if ($p_s_modalFooterClass != null) {
			$this->ModalFooterClass->value = $p_s_modalFooterClass;
		}
	}
	
	public function loadJSON($p_s_jsonDataSettings) {
		forestFormElement::JSONSettingsMultilanguage($p_s_jsonDataSettings);
		$a_settings = json_decode($p_s_jsonDataSettings, true);
		
		if ($a_settings != null) {
			foreach (get_object_vars($this) as $s_key => $s_value) {
				if (array_key_exists($s_key, $a_settings['FormModalConfiguration'])) {
					$this->{$s_key}->value = $a_settings['FormModalConfiguration'][$s_key];
				}
			}
		} else {
			throw new forestException('Cannot decode json data. Please check json data settings for correct syntax.');
		}
	}

	public function CheckIsset() {
		if (!( 
			issetStr($this->ModalClass->value) && 
			issetStr($this->ModalId->value) && 
			issetStr($this->ModalTitle->value) && 
			issetStr($this->ModalRole->value) && 
			issetStr($this->ModalDialogClass->value) && 
			issetStr($this->ModalDialogContentClass->value) && 
			issetStr($this->ModalHeaderClass->value) && 
			issetStr($this->ModalHeaderCloseClass->value) && 
			issetStr($this->ModalHeaderDismissClass->value) && 
			issetStr($this->ModalHeaderCloseContent->value) && 
			issetStr($this->ModalBodyClass->value) && 
			issetStr($this->ModalFooterClass->value)
		)) {
			throw new forestException('Not all necessary ModalConfiguration settings are set.');
		}
	}
}

?>