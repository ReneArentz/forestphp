<?php
class translationBranch extends forestBranch {
	use forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	protected function initBranch() {
		$this->Filter->value = true;
		$this->StandardView = forestBranch::LIST;
		$this->KeepFilter->value = false;
		
		$this->Twig = new translationTwig();
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
	
	protected function newAction() {
		$this->NewRecord();
	}
	
		protected function afterNewAction() {
			/* $this->Twig holds current record */
		}
		
		protected function beforeEditAction() {
			/* $this->Twig holds current record */
		}
			
	protected function editAction() {
		$this->EditRecord();
	}
	
		protected function afterEditAction() {
			/* $this->Twig holds current record */
		}
		
		protected function beforeDeleteAction() {
			/* $this->Twig holds current record */
		}
		
	protected function deleteAction() {
		$this->DeleteRecord();
	}
	
		protected function afterDeleteAction() {
			/* $this->Twig holds current record */
		}
}
?>