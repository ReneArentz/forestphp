<?php

namespace fPHP\Twigs;
use \fPHP\Roots\{forestString, forestList, forestNumericString, forestInt, forestFloat, forestBool, forestArray, forestObject, forestLookup};
use \fPHP\Helper\forestLookupData;

class accountTwig extends forestTwig {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $Id;
	private $UUID;
	private $Mail;
	private $LanguageCode;
	private $Joined;
	private $LastLogin;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->Mail = new forestString;
		$this->LanguageCode = new forestLookup(new forestLookupData('sys_fphp_language', array('UUID'), array('Language'), array(), ' - '));
		$this->Joined = new forestObject('forestDateTime');
		$this->LastLogin = new forestObject('forestDateTime');
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_account';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID');
		$this->fphp_SortOrder->value->Add(true, 'Id');
		$this->fphp_Interval->value = 1;
		$this->fphp_View->value = array('Mail','LanguageCode','Joined','LastLogin');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>