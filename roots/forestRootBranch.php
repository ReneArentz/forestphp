<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.2.0 (0x1 0001F)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * adminisration class for handling all adminstrative use cases for forestBranch and forestTwig objects
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.2.0 beta	renatus		2019-10-18	added to framework	
 */

abstract class forestRootBranch {
	use forestData;
	
	/* Fields */
	
	private $ForbiddenTablefieldNames = array('Id', 'UUID');
	private $StandardActions = array(
		'Delete' => 'delete',
		'Edit' => 'edit',
		'fphp Captcha' => 'fphp_captcha',
		'fphp Image Thumbnail' => 'fphp_imageThumbnail',
		'fphp Upload' => 'fphp_upload',
		'fphp Upload Delete' => 'fphp_upload_delete',
		'Read' => 'init',
		'Move Down' => 'moveDown',
		'Move Up' => 'moveUp',
		'New' => 'new',
		'Replace File' => 'replaceFile',
		'View' => 'view',
		'View Files' => 'viewFiles'
	);
	
	/* Properties */
	
	/* Methods */
	
	/* render root menu for root actions for every branch */
	protected function RenderRootMenu() {
		$o_glob = forestGlobals::init();
		
		$s_rootMenu = '';
		
		$s_rootMenu .= '<button class="btn btn-danger dropdown-toggle" type="button" data-toggle="dropdown" title="Root-Menu" style="margin-top: 8px;"><span class="glyphicon glyphicon glyphicon-wrench"></span></button>' . "\n";
		$s_rootMenu .= '<ul class="dropdown-menu">' . "\n";
			
			$s_rootMenu .= '<li><a href="' . forestLink::Link($o_glob->URL->Branch, 'newBranch') . '"><span class="glyphicon glyphicon-plus text-success" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootCreateBranchTitle', 1) . '</a></li>' . "\n";
			$s_rootMenu .= '<li><a href="' . forestLink::Link($o_glob->URL->Branch, 'viewBranch') . '"><span class="glyphicon glyphicon-zoom-in" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootViewBranchTitle', 1) . '</a></li>' . "\n";
			
			$s_rootMenu .= '<li class="dropdown-submenu">' . "\n";
				
				$s_rootMenu .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'editBranch') . '"><span class="glyphicon glyphicon-pencil" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootEditBranchTitle', 1) . '</a><a class="fphp_menu_dropdown" href="#"><span class="glyphicon glyphicon-menu-down"></span></a>' . "\n";
				
				$s_rootMenu .= '<ul class="dropdown-menu">' . "\n";
					if (!issetStr($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue)) {
						$s_rootMenu .= '<li><a href="' . forestLink::Link($o_glob->URL->Branch, 'newTwig') . '"><span class="glyphicon glyphicon-plus text-success" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootCreateTwigTitle', 1) . '</a></li>' . "\n";
					} else {
						$s_rootMenu .= '<li><a href="' . forestLink::Link($o_glob->URL->Branch, 'viewTwig') . '"><span class="glyphicon glyphicon-zoom-in" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootViewTwigTitle', 1) . '</a></li>' . "\n";
						$s_rootMenu .= '<li><a href="' . forestLink::Link($o_glob->URL->Branch, 'editTwig') . '"><span class="glyphicon glyphicon-pencil" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootEditTwigTitle', 1) . '</a></li>' . "\n";
						$s_rootMenu .= '<li><a href="' . forestLink::Link($o_glob->URL->Branch, 'deleteTwig') . '"><span class="glyphicon glyphicon-trash text-danger" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootDeleteTwigTitle', 1) . '</a></li>' . "\n";
					}
				$s_rootMenu .= '</ul>' . "\n";
			$s_rootMenu .= '</li>' . "\n";
			
			$s_rootMenu .= '<li><a href="' . forestLink::Link($o_glob->URL->Branch, 'moveUpBranch', array('editKey' => $o_glob->URL->BranchId)) . '"><span class="glyphicon glyphicon-triangle-top" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootMoveUpBranchTitle', 1) . '</a></li>' . "\n";
			$s_rootMenu .= '<li><a href="' . forestLink::Link($o_glob->URL->Branch, 'moveDownBranch', array('editKey' => $o_glob->URL->BranchId)) . '"><span class="glyphicon glyphicon-triangle-bottom" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootMoveDownBranchTitle', 1) . '</a></li>' . "\n";
			$s_rootMenu .= '<li><a href="' . forestLink::Link($o_glob->URL->Branch, 'deleteBranch') . '"><span class="glyphicon glyphicon-trash text-danger" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootDeleteBranchTitle', 1) . '</a></li>' . "\n";
			
		$s_rootMenu .= '</ul>' . "\n";
		
		return $s_rootMenu;
	}
	
	
	/* handle new branch record action */
	protected function newBranchAction() {
		$o_glob = forestGlobals::init();
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new branchTwig;
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			/* add new branch record form */
			$o_glob->PostModalForm = new forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
			
			/* delete NavigationOrder-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_NavigationOrder')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_NavigationOrder].');
			}
		} else {
			$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
			
			/* check posted data for new record */
			$this->TransferPOST_Twig();
			
			/* branch name must be all lowercase */
			$this->Twig->Name = strtolower($this->Twig->Name);
			
			/* add ParentBranch value to record */
			$this->Twig->ParentBranch = $o_glob->URL->BranchId;
			
			/* get last branch record */
			$i_order = 1;
			$o_branchTwig = new branchTwig;
			
			$a_sqlAdditionalFilter = array(array('column' => 'ParentBranch', 'value' => $this->Twig->ParentBranch, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			if ($o_branchTwig->GetLastRecord()) {
				$i_order = $o_branchTwig->NavigationOrder + 1;
			}
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			/* add NavigationOrder value to record */
			$this->Twig->NavigationOrder = $i_order;
			
			/* insert record */
			$i_result = $this->Twig->InsertRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				throw new forestException(0x10001402);
			} else if ($i_result == 1) {
				/* create branch file with folder in trunk */
				$s_path = '';
				
				if (count($o_glob->URL->Branches) > 0) {
					foreach($o_glob->URL->Branches as $s_value) {
						$s_path .= $s_value . '/';
					}
				}
				
				$s_path .= $o_glob->URL->Branch . '/';
				
				/* get directory content of current page into array */
				$a_dirContent = scandir('./trunk/' . $s_path);
				$s_path = './trunk/' . $s_path . $this->Twig->Name;
				
				/* if we cannot find fphp_files folder and we cannot create fphp_files folder as new directory */
				if (!in_array($this->Twig->Name, $a_dirContent)) {
					if (!mkdir($s_path)) {
						throw new forestException('Cannot create directory [%0].', array($s_path . $this->Twig->Name . '/'));
					}
				}
				
				$o_file = new forestFile($s_path . '/' . $this->Twig->Name . 'Branch.php', (!file_exists($s_path . '/' . $this->Twig->Name . 'Branch.php')));
				$o_file->ReplaceContent( strval(new forestTemplates(forestTemplates::CREATENEWBRANCH, array($this->Twig->Name))) );
				
				/* create translation record for branch title */
				$o_translationTwig = new translationTwig;
				$o_translationTwig->BranchId = 1;
				$o_translationTwig->LanguageCode = $o_glob->Trunk->LanguageCode;
				$o_translationTwig->Name = $this->Twig->Title;
				$o_translationTwig->Value = $this->Twig->Title;
				$i_result = $o_translationTwig->InsertRecord();
				
				/* create standard actions for branch */
				foreach($this->StandardActions as $s_actionLabel => $s_actionValue) {
					$o_actionTwig = new actionTwig;
					$o_actionTwig->BranchId = $this->Twig->Id;
					$o_actionTwig->Name = $s_actionValue;
					
					/* insert action record */
					$i_result = $o_actionTwig->InsertRecord();
					
					/* evaluate result */
					if ($i_result == -1) {
						throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
					} else if ($i_result == 0) {
						throw new forestException(0x10001402);
					}
				}
				
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
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/* handle edit branch record action */
	protected function editBranchAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ( ($this->Twig != null) && ($this->Twig->fphp_SystemTable) ) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new branchTwig;
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if ($o_glob->IsPost) {
			/* query record */
			if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_branchKey']))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}
			
			/* get StandardView value */
			if ($this->StandardView == forestBranch::LIST) {
				$this->Twig->StandardView = 1;
			} else if ($this->StandardView == forestBranch::DETAIL) {
				$this->Twig->StandardView = 10;
			} else if ($this->StandardView == forestBranch::FLEX) {
				$this->Twig->StandardView = 100;
			}
			
			/* get Filter value */
			if ($this->Filter->value) {
				$this->Twig->Filter = true;
			} else {
				$this->Twig->Filter = false;
			}
			
			$this->TransferPOST_Twig();
			
			/* change translation record for new title */
			$o_translationTwig = new translationTwig;
			
			if (! ($o_translationTwig->GetRecordPrimary(array(1, $o_glob->Trunk->LanguageCode, $o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Title']), array('BranchId', 'LanguageCode', 'Name'))) ) {
				throw new forestException(0x10001401, array($o_translationTwig->fphp_Table));
			}
			
			$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
			
			/* change title value and execute update */
			$o_translationTwig->Name = $this->Twig->Title;
			$o_translationTwig->Value = $this->Twig->Title;
			$i_result = $o_translationTwig->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			}
			
			/* if branch is connected with table, update branch file */
			if (issetStr($this->Twig->Table->PrimaryValue)) {
				/* gather information */
				$s_filter = 'false';
				
				if ($this->Twig->Filter) {
					$s_filter = 'true';
				}
				
				$s_keepFilter = 'false';
				
				if ($this->Twig->KeepFilter) {
					$s_keepFilter = 'true';
				}
				
				$s_standardView = 'forestBranch::LIST';
				
				if ($this->Twig->StandardView == 10) {
					$s_standardView = 'forestBranch::DETAIL';
				} else if ($this->Twig->StandardView == 100) {
					$s_standardView = 'forestBranch::FLEX';
				}
				
				$o_tableTwig = new tableTwig;
				
				if (! ($o_tableTwig->GetRecord(array($this->Twig->Table->PrimaryValue))) ) {
					throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
				}
				
				$s_tableName = $o_tableTwig->Name;
				forestStringLib::RemoveTablePrefix($s_tableName);
				
				/* get branch file path */
				$s_path = '';
						
				if (count($o_glob->URL->Branches) > 0) {
					foreach($o_glob->URL->Branches as $s_value) {
						$s_path .= $s_value . '/';
					}
				}
				
				$s_path .= $o_glob->URL->Branch . '/';
				
				/* get directory content of current page into array */
				$a_dirContent = scandir('./trunk/' . $s_path);
				$s_path = './trunk/' . $s_path . $this->Twig->Name . 'Branch.php';
				
				/* if we cannot find branch file */
				if (!in_array($this->Twig->Name . 'Branch.php', $a_dirContent)) {
					throw new forestException('Cannot find file [%0].', array($s_path));
				}
				
				/* update branch file */
				$o_file = new forestFile($s_path);
				$o_file->ReplaceContent( strval(new forestTemplates(forestTemplates::CREATENEWBRANCHWITHTWIG, array($this->Twig->Name, $s_tableName, $s_filter, $s_standardView, $s_keepFilter))) );
			}
			
			/* edit recrod */
			$i_result = $this->Twig->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				$o_glob->SystemMessages->Add(new forestException(0x10001406));
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001407));
			}
		} else {
			/* query record */
			if (! ($this->Twig->GetRecord(array($o_glob->URL->BranchId))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}
			
			/* get StandardView value */
			if ($this->StandardView == forestBranch::LIST) {
				$this->Twig->StandardView = 1;
			} else if ($this->StandardView == forestBranch::DETAIL) {
				$this->Twig->StandardView = 10;
			} else if ($this->StandardView == forestBranch::FLEX) {
				$this->Twig->StandardView = 100;
			}
			
			/* get Filter value */
			if ($this->Filter->value) {
				$this->Twig->Filter = true;
			} else {
				$this->Twig->Filter = false;
			}
			
			/* build modal form */
			$o_glob->PostModalForm = new forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('EditModalTitle', 1);
			
			/* add current record key to modal form */
			$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
			$o_hidden->Id = 'sys_fphp_branchKey';
			$o_hidden->Value = strval($o_glob->URL->BranchId);
			
			$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
			
			/* delete Name-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_Name')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_Name].');
			}
			
			/* delete NavigationOrder-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_NavigationOrder')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_NavigationOrder].');
			}
			
			/* add current record order to modal form as hidden field */
			$o_hiddenOrder = new forestFormElement(forestFormElement::HIDDEN);
			$o_hiddenOrder->Id = 'sys_fphp_branch_NavigationOrder';
			$o_hiddenOrder->Value = strval($this->Twig->NavigationOrder);
			
			$o_glob->PostModalForm->FormFooterElements->Add($o_hiddenOrder);
		}
		
		if (isset($this->KeepFilter)) {
			if ($this->KeepFilter->value) {
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/* handle view branch record action */
	protected function viewBranchAction() {
		$o_glob = forestGlobals::init();
		$o_saveTwig = $this->Twig;
		$this->Twig = new branchTwig;
		
		/* query branch record */
		if (! ($this->Twig->GetRecord(array($o_glob->URL->BranchId))) ) {
			throw new forestException(0x10001401, array($this->Twig->fphp_Table));
		}
		
		/* get StandardView value */
		if ($this->StandardView == forestBranch::LIST) {
			$this->Twig->StandardView = 1;
		} else if ($this->StandardView == forestBranch::DETAIL) {
			$this->Twig->StandardView = 10;
		} else if ($this->StandardView == forestBranch::FLEX) {
			$this->Twig->StandardView = 100;
		}
		
		/* get Filter value */
		if ($this->Filter->value) {
			$this->Twig->Filter = true;
		} else {
			$this->Twig->Filter = false;
		}
		
		/* create modal read only form */
		$o_glob->PostModalForm = new forestForm($this->Twig, true, true);
		$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->URL->BranchTitle . ' Branch';
		
		/* delete Name-element */
		if (!$o_glob->PostModalForm->DeleteFormElementByFormId('readonly_sys_fphp_branch_Name')) {
			throw new forestException('Cannot delete form element with Id[readonly_sys_fphp_branch_Name].');
		}
		
		/* delete NavigationOrder-element */
		if (!$o_glob->PostModalForm->DeleteFormElementByFormId('readonly_sys_fphp_branch_NavigationOrder')) {
			throw new forestException('Cannot delete form element with Id[readonly_sys_fphp_branch_NavigationOrder].');
		}
		
		$s_subFormItems = '';
	
		/* ************************************************** */
		/* **********************ACTIONS********************* */
		/* ************************************************** */
		/* look for tablefields */
		$o_actionTwig = new actionTwig;
		
		$a_sqlAdditionalFilter = array(array('column' => 'BranchId', 'value' => $o_glob->URL->BranchId, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_actions = $o_actionTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		/* ************************* */
		/* ***********HEAD********** */
		/* ************************* */
		
		$s_subTableHead = '';
		$s_subTableHead .= '<th>Action</th>' . "\n";
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
		
		/* ************************* */
		/* ***********ROWS********** */
		/* ************************* */
		
		$s_subTableRows = '';
		
		foreach ($o_actions->Twigs as $o_action) {
			/* render records based on twig view columns */
			$s_subTableRows .=  '<tr>' . "\n";
			$s_subTableRows .=  '<td><span>' . $o_action->Name . '</span></td>' . "\n";
			
			$s_options = '<span class="btn-group">' . "\n";
			
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
			$a_parameters['editKey'] = $o_action->Id;
			$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'editAction', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="glyphicon glyphicon-pencil"></span></a>' . "\n";
			
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
			$a_parameters['deleteKey'] = $o_action->Id;
			$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'deleteAction', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="glyphicon glyphicon-trash text-danger"></span></a>' . "\n";
			
			$s_options .= '</span>' . "\n";
			$s_subTableRows .=  '<td>' . $s_options . '</td>' . "\n";
			
			$s_subTableRows .=  '</tr>' . "\n";
		}
		
		/* new link */
		$s_newButton = '<a href="' . forestLink::Link($o_glob->URL->Branch, 'newAction') . '" class="btn btn-default" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="glyphicon glyphicon-plus text-success"></span></a>' . "\n";
		
		$s_subFormItemContent = new forestTemplates(forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
		$s_subFormItems .= new forestTemplates(forestTemplates::SUBLISTVIEWITEM, array('actions' . $this->Twig->fphp_Table, 'Actions' . ' (' . $o_actions->Twigs->Count() . ')', ' in', $s_subFormItemContent));
		
		/* use template to render and add actions for modal form of branch record */
		$o_glob->PostModalForm->FormModalSubForm = strval(new forestTemplates(forestTemplates::SUBLISTVIEW, array($s_subFormItems)));
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction('init');
	}
	
	/* handle action to change order of branch record in navigation, moving one record up */
	protected function moveUpBranchAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ( ($this->Twig != null) && ($this->Twig->fphp_SystemTable) ) {
			throw new forestException(0x10001F16);
		}
		
		$o_saveTwig = $this->Twig;
		$this->Twig = new branchTwig;
		
		/* query branch record */
		if (! ($this->Twig->GetRecord(array($o_glob->URL->BranchId))) ) {
			throw new forestException(0x10001401, array($this->Twig->fphp_Table));
		}
		
		$a_sqlAdditionalFilter = array(array('column' => 'ParentBranch', 'value' => $o_glob->BranchTree['Id'][$this->Twig->Id]['ParentBranch'], 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$this->MoveUpRecord();
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		if (isset($this->KeepFilter)) {
			if ($this->KeepFilter->value) {
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction('RELOADBRANCH');
	}
	
	/* handle action to change order of branch record in navigation, moving one record down */
	protected function moveDownBranchAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ( ($this->Twig != null) && ($this->Twig->fphp_SystemTable) ) {
			throw new forestException(0x10001F16);
		}
		
		$o_saveTwig = $this->Twig;
		$this->Twig = new branchTwig;
		
		/* query branch record */
		if (! ($this->Twig->GetRecord(array($o_glob->URL->BranchId))) ) {
			throw new forestException(0x10001401, array($this->Twig->fphp_Table));
		}
		
		$a_sqlAdditionalFilter = array(array('column' => 'ParentBranch', 'value' => $o_glob->BranchTree['Id'][$this->Twig->Id]['ParentBranch'], 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$this->MoveDownRecord();
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		if (isset($this->KeepFilter)) {
			if ($this->KeepFilter->value) {
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction('RELOADBRANCH');
	}
	
	/* handle delete branch record action */
	protected function deleteBranchAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ( ($this->Twig != null) && ($this->Twig->fphp_SystemTable) ) {
			throw new forestException(0x10001F16);
		}
		
		$o_saveTwig = $this->Twig;
		$this->Twig = new branchTwig;
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			if (! ($this->Twig->GetRecord(array($o_glob->URL->BranchId))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}
			
			/* check if branch has no children */
			$i_amount = 0;
		
			foreach ($o_glob->BranchTree['Id'] as $o_branch) {
				if ($o_branch['ParentBranch'] == $this->Twig->Id) {
					$i_amount++;
				}
			}
			
			if ($i_amount > 0) {
				throw new forestException(0x10001F00);
			}
			
			/* check if branch is not connected with a table */
			if (issetStr($this->Twig->Table->PrimaryValue)) {
				throw new forestException(0x10001F01);
			}
			
			/* create modal confirmation form for deleting record */
			$o_glob->PostModalForm = new forestForm($this->Twig);
			$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
			$s_description = '<b>' . $o_glob->GetTranslation('DeleteModalDescriptionOne', 1) . '</b>';
			$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);
			
			$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
			$o_hidden->Id = 'sys_fphp_branchKey';
			$o_hidden->Value = strval($o_glob->URL->BranchId);
			$o_glob->PostModalForm->FormElements->Add($o_hidden);
		} else {
			/* delete record */
			if (array_key_exists('sys_fphp_branchKey', $_POST)) {
				if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_branchKey']))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* check if branch has no children */
				$i_amount = 0;
			
				foreach ($o_glob->BranchTree['Id'] as $o_branch) {
					if ($o_branch['ParentBranch'] == $this->Twig->Id) {
						$i_amount++;
					}
				}
			
				if ($i_amount > 0) {
					throw new forestException(0x10001F00);
				}
				
				/* check if branch is not connected with a table */
				if (issetStr($this->Twig->Table->PrimaryValue)) {
					throw new forestException(0x10001F01);
				}
				
				$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
				
				/* delete all actions */
				$o_actionTwig = new actionTwig; 
				
				$a_sqlAdditionalFilter = array(array('column' => 'BranchId', 'value' => $_POST['sys_fphp_branchKey'], 'operator' => '=', 'filterOperator' => 'AND'));
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$o_actions = $o_actionTwig->GetAllRecords(true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				
				if ($o_actions->Twigs->Count() > 0) {
					foreach ($o_actions->Twigs as $o_action) {
						/* delete file record */
						$i_return = $o_action->DeleteRecord();
						
						/* evaluate the result */
						if ($i_return <= 0) {
							throw new forestException(0x10001423);
						}
					}
				}
				
				/* delete translation title */
				$o_translationTwig = new translationTwig;
	
				if (! ($o_translationTwig->GetRecordPrimary(array(1, $o_glob->Trunk->LanguageCode, $this->Twig->Title), array('BranchId', 'LanguageCode', 'Name'))) ) {
					throw new forestException(0x10001401, array($o_translationTwig->fphp_Table));
				}
				
				/* change title value and execute update */
				$i_return = $o_translationTwig->DeleteRecord();
				
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
				
				/* delete branch file structure */
				$s_path = '';

				if (count($o_glob->URL->Branches) > 0) {
					foreach($o_glob->URL->Branches as $s_value) {
						$s_path .= $s_value . '/';
					}
				}
				
				$s_path .= $o_glob->URL->Branch . '/';
				
				$s_path = './trunk/' . $s_path;
				forestFile::RemoveDirectoryRecursive($s_path);
				
				/* delete record */
				$i_return = $this->Twig->DeleteRecord();
				
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
				
				$o_glob->SystemMessages->Add(new forestException(0x10001427));
			}
		}
		
		if (isset($this->KeepFilter)) {
			if ($this->KeepFilter->value) {
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction('init');
	}


	/* handle new action record action */
	protected function newActionAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$o_saveTwig = $this->Twig;
		$this->Twig = new actionTwig;
		$o_branchTwig = new branchTwig;
		$s_nextAction = 'init';
		
		/* query branch record */
		if (! ($o_branchTwig->GetRecord(array($o_glob->URL->BranchId))) ) {
			throw new forestException(0x10001401, array($o_branchTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			/* add new branch record form */
			$o_glob->PostModalForm = new forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
			
			/* delete BranchId-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_action_BranchId')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_action_BranchId].');
			}
		} else {
			/* check posted data for new tablefield_validationrule record */
			$this->TransferPOST_Twig();
			
			/* add BranchId value to record */
			$this->Twig->BranchId = $o_glob->URL->BranchId;
			
			/* first character of action name must be lowercase */
			$this->Twig->Name = lcfirst($this->Twig->Name);
			
			/* insert record */
			$i_result = $this->Twig->InsertRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				throw new forestException(0x10001402);
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001404));
				$s_nextAction = 'viewBranch';
			}
		}
		
		if (isset($this->KeepFilter)) {
			if ($this->KeepFilter->value) {
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}

	/* handle edit action record action */
	protected function editActionAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$o_saveTwig = $this->Twig;
		$this->Twig = new actionTwig;
		$o_branchTwig = new branchTwig;
		$s_nextAction = 'init';
		
		/* query branch record */
		if (! ($o_branchTwig->GetRecord(array($o_glob->URL->BranchId))) ) {
			throw new forestException(0x10001401, array($o_branchTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if ($o_glob->IsPost) {
			/* query record */
			if (array_key_exists('sys_fphp_actionKey', $_POST)) {
				if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_actionKey']))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* check posted data for tablefield_validationrule record */
				$this->TransferPOST_Twig();
				
				/* add BranchId value to record */
				$this->Twig->BranchId = $o_glob->URL->BranchId;
				
				/* first character of action name must be lowercase */
				$this->Twig->Name = lcfirst($this->Twig->Name);
				
				/* edit record */
				$i_result = $this->Twig->UpdateRecord();
				
				/* evaluate result */
				if ($i_result == -1) {
					throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
				} else if ($i_result == 0) {
					$o_glob->SystemMessages->Add(new forestException(0x10001406));
					$s_nextAction = 'viewBranch';
				} else if ($i_result == 1) {
					$o_glob->SystemMessages->Add(new forestException(0x10001407));
					$s_nextAction = 'viewBranch';
				}
			}
		} else {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editKey'), 'editKey' );
		
			if ( ($o_glob->Temp->Exists('editKey')) && ($o_glob->Temp->{'editKey'} != null) ) {
				/* query record */
				if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'editKey'}))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* build modal form */
				$o_glob->PostModalForm = new forestForm($this->Twig, true);
				$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('EditModalTitle', 1);
				
				/* add tablefield record key to modal form as hidden field */
				$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_actionKey';
				$o_hidden->Value = strval($this->Twig->Id);
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
				
				/* delete BranchId-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_action_BranchId')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_action_BranchId].');
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
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/* handle delete action record action */
	protected function deleteActionAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$o_saveTwig = $this->Twig;
		$this->Twig = new actionTwig;
		$o_branchTwig = new branchTwig;
		$s_nextAction = 'init';
		
		/* query branch record */
		if (! ($o_branchTwig->GetRecord(array($o_glob->URL->BranchId))) ) {
			throw new forestException(0x10001401, array($o_branchTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteKey'), 'deleteKey' );
			
			if ( ($o_glob->Temp->Exists('deleteKey')) && ($o_glob->Temp->{'deleteKey'} != null) ) {
				if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'deleteKey'}))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* create modal confirmation form for deleting record */
				$o_glob->PostModalForm = new forestForm($this->Twig);
				$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
				$s_description = '<b>' . $o_glob->GetTranslation('DeleteModalDescriptionOne', 1) . '</b>';
				$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);	
				
				$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_actionKey';
				$o_hidden->Value = strval($this->Twig->Id);
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
			}
		} else {
			if (array_key_exists('sys_fphp_actionKey', $_POST)) {
				if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_actionKey']))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* delete record */
				$i_return = $this->Twig->DeleteRecord();
				
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
				
				$o_glob->SystemMessages->Add(new forestException(0x10001427));
				$s_nextAction = 'viewBranch';
			}
		}
		
		if (isset($this->KeepFilter)) {
			if ($this->KeepFilter->value) {
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}


	/* handle new twig record action */
	protected function newTwigAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ( ($this->Twig != null) && ($this->Twig->fphp_SystemTable) ) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new tablefieldTwig;
		
		if ($o_glob->URL->BranchId == 1) {
			throw new forestException(0x10001F05);
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			/* add new branch record form */
			$o_glob->PostModalForm = new forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
			
			$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
			$o_hidden->Id = 'sys_fphp_table_name';
			$o_hidden->Value = strval($o_glob->URL->Branch);
			
			$o_description = new forestFormElement(forestFormElement::DESCRIPTION);
			$o_description->Description = '<b>' . $o_glob->GetTranslation('rootNewTwigFirstFieldTitle', 0) . '</b>';
			
			/* add manual created form elements to genereal tab */
			if (!$o_glob->PostModalForm->AddFormElement($o_description, 'general', true)) {
				throw new forestException('Cannot add form element to tab with id[general].');
			}
			
			if (!$o_glob->PostModalForm->AddFormElement($o_hidden, 'general', true)) {
				throw new forestException('Cannot add form element to tab with id[general].');
			}
			
			/* delete Order-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_tablefield_Order')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_tablefield_Order].');
			}
			
			/* delete SubRecordField-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_tablefield_SubRecordField')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_tablefield_SubRecordField].');
			}
		} else {
			if (!array_key_exists('sys_fphp_table_name', $_POST)) {
				throw new forestException('No POST data for field[sys_fphp_table_name]');
			}
			
			if (array_key_exists('sys_fphp_tablefield_FormElementUUID', $_POST)) {
				if (array_key_exists('sys_fphp_tablefield_SqlTypeUUID', $_POST)) {
					$o_formelement_sqltypeTwig = new formelement_sqltypeTwig;
					
					if (! $o_formelement_sqltypeTwig->GetRecord(array($_POST['sys_fphp_tablefield_FormElementUUID'], $_POST['sys_fphp_tablefield_SqlTypeUUID']))) {
						$o_formelementTwig = new formelementTwig;
						$o_sqltypeTwig = new sqltypeTwig;
						
						if (! ($o_formelementTwig->GetRecord(array($_POST['sys_fphp_tablefield_FormElementUUID']))) ) {
							throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
						}
						
						if (! ($o_sqltypeTwig->GetRecord(array($_POST['sys_fphp_tablefield_SqlTypeUUID']))) ) {
							throw new forestException(0x10001401, array($o_sqltypeTwig->fphp_Table));
						}
						
						throw new forestException(0x10001F0D, array($o_formelementTwig->Name, $o_sqltypeTwig->Name));
					}
				}
				
				if (array_key_exists('sys_fphp_tablefield_ForestDataUUID', $_POST)) {
					$o_formelement_forestdataTwig = new formelement_forestdataTwig;
					
					if (! $o_formelement_forestdataTwig->GetRecord(array($_POST['sys_fphp_tablefield_FormElementUUID'], $_POST['sys_fphp_tablefield_ForestDataUUID']))) {
						$o_formelementTwig = new formelementTwig;
						$o_forestdataTwig = new forestdataTwig;
						
						if (! ($o_formelementTwig->GetRecord(array($_POST['sys_fphp_tablefield_FormElementUUID']))) ) {
							throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
						}
						
						if (! ($o_forestdataTwig->GetRecord(array($_POST['sys_fphp_tablefield_ForestDataUUID']))) ) {
							throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
						}
						
						throw new forestException(0x10001F0E, array($o_formelementTwig->Name, $o_forestdataTwig->Name));
					}
				}
			}
			
			$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
			
			/* create new table record */
			$o_tableTwig = new tableTwig;
			
			if ($_POST['sys_fphp_table_name'] != $o_glob->URL->Branch) {
				throw new forestException(0x10001F03);
			}
			
			if (! ( (forestStringLib::StartsWith($_POST['sys_fphp_table_name'], 'fphp_')) || (forestStringLib::StartsWith($_POST['sys_fphp_table_name'], 'sys_fphp_')) ) ) {
				$_POST['sys_fphp_table_name'] = 'fphp_' . $_POST['sys_fphp_table_name'];
			}
			
			$o_tableTwig->Name = $_POST['sys_fphp_table_name'];
			
			/* insert record */
			$i_result = $o_tableTwig->InsertRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				throw new forestException(0x10001402);
			} else if ($i_result == 1) {
				/* create table in dbms with standard Id + UUID */
				$o_queryCreate = new forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, forestSQLQuery::CREATE, $o_tableTwig->Name);
	
				$o_columnId = new forestSQLColumnStructure($o_queryCreate);
					$o_columnId->Name = 'Id';
					$o_columnId->ColumnType = 'INT';
					$o_columnId->ConstraintList->Add('UNSIGNED');
					$o_columnId->ConstraintList->Add('NOT NULL');
					$o_columnId->ConstraintList->Add('PRIMARY KEY');
					$o_columnId->ConstraintList->Add('AUTO_INCREMENT');
				
				$o_columnUUID = new forestSQLColumnStructure($o_queryCreate);
					$o_columnUUID->Name = 'UUID';
					$o_columnUUID->ColumnType = 'VARCHAR';
					$o_columnUUID->ColumnTypeLength = 36;
					$o_columnUUID->ConstraintList->Add('NOT NULL');
					$o_columnUUID->ConstraintList->Add('UNIQUE');
					
				$o_queryCreate->Query->Columns->Add($o_columnId);	
				$o_queryCreate->Query->Columns->Add($o_columnUUID);
				
				/* create table does not return a value - maybe using show_tables can be used as extra verification */
				$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryCreate, false, false);
				
				/* update branch record with new table connection */
				$o_branchTwig = new branchTwig;
				
				/* query record */
				if (! ($o_branchTwig->GetRecord(array($o_glob->URL->BranchId))) ) {
					throw new forestException(0x10001401, array($o_branchTwig->fphp_Table));
				}
				
				/* update table connection */
				$o_branchTwig->Table = $o_tableTwig->UUID;
				
				/* edit record */
				$i_result = $o_branchTwig->UpdateRecord();
				
				/* evaluate result */
				if ($i_result == -1) {
					throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
				} else if ($i_result == 0) {
					$o_glob->SystemMessages->Add(new forestException(0x10001406));
				} else if ($i_result == 1) {
					/* check posted data for new tablefield record */
					$this->TransferPOST_Twig();
					
					if (in_array($this->Twig->FieldName, $this->ForbiddenTablefieldNames)) {
						throw new forestException(0x10001F10, array(implode(',', $this->ForbiddenTablefieldNames)));
					}
					
					/* add Order value to record */
					$this->Twig->Order = 1;
					
					/* check if json encoded settings are valid */
					$a_settings = json_decode($_POST['sys_fphp_tablefield_JSONEncodedSettings'], true);
				
					if ($a_settings == null) {
						throw new forestException(0x10001F02, array($_POST['sys_fphp_tablefield_JSONEncodedSettings']));
					}
					
					/* if no json setting for Id is available, add it automatically */
					if (!array_key_exists('Id', $a_settings)) {
						$a_settings['Id'] = $o_tableTwig->Name . '_' . $this->Twig->FieldName;
						$_POST['sys_fphp_tablefield_JSONEncodedSettings'] = json_encode($a_settings, JSON_UNESCAPED_SLASHES );
						$this->Twig->JSONEncodedSettings = strval($_POST['sys_fphp_tablefield_JSONEncodedSettings']);
					}
					
					/* recognize translation name and value pair within json encoded settings and create tranlsation records automatically */
					preg_match_all('/\#([^#]+)\#/', $_POST['sys_fphp_tablefield_JSONEncodedSettings'], $a_matches);
			
					if (count($a_matches) > 1) {
						foreach ($a_matches[1] as $s_match) {
							$s_name = 'NULL';
							$s_value = 'translation_in_progress';
							
							if (strpos($s_match, '=') !== false) {
								$a_match = explode('=', $s_match);
								$s_name = $a_match[0];
								$s_value = $a_match[1];
								$_POST['sys_fphp_tablefield_JSONEncodedSettings'] = str_replace('#' . $s_match . '#', '#' . $s_name . '#', $_POST['sys_fphp_tablefield_JSONEncodedSettings']);
								$this->Twig->JSONEncodedSettings = strval($_POST['sys_fphp_tablefield_JSONEncodedSettings']);
							} else {
								$s_name = $s_match;
							}
							
							if (strlen($s_name) >= 8) {
								$o_translationTwig = new translationTwig;
								$o_translationTwig->BranchId = $o_glob->URL->BranchId;
								$o_translationTwig->LanguageCode = $o_glob->Trunk->LanguageCode->PrimaryValue;
								$o_translationTwig->Name = $s_name;
								$o_translationTwig->Value = forestStringLib::ReplaceUnicodeEscapeSequence($s_value);
								
								/* insert translation record */
								$i_result = $o_translationTwig->InsertRecord();
							}
						}
					}
					
					/* add TableUUID value to record */
					$this->Twig->TableUUID = $o_tableTwig->UUID;
					
					/* insert record */
					$i_result = $this->Twig->InsertRecord();
					
					/* evaluate result */
					if ($i_result == -1) {
						throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
					} else if ($i_result == 0) {
						throw new forestException(0x10001402);
					} else if ($i_result == 1) {
						$o_glob->SystemMessages->Add(new forestException(0x10001F04));
						
						/* execute dbms create column if sql type is not empty */
						if (issetStr($this->Twig->SqlTypeUUID->PrimaryValue)) {
							/* new tablefield for twig - ignore forestCombination, Form and Dropzone field */
							if ( (!(strval($this->Twig->ForestDataUUID) == 'forestCombination')) && (!(strval($this->Twig->FormElementUUID) == forestformElement::FORM)) && (!(strval($this->Twig->FormElementUUID) == forestformElement::DROPZONE)) ) {
								/* add new column within table in dbms */
								$o_queryAlter = new forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, forestSQLQuery::ALTER, $o_tableTwig->Name);
		
								$o_column = new forestSQLColumnStructure($o_queryAlter);
									$o_column->Name = $this->Twig->FieldName;
									
									$s_columnType = null;
									$i_columnLength = null;
									$i_columnDecimalLength = null;
									forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, strval($this->Twig->SqlTypeUUID), $s_columnType, $i_columnLength, $i_columnDecimalLength);
									
									$o_column->ColumnType = $s_columnType;
									if ($i_columnLength != null) { $o_column->ColumnTypeLength = $i_columnLength; }
									if ($i_columnDecimalLength != null) { $o_column->ColumnTypeDecimalLength = $i_columnDecimalLength; }
									$o_column->AlterOperation = 'ADD';
									$o_column->ConstraintList->Add('NULL');
								
								$o_queryAlter->Query->Columns->Add($o_column);	
								
								/* alter table does not return a value */
								$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false);
							}
						}
						
						/* create twig file */
						$this->doTwigFile($o_tableTwig);
						
						/* update branch */
						/* gather information */
						$s_filter = 'false';
						
						if ($o_branchTwig->Filter) {
							$s_filter = 'true';
						}
						
						$s_keepFilter = 'false';
						
						if ($o_branchTwig->KeepFilter) {
							$s_keepFilter = 'true';
						}
						
						$s_standardView = 'forestBranch::LIST';
						
						if ($o_branchTwig->StandardView == 10) {
							$s_standardView = 'forestBranch::DETAIL';
						} else if ($o_branchTwig->StandardView == 100) {
							$s_standardView = 'forestBranch::FLEX';
						}
						
						$s_tableName = $o_tableTwig->Name;
						forestStringLib::RemoveTablePrefix($s_tableName);
						
						/* get branch file path */
						$s_path = '';
								
						if (count($o_glob->URL->Branches) > 0) {
							foreach($o_glob->URL->Branches as $s_value) {
								$s_path .= $s_value . '/';
							}
						}
						
						$s_path .= $o_glob->URL->Branch . '/';
						
						/* get directory content of current page into array */
						$a_dirContent = scandir('./trunk/' . $s_path);
						$s_path = './trunk/' . $s_path . $o_branchTwig->Name . 'Branch.php';
						
						/* if we cannot find branch file */
						if (!in_array($o_branchTwig->Name . 'Branch.php', $a_dirContent)) {
							throw new forestException('Cannot find file [%0].', array($s_path));
						}
						
						/* update branch file */
						$o_file = new forestFile($s_path);
						$o_file->ReplaceContent( strval(new forestTemplates(forestTemplates::CREATENEWBRANCHWITHTWIG, array($o_branchTwig->Name, $s_tableName, $s_filter, $s_standardView, $s_keepFilter))) );
						
						$s_nextAction = 'RELOADBRANCH';
					}
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
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}

	/* handle edit twig record action */
	protected function editTwigAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new tableTwig;
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if ($o_glob->IsPost) {
			/* query record */
			if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_tableKey']))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}
			
			$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
			
			$this->TransferPOST_Twig();
			
			/* edit recrod */
			$i_result = $this->Twig->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				$o_glob->SystemMessages->Add(new forestException(0x10001406));
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001407));
				
				/* update twig file */
				$this->doTwigFile($this->Twig);
			}
		} else {
			/* query record */
			if (! ($this->Twig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}
			
			$o_forestdataTwig = new forestdataTwig;
			
			if (! ($o_forestdataTwig->GetRecordPrimary(array('forestCombination'), array('Name'))) ) {
				throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
			}
			
			$s_forestCombinationUUID = $o_forestdataTwig->UUID;
			
			if (! ($o_forestdataTwig->GetRecordPrimary(array('forestLookup'), array('Name'))) ) {
				throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
			}
			
			$s_forestLookupUUID = $o_forestdataTwig->UUID;
			
			/* update lookup filter */
			$o_forestLookupData = new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID, '!2ForestDataUUID' => $s_forestLookupUUID));
			$this->Twig->Unique->SetLookupData($o_forestLookupData);
			$this->Twig->SortOrder->SetLookupData($o_forestLookupData);
			$this->Twig->View->SetLookupData(new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID)));
			$this->Twig->SortColumn->SetLookupData($o_forestLookupData);
			
			/* build modal form */
			$o_glob->PostModalForm = new forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('EditModalTitle', 1);
			
			/* add current record key to modal form */
			$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
			$o_hidden->Id = 'sys_fphp_tableKey';
			$o_hidden->Value = strval($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue);
			
			$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
			
			/* delete Unique-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Unique[]')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_Unique[]].');
			}
			
			/* delete SortOrder-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_SortOrder')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_SortOrder].');
			}
		}
		
		if (isset($this->KeepFilter)) {
			if ($this->KeepFilter->value) {
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/* handle view twig record action */
	protected function viewTwigAction() {
		$o_glob = forestGlobals::init();
		$o_saveTwig = $this->Twig;
		$this->Twig = new tableTwig;
			
		/* query twig record if we have view key in url parameters */
		if (! ($this->Twig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($this->Twig->fphp_Table));
		}
		
		$o_forestdataTwig = new forestdataTwig;
			
		if (! ($o_forestdataTwig->GetRecordPrimary(array('forestCombination'), array('Name'))) ) {
			throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
		}
		
		$s_forestCombinationUUID = $o_forestdataTwig->UUID;
		
		if (! ($o_forestdataTwig->GetRecordPrimary(array('forestLookup'), array('Name'))) ) {
			throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
		}
		
		$s_forestLookupUUID = $o_forestdataTwig->UUID;
		
		/* update lookup filter */
		$o_forestLookupData = new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID, '!2ForestDataUUID' => $s_forestLookupUUID));
		$this->Twig->Unique->SetLookupData($o_forestLookupData);
		$this->Twig->SortOrder->SetLookupData($o_forestLookupData);
		$this->Twig->View->SetLookupData(new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID)));
		$this->Twig->SortColumn->SetLookupData($o_forestLookupData);
		
		/* create modal read only form */
		$o_glob->PostModalForm = new forestForm($this->Twig, true, true);
		$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->URL->BranchTitle . ' Twig';
		
		/* delete Unique-element */
		if (!$o_glob->PostModalForm->DeleteFormElementByFormId('readonly_sys_fphp_table_Unique[]')) {
			throw new forestException('Cannot delete form element with Id[sys_fphp_table_Unique[]].');
		}
		
		/* delete SortOrder-element */
		if (!$o_glob->PostModalForm->DeleteFormElementByFormId('readonly_sys_fphp_table_SortOrder')) {
			throw new forestException('Cannot delete form element with Id[sys_fphp_table_SortOrder].');
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction('init');
	}
	
	/* truncate execution of a twig */
	protected function executeTruncateTwig(tableTwig $p_o_table) {
		$o_glob = forestGlobals::init();
		
		/* get table twig object */
		$s_table = $p_o_table->Name;
		forestStringLib::RemoveTablePrefix($s_table);
		$s_foo = $s_table . 'Twig';
		$o_twig = new $s_foo;
		
		/* query all records */
		$o_records = $o_twig->GetAllRecords(true);
		
		foreach ($o_records->Twigs as $o_record) {
			/* check record relations before deletion */
			$this->CheckandCleanupRecordBeforeDeletion($o_record);
			
			/* delete record */
			$this->executeDeleteRecord($o_record);
		}
	}
	
	/* handle delete twig record action */
	protected function deleteTwigAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new tableTwig;
			
		/* query table record */
		if (! ($this->Twig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($this->Twig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			/* create modal confirmation form for deleting record */
			$o_glob->PostModalForm = new forestForm($this->Twig);
			$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
			$s_description = '<div class="alert alert-danger">' . $o_glob->GetTranslation('DeleteModalDescription', 1) . '</div>';
			$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);
		} else {
			$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
			
			/* check sub constraint records, if twig is join part of sub constraint */
			$o_subconstraintTwig = new subconstraintTwig;
			
			$a_sqlAdditionalFilter = array(array('column' => 'SubTableUUID', 'value' => $this->Twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$i_subconstraints = $o_subconstraintTwig->GetCount(null, true, false);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			if ($i_subconstraints > 0) {
				throw new forestException(0x10001F0B);
			}
			
			/* execute truncation of twig */
			$this->executeTruncateTwig($this->Twig);
			
			/* delete sub constraint records */
			$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $this->Twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_subconstraints = $o_subconstraintTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			foreach ($o_subconstraints->Twigs as $o_subconstraint) {
				/* delete tablefield records */
				$o_tablefieldTwig = new tablefieldTwig;
				
				$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $o_subconstraint->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				
				foreach ($o_tablefields->Twigs as $o_tablefield) {
					/* delete tablefield validationrule records */
					$o_tablefield_validationruleTwig = new tablefield_validationruleTwig;
					
					$a_sqlAdditionalFilter = array(array('column' => 'TablefieldUUID', 'value' => $o_tablefield->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
					$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
					$o_tablefield_validationrules = $o_tablefield_validationruleTwig->GetAllRecords(true);
					$o_glob->Temp->Del('SQLAdditionalFilter');
					
					foreach ($o_tablefield_validationrules->Twigs as $o_tablefield_validationrule) {
						/* delete tablefield validationrule record */
						$i_return = $o_tablefield_validationrule->DeleteRecord();
					
						/* evaluate the result */
						if ($i_return <= 0) {
							throw new forestException(0x10001423);
						}
					}
					
					/* delete translation records */
					preg_match_all('/\#([^#]+)\#/', $o_tablefield->JSONEncodedSettings, $a_matches);
			
					if (count($a_matches) > 1) {
						$o_translationTwig = new translationTwig;
						
						foreach ($a_matches[1] as $s_match) {
							if ($o_translationTwig->GetRecordPrimary(array($o_glob->URL->BranchId, $s_match), array('BranchId', 'Name'))) {
								/* delete translation record */
								$i_return = $o_translationTwig->DeleteRecord();
							
								/* evaluate the result */
								if ($i_return <= 0) {
									throw new forestException(0x10001423);
								}
							}
						}
					}
					
					/* delete tablefield record */
					$i_return = $o_tablefield->DeleteRecord();
				
					/* evaluate the result */
					if ($i_return <= 0) {
						throw new forestException(0x10001423);
					}
				}
				
				/* delete sub constraint record */
				$i_return = $o_subconstraint->DeleteRecord();
			
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
			}
			
			/* delete tablefield records */
			$o_tablefieldTwig = new tablefieldTwig;
			
			$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $this->Twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			foreach ($o_tablefields->Twigs as $o_tablefield) {
				/* delete tablefield validationrule records */
				$o_tablefield_validationruleTwig = new tablefield_validationruleTwig;
				
				$a_sqlAdditionalFilter = array(array('column' => 'TablefieldUUID', 'value' => $o_tablefield->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$o_tablefield_validationrules = $o_tablefield_validationruleTwig->GetAllRecords(true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				
				foreach ($o_tablefield_validationrules->Twigs as $o_tablefield_validationrule) {
					/* delete tablefield validationrule record */
					$i_return = $o_tablefield_validationrule->DeleteRecord();
				
					/* evaluate the result */
					if ($i_return <= 0) {
						throw new forestException(0x10001423);
					}
				}
				
				/* delete translation records */
				preg_match_all('/\#([^#]+)\#/', $o_tablefield->JSONEncodedSettings, $a_matches);
		
				if (count($a_matches) > 1) {
					$o_translationTwig = new translationTwig;
					
					foreach ($a_matches[1] as $s_match) {
						if ($o_translationTwig->GetRecordPrimary(array($o_glob->URL->BranchId, $s_match), array('BranchId', 'Name'))) {
							/* delete translation record */
							$i_return = $o_translationTwig->DeleteRecord();
						
							/* evaluate the result */
							if ($i_return <= 0) {
								throw new forestException(0x10001423);
							}
						}
					}
				}
				
				/* delete tablefield record */
				$i_return = $o_tablefield->DeleteRecord();
			
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
			}
			
			/* disconnect twig from branch */
			$o_branchTwig = new branchTwig;
			
			/* query branch record */
			if (! ($o_branchTwig->GetRecord(array($o_glob->URL->BranchId))) ) {
				throw new forestException(0x10001401, array($o_branchTwig->fphp_Table));
			}
			
			/* disconnect table connection */
			$o_branchTwig->Table = 'NULL';
			
			/* edit branch record */
			$i_result = $o_branchTwig->UpdateRecord();
			
			/* exchange branch file with new branch file + landing page */
			$s_path = '';
			
			if (count($o_glob->URL->Branches) > 0) {
				foreach($o_glob->URL->Branches as $s_value) {
					$s_path .= $s_value . '/';
				}
			}
			
			/* get directory content of current page into array */
			$a_dirContent = scandir('./trunk/' . $s_path);
			$s_path = './trunk/' . $s_path . $o_branchTwig->Name . '/';
			
			/* if we cannot find folder */
			if (!in_array($o_branchTwig->Name, $a_dirContent)) {
				throw new forestException('Cannot find directory [%0].', array($s_path));
			}
			
			$a_dirContent = scandir($s_path);
			$s_path = $s_path . $o_branchTwig->Name . 'Branch.php';
			
			/* if we cannot find branch file */
			if (!in_array($o_branchTwig->Name . 'Branch.php', $a_dirContent)) {
				throw new forestException('Cannot find file [%0].', array($o_branchTwig->Name . 'Branch.php'));
			}
			
			/* if we cannot delete branch file */
			if (!(@unlink($s_path))) {
				throw new forestException(0x10001422, array($s_path));
			}
			
			$o_file = new forestFile($s_path, true);
			$o_file->ReplaceContent( strval(new forestTemplates(forestTemplates::CREATENEWBRANCH, array($o_branchTwig->Name))) );
			
			/* delete twig file */
			/* get twigs directory content */
			$a_dirContent = scandir('./twigs/');
			$s_tempName = $this->Twig->Name;
			forestStringLib::RemoveTablePrefix($s_tempName);
			$s_tempName .= 'Twig.php';
			
			/* if we can find twig file, delete it */
			if (in_array($s_tempName, $a_dirContent)) {
				if (!(@unlink('./twigs/' . $s_tempName))) {
					throw new forestException(0x10001422, array('./twigs/' . $s_tempName));
				}
			}
			
			/* delete table in dbms */
			$o_queryDrop = new forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, forestSQLQuery::DROP, $this->Twig->Name);

			/* drop table does not return a value - maybe using show_tables can be used as extra verification */
			$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryDrop, false, false);
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				$o_glob->SystemMessages->Add(new forestException(0x10001406));
			} else if ($i_result == 1) {
				/* delete twig record */
				$i_return = $this->Twig->DeleteRecord();
				
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
				
				$o_glob->SystemMessages->Add(new forestException(0x10001427));
				
				$s_nextAction = 'RELOADBRANCH';
			}
		}
		
		if (isset($this->KeepFilter)) {
			if ($this->KeepFilter->value) {
				if ($o_glob->Security->SessionData->Exists('last_filter')) {
					$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'last_filter'}, 'filter');
				}
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/* function to update twig file of current branch with current tablefields and twig settings */
	protected function doTwigFile(tableTwig $p_o_tableTwig) {
		$o_glob = forestGlobals::init();
		
		/* gather information */
		$s_tableName = '';
		$s_fieldDefinitions = '';
		$s_fields = '';
		$s_fullTableName = $p_o_tableTwig->Name;
		$s_primary = '';
		$s_uniques = '';
		$s_sorts = '';
		$s_interval = '';
		$s_view = '';
		$s_view_reserve = '';
		
		/* standard Id + UUID */
		$s_primary .= '\'Id\'';
		$s_uniques .= '\'UUID\',';
		$s_sorts = '$this->fphp_SortOrder->value->Add(true, \'Id\');' . "\n\t\t";
		$s_fieldDefinitions .= 'private $Id;' . "\n\t" . 'private $UUID;' . "\n\t";
		$s_fields .= '$this->Id = new forestNumericString(1);' . "\n\t\t" . '$this->UUID = new forestString;' . "\n\t\t";
		
		/* look for tablefields */
		$o_tablefieldTwig = new tablefieldTwig;
		
		$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $p_o_tableTwig->UUID, 'operator' => '=', 'filterOperator' => 'AND'), array('column' => 'SqlTypeUUID', 'value' => 'NULL', 'operator' => 'IS NOT', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		foreach ($o_tablefields->Twigs as $o_tablefield) {
			/* ignore forestCombination, dropzone and form field */
			if ((strval($o_tablefield->ForestDataUUID) == 'forestCombination') || (strval($o_tablefield->FormElementUUID) == forestFormElement::DROPZONE) || (strval($o_tablefield->FormElementUUID) == forestFormElement::FORM)) {
				continue;
			}
			
			$s_fieldDefinitions .= 'private $' . $o_tablefield->FieldName . ';' . "\n\t";
			
			if ((strval($o_tablefield->ForestDataUUID) == 'forestLookup')) {
				$s_fields .= '$this->' . $o_tablefield->FieldName . ' = new ';
				
				/* get json encoded settings as array */
				$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_tablefield->JSONEncodedSettings);
				$a_settings = json_decode($s_JSONEncodedSettings, true);
				
				$s_lookupTable = '\'sys_fphp_trunk\'';
				$s_lookupPrimary = '\'Id\'';
				$s_lookupLabel = '\'Id\'';
				$s_lookupFilter = 'array()';
				$s_lookupConcat = '\' - \'';
				
				/* check if json encoded settings are valid */
				if ($a_settings != null) {
					if ( (array_key_exists('forestLookupDataTable', $a_settings)) && (array_key_exists('forestLookupDataPrimary', $a_settings)) && (array_key_exists('forestLookupDataLabel', $a_settings)) ) {
						$s_lookupTable = '\'' . $a_settings['forestLookupDataTable'] . '\'';
						$s_lookupPrimary = '\'' . implode('\',\'', $a_settings['forestLookupDataPrimary']) . '\'';
						$s_lookupLabel = '\'' . implode('\',\'', $a_settings['forestLookupDataLabel']) . '\'';
						
						if (array_key_exists('forestLookupDataFilter', $a_settings)) {
							$a_filters = array();
							
							foreach ($a_settings['forestLookupDataFilter'] as $s_filterKey => $s_filterValue) {
								$a_filters[] = '\'' . $s_filterKey . '\' => \'' . $s_filterValue . '\'';
							}
							
							$s_lookupFilter = 'array(' . implode(',', $a_filters) . ')';
						}
						
						if (array_key_exists('forestLookupDataConcat', $a_settings)) {
							$s_lookupConcat = '\'' . $a_settings['forestLookupDataConcat'] . '\'';
						}
					}
				}
				
				$s_fields .= 'forestLookup(new forestLookupData(' . $s_lookupTable . ', array(' . $s_lookupPrimary . '), array(' . $s_lookupLabel . '), ' . $s_lookupFilter . ', ' . $s_lookupConcat . '))';
				$s_fields .= ';' . "\n\t\t";
			} else if ((strval($o_tablefield->ForestDataUUID) == 'forestList')) {
				$s_fields .= '$this->' . $o_tablefield->FieldName . ' = new ';
				
				/* get json encoded settings as array */
				$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_tablefield->JSONEncodedSettings);
				$a_settings = json_decode($s_JSONEncodedSettings, true);
				
				$s_options = 'array()';
				
				/* check if json encoded settings are valid */
				if ($a_settings != null) {
					if (array_key_exists('Options', $a_settings)) {
						if (array_key_exists('Value', $a_settings)) {
							$s_options = 'array(\'' . implode('\',\'', $a_settings['Options']) . '\'), \'' . $a_settings['Value'] . '\'';
						} else {
							$s_options = 'array(\'' . implode('\',\'', $a_settings['Options']) . '\')';
						}
					}
				}
				
				$s_fields .= 'forestList(' . $s_options . ')';
				$s_fields .= ';' . "\n\t\t";
			} else {
				$s_fields .= '$this->' . $o_tablefield->FieldName . ' = new ' . htmlspecialchars_decode(strval($o_tablefield->ForestDataUUID), ( ENT_QUOTES | ENT_HTML5 )) . ';' . "\n\t\t";
			}
			
			$s_view_reserve .= '\'' . $o_tablefield->FieldName . '\',';
		}
		
		/* unique keys */
		$a_uniqueKeys = array();
		
		if (issetStr($p_o_tableTwig->Unique->PrimaryValue)) {
			$a_uniqueKeys = explode(':', $p_o_tableTwig->Unique->PrimaryValue);
		}
		
		foreach ($a_uniqueKeys as $o_uniqueKey) {
			/* split name from uuid keys */
			$a_unique = explode('=', $o_uniqueKey);
			$s_name = $a_unique[0];
			
			$a_keys = array();
			$a_foo = explode(';', $a_unique[1]);
			
			foreach ($a_foo as $s_foo) {
				/* query tablefield to get FieldName for display */
				if ($o_tablefieldTwig->GetRecord(array($s_foo))) {
					$a_keys[] = $o_tablefieldTwig->FieldName;
				} else {
					$a_keys[] = 'invalid_key';
				}
			}
			
			$s_uniques .= '\'' .  implode(';', $a_keys) . '\'' . ',';
		}
		
		$s_uniques = substr($s_uniques, 0, -1);
		
		/* sort orders */
		$a_sortOrders = array();
		
		if (issetStr($p_o_tableTwig->SortOrder->PrimaryValue)) {
			$s_sorts = '';
			$a_sortOrders = explode(':', $p_o_tableTwig->SortOrder->PrimaryValue);
		}
		
		foreach ($a_sortOrders as $o_sortOrder) {
			/* render sort orders based on twig sort order column */
			$a_sort = explode(';', $o_sortOrder);
			
			if (count($a_sort) != 2) {
				continue;
			}
			
			$s_name = '';
			$s_direction = '';
			
			/* query tablefield to get FieldName for display */
			if ($o_tablefieldTwig->GetRecord(array($a_sort[1]))) {
				$s_name = $o_tablefieldTwig->FieldName;
			} else {
				$s_name = 'invalid_column';
			}
			
			if ($a_sort[0] == 'false') {
				$s_direction = 'false';
			} else if ($a_sort[0] == 'true') {
				$s_direction = 'true';
			}
			
			$s_sorts .= '$this->fphp_SortOrder->value->Add(' . $s_direction . ', \'' . $s_name . '\');' . "\n\t\t";
		}
		
		/* interval */
		if ($p_o_tableTwig->Interval != 0) {
			$s_interval = strval($p_o_tableTwig->Interval);
		} else {
			$s_interval = strval(50);
		}
		
		/* view */
		if (issetStr($p_o_tableTwig->View->PrimaryValue)) {
			$a_keys = array();
			$a_foo = explode(';', $p_o_tableTwig->View->PrimaryValue);
			
			foreach ($a_foo as $s_foo) {
				/* query tablefield to get FieldName for display */
				if ($o_tablefieldTwig->GetRecord(array($s_foo))) {
					$a_keys[] = $o_tablefieldTwig->FieldName;
				} else {
					$a_keys[] = 'invalid_key';
				}
			}
			
			$s_view .=  '\'' . implode('\',\'', $a_keys) . '\'';
		} else {
			$s_view .= substr($s_view_reserve, 0, -1);
		}
		
		/* get twigs directory content */
		$a_dirContent = scandir('./twigs/');
		$s_tempName = $p_o_tableTwig->Name;
		forestStringLib::RemoveTablePrefix($s_tempName);
		$s_tableName = $s_tempName;
		$s_tempName .= 'Twig.php';
		
		/* if we can find twig file, delete it */
		if (in_array($s_tempName, $a_dirContent)) {
			if (!(@unlink('./twigs/' . $s_tempName))) {
				throw new forestException(0x10001422, array('./twigs/' . $s_tempName));
			}
		}
		
		/* create new twig file */
		$o_file = new forestFile('./twigs/' . $s_tempName, true);
		$o_file->ReplaceContent( strval(new forestTemplates(forestTemplates::CREATENEWTWIG, array($s_tableName, $s_fieldDefinitions, $s_fields, $s_fullTableName, $s_primary, $s_uniques, $s_sorts, $s_interval, $s_view))) );
	}
	

	/* check twigfield relation to other elements */
	protected function checkTwigFieldBeforeDeletion(tableTwig $p_o_table, tablefieldTwig $p_o_tablefield) {
		$o_glob = forestGlobals::init();
		
		$o_forestdataTwig = new forestdataTwig;
		
		if (! ($o_forestdataTwig->GetRecordPrimary(array('forestCombination'), array('Name'))) ) {
			throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
		}
		
		$s_forestCombinationUUID = $o_forestdataTwig->UUID;
		
		if (! ($o_forestdataTwig->GetRecordPrimary(array('forestLookup'), array('Name'))) ) {
			throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
		}
		
		$s_forestLookupUUID = $o_forestdataTwig->UUID;
		
		/* look for forestLookup tablefields */
		$o_tablefieldTwig = new tablefieldTwig;
		
		$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $p_o_table->UUID, 'operator' => '<>', 'filterOperator' => 'AND'), array('column' => 'ForestDataUUID', 'value' => $s_forestLookupUUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		foreach ($o_tablefields->Twigs as $o_tablefield) {
			$s_table = strval($o_tablefield->TableUUID);
			
			if ($s_table == 'record_not_found_with_primary') {
				$s_table = 'SubRecords';
			}
			
			/* get json encoded settings as array */
			$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_tablefield->JSONEncodedSettings);
			$a_settings = json_decode($s_JSONEncodedSettings, true);
			
			/* check if json encoded settings are valid */
			if ($a_settings == null) {
				throw new forestException(0x10001F02, array($s_JSONEncodedSettings));
			}
			
			if (array_key_exists('forestLookupDataTable', $a_settings)) {
				if ($a_settings['forestLookupDataTable'] == $p_o_table->Name) {
					if (array_key_exists('forestLookupDataPrimary', $a_settings)) {
						if (in_array($p_o_tablefield->FieldName, $a_settings['forestLookupDataPrimary'])) {
							throw new forestException(0x10001F07, array($p_o_tablefield->FieldName, $o_tablefield->FieldName, $s_table));
						}
					}
					
					if (array_key_exists('forestLookupDataLabel', $a_settings)) {
						if (in_array($p_o_tablefield->FieldName, $a_settings['forestLookupDataLabel'])) {
							throw new forestException(0x10001F07, array($p_o_tablefield->FieldName, $o_tablefield->FieldName, $s_table));
						}
					}
					
					if (array_key_exists('forestLookupDataFilter', $a_settings)) {
						if (in_array($p_o_tablefield->FieldName, $a_settings['forestLookupDataFilter'])) {
							throw new forestException(0x10001F07, array($p_o_tablefield->FieldName, $o_tablefield->FieldName, $s_table));
						}
					}
				}
			}
		}
		
		/* look for forestCombination tablefields */
		$o_tablefieldTwig = new tablefieldTwig;
		
		$a_sqlAdditionalFilter = array(array('column' => 'ForestDataUUID', 'value' => $s_forestCombinationUUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		$s_table = strval($p_o_tablefield->TableUUID);
		
		if ($s_table == 'record_not_found_with_primary') {
			/* get real table with sub constraint relation */
			$o_subconstraintTwig = new subconstraintTwig;
			
			if ($o_subconstraintTwig->GetRecord(array($p_o_tablefield->TableUUID->PrimaryValue))) {
				$s_table = strval($o_subconstraintTwig->SubTableUUID);
			}
		}
		
		foreach ($o_tablefields->Twigs as $o_tablefield) {
			/* get json encoded settings as array */
			$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_tablefield->JSONEncodedSettings);
			$a_settings = json_decode($s_JSONEncodedSettings, true);
			
			/* check if json encoded settings are valid */
			if ($a_settings == null) {
				throw new forestException(0x10001F02, array($s_JSONEncodedSettings));
			}
			
			if (array_key_exists('forestCombination', $a_settings)) {
				if ( (strpos($a_settings['forestCombination'], $p_o_tablefield->FieldName) !== false) && ($p_o_tablefield->TableUUID->PrimaryValue == $o_tablefield->TableUUID->PrimaryValue) ) {
					/* tablefield is used in forestCombination of the same table or subrecord constellation */
					throw new forestException(0x10001F08, array($p_o_tablefield->FieldName, $o_tablefield->FieldName, ((strval($o_tablefield->TableUUID) == 'record_not_found_with_primary') ? 'SubRecords' : strval($o_tablefield->TableUUID)) ));
				} else if ( (strpos($a_settings['forestCombination'], $s_table . '$' . $p_o_tablefield->FieldName) !== false) && ($o_tablefield->TableUUID->PrimaryValue == $p_o_table->UUID) ) {
					/* tablefield is used in forestCombination of higher table(parameter table) */
					throw new forestException(0x10001F08, array($p_o_tablefield->FieldName, $o_tablefield->FieldName, strval($p_o_table->Name)));
				}
			}
		}
	}
	
	/* check twigfield relation to other elements */
	protected function cleanupTwigFieldAfterDeletion(tableTwig &$p_o_table, tablefieldTwig $p_o_tablefield) {
		$o_glob = forestGlobals::init();
		$b_table_changed = false;
		
		/* cleanup unique keys */
		if (issetStr($p_o_table->Unique->PrimaryValue)) {
			$a_uniqueKeys = explode(':', $p_o_table->Unique->PrimaryValue);
			$i_deleteKey = null;
			
			foreach($a_uniqueKeys as $i_key => $o_uniqueKey) {
				$a_uniqueKey = explode('=', $o_uniqueKey);
				
				$a_keys = explode(';', $a_uniqueKey[1]);
				
				if (in_array($p_o_tablefield->UUID, $a_keys)) {
					$b_table_changed = true;
					$i_deleteKey = $i_key;
				}
			}
			
			if ($i_deleteKey !== null) {
				unset($a_uniqueKeys[$i_deleteKey]);
				
				if (count($a_uniqueKeys) > 0) {
					$p_o_table->Unique = implode(':', $a_uniqueKeys);
				} else {
					$p_o_table->Unique = 'NULL';
				}
			}
		}
		
		/* cleanup sort orders */
		if (issetStr($p_o_table->SortOrder->PrimaryValue)) {
			$a_sortOrders = explode(':', $p_o_table->SortOrder->PrimaryValue);
			$i_deleteKey = null;
			
			foreach($a_sortOrders as $i_key => $o_sortOrder) {
				$a_sort = explode(';', $o_sortOrder);
				
				if (count($a_sort) != 2) {
					continue;
				}
				
				if ($p_o_tablefield->UUID == $a_sort[1]) {
					$b_table_changed = true;
					$i_deleteKey = $i_key;
				}
			}
			
			if ($i_deleteKey !== null) {
				unset($a_sortOrders[$i_deleteKey]);
				
				if (count($a_sortOrders) > 0) {
					$p_o_table->SortOrder = implode(':', $a_sortOrders);
				} else {
					$p_o_table->SortOrder = 'NULL';
				}
			}
		}
		
		/* cleanup view */
		if (issetStr($p_o_table->View->PrimaryValue)) {
			$a_view = explode(';', $p_o_table->View->PrimaryValue);
			$i_deleteKey = null;
			
			foreach($a_view as $i_key => $s_view) {
				if ($p_o_tablefield->UUID == $s_view) {
					$b_table_changed = true;
					$i_deleteKey = $i_key;
				}
			}
			
			if ($i_deleteKey !== null) {
				unset($a_view[$i_deleteKey]);
				
				if (count($a_view) > 0) {
					$p_o_table->View = implode(';', $a_view);
				} else {
					$p_o_table->View = 'NULL';
				}
			}
		}
		
		/* cleanup sort column */
		if (issetStr($p_o_table->SortColumn->PrimaryValue)) {
			if ($p_o_tablefield->UUID == $p_o_table->SortColumn->PrimaryValue) {
				$p_o_table->SortColumn = 'NULL';
				$b_table_changed = true;
			}
		}
		
		/* change twig record if flag has been set */
		if ($b_table_changed) {
			$p_o_table->UpdateRecord();
		}
		
		/* cleanup sub constraints */
		$o_subconstraintTwig = new subconstraintTwig;
		
		$a_sqlAdditionalFilter = array(array('column' => 'SubTableUUID', 'value' => $p_o_table->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_subconstraints = $o_subconstraintTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		foreach ($o_subconstraints->Twigs as $o_subconstraint) {
			/* cleanup sub constraint view */
			if (issetStr($o_subconstraint->View->PrimaryValue)) {
				$a_view = explode(';', $o_subconstraint->View->PrimaryValue);
				$i_deleteKey = null;
				
				foreach($a_view as $i_key => $s_view) {
					if ($p_o_tablefield->UUID == $s_view) {
						$i_deleteKey = $i_key;
					}
				}
				
				if ($i_deleteKey !== null) {
					unset($a_view[$i_deleteKey]);
					
					if (count($a_view) > 0) {
						$o_subconstraint->View = implode(';', $a_view);
					} else {
						$o_subconstraint->View = 'NULL';
					}
					
					$o_subconstraint->UpdateRecord();
				}
			}
		}
	}


	/* check twigfield relation to other elements */
	protected function checkSubConstraintBeforeDeletion(tableTwig $p_o_table, subconstraintTwig $p_o_subconstraint) {
		$o_glob = forestGlobals::init();
		
		$o_forestdataTwig = new forestdataTwig;
		
		if (! ($o_forestdataTwig->GetRecordPrimary(array('forestCombination'), array('Name'))) ) {
			throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
		}
		
		$s_forestCombinationUUID = $o_forestdataTwig->UUID;
		
		/* look for forestCombination tablefields */
		$o_tablefieldTwig = new tablefieldTwig;
		
		$a_sqlAdditionalFilter = array(array('column' => 'ForestDataUUID', 'value' => $s_forestCombinationUUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		$s_table = strval($p_o_subconstraint->SubTableUUID);
		
		foreach ($o_tablefields->Twigs as $o_tablefield) {
			/* get json encoded settings as array */
			$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_tablefield->JSONEncodedSettings);
			$a_settings = json_decode($s_JSONEncodedSettings, true);
			
			/* check if json encoded settings are valid */
			if ($a_settings == null) {
				throw new forestException(0x10001F02, array($s_JSONEncodedSettings));
			}
			
			if (array_key_exists('forestCombination', $a_settings)) {
				if ( (strpos($a_settings['forestCombination'], $s_table . '$') !== false) && ($o_tablefield->TableUUID->PrimaryValue == $p_o_table->UUID) ) {
					/* tablefield is used in forestCombination of higher table(parameter table) */
					throw new forestException(0x10001F09, array($s_table, $o_tablefield->FieldName, strval($p_o_table->Name)));
				}
			}
		}
	}
}
?>