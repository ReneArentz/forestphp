<?php
/**
 * central control class of forestPHP framework for fetch-content and render-content
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestphp.de>
 * @copyright   (c) 2019 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     0.9.0 beta
 * @link        http://www.forestphp.de/
 * @object-id   0x1 00001
 * @since       File available since Release 0.1.0 alpha
 * @deprecated  -
 *
 * @version log Version		Developer	Date		Comment
 * 		0.1.0 alpha	renatus		2019-08-04	first build
 * 		0.1.1 alpha	renatus		2019-08-09	added trunk, form and systemmessages functionality
 * 		0.1.2 alpha	renatus		2019-08-23	added list and view rendering
 * 		0.1.4 alpha	renatus		2019-09-26	added fphp_upload and fphp_upload_delete to fast-processing actions
 * 		0.1.5 alpha	renatus		2019-10-09	added fphp_captcha and fphp_imageThumbnail to fast-processing actions
 * 		0.4.0 beta	renatus		2019-11-20	added permission checks, user and guest access
 * 		0.7.0 beta	renatus		2020-01-02	added Maintenance Mode
 * 		0.8.0 beta	renatus		2020-01-16	added fphp_flex functionality and log entry on permission denied message
 * 		0.9.0 beta	renatus		2020-01-30	changed render structure
 */

namespace fPHP\Roots;

use \fPHP\Roots\forestException as forestException;

class forestPHP {
	/* Fields */
	
	/* Properties */
	
	/* Methods */
	
	/**
	 * constructor of forestPHP framework
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __construct() {
		global $o_main_exception;
		global $b_write_url_info;
		global $b_write_security_debug;
		global $b_write_post_files;
		
		try {
			/* initialize AutoLoad-routine for classes */
			if (!(@include_once './roots/forestAutoLoad.php')) {
				throw new \Exception('Cannot find forestAutoLoad.php');
			}
			
			/* apply forestAutoLoad class for autoload functionality */
			spl_autoload_register(function ($p_s_className) {
				$foo = new \fPHP\Roots\forestAutoLoad($p_s_className);
			});

