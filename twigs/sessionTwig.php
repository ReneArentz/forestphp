<?php
class sessionTwig extends forestTwig {
	use forestData;
	
	/* Fields */
	
	private $Id;
	private $UUID;
	private $Timestamp;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->Timestamp = new forestObject('DateTime');
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_session';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID');
		$this->fphp_SortOrder->value->Add(false, 'Timestamp');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('Timestamp');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>