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

class accountBranch extends forestBranch {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	protected function initBranch() {
		$this->Filter->value = false;
		$this->StandardView = forestBranch::DETAIL;
		$this->KeepFilter->value = false;
		
		$this->Twig = new \fPHP\Twigs\accountTwig();
	}
	
	protected function init() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if ($this->StandardView == forestBranch::DETAIL) {
			$a_sqlAdditionalFilter = array(array('column' => 'UUID', 'value' => $o_glob->Security->UserUUID, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$this->GenerateView();
			$o_glob->Temp->Del('SQLAdditionalFilter');
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
		$this->NewRecord();
	}
	
			protected function afterNewSubAction() {
				/* $this->Twig holds current sub record */
			}
	
		protected function afterNewAction() {
			/* $this->Twig holds current record */
		}
		
		protected function beforeEditAction() {
			/* $this->Twig holds current record */
			
			$o_glob = \fPHP\Roots\forestGlobals::init();
			
			/* get user record */
			$o_userTwig = new \fPHP\Twigs\userTwig;
			
			if (!$o_userTwig->GetRecord(array($o_glob->Security->UserUUID))) {
				throw new forestException(0x1000142E);
			}
			
			/* verify old password */
			if (!array_key_exists('sys_fphp_account_OldPassword', $_POST)) {
				throw new forestException(0x1000142E);
			}
			
			if (!password_verify($_POST['sys_fphp_account_OldPassword'], $o_userTwig->Password)) {
				throw new forestException(0x1000142E);
			}
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
			
			$o_glob = \fPHP\Roots\forestGlobals::init();
			
			/* get user record */
			$o_userTwig = new \fPHP\Twigs\userTwig;
			
			if (!$o_userTwig->GetRecord(array($o_glob->Security->UserUUID))) {
				throw new forestException(0x1000142E);
			}
			
			/* check if new password was send */
			if ( (array_key_exists('sys_fphp_account_Password', $_POST)) && (array_key_exists('sys_fphp_account_PasswordEqual', $_POST)) ) {
				if ($_POST['sys_fphp_account_Password'] == $_POST['sys_fphp_account_PasswordEqual']) {
					if (!empty($_POST['sys_fphp_account_PasswordEqual'])) {
						/* update user password */
						$o_userTwig->Password = password_hash(strval($_POST['sys_fphp_account_PasswordEqual']), PASSWORD_DEFAULT);
						
						/* edit user recrod */
						$i_result = $o_userTwig->UpdateRecord();
						
						/* evaluate result */
						if ($i_result == -1) {
							throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
						} else if ($i_result == 1) {
							$o_glob->SystemMessages->Add(new forestException(0x10001440));
						}
					}
				}
			}
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