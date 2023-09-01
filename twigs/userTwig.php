<?php

namespace fPHP\Twigs;
use \fPHP\Roots\{forestString, forestList, forestNumericString, forestInt, forestFloat, forestBool, forestArray, forestObject, forestLookup};
use \fPHP\Helper\forestLookupData;

class userTwig extends forestTwig {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $Id;
	private $UUID;
	private $Created;
	private $CreatedBy;
	private $Modified;
	private $ModifiedBy;
	private $User;
	private $Password;
	private $Locked;
	private $FailLogin;
	private $RootUser;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->Created = new forestObject('forestDateTime');
		$this->CreatedBy = new forestString;
		$this->Modified = new forestObject('forestDateTime');
		$this->ModifiedBy = new forestString;
		$this->User = new forestString;
		$this->Password = new forestString;
		$this->Locked = new forestBool;
		$this->FailLogin = new forestInt;
		$this->RootUser = new forestBool;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_user';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID','User');
		$this->fphp_SortOrder->value->Add(true, 'User');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('User','Locked','FailLogin');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>