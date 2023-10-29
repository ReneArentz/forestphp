<?php
/**
 * template class using to print standard data elements
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestphp.de>
 * @copyright   (c) 2019 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.0.0 stable
 * @link        http://www.forestphp.de/
 * @object-id   0x1 0001B
 * @since       File available since Release 0.1.1 alpha
 * @deprecated  -
 *
 * @version log Version		Developer	Date		Comment
 * 		0.1.1 alpha	renatus		2019-08-15	added to framework
 * 		0.1.2 alpha	renatus		2019-08-25	added listview and view
 * 		0.1.4 alpha	renatus		2019-09-28	added sublistview
 * 		0.2.0 beta	renatus		2019-10-20	added create-new-branch and create-new-twig
 * 		0.6.0 beta	renatus		2019-12-20	added restoreFile additional actions for create-new-branch-with-twig
 * 		0.8.0 beta	renatus		2020-01-18	added fphp_flex functionality
 * 		0.9.0 beta	renatus		2020-01-30	changes for bootstrap 4
 * 		1.0.0 stable	renatus		2020-02-14	change all "use namespace" one-line commands to single "use namespace" commands because of PHP compatibility 5.x
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

class forestTemplates {
	use \fPHP\Roots\forestData;
	
	/* Fields */

	const LANDINGPAGE = 'landingpage';
	
	const LISTVIEW = 'listview';
	const LISTVIEWOPTIONSTOP = 'listviewoptionstop';
	const LISTVIEWOPTIONSDOWN = 'listviewoptionsdown';
	
	const VIEW = 'view';
	const VIEWOPTIONSTOP = 'viewoptionstop';
	const VIEWOPTIONSDOWN = 'viewoptionsdown';
	
	const FLEXVIEW = 'flexview';
	const FLEXVIEWTOP = 'flexviewtop';
	const FLEXVIEWDOWN = 'flexviewdown';
	const FLEXVIEWGENERALELEMENT = 'flexviewgeneralelement';
	const FLEXVIEWELEMENT = 'flexviewelement';
	const FLEXVIEWGENERALELEMENTREADONLY = 'flexviewgeneralelementreadonly';
	const FLEXVIEWELEMENTREADONLY = 'flexviewelementreadonly';
	
	const SUBLISTVIEW = 'sublistview';
	const SUBLISTVIEWITEM = 'sublistviewitem';
	const SUBLISTVIEWITEMCONTENT = 'sublistviewitemcontent';
	
	const CREATENEWBRANCH = 'createnewbranch';
	const CREATENEWBRANCHWITHTWIG = 'createnewbranchwithtwig';
	const CREATENEWTWIG = 'createnewtwig';

	private $Type;
	private $PlaceHolders;

	const LANDINGPAGETXT = <<< EOF
	%0
EOF;
	
	const LISTVIEWTXT = <<< EOF
	%0
	<div class="table-responsive">
		<table class="table table-hover table-selectable">
			<thead>
				<tr>
				%1
				</tr>
			</thead>
			<tbody id="%2">
			%3
			</tbody>
		</table>
	</div>
	%4
EOF;

const LISTVIEWOPTIONSTOPTXT = <<< EOF
	<div class="mb-2">
		<div class="row">
			<div class="col-sm-4">
				%0
			</div>
			<div class="col-sm-8">
				<div class="float-right">
					%1
				</div>
			</div>
		</div>
		
		<div class="row mt-2">
			<div class="col-sm-4">
				%2
			</div>
			<div class="col-sm-8">
				<div class="filter-terms text-center">
					%3
				</div>
			</div>
		</div>
	</div>
	%4
EOF;

	const LISTVIEWOPTIONSDOWNTXT = <<< EOF
	<div class="mt-2">
		<div class="row">
			<div class="col-sm-4">
				%0
			</div>
			<div class="col-sm-8">
				<div class="float-right">
					%1
				</div>
			</div>
		</div>
	</div>
EOF;

	const VIEWTXT = <<< EOF
	%0
	<h2>%1</h2>
	%2
	%3
	%4
EOF;

	const VIEWOPTIONSTOPTXT = <<< EOF
	<div class="mb-2">
		<div class="row">
			<div class="col-sm-4">
				%0
			</div>
			<div class="col-sm-8">
				<div class="float-right">
					%1
				</div>
			</div>
		</div>
		
		<div class="row mt-2">
			<div class="col-sm-4">
				%2
			</div>
			<div class="col-sm-8">
				<div class="filter-terms text-center">
					%3
				</div>
			</div>
		</div>
	</div>
