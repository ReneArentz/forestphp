<?php

namespace fPHP\Branches;
use \fPHP\Roots\forestString as forestString;
use \fPHP\Roots\forestList as forestList;
use \fPHP\Roots\forestNumericString as forestNumericString;
use \fPHP\Roots\forestInt as forestInt;
use \fPHP\Roots\forestFloat as forestFloat;
use \fPHP\Roots\forestBool as forestBool;
use \fPHP\Roots\forestArray as forestArray;
use \fPHP\Roots\forestObject as forestObject;
use \fPHP\Roots\forestLookup as forestLookup;

class roleBranch extends forestBranch {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	protected function initAction() {
		$this->Init();
	}
	
		protected function beforeViewAction() {
			/* $this->Twig holds current record */
		}
	
	protected function viewAction() {
		$this->ViewRecord();
	}
	
		protected function afterViewAction() {
			/* $this->Twig holds current record */
		}
	
	protected function viewFlexAction() {
		$this->GenerateFlexView();
	}
	
	protected function editFlexAction() {
		$this->EditFlexView();
	}
	
		protected function beforeNewAction() {
			/* $this->Twig holds current record */
		}
		
			protected function beforeNewSubAction() {
				/* $this->Twig holds current sub record */
			}
	
	protected function newAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'newKey'), 'newKey' );
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'subConstraintKey'), 'subConstraintKey' );
		
		if (!$o_glob->IsPost) {
			$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
			
			if ( ($o_glob->Temp->Exists('newKey')) && ($o_glob->Temp->{'newKey'} != null) && ($o_glob->Temp->Exists('subConstraintKey')) && ($o_glob->Temp->{'subConstraintKey'} != null) ) {
				/* check if posted uuid matches with head record */
				if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'newKey'}))) ) {
					throw new \fPHP\Roots\forestException(0x10001402);
				}
				
				/* add new sub record */
				$this->RenderNewSubRecordForm();
				$o_glob->StandardView->PrimaryValue = $o_glob->StandardViews[\fPHP\Branches\forestBranch::DETAIL]; /* because it only makes sense if we stay in detail view, when we open modal read only form for record */
			} else {
				/* add new record */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
				$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
			}
		} else {
			if ( (array_key_exists('sys_fphp_subConstraintKey', $_POST)) || (array_key_exists('sys_fphp_newKey', $_POST)) ) {
				if ( (array_key_exists('sys_fphp_step', $_POST)) && ($_POST['sys_fphp_step'] == 'one') ) {
					$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form', true);
					
					/* add new sub record */
					$this->RenderNewSubRecordForm();
					$o_glob->StandardView->PrimaryValue = $o_glob->StandardViews[\fPHP\Branches\forestBranch::DETAIL]; /* because it only makes sense if we stay in detail view, when we open modal read only form for record */
				} else if ( (array_key_exists('sys_fphp_step', $_POST)) && ($_POST['sys_fphp_step'] == 'submit') ) {
					$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
					
					/* check posted data for new sub record */
					
					/* check if posted uuid matches with head record */
					if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_newKey']))) ) {
						throw new \fPHP\Roots\forestException(0x10001402);
					}
					
					$i_result = null;
					$o_subconstraintTwig = null;
					
					if ( ($o_glob->Temp->Exists('subConstraintKey')) && ($o_glob->Temp->{'subConstraintKey'} != null) ) {
						if ($_POST['sys_fphp_subConstraintKey'] == 'permission') {
							$o_subconstraintTwig = new \fPHP\Twigs\role_permissionTwig;
						} else {
							$o_subconstraintTwig = new \fPHP\Twigs\role_permissionTwig;
						}
					}
					
					/* set role uuid for new record(s) */
					$o_subconstraintTwig->roleUUID = $this->Twig->UUID;
					
					$a_tempTable = explode('_', $o_subconstraintTwig->fphp_Table);
					$s_tempTable = $a_tempTable[(count($a_tempTable) - 1)];
					$s_foo = '\\fPHP\\Twigs\\' . $s_tempTable . 'Twig';
					$o_joinTwig = new $s_foo;
					
					if (is_array($_POST[$o_subconstraintTwig->fphp_Table . '_Lookup'])) {
						/* post value is array, so we need to valiate multiple selected items */
						foreach ($_POST[$o_subconstraintTwig->fphp_Table . '_Lookup'] as $s_permissionUUID) {
							/* check if selected uuid matches with join record */
							if (! ($o_joinTwig->GetRecord(array($s_permissionUUID))) ) {
								throw new \fPHP\Roots\forestException(0x10001402);
							}
							
							$o_subconstraintTwig->permissionUUID = $s_permissionUUID;
							
							/* insert record */
							$i_result = $o_subconstraintTwig->InsertRecord(true);
							
							/* evaluate result */
							if ($i_result == -1) {
								throw new \fPHP\Roots\forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
							} else if ($i_result == 0) {
								throw new \fPHP\Roots\forestException(0x10001402);
							}
						}
					}
					
					if ($i_result == 1) {
						$o_glob->SystemMessages->Add(new \fPHP\Roots\forestException(0x10001404));
					}
					
					$o_glob->StandardView->PrimaryValue = $o_glob->StandardViews[\fPHP\Branches\forestBranch::DETAIL]; /* because it only makes sense if we stay in detail view, when we open modal read only form for record */
				}
			} else {
				$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
				
				/* check posted data for new record */
				
				$this->TransferPOST_Twig();
				
				/* set values for info columns when configured */
				$i_infoColumns = $o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['InfoColumns'];
				
				if ($i_infoColumns == 10) {
					$this->Twig->Created = new \fPHP\Helper\forestDateTime;
					$this->Twig->CreatedBy = $o_glob->Security->UserUUID;
				} else if ($i_infoColumns == 100) {
					$this->Twig->Modified = new \fPHP\Helper\forestDateTime;
					$this->Twig->ModifiedBy = $o_glob->Security->UserUUID;
				} else if ($i_infoColumns == 1000) {
					$this->Twig->Created = new \fPHP\Helper\forestDateTime;
					$this->Twig->CreatedBy = $o_glob->Security->UserUUID;
					$this->Twig->Modified = new \fPHP\Helper\forestDateTime;
					$this->Twig->ModifiedBy = $o_glob->Security->UserUUID;
				}
				
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
					$this->UndoFilesEntries();
					throw new \fPHP\Roots\forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
				} else if ($i_result == 0) {
					$this->UndoFilesEntries();
					throw new \fPHP\Roots\forestException(0x10001402);
				} else if ($i_result == 1) {
					$o_glob->SystemMessages->Add(new \fPHP\Roots\forestException(0x10001404));
				}
				
				/* handle uploads */
				$this->TransferFILES_Twig();
			}
		}
		
		$this->SetNextAction('init');
	}
	
			protected function afterNewSubAction() {
				/* $this->Twig holds current sub record */
			}
	
		protected function afterNewAction() {
			/* $this->Twig holds current record */
		}
		
		protected function beforeEditAction() {
			/* $this->Twig holds current record */
		}
			
			protected function beforeEditSubAction() {
				/* $this->Twig holds current sub record */
			}
	
	protected function editAction() {
		$this->EditRecord();
	}
	
			protected function afterEditSubAction() {
				/* $this->Twig holds current sub record */
			}
	
		protected function afterEditAction() {
			/* $this->Twig holds current record */
		}
		
		protected function beforeDeleteAction() {
			/* $this->Twig holds current record */
		}
		
			protected function beforeDeleteSubAction() {
				/* $this->Twig holds current sub record */
			}
			
				protected function beforeDeleteFileAction() {
					/* $this->Twig holds current file record */
				}
		
	protected function deleteAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteKey'), 'deleteKey' );
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteSubKey'), 'deleteSubKey' );
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteFileKey'), 'deleteFileKey' );
		
		if (!$o_glob->IsPost) {
			/* delete record form */
			if ( ($o_glob->Temp->Exists('deleteKey')) && ($o_glob->Temp->{'deleteKey'} != null) ) {
				/* create modal confirmation form for deleting record */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig);
				$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
				
				if (count(explode('~', $o_glob->Temp->{'deleteKey'})) == 1) {
					$s_description = '<b>' . $o_glob->GetTranslation('DeleteModalDescriptionOne', 1) . '</b>';
				} else {
					$s_description = \fPHP\Helper\forestStringLib::sprintf2('<b>' . $o_glob->GetTranslation('DeleteModalDescriptionMultiple', 1) . '</b>', array(count(explode('~', $o_glob->Temp->{'deleteKey'}))));
				}
				
				$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);
				
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_deleteKey';
				$o_hidden->Value = $o_glob->Temp->{'deleteKey'};
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
			}
			
			/* delete sub record form */
			else if ( ($o_glob->Temp->Exists('deleteSubKey')) && ($o_glob->Temp->{'deleteSubKey'} != null) ) {
				/* create modal confirmation form for deleting record */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig);
				$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
				$s_description = '<b>' . $o_glob->GetTranslation('DeleteModalDescriptionLine', 1) . '</b>';
				$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);
				
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_deleteSubKey';
				$o_hidden->Value = $o_glob->Temp->{'deleteSubKey'};
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
				
				$o_glob->StandardView->PrimaryValue = $o_glob->StandardViews[\fPHP\Branches\forestBranch::DETAIL]; /* because it only makes sense if we stay in detail view, when we open modal read only form for record */
			}
		} else {
			/* delete record */
			if (array_key_exists('sys_fphp_deleteKey', $_POST)) {
				$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
				
				$a_deleteKeys = explode('~', $_POST['sys_fphp_deleteKey']);
				
				foreach ($a_deleteKeys as $s_deleteKey) {
					if (! ($this->Twig->GetRecord(array($s_deleteKey))) ) {
						throw new \fPHP\Roots\forestException(0x10001401, array($this->Twig->fphp_Table));
					} else {
						if (method_exists($this, 'beforeDeleteAction')) {
							$this->beforeDeleteAction();
						}
						
						/* check record relations before deletion */
						$this->CheckandCleanupRecordBeforeDeletion($this->Twig);
						
						/* look for permissions */
						$o_role_permissionTwig = new \fPHP\Twigs\role_permissionTwig;
						
						$a_sqlAdditionalFilter = array(array('column' => 'roleUUID', 'value' => $this->Twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
						$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
						$o_role_permissions = $o_role_permissionTwig->GetAllRecords(true);
						$o_glob->Temp->Del('SQLAdditionalFilter');
						
						foreach ($o_role_permissions->Twigs as $o_role_permission) {
							/* delete record */
							$i_return = $o_role_permission->DeleteRecord();
							
							/* evaluate the result */
							if ($i_return <= 0) {
								throw new \fPHP\Roots\forestException(0x10001423);
							}
						}
						
						/* look for usergroups */
						$o_usergroup_roleTwig = new \fPHP\Twigs\usergroup_roleTwig;
						
						$a_sqlAdditionalFilter = array(array('column' => 'roleUUID', 'value' => $this->Twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
						$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
						$o_usergroup_roles = $o_usergroup_roleTwig->GetAllRecords(true);
						$o_glob->Temp->Del('SQLAdditionalFilter');
						
						foreach ($o_usergroup_roles->Twigs as $o_usergroup_role) {
							/* delete record */
							$i_return = $o_usergroup_role->DeleteRecord();
							
							/* evaluate the result */
							if ($i_return <= 0) {
								throw new \fPHP\Roots\forestException(0x10001423);
							}
						}
						
						/* delete record */
						$i_return = $this->Twig->DeleteRecord();
						
						if (method_exists($this, 'afterDeleteAction')) {
							$this->afterDeleteAction();
						}
						
						/* evaluate the result */
						if ($i_return <= 0) {
							throw new \fPHP\Roots\forestException(0x10001423);
						}
					}
				}
				
				if (count($a_deleteKeys) == 1) {
					$o_glob->SystemMessages->Add(new \fPHP\Roots\forestException(0x10001427));
				} else {
					$o_glob->SystemMessages->Add(new \fPHP\Roots\forestException(0x10001428));
				}
			}
			
			/* delete sub record */
			else if (array_key_exists('sys_fphp_deleteSubKey', $_POST)) {
				$a_deleteSubKey = explode('~', $_POST['sys_fphp_deleteSubKey']);
				
				if (count($a_deleteSubKey) != 3) {
					throw new \fPHP\Roots\forestException(0x10001423);
				}
				
				$o_subconstraintTwig = null;
				
				if ($a_deleteSubKey[0] == 'permission') {
					$o_subconstraintTwig = new \fPHP\Twigs\role_permissionTwig;
				} else {
					$o_subconstraintTwig = new \fPHP\Twigs\role_permissionTwig;
				}
				
				/* query sub record with key */
				if (! ($o_subconstraintTwig->GetRecord(array($a_deleteSubKey[1], $a_deleteSubKey[2]))) ) {
					throw new \fPHP\Roots\forestException(0x10001401, array($o_subconstraintTwig->fphp_Table));
				} else {
					if (method_exists($this, 'beforeDeleteSubAction')) {
						$this->beforeDeleteSubAction();
					}
					
					/* delete sub record */
					$i_return = $o_subconstraintTwig->DeleteRecord();
					
					if (method_exists($this, 'afterDeleteSubAction')) {
						$this->afterDeleteSubAction();
					}
					
					/* evaluate the result */
					if ($i_return <= 0) {
						throw new \fPHP\Roots\forestException(0x10001423);
					}
				}
				
				$o_glob->SystemMessages->Add(new \fPHP\Roots\forestException(0x10001427));
				
				$o_glob->StandardView->PrimaryValue = $o_glob->StandardViews[\fPHP\Branches\forestBranch::DETAIL]; /* because it only makes sense if we stay in detail view, when we open modal read only form for record */
			}
		}
		
		$this->SetNextAction('init');
	}
				
				protected function afterDeleteFileAction() {
					/* $this->Twig holds current file record */
				}
			
			protected function afterDeleteSubAction() {
				/* $this->Twig holds current sub record */
			}
	
		protected function afterDeleteAction() {
			/* $this->Twig holds current record */
		}
		
	protected function moveUpAction() {
		$this->MoveUpRecord();
	}
	
	protected function moveDownAction() {
		$this->MoveDownRecord();
	}
	
		protected function beforeReplaceFileAction() {
			/* $this->Twig holds current file record */
		}
		
		protected function afterReplaceFileAction() {
			/* $this->Twig holds current file record */
		}
		
		protected function beforeRestoreFileAction() {
			/* $this->Twig holds current file record */
		}
		
		protected function afterRestoreFileAction() {
			/* $this->Twig holds current file record */
		}
	
	/* overwrite - handle sub records display in detail view */
	protected function additionalListSubRecordsAction(\fPHP\Twigs\forestTwig $p_o_twig, $p_b_readonly, &$p_s_subFormItems, &$p_b_firstSubElement) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* get table info, needed for general accoridon id -> based on table name */
		$s_tableUUID = $o_glob->Tables[$p_o_twig->fphp_Table];
		$s_tableName = array_search($s_tableUUID, $o_glob->Tables);

		/* look for permissions */
		$o_role_permissionTwig = new \fPHP\Twigs\role_permissionTwig;
		
		$a_tempTable = explode('_', $o_role_permissionTwig->fphp_Table);
		$s_tempTable = $a_tempTable[(count($a_tempTable) - 1)];
		$s_foo = '\\fPHP\\Twigs\\' . $s_tempTable . 'Twig';
		$o_lookupTwig = new $s_foo;
		$o_lookupData = new \fPHP\Helper\forestLookupData($o_lookupTwig->fphp_Table, $o_lookupTwig->fphp_Primary, $o_lookupTwig->fphp_View);
		
		$a_sqlAdditionalFilter = array(array('column' => 'roleUUID', 'value' => $p_o_twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_role_permissions = $o_role_permissionTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		$s_subTableHead = '';
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortPermission') . '</th>' . "\n";
		
		if (!$p_b_readonly) {
			$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
		}
		
		$s_subTableRows = '';
		
		foreach ($o_role_permissions->Twigs as $o_role_permission) {
			$s_subTableRows .=  '<tr>' . "\n";
			$o_lookupData->PrimaryValue = $o_role_permission->permissionUUID;
			$s_subTableRows .=  '<td>' . $o_lookupData . '</td>' . "\n";
			
			if (!$p_b_readonly) {
				$a_parameters = $o_glob->URL->Parameters;
				unset($a_parameters['newKey']);
				unset($a_parameters['viewKey']);
				unset($a_parameters['editKey']);
				unset($a_parameters['editSubKey']);
				unset($a_parameters['deleteKey']);
				unset($a_parameters['deleteSubKey']);
				$a_parameters['deleteSubKey'] = 'permission~' . $o_role_permission->roleUUID . '~' . $o_role_permission->permissionUUID;
				
				$s_subTableRows .=  '<td><a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'delete', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="bi bi-trash-fill text-danger"></span></a></td>' . "\n";
			}
			
			$s_subTableRows .=  '</tr>' . "\n";
		}
		
		if (!$p_b_readonly) {
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['newKey'] = $p_o_twig->UUID;
			$a_parameters['subConstraintKey'] = 'permission';
			$s_newButton = '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'new', $a_parameters) . '" class="btn btn-light" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="bi bi-plus-circle-fill text-success"></span></a>' . "\n";
		} else {
			$s_newButton = '';
		}
		
		$s_firstElement = '';
		
		if ($p_b_firstSubElement == false) {
			$s_firstElement = ' show';
			$p_b_firstSubElement = true;
		}
		
		$s_subFormItemContent = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
		$p_s_subFormItems .= new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEM, array('subpermission', $o_glob->GetTranslation('Permission') . ' (' . $o_role_permissions->Twigs->Count() . ')', $s_firstElement, $s_subFormItemContent, $s_tableName));
	}

	/* overwrite - render modal form for new sub record */
	protected function RenderNewSubRecordForm() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$o_subconstraintTwig = null;
		
		if ( ($o_glob->Temp->Exists('subConstraintKey')) && ($o_glob->Temp->{'subConstraintKey'} != null) ) {
			if ($o_glob->Temp->{'subConstraintKey'} == 'permission') {
				$o_subconstraintTwig = new \fPHP\Twigs\role_permissionTwig;
			}
		}
		
		$o_glob->PostModalForm = new \fPHP\Forms\forestForm($o_subconstraintTwig);
		
		/* get table */
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (!($o_tableTwig->GetRecordPrimary(array($o_subconstraintTwig->fphp_Table), array('Name')))) {
			throw new \fPHP\Roots\forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$o_formelementTwig = new \fPHP\Twigs\formelementTwig;
		
		/* look in tablefields for formobject, if not get the standard by formelementuuid */
		if (!($o_formelementTwig->GetRecordPrimary(array('form'), array('Name')))) {
			throw new \fPHP\Roots\forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		}
			
		$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
		$s_formObjectJSONsettings = '';
		
		if (!($o_tablefieldTwig->GetRecordPrimary(array($o_tableTwig->UUID, $o_formelementTwig->UUID), array('TableUUID', 'FormElementUUID')))) {
			/* no tablefield for table, take standard */
			$s_formObjectJSONsettings = $o_formelementTwig->JSONEncodedSettings;
		} else {
			$s_formObjectJSONsettings = $o_tablefieldTwig->JSONEncodedSettings;
		}
		
		/* create formobject(check $this->Readonly->value), modal object and maybe tab objects */
		$o_glob->PostModalForm->FormObject->loadJSON($s_formObjectJSONsettings);
		$o_glob->PostModalForm->FormModalConfiguration->loadJSON($s_formObjectJSONsettings);
		$o_glob->PostModalForm->FormTabConfiguration->loadJSON($s_formObjectJSONsettings);
		
		$o_glob->PostModalForm->FormModalConfiguration->ModalId = $o_glob->PostModalForm->FormObject->Id . 'Modal';
		$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewSubModalTitle', 1);
		$o_glob->PostModalForm->FormTabConfiguration->Tab = false;
		
		if (!$o_glob->IsPost) {
			/* add step flag to modal form */
			$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
			$o_hidden->Id = 'sys_fphp_step';
			$o_hidden->Value = 'one';
			
			$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
			
			$o_lookupTwig = new \fPHP\Twigs\branchTwig;
			$o_lookupData = new \fPHP\Helper\forestLookupData($o_lookupTwig->fphp_Table, $o_lookupTwig->fphp_Primary, array('Title','Name'));
			
			$a_options = $o_lookupData->CreateOptionsArray();
			$a_options = array('Any' => 0) + $a_options;
			
			$o_lookup = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::LOOKUP);
			$o_lookup->Label = $o_glob->GetTranslation('formBranchLabel');
			$o_lookup->Id = $o_subconstraintTwig->fphp_Table . '_Branch';
			$o_lookup->Options = $a_options;
			$o_lookup->Required = true;
			$o_glob->PostModalForm->FormElements->Add($o_lookup);
			
			$o_glob->PostModalForm->FormObject->ValRequiredMessage = $o_glob->GetTranslation('ValRequiredMessage', 1);
			$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule($o_lookup->Id, 'required', 'true', 'NULL', 'false'));
		} else if ( (array_key_exists('sys_fphp_step', $_POST)) && ($_POST['sys_fphp_step'] == 'one') ) {
			/* add step flag to modal form */
			$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
			$o_hidden->Id = 'sys_fphp_step';
			$o_hidden->Value = 'submit';
			
			$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
			
			/* create lookup for choosing subrecords as input list */
			$a_tempTable = explode('_', $o_subconstraintTwig->fphp_Table);
			$s_tempTable = $a_tempTable[(count($a_tempTable) - 1)];
			$s_foo = '\\fPHP\\Twigs\\' . $s_tempTable . 'Twig';
			$o_lookupTwig = new $s_foo;
			$o_lookupData = new \fPHP\Helper\forestLookupData($o_lookupTwig->fphp_Table, $o_lookupTwig->fphp_Primary, $o_lookupTwig->fphp_View, array('Branch' => $_POST[$o_subconstraintTwig->fphp_Table . '_Branch']));
			
			$a_options = $o_lookupData->CreateOptionsArray();
			
			/* remove existing permission actions of lookup element */
			$o_role_permissionTwig = new \fPHP\Twigs\role_permissionTwig;

			$a_sqlAdditionalFilter = array(array('column' => 'roleUUID', 'value' => $o_glob->Temp->{'newKey'}, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_role_permissions = $o_role_permissionTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			/* adjust options data */
			foreach ($a_options as $s_permissionLabel => $s_permissionId) {
				$b_found = false;
				
				foreach ($o_role_permissions->Twigs as $o_role_permission) {
					if ($o_role_permission->permissionUUID == $s_permissionId) {
						$b_found = true;
						break;
					}
				}
				
				if ($b_found) {
					unset($a_options[$s_permissionLabel]);
				}
			}
			
			if (count($a_options) <= 0) {
				throw new \fPHP\Roots\forestException(0x10001F12);
			}
			
			$o_lookup = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::LOOKUP);
			$o_lookup->Label = $o_glob->GetTranslation($o_glob->BranchTree['Id'][$o_glob->BranchTree['Name'][$s_tempTable]]['Title'], 1) . ' ' . $o_glob->GetTranslation('BranchTitleRecord', 1) . ':';
			$o_lookup->Id = $o_subconstraintTwig->fphp_Table . '_Lookup[]';
			$o_lookup->Options = $a_options;
			$o_lookup->Required = true;
			$o_lookup->Multiple = true;
			$o_lookup->Size = 8;
			$o_glob->PostModalForm->FormElements->Add($o_lookup);
			
			$o_glob->PostModalForm->FormObject->ValRequiredMessage = $o_glob->GetTranslation('ValRequiredMessage', 1);
			$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule($o_lookup->Id, 'required', 'true', 'NULL', 'false'));
		}
		
		$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
		$o_hidden->Id = 'sys_fphp_subConstraintKey';
		$o_hidden->Value = $o_glob->Temp->{'subConstraintKey'};
		$o_glob->PostModalForm->FormElements->Add($o_hidden);
		
		$o_hidden2 = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
		$o_hidden2->Id = 'sys_fphp_newKey';
		$o_hidden2->Value = $o_glob->Temp->{'newKey'};
		$o_glob->PostModalForm->FormElements->Add($o_hidden2);
		
		if (!($o_formelementTwig->GetRecordPrimary(array('submit'), array('Name')))) {
			throw new \fPHP\Roots\forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		} else {
			$o_button = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::BUTTON);
			$o_button->loadJSON($o_formelementTwig->JSONEncodedSettings);
			$o_button->ButtonText = htmlspecialchars_decode($o_button->ButtonText);
			$o_glob->PostModalForm->FormFooterElements->Add($o_button);
		}
		
		if (!($o_formelementTwig->GetRecordPrimary(array('cancel'), array('Name')))) {
			throw new \fPHP\Roots\forestException(0x10001401, array($o_formelementTwig->fphp_Table));
		} else {
			$o_cancel = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::BUTTON);
			$o_cancel->loadJSON($o_formelementTwig->JSONEncodedSettings);
			$o_cancel->ButtonText = htmlspecialchars_decode($o_cancel->ButtonText);
			$o_glob->PostModalForm->FormFooterElements->Add($o_cancel);
		}
		
		$o_glob->PostModalForm->AddFormKey();
		$o_glob->PostModalForm->Automatic = true;
	}
}
?>