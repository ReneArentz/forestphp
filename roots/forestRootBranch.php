<?php
/**
 * adminisration class for handling all adminstrative use cases for forestBranch and forestTwig objects
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestany.net>
 * @copyright   (c) 2024 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.1.0 stable
 * @link        https://forestany.net
 * @object-id   0x1 0001F
 * @since       File available since Release 0.2.0 beta
 * @deprecated  -
 *
 * @version log Version			Developer	Date		Comment
 * 				0.2.0 beta		renea		2019-10-18	added to framework
 * 				0.3.0 beta		renea		2019-10-29	added create, modify and delete for tablefields
 * 				0.3.0 beta		renea		2019-10-30	added twig properties, sort, unique and translation
 * 				0.3.0 beta		renea		2019-10-30	added sub-constraints to twig properties
 * 				0.3.0 beta		renea		2019-11-01	added sub-constraints tablefields
 * 				0.3.0 beta		renea		2019-11-02	added validation rules to tablefields
 * 				0.3.0 beta		renea		2019-11-04	added moveUp and moveDown for tablefields and sub-constraints
 * 				0.4.0 beta		renea		2019-11-20	added truncateTwig and transferTwig functions
 * 				0.4.0 beta		renea		2019-11-21	added permission checks for standard root actions
 * 				0.5.0 beta		renea		2019-12-05	added checkout and checkin functionality for twigs
 * 				0.6.0 beta		renea		2019-12-12	added versioning and info columns administration
 * 				0.7.0 beta		renea		2020-01-02	added identifier administration
 * 				0.7.0 beta		renea		2020-01-16	added fphp_flex functionality and create log entry
 * 				0.9.0 beta		renea		2020-01-30	changes for bootstrap 4
 * 				1.0.0 stable	renea		2020-02-14	added translation deletion if a branch is deleted
 * 				1.1.0 stable	renea		2023-11-02	updated template for creating branch file
 * 				1.1.0 stable	renea		2023-11-02	added action chain processing, which makes it possible to use branch templates easily
 * 				1.1.0 stable	renea		2024-02-02	relocate standard view into database
 * 				1.1.0 stable	renea		2024-02-02	added document library template
 * 				1.1.0 stable	renea		2024-02-02	added picture library template
 * 				1.1.0 stable	renea		2024-05-03	added slide gallery template
 * 				1.1.0 stable	renea		2024-05-05	added slide calender template
 * 				1.1.0 stable	renea		2024-06-07	added static page template
 * 				1.1.0 stable	renea		2024-07-08	added static page functionality
 * 				1.1.0 stable	renea		2024-07-10	use identifier column to move records up or down
 * 				1.1.0 stable	renea		2024-08-10	moved keep filter query to forestPHP.php general Init method
 * 				1.1.0 stable	renea		2024-08-10	changes for bootstrap 5
 *				1.1.0 stable	renea		2024-08-17	can create static page from parent branch and from static page source itself
 */

namespace fPHP\Branches;

use \fPHP\Roots\forestException as forestException;

abstract class forestRootBranch {
	use \fPHP\Roots\forestData;
	
	/* Fields */
	
	private $ForbiddenTablefieldNames = array('Id', 'UUID', 'Created', 'CreatedBy', 'Modified', 'ModifiedBy', 'Identifier');
	private $StandardActions = array(
		'Checkin' => 'checkin',
		'Checkout' => 'checkout',
		'Delete' => 'delete',
		'Edit' => 'edit',
		'Edit Flex' => 'editFlex',
		'fphp Captcha' => 'fphp_captcha',
		'fphp Image Thumbnail' => 'fphp_imageThumbnail',
		'fphp Upload' => 'fphp_upload',
		'fphp Upload Delete' => 'fphp_upload_delete',
		'fphp Update Flex' => 'fphp_updateFlex',
		'Read' => 'init',
		'Move Down' => 'moveDown',
		'Move Up' => 'moveUp',
		'New' => 'new',
		'Replace File' => 'replaceFile',
		'Restore File' => 'restoreFile',
		'View' => 'view',
		'View Files' => 'viewFiles',
		'View Files History' => 'viewFilesHistory',
		'View Flex' => 'viewFlex',
		'Create Static Page' => 'createStaticPage',
		'Delete Static Page' => 'deleteStaticPage'
	);
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * render root menu for root actions for every branch
	 *
	 * @return string  string value with root menu part for navigation
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function RenderRootMenu() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$s_rootMenu = '';
		
		if ($o_glob->Security->CheckUserPermission(null, 'rootMenu')) {
			$s_rootMenu .= '<li class="nav-link dropdown">' . "\n";
				$s_rootMenu .= '<a href="#" class="nav-link text-nowrap dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false" id="navbardrop">' . "\n";
					$s_rootMenu .= '<button class="btn btn-sm btn-danger" type="button" title="Root-Menu"><span class="bi bi-tools"></span></button>' . "\n";
				$s_rootMenu .= '</a>' . "\n";
				$s_rootMenu .= '<ul class="dropdown-menu">' . "\n";
					
					if ($o_glob->Security->CheckUserPermission(null, 'viewIdentifier')) {
						$s_rootMenu .= '<li class="dropdown-item"><a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'viewIdentifier') . '" class="nav-link text-nowrap"><span class="bi bi-upc"></span> ' . $o_glob->GetTranslation('rootViewIdentifierTitle', 1) . '</a></li>' . "\n";
					}
					
					if ($o_glob->Security->CheckUserPermission(null, 'newBranch')) {
						$s_rootMenu .= '<li class="dropdown-item"><a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'newBranch') . '" class="nav-link text-nowrap"><span class="bi bi-plus-circle-fill text-success"></span> ' . $o_glob->GetTranslation('rootCreateBranchTitle', 1) . '</a></li>' . "\n";
					}
					
					if ($o_glob->Security->CheckUserPermission(null, 'viewBranch')) {
						$s_rootMenu .= '<li class="dropdown-item"><a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'viewBranch') . '" class="nav-link text-nowrap"><span class="bi bi-search"></span> ' . $o_glob->GetTranslation('rootViewBranchTitle', 1) . '</a></li>' . "\n";
					}
					
					$s_rootMenu .= '<li class="dropdown-submenu">' . "\n";
						
						if ($o_glob->Security->CheckUserPermission(null, 'editBranch')) {
							$s_rootMenu .= '<span class="dropdown-item">' . "\n";
								$s_rootMenu .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'editBranch') . '" class="text-secondary-emphasis text-decoration-none"><span class="bi bi-pencil-square"></span> ' . $o_glob->GetTranslation('rootEditBranchTitle', 1) . '</a>' . "\n";
								$s_rootMenu .= '<a href="#" class="dropdown-submenu-item text-secondary-emphasis"><span class="bi bi-caret-down"></span></a>' . "\n";
							$s_rootMenu .= '</span>' . "\n";
						}
						
						$s_rootMenu .= '<ul class="dropdown-menu">' . "\n";
							if (!issetStr($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue)) {
								if ($o_glob->Security->CheckUserPermission(null, 'newTwig')) {
									$s_rootMenu .= '<li class="dropdown-item"><a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'newTwig') . '" class="nav-link text-nowrap"><span class="bi bi-plus-circle-fill text-success"></span> ' . $o_glob->GetTranslation('rootCreateTwigTitle', 1) . '</a></li>' . "\n";
								}
								
								if ($o_glob->Security->CheckUserPermission(null, 'transferTwig')) {
									$s_rootMenu .= '<li class="dropdown-item"><a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'transferTwig') . '" class="nav-link text-nowrap"><span class="bi bi-arrow-left-right"></span> ' . $o_glob->GetTranslation('rootTransferTwigTitle', 1) . '</a></li>' . "\n";
								}
							} else {
								if ($o_glob->Security->CheckUserPermission(null, 'viewTwig')) {
									$s_rootMenu .= '<li class="dropdown-item"><a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'viewTwig') . '" class="nav-link text-nowrap"><span class="bi bi-search"></span> ' . $o_glob->GetTranslation('rootViewTwigTitle', 1) . '</a></li>' . "\n";
								}
								
								if ($o_glob->Security->CheckUserPermission(null, 'editTwig')) {
									$s_rootMenu .= '<li class="dropdown-item"><a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'editTwig') . '" class="nav-link text-nowrap"><span class="bi bi-pencil-square"></span> ' . $o_glob->GetTranslation('rootEditTwigTitle', 1) . '</a></li>' . "\n";
								}
								
								if ($o_glob->Security->CheckUserPermission(null, 'truncateTwig')) {
									$s_rootMenu .= '<li class="dropdown-item"><a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'truncateTwig') . '" class="nav-link text-nowrap"><span class="bi bi-eraser-fill text-primary"></span> ' . $o_glob->GetTranslation('rootTruncateTwigTitle', 1) . '</a></li>' . "\n";
								}
								
								if ($o_glob->Security->CheckUserPermission(null, 'transferTwig')) {
									$s_rootMenu .= '<li class="dropdown-item"><a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'transferTwig') . '" class="nav-link text-nowrap"><span class="bi bi-arrow-left-right"></span> ' . $o_glob->GetTranslation('rootTransferTwigTitle', 1) . '</a></li>' . "\n";
								}
								
								if ($o_glob->Security->CheckUserPermission(null, 'deleteTwig')) {
									$s_rootMenu .= '<li class="dropdown-item"><a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'deleteTwig') . '" class="nav-link text-nowrap"><span class="bi bi-trash-fill text-danger"></span> ' . $o_glob->GetTranslation('rootDeleteTwigTitle', 1) . '</a></li>' . "\n";
								}
								
								if ($o_glob->Security->CheckUserPermission(null, 'editFlex')) {
									$s_rootMenu .= '<li class="dropdown-item"><a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'editFlex') . '" class="nav-link text-nowrap"><span class="bi bi-pen"></span> ' . $o_glob->GetTranslation('rootEditFlexTitle', 1) . '</a></li>' . "\n";
								}
							}
						$s_rootMenu .= '</ul>' . "\n";
					$s_rootMenu .= '</li>' . "\n";
					
					if ($o_glob->Security->CheckUserPermission(null, 'moveUpBranch')) {
						$s_rootMenu .= '<li class="dropdown-item"><a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'moveUpBranch', array('editKey' => $o_glob->URL->BranchId)) . '" class="nav-link text-nowrap"><span class="bi bi-caret-up-fill"></span> ' . $o_glob->GetTranslation('rootMoveUpBranchTitle', 1) . '</a></li>' . "\n";
					}
					
					if ($o_glob->Security->CheckUserPermission(null, 'moveDownBranch')) {
						$s_rootMenu .= '<li class="dropdown-item"><a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'moveDownBranch', array('editKey' => $o_glob->URL->BranchId)) . '" class="nav-link text-nowrap"><span class="bi bi-caret-down-fill"></span> ' . $o_glob->GetTranslation('rootMoveDownBranchTitle', 1) . '</a></li>' . "\n";
					}
					
