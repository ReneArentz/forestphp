<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.1.3 (0x1 00014)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * abstract class for all branches
 * core functionality for rendering and editing records which are managed in the twig object
 * sub record editing support as well
 * all functions can be overwritten for user specific use case
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.0 alpha	renatus		2019-08-04	first build
 * 0.1.1 alpha	renatus		2019-08-07	added view property and landing page function
 * 0.1.2 alpha	renatus		2019-08-23	added list view and view functionalities + CRUD actions
 * 0.1.3 alpha	renatus		2019-09-05	added formkey and validationrules
 */

abstract class forestBranch {
	use forestData;

	/* Fields */
	
	const LIST = 'list';
	const DETAIL = 'detail';
	
	protected $Twig;
	protected $NextAction;
	protected $Filter;
	protected $StandardView;
	protected $KeepFilter;
	protected $OriginalView;
	
	/* Properties */
	
	/* Methods */
	
	abstract protected function initBranch();
	
	abstract protected function init();
	
	public function __construct() {
		$this->NextAction = new forestBool;
		$this->Filter = new forestBool;
		$this->KeepFilter = new forestBool;
		
		/* call initBranch method to set branch properties within forestBranch objects */
		$this->initBranch();
				
		if (!isset($this->StandardView)) {
			$this->StandardView = forestBranch::LIST;
		}
	
		$o_glob = forestGlobals::init();
		
		$this->OriginalView = $this->StandardView;
		$o_glob->OriginalView = $this->OriginalView;
		
		if (!$o_glob->FastProcessing) {
			/* init navigation object */
			$o_glob->Navigation->InitNavigation();
			
			$i_lastBranchId = 0;
			$i_lastActionId = 0;
			
			/* get using branch and action-id out of session */
			if ($o_glob->Security->SessionData->Exists('lastBranchId')) {
				$i_lastBranchId = $o_glob->Security->SessionData->{'lastBranchId'};
			}
			
			if ($o_glob->Security->SessionData->Exists('lastActionId')) {
				$i_lastActionId = $o_glob->Security->SessionData->{'lastActionId'};
			}
			
			$o_glob->URL->LastBranchId = $i_lastBranchId;
			$o_glob->URL->LastActionId = $i_lastActionId;
			
			/* save filters from last request if you may use them in the new request */
			if ($o_glob->Security->SessionData->Exists('filter')) {
				$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'filter'}, 'last_filter');
			} else {
				$o_glob->Security->SessionData->Del('last_filter');
			}
			
			/* if branch or action-id changes in the new request we delete old filter options in user's session */
			if (($o_glob->URL->BranchId != $i_lastBranchId) || ($o_glob->URL->ActionId != $i_lastActionId)) {
				$o_glob->Security->SessionData->Del('filter');
			}
			
			/* save used branch and action-id in session */
			$o_glob->Security->SessionData->Add($o_glob->URL->BranchId, 'lastBranchId');
			$o_glob->Security->SessionData->Add($o_glob->URL->ActionId, 'lastActionId');
			
