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

class identifierTwig extends forestTwig {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $Id;
	private $UUID;
	private $IdentifierName;
	private $IdentifierStart;
	private $IdentifierNext;
	private $IdentifierIncrement;
	private $UseAsSortColumn;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->IdentifierName = new forestString;
		$this->IdentifierStart = new forestString;
		$this->IdentifierNext = new forestString;
		$this->IdentifierIncrement = new forestInt;
		$this->UseAsSortColumn = new forestBool;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_identifier';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID','IdentifierName');
		$this->fphp_SortOrder->value->Add(true, 'IdentifierName');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('IdentifierName','IdentifierStart','IdentifierNext','IdentifierIncrement','UseAsSortColumn');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>