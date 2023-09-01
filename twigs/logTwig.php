<?php

namespace fPHP\Twigs;
use \fPHP\Roots\{forestString, forestList, forestNumericString, forestInt, forestFloat, forestBool, forestArray, forestObject, forestLookup};
use \fPHP\Helper\forestLookupData;

class logTwig extends forestTwig {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $Id;
	private $UUID;
	private $Created;
	private $CreatedBy;
	private $Branch;
	private $Action;
	private $Session;
	private $Event;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->Created = new forestObject('forestDateTime');
		$this->CreatedBy = new forestString;
		$this->Branch = new forestString;
		$this->Action = new forestString;
		$this->Session = new forestString;
		$this->Event = new forestString;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_log';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID');
		$this->fphp_SortOrder->value->Add(true, 'Id');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('Branch','Action','Session','Event','Created','CreatedBy');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>