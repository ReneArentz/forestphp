<?php

namespace fPHP\Branches;
use \fPHP\Roots\{forestString, forestList, forestNumericString, forestInt, forestFloat, forestBool, forestArray, forestObject, forestLookup};

class languageBranch extends forestBranch {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	protected function initBranch() {
		$this->Filter->value = true;
		$this->StandardView = forestBranch::LIST;
		$this->KeepFilter->value = false;
		
		$this->Twig = new \fPHP\Twigs\languageTwig();
	}
	
	protected function init() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if ($this->StandardView == forestBranch::DETAIL) {
			$this->GenerateView();
		} else if ($this->StandardView == forestBranch::LIST) {
			$this->GenerateListView();
		} else if ($this->StandardView == forestBranch::FLEX) {
			if ( ($o_glob->Security->SessionData->Exists('lastView')) && ($o_glob->URL->LastBranchId == $o_glob->URL->BranchId) ) {
				if ($o_glob->Security->SessionData->{'lastView'} == forestBranch::LIST) {
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