					/* check for right to create a static page */
					if ($o_glob->Security->CheckUserPermission(null, 'createStaticPage')) {
						/* we must check if we can create a static page from current branch */
						$b_canCreateStaticPage = false;
						/* get standard view record for 'static page' */
						$o_standardviewsTwig = new \fPHP\Twigs\standardviewsTwig();
						$o_standardviewsTwig->GetRecordPrimary(array('Static Page'), array('Name'));

						/* create branch twig */
						$o_branchTwig = new \fPHP\Twigs\branchTwig();

						/* get all child branches from current branch */
						$a_sqlAdditionalFilter = array(array('column' => 'ParentBranch', 'value' => $o_glob->URL->BranchId, 'operator' => '=', 'filterOperator' => 'AND'));
						$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
						$o_branchChildren = $o_branchTwig->GetAllRecords(true);
						$o_glob->Temp->Del('SQLAdditionalFilter');
						
						/* check if there are any children */
						if ($o_branchChildren->Twigs->Count() > 0) {
							/* iterate current branch children */
							foreach ($o_branchChildren->Twigs as $o_branchChild) {
								/* check if a child has 'static page' as standard view */
								if ($o_branchChild->StandardView->PrimaryValue == $o_standardviewsTwig->UUID) {
									/* in that case, we can create a static page for current branch */
									$b_canCreateStaticPage = true;
									break;
								}
							}
						}
						
						/* show option in root menu if we really can create a static page from current branch */
						if ($b_canCreateStaticPage) {
							/* check if a static page is already available and we have permission to handle a static page */
							if ( (\fPHP\Roots\forestAutoLoad::IsReadable('./files/' . $o_glob->URL->Branch . '.html')) && ($o_glob->Security->CheckUserPermission(null, 'deleteStaticPage')) ) {
								/* create root option with dropdown */
								$s_rootMenu .= '<li class="dropdown-submenu">' . "\n";
									$s_rootMenu .= '<span class="dropdown-item">' . "\n";
										$s_rootMenu .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'createStaticPage') . '" class="text-decoration-none text-secondary-emphasis"><span class="bi bi-window-stack text-warning"></span> ' . $o_glob->GetTranslation('rootCreateStaticPageTitle', 1) . '</a>' . "\n";
										$s_rootMenu .= '<a href="#" class="dropdown-submenu-item text-secondary-emphasis"><span class="bi bi-caret-down"></span></a>' . "\n";
									$s_rootMenu .= '</span>' . "\n";
									
									$s_rootMenu .= '<ul class="dropdown-menu">' . "\n";
										$s_rootMenu .= '<li class="dropdown-item"><a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'deleteStaticPage') . '" class="nav-link text-nowrap text-black"><span class="bi bi-trash-fill text-danger"></span> ' . $o_glob->GetTranslation('rootDeleteStaticPageTitle', 1) . '</a></li>' . "\n";
									$s_rootMenu .= '</ul>' . "\n";
								$s_rootMenu .= '</li>' . "\n";
							} else { /* just show option to create a static page */
								$s_rootMenu .= '<li class="dropdown-item"><a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'createStaticPage') . '" class="nav-link text-nowrap text-black"><span class="bi bi-window-stack text-warning"></span> ' . $o_glob->GetTranslation('rootCreateStaticPageTitle', 1) . '</a></li>' . "\n";
							}
						} else {
							/* get current branch */
							$o_branchTwig->GetRecord(array($o_glob->URL->BranchId));

							/* create parent branch twig */
							$o_parentBranchTwig = new \fPHP\Twigs\branchTwig();
							$i_parentBranchId = $o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['ParentBranch'];
							
							/* get parent branch */
							$o_parentBranchTwig->GetRecord(array($i_parentBranchId));
							
							/* check if current branch is static page and parent has no twig */
							if ( ($o_branchTwig->StandardView->PrimaryValue == $o_standardviewsTwig->UUID) && (!issetStr($o_glob->BranchTree['Id'][$i_parentBranchId]['Table']->PrimaryValue)) ) {
								/* check if a static page is already available and we have permission to handle a static page */
								if ( (\fPHP\Roots\forestAutoLoad::IsReadable('./files/' . $o_parentBranchTwig->Name . '.html')) && ($o_glob->Security->CheckUserPermission(null, 'deleteStaticPage')) ) {
									/* create root option with dropdown */
									$s_rootMenu .= '<li class="dropdown-submenu">' . "\n";
										$s_rootMenu .= '<span class="dropdown-item">' . "\n";
											$s_rootMenu .= '<a href="' . \fPHP\Helper\forestLink::Link($o_parentBranchTwig->Name, 'createStaticPage') . '" class="text-decoration-none text-secondary-emphasis"><span class="bi bi-window-stack text-warning"></span> ' . $o_glob->GetTranslation('rootCreateStaticPageTitle', 1) . '</a>' . "\n";
											$s_rootMenu .= '<a href="#" class="dropdown-submenu-item text-secondary-emphasis"><span class="bi bi-caret-down"></span></a>' . "\n";
										$s_rootMenu .= '</span>' . "\n";
										
										$s_rootMenu .= '<ul class="dropdown-menu bg-transparent">' . "\n";
											$s_rootMenu .= '<li class="dropdown-item"><a href="' . \fPHP\Helper\forestLink::Link($o_parentBranchTwig->Name, 'deleteStaticPage') . '" class="nav-link text-nowrap text-black"><span class="bi bi-trash-fill text-danger"></span> ' . $o_glob->GetTranslation('rootDeleteStaticPageTitle', 1) . '</a></li>' . "\n";
										$s_rootMenu .= '</ul>' . "\n";
									$s_rootMenu .= '</li>' . "\n";
								} else { /* just show option to create a static page */
									$s_rootMenu .= '<li class="dropdown-item"><a href="' . \fPHP\Helper\forestLink::Link($o_parentBranchTwig->Name, 'createStaticPage') . '" class="nav-link text-nowrap text-black"><span class="bi bi-window-stack text-warning"></span> ' . $o_glob->GetTranslation('rootCreateStaticPageTitle', 1) . '</a></li>' . "\n";
								}
							}
						}
					}

					if ($o_glob->Security->CheckUserPermission(null, 'deleteBranch')) {
						$s_rootMenu .= '<li class="dropdown-item"><a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'deleteBranch') . '" class="nav-link text-nowrap"><span class="bi bi-trash-fill text-danger"></span> ' . $o_glob->GetTranslation('rootDeleteBranchTitle', 1) . '</a></li>' . "\n";
					}
					
				$s_rootMenu .= '</ul>' . "\n";
			$s_rootMenu .= '</li>' . "\n";
		}
		
		return $s_rootMenu;
	}
	
	
	/**
	 * handle view identifier action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function viewIdentifierAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* get all identifier records */
		$o_identifierTwig = new \fPHP\Twigs\identifierTwig;
		
		$o_identifiers = $o_identifierTwig->GetAllRecords(true);
		
		/* create modal form for listing identifier records */
		$o_glob->PostModalForm = new \fPHP\Forms\forestForm($o_identifierTwig);
		$s_title = $o_glob->GetTranslation('Identifiers', 1);
		$o_glob->PostModalForm->CreateModalForm($o_identifierTwig, $s_title, false);
		
		if ($o_identifiers->Twigs->Count() == 0) {
			/* add description element to show that no files exists for sub record */
			$o_description = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::DESCRIPTION);
			
			$o_description->Description = '<div class="alert alert-primary alert-dismissible fade show" role="alert">' .
				'<div><span class="bi bi-info-circle-fill h5"></span>&nbsp;' . $o_glob->GetTranslation('NoRecords', 1) . "\n" . '</div>' . "\n" .
				'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' . "\n" .
			'</div>' . "\n";

			$o_description->NoFormGroup = true;
			
			$o_glob->PostModalForm->FormElements->Add($o_description);
		}

		/* list identifiers of sub record */
		$s_subTableHead = '';
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortIdentifierName', 0) . '</th>' . "\n";
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortIdentifierStart', 0) . '</th>' . "\n";
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortIdentifierNext', 0) . '</th>' . "\n";
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortIdentifierIncrement', 0) . '</th>' . "\n";
		$s_subTableHead .= '<th>' . substr($o_glob->GetTranslation('formSortColumnLabel', 0), 0, -1) . '</th>' . "\n";
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
				
		$s_subTableRows = '';
			
		if ($o_identifiers->Twigs->Count() > 0) {
			foreach ($o_identifiers->Twigs as $o_identifier) {
				$s_subTableRows .= '<tr';
				$s_subTableRows .= '>' . "\n";
				
				$s_subTableRows .=  '<td>' . $o_identifier->IdentifierName . '</td>' . "\n";
				$s_subTableRows .=  '<td>' . $o_identifier->IdentifierStart . '</td>' . "\n";
				$s_subTableRows .=  '<td>' . $o_identifier->IdentifierNext . '</td>' . "\n";
				$s_subTableRows .=  '<td>' . $o_identifier->IdentifierIncrement . '</td>' . "\n";
				
				$s_value = '<span class="bi bi-x-lg text-danger"></span>';

				if (boolval($o_identifier->UseAsSortColumn) == true) {
					$s_value = '<span class="bi bi-check-lg h5 text-success"></span>';
				}
				
				$s_subTableRows .=  '<td>' . $s_value . '</td>' . "\n";
				
				$s_options = '<span class="btn-group">' . "\n";
				
				$a_parameters = $o_glob->URL->Parameters;
				unset($a_parameters['newKey']);
				unset($a_parameters['viewKey']);
				unset($a_parameters['viewSubKey']);
				unset($a_parameters['editKey']);
				unset($a_parameters['editFileKey']);
				unset($a_parameters['deleteKey']);
				unset($a_parameters['editSubKey']);
				unset($a_parameters['deleteSubKey']);
				unset($a_parameters['deleteFileKey']);
				unset($a_parameters['subConstraintKey']);
				$a_parameters['editKey'] = $o_identifier->UUID;
				
				if ($o_glob->Security->CheckUserPermission(null, 'editIdentifier')) {
					$s_options .=  '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'editIdentifier', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="bi bi-pencil-square"></span></a>' . "\n";
				}
				
				$a_parameters = $o_glob->URL->Parameters;
				unset($a_parameters['newKey']);
				unset($a_parameters['viewKey']);
				unset($a_parameters['viewSubKey']);
				unset($a_parameters['editKey']);
				unset($a_parameters['editFileKey']);
				unset($a_parameters['deleteKey']);
				unset($a_parameters['editSubKey']);
				unset($a_parameters['deleteSubKey']);
				unset($a_parameters['deleteFileKey']);
				unset($a_parameters['subConstraintKey']);
				$a_parameters['deleteKey'] = $o_identifier->UUID;
				
				if ($o_glob->Security->CheckUserPermission(null, 'deleteIdentifier')) {
					$s_options .=  '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'deleteIdentifier', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="bi bi-trash-fill text-danger"></span></a>' . "\n";
				}
				
				$s_options .= '</span>' . "\n";
				$s_subTableRows .=  '<td>' . $s_options . '</td>' . "\n";
				$s_subTableRows .=  '</tr>' . "\n";
			}
		}
		
		$s_newButton = '';
		
		if ($o_glob->Security->CheckUserPermission(null, 'newIdentifier')) {
			$s_newButton =  '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'newIdentifier') . '" class="btn btn-light" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="bi bi-plus-circle-fill text-success"></span></a>' . "\n";
		}
		
		$s_subFormItemContent = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
		
		/* add description element to show existing files for sub record */
		/* use template to render files of a record */
		$o_description = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::DESCRIPTION);
		$o_description->Description = strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEW, array($o_glob->PostModalForm->FormObject->Id, $s_subFormItemContent)));
		$o_description->NoFormGroup = true;
		
		$o_glob->PostModalForm->FormElements->Add($o_description);
		
		$this->SetNextAction('init');
	}
	
	/**
	 * handle new identifier record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function newIdentifierAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\identifierTwig;
		$s_nextAction = 'init';
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			/* add new branch record form */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
			
			/* delete BranchId-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_identifier_IdentifierNext')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_identifier_IdentifierNext].');
			}
		} else {
			/* check posted data for new tablefield_validationrule record */
			$this->TransferPOST_Twig();
			
			/* identifier characters should be only capitals */
			$this->Twig->IdentifierStart = strtoupper($this->Twig->IdentifierStart);
			
			/* new identifier record has same next value like start */
			$this->Twig->IdentifierNext = $this->Twig->IdentifierStart;
			
			/* test increase of next value */
			$s_identifierNext = \fPHP\Helper\forestStringLib::IncreaseIdentifier($this->Twig->IdentifierNext, $this->Twig->IdentifierIncrement);
			
			if ($s_identifierNext == 'INVALID') {
				throw new forestException(0x10001F13, array($this->Twig->IdentifierNext));
			} else if ($s_identifierNext == 'OVERFLOW') {
				throw new forestException(0x10001F14, array($this->Twig->IdentifierNext));
			}
			
			/* insert record */
			$i_result = $this->Twig->InsertRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				throw new forestException(0x10001402);
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001404));
				$s_nextAction = 'viewIdentifier';
			}
			
			/* remember identifier uuid */
			$this->HandleProcessingActionChain();
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/**
	 * handle edit identifier record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function editIdentifierAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\identifierTwig;
		$s_nextAction = 'init';
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if ($o_glob->IsPost) {
			/* query record */
			if (array_key_exists('sys_fphp_identifierKey', $_POST)) {
				if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_identifierKey']))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* check posted data for tablefield_validationrule record */
				$this->TransferPOST_Twig();
				
				/* identifier characters should be only capitals */
				$this->Twig->IdentifierStart = strtoupper($this->Twig->IdentifierStart);
				$this->Twig->IdentifierNext = strtoupper($this->Twig->IdentifierNext);
				
				/* test increase of start value */
				$s_identifierStart = \fPHP\Helper\forestStringLib::IncreaseIdentifier($this->Twig->IdentifierStart, $this->Twig->IdentifierIncrement);
				
				if ($s_identifierStart == 'INVALID') {
					throw new forestException(0x10001F13, array($this->Twig->IdentifierStart));
				} else if ($s_identifierStart == 'OVERFLOW') {
					throw new forestException(0x10001F14, array($this->Twig->IdentifierStart));
				}
				
				/* test increase of next value */
				$s_identifierNext = \fPHP\Helper\forestStringLib::IncreaseIdentifier($this->Twig->IdentifierNext, $this->Twig->IdentifierIncrement);
				
				if ($s_identifierNext == 'INVALID') {
					throw new forestException(0x10001F13, array($this->Twig->IdentifierNext));
				} else if ($s_identifierNext == 'OVERFLOW') {
					throw new forestException(0x10001F14, array($this->Twig->IdentifierNext));
				}
				
				/* edit record */
				$i_result = $this->Twig->UpdateRecord();
				
				/* evaluate result */
				if ($i_result == -1) {
					throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
				} else if ($i_result == 0) {
					$o_glob->SystemMessages->Add(new forestException(0x10001406));
					$s_nextAction = 'viewIdentifier';
				} else if ($i_result == 1) {
					$o_glob->SystemMessages->Add(new forestException(0x10001407));
					$s_nextAction = 'viewIdentifier';
				}
			}
		} else {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editKey'), 'editKey' );
		
			if ( ($o_glob->Temp->Exists('editKey')) && ($o_glob->Temp->{'editKey'} != null) ) {
				/* query record */
				if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'editKey'}))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}

				/* build modal form */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
				$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('EditModalTitle', 1);
				
				/* add tablefield record key to modal form as hidden field */
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_identifierKey';
				$o_hidden->Value = strval($this->Twig->Id);
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/**
	 * handle delete identifier record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function deleteIdentifierAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\identifierTwig;
		$s_nextAction = 'init';
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteKey'), 'deleteKey' );
			
			if ( ($o_glob->Temp->Exists('deleteKey')) && ($o_glob->Temp->{'deleteKey'} != null) ) {
				if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'deleteKey'}))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* create modal confirmation form for deleting record */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig);
				$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
				$s_description = '<b>' . $o_glob->GetTranslation('DeleteModalDescriptionOne', 1) . '</b>';
				$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);	
				
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_identifierKey';
				$o_hidden->Value = strval($this->Twig->UUID);
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
			}
		} else {
			if (array_key_exists('sys_fphp_identifierKey', $_POST)) {
				if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_identifierKey']))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				} else {
					/* check if identifier is used in any table */
					foreach ($o_glob->TablesInformation as $a_tableInfo) {
						/* if identifier is in use, we abort deletion and tell that it cannot be deleted until branch is using it with its records */
						if ($a_tableInfo['Identifier']->PrimaryValue == $this->Twig->UUID) {
							$s_branchTitle = '';
							
							/* get branch title */
							foreach ($o_glob->BranchTree['Id'] as $a_branchInfo) {
								if ($a_branchInfo['Table']->PrimaryValue == $o_glob->Tables[$a_tableInfo['Name']]) {
									$s_branchTitle = $o_glob->GetTranslation($a_branchInfo['Title'], 1);
									break;
								}
							}
							
							throw new forestException(0x10001F17, array($s_branchTitle));
						}
					}
					
					/* delete record */
					$i_return = $this->Twig->DeleteRecord();
					
					/* evaluate the result */
					if ($i_return <= 0) {
						throw new forestException(0x10001423);
					}
					
					$o_glob->SystemMessages->Add(new forestException(0x10001427));
					$s_nextAction = 'viewIdentifier';
				}
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}

	
	/**
	 * handle new branch record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function newBranchAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\branchTwig;
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			/* add new branch record form */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
			
			/* delete StorageSpace-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_StorageSpace')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_StorageSpace].');
			}
			
			/* delete NavigationOrder-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_NavigationOrder')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_NavigationOrder].');
			}
			
			/* delete MaintenanceMode-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_MaintenanceMode')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_MaintenanceMode].');
			}
			
			/* delete MaintenanceModeMessage-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_MaintenanceModeMessage')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_MaintenanceModeMessage].');
			}
		} else {
			$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
			
			/* check posted data for new record */
			$this->TransferPOST_Twig();
			
			/* branch name must be all lowercase */
			$this->Twig->Name = strtolower($this->Twig->Name);
			
			/* add ParentBranch value to record */
			$this->Twig->ParentBranch = $o_glob->URL->BranchId;
			
			/* get last branch record */
			$i_order = 1;
			$o_branchTwig = new \fPHP\Twigs\branchTwig;
			
			$a_sqlAdditionalFilter = array(array('column' => 'ParentBranch', 'value' => $this->Twig->ParentBranch, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			if ($o_branchTwig->GetLastRecord()) {
				$i_order = $o_branchTwig->NavigationOrder + 1;
			}
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			/* add NavigationOrder value to record */
			$this->Twig->NavigationOrder = $i_order;
			
			/* insert record */
			$i_result = $this->Twig->InsertRecord();
			
			/* access to global branch tree */
			$a_branchTree = $o_glob->BranchTree;

			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				throw new forestException(0x10001402);
			} else if ($i_result == 1) {
				/* update global branch tree for later use */
				$a_branchTree['Id'][$this->Twig->Id]['Id'] = $this->Twig->Id;
				$a_branchTree['Id'][$this->Twig->Id]['Name'] = $this->Twig->Name;
				$a_branchTree['Id'][$this->Twig->Id]['ParentBranch'] = $this->Twig->ParentBranch;
				$a_branchTree['Id'][$this->Twig->Id]['Title'] = $this->Twig->Title;
				$a_branchTree['Id'][$this->Twig->Id]['Navigation'] = $this->Twig->Navigation;
				$a_branchTree['Id'][$this->Twig->Id]['NavigationOrder'] = $this->Twig->NavigationOrder;
				$a_branchTree['Id'][$this->Twig->Id]['StandardView'] = $this->Twig->StandardView;
				$a_branchTree['Id'][$this->Twig->Id]['Filter'] = $this->Twig->Filter;
				$a_branchTree['Id'][$this->Twig->Id]['KeepFilter'] = $this->Twig->KeepFilter;
				$a_branchTree['Id'][$this->Twig->Id]['PermissionInheritance'] = $this->Twig->PermissionInheritance;
				$a_branchTree['Name'][$this->Twig->Name] = $this->Twig->Id;

				/* create branch file with folder in trunk */
				$s_path = '';
				
				if (count($o_glob->URL->Branches) > 0) {
					foreach($o_glob->URL->Branches as $s_value) {
						$s_path .= $s_value . '/';
					}
				}
				
				$s_path .= $o_glob->URL->Branch . '/';
				
				/* get directory content of current page into array */
				$a_dirContent = scandir('./trunk/' . $s_path);
				$s_path = './trunk/' . $s_path . $this->Twig->Name;
				
				/* if we cannot find fphp_files folder and we cannot create fphp_files folder as new directory */
				if (!in_array($this->Twig->Name, $a_dirContent)) {
					if (!@mkdir($s_path)) {
						throw new forestException('Cannot create directory [%0].', array($s_path . $this->Twig->Name . '/'));
					}
				}
				
				$o_file = new \fPHP\Helper\forestFile($s_path . '/' . $this->Twig->Name . 'Branch.php', (!file_exists($s_path . '/' . $this->Twig->Name . 'Branch.php')));
				$o_file->ReplaceContent( strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::CREATENEWBRANCH, array($this->Twig->Name))) );
				
				/* create translation record for branch title */
				$o_translationTwig = new \fPHP\Twigs\translationTwig;
				$o_translationTwig->BranchId = 1;
				$o_translationTwig->LanguageCode = $o_glob->Trunk->LanguageCode;
				$o_translationTwig->Name = $this->Twig->Title;
				$o_translationTwig->Value = $this->Twig->Title;
				$i_result = $o_translationTwig->InsertRecord();
				
				/* create standard actions for branch */
				foreach($this->StandardActions as $s_actionLabel => $s_actionValue) {
					$o_actionTwig = new \fPHP\Twigs\actionTwig;
					$o_actionTwig->BranchId = $this->Twig->Id;
					$o_actionTwig->Name = $s_actionValue;
					
					/* insert action record */
					$i_result = $o_actionTwig->InsertRecord();
					
					/* evaluate result */
					if ($i_result == -1) {
						throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
					} else if ($i_result == 0) {
						throw new forestException(0x10001402);
					} else if ($i_result == 1) {
						/* update global branch tree for later use */
						$a_branchTree['Id'][$this->Twig->Id]['actions']['Id'][$o_actionTwig->Id] = $o_actionTwig->Name;
						$a_branchTree['Id'][$this->Twig->Id]['actions']['Name'][$o_actionTwig->Name] = $o_actionTwig->Id;
						
						/* create permission for standard action of branch */
						$o_permissionTwig = new \fPHP\Twigs\permissionTwig;
						$o_permissionTwig->Name = $s_actionLabel;
						$o_permissionTwig->Branch->PrimaryValue = strval($this->Twig->Id);
						$o_permissionTwig->Action->PrimaryValue = strval($o_actionTwig->Id);
						
						/* insert permission record */
						$i_result = $o_permissionTwig->InsertRecord();
						
						/* evaluate result */
						if ($i_result == -1) {
							throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
						} else if ($i_result == 0) {
							throw new forestException(0x10001402);
						}
					}
				}

				$o_glob->SystemMessages->Add(new forestException(0x10001404));

				/* template has been chosen or processing action chain */
				if ( ($_POST['sys_fphp_branch_Template'] != 'NULL') || ($o_glob->ProcessingActionChain) ) {
					/* update anything after creating new branch, because we still have no new request from client which updates global settings */

					/* we need all actions from branch '0' in our new branch */
					foreach ($a_branchTree['Zero']['actions']['Id'] as $s_id => $s_action) {
						$a_branchTree['Id'][$this->Twig->Id]['actions']['Id'][$s_id] = $s_action;
					}

					foreach ($a_branchTree['Zero']['actions']['Name'] as $s_action => $s_id) {
						$a_branchTree['Id'][$this->Twig->Id]['actions']['Name'][$s_action] = $s_id;
					}

					$o_glob->ProcessingActionChain = true;
					$o_glob->BranchTree = $a_branchTree;
					$o_glob->URL->OverwriteURL(\fPHP\Helper\forestLink::Link($this->Twig->Name, 'init'));
				}

				if ($_POST['sys_fphp_branch_Template'] == 'template1') { /* document library */
					if ((\fPHP\Helper\forestStringLib::StartsWith(strval($o_glob->Trunk->LanguageCode), 'Deutsch'))) {
						$o_glob->ActionChain = json_decode( strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::DOCUMENTLIBRARY, array($this->Twig->Title, $this->Twig->Name, 'Datei', 'Datei auswählen.', 'Dateiname', 'Version'))), true);
					} else if ((\fPHP\Helper\forestStringLib::StartsWith(strval($o_glob->Trunk->LanguageCode), 'English'))) {
						$o_glob->ActionChain = json_decode( strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::DOCUMENTLIBRARY, array($this->Twig->Title, $this->Twig->Name, 'File', 'Choose a file.', 'Filename', 'Version'))), true);
					}
				} else if ($_POST['sys_fphp_branch_Template'] == 'template2') { /* picture library */
					if ((\fPHP\Helper\forestStringLib::StartsWith(strval($o_glob->Trunk->LanguageCode), 'Deutsch'))) {
						$o_glob->ActionChain = json_decode( strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::PICTURELIBRARY, array($this->Twig->Title, $this->Twig->Name, 'Bild', 'Bild auswählen.', 'Titel', 'Titel angeben.', 'Titel...'))), true);
					} else if ((\fPHP\Helper\forestStringLib::StartsWith(strval($o_glob->Trunk->LanguageCode), 'English'))) {
						$o_glob->ActionChain = json_decode( strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::PICTURELIBRARY, array($this->Twig->Title, $this->Twig->Name, 'Picture', 'Choose a picture.', 'Title', 'Enter title.', 'Title...'))), true);
					}
				} else if ($_POST['sys_fphp_branch_Template'] == 'template3') { /* gallery */
					if ((\fPHP\Helper\forestStringLib::StartsWith(strval($o_glob->Trunk->LanguageCode), 'Deutsch'))) {
						$o_glob->ActionChain = json_decode( strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SLIDEGALLERY, array($this->Twig->Title, $this->Twig->Name, 'Dia', 'Dia auswählen.', 'Titel', 'Titel angeben.', 'Titel...'))), true);
					} else if ((\fPHP\Helper\forestStringLib::StartsWith(strval($o_glob->Trunk->LanguageCode), 'English'))) {
						$o_glob->ActionChain = json_decode( strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SLIDEGALLERY, array($this->Twig->Title, $this->Twig->Name, 'Slide', 'Choose a slide.', 'Title', 'Enter title.', 'Title...'))), true);
					}
				} else if ($_POST['sys_fphp_branch_Template'] == 'template4') { /* calender */
					if ((\fPHP\Helper\forestStringLib::StartsWith(strval($o_glob->Trunk->LanguageCode), 'Deutsch'))) {
						$o_glob->ActionChain = json_decode( strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SLIDECALENDER, array(
							$this->Twig->Name,
							'Beginn',
							'Zeitpunkt angeben.',
							'Ende (optional)',
							'Ende',
							'Ende der Veranstaltung angeben.',
							'Optional, für mehrtägige Veranstaltungen',
							'Veranstaltung',
							'Name für Veranstaltung angeben.',
							'Veranstaltung...',
							'Hervorheben',
							'Hervorheben auswählen.',
							'Kursiv',
							'Kursiv auswählen.',
							'Textfarbe',
							'Textfarbe auswählen.',
							'blau',
							'schwaches schwarz',
							'grün',
							'rot',
							'gelb',
							'schwaches blau',
							'dunkel',
							'grau',
							$o_glob->Trunk->LanguageCode->PrimaryValue,
							'Datum'
						))), true);
					} else if ((\fPHP\Helper\forestStringLib::StartsWith(strval($o_glob->Trunk->LanguageCode), 'English'))) {
						$o_glob->ActionChain = json_decode( strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SLIDECALENDER, array(
							$this->Twig->Name,
							'Start',
							'Enter timestamp.',
							'End (optional)',
							'End',
							'Enter the end of an event.',
							'Optional, for events of several days',
							'Event',
							'Enter the name of an event.',
							'Event...',
							'Highlight',
							'Choose highlight.',
							'Italic',
							'Choose italic.',
							'Text-Color',
							'Choose text-color.',
							'blue',
							'faint black',
							'green',
							'red',
							'yellow',
							'faint blue',
							'dark',
							'gray',
							$o_glob->Trunk->LanguageCode->PrimaryValue,
							'Date'
						))), true);
					}
				} else if ($_POST['sys_fphp_branch_Template'] == 'template5') { /* section counter */
					if ((\fPHP\Helper\forestStringLib::StartsWith(strval($o_glob->Trunk->LanguageCode), 'Deutsch'))) {
						$o_glob->ActionChain = json_decode( strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SECTIONCOUNTER, array(
							$this->Twig->Title,
							$this->Twig->Name,
							'Zählerwert',
							'Positiven Zählerwert angeben.',
							'123456',
							'Wert bis wohin der Zähler gehen soll',
							'Präfix',
							'Präfix angeben.',
							'vor dem Zähler',
							'kurzer Text vor der Anzeige des Zählerwertes',
							'Suffix',
							'Suffix angeben.',
							'nach dem Zähler',
							'kurzer Text nach der Anzeige des Zählerwertes',
							'Icon',
							'Icon angeben.',
							'bla',
							'Icon für den Zähler zur Visualisierung',
							'Text',
							'Text angeben.',
							'Beschreibung zum Zähler',
							'Beschreibungstext zum Zähler',
							'Zähler neu starten',
							'Aktivieren Sie die Option zum Neustart der Zähler.',
							'Ist diese Option einmal aktiv, werden die Zähler zurückgesetzt, wenn sie sich außerhalb des Sichtfensters befinden'
						))), true);
					} else if ((\fPHP\Helper\forestStringLib::StartsWith(strval($o_glob->Trunk->LanguageCode), 'English'))) {
						$o_glob->ActionChain = json_decode( strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SECTIONCOUNTER, array(
							$this->Twig->Title,
							$this->Twig->Name,
							'Counter',
							'Enter positive counter value.',
							'123456',
							'Value to where the counter should go',
							'Prefix',
							'Enter prefix value.',
							'before counter',
							'short text before the counter value is displayed',
							'Suffix',
							'Enter suffix.',
							'after counter',
							'short text after the counter value is displayed',
							'Icon',
							'Enter icon.',
							'',
							'Icon\'s counter for visualization',
							'Text',
							'Enter text.',
							'Lorem ipsum dolor sit amet',
							'Description text for the counter',
							'Restart counters',
							'Check option to restart counters.',
							'Is this option activated once, counters will be reset if they are out of viewport'
						))), true);
					}
				} else if ($_POST['sys_fphp_branch_Template'] == 'template6') { /* static page */
					if ((\fPHP\Helper\forestStringLib::StartsWith(strval($o_glob->Trunk->LanguageCode), 'Deutsch'))) {
						$o_glob->ActionChain = json_decode( strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::STATICPAGE, array(
							$this->Twig->Title,
							$this->Twig->Name,
							'Abschnitt',
							'Abschnitt angeben.',
							'Abschnitt...',
							'Überschrift zum Abschnitt',
							'Inhalt',
							' ',
							'Hintergrund',
							'Hintergrund auswählen.',
							'Festlegen eines Hintergrundbildes für den Abschnitt. Hintergrund überschreibt Hintergrundfarbe.',
							'Hintergrundfarbe',
							'Hintergrundfarbe auswählen.',
							'Festlegen einer Hintergrundfarbe für den Abschnitt.',
							'Typ',
							'Typ auswählen.',
							'Typ für Abschnitt auswählen: Inhalt(standard), Bildbibliothek(Quelle der Bilder für Inhalt dieses Abschnitts), Dia-Galerie(Anzeige einer Dia-Galerie), Dia-Kalender(Anzeige eines Dia-Kalenders), Abschnittszähler(Anzeige eines Abschnittzählers)',
							'Quelle',
							'Quelle für diesen Abschnitt. Ein Branch als Quelle für Bilder, Dia-Galerie oder Dia-Kalender auf der gleichen Ebene.',
							$this->Twig->ParentBranch,
							$this->Twig->Id
						))), true);
					} else if ((\fPHP\Helper\forestStringLib::StartsWith(strval($o_glob->Trunk->LanguageCode), 'English'))) {
						$o_glob->ActionChain = json_decode( strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::STATICPAGE, array(
							$this->Twig->Title,
							$this->Twig->Name,
							'Section',
							'Enter section.',
							'Section...',
							'Heading to the section',
							'Content',
							' ',
							'Background',
							'Choose background.',
							'Set a background image for the section. Background image overwrites background color.',
							'Background Color',
							'Choose background color.',
							'Set a background coor for the section.',
							'Type',
							'Choose type.',
							'Choose type for section: Content(standard), Picture library(Source for pictures for section\'s content), Slide Gallery(Display of a slide gallery), Slide Calender(Display of a slide calender), Section Counter(Display of a section counter)',
							'Source',
							'Source for this section. A branch as a source for images, slide gallery or slide calendar on the same level.',
							$this->Twig->ParentBranch,
							$this->Twig->Id
						))), true);
					}
				}
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/**
	 * handle edit branch record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function editBranchAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ( ($this->Twig != null) && ($this->Twig->fphp_SystemTable) ) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\branchTwig;
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if ($o_glob->IsPost) {
			/* query record */
			if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_branchKey']))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}
			
			$this->TransferPOST_Twig();
			
			/* change translation record for new title */
			$o_translationTwig = new \fPHP\Twigs\translationTwig;
			
			if (! ($o_translationTwig->GetRecordPrimary(array(1, $o_glob->Trunk->LanguageCode, $o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Title']), array('BranchId', 'LanguageCode', 'Name'))) ) {
				throw new forestException(0x10001401, array($o_translationTwig->fphp_Table));
			}
			
			$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
			
			/* change title value and execute update */
			$o_translationTwig->Name = $this->Twig->Title;
			$o_translationTwig->Value = $this->Twig->Title;
			$i_result = $o_translationTwig->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			}
						
			/* edit recrod */
			$i_result = $this->Twig->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				$o_glob->SystemMessages->Add(new forestException(0x10001406));
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001407));
			}
		} else {
			/* query record */
			if (! ($this->Twig->GetRecord(array($o_glob->URL->BranchId))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}
			
			/* build modal form */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('EditModalTitle', 1);
			
			/* add current record key to modal form */
			$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
			$o_hidden->Id = 'sys_fphp_branchKey';
			$o_hidden->Value = strval($o_glob->URL->BranchId);
			
			$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
			
			/* delete StorageSpace-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_StorageSpace')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_StorageSpace].');
			}
			
			/* delete Name-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_Name')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_Name].');
			}
			
			/* delete NavigationOrder-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_NavigationOrder')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_NavigationOrder].');
			}

			/* delete Template-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_Template')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_Template].');
			}
			
			/* add current record order to modal form as hidden field */
			$o_hiddenOrder = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
			$o_hiddenOrder->Id = 'sys_fphp_branch_NavigationOrder';
			$o_hiddenOrder->Value = strval($this->Twig->NavigationOrder);
			
			$o_glob->PostModalForm->FormFooterElements->Add($o_hiddenOrder);
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/**
	 * handle view branch record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function viewBranchAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\branchTwig;
		
		/* query branch record */
		if (! ($this->Twig->GetRecord(array($o_glob->URL->BranchId))) ) {
			throw new forestException(0x10001401, array($this->Twig->fphp_Table));
		}
		
		/* create modal read only form */
		$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true, true);
		$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->URL->BranchTitle . ' Branch';
		
		/* check for fphp_files folder */
		$i_parentBranchId = $o_glob->URL->BranchId;
			
		/* with our branchId we start a loop for all parent branches to get our necessary path for linking */
		do {
			$a_branches[] = $o_glob->BranchTree['Id'][$i_parentBranchId]['Name'];
			$i_parentBranchId = $o_glob->BranchTree['Id'][$i_parentBranchId]['ParentBranch'];
		} while ($i_parentBranchId != 0);
		
		$a_branches = array_reverse($a_branches);
		$s_link = './trunk';
		
		/* create link path */
		foreach($a_branches as $s_branch) {
			$s_link .= '/' . $s_branch;
		}
		
		/* variable to save branch storage space */
		$i_sum = 0;
		
		/* fphp_files folder must exist and be a directory */
		if ( (file_exists($s_link . '/fphp_files')) && (is_dir($s_link . '/fphp_files')) ) {
			foreach (scandir($s_link . '/fphp_files') as $subfolder) {
				if ( (is_dir($s_link . '/fphp_files/' . $subfolder)) && ($subfolder != ".") && ($subfolder != "..") ) {
					foreach (scandir($s_link . '/fphp_files/' . $subfolder) as $file) {
						if ( ($file != ".") && ($file != "..") ) {
							$i_sum += filesize($s_link . '/fphp_files/' . $subfolder . '/' . $file);
						}
					}
				}
			}
		}
		
		/* delete StorageSpace-element if branch has no files */
		if ($i_sum < 1) {
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('readonly_sys_fphp_branch_StorageSpace')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_StorageSpace].');
			}
		} else {
			$o_storageSpaceElement = $o_glob->PostModalForm->GetFormElementByFormId('readonly_sys_fphp_branch_StorageSpace');
			
			if ($o_storageSpaceElement != null) {
				$o_storageSpaceElement->Value = getNiceFileSize($i_sum, false);
			}
		}
		
		/* delete Name-element */
		if (!$o_glob->PostModalForm->DeleteFormElementByFormId('readonly_sys_fphp_branch_Name')) {
			throw new forestException('Cannot delete form element with Id[readonly_sys_fphp_branch_Name].');
		}
		
		/* delete NavigationOrder-element */
		if (!$o_glob->PostModalForm->DeleteFormElementByFormId('readonly_sys_fphp_branch_NavigationOrder')) {
			throw new forestException('Cannot delete form element with Id[readonly_sys_fphp_branch_NavigationOrder].');
		}
		
		/* delete Template-element */
		if (!$o_glob->PostModalForm->DeleteFormElementByFormId('readonly_sys_fphp_branch_Template')) {
			throw new forestException('Cannot delete form element with Id[readonly_sys_fphp_branch_Template].');
		}

		$s_subFormItems = '';
	
		/* ************************************************** */
		/* **********************ACTIONS********************* */
		/* ************************************************** */
		/* look for tablefields */
		$o_actionTwig = new \fPHP\Twigs\actionTwig;
		
		$a_sqlAdditionalFilter = array(array('column' => 'BranchId', 'value' => $o_glob->URL->BranchId, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_actions = $o_actionTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		/* ************************* */
		/* ***********HEAD********** */
		/* ************************* */
		
		$s_subTableHead = '';
		$s_subTableHead .= '<th>Action</th>' . "\n";
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
		
		/* ************************* */
		/* ***********ROWS********** */
		/* ************************* */
		
		$s_subTableRows = '';
		
		foreach ($o_actions->Twigs as $o_action) {
			/* render records based on twig view columns */
			$s_subTableRows .=  '<tr>' . "\n";
			$s_subTableRows .=  '<td><span>' . $o_action->Name . '</span></td>' . "\n";
			
			$s_options = '<span class="btn-group">' . "\n";
			
			/* edit link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['editKey'] = $o_action->Id;
			$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'editAction', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="bi bi-pencil-square"></span></a>' . "\n";
			
			/* delete link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['deleteKey'] = $o_action->Id;
			$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'deleteAction', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="bi bi-trash-fill text-danger"></span></a>' . "\n";
			
			$s_options .= '</span>' . "\n";
			$s_subTableRows .=  '<td>' . $s_options . '</td>' . "\n";
			
			$s_subTableRows .=  '</tr>' . "\n";
		}
		
		/* new link */
		$s_newButton = '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'newAction') . '" class="btn btn-light" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="bi bi-plus-circle-fill text-success"></span></a>' . "\n";
		
		$s_subFormItemContent = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
		$s_subFormItems .= new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEM, array('actions' . $this->Twig->fphp_Table, 'Actions' . ' (' . $o_actions->Twigs->Count() . ')', ' show', $s_subFormItemContent, $o_glob->PostModalForm->FormObject->Id));
		
		/* edit link */
		$s_editButton = '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'editBranch') . '" class="btn btn-lg btn-light" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="bi bi-pencil-square"></span></a><br>' . "\n";
		$o_glob->PostModalForm->BeforeForm = $s_editButton;
		$o_glob->PostModalForm->BeforeFormRightAlign = true;
		
		/* use template to render and add actions for modal form of branch record */
		$o_glob->PostModalForm->FormModalSubForm = strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEW, array($o_glob->PostModalForm->FormObject->Id, $s_subFormItems)));
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction('init');
	}
	
	/**
	 * handle action to change order of branch record in navigation, moving one record up
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function moveUpBranchAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ( ($this->Twig != null) && ($this->Twig->fphp_SystemTable) ) {
			throw new forestException(0x10001F16);
		}
		
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\branchTwig;
		
		/* query branch record */
		if (! ($this->Twig->GetRecord(array($o_glob->URL->BranchId))) ) {
			throw new forestException(0x10001401, array($this->Twig->fphp_Table));
		}
		
		$a_sqlAdditionalFilter = array(array('column' => 'ParentBranch', 'value' => $o_glob->BranchTree['Id'][$this->Twig->Id]['ParentBranch'], 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$this->MoveUpRecord();
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction('RELOADBRANCH');
	}
	
	/**
	 * handle action to change order of branch record in navigation, moving one record down
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function moveDownBranchAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ( ($this->Twig != null) && ($this->Twig->fphp_SystemTable) ) {
			throw new forestException(0x10001F16);
		}
		
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\branchTwig;
		
		/* query branch record */
		if (! ($this->Twig->GetRecord(array($o_glob->URL->BranchId))) ) {
			throw new forestException(0x10001401, array($this->Twig->fphp_Table));
		}
		
		$a_sqlAdditionalFilter = array(array('column' => 'ParentBranch', 'value' => $o_glob->BranchTree['Id'][$this->Twig->Id]['ParentBranch'], 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$this->MoveDownRecord();
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction('RELOADBRANCH');
	}
	
	/**
	 * handle delete branch record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function deleteBranchAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ( ($this->Twig != null) && ($this->Twig->fphp_SystemTable) ) {
			throw new forestException(0x10001F16);
		}
		
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\branchTwig;
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			if (! ($this->Twig->GetRecord(array($o_glob->URL->BranchId))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}
			
			/* check if branch has no children */
			$i_amount = 0;
		
			foreach ($o_glob->BranchTree['Id'] as $o_branch) {
				if ($o_branch['ParentBranch'] == $this->Twig->Id) {
					$i_amount++;
				}
			}
			
			if ($i_amount > 0) {
				throw new forestException(0x10001F00);
			}
			
			/* check if branch is not connected with a table */
			if (issetStr($this->Twig->Table->PrimaryValue)) {
				throw new forestException(0x10001F01);
			}
			
			/* create modal confirmation form for deleting record */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig);
			$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
			$s_description = '<b>' . $o_glob->GetTranslation('DeleteModalDescriptionOne', 1) . '</b>';
			$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);
			
			$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
			$o_hidden->Id = 'sys_fphp_branchKey';
			$o_hidden->Value = strval($o_glob->URL->BranchId);
			$o_glob->PostModalForm->FormElements->Add($o_hidden);
		} else {
			/* delete record */
			if (array_key_exists('sys_fphp_branchKey', $_POST)) {
				if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_branchKey']))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* check if branch has no children */
				$i_amount = 0;
			
				foreach ($o_glob->BranchTree['Id'] as $o_branch) {
					if ($o_branch['ParentBranch'] == $this->Twig->Id) {
						$i_amount++;
					}
				}
			
				if ($i_amount > 0) {
					throw new forestException(0x10001F00);
				}
				
				/* check if branch is not connected with a table */
				if (issetStr($this->Twig->Table->PrimaryValue)) {
					throw new forestException(0x10001F01);
				}
				
				$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
				
				/* delete all actions */
				$o_actionTwig = new \fPHP\Twigs\actionTwig; 
				
				$a_sqlAdditionalFilter = array(array('column' => 'BranchId', 'value' => $_POST['sys_fphp_branchKey'], 'operator' => '=', 'filterOperator' => 'AND'));
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$o_actions = $o_actionTwig->GetAllRecords(true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				
				if ($o_actions->Twigs->Count() > 0) {
					foreach ($o_actions->Twigs as $o_action) {
						/* delete permission records linked to action record */
						$o_permissionTwig = new \fPHP\Twigs\permissionTwig;
			
						$a_sqlAdditionalFilter = array(array('column' => 'Action', 'value' => $o_action->Id, 'operator' => '=', 'filterOperator' => 'AND'));
						$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
						$o_permissions = $o_permissionTwig->GetAllRecords(true);
						$o_glob->Temp->Del('SQLAdditionalFilter');
						
						foreach ($o_permissions->Twigs as $o_permission) {
							/* delete role_permission records linked to permission */
							$o_role_permissionTwig = new \fPHP\Twigs\role_permissionTwig;
			
							$a_sqlAdditionalFilter = array(array('column' => 'permissionUUID', 'value' => $o_permission->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
							$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
							$o_role_permissions = $o_role_permissionTwig->GetAllRecords(true);
							$o_glob->Temp->Del('SQLAdditionalFilter');
							
							foreach ($o_role_permissions->Twigs as $o_role_permission) {
								/* delete record */
								$i_return = $o_role_permission->DeleteRecord();
								
								/* evaluate the result */
								if ($i_return <= 0) {
									throw new forestException(0x10001423);
								}
							}
							
							/* delete record */
							$i_return = $o_permission->DeleteRecord();
							
							/* evaluate the result */
							if ($i_return <= 0) {
								throw new forestException(0x10001423);
							}
						}
						
						/* delete file record */
						$i_return = $o_action->DeleteRecord();
						
						/* evaluate the result */
						if ($i_return <= 0) {
							throw new forestException(0x10001423);
						}
					}
				}
				
				/* delete translation title */
				$o_translationTwig = new \fPHP\Twigs\translationTwig;
	
				if ( ($o_translationTwig->GetRecordPrimary(array(1, $o_glob->Trunk->LanguageCode, $this->Twig->Title), array('BranchId', 'LanguageCode', 'Name'))) ) {
					/* delete translation record */
					$i_return = $o_translationTwig->DeleteRecord();
					
					/* evaluate the result */
					if ($i_return <= 0) {
						throw new forestException(0x10001423);
					}
				}
				
				/* delete all translations of current branch */
				$o_translationTwig = new \fPHP\Twigs\translationTwig;
				
				$a_sqlAdditionalFilter = array(array('column' => 'BranchId', 'value' => $_POST['sys_fphp_branchKey'], 'operator' => '=', 'filterOperator' => 'AND'));
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$o_translations = $o_translationTwig->GetAllRecords(true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				
				if ($o_translations->Twigs->Count() > 0) {
					foreach ($o_translations->Twigs as $o_translation) {
						/* delete translation record */
						$i_return = $o_translation->DeleteRecord();
						
						/* evaluate the result */
						if ($i_return <= 0) {
							throw new forestException(0x10001423);
						}
					}
				}
				
				/* delete branch file structure */
				$s_path = '';

				if (count($o_glob->URL->Branches) > 0) {
					foreach($o_glob->URL->Branches as $s_value) {
						$s_path .= $s_value . '/';
					}
				}
				
				$s_path .= $o_glob->URL->Branch . '/';
				
				$s_path = './trunk/' . $s_path;
				\fPHP\Helper\forestFile::RemoveDirectoryRecursive($s_path);
				
				/* delete record */
				$i_return = $this->Twig->DeleteRecord();
				
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
				
				$o_glob->SystemMessages->Add(new forestException(0x10001427));

				/* prepare everything to reload page to parent branch */
				$s_parentBranchName = $o_glob->BranchTree['Id'][$o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['ParentBranch']]['Name'];
				$o_glob->URL->OverwriteURL(\fPHP\Helper\forestLink::Link($s_parentBranchName, 'init'));
				$this->SetNextAction('RELOADBRANCH', 'init');
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction('init');
	}


	/**
	 * handle new action record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function newActionAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\actionTwig;
		$o_branchTwig = new \fPHP\Twigs\branchTwig;
		$s_nextAction = 'init';
		
		/* query branch record */
		if (! ($o_branchTwig->GetRecord(array($o_glob->URL->BranchId))) ) {
			throw new forestException(0x10001401, array($o_branchTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			/* add new branch record form */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
			
			/* delete BranchId-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_action_BranchId')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_action_BranchId].');
			}
		} else {
			/* check posted data for new tablefield_validationrule record */
			$this->TransferPOST_Twig();
			
			/* add BranchId value to record */
			$this->Twig->BranchId = $o_glob->URL->BranchId;
			
			/* first character of action name must be lowercase */
			$this->Twig->Name = lcfirst($this->Twig->Name);
			
			/* insert record */
			$i_result = $this->Twig->InsertRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				throw new forestException(0x10001402);
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001404));
				$s_nextAction = 'viewBranch';
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}

	/**
	 * handle edit action record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function editActionAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\actionTwig;
		$o_branchTwig = new \fPHP\Twigs\branchTwig;
		$s_nextAction = 'init';
		
		/* query branch record */
		if (! ($o_branchTwig->GetRecord(array($o_glob->URL->BranchId))) ) {
			throw new forestException(0x10001401, array($o_branchTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if ($o_glob->IsPost) {
			/* query record */
			if (array_key_exists('sys_fphp_actionKey', $_POST)) {
				if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_actionKey']))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* check posted data for tablefield_validationrule record */
				$this->TransferPOST_Twig();
				
				/* add BranchId value to record */
				$this->Twig->BranchId = $o_glob->URL->BranchId;
				
				/* first character of action name must be lowercase */
				$this->Twig->Name = lcfirst($this->Twig->Name);
				
				/* edit record */
				$i_result = $this->Twig->UpdateRecord();
				
				/* evaluate result */
				if ($i_result == -1) {
					throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
				} else if ($i_result == 0) {
					$o_glob->SystemMessages->Add(new forestException(0x10001406));
					$s_nextAction = 'viewBranch';
				} else if ($i_result == 1) {
					$o_glob->SystemMessages->Add(new forestException(0x10001407));
					$s_nextAction = 'viewBranch';
				}
			}
		} else {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editKey'), 'editKey' );
		
			if ( ($o_glob->Temp->Exists('editKey')) && ($o_glob->Temp->{'editKey'} != null) ) {
				/* query record */
				if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'editKey'}))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* build modal form */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
				$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('EditModalTitle', 1);
				
				/* add tablefield record key to modal form as hidden field */
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_actionKey';
				$o_hidden->Value = strval($this->Twig->Id);
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
				
				/* delete BranchId-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_action_BranchId')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_action_BranchId].');
				}
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/**
	 * handle delete action record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function deleteActionAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\actionTwig;
		$o_branchTwig = new \fPHP\Twigs\branchTwig;
		$s_nextAction = 'init';
		
		/* query branch record */
		if (! ($o_branchTwig->GetRecord(array($o_glob->URL->BranchId))) ) {
			throw new forestException(0x10001401, array($o_branchTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteKey'), 'deleteKey' );
			
			if ( ($o_glob->Temp->Exists('deleteKey')) && ($o_glob->Temp->{'deleteKey'} != null) ) {
				if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'deleteKey'}))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* create modal confirmation form for deleting record */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig);
				$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
				$s_description = '<b>' . $o_glob->GetTranslation('DeleteModalDescriptionOne', 1) . '</b>';
				$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);	
				
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_actionKey';
				$o_hidden->Value = strval($this->Twig->Id);
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
			}
		} else {
			if (array_key_exists('sys_fphp_actionKey', $_POST)) {
				if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_actionKey']))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* delete permission records linked to this action */
				$o_permissionTwig = new \fPHP\Twigs\permissionTwig;
	
				$a_sqlAdditionalFilter = array(array('column' => 'Action', 'value' => $this->Twig->Id, 'operator' => '=', 'filterOperator' => 'AND'));
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$o_permissions = $o_permissionTwig->GetAllRecords(true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				
				foreach ($o_permissions->Twigs as $o_permission) {
					/* delete role_permission records linked to permission */
					$o_role_permissionTwig = new \fPHP\Twigs\role_permissionTwig;
	
					$a_sqlAdditionalFilter = array(array('column' => 'permissionUUID', 'value' => $o_permission->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
					$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
					$o_role_permissions = $o_role_permissionTwig->GetAllRecords(true);
					$o_glob->Temp->Del('SQLAdditionalFilter');
					
					foreach ($o_role_permissions->Twigs as $o_role_permission) {
						/* delete record */
						$i_return = $o_role_permission->DeleteRecord();
						
						/* evaluate the result */
						if ($i_return <= 0) {
							throw new forestException(0x10001423);
						}
					}
					
					/* delete record */
					$i_return = $o_permission->DeleteRecord();
					
					/* evaluate the result */
					if ($i_return <= 0) {
						throw new forestException(0x10001423);
					}
				}
				
				/* delete record */
				$i_return = $this->Twig->DeleteRecord();
				
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
				
				$o_glob->SystemMessages->Add(new forestException(0x10001427));
				$s_nextAction = 'viewBranch';
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}


	/**
	 * handle new twig record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function newTwigAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ( ($this->Twig != null) && ($this->Twig->fphp_SystemTable) ) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$s_nextActionAfterReload = null;
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\tablefieldTwig;
		
		if ($o_glob->URL->BranchId == 1) {
			throw new forestException(0x10001F05);
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			/* add new branch record form */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
			
			$o_description = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::DESCRIPTION);
			$o_description->Description = '<b>' . $o_glob->GetTranslation('rootNewTwigTableTitle', 0) . '</b>';
			
			$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
			$o_hidden->Id = 'sys_fphp_table_name';
			$o_hidden->Value = strval($o_glob->URL->Branch);
			
			$o_lookupIdentifier = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::LOOKUP);
			$o_lookupIdentifier->Label = $o_glob->GetTranslation('formIdentifierLabel', 0);
			$o_lookupIdentifier->Id = 'sys_fphp_table_identifier';
			$o_forestLookupData = new \fPHP\Helper\forestLookupData('sys_fphp_identifier', array('UUID'), array('IdentifierName','IdentifierStart'));
			
			/* remove used identifiers of lookup element */
			$o_tableTwig = new \fPHP\Twigs\tableTwig;

			$o_tables = $o_tableTwig->GetAllRecords(true);
			$a_options = $o_forestLookupData->CreateOptionsArray();
			
			foreach ($a_options as $s_identifierLabel => $s_identifierUUID) {
				$b_found = false;
				
				foreach ($o_tables->Twigs as $o_table) {
					if ($o_table->Identifier->PrimaryValue == $s_identifierUUID) {
						$b_found = true;
						break;
					}
				}
				
				if ($b_found) {
					unset($a_options[$s_identifierLabel]);
				}
			}
			
			if (count($a_options) <= 0) {
				$a_options = array();
			}
			
			$o_lookupIdentifier->Options = $a_options;
			
			$o_radioInfoColumns = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::RADIO);
			$o_radioInfoColumns->Label = $o_glob->GetTranslation('formInfoColumnsLabel', 0);
			$o_radioInfoColumns->Id = 'sys_fphp_table_infoColumns';
			$o_radioInfoColumns->RadioContainerClass = 'form-check form-check-inline';
			$o_radioInfoColumns->ValMessage = $o_glob->GetTranslation('formInfoColumnsValMessage', 0);
			$o_radioInfoColumns->Options = array('None' => '1', 'Created' => '10', 'Modified' => '100', 'Created + Modified' => '1000');
			
			$o_checkboxInfoColumnsView = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::CHECKBOX);
			$o_checkboxInfoColumnsView->Label = $o_glob->GetTranslation('formInfoColumnsViewLabel', 0);
			$o_checkboxInfoColumnsView->Id = 'sys_fphp_table_infoColumnsView[]';
			$o_checkboxInfoColumnsView->CheckboxContainerClass = 'form-check form-check-inline';
			$o_checkboxInfoColumnsView->ValMessage = $o_glob->GetTranslation('formInfoColumnsViewValMessage', 0);
			$o_checkboxInfoColumnsView->Options = array(
				'Created' => '0001',
				'Created By' => '0010',
				'Modified' => '0100',
				'Modified By' => '1000'
			);
			
			$o_radioVersioning = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::RADIO);
			$o_radioVersioning->Label = $o_glob->GetTranslation('formVersioningLabel', 0);
			$o_radioVersioning->Id = 'sys_fphp_table_versioning';
			$o_radioVersioning->RadioContainerClass = 'form-check form-check-inline';
			$o_radioVersioning->ValMessage = $o_glob->GetTranslation('formVersioningValMessage', 0);
			$o_radioVersioning->Options = array('None' => '1', 'Checkout' => '10', 'Checkout + Versioning (Files)' => '100');
			
			$o_description2 = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::DESCRIPTION);
			$o_description2->Description = '<b>' . $o_glob->GetTranslation('rootNewTwigFirstFieldTitle', 0) . '</b>';
			
			/* add manual created form elements to genereal tab */
			if (!$o_glob->PostModalForm->AddFormElement($o_description2, 'general', true)) {
				throw new forestException('Cannot add form element to tab with id[general].');
			}
			
			if (!$o_glob->PostModalForm->AddFormElement($o_radioVersioning, 'general', true)) {
				throw new forestException('Cannot add form element to tab with id[general].');
			}
			
			if (!$o_glob->PostModalForm->AddFormElement($o_checkboxInfoColumnsView, 'general', true)) {
				throw new forestException('Cannot add form element to tab with id[general].');
			}
						
			if (!$o_glob->PostModalForm->AddFormElement($o_radioInfoColumns, 'general', true)) {
				throw new forestException('Cannot add form element to tab with id[general].');
			}
			
			if (!$o_glob->PostModalForm->AddFormElement($o_lookupIdentifier, 'general', true)) {
				throw new forestException('Cannot add form element to tab with id[general].');
			}
			
			if (!$o_glob->PostModalForm->AddFormElement($o_hidden, 'general', true)) {
				throw new forestException('Cannot add form element to tab with id[general].');
			}
			
			if (!$o_glob->PostModalForm->AddFormElement($o_description, 'general', true)) {
				throw new forestException('Cannot add form element to tab with id[general].');
			}
			
			/* delete Order-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_tablefield_Order')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_tablefield_Order].');
			}
			
			/* delete SubRecordField-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_tablefield_SubRecordField')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_tablefield_SubRecordField].');
			}
			
			/* add validation rules for manual created form elements */
			$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule('sys_fphp_table_infoColumns', 'required', 'true', 'fphpByName'));
			$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule('sys_fphp_table_versioning', 'required', 'true', 'fphpByName'));
		} else {
			if (!array_key_exists('sys_fphp_table_name', $_POST)) {
				throw new forestException('No POST data for field[sys_fphp_table_name]');
			}
			
			if (array_key_exists('sys_fphp_tablefield_FormElementUUID', $_POST)) {
				if ( (array_key_exists('sys_fphp_tablefield_SqlTypeUUID', $_POST)) && (issetStr($_POST['sys_fphp_tablefield_SqlTypeUUID'])) ) {
					$o_formelement_sqltypeTwig = new \fPHP\Twigs\formelement_sqltypeTwig;
					
					if (! $o_formelement_sqltypeTwig->GetRecord(array($_POST['sys_fphp_tablefield_FormElementUUID'], $_POST['sys_fphp_tablefield_SqlTypeUUID']))) {
						$o_formelementTwig = new \fPHP\Twigs\formelementTwig;
						$o_sqltypeTwig = new \fPHP\Twigs\sqltypeTwig;
						
						if (! ($o_formelementTwig->GetRecord(array($_POST['sys_fphp_tablefield_FormElementUUID']))) ) {
							throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
						}
						
						if (! ($o_sqltypeTwig->GetRecord(array($_POST['sys_fphp_tablefield_SqlTypeUUID']))) ) {
							throw new forestException(0x10001401, array($o_sqltypeTwig->fphp_Table));
						}
						
						throw new forestException(0x10001F0D, array($o_formelementTwig->Name, $o_sqltypeTwig->Name));
					}
				}
				
				if ( (array_key_exists('sys_fphp_tablefield_ForestDataUUID', $_POST)) && (issetStr($_POST['sys_fphp_tablefield_ForestDataUUID'])) ) {
					$o_formelement_forestdataTwig = new \fPHP\Twigs\formelement_forestdataTwig;
					
					if (! $o_formelement_forestdataTwig->GetRecord(array($_POST['sys_fphp_tablefield_FormElementUUID'], $_POST['sys_fphp_tablefield_ForestDataUUID']))) {
						$o_formelementTwig = new \fPHP\Twigs\formelementTwig;
						$o_forestdataTwig = new \fPHP\Twigs\forestdataTwig;
						
						if (! ($o_formelementTwig->GetRecord(array($_POST['sys_fphp_tablefield_FormElementUUID']))) ) {
							throw new forestException(0x10001401, array($o_formelementTwig->fphp_Table));
						}
						
						if (! ($o_forestdataTwig->GetRecord(array($_POST['sys_fphp_tablefield_ForestDataUUID']))) ) {
							throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
						}
						
						throw new forestException(0x10001F0E, array($o_formelementTwig->Name, $o_forestdataTwig->Name));
					}
				}
			}
			
			$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
			
			/* create new table record */
			$o_tableTwig = new \fPHP\Twigs\tableTwig;
			
			if ($_POST['sys_fphp_table_name'] != $o_glob->URL->Branch) {
				throw new forestException(0x10001F03);
			}
			
			if (! ( (\fPHP\Helper\forestStringLib::StartsWith($_POST['sys_fphp_table_name'], 'fphp_')) || (\fPHP\Helper\forestStringLib::StartsWith($_POST['sys_fphp_table_name'], 'sys_fphp_')) ) ) {
				$_POST['sys_fphp_table_name'] = 'fphp_' . $_POST['sys_fphp_table_name'];
			}
			
			$o_tableTwig->Name = $_POST['sys_fphp_table_name'];
			
			if (array_key_exists('sys_fphp_table_identifier', $_POST)) {
				$o_tableTwig->Identifier = $_POST['sys_fphp_table_identifier'];
			}
			
			$o_tableTwig->InfoColumns = intval($_POST['sys_fphp_table_infoColumns']);
			$o_tableTwig->InfoColumnsView = 0;

			if (array_key_exists('sys_fphp_table_infoColumnsView', $_POST)) {
				if (is_array($_POST['sys_fphp_table_infoColumnsView'])) {
					/* post value is array, so we need to valiate multiple checkboxes */
					$i_sum = 0;
					
					foreach ($_POST['sys_fphp_table_infoColumnsView'] as $s_checkboxValue) {
						if (!preg_match('/[^01$]/', $s_checkboxValue)) {
							$i_sum += intval($s_checkboxValue);
						}
					}
					
					$o_tableTwig->InfoColumnsView = $i_sum;
				} else {
					if (!preg_match('/[^01$]/', $_POST['sys_fphp_table_infoColumnsView'])) {
						$o_tableTwig->InfoColumnsView = intval($_POST['sys_fphp_table_infoColumnsView']);
					} else {
						$o_tableTwig->InfoColumnsView = 0;
					}
				}
			}
			
			$o_tableTwig->Versioning = intval($_POST['sys_fphp_table_versioning']);
			
			/* check that info columns and info columns view match together */
			if ($o_tableTwig->InfoColumns == 1) {
				$o_tableTwig->InfoColumnsView = 0;
			} else if ($o_tableTwig->InfoColumns == 10) {
				if ($o_tableTwig->InfoColumnsView > 11) {
					if ($o_tableTwig->InfoColumnsView > 111) {
						$o_tableTwig->InfoColumnsView -= 1000;
					} else {
						$o_tableTwig->InfoColumnsView -= 100;
					}
				}
			} else if ($o_tableTwig->InfoColumns == 100) {
				$i_temp = $o_tableTwig->InfoColumnsView;
				
				if ($i_temp > 111) {
					$i_temp -= 1000;
				}
				
				if ($i_temp > 11) {
					$i_temp -= 100;
				}
				
				if ($i_temp == 11) {
					$o_tableTwig->InfoColumnsView -= 11;
				} else if ($i_temp == 10) {
					$o_tableTwig->InfoColumnsView -= 10;
				} else if ($i_temp == 1) {
					$o_tableTwig->InfoColumnsView -= 1;
				}
			}
			
			/* insert record */
			$i_result = $o_tableTwig->InsertRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				throw new forestException(0x10001402);
			} else if ($i_result == 1) {
				/* create table in dbms with standard Id + UUID */
				$o_queryCreate = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::CREATE, $o_tableTwig->Name);
				
				$s_columnType = null;
				$i_columnLength = null;
				$i_columnDecimalLength = null;
				\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'integer [int]', $s_columnType, $i_columnLength, $i_columnDecimalLength);
				
				$o_columnId = new \fPHP\Base\forestSQLColumnStructure($o_queryCreate);
					$o_columnId->Name = 'Id';
					
					$o_columnId->ColumnType = $s_columnType;
					if ($i_columnLength != null) { $o_columnId->ColumnTypeLength = $i_columnLength; }
					if ($i_columnDecimalLength != null) { $o_columnId->ColumnTypeDecimalLength = $i_columnDecimalLength; }
					$o_columnId->AlterOperation = 'ADD';
					
					$s_constraintType = null;
					\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'NOT NULL', $s_constraintType);
					$o_columnId->ConstraintList->Add($s_constraintType);
					
					$s_constraintType = null;
					\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'PRIMARY KEY', $s_constraintType);
					$o_columnId->ConstraintList->Add($s_constraintType);
					
					$s_constraintType = null;
					\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'AUTO_INCREMENT', $s_constraintType);
					$o_columnId->ConstraintList->Add($s_constraintType);
				
				$o_queryCreate->Query->Columns->Add($o_columnId);	
				
				$s_columnType = null;
				$i_columnLength = null;
				$i_columnDecimalLength = null;
				\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'text [36]', $s_columnType, $i_columnLength, $i_columnDecimalLength);
				
				$o_columnUUID = new \fPHP\Base\forestSQLColumnStructure($o_queryCreate);
					$o_columnUUID->Name = 'UUID';
					
					$o_columnUUID->ColumnType = $s_columnType;
					if ($i_columnLength != null) { $o_columnUUID->ColumnTypeLength = $i_columnLength; }
					if ($i_columnDecimalLength != null) { $o_columnUUID->ColumnTypeDecimalLength = $i_columnDecimalLength; }
					$o_columnUUID->AlterOperation = 'ADD';
					
					$s_constraintType = null;
					\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'NOT NULL', $s_constraintType);
					$o_columnUUID->ConstraintList->Add($s_constraintType);
					
					$s_constraintType = null;
					\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'UNIQUE', $s_constraintType);
					$o_columnUUID->ConstraintList->Add($s_constraintType);
					
				$o_queryCreate->Query->Columns->Add($o_columnUUID);
				
				/* create identifier column if an identifier has been choosen */
				if (issetStr($o_tableTwig->Identifier->PrimaryValue)) {
					$s_columnType = null;
					$i_columnLength = null;
					$i_columnDecimalLength = null;
					\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'text [255]', $s_columnType, $i_columnLength, $i_columnDecimalLength);
					
					$o_columnIdentifier = new \fPHP\Base\forestSQLColumnStructure($o_queryCreate);
						$o_columnIdentifier->Name = 'Identifier';
						
						$o_columnIdentifier->ColumnType = $s_columnType;
						if ($i_columnLength != null) { $o_columnIdentifier->ColumnTypeLength = $i_columnLength; }
						if ($i_columnDecimalLength != null) { $o_columnIdentifier->ColumnTypeDecimalLength = $i_columnDecimalLength; }
						$o_columnIdentifier->AlterOperation = 'ADD';
						
						$s_constraintType = null;
						\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'NOT NULL', $s_constraintType);
						$o_columnIdentifier->ConstraintList->Add($s_constraintType);
						
						$s_constraintType = null;
						\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'UNIQUE', $s_constraintType);
						$o_columnIdentifier->ConstraintList->Add($s_constraintType);
						
					$o_queryCreate->Query->Columns->Add($o_columnIdentifier);
				}
				
				/* create table does not return a value - maybe using show_tables can be used as extra verification */
				$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryCreate, false, false);
				
				/* update branch record with new table connection */
				$o_branchTwig = new \fPHP\Twigs\branchTwig;
				
				/* query record */
				if (! ($o_branchTwig->GetRecord(array($o_glob->URL->BranchId))) ) {
					throw new forestException(0x10001401, array($o_branchTwig->fphp_Table));
				}
				
				/* update table connection */
				$o_branchTwig->Table = $o_tableTwig->UUID;
				
				/* edit record */
				$i_result = $o_branchTwig->UpdateRecord();
				
				/* evaluate result */
				if ($i_result == -1) {
					throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
				} else if ($i_result == 0) {
					$o_glob->SystemMessages->Add(new forestException(0x10001406));
				} else if ($i_result == 1) {
					/* check posted data for new tablefield record */
					$this->TransferPOST_Twig();
					
					if (in_array($this->Twig->FieldName, $this->ForbiddenTablefieldNames)) {
						throw new forestException(0x10001F10, array(implode(',', $this->ForbiddenTablefieldNames)));
					}
					
					/* add Order value to record */
					$this->Twig->Order = 1;
					
					/* check if json encoded settings are valid */
					$a_settings = json_decode($_POST['sys_fphp_tablefield_JSONEncodedSettings'], true);
				
					if ($a_settings == null) {
						throw new forestException(0x10001F02, array($_POST['sys_fphp_tablefield_JSONEncodedSettings']));
					}
					
					/* memory validation rule from json settings */
					$a_validationRule['rule'] = null;
					$a_validationRule['ruleParam01'] = null;
					$a_validationRule['ruleParam02'] = null;
					$a_validationRule['ruleAutoRequired'] = null;
					
					/* if no json setting for Id is available, add it automatically or handle validation rule if it is present */
					if ( (!array_key_exists('Id', $a_settings)) || (array_key_exists('ValidationRule', $a_settings)) ) {
						if (!array_key_exists('Id', $a_settings)) {
							$a_settings['Id'] = $o_tableTwig->Name . '_' . $this->Twig->FieldName;
						}
						
						if (array_key_exists('ValidationRule', $a_settings)) {
							$a_validationRule['rule'] = $a_settings['ValidationRule'];
							unset($a_settings['ValidationRule']);
						}
						
						if (array_key_exists('ValidationRuleParam01', $a_settings)) {
							$a_validationRule['ruleParam01'] = $a_settings['ValidationRuleParam01'];
							unset($a_settings['ValidationRuleParam01']);
						}
						
						if (array_key_exists('ValidationRuleParam02', $a_settings)) {
							$a_validationRule['ruleParam02'] = $a_settings['ValidationRuleParam02'];
							unset($a_settings['ValidationRuleParam02']);
						}
						
						if (array_key_exists('ValidationRuleAutoRequired', $a_settings)) {
							$a_validationRule['ruleAutoRequired'] = $a_settings['ValidationRuleAutoRequired'];
							unset($a_settings['ValidationRuleAutoRequired']);
						}
						
						$_POST['sys_fphp_tablefield_JSONEncodedSettings'] = json_encode($a_settings, JSON_UNESCAPED_SLASHES );
						$this->Twig->JSONEncodedSettings = strval($_POST['sys_fphp_tablefield_JSONEncodedSettings']);
					}
					
					/* recognize translation name and value pair within json encoded settings and create tranlsation records automatically */
					preg_match_all('/\#([^#]+)\#/', $_POST['sys_fphp_tablefield_JSONEncodedSettings'], $a_matches);
			
					if (count($a_matches) > 1) {
						foreach ($a_matches[1] as $s_match) {
							/* we want to create a new translation record */
							if (strpos($s_match, '=') !== false) {
								$a_match = explode('=', $s_match);
								$s_name = $a_match[0];
								$s_value = $a_match[1];
								/* keep translation name in json encoded settings */
								$_POST['sys_fphp_tablefield_JSONEncodedSettings'] = str_replace('#' . $s_match . '#', '#' . $s_name . '#', $_POST['sys_fphp_tablefield_JSONEncodedSettings']);
								$this->Twig->JSONEncodedSettings = strval($_POST['sys_fphp_tablefield_JSONEncodedSettings']);

								/* prepare translation record */
								$o_translationTwig = new \fPHP\Twigs\translationTwig;
								$o_translationTwig->BranchId = $o_glob->URL->BranchId;
								$o_translationTwig->LanguageCode = $o_glob->Trunk->LanguageCode->PrimaryValue;
								$o_translationTwig->Name = $s_name;
								$o_translationTwig->Value = \fPHP\Helper\forestStringLib::ReplaceUnicodeEscapeSequence($s_value);
								
								/* insert translation record */
								$i_result = $o_translationTwig->InsertRecord();

								/* evaluate result */
								if ($i_result == -1) {
									throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
								} else if ($i_result == 0) {
									throw new forestException(0x10001402);
								}
							}
						}
					}
					
					/* add TableUUID value to record */
					$this->Twig->TableUUID = $o_tableTwig->UUID;
					
					/* insert record */
					$i_result = $this->Twig->InsertRecord();
					
					/* evaluate result */
					if ($i_result == -1) {
						throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
					} else if ($i_result == 0) {
						throw new forestException(0x10001402);
					} else if ($i_result == 1) {
						$o_glob->SystemMessages->Add(new forestException(0x10001F04));
						
						/* remember tablefied uuid */
						$this->HandleProcessingActionChain();

						/* update table in branch tree for current branch for later use */
						$a_branchTree = $o_glob->BranchTree;
						$a_branchTree['Id'][$o_glob->URL->BranchId]['Table'] = new \fPHP\Roots\forestLookup(new \fPHP\Helper\forestLookupData('sys_fphp_table', array('UUID'), array('Name')));
						$a_branchTree['Id'][$o_glob->URL->BranchId]['Table'] = $this->Twig->TableUUID;
						$o_glob->BranchTree = $a_branchTree;
						
						/* add validation rule from input json settings if not null */
						if ($a_validationRule['rule'] != null) {
							$o_tablefieldValidationRuleTwig = new \fPHP\Twigs\tablefield_validationruleTwig;
							$o_tablefieldValidationRuleTwig->TablefieldUUID = $this->Twig->UUID;
							
							/* find validation rule by name */
							$o_getValidationruleTwig = new \fPHP\Twigs\validationruleTwig;
							
							if ($o_getValidationruleTwig->GetRecordPrimary(array($a_validationRule['rule']), array('Name'))) {
								$o_tablefieldValidationRuleTwig->ValidationruleUUID = $o_getValidationruleTwig->UUID;
							
								/* check if validation rule is valid for new tablefield */
								$o_validationruleTwig = new \fPHP\Twigs\validationruleTwig;
								
								$o_formelement_validationruleTwig = new \fPHP\Twigs\formelement_validationruleTwig;
								
								if (! ($o_validationruleTwig->GetRecordPrimary(array('any'), array('Name'))) ) {
									throw new forestException(0x10001401, array($o_validationruleTwig->fphp_Table));
								}
								
								if (! $o_formelement_validationruleTwig->GetRecord(array($this->Twig->FormElementUUID->PrimaryValue, $o_validationruleTwig->UUID))) {
									if (! $o_formelement_validationruleTwig->GetRecord(array($this->Twig->FormElementUUID->PrimaryValue, $o_tablefieldValidationRuleTwig->ValidationruleUUID->PrimaryValue))) {
										throw new forestException(0x10001F0F, array($this->Twig->FormElementUUID, $o_tablefieldValidationRuleTwig->ValidationruleUUID));
									}
								}
								
								/* add validation rule parameters if not null */
								if ( ($a_validationRule['ruleParam01'] == null) && ($a_validationRule['ruleParam02'] == null) ) {
									/* auto set param 01 to 'true' if you enter one of these validation rules */
									if (
										($a_validationRule['rule'] == 'required') || 
										($a_validationRule['rule'] == 'email') || 
										($a_validationRule['rule'] == 'url') || 
										($a_validationRule['rule'] == 'digits') || 
										($a_validationRule['rule'] == 'number') || 
										($a_validationRule['rule'] == 'fphp_month') || 
										($a_validationRule['rule'] == 'fphp_week') || 
										($a_validationRule['rule'] == 'fphp_dateISO') || 
										($a_validationRule['rule'] == 'fphp_time') || 
										($a_validationRule['rule'] == 'dateISO') || 
										($a_validationRule['rule'] == 'fphp_dateDMYpoint') || 
										($a_validationRule['rule'] == 'fphp_dateDMYslash') || 
										($a_validationRule['rule'] == 'fphp_dateMDYslash') || 
										($a_validationRule['rule'] == 'fphp_datetime') || 
										($a_validationRule['rule'] == 'fphp_datetimeISO') || 
										($a_validationRule['rule'] == 'fphp_dateinterval') || 
										($a_validationRule['rule'] == 'fphp_password') || 
										($a_validationRule['rule'] == 'fphp_username') || 
										($a_validationRule['rule'] == 'fphp_onlyletters')
									) {
										$o_tablefieldValidationRuleTwig->ValidationRuleParam01 = 'true';
									}
								}
								
								if ($a_validationRule['ruleParam01'] != null) {
									$o_tablefieldValidationRuleTwig->ValidationRuleParam01 = $a_validationRule['ruleParam01'];
								}
								
								if ($a_validationRule['ruleParam02'] != null) {
									$o_tablefieldValidationRuleTwig->ValidationRuleParam02 = $a_validationRule['ruleParam02'];
								}
								
								if ($a_validationRule['ruleAutoRequired'] != null) {
									$o_tablefieldValidationRuleTwig->ValidationRuleRequired = $a_validationRule['ruleAutoRequired'];
								}
								
								/* create validation rule for new tabelfield */
								$i_result = $o_tablefieldValidationRuleTwig->InsertRecord();;
								
								/* evaluate result */
								if ($i_result == -1) {
									throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
								} else if ($i_result == 0) {
									throw new forestException(0x10001402);
								} else if ($i_result == 1) {
									/* nothing to do */
								}
							}
						}
						
						/* execute dbms create column if sql type is not empty */
						if (issetStr($this->Twig->SqlTypeUUID->PrimaryValue)) {
							/* new tablefield for twig - ignore forestCombination, Form and Dropzone field */
							if ( (!(strval($this->Twig->ForestDataUUID) == 'forestCombination')) && (!(strval($this->Twig->FormElementUUID) == \fPHP\Forms\forestformElement::FORM)) && (!(strval($this->Twig->FormElementUUID) == \fPHP\Forms\forestformElement::DROPZONE)) ) {
								/* add new column within table in dbms */
								$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, $o_tableTwig->Name);
		
								$o_column = new \fPHP\Base\forestSQLColumnStructure($o_queryAlter);
									$o_column->Name = $this->Twig->FieldName;
									
									$s_columnType = null;
									$i_columnLength = null;
									$i_columnDecimalLength = null;
									\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, strval($this->Twig->SqlTypeUUID), $s_columnType, $i_columnLength, $i_columnDecimalLength);
									
									$o_column->ColumnType = $s_columnType;
									if ($i_columnLength != null) { $o_column->ColumnTypeLength = $i_columnLength; }
									if ($i_columnDecimalLength != null) { $o_column->ColumnTypeDecimalLength = $i_columnDecimalLength; }
									$o_column->AlterOperation = 'ADD';
									
									$s_constraintType = null;
									\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'NULL', $s_constraintType);
									$o_column->ConstraintList->Add($s_constraintType);
								
								$o_queryAlter->Query->Columns->Add($o_column);
								
								/* alter table does not return a value */
								$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false);
							}
						}
						
						/* column declaration for created, createdby, modified, modifiedby */
						$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, $o_tableTwig->Name);
						
						$o_columnCreated = new \fPHP\Base\forestSQLColumnStructure($o_queryAlter);
							$o_columnCreated->Name = 'Created';
							
							$s_columnType = null;
							$i_columnLength = null;
							$i_columnDecimalLength = null;
							\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'datetime', $s_columnType, $i_columnLength, $i_columnDecimalLength);
							
							$o_columnCreated->ColumnType = $s_columnType;
							if ($i_columnLength != null) { $o_columnCreated->ColumnTypeLength = $i_columnLength; }
							if ($i_columnDecimalLength != null) { $o_columnCreated->ColumnTypeDecimalLength = $i_columnDecimalLength; }
							$o_columnCreated->AlterOperation = 'ADD';
							
							$s_constraintType = null;
							\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'NULL', $s_constraintType);
							$o_columnCreated->ConstraintList->Add($s_constraintType);
							
						$o_columnCreatedBy = new \fPHP\Base\forestSQLColumnStructure($o_queryAlter);
							$o_columnCreatedBy->Name = 'CreatedBy';
							
							$s_columnType = null;
							$i_columnLength = null;
							$i_columnDecimalLength = null;
							\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'text [36]', $s_columnType, $i_columnLength, $i_columnDecimalLength);
							
							$o_columnCreatedBy->ColumnType = $s_columnType;
							if ($i_columnLength != null) { $o_columnCreatedBy->ColumnTypeLength = $i_columnLength; }
							if ($i_columnDecimalLength != null) { $o_columnCreatedBy->ColumnTypeDecimalLength = $i_columnDecimalLength; }
							$o_columnCreatedBy->AlterOperation = 'ADD';
							
							$s_constraintType = null;
							\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'NULL', $s_constraintType);
							$o_columnCreatedBy->ConstraintList->Add($s_constraintType);
						
						$o_columnModified = new \fPHP\Base\forestSQLColumnStructure($o_queryAlter);
							$o_columnModified->Name = 'Modified';
							
							$s_columnType = null;
							$i_columnLength = null;
							$i_columnDecimalLength = null;
							\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'datetime', $s_columnType, $i_columnLength, $i_columnDecimalLength);
							
							$o_columnModified->ColumnType = $s_columnType;
							if ($i_columnLength != null) { $o_columnModified->ColumnTypeLength = $i_columnLength; }
							if ($i_columnDecimalLength != null) { $o_columnModified->ColumnTypeDecimalLength = $i_columnDecimalLength; }
							$o_columnModified->AlterOperation = 'ADD';
							
							$s_constraintType = null;
							\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'NULL', $s_constraintType);
							$o_columnModified->ConstraintList->Add($s_constraintType);
							
						$o_columnModifiedBy = new \fPHP\Base\forestSQLColumnStructure($o_queryAlter);
							$o_columnModifiedBy->Name = 'ModifiedBy';
							
							$s_columnType = null;
							$i_columnLength = null;
							$i_columnDecimalLength = null;
							\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'text [36]', $s_columnType, $i_columnLength, $i_columnDecimalLength);
							
							$o_columnModifiedBy->ColumnType = $s_columnType;
							if ($i_columnLength != null) { $o_columnModifiedBy->ColumnTypeLength = $i_columnLength; }
							if ($i_columnDecimalLength != null) { $o_columnModifiedBy->ColumnTypeDecimalLength = $i_columnDecimalLength; }
							$o_columnModifiedBy->AlterOperation = 'ADD';
							
							$s_constraintType = null;
							\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'NULL', $s_constraintType);
							$o_columnModifiedBy->ConstraintList->Add($s_constraintType);
						
						/* handle info columns */
						if ($o_tableTwig->InfoColumns == 10) {
							/* create created, createdby columns */
							$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, $o_tableTwig->Name);
							$o_queryAlter->Query->Columns->Add($o_columnCreated);
							$o_queryAlter->Query->Columns->Add($o_columnCreatedBy);
							
							/* alter table does not return a value */
							$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false);
						} else if ($o_tableTwig->InfoColumns == 100) {
							/* create modified, modifiedby columns */
							$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, $o_tableTwig->Name);
							$o_queryAlter->Query->Columns->Add($o_columnModified);
							$o_queryAlter->Query->Columns->Add($o_columnModifiedBy);
							
							/* alter table does not return a value */
							$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false);
						} else if ($o_tableTwig->InfoColumns == 1000) {
							/* create created, createdby, modified, modifiedby columns */
							$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, $o_tableTwig->Name);
							$o_queryAlter->Query->Columns->Add($o_columnCreated);
							$o_queryAlter->Query->Columns->Add($o_columnCreatedBy);
							$o_queryAlter->Query->Columns->Add($o_columnModified);
							$o_queryAlter->Query->Columns->Add($o_columnModifiedBy);
							
							/* alter table does not return a value */
							$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false);
						}
						
						/* create twig file */
						$this->doTwigFile($o_tableTwig);
						
						/* update branch */
						/* get branch file path */
						$s_path = '';
								
						if (count($o_glob->URL->Branches) > 0) {
							foreach($o_glob->URL->Branches as $s_value) {
								$s_path .= $s_value . '/';
							}
						}
						
						$s_path .= $o_glob->URL->Branch . '/';
						
						/* get directory content of current page into array */
						$a_dirContent = scandir('./trunk/' . $s_path);
						$s_path = './trunk/' . $s_path . $o_branchTwig->Name . 'Branch.php';
						
						/* if we cannot find branch file */
						if (!in_array($o_branchTwig->Name . 'Branch.php', $a_dirContent)) {
							throw new forestException('Cannot find file [%0].', array($s_path));
						}
						
						/* update branch file */
						$o_file = new \fPHP\Helper\forestFile($s_path);
						$o_file->ReplaceContent( strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::CREATENEWBRANCHWITHTWIG, array($o_branchTwig->Name))) );
						
						$s_nextAction = 'RELOADBRANCH';
						$s_nextActionAfterReload = 'viewTwig';
					}
				}
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction, $s_nextActionAfterReload);
	}

	/**
	 * handle edit twig record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function editTwigAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if (($this->Twig != null) && ($this->Twig->fphp_SystemTable)) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\tableTwig;
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if ($o_glob->IsPost) {
			/* query record */
			if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_tableKey']))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}
			
			$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
			
			/* save old info columns value for later check */
			$i_oldInfoColumns = $this->Twig->InfoColumns;
			
			/* get View-element data and check for invalid columns */
			if (array_key_exists('sys_fphp_table_View[]', $_POST)) {
				$a_options = explode(';', $_POST['sys_fphp_table_View[]']);
				
				foreach ($a_options as $s_tablefieldUUID) {
					$o_tablefieldTwig = null;
					
					foreach ($o_glob->TablefieldsDictionary as $o_foundTablefieldTwig) {
						if ($s_tablefieldUUID == $o_foundTablefieldTwig->UUID) {
							$o_tablefieldTwig = $o_foundTablefieldTwig;
							break;
						}
					}
					
					if ($o_tablefieldTwig != null) {
						/* delete forestCombination which not starts with SUM( CNT(, FILENAME( and FILEVERSION( */
						if ($o_tablefieldTwig->ForestDataName == 'forestCombination') {
							$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_tablefieldTwig->JSONEncodedSettings);
							$a_settings = json_decode($s_JSONEncodedSettings, true);
							
							if (!( (\fPHP\Helper\forestStringLib::StartsWith($a_settings['forestCombination'], 'SUM(')) || (\fPHP\Helper\forestStringLib::StartsWith($a_settings['forestCombination'], 'CNT(')) || (\fPHP\Helper\forestStringLib::StartsWith($a_settings['forestCombination'], 'FILENAME(')) || (\fPHP\Helper\forestStringLib::StartsWith($a_settings['forestCombination'], 'FILEVERSION(')) )) {
								unset($a_options[$s_key]);
							}
						}
					}
				}
				
				$_POST['sys_fphp_table_View[]'] = implode(';', $a_options);
			}
			
			$this->TransferPOST_Twig();
			
			/* check that info columns and info columns view match together */
			if ($this->Twig->InfoColumns == 1) {
				$this->Twig->InfoColumnsView = 0;
			} else if ($this->Twig->InfoColumns == 10) {
				if ($this->Twig->InfoColumnsView > 11) {
					if ($this->Twig->InfoColumnsView > 111) {
						$this->Twig->InfoColumnsView -= 1000;
					} else {
						$this->Twig->InfoColumnsView -= 100;
					}
				}
			} else if ($this->Twig->InfoColumns == 100) {
				$i_temp = $this->Twig->InfoColumnsView;
				
				if ($i_temp > 111) {
					$i_temp -= 1000;
				}
				
				if ($i_temp > 11) {
					$i_temp -= 100;
				}
				
				if ($i_temp == 11) {
					$this->Twig->InfoColumnsView -= 11;
				} else if ($i_temp == 10) {
					$this->Twig->InfoColumnsView -= 10;
				} else if ($i_temp == 1) {
					$this->Twig->InfoColumnsView -= 1;
				}
			}
			
			/* edit recrod */
			$i_result = $this->Twig->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				$o_glob->SystemMessages->Add(new forestException(0x10001406));
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001407));
				
				/* column declaration for created, createdby, modified, modifiedby */
				$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, $this->Twig->Name);
				
				$o_columnCreated = new \fPHP\Base\forestSQLColumnStructure($o_queryAlter);
					$o_columnCreated->Name = 'Created';
					
					$s_columnType = null;
					$i_columnLength = null;
					$i_columnDecimalLength = null;
					\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'datetime', $s_columnType, $i_columnLength, $i_columnDecimalLength);
					
					$o_columnCreated->ColumnType = $s_columnType;
					if ($i_columnLength != null) { $o_columnCreated->ColumnTypeLength = $i_columnLength; }
					if ($i_columnDecimalLength != null) { $o_columnCreated->ColumnTypeDecimalLength = $i_columnDecimalLength; }
					$o_columnCreated->AlterOperation = 'ADD';
					
					$s_constraintType = null;
					\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'NULL', $s_constraintType);
					$o_columnCreated->ConstraintList->Add($s_constraintType);
					
				$o_columnCreatedBy = new \fPHP\Base\forestSQLColumnStructure($o_queryAlter);
					$o_columnCreatedBy->Name = 'CreatedBy';
					
					$s_columnType = null;
					$i_columnLength = null;
					$i_columnDecimalLength = null;
					\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'text [36]', $s_columnType, $i_columnLength, $i_columnDecimalLength);
					
					$o_columnCreatedBy->ColumnType = $s_columnType;
					if ($i_columnLength != null) { $o_columnCreatedBy->ColumnTypeLength = $i_columnLength; }
					if ($i_columnDecimalLength != null) { $o_columnCreatedBy->ColumnTypeDecimalLength = $i_columnDecimalLength; }
					$o_columnCreatedBy->AlterOperation = 'ADD';
					
					$s_constraintType = null;
					\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'NULL', $s_constraintType);
					$o_columnCreatedBy->ConstraintList->Add($s_constraintType);
				
				$o_columnModified = new \fPHP\Base\forestSQLColumnStructure($o_queryAlter);
					$o_columnModified->Name = 'Modified';
					
					$s_columnType = null;
					$i_columnLength = null;
					$i_columnDecimalLength = null;
					\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'datetime', $s_columnType, $i_columnLength, $i_columnDecimalLength);
					
					$o_columnModified->ColumnType = $s_columnType;
					if ($i_columnLength != null) { $o_columnModified->ColumnTypeLength = $i_columnLength; }
					if ($i_columnDecimalLength != null) { $o_columnModified->ColumnTypeDecimalLength = $i_columnDecimalLength; }
					$o_columnModified->AlterOperation = 'ADD';
					
					$s_constraintType = null;
					\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'NULL', $s_constraintType);
					$o_columnModified->ConstraintList->Add($s_constraintType);
						
				$o_columnModifiedBy = new \fPHP\Base\forestSQLColumnStructure($o_queryAlter);
					$o_columnModifiedBy->Name = 'ModifiedBy';
					
					$s_columnType = null;
					$i_columnLength = null;
					$i_columnDecimalLength = null;
					\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'text [36]', $s_columnType, $i_columnLength, $i_columnDecimalLength);
					
					$o_columnModifiedBy->ColumnType = $s_columnType;
					if ($i_columnLength != null) { $o_columnModifiedBy->ColumnTypeLength = $i_columnLength; }
					if ($i_columnDecimalLength != null) { $o_columnModifiedBy->ColumnTypeDecimalLength = $i_columnDecimalLength; }
					$o_columnModifiedBy->AlterOperation = 'ADD';
					
					$s_constraintType = null;
					\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'NULL', $s_constraintType);
					$o_columnModifiedBy->ConstraintList->Add($s_constraintType);
				
				/* handle info columns */
				$a_infoColumnsCommands = array();
				
				/* calculate commands based on infocolumns before-after value */
				if (($i_oldInfoColumns == 1) && ($this->Twig->InfoColumns == 10)) {
					$a_infoColumnsCommands[] = 'addC';
				} else if (($i_oldInfoColumns == 1) && ($this->Twig->InfoColumns == 100)) {
					$a_infoColumnsCommands[] = 'addM';
				} else if (($i_oldInfoColumns == 1) && ($this->Twig->InfoColumns == 1000)) {
					$a_infoColumnsCommands[] = 'addC';
					$a_infoColumnsCommands[] = 'addM';
				} else if (($i_oldInfoColumns == 10) && ($this->Twig->InfoColumns == 1)) {
					$a_infoColumnsCommands[] = 'dropC';
				} else if (($i_oldInfoColumns == 10) && ($this->Twig->InfoColumns == 100)) {
					$a_infoColumnsCommands[] = 'dropC';
					$a_infoColumnsCommands[] = 'addM';
				} else if (($i_oldInfoColumns == 10) && ($this->Twig->InfoColumns == 1000)) {
					$a_infoColumnsCommands[] = 'addM';
				} else if (($i_oldInfoColumns == 100) && ($this->Twig->InfoColumns == 1)) {
					$a_infoColumnsCommands[] = 'dropM';
				} else if (($i_oldInfoColumns == 100) && ($this->Twig->InfoColumns == 10)) {
					$a_infoColumnsCommands[] = 'dropM';
					$a_infoColumnsCommands[] = 'addC';
				} else if (($i_oldInfoColumns == 100) && ($this->Twig->InfoColumns == 1000)) {
					$a_infoColumnsCommands[] = 'addC';
				} else if (($i_oldInfoColumns == 1000) && ($this->Twig->InfoColumns == 1)) {
					$a_infoColumnsCommands[] = 'dropC';
					$a_infoColumnsCommands[] = 'dropM';
				} else if (($i_oldInfoColumns == 1000) && ($this->Twig->InfoColumns == 10)) {
					$a_infoColumnsCommands[] = 'dropM';
				} else if (($i_oldInfoColumns == 1000) && ($this->Twig->InfoColumns == 100)) {
					$a_infoColumnsCommands[] = 'dropC';
				}
				
				/* execute alter table commands based on infocolumns before-after value */
				
				if (in_array('addC', $a_infoColumnsCommands)) {
					/* create created, createdby columns */
					$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, $this->Twig->Name);
					$o_queryAlter->Query->Columns->Add($o_columnCreated);
					$o_queryAlter->Query->Columns->Add($o_columnCreatedBy);
					
					/* alter table does not return a value */
					$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false);
				}
				
				if (in_array('addM', $a_infoColumnsCommands)) {
					/* create modified, modifiedby columns */
					$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, $this->Twig->Name);
					$o_queryAlter->Query->Columns->Add($o_columnModified);
					$o_queryAlter->Query->Columns->Add($o_columnModifiedBy);
					
					/* alter table does not return a value */
					$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false);
				}
				
				if (in_array('dropC', $a_infoColumnsCommands)) {
					$o_columnCreated->AlterOperation = 'DROP';
					$o_columnCreatedBy->AlterOperation = 'DROP';
					
					/* drop created, createdby columns */
					$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, $this->Twig->Name);
					$o_queryAlter->Query->Columns->Add($o_columnCreated);
					$o_queryAlter->Query->Columns->Add($o_columnCreatedBy);
					
					/* alter table does not return a value */
					$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false);
				}
				
				if (in_array('dropM', $a_infoColumnsCommands)) {
					$o_columnModified->AlterOperation = 'DROP';
					$o_columnModifiedBy->AlterOperation = 'DROP';
					
					/* drop modified, modifiedby columns */
					$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, $this->Twig->Name);
					$o_queryAlter->Query->Columns->Add($o_columnModified);
					$o_queryAlter->Query->Columns->Add($o_columnModifiedBy);
					
					/* alter table does not return a value */
					$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false);
				}
				
				/* update twig file */
				$this->doTwigFile($this->Twig);
			}
		} else {
			/* query record */
			if (! ($this->Twig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}
			
			$o_forestdataTwig = new \fPHP\Twigs\forestdataTwig;
			
			if (! ($o_forestdataTwig->GetRecordPrimary(array('forestCombination'), array('Name'))) ) {
				throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
			}
			
			$s_forestCombinationUUID = $o_forestdataTwig->UUID;
			
			if (! ($o_forestdataTwig->GetRecordPrimary(array('forestLookup'), array('Name'))) ) {
				throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
			}
			
			$s_forestLookupUUID = $o_forestdataTwig->UUID;
			
			/* update lookup filter */
			$o_forestLookupData = new \fPHP\Helper\forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID, '!2ForestDataUUID' => $s_forestLookupUUID));
			$this->Twig->Unique->SetLookupData($o_forestLookupData);
			$this->Twig->SortOrder->SetLookupData($o_forestLookupData);
			$this->Twig->View->SetLookupData(new \fPHP\Helper\forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID)));
			$this->Twig->SortColumn->SetLookupData($o_forestLookupData);
			
			/* build modal form */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('EditModalTitle', 1);
			
			/* add current record key to modal form */
			$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
			$o_hidden->Id = 'sys_fphp_tableKey';
			$o_hidden->Value = strval($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue);
			
			$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
			
			/* delete StorageSpace-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_StorageSpace')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_StorageSpace].');
			}
			
			/* delete Identifier-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Identifier')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_Identifier].');
			}
			
			/* delete Unique-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Unique[]')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_Unique[]].');
			}
			
			/* delete SortOrder-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_SortOrder')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_SortOrder].');
			}
			
			/* get View-element */
			$o_viewElement = $o_glob->PostModalForm->GetFormElementByFormId('sys_fphp_table_View[]');
			
			if ($o_viewElement != null) {
				$a_options = $o_viewElement->Options;
				
				foreach ($a_options as $s_key => $s_value) {
					/* skip forestCombination which not starts with SUM( CNT(, FILENAME( and FILEVERSION( */
					if ( ($o_glob->TablefieldsDictionary->Exists($this->Twig->Name . '_' . $s_key)) && ($o_glob->TablefieldsDictionary->{$this->Twig->Name . '_' . $s_key}->ForestDataName == 'forestCombination') ) {
						$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_glob->TablefieldsDictionary->{$this->Twig->Name . '_' . $s_key}->JSONEncodedSettings);
						$a_settings = json_decode($s_JSONEncodedSettings, true);
						
						if (!( (\fPHP\Helper\forestStringLib::StartsWith($a_settings['forestCombination'], 'SUM(')) || (\fPHP\Helper\forestStringLib::StartsWith($a_settings['forestCombination'], 'CNT(')) || (\fPHP\Helper\forestStringLib::StartsWith($a_settings['forestCombination'], 'FILENAME(')) || (\fPHP\Helper\forestStringLib::StartsWith($a_settings['forestCombination'], 'FILEVERSION(')) )) {
							unset($a_options[$s_key]);
						}
					}
				}
				
				$o_viewElement->Options = $a_options;
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/**
	 * handle view twig record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function viewTwigAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\tableTwig;
			
		/* query twig record if we have view key in url parameters */
		if (! ($this->Twig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($this->Twig->fphp_Table));
		}
		
		$o_forestdataTwig = new \fPHP\Twigs\forestdataTwig;
			
		if (! ($o_forestdataTwig->GetRecordPrimary(array('forestCombination'), array('Name'))) ) {
			throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
		}
		
		$s_forestCombinationUUID = $o_forestdataTwig->UUID;
		
		if (! ($o_forestdataTwig->GetRecordPrimary(array('forestLookup'), array('Name'))) ) {
			throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
		}
		
		$s_forestLookupUUID = $o_forestdataTwig->UUID;
		
		/* update lookup filter */
		$o_forestLookupData = new \fPHP\Helper\forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID, '!2ForestDataUUID' => $s_forestLookupUUID));
		$this->Twig->Unique->SetLookupData($o_forestLookupData);
		$this->Twig->SortOrder->SetLookupData($o_forestLookupData);
		$this->Twig->View->SetLookupData(new \fPHP\Helper\forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID)));
		$this->Twig->SortColumn->SetLookupData($o_forestLookupData);
		
		/* create modal read only form */
		$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true, true);
		$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->URL->BranchTitle . ' Twig';
		
		/* check for fphp_files folder */
		$i_parentBranchId = $o_glob->URL->BranchId;
			
		/* with our branchId we start a loop for all parent branches to get our necessary path for linking */
		do {
			$a_branches[] = $o_glob->BranchTree['Id'][$i_parentBranchId]['Name'];
			$i_parentBranchId = $o_glob->BranchTree['Id'][$i_parentBranchId]['ParentBranch'];
		} while ($i_parentBranchId != 0);
		
		$a_branches = array_reverse($a_branches);
		$s_link = './trunk';
		
		/* create link path */
		foreach($a_branches as $s_branch) {
			$s_link .= '/' . $s_branch;
		}
		
		/* variable to save branch storage space */
		$i_sum = 0;
		
		/* fphp_files folder must exist and be a directory */
		if ( (file_exists($s_link . '/fphp_files')) && (is_dir($s_link . '/fphp_files')) ) {
			foreach (scandir($s_link . '/fphp_files') as $subfolder) {
				if ( (is_dir($s_link . '/fphp_files/' . $subfolder)) && ($subfolder != ".") && ($subfolder != "..") ) {
					foreach (scandir($s_link . '/fphp_files/' . $subfolder) as $file) {
						if ( ($file != ".") && ($file != "..") ) {
							$i_sum += filesize($s_link . '/fphp_files/' . $subfolder . '/' . $file);
						}
					}
				}
			}
		}
		
		/* delete StorageSpace-element if branch has no files */
		if ($i_sum < 1) {
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('readonly_sys_fphp_table_StorageSpace')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_StorageSpace].');
			}
		} else {
			$o_storageSpaceElement = $o_glob->PostModalForm->GetFormElementByFormId('readonly_sys_fphp_table_StorageSpace');
			
			if ($o_storageSpaceElement != null) {
				$o_storageSpaceElement->Value = getNiceFileSize($i_sum, false);
			}
		}
		
		/* delete Unique-element */
		if (!$o_glob->PostModalForm->DeleteFormElementByFormId('readonly_sys_fphp_table_Unique[]')) {
			throw new forestException('Cannot delete form element with Id[sys_fphp_table_Unique[]].');
		}
		
		/* delete SortOrder-element */
		if (!$o_glob->PostModalForm->DeleteFormElementByFormId('readonly_sys_fphp_table_SortOrder')) {
			throw new forestException('Cannot delete form element with Id[sys_fphp_table_SortOrder].');
		}
		
		/* edit link */
		$s_editButton = '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'editTwig') . '" class="btn btn-lg btn-light" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="bi bi-pencil-square"></span></a><br>' . "\n";
		$o_glob->PostModalForm->BeforeForm = $s_editButton;
		$o_glob->PostModalForm->BeforeFormRightAlign = true;
		
		/* add tablefields, translations, unique keys, sort orders, sub constraints and sub constraint tablefields for modal form */
		$o_glob->PostModalForm->FormModalSubForm = strval($this->ListTwigProperties($this->Twig));
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction('init');
	}
	
	/**
	 * handle transfer twig recorrd action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function transferTwigAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$a_systemBranches = array(
			'account','forestdata','forestphp','formelement','index','language','log','permission','role','root',
			'session','settings','sqltype','systemmessage','translation','trunk','user','useradmin','usergroup','validationrule'
		);
		
		/* check if standard twig is a system object, even as root */
		if ( (in_array($o_glob->URL->Branch, $a_systemBranches)) || ( ($this->Twig != null) && ( ($this->Twig->fphp_SystemTable) || (\fPHP\Helper\forestStringLib::StartsWith($this->Twig->fphp_Table, 'sys_fphp_')) ) ) ) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\branchTwig;
		$o_table = new \fPHP\Twigs\tableTwig;
		
		if ($o_glob->URL->BranchId == 1) {
			throw new forestException(0x10001F05);
		}
		
		/* query branch record */
		if (! ($this->Twig->GetRecord(array($o_glob->URL->BranchId))) ) {
			throw new forestException(0x10001401, array($this->Twig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			/* create modal confirmation form for deleting record */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig);
			$s_title = $o_glob->GetTranslation('TransferTwigModalTitle', 1);
			$o_glob->PostModalForm->CreateModalForm($this->Twig, $s_title);
			
			$o_branchTwig = new \fPHP\Twigs\branchTwig;
			$a_sqlAdditionalFilter = array();
			
			$s_label = $o_glob->GetTranslation('formTransferTwigFromLabel', 0);
			$s_valMessage = $o_glob->GetTranslation('formTransferTwigFromValMessage', 0);
			
			if (issetStr($this->Twig->Table->PrimaryValue)) {
				/* get all branches without table settings */
				$a_sqlAdditionalFilter = array(array('column' => 'Table', 'value' => 'NULL', 'operator' => 'IS', 'filterOperator' => 'AND'), array('column' => 'Id', 'value' => 1, 'operator' => '<>', 'filterOperator' => 'AND'));
				
				$s_label = $o_glob->GetTranslation('formTransferTwigToLabel', 0);
				$s_valMessage = $o_glob->GetTranslation('formTransferTwigToValMessage', 0);
			} else {
				/* get all branches with table settings */
				$a_sqlAdditionalFilter = array(array('column' => 'Table', 'value' => 'NULL', 'operator' => 'IS NOT', 'filterOperator' => 'AND'), array('column' => 'Id', 'value' => 1, 'operator' => '<>', 'filterOperator' => 'AND'));
			}
			
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_branches = $o_branchTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			$a_options = array();

			foreach($o_branches->Twigs as $o_branch) {
				if (in_array($o_branch->Name, $a_systemBranches)) {
					continue;
				}
				
				$a_options[$o_branch->Title . ' (' . $o_branch->Name . ')'] = $o_branch->Id;
			}
			
			$o_select = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::SELECT);
			$o_select->Label = $s_label;
			$o_select->Id = 'sys_fphp_transferTwig_branch_id';
			$o_select->ValMessage = $s_valMessage;
			$o_select->Required = true;
			$o_select->Options = $a_options;
			
			$o_glob->PostModalForm->FormElements->Add($o_select);
			
			/* add validation rule for manual created form element */
			$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule($o_select->Id, 'required', 'true'));
		} else {
			if (intval($_POST['sys_fphp_transferTwig_branch_id']) == 1) {
				throw new forestException(0x10001F05);
			}
			
			$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
			
			$o_branchTwig = new \fPHP\Twigs\branchTwig;
			
			if (! ($o_branchTwig->GetRecord(array($_POST['sys_fphp_transferTwig_branch_id']))) ) {
				throw new forestException(0x10001401, array($o_branchTwig->fphp_Table));
			}
			
			if (issetStr($this->Twig->Table->PrimaryValue)) {
				/* transfer twig to target branch */
				$o_sourceBranch = $this->Twig;
				$o_targetBranch = $o_branchTwig;
			} else {
				/* transfer twig from target branch */
				$o_sourceBranch = $o_branchTwig;
				$o_targetBranch = $this->Twig;
			}
			
			/* edit branches */
			$o_targetBranch->Table = $o_sourceBranch->Table->PrimaryValue;
			$o_targetBranch->Filter = true;
			$o_targetBranch->KeepFilter = $o_sourceBranch->KeepFilter;
			$o_sourceBranch->Table = 'NULL';
			$o_sourceBranch->KeepFilter = false;
			$o_sourceBranch->Filter = false;
			
			/* edit source branch record */
			$i_result = $o_sourceBranch->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			}
			
			/* edit target branch record */
			$i_result = $o_targetBranch->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			}
			
			/* update source branch file */
			/* exchange branch file with new branch file + landing page */
			/* generate source path */
			$o_glob->SetVirtualTarget($o_sourceBranch->Id);
			$s_sourcePath = '';
			
			if (count($o_glob->URL->VirtualBranches) > 0) {
				foreach($o_glob->URL->VirtualBranches as $s_value) {
					$s_sourcePath .= $s_value . '/';
				}
			} else {
				$s_sourcePath .= $o_glob->URL->VirtualBranch . '/';
			}
			
			/* get directory content of current page into array */
			$a_dirContent = scandir('./trunk/' . $s_sourcePath);
			$s_sourcePath = './trunk/' . $s_sourcePath . $o_sourceBranch->Name . 'Branch.php';
			
			/* if we cannot find branch file */
			if (!in_array($o_sourceBranch->Name . 'Branch.php', $a_dirContent)) {
				throw new forestException('Cannot find file [%0].', array($o_sourceBranch->Name . 'Branch.php'));
			}
			
			$o_file = new \fPHP\Helper\forestFile($s_sourcePath);
			$o_file->ReplaceContent( strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::CREATENEWBRANCH, array($o_sourceBranch->Name))) );
			
			/* update target branch file */
			/* exchange branch file with new branch file */
			/* generate target path */
			$o_glob->SetVirtualTarget($o_targetBranch->Id);
			$s_targetPath = '';
			
			if (count($o_glob->URL->VirtualBranches) > 0) {
				foreach($o_glob->URL->VirtualBranches as $s_value) {
					$s_targetPath .= $s_value . '/';
				}
			} else {
				$s_targetPath .= $o_glob->URL->VirtualBranch . '/';
			}
			
			/* get directory content of current page into array */
			$a_dirContent = scandir('./trunk/' . $s_targetPath);
			$s_targetPath = './trunk/' . $s_targetPath . $o_targetBranch->Name . 'Branch.php';
			
			/* if we cannot find branch file */
			if (!in_array($o_targetBranch->Name . 'Branch.php', $a_dirContent)) {
				throw new forestException('Cannot find file [%0].', array($o_targetBranch->Name . 'Branch.php'));
			}
			
			/* rename table name */
			$o_tableTwig = new \fPHP\Twigs\tableTwig;
			
			if (! ($o_tableTwig->GetRecord(array($o_targetBranch->Table->PrimaryValue))) ) {
				throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
			}
			
			$o_tableTwig->Name = 'fphp_' . $o_targetBranch->Name;
			
			/* update branch file because of transfer */
			$o_file = new \fPHP\Helper\forestFile($s_targetPath);
			$o_file->ReplaceContent( strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::CREATENEWBRANCHWITHTWIG, array($o_targetBranch->Name))) );
			
			/* rename table */
			$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, 'fphp_' . $o_sourceBranch->Name);
			$o_queryAlter->Query->NewTableName = $o_tableTwig->Name;
			$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false);
			
			/* rename table name in sys_fphp_table */
			$i_result = $o_tableTwig->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			}
			
			/* edit files */
			$o_filesTwig = new \fPHP\Twigs\filesTwig;
			$a_sqlAdditionalFilter = array(array('column' => 'BranchId', 'value' => $o_sourceBranch->Id, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_files = $o_filesTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			foreach ($o_files->Twigs as $o_file) {
				/* edit translation branch id */
				$o_file->BranchId = $o_targetBranch->Id;
				
				/* edit translation recrod */
				$i_result = $o_file->UpdateRecord();
				
				/* evaluate result */
				if ($i_result == -1) {
					throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
				}
			}
			
			/* transfer files */
			/* generate source path */
			$o_glob->SetVirtualTarget($o_sourceBranch->Id);
			$s_sourcePath = '';
			
			if (count($o_glob->URL->VirtualBranches) > 0) {
				foreach ($o_glob->URL->VirtualBranches as $s_value) {
					$s_sourcePath .= $s_value . '/';
				}
			} else {
				$s_sourcePath .= $o_glob->URL->VirtualBranch . '/';
			}
			
			$s_sourcePath = './trunk/' . $s_sourcePath . 'fphp_files/';
			
			/* generate target path */
			$o_glob->SetVirtualTarget($o_targetBranch->Id);
			$s_targetPath = '';
			
			if (count($o_glob->URL->VirtualBranches) > 0) {
				foreach ($o_glob->URL->VirtualBranches as $s_value) {
					$s_targetPath .= $s_value . '/';
				}
			} else {
				$s_targetPath .= $o_glob->URL->VirtualBranch . '/';
			}
			
			$s_targetPath = './trunk/' . $s_targetPath . 'fphp_files/';
			
			\fPHP\Helper\forestFile::CopyRecursive($s_sourcePath, $s_targetPath);
			\fPHP\Helper\forestFile::RemoveDirectoryRecursive($s_sourcePath);
			
			/* rename old table name to new table name in all twig files, because of lookup use */
			$a_dirContent = scandir('./twigs/');
			
			foreach ($a_dirContent as $s_twigFile) {
				if ( (!is_dir('./twigs/' . $s_twigFile)) && ($s_twigFile != '.') && ($s_twigFile != '..') ) {
					$s_fileContent = file_get_contents('./twigs/' . $s_twigFile);
					
					if ( (!(strpos($s_fileContent, '\'fphp_' . $o_sourceBranch->Name . '\'') === false)) || (!(strpos($s_fileContent, $o_sourceBranch->Name . 'Twig') === false)) ) {
						file_put_contents('./twigs/' . $s_twigFile, str_replace(array('\'fphp_' . $o_sourceBranch->Name . '\'', $o_sourceBranch->Name . 'Twig'), array('\'fphp_' . $o_targetBranch->Name . '\'', $o_targetBranch->Name . 'Twig'), $s_fileContent));
					}
				}
			}
			
			/* rename twig file */
			if (file_exists('./twigs/' . $o_sourceBranch->Name . 'Twig.php')) {
				rename('./twigs/' . $o_sourceBranch->Name . 'Twig.php', './twigs/' . $o_targetBranch->Name . 'Twig.php');
			}
			
			/* all work for transfer twig has been done */
			$o_glob->SystemMessages->Add(new forestException(0x10001F0C));
			
			/* rename each occurence of old table name with new table name in sys_fphp_tablefield in json settings */
			/* all occurences in json encoded settings have 'old table name + a special character' which must be replaced */
			$a_specialCharacters = array('{[(_)]}', '"', '$', ')');
			
			foreach ($a_specialCharacters as $s_specialCharacter) {
				$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
				$a_sqlAdditionalFilter = array(array('column' => 'JSONEncodedSettings', 'value' => '%fphp_' . $o_sourceBranch->Name . $s_specialCharacter . '%', 'operator' => 'LIKE', 'filterOperator' => 'AND', 'escapeMarkedUnderscore' => true));
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				
				if ($s_specialCharacter == '{[(_)]}') {
					$s_specialCharacter = '_';
				} else if ($s_specialCharacter == '"') {
					$s_specialCharacter = '&quot;';
				}
				
				foreach ($o_tablefields->Twigs as $o_tablefield) {
					/* look for string part we want to replace, so we do not update every single tablefield */
					if (!(strpos($o_tablefield->JSONEncodedSettings, 'fphp_' . $o_sourceBranch->Name . $s_specialCharacter) === false)) {
						/* edit tablefield json encoded settings */
						$o_tablefield->JSONEncodedSettings = str_replace( '&quot;', '"', str_replace('fphp_' . $o_sourceBranch->Name . $s_specialCharacter, 'fphp_' . $o_targetBranch->Name . $s_specialCharacter, $o_tablefield->JSONEncodedSettings) );
						
						/* edit tablefield recrod */
						$i_result = $o_tablefield->UpdateRecord();
						
						/* evaluate result */
						if ($i_result == -1) {
							throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
						}
					}
				}
			}
			
			/* edit translations */
			$o_translationTwig = new \fPHP\Twigs\translationTwig;
			$a_sqlAdditionalFilter = array(array('column' => 'BranchId', 'value' => $o_sourceBranch->Id, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_translations = $o_translationTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			foreach ($o_translations->Twigs as $o_translation) {
				/* edit translation branch id */
				$o_translation->BranchId = $o_targetBranch->Id;
				
				/* edit translation recrod */
				$i_result = $o_translation->UpdateRecord();
				
				/* evaluate result */
				if ($i_result == -1) {
					throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
				}
			}
			
			$s_nextAction = 'RELOADBRANCH';
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/**
	 * handle truncate twig record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function truncateTwigAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\tableTwig;
			
		/* query table record */
		if (! ($this->Twig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($this->Twig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			/* create modal confirmation form for deleting record */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig);
			$s_title = $o_glob->GetTranslation('TruncateModalTitle', 1);
			
			$s_description = '<div class="alert alert-warning" role="alert">' .
				'<div><span class="bi bi-exclamation-triangle-fill h5"></span>&nbsp;' . $o_glob->GetTranslation('TruncateModalDescription', 1) . "\n" . '</div>' . "\n" .
			'</div>' . "\n";

			$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);
		} else {
			$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
			
			/* execute truncation of twig */
			$this->executeTruncateTwig($this->Twig);
			
			$o_glob->SystemMessages->Add(new forestException(0x10001F0A));
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction('init');
	}
	
	/**
	 * truncate execution of a twig
	 *
	 * @param tableTwig $p_o_table  table record of table sys_fphp_table which will be truncated
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function executeTruncateTwig(\fPHP\Twigs\tableTwig $p_o_table) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* get table twig object */
		$s_table = $p_o_table->Name;
		\fPHP\Helper\forestStringLib::RemoveTablePrefix($s_table);
		$s_foo = '\\fPHP\\Twigs\\' . $s_table . 'Twig';
		$o_twig = new $s_foo;
		
		/* query all records */
		$o_records = $o_twig->GetAllRecords(true);
		
		foreach ($o_records->Twigs as $o_record) {
			/* check record relations before deletion */
			$this->CheckandCleanupRecordBeforeDeletion($o_record);
			
			/* delete record */
			$this->executeDeleteRecord($o_record);
			
			$this->CreateLogEntry('record deleted by twig truncate', $o_record);
		}
	}
	
	/**
	 * handle delete twig record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function deleteTwigAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\tableTwig;
			
		/* query table record */
		if (! ($this->Twig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($this->Twig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			/* create modal confirmation form for deleting record */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig);
			$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
			
			$s_description = '<div class="alert alert-danger" role="alert">' .
				'<div><span class="bi bi-exclamation-square-fill h5"></span>&nbsp;' . $o_glob->GetTranslation('DeleteModalDescription', 1) . "\n" . '</div>' . "\n" .
			'</div>' . "\n";

			$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);
		} else {
			$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
			
			/* check sub constraint records, if twig is join part of sub constraint */
			$o_subconstraintTwig = new \fPHP\Twigs\subconstraintTwig;
			
			$a_sqlAdditionalFilter = array(array('column' => 'SubTableUUID', 'value' => $this->Twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$i_subconstraints = $o_subconstraintTwig->GetCount(null, true, false);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			if ($i_subconstraints > 0) {
				throw new forestException(0x10001F0B);
			}
			
			/* execute truncation of twig */
			$this->executeTruncateTwig($this->Twig);
			
			/* delete sub constraint records */
			$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $this->Twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_subconstraints = $o_subconstraintTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			foreach ($o_subconstraints->Twigs as $o_subconstraint) {
				/* delete tablefield records */
				$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
				
				$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $o_subconstraint->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				
				foreach ($o_tablefields->Twigs as $o_tablefield) {
					/* delete tablefield validationrule records */
					$o_tablefield_validationruleTwig = new \fPHP\Twigs\tablefield_validationruleTwig;
					
					$a_sqlAdditionalFilter = array(array('column' => 'TablefieldUUID', 'value' => $o_tablefield->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
					$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
					$o_tablefield_validationrules = $o_tablefield_validationruleTwig->GetAllRecords(true);
					$o_glob->Temp->Del('SQLAdditionalFilter');
					
					foreach ($o_tablefield_validationrules->Twigs as $o_tablefield_validationrule) {
						/* delete tablefield validationrule record */
						$i_return = $o_tablefield_validationrule->DeleteRecord();
					
						/* evaluate the result */
						if ($i_return <= 0) {
							throw new forestException(0x10001423);
						}
					}
					
					/* delete translation records */
					preg_match_all('/\#([^#]+)\#/', $o_tablefield->JSONEncodedSettings, $a_matches);
			
					if (count($a_matches) > 1) {
						$o_translationTwig = new \fPHP\Twigs\translationTwig;
						
						foreach ($a_matches[1] as $s_match) {
							if ($o_translationTwig->GetRecordPrimary(array($o_glob->URL->BranchId, $s_match), array('BranchId', 'Name'))) {
								/* delete translation record */
								$i_return = $o_translationTwig->DeleteRecord();
							
								/* evaluate the result */
								if ($i_return <= 0) {
									throw new forestException(0x10001423);
								}
							}
						}
					}
					
					/* delete tablefield record */
					$i_return = $o_tablefield->DeleteRecord();
				
					/* evaluate the result */
					if ($i_return <= 0) {
						throw new forestException(0x10001423);
					}
				}
				
				/* delete sub constraint record */
				$i_return = $o_subconstraint->DeleteRecord();
			
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
			}
			
			/* delete tablefield records */
			$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
			
			$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $this->Twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			foreach ($o_tablefields->Twigs as $o_tablefield) {
				/* delete tablefield validationrule records */
				$o_tablefield_validationruleTwig = new \fPHP\Twigs\tablefield_validationruleTwig;
				
				$a_sqlAdditionalFilter = array(array('column' => 'TablefieldUUID', 'value' => $o_tablefield->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$o_tablefield_validationrules = $o_tablefield_validationruleTwig->GetAllRecords(true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				
				foreach ($o_tablefield_validationrules->Twigs as $o_tablefield_validationrule) {
					/* delete tablefield validationrule record */
					$i_return = $o_tablefield_validationrule->DeleteRecord();
				
					/* evaluate the result */
					if ($i_return <= 0) {
						throw new forestException(0x10001423);
					}
				}
				
				/* delete translation records */
				preg_match_all('/\#([^#]+)\#/', $o_tablefield->JSONEncodedSettings, $a_matches);
		
				if (count($a_matches) > 1) {
					$o_translationTwig = new \fPHP\Twigs\translationTwig;
					
					foreach ($a_matches[1] as $s_match) {
						if ($o_translationTwig->GetRecordPrimary(array($o_glob->URL->BranchId, $s_match), array('BranchId', 'Name'))) {
							/* delete translation record */
							$i_return = $o_translationTwig->DeleteRecord();
						
							/* evaluate the result */
							if ($i_return <= 0) {
								throw new forestException(0x10001423);
							}
						}
					}
				}
				
				/* delete tablefield record */
				$i_return = $o_tablefield->DeleteRecord();
			
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
			}
			
			/* delete flex records */
			$o_flexTwig = new \fPHP\Twigs\flexTwig;
			
			$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $this->Twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_flexs = $o_flexTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			foreach ($o_flexs->Twigs as $o_flex) {
				/* delete flex record */
				$i_return = $o_flex->DeleteRecord();
			
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
			}
			
			/* disconnect twig from branch */
			$o_branchTwig = new \fPHP\Twigs\branchTwig;
			
			/* query branch record */
			if (! ($o_branchTwig->GetRecord(array($o_glob->URL->BranchId))) ) {
				throw new forestException(0x10001401, array($o_branchTwig->fphp_Table));
			}
			
			/* disconnect table connection */
			$o_branchTwig->Table = 'NULL';
			
			/* edit branch record */
			$i_result = $o_branchTwig->UpdateRecord();
			
			/* exchange branch file with new branch file + landing page */
			$s_path = '';
			
			if (count($o_glob->URL->Branches) > 0) {
				foreach($o_glob->URL->Branches as $s_value) {
					$s_path .= $s_value . '/';
				}
			}
			
			/* get directory content of current page into array */
			$a_dirContent = scandir('./trunk/' . $s_path);
			$s_path = './trunk/' . $s_path . $o_branchTwig->Name . '/';
			
			/* if we cannot find folder */
			if (!in_array($o_branchTwig->Name, $a_dirContent)) {
				throw new forestException('Cannot find directory [%0].', array($s_path));
			}
			
			$a_dirContent = scandir($s_path);
			$s_path = $s_path . $o_branchTwig->Name . 'Branch.php';
			
			/* if we cannot find branch file */
			if (!in_array($o_branchTwig->Name . 'Branch.php', $a_dirContent)) {
				throw new forestException('Cannot find file [%0].', array($o_branchTwig->Name . 'Branch.php'));
			}
			
			/* if we cannot delete branch file */
			if (!(@unlink($s_path))) {
				throw new forestException(0x10001422, array($s_path));
			}
			
			$o_file = new \fPHP\Helper\forestFile($s_path, true);
			$o_file->ReplaceContent( strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::CREATENEWBRANCH, array($o_branchTwig->Name))) );
			
			/* delete twig file */
			/* get twigs directory content */
			$a_dirContent = scandir('./twigs/');
			$s_tempName = $this->Twig->Name;
			\fPHP\Helper\forestStringLib::RemoveTablePrefix($s_tempName);
			$s_tempName .= 'Twig.php';
			
			/* if we can find twig file, delete it */
			if (in_array($s_tempName, $a_dirContent)) {
				if (!(@unlink('./twigs/' . $s_tempName))) {
					throw new forestException(0x10001422, array('./twigs/' . $s_tempName));
				}
			}
			
			/* delete table in dbms */
			$o_queryDrop = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::DROP, $this->Twig->Name);

			/* drop table does not return a value - maybe using show_tables can be used as extra verification */
			$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryDrop, false, false);
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				$o_glob->SystemMessages->Add(new forestException(0x10001406));
			} else if ($i_result == 1) {
				/* delete twig record */
				$i_return = $this->Twig->DeleteRecord();
				
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
				
				$o_glob->SystemMessages->Add(new forestException(0x10001427));
				
				$s_nextAction = 'RELOADBRANCH';
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/**
	 * handle sub records display in detail view
	 *
	 * @param tableTwig $p_o_table  table record of table sys_fphp_table where all properties will be queried and shown within a modal form
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function ListTwigProperties(\fPHP\Twigs\tableTwig $p_o_twig) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$s_subFormItems = '';
		
		/* ************************************************** */
		/* ********************TABLEFIELDS******************* */
		/* ************************************************** */
		/* look for tablefields */
		$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
		
		$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $p_o_twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		/* ************************* */
		/* ***********HEAD********** */
		/* ************************* */
		
		$s_subTableHead = '';
		
		foreach ($o_tablefieldTwig->fphp_View as $s_columnHead) {
			if ( ($s_columnHead == 'Order') || ($s_columnHead == 'JSONEncodedSettings') || ($s_columnHead == 'FooterElement') || ($s_columnHead == 'SubRecordField') ) {
				continue;
			}
			
			if ($s_columnHead == 'FooterElement') {
				$s_columnHead = $o_glob->GetTranslation('formFooterElementOptionLabel00', 0);
			} else {
				$s_columnHead = $o_glob->GetTranslation('form' . $s_columnHead . 'Label', 0);
			}
			
			if (\fPHP\Helper\forestStringLib::EndsWith($s_columnHead, ':')) {
				$s_columnHead = substr($s_columnHead, 0, -1);
			}
			
			$s_subTableHead .= '<th>' . $s_columnHead . '</th>' . "\n";
		}
		
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('ValidationRules', 0) . '</th>' . "\n";
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
		
		/* ************************* */
		/* ***********ROWS********** */
		/* ************************* */
		
		$s_subTableRows = '';
		
		foreach ($o_tablefields->Twigs as $o_tablefield) {
			/* render records based on twig view columns */
			$s_subTableRows .=  '<tr>' . "\n";
			
			foreach ($o_tablefieldTwig->fphp_View as $s_column) {
				if ( ($s_column == 'Order') || ($s_column == 'JSONEncodedSettings') || ($s_column == 'FooterElement') || ($s_column == 'SubRecordField') ) {
					continue;
				}
				
				$s_formElement = '';
				
				if ($o_glob->TablefieldsDictionary->Exists($o_tablefieldTwig->fphp_Table . '_' . $s_column)) {
					$s_formElement = $o_glob->TablefieldsDictionary->{$o_tablefieldTwig->fphp_Table . '_' . $s_column}->FormElementName;
				}
				
				$s_value = '-';
				$this->ListViewRenderColumnValue($s_value, $s_formElement, $s_column, $o_tablefield, $o_tablefieldTwig->fphp_Table . '_' . $s_column);
				
				if ($s_column == 'JSONEncodedSettings') {
					$s_value = substr($s_value, 0, 100) . '...';
				}
				
				$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
			}
			
			$o_tablefield_validationruleTwig = new \fPHP\Twigs\tablefield_validationruleTwig;
			$a_sqlAdditionalFilter = array(array('column' => 'TablefieldUUID', 'value' => $o_tablefield->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$i_validationrules = $o_tablefield_validationruleTwig->GetCount(null, true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			$s_value = '-';
			
			if ($i_validationrules > 0) {
				$s_value = '<span class="bi bi-check-lg h5 text-success"></span>';
			} else {
				$s_value = '<span class="bi bi-x-lg text-danger"></span>';
			}
			
			$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
			
			$s_options = '<span class="btn-group">' . "\n";
			
			/* view link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['viewKey'] = $o_tablefield->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'viewTwigField')) {
				$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'viewTwigField', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnViewText', 1) . '"><span class="bi bi-search"></span></a>';
			}
			
			/* edit link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['editKey'] = $o_tablefield->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'editTwigField')) {
				$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'editTwigField', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="bi bi-pencil-square"></span></a>' . "\n";
			}
			
			/* move up link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['editKey'] = $o_tablefield->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'moveUpTwigField')) {
				$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'moveUpTwigField', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnMoveUpText', 1) . '"><span class="bi bi-caret-up-fill"></span></a>';
			}
			
			/* move down link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['editKey'] = $o_tablefield->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'moveDownTwigField')) {
				$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'moveDownTwigField', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnMoveDownText', 1) . '"><span class="bi bi-caret-down-fill"></span></a>';
			}
			
			/* delete link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['deleteKey'] = $o_tablefield->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'deleteTwigField')) {
				$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'deleteTwigField', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="bi bi-trash-fill text-danger"></span></a>' . "\n";
			}
			
			$s_options .= '</span>' . "\n";
			$s_subTableRows .=  '<td>' . $s_options . '</td>' . "\n";
			
			$s_subTableRows .=  '</tr>' . "\n";
		}
		
		/* new link */
		if ($o_glob->Security->CheckUserPermission(null, 'newTwigField')) {
			$s_newButton = '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'newTwigField') . '" class="btn btn-light" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="bi bi-plus-circle-fill text-success"></span></a>' . "\n";
		} else {
			$s_newButton = '';
		}
		
		$s_subFormItemContent = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
		$s_subFormItems .= new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEM, array('tablefields' . $p_o_twig->fphp_Table, $o_glob->GetTranslation('TableFields', 0) . ' (' . $o_tablefields->Twigs->Count() . ')', ' show', $s_subFormItemContent, $p_o_twig->Name));
		
		/* ************************************************** */
		/* *******************TRANSLATIONS******************* */
		/* ************************************************** */
		/* look for translations */
		$o_translationTwig = new \fPHP\Twigs\translationTwig;
		
		$a_sqlAdditionalFilter = array(array('column' => 'BranchId', 'value' => $o_glob->URL->BranchId, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_translations = $o_translationTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		/* ************************* */
		/* ***********HEAD********** */
		/* ************************* */
		
		$s_subTableHead = '';
		
		foreach ($o_translationTwig->fphp_View as $s_columnHead) {
			$s_columnHead = $o_glob->GetTranslation('form' . $s_columnHead . 'Label', 0);
			
			if (\fPHP\Helper\forestStringLib::EndsWith($s_columnHead, ':')) {
				$s_columnHead = substr($s_columnHead, 0, -1);
			}
			
			$s_subTableHead .= '<th>' . $s_columnHead . '</th>' . "\n";
		}
		
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
		
		/* ************************* */
		/* ***********ROWS********** */
		/* ************************* */
		
		$s_subTableRows = '';
		
		foreach ($o_translations->Twigs as $o_translation) {
			/* render records based on twig view columns */
			$s_subTableRows .=  '<tr>' . "\n";
			
			foreach ($o_translationTwig->fphp_View as $s_column) {
				$s_formElement = '';
				
				if ($o_glob->TablefieldsDictionary->Exists($o_translationTwig->fphp_Table . '_' . $s_column)) {
					$s_formElement = $o_glob->TablefieldsDictionary->{$o_translationTwig->fphp_Table . '_' . $s_column}->FormElementName;
				}
				
				$s_value = '-';
				$this->ListViewRenderColumnValue($s_value, $s_formElement, $s_column, $o_translation, $o_translationTwig->fphp_Table . '_' . $s_column);
				
				$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
			}
			
			$s_options = '<span class="btn-group">' . "\n";
			
			/* edit link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['editKey'] = $o_translation->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'editTranslation')) {
				$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'editTranslation', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="bi bi-pencil-square"></span></a>' . "\n";
			}
			
			/* delete link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['deleteKey'] = $o_translation->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'deleteTranslation')) {
				$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'deleteTranslation', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="bi bi-trash-fill text-danger"></span></a>' . "\n";
			}
			
			$s_options .= '</span>' . "\n";
			$s_subTableRows .=  '<td>' . $s_options . '</td>' . "\n";
			
			$s_subTableRows .=  '</tr>' . "\n";
		}
		
		/* new link */
		if ($o_glob->Security->CheckUserPermission(null, 'newTwigField')) {
			$s_newButton = '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'newTranslation') . '" class="btn btn-light" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="bi bi-plus-circle-fill text-success"></span></a>' . "\n";
		} else {
			$s_newButton = '';
		}
		
		$s_subFormItemContent = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
		$s_subFormItems .= new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEM, array('translations' . $p_o_twig->fphp_Table, $o_glob->GetTranslation('TranslationLines', 0) . ' (' . $o_translations->Twigs->Count() . ')', '', $s_subFormItemContent, $p_o_twig->Name));
		
		/* ************************************************** */
		/* *******************UNIQUE KEYS******************** */
		/* ************************************************** */
		/* look for unique keys in table record */
		$a_uniqueKeys = array();
		
		if (issetStr($p_o_twig->Unique->PrimaryValue)) {
			$a_uniqueKeys = explode(':', $p_o_twig->Unique->PrimaryValue);
		}
		
		/* ************************* */
		/* ***********HEAD********** */
		/* ************************* */
		
		$s_subTableHead = '<th>' . substr($o_glob->GetTranslation('formNameLabel', 0), 0, -1) . '</th>' . "\n";
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('UniqueKey', 0) . '</th>' . "\n";
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
		
		/* ************************* */
		/* ***********ROWS********** */
		/* ************************* */
		
		$s_subTableRows = '';
		$i = 0;
		
		foreach ($a_uniqueKeys as $o_uniqueKey) {
			/* render unique keys based on twig unique column */
			$s_subTableRows .=  '<tr>' . "\n";
			
			/* split name from uuid keys */
			$a_unique = explode('=', $o_uniqueKey);
			$s_name = $a_unique[0];
			
			$a_keys = array();
			$a_foo = explode(';', $a_unique[1]);
			
			foreach ($a_foo as $s_foo) {
				/* query tablefield to get FieldName for display */
				if ($o_tablefieldTwig->GetRecord(array($s_foo))) {
					$a_keys[] = $o_tablefieldTwig->FieldName;
				} else {
					$a_keys[] = 'invalid_key';
				}
			}
			
			$s_subTableRows .=  '<td><span>' . $s_name . '</span></td>' . "\n";
			$s_subTableRows .=  '<td><span>' . implode(', ', $a_keys) . '</span></td>' . "\n";
			
			$s_options = '<span class="btn-group">' . "\n";
			
			/* edit link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['editKey'] = $i;
			
			if ($o_glob->Security->CheckUserPermission(null, 'editUnique')) {
				$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'editUnique', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="bi bi-pencil-square"></span></a>' . "\n";
			}
			
			/* delete link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['deleteKey'] = $i;
			
			if ($o_glob->Security->CheckUserPermission(null, 'deleteUnique')) {
				$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'deleteUnique', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="bi bi-trash-fill text-danger"></span></a>' . "\n";
			}
			
			$s_options .= '</span>' . "\n";
			$s_subTableRows .=  '<td>' . $s_options . '</td>' . "\n";
			
			$s_subTableRows .=  '</tr>' . "\n";
			
			$i++;
		}
		
		/* new link */
		if ($o_glob->Security->CheckUserPermission(null, 'newUnique')) {
			$s_newButton = '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'newUnique') . '" class="btn btn-light" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="bi bi-plus-circle-fill text-success"></span></a>' . "\n";
		} else {
			$s_newButton = '';
		}
		
		$s_subFormItemContent = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
		$s_subFormItems .= new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEM, array('uniques' . $p_o_twig->fphp_Table, $o_glob->GetTranslation('UniqueKey', 0) . ' (' . count($a_uniqueKeys) . ')', '', $s_subFormItemContent, $p_o_twig->Name));
		
		/* ************************************************** */
		/* *******************SORT ORDER********************* */
		/* ************************************************** */
		/* look for unique keys in table record */
		$a_sortOrders = array();
		
		if (issetStr($p_o_twig->SortOrder->PrimaryValue)) {
			$a_sortOrders = explode(':', $p_o_twig->SortOrder->PrimaryValue);
		}
		
		/* ************************* */
		/* ***********HEAD********** */
		/* ************************* */
		
		$s_subTableHead = '<th>' . $o_glob->GetTranslation('SortOrder', 0) . '</th>' . "\n";
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
		
		/* ************************* */
		/* ***********ROWS********** */
		/* ************************* */
		
		$s_subTableRows = '';
		$i = 0;
		
		foreach ($a_sortOrders as $o_sortOrder) {
			/* render sort orders based on twig sort order column */
			$a_sort = explode(';', $o_sortOrder);
			
			if (count($a_sort) != 2) {
				continue;
			}
			
			$s_subTableRows .=  '<tr>' . "\n";
			
			$s_name = '';
			$s_direction = '';
			
			/* query tablefield to get FieldName for display */
			if ($o_tablefieldTwig->GetRecord(array($a_sort[1]))) {
				$s_name = $o_tablefieldTwig->FieldName;
			} else {
				$s_name = 'invalid_column';
			}
			
			if ($a_sort[0] == 'false') {
				$s_direction = ' <span class="bi bi-sort-down"></span>';
			} else if ($a_sort[0] == 'true') {
				$s_direction = ' <span class="bi bi-sort-down-alt"></span>';
			}
			
			$s_subTableRows .=  '<td><span>' . $s_name . $s_direction . '</span></td>' . "\n";
			
			$s_options = '<span class="btn-group">' . "\n";
			
			/* edit link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['editKey'] = $i;
			
			if ($o_glob->Security->CheckUserPermission(null, 'editSort')) {
				$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'editSort', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="bi bi-pencil-square"></span></a>' . "\n";
			}
			
			/* delete link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['deleteKey'] = $i;
			
			if ($o_glob->Security->CheckUserPermission(null, 'deleteSort')) {
				$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'deleteSort', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="bi bi-trash-fill text-danger"></span></a>' . "\n";
			}
			
			$s_options .= '</span>' . "\n";
			$s_subTableRows .=  '<td>' . $s_options . '</td>' . "\n";
			
			$s_subTableRows .=  '</tr>' . "\n";
			
			$i++;
		}
		
		/* new link */
		if ($o_glob->Security->CheckUserPermission(null, 'newSort')) {
			$s_newButton = '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'newSort') . '" class="btn btn-light" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="bi bi-plus-circle-fill text-success"></span></a>' . "\n";
		} else {
			$s_newButton = '';
		}
		
		$s_subFormItemContent = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
		$s_subFormItems .= new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEM, array('sortOrders' . $p_o_twig->fphp_Table, $o_glob->GetTranslation('SortOrder', 0) . ' (' . count($a_sortOrders) . ')', '', $s_subFormItemContent, $p_o_twig->Name));
		
		/* ************************************************** */
		/* ******************SUB CONSTRAINTS***************** */
		/* ************************************************** */
		/* look for sub constraints */
		$o_subconstraintTwig = new \fPHP\Twigs\subconstraintTwig;
		
		$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $p_o_twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_subconstraints = $o_subconstraintTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		/* ************************* */
		/* ***********HEAD********** */
		/* ************************* */
		
		$s_subTableHead = '<th>' . $o_glob->GetTranslation('Table', 0) . '</th>' . "\n";
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('View', 1) . '</th>' . "\n";
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortIdentifierStart', 0) . '</th>' . "\n";
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('sortIdentifierIncrement', 0) . '</th>' . "\n";
		$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
		
		/* ************************* */
		/* ***********ROWS********** */
		/* ************************* */
		
		$s_subTableRows = '';
		
		foreach ($o_subconstraints->Twigs as $o_subconstraint) {
			/* render records based on twig view columns */
			$s_subTableRows .=  '<tr>' . "\n";
			
			$s_table = $o_glob->TablesInformation[$o_subconstraint->SubTableUUID->PrimaryValue]['Name'];
			\fPHP\Helper\forestStringLib::RemoveTablePrefix($s_table);
			$s_table = $o_glob->GetTranslation($o_glob->BranchTree['Id'][$o_glob->BranchTree['Name'][$s_table]]['Title'], 1);
			
			$a_view = array();
			
			if (issetStr($o_subconstraint->View->PrimaryValue)) {
				$a_foo = explode(';', $o_subconstraint->View->PrimaryValue);
				
				foreach ($a_foo as $s_foo) {
					/* query tablefield to get FieldName for display */
					if ($o_tablefieldTwig->GetRecord(array($s_foo))) {
						$a_view[] = $o_tablefieldTwig->FieldName;
					} else {
						$a_view[] = 'invalid_tablefield';
					}
				}
			}
			
			$s_subTableRows .=  '<td><span>' . $s_table . ' (' . $o_glob->TablesInformation[$o_subconstraint->SubTableUUID->PrimaryValue]['Name'] . ')' . '</span></td>' . "\n";
			$s_subTableRows .=  '<td><span>' . implode(', ', $a_view) . '</span></td>' . "\n";
			
			if (issetStr($o_subconstraint->IdentifierStart)) {
				$s_subTableRows .=  '<td><span>' . $o_subconstraint->IdentifierStart . '</span></td>' . "\n";
			} else {
				$s_subTableRows .=  '<td><span>-</span></td>' . "\n";
			}
			
			if ($o_subconstraint->IdentifierIncrement > 0) {
				$s_subTableRows .=  '<td><span>' . $o_subconstraint->IdentifierIncrement . '</span></td>' . "\n";
			} else {
				$s_subTableRows .=  '<td><span>-</span></td>' . "\n";
			}
			
			$s_options = '<span class="btn-group">' . "\n";
			
			/* edit link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['editKey'] = $o_subconstraint->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'editSubConstraint')) {
				$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'editSubConstraint', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="bi bi-pencil-square"></span></a>' . "\n";
			}
			
			/* move up link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['editKey'] = $o_subconstraint->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'moveUpSubConstraint')) {
				$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'moveUpSubConstraint', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnMoveUpText', 1) . '"><span class="bi bi-caret-up-fill"></span></a>';
			}

			/* move down link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['editKey'] = $o_subconstraint->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'moveDownSubConstraint')) {
				$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'moveDownSubConstraint', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnMoveDownText', 1) . '"><span class="bi bi-caret-down-fill"></span></a>';
			}

			/* delete link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['deleteKey'] = $o_subconstraint->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'deleteSubConstraint')) {
				$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'deleteSubConstraint', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="bi bi-trash-fill text-danger"></span></a>' . "\n";
			}

			$s_options .= '</span>' . "\n";
			$s_subTableRows .=  '<td>' . $s_options . '</td>' . "\n";
			
			$s_subTableRows .=  '</tr>' . "\n";
		}
		
		/* new link */
		if ($o_glob->Security->CheckUserPermission(null, 'newSubConstraint')) {
			$s_newButton = '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'newSubConstraint') . '" class="btn btn-light" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="bi bi-plus-circle-fill text-success"></span></a>' . "\n";
		} else {
			$s_newButton = '';
		}

		$s_subFormItemContent = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
		$s_subFormItems .= new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEM, array('subConstraints' . $p_o_twig->fphp_Table, $o_glob->GetTranslation('SubConstraints', 0) . ' (' . $o_subconstraints->Twigs->Count() . ')', '', $s_subFormItemContent, $p_o_twig->Name));
		
		/* ************************************************** */
		/* ***********SUB CONSTRAINTS TABLEFIELDS************ */
		/* ************************************************** */
		foreach ($o_subconstraints->Twigs as $o_subconstraint) {
			/* look for tablefields */
			$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
			
			$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $o_subconstraint->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_subTablefields = $o_tablefieldTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			/* ************************* */
			/* ***********HEAD********** */
			/* ************************* */
			
			$s_subTableHead = '';
			
			foreach ($o_tablefieldTwig->fphp_View as $s_columnHead) {
				if ( ($s_columnHead == 'Order') || ($s_columnHead == 'JSONEncodedSettings') || ($s_columnHead == 'FooterElement') ) {
					continue;
				}
				
				if ($s_columnHead == 'FooterElement') {
					$s_columnHead = $o_glob->GetTranslation('formFooterElementOptionLabel00', 0);
				} else {
					$s_columnHead = $o_glob->GetTranslation('form' . $s_columnHead . 'Label', 0);
				}
				
				if (\fPHP\Helper\forestStringLib::EndsWith($s_columnHead, ':')) {
					$s_columnHead = substr($s_columnHead, 0, -1);
				}
				
				$s_subTableHead .= '<th>' . $s_columnHead . '</th>' . "\n";
			}
			
			$s_subTableHead .= '<th>' . $o_glob->GetTranslation('ValidationRules', 0) . '</th>' . "\n";
			$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
			
			/* ************************* */
			/* ***********ROWS********** */
			/* ************************* */
			
			$s_subTableRows = '';
			
			foreach ($o_subTablefields->Twigs as $o_subTablefield) {
				/* render records based on twig view columns */
				$s_subTableRows .=  '<tr>' . "\n";
				
				foreach ($o_tablefieldTwig->fphp_View as $s_column) {
					if ( ($s_column == 'Order') || ($s_column == 'JSONEncodedSettings') || ($s_column == 'FooterElement') ) {
						continue;
					}
					
					$s_formElement = '';
					
					if ($o_glob->TablefieldsDictionary->Exists($o_tablefieldTwig->fphp_Table . '_' . $s_column)) {
						$s_formElement = $o_glob->TablefieldsDictionary->{$o_tablefieldTwig->fphp_Table . '_' . $s_column}->FormElementName;
					}
					
					$s_value = '-';
					$this->ListViewRenderColumnValue($s_value, $s_formElement, $s_column, $o_subTablefield, $o_tablefieldTwig->fphp_Table . '_' . $s_column);
					
					if ($s_column == 'JSONEncodedSettings') {
						$s_value = substr($s_value, 0, 100) . '...';
					}
					
					$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
				}
				
				$o_tablefield_validationruleTwig = new \fPHP\Twigs\tablefield_validationruleTwig;
				$a_sqlAdditionalFilter = array(array('column' => 'TablefieldUUID', 'value' => $o_subTablefield->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$i_validationrules = $o_tablefield_validationruleTwig->GetCount(null, true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				$s_value = '-';
				
				if ($i_validationrules > 0) {
					$s_value = '<span class="bi bi-check-lg h5 text-success"></span>';
				} else {
					$s_value = '<span class="bi bi-x-lg text-danger"></span>';
				}
				
				$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
				
				$s_options = '<span class="btn-group">' . "\n";
				
				/* view link */
				$a_parameters = $o_glob->URL->Parameters;
				unset($a_parameters['newKey']);
				unset($a_parameters['viewKey']);
				unset($a_parameters['editKey']);
				unset($a_parameters['deleteKey']);
				unset($a_parameters['editSubKey']);
				unset($a_parameters['deleteSubKey']);
				unset($a_parameters['deleteFileKey']);
				unset($a_parameters['subConstraintKey']);
				unset($a_parameters['rootSubConstraintKey']);
				$a_parameters['viewKey'] = $o_subTablefield->UUID;
				$a_parameters['rootSubConstraintKey'] = $o_subconstraint->UUID;
				
				if ($o_glob->Security->CheckUserPermission(null, 'viewTwigField')) {
					$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'viewTwigField', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnViewText', 1) . '"><span class="bi bi-search"></span></a>';
				}

				/* edit link */
				$a_parameters = $o_glob->URL->Parameters;
				unset($a_parameters['newKey']);
				unset($a_parameters['viewKey']);
				unset($a_parameters['editKey']);
				unset($a_parameters['deleteKey']);
				unset($a_parameters['editSubKey']);
				unset($a_parameters['deleteSubKey']);
				unset($a_parameters['deleteFileKey']);
				unset($a_parameters['subConstraintKey']);
				unset($a_parameters['rootSubConstraintKey']);
				$a_parameters['editKey'] = $o_subTablefield->UUID;
				$a_parameters['rootSubConstraintKey'] = $o_subconstraint->UUID;
				
				if ($o_glob->Security->CheckUserPermission(null, 'editTwigField')) {
					$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'editTwigField', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="bi bi-pencil-square"></span></a>' . "\n";
				}

				/* move up link */
				$a_parameters = $o_glob->URL->Parameters;
				unset($a_parameters['newKey']);
				unset($a_parameters['viewKey']);
				unset($a_parameters['editKey']);
				unset($a_parameters['deleteKey']);
				unset($a_parameters['editSubKey']);
				unset($a_parameters['deleteSubKey']);
				unset($a_parameters['deleteFileKey']);
				unset($a_parameters['subConstraintKey']);
				unset($a_parameters['rootSubConstraintKey']);
				$a_parameters['editKey'] = $o_subTablefield->UUID;
				$a_parameters['rootSubConstraintKey'] = $o_subconstraint->UUID;
				
				if ($o_glob->Security->CheckUserPermission(null, 'moveUpTwigField')) {
					$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'moveUpTwigField', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnMoveUpText', 1) . '"><span class="bi bi-caret-up-fill"></span></a>';
				}

				/* move down link */
				$a_parameters = $o_glob->URL->Parameters;
				unset($a_parameters['newKey']);
				unset($a_parameters['viewKey']);
				unset($a_parameters['editKey']);
				unset($a_parameters['deleteKey']);
				unset($a_parameters['editSubKey']);
				unset($a_parameters['deleteSubKey']);
				unset($a_parameters['deleteFileKey']);
				unset($a_parameters['subConstraintKey']);
				unset($a_parameters['rootSubConstraintKey']);
				$a_parameters['editKey'] = $o_subTablefield->UUID;
				$a_parameters['rootSubConstraintKey'] = $o_subconstraint->UUID;
				
				if ($o_glob->Security->CheckUserPermission(null, 'moveDownTwigField')) {
					$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'moveDownTwigField', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnMoveDownText', 1) . '"><span class="bi bi-caret-down-fill"></span></a>';
				}

				/* delete link */
				$a_parameters = $o_glob->URL->Parameters;
				unset($a_parameters['newKey']);
				unset($a_parameters['viewKey']);
				unset($a_parameters['editKey']);
				unset($a_parameters['deleteKey']);
				unset($a_parameters['editSubKey']);
				unset($a_parameters['deleteSubKey']);
				unset($a_parameters['deleteFileKey']);
				unset($a_parameters['subConstraintKey']);
				unset($a_parameters['rootSubConstraintKey']);
				$a_parameters['deleteKey'] = $o_subTablefield->UUID;
				$a_parameters['rootSubConstraintKey'] = $o_subconstraint->UUID;
				
				if ($o_glob->Security->CheckUserPermission(null, 'deleteTwigField')) {
					$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'deleteTwigField', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="bi bi-trash-fill text-danger"></span></a>' . "\n";
				}

				$s_options .= '</span>' . "\n";
				$s_subTableRows .=  '<td>' . $s_options . '</td>' . "\n";
				
				$s_subTableRows .=  '</tr>' . "\n";
			}
			
			/* new link */
			if ($o_glob->Security->CheckUserPermission(null, 'newTwigField')) {
				$s_newButton = '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'newTwigField', array('rootSubConstraintKey' => $o_subconstraint->UUID)) . '" class="btn btn-light" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="bi bi-plus-circle-fill text-success"></span></a>' . "\n";
			} else {
				$s_newButton = '';
			}

			$s_tempTable = $o_glob->TablesInformation[$o_subconstraint->SubTableUUID->PrimaryValue]['Name'];
			\fPHP\Helper\forestStringLib::RemoveTablePrefix($s_tempTable);
			
			$s_subFormItemContent = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
			$s_subFormItems .= new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEM, array('sub' . $s_tempTable, $o_glob->GetTranslation($o_glob->BranchTree['Id'][$o_glob->BranchTree['Name'][$s_tempTable]]['Title'], 1) . ' ' . $o_glob->GetTranslation('SubConstraint', 0) . ' (' . $o_subTablefields->Twigs->Count() . ')', '', $s_subFormItemContent, $p_o_twig->Name));
		}
		
		/* use template to render tablefields, translations, unique keys, sort orders, sub constraints and sub constraint tablefields of a record */
		return new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEW, array($p_o_twig->Name, $s_subFormItems));
	}
	
	/**
	 * function to update twig file of current branch with current tablefields and twig settings
	 *
	 * @param tableTwig $p_o_table  table record of table sys_fphp_table to update the twig file in ./twigs/
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function doTwigFile(\fPHP\Twigs\tableTwig $p_o_tableTwig) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* gather information */
		$s_tableName = '';
		$s_fieldDefinitions = '';
		$s_fields = '';
		$s_fullTableName = $p_o_tableTwig->Name;
		$s_primary = '';
		$s_uniques = '';
		$s_sorts = '';
		$s_interval = '';
		$s_view = '';
		$s_view_reserve = '';
		
		/* standard Id + UUID */
		$s_primary .= '\'Id\'';
		$s_uniques .= '\'UUID\',';
		$s_sorts = '$this->fphp_SortOrder->value->Add(true, \'Id\');' . "\n\t\t";
		$s_fieldDefinitions .= 'private $Id;' . "\n\t" . 'private $UUID;' . "\n\t";
		$s_fields .= '$this->Id = new forestNumericString(1);' . "\n\t\t" . '$this->UUID = new forestString;' . "\n\t\t";
		
		if (issetStr($p_o_tableTwig->Identifier->PrimaryValue)) {
			$s_fieldDefinitions .= 'private $Identifier;' . "\n\t";
			$s_fields .= '$this->Identifier = new forestString;' . "\n\t\t";
			$s_uniques .= '\'Identifier\',';
		}
		
		/* handle info columns */
		if ($p_o_tableTwig->InfoColumns == 10) {
			/* create created, createdby columns */
			$s_fieldDefinitions .= 'private $Created;' . "\n\t" . 'private $CreatedBy;' . "\n\t";
			$s_fields .= '$this->Created = new forestObject(\'forestDateTime\');' . "\n\t\t" . '$this->CreatedBy = new forestString;' . "\n\t\t";
		} else if ($p_o_tableTwig->InfoColumns == 100) {
			/* create modified, modifiedby columns */
			$s_fieldDefinitions .= 'private $Modified;' . "\n\t" . 'private $ModifiedBy;' . "\n\t";
			$s_fields .= '$this->Modified = new forestObject(\'forestDateTime\');' . "\n\t\t" . '$this->ModifiedBy = new forestString;' . "\n\t\t";
		} else if ($p_o_tableTwig->InfoColumns == 1000) {
			/* create created, createdby, modified, modifiedby columns */
			$s_fieldDefinitions .= 'private $Created;' . "\n\t" . 'private $CreatedBy;' . "\n\t";
			$s_fields .= '$this->Created = new forestObject(\'forestDateTime\');' . "\n\t\t" . '$this->CreatedBy = new forestString;' . "\n\t\t";
			$s_fieldDefinitions .= 'private $Modified;' . "\n\t" . 'private $ModifiedBy;' . "\n\t";
			$s_fields .= '$this->Modified = new forestObject(\'forestDateTime\');' . "\n\t\t" . '$this->ModifiedBy = new forestString;' . "\n\t\t";
		}
		
		/* look for tablefields */
		$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
		
		$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $p_o_tableTwig->UUID, 'operator' => '=', 'filterOperator' => 'AND'), array('column' => 'SqlTypeUUID', 'value' => 'NULL', 'operator' => 'IS NOT', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		foreach ($o_tablefields->Twigs as $o_tablefield) {
			/* ignore forestCombination, dropzone and form field */
			if ((strval($o_tablefield->ForestDataUUID) == 'forestCombination') || (strval($o_tablefield->FormElementUUID) == \fPHP\Forms\forestFormElement::DROPZONE) || (strval($o_tablefield->FormElementUUID) == \fPHP\Forms\forestFormElement::FORM)) {
				continue;
			}
			
			$s_fieldDefinitions .= 'private $' . $o_tablefield->FieldName . ';' . "\n\t";
			
			if ((strval($o_tablefield->ForestDataUUID) == 'forestLookup')) {
				$s_fields .= '$this->' . $o_tablefield->FieldName . ' = new ';
				
				/* get json encoded settings as array */
				$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_tablefield->JSONEncodedSettings);
				$a_settings = json_decode($s_JSONEncodedSettings, true);
				
				$s_lookupTable = '\'sys_fphp_trunk\'';
				$s_lookupPrimary = '\'Id\'';
				$s_lookupLabel = '\'Id\'';
				$s_lookupFilter = 'array()';
				$s_lookupConcat = '\' - \'';
				
				/* check if json encoded settings are valid */
				if ($a_settings != null) {
					if ( (array_key_exists('forestLookupDataTable', $a_settings)) && (array_key_exists('forestLookupDataPrimary', $a_settings)) && (array_key_exists('forestLookupDataLabel', $a_settings)) ) {
						$s_lookupTable = '\'' . $a_settings['forestLookupDataTable'] . '\'';
						$s_lookupPrimary = '\'' . implode('\',\'', $a_settings['forestLookupDataPrimary']) . '\'';
						$s_lookupLabel = '\'' . implode('\',\'', $a_settings['forestLookupDataLabel']) . '\'';
						
						if (array_key_exists('forestLookupDataFilter', $a_settings)) {
							$a_filters = array();
							
							foreach ($a_settings['forestLookupDataFilter'] as $s_filterKey => $s_filterValue) {
								$a_filters[] = '\'' . $s_filterKey . '\' => \'' . $s_filterValue . '\'';
							}
							
							$s_lookupFilter = 'array(' . implode(',', $a_filters) . ')';
						}
						
						if (array_key_exists('forestLookupDataConcat', $a_settings)) {
							$s_lookupConcat = '\'' . $a_settings['forestLookupDataConcat'] . '\'';
						}
					}
				}
				
				$s_fields .= 'forestLookup(new forestLookupData(' . $s_lookupTable . ', array(' . $s_lookupPrimary . '), array(' . $s_lookupLabel . '), ' . $s_lookupFilter . ', ' . $s_lookupConcat . '))';
				$s_fields .= ';' . "\n\t\t";
			} else if ((strval($o_tablefield->ForestDataUUID) == 'forestList')) {
				$s_fields .= '$this->' . $o_tablefield->FieldName . ' = new ';
				
				/* get json encoded settings as array */
				$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_tablefield->JSONEncodedSettings);
				$a_settings = json_decode($s_JSONEncodedSettings, true);
				
				$s_options = 'array()';
				
				/* check if json encoded settings are valid */
				if ($a_settings != null) {
					if (array_key_exists('Options', $a_settings)) {
						if (array_key_exists('Value', $a_settings)) {
							$s_options = 'array(\'' . implode('\',\'', $a_settings['Options']) . '\'), \'' . $a_settings['Value'] . '\'';
						} else {
							$s_options = 'array(\'' . implode('\',\'', $a_settings['Options']) . '\')';
						}
					}
				}
				
				$s_fields .= 'forestList(' . $s_options . ')';
				$s_fields .= ';' . "\n\t\t";
			} else {
				$s_fields .= '$this->' . $o_tablefield->FieldName . ' = new ' . htmlspecialchars_decode(strval($o_tablefield->ForestDataUUID), ( ENT_QUOTES | ENT_HTML5 )) . ';' . "\n\t\t";
			}
			
			$s_view_reserve .= '\'' . $o_tablefield->FieldName . '\',';
		}
		
		/* unique keys */
		$a_uniqueKeys = array();
		
		if (issetStr($p_o_tableTwig->Unique->PrimaryValue)) {
			$a_uniqueKeys = explode(':', $p_o_tableTwig->Unique->PrimaryValue);
		}
		
		foreach ($a_uniqueKeys as $o_uniqueKey) {
			/* split name from uuid keys */
			$a_unique = explode('=', $o_uniqueKey);
			$s_name = $a_unique[0];
			
			$a_keys = array();
			$a_foo = explode(';', $a_unique[1]);
			
			foreach ($a_foo as $s_foo) {
				/* query tablefield to get FieldName for display */
				if ($o_tablefieldTwig->GetRecord(array($s_foo))) {
					$a_keys[] = $o_tablefieldTwig->FieldName;
				} else {
					$a_keys[] = 'invalid_key';
				}
			}
			
			$s_uniques .= '\'' .  implode(';', $a_keys) . '\'' . ',';
		}
		
		$s_uniques = substr($s_uniques, 0, -1);
		
		/* sort orders */
		$a_sortOrders = array();
		
		if (issetStr($p_o_tableTwig->SortOrder->PrimaryValue)) {
			$s_sorts = '';
			$a_sortOrders = explode(':', $p_o_tableTwig->SortOrder->PrimaryValue);
		}
		
		foreach ($a_sortOrders as $o_sortOrder) {
			/* render sort orders based on twig sort order column */
			$a_sort = explode(';', $o_sortOrder);
			
			if (count($a_sort) != 2) {
				continue;
			}
			
			$s_name = '';
			$s_direction = '';
			
			/* query tablefield to get FieldName for display */
			if ($o_tablefieldTwig->GetRecord(array($a_sort[1]))) {
				$s_name = $o_tablefieldTwig->FieldName;
			} else {
				$s_name = 'invalid_column';
			}
			
			if ($a_sort[0] == 'false') {
				$s_direction = 'false';
			} else if ($a_sort[0] == 'true') {
				$s_direction = 'true';
			}
			
			$s_sorts .= '$this->fphp_SortOrder->value->Add(' . $s_direction . ', \'' . $s_name . '\');' . "\n\t\t";
		}
		
		/* interval */
		if ($p_o_tableTwig->Interval != 0) {
			$s_interval = strval($p_o_tableTwig->Interval);
		} else {
			$s_interval = strval(50);
		}
		
		/* view */
		if (issetStr($p_o_tableTwig->View->PrimaryValue)) {
			$a_keys = array();
			$a_foo = explode(';', $p_o_tableTwig->View->PrimaryValue);
			
			foreach ($a_foo as $s_foo) {
				/* query tablefield to get FieldName for display */
				if ($o_tablefieldTwig->GetRecord(array($s_foo))) {
					$a_keys[] = $o_tablefieldTwig->FieldName;
				} else {
					$a_keys[] = 'invalid_key';
				}
			}
			
			$s_view .=  '\'' . implode('\',\'', $a_keys) . '\'';
		} else {
			$s_view .= substr($s_view_reserve, 0, -1);
		}
		
		if ($p_o_tableTwig->InfoColumnsView > 0) {
			$a_keys = array();
			$i_temp = $p_o_tableTwig->InfoColumnsView;
			
			if ($i_temp >= 1000) {
				/* add modified by field */
				$a_keys[] = 'ModifiedBy';
				$i_temp -= 1000;
			}
			
			if ($i_temp >= 100) {
				/* add modified field */
				$a_keys[] = 'Modified';
				$i_temp -= 100;
			}
			
			if ($i_temp >= 10) {
				/* add created by field */
				$a_keys[] = 'CreatedBy';
				$i_temp -= 10;
			}
			
			if ($i_temp == 1) {
				/* add created field */
				$a_keys[] = 'Created';
			}
			
			/* revert sort within array */
			krsort($a_keys);
			
			if ($s_view != '') {
				$s_view .=  ',\'' . implode('\',\'', $a_keys) . '\'';
			} else {
				$s_view .=  '\'' . implode('\',\'', $a_keys) . '\'';
			}
		}
		
		/* get twigs directory content */
		$a_dirContent = scandir('./twigs/');
		$s_tempName = $p_o_tableTwig->Name;
		\fPHP\Helper\forestStringLib::RemoveTablePrefix($s_tempName);
		$s_tableName = $s_tempName;
		$s_tempName .= 'Twig.php';
		
		/* if we can find twig file, delete it */
		if (in_array($s_tempName, $a_dirContent)) {
			if (!(@unlink('./twigs/' . $s_tempName))) {
				throw new forestException(0x10001422, array('./twigs/' . $s_tempName));
			}
		}
		
		/* create new twig file */
		$o_file = new \fPHP\Helper\forestFile('./twigs/' . $s_tempName, true);
		$o_file->ReplaceContent( strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::CREATENEWTWIG, array($s_tableName, $s_fieldDefinitions, $s_fields, $s_fullTableName, $s_primary, $s_uniques, $s_sorts, $s_interval, $s_view))) );
	}
	
	
	/**
	 * handle new twig tablefield record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function newTwigFieldAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if (($this->Twig != null) && ($this->Twig->fphp_SystemTable)) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$s_nextActionAfterReload = null;
		$a_nextParameters = null;
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\tablefieldTwig;
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'rootSubConstraintKey'), 'rootSubConstraintKey' );
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			/* add new branch record form */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
			
			/* delete Order-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_tablefield_Order')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_tablefield_Order].');
			}
			
			if (!( ($o_glob->Temp->Exists('rootSubConstraintKey')) && ($o_glob->Temp->{'rootSubConstraintKey'} != null) )) {
				/* delete SubRecordField-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_tablefield_SubRecordField')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_tablefield_SubRecordField].');
				}
			} else {
				/* query sub constraint record */
				$o_subconstraintTwig = new \fPHP\Twigs\subconstraintTwig;
				
				if (! ($o_subconstraintTwig->GetRecord(array($o_glob->Temp->{'rootSubConstraintKey'}))) ) {
					throw new forestException(0x10001401, array($o_subconstraintTwig->fphp_Table));
				}
				
				/* add current record key to modal form as hidden field */
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_subconstraintKey';
				$o_hidden->Value = strval($o_glob->Temp->{'rootSubConstraintKey'});
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
			}
		} else {
			$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
			
			/* check posted data for new tablefield record */
			$this->TransferPOST_Twig();
			
			if (in_array($this->Twig->FieldName, $this->ForbiddenTablefieldNames)) {
				throw new forestException(0x10001F10, array(implode(',', $this->ForbiddenTablefieldNames)));
			}
			
			if (issetStr($this->Twig->FormElementUUID->PrimaryValue)) {
				if (issetStr($this->Twig->SqlTypeUUID->PrimaryValue)) {
					$o_formelement_sqltypeTwig = new \fPHP\Twigs\formelement_sqltypeTwig;
					
					if (! $o_formelement_sqltypeTwig->GetRecord(array($this->Twig->FormElementUUID->PrimaryValue, $this->Twig->SqlTypeUUID->PrimaryValue))) {
						throw new forestException(0x10001F0D, array($this->Twig->FormElementUUID, $this->Twig->SqlTypeUUID));
					}
				}
				
				if (issetStr($this->Twig->ForestDataUUID->PrimaryValue)) {
					$o_formelement_forestdataTwig = new \fPHP\Twigs\formelement_forestdataTwig;
					
					if (! $o_formelement_forestdataTwig->GetRecord(array($this->Twig->FormElementUUID->PrimaryValue, $this->Twig->ForestDataUUID->PrimaryValue))) {
						throw new forestException(0x10001F0E, array($this->Twig->FormElementUUID, $this->Twig->ForestDataUUID));
					}
				}
			}
			
			$s_uuid = $o_tableTwig->UUID;
			
			if (array_key_exists('sys_fphp_subconstraintKey', $_POST)) {
				/* query sub constraint record */
				$o_subconstraintTwig = new \fPHP\Twigs\subconstraintTwig;
				
				if (! ($o_subconstraintTwig->GetRecord(array($_POST['sys_fphp_subconstraintKey']))) ) {
					throw new forestException(0x10001401, array($o_subconstraintTwig->fphp_Table));
				}
				
				$s_uuid = $o_subconstraintTwig->UUID;
			}
			
			$i_order = 1;
			$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
			
			$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $s_uuid, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			if ($o_tablefieldTwig->GetLastRecord()) {
				$i_order = $o_tablefieldTwig->Order + 1;
			}
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			/* add Order value to record */
			$this->Twig->Order = $i_order;
			
			/* check if json encoded settings are valid */
			$a_settings = json_decode($_POST['sys_fphp_tablefield_JSONEncodedSettings'], true);
		
			if ($a_settings == null) {
				throw new forestException(0x10001F02, array($_POST['sys_fphp_tablefield_JSONEncodedSettings']));
			}
			
			/* memory validation rule from json settings */
			$a_validationRule['rule'] = null;
			$a_validationRule['ruleParam01'] = null;
			$a_validationRule['ruleParam02'] = null;
			$a_validationRule['ruleAutoRequired'] = null;
			
			/* if no json setting for Id is available, add it automatically */
			if ( (!array_key_exists('Id', $a_settings)) || (array_key_exists('ValidationRule', $a_settings)) ) {
				if (!array_key_exists('Id', $a_settings)) {
					$s_table = $o_tableTwig->Name;
					
					if (array_key_exists('sys_fphp_subconstraintKey', $_POST)) {
						$s_table = $o_subconstraintTwig->fphp_Table;
					}
					
					$a_settings['Id'] = $s_table . '_' . $this->Twig->FieldName;
				}
				
				if (array_key_exists('ValidationRule', $a_settings)) {
					$a_validationRule['rule'] = $a_settings['ValidationRule'];
					unset($a_settings['ValidationRule']);
				}
				
				if (array_key_exists('ValidationRuleParam01', $a_settings)) {
					$a_validationRule['ruleParam01'] = $a_settings['ValidationRuleParam01'];
					unset($a_settings['ValidationRuleParam01']);
				}
				
				if (array_key_exists('ValidationRuleParam02', $a_settings)) {
					$a_validationRule['ruleParam02'] = $a_settings['ValidationRuleParam02'];
					unset($a_settings['ValidationRuleParam02']);
				}
				
				if (array_key_exists('ValidationRuleAutoRequired', $a_settings)) {
					$a_validationRule['ruleAutoRequired'] = $a_settings['ValidationRuleAutoRequired'];
					unset($a_settings['ValidationRuleAutoRequired']);
				}
				
				$_POST['sys_fphp_tablefield_JSONEncodedSettings'] = json_encode($a_settings, JSON_UNESCAPED_SLASHES );
				$this->Twig->JSONEncodedSettings = strval($_POST['sys_fphp_tablefield_JSONEncodedSettings']);
			}
			
			/* recognize translation name and value pair within json encoded settings and create tranlsation records automatically */
			preg_match_all('/\#([^#]+)\#/', $_POST['sys_fphp_tablefield_JSONEncodedSettings'], $a_matches);
	
			if (count($a_matches) > 1) {
				foreach ($a_matches[1] as $s_match) {
					/* we want to create a new translation record */
					if (strpos($s_match, '=') !== false) {
						$a_match = explode('=', $s_match);
						$s_name = $a_match[0];
						$s_value = $a_match[1];
						/* keep translation name in json encoded settings */
						$_POST['sys_fphp_tablefield_JSONEncodedSettings'] = str_replace('#' . $s_match . '#', '#' . $s_name . '#', $_POST['sys_fphp_tablefield_JSONEncodedSettings']);
						$this->Twig->JSONEncodedSettings = strval($_POST['sys_fphp_tablefield_JSONEncodedSettings']);

						/* prepare translation record */
						$o_translationTwig = new \fPHP\Twigs\translationTwig;
						$o_translationTwig->BranchId = $o_glob->URL->BranchId;
						$o_translationTwig->LanguageCode = $o_glob->Trunk->LanguageCode->PrimaryValue;
						$o_translationTwig->Name = $s_name;
						$o_translationTwig->Value = \fPHP\Helper\forestStringLib::ReplaceUnicodeEscapeSequence($s_value);
						
						/* insert translation record */
						$i_result = $o_translationTwig->InsertRecord();

						/* evaluate result */
						if ($i_result == -1) {
							throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
						} else if ($i_result == 0) {
							throw new forestException(0x10001402);
						}
					}
				}
			}
			
			$s_tableUUID = $o_tableTwig->UUID;
			
			if (array_key_exists('sys_fphp_subconstraintKey', $_POST)) {
				$s_tableUUID = $o_subconstraintTwig->UUID;
			}
			
			/* add TableUUID value to record */
			$this->Twig->TableUUID = $s_tableUUID;
			
			/* insert record */
			$i_result = $this->Twig->InsertRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				throw new forestException(0x10001402);
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001404));
				
				/* remember tablefied uuid */
				$this->HandleProcessingActionChain();

				/* add validation rule from input json settings if not null */
				if ($a_validationRule['rule'] != null) {
					$o_tablefieldValidationRuleTwig = new \fPHP\Twigs\tablefield_validationruleTwig;
					$o_tablefieldValidationRuleTwig->TablefieldUUID = $this->Twig->UUID;
					
					/* find validation rule by name */
					$o_getValidationruleTwig = new \fPHP\Twigs\validationruleTwig;
					
					if ($o_getValidationruleTwig->GetRecordPrimary(array($a_validationRule['rule']), array('Name'))) {
						$o_tablefieldValidationRuleTwig->ValidationruleUUID = $o_getValidationruleTwig->UUID;
					
						/* check if validation rule is valid for new tablefield */
						$o_validationruleTwig = new \fPHP\Twigs\validationruleTwig;
						
						$o_formelement_validationruleTwig = new \fPHP\Twigs\formelement_validationruleTwig;
						
						if (! ($o_validationruleTwig->GetRecordPrimary(array('any'), array('Name'))) ) {
							throw new forestException(0x10001401, array($o_validationruleTwig->fphp_Table));
						}
						
						if (! $o_formelement_validationruleTwig->GetRecord(array($this->Twig->FormElementUUID->PrimaryValue, $o_validationruleTwig->UUID))) {
							if (! $o_formelement_validationruleTwig->GetRecord(array($this->Twig->FormElementUUID->PrimaryValue, $o_tablefieldValidationRuleTwig->ValidationruleUUID->PrimaryValue))) {
								throw new forestException(0x10001F0F, array($this->Twig->FormElementUUID, $o_tablefieldValidationRuleTwig->ValidationruleUUID));
							}
						}
						
						/* add validation rule parameters if not null */
						if ( ($a_validationRule['ruleParam01'] == null) && ($a_validationRule['ruleParam02'] == null) ) {
							/* auto set param 01 to 'true' if you enter one of these validation rules */
							if (
								($a_validationRule['rule'] == 'required') || 
								($a_validationRule['rule'] == 'email') || 
								($a_validationRule['rule'] == 'url') || 
								($a_validationRule['rule'] == 'digits') || 
								($a_validationRule['rule'] == 'number') || 
								($a_validationRule['rule'] == 'fphp_month') || 
								($a_validationRule['rule'] == 'fphp_week') || 
								($a_validationRule['rule'] == 'fphp_dateISO') || 
								($a_validationRule['rule'] == 'fphp_time') || 
								($a_validationRule['rule'] == 'dateISO') || 
								($a_validationRule['rule'] == 'fphp_dateDMYpoint') || 
								($a_validationRule['rule'] == 'fphp_dateDMYslash') || 
								($a_validationRule['rule'] == 'fphp_dateMDYslash') || 
								($a_validationRule['rule'] == 'fphp_datetime') || 
								($a_validationRule['rule'] == 'fphp_datetimeISO') || 
								($a_validationRule['rule'] == 'fphp_dateinterval') || 
								($a_validationRule['rule'] == 'fphp_password') || 
								($a_validationRule['rule'] == 'fphp_username') || 
								($a_validationRule['rule'] == 'fphp_onlyletters')
							) {
								$o_tablefieldValidationRuleTwig->ValidationRuleParam01 = 'true';
							}
						}
						
						if ($a_validationRule['ruleParam01'] != null) {
							$o_tablefieldValidationRuleTwig->ValidationRuleParam01 = $a_validationRule['ruleParam01'];
						}
						
						if ($a_validationRule['ruleParam02'] != null) {
							$o_tablefieldValidationRuleTwig->ValidationRuleParam02 = $a_validationRule['ruleParam02'];
						}
						
						if ($a_validationRule['ruleAutoRequired'] != null) {
							$o_tablefieldValidationRuleTwig->ValidationRuleRequired = $a_validationRule['ruleAutoRequired'];
						}
						
						/* create validation rule for new tabelfield */
						$i_result = $o_tablefieldValidationRuleTwig->InsertRecord();;
						
						/* evaluate result */
						if ($i_result == -1) {
							throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
						} else if ($i_result == 0) {
							throw new forestException(0x10001402);
						} else if ($i_result == 1) {
							/* nothing to do */
						}
					}
				}
				
				$s_nextAction = 'RELOADBRANCH';
				$s_nextActionAfterReload = 'newTwigField';
				
				if ($o_glob->Security->SessionData->Exists('lastView')) {
					if ($o_glob->Security->SessionData->{'lastView'} == \fPHP\Branches\forestBranch::DETAIL) {
						$s_nextAction = null;
						$s_nextActionAfterReload = null;
					}
				}
				
				if (!array_key_exists('sys_fphp_subconstraintKey', $_POST)) {
					/* execute dbms change if sql type is not empty */
					if (issetStr($this->Twig->SqlTypeUUID->PrimaryValue)) {
						/* ignore forestCombination, Form and Dropzone field */
						if ( (!(strval($this->Twig->ForestDataUUID) == 'forestCombination')) && (!(strval($this->Twig->FormElementUUID) == \fPHP\Forms\forestformElement::FORM)) && (!(strval($this->Twig->FormElementUUID) == \fPHP\Forms\forestformElement::DROPZONE)) ) {
							/* add new column to table in dbms */
							$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, $o_tableTwig->Name);

							$o_column = new \fPHP\Base\forestSQLColumnStructure($o_queryAlter);
								$o_column->Name = $this->Twig->FieldName;
								
								$s_columnType = null;
								$i_columnLength = null;
								$i_columnDecimalLength = null;
								\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, strval($this->Twig->SqlTypeUUID), $s_columnType, $i_columnLength, $i_columnDecimalLength);
								
								$o_column->ColumnType = $s_columnType;
								if ($i_columnLength != null) { $o_column->ColumnTypeLength = $i_columnLength; }
								if ($i_columnDecimalLength != null) { $o_column->ColumnTypeDecimalLength = $i_columnDecimalLength; }
								$o_column->AlterOperation = 'ADD';
								
								$s_constraintType = null;
								\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'NULL', $s_constraintType);
								$o_column->ConstraintList->Add($s_constraintType);
								
							$o_queryAlter->Query->Columns->Add($o_column);	
							
							/* alter table does not return a value */
							$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false);
						}
					}
					
					/* update twig file */
					$this->doTwigFile($o_tableTwig);
				} else {
					$a_nextParameters = array();
					$a_nextParameters['rootSubConstraintKey'] = $_POST['sys_fphp_subconstraintKey'];
				}
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction, $s_nextActionAfterReload, $a_nextParameters);
	}

	/**
	 * handle edit twig tabelfield record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function editTwigFieldAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\tablefieldTwig;
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if ($o_glob->IsPost) {
			/* query record */
			if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_tablefieldKey']))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}
			
			$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
			$s_oldFieldName = $this->Twig->FieldName;
			$this->TransferPOST_Twig();
			
			if (in_array($this->Twig->FieldName, $this->ForbiddenTablefieldNames)) {
				throw new forestException(0x10001F10, array(implode(',', $this->ForbiddenTablefieldNames)));
			}
			
			if (issetStr($this->Twig->FormElementUUID->PrimaryValue)) {
				if (issetStr($this->Twig->SqlTypeUUID->PrimaryValue)) {
					$o_formelement_sqltypeTwig = new \fPHP\Twigs\formelement_sqltypeTwig;
					
					if (! $o_formelement_sqltypeTwig->GetRecord(array($this->Twig->FormElementUUID->PrimaryValue, $this->Twig->SqlTypeUUID->PrimaryValue))) {
						throw new forestException(0x10001F0D, array($this->Twig->FormElementUUID, $this->Twig->SqlTypeUUID));
					}
				}
				
				if (issetStr($this->Twig->ForestDataUUID->PrimaryValue)) {
					$o_formelement_forestdataTwig = new \fPHP\Twigs\formelement_forestdataTwig;
					
					if (! $o_formelement_forestdataTwig->GetRecord(array($this->Twig->FormElementUUID->PrimaryValue, $this->Twig->ForestDataUUID->PrimaryValue))) {
						throw new forestException(0x10001F0E, array($this->Twig->FormElementUUID, $this->Twig->ForestDataUUID));
					}
				}
			}
			
			if (array_key_exists('sys_fphp_subconstraintKey', $_POST)) {
				/* query sub constraint record */
				$o_subconstraintTwig = new \fPHP\Twigs\subconstraintTwig;
				
				if (! ($o_subconstraintTwig->GetRecord(array($_POST['sys_fphp_subconstraintKey']))) ) {
					throw new forestException(0x10001401, array($o_subconstraintTwig->fphp_Table));
				}
			}
			
			/* check if json encoded settings are valid */
			$a_settings = json_decode($_POST['sys_fphp_tablefield_JSONEncodedSettings'], true);
		
			if ($a_settings == null) {
				throw new forestException(0x10001F02, array($_POST['sys_fphp_tablefield_JSONEncodedSettings']));
			}
			
			/* recognize translation name and value pair within json encoded settings and create tranlsation records automatically */
			preg_match_all('/\#([^#]+)\#/', $_POST['sys_fphp_tablefield_JSONEncodedSettings'], $a_matches);
	
			if (count($a_matches) > 1) {
				foreach ($a_matches[1] as $s_match) {
					/* check if we want to create a new translation record or updating translation value */
					if (strpos($s_match, '=') !== false) {
						$a_match = explode('=', $s_match);
						$s_name = $a_match[0];
						$s_value = $a_match[1];
						$_POST['sys_fphp_tablefield_JSONEncodedSettings'] = str_replace('#' . $s_match . '#', '#' . $s_name . '#', $_POST['sys_fphp_tablefield_JSONEncodedSettings']);
						/* keep translation name in json encoded settings */
						$this->Twig->JSONEncodedSettings = strval($_POST['sys_fphp_tablefield_JSONEncodedSettings']);

						$o_translationTwig = new \fPHP\Twigs\translationTwig;

						/* if we find a translation record with name from json encoded settings, we will update the value */
						if ($o_translationTwig->GetRecordPrimary(array($o_glob->URL->BranchId, $o_glob->Trunk->LanguageCode->PrimaryValue, $s_name), array('BranchId', 'LanguageCode', 'Name'))) {
							/* update translation recorod with new value */
							$o_translationTwig->Value = $s_value;
														
							/* edit record */
							$i_result = $o_translationTwig->UpdateRecord();

							/* evaluate result */
							if ($i_result == -1) {
								throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
							} else if ($i_result == 0) {
								$o_glob->SystemMessages->Add(new forestException(0x10001406));
							}
						} else { /* create a new translation record */
							$o_translationTwig->Id = null;
							$o_translationTwig->UUID = null;
							$o_translationTwig->BranchId = $o_glob->URL->BranchId;
							$o_translationTwig->LanguageCode = $o_glob->Trunk->LanguageCode->PrimaryValue;
							$o_translationTwig->Name = $s_name;
							$o_translationTwig->Value = $s_value;
							
							/* insert translation record */
							$i_result = $o_translationTwig->InsertRecord();
							
							/* evaluate result */
							if ($i_result == -1) {
								throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
							} else if ($i_result == 0) {
								throw new forestException(0x10001402);
							}
						}
					}
				}
			}
			
			/* if exists update flex record, but only if field name has changed */
			if ($s_oldFieldName != $this->Twig->FieldName) {
				$o_flexTwig = new \fPHP\Twigs\flexTwig;
				
				if ($o_flexTwig->GetRecordPrimary(array($this->Twig->TableUUID, $s_oldFieldName), array('TableUUID', 'FieldName'))) {
					/* set new field name */
					$o_flexTwig->FieldName = $this->Twig->FieldName;
					
					/* edit record */
					$i_result = $o_flexTwig->UpdateRecord();
					
					/* evaluate result */
					if ($i_result == -1) {
						throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
					}
				}
			}
			
			/* edit record */
			$i_result = $this->Twig->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				$o_glob->SystemMessages->Add(new forestException(0x10001406));
				$s_nextAction = 'viewTwig';
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001407));
				$s_nextAction = 'viewTwig';
				
				/* do not execute next action if a field name changed */
				if ($s_oldFieldName != $this->Twig->FieldName) {
					$s_nextAction = null;
				}
				
				if (!array_key_exists('sys_fphp_subconstraintKey', $_POST)) {
					/* execute dbms change if sql type is not empty */
					if (issetStr($this->Twig->SqlTypeUUID->PrimaryValue)) {
						/* ignore forestCombination, Form and Dropzone field */
						if ( (!(strval($this->Twig->ForestDataUUID) == 'forestCombination')) && (!(strval($this->Twig->FormElementUUID) == \fPHP\Forms\forestformElement::FORM)) && (!(strval($this->Twig->FormElementUUID) == \fPHP\Forms\forestformElement::DROPZONE)) ) {
							/* change column within table in dbms */
							$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, $o_tableTwig->Name);

							$o_column = new \fPHP\Base\forestSQLColumnStructure($o_queryAlter);
								$o_column->Name = $s_oldFieldName;
								$o_column->NewName = $this->Twig->FieldName;
								
								if (issetStr($this->Twig->SqlTypeUUID->PrimaryValue)) {
									$s_columnType = null;
									$i_columnLength = null;
									$i_columnDecimalLength = null;
									\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, strval($this->Twig->SqlTypeUUID), $s_columnType, $i_columnLength, $i_columnDecimalLength);
									
									$o_column->ColumnType = $s_columnType;
									if ($i_columnLength != null) { $o_column->ColumnTypeLength = $i_columnLength; }
									if ($i_columnDecimalLength != null) { $o_column->ColumnTypeDecimalLength = $i_columnDecimalLength; }
									
									$s_constraintType = null;
									\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'NULL', $s_constraintType);
									$o_column->ConstraintList->Add($s_constraintType);
									
									$o_column->AlterOperation = 'CHANGE';
								} else {
									$o_column->AlterOperation = 'DROP';
								}
								
							$o_queryAlter->Query->Columns->Add($o_column);	
							
							/* alter table does not return a value */
							$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false);
						}
					}
					
					/* update twig file */
					$this->doTwigFile($o_tableTwig);
				}
			}
		} else {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editKey'), 'editKey' );
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'rootSubConstraintKey'), 'rootSubConstraintKey' );
			
			if ( ($o_glob->Temp->Exists('editKey')) && ($o_glob->Temp->{'editKey'} != null) ) {
				/* query record */
				if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'editKey'}))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* build modal form */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
				$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('EditModalTitle', 1);
				
				/* add current record key to modal form as hidden field */
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_tablefieldKey';
				$o_hidden->Value = strval($this->Twig->UUID);
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
				
				/* delete Order-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_tablefield_Order')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_tablefield_Order].');
				}
				
				if (!( ($o_glob->Temp->Exists('rootSubConstraintKey')) && ($o_glob->Temp->{'rootSubConstraintKey'} != null) )) {
					/* delete SubRecordField-element */
					if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_tablefield_SubRecordField')) {
						throw new forestException('Cannot delete form element with Id[sys_fphp_tablefield_SubRecordField].');
					}
				} else {
					/* query sub constraint record */
					$o_subconstraintTwig = new \fPHP\Twigs\subconstraintTwig;
					
					if (! ($o_subconstraintTwig->GetRecord(array($o_glob->Temp->{'rootSubConstraintKey'}))) ) {
						throw new forestException(0x10001401, array($o_subconstraintTwig->fphp_Table));
					}
					
					/* add current record key to modal form as hidden field */
					$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
					$o_hidden->Id = 'sys_fphp_subconstraintKey';
					$o_hidden->Value = strval($o_glob->Temp->{'rootSubConstraintKey'});
					
					$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
				}
				
				/* add current record order to modal form as hidden field */
				$o_hiddenOrder = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hiddenOrder->Id = 'sys_fphp_tablefield_Order';
				$o_hiddenOrder->Value = strval($this->Twig->Order);
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hiddenOrder);
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/**
	 * handle delete twig tabelfield record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function deleteTwigFieldAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$s_nextActionAfterReload = null;
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\tablefieldTwig;
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteKey'), 'deleteKey' );
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'rootSubConstraintKey'), 'rootSubConstraintKey' );
			
			if ( ($o_glob->Temp->Exists('deleteKey')) && ($o_glob->Temp->{'deleteKey'} != null) ) {
				if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'deleteKey'}))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* create modal confirmation form for deleting record */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig);
				$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
				$s_description = '<b>' . $o_glob->GetTranslation('DeleteModalDescriptionOne', 1) . '</b>';
				$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);
				
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_twigfieldKey';
				$o_hidden->Value = strval($this->Twig->UUID);
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
				
				if ( ($o_glob->Temp->Exists('rootSubConstraintKey')) && ($o_glob->Temp->{'rootSubConstraintKey'} != null) ) {
					/* query sub constraint record */
					$o_subconstraintTwig = new \fPHP\Twigs\subconstraintTwig;
					
					if (! ($o_subconstraintTwig->GetRecord(array($o_glob->Temp->{'rootSubConstraintKey'}))) ) {
						throw new forestException(0x10001401, array($o_subconstraintTwig->fphp_Table));
					}
					
					/* add current record key to modal form as hidden field */
					$o_hidden2 = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
					$o_hidden2->Id = 'sys_fphp_subconstraintKey';
					$o_hidden2->Value = strval($o_glob->Temp->{'rootSubConstraintKey'});
					
					$o_glob->PostModalForm->FormElements->Add($o_hidden2);
				}
			}
		} else {
			/* delete record */
			if (array_key_exists('sys_fphp_twigfieldKey', $_POST)) {
				if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_twigfieldKey']))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				if (array_key_exists('sys_fphp_subconstraintKey', $_POST)) {
					/* query sub constraint record */
					$o_subconstraintTwig = new \fPHP\Twigs\subconstraintTwig;
					
					if (! ($o_subconstraintTwig->GetRecord(array($_POST['sys_fphp_subconstraintKey']))) ) {
						throw new forestException(0x10001401, array($o_subconstraintTwig->fphp_Table));
					}
				}
				
				/* check twigfield relation before deletion */
				$this->checkTwigFieldBeforeDeletion($o_tableTwig, $this->Twig);
				
				$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
				
				/* delete tablefield validationrule records */
				$o_tablefield_validationruleTwig = new \fPHP\Twigs\tablefield_validationruleTwig;
				
				$a_sqlAdditionalFilter = array(array('column' => 'TablefieldUUID', 'value' => $this->Twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$o_tablefield_validationrules = $o_tablefield_validationruleTwig->GetAllRecords(true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				
				foreach ($o_tablefield_validationrules->Twigs as $o_tablefield_validationrule) {
					/* delete tablefield validationrule record */
					$i_return = $o_tablefield_validationrule->DeleteRecord();
				
					/* evaluate the result */
					if ($i_return <= 0) {
						throw new forestException(0x10001423);
					}
				}
				
				/* delete flex record */
				$o_flexTwig = new \fPHP\Twigs\flexTwig;
				
				if ($o_flexTwig->GetRecordPrimary(array($this->Twig->TableUUID, $this->Twig->FieldName), array('TableUUID', 'FieldName'))) {
					/* delete record */
					$i_return = $o_flexTwig->DeleteRecord();
					
					/* evaluate the result */
					if ($i_return <= 0) {
						throw new forestException(0x10001423);
					}
				}
				
				/* delete translation records */
				preg_match_all('/\#([^#]+)\#/', $this->Twig->JSONEncodedSettings, $a_matches);
		
				if (count($a_matches) > 1) {
					$o_translationTwig = new \fPHP\Twigs\translationTwig;
					
					foreach ($a_matches[1] as $s_match) {
						if ($o_translationTwig->GetRecordPrimary(array($o_glob->URL->BranchId, $s_match), array('BranchId', 'Name'))) {
							/* delete translation record */
							$i_return = $o_translationTwig->DeleteRecord();
						
							/* evaluate the result */
							if ($i_return <= 0) {
								throw new forestException(0x10001423);
							}
						}
					}
				}
				
				/* delete record */
				$i_return = $this->Twig->DeleteRecord();
				
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
				
				$o_glob->SystemMessages->Add(new forestException(0x10001427));
				
				/* reload branch and call viewTwig if a twig field has been deleted */
				$s_nextAction = 'RELOADBRANCH';
				$s_nextActionAfterReload = 'viewTwig';
				
				/* cleanup tablefield relations */
				$this->cleanupTwigFieldAfterDeletion($o_tableTwig, $this->Twig);
				
				if (!array_key_exists('sys_fphp_subconstraintKey', $_POST)) {
					/* change column within table in dbms */
					$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, $o_tableTwig->Name);

					$o_column = new \fPHP\Base\forestSQLColumnStructure($o_queryAlter);
						$o_column->Name = $this->Twig->FieldName;
						
						$s_columnType = null;
						$i_columnLength = null;
						$i_columnDecimalLength = null;
						\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, strval($this->Twig->SqlTypeUUID), $s_columnType, $i_columnLength, $i_columnDecimalLength);
						
						$o_column->ColumnType = $s_columnType;
						if ($i_columnLength != null) { $o_column->ColumnTypeLength = $i_columnLength; }
						if ($i_columnDecimalLength != null) { $o_column->ColumnTypeDecimalLength = $i_columnDecimalLength; }
						
						$s_constraintType = null;
						\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'NULL', $s_constraintType);
						$o_column->ConstraintList->Add($s_constraintType);
						
						$o_column->AlterOperation = 'DROP';
						
					$o_queryAlter->Query->Columns->Add($o_column);	
					
					/* alter table does not return a value */
					$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false);
					
					/* update twig file */
					$this->doTwigFile($o_tableTwig);
				}
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction, $s_nextActionAfterReload);
	}

	/**
	 * check twigfield relation to other elements
	 *
	 * @param tableTwig $p_o_table  table record of table sys_fphp_table
	 * @param tablefieldTwig $p_o_tablefield  tablefield record of table sys_fphp_tablefield
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function checkTwigFieldBeforeDeletion(\fPHP\Twigs\tableTwig $p_o_table, \fPHP\Twigs\tablefieldTwig $p_o_tablefield) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$o_forestdataTwig = new \fPHP\Twigs\forestdataTwig;
		
		if (! ($o_forestdataTwig->GetRecordPrimary(array('forestCombination'), array('Name'))) ) {
			throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
		}
		
		$s_forestCombinationUUID = $o_forestdataTwig->UUID;
		
		if (! ($o_forestdataTwig->GetRecordPrimary(array('forestLookup'), array('Name'))) ) {
			throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
		}
		
		$s_forestLookupUUID = $o_forestdataTwig->UUID;
		
		/* look for forestLookup tablefields */
		$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
		
		$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $p_o_table->UUID, 'operator' => '<>', 'filterOperator' => 'AND'), array('column' => 'ForestDataUUID', 'value' => $s_forestLookupUUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		foreach ($o_tablefields->Twigs as $o_tablefield) {
			$s_table = strval($o_tablefield->TableUUID);
			
			if ($s_table == 'record_not_found_with_primary') {
				$s_table = 'SubRecords';
			}
			
			/* get json encoded settings as array */
			$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_tablefield->JSONEncodedSettings);
			$a_settings = json_decode($s_JSONEncodedSettings, true);
			
			/* check if json encoded settings are valid */
			if ($a_settings == null) {
				throw new forestException(0x10001F02, array($s_JSONEncodedSettings));
			}
			
			if (array_key_exists('forestLookupDataTable', $a_settings)) {
				if ($a_settings['forestLookupDataTable'] == $p_o_table->Name) {
					if (array_key_exists('forestLookupDataPrimary', $a_settings)) {
						if (in_array($p_o_tablefield->FieldName, $a_settings['forestLookupDataPrimary'])) {
							throw new forestException(0x10001F07, array($p_o_tablefield->FieldName, $o_tablefield->FieldName, $s_table));
						}
					}
					
					if (array_key_exists('forestLookupDataLabel', $a_settings)) {
						if (in_array($p_o_tablefield->FieldName, $a_settings['forestLookupDataLabel'])) {
							throw new forestException(0x10001F07, array($p_o_tablefield->FieldName, $o_tablefield->FieldName, $s_table));
						}
					}
					
					if (array_key_exists('forestLookupDataFilter', $a_settings)) {
						if (in_array($p_o_tablefield->FieldName, $a_settings['forestLookupDataFilter'])) {
							throw new forestException(0x10001F07, array($p_o_tablefield->FieldName, $o_tablefield->FieldName, $s_table));
						}
					}
				}
			}
		}
		
		/* look for forestCombination tablefields */
		$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
		
		$a_sqlAdditionalFilter = array(array('column' => 'ForestDataUUID', 'value' => $s_forestCombinationUUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		$s_table = strval($p_o_tablefield->TableUUID);
		
		if ($s_table == 'record_not_found_with_primary') {
			/* get real table with sub constraint relation */
			$o_subconstraintTwig = new \fPHP\Twigs\subconstraintTwig;
			
			if ($o_subconstraintTwig->GetRecord(array($p_o_tablefield->TableUUID->PrimaryValue))) {
				$s_table = strval($o_subconstraintTwig->SubTableUUID);
			}
		}
		
		foreach ($o_tablefields->Twigs as $o_tablefield) {
			/* get json encoded settings as array */
			$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_tablefield->JSONEncodedSettings);
			$a_settings = json_decode($s_JSONEncodedSettings, true);
			
			/* check if json encoded settings are valid */
			if ($a_settings == null) {
				throw new forestException(0x10001F02, array($s_JSONEncodedSettings));
			}
			
			if (array_key_exists('forestCombination', $a_settings)) {
				if ( (strpos($a_settings['forestCombination'], $p_o_tablefield->FieldName) !== false) && ($p_o_tablefield->TableUUID->PrimaryValue == $o_tablefield->TableUUID->PrimaryValue) ) {
					/* tablefield is used in forestCombination of the same table or subrecord constellation */
					throw new forestException(0x10001F08, array($p_o_tablefield->FieldName, $o_tablefield->FieldName, ((strval($o_tablefield->TableUUID) == 'record_not_found_with_primary') ? 'SubRecords' : strval($o_tablefield->TableUUID)) ));
				} else if ( (strpos($a_settings['forestCombination'], $s_table . '$' . $p_o_tablefield->FieldName) !== false) && ($o_tablefield->TableUUID->PrimaryValue == $p_o_table->UUID) ) {
					/* tablefield is used in forestCombination of higher table(parameter table) */
					throw new forestException(0x10001F08, array($p_o_tablefield->FieldName, $o_tablefield->FieldName, strval($p_o_table->Name)));
				}
			}
		}
	}
	
	/**
	 * check twigfield relation to other elements
	 *
	 * @param tableTwig $p_o_table  table record of table sys_fphp_table, passed by reference
	 * @param tablefieldTwig $p_o_tablefield  tablefield record of table sys_fphp_tablefield
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function cleanupTwigFieldAfterDeletion(\fPHP\Twigs\tableTwig &$p_o_table, \fPHP\Twigs\tablefieldTwig $p_o_tablefield) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$b_table_changed = false;
		
		/* cleanup unique keys */
		if (issetStr($p_o_table->Unique->PrimaryValue)) {
			$a_uniqueKeys = explode(':', $p_o_table->Unique->PrimaryValue);
			$i_deleteKey = null;
			
			foreach($a_uniqueKeys as $i_key => $o_uniqueKey) {
				$a_uniqueKey = explode('=', $o_uniqueKey);
				
				$a_keys = explode(';', $a_uniqueKey[1]);
				
				if (in_array($p_o_tablefield->UUID, $a_keys)) {
					$b_table_changed = true;
					$i_deleteKey = $i_key;
				}
			}
			
			if ($i_deleteKey !== null) {
				unset($a_uniqueKeys[$i_deleteKey]);
				
				if (count($a_uniqueKeys) > 0) {
					$p_o_table->Unique = implode(':', $a_uniqueKeys);
				} else {
					$p_o_table->Unique = 'NULL';
				}
			}
		}
		
		/* cleanup sort orders */
		if (issetStr($p_o_table->SortOrder->PrimaryValue)) {
			$a_sortOrders = explode(':', $p_o_table->SortOrder->PrimaryValue);
			$i_deleteKey = null;
			
			foreach($a_sortOrders as $i_key => $o_sortOrder) {
				$a_sort = explode(';', $o_sortOrder);
				
				if (count($a_sort) != 2) {
					continue;
				}
				
				if ($p_o_tablefield->UUID == $a_sort[1]) {
					$b_table_changed = true;
					$i_deleteKey = $i_key;
				}
			}
			
			if ($i_deleteKey !== null) {
				unset($a_sortOrders[$i_deleteKey]);
				
				if (count($a_sortOrders) > 0) {
					$p_o_table->SortOrder = implode(':', $a_sortOrders);
				} else {
					$p_o_table->SortOrder = 'NULL';
				}
			}
		}
		
		/* cleanup view */
		if (issetStr($p_o_table->View->PrimaryValue)) {
			$a_view = explode(';', $p_o_table->View->PrimaryValue);
			$i_deleteKey = null;
			
			foreach($a_view as $i_key => $s_view) {
				if ($p_o_tablefield->UUID == $s_view) {
					$b_table_changed = true;
					$i_deleteKey = $i_key;
				}
			}
			
			if ($i_deleteKey !== null) {
				unset($a_view[$i_deleteKey]);
				
				if (count($a_view) > 0) {
					$p_o_table->View = implode(';', $a_view);
				} else {
					$p_o_table->View = 'NULL';
				}
			}
		}
		
		/* cleanup sort column */
		if (issetStr($p_o_table->SortColumn->PrimaryValue)) {
			if ($p_o_tablefield->UUID == $p_o_table->SortColumn->PrimaryValue) {
				$p_o_table->SortColumn = 'NULL';
				$b_table_changed = true;
			}
		}
		
		/* change twig record if flag has been set */
		if ($b_table_changed) {
			$p_o_table->UpdateRecord();
		}
		
		/* cleanup sub constraints */
		$o_subconstraintTwig = new \fPHP\Twigs\subconstraintTwig;
		
		$a_sqlAdditionalFilter = array(array('column' => 'SubTableUUID', 'value' => $p_o_table->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_subconstraints = $o_subconstraintTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		foreach ($o_subconstraints->Twigs as $o_subconstraint) {
			/* cleanup sub constraint view */
			if (issetStr($o_subconstraint->View->PrimaryValue)) {
				$a_view = explode(';', $o_subconstraint->View->PrimaryValue);
				$i_deleteKey = null;
				
				foreach($a_view as $i_key => $s_view) {
					if ($p_o_tablefield->UUID == $s_view) {
						$i_deleteKey = $i_key;
					}
				}
				
				if ($i_deleteKey !== null) {
					unset($a_view[$i_deleteKey]);
					
					if (count($a_view) > 0) {
						$o_subconstraint->View = implode(';', $a_view);
					} else {
						$o_subconstraint->View = 'NULL';
					}
					
					$o_subconstraint->UpdateRecord();
				}
			}
		}
	}

	/**
	 * handle action to change order of twig tablefield records, moving one record up
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function moveUpTwigFieldAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\tablefieldTwig;
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'rootSubConstraintKey'), 'rootSubConstraintKey' );
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$s_uuid = $o_tableTwig->UUID;
		
		if ( ($o_glob->Temp->Exists('rootSubConstraintKey')) && ($o_glob->Temp->{'rootSubConstraintKey'} != null) ) {
			$s_uuid = $o_glob->Temp->{'rootSubConstraintKey'};
		}
		
		$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $s_uuid, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$this->MoveUpRecord();
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		$s_nextAction = 'viewTwig';
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/**
	 * handle action to change order of twig tablefield records, moving one record down
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function moveDownTwigFieldAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ( ($this->Twig != null) && ($this->Twig->fphp_SystemTable) ) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\tablefieldTwig;
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'rootSubConstraintKey'), 'rootSubConstraintKey' );
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$s_uuid = $o_tableTwig->UUID;
		
		if ( ($o_glob->Temp->Exists('rootSubConstraintKey')) && ($o_glob->Temp->{'rootSubConstraintKey'} != null) ) {
			$s_uuid = $o_glob->Temp->{'rootSubConstraintKey'};
		}
		
		$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $s_uuid, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$this->MoveDownRecord();
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		$s_nextAction = 'viewTwig';
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}

	/**
	 * handle view twig tablefield record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function viewTwigFieldAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\tablefieldTwig;
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
			
		/* query twig record if we have view key in url parameters */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'viewKey'), 'viewKey' );
		$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'rootSubConstraintKey'), 'rootSubConstraintKey' );
		
		if ( ($o_glob->Temp->Exists('viewKey')) && ($o_glob->Temp->{'viewKey'} != null) ) {
			/* query record */
			if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'viewKey'}))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}
			
			/* create modal read only form */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $this->Twig->FieldName . ' Tablefield';
			
			/* delete Order-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('readonly_sys_fphp_tablefield_Order')) {
				throw new forestException('Cannot delete form element with Id[readonly_sys_fphp_tablefield_Order].');
			}
			
			if (!( ($o_glob->Temp->Exists('rootSubConstraintKey')) && ($o_glob->Temp->{'rootSubConstraintKey'} != null) )) {
				/* delete SubRecordField-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('readonly_sys_fphp_tablefield_SubRecordField')) {
					throw new forestException('Cannot delete form element with Id[readonly_sys_fphp_tablefield_SubRecordField].');
				}
			} else {
				/* query sub constraint record */
				$o_subconstraintTwig = new \fPHP\Twigs\subconstraintTwig;
				
				if (! ($o_subconstraintTwig->GetRecord(array($o_glob->Temp->{'rootSubConstraintKey'}))) ) {
					throw new forestException(0x10001401, array($o_subconstraintTwig->fphp_Table));
				}
			}
			
			$s_subFormItems = '';

			/* ************************************************** */
			/* *****************VALIDATION RULES***************** */
			/* ************************************************** */
			/* look for tablefield validation rules */
			$o_tablefield_validationruleTwig = new \fPHP\Twigs\tablefield_validationruleTwig;
			
			$a_sqlAdditionalFilter = array(array('column' => 'TablefieldUUID', 'value' => $this->Twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_tablefield_validationrules = $o_tablefield_validationruleTwig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			/* ************************* */
			/* ***********HEAD********** */
			/* ************************* */
			
			$s_subTableHead = '';
			
			foreach ($o_tablefield_validationruleTwig->fphp_View as $s_columnHead) {
				$s_columnHead = $o_glob->GetTranslation('form' . $s_columnHead . 'Label', 0);
				
				if (\fPHP\Helper\forestStringLib::EndsWith($s_columnHead, ':')) {
					$s_columnHead = substr($s_columnHead, 0, -1);
				}
				
				$s_subTableHead .= '<th>' . $s_columnHead . '</th>' . "\n";
			}
			
			$s_subTableHead .= '<th>' . $o_glob->GetTranslation('formSubOptions', 1) . '</th>' . "\n";
			
			/* ************************* */
			/* ***********ROWS********** */
			/* ************************* */
			
			$s_subTableRows = '';
			
			foreach ($o_tablefield_validationrules->Twigs as $o_tablefield_validationrule) {
				/* render records based on twig view columns */
				$s_subTableRows .=  '<tr>' . "\n";
				
				foreach ($o_tablefield_validationruleTwig->fphp_View as $s_column) {
					$s_formElement = '';
		
					if ($o_glob->TablefieldsDictionary->Exists($o_tablefield_validationrule->fphp_Table . '_' . $s_column)) {
						$s_formElement = $o_glob->TablefieldsDictionary->{$o_tablefield_validationrule->fphp_Table . '_' . $s_column}->FormElementName;
					}
					
					$s_value = '-';
					$this->ListViewRenderColumnValue($s_value, $s_formElement, $s_column, $o_tablefield_validationrule, $o_tablefield_validationruleTwig->fphp_Table . '_' . $s_column);
					$s_subTableRows .=  '<td><span>' . $s_value . '</span></td>' . "\n";
				}
				
				$s_options = '<span class="btn-group">' . "\n";
				
				/* edit link */
				$a_parameters = $o_glob->URL->Parameters;
				unset($a_parameters['newKey']);
				unset($a_parameters['viewKey']);
				unset($a_parameters['editKey']);
				unset($a_parameters['deleteKey']);
				unset($a_parameters['editSubKey']);
				unset($a_parameters['deleteSubKey']);
				unset($a_parameters['deleteFileKey']);
				unset($a_parameters['subConstraintKey']);
				$a_parameters['viewKey'] = $this->Twig->UUID;
				$a_parameters['editKey'] = $o_tablefield_validationrule->UUID;
				
				if ($o_glob->Security->CheckUserPermission(null, 'editValidationRule')) {
					$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'editValidationRule', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="bi bi-pencil-square"></span></a>' . "\n";
				}

				/* delete link */
				$a_parameters = $o_glob->URL->Parameters;
				unset($a_parameters['newKey']);
				unset($a_parameters['viewKey']);
				unset($a_parameters['editKey']);
				unset($a_parameters['deleteKey']);
				unset($a_parameters['editSubKey']);
				unset($a_parameters['deleteSubKey']);
				unset($a_parameters['deleteFileKey']);
				unset($a_parameters['subConstraintKey']);
				$a_parameters['viewKey'] = $this->Twig->UUID;
				$a_parameters['deleteKey'] = $o_tablefield_validationrule->UUID;
				
				if ($o_glob->Security->CheckUserPermission(null, 'deleteValidationRule')) {
					$s_options .= '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'deleteValidationRule', $a_parameters) . '" class="btn btn-light" title="' . $o_glob->GetTranslation('btnDeleteText', 1) . '"><span class="bi bi-trash-fill text-danger"></span></a>' . "\n";
				}

				$s_options .= '</span>' . "\n";
				$s_subTableRows .=  '<td>' . $s_options . '</td>' . "\n";
				
				$s_subTableRows .=  '</tr>' . "\n";
			}
			
			/* new link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			$a_parameters['viewKey'] = $this->Twig->UUID;
			
			if ($o_glob->Security->CheckUserPermission(null, 'newValidationRule')) {
				$s_newButton = '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'newValidationRule', $a_parameters) . '" class="btn btn-light" style="margin-bottom: 5px;" title="' . $o_glob->GetTranslation('btnNewText', 1) . '"><span class="bi bi-plus-circle-fill text-success"></span></a>' . "\n";
			} else {
				$s_newButton = '';
			}

			$s_subFormItemContent = new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEMCONTENT, array($s_newButton, $s_subTableHead, $s_subTableRows));
			$s_subFormItems .= new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEWITEM, array('tablefield_validationrules' . $this->Twig->fphp_Table, $o_glob->GetTranslation('ValidationRules', 0), ' show', $s_subFormItemContent, $o_tableTwig->Name));
			
			/* go back link */
			$s_goBackButton = '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'viewTwig') . '" class="btn btn-lg btn-light" title="' . $o_glob->GetTranslation('btnBack', 1) . '"><span class="bi bi-arrow-left"></span></a>&nbsp;' . "\n";
			
			/* edit link */
			$a_parameters = $o_glob->URL->Parameters;
			unset($a_parameters['newKey']);
			unset($a_parameters['viewKey']);
			unset($a_parameters['editKey']);
			unset($a_parameters['deleteKey']);
			unset($a_parameters['editSubKey']);
			unset($a_parameters['deleteSubKey']);
			unset($a_parameters['deleteFileKey']);
			unset($a_parameters['subConstraintKey']);
			unset($a_parameters['rootSubConstraintKey']);
			$a_parameters['editKey'] = $this->Twig->UUID;
			if ( ($o_glob->Temp->Exists('rootSubConstraintKey')) && ($o_glob->Temp->{'rootSubConstraintKey'} != null) ) {
				$a_parameters['rootSubConstraintKey'] = $o_glob->Temp->{'rootSubConstraintKey'};
			}
			$s_editButton = '<a href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, 'editTwigField', $a_parameters) . '" class="btn btn-lg btn-light" title="' . $o_glob->GetTranslation('btnEditText', 1) . '"><span class="bi bi-pencil-square"></span></a><br>' . "\n";
			
			$o_glob->PostModalForm->BeforeForm = $s_goBackButton . $s_editButton;
			$o_glob->PostModalForm->BeforeFormRightAlign = true;
			
			/* add sub constraints and files for modal form */
			$o_glob->PostModalForm->FormModalSubForm = strval(new \fPHP\Branches\forestTemplates(\fPHP\Branches\forestTemplates::SUBLISTVIEW, array($o_tableTwig->Name, $s_subFormItems)));
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction('init');
	}
	
	
	/**
	 * handle new translation record action for twig
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function newTranslationAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\translationTwig;
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			/* add new branch record form */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
			
			/* delete BranchId-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_translation_BranchId')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_translation_BranchId].');
			}
		} else {
			/* check posted data for new tablefield record */
			$this->TransferPOST_Twig();
			
			/* add BranchId value to record */
			$this->Twig->BranchId = $o_glob->URL->BranchId;
			
			/* insert record */
			$i_result = $this->Twig->InsertRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				throw new forestException(0x10001402);
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001404));
				$s_nextAction = 'viewTwig';
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}

	/**
	 * handle edit translation record action for twig
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function editTranslationAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\translationTwig;
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if ($o_glob->IsPost) {
			/* query record */
			if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_translationKey']))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}
			
			$this->TransferPOST_Twig();
			
			/* add BranchId value to record */
			$this->Twig->BranchId = $o_glob->URL->BranchId;
			
			/* edit record */
			$i_result = $this->Twig->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				$o_glob->SystemMessages->Add(new forestException(0x10001406));
				$s_nextAction = 'viewTwig';
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001407));
				$s_nextAction = 'viewTwig';
			}
		} else {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editKey'), 'editKey' );
		
			if ( ($o_glob->Temp->Exists('editKey')) && ($o_glob->Temp->{'editKey'} != null) ) {
				/* query record */
				if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'editKey'}))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* build modal form */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
				$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('EditModalTitle', 1);
				
				/* add current record key to modal form as hidden field */
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_translationKey';
				$o_hidden->Value = strval($this->Twig->UUID);
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
				
				/* delete BranchId-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_translation_BranchId')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_translation_BranchId].');
				}
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/**
	 * handle delete translation record action for twig
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function deleteTranslationAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\translationTwig;
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteKey'), 'deleteKey' );
		
			if ( ($o_glob->Temp->Exists('deleteKey')) && ($o_glob->Temp->{'deleteKey'} != null) ) {
				if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'deleteKey'}))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* create modal confirmation form for deleting record */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig);
				$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
				$s_description = '<b>' . $o_glob->GetTranslation('DeleteModalDescriptionOne', 1) . '</b>';
				$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);
				
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_translationKey';
				$o_hidden->Value = strval($this->Twig->UUID);
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
			}
		} else {
			/* delete record */
			if (array_key_exists('sys_fphp_translationKey', $_POST)) {
				if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_translationKey']))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* delete record */
				$i_return = $this->Twig->DeleteRecord();
				
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
				
				$o_glob->SystemMessages->Add(new forestException(0x10001427));
				$s_nextAction = 'viewTwig';
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}


	/**
	 * handle new unique record action for twig
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function newUniqueAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (! ($this->Twig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($this->Twig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			$o_forestdataTwig = new \fPHP\Twigs\forestdataTwig;
			
			if (! ($o_forestdataTwig->GetRecordPrimary(array('forestCombination'), array('Name'))) ) {
				throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
			}
			
			$s_forestCombinationUUID = $o_forestdataTwig->UUID;
			
			if (! ($o_forestdataTwig->GetRecordPrimary(array('forestLookup'), array('Name'))) ) {
				throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
			}
			
			$s_forestLookupUUID = $o_forestdataTwig->UUID;
		
			/* update lookup filter */
			$o_forestLookupData = new \fPHP\Helper\forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID, '!2ForestDataUUID' => $s_forestLookupUUID));
			$this->Twig->Unique->SetLookupData(new \fPHP\Helper\forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID)));
			$this->Twig->Unique->PrimaryValue = 'NULL';
			$this->Twig->SortOrder->SetLookupData(new \fPHP\Helper\forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID)));
			$this->Twig->View->SetLookupData(new \fPHP\Helper\forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID)));
			$this->Twig->SortColumn->SetLookupData($o_forestLookupData);
			
			/* add new unique record form */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
			
			/* create manual form element for unique key name */
			$o_text = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::TEXT);
			$o_text->Label = $o_glob->GetTranslation('rootUniqueNameLabel', 0);
			$o_text->Id = 'sys_fphp_table_uniqueName';
			$o_text->ValMessage = $o_glob->GetTranslation('rootUniqueNameValMessage', 0);
			$o_text->Placeholder = $o_glob->GetTranslation('rootUniqueNamePlaceholder', 0);
			
			/* add manual created form element to genereal tab */
			if (!$o_glob->PostModalForm->AddFormElement($o_text, 'general', true)) {
				throw new forestException('Cannot add form element to tab with id[general].');
			}
			
			/* add validation rules for manual created form elements */
			$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule('sys_fphp_table_uniqueName', 'required', 'true'));
			
			/* delete StorageSpace-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_StorageSpace')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_StorageSpace].');
			}
			
			/* delete SortOrder-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_SortOrder')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_SortOrder].');
			}
			
			/* delete Interval-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Interval')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_Interval].');
			}
			
			/* delete View-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_View[]')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_View[]].');
			}
			
			/* delete SortColumn-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_SortColumn')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_SortColumn].');
			}
			
			/* delete InfoColumns-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_InfoColumns')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_InfoColumns].');
			}
			
			/* delete InfoColumnsView-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_InfoColumnsView[]')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_InfoColumnsView[]].');
			}
			
			/* delete Versioning-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Versioning')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_Versioning].');
			}
			
			/* delete CheckoutInterval-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_CheckoutInterval')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_CheckoutInterval].');
			}
			
			/* delete Identifier-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Identifier')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_Identifier].');
			}
		} else {
			$s_unique = '';
			
			if (array_key_exists($this->Twig->fphp_Table . '_uniqueName', $_POST)) {
				$s_unique = $_POST[$this->Twig->fphp_Table . '_uniqueName'] . '=';
			} else {
				throw new forestException(0x10001402);
			}
			
			if (is_array($_POST[$this->Twig->fphp_Table . '_Unique'])) {
				/* post value is array, so we need to valiate multiple selected items */
				$s_sum = '';
				
				foreach ($_POST[$this->Twig->fphp_Table . '_Unique'] as $s_selectOptValue) {
					$s_sum .= strval($s_selectOptValue) . ';';
				}
				
				$s_sum = substr($s_sum, 0, -1);
				$s_unique .= $s_sum;
			} else {
				$s_unique .= strval($_POST[$this->Twig->fphp_Table . '_Unique']);
			}
			
			if (issetStr($this->Twig->Unique->PrimaryValue)) {
				$this->Twig->Unique = $this->Twig->Unique->PrimaryValue . ':' . $s_unique;
			} else {
				$this->Twig->Unique = $s_unique;
			}
			
			/* edit recrod */
			$i_result = $this->Twig->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				$o_glob->SystemMessages->Add(new forestException(0x10001406));
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001404));
				$s_nextAction = 'viewTwig';
				
				/* update twig file */
				$this->doTwigFile($this->Twig);
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}

	/**
	 * handle edit unique record action for twig
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function editUniqueAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (! ($this->Twig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($this->Twig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if ($o_glob->IsPost) {
			$s_unique = '';
		
			if (array_key_exists($this->Twig->fphp_Table . '_uniqueName', $_POST)) {
				$s_unique = $_POST[$this->Twig->fphp_Table . '_uniqueName'] . '=';
			} else {
				throw new forestException(0x10001402);
			}
			
			if (is_array($_POST[$this->Twig->fphp_Table . '_Unique'])) {
				/* post value is array, so we need to validate multiple selected items */
				$s_sum = '';
				
				foreach ($_POST[$this->Twig->fphp_Table . '_Unique'] as $s_selectOptValue) {
					$s_sum .= strval($s_selectOptValue) . ';';
				}
				
				$s_sum = substr($s_sum, 0, -1);
				$s_unique .= $s_sum;
			} else {
				$s_unique .= strval($_POST[$this->Twig->fphp_Table . '_Unique']);
			}
			
			if (issetStr($this->Twig->Unique->PrimaryValue)) {
				$a_uniqueKeys = explode(':', $this->Twig->Unique->PrimaryValue);
				
				if ( (!array_key_exists('sys_fphp_uniqueKey', $_POST)) || ($_POST['sys_fphp_uniqueKey'] >= count($a_uniqueKeys)) ) {
					throw new forestException(0x10001405, array($this->Twig->fphp_Table));
				}
				
				$a_uniqueKeys[intval($_POST['sys_fphp_uniqueKey'])] = $s_unique;
				$this->Twig->Unique = implode(':', $a_uniqueKeys);
			} else {
				$this->Twig->Unique = $s_unique;
			}
			
			/* edit record */
			$i_result = $this->Twig->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				$o_glob->SystemMessages->Add(new forestException(0x10001406));
				$s_nextAction = 'viewTwig';
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001407));
				$s_nextAction = 'viewTwig';
				
				/* update twig file */
				$this->doTwigFile($this->Twig);
			}
		} else {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editKey'), 'editKey' );
		
			if ( ($o_glob->Temp->Exists('editKey')) && ($o_glob->Temp->{'editKey'} != null) ) {
				$o_forestdataTwig = new \fPHP\Twigs\forestdataTwig;
			
				if (! ($o_forestdataTwig->GetRecordPrimary(array('forestCombination'), array('Name'))) ) {
					throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
				}
				
				$s_forestCombinationUUID = $o_forestdataTwig->UUID;
				
				if (! ($o_forestdataTwig->GetRecordPrimary(array('forestLookup'), array('Name'))) ) {
					throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
				}
				
				$s_forestLookupUUID = $o_forestdataTwig->UUID;
				
				/* update lookup filter */
				$o_forestLookupData = new \fPHP\Helper\forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID, '!2ForestDataUUID' => $s_forestLookupUUID));
				$this->Twig->Unique->SetLookupData(new \fPHP\Helper\forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID)));
				$this->Twig->SortOrder->SetLookupData(new \fPHP\Helper\forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID)));
				$this->Twig->View->SetLookupData(new \fPHP\Helper\forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID)));
				$this->Twig->SortColumn->SetLookupData($o_forestLookupData);
				
				/* get value */
				$a_uniqueKeys = explode(':', $this->Twig->Unique->PrimaryValue);
				
				if ($o_glob->Temp->{'editKey'} >= count($a_uniqueKeys)) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				$o_uniqueKey = explode('=', $a_uniqueKeys[intval($o_glob->Temp->{'editKey'})]);
				
				$s_name = $o_uniqueKey[0];
				$this->Twig->Unique->PrimaryValue = $o_uniqueKey[1];
				
				/* build modal form */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
				$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('EditModalTitle', 1);
				
				/* add current record key to modal form as hidden field */
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_uniqueKey';
				$o_hidden->Value = strval($o_glob->Temp->{'editKey'});
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
				
				/* create manual form element for unique key name */
				$o_text = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::TEXT);
				$o_text->Label = $o_glob->GetTranslation('rootUniqueNameLabel', 0);
				$o_text->Id = 'sys_fphp_table_uniqueName';
				$o_text->ValMessage = $o_glob->GetTranslation('rootUniqueNameValMessage', 0);
				$o_text->Placeholder = $o_glob->GetTranslation('rootUniqueNamePlaceholder', 0);
				$o_text->Value = $s_name;
				
				/* add manual created form element to genereal tab */
				if (!$o_glob->PostModalForm->AddFormElement($o_text, 'general', true)) {
					throw new forestException('Cannot add form element to tab with id[general].');
				}
				
				/* add validation rules for manual created form elements */
				$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule('sys_fphp_table_uniqueName', 'required', 'true'));
				
				/* delete StorageSpace-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_StorageSpace')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_StorageSpace].');
				}
				
				/* delete SortOrder-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_SortOrder')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_SortOrder].');
				}
				
				/* delete Interval-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Interval')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_Interval].');
				}
				
				/* delete View-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_View[]')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_View[]].');
				}
				
				/* delete SortColumn-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_SortColumn')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_SortColumn].');
				}
				
				/* delete InfoColumns-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_InfoColumns')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_InfoColumns].');
				}
				
				/* delete InfoColumnsView-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_InfoColumnsView[]')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_InfoColumnsView[]].');
				}
				
				/* delete Versioning-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Versioning')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_Versioning].');
				}
				
				/* delete CheckoutInterval-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_CheckoutInterval')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_CheckoutInterval].');
				}
				
				/* delete Identifier-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Identifier')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_Identifier].');
				}
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/**
	 * handle delete unique record action for twig
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function deleteUniqueAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (! ($this->Twig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($this->Twig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteKey'), 'deleteKey' );
		
			if ( ($o_glob->Temp->Exists('deleteKey')) && ($o_glob->Temp->{'deleteKey'} != null) ) {
				/* create modal confirmation form for deleting record */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig);
				$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
				$s_description = '<b>' . $o_glob->GetTranslation('DeleteModalDescriptionOne', 1) . '</b>';
				$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);
				
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_uniqueKey';
				$o_hidden->Value = strval($o_glob->Temp->{'deleteKey'});
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
			}
		} else {
			/* delete record */
			if (array_key_exists('sys_fphp_uniqueKey', $_POST)) {
				if (issetStr($this->Twig->Unique->PrimaryValue)) {
					$a_uniqueKeys = explode(':', $this->Twig->Unique->PrimaryValue);
					
					if ( (!array_key_exists('sys_fphp_uniqueKey', $_POST)) || ($_POST['sys_fphp_uniqueKey'] >= count($a_uniqueKeys)) ) {
						throw new forestException(0x10001405, array($this->Twig->fphp_Table));
					}
					
					unset($a_uniqueKeys[intval($_POST['sys_fphp_uniqueKey'])]);
					
					if (count($a_uniqueKeys) > 0) {
						$this->Twig->Unique = implode(':', $a_uniqueKeys);
					} else {
						$this->Twig->Unique = 'NULL';
					}
				} else {
					$this->Twig->Unique = 'NULL';
				}
				
				/* edit record */
				$i_return = $this->Twig->UpdateRecord();
				
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
				
				$o_glob->SystemMessages->Add(new forestException(0x10001427));
				$s_nextAction = 'viewTwig';
				
				/* update twig file */
				$this->doTwigFile($this->Twig);
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}


	/**
	 * handle new sort order record action for twig
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function newSortAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (! ($this->Twig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($this->Twig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			$o_forestdataTwig = new \fPHP\Twigs\forestdataTwig;
			
			if (! ($o_forestdataTwig->GetRecordPrimary(array('forestCombination'), array('Name'))) ) {
				throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
			}
			
			$s_forestCombinationUUID = $o_forestdataTwig->UUID;
			
			if (! ($o_forestdataTwig->GetRecordPrimary(array('forestLookup'), array('Name'))) ) {
				throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
			}
			
			$s_forestLookupUUID = $o_forestdataTwig->UUID;
			
			/* update lookup filter */
			$o_forestLookupData = new \fPHP\Helper\forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID, '!2ForestDataUUID' => $s_forestLookupUUID));
			$this->Twig->Unique->SetLookupData(new \fPHP\Helper\forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID)));
			$this->Twig->SortOrder->SetLookupData(new \fPHP\Helper\forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID)));
			$this->Twig->SortOrder->PrimaryValue = 'NULL';
			$this->Twig->View->SetLookupData(new \fPHP\Helper\forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID)));
			$this->Twig->SortColumn->SetLookupData($o_forestLookupData);
			
			/* add new sort order record form */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
			
			/* create manual form element for unique key name */
			$o_select = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::SELECT);
			$o_select->Label = $o_glob->GetTranslation('rootSortDirectionLabel', 0);
			$o_select->Id = 'sys_fphp_table_sortDirection';
			$o_select->ValMessage = $o_glob->GetTranslation('rootSortDirectionValMessage', 0);
			$o_select->Options = array($o_glob->GetTranslation('rootSortDirectionAscending', 0) => 'true', $o_glob->GetTranslation('rootSortDirectionDescending', 0) => 'false');
			$o_select->Required = true;
			
			/* add manual created form element to genereal tab */
			if (!$o_glob->PostModalForm->AddFormElement($o_select, 'general', true)) {
				throw new forestException('Cannot add form element to tab with id[general].');
			}
			
			/* add validation rules for manual created form elements */
			$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule('sys_fphp_table_sortDirection', 'required', 'true'));
			
			/* delete StorageSpace-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_StorageSpace')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_StorageSpace].');
			}
			
			/* delete Unique-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Unique[]')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_Unique[]].');
			}
			
			/* delete Interval-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Interval')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_Interval].');
			}
			
			/* delete View-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_View[]')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_View[]].');
			}
			
			/* delete SortColumn-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_SortColumn')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_SortColumn].');
			}
			
			/* delete InfoColumns-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_InfoColumns')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_InfoColumns].');
			}
			
			/* delete InfoColumnsView-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_InfoColumnsView[]')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_InfoColumnsView[]].');
			}
			
			/* delete Versioning-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Versioning')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_Versioning].');
			}
			
			/* delete CheckoutInterval-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_CheckoutInterval')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_CheckoutInterval].');
			}
			
			/* delete Identifier-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Identifier')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_table_Identifier].');
			}
		} else {
			$s_sortOrder = '';
			
			if (array_key_exists($this->Twig->fphp_Table . '_sortDirection', $_POST)) {
				$s_sortOrder = strval($_POST[$this->Twig->fphp_Table . '_sortDirection']) . ';';
			} else {
				throw new forestException(0x10001402);
			}
			
			if (is_array($_POST[$this->Twig->fphp_Table . '_SortOrder'])) {
				/* post value is array, so we need to valiate multiple selected items */
				$s_sum = '';
				
				foreach ($_POST[$this->Twig->fphp_Table . '_SortOrder'] as $s_selectOptValue) {
					$s_sum .= strval($s_selectOptValue) . ';';
				}
				
				$s_sum = substr($s_sum, 0, -1);
				$s_sortOrder .= $s_sum;
			} else {
				$s_sortOrder .= strval($_POST[$this->Twig->fphp_Table . '_SortOrder']);
			}
			
			if (issetStr($this->Twig->SortOrder->PrimaryValue)) {
				$this->Twig->SortOrder = $this->Twig->SortOrder->PrimaryValue . ':' . $s_sortOrder;
			} else {
				$this->Twig->SortOrder = $s_sortOrder;
			}
			
			/* edit recrod */
			$i_result = $this->Twig->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				$o_glob->SystemMessages->Add(new forestException(0x10001406));
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001404));
				$s_nextAction = 'viewTwig';
				
				/* update twig file */
				$this->doTwigFile($this->Twig);
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}

	/**
	 * handle edit sort order record action for twig
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function editSortAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (! ($this->Twig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($this->Twig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if ($o_glob->IsPost) {
			$s_sortOrder = '';
		
			if (array_key_exists($this->Twig->fphp_Table . '_sortDirection', $_POST)) {
				$s_sortOrder = $_POST[$this->Twig->fphp_Table . '_sortDirection'] . ';';
			} else {
				throw new forestException(0x10001402);
			}
			
			if (is_array($_POST[$this->Twig->fphp_Table . '_SortOrder'])) {
				/* post value is array, so we need to validate multiple selected items */
				$s_sum = '';
				
				foreach ($_POST[$this->Twig->fphp_Table . '_SortOrder'] as $s_selectOptValue) {
					$s_sum .= strval($s_selectOptValue) . ';';
				}
				
				$s_sum = substr($s_sum, 0, -1);
				$s_sortOrder .= $s_sum;
			} else {
				$s_sortOrder .= strval($_POST[$this->Twig->fphp_Table . '_SortOrder']);
			}
			
			if (issetStr($this->Twig->SortOrder->PrimaryValue)) {
				$a_sortOrders = explode(':', $this->Twig->SortOrder->PrimaryValue);
				
				if ( (!array_key_exists('sys_fphp_sortOrderKey', $_POST)) || ($_POST['sys_fphp_sortOrderKey'] >= count($a_sortOrders)) ) {
					throw new forestException(0x10001405, array($this->Twig->fphp_Table));
				}
				
				$a_sortOrders[intval($_POST['sys_fphp_sortOrderKey'])] = $s_sortOrder;
				$this->Twig->SortOrder = implode(':', $a_sortOrders);
			} else {
				$this->Twig->SortOrder = $s_sortOrder;
			}
			
			/* edit record */
			$i_result = $this->Twig->UpdateRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				$o_glob->SystemMessages->Add(new forestException(0x10001406));
				$s_nextAction = 'viewTwig';
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001407));
				$s_nextAction = 'viewTwig';
				
				/* update twig file */
				$this->doTwigFile($this->Twig);
			}
		} else {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editKey'), 'editKey' );
		
			if ( ($o_glob->Temp->Exists('editKey')) && ($o_glob->Temp->{'editKey'} != null) ) {
				$o_forestdataTwig = new \fPHP\Twigs\forestdataTwig;
			
				if (! ($o_forestdataTwig->GetRecordPrimary(array('forestCombination'), array('Name'))) ) {
					throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
				}
				
				$s_forestCombinationUUID = $o_forestdataTwig->UUID;
				
				if (! ($o_forestdataTwig->GetRecordPrimary(array('forestLookup'), array('Name'))) ) {
					throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
				}
				
				$s_forestLookupUUID = $o_forestdataTwig->UUID;
				
				/* update lookup filter */
				$o_forestLookupData = new \fPHP\Helper\forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID, '!2ForestDataUUID' => $s_forestLookupUUID));
				$this->Twig->Unique->SetLookupData(new \fPHP\Helper\forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID)));
				$this->Twig->SortOrder->SetLookupData(new \fPHP\Helper\forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID, '!1ForestDataUUID' => $s_forestCombinationUUID)));
				$this->Twig->View->SetLookupData(new \fPHP\Helper\forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->UUID)));
				$this->Twig->SortColumn->SetLookupData($o_forestLookupData);
				
				/* get value */
				$a_sortOrders = explode(':', $this->Twig->SortOrder->PrimaryValue);
				
				if ($o_glob->Temp->{'editKey'} >= count($a_sortOrders)) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				$o_sortOrder = explode(';', $a_sortOrders[intval($o_glob->Temp->{'editKey'})]);
				
				$s_sortDirection = strval($o_sortOrder[0]);
				$this->Twig->SortOrder->PrimaryValue = $o_sortOrder[1];
				
				/* build modal form */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
				$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('EditModalTitle', 1);
				
				/* add current record key to modal form as hidden field */
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_sortOrderKey';
				$o_hidden->Value = strval($o_glob->Temp->{'editKey'});
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
				
				/* create manual form element for unique key name */
				$o_select = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::SELECT);
				$o_select->Label = $o_glob->GetTranslation('rootSortDirectionLabel', 0);
				$o_select->Id = 'sys_fphp_table_sortDirection';
				$o_select->ValMessage = $o_glob->GetTranslation('rootSortDirectionValMessage', 0);
				$o_select->Options = array($o_glob->GetTranslation('rootSortDirectionAscending', 0) => 'true', $o_glob->GetTranslation('rootSortDirectionDescending', 0) => 'false');
				$o_select->Required = true;
				$o_select->Value = $s_sortDirection;
				
				/* add manual created form element to genereal tab */
				if (!$o_glob->PostModalForm->AddFormElement($o_select, 'general', true)) {
					throw new forestException('Cannot add form element to tab with id[general].');
				}
				
				/* add validation rules for manual created form elements */
				$o_glob->PostModalForm->FormObject->ValRules->Add(new \fPHP\Forms\forestFormValidationRule('sys_fphp_table_sortDirection', 'required', 'true'));
				
				/* delete StorageSpace-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_StorageSpace')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_StorageSpace].');
				}
				
				/* delete SortOrder-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Unique[]')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_Unique[]].');
				}
				
				/* delete Interval-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Interval')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_Interval].');
				}
				
				/* delete View-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_View[]')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_View[]].');
				}
				
				/* delete SortColumn-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_SortColumn')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_SortColumn].');
				}
				
				/* delete InfoColumns-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_InfoColumns')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_InfoColumns].');
				}
				
				/* delete InfoColumnsView-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_InfoColumnsView[]')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_InfoColumnsView[]].');
				}
				
				/* delete Versioning-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Versioning')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_Versioning].');
				}
				
				/* delete CheckoutInterval-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_CheckoutInterval')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_CheckoutInterval].');
				}
				
				/* delete Identifier-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_table_Identifier')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_table_Identifier].');
				}
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/**
	 * handle delete sort order record action for twig
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function deleteSortAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (! ($this->Twig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($this->Twig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteKey'), 'deleteKey' );
		
			if ( ($o_glob->Temp->Exists('deleteKey')) && ($o_glob->Temp->{'deleteKey'} != null) ) {
				/* create modal confirmation form for deleting record */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig);
				$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
				$s_description = '<b>' . $o_glob->GetTranslation('DeleteModalDescriptionOne', 1) . '</b>';
				$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);
					
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_sortOrderKey';
				$o_hidden->Value = strval($o_glob->Temp->{'deleteKey'});
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
			}
		} else {
			/* delete record */
			if (array_key_exists('sys_fphp_sortOrderKey', $_POST)) {
				if (issetStr($this->Twig->SortOrder->PrimaryValue)) {
					$a_sortOrders = explode(':', $this->Twig->SortOrder->PrimaryValue);
					
					if ( (!array_key_exists('sys_fphp_sortOrderKey', $_POST)) || ($_POST['sys_fphp_sortOrderKey'] >= count($a_sortOrders)) ) {
						throw new forestException(0x10001405, array($this->Twig->fphp_Table));
					}
					
					unset($a_sortOrders[intval($_POST['sys_fphp_sortOrderKey'])]);
					
					if (count($a_sortOrders) > 0) {
						$this->Twig->SortOrder = implode(':', $a_sortOrders);
					} else {
						$this->Twig->SortOrder = 'NULL';
					}
				} else {
					$this->Twig->SortOrder = 'NULL';
				}
				
				/* edit record */
				$i_return = $this->Twig->UpdateRecord();
				
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
				
				$o_glob->SystemMessages->Add(new forestException(0x10001427));
				$s_nextAction = 'viewTwig';
				
				/* update twig file */
				$this->doTwigFile($this->Twig);
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}


	/**
	 * handle new twig sub constraint record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function newSubConstraintAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\subconstraintTwig;
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			/* add new branch record form */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
			
			/* delete TableUUID-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_subconstraint_TableUUID')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_subconstraint_TableUUID].');
			}
			
			/* delete View-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_subconstraint_View[]')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_subconstraint_View[]].');
			}
			
			/* delete Order-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_subconstraint_Order')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_subconstraint_Order].');
			}
			
			/* filter out all sytem tables starting with 'sys_fphp_' */
			$o_formElementSubTableUUID = $o_glob->PostModalForm->GetFormElementByFormId('sys_fphp_subconstraint_SubTableUUID');
			
			if ($o_formElementSubTableUUID != null) {
				/* retrieve existing subcontraints with current table */
				$a_assignedSubConstraints = array();
				$o_subconstraintTwig = new \fPHP\Twigs\subconstraintTwig;
			
				$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $o_tableTwig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				foreach (($o_subconstraintTwig->GetAllRecords(true))->Twigs as $o_subconstraint) {
					$a_assignedSubConstraints[] = $o_subconstraint->SubTableUUID->PrimaryValue;
				}
				$o_glob->Temp->Del('SQLAdditionalFilter');

				$a_options = $o_formElementSubTableUUID->Options;
				
				foreach ($a_options as $s_key => $s_value) {
					if ((\fPHP\Helper\forestStringLib::StartsWith($s_key, 'sys_fphp_')) || (in_array($s_value, $a_assignedSubConstraints))) {
						unset($a_options[$s_key]);
					}
				}
				
				$o_formElementSubTableUUID->Options = $a_options;
			}
		} else {
			/* check posted data for new sub constraint record */
			$this->TransferPOST_Twig();
			
			/* block any table which name starts with 'sys_fphp_' */
			if (\fPHP\Helper\forestStringLib::StartsWith($o_glob->TablesInformation[$this->Twig->SubTableUUID->PrimaryValue]['Name'], 'sys_fphp_')) {
				throw new forestException(0x10001F16);
			}
			
			/* test increase of next value, if it is set */
			if (issetStr($this->Twig->IdentifierStart)) {
				$s_identifierStart = \fPHP\Helper\forestStringLib::IncreaseIdentifier($this->Twig->IdentifierStart, $this->Twig->IdentifierIncrement);
				
				if ($s_identifierStart == 'INVALID') {
					throw new forestException(0x10001F13, array($this->Twig->IdentifierStart));
				} else if ($s_identifierStart == 'OVERFLOW') {
					throw new forestException(0x10001F14, array($this->Twig->IdentifierStart));
				}
			}
			
			/* add TableUUID value to record */
			$this->Twig->TableUUID = $o_tableTwig->UUID;
			
			$i_order = 1;
			$o_subconstraintTwig = new \fPHP\Twigs\subconstraintTwig;
			
			$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $o_tableTwig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			if ($o_subconstraintTwig->GetLastRecord()) {
				$i_order = $o_subconstraintTwig->Order + 1;
			}
			$o_glob->Temp->Del('SQLAdditionalFilter');
			
			/* add Order value to record */
			$this->Twig->Order = $i_order;
			
			/* insert record */
			$i_result = $this->Twig->InsertRecord();
			
			/* evaluate result */
			if ($i_result == -1) {
				throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
			} else if ($i_result == 0) {
				throw new forestException(0x10001402);
			} else if ($i_result == 1) {
				$o_glob->SystemMessages->Add(new forestException(0x10001404));
				$s_nextAction = 'viewTwig';
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}

	/**
	 * handle edit twig sub constraint record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function editSubConstraintAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\subconstraintTwig;
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if ($o_glob->IsPost) {
			/* query record */
			if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_subconstraintKey']))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}
			
			/* query sub table record */
			$o_subTableTwig = new \fPHP\Twigs\tableTwig;
			
			if (! ($o_subTableTwig->GetRecord(array($this->Twig->SubTableUUID->PrimaryValue))) ) {
				throw new forestException(0x10001401, array($o_subTableTwig->fphp_Table));
			} else {
				$s_oldIdentifierStart = $this->Twig->IdentifierStart;
				
				$this->TransferPOST_Twig();
				
				if ( ($s_oldIdentifierStart != $this->Twig->IdentifierStart) && (issetStr($this->Twig->IdentifierStart)) ) {
					/* check if we have sub records of current sub constraint */
					if (array_key_exists($o_saveTwig->fphp_TableUUID, $o_glob->SubConstraintsDictionary)) {
						foreach ($o_glob->SubConstraintsDictionary[$o_saveTwig->fphp_TableUUID] as $o_subconstraint) {
							/* look for sub constraint which matches table in forestCombination */
							if ($o_subconstraint->UUID == $this->Twig->UUID) {
								/* get all records of twig */
								$o_records = $o_saveTwig->GetAllRecords(true);
								
								/* iterate each record */
								foreach($o_records->Twigs as $o_record) {
									/* query all sub records of record of found sub constraint */
									$o_subRecords = $o_record->QuerySubRecords($o_subconstraint);
									
									if ($o_subRecords->Twigs->Count() > 0) {
										/* we can only add identifier column to sub constraint, if we have no sub records, because of unique issue within the sub record dataset */
										throw new forestException(0x10001F15);
									}
								}
							}
						}
					}
				}
				
				/* test increase of next value, if it is set */
				if (issetStr($this->Twig->IdentifierStart)) {
					$s_identifierStart = \fPHP\Helper\forestStringLib::IncreaseIdentifier($this->Twig->IdentifierStart, $this->Twig->IdentifierIncrement);
					
					if ($s_identifierStart == 'INVALID') {
						throw new forestException(0x10001F13, array($this->Twig->IdentifierStart));
					} else if ($s_identifierStart == 'OVERFLOW') {
						throw new forestException(0x10001F14, array($this->Twig->IdentifierStart));
					}
				}
				
				/* edit record */
				$i_result = $this->Twig->UpdateRecord();
				
				/* evaluate result */
				if ($i_result == -1) {
					throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
				} else if ($i_result == 0) {
					$o_glob->SystemMessages->Add(new forestException(0x10001406));
					$s_nextAction = 'viewTwig';
				} else if ($i_result == 1) {
					$o_glob->SystemMessages->Add(new forestException(0x10001407));
					$s_nextAction = 'viewTwig';
				}
			}
		} else {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editKey'), 'editKey' );
		
			if ( ($o_glob->Temp->Exists('editKey')) && ($o_glob->Temp->{'editKey'} != null) ) {
				/* query record */
				if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'editKey'}))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* query sub table record */
				$o_subTableTwig = new \fPHP\Twigs\tableTwig;
				
				if (! ($o_subTableTwig->GetRecord(array($this->Twig->SubTableUUID->PrimaryValue))) ) {
					throw new forestException(0x10001401, array($o_subTableTwig->fphp_Table));
				}
				
				/* update lookup filter */
				$this->Twig->View->SetLookupData(new \fPHP\Helper\forestLookupData('sys_fphp_tablefield', array('UUID'), array('FieldName'), array('TableUUID' => $this->Twig->SubTableUUID->PrimaryValue)));
				
				/* build modal form */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
				$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('EditModalTitle', 1);
				
				/* add current record key to modal form as hidden field */
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_subconstraintKey';
				$o_hidden->Value = strval($this->Twig->UUID);
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
				
				/* delete TableUUID-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_subconstraint_TableUUID')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_subconstraint_TableUUID].');
				}
				
				/* delete SubTableUUID-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_subconstraint_SubTableUUID')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_subconstraint_SubTableUUID].');
				}
				
				/* delete Order-element */
				if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_subconstraint_Order')) {
					throw new forestException('Cannot delete form element with Id[sys_fphp_subconstraint_Order].');
				}
				
				/* add current record order to modal form as hidden field */
				$o_hiddenOrder = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hiddenOrder->Id = 'sys_fphp_subconstraint_Order';
				$o_hiddenOrder->Value = strval($this->Twig->Order);
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hiddenOrder);
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/**
	 * handle action to change order of sub constraint records, moving one record up
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function moveUpSubConstraintAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\subconstraintTwig;
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $o_tableTwig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$this->MoveUpRecord();
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		$s_nextAction = 'viewTwig';
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/**
	 * handle action to change order of sub constraint records, moving one record down
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function moveDownSubConstraintAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\subconstraintTwig;
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $o_tableTwig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$this->MoveDownRecord();
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		$s_nextAction = 'viewTwig';
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/**
	 * handle delete twig sub constraint record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function deleteSubConstraintAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\subconstraintTwig;
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteKey'), 'deleteKey' );
		
			if ( ($o_glob->Temp->Exists('deleteKey')) && ($o_glob->Temp->{'deleteKey'} != null) ) {
				if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'deleteKey'}))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				/* create modal confirmation form for deleting record */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig);
				$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
				$s_description = '<b>' . $o_glob->GetTranslation('DeleteModalDescriptionOne', 1) . '</b>';
				$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);
				
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_subconstraintKey';
				$o_hidden->Value = strval($this->Twig->UUID);
				$o_glob->PostModalForm->FormElements->Add($o_hidden);
			}
		} else {
			/* delete record */
			if (array_key_exists('sys_fphp_subconstraintKey', $_POST)) {
				if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_subconstraintKey']))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
				
				/* check sub constraint relation before deletion */
				$this->checkSubConstraintBeforeDeletion($o_tableTwig, $this->Twig);
				
				/* query all head records of subconstraint */
				if (array_key_exists($this->Twig->TableUUID, $o_glob->TablesInformation)) {
					$s_headTable = $o_glob->TablesInformation[$this->Twig->TableUUID]['Name'];
					\fPHP\Helper\forestStringLib::RemoveTablePrefix($s_headTable);
					$s_foo = '\\fPHP\\Twigs\\' . $s_headTable . 'Twig';
					$o_headTwig = new $s_foo;
					
					/* query records */
					$o_records = $o_headTwig->GetAllRecords(true);
					
					foreach ($o_records->Twigs as $o_record) {
						/* query sub records of current sub constraint for each record */
						$o_subRecords = $o_record->QuerySubRecords($this->Twig);
						
						foreach ($o_subRecords->Twigs as $o_subRecord) {
							/* delete files of sub record */
							$o_filesTwig = new \fPHP\Twigs\filesTwig; 
							
							$a_sqlAdditionalFilter = array(array('column' => 'ForeignUUID', 'value' => $o_subRecord->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
							$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
							$o_files = $o_filesTwig->GetAllRecords(true);
							$o_glob->Temp->Del('SQLAdditionalFilter');
							
							foreach ($o_files->Twigs as $o_file) {
								$s_folder = substr(pathinfo($o_file->Name, PATHINFO_FILENAME), 6, 2);
									
								$s_path = '';
			
								if (count($o_glob->URL->Branches) > 0) {
									foreach($o_glob->URL->Branches as $s_value) {
										$s_path .= $s_value . '/';
									}
								}
								
								$s_path .= $o_glob->URL->Branch . '/';
								
								$s_path = './trunk/' . $s_path . 'fphp_files/' . $s_folder . '/';
								
								if (is_dir($s_path)) {
									if (file_exists($s_path . $o_file->Name)) {
										/* delete file */
										if (!(@unlink($s_path . $o_file->Name))) {
											throw new forestException(0x10001422, array($s_path . $o_file->Name));
										}
									}
								}
								
								/* delete file record */
								$i_return = $o_file->DeleteRecord();
								
								/* evaluate the result */
								if ($i_return <= 0) {
									throw new forestException(0x10001423);
								}
							}
							
							/* delete sub record */
							$i_return = $o_subRecord->DeleteRecord();
								
							/* evaluate the result */
							if ($i_return <= 0) {
								throw new forestException(0x10001423);
							}
						}
					}
				}
				
				/* delete tablefield records */
				$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
				
				$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $this->Twig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				
				foreach ($o_tablefields->Twigs as $o_tablefield) {
					/* delete tablefield validationrule records */
					$o_tablefield_validationruleTwig = new \fPHP\Twigs\tablefield_validationruleTwig;
					
					$a_sqlAdditionalFilter = array(array('column' => 'TablefieldUUID', 'value' => $o_tablefield->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
					$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
					$o_tablefield_validationrules = $o_tablefield_validationruleTwig->GetAllRecords(true);
					$o_glob->Temp->Del('SQLAdditionalFilter');
					
					foreach ($o_tablefield_validationrules->Twigs as $o_tablefield_validationrule) {
						/* delete tablefield validationrule record */
						$i_return = $o_tablefield_validationrule->DeleteRecord();
					
						/* evaluate the result */
						if ($i_return <= 0) {
							throw new forestException(0x10001423);
						}
					}
					
					/* delete translation records */
					preg_match_all('/\#([^#]+)\#/', $o_tablefield->JSONEncodedSettings, $a_matches);
			
					if (count($a_matches) > 1) {
						$o_translationTwig = new \fPHP\Twigs\translationTwig;
						
						foreach ($a_matches[1] as $s_match) {
							if ($o_translationTwig->GetRecordPrimary(array($o_glob->URL->BranchId, $s_match), array('BranchId', 'Name'))) {
								/* delete translation record */
								$i_return = $o_translationTwig->DeleteRecord();
							
								/* evaluate the result */
								if ($i_return <= 0) {
									throw new forestException(0x10001423);
								}
							}
						}
					}
					
					/* delete tablefield record */
					$i_return = $o_tablefield->DeleteRecord();
				
					/* evaluate the result */
					if ($i_return <= 0) {
						throw new forestException(0x10001423);
					}
				}
				
				/* delete record */
				$i_return = $this->Twig->DeleteRecord();
				
				/* evaluate the result */
				if ($i_return <= 0) {
					throw new forestException(0x10001423);
				}
				
				$o_glob->SystemMessages->Add(new forestException(0x10001427));
				$s_nextAction = 'viewTwig';
			}
		}
				
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}

	/**
	 * check twigfield relation to other elements
	 *
	 * @param tableTwig $p_o_table  table record of table sys_fphp_table
	 * @param subconstraintTwig $p_o_subconstraint  sub contraint record of table sys_fphp_subcontraint
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function checkSubConstraintBeforeDeletion(\fPHP\Twigs\tableTwig $p_o_table, \fPHP\Twigs\subconstraintTwig $p_o_subconstraint) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$o_forestdataTwig = new \fPHP\Twigs\forestdataTwig;
		
		if (! ($o_forestdataTwig->GetRecordPrimary(array('forestCombination'), array('Name'))) ) {
			throw new forestException(0x10001401, array($o_forestdataTwig->fphp_Table));
		}
		
		$s_forestCombinationUUID = $o_forestdataTwig->UUID;
		
		/* look for forestCombination tablefields */
		$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
		
		$a_sqlAdditionalFilter = array(array('column' => 'ForestDataUUID', 'value' => $s_forestCombinationUUID, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		$s_table = strval($p_o_subconstraint->SubTableUUID);
		
		foreach ($o_tablefields->Twigs as $o_tablefield) {
			/* get json encoded settings as array */
			$s_JSONEncodedSettings = str_replace('&quot;', '"', $o_tablefield->JSONEncodedSettings);
			$a_settings = json_decode($s_JSONEncodedSettings, true);
			
			/* check if json encoded settings are valid */
			if ($a_settings == null) {
				throw new forestException(0x10001F02, array($s_JSONEncodedSettings));
			}
			
			if (array_key_exists('forestCombination', $a_settings)) {
				if ( (strpos($a_settings['forestCombination'], $s_table . '$') !== false) && ($o_tablefield->TableUUID->PrimaryValue == $p_o_table->UUID) ) {
					/* tablefield is used in forestCombination of higher table(parameter table) */
					throw new forestException(0x10001F09, array($s_table, $o_tablefield->FieldName, strval($p_o_table->Name)));
				}
			}
		}
	}
	
	
	/**
	 * handle new twig validation rule record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function newValidationRuleAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\tablefield_validationruleTwig;
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'viewKey'), 'viewKey' );
		
			if ( ($o_glob->Temp->Exists('viewKey')) && ($o_glob->Temp->{'viewKey'} != null) ) {
				/* query tablefield record */
				if (! ($o_tablefieldTwig->GetRecord(array($o_glob->Temp->{'viewKey'}))) ) {
					throw new forestException(0x10001401, array($o_tablefieldTwig->fphp_Table));
				}
				
				/* add new branch record form */
				$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
				$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('NewModalTitle', 1);
				
				/* add tablefield record key to modal form as hidden field */
				$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
				$o_hidden->Id = 'sys_fphp_tablefieldKey';
				$o_hidden->Value = strval($o_tablefieldTwig->UUID);
				
				$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
			}
		} else {
			if (array_key_exists('sys_fphp_tablefieldKey', $_POST)) {
				/* query tablefield record */
				if (! ($o_tablefieldTwig->GetRecord(array($_POST['sys_fphp_tablefieldKey']))) ) {
					throw new forestException(0x10001401, array($o_tablefieldTwig->fphp_Table));
				}
				
				/* check posted data for new tablefield_validationrule record */
				$this->TransferPOST_Twig();
				
				if (issetStr($this->Twig->ValidationruleUUID->PrimaryValue)) {
					$o_validationruleTwig = new \fPHP\Twigs\validationruleTwig;
					$o_formelement_validationruleTwig = new \fPHP\Twigs\formelement_validationruleTwig;
					
					if (! ($o_validationruleTwig->GetRecordPrimary(array('any'), array('Name'))) ) {
						throw new forestException(0x10001401, array($o_validationruleTwig->fphp_Table));
					}
					
					if (! $o_formelement_validationruleTwig->GetRecord(array($o_tablefieldTwig->FormElementUUID->PrimaryValue, $o_validationruleTwig->UUID))) {
						if (! $o_formelement_validationruleTwig->GetRecord(array($o_tablefieldTwig->FormElementUUID->PrimaryValue, $this->Twig->ValidationruleUUID->PrimaryValue))) {
							throw new forestException(0x10001F0F, array($o_tablefieldTwig->FormElementUUID, $this->Twig->ValidationruleUUID));
						}
					}
				}
				
				/* add TableUUID value to record */
				$this->Twig->TablefieldUUID = $o_tablefieldTwig->UUID;
				
				/* insert record */
				$i_result = $this->Twig->InsertRecord();
				
				/* evaluate result */
				if ($i_result == -1) {
					throw new forestException(0x10001403, array($o_glob->Temp->{'UniqueIssue'}));
				} else if ($i_result == 0) {
					throw new forestException(0x10001402);
				} else if ($i_result == 1) {
					$o_glob->SystemMessages->Add(new forestException(0x10001404));
					$s_nextAction = 'viewTwigField';
				}
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}

	/**
	 * handle edit twig validation rule record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function editValidationRuleAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\tablefield_validationruleTwig;
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if ($o_glob->IsPost) {
			/* query tablefield record */
			if (array_key_exists('sys_fphp_tablefieldKey', $_POST)) {
				if (! ($o_tablefieldTwig->GetRecord(array($_POST['sys_fphp_tablefieldKey']))) ) {
					throw new forestException(0x10001401, array($o_tablefieldTwig->fphp_Table));
				}
				
				/* query record */
				if (array_key_exists('sys_fphp_tablefield_validationruleKey', $_POST)) {
					if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_tablefield_validationruleKey']))) ) {
						throw new forestException(0x10001401, array($this->Twig->fphp_Table));
					}
					
					/* check posted data for tablefield_validationrule record */
					$this->TransferPOST_Twig();
					
					if (issetStr($this->Twig->ValidationruleUUID->PrimaryValue)) {
						$o_validationruleTwig = new \fPHP\Twigs\validationruleTwig;
						$o_formelement_validationruleTwig = new \fPHP\Twigs\formelement_validationruleTwig;
						
						if (! ($o_validationruleTwig->GetRecordPrimary(array('any'), array('Name'))) ) {
							throw new forestException(0x10001401, array($o_validationruleTwig->fphp_Table));
						}
						
						if (! $o_formelement_validationruleTwig->GetRecord(array($o_tablefieldTwig->FormElementUUID->PrimaryValue, $o_validationruleTwig->UUID))) {
							if (! $o_formelement_validationruleTwig->GetRecord(array($o_tablefieldTwig->FormElementUUID->PrimaryValue, $this->Twig->ValidationruleUUID->PrimaryValue))) {
								throw new forestException(0x10001F0F, array($o_tablefieldTwig->FormElementUUID, $this->Twig->ValidationruleUUID));
							}
						}
					}
					
					/* edit record */
					$i_result = $this->Twig->UpdateRecord();
					
					/* evaluate result */
					if ($i_result == -1) {
						throw new forestException(0x10001405, array($o_glob->Temp->{'UniqueIssue'}));
					} else if ($i_result == 0) {
						$o_glob->SystemMessages->Add(new forestException(0x10001406));
						$s_nextAction = 'viewTwigField';
					} else if ($i_result == 1) {
						$o_glob->SystemMessages->Add(new forestException(0x10001407));
						$s_nextAction = 'viewTwigField';
					}
				}
			}
		} else {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'viewKey'), 'viewKey' );
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'editKey'), 'editKey' );
		
			if ( ($o_glob->Temp->Exists('editKey')) && ($o_glob->Temp->{'editKey'} != null) ) {
				/* query record */
				if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'editKey'}))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}
				
				if ( ($o_glob->Temp->Exists('viewKey')) && ($o_glob->Temp->{'viewKey'} != null) ) {
					/* query tablefield record */
					if (! ($o_tablefieldTwig->GetRecord(array($o_glob->Temp->{'viewKey'}))) ) {
						throw new forestException(0x10001401, array($o_tablefieldTwig->fphp_Table));
					}
					
					/* build modal form */
					$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
					$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('EditModalTitle', 1);
					
					/* add tablefield record key to modal form as hidden field */
					$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
					$o_hidden->Id = 'sys_fphp_tablefieldKey';
					$o_hidden->Value = strval($o_tablefieldTwig->UUID);
					
					$o_glob->PostModalForm->FormFooterElements->Add($o_hidden);
					
					/* add current record key to modal form as hidden field */
					$o_hidden2 = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
					$o_hidden2->Id = 'sys_fphp_tablefield_validationruleKey';
					$o_hidden2->Value = strval($this->Twig->UUID);
					
					$o_glob->PostModalForm->FormFooterElements->Add($o_hidden2);
				}
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}
	
	/**
	 * handle delete twig validation rule record action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function deleteValidationRuleAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ($this->Twig->fphp_SystemTable) {
			throw new forestException(0x10001F16);
		}
		
		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\tablefield_validationruleTwig;
		$o_tableTwig = new \fPHP\Twigs\tableTwig;
		$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
		
		/* query table record */
		if (! ($o_tableTwig->GetRecord(array($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['Table']->PrimaryValue))) ) {
			throw new forestException(0x10001401, array($o_tableTwig->fphp_Table));
		}
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'viewKey'), 'viewKey' );
			$o_glob->Temp->Add( get($o_glob->URL->Parameters, 'deleteKey'), 'deleteKey' );
			
			if ( ($o_glob->Temp->Exists('viewKey')) && ($o_glob->Temp->{'viewKey'} != null) ) {
				if (! ($o_tablefieldTwig->GetRecord(array($o_glob->Temp->{'viewKey'}))) ) {
					throw new forestException(0x10001401, array($o_tablefieldTwig->fphp_Table));
				}
				
				if ( ($o_glob->Temp->Exists('deleteKey')) && ($o_glob->Temp->{'deleteKey'} != null) ) {
					if (! ($this->Twig->GetRecord(array($o_glob->Temp->{'deleteKey'}))) ) {
						throw new forestException(0x10001401, array($this->Twig->fphp_Table));
					}
					
					/* create modal confirmation form for deleting record */
					$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig);
					$s_title = $o_glob->GetTranslation('DeleteModalTitle', 1);
					$s_description = '<b>' . $o_glob->GetTranslation('DeleteModalDescriptionOne', 1) . '</b>';
					$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);	
					
					$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
					$o_hidden->Id = 'sys_fphp_tablefieldKey';
					$o_hidden->Value = strval($o_tablefieldTwig->UUID);
					$o_glob->PostModalForm->FormElements->Add($o_hidden);
					
					$o_hidden2 = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
					$o_hidden2->Id = 'sys_fphp_tablefield_validationruleKey';
					$o_hidden2->Value = strval($this->Twig->UUID);
					$o_glob->PostModalForm->FormElements->Add($o_hidden2);
				}
			}
		} else {
			if (array_key_exists('sys_fphp_tablefieldKey', $_POST)) {
				if (! ($o_tablefieldTwig->GetRecord(array($_POST['sys_fphp_tablefieldKey']))) ) {
					throw new forestException(0x10001401, array($o_tablefieldTwig->fphp_Table));
				}
				
				if (array_key_exists('sys_fphp_tablefield_validationruleKey', $_POST)) {
					if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_tablefield_validationruleKey']))) ) {
						throw new forestException(0x10001401, array($this->Twig->fphp_Table));
					}
					
					/* delete record */
					$i_return = $this->Twig->DeleteRecord();
					
					/* evaluate the result */
					if ($i_return <= 0) {
						throw new forestException(0x10001423);
					}
					
					$o_glob->SystemMessages->Add(new forestException(0x10001427));
					$s_nextAction = 'viewTwigField';
				}
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}

	
	/**
	 * handle create static page action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function createStaticPageAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ( ($this->Twig != null) && ($this->Twig->fphp_SystemTable) ) {
			throw new forestException(0x10001F16);
		}

		/* we must check if we can create a static page from current branch */
		$b_canCreateStaticPage = false;
		/* get standard view record for 'static page' */
		$o_standardviewsTwig = new \fPHP\Twigs\standardviewsTwig();
		
		if (!$o_standardviewsTwig->GetRecordPrimary(array('Static Page'), array('Name'))) {
			throw new forestException(0x10001401, array('\'standard view\' with name \'Static Page\''));
		}

		/* create branch twig */
		$o_branchTwig = new \fPHP\Twigs\branchTwig();

		/* get all child branches from current branch */
		$a_sqlAdditionalFilter = array(array('column' => 'ParentBranch', 'value' => $o_glob->URL->BranchId, 'operator' => '=', 'filterOperator' => 'AND'));
		$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
		$o_branchChildren = $o_branchTwig->GetAllRecords(true);
		$o_glob->Temp->Del('SQLAdditionalFilter');
		
		/* check if there are any children */
		if ($o_branchChildren->Twigs->Count() > 0) {
			/* iterate current branch children */
			foreach ($o_branchChildren->Twigs as $o_branchChild) {
				/* check if a child has 'static page' as standard view */
				if ($o_branchChild->StandardView->PrimaryValue == $o_standardviewsTwig->UUID) {
					/* in that case, we can create a static page for current branch */
					$b_canCreateStaticPage = true;
					break;
				}
			}
		}
		
		if (!$b_canCreateStaticPage) {
			throw new forestException(0x10001401, array('no branch child has \'static page\' as \'standard view\''));
		}

		$s_nextAction = 'init';
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\branchTwig;
		
		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');

		if (!$this->Twig->GetRecord(array($o_glob->URL->BranchId))) {
			throw new forestException(0x10001401, array('branch id \'' . $o_glob->URL->BranchId . '\''));
		}
		
		if (!$o_glob->IsPost) {
			/* add new branch record form */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig, true);
			$o_glob->PostModalForm->FormModalConfiguration->ModalTitle = $o_glob->GetTranslation('rootCreateStaticPageTitle', 0);
			
			/* delete StorageSpace-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_StorageSpace')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_StorageSpace].');
			}

			/* delete Name-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_Name')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_Name].');
			}

			/* delete Title-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_Title')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_Title].');
			}

			/* delete Navigation-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_Navigation')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_Navigation].');
			}

			/* delete Filename-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_Filename')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_Filename].');
			}

			/* delete StandardView-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_StandardView')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_StandardView].');
			}

			/* delete Filter-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_Filter')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_Filter].');
			}

			/* delete KeepFilter-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_KeepFilter')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_KeepFilter].');
			}

			/* delete NavigationOrder-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_NavigationOrder')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_NavigationOrder].');
			}

			/* delete PermissionInheritance-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_PermissionInheritance')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_PermissionInheritance].');
			}

			/* delete MaintenanceMode-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_MaintenanceMode')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_MaintenanceMode].');
			}

			/* delete MaintenanceModeMessage-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_MaintenanceModeMessage')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_MaintenanceModeMessage].');
			}

			/* delete Template-element */
			if (!$o_glob->PostModalForm->DeleteFormElementByFormId('sys_fphp_branch_Template')) {
				throw new forestException('Cannot delete form element with Id[sys_fphp_branch_Template].');
			}

			/* add branch child lookup to form */
			$o_branchLookup = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::LOOKUP);
			$o_branchLookup->Id = 'sys_fphp_branch_child';
			$o_branchLookup->Label = 'Source for static page:';
			$o_branchLookup->ValMessage = 'Please choose a branch child as source.';
			$o_branchLookupData = new \fPHP\Helper\forestLookupData('sys_fphp_branch', array('Id'), array('Name','Title'), array('ParentBranch' => $o_glob->URL->BranchId, 'StandardView' => $o_standardviewsTwig->UUID));
			$o_branchLookup->Options = $o_branchLookupData->CreateOptionsArray();
			$o_branchLookup->Required = true;
			
			$o_glob->PostModalForm->AddFormElement($o_branchLookup);

			/* add logo present choice to form */
			$o_logoPresent = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::CHECKBOX);
			$o_logoPresent->Id = 'sys_fphp_logo_present';
			$o_logoPresent->Label = ' ';
			$o_logoPresent->CheckboxContainerClass = 'form-check form-switch position-relative';
			$o_logoPresent->Options = array($o_glob->GetTranslation('ShowLogo', 1) => '1');
			
			$o_glob->PostModalForm->AddFormElement($o_logoPresent);
		} else {
			/* check if a branch child has been selected */
			if (!array_key_exists('sys_fphp_branch_child', $_POST)) {
				$o_glob->SystemMessages->Add(new forestException(0x10001408, array('sys_fphp_branch_child')));
			} else {
				/* get branch name of selected child */
				$s_branchName = $o_glob->BranchTree['Id'][$_POST['sys_fphp_branch_child']]['Name'];

				/* parameter array for link */
				$a_parameters = array('link' => 'no');

				/* if option logo present is checked, add parameter to array */
				if ( (array_key_exists('sys_fphp_logo_present', $_POST)) && ($_POST['sys_fphp_logo_present'] == '1') ) {
					$a_parameters['logo'] = 'yes';
				} else {
					$a_parameters['logo'] = 'no';
				}

				/* create link for content of static page */
				$s_link = explode("/index.php?", $_SERVER['HTTP_REFERER'])[0] . substr(\fPHP\Helper\forestLink::Link($s_branchName, 'init', $a_parameters), 1);
				
				/* request need current session cookie to get content */
				$opts = array('http' => array('header'=> 'Cookie: ' . $_SERVER['HTTP_COOKIE']."\r\n"));
				$context = stream_context_create($opts);
				/* get content for static page */
				$s_getContents = @file_get_contents($s_link, false, $context);
				
				/* check if request was successful */
				if ($s_getContents === false) {
					$o_glob->SystemMessages->Add(new forestException(0x10001424, array($s_link)));
					$this->CreateLogEntry('could not get content from file: ' . $s_link);
				} else {
					/* correct paths */
					$s_getContents = str_replace('./', '../', $s_getContents);

					/* check if static page already exists */
					if (\fPHP\Roots\forestAutoLoad::IsReadable('./files/' . $o_glob->URL->Branch . '.html')) {
						/* create backup */
						copy('./files/' . $o_glob->URL->Branch . '.html', './files/bkp_' . $o_glob->URL->Branch . '_' . date("Ymd-His") . '.html');
					}

					/* store content to static page file */
					if (file_put_contents('./files/' . $o_glob->URL->Branch . '.html', $s_getContents) === false) {
						$this->CreateLogEntry('could not write content to file: ' . './files/' . $o_glob->URL->Branch . '.html');
						$o_glob->SystemMessages->Add(new forestException(0x10001425, array($o_glob->URL->Branch . '.html')));
					} else {
						$this->CreateLogEntry('created static page: ' . $o_glob->URL->Branch . '.html');
						$o_glob->SystemMessages->Add(new forestException(0x10001426));
					}
				}
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction($s_nextAction);
	}

	/**
	 * handle delete static page action
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access protected
	 * @static no
	 */
	protected function deleteStaticPageAction() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		/* check if standard twig is a system object */
		if ( ($this->Twig != null) && ($this->Twig->fphp_SystemTable) ) {
			throw new forestException(0x10001F16);
		}
		
		$o_saveTwig = $this->Twig;
		$this->Twig = new \fPHP\Twigs\branchTwig;

		$this->HandleFormKey($o_glob->URL->Branch . $o_glob->URL->Action . 'Form');
		
		if (!$o_glob->IsPost) {
			if (! ($this->Twig->GetRecord(array($o_glob->URL->BranchId))) ) {
				throw new forestException(0x10001401, array($this->Twig->fphp_Table));
			}

			/* create modal confirmation form for deleting record */
			$o_glob->PostModalForm = new \fPHP\Forms\forestForm($this->Twig);
			$s_title = $o_glob->GetTranslation('rootDeleteStaticPageTitle', 0);
			$s_description = '<b>' . $o_glob->GetTranslation('DeleteModalDescriptionFile', 1) . '</b>';
			$o_glob->PostModalForm->CreateDeleteModalForm($this->Twig, $s_title, $s_description);

			$o_hidden = new \fPHP\Forms\forestFormElement(\fPHP\Forms\forestFormElement::HIDDEN);
			$o_hidden->Id = 'sys_fphp_branchKey';
			$o_hidden->Value = strval($o_glob->URL->BranchId);
			$o_glob->PostModalForm->FormElements->Add($o_hidden);
		} else {
			if (array_key_exists('sys_fphp_branchKey', $_POST)) {
				if (! ($this->Twig->GetRecord(array($_POST['sys_fphp_branchKey']))) ) {
					throw new forestException(0x10001401, array($this->Twig->fphp_Table));
				}

				/* if we cannot find static page file */
				if (!\fPHP\Roots\forestAutoLoad::IsReadable('./files/' . $o_glob->URL->Branch . '.html')) {
					$this->CreateLogEntry('static page could not be found: ./files/' . $o_glob->URL->Branch . '.html');
					throw new forestException('Cannot find file [%0].', array($o_branchTwig->Name . 'Branch.php'));
				}
				
				/* if we cannot delete static page file */
				if (!(@unlink('./files/' . $o_glob->URL->Branch . '.html'))) {
					$this->CreateLogEntry('static page could not be deleted: ./files/' . $o_glob->URL->Branch . '.html');
					throw new forestException(0x10001422, array('./files/' . $o_glob->URL->Branch . '.html'));
				}

				$this->CreateLogEntry('deleted static page: ./files/' . $o_glob->URL->Branch . '.html');
				$o_glob->SystemMessages->Add(new forestException(0x10001427));
			}
		}
		
		$this->Twig = $o_saveTwig;
		$this->SetNextAction('init');
	}
}
?>