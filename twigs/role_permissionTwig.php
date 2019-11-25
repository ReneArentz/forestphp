<?php
class role_permissionTwig extends forestTwig {
	use forestData;
	
	/* Fields */
	
	private $roleUUID;
	private $permissionUUID;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->roleUUID = new forestString;
		$this->permissionUUID = new forestString;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_role_permission';
		$this->fphp_Primary->value = array('roleUUID', 'permissionUUID');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('roleUUID', 'permissionUUID');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>