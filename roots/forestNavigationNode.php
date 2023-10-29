<?php
/**
 * class holding navigation information
 * action and parameter properties are not necessary to be set at this point of development
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestphp.de>
 * @copyright   (c) 2019 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.0.0 stable
 * @link        http://www.forestphp.de/
 * @object-id   0x1 00019
 * @since       File available since Release 0.1.0 alpha
 * @deprecated  -
 *
 * @version log Version		Developer	Date		Comment
 * 		0.1.1 alpha	renatus		2019-08-14	added to framework
 * 		0.9.0 beta	renatus		2020-01-30	changes for bootstrap 4, Sidebar and FullScreen
 */

namespace fPHP\Branches;

use \fPHP\Roots\forestString as forestString;
use \fPHP\Roots\forestList as forestList;
use \fPHP\Roots\forestNumericString as forestNumericString;
use \fPHP\Roots\forestInt as forestInt;
use \fPHP\Roots\forestFloat as forestFloat;
use \fPHP\Roots\forestBool as forestBool;
use \fPHP\Roots\forestArray as forestArray;
use \fPHP\Roots\forestObject as forestObject;
use \fPHP\Roots\forestLookup as forestLookup;
use \fPHP\Helper\forestObjectList;
use \fPHP\Roots\forestException as forestException;

class forestNavigationNode {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $Title;
	private $BranchId;
	private $BranchName;
	private $NavigationNodes;
	private $Up;
	private $Navigation;
	private $BranchFile;
	private $Zero;
	private $Navbar;
	private $Active;
	private $IconClass;
	private $Icon;
	private $NoDropDown;
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestNavigationNode class
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __construct() {
		$this->Title = new forestString;
		$this->BranchId = new forestNumericString(1);
		$this->BranchName = new forestString;
		$this->NavigationNodes = new forestObject(new forestObjectList('forestNavigationNode'), false);
		$this->Up = new forestBool;
		$this->Navigation = new forestBool;
		$this->BranchFile = new forestString;
		$this->Zero = new forestBool;
		$this->Navbar = new forestBool(true);
		$this->Active = new forestBool;
		$this->IconClass = new forestString;
		$this->Icon = new forestString;
		$this->NoDropDown = new forestBool;
	}
	
	/**
	 * function to render navigation node for landing page
	 *
	 * @return string  navigation node
	 *
	 * @access public
	 * @static no
	 */
	public function RenderForLandingPage() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$this->NoDropDown->value = true;
		$s_foo = strval($this);
		$this->NoDropDown->value = false;
		
		return $s_foo;
	}
	
	/**
	 * function to render navigation node
	 *
	 * @return string  navigation node
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __toString() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
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
					$s_foo .= \fPHP\Helper\forestLink::Link($this->BranchName->value);
				} else {
					$s_foo .= \fPHP\Helper\forestLink::Link();
				}
			}
		} else {
			/* navigation node is current papge */
			$s_foo .= \fPHP\Helper\forestLink::Link($o_glob->URL->Branch);
		}
		
		$s_foo .= '"';
		
		/* render class */
		if ($this->Navbar->value) {
			if ( ($this->Zero->value) && (!$this->NoDropDown->value) ) {
				if ( ($this->NavigationNodes->value->Count() > 0) && ($this->BranchName->value != 'index') && (!$this->Up->value) ) {
					$s_foo .= ' class="nav-link text-nowrap dropdown-menu-item-title"';
				} else {
					$s_foo .= ' class="nav-link text-nowrap"';
				}
			} else {
				if ( ($this->NavigationNodes->value->Count() > 0) && ($this->BranchName->value != 'index') ) {
					$s_foo .= ' class="text-dark text-nowrap"';
				} else {
					$s_foo .= ' class="dropdown-item text-nowrap"';
				}
			}
		} else {
			if ($this->Active->value) {
				$s_foo .= ' class="active"';
				$s_foo .= ' style="color: ' . $o_glob->Trunk->NavTextActiveColor . ';"';
			} else {
				$s_foo .= ' style="color: ' . $o_glob->Trunk->NavTextColor . ';"';
			}
		}
		
		/* render title */
		$s_foo .= ' title="' . $this->Title->value . '">';
		
		if ( (issetStr($this->IconClass->value)) && (issetStr($this->Icon->value)) ) {
			/* render navigation node icon if it is set */
			$s_foo .= '<span class="' . $this->IconClass->value . $this->Icon->value . '"></span> ';
		}
		
		if (!$this->Up->value) {
			$s_foo .= $this->Title->value . '</a>';
			
			if ($this->Navbar->value) {
				if ( ($this->NavigationNodes->value->Count() > 0) && ($this->BranchName->value != 'index') && (!$this->NoDropDown->value) ) {
					/* navigation node has children -> render dropdown */
					$s_foo .= '<a';
					
					if ($this->Zero->value) {
						$s_foo .= ' class="nav-link text-nowrap dropdown-menu-item" data-toggle="dropdown" id="navbardrop"';
					} else {
						$s_foo .= ' class="dropdown-submenu-item text-dark"';
					}
					
					$s_foo .= ' href="#"><span class="fas fa-caret-down"></span></a>';
				}
			}
		} else {
			/* up navigation element */
			$s_foo .= '<span class="fphp_nav_icon fas fa-caret-up"></span>' . $this->Title->value . '</a>';
		}
		
		return $s_foo;
	}
}
?>