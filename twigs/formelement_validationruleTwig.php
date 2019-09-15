<?php
class formelement_validationruleTwig extends forestTwig {
	use forestData;
	
	/* Fields */
	
	private $formelementUUID;
	private $validationruleUUID;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->formelementUUID = new forestString;
		$this->validationruleUUID = new forestString;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_formelement_validationrule';
		$this->fphp_Primary->value = array('formelementUUID', 'validationruleUUID');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('formelementUUID', 'validationruleUUID');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>