EOF;

	const VIEWOPTIONSDOWNTXT = <<< EOF
	<div class="mt-2">
		<div class="row">
			<div class="col-sm-4">
				%0
			</div>
			<div class="col-sm-8">
				<div class="float-right">
					%1
				</div>
			</div>
		</div>
	</div>
EOF;

	const FLEXVIEWTXT = <<< EOF
	%0
	%1
	%2
EOF;

	const FLEXVIEWTOPTXT = <<< EOF
	<div class="mb-2">
		<div class="row">
			<div class="col-sm-4">
				%0
			</div>
			<div class="col-sm-8">
				<div class="float-right">
					%1
				</div>
			</div>
		</div>
		<div class="mt-2">
			<div class="col-sm-4">
				%2
			</div>
			<div class="col-sm-8">
				<div class="filter-terms text-center">
					%3
				</div>
			</div>
		</div>
	</div>
EOF;

	const FLEXVIEWDOWNTXT = <<< EOF
	<div class="mt-2">
		<div class="row">
			<div class="col-sm-4">
				%0
			</div>
			<div class="col-sm-8">
				<div class="float-right">
					%1
				</div>
			</div>
		</div>
	</div>
EOF;

	const FLEXVIEWGENERALELEMENTTXT = <<< EOF
	<div id="fphpFlexContainer" class="fphpFlexContainer" data-flexUUID="%0" data-flexURL="%1" style="width: %2px; height: %3px;">
EOF;

	const FLEXVIEWELEMENTTXT = <<< EOF
	<div id="%0_fphpFlex" class="%0_fphpFlex" data-flexUUID="%1" style="width: %2px; height: %3px; top: %4px; left: %5px;">%6</div>
EOF;

	const FLEXVIEWGENERALELEMENTREADONLYTXT = <<< EOF
	<div id="fphpFlexContainer_readonly" class="fphpFlexContainer_readonly" data-flexUUID="%0" data-flexURL="%1" style="height: %3px;">
EOF;

	const FLEXVIEWELEMENTREADONLYTXT = <<< EOF
	<div id="%0_fphpFlex_readonly" class="%0_fphpFlex_readonly" data-flexUUID="%1" style="width: %2px; height: %3px; top: %4px; left: %5px;">%6</div>
EOF;

	const SUBLISTVIEWTXT = <<< EOF
<div id="accordion">
	%0
</div>
EOF;

	const SUBLISTVIEWITEMTXT = <<< EOF
	<div class="card">
		<div class="card-header">
			<a class="card-link" data-toggle="collapse" href="#%0">
				<h5>%1</h5>
			</a>
		</div>
		<div id="%0" class="collapse%2" data-parent="#accordion">
			<div class="card-body">
				%3
			</div>
		</div>
	</div>
EOF;

	const SUBLISTVIEWITEMCONTENTTXT = <<< EOF
	%0
	<div class="table-responsive">
		<table class="table table-hover table-sm">
			<thead>
				<tr>
				%1
				</tr>
			</thead>
			<tbody>
			%2
			</tbody>
		</table>
	</div>
EOF;

	const CREATENEWBRANCHTXT = <<< EOF
<?php

namespace fPHP\\Branches;
use \\fPHP\\Roots\\forestString as forestString;
use \\fPHP\\Roots\\forestList as forestList;
use \\fPHP\\Roots\\forestNumericString as forestNumericString;
use \\fPHP\\Roots\\forestInt as forestInt;
use \\fPHP\\Roots\\forestFloat as forestFloat;
use \\fPHP\\Roots\\forestBool as forestBool;
use \\fPHP\\Roots\\forestArray as forestArray;
use \\fPHP\\Roots\\forestObject as forestObject;
use \\fPHP\\Roots\\forestLookup as forestLookup;

class %0Branch extends forestBranch {
	use \\fPHP\\Roots\\forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	protected function initBranch() {
		
	}
	
	protected function init() {
		\$this->GenerateLandingPage();
	}
}
?>
EOF;

	const CREATENEWBRANCHWITHTWIGTXT = <<< EOF
<?php

namespace fPHP\\Branches;
use \\fPHP\\Roots\\forestString as forestString;
use \\fPHP\\Roots\\forestList as forestList;
use \\fPHP\\Roots\\forestNumericString as forestNumericString;
use \\fPHP\\Roots\\forestInt as forestInt;
use \\fPHP\\Roots\\forestFloat as forestFloat;
use \\fPHP\\Roots\\forestBool as forestBool;
use \\fPHP\\Roots\\forestArray as forestArray;
use \\fPHP\\Roots\\forestObject as forestObject;
use \\fPHP\\Roots\\forestLookup as forestLookup;

