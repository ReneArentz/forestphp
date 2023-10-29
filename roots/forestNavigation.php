<?php
/**
 * class for render the navigation-bar
 * the navigation-bar itself is a linked list of navigation-nodes
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestphp.de>
 * @copyright   (c) 2019 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.0.0 stable
 * @link        http://www.forestphp.de/
 * @object-id   0x1 00018
 * @since       File available since Release 0.1.1 alpha
 * @deprecated  -
 *
 * @version log Version		Developer	Date		Comment
 * 		0.1.1 alpha	renatus		2019-08-13	added to framework
 * 		0.2.0 beta	renatus		2019-10-24	added RootMenu to navigation
 * 		0.4.0 beta	renatus		2019-11-14	added login an logout part
 * 		0.4.0 beta	renatus		2019-11-14	added permission check for navigation nodes
 * 		0.8.0 beta	renatus		2020-01-18	activated account link in logout part
 * 		0.9.0 beta	renatus		2020-01-30	changes for bootstrap 4
 * 		0.9.0 beta	renatus		2020-01-30	added Sidebar and FullScreen
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
use \fPHP\Roots\forestException as forestException;

class forestNavigation {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $NavigationNode;
	private $ParentNavigationNode;
	private $NavbarAdditionalClass;
	private $NavbarAlign;
	private $NavbarBrandLink;
	private $NavbarBrandTitle;
	private $NavbarMaxLevel;
	
	private $NavbarShowLoginPart;
	private $NavbarIconClass;
	private $NavbarLoginIcon;
	private $NavbarLoginLink;
	private $NavbarLoginTitle;
	private $NavbarSignUpIcon;
	private $NavbarSignUpLink;
	private $NavbarSignUpTitle;
		
	private $NavbarShowLogoutPart;
	private $NavbarUserIcon;
	private $NavbarUserLink;
	private $NavbarUserTitle;
	private $NavbarLogoutIcon;
	private $NavbarLogoutLink;
	private $NavbarLogoutTitle;
	
	/* Properties */

	/* Methods */
	
	/**
	 * constructor of forestNavigation class
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __construct() {
		$this->NavigationNode = new forestObject('forestNavigationNode', false);
		$this->ParentNavigationNode = new forestObject('forestNavigationNode', false);
		$this->NavbarAdditionalClass = new forestString('navbar-expand-xl bg-dark navbar-dark');
		$this->NavbarAlign = new forestList(array('fixed-top', 'fixed-bottom'), 'fixed-top');
		$this->NavbarBrandLink = new forestString('./');
		$this->NavbarBrandTitle = new forestString('forestPHP');
		$this->NavbarMaxLevel = new forestInt(10);
		
		$this->NavbarShowLoginPart = new forestBool;
		$this->NavbarIconClass = new forestString('fphp_nav_icon');
		$this->NavbarLoginIcon = new forestString('fas fa-sign-in-alt');
		$this->NavbarLoginLink = new forestString('./');
		$this->NavbarLoginTitle = new forestString('Login');
		$this->NavbarSignUpIcon = new forestString('fas fa-user-plus');
		$this->NavbarSignUpLink = new forestString('./');
		$this->NavbarSignUpTitle = new forestString('Sign up');
		
		$this->NavbarShowLogoutPart = new forestBool;
		$this->NavbarUserIcon = new forestString('fas fa-user');
		$this->NavbarUserLink = new forestString('./');
		$this->NavbarUserTitle = new forestString('User');
		$this->NavbarLogoutIcon = new forestString('fas fa-sign-out-alt');
		$this->NavbarLogoutLink = new forestString('./');
		$this->NavbarLogoutTitle = new forestString('Logout');
	}
	
	/**
	 * initialisation function to get navigation settings from database and creation of all navigation nodes
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function InitNavigation() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* generate nav links */
		$this->NavbarLoginLink = new forestString(\fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'login'));
		$this->NavbarSignUpLink = new forestString(\fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'signUp'));
		$this->NavbarUserLink = new forestString(\fPHP\Helper\forestLink::Link('account'));
		$this->NavbarLogoutLink = new forestString(\fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'logout'));
		
		/* load navbar settings from trunk record */
		$this->NavbarAdditionalClass->value = $o_glob->Trunk->NavbarAdditionalClass;
		$this->NavbarAlign->value = $o_glob->Trunk->NavbarAlign;
		$this->NavbarBrandTitle->value = $o_glob->Trunk->NavbarBrandTitle;
		$this->NavbarMaxLevel->value = $o_glob->Trunk->NavbarMaxLevel;
		
		if (!$this->NavbarShowLogoutPart->value) {
			$this->NavbarShowLoginPart->value = $o_glob->Trunk->NavbarShowLoginPart;
			$this->NavbarLoginIcon->value = $o_glob->Trunk->NavbarLoginIcon;
			$this->NavbarLoginTitle->value = $o_glob->GetTranslation('NavbarLoginTitle', 1);
			$this->NavbarSignUpIcon->value = $o_glob->Trunk->NavbarSignUpIcon;
			$this->NavbarSignUpTitle->value = $o_glob->GetTranslation('NavbarSignUpTitle', 1);
		}
		
		if (!$this->NavbarShowLoginPart->value) {
			$this->NavbarShowLogoutPart->value = $o_glob->Trunk->NavbarShowLogoutPart;
			$this->NavbarUserIcon->value = $o_glob->Trunk->NavbarUserIcon;
			
			if (issetStr($o_glob->Security->User)) {
				$this->NavbarUserTitle->value = $o_glob->Security->User;
			} else {
				$this->NavbarUserTitle->value = $o_glob->GetTranslation('NavbarUserTitle', 1);
			}
			
			$this->NavbarLogoutIcon->value = $o_glob->Trunk->NavbarLogoutIcon;
			$this->NavbarLogoutTitle->value = $o_glob->GetTranslation('NavbarLogoutTitle', 1);
		}
		
		$a_branchTree = $o_glob->BranchTree;
		
		/* get current branch information */
		if (!array_key_exists($o_glob->URL->BranchId, $a_branchTree['Id'])) {
			throw new forestException('Branch with Id[%0] could not be found', array($o_glob->URL->BranchId));
		}
		
		/* if current branch has parent branch, we have this as up-link */
		if ($a_branchTree['Id'][$o_glob->URL->BranchId]['ParentBranch'] != 0) {
			$i_parentBranchId = $a_branchTree['Id'][$o_glob->URL->BranchId]['ParentBranch'];
			
			/* check if parent branch really exists */
			if (!array_key_exists($i_parentBranchId, $a_branchTree['Id'])) {
				throw new forestException('ParentBranch with Id[%0] could not be found', array($i_parentBranchId));
			}
			
			/* create parent up-link navigation node */
			$o_navigationNode = new \fPHP\Branches\forestNavigationNode;
			$o_navigationNode->Title = $o_glob->GetTranslation($a_branchTree['Id'][$i_parentBranchId]['Title'], 1);
			$o_navigationNode->BranchId = $a_branchTree['Id'][$i_parentBranchId]['Id'];
			$o_navigationNode->BranchName = $a_branchTree['Id'][$i_parentBranchId]['Name'];
			$o_navigationNode->BranchFile = $a_branchTree['Id'][$i_parentBranchId]['Filename'];
			$o_navigationNode->Up = true;
			
			/* start building navigation tree recursive */
			$this->InitNavigationRecursive($a_branchTree['Id'][$i_parentBranchId]['Id'], $o_navigationNode);
			
			$this->ParentNavigationNode->value = $o_navigationNode;
		} else {
			/* create navigation node */
			$o_navigationNode = new \fPHP\Branches\forestNavigationNode;
			$o_navigationNode->Title = $o_glob->GetTranslation($a_branchTree['Id'][$o_glob->URL->BranchId]['Title'], 1);
			$o_navigationNode->BranchId = $a_branchTree['Id'][$o_glob->URL->BranchId]['Id'];
			$o_navigationNode->BranchName = $a_branchTree['Id'][$o_glob->URL->BranchId]['Name'];
			$o_navigationNode->BranchFile = $a_branchTree['Id'][$o_glob->URL->BranchId]['Filename'];
			
			/* start building navigation tree recursive */
			$this->InitNavigationRecursive($o_glob->URL->BranchId, $o_navigationNode);
			
			$this->NavigationNode->value = $o_navigationNode;
		}
		
		/*echo '<pre>';
		print_r($this->ParentNavigationNode->value);
		print_r($this->NavigationNode->value);
		echo '</pre>';*/
	}
	
	/**
	 * initialisation recursive function to get navigation nodes of multiple levels
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function InitNavigationRecursive($p_i_branchId, &$p_o_navigationNode) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		foreach ($o_glob->BranchTree['Id'] as $o_branch) {
			if ($o_branch['ParentBranch'] == $p_i_branchId) {
				/* only show navigation nodes where the user has permission on 'init' */
				if ($o_glob->Security->CheckUserPermission($o_branch['Name'], 'init')) {
					/* only show navigation nodes which are activated in branch settings, or the current branch */
					if ($o_branch['Navigation']) {
						$o_navigationNode = new \fPHP\Branches\forestNavigationNode;
						$o_navigationNode->Title = $o_glob->GetTranslation($o_branch['Title'], 1);
						$o_navigationNode->BranchId = $o_branch['Id'];
						$o_navigationNode->BranchName = $o_branch['Name'];
						$o_navigationNode->Navigation = $o_branch['Navigation'];
						$o_navigationNode->BranchFile = $o_branch['Filename'];
						
						$this->InitNavigationRecursive($o_branch['Id'], $o_navigationNode);
						
						$p_o_navigationNode->NavigationNodes->Add($o_navigationNode);
					} else if ($o_branch['Id'] == $o_glob->URL->BranchId) {
						$o_navigationNode = new \fPHP\Branches\forestNavigationNode;
						$o_navigationNode->Title = $o_glob->GetTranslation($o_branch['Title'], 1);
						$o_navigationNode->BranchId = $o_branch['Id'];
						$o_navigationNode->BranchName = $o_branch['Name'];
						$o_navigationNode->Navigation = $o_branch['Navigation'];
						$o_navigationNode->BranchFile = $o_branch['Filename'];
						
						$p_o_navigationNode->NavigationNodes->Add($o_navigationNode);
					}
				}
			}
		}
	}
	
	/**
	 * main render function
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function RenderNavigation() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if ($o_glob->Trunk->Navmode == 1) {
			$this->RenderNavbar();
		} else if ($o_glob->Trunk->Navmode == 10) {
			$this->RenderNavSidebar();
		} else if ($o_glob->Trunk->Navmode == 100) {
			$this->RenderNavFullScreen();
		}
	}
	
	/**
	 * render function to return a navbar
	 *
	 * @return string  contains all html nodes to show a navbar
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function RenderNavbar() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$i_maxLevel = $this->NavbarMaxLevel->value;
		$s_navigation = '';
		
		/* render navbar */
		$s_navigation .= '<nav class="navbar';
		
		if (issetStr($this->NavbarAdditionalClass->value)) {
			$s_navigation .= ' ' . $this->NavbarAdditionalClass->value;
		}
		
		if (issetStr($this->NavbarAlign->value)) {
			$s_navigation .= ' ' . $this->NavbarAlign->value;
		}
		
		$s_navigation .= '">' . "\n";
		
			/* render navbar header */
			$s_navigation .= '<a class="navbar-brand" href="' . $this->NavbarBrandLink->value . '">' . $this->NavbarBrandTitle->value . '</a>' . "\n";
			$s_navigation .= '<button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#fphp_navbar">' . "\n";
				$s_navigation .= '<span class="navbar-toggler-icon"></span>' . "\n";
			$s_navigation .= '</button>' . "\n";
			
			$s_navigation .= '<div class="collapse navbar-collapse" id="fphp_navbar">' . "\n";
				$s_navigation .= '<ul class="navbar-nav mr-auto">' . "\n";
				
				/* render root menu */
				if (issetStr($o_glob->RootMenu)) {
					$s_navigation .= $o_glob->RootMenu;
				}
				
				/* recurse rendering of navigation node elements */
				if (!is_null($this->ParentNavigationNode->value)) {
					$this->RenderNavigationRecursive($this->ParentNavigationNode->value, $s_navigation, 0, $i_maxLevel);
				} else {
					$this->RenderNavigationRecursive($this->NavigationNode->value, $s_navigation, 0, $i_maxLevel);
				}
			
				$s_navigation .= '</ul>' . "\n";
				
				/* render navbar right part */
				if ( ($this->NavbarShowLoginPart->value) || ($this->NavbarShowLogoutPart->value) ) {
					$s_navigation .= '<ul class="navbar-nav ml-auto">' . "\n";
				}
				
				if ($this->NavbarShowLoginPart->value) {
					/* render navbar login part */
					if ( (issetStr($this->NavbarSignUpLink->value)) && (issetStr($this->NavbarIconClass->value)) && (issetStr($this->NavbarSignUpIcon->value)) && (issetStr($this->NavbarSignUpTitle->value)) ) {
						$s_navigation .= '<li class="nav-item"><a href="' . $this->NavbarSignUpLink->value . '" class="nav-link text-nowrap"><span class="' . $this->NavbarIconClass->value . ' ' . $this->NavbarSignUpIcon->value . '"></span> ' . $this->NavbarSignUpTitle->value . '</a></li>';
					}
					
					if ( (issetStr($this->NavbarLoginLink->value)) && (issetStr($this->NavbarIconClass->value)) && (issetStr($this->NavbarLoginIcon->value)) && (issetStr($this->NavbarLoginTitle->value)) ) {
						$s_navigation .= '<li class="nav-item"><a href="' . $this->NavbarLoginLink->value . '" class="nav-link text-nowrap"><span class="' . $this->NavbarIconClass->value . ' ' . $this->NavbarLoginIcon->value . '"></span> ' . $this->NavbarLoginTitle->value . '</a></li>';
					}
				} else if ($this->NavbarShowLogoutPart->value) {
					/* render navbar logout part */
					if ( (issetStr($this->NavbarUserLink->value)) && (issetStr($this->NavbarIconClass->value)) && (issetStr($this->NavbarUserIcon->value)) && (issetStr($this->NavbarUserTitle->value)) ) {
						$s_navigation .= '<li class="nav-item"><a href="' . $this->NavbarUserLink->value . '" class="nav-link text-nowrap"><span class="' . $this->NavbarIconClass->value . ' ' . $this->NavbarUserIcon->value . '"></span> ' . $this->NavbarUserTitle->value . '</a></li>';
					}
					
					if ( (issetStr($this->NavbarLogoutLink->value)) && (issetStr($this->NavbarIconClass->value)) && (issetStr($this->NavbarLogoutIcon->value)) && (issetStr($this->NavbarLogoutTitle->value)) ) {
						$s_navigation .= '<li class="nav-item"><a href="' . $this->NavbarLogoutLink->value . '" class="nav-link text-nowrap"><span class="' . $this->NavbarIconClass->value . ' ' . $this->NavbarLogoutIcon->value . '"></span> ' . $this->NavbarLogoutTitle->value . '</a></li>';
					}
				}
				
				if ( ($this->NavbarShowLoginPart->value) || ($this->NavbarShowLogoutPart->value) ) {
					$s_navigation .= '</ul>' . "\n";
				}
			$s_navigation .= '</div>' . "\n";	
		$s_navigation .= '</nav>' . "\n";
		
		echo $s_navigation;
	}
	
	/**
	 * render function to return a navsidebar
	 *
	 * @return string  contains all html nodes to show a navsidebar
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function RenderNavSidebar() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$i_maxLevel = $this->NavbarMaxLevel->value;
		$s_navigation = '';
		
		/* render navbar */
		$s_navigation .= '<nav class="navbar';
		
		if (issetStr($this->NavbarAdditionalClass->value)) {
			$s_navigation .= ' ' . $this->NavbarAdditionalClass->value;
		}
		
		if (issetStr($this->NavbarAlign->value)) {
			$s_navigation .= ' ' . $this->NavbarAlign->value;
		}
		
		$s_navigation .= '">' . "\n";
			/* render navbar compass */
			$s_navigation .= '<a href="#" class="navbar-brand" onclick="fphp_toggleNavsidebar()">' . "\n";
				$s_navigation .= '<button class="btn btn-sm btn-info" type="button" title="Menu"><span class="fas fa-compass"></span></button>' . "\n";
			$s_navigation .= '</a>' . "\n";
			
			/* render navbar header */
			$s_navigation .= '<a class="navbar-brand" href="' . $this->NavbarBrandLink->value . '">' . $this->NavbarBrandTitle->value . '</a>' . "\n";
			
			$s_navigation .= '<button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#fphp_navbar">' . "\n";
				$s_navigation .= '<span class="navbar-toggler-icon"></span>' . "\n";
			$s_navigation .= '</button>' . "\n";
			
			$s_navigation .= '<div class="collapse navbar-collapse" id="fphp_navbar">' . "\n";
				$s_navigation .= '<ul class="navbar-nav">' . "\n";
				
				/* render root menu */
				if (issetStr($o_glob->RootMenu)) {
					$s_navigation .= $o_glob->RootMenu;
				}
				
				$s_navigation .= '</ul>' . "\n";
				
				/* render navbar right part */
				if ( ($this->NavbarShowLoginPart->value) || ($this->NavbarShowLogoutPart->value) ) {
					$s_navigation .= '<ul class="navbar-nav ml-auto">' . "\n";
				}
				
				if ($this->NavbarShowLoginPart->value) {
					/* render navbar login part */
					if ( (issetStr($this->NavbarSignUpLink->value)) && (issetStr($this->NavbarIconClass->value)) && (issetStr($this->NavbarSignUpIcon->value)) && (issetStr($this->NavbarSignUpTitle->value)) ) {
						$s_navigation .= '<li class="nav-item"><a href="' . $this->NavbarSignUpLink->value . '" class="nav-link text-nowrap"><span class="' . $this->NavbarIconClass->value . ' ' . $this->NavbarSignUpIcon->value . '"></span> ' . $this->NavbarSignUpTitle->value . '</a></li>';
					}
					
					if ( (issetStr($this->NavbarLoginLink->value)) && (issetStr($this->NavbarIconClass->value)) && (issetStr($this->NavbarLoginIcon->value)) && (issetStr($this->NavbarLoginTitle->value)) ) {
						$s_navigation .= '<li class="nav-item"><a href="' . $this->NavbarLoginLink->value . '" class="nav-link text-nowrap"><span class="' . $this->NavbarIconClass->value . ' ' . $this->NavbarLoginIcon->value . '"></span> ' . $this->NavbarLoginTitle->value . '</a></li>';
					}
				} else if ($this->NavbarShowLogoutPart->value) {
					/* render navbar logout part */
					if ( (issetStr($this->NavbarUserLink->value)) && (issetStr($this->NavbarIconClass->value)) && (issetStr($this->NavbarUserIcon->value)) && (issetStr($this->NavbarUserTitle->value)) ) {
						$s_navigation .= '<li class="nav-item"><a href="' . $this->NavbarUserLink->value . '" class="nav-link text-nowrap"><span class="' . $this->NavbarIconClass->value . ' ' . $this->NavbarUserIcon->value . '"></span> ' . $this->NavbarUserTitle->value . '</a></li>';
					}
					
					if ( (issetStr($this->NavbarLogoutLink->value)) && (issetStr($this->NavbarIconClass->value)) && (issetStr($this->NavbarLogoutIcon->value)) && (issetStr($this->NavbarLogoutTitle->value)) ) {
						$s_navigation .= '<li class="nav-item"><a href="' . $this->NavbarLogoutLink->value . '" class="nav-link text-nowrap"><span class="' . $this->NavbarIconClass->value . ' ' . $this->NavbarLogoutIcon->value . '"></span> ' . $this->NavbarLogoutTitle->value . '</a></li>';
					}
				}
				
				if ( ($this->NavbarShowLoginPart->value) || ($this->NavbarShowLogoutPart->value) ) {
					$s_navigation .= '</ul>' . "\n";
				}
			$s_navigation .= '</div>' . "\n";	
		$s_navigation .= '</nav>' . "\n";
		
		$s_navigation .= '<div id="fphp_navsidebarId" class="fphp_navsidebar" style="background: ' . $o_glob->Trunk->NavBackgroundColor . ';">' . "\n";
			/* recurse rendering of navigation node elements */
			if (!is_null($this->ParentNavigationNode->value)) {
				$this->RenderNavigationRecursive($this->ParentNavigationNode->value, $s_navigation, 0, 0, false);
			} else {
				$this->RenderNavigationRecursive($this->NavigationNode->value, $s_navigation, 0, 0, false);
			}
			
		$s_navigation .= '</div>' . "\n";
		
		$s_navigation .= '<div id="fphp_navoverlayId" class="fphp_navoverlay"></div>' . "\n";
		
		echo $s_navigation;
	}
	
	/**
	 * render function to return a nav full screen
	 *
	 * @return string  contains all html nodes to show a nav full screen
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function RenderNavFullScreen() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$i_maxLevel = $this->NavbarMaxLevel->value;
		$s_navigation = '';
		
		/* render navbar */
		$s_navigation .= '<nav class="navbar';
		
		if (issetStr($this->NavbarAdditionalClass->value)) {
			$s_navigation .= ' ' . $this->NavbarAdditionalClass->value;
		}
		
		if (issetStr($this->NavbarAlign->value)) {
			$s_navigation .= ' ' . $this->NavbarAlign->value;
		}
		
		$s_navigation .= '">' . "\n";
			/* render navbar compass */
			$s_navigation .= '<a href="#" class="navbar-brand" onclick="fphp_toggleNavfullscreen(' . $o_glob->Trunk->NavCurtain . ')">' . "\n";
				$s_navigation .= '<button class="btn btn-sm btn-info" type="button" title="Menu"><span class="fas fa-compass"></span></button>' . "\n";
			$s_navigation .= '</a>' . "\n";
			
			/* render navbar header */
			$s_navigation .= '<a class="navbar-brand" href="' . $this->NavbarBrandLink->value . '">' . $this->NavbarBrandTitle->value . '</a>' . "\n";
			
			$s_navigation .= '<button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#fphp_navbar">' . "\n";
				$s_navigation .= '<span class="navbar-toggler-icon"></span>' . "\n";
			$s_navigation .= '</button>' . "\n";
			
			$s_navigation .= '<div class="collapse navbar-collapse" id="fphp_navbar">' . "\n";
				$s_navigation .= '<ul class="navbar-nav">' . "\n";
				
				/* render root menu */
				if (issetStr($o_glob->RootMenu)) {
					$s_navigation .= $o_glob->RootMenu;
				}
				
				$s_navigation .= '</ul>' . "\n";
				
				/* render navbar right part */
				if ( ($this->NavbarShowLoginPart->value) || ($this->NavbarShowLogoutPart->value) ) {
					$s_navigation .= '<ul class="navbar-nav ml-auto">' . "\n";
				}
				
				if ($this->NavbarShowLoginPart->value) {
					/* render navbar login part */
					if ( (issetStr($this->NavbarSignUpLink->value)) && (issetStr($this->NavbarIconClass->value)) && (issetStr($this->NavbarSignUpIcon->value)) && (issetStr($this->NavbarSignUpTitle->value)) ) {
						$s_navigation .= '<li class="nav-item"><a href="' . $this->NavbarSignUpLink->value . '" class="nav-link text-nowrap"><span class="' . $this->NavbarIconClass->value . ' ' . $this->NavbarSignUpIcon->value . '"></span> ' . $this->NavbarSignUpTitle->value . '</a></li>';
					}
					
					if ( (issetStr($this->NavbarLoginLink->value)) && (issetStr($this->NavbarIconClass->value)) && (issetStr($this->NavbarLoginIcon->value)) && (issetStr($this->NavbarLoginTitle->value)) ) {
						$s_navigation .= '<li class="nav-item"><a href="' . $this->NavbarLoginLink->value . '" class="nav-link text-nowrap"><span class="' . $this->NavbarIconClass->value . ' ' . $this->NavbarLoginIcon->value . '"></span> ' . $this->NavbarLoginTitle->value . '</a></li>';
					}
				} else if ($this->NavbarShowLogoutPart->value) {
					/* render navbar logout part */
					if ( (issetStr($this->NavbarUserLink->value)) && (issetStr($this->NavbarIconClass->value)) && (issetStr($this->NavbarUserIcon->value)) && (issetStr($this->NavbarUserTitle->value)) ) {
						$s_navigation .= '<li class="nav-item"><a href="' . $this->NavbarUserLink->value . '" class="nav-link text-nowrap"><span class="' . $this->NavbarIconClass->value . ' ' . $this->NavbarUserIcon->value . '"></span> ' . $this->NavbarUserTitle->value . '</a></li>';
					}
					
					if ( (issetStr($this->NavbarLogoutLink->value)) && (issetStr($this->NavbarIconClass->value)) && (issetStr($this->NavbarLogoutIcon->value)) && (issetStr($this->NavbarLogoutTitle->value)) ) {
						$s_navigation .= '<li class="nav-item"><a href="' . $this->NavbarLogoutLink->value . '" class="nav-link text-nowrap"><span class="' . $this->NavbarIconClass->value . ' ' . $this->NavbarLogoutIcon->value . '"></span> ' . $this->NavbarLogoutTitle->value . '</a></li>';
					}
				}
				
				if ( ($this->NavbarShowLoginPart->value) || ($this->NavbarShowLogoutPart->value) ) {
					$s_navigation .= '</ul>' . "\n";
				}
			$s_navigation .= '</div>' . "\n";	
		$s_navigation .= '</nav>' . "\n";
		
		$s_navClass = 'fphp_navfullscreen';
		
		if ($o_glob->Trunk->NavCurtain == 1) {
			$s_navClass = 'fphp_navfullscreen-no-slide';
		} else if ($o_glob->Trunk->NavCurtain == 100) {
			$s_navClass = 'fphp_navfullscreen-slide-top';
		}
		
		$s_navigation .= '<div id="fphp_navfullscreenId" class="' . $s_navClass . '" style="background: ' . $o_glob->Trunk->NavBackgroundColor . ';">' . "\n";
			/* recurse rendering of navigation node elements */
			if (!is_null($this->ParentNavigationNode->value)) {
				$this->RenderNavigationRecursive($this->ParentNavigationNode->value, $s_navigation, 0, 0, false);
			} else {
				$this->RenderNavigationRecursive($this->NavigationNode->value, $s_navigation, 0, 0, false);
			}
			
		$s_navigation .= '</div>' . "\n";
		
		echo $s_navigation;
	}
	
	/**
	 * recursive render function to return navigation nodes of multiple levels
	 *
	 * @param forestNavigationNode $p_o_navigationNode
	 * @param string $p_s_navigation  string value which contains all html nodes for navigation element
	 * @param integer $p_i_level  current navigation level
	 * @param integer $p_i_maxLevel  value of level where navigation should stop
	 * @param bool $p_b_navbar  navbar flag to distinguish between a navbar and other navigation modes
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function RenderNavigationRecursive(\fPHP\Branches\forestNavigationNode $p_o_navigationNode, &$p_s_navigation, $p_i_level, $p_i_maxLevel, $p_b_navbar = true) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$p_o_navigationNode->Navbar = $p_b_navbar;
		
		if ($p_i_level <= 0) {
			/* the zero flague is needed to differ between the main navigation nodes in the bar and their child-nodes */
			$b_zero = true;
			$p_o_navigationNode->Zero = true;
		} else {
			$b_zero = false;
		}
		
		if ( ($p_o_navigationNode->BranchName == 'index') || ($p_o_navigationNode->Up) ) {
			if ($p_b_navbar) {
				/* navigation top level */
				$p_s_navigation .= '<li class="nav item';
				
				if ($o_glob->URL->BranchId == $p_o_navigationNode->BranchId) {
					/* mark active node */
					$p_s_navigation .= ' active';
				}
				
				$p_s_navigation .= '">';
			} else {
				if ($o_glob->URL->BranchId == $p_o_navigationNode->BranchId) {
					$p_o_navigationNode->Active = true;
				}
			}
			
			/* render home node */
			$p_s_navigation .= $p_o_navigationNode;
				
			if ($p_b_navbar) {	
				$p_s_navigation .= '</li>' . "\n";
			}
			
			if ($p_o_navigationNode->NavigationNodes->Count() > 0) {
				foreach ($p_o_navigationNode->NavigationNodes as $o_navigationNode) {
					$this->RenderNavigationRecursive($o_navigationNode, $p_s_navigation, $p_i_level, $p_i_maxLevel, $p_b_navbar);
				}
			}
		} else {
			if ($p_b_navbar) {
				$p_s_navigation .= '<li';
				
				/* calulate classes of navigation node element */
				$a_classes = array();
				
				if ($o_glob->URL->BranchId == $p_o_navigationNode->BranchId) {
					$a_classes[] = 'active';
				}
				
				if (($p_o_navigationNode->NavigationNodes->Count() > 0) && ($p_i_level <= $p_i_maxLevel)) {
					if ($b_zero) {
						$a_classes[] = 'nav-item dropdown';
					} else {
						$a_classes[] = 'dropdown-submenu';
					}
				} else {
					$a_classes[] = 'nav-item';
				}
				
				/* render classes of navigation node element */
				if (count($a_classes) > 0) {
					$p_s_navigation .= ' class="';
					$i = 0;
					
					foreach ($a_classes as $s_class) {
						if ($i == 0) {
							$p_s_navigation .= $s_class;
						} else {
							$p_s_navigation .= ' ' . $s_class;
						}
						
						$i++;
					}
					
					$p_s_navigation .= '"';
				}
				
				$p_s_navigation .= '>';
			
				/* render dropdown item span wrapper */
				if (($p_o_navigationNode->NavigationNodes->Count() > 0) && ($p_i_level <= $p_i_maxLevel) && (!$b_zero)) {
					$p_s_navigation .= '<span class="dropdown-item">' . "\n";
				}
			} else {
				if ($o_glob->URL->BranchId == $p_o_navigationNode->BranchId) {
					$p_o_navigationNode->Active = true;
				}
			}
			
			/* render node */
			$p_s_navigation .= $p_o_navigationNode;
			
			if ($p_b_navbar) {
				/* close dropdown item span wrapper */
				if (($p_o_navigationNode->NavigationNodes->Count() > 0) && ($p_i_level <= $p_i_maxLevel) && (!$b_zero)) {
					$p_s_navigation .= '</span>' . "\n";
				}
				
				if (!(($p_o_navigationNode->NavigationNodes->Count() > 0) && ($p_i_level <= $p_i_maxLevel))) {
					$p_s_navigation .= '</li>' . "\n";
				}
				
				/* render child-nodes if maxLevel is not exceeded */
				if (($p_o_navigationNode->NavigationNodes->Count() > 0) && ($p_i_level <= $p_i_maxLevel)) {
					$p_s_navigation .=  "\n" . '<ul class="dropdown-menu">' . "\n";
					$p_i_level++;
					
					foreach ($p_o_navigationNode->NavigationNodes as $o_navigationNode) {
						$this->RenderNavigationRecursive($o_navigationNode, $p_s_navigation, $p_i_level, $p_i_maxLevel, $p_b_navbar);
					}
					
					$p_s_navigation .= "\n" . '</ul>' . "\n";
					$p_s_navigation .= '</li>' . "\n";
				}
			}
		}
	}
	
	/**
	 * function to render a landing page
	 *
	 * @return string  contains all html nodes to show a landing page
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function RenderLandingPage() {
		$s_navigation = '';
		
		/* render landing page navigation list */
		$s_navigation .= '<div class="container-fluid">' . "\n";
				$s_navigation .= '<ul class="fphp_tiles clearfix">' . "\n";
				
				/* recurse rendering of navigation node elements */
				if (!is_null($this->ParentNavigationNode->value)) {
					$this->RenderLandingPageRecursive($this->ParentNavigationNode->value, $s_navigation);
				} else {
					$this->RenderLandingPageRecursive($this->NavigationNode->value, $s_navigation);
				}
				
				$s_navigation .= '</ul>' . "\n";
		$s_navigation .= '</div>' . "\n";
		
		return $s_navigation;
	}
	
	/**
	 * recursive render function to return navigation nodes of multiple levels for a landing page
	 *
	 * @param forestNavigationNode $p_o_navigationNode
	 * @param string $p_s_navigation  string value which contains all html nodes for navigation element
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function RenderLandingPageRecursive(\fPHP\Branches\forestNavigationNode $p_o_navigationNode, &$p_s_navigation) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if ($p_o_navigationNode->Up) {
			$b_found = false;
			
			/* look for current navigation node */
			if ($p_o_navigationNode->NavigationNodes->Count() > 0) {
				/* if there are no children we have nothing to display on landing page */
				foreach ($p_o_navigationNode->NavigationNodes as $o_navigationNode) {
					if ($o_glob->URL->BranchId == $o_navigationNode->BranchId) {
						$p_o_navigationNode = $o_navigationNode;
						$b_found = true;
					}
				}
			}
			
			if (!$b_found) {
				return;
			}
		}
		
		if ($p_o_navigationNode->NavigationNodes->Count() > 0) {
			foreach ($p_o_navigationNode->NavigationNodes as $o_navigationNode) {
				if ($o_glob->URL->BranchId == $o_navigationNode->BranchId) {
					continue;
				}
				
				$p_s_navigation .= '<li>';
				
				/* render node */
				$p_s_navigation .= $o_navigationNode->RenderForLandingPage();
				
				$p_s_navigation .= '</li>' . "\n";
			}
		}
	}
}
?>