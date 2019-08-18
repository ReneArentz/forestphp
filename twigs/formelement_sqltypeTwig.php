<?php
class formelement_sqltypeTwig extends forestTwig {
	use forestData;
	
	/* Fields */
	
	private $formelementUUID;
	private $sqltypeUUID;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->formelementUUID = new forestString;
		$this->sqltypeUUID = new forestString;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_formelement_sqltype';
		$this->fphp_Primary->value = array('formelementUUID', 'sqltypeUUID');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('formelementUUID', 'sqltypeUUID');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>