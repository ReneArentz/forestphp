<?php
class usergroupTwig extends forestTwig {
	use forestData;
	
	/* Fields */
	
	private $Id;
	private $UUID;
	private $Name;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->Name = new forestString;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_usergroup';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID','Name');
		$this->fphp_SortOrder->value->Add(true, 'Name');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('Name');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>