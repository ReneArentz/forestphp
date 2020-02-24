<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.8.0 (0x1 00018)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * class for render the navigation-bar
 * the navigation-bar itself is a linked list of navigation-nodes
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.1 alpha	renatus		2019-08-13	added to framework
 * 0.2.0 beta	renatus		2019-10-24	added RootMenu to navigation
 * 0.4.0 beta	renatus		2019-11-14	added login an logout part
 * 0.4.0 beta	renatus		2019-11-14	added permission check for navigation nodes
 * 0.8.0 beta	renatus		2020-01-18	activated account link in logout part
 */

class forestNavigation {
	use forestData;
	
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
	
	public function __construct() {
		$this->NavigationNode = new forestObject('forestNavigationNode', false);
		$this->ParentNavigationNode = new forestObject('forestNavigationNode', false);
		$this->NavbarAdditionalClass = new forestString('navbar-inverse');
		$this->NavbarAlign = new forestList(array('navbar-fixed-top', 'navbar-fixed-bottom'), 'navbar-fixed-top');
		$this->NavbarBrandLink = new forestString('./');
		$this->NavbarBrandTitle = new forestString('forestPHP');
		$this->NavbarMaxLevel = new forestInt(10);
		
		$this->NavbarShowLoginPart = new forestBool;
		$this->NavbarIconClass = new forestString('fphp_nav_icon');
		$this->NavbarLoginIcon = new forestString('glyphicon-log-in');
		$this->NavbarLoginLink = new forestString('./');
		$this->NavbarLoginTitle = new forestString('Login');
		$this->NavbarSignUpIcon = new forestString('glyphicon-user');
		$this->NavbarSignUpLink = new forestString('./');
		$this->NavbarSignUpTitle = new forestString('Sign up');
		
		$this->NavbarShowLogoutPart = new forestBool;
		$this->NavbarUserIcon = new forestString('glyphicon-user');
		$this->NavbarUserLink = new forestString('./');
		$this->NavbarUserTitle = new forestString('User');
		$this->NavbarLogoutIcon = new forestString('glyphicon-log-out');
		$this->NavbarLogoutLink = new forestString('./');
		$this->NavbarLogoutTitle = new forestString('Logout');
	}
	
