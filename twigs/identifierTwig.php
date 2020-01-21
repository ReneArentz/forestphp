<?php
class identifierTwig extends forestTwig {
	use forestData;
	
	/* Fields */
	
	private $Id;
	private $UUID;
	private $IdentifierName;
	private $IdentifierStart;
	private $IdentifierNext;
	private $IdentifierIncrement;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->IdentifierName = new forestString;
		$this->IdentifierStart = new forestString;
		$this->IdentifierNext = new forestString;
		$this->IdentifierIncrement = new forestInt;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_identifier';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID','IdentifierName');
		$this->fphp_SortOrder->value->Add(true, 'IdentifierName');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('IdentifierName','IdentifierStart','IdentifierNext','IdentifierIncrement');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>