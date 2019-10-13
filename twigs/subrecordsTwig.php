<?php
class subrecordsTwig extends forestTwig {
	use forestData;
	
	/* Fields */
	
	private $Id;
	private $UUID;
	private $HeadUUID;
	private $JoinUUID;
	private $ShortText00;
	private $ShortText01;
	private $ShortText02;
	private $ShortText03;
	private $ShortText04;
	private $ShortText05;
	private $ShortText06;
	private $ShortText07;
	private $ShortText08;
	private $ShortText09;
	private $Text00;
	private $Text01;
	private $SmallInt00;
	private $SmallInt01;
	private $SmallInt02;
	private $SmallInt03;
	private $SmallInt04;
	private $Datetime00;
	private $Datetime01;
	private $Datetime02;
	private $Datetime03;
	private $Time00;
	private $Time01;
	private $Double00;
	private $Double01;
	private $Decimal00;
	private $Decimal01;
	private $Decimal02;
	private $Decimal03;
	private $Decimal04;
	private $Decimal05;
	private $Decimal06;
	private $Bool00;
	private $Bool01;
	private $Lookup00;
	private $Lookup01;
	private $Lookup02;
	private $Lookup03;
	private $Lookup04;
	private $Lookup05;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->HeadUUID = new forestString;
		$this->JoinUUID = new forestString;
		$this->ShortText00 = new forestString;
		$this->ShortText01 = new forestString;
		$this->ShortText02 = new forestString;
		$this->ShortText03 = new forestString;
		$this->ShortText04 = new forestString;
		$this->ShortText05 = new forestString;
		$this->ShortText06 = new forestString;
		$this->ShortText07 = new forestString;
		$this->ShortText08 = new forestString;
		$this->ShortText09 = new forestString;
		$this->Text00 = new forestString;
		$this->Text01 = new forestString;
		$this->SmallInt00 = new forestInt;
		$this->SmallInt01 = new forestInt;
		$this->SmallInt02 = new forestInt;
		$this->SmallInt03 = new forestInt;
		$this->SmallInt04 = new forestInt;
		$this->Datetime00 = new forestObject('forestDateTime');
		$this->Datetime01 = new forestObject('forestDateTime');
		$this->Datetime02 = new forestObject('forestDateTime');
		$this->Datetime03 = new forestObject('forestDateTime');
		$this->Time00 = new forestObject('forestDateTime');
		$this->Time01 = new forestObject('forestDateTime');
		$this->Double00 = new forestFloat;
		$this->Double01 = new forestFloat;
		$this->Decimal00 = new forestFloat;
		$this->Decimal01 = new forestFloat;
		$this->Decimal02 = new forestFloat;
		$this->Decimal03 = new forestFloat;
		$this->Decimal04 = new forestFloat;
		$this->Decimal05 = new forestFloat;
		$this->Decimal06 = new forestFloat;
		$this->Bool00 = new forestBool;
		$this->Bool01 = new forestBool;
		$this->Lookup00 = new forestLookup(new forestLookupData('table', array('primary'), array('label')));
		$this->Lookup01 = new forestLookup(new forestLookupData('table', array('primary'), array('label')));
		$this->Lookup02 = new forestLookup(new forestLookupData('table', array('primary'), array('label')));
		$this->Lookup03 = new forestLookup(new forestLookupData('table', array('primary'), array('label')));
		$this->Lookup04 = new forestLookup(new forestLookupData('table', array('primary'), array('label')));
		$this->Lookup05 = new forestLookup(new forestLookupData('table', array('primary'), array('label')));
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_subrecords';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID');
		$this->fphp_SortOrder->value->Add(true, 'Id');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('Id');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>