			try {
				/* fphp core executions */
				$o_glob = \fPHP\Roots\forestGlobals::init();
				
				if ($_POST) {
					$o_glob->IsPost = true;
				}
				
				/* these actions are using fast processing, which means minimum execution of fphp system functions */
				if (($o_glob->URL->Action == 'fphp_upload') || ($o_glob->URL->Action == 'fphp_upload_delete') || ($o_glob->URL->Action == 'fphp_captcha') || ($o_glob->URL->Action == 'fphp_imageThumbnail') || ($o_glob->URL->Action == 'fphp_updateFlex')) {
					$o_glob->FastProcessing = true;
				}
				
				$o_glob->Base->Add(new \fPHP\Base\forestBase(\fPHP\Base\forestBase::MariaSQL, '127.0.0.1', 'forestphp', 'root', 'root'), 'forestPHPMariaSQLBase');
				$o_glob->ActiveBase = 'forestPHPMariaSQLBase';
				
				/* load trunk record with fphp system settings */
				$o_trunk = new \fPHP\Twigs\trunkTwig;
				
				if (!$o_trunk->GetRecord(array(1))) {
					throw new forestException('Could not read trunk system table');
				}
				
				$o_glob->Trunk = $o_trunk;
				unset($o_trunk);
				
				/* call global functions */
				$o_glob->BuildBranchTree();
				$o_glob->URL->RetrieveInformationByURL($b_write_url_info);
				
				$o_glob->Security->init($b_write_security_debug);
				$o_glob->Security->ListUserPermissions();
				
				/* handle maintenance mode, except for root user */
				if (!$o_glob->Security->RootUser) {
					/* maintenance mode on trunk level */
					if ($o_glob->Trunk->MaintenanceMode) {
						if ( !( ($o_glob->URL->BranchId == 1) && ( (!issetStr($o_glob->URL->Action)) || ($o_glob->URL->Action == 'init') || ($o_glob->URL->Action == 'login') || ($o_glob->URL->Action == 'logout') ) ) ) {
							throw new \Exception($o_glob->Trunk->MaintenanceModeMessage);
						}
					}
					/* maintenance mode on branch level */
					else if ($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['MaintenanceMode']) {
						if ( !( ($o_glob->URL->BranchId == 1) && ( (!issetStr($o_glob->URL->Action)) || ($o_glob->URL->Action == 'init') || ($o_glob->URL->Action == 'login') || ($o_glob->URL->Action == 'logout') ) ) ) {
							throw new \Exception($o_glob->BranchTree['Id'][$o_glob->URL->BranchId]['MaintenanceModeMessage']);
						}
					}
				}
				
				if (!$o_glob->FastProcessing) {
					$o_glob->ListTranslations();
					$o_glob->ListTables();
					$o_glob->ListUserNames();
				}
				
				if (($b_write_post_files) && (!$o_glob->FastProcessing)) {
					echo '<pre>POST<br>';
					print_r($_POST);
					echo '</pre><hr>';
					
					echo '<pre>FILES<br>';
					print_r($_FILES);
					echo '</pre><hr>';
				}
				
				/* init forestPHP framework */
				$this->init();
				
				$o_glob->Security->SyncSessionData($b_write_security_debug);
			} catch (forestException $o_exc) {
				/* deactivate output buffer for content scripting */
				ob_end_flush();
				
				$o_glob = \fPHP\Roots\forestGlobals::init();
				
				global $b_transaction_active;
				if ($b_transaction_active) {
					$o_glob->Base->{$o_glob->ActiveBase}->ManualRollBack();
				}
				
				if (!(@include './trunk/trunkHeadLeaf.php')) {
					echo '<body>' . "\n";
					echo "\t" . '<h1>FATAL ERROR</h1>' . "\n";
				}
				
				echo "\t" . '<div class="container">' . "\n";
				echo "\t" . "\t" . $o_exc;
				echo "\t" . '</div>' . "\n";
				echo '</body>' . "\n";
				echo '</html>' . "\n";
			}
		} catch (\Exception $o_exc) {
			/* deactivate output buffer for content scripting */
			ob_end_flush();
			
			$o_main_exception = $o_exc;
			
			if (!(@include './roots/forestMaintenance.php')) {
				echo '<body>' . "\n";
					echo "\t" . '<h1>FATAL ERROR</h1>' . "\n";
				echo '</body>' . "\n";
				echo '</html>' . "\n";
			}
		}
	}
	
	/**
	 * init function to separate fetching content and rendering output of forestPHP framework
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
	public function init() {
		/* run content scripting */
		$this->FetchContent();
		
		/* deactivate output buffer for content scripting */
		ob_end_flush();
		
		/* render content */
		$this->Render();
	}
	
	/**
	 * FetchContent function with permission check and branch initialisation
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function FetchContent() {
		try {
			$o_glob = \fPHP\Roots\forestGlobals::init();
			
			if ($o_glob->Security->InitAccess) {
				/* do something if session was just created */
			} else if (($o_glob->Security->GuestAccess) || ($o_glob->Security->UserAccess)) {
				if (!$o_glob->Security->CheckUserPermission()) {
					$o_logTwig = new \fPHP\Twigs\logTwig;
		
					$o_logTwig->Branch = $o_glob->URL->Branch;
					$o_logTwig->Action = $o_glob->URL->Action;
					$o_logTwig->Session = $o_glob->Security->SessionUUID;
					$o_logTwig->Event = 'permission denied';
					$o_logTwig->Created = new \fPHP\Helper\forestDateTime;
					$o_logTwig->CreatedBy = $o_glob->Security->UserUUID;
					
					/* insert record */
					$i_result = $o_logTwig->InsertRecord();
		
					throw new forestException(0x10000100);
				}
				
				/* call branch content */
				$s_foo = '\\fPHP\Branches\\' . $o_glob->URL->Branch . 'Branch';
				$o_foo = new $s_foo;
			}
		} catch (forestException $o_exc) {
			if (($o_exc->ExceptionType == forestException::WARNING) || ($o_exc->ExceptionType == forestException::MESSAGE)) {
				$o_glob->SystemMessages->Add($o_exc);
				
				global $b_transaction_active;
				if ($b_transaction_active) {
					$o_glob->Base->{$o_glob->ActiveBase}->ManualRollBack();
				}
			} else {
				throw $o_exc;
			}
		}
	}
	
	/**
	 * Render function to display header, content and footer
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	private function Render() {
		try {
			$o_glob = \fPHP\Roots\forestGlobals::init();
			
			if (!$o_glob->FastProcessing) {
				if ($o_glob->Security->InitAccess) {
					/* render init-leaf */
					$this->RenderLeaf('initLeaf');
				} else if (($o_glob->Security->GuestAccess) || ($o_glob->Security->UserAccess)) {
					/* render trunk-head-leaf */
					$this->RenderLeaf('trunkHeadLeaf');
					
					/* render navigation */
					if ($o_glob->URL->ShowNavigation) {
						$o_glob->Navigation->RenderNavigation();
					}
					
					/* render main container */
					echo '<div class="container-fluid">' . "\n";
					
						/* render branch title */
						if (!is_null($o_glob->URL->BranchTitle)) {
							echo '<h1>' . $o_glob->URL->BranchTitle . '</h1>' . "\n";
						}
						
						$b_warning = false;
						$o_warningMessage = null;
						
						/* iterate all system messages to get latest warning message */
						foreach ($o_glob->SystemMessages as $o_systemMessage) {
							if ($o_systemMessage->ExceptionType == forestException::WARNING) {
								$b_warning = true;
								$o_warningMessage = $o_systemMessage;
							}
						}
						
						if ($b_warning) {
							/* render system warning message */
							echo $o_warningMessage;
						} else {
							/* render system messages */
							foreach ($o_glob->SystemMessages as $o_systemMessage) {
								echo $o_systemMessage;
							}

							/* generate path for rendering branch leaf */
							$s_path = '';
						
							if (count($o_glob->URL->Branches) > 0) {
								foreach($o_glob->URL->Branches as $s_value) {
									$s_path .= $s_value . '/';
								}
							}
							
							$s_path .= $o_glob->URL->Branch . '/';
							
							/* render branch leaf if it is set */
							if (issetStr($o_glob->Leaf)) {
								$this->RenderLeaf($o_glob->Leaf, $s_path);
							} else {
								/* render standard template if it is set, LandingPage, ListView or BranchView */
								if ($o_glob->Templates->Exists($o_glob->URL->Branch . 'LandingPage')) {
									echo $o_glob->Templates->{$o_glob->URL->Branch . 'LandingPage'};
								} else if ($o_glob->Templates->Exists($o_glob->URL->Branch . 'ListView')) {
									echo $o_glob->Templates->{$o_glob->URL->Branch . 'ListView'};
								} else if ($o_glob->Templates->Exists($o_glob->URL->Branch . 'View')) {
									echo $o_glob->Templates->{$o_glob->URL->Branch . 'View'};
								} else if ($o_glob->Templates->Exists($o_glob->URL->Branch . 'FlexView')) {
									echo $o_glob->Templates->{$o_glob->URL->Branch . 'FlexView'};
								}
							}
							
							/* render modal form, generated by branch content scripting and call modal form with javascript */
							if ( ($o_glob->PostModalForm != null) && ($o_glob->PostModalForm->Automatic) ) {
								echo $o_glob->PostModalForm;
								$s_formId = '#' . $o_glob->PostModalForm->FormObject->Id . 'Modal';
								echo '<script>$(function(){$(\'' . $s_formId . '\').modal();});</script>';
							}
						}
					
					/* close main container */
					echo '</div>' . "\n";
					
					/* render trunk-foot-leaf */
					$this->RenderLeaf('trunkFootLeaf');
				}
			}
		} catch (forestException $o_exc) {
			throw $o_exc;
		}
	}
	
	/**
	 * method for calling leaf-rendering
	 *
	 * @param string $p_s_leaf  leaf name without .php extension
	 * @param string $p_s_path  folder path to leaf file
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function RenderLeaf($p_s_leaf, $p_s_path = null) {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		if (is_null($p_s_path)) {
			$s_foo = './trunk/' . $p_s_leaf . '.php';
		
			if (forestAutoLoad::IsReadable($s_foo)) {
				if (!(@include_once($s_foo))) {
					throw new forestException('File[' . $p_s_leaf . '] could not be loaded');
				}
			}
		} else {
			$s_foo = './trunk/' . $p_s_path . $p_s_leaf . '.php';
			
			if (forestAutoLoad::IsReadable($s_foo)) {
				if (!(@include_once($s_foo))) {
					throw new forestException('File[' . $p_s_leaf . '] could not be loaded');
				}
			}
		}
	}
}
?>