			/* if available get twig of branch object */
			if (!isset($this->Twig)) {
				$s_foo = $o_glob->URL->Branch . 'Twig';
				
				if (forestAutoLoad::IsReadable('./twigs/' . $s_foo . '.php')) {
					$this->Twig = new $s_foo;
				} else {
					$this->Twig = null;
					
					if (isset($this->Filter)) {
						$this->Filter->value = false;
					}
				}
			}
		}
		
		/* handle branch's action */
		$s_action = $o_glob->URL->Action;		
		
		if (!issetStr($s_action)) {
			$o_glob->URL->Action = 'init';
		}
		
		/* if filter-flag of branch object is true we init our global filter mask */
		if (isset($this->Filter)) {
			if ( ($this->Filter->value) && (!$o_glob->FastProcessing) ) {
				$this->InitFilter();
			}
		}
		
		do {
			$this->NextAction->value = false;
			
			/* if standard action is 'init' we do not need to attach .'Action' to it */
			$s_action = ($o_glob->URL->Action == 'init') ? $o_glob->URL->Action : $o_glob->URL->Action . 'Action';
			$this->$s_action();
			
			if (!$o_glob->FastProcessing) {
				global $b_transaction_active;
				if ($b_transaction_active) {
					$o_glob->Base->{$o_glob->ActiveBase}->ManualCommit();
				}
			}
			
			/* init global filter mask again, because we do not know how many actions are going to be handled in this do-while-loop */
			if (isset($this->Filter)) {
				if (($this->NextAction->value) && ($this->Filter->value) && (!$o_glob->FastProcessing)) {
					$this->InitFilter();
				}
			}
		} while ($this->NextAction->value);
	}
	
	/* individual branch-classes are calling this method to set next action */
	protected function SetNextAction($p_s_nextAction, $p_s_nextActionAfterReload = null) {
		if ($p_s_nextAction != null) {
			$o_glob = forestGlobals::init();
			$a_branchTree = $o_glob->BranchTree;
			
			if ($p_s_nextAction == 'RELOADBRANCH') {
				if ($p_s_nextActionAfterReload != null) {
					header('Location: '. forestLink::Link($o_glob->URL->Branch, $p_s_nextActionAfterReload));
				} else {
					header('Location: '. forestLink::Link($o_glob->URL->Branch));
				}
			}
			
			/* check if action really exists */
			if (!array_key_exists($p_s_nextAction, $a_branchTree['Id'][$o_glob->URL->BranchId]['actions']['Name'])) {
				throw new forestException('Action[%0] with BranchId[%1] could not be found', array($p_s_nextAction, $o_glob->URL->BranchId));
			}
			
			$o_glob->URL->Action = $p_s_nextAction;
			$o_glob->URL->ActionId = $a_branchTree['Id'][$o_glob->URL->BranchId]['actions']['Name'][$p_s_nextAction];
			
			$this->NextAction->value = true;
		}
	}
	
	/* inits global filter mask for all branches and actions */
	protected function InitFilter() {
		$o_glob = forestGlobals::init();
		
		/* if filter form has been used */
		if ($o_glob->IsPost) {
			/* set filter post action */
			if (array_key_exists('filterSubmit', $_POST)) {
				/* clear page option */
				$o_glob->Limit->Page = 0;
				
				$a_filter = array();
				
				/* get existing filter values out of session */
				if ($o_glob->Security->SessionData->Exists('filter')) {
					$a_filter = $o_glob->Security->SessionData->{'filter'};
				}
				
				/* handle delete action of filter terms */
				if (array_key_exists('deleteFilterColumn', $_POST)) {
					unset($a_filter[$_POST['deleteFilterColumn']]);
				}
				
				/* add new filter to our filter-array if the new line of our filter formular has been used */
				if ((array_key_exists('newFilterColumn', $_POST)) && (array_key_exists('newFilterValue', $_POST))) {
					if ((!empty($_POST['newFilterColumn'])) && (!empty($_POST['newFilterValue']))) {
						$a_filter[$_POST['newFilterColumn']] = $_POST['newFilterValue'];
					}
				}
				
				/* if current filter array is not empty, set the new filter values into session or clear filter values out of session */
				if (!empty($a_filter)) {
					$o_glob->Security->SessionData->Add($a_filter, 'filter');
				} else {
					$o_glob->Security->SessionData->Del('filter');
				}
			}
		}
		
		/* create form for filter */
		$o_glob->FilterForm = new forestForm($this->Twig);
		
		$o_glob->FilterForm->FormObject->Id = $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'FilterForm';
		$o_glob->FilterForm->FormObject->Name = $o_glob->FilterForm->FormObject->Id;
		$o_glob->FilterForm->FormObject->Class = 'form-inline';
		
		$o_filterInput = new forestFormElement(forestFormElement::TEXT);
		$o_filterInput->Id = 'newFilterValue';
		$o_filterInput->Class = 'form-control';
		$o_filterInput->Placeholder = $o_glob->GetTranslation('FilterInputPlaceholder', 1);
		
		$o_filterInputColumn = new forestFormElement(forestFormElement::HIDDEN);
		$o_filterInputColumn->Id = 'newFilterColumn';
		$o_filterInputColumn->Value = '';
		
		$o_filterDeleteColumn = new forestFormElement(forestFormElement::HIDDEN);
		$o_filterDeleteColumn->Id = 'deleteFilterColumn';
		$o_filterDeleteColumn->Value = '';
		
		$o_button = new forestFormElement(forestFormElement::BUTTON);
		$o_button->Class = 'btn btn-default';
		$o_button->Id = 'filterSubmit';
		$o_button->ButtonText = '<span class="glyphicon glyphicon-search"></span>';
		$o_button->WrapSpanClass = 'input-group-btn';
		
		$o_glob->FilterForm->FormElements->Add($o_filterInput);
		$o_glob->FilterForm->FormElements->Add($o_filterInputColumn);
		$o_glob->FilterForm->FormElements->Add($o_filterDeleteColumn);
		$o_glob->FilterForm->FormElements->Add($o_button);
	}
	
	/* handle form key functionality */
	protected function HandleFormKey($p_s_formId = 'NULL', $p_b_postChain = false) {
		$o_glob = forestGlobals::init();
		
		if ($o_glob->Trunk->FormKey) {
			/* delete old invalid form keys */
			$o_DIFormKey = new forestDateInterval($o_glob->Trunk->FormKeyInterval);
			$o_nowFormKey = new forestDateTime($o_glob->Trunk->DateTimeSqlFormat);
			$o_nowFormKey->SubDateInterval($o_DIFormKey->y, $o_DIFormKey->m, $o_DIFormKey->d, $o_DIFormKey->h, $o_DIFormKey->i, $o_DIFormKey->s);
			
			$o_querySelect = new forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, forestSQLQuery::SELECT, 'sys_fphp_formkey');
					
				$o_column_A = new forestSQLColumn($o_querySelect);
					$o_column_A->Column = 'UUID';
				
				$o_column_B = new forestSQLColumn($o_querySelect);
					$o_column_B->Column = 'Timestamp';
				
			$o_querySelect->Query->Columns->Add($o_column_A);
			$o_querySelect->Query->Columns->Add($o_column_B);
				
				$o_where_A = new forestSQLWhere($o_querySelect);
					$o_where_A->Column = $o_column_B;
					$o_where_A->Value = $o_where_A->ParseValue($o_nowFormKey->ToString());
					$o_where_A->Operator = '<=';
					
			$o_querySelect->Query->Where->Add($o_where_A);
			
			$o_formKeys = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect);
			
			if ($o_formKeys->Twigs->Count() > 0) {
				foreach ($o_formKeys->Twigs as $o_formKey) {
					$o_formKey->DeleteRecord();
				}
			}
		
			if ( ($o_glob->IsPost) && (!$p_b_postChain) ) {
				/* check if form key is valid */
				$o_formkeyTwig = new formkeyTwig;
				$s_formkey = 'NULL';
				
				if ($o_glob->Security->SessionData->Exists('formkey')) {
					$s_formkey = $o_glob->Security->SessionData->{'formkey'};
				}
				
				/* if we cannot find form key record or transmitted hash does not match */
				if ( (!$o_formkeyTwig->GetRecordPrimary(
						array(
							$s_formkey,
							$o_glob->Security->SessionUUID,
							$o_glob->URL->BranchId,
							$o_glob->URL->ActionId,
							$p_s_formId
						),
						array(
							'UUID',
							'SessionUUID',
							'BranchId',
							'ActionId',
							'FormId'
						)
					)
				) || (!password_verify($s_formkey, $_POST['sys_fphp_formkeyHash'])) ) {
					throw new forestException(0x10001429);
				} else {
					if (issetStr($o_glob->Trunk->FormKeyMinimumInterval)) {
						$o_DIFormKeyMinimumInterval = new forestDateInterval($o_glob->Trunk->FormKeyMinimumInterval);
						$o_formkeyTwig->Timestamp->AddDateInterval($o_DIFormKeyMinimumInterval->y, $o_DIFormKeyMinimumInterval->m, $o_DIFormKeyMinimumInterval->d, $o_DIFormKeyMinimumInterval->h, $o_DIFormKeyMinimumInterval->i, $o_DIFormKeyMinimumInterval->s);
						$o_now = new forestDateTime($o_glob->Trunk->DateTimeSqlFormat);
						
						/* if minimum time for a form has not expired */
						if ($o_now < $o_formkeyTwig->Timestamp) {
							throw new forestException(0x10001429);
						}
					}
					
					/* after validating the form key, it is obsolete and can be deleted */
					$o_formkeyTwig->DeleteRecord();
				}
			} else {
				/* init form key */
				
				/* delete current form key if form has been reloaded only without post */
				$o_formkeyTwig = new formkeyTwig;
				$s_formkey = 'NULL';
				
				if ($o_glob->Security->SessionData->Exists('formkey')) {
					$s_formkey = $o_glob->Security->SessionData->{'formkey'};
				}
				
				if ($o_formkeyTwig->GetRecord(array($s_formkey))) {
					$o_formkeyTwig->DeleteRecord();
				}
				
				/* create new form key */
				$o_formkeyTwig = new formkeyTwig;
				$o_formkeyTwig->SessionUUID = $o_glob->Security->SessionUUID;
				$o_formkeyTwig->Timestamp = new forestDateTime($o_glob->Trunk->DateTimeSqlFormat);
				$o_formkeyTwig->BranchId = $o_glob->URL->BranchId;
				$o_formkeyTwig->ActionId = $o_glob->URL->ActionId;
				$o_formkeyTwig->FormId = $p_s_formId;
				
				$o_formkeyTwig->InsertRecord();
				
				/* insert new form key into session */
				$o_glob->Security->SessionData->Add($o_formkeyTwig->UUID, 'formkey');
			}
		}
	}
		
	
	/* generates landing page */
	protected function GenerateLandingPage() {
		$o_glob = forestGlobals::init();
		$s_landingPageNavigation = $o_glob->Navigation->RenderLandingPage();
		
		/* use template to render landing page */
		$o_glob->Templates->Add(new forestTemplates(forestTemplates::LANDINGPAGE, array($s_landingPageNavigation)), $o_glob->URL->Branch . 'LandingPage');
	}
	
	
	/* generates list view */
	protected function GenerateListView() {
		$o_glob = forestGlobals::init();
		
		$o_records = $this->Twig->GetAllViewRecords();
		$o_glob->BackupLimit();
		
		$o_options = '<div class="btn-group" style="display:inline-block;">' . "\n";
		
		/* new link */
		$a_parameters = $o_glob->URL->Parameters;
		unset($a_parameters['newKey']);
		unset($a_parameters['viewKey']);
		unset($a_parameters['editKey']);
		unset($a_parameters['deleteKey']);
		unset($a_parameters['editSubKey']);
		unset($a_parameters['deleteSubKey']);
		unset($a_parameters['deleteFileKey']);
		unset($a_parameters['subConstraintKey']);
		
		$o_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'new', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="glyphicon glyphicon-plus text-success"></span></a>' . "\n";
		
		/* edit link */
		$a_parameters = $o_glob->URL->Parameters;
		unset($a_parameters['newKey']);
		unset($a_parameters['viewKey']);
		unset($a_parameters['editKey']);
		unset($a_parameters['deleteKey']);
		unset($a_parameters['editSubKey']);
		unset($a_parameters['deleteSubKey']);
		unset($a_parameters['deleteFileKey']);
		unset($a_parameters['subConstraintKey']);
		$a_parameters['editKey'] = 'inserteditkey';
		
		$o_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'edit', $a_parameters) . '" id="' . $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'Edit" class="btn btn-default a-button-edit-record disabled" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="glyphicon glyphicon-pencil"></span></a>' . "\n";
		
		/* delete link */
		$a_parameters = $o_glob->URL->Parameters;
		unset($a_parameters['newKey']);
		unset($a_parameters['viewKey']);
		unset($a_parameters['editKey']);
		unset($a_parameters['deleteKey']);
		unset($a_parameters['editSubKey']);
		unset($a_parameters['deleteSubKey']);
		unset($a_parameters['deleteFileKey']);
		unset($a_parameters['subConstraintKey']);
		$a_parameters['deleteKey'] = 'insertdeletekey';
		
		$o_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'delete', $a_parameters) . '" id="' . $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'Delete" class="btn btn-default a-button-delete-record disabled" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="glyphicon glyphicon-trash text-danger"></span></a>' . "\n";
		
		/* details link */
		$a_parameters = $o_glob->URL->Parameters;
		unset($a_parameters['viewKey']);
		unset($a_parameters['editKey']);
		unset($a_parameters['deleteKey']);
		unset($a_parameters['editSubKey']);
		unset($a_parameters['deleteSubKey']);
		unset($a_parameters['deleteFileKey']);
		unset($a_parameters['subConstraintKey']);
		
		$o_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, (($this->OriginalView == forestBranch::LIST) ? 'view' : null), $a_parameters) . '" class="btn btn-default"><span class="glyphicon glyphicon-eye-open text-info" title="' . $o_glob->GetTranslation('btnDetailsText', 1) . '"></span></a>' . "\n";
		
		/* add columns link */
		$o_options .= '<a href="#" class="btn btn-default modal-call" data-modal-call="#' . $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'ListViewAddColumns" title="' . $o_glob->GetTranslation('btnAddColumnsText', 1) . '"><span class="glyphicon glyphicon-cog"></span></a>' . "\n";
		
		$o_options .= '</div>' . "\n";
		
		$s_tableHead = '';
		
		/* render table head */
		foreach ($this->Twig->fphp_View as $s_columnHead) {
			if ($o_glob->TablefieldsDictionary->Exists($this->Twig->fphp_Table . '_' . $s_columnHead)) {
				$s_formElement = $o_glob->TablefieldsDictionary->{$this->Twig->fphp_Table . '_' . $s_columnHead}->FormElementName;
				$s_forestData = $o_glob->TablefieldsDictionary->{$this->Twig->fphp_Table . '_' . $s_columnHead}->ForestDataName;
			}
			
			if ( ($s_formElement == forestFormElement::FORM) || ($s_formElement == forestFormElement::PASSWORD) ) {
				continue;
			}
			
			$s_tableHead .= '<th>' . $o_glob->GetSort($s_columnHead)->ToString($o_glob->GetTranslation('sort' . $s_columnHead)) . '</th>' . "\n";
		}
		
		$s_tableRows = '';
		
		/* render table rows */
		if ($o_records->Twigs->Count() > 0) {
			foreach ($o_records->Twigs as $o_record) {
				$s_tableRows .=  '<tr';
				
				if ($o_record->fphp_HasUUID) {
					$s_tableRows .= ' data-fphp_uuid="' . $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . ';' . $o_record->UUID;
				}
				
				$s_tableRows .= '">' . "\n";
				
				foreach ($this->Twig->fphp_View as $s_column) {
					if ($o_glob->TablefieldsDictionary->Exists($this->Twig->fphp_Table . '_' . $s_column)) {
						$s_formElement = $o_glob->TablefieldsDictionary->{$this->Twig->fphp_Table . '_' . $s_column}->FormElementName;
						$s_forestData = $o_glob->TablefieldsDictionary->{$this->Twig->fphp_Table . '_' . $s_column}->ForestDataName;
					}
					
					if ( ($s_formElement == forestFormElement::FORM) || ($s_formElement == forestFormElement::PASSWORD) ) {
						continue;
					}
					
					$s_value = '-';
					/* render value */
					$this->ListViewRenderColumnValue($s_value, $s_formElement, $s_column, $o_record, $this->Twig->fphp_Table . '_' . $s_column);
					
					$s_tableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
				}
					
				$s_tableRows .=  '</tr>' . "\n";
			}
		} else {
			/* no records found */
			$o_glob->SystemMessages->Add(new forestException(0x10001400));
		}
		
		/* create modal form for adding hidden columns */
		$o_addColumnsForm = new forestForm($this->Twig);
		
		$this->GenerateHiddenColumnsModal($o_addColumnsForm);
		
		/* create search form */
		$s_searchForm = '';
		$s_searchTerms = '';
		$this->RenderSearchForm($s_searchForm, $s_searchTerms);
		
		$o_glob->RestoreLimit();
		$o_glob->Security->SessionData->Add(forestBranch::LIST, 'lastView');
		
		$o_listViewOptionsTop = new forestTemplates(forestTemplates::LISTVIEWOPTIONSTOP, array( $o_options, strval($o_glob->Limit), $s_searchForm, $s_searchTerms, strval($o_addColumnsForm) ));
		$o_listViewOptionsDown = new forestTemplates(forestTemplates::LISTVIEWOPTIONSDOWN, array($o_options, strval($o_glob->Limit)));
		
		/* use template to render general list view */
		$o_glob->Templates->Add(new forestTemplates(forestTemplates::LISTVIEW, array($o_listViewOptionsTop, $s_tableHead, $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'ListView', $s_tableRows, $o_listViewOptionsDown)), $o_glob->URL->Branch . 'ListView');
	}
	
	/* render column value for list view */
	protected function ListViewRenderColumnValue(&$p_s_value, $p_s_formElement, $p_s_column, $p_o_record, $p_s_dictionaryKey) {
		$o_glob = forestGlobals::init();
		
		if ( ($p_s_formElement == forestFormElement::CHECKBOX) && ($o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->ForestDataName == 'forestBool') ) {
			/* render checkbox value as glyphicon icons */
			if (boolval($p_o_record->{$p_s_column}) == true) {
				$p_s_value = '<span class="glyphicon glyphicon-ok text-success"></span>';
			} else {
				$p_s_value = '<span class="glyphicon glyphicon-remove text-danger"></span>';
			}
		} else if ($p_s_formElement == forestFormElement::WEEK) {
			/* render calender week value */
			if (issetStr($p_o_record->{$p_s_column})) {
				$s_year = substr($p_o_record->{$p_s_column}, 0, 4);
				
				$s_cw = substr($p_o_record->{$p_s_column}, (strlen($p_o_record->{$p_s_column}) - 2), 2);
				
				$p_s_value = $s_year . ' ' . $o_glob->GetTranslation('calenderWeek', 1) . $s_cw;
			}
		} else if ($p_s_formElement == forestFormElement::MONTH) {
			/* render month value */
			if (strval($p_o_record->{$p_s_column}) != 'NULL') {
				$p_s_value = $o_glob->GetTranslation('month' . $p_o_record->{$p_s_column}->ToString('F'), 1) . ' ' . $p_o_record->{$p_s_column}->ToString('Y');
			}
		} else if ($p_s_formElement == forestFormElement::TIME) {
			/* render time value */
			if (strval($p_o_record->{$p_s_column}) != 'NULL') {
				$p_s_value = $p_o_record->{$p_s_column}->ToString($o_glob->Trunk->TimeFormat);
			}
		} else if ($p_s_formElement == forestFormElement::DATE) {
			/* render date value */
			if (strval($p_o_record->{$p_s_column}) != 'NULL') {
				$p_s_value = $p_o_record->{$p_s_column}->ToString($o_glob->Trunk->DateFormat);
			}
		} else if ($p_s_formElement == forestFormElement::DATETIMELOCAL) {
			/* render datetime value */
			if (strval($p_o_record->{$p_s_column}) != 'NULL') {
				$p_s_value = $p_o_record->{$p_s_column}->ToString($o_glob->Trunk->DateTimeFormat);
			}
		} else if ($p_s_formElement == forestFormElement::URL) {
			/* render url value as usable link element */
			if (issetStr($p_o_record->{$p_s_column})) {
				$p_s_value = '<a href="' . $p_o_record->{$p_s_column} . '" target="_blank" title="' . $p_o_record->{$p_s_column} . '"><span class="glyphicon glyphicon-link"></span></a>';
			}
		} else if ($p_s_formElement == forestFormElement::COLOR) {
			/* render color value by showing a small rectangle with the color as background color */
			if (issetStr($p_o_record->{$p_s_column})) {
				$p_s_value = '<span style="padding:3px 10px; background-color: ' . $p_o_record->{$p_s_column} . ';"></span>';
			}
		} else if ($p_s_formElement == forestFormElement::RADIO) {
			/* render radio value by evaluating json settings */
			$o_tempFormElement = new forestFormElement($p_s_formElement);
			$o_tempFormElement->loadJSON($o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->JSONEncodedSettings);
			
			if (count($o_tempFormElement->Options) > 0) {
				$b_isAssoc = ( array_keys($o_tempFormElement->Options) !== range(0, count($o_tempFormElement->Options) - 1) );
				
				foreach ($o_tempFormElement->Options as $s_option_label => $s_option_value) {
					if ($p_o_record->{$p_s_column} == intval($s_option_value)) {
						$p_s_value = (($b_isAssoc) ? $s_option_label : $s_option_value);
					}
				}
			}
		} else if ($p_s_formElement == forestFormElement::SELECT) {
			/* render select value by evaluating json settings, show as small labels */
			if (issetStr($p_o_record->{$p_s_column})) {
				$p_s_value = '';
				
				$o_tempFormElement = new forestFormElement($p_s_formElement);
				$o_tempFormElement->loadJSON($o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->JSONEncodedSettings);
				
				$a_valueOptions = array();

				if (strpos($p_o_record->{$p_s_column}, ';') !== false) {
					$a_valueOptions = explode(';', $p_o_record->{$p_s_column});
				}
				
				if (count($o_tempFormElement->Options) > 0) {
					$b_isAssoc = ( array_keys($o_tempFormElement->Options) !== range(0, count($o_tempFormElement->Options) - 1) );
					
					foreach ($o_tempFormElement->Options as $s_option_label => $s_option_value) {
						if (is_array($s_option_value)) {
							if (count($s_option_value) > 0) {
								$b_isAssocOpt = ( array_keys($s_option_value) !== range(0, count($s_option_value) - 1) );
								
								foreach ($s_option_value as $s_optgroup_option_label => $s_optgroup_option_value) {
									if (($p_o_record->{$p_s_column} == $s_optgroup_option_value) || (in_array($s_optgroup_option_value, $a_valueOptions))) {
										$p_s_value .= ' ';
										
										if (!empty($a_valueOptions)) {
											$p_s_value .= '<span class="label label-default">';
										}
										
										$p_s_value .= (($b_isAssocOpt) ? $s_optgroup_option_label : $s_optgroup_option_value);
										
										if (!empty($a_valueOptions)) {
											$p_s_value .= '</span>';
										}
									}
								}
							}
						} else {
							if (($p_o_record->{$p_s_column} == $s_option_value) || (in_array($s_option_value, $a_valueOptions))) {
								$p_s_value .= ' ';
										
								if (!empty($a_valueOptions)) {
									$p_s_value .= '<span class="label label-default">';
								}
								
								$p_s_value .= (($b_isAssoc) ? $s_option_label : $s_option_value);
								
								if (!empty($a_valueOptions)) {
									$p_s_value .= '</span>';
								}
							}
						}
					}
				}
			}
		} else if ( ($p_s_formElement == forestFormElement::CHECKBOX) && ($o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->ForestDataName == 'forestInt') ) {
			/* render checkbox value with multiple elements, show as small labels */
			$p_s_value = '';
				
			$o_tempFormElement = new forestFormElement($p_s_formElement);
			$o_tempFormElement->loadJSON($o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->JSONEncodedSettings);
			$i = 0;
			$b_hasValue = false;
			
			if (count($o_tempFormElement->Options) > 0) {
				$b_isAssoc = ( array_keys($o_tempFormElement->Options) !== range(0, count($o_tempFormElement->Options) - 1) );
				$i_results = substr_count(strval($p_o_record->{$p_s_column}), '1');
				
				foreach ($o_tempFormElement->Options as $s_option_label => $s_option_value) {
					if (strlen($p_o_record->{$p_s_column}) > $i) {
						if (strval($p_o_record->{$p_s_column})[( strlen($p_o_record->{$p_s_column}) - $i - 1 )] == '1') {
							$p_s_value .= ' ';
							
							if ($i_results > 1) {
								$p_s_value .= '<span class="label label-default">';
							}
							
							$p_s_value .= (($b_isAssoc) ? $s_option_label : $s_option_value);
							
							if ($i_results > 1) {
								$p_s_value .= '</span>';
							}
							
							$b_hasValue = true;
						}
					}
					
					$i++;
				}
			}
			
			if (!$b_hasValue) {
				$p_s_value = '-';
			}
		} else if ( ($o_glob->TablefieldsDictionary->Exists($p_s_dictionaryKey)) && ($o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->ForestDataName == 'forestString') ) {
			/* render string value */
			if (issetStr($p_o_record->{$p_s_column})) {
				$p_s_value = $p_o_record->{$p_s_column};
			}
			
			/* render date interval */
			if ($o_glob->TablefieldsDictionary->Exists($p_s_dictionaryKey)) {
				$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_glob->TablefieldsDictionary->{$p_s_dictionaryKey}->JSONEncodedSettings);
				$a_settings = json_decode($s_JSONEncodedSettings, true);
				
				if (!empty($a_settings)) {
					if (array_key_exists('DateIntervalFormat', $a_settings)) {
						/* check if we want to render value as date interval value */
						if ($a_settings['DateIntervalFormat']) {
							$p_s_value = strval(new forestDateInterval($p_s_value));
						}
					}
				}
			}
		} else {
			/* standard rendering value */
			if (issetStr($p_o_record->{$p_s_column})) {
				$p_s_value = $p_o_record->{$p_s_column};
			}
		}
	}
	
	/* create modal form for selecting hidden columns */
	protected function GenerateHiddenColumnsModal($p_o_form) {
		$o_glob = forestGlobals::init();
		
		/* get table record */
		$o_tableTwig = new tableTwig;
		
		/* query table record */
		if (!($o_tableTwig->GetRecordPrimary(array($this->Twig->fphp_Table), array('Name')))) {
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
		$p_o_form->FormObject->loadJSON($s_formObjectJSONsettings);
		$p_o_form->FormModalConfiguration->loadJSON($s_formObjectJSONsettings);
		$p_o_form->FormTabConfiguration->loadJSON($s_formObjectJSONsettings);
		
		/* because it switches back to detail view when original is detail */
		if ($this->OriginalView != $this->StandardView) {
			$s_action = 'view';
		} else {
			$s_action = $o_glob->URL->Action;
		}
		
		$p_o_form->FormObject->Id = $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'ListViewAddColumnsForm';
		$p_o_form->FormObject->Name = $p_o_form->FormObject->Id;
		
		/* adjust form action link */
		$a_parameters = $o_glob->URL->Parameters;
		unset($a_parameters['viewKey']);
		unset($a_parameters['editKey']);
		unset($a_parameters['deleteKey']);
		$a_parameters['-hiddencolumns'] = '-hiddencolumnsval';
		$p_o_form->FormObject->Action = forestLink::Link($o_glob->URL->Branch, $s_action, $a_parameters);
		
		/* adjust modal id and title */
		$p_o_form->FormModalConfiguration->ModalId = $o_glob->URL->Branch . $o_glob->URL->Action . $this->Twig->fphp_Table . 'ListViewAddColumns';
		$p_o_form->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('AddColumnsModalTitle', 1);
		$p_o_form->FormTabConfiguration->Tab = false;
		
		/* create select form element for choosing hidden columns */
		$o_select = new forestFormElement(forestFormElement::SELECT);
		$o_select->Label = $o_glob->GetTranslation('AddColumnsLabel', 1);
		$o_select->Id = 'selectHiddenColumns';
		$o_select->Class = 'form-control select-modal-call-add-column';
		$o_select->Size = 8;
		$o_select->Multiple = true;
		$o_select->Data = 'columns=""';
		$o_select->ValMessage = $o_glob->GetTranslation('AddColumnsValMessage', 1);
		
		$a_hidden_columns = array();
		
		/* get hidden columns */
		$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $this->Twig->fphp_TableUUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		foreach ($o_tablefields->Twigs as $o_tablefield) {
			$s_column = $o_tablefield->FieldName;
			
			/* skip standard Id + UUID column, and all columns which are already shown */
			if ( ($s_column != 'Id') && ($s_column != 'UUID') && (!in_array($s_column, $this->Twig->fphp_View)) ) {
				/* skip fields which have no tablefield setting in database */
				if (!$o_glob->TablefieldsDictionary->Exists($this->Twig->fphp_Table . '_' . $s_column)) {
					continue;
				}
				
				$s_formElement = $o_glob->TablefieldsDictionary->{$this->Twig->fphp_Table . '_' . $s_column}->FormElementName;
				$s_forestData = $o_glob->TablefieldsDictionary->{$this->Twig->fphp_Table . '_' . $s_column}->ForestDataName;
				
				/* skip other tablefields which have no use in a list view */
				if ( ($s_formElement == forestFormElement::FORM) || ($s_formElement == forestFormElement::PASSWORD) ) {
					continue;
				}
				
				$a_hidden_columns[$o_glob->GetTranslation('sort' . $s_column)] = $s_column;
			}
		}
		
		$o_select->Options = $a_hidden_columns;
		
		$o_description = new forestFormElement(forestFormElement::DESCRIPTION);
		$o_description->Description = $o_glob->GetTranslation('AddColumnsDescription', 1);
		
		if (count($a_hidden_columns) > 0) {
			$p_o_form->FormElements->Add($o_select);
		} else {
			$p_o_form->FormElements->Add($o_description);
		}
		
		/* render cancel button */
		if (!($o_formelementTwig->GetRecordPrimary(array('cancel'), array('Name')))) {
			throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		}
		
		$o_cancel = new forestFormElement(forestFormElement::BUTTON);
		$o_cancel->loadJSON($o_formelementTwig->JSONEncodedSettings);
		$o_cancel->ButtonText = htmlspecialchars_decode($o_cancel->ButtonText);
		$p_o_form->FormFooterElements->Add($o_cancel);
		
		/* render submit button */
		if (count($a_hidden_columns) > 0) {
			if (!($o_formelementTwig->GetRecordPrimary(array('submit'), array('Name')))) {
				throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
			}
			
			$o_button = new forestFormElement(forestFormElement::BUTTON);
			$o_button->loadJSON($o_formelementTwig->JSONEncodedSettings);
			$o_button->Class = 'btn btn-success btn-default pull-right button-modal-call-add-column';
			$o_button->Data = 'form_id="' . $p_o_form->FormObject->Id . '"';
			$o_button->ButtonText = htmlspecialchars_decode($o_button->ButtonText);
			$p_o_form->FormFooterElements->Add($o_button);
		}
	}
	
	/* render search form */
	protected function RenderSearchForm(&$p_s_searchForm, &$p_s_searchTerms) {
		$o_glob = forestGlobals::init();
		
		if (isset($this->Filter)) {
			if ($this->Filter->value) {
				$p_s_searchForm .= strval($o_glob->FilterForm->FormObject);
				
				$p_s_searchForm .= '
				<div class="input-group">
					<div class="input-group-btn filter-panel">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							<span id="filterDropDownButton">' . $o_glob->GetTranslation('FilterDropDownText', 1) . '</span> <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">';
							
							$b_columns = false;
							
							foreach ($this->Twig->fphp_Mapping as $s_column) {
								/* skip fields which are already used within filter */
								if ($o_glob->Security->SessionData->Exists('filter')) {
									if ( (in_array($s_column, array_keys($o_glob->Security->SessionData->{'filter'}))) || (in_array('FilterAllColumns', array_keys($o_glob->Security->SessionData->{'filter'}))) ) {
										continue;
									}
								}
								
								/* skip standard Id + UUID column */
								if ( ($s_column != 'Id') && ($s_column != 'UUID') ) {
									/* add only tablefields which have a configuration in the database */
									if ($o_glob->TablefieldsDictionary->Exists($this->Twig->fphp_Table . '_' . $s_column)) {
										/* skip Password fields */
										if ($o_glob->TablefieldsDictionary->{$this->Twig->fphp_Table . '_' . $s_column}->FormElementName != forestFormElement::PASSWORD) {
											$p_s_searchForm .= '<li><a href="#' . $s_column . '">' . $o_glob->GetTranslation('sort' . $s_column) . '</a></li>';
											$b_columns = true;
										}
									}
								}
							}
							
							$b_all = true;
							
							/* if we are using at least one column for filtering, we cannot use option to filter "all columns" anymore */
							if ($o_glob->Security->SessionData->Exists('filter')) {
								if (count($o_glob->Security->SessionData->{'filter'}) > 0) {
									$b_all = false;
								}
							}
							
							if ($b_all) {
								$p_s_searchForm .= '<li class="divider"></li>
								<li><a href="#FilterAllColumns">' . $o_glob->GetTranslation('FilterAllColumnsText', 1) . '</a></li>';
							}
							
							/* if we have no columns, add disabled option that all columns are in use right now */
							if (!$b_columns) {
								$p_s_searchForm .= '<li class="disabled"><a href="#">' . $o_glob->GetTranslation('FilterAllColumnsUsedText', 1) . '</a></li>';
							}
							
						$p_s_searchForm .= '</ul>
					</div>' . "\n";
				
				foreach ($o_glob->FilterForm->FormElements as $o_formElement) {
					$p_s_searchForm .= strval($o_formElement);
				}
				
				$p_s_searchForm .= '</div>' . "\n";
				$p_s_searchForm .= '</form>' . "\n";
				
				/* render currently used filter values and show them as labels next to search form */
				if ($o_glob->Security->SessionData->Exists('filter')) {
					foreach ($o_glob->Security->SessionData->{'filter'} as $s_filterColumn => $s_filterValue) {
						if ($o_glob->TablefieldsDictionary->Exists($this->Twig->fphp_Table . '_' . $s_filterColumn)) {
							if ($o_glob->TablefieldsDictionary->{$this->Twig->fphp_Table . '_' . $s_filterColumn}->FormElementName == forestFormElement::PASSWORD) {
								continue;
							}
						}
						
						$s_displayFilterColumn = $o_glob->GetTranslation('sort' . $s_filterColumn);
						$s_filterValue = htmlspecialchars($s_filterValue, ( ENT_QUOTES | ENT_HTML5 ));
						$p_s_searchTerms .= '<span class="tag label label-primary">' . (($s_filterColumn == 'FilterAllColumns') ? '<span class="glyphicon glyphicon-asterisk"></span> ' : $s_displayFilterColumn) . ': ' . $s_filterValue . ' <a href="#' . $s_filterColumn . '"><span class="remove glyphicon glyphicon-remove-sign glyphicon-white"></span></a></span></span>' . "\n";
					}
				}
			}
		}
	}
	
	/* generates detail view */
	protected function GenerateView() {
		$o_glob = forestGlobals::init();
		
		if ( ($o_glob->Security->SessionData->Exists('last_filter')) && (!$o_glob->IsPost) ) {
			$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
		}
		
		/* set limit interval to 1, because of detail view (one record on one page) */
		$this->Twig->fphp_Interval = 1;
		
		/* nevertheless query all records */
		$o_records = $this->Twig->GetAllRecords();
		$o_glob->BackupLimit();
		
		$s_form = '';
		$s_title = '';
		$s_subRecords = '';
		
		if ($o_records->Twigs->Count() > 0) {
			$o_options = '<div class="btn-group">' . "\n";
			
			/* new link */
			$o_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'new') . '" class="btn btn-default"  title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="glyphicon glyphicon-plus text-success"></span></a>' . "\n";
			
			/* edit link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['editKey'] = $o_records->Twigs->{0}->UUID;
			
			$o_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'edit', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="glyphicon glyphicon-pencil"></span></a>' . "\n";
			
			/* delete link */
			$o_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'delete', array('deleteKey' => $o_records->Twigs->{0}->UUID)) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="glyphicon glyphicon-trash text-danger"></span></a>' . "\n";
			
			/* list link */
			$o_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, (($this->OriginalView == forestBranch::LIST) ? null : 'view')) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnListText', 1) . '"><span class="glyphicon glyphicon-th-list text-info"></span></a>' . "\n";
			
			$o_options .= '</div>' . "\n";
			
			/* create form with record in read only mode */
			$s_form = new forestForm($o_records->Twigs->{0}, true, true, false);
			$s_form->FormModalConfiguration->Modal = false;
			
			/* adjust title */
			$s_title = $o_glob->URL->BranchTitle . ' ' . $o_glob->GetTranslation('BranchTitleRecord', 1);
		} else {
			$o_options = '<div class="btn-group">' . "\n";
			
			$o_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'new') . '" class="btn btn-default"  title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="glyphicon glyphicon-plus text-success"></span></a>' . "\n";
			$o_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'edit') . '" class="btn btn-default disabled"  title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="glyphicon glyphicon-pencil"></span></a>' . "\n";
			$o_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'delete') . '" class="btn btn-default disabled"  title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="glyphicon glyphicon-trash text-danger"></span></a>' . "\n";
			$o_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, (($this->OriginalView == forestBranch::LIST) ? null : 'view')) . '" class="btn btn-default"  title="' . $o_glob->GetTranslation('btnListText', 1) . '"><span class="glyphicon glyphicon-th-list text-info"></span></a>' . "\n";
			
			$o_options .= '</div>' . "\n";
			
			/* no records found */
			$o_glob->SystemMessages->Add(new forestException(0x10001400));
		}
		
		/* create search form */
		$s_searchForm = '';
		$s_searchTerms = '';
		$this->RenderSearchForm($s_searchForm, $s_searchTerms);
		
		$o_glob->RestoreLimit();
		$o_glob->Security->SessionData->Add(forestBranch::DETAIL, 'lastView');
		
		$o_viewOptionsTop = new forestTemplates(forestTemplates::VIEWOPTIONSTOP, array( $o_options, strval($o_glob->Limit), $s_searchForm, $s_searchTerms ));
		$o_viewOptionsDown = new forestTemplates(forestTemplates::VIEWOPTIONSDOWN, array($o_options, strval($o_glob->Limit)));
		
		/* use template to render general view */
		$o_glob->Templates->Add(new forestTemplates(forestTemplates::VIEW, array($o_viewOptionsTop, $s_title, '', strval($s_form), $s_subRecords, $o_viewOptionsDown)), $o_glob->URL->Branch . 'View');
	}
	
	
	/* handle view record action */
	protected function ViewRecord() {
		$o_glob = forestGlobals::init();
		
		if ($this->StandardView == forestBranch::DETAIL) {
			$this->GenerateListView();
		} else if ($this->StandardView == forestBranch::LIST) {
			$this->GenerateView();
		}
	}
	
	
	/* handle new record action */
	protected function NewRecord() {
		$o_glob = forestGlobals::init();
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			/* add new record */
			$o_glob->PostModalForm = new forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
		} else {
			/* check posted data for new record */
			$this->TransferPOST_Twig();
			
			if (method_exists($this, 'beforeNewAction')) {
				$this->beforeNewAction();
			}
			
			/* insert record */
			$i_result = $this->Twig->InsertRecord();
			
			if (method_exists($this, 'afterNewAction')) {
				$this->afterNewAction();
			}
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				throw new forestException(0x10001402);
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001404));
				
			}
		}
		
		if (isset($this->KeepFilter)) {
			if ($this->KeepFilter->value) {
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
			}
		}
		
		$this->SetNextAction('init');
	}
	
	
	/* handle edit record action */
	protected function EditRecord() {
		$o_glob = forestGlobals::init();
		
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editKey'), 'editKey' );
		
		if ($o_glob->IsPost) {
			$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
			
			/* check posted data for edit record */
			
			/* query record */
			if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_editKey']))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}
			
			$this->TransferPOST_Twig();
			
			if (method_exists($this, 'beforeEditAction')) {
				$this->beforeEditAction();
			}
			
			/* edit recrod */
			$i_result = $this->Twig->UpdateRecord();
			
			if (method_exists($this, 'afterEditAction')) {
				$this->afterEditAction();
			}
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				$o_glob->SystemMessages->Add(new forestException(0x10001406));
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001407));
			}
		}
		
		/* if we have choosen multiple records for edit in general list view */
		if ( ($o_glob->Temp->Exists('editKey')) && ($o_glob->Temp->{'editKey'} != null) ) {
			$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form', true);
			
			/* get record keys */
			$a_editKeys = explode('~', $o_glob->Temp->{'editKey'});
			
			/* query record */
			if (! ($this->Twig->GetRecord(array($a_editKeys[0]))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}
			/* build modal form */
			$o_glob->PostModalForm = new forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('EditModalTitle', 1);
			$a_newEditKeys = array();
			
			/* adjust form action parameters for edit keys - remove one key which is opened in current modal form */
			foreach ($a_editKeys as $s_editKey) {
				if ($s_editKey != $a_editKeys[0]) {
					$a_newEditKeys[] = $s_editKey;
				}
			}
			
			$a_parameters = array();
			
			foreach ($o_glob->URL->Parameters as $s_key => $s_value) {
				if ( ($s_key != 'viewKey') && ($s_key != 'editKey') && ($s_key != 'deleteKey') && ($s_key != 'editSubKey') && ($s_key != 'deleteSubKey') && ($s_key != 'deleteFileKey') && ($s_key != 'subConstraintKey') ) {
					$a_parameters[$s_key] = $s_value;
				}
			}
			
			$a_parameters['editKey'] = implode('~', $a_newEditKeys);
			
			$o_glob->PostModalForm->FormObject->Action = forestLink::Link($o_glob->URL->Branch, $o_glob->URL->Action, $a_parameters);
			
			/* add current record key to modal form */
			$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
			$o_hidden->Id = 'sys_fphp_editKey';
			$o_hidden->Value = $a_editKeys[0];
			
			$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
		}
		
		if (isset($this->KeepFilter)) {
			if ($this->KeepFilter->value) {
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
			}
		}
		
		$this->SetNextAction('init');
	}
	
	/* write POST data into twig property fields */
	protected function TransferPOST_Twig() {
		$o_glob = forestGlobals::init();
		
		/* iterate each column of table twig for possible post data */
		foreach ($this->Twig->fphp_Mapping as $s_column) {
			/* skip standard Id + UUID column */
			if ( ($s_column != 'Id') && ($s_column != 'UUID') ) {
				/* set tablefield identifier */
				$s_tableFieldIdentifier = $this->Twig->fphp_Table . '_' . $s_column;
				$s_forestData = '';
				
				/* check if tablefield is in dictionary */
				if ($o_glob->TablefieldsDictionary->Exists($s_tableFieldIdentifier)) {
					$s_forestData = $o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->ForestDataName;
				}
				
				if (array_key_exists($this->Twig->fphp_Table . '_' . $s_column, $_POST)) {
					/* cast post data to twig property data fields */
					if (($s_forestData == 'forestNumericString') || ($s_forestData == 'forestNumericString(1)') || ($s_forestData == 'forestInt')) {
						if (is_array($_POST[$this->Twig->fphp_Table . '_' . $s_column])) {
							/* post value is array, so we need to valiate multiple checkboxes */
							$i_sum = 0;
							
							foreach ($_POST[$this->Twig->fphp_Table . '_' . $s_column] as $s_checkboxValue) {
								if (!preg_match('/[^01$]/', $s_checkboxValue)) {
									$i_sum += intval($s_checkboxValue);
								}
							}
							
							$this->Twig->{$s_column} = $i_sum;
						} else {
							if ( ($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->FormElementName == forestFormElement::RADIO) || ($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->FormElementName == forestFormElement::CHECKBOX) ) {
								if (!preg_match('/[^01$]/', $_POST[$this->Twig->fphp_Table . '_' . $s_column])) {
									$this->Twig->{$s_column} = intval($_POST[$this->Twig->fphp_Table . '_' . $s_column]);
								} else {
									$this->Twig->{$s_column} = 0;
								}
							} else {
								$this->Twig->{$s_column} = intval($_POST[$this->Twig->fphp_Table . '_' . $s_column]);
							}
						}
					} else if ($s_forestData == 'forestFloat') {
						$this->Twig->{$s_column} = floatval($_POST[$this->Twig->fphp_Table . '_' . $s_column]);
					} else if ($s_forestData == 'forestBool') {
						$this->Twig->{$s_column} = boolval($_POST[$this->Twig->fphp_Table . '_' . $s_column]);
					} else if ($s_forestData == 'forestArray') {
						$this->Twig->{$s_column} = null;
					} else if ($s_forestData == 'forestObject') {
						$this->Twig->{$s_column} = null;
					} else if ($s_forestData == 'forestObject(&apos;forestDateTime&apos;)') {
						$this->Twig->{$s_column} = forestStringLib::TextToDate($_POST[$this->Twig->fphp_Table . '_' . $s_column]);
					} else if ($s_forestData == 'forestList') {
						if (!empty($_POST[$this->Twig->fphp_Table . '_' . $s_column])) {
							$this->Twig->{$s_column} = strval($_POST[$this->Twig->fphp_Table . '_' . $s_column]);
						}
					} else {
						if (is_array($_POST[$this->Twig->fphp_Table . '_' . $s_column])) {
							/* post value is array, so we need to valiate multiple selected items */
							$s_sum = '';
							
							foreach ($_POST[$this->Twig->fphp_Table . '_' . $s_column] as $s_selectOptValue) {
								$s_sum .= strval($s_selectOptValue) . ';';
							}
							
							$s_sum = substr($s_sum, 0, -1);
							$this->Twig->{$s_column} = $s_sum;
						} else {
							if ($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->FormElementName == forestFormElement::PASSWORD) {
								if (!issetStr(strval($_POST[$this->Twig->fphp_Table . '_' . $s_column]))) {
									/* if we have not post any password, do not clear password field, but try to restore the old password value */
									$this->Twig->RestoreFieldValue($s_column);
								} else {
									/* hash password, so we do not store password in plain text */
									$this->Twig->{$s_column} = password_hash(strval($_POST[$this->Twig->fphp_Table . '_' . $s_column]), PASSWORD_DEFAULT);
								}
							} else {
								/* normal string */
								$this->Twig->{$s_column} = strval($_POST[$this->Twig->fphp_Table . '_' . $s_column]);
							}
						}
					}
					
					/* identify posted radio and select data by json encoded settings and it's multi-language values */
					if (issetStr($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->JSONEncodedSettings)) {
						if ($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->FormElementName == forestFormElement::RADIO) {
							$o_tempFormElement = new forestFormElement($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->FormElementName);
							$o_tempFormElement->loadJSON($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->JSONEncodedSettings);
							
							if (!in_array($this->Twig->{$s_column}, $o_tempFormElement->Options)) {
								$this->Twig->{$s_column} = 0;
							}
						} else if ($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->FormElementName == forestFormElement::SELECT) {
							$o_tempFormElement = new forestFormElement($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->FormElementName);
							$o_tempFormElement->loadJSON($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->JSONEncodedSettings);
							
							$b_value_found = false;
							
							$a_valueOptions = array();
		
							if (strpos($this->Twig->{$s_column}, ';') !== false) {
								$a_valueOptions = explode(';', $this->Twig->{$s_column});
							}
							
							if (count($o_tempFormElement->Options) > 0) {
								foreach ($o_tempFormElement->Options as $s_option_label => $s_option_value) {
									if (is_array($s_option_value)) {
										if (count($s_option_value) > 0) {
											foreach ($s_option_value as $s_optgroup_option_label => $s_optgroup_option_value) {
												if ((issetStr($this->Twig->{$s_column})) && (($this->Twig->{$s_column} == $s_optgroup_option_value) || (in_array($s_optgroup_option_value, $a_valueOptions)))) {
													$b_value_found = true;
												}
											}
										}
									} else {
										if ((issetStr($this->Twig->{$s_column})) && (($this->Twig->{$s_column} == $s_option_value) || (in_array($s_option_value, $a_valueOptions)))) {
											$b_value_found = true;
										}
									}
								}
							}
							
							if (!$b_value_found) {
								$this->Twig->{$s_column} = 'NULL';
							}
						}
					}
					
					$a_validationRules = array();
					$this->GetValidationRules($o_glob->TablefieldsDictionary->{$s_tableFieldIdentifier}->UUID, $a_validationRules);
					
					/* check posted data with configurued validation rules */
					if (count($a_validationRules) > 0) {
						foreach ($a_validationRules as $o_validationRule) {
							if ( ($o_validationRule['Name'] == 'required') || ($o_validationRule['ValidationRuleRequired'] == 1) ) {
								/* d2c('!issetStr -> ' . (!issetStr($this->Twig->{$s_column}))); */
								/* d2c('=== 0 -> ' . ($this->Twig->{$s_column} === 0)); */
								/* d2c('=== floatval(0) -> ' . ($this->Twig->{$s_column} === floatval(0))); */
								/* d2c('=== false -> ' . ($this->Twig->{$s_column} === false)); */
								/* d2c('array count <= 0 -> ' . ((is_array($this->Twig->{$s_column})) && (count($this->Twig->{$s_column}) == 0))); */
								if ( (!issetStr($this->Twig->{$s_column})) ^ ($this->Twig->{$s_column} === 0) ^ ($this->Twig->{$s_column} === floatval(0)) ^ ($this->Twig->{$s_column} === false) ^ ((is_array($this->Twig->{$s_column})) && (count($this->Twig->{$s_column}) == 0)) ) {
									throw new forestException(0x10001408, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'minlength') {
								if ( (issetStr($this->Twig->{$s_column})) && (strlen($this->Twig->{$s_column}) < intval($o_validationRule['ValidationRuleParam01'])) ) {
									throw new forestException(0x10001409, array($s_column, intval($o_validationRule['ValidationRuleParam01'])));
								}
							} else if ($o_validationRule['Name'] == 'maxlength') {
								if ( (issetStr($this->Twig->{$s_column})) && (strlen($this->Twig->{$s_column}) > intval($o_validationRule['ValidationRuleParam01'])) ) {
									throw new forestException(0x1000140A, array($s_column, intval($o_validationRule['ValidationRuleParam01'])));
								}
							} else if ($o_validationRule['Name'] == 'min') {
								if ($this->Twig->{$s_column} < intval($o_validationRule['ValidationRuleParam01'])) {
									throw new forestException(0x1000140B, array($s_column, intval($o_validationRule['ValidationRuleParam01'])));
								}
							} else if ($o_validationRule['Name'] == 'max') {
								if ($this->Twig->{$s_column} > intval($o_validationRule['ValidationRuleParam01'])) {
									throw new forestException(0x1000140C, array($s_column, intval($o_validationRule['ValidationRuleParam01'])));
								}
							} else if ($o_validationRule['Name'] == 'email') {
								if ( (issetStr($this->Twig->{$s_column})) && (!preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/", $this->Twig->{$s_column})) ) {
									throw new forestException(0x1000140D, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'url') {
								if ( (issetStr($this->Twig->{$s_column})) && (!preg_match("_^(https?|ftp)://(\S+(:\S*)?@)?(([1-9]|[1-9]\d|1\d\d|2[0-1]\d|22[0-3])(\.([0-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])){2}(\.([1-9]|[1-9]\d|1\d\d|2[0-4]\d|25[0-4]))|(([a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(\.([a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(\.([a-z\x{00a1}-\x{ffff}]{2,})))(:\d{2,5})?(/[^\s]*)?\$_iuS", $this->Twig->{$s_column})) ) {
									throw new forestException(0x1000140E, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'digits') {
								if ( (issetStr($this->Twig->{$s_column})) && (!preg_match("/^\d+$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x1000140F, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'equalTo') {
								$s_field = str_replace('#' . $this->Twig->fphp_Table . '_', '', strval($o_validationRule['ValidationRuleParam01']));
								
								if ( (issetStr($this->Twig->{$s_column})) && ($this->Twig->{$s_column} != $this->Twig->{$s_field}) ) {
									throw new forestException(0x10001410, array($s_column, $s_field));
								}
							} else if ($o_validationRule['Name'] == 'fphp_dateISO') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^(\d){4}-((0[1-9])|(1[0-2]))-((0[1-9])|(1[0-9])|(2[0-9])|(3[0-1]))$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x10001411, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'dateISO') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x10001411, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_dateDMYpoint') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^((0[1-9])|(1[0-9])|(2[0-9])|(3[0-1]))\.((0[1-9])|(1[0-2]))\.(\d){4}$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x10001412, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_dateDMYslash') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^((0[1-9])|(1[0-9])|(2[0-9])|(3[0-1]))\/((0[1-9])|(1[0-2]))\/(\d){4}$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x10001413, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_dateMDYslash') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^((0[1-9])|(1[0-2]))\/((0[1-9])|(1[0-9])|(2[0-9])|(3[0-1]))\/(\d){4}$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x10001414, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_time') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])(:([0-5][0-9])){0,1}$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x10001415, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_datetime') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^((0[1-9])|(1[0-9])|(2[0-9])|(3[0-1]))\.((0[1-9])|(1[0-2]))\.(\d){4}\s(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x10001416, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_datetimeISO') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^(\d){4}-((0[1-9])|(1[0-2]))-((0[1-9])|(1[0-9])|(2[0-9])|(3[0-1]))(\s|T)(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])(:([0-5][0-9])){0,1}$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x10001417, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_dateinterval') {
								if ( (issetStr($this->Twig->{$s_column})) && ((!preg_match("/^(P(((\d)+Y(\d)+M((\d)+(W|D))?)|((\d)+(Y|M)(\d)+(W|D))|((\d)+(Y|M|W|D)))T(((\d)+H(\d)+M(\d)+S)|((\d)+H(\d)+(M|S))|((\d)+M(\d)+S)|((\d)+(H|M|S))))$/", $this->Twig->{$s_column}))
								&& (!preg_match("/^(PT(((\d)+H(\d)+M(\d)+S)|((\d)+H(\d)+(M|S))|((\d)+M(\d)+S)|((\d)+(H|M|S))))$/", $this->Twig->{$s_column}))
								&& (!preg_match("/^(P(((\d)+Y(\d)+M((\d)+(W|D))?)|((\d)+(Y|M)(\d)+(W|D))|((\d)+(Y|M|W|D))))$/", $this->Twig->{$s_column}))) )
								{
									throw new forestException(0x10001418, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_password') {
								if ( (issetStr(strval($_POST[$this->Twig->fphp_Table . '_' . $s_column]))) && (!((preg_match("/^[A-Za-z0-9\d=!\-@._*?#§$%&'~:;,]*$/", strval($_POST[$this->Twig->fphp_Table . '_' . $s_column])))
								&& (preg_match("/[=!\-@._*?#§$%&'~:;,]/", strval($_POST[$this->Twig->fphp_Table . '_' . $s_column])))
								&& (preg_match("/[a-z]/", strval($_POST[$this->Twig->fphp_Table . '_' . $s_column])))
								&& (preg_match("/[A-Z]/", strval($_POST[$this->Twig->fphp_Table . '_' . $s_column])))
								&& (preg_match("/\d/", strval($_POST[$this->Twig->fphp_Table . '_' . $s_column]))))) )
								{
									throw new forestException(0x10001419, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_username') {
								if ( (issetStr($this->Twig->{$s_column})) && (!preg_match("/^[a-zA-Z0-9_\-]*$/", $this->Twig->{$s_column})) ) {
									throw new forestException(0x1000141A, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_onlyletters') {
								if ( (issetStr($this->Twig->{$s_column})) && (!preg_match("/^[a-zA-Z]*$/", $this->Twig->{$s_column})) ) {
									throw new forestException(0x1000143F, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'number') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^(?:-?\d+|-?\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x1000141B, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'range') {
								if ( ($this->Twig->{$s_column} < intval($o_validationRule['ValidationRuleParam01'])) || ($this->Twig->{$s_column} > intval($o_validationRule['ValidationRuleParam02'])) ) {
									throw new forestException(0x1000141C, array($s_column, intval($o_validationRule['ValidationRuleParam01']), intval($o_validationRule['ValidationRuleParam02'])));
								}
							} else if ($o_validationRule['Name'] == 'rangelength') {
								if ( (issetStr($this->Twig->{$s_column})) && ( (strlen($this->Twig->{$s_column}) < intval($o_validationRule['ValidationRuleParam01'])) || (strlen($this->Twig->{$s_column}) > intval($o_validationRule['ValidationRuleParam02'])) ) ) {
									throw new forestException(0x1000141D, array($s_column, intval($o_validationRule['ValidationRuleParam01']), intval($o_validationRule['ValidationRuleParam02'])));
								}
							} else if ($o_validationRule['Name'] == 'fphp_month') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^(\d){4}-((0[1-9])|(1[0-2]))$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x1000141E, array($s_column));
								}
							} else if ($o_validationRule['Name'] == 'fphp_week') {
								if ( (issetStr($_POST[$this->Twig->fphp_Table . '_' . $s_column])) && (!preg_match("/^(\d){4}-W((0[1-9])|([1-4][0-9])|(5[0-3]))$/", $_POST[$this->Twig->fphp_Table . '_' . $s_column])) ) {
									throw new forestException(0x1000141F, array($s_column));
								}
							}
						}
					}
				} else {
					/* columns which are not in post data, e.g. bool unchecked checkboxes */
					if ($s_forestData == 'forestBool') {
						$this->Twig->{$s_column} = false;
					} else if ($s_forestData == 'forestInt') {
						$this->Twig->{$s_column} = 0;
					}
				}
			}
		}
	}
	
	/* get validation rules of form element */
	protected function GetValidationRules($p_s_tableFieldUUID, &$p_a_validationRules) {
		$o_glob = forestGlobals::init();
		
		$o_querySelect = new forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, forestSQLQuery::SELECT, 'sys_fphp_tablefield_validationrule');
		
		$column_A = new forestSQLColumn($o_querySelect);
			$column_A->Column = '*';
		
		$o_querySelect->Query->Columns->Add($column_A);
		
		$join_A = new forestSQLJoin($o_querySelect);
			$join_A->JoinType = 'INNER JOIN';
			$join_A->Table = 'sys_fphp_validationrule';

		$relation_A = new forestSQLRelation($o_querySelect);
		
		$column_B = new forestSQLColumn($o_querySelect);
			$column_B->Column = 'validationruleUUID';
			
		$column_C = new forestSQLColumn($o_querySelect);
			$column_C->Column = 'UUID';
			$column_C->Table = $join_A->Table;
		
		$relation_A->ColumnLeft = $column_B;
		$relation_A->ColumnRight = $column_C;
		$relation_A->Operator = '=';
		
		$join_A->Relations->Add($relation_A);
			
		$o_querySelect->Query->Joins->Add($join_A);
		
		$column_D = new forestSQLColumn($o_querySelect);
			$column_D->Column = 'tablefieldUUID';
		
		$where_A = new forestSQLWhere($o_querySelect);
			$where_A->Column = $column_D;
			$where_A->Value = $where_A->ParseValue($p_s_tableFieldUUID);
			$where_A->Operator = '=';
		
		$o_querySelect->Query->Where->Add($where_A);
		
		$o_result = $o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_querySelect, false, false);
		
		foreach ($o_result as $o_row) {
			$p_a_validationRules[] = $o_row;
		}
	}
	
	
	/* handle delete record action */
	protected function DeleteRecord() {
		$o_glob = forestGlobals::init();
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteKey'), 'deleteKey' );
		
		if (!$o_glob->IsPost) {
			/* delete record form */
			if ( ($o_glob->Temp->Exists('deleteKey')) && ($o_glob->Temp->{'deleteKey'} != null) ) {
				/* create modal confirmation form for deleting record */
				$o_glob->PostModalForm = new forestForm($this->Twig);
				$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
				
				if (count(explode('~', $o_glob->Temp->{'deleteKey'})) == 1) {
					$s_description = '<b>' . $o_glob->GetTranslation('DeleteModalDescriptionOne', 1) . '</b>';
				} else {
					$s_description = forestStringLib::sprintf2('<b>' . $o_glob->GetTranslation('DeleteModalDescriptionMultiple', 1) . '</b>', array(count(explode('~', $o_glob->Temp->{'deleteKey'}))));
				}
				
				$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);
				
				$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_deleteKey';
				$o_hidden->Value = $o_glob->Temp->{'deleteKey'};
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
			}
		} else {
			/* delete record */
			if (array_key_exists('sys_fphp_deleteKey', $_POST)) {
				$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
				
				$a_deleteKeys = explode('~', $_POST['sys_fphp_deleteKey']);
				
				foreach ($a_deleteKeys as $s_deleteKey) {
					if (! ($this->Twig->GetRecord(array($s_deleteKey))) ) {
						throw new forestException(0x10001401, array($this->Twig->fphp_Table));
					}
					
					/* check record relations before deletion */
					$this->CheckandCleanupRecordBeforeDeletion($this->Twig);
					
					if (method_exists($this, 'beforeDeleteAction')) {
						$this->beforeDeleteAction();
					}
					
					/* delete record */
					$this->executeDeleteRecord($this->Twig);
					
					if (method_exists($this, 'afterDeleteAction')) {
						$this->afterDeleteAction();
					}
				}
				
				if (count($a_deleteKeys) == 1) {
					$o_glob->SystemMessages->Add(new forestException(0x10001427));
				} else {
					$o_glob->SystemMessages->Add(new forestException(0x10001428));
				}
			}
		}
		
		if (isset($this->KeepFilter)) {
			if ($this->KeepFilter->value) {
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
			}
		}
		
		$this->SetNextAction('init');
	}
	
	/* check twigfield relation to other elements */
	protected function CheckandCleanupRecordBeforeDeletion(forestTwig $p_o_twig) {
		$o_glob = forestGlobals::init();
	}
	
	/* re-usable delete record function */
	protected function executeDeleteRecord(forestTwig $p_o_record) {
		$o_glob = forestGlobals::init();
		
		/* delete record */
		$i_return = $p_o_record->DeleteRecord();
		
		/* evaluate the result */
		if ($i_return <= 0) {
			throw new forestException(0x10001423);
		}
	}
}
?>