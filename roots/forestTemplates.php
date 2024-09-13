<?php
/**
 * template class using to print standard data elements
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestany.net>
 * @copyright   (c) 2024 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.1.0 stable
 * @link        https://forestany.net
 * @object-id   0x1 0001B
 * @since       File available since Release 0.1.1 alpha
 * @deprecated  -
 *
 * @version log Version			Developer	Date		Comment
 * 				0.1.1 alpha		renea		2019-08-15	added to framework
 * 				0.1.2 alpha		renea		2019-08-25	added listview and view
 * 				0.1.4 alpha		renea		2019-09-28	added sublistview
 * 				0.2.0 beta		renea		2019-10-20	added create-new-branch and create-new-twig
 * 				0.6.0 beta		renea		2019-12-20	added restoreFile additional actions for create-new-branch-with-twig
 * 				0.8.0 beta		renea		2020-01-18	added fphp_flex functionality
 * 				0.9.0 beta		renea		2020-01-30	changes for bootstrap 4
 * 				1.0.0 stable	renea		2020-02-14	change all "use namespace" one-line commands to single "use namespace" commands because of PHP compatibility 5.x
 * 				1.1.0 stable	renea		2024-05-02	simplified some templates
 * 				1.1.0 stable	renea		2024-05-02	added document library template
 * 				1.1.0 stable	renea		2024-05-02	added picture library template
 * 				1.1.0 stable	renea		2024-06-03	added slide gallery view
 * 				1.1.0 stable	renea		2024-07-05	added slide calender view
 * 				1.1.0 stable	renea		2024-08-08	added static page view
 * 				1.1.0 stable	renea		2024-08-10	changes for bootstrap 5
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
	
	const SLIDEGALLERYVIEW = 'slidegalleryview';
	const SLIDEGALLERYVIEWPART = 'slidegalleryviewpart';
	const SLIDECALENDERVIEW = 'slidecalenderview';
	const SLIDECALENDERVIEWPART = 'slidecalenderviewpart';
	const SECTIONCOUNTERVIEW = 'sectioncounterview';
	const SECTIONCOUNTERVIEWPART = 'sectioncounterviewpart';
	const SECTIONCOUNTERVIEWCARD = 'sectioncounterviewcard';
	const STATICPAGEVIEW = 'staticpageview';

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

	const DOCUMENTLIBRARY = 'documentlibrary';
	const PICTURELIBRARY = 'picturelibrary';
	const SLIDEGALLERY = 'slidegallery';
	const SLIDECALENDER = 'slidecalender';
	const SECTIONCOUNTER = 'sectioncounter';
	const STATICPAGE = 'staticpage';

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
				%1
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
	<div class="mt-3">
		<div class="row">
			<div class="col-sm-4">
				%0
			</div>
			<div class="col-sm-8">
				%1
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
				%1
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
	<div class="mt-3">
		<div class="row">
			<div class="col-sm-4">
				%0
			</div>
			<div class="col-sm-8">
				%1
			</div>
		</div>
	</div>
EOF;

	const SLIDEGALLERYVIEWTXT = <<< EOF
	%0
	
	<div class="container-xl container-xl-section h-100 overflow-auto">
		<div id="carousel%1Indicators" class="shadow carousel slide carousel-fade">

			<div class="carousel-indicators">
				%2
			</div>

			<div class="carousel-inner">
				%3
			</div>

			<button class="carousel-control-prev carousel-control-prev-slide" type="button" data-bs-target="#carousel%1Indicators" data-bs-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Previous</span>
			</button>
			<button class="carousel-control-next carousel-control-next-slide" type="button" data-bs-target="#carousel%1Indicators" data-bs-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Next</span>
			</button>
		</div>
	</div>

	%4
EOF;

	const SLIDEGALLERYVIEWPARTTXT = <<< EOF
		<div id="carousel%0Indicators" class="%1shadow carousel slide carousel-fade">

			<div class="carousel-indicators">
				%2
			</div>

			<div class="carousel-inner">
				%3
			</div>

			<button class="carousel-control-prev carousel-control-prev-slide" type="button" data-bs-target="#carousel%0Indicators" data-bs-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Previous</span>
			</button>
			<button class="carousel-control-next carousel-control-next-slide" type="button" data-bs-target="#carousel%0Indicators" data-bs-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Next</span>
			</button>
		</div>
EOF;

	const SLIDECALENDERVIEWTXT = <<< EOF
	%0
	
	<div class="container-xl container-xl-section h-100 overflow-auto">
		<div id="carousel%1IndicatorsCalender" class="%2carousel slide">
			<div class="carousel-inner">
				%3
			</div>

			<button class="carousel-control-prev carousel-control-prev-calender" type="button" data-bs-target="#carousel%1IndicatorsCalender" data-bs-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Previous</span>
			</button>
			<button class="carousel-control-next carousel-control-next-calender" type="button" data-bs-target="#carousel%1IndicatorsCalender" data-bs-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Next</span>
			</button>
		</div>
	</div>

	%4
EOF;

	const SLIDECALENDERVIEWPARTTXT = <<< EOF
		<div id="carousel%0IndicatorsCalender" class="%1carousel slide">
			<div class="carousel-inner">
				%2
			</div>

			<button class="carousel-control-prev carousel-control-prev-calender" type="button" data-bs-target="#carousel%0IndicatorsCalender" data-bs-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Previous</span>
			</button>
			<button class="carousel-control-next carousel-control-next-calender" type="button" data-bs-target="#carousel%0IndicatorsCalender" data-bs-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Next</span>
			</button>
		</div>
EOF;

	const SECTIONCOUNTERVIEWTXT = <<< EOF
	%0

	<div class="container-xl container-xl-section h-100 overflow-auto">
		<div class="row justify-content-center">
			<span id="scroll-to-%1"></span>
			%2
		</div>

		<script>
			function start%1() {
				document.querySelectorAll(".%1").forEach((counter, i) => { /* iterate each counter in section */
					if (counter.innerText == "0") { /* only do something if value is '0' */
						let j = 0;

						const fooBar = () => {
							/* calculte large increment steps with high data-counter-value number */
							j += Math.ceil( (parseInt(counter.getAttribute("data-counter-value")) / 100) );

							if (j <= parseInt(counter.getAttribute("data-counter-value"))) { /* still counting */
								/* set new value and request animation frame */
								counter.innerText = j;
								requestAnimationFrame(fooBar);
							} else { /* finished */
								counter.innerText = parseInt(counter.getAttribute("data-counter-value"));
							}
						};

						/* delay counter loop by 750 milliseconds */
						setTimeout(() => {
							fooBar();
						}, i * 750);
					}
				}); 
			} 

			start%1();
		</script>
	</div>

	%3
