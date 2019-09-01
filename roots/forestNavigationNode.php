<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.1.2 (0x1 00019)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * class holding navigation information
 * action and parameter properties are not necessary to be set at this point of development
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.1 alpha	renatus		2019-08-14	added to framework		
 */

class forestNavigationNode {
	use forestData;
	
	/* Fields */
	
	private $Title;
	private $BranchId;
	private $BranchName;
	private $NavigationNodes;
	private $Up;
	private $Navigation;
	private $BranchFile;
	private $Zero;
	private $IconClass;
	private $Icon;
	private $NoDropDown;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		$this->Title = new forestString;
		$this->BranchId = new forestNumericString(1);
		$this->BranchName = new forestString;
		$this->NavigationNodes = new forestObject(new forestObjectList('forestNavigationNode'), false);
		$this->Up = new forestBool;
		$this->Navigation = new forestBool;
		$this->BranchFile = new forestString;
		$this->Zero = new forestBool;
		$this->IconClass = new forestString;
		$this->Icon = new forestString;
		$this->NoDropDown = new forestBool;
	}
	
	public function RenderForLandingPage() {
		$o_glob = forestGlobals::init();
		
		$this->NoDropDown->value = true;
		$s_foo = strval($this);
		$this->NoDropDown->value = false;
		
		return $s_foo;
	}
	
	function __toString() {
		$o_glob = forestGlobals::init();
		
		$s_foo = '<a';
		
		if ($this->NoDropDown->value) {
			$s_foo .= ' class="fphp_tile"';
		}
		
		$s_foo .= ' href="';
		
		if ($o_glob->URL->BranchId != $this->BranchId->value) {
			/* navigation node is not current page */
			if ($this->BranchFile->value != 'NULL') {
				/* render file path for static file link */
				$s_foo .= './files/' . $this->BranchFile->value;
			} else {
				/* create link to page */
				if ($this->BranchId->value > 1) {
					$s_foo .= forestLink::Link($this->BranchName->value);
				} else {
					$s_foo .= forestLink::Link();
				}
			}
		} else {
			/* navigation node is current papge */
			$s_foo .= forestLink::Link($o_glob->URL->Branch);
		}
		
		/* render title */
		$s_foo .= '" title="' . $this->Title->value . '">';
		
		if ( (issetStr($this->IconClass->value)) && (issetStr($this->Icon->value)) ) {
			/* render navigation node icon if it is set */
			$s_foo .= '<span class="' . $this->IconClass->value . ' glyphicon ' . $this->Icon->value . '"></span> ';
		}
		
		if (!$this->Up->value) {
			$s_foo .= $this->Title->value . '</a>';
			
			if ( ($this->NavigationNodes->value->Count() > 0) && ($this->BranchName->value != 'index') && (!$this->NoDropDown->value) ) {
				/* navigation node has children -> render dropdown */
				$s_foo .= '<a';
				
				if ($this->Zero->value) {
					$s_foo .= ' class="fphp_menu_dropdown dropdown-toggle" data-toggle="dropdown"';
				} else {
					$s_foo .= ' class="fphp_menu_dropdown"';
				}
				
				$s_foo .= ' href="#"><span class="glyphicon glyphicon-menu-down"></span></a>';
			}
		} else {
			/* up navigation element */
			$s_foo .= '<span class="fphp_nav_icon glyphicon glyphicon-menu-up"></span>' . $this->Title->value . '</a>';
		}
		
		return $s_foo;
	}
}
?>