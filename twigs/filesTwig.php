<?php
class filesTwig extends forestTwig {
	use forestData;
	
	/* Fields */
	
	private $Id;
	private $UUID;
	private $BranchId;
	private $ForeignUUID;
	private $Name;
	private $DisplayName;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->BranchId = new forestInt;
		$this->ForeignUUID = new forestString;
		$this->Name = new forestString;
		$this->DisplayName = new forestString;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_files';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID');
		$this->fphp_SortOrder->value->Add(true, 'Id');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('Id');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>