EOF;

	const SECTIONCOUNTERVIEWPARTTXT = <<< EOF
	<div class="%2row justify-content-center">
		<span id="scroll-to-%0"></span>
		%1
	</div>

	<script>
		function start%0() {
			document.querySelectorAll(".%0").forEach((counter, i) => { /* iterate each counter in section */
				if (counter.innerText == "0") { /* only do something if value is '0' */
					let j = 0;

					const fooBar = () => {
						/* calculte large increment steps with high data-counter-value number */
						j += Math.ceil( (parseInt(counter.getAttribute("data-counter-value")) / 100) );

						if (j <= parseInt(counter.getAttribute("data-counter-value"))) { /* still counting */
							/* set new value and request animation frame */
							counter.innerText = j;
							requestAnimationFrame(fooBar);
						} else { /* finished */
							counter.innerText = parseInt(counter.getAttribute("data-counter-value"));
						}
					};

					/* delay counter loop by 750 milliseconds */
					setTimeout(() => {
						fooBar();
					}, i * 750);
				}
			}); 
		} 

		function reset%0() {
			document.querySelectorAll(".%0").forEach((counter) => { /* iterate each counter in section */
				/* set value to '0' */
				counter.innerText = "0";
			});
		}
		
		$(window).scroll(function() {
			/* calculate if the section counter is in the current viewport */
			if (
				$(this).scrollTop() > ($('#scroll-to-%0').offset().top + $('#scroll-to-%0').outerHeight() - $(window).height()) && 
				($('#scroll-to-%0').offset().top > $(this).scrollTop()) && 
				($(this).scrollTop() + $(window).height() > $('#scroll-to-%0').offset().top + $('#scroll-to-%0').outerHeight())
			) {
				start%0();
			} else {
				%3reset%0();
			}
		});
	</script>
EOF;

		const SECTIONCOUNTERVIEWCARDTXT = <<< EOF
			<div class="col-md-4 mb-3">
				<div class="card text-center bg-light text-dark rounded bg-opacity-90 p-3 ms-4 me-4 ms-md-1 me-md-1">
					<div class="card-body">
						<div class="card-title" style="font-size: 42px;"><span class="%5"></span></div>
						<h3 class="card-text">%2 <span class="%0" data-counter-value="%1">0</span> %3</h3>
						<p class="card-title fs-5 text-dark">%4</p>
					</div>
				</div>
			</div>
EOF;

const STATICPAGEVIEWTXT = <<< EOF
	%0
	%1
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
				%1
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

	const FLEXVIEWDOWNTXT = <<< EOF
	<div class="mt-3">
		<div class="row">
			<div class="col-sm-4">
				%0
			</div>
			<div class="col-sm-8">
				%1
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
	<div class="accordion" id="accordion_%0">
		%1
	</div>
