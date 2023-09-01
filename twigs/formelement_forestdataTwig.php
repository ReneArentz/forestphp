<?php

namespace fPHP\Twigs;
use \fPHP\Roots\{forestString, forestList, forestNumericString, forestInt, forestFloat, forestBool, forestArray, forestObject, forestLookup};
use \fPHP\Helper\forestLookupData;

class formelement_forestdataTwig extends forestTwig {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $formelementUUID;
	private $forestdataUUID;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->formelementUUID = new forestString;
		$this->forestdataUUID = new forestString;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_formelement_forestdata';
		$this->fphp_Primary->value = array('formelementUUID', 'forestdataUUID');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('formelementUUID', 'forestdataUUID');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>