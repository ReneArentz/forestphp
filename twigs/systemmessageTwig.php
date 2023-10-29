<?php

namespace fPHP\Twigs;
use \fPHP\Roots\forestString as forestString;
use \fPHP\Roots\forestList as forestList;
use \fPHP\Roots\forestNumericString as forestNumericString;
use \fPHP\Roots\forestInt as forestInt;
use \fPHP\Roots\forestFloat as forestFloat;
use \fPHP\Roots\forestBool as forestBool;
use \fPHP\Roots\forestArray as forestArray;
use \fPHP\Roots\forestObject as forestObject;
use \fPHP\Roots\forestLookup as forestLookup;
use \fPHP\Helper\forestLookupData;

class systemmessageTwig extends forestTwig {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $Id;
	private $UUID;
	private $IdInternal;
	private $LanguageCode;
	private $Message;
	private $Type;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->IdInternal = new forestInt;
		$this->LanguageCode = new forestLookup(new forestLookupData('sys_fphp_language', array('UUID'), array('Language'), array(), ' - '));
		$this->Message = new forestString;
		$this->Type = new forestString;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_systemmessage';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID','IdInternal;LanguageCode');
		$this->fphp_SortOrder->value->Add(true, 'LanguageCode');
		$this->fphp_SortOrder->value->Add(true, 'IdInternal');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('IdInternal','LanguageCode','Message','Type');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>