EOF;

	const SUBLISTVIEWITEMTXT = <<< EOF
	<div class="accordion-item">
		<h2 class="accordion-header">
			<button class="accordion-button bg-light fs-5 fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#%0" aria-expanded="false" aria-controls="%0">
			%1
			</button>
		</h2>
		<div id="%0" class="accordion-collapse collapse%2" data-bs-parent="#accordion_%4">
			<div class="accordion-body">
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
	
	protected function initAction() {
		\$this->Init();
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
	
	protected function initAction() {
		\$this->Init();
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

/*	const multiple_branches_example = <<< EOF
[
	{
		"action" : "newBranch",
		"data" : {
			"sys_fphp_branch_Name" : "asd",
			"sys_fphp_branch_Title" : "ASD",
			"sys_fphp_branch_Navigation" : "1",
			"sys_fphp_branch_Filename" : "" ,
			"sys_fphp_branch_StandardView" : "b9799d31-a3d9-8898-6507-f1fbc48492c0",
			"sys_fphp_branch_Template" : "NULL"
		}
	},
	{
		"action" : "PARENTBRANCH"
	},
	{
		"action" : "newBranch",
		"data" : {
			"sys_fphp_branch_Name" : "qwe",
			"sys_fphp_branch_Title" : "qwe",
			"sys_fphp_branch_Navigation" : "1",
			"sys_fphp_branch_Filename" : "" ,
			"sys_fphp_branch_StandardView" : "b9799d31-a3d9-8898-6507-f1fbc48492c0",
			"sys_fphp_branch_Template" : "NULL"
		}
	}
]	
EOF;*/

	const DOCUMENTLIBRARYTXT = <<< EOF
[
	{
		"action" : "newIdentifier",
		"data" : {
			"sys_fphp_identifier_IdentifierName" : "%0 Docs ID",
			"sys_fphp_identifier_IdentifierStart" : "DOCS0001",
			"sys_fphp_identifier_IdentifierIncrement" : "1"
		},
		"remember" : {
			"ActionChain_Identifier_UUID" : "UUID"
		}
	},
	{
		"action" : "newTwig",
		"data" : {
			"sys_fphp_table_name" : "%1",
			"sys_fphp_table_identifier" : "ActionChain_Identifier_UUID",
			"sys_fphp_table_infoColumns" : "1000",
			"sys_fphp_table_versioning" : "100",
			"sys_fphp_tablefield_FieldName" : "File",
			"sys_fphp_tablefield_FormElementUUID" : "ac1e2fd2-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "d8796ff4-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "1098ab89-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formFileLabel=%2:#\",\"SortHeader\":\"#sortFile=%2#\",\"ValMessage\":\"#formFileValMessage=%3#\",\"ValidationRule\":\"required\"}"
		},
		"remember" : {
			"ActionChain_TableUUID" : "LU_TableUUID",
			"ActionChain_Field_One" : "UUID"
		}
	},
	{
		"action" : "newTwigField",
		"data" : {
			"sys_fphp_tablefield_FieldName" : "Name",
			"sys_fphp_tablefield_FormElementUUID" : "ac1c4d63-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "NULL",
			"sys_fphp_tablefield_ForestDataUUID" : "63e9e10d-6011-11e9-a2c6-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formNameLabel=%4:#\",\"SortHeader\":\"#sortName=%4#\",\"forestCombination\":\"FILENAME(File)\"}"
		},
		"remember" : {
			"ActionChain_Field_Two" : "UUID"
		}
	},
	{
		"action" : "newTwigField",
		"data" : {
			"sys_fphp_tablefield_FieldName" : "Version",
			"sys_fphp_tablefield_FormElementUUID" : "ac1c4d63-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "NULL",
			"sys_fphp_tablefield_ForestDataUUID" : "63e9e10d-6011-11e9-a2c6-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formVersionLabel=%5:#\",\"SortHeader\":\"#sortVersion=%5#\",\"forestCombination\":\"FILEVERSION(File)\"}"
		},
		"remember" : {
			"ActionChain_Field_Three" : "UUID"
		}
	},
	{
		"action" : "moveDownTwigField",
		"data" : {
			"urlParameterNames" : ["editKey"],
			"urlParameterValues" : ["ActionChain_Field_One"]
		}
	},
	{
		"action" : "moveDownTwigField",
		"data" : {
			"urlParameterNames" : ["editKey"],
			"urlParameterValues" : ["ActionChain_Field_One"]
		}
	},
	{
		"action" : "editTwig",
		"data" : {
			"sys_fphp_tableKey" : "ActionChain_TableUUID",
			"sys_fphp_table_Interval" : "50",
			"sys_fphp_table_View" : [
				"ActionChain_Field_Two",
				"ActionChain_Field_Three",
				"ActionChain_Field_One"
			],
			"sys_fphp_table_SortColumn" : "NULL",
			"sys_fphp_table_InfoColumns" : "1000",
			"sys_fphp_table_Versioning" : "100",
			"sys_fphp_table_CheckoutInterval" : ""
		}
	},
	{
		"action" : "RELOADBRANCH",
		"actionAfterReload" : "init"
	}
]
EOF;

	const PICTURELIBRARYTXT = <<< EOF
[
	{
		"action" : "newIdentifier",
		"data" : {
			"sys_fphp_identifier_IdentifierName" : "%0 Pictures ID",
			"sys_fphp_identifier_IdentifierStart" : "PIC0001",
			"sys_fphp_identifier_IdentifierIncrement" : "1"
		},
		"remember" : {
			"ActionChain_Identifier_UUID" : "UUID"
		}
	},
	{
		"action" : "newTwig",
		"data" : {
			"sys_fphp_table_name" : "%1",
			"sys_fphp_table_identifier" : "ActionChain_Identifier_UUID",
			"sys_fphp_table_infoColumns" : "1000",
			"sys_fphp_table_infoColumnsView" : [
				"0001",
				"0100"
			],
        	"sys_fphp_table_versioning" : "1",
			"sys_fphp_tablefield_FieldName" : "Picture",
			"sys_fphp_tablefield_FormElementUUID" : "ac1e2fd2-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "d8796ff4-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "1098ab89-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formPictureLabel=%2:#\",\"SortHeader\":\"#sortPicture=%2#\",\"ValMessage\":\"#formPictureValMessage=%3#\",\"Accept\":\"image/*\",\"Capture\":\"camera\",\"Thumbnail\":true,\"ThumbnailMaxWidth\":\"120\",\"FilenameFromField\":\"Title\",\"ShowInDetailView\":true,\"ShowInDetailViewMaxWidth\":\"560\"}"
		},
		"remember" : {
			"ActionChain_TableUUID" : "LU_TableUUID",
			"ActionChain_Field_One" : "UUID"
		}
	},
	{
		"action" : "newTwigField",
		"data" : {
			"sys_fphp_tablefield_FieldName" : "Title",
			"sys_fphp_tablefield_FormElementUUID" : "ac1c4d63-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "d8796ff4-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "1098ab89-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formTitleLabel=%4:#\",\"SortHeader\":\"#sortTitle=%4#\",\"ValMessage\":\"#formTitleValMessage=%5#\",\"Placeholder\":\"#formTitlePlaceholder=%6#\",\"ValidationRule\":\"required\"}"
		},
		"remember" : {
			"ActionChain_Field_Two" : "UUID"
		}
	},
	{
		"action" : "editTwig",
		"data" : {
			"sys_fphp_tableKey" : "ActionChain_TableUUID",
			"sys_fphp_table_Interval" : "50",
			"sys_fphp_table_View" : [
				"ActionChain_Field_One",
				"ActionChain_Field_Two"
			],
			"sys_fphp_table_SortColumn" : "NULL",
			"sys_fphp_table_InfoColumns" : "1000",
			"sys_fphp_table_InfoColumnsView" : [
				"0001",
				"0100"
			],
			"sys_fphp_table_Versioning" : "1",
			"sys_fphp_table_CheckoutInterval" : ""
		}
	},
	{
		"action" : "RELOADBRANCH",
		"actionAfterReload" : "init"
	}
]
EOF;

	const SLIDEGALLERYTXT = <<< EOF
[
	{
		"action" : "newIdentifier",
		"data" : {
			"sys_fphp_identifier_IdentifierName" : "%0 Slide Gallery ID",
			"sys_fphp_identifier_IdentifierStart" : "SLIDE0001",
			"sys_fphp_identifier_IdentifierIncrement" : "1",
			"sys_fphp_identifier_UseAsSortColumn" : "1"
		},
		"remember" : {
			"ActionChain_Identifier_UUID" : "UUID"
		}
	},
	{
		"action" : "newTwig",
		"data" : {
			"sys_fphp_table_name" : "%1",
			"sys_fphp_table_identifier" : "ActionChain_Identifier_UUID",
			"sys_fphp_table_infoColumns" : "1000",
			"sys_fphp_table_infoColumnsView" : [
				"0001",
				"0100"
			],
        	"sys_fphp_table_versioning" : "1",
			"sys_fphp_tablefield_FieldName" : "Slide",
			"sys_fphp_tablefield_FormElementUUID" : "ac1e2fd2-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "d8796ff4-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "1098ab89-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formSlideLabel=%2:#\",\"SortHeader\":\"#sortSlide=%2#\",\"ValMessage\":\"#formSlideValMessage=%3#\",\"Accept\":\"image/*\",\"Capture\":\"camera\",\"Thumbnail\":true,\"ThumbnailMaxWidth\":\"120\",\"FilenameFromField\":\"Title\",\"ShowInDetailView\":true,\"ShowInDetailViewMaxWidth\":\"560\"}"
		},
		"remember" : {
			"ActionChain_TableUUID" : "LU_TableUUID",
			"ActionChain_Field_One" : "UUID"
		}
	},
	{
		"action" : "newTwigField",
		"data" : {
			"sys_fphp_tablefield_FieldName" : "Title",
			"sys_fphp_tablefield_FormElementUUID" : "ac1c4d63-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "d8796ff4-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "1098ab89-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formTitleLabel=%4:#\",\"SortHeader\":\"#sortTitle=%4#\",\"ValMessage\":\"#formTitleValMessage=%5#\",\"Placeholder\":\"#formTitlePlaceholder=%6#\",\"ValidationRule\":\"required\"}"
		},
		"remember" : {
			"ActionChain_Field_Two" : "UUID"
		}
	},
	{
		"action" : "editTwig",
		"data" : {
			"sys_fphp_tableKey" : "ActionChain_TableUUID",
			"sys_fphp_table_Interval" : "50",
			"sys_fphp_table_View" : [
				"ActionChain_Field_One",
				"ActionChain_Field_Two"
			],
			"sys_fphp_table_SortColumn" : "NULL",
			"sys_fphp_table_InfoColumns" : "1000",
			"sys_fphp_table_InfoColumnsView" : [
				"0001",
				"0100"
			],
			"sys_fphp_table_Versioning" : "1",
			"sys_fphp_table_CheckoutInterval" : ""
		}
	},
	{
		"action" : "RELOADBRANCH",
		"actionAfterReload" : "init"
	}
]
EOF;

	const SLIDECALENDERTXT = <<< EOF
[
	{
		"action" : "newTwig",
		"data" : {
			"sys_fphp_table_name" : "%0",
			"sys_fphp_table_infoColumns" : "1000",
			"sys_fphp_table_versioning" : "1",
			"sys_fphp_tablefield_FieldName" : "Start",
			"sys_fphp_tablefield_FormElementUUID" : "ac20a373-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "15937c51-4718-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "3e0f992f-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formStartLabel=%1:#\",\"SortHeader\":\"#sortStart=%1#\",\"ValMessage\":\"#formStartValMessage=%2#\",\"ValidationRule\":\"fphp_datetimeISO\",\"ValidationRuleParam01\":\"true\",\"ValidationRuleAutoRequired\":\"true\"}"
		},
		"remember" : {
			"ActionChain_TableUUID" : "LU_TableUUID",
			"ActionChain_Field_One" : "UUID"
		}
	},
	{
		"action" : "newTwigField",
		"data" : {
			"sys_fphp_tablefield_FieldName" : "End",
			"sys_fphp_tablefield_FormElementUUID" : "ac20a373-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "15937c51-4718-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "3e0f992f-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formEndLabel=%3:#\",\"SortHeader\":\"#sortEnd#\",\"ValMessage\":\"#formEndValMessage=%5#\",\"ValidationRule\":\"fphp_datetimeISO\",\"Description\":\"#formEndDescription=%6#\",\"DescriptionClass\":\"text-end text-info\"}"
		},
		"remember" : {
			"ActionChain_Field_Two" : "UUID"
		}
	},
	{
		"action" : "newTwigField",
		"data" : {
			"sys_fphp_tablefield_FieldName" : "Event",
			"sys_fphp_tablefield_FormElementUUID" : "ac1c4d63-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "d8796ff4-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "1098ab89-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formEventLabel=%7:#\",\"SortHeader\":\"#sortEvent=%7#\",\"ValMessage\":\"#formEventValMessage=%8#\",\"Placeholder\":\"#formEventPlaceholder=%9#\",\"ValidationRule\":\"required\"}"
		},
		"remember" : {
			"ActionChain_Field_Three" : "UUID"
		}
	},
	{
		"action" : "newTwigField",
		"data" : {
			"sys_fphp_tablefield_FieldName" : "Highlight",
			"sys_fphp_tablefield_FormElementUUID" : "ac1ed318-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "159489e2-4718-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "1e7bae52-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formHighlightLabel= #\",\"SortHeader\":\"#sortHighlight=%10#\",\"ValMessage\":\"#formHighlightValMessage=%11#\",\"CheckboxContainerClass\":\"form-check form-switch position-relative\",\"Options\":{\"#formHighlightOptionLabel00=%10#\":\"1\"}}"
		}
	},
	{
		"action" : "newTwigField",
		"data" : {
			"sys_fphp_tablefield_FieldName" : "Italic",
			"sys_fphp_tablefield_FormElementUUID" : "ac1ed318-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "159489e2-4718-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "1e7bae52-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formItalicLabel= #\",\"SortHeader\":\"#sortItalic=%12#\",\"ValMessage\":\"#formItalicValMessage=%13#\",\"CheckboxContainerClass\":\"form-check form-switch position-relative\",\"Options\":{\"#formItalicOptionLabel00=%12#\":\"1\"}}"
		}
	},
	{
		"action" : "newTwigField",
		"data" : {
			"sys_fphp_tablefield_FieldName" : "Color",
			"sys_fphp_tablefield_FormElementUUID" : "ac239d7f-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "d8796ff4-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "1098ab89-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formColorLabel=%14:#\",\"SortHeader\":\"#sortColor=%14#\",\"ValMessage\":\"#formColorValMessage=%15#\",\"Options\":{\"#formColorOptionLabel00=%16#\":\"text-primary\",\"#formColorOptionLabel01=%17#\":\"text-secondary\",\"#formColorOptionLabel02=%18#\":\"text-success\",\"#formColorOptionLabel03=%19#\":\"text-danger\",\"#formColorOptionLabel04=%20#\":\"text-warning\",\"#formColorOptionLabel05=%21#\":\"text-info\",\"#formColorOptionLabel06=%22#\":\"text-dark\",\"#formColorOptionLabel07=%23#\":\"text-muted\"}}"
		}
	},
	{
		"action" : "newTranslation",
		"data" : {
			"sys_fphp_translation_LanguageCode" : "%24",
    		"sys_fphp_translation_Name" : "sortEnd",
    		"sys_fphp_translation_Value" : "%4"
		}
	},
	{
		"action" : "newTranslation",
		"data" : {
			"sys_fphp_translation_LanguageCode" : "%24",
    		"sys_fphp_translation_Name" : "sortDate",
    		"sys_fphp_translation_Value" : "%25"
		}
	},
	{
		"action" : "newUnique",
		"data" : {
			"sys_fphp_table_uniqueName" : "unique_start_event",
    		"sys_fphp_table_Unique" : [
				"ActionChain_Field_One",
				"ActionChain_Field_Three"
			]
		}
	},
	{
		"action" : "newSort",
		"data" : {
			"sys_fphp_table_sortDirection" : "true",
    		"sys_fphp_table_SortOrder" : "ActionChain_Field_One"
    	}
	},
	{
		"action" : "editTwig",
		"data" : {
			"sys_fphp_tableKey" : "ActionChain_TableUUID",
			"sys_fphp_table_Interval" : "50",
			"sys_fphp_table_View" : [
				"ActionChain_Field_One",
				"ActionChain_Field_Two",
				"ActionChain_Field_Three"
			],
			"sys_fphp_table_SortColumn" : "NULL",
			"sys_fphp_table_InfoColumns" : "1000",
			"sys_fphp_table_Versioning" : "1",
			"sys_fphp_table_CheckoutInterval" : ""
		}
	},
	{
		"action" : "RELOADBRANCH",
		"actionAfterReload" : "init"
	}
]
EOF;

	const SECTIONCOUNTERTXT = <<< EOF
[
	{
		"action" : "newIdentifier",
		"data" : {
			"sys_fphp_identifier_IdentifierName" : "%0 Section Counter ID",
			"sys_fphp_identifier_IdentifierStart" : "CNT001",
			"sys_fphp_identifier_IdentifierIncrement" : "1",
			"sys_fphp_identifier_UseAsSortColumn" : "1"
		},
		"remember" : {
			"ActionChain_Identifier_UUID" : "UUID"
		}
	},
	{
		"action" : "newTwig",
		"data" : {
			"sys_fphp_table_name" : "%1",
			"sys_fphp_table_identifier" : "ActionChain_Identifier_UUID",
			"sys_fphp_table_infoColumns" : "1000",
			"sys_fphp_table_infoColumnsView" : [
				"0001",
				"0100"
			],
        	"sys_fphp_table_versioning" : "1",
			"sys_fphp_tablefield_FieldName" : "CounterValue",
			"sys_fphp_tablefield_FormElementUUID" : "ac21769b-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "1592a0e1-4718-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "18d92e02-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formCounterValueLabel=%2:#\",\"SortHeader\":\"#sortCounterValue=%2#\",\"ValMessage\":\"#formCounterValueValMessage=%3#\",\"Placeholder\":\"#formCounterValuePlaceholder=%4#\",\"ValidationRule\":\"digits\",\"ValidationRuleParam01\":\"true\",\"ValidationRuleAutoRequired\":\"true\",\"Description\":\"#formCounterValueDescription=%5#\",\"DescriptionClass\":\"text-end text-info\"}"
		},
		"remember" : {
			"ActionChain_TableUUID" : "LU_TableUUID",
			"ActionChain_Field_One" : "UUID"
		}
	},
	{
		"action" : "newTwigField",
		"data" : {
			"sys_fphp_tablefield_FieldName" : "Prefix",
			"sys_fphp_tablefield_FormElementUUID" : "ac1c4d63-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "d8796ff4-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "1098ab89-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formPrefixLabel=%6:#\",\"SortHeader\":\"#sortPrefix=%6#\",\"ValMessage\":\"#formPrefixValMessage=%7#\",\"Placeholder\":\"#formPrefixPlaceholder=%8#\",\"Description\":\"#formPrefixDescription=%9#\",\"DescriptionClass\":\"text-end text-info\"}"
		},
		"remember" : {
			"ActionChain_Field_Two" : "UUID"
		}
	},
	{
		"action" : "newTwigField",
		"data" : {
			"sys_fphp_tablefield_FieldName" : "Suffix",
			"sys_fphp_tablefield_FormElementUUID" : "ac1c4d63-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "d8796ff4-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "1098ab89-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formSuffixLabel=%10:#\",\"SortHeader\":\"#sortSuffix=%10#\",\"ValMessage\":\"#formSuffixValMessage=%11#\",\"Placeholder\":\"#formSuffixPlaceholder=%12#\",\"Description\":\"#formSuffixDescription=%13#\",\"DescriptionClass\":\"text-end text-info\"}"
		},
		"remember" : {
			"ActionChain_Field_Three" : "UUID"
		}
	},
	{
		"action" : "newTwigField",
		"data" : {
			"sys_fphp_tablefield_FieldName" : "Icon",
			"sys_fphp_tablefield_FormElementUUID" : "ac1c4d63-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "d8796ff4-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "1098ab89-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formIconLabel=%14:#\",\"SortHeader\":\"#sortIcon=%14#\",\"ValMessage\":\"#formIconValMessage=%15#\",\"Placeholder\":\"#formIconPlaceholder=%16#\",\"ValidationRule\":\"required\",\"Description\":\"#formIconDescription=%17#\",\"DescriptionClass\":\"text-end text-info\"}"
		},
		"remember" : {
			"ActionChain_Field_Four" : "UUID"
		}
	},
	{
		"action" : "newTwigField",
		"data" : {
			"sys_fphp_tablefield_FieldName" : "Text",
			"sys_fphp_tablefield_FormElementUUID" : "ac1c4d63-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "d8796ff4-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "1098ab89-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formTextLabel=%18:#\",\"SortHeader\":\"#sortText=%18#\",\"ValMessage\":\"#formTextValMessage=%19#\",\"Placeholder\":\"#formTextPlaceholder=%20#\",\"ValidationRule\":\"required\",\"Description\":\"#formTextDescription=%21#\",\"DescriptionClass\":\"text-end text-info\"}"
		},
		"remember" : {
			"ActionChain_Field_Five" : "UUID"
		}
	},
	{
		"action" : "newTwigField",
		"data" : {
			"sys_fphp_tablefield_FieldName" : "RestartCounters",
			"sys_fphp_tablefield_FormElementUUID" : "ac1ed318-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "159489e2-4718-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "1e7bae52-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formRestartCountersLabel= #\",\"SortHeader\":\"#sortRestartCounters=%22#\",\"ValMessage\":\"#formRestartCountersValMessage=%23#\",\"Description\":\"#formRestartCountersDescription=%24#\",\"DescriptionClass\":\"text-end text-info\",\"CheckboxContainerClass\":\"custom-control custom-switch\",\"Options\":{\"#formRestartCountersOptionLabel00=%22#\":\"1\"}}"
		},
		"remember" : {
			"ActionChain_Field_Six" : "UUID"
		}
	},
	{
		"action" : "editTwig",
		"data" : {
			"sys_fphp_tableKey" : "ActionChain_TableUUID",
			"sys_fphp_table_Interval" : "50",
			"sys_fphp_table_View" : [
				"ActionChain_Field_One",
				"ActionChain_Field_Four",
				"ActionChain_Field_Five",
				"ActionChain_Field_Six"
			],
			"sys_fphp_table_SortColumn" : "NULL",
			"sys_fphp_table_InfoColumns" : "1000",
			"sys_fphp_table_Versioning" : "1",
			"sys_fphp_table_CheckoutInterval" : ""
		}
	},
	{
		"action" : "RELOADBRANCH",
		"actionAfterReload" : "init"
	}
]
EOF;

	const STATICPAGETXT = <<< EOF
[
	{
		"action" : "newIdentifier",
		"data" : {
			"sys_fphp_identifier_IdentifierName" : "%0 Sections ID",
			"sys_fphp_identifier_IdentifierStart" : "SEC001",
			"sys_fphp_identifier_IdentifierIncrement" : "1",
			"sys_fphp_identifier_UseAsSortColumn" : "1"
		},
		"remember" : {
			"ActionChain_Identifier_UUID" : "UUID"
		}
	},
	{
		"action" : "newTwig",
		"data" : {
			"sys_fphp_table_name" : "%1",
			"sys_fphp_table_identifier" : "ActionChain_Identifier_UUID",
			"sys_fphp_table_infoColumns" : "1000",
			"sys_fphp_table_infoColumnsView" : [
				"0001",
				"0100"
			],
        	"sys_fphp_table_versioning" : "1",
			"sys_fphp_tablefield_FieldName" : "Section",
			"sys_fphp_tablefield_FormElementUUID" : "ac1c4d63-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "d8796ff4-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "1098ab89-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formSectionLabel=%2:#\",\"SortHeader\":\"#sortSection=%2#\",\"ValMessage\":\"#formSectionValMessage=%3#\",\"Placeholder\":\"#formSectionPlaceholder=%4#\",\"ValidationRule\":\"required\",\"Description\":\"#formSectionDescription=%5#\",\"DescriptionClass\":\"text-end text-info\"}"
		},
		"remember" : {
			"ActionChain_TableUUID" : "LU_TableUUID",
			"ActionChain_Field_One" : "UUID"
		}
	},
	{
		"action" : "newTwigField",
		"data" : {
			"sys_fphp_tablefield_FieldName" : "Content",
			"sys_fphp_tablefield_FormElementUUID" : "ac2447f7-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "fdf82909-ef94-8023-bf4c-4f366ddf9d71",
			"sys_fphp_tablefield_ForestDataUUID" : "1098ab89-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\" : \"#formContentLabel=%6:#\",\"SortHeader\" : \"#sortContent=%6#\",\"Id\" : \"fphp_%1_DivRichtext\",\"HiddenId\" : \"fphp_%1_Content\",\"ToolbarId\" : \"fphp_richtext-toolbar\",\"DataCommand\" : \"command\",\"HighlightToolbarBtnClass\" : \"btn-info\",\"CreateLink\" : true,\"CreateLinkQuestion\" : \"#001.formRichtextLinkQuestion#\",\"CreateLinkValue\" : \"#001.formRichtextLinkValue#\",\"DropImage\" : false,\"AskImageSize\" : false,\"ImagesWidth\" : 240,\"ImagesHeight\" : 160,\"ImageWidthQuestion\" : \"#001.formRichtextWidthQuestion#\",\"ImageHeightQuestion\" : \"#001.formRichtextHeightQuestion#\",\"UndoAndRedo\" : true,\"Description\":\"#formContentDescription=%7#\",\"DescriptionClass\":\"text-end text-info\"}"
		},
		"remember" : {
			"ActionChain_Field_Two" : "UUID"
		}
	},
	{
		"action" : "newTwigField",
		"data" : {
			"sys_fphp_tablefield_FieldName" : "Background",
			"sys_fphp_tablefield_FormElementUUID" : "ac1e2fd2-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "d8796ff4-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "1098ab89-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formBackgroundLabel=%8:#\",\"SortHeader\":\"#sortBackground=%8#\",\"ValMessage\":\"#formBackgroundValMessage=%9#\",\"Description\":\"#formBackgroundDescription=%10#\",\"DescriptionClass\":\"text-end text-info\"}"
		},
		"remember" : {
			"ActionChain_Field_Three" : "UUID"
		}
	},
	{
		"action" : "newTwigField",
		"data" : {
			"sys_fphp_tablefield_FieldName" : "BackgroundColor",
			"sys_fphp_tablefield_FormElementUUID" : "ac1f3289-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "d8796ff4-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "1098ab89-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formBackgroundColorLabel=%11:#\",\"SortHeader\":\"#sortBackgroundColor=%11#\",\"ValMessage\":\"#formBackgroundColorValMessage=%12#\",\"Description\":\"#formBackgroundColorDescription=%13#\",\"DescriptionClass\":\"text-end text-info\"}"
		},
		"remember" : {
			"ActionChain_Field_Four" : "UUID"
		}
	},
	{
		"action" : "newTwigField",
		"data" : {
			"sys_fphp_tablefield_FieldName" : "Type",
			"sys_fphp_tablefield_FormElementUUID" : "ac1e7f1c-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "159229d5-4718-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "18d92e02-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formTypeLabel=%14:#\",\"SortHeader\":\"#sortType=%14#\",\"ValMessage\":\"#formTypeValMessage=%15#\",\"Description\":\"#formTypeDescription=%16#\",\"DescriptionClass\":\"text-end text-info\",\"ValidationRule\":\"required\",\"RadioContainerClass\":\"form-check form-check-inline\",\"Options\":{\"#sortContent#\":\"00001\",\"#000.formTemplateLabel01#\":\"00010\",\"#000.formTemplateLabel02#\":\"00100\",\"#000.formTemplateLabel03#\":\"01000\",\"#000.formTemplateLabel04#\":\"10000\"}}"
		},
		"remember" : {
			"ActionChain_Field_Five" : "UUID"
		}
	},
	{
		"action" : "newTwigField",
		"data" : {
			"sys_fphp_tablefield_FieldName" : "Source",
			"sys_fphp_tablefield_FormElementUUID" : "3bb71d43-4ca4-11e9-bc30-1062e50d1fcb",
			"sys_fphp_tablefield_SqlTypeUUID" : "d8796ff4-4717-11e9-8210-1062e50d1fcb",
			"sys_fphp_tablefield_ForestDataUUID" : "faf27830-4c9a-11e9-bc30-1062e50d1fcb",
			"sys_fphp_tablefield_TabId" : "general",
			"sys_fphp_tablefield_JSONEncodedSettings" : "{\"Label\":\"#formSourceLabel=%17:#\",\"SortHeader\":\"#sortSource=%17#\",\"Description\":\"#formSourceDescription=%18#\",\"DescriptionClass\":\"text-end text-info\",\"forestLookupDataTable\":\"sys_fphp_branch\",\"forestLookupDataPrimary\":[\"Id\"],\"forestLookupDataLabel\":[\"Name\",\"Title\"],\"forestLookupDataFilter\":{\"ParentBranch\":%19,\"!1Id\":%20}}"
		},
		"remember" : {
			"ActionChain_Field_Six" : "UUID"
		}
	},
	{
		"action" : "editTwig",
		"data" : {
			"sys_fphp_tableKey" : "ActionChain_TableUUID",
			"sys_fphp_table_Interval" : "50",
			"sys_fphp_table_View" : [
				"ActionChain_Field_One"
			],
			"sys_fphp_table_InfoColumns" : "1000",
			"sys_fphp_table_InfoColumnsView" : [
				"0001",
				"0100"
			],
			"sys_fphp_table_Versioning" : "1",
			"sys_fphp_table_CheckoutInterval" : ""
		}
	},
	{
		"action" : "RELOADBRANCH",
		"actionAfterReload" : "init"
	}
]
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
			
			case self::SLIDEGALLERYVIEW:
				$this->Type->value = self::SLIDEGALLERYVIEW;
			break;
			case self::SLIDEGALLERYVIEWPART:
				$this->Type->value = self::SLIDEGALLERYVIEWPART;
			break;
			case self::SLIDECALENDERVIEW:
				$this->Type->value = self::SLIDECALENDERVIEW;
			break;
			case self::SLIDECALENDERVIEWPART:
				$this->Type->value = self::SLIDECALENDERVIEWPART;
			break;
			case self::SECTIONCOUNTERVIEW:
				$this->Type->value = self::SECTIONCOUNTERVIEW;
			break;
			case self::SECTIONCOUNTERVIEWPART:
				$this->Type->value = self::SECTIONCOUNTERVIEWPART;
			break;
			case self::SECTIONCOUNTERVIEWCARD:
				$this->Type->value = self::SECTIONCOUNTERVIEWCARD;
			break;
			case self::STATICPAGEVIEW:
				$this->Type->value = self::STATICPAGEVIEW;
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

			case self::DOCUMENTLIBRARY:
				$this->Type->value = self::DOCUMENTLIBRARY;
			break;
			case self::PICTURELIBRARY:
				$this->Type->value = self::PICTURELIBRARY;
			break;
			case self::SLIDEGALLERY:
				$this->Type->value = self::SLIDEGALLERY;
			break;
			case self::SLIDECALENDER:
				$this->Type->value = self::SLIDECALENDER;
			break;
			case self::SECTIONCOUNTER:
				$this->Type->value = self::SECTIONCOUNTER;
			break;
			case self::STATICPAGE:
				$this->Type->value = self::STATICPAGE;
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
			$s_foo = \fPHP\Helper\forestStringLib::sprintf2($s_foo, $this->PlaceHolders->value, ((count($this->PlaceHolders->value) > 9) ? true : false));
		}
		
		return $s_foo;
	}
}
?>