<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.4.0 (0x1 0001F)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * adminisration class for handling all adminstrative use cases for forestBranch and forestTwig objects
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.2.0 beta	renatus		2019-10-18	added to framework
 * 0.3.0 beta	renatus		2019-10-29	added create, modify and delete for tablefields
 * 0.3.0 beta	renatus		2019-10-30	added twig properties, sort, unique and translation
 * 0.3.0 beta	renatus		2019-10-30	added sub-constraints to twig properties
 * 0.3.0 beta	renatus		2019-11-01	added sub-constraints tablefields
 * 0.3.0 beta	renatus		2019-11-02	added validation rules to tablefields
 * 0.3.0 beta	renatus		2019-11-04	added moveUp and moveDown for tablefields and sub-constraints
 * 0.4.0 beta	renatus		2019-11-20	added truncateTwig and transferTwig functions
 * 0.4.0 beta	renatus		2019-11-21	added permission checks for standard root actions
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
		
		if ($o_glob->Security->CheckUserPermission(null, 'rootMenu')) {
			$s_rootMenu .= '<button class="btn btn-danger dropdown-toggle" type="button" data-toggle="dropdown" title="Root-Menu" style="margin-top: 8px;"><span class="glyphicon glyphicon glyphicon-wrench"></span></button>' . "\n";
			$s_rootMenu .= '<ul class="dropdown-menu">' . "\n";
				
				if ($o_glob->Security->CheckUserPermission(null, 'newBranch')) {
					$s_rootMenu .= '<li><a href="' . forestLink::Link($o_glob->URL->Branch, 'newBranch') . '"><span class="glyphicon glyphicon-plus text-success" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootCreateBranchTitle', 1) . '</a></li>' . "\n";
				}
				
				if ($o_glob->Security->CheckUserPermission(null, 'viewBranch')) {
					$s_rootMenu .= '<li><a href="' . forestLink::Link($o_glob->URL->Branch, 'viewBranch') . '"><span class="glyphicon glyphicon-zoom-in" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootViewBranchTitle', 1) . '</a></li>' . "\n";
				}
				
				$s_rootMenu .= '<li class="dropdown-submenu">' . "\n";
					
					if ($o_glob->Security->CheckUserPermission(null, 'editBranch')) {
						$s_rootMenu .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'editBranch') . '"><span class="glyphicon glyphicon-pencil" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootEditBranchTitle', 1) . '</a><a class="fphp_menu_dropdown" href="#"><span class="glyphicon glyphicon-menu-down"></span></a>' . "\n";
					}
					
					$s_rootMenu .= '<ul class="dropdown-menu">' . "\n";
						if (!issetStr($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue)) {
							if ($o_glob->Security->CheckUserPermission(null, 'newTwig')) {
								$s_rootMenu .= '<li><a href="' . forestLink::Link($o_glob->URL->Branch, 'newTwig') . '"><span class="glyphicon glyphicon-plus text-success" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootCreateTwigTitle', 1) . '</a></li>' . "\n";
							}
							
							if ($o_glob->Security->CheckUserPermission(null, 'transferTwig')) {
								$s_rootMenu .= '<li><a href="' . forestLink::Link($o_glob->URL->Branch, 'transferTwig') . '"><span class="glyphicon glyphicon-transfer" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootTransferTwigTitle', 1) . '</a></li>' . "\n";
							}
						} else {
							if ($o_glob->Security->CheckUserPermission(null, 'viewTwig')) {
								$s_rootMenu .= '<li><a href="' . forestLink::Link($o_glob->URL->Branch, 'viewTwig') . '"><span class="glyphicon glyphicon-zoom-in" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootViewTwigTitle', 1) . '</a></li>' . "\n";
							}
							
							if ($o_glob->Security->CheckUserPermission(null, 'editTwig')) {
								$s_rootMenu .= '<li><a href="' . forestLink::Link($o_glob->URL->Branch, 'editTwig') . '"><span class="glyphicon glyphicon-pencil" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootEditTwigTitle', 1) . '</a></li>' . "\n";
							}
							
							if ($o_glob->Security->CheckUserPermission(null, 'truncateTwig')) {
								$s_rootMenu .= '<li><a href="' . forestLink::Link($o_glob->URL->Branch, 'truncateTwig') . '"><span class="glyphicon glyphicon-erase text-primary" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootTruncateTwigTitle', 1) . '</a></li>' . "\n";
							}
							
							if ($o_glob->Security->CheckUserPermission(null, 'transferTwig')) {
								$s_rootMenu .= '<li><a href="' . forestLink::Link($o_glob->URL->Branch, 'transferTwig') . '"><span class="glyphicon glyphicon-transfer" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootTransferTwigTitle', 1) . '</a></li>' . "\n";
							}
							
							if ($o_glob->Security->CheckUserPermission(null, 'deleteTwig')) {
								$s_rootMenu .= '<li><a href="' . forestLink::Link($o_glob->URL->Branch, 'deleteTwig') . '"><span class="glyphicon glyphicon-trash text-danger" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootDeleteTwigTitle', 1) . '</a></li>' . "\n";
							}
						}
					$s_rootMenu .= '</ul>' . "\n";
				$s_rootMenu .= '</li>' . "\n";
				
				if ($o_glob->Security->CheckUserPermission(null, 'moveUpBranch')) {
					$s_rootMenu .= '<li><a href="' . forestLink::Link($o_glob->URL->Branch, 'moveUpBranch', array('editKey' => $o_glob->URL->BranchId)) . '"><span class="glyphicon glyphicon-triangle-top" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootMoveUpBranchTitle', 1) . '</a></li>' . "\n";
				}
				
				if ($o_glob->Security->CheckUserPermission(null, 'moveDownBranch')) {
					$s_rootMenu .= '<li><a href="' . forestLink::Link($o_glob->URL->Branch, 'moveDownBranch', array('editKey' => $o_glob->URL->BranchId)) . '"><span class="glyphicon glyphicon-triangle-bottom" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootMoveDownBranchTitle', 1) . '</a></li>' . "\n";
				}
				
				if ($o_glob->Security->CheckUserPermission(null, 'deleteBranch')) {
					$s_rootMenu .= '<li><a href="' . forestLink::Link($o_glob->URL->Branch, 'deleteBranch') . '"><span class="glyphicon glyphicon-trash text-danger" style="margin-right: 5px"></span> ' . $o_glob->GetTranslation('rootDeleteBranchTitle', 1) . '</a></li>' . "\n";
				}
				
			$s_rootMenu .= '</ul>' . "\n";
		}
		
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
					} else if ($i_result == 1) {
						/* create permission for standard action of branch */
						$o_permissionTwig = new permissionTwig;
						$o_permissionTwig->Name = $s_actionLabel;
						$o_permissionTwig->Branch->PrimaryValue = strval($this->Twig->Id);
						$o_permissionTwig->Action->PrimaryValue = strval($o_actionTwig->Id);
						
						/* insert permission record */
						$i_result = $o_permissionTwig->InsertRecord();
						
						/* evaluate result */
						if ($i_result == -1) {
							throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
						} else if ($i_result == 0) {
							throw new forestException(0x10001402);
						}
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
						/* delete permission records linked to action record */
						$o_permissionTwig = new permissionTwig;
			
						$a_sqlAdditionalFilter = array(array('column' => 'Action', 'value' => $o_action->Id, 'operator' => '=', 'filterOperator' => 'AND'));
						$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
						$o_permissions = $o_permissionTwig->GetAllRecords(true);
						$o_glob->Temp->Del('SQLAdditionalFilter');
						
						foreach ($o_permissions->Twigs as $o_permission) {
							/* delete role_permission records linked to permission */
							$o_role_permissionTwig = new role_permissionTwig;
			
							$a_sqlAdditionalFilter = array(array('column' => 'permissionUUID', 'value' => $o_permission->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
							$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
							$o_role_permissions = $o_role_permissionTwig->GetAllRecords(true);
							$o_glob->Temp->Del('SQLAdditionalFilter');
							
							foreach ($o_role_permissions->Twigs as $o_role_permission) {
								/* delete record */
								$i_return = $o_role_permission->DeleteRecord();
								
								/* evaluate the result */
								if ($i_return <= 0) {
									throw new forestException(0x10001423);
								}
							}
							
							/* delete record */
							$i_return = $o_permission->DeleteRecord();
							
							/* evaluate the result */
							if ($i_return <= 0) {
								throw new forestException(0x10001423);
							}
						}
						
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
				
				/* delete permission records linked to this action */
				$o_permissionTwig = new permissionTwig;
	
				$a_sqlAdditionalFilter = array(array('column' => 'Action', 'value' => $this->Twig->Id, 'operator' => '=', 'filterOperator' => 'AND'));
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$o_permissions = $o_permissionTwig->GetAllRecords(true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				
				foreach ($o_permissions->Twigs as $o_permission) {
					/* delete role_permission records linked to permission */
					$o_role_permissionTwig = new role_permissionTwig;
	
					$a_sqlAdditionalFilter = array(array('column' => 'permissionUUID', 'value' => $o_permission->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
					$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
					$o_role_permissions = $o_role_permissionTwig->GetAllRecords(true);
					$o_glob->Temp->Del('SQLAdditionalFilter');
					
					foreach ($o_role_permissions->Twigs as $o_role_permission) {
						/* delete record */
						$i_return = $o_role_permission->DeleteRecord();
						
						/* evaluate the result */
						if ($i_return <= 0) {
							throw new forestException(0x10001423);
						}
					}
					
					/* delete record */
					$i_return = $o_permission->DeleteRecord();
					
					/* evaluate the result */
					if ($i_return <= 0) {
						throw new forestException(0x10001423);
					}
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
		
		/* add tablefields, translations, unique keys, sort orders, sub constraints and sub constraint tablefields for modal form */
		$o_glob->PostModalForm->FormModalSubForm = strval($this->ListTwigProperties($this->Twig));
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction('init');
	}
	
	/* handle transfer twig recorrd action */
	protected function transferTwigAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ( ($this->Twig != null) && ($this->Twig->fphp_SystemTable) ) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new branchTwig;
		$o_table = new tableTwig;
		
		if ($o_glob->URL->BranchId == 1) {
			throw new forestException(0x10001F05);
		}
		
		/* query branch record */
		if (! ($this->Twig->GetRecord(array($o_glob->URL->BranchId))) ) {
			throw new forestException(0x10001401, array($this->Twig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			/* create modal confirmation form for deleting record */
			$o_glob->PostModalForm = new forestForm($this->Twig);
			$s_title = $o_glob->GetTranslation('TransferTwigModalTitle', 1);
			$o_glob->PostModalForm->CreateModalForm($this->Twig, $s_title);
			
			$o_branchTwig = new branchTwig;
			$a_sqlAdditionalFilter = array();
			
			$s_label = $o_glob->GetTranslation('formTransferTwigFromLabel', 0);
			$s_valMessage = $o_glob->GetTranslation('formTransferTwigFromValMessage', 0);
			
			if (issetStr($this->Twig->Table->PrimaryValue)) {
				/* get all branches without table settings */
				$a_sqlAdditionalFilter = array(array('column' => 'Table', 'value' => 'NULL', 'operator' => 'IS', 'filterOperator' => 'AND'), array('column' => 'Id', 'value' => 1, 'operator' => '<>', 'filterOperator' => 'AND'));
				
				$s_label = $o_glob->GetTranslation('formTransferTwigToLabel', 0);
				$s_valMessage = $o_glob->GetTranslation('formTransferTwigToValMessage', 0);
			} else {
				/* get all branches with table settings */
				$a_sqlAdditionalFilter = array(array('column' => 'Table', 'value' => 'NULL', 'operator' => 'IS NOT', 'filterOperator' => 'AND'), array('column' => 'Id', 'value' => 1, 'operator' => '<>', 'filterOperator' => 'AND'));
			}
			
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_branches = $o_branchTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			$a_options = array();
			
			foreach($o_branches->Twigs as $o_branch) {
				$a_options[$o_branch->Title . ' (' . $o_branch->Name . ')'] = $o_branch->Id;
			}
			
			$o_select = new forestFormElement(forestFormElement::SELECT);
			$o_select->Label = $s_label;
			$o_select->Id = 'sys_fphp_transferTwig_branch_id';
			$o_select->ValMessage = $s_valMessage;
			$o_select->Required = true;
			$o_select->Options = $a_options;
			
			$o_glob->PostModalForm->FormElements->Add($o_select);
			
			/* add validation rule for manual created form element */
			$o_glob->PostModalForm->FormObject->ValRules->Add(new forestFormValidationRule($o_select->Id, 'required', 'true'));
		} else {
			if (intval($_POST['sys_fphp_transferTwig_branch_id']) == 1) {
				throw new forestException(0x10001F05);
			}
			
			$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
			
			$o_branchTwig = new branchTwig;
			
			if (! ($o_branchTwig->GetRecord(array($_POST['sys_fphp_transferTwig_branch_id']))) ) {
				throw new forestException(0x10001401, array($o_branchTwig->fphp_Table));
			}
			
			if (issetStr($this->Twig->Table->PrimaryValue)) {
				/* transfer twig to target branch */
				$o_sourceBranch = $this->Twig;
				$o_targetBranch = $o_branchTwig;
			} else {
				/* transfer twig from target branch */
				$o_sourceBranch = $o_branchTwig;
				$o_targetBranch = $this->Twig;
			}
			
			/* edit translations */
			$o_translationTwig = new translationTwig;
			$a_sqlAdditionalFilter = array(array('column' => 'BranchId', 'value' => $o_sourceBranch->Id, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_translations = $o_translationTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			foreach ($o_translations->Twigs as $o_translation) {
				/* edit translation branch id */
				$o_translation->BranchId = $o_targetBranch->Id;
				
				/* edit translation recrod */
				$i_result = $o_translation->UpdateRecord();
				
				/* evaluate result */
				if ($i_result == -1) {
					throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
				}
			}
			
			/* edit files */
			$o_filesTwig = new filesTwig;
			$a_sqlAdditionalFilter = array(array('column' => 'BranchId', 'value' => $o_sourceBranch->Id, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_files = $o_filesTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			foreach ($o_files->Twigs as $o_file) {
				/* edit translation branch id */
				$o_file->BranchId = $o_targetBranch->Id;
				
				/* edit translation recrod */
				$i_result = $o_file->UpdateRecord();
				
				/* evaluate result */
				if ($i_result == -1) {
					throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
				}
			}
			
			/* transfer files */
			/* generate source path */
			$o_glob->SetVirtualTarget($o_sourceBranch->Id);
			$s_sourcePath = '';
			
			if (count($o_glob->URL->VirtualBranches) > 0) {
				foreach($o_glob->URL->VirtualBranches as $s_value) {
					$s_sourcePath .= $s_value . '/';
				}
			} else {
				$s_sourcePath .= $o_glob->URL->VirtualBranch . '/';
			}
			
			$s_sourcePath = './trunk/' . $s_sourcePath . 'fphp_files/';
			
			/* generate target path */
			$o_glob->SetVirtualTarget($o_targetBranch->Id);
			$s_targetPath = '';
			
			if (count($o_glob->URL->VirtualBranches) > 0) {
				foreach($o_glob->URL->VirtualBranches as $s_value) {
					$s_targetPath .= $s_value . '/';
				}
			} else {
				$s_targetPath .= $o_glob->URL->VirtualBranch . '/';
			}
			
			$s_targetPath = './trunk/' . $s_targetPath . 'fphp_files/';
			
			forestFile::CopyRecursive($s_sourcePath, $s_targetPath);
			forestFile::RemoveDirectoryRecursive($s_sourcePath);
			
			/* edit branches */
			$o_targetBranch->Table = $o_sourceBranch->Table->PrimaryValue;
			$o_sourceBranch->Table = 'NULL';
			
			/* edit source branch recrod */
			$i_result = $o_sourceBranch->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			}
			
			/* edit target branch recrod */
			$i_result = $o_targetBranch->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			}
			
			/* update source branch file */
			/* exchange branch file with new branch file + landing page */
			/* generate source path */
			$o_glob->SetVirtualTarget($o_sourceBranch->Id);
			$s_sourcePath = '';
			
			if (count($o_glob->URL->VirtualBranches) > 0) {
				foreach($o_glob->URL->VirtualBranches as $s_value) {
					$s_sourcePath .= $s_value . '/';
				}
			} else {
				$s_sourcePath .= $o_glob->URL->VirtualBranch . '/';
			}
			
			/* get directory content of current page into array */
			$a_dirContent = scandir('./trunk/' . $s_sourcePath);
			$s_sourcePath = './trunk/' . $s_sourcePath . $o_sourceBranch->Name . 'Branch.php';
			
			/* if we cannot find branch file */
			if (!in_array($o_sourceBranch->Name . 'Branch.php', $a_dirContent)) {
				throw new forestException('Cannot find file [%0].', array($o_sourceBranch->Name . 'Branch.php'));
			}
			
			$o_file = new forestFile($s_sourcePath);
			$o_file->ReplaceContent( strval(new forestTemplates(forestTemplates::CREATENEWBRANCH, array($o_sourceBranch->Name))) );
			
			/* update target branch file */
			/* exchange branch file with new branch file */
			/* generate target path */
			$o_glob->SetVirtualTarget($o_targetBranch->Id);
			$s_targetPath = '';
			
			if (count($o_glob->URL->VirtualBranches) > 0) {
				foreach($o_glob->URL->VirtualBranches as $s_value) {
					$s_targetPath .= $s_value . '/';
				}
			} else {
				$s_targetPath .= $o_glob->URL->VirtualBranch . '/';
			}
			
			/* get directory content of current page into array */
			$a_dirContent = scandir('./trunk/' . $s_targetPath);
			$s_targetPath = './trunk/' . $s_targetPath . $o_targetBranch->Name . 'Branch.php';
			
			/* if we cannot find branch file */
			if (!in_array($o_targetBranch->Name . 'Branch.php', $a_dirContent)) {
				throw new forestException('Cannot find file [%0].', array($o_targetBranch->Name . 'Branch.php'));
			}
			
			/* gather information */
			$s_filter = 'false';
			
			if ($o_targetBranch->Filter) {
				$s_filter = 'true';
			}
			
			$s_keepFilter = 'false';
			
			if ($o_targetBranch->KeepFilter) {
				$s_keepFilter = 'true';
			}
			
			$s_standardView = 'forestBranch::LIST';
			
			if ($o_targetBranch->StandardView == 10) {
				$s_standardView = 'forestBranch::DETAIL';
			} else if ($o_targetBranch->StandardView == 100) {
				$s_standardView = 'forestBranch::FLEX';
			}
			
			$o_tableTwig = new tableTwig;
			
			if (! ($o_tableTwig->GetRecord(array($o_targetBranch->Table->PrimaryValue))) ) {
				throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
			}
			
			$s_tableName = $o_tableTwig->Name;
			forestStringLib::RemoveTablePrefix($s_tableName);
			
			$o_file = new forestFile($s_targetPath);
			$o_file->ReplaceContent( strval(new forestTemplates(forestTemplates::CREATENEWBRANCHWITHTWIG, array($o_targetBranch->Name, $s_tableName, $s_filter, $s_standardView, $s_keepFilter))) );
			
			$o_glob->SystemMessages->Add(new forestException(0x10001F0C));
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
	
	/* handle truncate twig record action */
	protected function truncateTwigAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
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
			$s_title = $o_glob->GetTranslation('TruncateModalTitle', 1);
			$s_description = '<div class="alert alert-warning">' . $o_glob->GetTranslation('TruncateModalDescription', 1) . '</div>';
			$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);
		} else {
			$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
			
			/* execute truncation of twig */
			$this->executeTruncateTwig($this->Twig);
			
			$o_glob->SystemMessages->Add(new forestException(0x10001F0A));
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
	
	/* handle sub records display in detail view */
	protected function ListTwigProperties(tableTwig $p_o_twig) {
		$o_glob = forestGlobals::init();
		
		$s_subFormItems = '';
		
		/* ************************************************** */
		/* ********************TABLEFIELDS******************* */
		/* ************************************************** */
		/* look for tablefields */
		$o_tablefieldTwig = new tablefieldTwig;
		
		$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $p_o_twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		/* ************************* */
		/* ***********HEAD********** */
		/* ************************* */
		
		$s_subTableHead = '';
		
		foreach ($o_tablefieldTwig->fphp_View as $s_columnHead) {
			if ( ($s_columnHead == 'Order') || ($s_columnHead == 'JSONEncodedSettings') || ($s_columnHead == 'FooterElement') || ($s_columnHead == 'SubRecordField') ) {
				continue;
			}
			
			if ($s_columnHead == 'FooterElement') {
				$s_columnHead = $o_glob->GetTranslation('formFooterElementOptionLabel00', 0);
			} else {
				$s_columnHead = $o_glob->GetTranslation('form' . $s_columnHead . 'Label', 0);
			}
			
			if (forestStringLib::EndsWith($s_columnHead, ':')) {
				$s_columnHead = substr($s_columnHead, 0, -1);
			}
			
			$s_subTableHead .= '<th>' . $s_columnHead . '</th>' . "\n";
		}
		
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('ValidationRules', 0) . '</th>' . "\n";
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
		
		/* ************************* */
		/* ***********ROWS********** */
		/* ************************* */
		
		$s_subTableRows = '';
		
		foreach ($o_tablefields->Twigs as $o_tablefield) {
			/* render records based on twig view columns */
			$s_subTableRows .=  '<tr>' . "\n";
			
			foreach ($o_tablefieldTwig->fphp_View as $s_column) {
				if ( ($s_column == 'Order') || ($s_column == 'JSONEncodedSettings') || ($s_column == 'FooterElement') || ($s_column == 'SubRecordField') ) {
					continue;
				}
				
				$s_formElement = '';
				
				if ($o_glob->TablefieldsDictionary->Exists($o_tablefieldTwig->fphp_Table . '_' . $s_column)) {
					$s_formElement = $o_glob->TablefieldsDictionary->{$o_tablefieldTwig->fphp_Table . '_' . $s_column}->FormElementName;
				}
				
				$s_value = '-';
				$this->ListViewRenderColumnValue($s_value, $s_formElement, $s_column, $o_tablefield, $o_tablefieldTwig->fphp_Table . '_' . $s_column);
				
				if ($s_column == 'JSONEncodedSettings') {
					$s_value = substr($s_value, 0, 100) . '...';
				}
				
				$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
			}
			
			$o_tablefield_validationruleTwig = new tablefield_validationruleTwig;
			$a_sqlAdditionalFilter = array(array('column' => 'TablefieldUUID', 'value' => $o_tablefield->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$i_validationrules = $o_tablefield_validationruleTwig->GetCount(null, true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			$s_value = '-';
			
			if ($i_validationrules > 0) {
				$s_value = '<span class="glyphicon glyphicon-ok text-success"></span>';
			} else {
				$s_value = '<span class="glyphicon glyphicon-remove text-danger"></span>';
			}
			
			$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
			
			$s_options = '<span class="btn-group">' . "\n";
			
			/* view link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['viewKey'] = $o_tablefield->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'viewTwigField')) {
				$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'viewTwigField', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnViewText', 1) . '"><span class="glyphicon glyphicon-zoom-in"></span></a>';
			}
			
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
			$a_parameters['editKey'] = $o_tablefield->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'editTwigField')) {
				$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'editTwigField', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="glyphicon glyphicon-pencil"></span></a>' . "\n";
			}
			
			/* move up link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['editKey'] = $o_tablefield->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'moveUpTwigField')) {
				$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'moveUpTwigField', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnMoveUpText', 1) . '"><span class="glyphicon glyphicon-triangle-top"></span></a>';
			}
			
			/* move down link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['editKey'] = $o_tablefield->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'moveDownTwigField')) {
				$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'moveDownTwigField', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnMoveDownText', 1) . '"><span class="glyphicon glyphicon-triangle-bottom"></span></a>';
			}
			
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
			$a_parameters['deleteKey'] = $o_tablefield->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'deleteTwigField')) {
				$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'deleteTwigField', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="glyphicon glyphicon-trash text-danger"></span></a>' . "\n";
			}
			
			$s_options .= '</span>' . "\n";
			$s_subTableRows .=  '<td>' . $s_options . '</td>' . "\n";
			
			$s_subTableRows .=  '</tr>' . "\n";
		}
		
		/* new link */
		if ($o_glob->Security->CheckUserPermission(null, 'newTwigField')) {
			$s_newButton = '<a href="' . forestLink::Link($o_glob->URL->Branch, 'newTwigField') . '" class="btn btn-default" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="glyphicon glyphicon-plus text-success"></span></a>' . "\n";
		} else {
			$s_newButton = '';
		}
		
		$s_subFormItemContent = new forestTemplates(forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
		$s_subFormItems .= new forestTemplates(forestTemplates::SUBLISTVIEWITEM, array('tablefields' . $p_o_twig->fphp_Table, $o_glob->GetTranslation('TableFields', 0) . ' (' . $o_tablefields->Twigs->Count() . ')', ' in', $s_subFormItemContent));
		
		/* ************************************************** */
		/* *******************TRANSLATIONS******************* */
		/* ************************************************** */
		/* look for translations */
		$o_translationTwig = new translationTwig;
		
		$a_sqlAdditionalFilter = array(array('column' => 'BranchId', 'value' => $o_glob->URL->BranchId, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_translations = $o_translationTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		/* ************************* */
		/* ***********HEAD********** */
		/* ************************* */
		
		$s_subTableHead = '';
		
		foreach ($o_translationTwig->fphp_View as $s_columnHead) {
			$s_columnHead = $o_glob->GetTranslation('form' . $s_columnHead . 'Label', 0);
			
			if (forestStringLib::EndsWith($s_columnHead, ':')) {
				$s_columnHead = substr($s_columnHead, 0, -1);
			}
			
			$s_subTableHead .= '<th>' . $s_columnHead . '</th>' . "\n";
		}
		
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
		
		/* ************************* */
		/* ***********ROWS********** */
		/* ************************* */
		
		$s_subTableRows = '';
		
		foreach ($o_translations->Twigs as $o_translation) {
			/* render records based on twig view columns */
			$s_subTableRows .=  '<tr>' . "\n";
			
			foreach ($o_translationTwig->fphp_View as $s_column) {
				$s_formElement = '';
				
				if ($o_glob->TablefieldsDictionary->Exists($o_translationTwig->fphp_Table . '_' . $s_column)) {
					$s_formElement = $o_glob->TablefieldsDictionary->{$o_translationTwig->fphp_Table . '_' . $s_column}->FormElementName;
				}
				
				$s_value = '-';
				$this->ListViewRenderColumnValue($s_value, $s_formElement, $s_column, $o_translation, $o_translationTwig->fphp_Table . '_' . $s_column);
				
				$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
			}
			
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
			$a_parameters['editKey'] = $o_translation->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'editTranslation')) {
				$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'editTranslation', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="glyphicon glyphicon-pencil"></span></a>' . "\n";
			}
			
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
			$a_parameters['deleteKey'] = $o_translation->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'deleteTranslation')) {
				$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'deleteTranslation', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="glyphicon glyphicon-trash text-danger"></span></a>' . "\n";
			}
			
			$s_options .= '</span>' . "\n";
			$s_subTableRows .=  '<td>' . $s_options . '</td>' . "\n";
			
			$s_subTableRows .=  '</tr>' . "\n";
		}
		
		/* new link */
		if ($o_glob->Security->CheckUserPermission(null, 'newTwigField')) {
			$s_newButton = '<a href="' . forestLink::Link($o_glob->URL->Branch, 'newTranslation') . '" class="btn btn-default" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="glyphicon glyphicon-plus text-success"></span></a>' . "\n";
		} else {
			$s_newButton = '';
		}
		
		$s_subFormItemContent = new forestTemplates(forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
		$s_subFormItems .= new forestTemplates(forestTemplates::SUBLISTVIEWITEM, array('translations' . $p_o_twig->fphp_Table, $o_glob->GetTranslation('TranslationLines', 0) . ' (' . $o_translations->Twigs->Count() . ')', '', $s_subFormItemContent));
		
		/* ************************************************** */
		/* *******************UNIQUE KEYS******************** */
		/* ************************************************** */
		/* look for unique keys in table record */
		$a_uniqueKeys = array();
		
		if (issetStr($p_o_twig->Unique->PrimaryValue)) {
			$a_uniqueKeys = explode(':', $p_o_twig->Unique->PrimaryValue);
		}
		
		/* ************************* */
		/* ***********HEAD********** */
		/* ************************* */
		
		$s_subTableHead = '<th>' . substr($o_glob->GetTranslation('formNameLabel', 0), 0, -1) . '</th>' . "\n";
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('UniqueKey', 0) . '</th>' . "\n";
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
		
		/* ************************* */
		/* ***********ROWS********** */
		/* ************************* */
		
		$s_subTableRows = '';
		$i = 0;
		
		foreach ($a_uniqueKeys as $o_uniqueKey) {
			/* render unique keys based on twig unique column */
			$s_subTableRows .=  '<tr>' . "\n";
			
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
			
			$s_subTableRows .=  '<td><span>' . $s_name . '</span></td>' . "\n";
			$s_subTableRows .=  '<td><span>' . implode(', ', $a_keys) . '</span></td>' . "\n";
			
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
			$a_parameters['editKey'] = $i;
			
			if ($o_glob->Security->CheckUserPermission(null, 'editUnique')) {
				$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'editUnique', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="glyphicon glyphicon-pencil"></span></a>' . "\n";
			}
			
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
			$a_parameters['deleteKey'] = $i;
			
			if ($o_glob->Security->CheckUserPermission(null, 'deleteUnique')) {
				$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'deleteUnique', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="glyphicon glyphicon-trash text-danger"></span></a>' . "\n";
			}
			
			$s_options .= '</span>' . "\n";
			$s_subTableRows .=  '<td>' . $s_options . '</td>' . "\n";
			
			$s_subTableRows .=  '</tr>' . "\n";
			
			$i++;
		}
		
		/* new link */
		if ($o_glob->Security->CheckUserPermission(null, 'newUnique')) {
			$s_newButton = '<a href="' . forestLink::Link($o_glob->URL->Branch, 'newUnique') . '" class="btn btn-default" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="glyphicon glyphicon-plus text-success"></span></a>' . "\n";
		} else {
			$s_newButton = '';
		}
		
		$s_subFormItemContent = new forestTemplates(forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
		$s_subFormItems .= new forestTemplates(forestTemplates::SUBLISTVIEWITEM, array('uniques' . $p_o_twig->fphp_Table, $o_glob->GetTranslation('UniqueKey', 0) . ' (' . count($a_uniqueKeys) . ')', '', $s_subFormItemContent));
		
		/* ************************************************** */
		/* *******************SORT ORDER********************* */
		/* ************************************************** */
		/* look for unique keys in table record */
		$a_sortOrders = array();
		
		if (issetStr($p_o_twig->SortOrder->PrimaryValue)) {
			$a_sortOrders = explode(':', $p_o_twig->SortOrder->PrimaryValue);
		}
		
		/* ************************* */
		/* ***********HEAD********** */
		/* ************************* */
		
		$s_subTableHead = '<th>' . $o_glob->GetTranslation('SortOrder', 0) . '</th>' . "\n";
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
		
		/* ************************* */
		/* ***********ROWS********** */
		/* ************************* */
		
		$s_subTableRows = '';
		$i = 0;
		
		foreach ($a_sortOrders as $o_sortOrder) {
			/* render sort orders based on twig sort order column */
			$a_sort = explode(';', $o_sortOrder);
			
			if (count($a_sort) != 2) {
				continue;
			}
			
			$s_subTableRows .=  '<tr>' . "\n";
			
			$s_name = '';
			$s_direction = '';
			
			/* query tablefield to get FieldName for display */
			if ($o_tablefieldTwig->GetRecord(array($a_sort[1]))) {
				$s_name = $o_tablefieldTwig->FieldName;
			} else {
				$s_name = 'invalid_column';
			}
			
			if ($a_sort[0] == 'false') {
				$s_direction = ' <span class="glyphicon glyphicon-sort-by-attributes-alt"></span>';
			} else if ($a_sort[0] == 'true') {
				$s_direction = ' <span class="glyphicon glyphicon-sort-by-attributes"></span>';
			}
			
			$s_subTableRows .=  '<td><span>' . $s_name . $s_direction . '</span></td>' . "\n";
			
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
			$a_parameters['editKey'] = $i;
			
			if ($o_glob->Security->CheckUserPermission(null, 'editSort')) {
				$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'editSort', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="glyphicon glyphicon-pencil"></span></a>' . "\n";
			}
			
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
			$a_parameters['deleteKey'] = $i;
			
			if ($o_glob->Security->CheckUserPermission(null, 'deleteSort')) {
				$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'deleteSort', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="glyphicon glyphicon-trash text-danger"></span></a>' . "\n";
			}
			
			$s_options .= '</span>' . "\n";
			$s_subTableRows .=  '<td>' . $s_options . '</td>' . "\n";
			
			$s_subTableRows .=  '</tr>' . "\n";
			
			$i++;
		}
		
		/* new link */
		if ($o_glob->Security->CheckUserPermission(null, 'newSort')) {
			$s_newButton = '<a href="' . forestLink::Link($o_glob->URL->Branch, 'newSort') . '" class="btn btn-default" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="glyphicon glyphicon-plus text-success"></span></a>' . "\n";
		} else {
			$s_newButton = '';
		}
		
		$s_subFormItemContent = new forestTemplates(forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
		$s_subFormItems .= new forestTemplates(forestTemplates::SUBLISTVIEWITEM, array('sortOrders' . $p_o_twig->fphp_Table, $o_glob->GetTranslation('SortOrder', 0) . ' (' . count($a_sortOrders) . ')', '', $s_subFormItemContent));
		
		/* ************************************************** */
		/* ******************SUB CONSTRAINTS***************** */
		/* ************************************************** */
		/* look for sub constraints */
		$o_subconstraintTwig = new subconstraintTwig;
		
		$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $p_o_twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_subconstraints = $o_subconstraintTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		/* ************************* */
		/* ***********HEAD********** */
		/* ************************* */
		
		$s_subTableHead = '<th>' . $o_glob->GetTranslation('Table', 0) . '</th>' . "\n";
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('View', 1) . '</th>' . "\n";
		
		/* ************************* */
		/* ***********ROWS********** */
		/* ************************* */
		
		$s_subTableRows = '';
		
		foreach ($o_subconstraints->Twigs as $o_subconstraint) {
			/* render records based on twig view columns */
			$s_subTableRows .=  '<tr>' . "\n";
			
			$s_table = $o_glob->TablesInformation[$o_subconstraint->SubTableUUID->PrimaryValue]['Name'];
			forestStringLib::RemoveTablePrefix($s_table);
			$s_table = $o_glob->GetTranslation($o_glob->BranchTree['Id'][$o_glob->BranchTree['Name'][$s_table]]['Title'], 1);
			
			$a_view = array();
			
			if (issetStr($o_subconstraint->View->PrimaryValue)) {
				$a_foo = explode(';', $o_subconstraint->View->PrimaryValue);
				
				foreach ($a_foo as $s_foo) {
					/* query tablefield to get FieldName for display */
					if ($o_tablefieldTwig->GetRecord(array($s_foo))) {
						$a_view[] = $o_tablefieldTwig->FieldName;
					} else {
						$a_view[] = 'invalid_tablefield';
					}
				}
			}
			
			$s_subTableRows .=  '<td><span>' . $s_table . ' (' . $o_glob->TablesInformation[$o_subconstraint->SubTableUUID->PrimaryValue]['Name'] . ')' . '</span></td>' . "\n";
			$s_subTableRows .=  '<td><span>' . implode(', ', $a_view) . '</span></td>' . "\n";
			
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
			$a_parameters['editKey'] = $o_subconstraint->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'editSubConstraint')) {
				$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'editSubConstraint', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="glyphicon glyphicon-pencil"></span></a>' . "\n";
			}
			
			/* move up link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['editKey'] = $o_subconstraint->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'moveUpSubConstraint')) {
				$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'moveUpSubConstraint', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnMoveUpText', 1) . '"><span class="glyphicon glyphicon-triangle-top"></span></a>';
			}

			/* move down link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['editKey'] = $o_subconstraint->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'moveDownSubConstraint')) {
				$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'moveDownSubConstraint', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnMoveDownText', 1) . '"><span class="glyphicon glyphicon-triangle-bottom"></span></a>';
			}

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
			$a_parameters['deleteKey'] = $o_subconstraint->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'deleteSubConstraint')) {
				$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'deleteSubConstraint', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="glyphicon glyphicon-trash text-danger"></span></a>' . "\n";
			}

			$s_options .= '</span>' . "\n";
			$s_subTableRows .=  '<td>' . $s_options . '</td>' . "\n";
			
			$s_subTableRows .=  '</tr>' . "\n";
		}
		
		/* new link */
		if ($o_glob->Security->CheckUserPermission(null, 'newSubConstraint')) {
			$s_newButton = '<a href="' . forestLink::Link($o_glob->URL->Branch, 'newSubConstraint') . '" class="btn btn-default" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="glyphicon glyphicon-plus text-success"></span></a>' . "\n";
		} else {
			$s_newButton = '';
		}

		$s_subFormItemContent = new forestTemplates(forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
		$s_subFormItems .= new forestTemplates(forestTemplates::SUBLISTVIEWITEM, array('subConstraints' . $p_o_twig->fphp_Table, $o_glob->GetTranslation('SubConstraints', 0) . ' (' . $o_subconstraints->Twigs->Count() . ')', '', $s_subFormItemContent));
		
		/* ************************************************** */
		/* ***********SUB CONSTRAINTS TABLEFIELDS************ */
		/* ************************************************** */
		foreach ($o_subconstraints->Twigs as $o_subconstraint) {
			/* look for tablefields */
			$o_tablefieldTwig = new tablefieldTwig;
			
			$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $o_subconstraint->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_subTablefields = $o_tablefieldTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			/* ************************* */
			/* ***********HEAD********** */
			/* ************************* */
			
			$s_subTableHead = '';
			
			foreach ($o_tablefieldTwig->fphp_View as $s_columnHead) {
				if ( ($s_columnHead == 'Order') || ($s_columnHead == 'JSONEncodedSettings') || ($s_columnHead == 'FooterElement') ) {
					continue;
				}
				
				if ($s_columnHead == 'FooterElement') {
					$s_columnHead = $o_glob->GetTranslation('formFooterElementOptionLabel00', 0);
				} else {
					$s_columnHead = $o_glob->GetTranslation('form' . $s_columnHead . 'Label', 0);
				}
				
				if (forestStringLib::EndsWith($s_columnHead, ':')) {
					$s_columnHead = substr($s_columnHead, 0, -1);
				}
				
				$s_subTableHead .= '<th>' . $s_columnHead . '</th>' . "\n";
			}
			
			$s_subTableHead .= '<th>' . $o_glob->GetTranslation('ValidationRules', 0) . '</th>' . "\n";
			$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
			
			/* ************************* */
			/* ***********ROWS********** */
			/* ************************* */
			
			$s_subTableRows = '';
			
			foreach ($o_subTablefields->Twigs as $o_subTablefield) {
				/* render records based on twig view columns */
				$s_subTableRows .=  '<tr>' . "\n";
				
				foreach ($o_tablefieldTwig->fphp_View as $s_column) {
					if ( ($s_column == 'Order') || ($s_column == 'JSONEncodedSettings') || ($s_column == 'FooterElement') ) {
						continue;
					}
					
					$s_formElement = '';
					
					if ($o_glob->TablefieldsDictionary->Exists($o_tablefieldTwig->fphp_Table . '_' . $s_column)) {
						$s_formElement = $o_glob->TablefieldsDictionary->{$o_tablefieldTwig->fphp_Table . '_' . $s_column}->FormElementName;
					}
					
					$s_value = '-';
					$this->ListViewRenderColumnValue($s_value, $s_formElement, $s_column, $o_subTablefield, $o_tablefieldTwig->fphp_Table . '_' . $s_column);
					
					if ($s_column == 'JSONEncodedSettings') {
						$s_value = substr($s_value, 0, 100) . '...';
					}
					
					$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
				}
				
				$o_tablefield_validationruleTwig = new tablefield_validationruleTwig;
				$a_sqlAdditionalFilter = array(array('column' => 'TablefieldUUID', 'value' => $o_subTablefield->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$i_validationrules = $o_tablefield_validationruleTwig->GetCount(null, true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				$s_value = '-';
				
				if ($i_validationrules > 0) {
					$s_value = '<span class="glyphicon glyphicon-ok text-success"></span>';
				} else {
					$s_value = '<span class="glyphicon glyphicon-remove text-danger"></span>';
				}
				
				$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
				
				$s_options = '<span class="btn-group">' . "\n";
				
				/* view link */
				$a_parameters = $o_glob->URL->Parameters;
				unset($a_parameters['newKey']);
				unset($a_parameters['viewKey']);
				unset($a_parameters['editKey']);
				unset($a_parameters['deleteKey']);
				unset($a_parameters['editSubKey']);
				unset($a_parameters['deleteSubKey']);
				unset($a_parameters['deleteFileKey']);
				unset($a_parameters['subConstraintKey']);
				unset($a_parameters['rootSubConstraintKey']);
				$a_parameters['viewKey'] = $o_subTablefield->UUID;
				$a_parameters['rootSubConstraintKey'] = $o_subconstraint->UUID;
				
				if ($o_glob->Security->CheckUserPermission(null, 'viewTwigField')) {
					$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'viewTwigField', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnViewText', 1) . '"><span class="glyphicon glyphicon-zoom-in"></span></a>';
				}

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
				unset($a_parameters['rootSubConstraintKey']);
				$a_parameters['editKey'] = $o_subTablefield->UUID;
				$a_parameters['rootSubConstraintKey'] = $o_subconstraint->UUID;
				
				if ($o_glob->Security->CheckUserPermission(null, 'editTwigField')) {
					$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'editTwigField', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="glyphicon glyphicon-pencil"></span></a>' . "\n";
				}

				/* move up link */
				$a_parameters = $o_glob->URL->Parameters;
				unset($a_parameters['newKey']);
				unset($a_parameters['viewKey']);
				unset($a_parameters['editKey']);
				unset($a_parameters['deleteKey']);
				unset($a_parameters['editSubKey']);
				unset($a_parameters['deleteSubKey']);
				unset($a_parameters['deleteFileKey']);
				unset($a_parameters['subConstraintKey']);
				unset($a_parameters['rootSubConstraintKey']);
				$a_parameters['editKey'] = $o_subTablefield->UUID;
				$a_parameters['rootSubConstraintKey'] = $o_subconstraint->UUID;
				
				if ($o_glob->Security->CheckUserPermission(null, 'moveUpTwigField')) {
					$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'moveUpTwigField', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnMoveUpText', 1) . '"><span class="glyphicon glyphicon-triangle-top"></span></a>';
				}

				/* move down link */
				$a_parameters = $o_glob->URL->Parameters;
				unset($a_parameters['newKey']);
				unset($a_parameters['viewKey']);
				unset($a_parameters['editKey']);
				unset($a_parameters['deleteKey']);
				unset($a_parameters['editSubKey']);
				unset($a_parameters['deleteSubKey']);
				unset($a_parameters['deleteFileKey']);
				unset($a_parameters['subConstraintKey']);
				unset($a_parameters['rootSubConstraintKey']);
				$a_parameters['editKey'] = $o_subTablefield->UUID;
				$a_parameters['rootSubConstraintKey'] = $o_subconstraint->UUID;
				
				if ($o_glob->Security->CheckUserPermission(null, 'moveDownTwigField')) {
					$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'moveDownTwigField', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnMoveDownText', 1) . '"><span class="glyphicon glyphicon-triangle-bottom"></span></a>';
				}

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
				unset($a_parameters['rootSubConstraintKey']);
				$a_parameters['deleteKey'] = $o_subTablefield->UUID;
				$a_parameters['rootSubConstraintKey'] = $o_subconstraint->UUID;
				
				if ($o_glob->Security->CheckUserPermission(null, 'deleteTwigField')) {
					$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'deleteTwigField', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="glyphicon glyphicon-trash text-danger"></span></a>' . "\n";
				}

				$s_options .= '</span>' . "\n";
				$s_subTableRows .=  '<td>' . $s_options . '</td>' . "\n";
				
				$s_subTableRows .=  '</tr>' . "\n";
			}
			
			/* new link */
			if ($o_glob->Security->CheckUserPermission(null, 'newTwigField')) {
				$s_newButton = '<a href="' . forestLink::Link($o_glob->URL->Branch, 'newTwigField', array('rootSubConstraintKey' => $o_subconstraint->UUID)) . '" class="btn btn-default" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="glyphicon glyphicon-plus text-success"></span></a>' . "\n";
			} else {
				$s_newButton = '';
			}

			$s_tempTable = $o_glob->TablesInformation[$o_subconstraint->SubTableUUID->PrimaryValue]['Name'];
			forestStringLib::RemoveTablePrefix($s_tempTable);
			
			$s_subFormItemContent = new forestTemplates(forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
			$s_subFormItems .= new forestTemplates(forestTemplates::SUBLISTVIEWITEM, array('sub' . $s_tempTable, $o_glob->GetTranslation($o_glob->BranchTree['Id'][$o_glob->BranchTree['Name'][$s_tempTable]]['Title'], 1) . ' ' . $o_glob->GetTranslation('SubConstraint', 0) . ' (' . $o_subTablefields->Twigs->Count() . ')', '', $s_subFormItemContent));
		}
		
		/* use template to render tablefields, translations, unique keys, sort orders, sub constraints and sub constraint tablefields of a record */
		return new forestTemplates(forestTemplates::SUBLISTVIEW, array($s_subFormItems));
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
	
	
	/* handle new twig tablefield record action */
	protected function newTwigFieldAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$s_nextActionAfterReload = null;
		$o_saveTwig = $this->Twig;
		$this->Twig = new tablefieldTwig;
		$o_tableTwig = new tableTwig;
		
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'rootSubConstraintKey'), 'rootSubConstraintKey' );
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			/* add new branch record form */
			$o_glob->PostModalForm = new forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
			
			/* delete Order-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_tablefield_Order')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_tablefield_Order].');
			}
			
			if (!( ($o_glob->Temp->Exists('rootSubConstraintKey')) && ($o_glob->Temp->{'rootSubConstraintKey'} != null) )) {
				/* delete SubRecordField-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_tablefield_SubRecordField')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_tablefield_SubRecordField].');
				}
			} else {
				/* query sub constraint record */
				$o_subconstraintTwig = new subconstraintTwig;
				
				if (! ($o_subconstraintTwig->GetRecord(array($o_glob->Temp->{'rootSubConstraintKey'}))) ) {
					throw new forestException(0x10001401, array($o_subconstraintTwig->fphp_Table));
				}
				
				/* add current record key to modal form as hidden field */
				$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_subconstraintKey';
				$o_hidden->Value = strval($o_glob->Temp->{'rootSubConstraintKey'});
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
			}
		} else {
			$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
			
			/* check posted data for new tablefield record */
			$this->TransferPOST_Twig();
			
			if (in_array($this->Twig->FieldName, $this->ForbiddenTablefieldNames)) {
				throw new forestException(0x10001F10, array(implode(',', $this->ForbiddenTablefieldNames)));
			}
			
			if (issetStr($this->Twig->FormElementUUID->PrimaryValue)) {
				if (issetStr($this->Twig->SqlTypeUUID->PrimaryValue)) {
					$o_formelement_sqltypeTwig = new formelement_sqltypeTwig;
					
					if (! $o_formelement_sqltypeTwig->GetRecord(array($this->Twig->FormElementUUID->PrimaryValue, $this->Twig->SqlTypeUUID->PrimaryValue))) {
						throw new forestException(0x10001F0D, array($this->Twig->FormElementUUID, $this->Twig->SqlTypeUUID));
					}
				}
				
				if (issetStr($this->Twig->ForestDataUUID->PrimaryValue)) {
					$o_formelement_forestdataTwig = new formelement_forestdataTwig;
					
					if (! $o_formelement_forestdataTwig->GetRecord(array($this->Twig->FormElementUUID->PrimaryValue, $this->Twig->ForestDataUUID->PrimaryValue))) {
						throw new forestException(0x10001F0E, array($this->Twig->FormElementUUID, $this->Twig->ForestDataUUID));
					}
				}
			}
			
			$s_uuid = $o_tableTwig->UUID;
			
			if (array_key_exists('sys_fphp_subconstraintKey', $_POST)) {
				/* query sub constraint record */
				$o_subconstraintTwig = new subconstraintTwig;
				
				if (! ($o_subconstraintTwig->GetRecord(array($_POST['sys_fphp_subconstraintKey']))) ) {
					throw new forestException(0x10001401, array($o_subconstraintTwig->fphp_Table));
				}
				
				$s_uuid = $o_subconstraintTwig->UUID;
			}
			
			$i_order = 1;
			$o_tablefieldTwig = new tablefieldTwig;
			
			$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $s_uuid, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			if ($o_tablefieldTwig->GetLastRecord()) {
				$i_order = $o_tablefieldTwig->Order + 1;
			}
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			/* add Order value to record */
			$this->Twig->Order = $i_order;
			
			/* check if json encoded settings are valid */
			$a_settings = json_decode($_POST['sys_fphp_tablefield_JSONEncodedSettings'], true);
		
			if ($a_settings == null) {
				throw new forestException(0x10001F02, array($_POST['sys_fphp_tablefield_JSONEncodedSettings']));
			}
			
			/* if no json setting for Id is available, add it automatically */
			if (!array_key_exists('Id', $a_settings)) {
				$s_table = $o_tableTwig->Name;
				
				if (array_key_exists('sys_fphp_subconstraintKey', $_POST)) {
					$s_table = $o_subconstraintTwig->fphp_Table;
				}
				
				$a_settings['Id'] = $s_table . '_' . $this->Twig->FieldName;
				$_POST['sys_fphp_tablefield_JSONEncodedSettings'] = json_encode($a_settings, JSON_UNESCAPED_SLASHES );
				$this->Twig->JSONEncodedSettings = strval($_POST['sys_fphp_tablefield_JSONEncodedSettings']);
			}
			
			/* recognize translation name and value pair within json encoded settings and create tranlsation records automatically */
			preg_match_all('/\#([^#]+)\#/', $_POST['sys_fphp_tablefield_JSONEncodedSettings'], $a_matches);
	
			if (count($a_matches) > 1) {
				$o_translationTwig = new translationTwig;
				
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
						$o_translationTwig->BranchId = $o_glob->URL->BranchId;
						$o_translationTwig->LanguageCode = $o_glob->Trunk->LanguageCode->PrimaryValue;
						$o_translationTwig->Name = $s_name;
						$o_translationTwig->Value = forestStringLib::ReplaceUnicodeEscapeSequence($s_value);
						
						/* insert translation record */
						$i_result = $o_translationTwig->InsertRecord();
					}
				}
			}
			
			$s_tableUUID = $o_tableTwig->UUID;
			
			if (array_key_exists('sys_fphp_subconstraintKey', $_POST)) {
				$s_tableUUID = $o_subconstraintTwig->UUID;
			}
			
			/* add TableUUID value to record */
			$this->Twig->TableUUID = $s_tableUUID;
			
			/* insert record */
			$i_result = $this->Twig->InsertRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				throw new forestException(0x10001402);
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001404));
				$s_nextAction = 'viewTwig';
				
				if ($o_glob->Security->SessionData->Exists('lastView')) {
					if ($o_glob->Security->SessionData->{'lastView'} == forestBranch::DETAIL) {
						$s_nextAction = null;
					}
				}
				
				if (!array_key_exists('sys_fphp_subconstraintKey', $_POST)) {
					/* execute dbms change if sql type is not empty */
					if (issetStr($this->Twig->SqlTypeUUID->PrimaryValue)) {
						/* ignore forestCombination, Form and Dropzone field */
						if ( (!(strval($this->Twig->ForestDataUUID) == 'forestCombination')) && (!(strval($this->Twig->FormElementUUID) == forestformElement::FORM)) && (!(strval($this->Twig->FormElementUUID) == forestformElement::DROPZONE)) ) {
							/* add new column to table in dbms */
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
					
					/* update twig file */
					$this->doTwigFile($o_tableTwig);
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
		$this->SetNextAction($s_nextAction, $s_nextActionAfterReload);
	}

	/* handle edit twig tabelfield record action */
	protected function editTwigFieldAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new tablefieldTwig;
		$o_tableTwig = new tableTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if ($o_glob->IsPost) {
			/* query record */
			if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_tablefieldKey']))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}
			
			$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
			$s_oldFieldName = $this->Twig->FieldName;
			$this->TransferPOST_Twig();
			
			if (in_array($this->Twig->FieldName, $this->ForbiddenTablefieldNames)) {
				throw new forestException(0x10001F10, array(implode(',', $this->ForbiddenTablefieldNames)));
			}
			
			if (issetStr($this->Twig->FormElementUUID->PrimaryValue)) {
				if (issetStr($this->Twig->SqlTypeUUID->PrimaryValue)) {
					$o_formelement_sqltypeTwig = new formelement_sqltypeTwig;
					
					if (! $o_formelement_sqltypeTwig->GetRecord(array($this->Twig->FormElementUUID->PrimaryValue, $this->Twig->SqlTypeUUID->PrimaryValue))) {
						throw new forestException(0x10001F0D, array($this->Twig->FormElementUUID, $this->Twig->SqlTypeUUID));
					}
				}
				
				if (issetStr($this->Twig->ForestDataUUID->PrimaryValue)) {
					$o_formelement_forestdataTwig = new formelement_forestdataTwig;
					
					if (! $o_formelement_forestdataTwig->GetRecord(array($this->Twig->FormElementUUID->PrimaryValue, $this->Twig->ForestDataUUID->PrimaryValue))) {
						throw new forestException(0x10001F0E, array($this->Twig->FormElementUUID, $this->Twig->ForestDataUUID));
					}
				}
			}
			
			if (array_key_exists('sys_fphp_subconstraintKey', $_POST)) {
				/* query sub constraint record */
				$o_subconstraintTwig = new subconstraintTwig;
				
				if (! ($o_subconstraintTwig->GetRecord(array($_POST['sys_fphp_subconstraintKey']))) ) {
					throw new forestException(0x10001401, array($o_subconstraintTwig->fphp_Table));
				}
			}
			
			/* check if json encoded settings are valid */
			$a_settings = json_decode($_POST['sys_fphp_tablefield_JSONEncodedSettings'], true);
		
			if ($a_settings == null) {
				throw new forestException(0x10001F02, array($_POST['sys_fphp_tablefield_JSONEncodedSettings']));
			}
			
			/* recognize translation name and value pair within json encoded settings and create tranlsation records automatically */
			preg_match_all('/\#([^#]+)\#/', $_POST['sys_fphp_tablefield_JSONEncodedSettings'], $a_matches);
	
			if (count($a_matches) > 1) {
				$o_translationTwig = new translationTwig;
				
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
					
					if ($o_translationTwig->GetRecordPrimary(array($o_glob->URL->BranchId, $o_glob->Trunk->LanguageCode->PrimaryValue, $s_name), array('BranchId', 'LanguageCode', 'Name'))) {
						if ($s_value != 'translation_in_progress') {
							/* update translation recorod with new value */
							$o_translationTwig->Value = $s_value;
							
							/* edit record */
							$i_result = $o_translationTwig->UpdateRecord();
							
							/* evaluate result */
							if ($i_result == -1) {
								throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
							} else if ($i_result == 0) {
								$o_glob->SystemMessages->Add(new forestException(0x10001406));
							}
						}
					} else if (strlen($s_name) >= 8) {
						/* create new translation record */
						$o_newTranslationTwig = new translationTwig;
						$o_newTranslationTwig->BranchId = $o_glob->URL->BranchId;
						$o_newTranslationTwig->LanguageCode = $o_glob->Trunk->LanguageCode->PrimaryValue;
						$o_newTranslationTwig->Name = $s_name;
						$o_newTranslationTwig->Value = $s_value;
						
						/* insert translation record */
						$i_result = $o_newTranslationTwig->InsertRecord();
						
						/* evaluate result */
						if ($i_result == -1) {
							throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
						} else if ($i_result == 0) {
							throw new forestException(0x10001402);
						}
					}
				}
			}
			
			/* edit record */
			$i_result = $this->Twig->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				$o_glob->SystemMessages->Add(new forestException(0x10001406));
				$s_nextAction = 'viewTwig';
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001407));
				$s_nextAction = 'viewTwig';
				
				/* do not execute next action if a field name changed */
				if ($s_oldFieldName != $this->Twig->FieldName) {
					$s_nextAction = null;
				}
				
				if (!array_key_exists('sys_fphp_subconstraintKey', $_POST)) {
					/* execute dbms change if sql type is not empty */
					if (issetStr($this->Twig->SqlTypeUUID->PrimaryValue)) {
						/* ignore forestCombination, Form and Dropzone field */
						if ( (!(strval($this->Twig->ForestDataUUID) == 'forestCombination')) && (!(strval($this->Twig->FormElementUUID) == forestformElement::FORM)) && (!(strval($this->Twig->FormElementUUID) == forestformElement::DROPZONE)) ) {
							/* change column within table in dbms */
							$o_queryAlter = new forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, forestSQLQuery::ALTER, $o_tableTwig->Name);

							$o_column = new forestSQLColumnStructure($o_queryAlter);
								$o_column->Name = $s_oldFieldName;
								$o_column->NewName = $this->Twig->FieldName;
								
								if (issetStr($this->Twig->SqlTypeUUID->PrimaryValue)) {
									$s_columnType = null;
									$i_columnLength = null;
									$i_columnDecimalLength = null;
									forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, strval($this->Twig->SqlTypeUUID), $s_columnType, $i_columnLength, $i_columnDecimalLength);
									
									$o_column->ColumnType = $s_columnType;
									if ($i_columnLength != null) { $o_column->ColumnTypeLength = $i_columnLength; }
									if ($i_columnDecimalLength != null) { $o_column->ColumnTypeDecimalLength = $i_columnDecimalLength; }
									$o_column->ConstraintList->Add('NULL');
									$o_column->AlterOperation = 'CHANGE';
								} else {
									$o_column->AlterOperation = 'DROP';
								}
								
							$o_queryAlter->Query->Columns->Add($o_column);	
							
							/* alter table does not return a value */
							$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false);
						}
					}
					
					/* update twig file */
					$this->doTwigFile($o_tableTwig);
				}
			}
		} else {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editKey'), 'editKey' );
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'rootSubConstraintKey'), 'rootSubConstraintKey' );
			
			if ( ($o_glob->Temp->Exists('editKey')) && ($o_glob->Temp->{'editKey'} != null) ) {
				/* query record */
				if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'editKey'}))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* build modal form */
				$o_glob->PostModalForm = new forestForm($this->Twig, true);
				$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('EditModalTitle', 1);
				
				/* add current record key to modal form as hidden field */
				$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_tablefieldKey';
				$o_hidden->Value = strval($this->Twig->UUID);
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
				
				/* delete Order-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_tablefield_Order')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_tablefield_Order].');
				}
				
				if (!( ($o_glob->Temp->Exists('rootSubConstraintKey')) && ($o_glob->Temp->{'rootSubConstraintKey'} != null) )) {
					/* delete SubRecordField-element */
					if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_tablefield_SubRecordField')) {
						throw new forestException('Cannot delete form element with Id[sys_fphp_tablefield_SubRecordField].');
					}
				} else {
					/* query sub constraint record */
					$o_subconstraintTwig = new subconstraintTwig;
					
					if (! ($o_subconstraintTwig->GetRecord(array($o_glob->Temp->{'rootSubConstraintKey'}))) ) {
						throw new forestException(0x10001401, array($o_subconstraintTwig->fphp_Table));
					}
					
					/* add current record key to modal form as hidden field */
					$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
					$o_hidden->Id = 'sys_fphp_subconstraintKey';
					$o_hidden->Value = strval($o_glob->Temp->{'rootSubConstraintKey'});
					
					$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
				}
				
				/* add current record order to modal form as hidden field */
				$o_hiddenOrder = new forestFormElement(forestFormElement::HIDDEN);
				$o_hiddenOrder->Id = 'sys_fphp_tablefield_Order';
				$o_hiddenOrder->Value = strval($this->Twig->Order);
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hiddenOrder);
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
	
	/* handle delete twig tabelfield record action */
	protected function deleteTwigFieldAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new tablefieldTwig;
		$o_tableTwig = new tableTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteKey'), 'deleteKey' );
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'rootSubConstraintKey'), 'rootSubConstraintKey' );
			
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
				$o_hidden->Id = 'sys_fphp_twigfieldKey';
				$o_hidden->Value = strval($this->Twig->UUID);
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
				
				if ( ($o_glob->Temp->Exists('rootSubConstraintKey')) && ($o_glob->Temp->{'rootSubConstraintKey'} != null) ) {
					/* query sub constraint record */
					$o_subconstraintTwig = new subconstraintTwig;
					
					if (! ($o_subconstraintTwig->GetRecord(array($o_glob->Temp->{'rootSubConstraintKey'}))) ) {
						throw new forestException(0x10001401, array($o_subconstraintTwig->fphp_Table));
					}
					
					/* add current record key to modal form as hidden field */
					$o_hidden2 = new forestFormElement(forestFormElement::HIDDEN);
					$o_hidden2->Id = 'sys_fphp_subconstraintKey';
					$o_hidden2->Value = strval($o_glob->Temp->{'rootSubConstraintKey'});
					
					$o_glob->PostModalForm->FormElements->Add($o_hidden2);
				}
			}
		} else {
			/* delete record */
			if (array_key_exists('sys_fphp_twigfieldKey', $_POST)) {
				if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_twigfieldKey']))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				if (array_key_exists('sys_fphp_subconstraintKey', $_POST)) {
					/* query sub constraint record */
					$o_subconstraintTwig = new subconstraintTwig;
					
					if (! ($o_subconstraintTwig->GetRecord(array($_POST['sys_fphp_subconstraintKey']))) ) {
						throw new forestException(0x10001401, array($o_subconstraintTwig->fphp_Table));
					}
				}
				
				/* check twigfield relation before deletion */
				$this->checkTwigFieldBeforeDeletion($o_tableTwig, $this->Twig);
				
				$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
				
				/* delete tablefield validationrule records */
				$o_tablefield_validationruleTwig = new tablefield_validationruleTwig;
				
				$a_sqlAdditionalFilter = array(array('column' => 'TablefieldUUID', 'value' => $this->Twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
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
				preg_match_all('/\#([^#]+)\#/', $this->Twig->JSONEncodedSettings, $a_matches);
		
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
				
				/* delete record */
				$i_return = $this->Twig->DeleteRecord();
				
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
				
				$o_glob->SystemMessages->Add(new forestException(0x10001427));
				
				/* do not call next action if a twig field has been deleted */
				$s_nextAction = null;
				
				/* cleanup tablefield relations */
				$this->cleanupTwigFieldAfterDeletion($o_tableTwig, $this->Twig);
				
				if (!array_key_exists('sys_fphp_subconstraintKey', $_POST)) {
					/* change column within table in dbms */
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
						$o_column->ConstraintList->Add('NULL');
						$o_column->AlterOperation = 'DROP';
						
					$o_queryAlter->Query->Columns->Add($o_column);	
					
					/* alter table does not return a value */
					$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false);
					
					/* update twig file */
					$this->doTwigFile($o_tableTwig);
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

	/* handle action to change order of twig tablefield records, moving one record up */
	protected function moveUpTwigFieldAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new tablefieldTwig;
		$o_tableTwig = new tableTwig;
		
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'rootSubConstraintKey'), 'rootSubConstraintKey' );
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$s_uuid = $o_tableTwig->UUID;
		
		if ( ($o_glob->Temp->Exists('rootSubConstraintKey')) && ($o_glob->Temp->{'rootSubConstraintKey'} != null) ) {
			$s_uuid = $o_glob->Temp->{'rootSubConstraintKey'};
		}
		
		$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $s_uuid, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$this->MoveUpRecord();
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		$s_nextAction = 'viewTwig';
		
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
	
	/* handle action to change order of twig tablefield records, moving one record down */
	protected function moveDownTwigFieldAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new tablefieldTwig;
		$o_tableTwig = new tableTwig;
		
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'rootSubConstraintKey'), 'rootSubConstraintKey' );
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$s_uuid = $o_tableTwig->UUID;
		
		if ( ($o_glob->Temp->Exists('rootSubConstraintKey')) && ($o_glob->Temp->{'rootSubConstraintKey'} != null) ) {
			$s_uuid = $o_glob->Temp->{'rootSubConstraintKey'};
		}
		
		$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $s_uuid, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$this->MoveDownRecord();
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		$s_nextAction = 'viewTwig';
		
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

	/* handle view twig tablefield record action */
	protected function viewTwigFieldAction() {
		$o_glob = forestGlobals::init();
		$o_saveTwig = $this->Twig;
		$this->Twig = new tablefieldTwig;
		$o_tableTwig = new tableTwig;
			
		/* query twig record if we have view key in url parameters */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'viewKey'), 'viewKey' );
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'rootSubConstraintKey'), 'rootSubConstraintKey' );
		
		if ( ($o_glob->Temp->Exists('viewKey')) && ($o_glob->Temp->{'viewKey'} != null) ) {
			/* query record */
			if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'viewKey'}))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}
			
			/* create modal read only form */
			$o_glob->PostModalForm = new forestForm($this->Twig, true, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $this->Twig->FieldName . ' Tablefield';
			
			/* delete Order-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('readonly_sys_fphp_tablefield_Order')) {
				throw new forestException('Cannot delete form element with Id[readonly_sys_fphp_tablefield_Order].');
			}
			
			if (!( ($o_glob->Temp->Exists('rootSubConstraintKey')) && ($o_glob->Temp->{'rootSubConstraintKey'} != null) )) {
				/* delete SubRecordField-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('readonly_sys_fphp_tablefield_SubRecordField')) {
					throw new forestException('Cannot delete form element with Id[readonly_sys_fphp_tablefield_SubRecordField].');
				}
			} else {
				/* query sub constraint record */
				$o_subconstraintTwig = new subconstraintTwig;
				
				if (! ($o_subconstraintTwig->GetRecord(array($o_glob->Temp->{'rootSubConstraintKey'}))) ) {
					throw new forestException(0x10001401, array($o_subconstraintTwig->fphp_Table));
				}
			}
			
			$s_subFormItems = '';

			/* ************************************************** */
			/* *****************VALIDATION RULES***************** */
			/* ************************************************** */
			/* look for tablefield validation rules */
			$o_tablefield_validationruleTwig = new tablefield_validationruleTwig;
			
			$a_sqlAdditionalFilter = array(array('column' => 'TablefieldUUID', 'value' => $this->Twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_tablefield_validationrules = $o_tablefield_validationruleTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			/* ************************* */
			/* ***********HEAD********** */
			/* ************************* */
			
			$s_subTableHead = '';
			
			foreach ($o_tablefield_validationruleTwig->fphp_View as $s_columnHead) {
				$s_columnHead = $o_glob->GetTranslation('form' . $s_columnHead . 'Label', 0);
				
				if (forestStringLib::EndsWith($s_columnHead, ':')) {
					$s_columnHead = substr($s_columnHead, 0, -1);
				}
				
				$s_subTableHead .= '<th>' . $s_columnHead . '</th>' . "\n";
			}
			
			$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
			
			/* ************************* */
			/* ***********ROWS********** */
			/* ************************* */
			
			$s_subTableRows = '';
			
			foreach ($o_tablefield_validationrules->Twigs as $o_tablefield_validationrule) {
				/* render records based on twig view columns */
				$s_subTableRows .=  '<tr>' . "\n";
				
				foreach ($o_tablefield_validationruleTwig->fphp_View as $s_column) {
					$s_formElement = '';
		
					if ($o_glob->TablefieldsDictionary->Exists($o_tablefield_validationrule->fphp_Table . '_' . $s_column)) {
						$s_formElement = $o_glob->TablefieldsDictionary->{$o_tablefield_validationrule->fphp_Table . '_' . $s_column}->FormElementName;
					}
					
					$s_value = '-';
					$this->ListViewRenderColumnValue($s_value, $s_formElement, $s_column, $o_tablefield_validationrule, $o_tablefield_validationruleTwig->fphp_Table . '_' . $s_column);
					$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
				}
				
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
				$a_parameters['viewKey'] = $this->Twig->UUID;
				$a_parameters['editKey'] = $o_tablefield_validationrule->UUID;
				
				if ($o_glob->Security->CheckUserPermission(null, 'editValidationRule')) {
					$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'editValidationRule', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="glyphicon glyphicon-pencil"></span></a>' . "\n";
				}

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
				$a_parameters['viewKey'] = $this->Twig->UUID;
				$a_parameters['deleteKey'] = $o_tablefield_validationrule->UUID;
				
				if ($o_glob->Security->CheckUserPermission(null, 'deleteValidationRule')) {
					$s_options .= '<a href="' . forestLink::Link($o_glob->URL->Branch, 'deleteValidationRule', $a_parameters) . '" class="btn btn-default" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="glyphicon glyphicon-trash text-danger"></span></a>' . "\n";
				}

				$s_options .= '</span>' . "\n";
				$s_subTableRows .=  '<td>' . $s_options . '</td>' . "\n";
				
				$s_subTableRows .=  '</tr>' . "\n";
			}
			
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
			$a_parameters['viewKey'] = $this->Twig->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'newValidationRule')) {
				$s_newButton = '<a href="' . forestLink::Link($o_glob->URL->Branch, 'newValidationRule', $a_parameters) . '" class="btn btn-default" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="glyphicon glyphicon-plus text-success"></span></a>' . "\n";
			} else {
				$s_newButton = '';
			}

			$s_subFormItemContent = new forestTemplates(forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
			$s_subFormItems .= new forestTemplates(forestTemplates::SUBLISTVIEWITEM, array('tablefield_validationrules' . $this->Twig->fphp_Table, $o_glob->GetTranslation('ValidationRules', 0), ' in', $s_subFormItemContent));
			
			/* go back link */
			$s_goBackButton = '<a href="' . forestLink::Link($o_glob->URL->Branch, 'viewTwig') . '" class="btn btn-lg btn-primary" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnBack', 1) . '"><span class="glyphicon glyphicon-arrow-left"></span></a><br>' . "\n";
			
			/* add sub constraints and files for modal form */
			$o_glob->PostModalForm->FormModalSubForm = $s_goBackButton . strval(new forestTemplates(forestTemplates::SUBLISTVIEW, array($s_subFormItems)));
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction('init');
	}
	
	
	/* handle new translation record action for twig */
	protected function newTranslationAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new translationTwig;
		$o_tableTwig = new tableTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			/* add new branch record form */
			$o_glob->PostModalForm = new forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
			
			/* delete BranchId-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_translation_BranchId')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_translation_BranchId].');
			}
		} else {
			/* check posted data for new tablefield record */
			$this->TransferPOST_Twig();
			
			/* add BranchId value to record */
			$this->Twig->BranchId = $o_glob->URL->BranchId;
			
			/* insert record */
			$i_result = $this->Twig->InsertRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				throw new forestException(0x10001402);
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001404));
				$s_nextAction = 'viewTwig';
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

	/* handle edit translation record action for twig */
	protected function editTranslationAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new translationTwig;
		$o_tableTwig = new tableTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if ($o_glob->IsPost) {
			/* query record */
			if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_translationKey']))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}
			
			$this->TransferPOST_Twig();
			
			/* add BranchId value to record */
			$this->Twig->BranchId = $o_glob->URL->BranchId;
			
			/* edit record */
			$i_result = $this->Twig->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				$o_glob->SystemMessages->Add(new forestException(0x10001406));
				$s_nextAction = 'viewTwig';
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001407));
				$s_nextAction = 'viewTwig';
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
				
				/* add current record key to modal form as hidden field */
				$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_translationKey';
				$o_hidden->Value = strval($this->Twig->UUID);
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
				
				/* delete BranchId-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_translation_BranchId')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_translation_BranchId].');
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
	
	/* handle delete translation record action for twig */
	protected function deleteTranslationAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new translationTwig;
		$o_tableTwig = new tableTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
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
				$o_hidden->Id = 'sys_fphp_translationKey';
				$o_hidden->Value = strval($this->Twig->UUID);
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
			}
		} else {
			/* delete record */
			if (array_key_exists('sys_fphp_translationKey', $_POST)) {
				if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_translationKey']))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* delete record */
				$i_return = $this->Twig->DeleteRecord();
				
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
				
				$o_glob->SystemMessages->Add(new forestException(0x10001427));
				$s_nextAction = 'viewTwig';
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


	/* handle new unique record action for twig */
	protected function newUniqueAction() {
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
			$this->Twig->Unique->SetLookupData(new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID)));
			$this->Twig->Unique->PrimaryValue = 'NULL';
			$this->Twig->SortOrder->SetLookupData(new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID)));
			$this->Twig->View->SetLookupData(new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID)));
			$this->Twig->SortColumn->SetLookupData($o_forestLookupData);
			
			/* add new unique record form */
			$o_glob->PostModalForm = new forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
			
			/* create manual form element for unique key name */
			$o_text = new forestFormElement(forestFormElement::TEXT);
			$o_text->Label = $o_glob->GetTranslation('rootUniqueNameLabel', 0);
			$o_text->Id = 'sys_fphp_table_uniqueName';
			$o_text->ValMessage = $o_glob->GetTranslation('rootUniqueNameValMessage', 0);
			$o_text->Placeholder = $o_glob->GetTranslation('rootUniqueNamePlaceholder', 0);
			
			/* add manual created form element to genereal tab */
			if (!$o_glob->PostModalForm->AddFormElement($o_text, 'general', true)) {
				throw new forestException('Cannot add form element to tab with id[general].');
			}
			
			/* add validation rules for manual created form elements */
			$o_glob->PostModalForm->FormObject->ValRules->Add(new forestFormValidationRule('sys_fphp_table_uniqueName', 'required', 'true'));
			
			/* delete SortOrder-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_SortOrder')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_SortOrder].');
			}
			
			/* delete Interval-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Interval')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_Interval].');
			}
			
			/* delete View-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_View[]')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_View[]].');
			}
			
			/* delete SortColumn-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_SortColumn')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_SortColumn].');
			}
		} else {
			$s_unique = '';
			
			if (array_key_exists($this->Twig->fphp_Table . '_uniqueName', $_POST)) {
				$s_unique = $_POST[$this->Twig->fphp_Table . '_uniqueName'] . '=';
			} else {
				throw new forestException(0x10001402);
			}
			
			if (is_array($_POST[$this->Twig->fphp_Table . '_Unique'])) {
				/* post value is array, so we need to valiate multiple selected items */
				$s_sum = '';
				
				foreach ($_POST[$this->Twig->fphp_Table . '_Unique'] as $s_selectOptValue) {
					$s_sum .= strval($s_selectOptValue) . ';';
				}
				
				$s_sum = substr($s_sum, 0, -1);
				$s_unique .= $s_sum;
			} else {
				$s_unique .= strval($_POST[$this->Twig->fphp_Table . '_Unique']);
			}
			
			if (issetStr($this->Twig->Unique->PrimaryValue)) {
				$this->Twig->Unique = $this->Twig->Unique->PrimaryValue . ':' . $s_unique;
			} else {
				$this->Twig->Unique = $s_unique;
			}
			
			/* edit recrod */
			$i_result = $this->Twig->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				$o_glob->SystemMessages->Add(new forestException(0x10001406));
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001404));
				$s_nextAction = 'viewTwig';
				
				/* update twig file */
				$this->doTwigFile($this->Twig);
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

	/* handle edit unique record action for twig */
	protected function editUniqueAction() {
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
		
		if ($o_glob->IsPost) {
			$s_unique = '';
		
			if (array_key_exists($this->Twig->fphp_Table . '_uniqueName', $_POST)) {
				$s_unique = $_POST[$this->Twig->fphp_Table . '_uniqueName'] . '=';
			} else {
				throw new forestException(0x10001402);
			}
			
			if (is_array($_POST[$this->Twig->fphp_Table . '_Unique'])) {
				/* post value is array, so we need to validate multiple selected items */
				$s_sum = '';
				
				foreach ($_POST[$this->Twig->fphp_Table . '_Unique'] as $s_selectOptValue) {
					$s_sum .= strval($s_selectOptValue) . ';';
				}
				
				$s_sum = substr($s_sum, 0, -1);
				$s_unique .= $s_sum;
			} else {
				$s_unique .= strval($_POST[$this->Twig->fphp_Table . '_Unique']);
			}
			
			if (issetStr($this->Twig->Unique->PrimaryValue)) {
				$a_uniqueKeys = explode(':', $this->Twig->Unique->PrimaryValue);
				
				if ( (!array_key_exists('sys_fphp_uniqueKey', $_POST)) || ($_POST['sys_fphp_uniqueKey'] >= count($a_uniqueKeys)) ) {
					throw new forestException(0x10001405, array($this->Twig->fphp_Table));
				}
				
				$a_uniqueKeys[intval($_POST['sys_fphp_uniqueKey'])] = $s_unique;
				$this->Twig->Unique = implode(':', $a_uniqueKeys);
			} else {
				$this->Twig->Unique = $s_unique;
			}
			
			/* edit record */
			$i_result = $this->Twig->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				$o_glob->SystemMessages->Add(new forestException(0x10001406));
				$s_nextAction = 'viewTwig';
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001407));
				$s_nextAction = 'viewTwig';
				
				/* update twig file */
				$this->doTwigFile($this->Twig);
			}
		} else {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editKey'), 'editKey' );
		
			if ( ($o_glob->Temp->Exists('editKey')) && ($o_glob->Temp->{'editKey'} != null) ) {
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
				$this->Twig->Unique->SetLookupData(new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID)));
				$this->Twig->SortOrder->SetLookupData(new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID)));
				$this->Twig->View->SetLookupData(new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID)));
				$this->Twig->SortColumn->SetLookupData($o_forestLookupData);
				
				/* get value */
				$a_uniqueKeys = explode(':', $this->Twig->Unique->PrimaryValue);
				
				if ($o_glob->Temp->{'editKey'} >= count($a_uniqueKeys)) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				$o_uniqueKey = explode('=', $a_uniqueKeys[intval($o_glob->Temp->{'editKey'})]);
				
				$s_name = $o_uniqueKey[0];
				$this->Twig->Unique->PrimaryValue = $o_uniqueKey[1];
				
				/* build modal form */
				$o_glob->PostModalForm = new forestForm($this->Twig, true);
				$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('EditModalTitle', 1);
				
				/* add current record key to modal form as hidden field */
				$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_uniqueKey';
				$o_hidden->Value = strval($o_glob->Temp->{'editKey'});
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
				
				/* create manual form element for unique key name */
				$o_text = new forestFormElement(forestFormElement::TEXT);
				$o_text->Label = $o_glob->GetTranslation('rootUniqueNameLabel', 0);
				$o_text->Id = 'sys_fphp_table_uniqueName';
				$o_text->ValMessage = $o_glob->GetTranslation('rootUniqueNameValMessage', 0);
				$o_text->Placeholder = $o_glob->GetTranslation('rootUniqueNamePlaceholder', 0);
				$o_text->Value = $s_name;
				
				/* add manual created form element to genereal tab */
				if (!$o_glob->PostModalForm->AddFormElement($o_text, 'general', true)) {
					throw new forestException('Cannot add form element to tab with id[general].');
				}
				
				/* add validation rules for manual created form elements */
				$o_glob->PostModalForm->FormObject->ValRules->Add(new forestFormValidationRule('sys_fphp_table_uniqueName', 'required', 'true'));
				
				/* delete SortOrder-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_SortOrder')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_SortOrder].');
				}
				
				/* delete Interval-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Interval')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_Interval].');
				}
				
				/* delete View-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_View[]')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_View[]].');
				}
				
				/* delete SortColumn-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_SortColumn')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_SortColumn].');
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
	
	/* handle delete unique record action for twig */
	protected function deleteUniqueAction() {
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
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteKey'), 'deleteKey' );
		
			if ( ($o_glob->Temp->Exists('deleteKey')) && ($o_glob->Temp->{'deleteKey'} != null) ) {
				/* create modal confirmation form for deleting record */
				$o_glob->PostModalForm = new forestForm($this->Twig);
				$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
				$s_description = '<b>' . $o_glob->GetTranslation('DeleteModalDescriptionOne', 1) . '</b>';
				$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);
				
				$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_uniqueKey';
				$o_hidden->Value = strval($o_glob->Temp->{'deleteKey'});
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
			}
		} else {
			/* delete record */
			if (array_key_exists('sys_fphp_uniqueKey', $_POST)) {
				if (issetStr($this->Twig->Unique->PrimaryValue)) {
					$a_uniqueKeys = explode(':', $this->Twig->Unique->PrimaryValue);
					
					if ( (!array_key_exists('sys_fphp_uniqueKey', $_POST)) || ($_POST['sys_fphp_uniqueKey'] >= count($a_uniqueKeys)) ) {
						throw new forestException(0x10001405, array($this->Twig->fphp_Table));
					}
					
					unset($a_uniqueKeys[intval($_POST['sys_fphp_uniqueKey'])]);
					
					if (count($a_uniqueKeys) > 0) {
						$this->Twig->Unique = implode(':', $a_uniqueKeys);
					} else {
						$this->Twig->Unique = 'NULL';
					}
				} else {
					$this->Twig->Unique = 'NULL';
				}
				
				/* edit record */
				$i_return = $this->Twig->UpdateRecord();
				
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
				
				$o_glob->SystemMessages->Add(new forestException(0x10001427));
				$s_nextAction = 'viewTwig';
				
				/* update twig file */
				$this->doTwigFile($this->Twig);
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


	/* handle new sort order record action for twig */
	protected function newSortAction() {
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
			$this->Twig->Unique->SetLookupData(new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID)));
			$this->Twig->SortOrder->SetLookupData(new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID)));
			$this->Twig->SortOrder->PrimaryValue = 'NULL';
			$this->Twig->View->SetLookupData(new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID)));
			$this->Twig->SortColumn->SetLookupData($o_forestLookupData);
			
			/* add new sort order record form */
			$o_glob->PostModalForm = new forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
			
			/* create manual form element for unique key name */
			$o_select = new forestFormElement(forestFormElement::SELECT);
			$o_select->Label = $o_glob->GetTranslation('rootSortDirectionLabel', 0);
			$o_select->Id = 'sys_fphp_table_sortDirection';
			$o_select->ValMessage = $o_glob->GetTranslation('rootSortDirectionValMessage', 0);
			$o_select->Options = array($o_glob->GetTranslation('rootSortDirectionAscending', 0) => 'true', $o_glob->GetTranslation('rootSortDirectionDescending', 0) => 'false');
			$o_select->Required = true;
			
			/* add manual created form element to genereal tab */
			if (!$o_glob->PostModalForm->AddFormElement($o_select, 'general', true)) {
				throw new forestException('Cannot add form element to tab with id[general].');
			}
			
			/* add validation rules for manual created form elements */
			$o_glob->PostModalForm->FormObject->ValRules->Add(new forestFormValidationRule('sys_fphp_table_sortDirection', 'required', 'true'));
			
			/* delete Unique-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Unique[]')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_Unique[]].');
			}
			
			/* delete Interval-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Interval')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_Interval].');
			}
			
			/* delete View-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_View[]')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_View[]].');
			}
			
			/* delete SortColumn-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_SortColumn')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_SortColumn].');
			}
		} else {
			$s_sortOrder = '';
			
			if (array_key_exists($this->Twig->fphp_Table . '_sortDirection', $_POST)) {
				$s_sortOrder = strval($_POST[$this->Twig->fphp_Table . '_sortDirection']) . ';';
			} else {
				throw new forestException(0x10001402);
			}
			
			if (is_array($_POST[$this->Twig->fphp_Table . '_SortOrder'])) {
				/* post value is array, so we need to valiate multiple selected items */
				$s_sum = '';
				
				foreach ($_POST[$this->Twig->fphp_Table . '_SortOrder'] as $s_selectOptValue) {
					$s_sum .= strval($s_selectOptValue) . ';';
				}
				
				$s_sum = substr($s_sum, 0, -1);
				$s_sortOrder .= $s_sum;
			} else {
				$s_sortOrder .= strval($_POST[$this->Twig->fphp_Table . '_SortOrder']);
			}
			
			if (issetStr($this->Twig->SortOrder->PrimaryValue)) {
				$this->Twig->SortOrder = $this->Twig->SortOrder->PrimaryValue . ':' . $s_sortOrder;
			} else {
				$this->Twig->SortOrder = $s_sortOrder;
			}
			
			/* edit recrod */
			$i_result = $this->Twig->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				$o_glob->SystemMessages->Add(new forestException(0x10001406));
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001404));
				$s_nextAction = 'viewTwig';
				
				/* update twig file */
				$this->doTwigFile($this->Twig);
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

	/* handle edit sort order record action for twig */
	protected function editSortAction() {
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
		
		if ($o_glob->IsPost) {
			$s_sortOrder = '';
		
			if (array_key_exists($this->Twig->fphp_Table . '_sortDirection', $_POST)) {
				$s_sortOrder = $_POST[$this->Twig->fphp_Table . '_sortDirection'] . ';';
			} else {
				throw new forestException(0x10001402);
			}
			
			if (is_array($_POST[$this->Twig->fphp_Table . '_SortOrder'])) {
				/* post value is array, so we need to validate multiple selected items */
				$s_sum = '';
				
				foreach ($_POST[$this->Twig->fphp_Table . '_SortOrder'] as $s_selectOptValue) {
					$s_sum .= strval($s_selectOptValue) . ';';
				}
				
				$s_sum = substr($s_sum, 0, -1);
				$s_sortOrder .= $s_sum;
			} else {
				$s_sortOrder .= strval($_POST[$this->Twig->fphp_Table . '_SortOrder']);
			}
			
			if (issetStr($this->Twig->SortOrder->PrimaryValue)) {
				$a_sortOrders = explode(':', $this->Twig->SortOrder->PrimaryValue);
				
				if ( (!array_key_exists('sys_fphp_sortOrderKey', $_POST)) || ($_POST['sys_fphp_sortOrderKey'] >= count($a_sortOrders)) ) {
					throw new forestException(0x10001405, array($this->Twig->fphp_Table));
				}
				
				$a_sortOrders[intval($_POST['sys_fphp_sortOrderKey'])] = $s_sortOrder;
				$this->Twig->SortOrder = implode(':', $a_sortOrders);
			} else {
				$this->Twig->SortOrder = $s_sortOrder;
			}
			
			/* edit record */
			$i_result = $this->Twig->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				$o_glob->SystemMessages->Add(new forestException(0x10001406));
				$s_nextAction = 'viewTwig';
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001407));
				$s_nextAction = 'viewTwig';
				
				/* update twig file */
				$this->doTwigFile($this->Twig);
			}
		} else {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editKey'), 'editKey' );
		
			if ( ($o_glob->Temp->Exists('editKey')) && ($o_glob->Temp->{'editKey'} != null) ) {
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
				$this->Twig->Unique->SetLookupData(new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID)));
				$this->Twig->SortOrder->SetLookupData(new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID)));
				$this->Twig->View->SetLookupData(new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID)));
				$this->Twig->SortColumn->SetLookupData($o_forestLookupData);
				
				/* get value */
				$a_sortOrders = explode(':', $this->Twig->SortOrder->PrimaryValue);
				
				if ($o_glob->Temp->{'editKey'} >= count($a_sortOrders)) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				$o_sortOrder = explode(';', $a_sortOrders[intval($o_glob->Temp->{'editKey'})]);
				
				$s_sortDirection = strval($o_sortOrder[0]);
				$this->Twig->SortOrder->PrimaryValue = $o_sortOrder[1];
				
				/* build modal form */
				$o_glob->PostModalForm = new forestForm($this->Twig, true);
				$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('EditModalTitle', 1);
				
				/* add current record key to modal form as hidden field */
				$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_sortOrderKey';
				$o_hidden->Value = strval($o_glob->Temp->{'editKey'});
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
				
				/* create manual form element for unique key name */
				$o_select = new forestFormElement(forestFormElement::SELECT);
				$o_select->Label = $o_glob->GetTranslation('rootSortDirectionLabel', 0);
				$o_select->Id = 'sys_fphp_table_sortDirection';
				$o_select->ValMessage = $o_glob->GetTranslation('rootSortDirectionValMessage', 0);
				$o_select->Options = array($o_glob->GetTranslation('rootSortDirectionAscending', 0) => 'true', $o_glob->GetTranslation('rootSortDirectionDescending', 0) => 'false');
				$o_select->Required = true;
				$o_select->Value = $s_sortDirection;
				
				/* add manual created form element to genereal tab */
				if (!$o_glob->PostModalForm->AddFormElement($o_select, 'general', true)) {
					throw new forestException('Cannot add form element to tab with id[general].');
				}
				
				/* add validation rules for manual created form elements */
				$o_glob->PostModalForm->FormObject->ValRules->Add(new forestFormValidationRule('sys_fphp_table_sortDirection', 'required', 'true'));
				
				/* delete SortOrder-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Unique[]')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_Unique[]].');
				}
				
				/* delete Interval-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Interval')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_Interval].');
				}
				
				/* delete View-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_View[]')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_View[]].');
				}
				
				/* delete SortColumn-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_SortColumn')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_SortColumn].');
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
	
	/* handle delete sort order record action for twig */
	protected function deleteSortAction() {
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
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteKey'), 'deleteKey' );
		
			if ( ($o_glob->Temp->Exists('deleteKey')) && ($o_glob->Temp->{'deleteKey'} != null) ) {
				/* create modal confirmation form for deleting record */
				$o_glob->PostModalForm = new forestForm($this->Twig);
				$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
				$s_description = '<b>' . $o_glob->GetTranslation('DeleteModalDescriptionOne', 1) . '</b>';
				$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);
					
				$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_sortOrderKey';
				$o_hidden->Value = strval($o_glob->Temp->{'deleteKey'});
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
			}
		} else {
			/* delete record */
			if (array_key_exists('sys_fphp_sortOrderKey', $_POST)) {
				if (issetStr($this->Twig->SortOrder->PrimaryValue)) {
					$a_sortOrders = explode(':', $this->Twig->SortOrder->PrimaryValue);
					
					if ( (!array_key_exists('sys_fphp_sortOrderKey', $_POST)) || ($_POST['sys_fphp_sortOrderKey'] >= count($a_sortOrders)) ) {
						throw new forestException(0x10001405, array($this->Twig->fphp_Table));
					}
					
					unset($a_sortOrders[intval($_POST['sys_fphp_sortOrderKey'])]);
					
					if (count($a_sortOrders) > 0) {
						$this->Twig->SortOrder = implode(':', $a_sortOrders);
					} else {
						$this->Twig->SortOrder = 'NULL';
					}
				} else {
					$this->Twig->SortOrder = 'NULL';
				}
				
				/* edit record */
				$i_return = $this->Twig->UpdateRecord();
				
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
				
				$o_glob->SystemMessages->Add(new forestException(0x10001427));
				$s_nextAction = 'viewTwig';
				
				/* update twig file */
				$this->doTwigFile($this->Twig);
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


	/* handle new twig sub constraint record action */
	protected function newSubConstraintAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new subconstraintTwig;
		$o_tableTwig = new tableTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			/* add new branch record form */
			$o_glob->PostModalForm = new forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
			
			/* delete TableUUID-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_subconstraint_TableUUID')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_subconstraint_TableUUID].');
			}
			
			/* delete View-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_subconstraint_View[]')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_subconstraint_View[]].');
			}
			
			/* delete Order-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_subconstraint_Order')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_subconstraint_Order].');
			}
		} else {
			/* check posted data for new sub constraint record */
			$this->TransferPOST_Twig();
			
			/* add TableUUID value to record */
			$this->Twig->TableUUID = $o_tableTwig->UUID;
			
			$i_order = 1;
			$o_subconstraintTwig = new subconstraintTwig;
			
			$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $o_tableTwig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			if ($o_subconstraintTwig->GetLastRecord()) {
				$i_order = $o_subconstraintTwig->Order + 1;
			}
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			/* add Order value to record */
			$this->Twig->Order = $i_order;
			
			/* insert record */
			$i_result = $this->Twig->InsertRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				throw new forestException(0x10001402);
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001404));
				$s_nextAction = 'viewTwig';
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

	/* handle edit twig sub constraint record action */
	protected function editSubConstraintAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new subconstraintTwig;
		$o_tableTwig = new tableTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if ($o_glob->IsPost) {
			/* query record */
			if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_subconstraintKey']))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}
			
			/* query sub table record */
			$o_subTableTwig = new tableTwig;
			
			if (! ($o_subTableTwig->GetRecord(array($this->Twig->SubTableUUID->PrimaryValue))) ) {
				throw new forestException(0x10001401, array($o_subTableTwig->fphp_Table));
			} else {
				$this->TransferPOST_Twig();
				
				/* edit record */
				$i_result = $this->Twig->UpdateRecord();
				
				/* evaluate result */
				if ($i_result == -1) {
					throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
				} else if ($i_result == 0) {
					$o_glob->SystemMessages->Add(new forestException(0x10001406));
					$s_nextAction = 'viewTwig';
				} else if ($i_result == 1) {
					$o_glob->SystemMessages->Add(new forestException(0x10001407));
					$s_nextAction = 'viewTwig';
				}
			}
		} else {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editKey'), 'editKey' );
		
			if ( ($o_glob->Temp->Exists('editKey')) && ($o_glob->Temp->{'editKey'} != null) ) {
				/* query record */
				if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'editKey'}))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* query sub table record */
				$o_subTableTwig = new tableTwig;
				
				if (! ($o_subTableTwig->GetRecord(array($this->Twig->SubTableUUID->PrimaryValue))) ) {
					throw new forestException(0x10001401, array($o_subTableTwig->fphp_Table));
				}
				
				/* update lookup filter */
				$this->Twig->View->SetLookupData(new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->SubTableUUID->PrimaryValue)));
				
				/* build modal form */
				$o_glob->PostModalForm = new forestForm($this->Twig, true);
				$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('EditModalTitle', 1);
				
				/* add current record key to modal form as hidden field */
				$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_subconstraintKey';
				$o_hidden->Value = strval($this->Twig->UUID);
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
				
				/* delete TableUUID-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_subconstraint_TableUUID')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_subconstraint_TableUUID].');
				}
				
				/* delete SubTableUUID-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_subconstraint_SubTableUUID')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_subconstraint_SubTableUUID].');
				}
				
				/* delete Order-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_subconstraint_Order')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_subconstraint_Order].');
				}
				
				/* add current record order to modal form as hidden field */
				$o_hiddenOrder = new forestFormElement(forestFormElement::HIDDEN);
				$o_hiddenOrder->Id = 'sys_fphp_subconstraint_Order';
				$o_hiddenOrder->Value = strval($this->Twig->Order);
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hiddenOrder);
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
	
	/* handle action to change order of sub constraint records, moving one record up */
	protected function moveUpSubConstraintAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new subconstraintTwig;
		$o_tableTwig = new tableTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $o_tableTwig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$this->MoveUpRecord();
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		$s_nextAction = 'viewTwig';
		
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
	
	/* handle action to change order of sub constraint records, moving one record down */
	protected function moveDownSubConstraintAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new subconstraintTwig;
		$o_tableTwig = new tableTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $o_tableTwig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$this->MoveDownRecord();
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		$s_nextAction = 'viewTwig';
		
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
	
	/* handle delete twig sub constraint record action */
	protected function deleteSubConstraintAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new subconstraintTwig;
		$o_tableTwig = new tableTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
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
				$o_hidden->Id = 'sys_fphp_subconstraintKey';
				$o_hidden->Value = strval($this->Twig->UUID);
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
			}
		} else {
			/* delete record */
			if (array_key_exists('sys_fphp_subconstraintKey', $_POST)) {
				if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_subconstraintKey']))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
				
				/* check sub constraint relation before deletion */
				$this->checkSubConstraintBeforeDeletion($o_tableTwig, $this->Twig);
				
				/* query all head records of subconstraint */
				if (array_key_exists($this->Twig->TableUUID, $o_glob->TablesInformation)) {
					$s_headTable = $o_glob->TablesInformation[$this->Twig->TableUUID]['Name'];
					forestStringLib::RemoveTablePrefix($s_headTable);
					$s_foo = $s_headTable . 'Twig';
					$o_headTwig = new $s_foo;
					
					/* query records */
					$o_records = $o_headTwig->GetAllRecords(true);
					
					foreach ($o_records->Twigs as $o_record) {
						/* query sub records of current sub constraint for each record */
						$o_subRecords = $o_record->QuerySubRecords($this->Twig);
						
						foreach ($o_subRecords->Twigs as $o_subRecord) {
							/* delete files of sub record */
							$o_filesTwig = new filesTwig; 
							
							$a_sqlAdditionalFilter = array(array('column' => 'ForeignUUID', 'value' => $o_subRecord->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
							$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
							$o_files = $o_filesTwig->GetAllRecords(true);
							$o_glob->Temp->Del('SQLAdditionalFilter');
							
							foreach ($o_files->Twigs as $o_file) {
								$s_folder = substr(pathinfo($o_file->Name, PATHINFO_FILENAME), 6, 2);
									
								$s_path = '';
			
								if (count($o_glob->URL->Branches) > 0) {
									foreach($o_glob->URL->Branches as $s_value) {
										$s_path .= $s_value . '/';
									}
								}
								
								$s_path .= $o_glob->URL->Branch . '/';
								
								$s_path = './trunk/' . $s_path . 'fphp_files/' . $s_folder . '/';
								
								if (is_dir($s_path)) {
									if (file_exists($s_path . $o_file->Name)) {
										/* delete file */
										if (!(@unlink($s_path . $o_file->Name))) {
											throw new forestException(0x10001422, array($s_path . $o_file->Name));
										}
									}
								}
								
								/* delete file record */
								$i_return = $o_file->DeleteRecord();
								
								/* evaluate the result */
								if ($i_return <= 0) {
									throw new forestException(0x10001423);
								}
							}
							
							/* delete sub record */
							$i_return = $o_subRecord->DeleteRecord();
								
							/* evaluate the result */
							if ($i_return <= 0) {
								throw new forestException(0x10001423);
							}
						}
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
				
				/* delete record */
				$i_return = $this->Twig->DeleteRecord();
				
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
				
				$o_glob->SystemMessages->Add(new forestException(0x10001427));
				$s_nextAction = 'viewTwig';
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
	
	
	/* handle new twig validation rule record action */
	protected function newValidationRuleAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new tablefield_validationruleTwig;
		$o_tableTwig = new tableTwig;
		$o_tablefieldTwig = new tablefieldTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'viewKey'), 'viewKey' );
		
			if ( ($o_glob->Temp->Exists('viewKey')) && ($o_glob->Temp->{'viewKey'} != null) ) {
				/* query tablefield record */
				if (! ($o_tablefieldTwig->GetRecord(array($o_glob->Temp->{'viewKey'}))) ) {
					throw new forestException(0x10001401, array($o_tablefieldTwig->fphp_Table));
				}
				
				/* add new branch record form */
				$o_glob->PostModalForm = new forestForm($this->Twig, true);
				$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
				
				/* add tablefield record key to modal form as hidden field */
				$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_tablefieldKey';
				$o_hidden->Value = strval($o_tablefieldTwig->UUID);
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
			}
		} else {
			if (array_key_exists('sys_fphp_tablefieldKey', $_POST)) {
				/* query tablefield record */
				if (! ($o_tablefieldTwig->GetRecord(array($_POST['sys_fphp_tablefieldKey']))) ) {
					throw new forestException(0x10001401, array($o_tablefieldTwig->fphp_Table));
				}
				
				/* check posted data for new tablefield_validationrule record */
				$this->TransferPOST_Twig();
				
				if (issetStr($this->Twig->ValidationruleUUID->PrimaryValue)) {
					$o_validationruleTwig = new validationruleTwig;
					$o_formelement_validationruleTwig = new formelement_validationruleTwig;
					
					if (! ($o_validationruleTwig->GetRecordPrimary(array('any'), array('Name'))) ) {
						throw new forestException(0x10001401, array($o_validationruleTwig->fphp_Table));
					}
					
					if (! $o_formelement_validationruleTwig->GetRecord(array($o_tablefieldTwig->FormElementUUID->PrimaryValue, $o_validationruleTwig->UUID))) {
						if (! $o_formelement_validationruleTwig->GetRecord(array($o_tablefieldTwig->FormElementUUID->PrimaryValue, $this->Twig->ValidationruleUUID->PrimaryValue))) {
							throw new forestException(0x10001F0F, array($o_tablefieldTwig->FormElementUUID, $this->Twig->ValidationruleUUID));
						}
					}
				}
				
				/* add TableUUID value to record */
				$this->Twig->TablefieldUUID = $o_tablefieldTwig->UUID;
				
				/* insert record */
				$i_result = $this->Twig->InsertRecord();
				
				/* evaluate result */
				if ($i_result == -1) {
					throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
				} else if ($i_result == 0) {
					throw new forestException(0x10001402);
				} else if ($i_result == 1) {
					$o_glob->SystemMessages->Add(new forestException(0x10001404));
					$s_nextAction = 'viewTwigField';
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

	/* handle edit twig validation rule record action */
	protected function editValidationRuleAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new tablefield_validationruleTwig;
		$o_tableTwig = new tableTwig;
		$o_tablefieldTwig = new tablefieldTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if ($o_glob->IsPost) {
			/* query tablefield record */
			if (array_key_exists('sys_fphp_tablefieldKey', $_POST)) {
				if (! ($o_tablefieldTwig->GetRecord(array($_POST['sys_fphp_tablefieldKey']))) ) {
					throw new forestException(0x10001401, array($o_tablefieldTwig->fphp_Table));
				}
				
				/* query record */
				if (array_key_exists('sys_fphp_tablefield_validationruleKey', $_POST)) {
					if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_tablefield_validationruleKey']))) ) {
						throw new forestException(0x10001401, array($this->Twig->fphp_Table));
					}
					
					/* check posted data for tablefield_validationrule record */
					$this->TransferPOST_Twig();
					
					if (issetStr($this->Twig->ValidationruleUUID->PrimaryValue)) {
						$o_validationruleTwig = new validationruleTwig;
						$o_formelement_validationruleTwig = new formelement_validationruleTwig;
						
						if (! ($o_validationruleTwig->GetRecordPrimary(array('any'), array('Name'))) ) {
							throw new forestException(0x10001401, array($o_validationruleTwig->fphp_Table));
						}
						
						if (! $o_formelement_validationruleTwig->GetRecord(array($o_tablefieldTwig->FormElementUUID->PrimaryValue, $o_validationruleTwig->UUID))) {
							if (! $o_formelement_validationruleTwig->GetRecord(array($o_tablefieldTwig->FormElementUUID->PrimaryValue, $this->Twig->ValidationruleUUID->PrimaryValue))) {
								throw new forestException(0x10001F0F, array($o_tablefieldTwig->FormElementUUID, $this->Twig->ValidationruleUUID));
							}
						}
					}
					
					/* edit record */
					$i_result = $this->Twig->UpdateRecord();
					
					/* evaluate result */
					if ($i_result == -1) {
						throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
					} else if ($i_result == 0) {
						$o_glob->SystemMessages->Add(new forestException(0x10001406));
						$s_nextAction = 'viewTwigField';
					} else if ($i_result == 1) {
						$o_glob->SystemMessages->Add(new forestException(0x10001407));
						$s_nextAction = 'viewTwigField';
					}
				}
			}
		} else {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'viewKey'), 'viewKey' );
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editKey'), 'editKey' );
		
			if ( ($o_glob->Temp->Exists('editKey')) && ($o_glob->Temp->{'editKey'} != null) ) {
				/* query record */
				if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'editKey'}))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				if ( ($o_glob->Temp->Exists('viewKey')) && ($o_glob->Temp->{'viewKey'} != null) ) {
					/* query tablefield record */
					if (! ($o_tablefieldTwig->GetRecord(array($o_glob->Temp->{'viewKey'}))) ) {
						throw new forestException(0x10001401, array($o_tablefieldTwig->fphp_Table));
					}
					
					/* build modal form */
					$o_glob->PostModalForm = new forestForm($this->Twig, true);
					$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('EditModalTitle', 1);
					
					/* add tablefield record key to modal form as hidden field */
					$o_hidden = new forestFormElement(forestFormElement::HIDDEN);
					$o_hidden->Id = 'sys_fphp_tablefieldKey';
					$o_hidden->Value = strval($o_tablefieldTwig->UUID);
					
					$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
					
					/* add current record key to modal form as hidden field */
					$o_hidden2 = new forestFormElement(forestFormElement::HIDDEN);
					$o_hidden2->Id = 'sys_fphp_tablefield_validationruleKey';
					$o_hidden2->Value = strval($this->Twig->UUID);
					
					$o_glob->PostModalForm->FormFooterElements->Add($o_hidden2);
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
	
	/* handle delete twig validation rule record action */
	protected function deleteValidationRuleAction() {
		$o_glob = forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new tablefield_validationruleTwig;
		$o_tableTwig = new tableTwig;
		$o_tablefieldTwig = new tablefieldTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'viewKey'), 'viewKey' );
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteKey'), 'deleteKey' );
			
			if ( ($o_glob->Temp->Exists('viewKey')) && ($o_glob->Temp->{'viewKey'} != null) ) {
				if (! ($o_tablefieldTwig->GetRecord(array($o_glob->Temp->{'viewKey'}))) ) {
					throw new forestException(0x10001401, array($o_tablefieldTwig->fphp_Table));
				}
				
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
					$o_hidden->Id = 'sys_fphp_tablefieldKey';
					$o_hidden->Value = strval($o_tablefieldTwig->UUID);
					$o_glob->PostModalForm->FormElements->Add($o_hidden);
					
					$o_hidden2 = new forestFormElement(forestFormElement::HIDDEN);
					$o_hidden2->Id = 'sys_fphp_tablefield_validationruleKey';
					$o_hidden2->Value = strval($this->Twig->UUID);
					$o_glob->PostModalForm->FormElements->Add($o_hidden2);
				}
			}
		} else {
			if (array_key_exists('sys_fphp_tablefieldKey', $_POST)) {
				if (! ($o_tablefieldTwig->GetRecord(array($_POST['sys_fphp_tablefieldKey']))) ) {
					throw new forestException(0x10001401, array($o_tablefieldTwig->fphp_Table));
				}
				
				if (array_key_exists('sys_fphp_tablefield_validationruleKey', $_POST)) {
					if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_tablefield_validationruleKey']))) ) {
						throw new forestException(0x10001401, array($this->Twig->fphp_Table));
					}
					
					/* delete record */
					$i_return = $this->Twig->DeleteRecord();
					
					/* evaluate the result */
					if ($i_return <= 0) {
						throw new forestException(0x10001423);
					}
					
					$o_glob->SystemMessages->Add(new forestException(0x10001427));
					$s_nextAction = 'viewTwigField';
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
}
?>