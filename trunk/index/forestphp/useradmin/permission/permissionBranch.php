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

class permissionBranch extends forestBranch {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	protected function initBranch() {
		$this->Filter->value = true;
		$this->StandardView = forestBranch::LISTVIEW;
		$this->KeepFilter->value = false;
		
		$this->Twig = new \fPHP\Twigs\permissionTwig();
	}
	
	protected function init() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if ($this->StandardView == forestBranch::DETAIL) {
			$this->GenerateView();
		} else if ($this->StandardView == forestBranch::LISTVIEW) {
			$this->GenerateListView();
		} else if ($this->StandardView == forestBranch::FLEX) {
			if ( ($o_glob->Security->SessionData->Exists('lastView')) && ($o_glob->URL->LastBranchId == $o_glob->URL->BranchId) ) {
				if ($o_glob->Security->SessionData->{'lastView'} == forestBranch::LISTVIEW) {
					$this->GenerateView();
				} else if ($o_glob->Security->SessionData->{'lastView'} == forestBranch::DETAIL) {
					$this->GenerateListView();
				} else {
					$this->GenerateFlexView();
				}
			} else {
				$this->GenerateFlexView();
			}
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
		
		if (!$o_glob->IsPost) {
			$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
			
			/* add new record - step zero */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
			
			/* add step flag to modal form */
			$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
			$o_hidden->Id = 'sys_fphp_step';
			$o_hidden->Value = 'one';
			
			$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
			
			/* delete Name-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_permission_Name')) {
				throw new \fPHP\Roots\forestException('Cannot delete form element with Id[sys_fphp_permission_Name].');
			}
			
			/* delete Action-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_permission_Action')) {
				throw new \fPHP\Roots\forestException('Cannot delete form element with Id[sys_fphp_permission_Action].');
			}
			
			/* add any branch to lookup element */
			$o_branchElement = $o_glob->PostModalForm->GetFormElementByFormId('sys_fphp_permission_Branch');
			
			if ($o_branchElement != null) {
				$a_options = $o_branchElement->Options;
				$a_options = array('Any' => 0) + $a_options;
				$o_branchElement->Options = $a_options;
				$o_branchElement->Value = 'Any';
			}
		} else {
			if ( (array_key_exists('sys_fphp_step', $_POST)) && ($_POST['sys_fphp_step'] == 'one') ) {
				$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form', true);
				
				/* check posted data for new sub record */
				$o_branchTwig = new \fPHP\Twigs\branchTwig;
				
				/* check if posted branch really exists */
				if (intval($_POST['sys_fphp_permission_Branch']) != 0) {
					if (! ($o_branchTwig->GetRecord(array($_POST['sys_fphp_permission_Branch']))) ) {
						throw new \fPHP\Roots\forestException(0x10001401, array($o_branchTwig->fphp_Table));
					}
				} else {
					$o_branchTwig->Id = 0;
					$o_branchTwig->Title = 'Any';
					$o_branchTwig->Name = 'any';
				}
				
				/* look for actions, if they are no actions for selected branch, throw warning */
				$o_actionTwig = new \fPHP\Twigs\actionTwig;
	
				$a_sqlAdditionalFilter = array(array('column' => 'BranchId', 'value' => $o_branchTwig->Id, 'operator' => '=', 'filterOperator' => 'AND'));
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$i_actions = $o_actionTwig->GetCount(null, true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				
				if ($i_actions <= 0) {
					throw new \fPHP\Roots\forestException(0x10001F11, array($o_branchTwig->Name));
				} else {
					/* update lookup form element with branch-id filter */
					$this->Twig->Action->SetLookupData(new \fPHP\Helper\forestLookupData('sys_fphp_action', array('Id'), array('Name'), array('BranchId' => $o_branchTwig->Id)));
				}
				
				/* add new record - step one */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
				$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
				
				/* add step flag to modal form */
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_step';
				$o_hidden->Value = 'submit';
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
				
				/* delete Branch-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_permission_Branch')) {
					throw new \fPHP\Roots\forestException('Cannot delete form element with Id[sys_fphp_permission_Branch].');
				}
				
				/* add hidden branch to modal form */
				$o_hiddenBranch = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hiddenBranch->Id = 'sys_fphp_permission_BranchTransfer';
				$o_hiddenBranch->Value = $_POST['sys_fphp_permission_Branch'];
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hiddenBranch);
				
				/* remove existing permission actions of lookup element */
				/* get actions */
				$o_permissionTwig = new \fPHP\Twigs\permissionTwig;
	
				$a_sqlAdditionalFilter = array(array('column' => 'Branch', 'value' => $o_branchTwig->Id, 'operator' => '=', 'filterOperator' => 'AND'));
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$o_permissions = $o_permissionTwig->GetAllRecords(true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				
				/* get action element */
				$o_actionElement = $o_glob->PostModalForm->GetFormElementByFormId('sys_fphp_permission_Action');
				
				if ($o_actionElement != null) {
					$a_options = $o_actionElement->Options;
					
					foreach ($a_options as $s_actionLabel => $s_actionId) {
						$b_found = false;
						
						foreach ($o_permissions->Twigs as $o_permission) {
							if (intval($o_permission->Action->PrimaryValue) == intval($s_actionId)) {
								$b_found = true;
								break;
							}
						}
						
						if ($b_found) {
							unset($a_options[$s_actionLabel]);
						}
					}
					
					if (count($a_options) <= 0) {
						throw new \fPHP\Roots\forestException(0x10001F11, array($o_branchTwig->Name));
					}
					
					$o_actionElement->Options = $a_options;
				}
			} else if ( (array_key_exists('sys_fphp_step', $_POST)) && ($_POST['sys_fphp_step'] == 'submit') ) {
				$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
				
				/* check posted data for new record */
				$this->TransferPOST_Twig();
				
				/* check posted data for new sub record */
				$o_branchTwig = new \fPHP\Twigs\branchTwig;
				
				/* check if posted branch really exists */
				if (intval($_POST['sys_fphp_permission_BranchTransfer']) != 0) {
					if (! ($o_branchTwig->GetRecord(array($_POST['sys_fphp_permission_BranchTransfer']))) ) {
						throw new \fPHP\Roots\forestException(0x10001401, array($o_branchTwig->fphp_Table));
					}
				}
				
				$this->Twig->Branch = $_POST['sys_fphp_permission_BranchTransfer'];
				
				/* check if posted action really exists */
				$o_actionTwig = new \fPHP\Twigs\actionTwig;
				
				if (! ($o_actionTwig->GetRecord(array($_POST['sys_fphp_permission_Action']))) ) {
					throw new \fPHP\Roots\forestException(0x10001401, array($o_actionTwig->fphp_Table));
				}
				
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
			
			$o_glob = \fPHP\Roots\forestGlobals::init();
		
			/* look for role_permission records */
			$o_role_permissionTwig = new \fPHP\Twigs\role_permissionTwig;
			
			$a_sqlAdditionalFilter = array(array('column' => 'permissionUUID', 'value' => $this->Twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
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
		}
		
			protected function beforeDeleteSubAction() {
				/* $this->Twig holds current sub record */
			}
			
				protected function beforeDeleteFileAction() {
					/* $this->Twig holds current file record */
				}
		
	protected function deleteAction() {
		$this->DeleteRecord();
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
}
?>