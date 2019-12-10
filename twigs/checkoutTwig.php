<?php
class checkoutTwig extends forestTwig {
	use forestData;
	
	/* Fields */
	
	private $Id;
	private $UUID;
	private $ForeignUUID;
	private $Timestamp;
	private $UserUUID;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->ForeignUUID = new forestString;
		$this->Timestamp = new forestObject('forestDateTime');
		$this->UserUUID = new forestString;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_checkout';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID', 'ForeignUUID');
		$this->fphp_SortOrder->value->Add(true, 'Id');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('Id');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>