	public function InitNavigation() {
		$o_glob = forestGlobals::init();
		
		/* generate nav links */
		$this->NavbarLoginLink = new forestString(forestLink::Link($o_glob->URL->Branch, 'login'));
		$this->NavbarSignUpLink = new forestString(forestLink::Link($o_glob->URL->Branch, 'signUp'));
		$this->NavbarUserLink = new forestString(forestLink::Link('account'));
		$this->NavbarLogoutLink = new forestString(forestLink::Link($o_glob->URL->Branch, 'logout'));
		
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
			$o_navigationNode = new forestNavigationNode;
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
			$o_navigationNode = new forestNavigationNode;
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
	
	private function InitNavigationRecursive($p_i_branchId, &$p_o_navigationNode) {
		$o_glob = forestGlobals::init();
		
		foreach ($o_glob->BranchTree['Id'] as $o_branch) {
			if ($o_branch['ParentBranch'] == $p_i_branchId) {
				/* only show navigation nodes where the user has permission on 'init' */
				if ($o_glob->Security->CheckUserPermission($o_branch['Name'], 'init')) {
					/* only show navigation nodes which are activated in branch settings, or the current branch */
					if ($o_branch['Navigation']) {
						$o_navigationNode = new forestNavigationNode;
						$o_navigationNode->Title = $o_glob->GetTranslation($o_branch['Title'], 1);
						$o_navigationNode->BranchId = $o_branch['Id'];
						$o_navigationNode->BranchName = $o_branch['Name'];
						$o_navigationNode->Navigation = $o_branch['Navigation'];
						$o_navigationNode->BranchFile = $o_branch['Filename'];
						
						$this->InitNavigationRecursive($o_branch['Id'], $o_navigationNode);
						
						$p_o_navigationNode->NavigationNodes->Add($o_navigationNode);
					} else if ($o_branch['Id'] == $o_glob->URL->BranchId) {
						$o_navigationNode = new forestNavigationNode;
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
	
	public function RenderNavigation() {
		$o_glob = forestGlobals::init();
		
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
			$s_navigation .= '<div class="container-fluid">' . "\n";
				/* render navbar header */
				$s_navigation .= '<div class="navbar-header">' . "\n";
					$s_navigation .= '<span><a class="navbar-brand" href="' . $this->NavbarBrandLink->value . '">' . $this->NavbarBrandTitle->value . '</a>' . "\n";
					if (issetStr($o_glob->RootMenu)) {
						$s_navigation .= $o_glob->RootMenu;
					}
					$s_navigation .= '</span>';
					$s_navigation .= '<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#fphp_navbar">' . "\n";
						$s_navigation .= '<span class="icon-bar"></span>' . "\n";
						$s_navigation .= '<span class="icon-bar"></span>' . "\n";
						$s_navigation .= '<span class="icon-bar"></span>' . "\n";
					$s_navigation .= '</button>' . "\n";
				$s_navigation .= '</div>' . "\n";
				$s_navigation .= '<div class="collapse navbar-collapse" id="fphp_navbar">' . "\n";
					$s_navigation .= '<ul class="nav navbar-nav">' . "\n";
					
					/* recurse rendering of navigation node elements */
					if (!is_null($this->ParentNavigationNode->value)) {
						$this->RenderNavigationRecursive($this->ParentNavigationNode->value, $s_navigation, 0, $i_maxLevel);
					} else {
						$this->RenderNavigationRecursive($this->NavigationNode->value, $s_navigation, 0, $i_maxLevel);
					}
				
					$s_navigation .= '</ul>' . "\n";
					
					if ( ($this->NavbarShowLoginPart->value) || ($this->NavbarShowLogoutPart->value) ) {
						$s_navigation .= '<ul class="nav navbar-nav navbar-right">' . "\n";
					}
					
					if ($this->NavbarShowLoginPart->value) {
						/* render navbar login part */
						if ( (issetStr($this->NavbarSignUpLink->value)) && (issetStr($this->NavbarIconClass->value)) && (issetStr($this->NavbarSignUpIcon->value)) && (issetStr($this->NavbarSignUpTitle->value)) ) {
							$s_navigation .= '<li><a href="' . $this->NavbarSignUpLink->value . '"><span class="' . $this->NavbarIconClass->value . ' glyphicon ' . $this->NavbarSignUpIcon->value . '"></span> ' . $this->NavbarSignUpTitle->value . '</a></li>';
						}
						
						if ( (issetStr($this->NavbarLoginLink->value)) && (issetStr($this->NavbarIconClass->value)) && (issetStr($this->NavbarLoginIcon->value)) && (issetStr($this->NavbarLoginTitle->value)) ) {
							$s_navigation .= '<li><a href="' . $this->NavbarLoginLink->value . '"><span class="' . $this->NavbarIconClass->value . ' glyphicon ' . $this->NavbarLoginIcon->value . '"></span> ' . $this->NavbarLoginTitle->value . '</a></li>';
						}
					} else if ($this->NavbarShowLogoutPart->value) {
						if ( (issetStr($this->NavbarUserLink->value)) && (issetStr($this->NavbarIconClass->value)) && (issetStr($this->NavbarUserIcon->value)) && (issetStr($this->NavbarUserTitle->value)) ) {
							$s_navigation .= '<li><a href="' . $this->NavbarUserLink->value . '"><span class="' . $this->NavbarIconClass->value . ' glyphicon ' . $this->NavbarUserIcon->value . '"></span> ' . $this->NavbarUserTitle->value . '</a></li>';
						}
						
						if ( (issetStr($this->NavbarLogoutLink->value)) && (issetStr($this->NavbarIconClass->value)) && (issetStr($this->NavbarLogoutIcon->value)) && (issetStr($this->NavbarLogoutTitle->value)) ) {
							$s_navigation .= '<li><a href="' . $this->NavbarLogoutLink->value . '"><span class="' . $this->NavbarIconClass->value . ' glyphicon ' . $this->NavbarLogoutIcon->value . '"></span> ' . $this->NavbarLogoutTitle->value . '</a></li>';
						}
					}
					
					if ( ($this->NavbarShowLoginPart->value) || ($this->NavbarShowLogoutPart->value) ) {
						$s_navigation .= '</ul>' . "\n";
					}
					
				$s_navigation .= '</div>' . "\n";
			$s_navigation .= '</div>' . "\n";
		$s_navigation .= '</nav>' . "\n";
		
		if (issetStr($this->NavbarAlign->value)) {
			/* for fixed top navbar, add two break elements, for content not hidden behind navbar */
			if ($this->NavbarAlign->value == 'navbar-fixed-top') {
				$s_navigation .= '<br>' . "\n";
				$s_navigation .= '<br>' . "\n";
			}
		}
		
		echo $s_navigation;
	}
	
	private function RenderNavigationRecursive(forestNavigationNode $p_o_navigationNode, &$p_s_navigation, $p_i_level, $p_i_maxLevel) {
		$o_glob = forestGlobals::init();
		
		if ($p_i_level <= 0) {
			/* the zero flague is needed to differ between the main navigation nodes in the bar and their child-nodes */
			$b_zero = true;
			$p_o_navigationNode->Zero = true;
		} else {
			$b_zero = false;
		}
		
		if ( ($p_o_navigationNode->BranchName == 'index') || ($p_o_navigationNode->Up) ) {
			/* navigation top level */
			$p_s_navigation .= '<li';
			
			if ($o_glob->URL->BranchId == $p_o_navigationNode->BranchId) {
				/* mark active node */
				$p_s_navigation .= ' class="active"';
			}
			
			$p_s_navigation .= '>';
			
			/* render home node */
			$p_s_navigation .= $p_o_navigationNode;
			
			$p_s_navigation .= '</li>' . "\n";
			
			if ($p_o_navigationNode->NavigationNodes->Count() > 0) {
				foreach ($p_o_navigationNode->NavigationNodes as $o_navigationNode) {
					$this->RenderNavigationRecursive($o_navigationNode, $p_s_navigation, $p_i_level, $p_i_maxLevel);
				}
			}
		} else {
			$p_s_navigation .= '<li';
			
			/* calulate classes of navigation node element */
			$a_classes = array();
			
			if ($o_glob->URL->BranchId == $p_o_navigationNode->BranchId) {
				$a_classes[] = 'active';
			}
			
			if (($p_o_navigationNode->NavigationNodes->Count() > 0) && ($p_i_level <= $p_i_maxLevel)) {
				if ($b_zero) {
					$a_classes[] = 'dropdown';
				} else {
					$a_classes[] = 'dropdown-submenu';
				}
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
			
			/* render node */
			$p_s_navigation .= $p_o_navigationNode;
			
			if (!(($p_o_navigationNode->NavigationNodes->Count() > 0) && ($p_i_level <= $p_i_maxLevel))) {
				$p_s_navigation .= '</li>' . "\n";
			}
			
			/* render child-nodes if maxLevel is not exceeded */
			if (($p_o_navigationNode->NavigationNodes->Count() > 0) && ($p_i_level <= $p_i_maxLevel)) {
				$p_s_navigation .=  "\n" . '<ul class="dropdown-menu">' . "\n";
				$p_i_level++;
				
				foreach ($p_o_navigationNode->NavigationNodes as $o_navigationNode) {
					$this->RenderNavigationRecursive($o_navigationNode, $p_s_navigation, $p_i_level, $p_i_maxLevel);
				}
				
				$p_s_navigation .= "\n" . '</ul>' . "\n";
				$p_s_navigation .= '</li>' . "\n";
			}
		}
	}
	
	public function RenderLandingPage() {
		$s_navigation = '';
		
		/* render landing page navigation list */
		$s_navigation .= '<div class="container-fluid">' . "\n";
				$s_navigation .= '<ul class="fphp_tiles">' . "\n";
				
				/* recurse rendering of navigation node elements */
				if (!is_null($this->ParentNavigationNode->value)) {
					$this->RenderLandingPageRecursive($this->ParentNavigationNode->value, $s_navigation, 0, 1);
				} else {
					$this->RenderLandingPageRecursive($this->NavigationNode->value, $s_navigation, 0, 1);
				}
				
				$s_navigation .= '</ul>' . "\n";
		$s_navigation .= '</div>' . "\n";
		
		return $s_navigation;
	}
	
	private function RenderLandingPageRecursive(forestNavigationNode $p_o_navigationNode, &$p_s_navigation, $p_i_level, $p_i_maxLevel) {
		$o_glob = forestGlobals::init();
		
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