class %0Branch extends forestBranch {
	use \\fPHP\Roots\\forestData;
	
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	protected function initBranch() {
		\$this->Filter->value = %2;
		\$this->StandardView = %3;
		\$this->KeepFilter->value = %4;
		
		\$this->Twig = new \\fPHP\\Twigs\\%1Twig();
	}
	
	protected function init() {
		\$o_glob = \\fPHP\\Roots\\forestGlobals::init();
		
		if (\$this->StandardView == forestBranch::DETAIL) {
			\$this->GenerateView();
		} else if (\$this->StandardView == forestBranch::LISTVIEW) {
			\$this->GenerateListView();
		} else if (\$this->StandardView == forestBranch::FLEX) {
			if ( (\$o_glob->Security->SessionData->Exists('lastView')) && (\$o_glob->URL->LastBranchId == \$o_glob->URL->BranchId) ) {
				if (\$o_glob->Security->SessionData->{'lastView'} == forestBranch::LISTVIEW) {
					\$this->GenerateView();
				} else if (\$o_glob->Security->SessionData->{'lastView'} == forestBranch::DETAIL) {
					\$this->GenerateListView();
				} else {
					\$this->GenerateFlexView();
				}
			} else {
				\$this->GenerateFlexView();
			}
		}
	}
	
		protected function beforeViewAction() {
			/* \$this->Twig holds current record */
		}
	
	protected function viewAction() {
		\$this->ViewRecord();
	}
	
		protected function afterViewAction() {
			/* \$this->Twig holds current record */
		}
	
	protected function viewFlexAction() {
		\$this->GenerateFlexView();
	}
	
	protected function editFlexAction() {
		\$this->EditFlexView();
	}
	
		protected function beforeNewAction() {
			/* \$this->Twig holds current record */
		}
		
			protected function beforeNewSubAction() {
				/* \$this->Twig holds current sub record */
			}
	
	protected function newAction() {
		\$this->NewRecord();
	}
	
			protected function afterNewSubAction() {
				/* \$this->Twig holds current sub record */
			}
	
		protected function afterNewAction() {
			/* \$this->Twig holds current record */
		}
		
		protected function beforeEditAction() {
			/* \$this->Twig holds current record */
		}
			
			protected function beforeEditSubAction() {
				/* \$this->Twig holds current sub record */
			}
	
	protected function editAction() {
		\$this->EditRecord();
	}
	
			protected function afterEditSubAction() {
				/* \$this->Twig holds current sub record */
			}
	
		protected function afterEditAction() {
			/* \$this->Twig holds current record */
		}
		
		protected function beforeDeleteAction() {
			/* \$this->Twig holds current record */
		}
		
			protected function beforeDeleteSubAction() {
				/* \$this->Twig holds current sub record */
			}
			
				protected function beforeDeleteFileAction() {
					/* \$this->Twig holds current file record */
				}
		
	protected function deleteAction() {
		\$this->DeleteRecord();
	}
				
				protected function afterDeleteFileAction() {
					/* \$this->Twig holds current file record */
				}
			
			protected function afterDeleteSubAction() {
				/* \$this->Twig holds current sub record */
			}
	
		protected function afterDeleteAction() {
			/* \$this->Twig holds current record */
		}
		
	protected function moveUpAction() {
		\$this->MoveUpRecord();
	}
	
	protected function moveDownAction() {
		\$this->MoveDownRecord();
	}
	
		protected function beforeReplaceFileAction() {
			/* \$this->Twig holds current file record */
		}
		
		protected function afterReplaceFileAction() {
			/* \$this->Twig holds current file record */
		}
		
		protected function beforeRestoreFileAction() {
			/* \$this->Twig holds current file record */
		}
		
		protected function afterRestoreFileAction() {
			/* \$this->Twig holds current file record */
		}
}
?>
EOF;

	const CREATENEWTWIGTXT = <<< EOF
<?php

namespace fPHP\Twigs;

use \\fPHP\\Roots\\forestString as forestString;
use \\fPHP\\Roots\\forestList as forestList;
use \\fPHP\\Roots\\forestNumericString as forestNumericString;
use \\fPHP\\Roots\\forestInt as forestInt;
use \\fPHP\\Roots\\forestFloat as forestFloat;
use \\fPHP\\Roots\\forestBool as forestBool;
use \\fPHP\\Roots\\forestArray as forestArray;
use \\fPHP\\Roots\\forestObject as forestObject;
use \\fPHP\\Roots\\forestLookup as forestLookup;
use \\fPHP\\Helper\\forestLookupData;

class %0Twig extends forestTwig {
	use \\fPHP\\Roots\\forestData;
	
