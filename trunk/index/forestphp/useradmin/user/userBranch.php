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

class userBranch extends forestBranch {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	protected function initBranch() {
		$this->Filter->value = true;
		$this->StandardView = forestBranch::LISTVIEW;
		$this->KeepFilter->value = false;
		
		$this->Twig = new \fPHP\Twigs\userTwig();
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
		
			/* delete membership records of user record */
			$o_usergroup_userTwig = new \fPHP\Twigs\usergroup_userTwig;
			
			$a_sqlAdditionalFilter = array(array('column' => 'userUUID', 'value' => $this->Twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_usergroup_users = $o_usergroup_userTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			foreach ($o_usergroup_users->Twigs as $o_usergroup_user) {
				/* delete membership record */
				$i_return = $o_usergroup_user->DeleteRecord();
				
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
			}
			
			/* delete account record of user record */
			$o_accountTwig = new \fPHP\Twigs\accountTwig;
			
			if ($o_accountTwig->GetRecord(array($this->Twig->UUID))) {
				/* delete membership record */
				$i_return = $o_accountTwig->DeleteRecord();
				
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
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