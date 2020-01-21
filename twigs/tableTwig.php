<?php
class tableTwig extends forestTwig {
	use forestData;
	
	/* Fields */
	
	private $Id;
	private $UUID;
	private $Name;
	private $Identifier;
	private $Unique;
	private $SortOrder;
	private $Interval;
	private $View;
	private $SortColumn;
	private $InfoColumns;
	private $InfoColumnsView;
	private $Versioning;
	private $CheckoutInterval;
	
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		$this->Id = new forestNumericString(1);
		$this->UUID = new forestString;
		$this->Name = new forestString;
		$this->Identifier = new forestLookup(new forestLookupData('sys_fphp_identifier', array('UUID'), array('IdentifierName','IdentifierStart')));
		$this->Unique = new forestLookup(new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => 'foo')));
		$this->SortOrder = new forestLookup(new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => 'foo')));
		$this->Interval = new forestInt;
		$this->View = new forestLookup(new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => 'foo')));
		$this->SortColumn = new forestLookup(new forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => 'foo')));
		$this->InfoColumns = new forestInt;
		$this->InfoColumnsView = new forestInt;
		$this->Versioning = new forestInt;
		$this->CheckoutInterval = new forestString;
		
		/* forestTwig system fields */
		$this->fphp_Table->value = 'sys_fphp_table';
		$this->fphp_Primary->value = array('Id');
		$this->fphp_Unique->value = array('UUID');
		$this->fphp_SortOrder->value->Add(true, 'Name');
		$this->fphp_Interval->value = 50;
		$this->fphp_View->value = array('Name');
		$this->fphp_FillMapping(get_object_vars($this));
	}
}
?>