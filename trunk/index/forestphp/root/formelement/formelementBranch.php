<?php
class formelementBranch extends forestBranch {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	protected function initBranch() {
		$this->Filter->value = true;
		$this->StandardView = forestBranch::LIST;
		$this->KeepFilter->value = false;
		
		$this->Twig = new formelementTwig();
	}
	
	protected function init() {
		$o_glob = forestGlobals::init();
		
		if ($this->StandardView == forestBranch::DETAIL) {
			$this->GenerateView();
		} else if ($this->StandardView == forestBranch::LIST) {
			$this->GenerateListView();
		}
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
	
		protected function beforeNewAction() {
			/* $this->Twig holds current record */
		}
		
			protected function beforeNewSubAction() {
				/* $this->Twig holds current sub record */
			}
	
	protected function newAction() {
		$o_glob = forestGlobals::init();
		
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'newKey'), 'newKey' );
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'subConstraintKey'), 'subConstraintKey' );
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			if ( ($o_glob->Temp->Exists('newKey')) && ($o_glob->Temp->{'newKey'} != null) && ($o_glob->Temp->Exists('subConstraintKey')) && ($o_glob->Temp->{'subConstraintKey'} != null) ) {
				/* add new sub record */
				$this->RenderNewSubRecordForm();
				$this->StandardView = forestBranch::DETAIL; /* because it only makes sense if we stay in detail view, when we open modal read only form for record */
			} else {
				/* add new record */
				$o_glob->PostModalForm = new forestForm($this->Twig, true);
				$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
			}
		} else {
			if ( (array_key_exists('sys_fphp_subConstraintKey', $_POST)) || (array_key_exists('sys_fphp_newKey', $_POST)) ) {
				/* check posted data for new sub record */
				
				$o_subconstraintTwig = null;
				
				if ( ($o_glob->Temp->Exists('subConstraintKey')) && ($o_glob->Temp->{'subConstraintKey'} != null) ) {
					if ($_POST['sys_fphp_subConstraintKey'] == 'forestdata') {
						$o_subconstraintTwig = new formelement_forestdataTwig;
					} else if ($_POST['sys_fphp_subConstraintKey'] == 'sqltype') {
						$o_subconstraintTwig = new formelement_sqltypeTwig;
					} else if ($_POST['sys_fphp_subConstraintKey'] == 'validationrule') {
						$o_subconstraintTwig = new formelement_validationruleTwig;
					} else {
						$o_subconstraintTwig = new formelement_forestdataTwig;
					}
				}
				
				/* check if posted uuid matches with head record */
				if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_newKey']))) ) {
					throw new forestException(0x10001402);
				}
				
				$a_tempTable = explode('_', $o_subconstraintTwig->fphp_Table);
				$s_tempTable = $a_tempTable[(count($a_tempTable) - 1)];
				$s_foo = $s_tempTable . 'Twig';
				$o_joinTwig = new $s_foo;
				
				/* check if selected uuid matches with join record */
				if (! ($o_joinTwig->GetRecord(array($_POST[$o_subconstraintTwig->fphp_Table . '_Lookup']))) ) {
					throw new forestException(0x10001402);
				}
				
				$o_subconstraintTwig->formelementUUID = $this->Twig->UUID;
				
				if ($_POST['sys_fphp_subConstraintKey'] == 'forestdata') {
					$o_subconstraintTwig->forestdataUUID = $_POST[$o_subconstraintTwig->fphp_Table . '_Lookup'];
				} else if ($_POST['sys_fphp_subConstraintKey'] == 'sqltype') {
					$o_subconstraintTwig->sqltypeUUID = $_POST[$o_subconstraintTwig->fphp_Table . '_Lookup'];
				} else if ($_POST['sys_fphp_subConstraintKey'] == 'validationrule') {
					$o_subconstraintTwig->validationruleUUID = $_POST[$o_subconstraintTwig->fphp_Table . '_Lookup'];
				} else {
					$o_subconstraintTwig->forestdataUUID = $_POST[$o_subconstraintTwig->fphp_Table . '_Lookup'];
				}
				
				/* insert record */
				$i_result = $o_subconstraintTwig->InsertRecord(true);
				
				/* evaluate result */
				if ($i_result == -1) {
					throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
				} else if ($i_result == 0) {
					throw new forestException(0x10001402);
				} else if ($i_result == 1) {
					$o_glob->SystemMessages->Add(new forestException(0x10001404));
				}
				
				$this->StandardView = forestBranch::DETAIL; /* because it only makes sense if we stay in detail view, when we open modal read only form for record */
			} else {
				/* check posted data for new record */
				
				$this->TransferPOST_Twig();
				
				/* set values for info columns when configured */
				$i_infoColumns = $o_glob->TablesInformation[$this->Twig->fphp_TableUUID]['InfoColumns'];
				
				if ($i_infoColumns == 10) {
					$this->Twig->Created = new forestDateTime;
					$this->Twig->CreatedBy = $o_glob->Security->UserUUID;
				} else if ($i_infoColumns == 100) {
					$this->Twig->Modified = new forestDateTime;
					$this->Twig->ModifiedBy = $o_glob->Security->UserUUID;
				} else if ($i_infoColumns == 1000) {
					$this->Twig->Created = new forestDateTime;
					$this->Twig->CreatedBy = $o_glob->Security->UserUUID;
					$this->Twig->Modified = new forestDateTime;
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
					throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
				} else if ($i_result == 0) {
					$this->UndoFilesEntries();
					throw new forestException(0x10001402);
				} else if ($i_result == 1) {
					$o_glob->SystemMessages->Add(new forestException(0x10001404));
				}
				
				/* handle uploads */
				$this->TransferFILES_Twig();
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
		$o_glob = forestGlobals::init();
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteKey'), 'deleteKey' );
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteSubKey'), 'deleteSubKey' );
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteFileKey'), 'deleteFileKey' );
		
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
			
			/* delete sub record form */
			else if ( ($o_glob->Temp->Exists('deleteSubKey')) && ($o_glob->Temp->{'deleteSubKey'} != null) ) {
				/* create modal confirmation form for deleting record */
				$o_glob->PostModalForm = new forestForm($this->Twig);
				$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
				$s_description = '<b>' . $o_glob->GetTranslation('DeleteModalDescriptionLine', 1) . '</b>';
				$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);
				
				$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_deleteSubKey';
				$o_hidden->Value = $o_glob->Temp->{'deleteSubKey'};
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
				
				$this->StandardView = forestBranch::DETAIL; /* because it only makes sense if we stay in detail view, when we open modal read only form for record */
			}
		} else {
			/* delete record */
			if (array_key_exists('sys_fphp_deleteKey', $_POST)) {
				$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
				
				$a_deleteKeys = explode('~', $_POST['sys_fphp_deleteKey']);
				
				foreach ($a_deleteKeys as $s_deleteKey) {
					if (! ($this->Twig->GetRecord(array($s_deleteKey))) ) {
						throw new forestException(0x10001401, array($this->Twig->fphp_Table));
					} else {
						if (method_exists($this, 'beforeDeleteAction')) {
							$this->beforeDeleteAction();
						}
						
						/* check record relations before deletion */
						$this->CheckandCleanupRecordBeforeDeletion($this->Twig);
						
						/* look for forestdata */
						$o_formelement_forestdataTwig = new formelement_forestdataTwig;
						
						$a_sqlAdditionalFilter = array(array('column' => 'formelementUUID', 'value' => $this->Twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
						$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
						$o_formelement_forestdatas = $o_formelement_forestdataTwig->GetAllRecords(true);
						$o_glob->Temp->Del('SQLAdditionalFilter');
						
						foreach ($o_formelement_forestdatas->Twigs as $o_formelement_forestdata) {
							/* delete record */
							$i_return = $o_formelement_forestdata->DeleteRecord();
							
							/* evaluate the result */
							if ($i_return <= 0) {
								throw new forestException(0x10001423);
							}
						}
						
						/* look for sqltype */
						$o_formelement_sqltypeTwig = new formelement_sqltypeTwig;
						
						$a_sqlAdditionalFilter = array(array('column' => 'formelementUUID', 'value' => $this->Twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
						$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
						$o_formelement_sqltypes = $o_formelement_sqltypeTwig->GetAllRecords(true);
						$o_glob->Temp->Del('SQLAdditionalFilter');
						
						foreach ($o_formelement_sqltypes->Twigs as $o_formelement_sqltype) {
							/* delete record */
							$i_return = $o_formelement_sqltype->DeleteRecord();
							
							/* evaluate the result */
							if ($i_return <= 0) {
								throw new forestException(0x10001423);
							}
						}
						
						/* look for validationrule */
						$o_formelement_validationruleTwig = new formelement_validationruleTwig;
						
						$a_sqlAdditionalFilter = array(array('column' => 'formelementUUID', 'value' => $this->Twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
						$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
						$o_formelement_validationrules = $o_formelement_validationruleTwig->GetAllRecords(true);
						$o_glob->Temp->Del('SQLAdditionalFilter');
						
						foreach ($o_formelement_validationrules->Twigs as $o_formelement_validationrule) {
							/* delete record */
							$i_return = $o_formelement_validationrule->DeleteRecord();
							
							/* evaluate the result */
							if ($i_return <= 0) {
								throw new forestException(0x10001423);
							}
						}
						
						/* delete record */
						$i_return = $this->Twig->DeleteRecord();
						
						if (method_exists($this, 'afterDeleteAction')) {
							$this->afterDeleteAction();
						}
						
						/* evaluate the result */
						if ($i_return <= 0) {
							throw new forestException(0x10001423);
						}
					}
				}
				
				if (count($a_deleteKeys) == 1) {
					$o_glob->SystemMessages->Add(new forestException(0x10001427));
				} else {
					$o_glob->SystemMessages->Add(new forestException(0x10001428));
				}
			}
			
			/* delete sub record */
			else if (array_key_exists('sys_fphp_deleteSubKey', $_POST)) {
				$a_deleteSubKey = explode('~', $_POST['sys_fphp_deleteSubKey']);
				
				if (count($a_deleteSubKey) != 3) {
					throw new forestException(0x10001423);
				}
				
				$o_subconstraintTwig = null;
				
				if ($a_deleteSubKey[0] == 'forestdata') {
					$o_subconstraintTwig = new formelement_forestdataTwig;
				} else if ($a_deleteSubKey[0] == 'sqltype') {
					$o_subconstraintTwig = new formelement_sqltypeTwig;
				} else if ($a_deleteSubKey[0] == 'validationrule') {
					$o_subconstraintTwig = new formelement_validationruleTwig;
				} else {
					$o_subconstraintTwig = new formelement_forestdataTwig;
				}
				
				/* query sub record with key */
				if (! ($o_subconstraintTwig->GetRecord(array($a_deleteSubKey[1], $a_deleteSubKey[2]))) ) {
					throw new forestException(0x10001401, array($o_subconstraintTwig->fphp_Table));
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
						throw new forestException(0x10001423);
					}
				}
				
				$o_glob->SystemMessages->Add(new forestException(0x10001427));
				
				$this->StandardView = forestBranch::DETAIL; /* because it only makes sense if we stay in detail view, when we open modal read only form for record */
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
	protected function additionalListSubRecordsAction(forestTwig $p_o_twig, $p_b_readonly, &$p_s_subFormItems, &$p_b_firstSubElement) {
		$o_glob = forestGlobals::init();
		
		/* look for forestdata */
		$o_formelement_forestdataTwig = new formelement_forestdataTwig;
		
		$a_tempTable = explode('_', $o_formelement_forestdataTwig->fphp_Table);
		$s_tempTable = $a_tempTable[(count($a_tempTable) - 1)];
		$s_foo = $s_tempTable . 'Twig';
		$o_lookupTwig = new $s_foo;
		$o_lookupData = new forestLookupData($o_lookupTwig->fphp_Table, $o_lookupTwig->fphp_Primary, $o_lookupTwig->fphp_View);
		
		$a_sqlAdditionalFilter = array(array('column' => 'formelementUUID', 'value' => $p_o_twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_formelement_forestdatas = $o_formelement_forestdataTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		$s_subTableHead = '';
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortName') . '</th>' . "\n";
		
		if (!$p_b_readonly) {
			$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
		}
		
		$s_subTableRows = '';
		
		foreach ($o_formelement_forestdatas->Twigs as $o_formelement_forestdata) {
			$s_subTableRows .=  '<tr>' . "\n";
			$o_lookupData->PrimaryValue = $o_formelement_forestdata->forestdataUUID;
			$s_subTableRows .=  '<td>' . $o_lookupData . '</td>' . "\n";
			
			if (!$p_b_readonly) {
				$a_parameters = $o_glob->URL->Parameters;
				unset($a_parameters['newKey']);
				unset($a_parameters['viewKey']);
				unset($a_parameters['editKey']);
				unset($a_parameters['editSubKey']);
				unset($a_parameters['deleteKey']);
				unset($a_parameters['deleteSubKey']);
				$a_parameters['deleteSubKey'] = 'forestdata~' . $o_formelement_forestdata->formelementUUID . '~' . $o_formelement_forestdata->forestdataUUID;
				
				$s_subTableRows .=  '<td><a href="' . forestLink::Link($o_glob->URL->Branch, 'delete', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="glyphicon glyphicon-trash text-danger"></span></a></td>' . "\n";
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
			$a_parameters['subConstraintKey'] = 'forestdata';
			$s_newButton = '<a href="' . forestLink::Link($o_glob->URL->Branch, 'new', $a_parameters) . '" class="btn btn-default" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="glyphicon glyphicon-plus text-success"></span></a>' . "\n";
		} else {
			$s_newButton = '';
		}
		
		$s_firstElement = '';
		
		if ($p_b_firstSubElement == false) {
			$s_firstElement = ' in';
			$p_b_firstSubElement = true;
		}
		
		$s_subFormItemContent = new forestTemplates(forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
		$p_s_subFormItems .= new forestTemplates(forestTemplates::SUBLISTVIEWITEM, array('subforestdata', $o_glob->GetTranslation('forestData') . ' (' . $o_formelement_forestdatas->Twigs->Count() . ')', $s_firstElement, $s_subFormItemContent));
		
		/* look for sqltype */
		$o_formelement_sqltypeTwig = new formelement_sqltypeTwig;
		
		$a_tempTable = explode('_', $o_formelement_sqltypeTwig->fphp_Table);
		$s_tempTable = $a_tempTable[(count($a_tempTable) - 1)];
		$s_foo = $s_tempTable . 'Twig';
		$o_lookupTwig = new $s_foo;
		$o_lookupData = new forestLookupData($o_lookupTwig->fphp_Table, $o_lookupTwig->fphp_Primary, $o_lookupTwig->fphp_View);
		
		$a_sqlAdditionalFilter = array(array('column' => 'formelementUUID', 'value' => $p_o_twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_formelement_sqltypes = $o_formelement_sqltypeTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		$s_subTableHead = '';
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortName') . '</th>' . "\n";
		
		if (!$p_b_readonly) {
			$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
		}
		
		$s_subTableRows = '';

		foreach ($o_formelement_sqltypes->Twigs as $o_formelement_sqltype) {
			$s_subTableRows .=  '<tr>' . "\n";
			$o_lookupData->PrimaryValue = $o_formelement_sqltype->sqltypeUUID;
			$s_subTableRows .=  '<td>' . $o_lookupData . '</td>' . "\n";
			
			if (!$p_b_readonly) {
				$a_parameters = $o_glob->URL->Parameters;
				unset($a_parameters['newKey']);
				unset($a_parameters['viewKey']);
				unset($a_parameters['editKey']);
				unset($a_parameters['editSubKey']);
				unset($a_parameters['deleteKey']);
				unset($a_parameters['deleteSubKey']);
				$a_parameters['deleteSubKey'] = 'sqltype~' . $o_formelement_sqltype->formelementUUID . '~' . $o_formelement_sqltype->sqltypeUUID;
				
				$s_subTableRows .=  '<td><a href="' . forestLink::Link($o_glob->URL->Branch, 'delete', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="glyphicon glyphicon-trash text-danger"></span></a></td>' . "\n";
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
			$a_parameters['subConstraintKey'] = 'sqltype';
			$s_newButton = '<a href="' . forestLink::Link($o_glob->URL->Branch, 'new', $a_parameters) . '" class="btn btn-default" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="glyphicon glyphicon-plus text-success"></span></a>' . "\n";
		} else {
			$s_newButton = '';
		}
		
		$s_firstElement = '';
		
		if ($p_b_firstSubElement == false) {
			$s_firstElement = ' in';
			$p_b_firstSubElement = true;
		}
		
		$s_subFormItemContent = new forestTemplates(forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
		$p_s_subFormItems .= new forestTemplates(forestTemplates::SUBLISTVIEWITEM, array('subsqltype', $o_glob->GetTranslation('SQLTypes') . ' (' . $o_formelement_sqltypes->Twigs->Count() . ')', $s_firstElement, $s_subFormItemContent));
		
		/* look for validationrule */
		$o_formelement_validationruleTwig = new formelement_validationruleTwig;
		
		$a_tempTable = explode('_', $o_formelement_validationruleTwig->fphp_Table);
		$s_tempTable = $a_tempTable[(count($a_tempTable) - 1)];
		$s_foo = $s_tempTable . 'Twig';
		$o_lookupTwig = new $s_foo;
		$o_lookupData = new forestLookupData($o_lookupTwig->fphp_Table, $o_lookupTwig->fphp_Primary, $o_lookupTwig->fphp_View);
		
		$a_sqlAdditionalFilter = array(array('column' => 'formelementUUID', 'value' => $p_o_twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_formelement_validationrules = $o_formelement_validationruleTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		$s_subTableHead = '';
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortName') . '</th>' . "\n";
		
		if (!$p_b_readonly) {
			$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
		}
		
		$s_subTableRows = '';

		foreach ($o_formelement_validationrules->Twigs as $o_formelement_validationrule) {
			$s_subTableRows .=  '<tr>' . "\n";
			$o_lookupData->PrimaryValue = $o_formelement_validationrule->validationruleUUID;
			$s_subTableRows .=  '<td>' . $o_lookupData . '</td>' . "\n";
			
			if (!$p_b_readonly) {
				$a_parameters = $o_glob->URL->Parameters;
				unset($a_parameters['newKey']);
				unset($a_parameters['viewKey']);
				unset($a_parameters['editKey']);
				unset($a_parameters['editSubKey']);
				unset($a_parameters['deleteKey']);
				unset($a_parameters['deleteSubKey']);
				$a_parameters['deleteSubKey'] = 'validationrule~' . $o_formelement_validationrule->formelementUUID . '~' . $o_formelement_validationrule->validationruleUUID;
				
				$s_subTableRows .=  '<td><a href="' . forestLink::Link($o_glob->URL->Branch, 'delete', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="glyphicon glyphicon-trash text-danger"></span></a></td>' . "\n";
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
			$a_parameters['subConstraintKey'] = 'validationrule';
			$s_newButton = '<a href="' . forestLink::Link($o_glob->URL->Branch, 'new', $a_parameters) . '" class="btn btn-default" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="glyphicon glyphicon-plus text-success"></span></a>' . "\n";
		} else {
			$s_newButton = '';
		}
		
		$s_firstElement = '';
		
		if ($p_b_firstSubElement == false) {
			$s_firstElement = ' in';
			$p_b_firstSubElement = true;
		}
		
		$s_subFormItemContent = new forestTemplates(forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
		$p_s_subFormItems .= new forestTemplates(forestTemplates::SUBLISTVIEWITEM, array('validationrule', $o_glob->GetTranslation('ValidationRules') . ' (' . $o_formelement_validationrules->Twigs->Count() . ')', $s_firstElement, $s_subFormItemContent));
	}

	/* overwrite - render modal form for new sub record */
	protected function RenderNewSubRecordForm() {
		$o_glob = forestGlobals::init();
		
		$o_subconstraintTwig = null;
		
		if ( ($o_glob->Temp->Exists('subConstraintKey')) && ($o_glob->Temp->{'subConstraintKey'} != null) ) {
			if ($o_glob->Temp->{'subConstraintKey'} == 'forestdata') {
				$o_subconstraintTwig = new formelement_forestdataTwig;
			} else if ($o_glob->Temp->{'subConstraintKey'} == 'sqltype') {
				$o_subconstraintTwig = new formelement_sqltypeTwig;
			} else if ($o_glob->Temp->{'subConstraintKey'} == 'validationrule') {
				$o_subconstraintTwig = new formelement_validationruleTwig;
			}
		}
		
		$o_glob->PostModalForm = new forestForm($o_subconstraintTwig);
		
		/* get table */
		$o_tableTwig = new tableTwig;
		
		/* query table record */
		if (!($o_tableTwig->GetRecordPrimary(array($o_subconstraintTwig->fphp_Table), array('Name')))) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		} else {
			$o_formelementTwig = new formelementTwig;
			
			/* look in tablefields for formobject, if not get the standard by formelementuuid */
			if (!($o_formelementTwig->GetRecordPrimary(array('form'), array('Name')))) {
				throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
			} else {
				$o_tablefieldTwig = new tablefieldTwig;
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
				
				/* create lookup for choosing subrecords as input list */
				$a_tempTable = explode('_', $o_subconstraintTwig->fphp_Table);
				$s_tempTable = $a_tempTable[(count($a_tempTable) - 1)];
				$s_foo = $s_tempTable . 'Twig';
				$o_lookupTwig = new $s_foo;
				$o_lookupData = new forestLookupData($o_lookupTwig->fphp_Table, $o_lookupTwig->fphp_Primary, $o_lookupTwig->fphp_View);
				
				$o_lookup = new forestFormElement(forestFormElement::LOOKUP);
				$o_lookup->Label = $o_glob->GetTranslation($o_glob->BranchTree['Id'][$o_glob->BranchTree['Name'][$s_tempTable]]['Title'], 1) . ' ' . $o_glob->GetTranslation('BranchTitleRecord', 1);
				$o_lookup->Id = $o_subconstraintTwig->fphp_Table . '_Lookup';
				$o_lookup->Options = $o_lookupData->CreateOptionsArray();
				$o_lookup->Required = true;
				$o_glob->PostModalForm->FormElements->Add($o_lookup);
				
				$o_glob->PostModalForm->FormObject->ValRequiredMessage = $o_glob->GetTranslation('ValRequiredMessage', 1);
				$o_glob->PostModalForm->FormObject->ValRules->Add(new forestFormValidationRule($o_lookup->Id, 'required', 'true', 'NULL', 'false'));
				
				$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_subConstraintKey';
				$o_hidden->Value = $o_glob->Temp->{'subConstraintKey'};
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
				
				$o_hidden2 = new forestFormElement(forestFormElement::HIDDEN);
				$o_hidden2->Id = 'sys_fphp_newKey';
				$o_hidden2->Value = $o_glob->Temp->{'newKey'};
				$o_glob->PostModalForm->FormElements->Add($o_hidden2);
				
				if (!($o_formelementTwig->GetRecordPrimary(array('cancel'), array('Name')))) {
					throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
				} else {
					$o_cancel = new forestFormElement(forestFormElement::BUTTON);
					$o_cancel->loadJSON($o_formelementTwig->JSONEncodedSettings);
					$o_cancel->ButtonText = htmlspecialchars_decode($o_cancel->ButtonText);
					$o_glob->PostModalForm->FormFooterElements->Add($o_cancel);
				}
				
				if (!($o_formelementTwig->GetRecordPrimary(array('submit'), array('Name')))) {
					throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
				} else {
					$o_button = new forestFormElement(forestFormElement::BUTTON);
					$o_button->loadJSON($o_formelementTwig->JSONEncodedSettings);
					$o_button->ButtonText = htmlspecialchars_decode($o_button->ButtonText);
					$o_glob->PostModalForm->FormFooterElements->Add($o_button);
				}
				
				$o_glob->PostModalForm->AddFormKey();
				$o_glob->PostModalForm->Automatic = true;
			}
		}
	}
}
?>