<?php
class usergroup_userTwig extends forestTwig {
	use forestData;
	
	/* Fields */
	
	private $usergroupUUID;
	private $userUUID;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->usergroupUUID = new forestString;
		$this->userUUID = new forestString;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_usergroup_user';
		$this->fphp_Primary->value = array('usergroupUUID', 'userUUID');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('usergroupUUID', 'userUUID');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>