	/* Fields */
	
	%1
	/* Properties */
	
	/* Methods */
	
	protected function init() {
		%2
		/* forestTwig system fields */
		\$this->fphp_Table->value = '%3';
		\$this->fphp_Primary->value = array(%4);
		\$this->fphp_Unique->value = array(%5);
		%6\$this->fphp_Interval->value = %7;
		\$this->fphp_View->value = array(%8);
		\$this->fphp_FillMapping(get_object_vars(\$this));
	}
}
?>
EOF;

	/* Properties */
	
	/**
	 * access to forestTemplate type value
	 *
	 * @return string  forestTemplate type
	 *
	 * @access public
	 * @static no
	 */
	public function getType() {
		return $this->Type->value;
	}
	
	/* Methods */
	
	/**
	 * constructor of forestTemplates class
	 *
	 * @param string $p_s_type  string value as constant pointer to desired template
	 * @param array $p_a_placeHolders  array list of values which should be inserted into the template
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function __construct($p_s_type, $p_a_placeHolders = null) {
		$this->Type = new forestString($p_s_type, false);
		$this->PlaceHolders = new forestArray;
		
		if ($p_a_placeHolders != null) {
			$this->PlaceHolders->value = $p_a_placeHolders;
		}
		
		switch ($this->Type->value) {
			case self::LANDINGPAGE:
				$this->Type->value = self::LANDINGPAGE;
			break;
			
			case self::LISTVIEW:
				$this->Type->value = self::LISTVIEW;
			break;
			case self::LISTVIEWOPTIONSTOP:
				$this->Type->value = self::LISTVIEWOPTIONSTOP;
			break;
			case self::LISTVIEWOPTIONSDOWN:
				$this->Type->value = self::LISTVIEWOPTIONSDOWN;
			break;
			
			case self::VIEW:
				$this->Type->value = self::VIEW;
			break;
			case self::VIEWOPTIONSTOP:
				$this->Type->value = self::VIEWOPTIONSTOP;
			break;
			case self::VIEWOPTIONSDOWN:
				$this->Type->value = self::VIEWOPTIONSDOWN;
			break;
			
			case self::FLEXVIEW:
				$this->Type->value = self::FLEXVIEW;
			break;
			case self::FLEXVIEWTOP:
				$this->Type->value = self::FLEXVIEWTOP;
			break;
			case self::FLEXVIEWDOWN:
				$this->Type->value = self::FLEXVIEWDOWN;
			break;
			case self::FLEXVIEWGENERALELEMENT:
				$this->Type->value = self::FLEXVIEWGENERALELEMENT;
			break;
			case self::FLEXVIEWELEMENT:
				$this->Type->value = self::FLEXVIEWELEMENT;
			break;
			case self::FLEXVIEWGENERALELEMENTREADONLY:
				$this->Type->value = self::FLEXVIEWGENERALELEMENTREADONLY;
			break;
			case self::FLEXVIEWELEMENTREADONLY:
				$this->Type->value = self::FLEXVIEWELEMENTREADONLY;
			break;
			
			case self::SUBLISTVIEW:
				$this->Type->value = self::SUBLISTVIEW;
			break;
			case self::SUBLISTVIEWITEM:
				$this->Type->value = self::SUBLISTVIEWITEM;
			break;
			case self::SUBLISTVIEWITEMCONTENT:
				$this->Type->value = self::SUBLISTVIEWITEMCONTENT;
			break;
			
			case self::CREATENEWBRANCH:
				$this->Type->value = self::CREATENEWBRANCH;
			break;
			case self::CREATENEWBRANCHWITHTWIG:
				$this->Type->value = self::CREATENEWBRANCHWITHTWIG;
			break;
			case self::CREATENEWTWIG:
				$this->Type->value = self::CREATENEWTWIG;
			break;
			
			default:
				throw new forestException('Invalid template type[%0]', array($this->Type->value));
			break;
		}
	}
	
	/**
	 * render method to print forestTemplate with values which should be inserted into template's content by sprintf2 helper function, usually dynamic values
	 *
	 * @return string
	 *
	 * @access public
	 * @static no
	 */
	public function __toString() {
		$s_foo = '';
		$s_pointer = strtoupper($this->Type->value) . 'TXT';
		$s_foo .= constant('\fPHP\Branches\forestTemplates::' . $s_pointer);
		
		if (count($this->PlaceHolders->value) > 0) {
			$s_foo = \fPHP\Helper\forestStringLib::sprintf2($s_foo, $this->PlaceHolders->value);
		}
		
		return $s_foo;
	}
}
?>