<?php
/**
 * central control class of forestPHP framework for fetch-content and render-content
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestphp.de>
 * @copyright   (c) 2021 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.0.1 stable
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
		global $b_run_testddl;
		global $b_run_testddl_embedded;
		global $b_fill_mongodb_from_sqlite3;
		
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
				
				$o_glob->Base->Add(new \fPHP\Base\forestBase(\fPHP\Base\forestBase::MariaSQL, '127.0.0.1', 'forestphp', 'db_user', 'db_password'), 'forestPHPMariaSQLBase');
				//$o_glob->Base->Add(new \fPHP\Base\forestBase(\fPHP\Base\forestBase::MSSQL, '127.0.0.1\MSSQLSERVER, 1433', 'forestphp', 'db_user', 'db_password', false), 'forestPHPMSSQLBase');
				//$o_glob->Base->Add(new \fPHP\Base\forestBase(\fPHP\Base\forestBase::SQLite3, './forestPHP.db', '', 'db_user', 'db_password'), 'forestPHPSQLite3Base');
				//$o_glob->Base->Add(new \fPHP\Base\forestBase(\fPHP\Base\forestBase::PGSQL, '127.0.0.1', 'forestphp', 'db_user', 'db_password'), 'forestPHPPGSQLBase');
				//$o_glob->Base->Add(new \fPHP\Base\forestBase(\fPHP\Base\forestBase::OCISQL, '127.0.0.1', 'XE:WE8MSWIN1252', 'db_user', 'db_password'), 'forestPHPOCISQLBase');
				//$o_glob->Base->Add(new \fPHP\Base\forestBase(\fPHP\Base\forestBase::MongoDB, '127.0.0.1:27017', 'forestphp', 'db_user', 'db_password'), 'forestPHPMongoBase');
				
				$o_glob->ActiveBase = 'forestPHPMariaSQLBase';
				//$o_glob->ActiveBase = 'forestPHPMSSQLBase';
				//$o_glob->ActiveBase = 'forestPHPSQLite3Base';
				//$o_glob->ActiveBase = 'forestPHPPGSQLBase';
				//$o_glob->ActiveBase = 'forestPHPOCISQLBase';
				//$o_glob->ActiveBase = 'forestPHPMongoBase';
				
				/* import records from sqlite3 database to mongodb database */
				if ($b_fill_mongodb_from_sqlite3) {
					$o_glob->Trunk = new \fPHP\Twigs\trunkTwig;
					$this->FillMongoDBFromSQLite3();
					exit;
				}
				
				/* execute function to test db data definition language */
				if ($b_run_testddl) {
					$o_glob->Trunk = new \fPHP\Twigs\trunkTwig;
					$this->TestDDL(true);
					exit;
				}
				
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
				
				/* execute function to test db data definition language */
				if ($b_run_testddl_embedded) {
					$this->TestDDL(true);
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

	/**
	 * method for testing db data definition language
	 *
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function TestDDL($p_b_testDML = false) {
		/*
		 * activate debug sql queries
		 */
		global $b_debug_sql_query;
		$b_debug_sql_query = true;
		
		try {
			echo '<h1>TestDDL - Start</h1>';
			
			$o_glob = \fPHP\Roots\forestGlobals::init();
			
			$o_glob->Base->{$o_glob->ActiveBase}->ManualTransaction();
			
			/*
			 * column definitions
			 */
			$a_columns = array(
				'Id' => array(
					'columnType' => 'integer [int]',
					'constraints' => array('NOT NULL', 'PRIMARY KEY', 'AUTO_INCREMENT')
				),
				'UUID' => array(
					'columnType' => 'text [36]',
					'constraints' => array('NOT NULL', 'UNIQUE')
				),
				'ShortText' => array(
					'columnType' => 'text [255]',
					'constraints' => array('NULL'),
					'forestdata' => '1098ab89-4717-11e9-8210-1062e50d1fcb',
					'formelement' => 'ac234a4c-4717-11e9-8210-1062e50d1fcb',
					'sqltype' => 'd8796ff4-4717-11e9-8210-1062e50d1fcb'
				),
				'Text' => array(
					'columnType' => 'text',
					'constraints' => array('NULL'),
					'forestdata' => '1098ab89-4717-11e9-8210-1062e50d1fcb',
					'formelement' => 'ac234a4c-4717-11e9-8210-1062e50d1fcb',
					'sqltype' => '1591cb35-4718-11e9-8210-1062e50d1fcb'
				),
				'SmallInt' => array(
					'columnType' => 'integer [small]',
					'constraints' => array('NULL'),
					'forestdata' => '18d92e02-4717-11e9-8210-1062e50d1fcb',
					'formelement' => 'ac21769b-4717-11e9-8210-1062e50d1fcb',
					'sqltype' => '159229d5-4718-11e9-8210-1062e50d1fcb'
				),
				'Int' => array(
					'columnType' => 'integer [int]',
					'constraints' => array('NULL'),
					'forestdata' => '18d92e02-4717-11e9-8210-1062e50d1fcb',
					'formelement' => 'ac21769b-4717-11e9-8210-1062e50d1fcb',
					'sqltype' => '1592a0e1-4718-11e9-8210-1062e50d1fcb'
				),
				'BigInt' => array(
					'columnType' => 'integer [big]',
					'constraints' => array('NULL'),
					'forestdata' => '18d92e02-4717-11e9-8210-1062e50d1fcb',
					'formelement' => 'ac21769b-4717-11e9-8210-1062e50d1fcb',
					'sqltype' => '15931a91-4718-11e9-8210-1062e50d1fcb'
				),
				'Timestamp' => array(
					'columnType' => 'datetime',
					'constraints' => array('NULL','DEFAULT'),
					'constraintDefaultValue' => '2000-01-01 00:00:00',
					'forestdata' => '3e0f992f-4717-11e9-8210-1062e50d1fcb',
					'formelement' => 'ac20a373-4717-11e9-8210-1062e50d1fcb',
					'sqltype' => '15937c51-4718-11e9-8210-1062e50d1fcb'
				),
				'Time' => array(
					'columnType' => 'time',
					'constraints' => array('NULL'),
					'forestdata' => '3e0f992f-4717-11e9-8210-1062e50d1fcb',
					'formelement' => 'ac22bdc3-4717-11e9-8210-1062e50d1fcb',
					'sqltype' => '1593ce61-4718-11e9-8210-1062e50d1fcb'
				),
				'DoubleCol' => array(
					'columnType' => 'double',
					'constraints' => array('NULL'),
					'forestdata' => '1b9785a9-4717-11e9-8210-1062e50d1fcb',
					'formelement' => 'ac21769b-4717-11e9-8210-1062e50d1fcb',
					'sqltype' => '15940ac2-4718-11e9-8210-1062e50d1fcb'
				),
				'Decimal' => array(
					'columnType' => 'decimal',
					'constraints' => array('NULL'),
					'forestdata' => '1b9785a9-4717-11e9-8210-1062e50d1fcb',
					'formelement' => 'ac21769b-4717-11e9-8210-1062e50d1fcb',
					'sqltype' => '159449b4-4718-11e9-8210-1062e50d1fcb'
				),
				'Bool' => array(
					'columnType' => 'bool',
					'constraints' => array('NULL'),
					'forestdata' => '1e7bae52-4717-11e9-8210-1062e50d1fcb',
					'formelement' => 'ac1ed318-4717-11e9-8210-1062e50d1fcb',
					'sqltype' => '159489e2-4718-11e9-8210-1062e50d1fcb'
				)
			);
			
			/*
			 * create table 'sys_fphp_testddl'
			 */
			$o_queryNew = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::CREATE, 'sys_fphp_testddl');
			
			foreach ($a_columns as $s_name => $a_info) {
				$o_column = new \fPHP\Base\forestSQLColumnStructure($o_queryNew);
					$o_column->Name = $s_name;
					
					$s_columnType = null;
					$i_columnLength = null;
					$i_columnDecimalLength = null;
					\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, strval($a_info['columnType']), $s_columnType, $i_columnLength, $i_columnDecimalLength);
					
					$o_column->ColumnType = $s_columnType;
					if ($i_columnLength != null) { $o_column->ColumnTypeLength = $i_columnLength; }
					if ($i_columnDecimalLength != null) { $o_column->ColumnTypeDecimalLength = $i_columnDecimalLength; }
					$o_column->AlterOperation = 'ADD';
					
					if (array_key_exists('constraints', $a_info)) {
						foreach ($a_info['constraints'] as $s_constraint) {
							$s_constraintType = null;
							\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, strval($s_constraint), $s_constraintType);
							
							$o_column->ConstraintList->Add($s_constraintType);
							
							if ( ($s_constraint == 'DEFAULT') && (array_key_exists('constraintDefaultValue', $a_info)) ) {
								$o_column->ConstraintDefaultValue = $a_info['constraintDefaultValue'];
							}
						}
					}
					
				$o_queryNew->Query->Columns->Add($o_column);
			}				
			
			/* alter table does not return a value */
			print_r($o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryNew, false, false));
			echo '<hr>';
			
			/*
			 * additional column definitions
			 */
			$a_columns2 = array(
				'Text2' => array(
					'columnType' => 'text [36]',
					'constraints' => array('NULL','DEFAULT'),
					'constraintDefaultValue' => 'Das ist das Haus vom Nikolaus',
					'forestdata' => '1098ab89-4717-11e9-8210-1062e50d1fcb',
					'formelement' => 'ac234a4c-4717-11e9-8210-1062e50d1fcb',
					'sqltype' => '1591cb35-4718-11e9-8210-1062e50d1fcb'
				),
				'ShortText2' => array(
					'columnType' => 'text [255]',
					'constraints' => array('NULL'),
					'forestdata' => '1098ab89-4717-11e9-8210-1062e50d1fcb',
					'formelement' => 'ac234a4c-4717-11e9-8210-1062e50d1fcb',
					'sqltype' => 'd8796ff4-4717-11e9-8210-1062e50d1fcb'
				)
			);
			
			/*
			 * add two new columns to table 'sys_fphp_testddl'
			 */
			$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, 'sys_fphp_testddl');
			
			foreach ($a_columns2 as $s_name => $a_info) {
				$o_column = new \fPHP\Base\forestSQLColumnStructure($o_queryAlter);
					$o_column->Name = $s_name;
					
					$s_columnType = null;
					$i_columnLength = null;
					$i_columnDecimalLength = null;
					\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, strval($a_info['columnType']), $s_columnType, $i_columnLength, $i_columnDecimalLength);
					
					$o_column->ColumnType = $s_columnType;
					if ($i_columnLength != null) { $o_column->ColumnTypeLength = $i_columnLength; }
					if ($i_columnDecimalLength != null) { $o_column->ColumnTypeDecimalLength = $i_columnDecimalLength; }
					$o_column->AlterOperation = 'ADD';
					
					if (array_key_exists('constraints', $a_info)) {
						foreach ($a_info['constraints'] as $s_constraint) {
							$s_constraintType = null;
							\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, strval($s_constraint), $s_constraintType);
							
							$o_column->ConstraintList->Add($s_constraintType);
							
							if ( ($s_constraint == 'DEFAULT') && (array_key_exists('constraintDefaultValue', $a_info)) ) {
								$o_column->ConstraintDefaultValue = $a_info['constraintDefaultValue'];
							}
						}
					}
					
				$o_queryAlter->Query->Columns->Add($o_column);
			}				
			
			/* alter table does not return a value */
			print_r($o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false));
			echo '<hr>';
			
			/*
			 * add new index to column Int on table 'sys_fphp_testddl'
			 */
			$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, 'sys_fphp_testddl');
			
			$o_constraint = new \fPHP\Base\forestSQLConstraint($o_queryAlter);
				$o_constraint->Constraint = 'UNIQUE';
				$o_constraint->Name = 'new_index_Int';
				$o_constraint->AlterOperation = 'ADD';
				$o_constraint->Columns->Add(new forestString('Int'));
				
			$o_queryAlter->Query->Constraints->Add($o_constraint);
			
			/* alter table does not return a value */
			print_r($o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false));
			echo '<hr>';
			
			/*
			 * change index by extending it with column BigInt on table 'sys_fphp_testddl'
			 */
			$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, 'sys_fphp_testddl');
			
			$o_constraint = new \fPHP\Base\forestSQLConstraint($o_queryAlter);
				$o_constraint->Constraint = 'UNIQUE';
				$o_constraint->Name = 'new_index_Int';
				$o_constraint->NewName = 'new_index_Int_Bool';
				$o_constraint->AlterOperation = 'CHANGE';
				$o_constraint->Columns->Add(new forestString('Int'));
				$o_constraint->Columns->Add(new forestString('Bool'));
				
			$o_queryAlter->Query->Constraints->Add($o_constraint);
			
			/* alter table does not return a value */
			print_r($o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false));
			echo '<hr>';
			
			/*
			 * add new index to column Text2 on table 'sys_fphp_testddl'
			 */
			$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, 'sys_fphp_testddl');
			
			$o_constraint = new \fPHP\Base\forestSQLConstraint($o_queryAlter);
				$o_constraint->Constraint = 'INDEX';
				$o_constraint->Name = 'new_index_Text2';
				$o_constraint->AlterOperation = 'ADD';
				$o_constraint->Columns->Add(new forestString('Text2'));
				
			$o_queryAlter->Query->Constraints->Add($o_constraint);
			
			/* alter table does not return a value */
			print_r($o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false));
			echo '<hr>';
			
			/*
			 * drop index Int_BigInt on table 'sys_fphp_testddl'
			 */
			$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, 'sys_fphp_testddl');
			
			$o_constraint = new \fPHP\Base\forestSQLConstraint($o_queryAlter);
				$o_constraint->Constraint = 'UNIQUE';
				$o_constraint->Name = 'new_index_Int_Bool';
				$o_constraint->AlterOperation = 'DROP';
				
			$o_queryAlter->Query->Constraints->Add($o_constraint);
			
			/* alter table does not return a value */
			print_r($o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false));
			echo '<hr>';
			
			/*
			 * drop index Text2 on table 'sys_fphp_testddl'
			 */
			$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, 'sys_fphp_testddl');
			
			$o_constraint = new \fPHP\Base\forestSQLConstraint($o_queryAlter);
				$o_constraint->Constraint = 'INDEX';
				$o_constraint->Name = 'new_index_Text2';
				$o_constraint->AlterOperation = 'DROP';
				
			$o_queryAlter->Query->Constraints->Add($o_constraint);
			
			/* alter table does not return a value */
			print_r($o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false));
			echo '<hr>';
			
			/* run routine to test db data manipulation language */
			if ($p_b_testDML) {
				/*
				 * create sys_fphp_testddl table record
				 */
				$o_tableTwig = new \fPHP\Twigs\tableTwig;
				
				$o_tableTwig->Name = 'sys_fphp_testddl';
				$o_tableTwig->Interval = 50;
				$o_tableTwig->InfoColumns = 1;
				$o_tableTwig->Versioning = 1;
				
				$o_tableTwig->InsertRecord();
				
				/*
				 * create sys_fphp_testddl tablefields records
				 */
				$i_order = 0;
				
				foreach ($a_columns as $s_name => $a_info) {
					if (!array_key_exists('forestdata', $a_info)) {
						continue;
					}
					
					$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
					
					$o_tablefieldTwig->TableUUID = $o_tableTwig->UUID;
					$o_tablefieldTwig->FieldName = $s_name;
					$o_tablefieldTwig->FormElementUUID = $a_info['formelement'];
					$o_tablefieldTwig->SqlTypeUUID = $a_info['sqltype'];
					$o_tablefieldTwig->ForestDataUUID = $a_info['forestdata'];
					$o_tablefieldTwig->TabId = 'general';
					$o_tablefieldTwig->Order = ++$i_order;
					
					$o_tablefieldTwig->InsertRecord();
				}
				
				foreach ($a_columns2 as $s_name => $a_info) {
					if (!array_key_exists('forestdata', $a_info)) {
						continue;
					}
					
					$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
					
					$o_tablefieldTwig->TableUUID = $o_tableTwig->UUID;
					$o_tablefieldTwig->FieldName = $s_name;
					$o_tablefieldTwig->FormElementUUID = $a_info['formelement'];
					$o_tablefieldTwig->SqlTypeUUID = $a_info['sqltype'];
					$o_tablefieldTwig->ForestDataUUID = $a_info['forestdata'];
					$o_tablefieldTwig->TabId = 'general';
					$o_tablefieldTwig->Order = ++$i_order;
					
					$o_tablefieldTwig->InsertRecord();
				}
				
				/*
				 * redo list table because of new table and tablefield records
				 */
				$o_glob->ListTables();
			
				$this->TestDML();
			}
			
			/*
			 * additional column definition
			 */
			$a_columns3 = array(
				'Text2' => array(
					'columnType' => 'text [255]',
					'constraints' => array('NOT NULL'),
					'newName' => 'Text2Changed'
				)
			);
			
			/*
			 * change column Text2 to Text2Changed and type 'text [255]' on table 'sys_fphp_testddl'
			 */
			$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, 'sys_fphp_testddl');
			
			foreach ($a_columns3 as $s_name => $a_info) {
				$o_column = new \fPHP\Base\forestSQLColumnStructure($o_queryAlter);
					$o_column->Name = $s_name;
					
					$s_columnType = null;
					$i_columnLength = null;
					$i_columnDecimalLength = null;
					\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, strval($a_info['columnType']), $s_columnType, $i_columnLength, $i_columnDecimalLength);
					
					$o_column->ColumnType = $s_columnType;
					if ($i_columnLength != null) { $o_column->ColumnTypeLength = $i_columnLength; }
					if ($i_columnDecimalLength != null) { $o_column->ColumnTypeDecimalLength = $i_columnDecimalLength; }
					$o_column->AlterOperation = 'CHANGE';
					
					if (array_key_exists('newName', $a_info)) {
						$o_column->NewName = $a_info['newName'];
					}
					
					if (array_key_exists('constraints', $a_info)) {
						foreach ($a_info['constraints'] as $s_constraint) {
							$s_constraintType = null;
							\fPHP\Base\forestSQLQuery::ConstraintTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, strval($s_constraint), $s_constraintType);
							
							$o_column->ConstraintList->Add($s_constraintType);
							
							if ( ($s_constraint == 'DEFAULT') && (array_key_exists('constraintDefaultValue', $a_info)) ) {
								$o_column->ConstraintDefaultValue = $a_info['constraintDefaultValue'];
							}
						}
					}
					
				$o_queryAlter->Query->Columns->Add($o_column);
			}
			
			/* alter table does not return a value */
			print_r($o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false));
			echo '<hr>';
			
			if ($p_b_testDML) {
				/*
				 * edit tablefield field name
				 */
				$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
				$o_tablefieldTwig->GetRecordPrimary(array($o_tableTwig->UUID, 'Text2'), array('TableUUID', 'FieldName'));
				$o_tablefieldTwig->FieldName = 'Text2Changed';
				$o_tablefieldTwig->UpdateRecord();
			}
			
			/*
			 * drop column ShortText2 on table 'sys_fphp_testddl'
			 */
			$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, 'sys_fphp_testddl');
			
			$o_column = new \fPHP\Base\forestSQLColumnStructure($o_queryAlter);
				$o_column->Name = 'ShortText2';
				
				$s_columnType = null;
				$i_columnLength = null;
				$i_columnDecimalLength = null;
				\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'text [255]', $s_columnType, $i_columnLength, $i_columnDecimalLength);
				
				$o_column->ColumnType = $s_columnType;
				if ($i_columnLength != null) { $o_column->ColumnTypeLength = $i_columnLength; }
				if ($i_columnDecimalLength != null) { $o_column->ColumnTypeDecimalLength = $i_columnDecimalLength; }
				$o_column->AlterOperation = 'DROP';
			
			$o_queryAlter->Query->Columns->Add($o_column);
			
			/* alter table does not return a value */
			print_r($o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false));
			echo '<hr>';
			
			if ($p_b_testDML) {
				/*
				 * drop tablefield record
				 */
				$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
				$o_tablefieldTwig->GetRecordPrimary(array($o_tableTwig->UUID, 'ShortText2'), array('TableUUID', 'FieldName'));
				$o_tablefieldTwig->DeleteRecord();
			}
			
			/*
			 * drop coluumn SmallInt and Int on table 'sys_fphp_testddl'
			 */
			$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, 'sys_fphp_testddl');
			
			$o_column = new \fPHP\Base\forestSQLColumnStructure($o_queryAlter);
				$o_column->Name = 'SmallInt';
				
				$s_columnType = null;
				$i_columnLength = null;
				$i_columnDecimalLength = null;
				\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'integer [small]', $s_columnType, $i_columnLength, $i_columnDecimalLength);
				
				$o_column->ColumnType = $s_columnType;
				if ($i_columnLength != null) { $o_column->ColumnTypeLength = $i_columnLength; }
				if ($i_columnDecimalLength != null) { $o_column->ColumnTypeDecimalLength = $i_columnDecimalLength; }
				$o_column->AlterOperation = 'DROP';
			
			$o_queryAlter->Query->Columns->Add($o_column);
			
			$o_column = new \fPHP\Base\forestSQLColumnStructure($o_queryAlter);
				$o_column->Name = 'Int';
				
				$s_columnType = null;
				$i_columnLength = null;
				$i_columnDecimalLength = null;
				\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'integer [int]', $s_columnType, $i_columnLength, $i_columnDecimalLength);
				
				$o_column->ColumnType = $s_columnType;
				if ($i_columnLength != null) { $o_column->ColumnTypeLength = $i_columnLength; }
				if ($i_columnDecimalLength != null) { $o_column->ColumnTypeDecimalLength = $i_columnDecimalLength; }
				$o_column->AlterOperation = 'DROP';
			
			$o_queryAlter->Query->Columns->Add($o_column);
			
			/* alter table does not return a value */
			print_r($o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false));
			echo '<hr>';
			
			if ($p_b_testDML) {			
				/*
				 * delete sys_fphp_testddl tablefield records
				 */
				$o_tablefieldTwig = new \fPHP\Twigs\tablefieldTwig;
			
				$a_sqlAdditionalFilter = array(array('column' => 'TableUUID', 'value' => $o_tableTwig->UUID, 'operator' => '=', 'filterOperator' => 'AND'));
				$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
				$o_tablefields = $o_tablefieldTwig->GetAllRecords(true);
				$o_glob->Temp->Del('SQLAdditionalFilter');
				
				foreach ($o_tablefields->Twigs as $o_tablefield) {
					$o_tablefield->DeleteRecord();
				}
				
				/*
				 * delete sys_fphp_testddl table record
				 */
				$o_tableTwig->DeleteRecord();
			}
		
			/*
			 * drop sys_fphp_testddl table
			 */
			$o_queryDrop = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::DROP, 'sys_fphp_testddl');
			
			/* alter table does not return a value */
			print_r($o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryDrop, false, false));
			echo '<hr>';
			
			echo '<h1>TestDDL - End</h1>';
			
			$o_glob->Base->{$o_glob->ActiveBase}->ManualCommit();
		} catch (forestException $o_exc) {
			$o_glob->Base->{$o_glob->ActiveBase}->ManualRollBack();
			throw $o_exc;
		} catch (\PDOException $o_exc) {
			$o_glob->Base->{$o_glob->ActiveBase}->ManualRollBack();
			throw $o_exc;
		} catch (\Exception $o_exc) {
			$o_glob->Base->{$o_glob->ActiveBase}->ManualRollBack();
			throw $o_exc;
		}
	}
	
	/**
	 * method for testing db data maipulation language
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function TestDML() {
		try {
			$o_glob = \fPHP\Roots\forestGlobals::init();
			
			echo '<h1>TestDML - Start</h1>';
			
			/*
			 * insert 5 new records
			 */
			$o_twig = new \fPHP\Twigs\testddlTwig;
			$o_twig->ShortText = 'Datensatz Eins';
			$o_twig->Text = 'Die Handelsstreitigkeiten zwischen den USA und China sorgen für eine Art Umdenken auf beiden Seiten. Während US-Unternehmen chinesische Hardware meiden, tun dies chinesische Unternehmen wohl mittlerweile auch: So denken laut einem Bericht der Nachrichtenagentur Bloomberg viele chinesische Hersteller stark darüber nach, ihre IT-Infrastruktur von lokalen Unternehmen statt von den US-Konzernen Oracle und IBM zu kaufen. Für diese Unternehmen sei der asiatische Markt wichtig. 16 respektive mehr als 20 Prozent des Umsatzes stammen aus dieser Region.';
			$o_twig->SmallInt = 1;
			$o_twig->Int = 10001;
			$o_twig->BigInt = 100001111;
			$o_twig->Timestamp = \fPHP\Helper\forestStringLib::TextToDate('01.01.2019T01:01:01');
			$o_twig->Time = \fPHP\Helper\forestStringLib::TextToDate('01:01:01');
			$o_twig->DoubleCol = 1.23456789;
			$o_twig->Decimal = 1234567.9;
			$o_twig->Bool = true;
			$o_twig->Text2 = 'Das ist das Haus vom Nikolaus #1';
			$o_twig->ShortText2 = 'Eins Datensatz';
			echo 'New record #1<br>'; print_r($o_twig->InsertRecord()); echo '<hr>';
			
			$o_twig = new \fPHP\Twigs\testddlTwig;
			$o_twig->ShortText = 'Datensatz Zwei';
			$o_twig->Text = 'Das Tech-Startup Pingcap ist eines der lokalen Unternehmen, die den Handelsstreit zu ihrem Vorteil nutzen, für lokale chinesische Produkte werben und selbst von US-Hardware wegmigrieren. Mehr als 300 Kunden betreut die Firma, darunter der Fahrradsharing-Dienst Mobike und der chinesische Smartphone-Hersteller Xiaomi. Piingcap bietet beispielsweise auf Mysql basierende Datenbanken wie TiDB an.';
			$o_twig->SmallInt = 2;
			$o_twig->Int = 20002;
			$o_twig->BigInt = 20002222;
			$o_twig->Timestamp = \fPHP\Helper\forestStringLib::TextToDate('02.02.2019T02:02:02');
			$o_twig->Time = \fPHP\Helper\forestStringLib::TextToDate('02:02:02');
			$o_twig->DoubleCol = 12.3456789;
			$o_twig->Decimal = 123456.79;
			$o_twig->Bool = false;
			$o_twig->Text2 = 'Das ist das Haus vom Nikolaus #2';
			$o_twig->ShortText2 = 'Zwei Datensatz';
			echo 'New record #2<br>'; print_r($o_twig->InsertRecord()); echo '<hr>';
			
			$o_twig = new \fPHP\Twigs\testddlTwig;
			$o_twig->ShortText = 'Datensatz Drei';
			$o_twig->Text = '"Viele Firmen, die auf Oracle und IBM gesetzt haben, dachten es sei noch ein entfernter Meilenstein, diese zu ersetzen", sagt Pingcap-CEO Huang Dongxu. "Wir schauen uns aber mittlerweile Plan B ernsthaft an". Allerdings seien chinesische Unternehmen laut dem lokalen Analystenunternehmen UOB Kay Hian noch nicht ganz bereit, wettbewerbsfähige Chips zu produzieren. "Wenn sie aber genug gereift sind, werden [viele Unternehmen, Anm. d. Red.] ausländische Chips mit den lokalen ersetzen", sagt die Firma.';
			$o_twig->SmallInt = 3;
			$o_twig->Int = 30003;
			$o_twig->BigInt = 30003333;
			$o_twig->Timestamp = \fPHP\Helper\forestStringLib::TextToDate('03.03.2019T03:03:03');
			$o_twig->Time = \fPHP\Helper\forestStringLib::TextToDate('03:03:03');
			$o_twig->DoubleCol = 123.456789;
			$o_twig->Decimal = 12345.679;
			$o_twig->Bool = true;
			$o_twig->Text2 = 'Das ist das Haus vom Nikolaus #3';
			$o_twig->ShortText2 = 'Drei Datensatz';
			echo 'New record #3<br>'; print_r($o_twig->InsertRecord()); echo '<hr>';
			
			$s_third_uuid = $o_twig->UUID;
			
			$o_twig = new \fPHP\Twigs\testddlTwig;
			$o_twig->ShortText = 'Datensatz Vier';
			$o_twig->Text = 'China migriert schneller von US-Hardware auf lokale Chips. Immer mehr chinesische Unternehmen wollen anscheinend von amerikanischen Produkten auf lokal hergestellte Hardware setzen. Davon betroffen sind beispielsweise IBM und Oracle, die einen großen Teil ihres Umsatzes in Asien machen. Noch sei die chinesische Technik aber nicht weit genug. ';
			$o_twig->SmallInt = 4;
			$o_twig->Int = 40004;
			$o_twig->BigInt = 40004444;
			$o_twig->Timestamp = \fPHP\Helper\forestStringLib::TextToDate('04.04.2019T04:04:04');
			$o_twig->Time = \fPHP\Helper\forestStringLib::TextToDate('04:04:04');
			$o_twig->DoubleCol = 1234.56789;
			$o_twig->Decimal = 123.45679;
			$o_twig->Bool = false;
			$o_twig->Text2 = 'Das ist das Haus vom Nikolaus #4';
			$o_twig->ShortText2 = 'Vier Datensatz';
			echo 'New record #4<br>'; print_r($o_twig->InsertRecord()); echo '<hr>';
			
			$s_fourth_uuid = $o_twig->UUID;
			
			$o_twig = new \fPHP\Twigs\testddlTwig;
			$o_twig->ShortText = 'Datensatz Fünf';
			$o_twig->Text = 'Weder IBM noch Oracle haben Bloomberg auf eine Anfrage hin geantwortet. Dass die US-Regierung China wirtschaftlich unter Druck setzt, könnte allerdings zu unerwünschten Ergebnissen und dem schnellen Verlust eines Marktes mit fast 1,4 Milliarden Einwohnern führen.';
			$o_twig->SmallInt = 5;
			$o_twig->Int = 50005;
			$o_twig->BigInt = 50005555;
			$o_twig->Timestamp = \fPHP\Helper\forestStringLib::TextToDate('05.05.2019T05:05:05');
			$o_twig->Time = \fPHP\Helper\forestStringLib::TextToDate('05:05:05');
			$o_twig->DoubleCol = 12345.6789;
			$o_twig->Decimal = 12.345679;
			$o_twig->Bool = true;
			$o_twig->Text2 = 'Das ist das Haus vom Nikolaus #5';
			$o_twig->ShortText2 = 'Fünf Datensatz';
			echo 'New record #5<br>'; print_r($o_twig->InsertRecord()); echo '<hr>';
			
			/*
			 * select one record
			 */
			$o_twig = new \fPHP\Twigs\testddlTwig;
			echo 'Get record #3 with UUID<br>'; print_r($o_twig->GetRecord(array($s_third_uuid))); echo $o_twig->ShowFields(false, true); echo '<hr>';
			
			/*
			 * select all records
			 */
			$o_twig = new \fPHP\Twigs\testddlTwig;
			echo 'Get all records<br>'; $o_result = $o_twig->GetAllRecords(true);
			foreach($o_result->Twigs as $o_record) {
				echo $o_record->ShowFields(false, true); echo '<br>';
			}
			echo '<hr>';
			
			/*
			 * update two records
			 */
			$o_twig = new \fPHP\Twigs\testddlTwig;
			echo 'Get record #3 with UUID and update ShortText+Int<br>'; print_r($o_twig->GetRecord(array($s_third_uuid))); $o_twig->ShortText = 'Datensatz Drei geändert.'; $o_twig->Int = 3; print_r($o_twig->UpdateRecord()); echo $o_twig->ShowFields(false, true); echo '<hr>';
			echo 'Get record #4 with UUID and update ShortText+Int<br>'; print_r($o_twig->GetRecord(array($s_fourth_uuid))); $o_twig->ShortText = 'Datensatz Vier geändert.'; $o_twig->Int = 4; print_r($o_twig->UpdateRecord()); echo $o_twig->ShowFields(false, true); echo '<hr>';
			
			/*
			 * select all records with filter Bool=true
			 */
			$o_twig = new \fPHP\Twigs\testddlTwig;
			echo 'Get all records with Bool=true<br>';
			$a_sqlAdditionalFilter = array(array('column' => 'Bool', 'value' => true, 'operator' => '=', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_result = $o_twig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			foreach($o_result->Twigs as $o_record) {
				echo $o_record->ShowFields(false, true); echo '<br>';
			}
			echo '<hr>';
			
			/*
			 * delete two records
			 */
			$o_twig = new \fPHP\Twigs\testddlTwig;
			echo 'Get record #3 with UUID for deletion<br>'; print_r($o_twig->GetRecord(array($s_third_uuid))); print_r($o_twig->DeleteRecord()); echo '<hr>';
			echo 'Get record #4 with UUID for deletion<br>'; print_r($o_twig->GetRecord(array($s_fourth_uuid))); print_r($o_twig->DeleteRecord()); echo '<hr>';
			
			/*
			 * select all with filter
			 */
			$o_twig = new \fPHP\Twigs\testddlTwig;
			echo 'Get all records with ShortText LIKE %i%<br>';
			$a_sqlAdditionalFilter = array(array('column' => 'ShortText', 'value' => '%i%', 'operator' => 'LIKE', 'filterOperator' => 'AND'));
			$o_glob->Temp->Add($a_sqlAdditionalFilter, 'SQLAdditionalFilter');
			$o_result = $o_twig->GetAllRecords(true);
			$o_glob->Temp->Del('SQLAdditionalFilter');
			foreach($o_result->Twigs as $o_record) {
				echo $o_record->ShowFields(false, true); echo '<br>';
			}
			echo '<hr>';
			
			echo '<h1>TestDML - End</h1>';
		} catch (forestException $o_exc) {
			throw $o_exc;
		} catch (\PDOException $o_exc) {
			throw $o_exc;
		} catch (\Exception $o_exc) {
			throw $o_exc;
		}
	}
	
	/**
	 * method for filling data from sqlite3 database to mongodb database
	 *
	 * @return null
	 *
	 * @throws forestException if error occurs
	 * @access private
	 * @static no
	 */
	private function FillMongoDBFromSQLite3() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
		$o_glob->ActiveBase = 'forestPHPSQLite3Base';
		
		$o_allTablesTwig = new \fPHP\Twigs\tableTwig;
		
		/* query all table records */
		$o_tableTwigs = $o_allTablesTwig->GetAllRecords(true);
		
		if ($o_tableTwigs->Twigs->Count() > 0) {
			/* iterate each table records */
			foreach ($o_tableTwigs->Twigs as $o_tableTwig) {
				/* create twig object of table record */
				$s_tempTable = $o_tableTwig->Name;
				\fPHP\Helper\forestStringLib::RemoveTablePrefix($s_tempTable);
				$s_foo = '\\fPHP\\Twigs\\' . $s_tempTable . 'Twig';
				$o_tempTwig = new $s_foo;
				
				$o_glob->ActiveBase = 'forestPHPMongoBase';
				
				/* create mongodb command to create table */
				$o_queryNew = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::CREATE, $o_tableTwig->Name);
					if (in_array('Id', $o_tempTwig->fphp_Mapping)) {
						$o_column = new \fPHP\Base\forestSQLColumnStructure($o_queryNew);
							$o_column->Name = 'Id';
							
							$s_columnType = null;
							$i_columnLength = null;
							$i_columnDecimalLength = null;
							\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'integer [int]', $s_columnType, $i_columnLength, $i_columnDecimalLength);
							
							$o_column->ColumnType = $s_columnType;
							if ($i_columnLength != null) { $o_column->ColumnTypeLength = $i_columnLength; }
							if ($i_columnDecimalLength != null) { $o_column->ColumnTypeDecimalLength = $i_columnDecimalLength; }
							$o_column->AlterOperation = 'ADD';
							$o_column->ConstraintList->Add('PRIMARY KEY');
							
							
						$o_queryNew->Query->Columns->Add($o_column);	
					}
					
					if (in_array('UUID', $o_tempTwig->fphp_Mapping)) {
						$o_column = new \fPHP\Base\forestSQLColumnStructure($o_queryNew);
							$o_column->Name = 'UUID';
							
							$s_columnType = null;
							$i_columnLength = null;
							$i_columnDecimalLength = null;
							\fPHP\Base\forestSQLQuery::ColumnTypeAllocation($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, 'text [36]', $s_columnType, $i_columnLength, $i_columnDecimalLength);
							
							$o_column->ColumnType = $s_columnType;
							if ($i_columnLength != null) { $o_column->ColumnTypeLength = $i_columnLength; }
							if ($i_columnDecimalLength != null) { $o_column->ColumnTypeDecimalLength = $i_columnDecimalLength; }
							$o_column->AlterOperation = 'ADD';
							$o_column->ConstraintList->Add('UNIQUE');
							
							
						$o_queryNew->Query->Columns->Add($o_column);
					}
				
				/* execute create command to add table as collection for mongodb */
				$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryNew, false, false);
				
				echo 'Created table ' . $o_tableTwig->Name . '<br>';
					
				$o_glob->ActiveBase = 'forestPHPSQLite3Base';
				
				/* query all records of current table record */
				$o_twigs = $o_tempTwig->GetAllRecords(true);
				
				if ($o_twigs->Twigs->Count() > 0) {
					/* insert each queried record into mongodb */
					foreach ($o_twigs->Twigs as $o_twig) {
						$o_glob->ActiveBase = 'forestPHPMongoBase';
						$o_twig->InsertRecord(true, true);
						$o_glob->ActiveBase = 'forestPHPSQLite3Base';
					}
					
					echo $o_twigs->Twigs->Count() . ' records inserted for ' . $o_twig->fphp_Table . '<br>';
				}
			}
		}
		
		$o_glob->ActiveBase = 'forestPHPMongoBase';
		
		/* list of all unique indexes */
		$a_uniqueIndexes = array();
		$a_uniqueIndexes['sys_fphp_action'] = array(0 => array('BranchId', 'Name'));
		$a_uniqueIndexes['sys_fphp_branch'] = array(0 => array('Name', 'ParentBranch', 'NavigationOrder'));
		$a_uniqueIndexes['sys_fphp_checkout'] = array(0 => array('ForeignUUID'));
		$a_uniqueIndexes['sys_fphp_files'] = array(0 => array('BranchId', 'ForeignUUID', 'Name', 'Major', 'Minor'));
		$a_uniqueIndexes['sys_fphp_flex'] = array(0 => array('TableUUID', 'FieldName'));
		$a_uniqueIndexes['sys_fphp_formelement_forestdata'] = array(0 => array('formelementUUID', 'forestdataUUID'));
		$a_uniqueIndexes['sys_fphp_formelement_sqltype'] = array(0 => array('formelementUUID', 'sqltypeUUID'));
		$a_uniqueIndexes['sys_fphp_formelement_validationrule'] = array(0 => array('formelementUUID', 'validationruleUUID'));
		$a_uniqueIndexes['sys_fphp_formkey'] = array(0 => array('UUID', 'SessionUUID', 'Timestamp', 'FormId'));
		$a_uniqueIndexes['sys_fphp_identifier'] = array(0 => array('IdentifierName'));
		$a_uniqueIndexes['sys_fphp_language'] = array(0 => array('Code', 'Language'));
		$a_uniqueIndexes['sys_fphp_role_permission'] = array(0 => array('roleUUID', 'permissionUUID'));
		$a_uniqueIndexes['sys_fphp_session'] = array(0 => array('UUID', 'UserUUID'));
		$a_uniqueIndexes['sys_fphp_subconstraint'] = array(0 => array('TableUUID', 'SubTableUUID'));
		$a_uniqueIndexes['sys_fphp_subrecords'] = array(0 => array('UUID', 'HeadUUID'));
		$a_uniqueIndexes['sys_fphp_systemmessage'] = array(0 => array('IdInternal', 'LanguageCode'));
		$a_uniqueIndexes['sys_fphp_tablefield'] = array(0 => array('TableUUID', 'Order'), 1 => array('TableUUID', 'FieldName'));
		$a_uniqueIndexes['sys_fphp_tablefield_validationrule'] = array(0 => array('TablefieldUUID', 'ValidationruleUUID'));
		$a_uniqueIndexes['sys_fphp_translation'] = array(0 => array('BranchId', 'LanguageCode', 'Name'));
		$a_uniqueIndexes['sys_fphp_user'] = array(0 => array('User'));
		$a_uniqueIndexes['sys_fphp_usergroup_role'] = array(0 => array('usergroupUUID', 'roleUUID'));
		$a_uniqueIndexes['sys_fphp_usergroup_user'] = array(0 => array('usergroupUUID', 'userUUID'));
		
		/* iterate all unique indexes */
		foreach ($a_uniqueIndexes as $s_table => $a_indexes) {
			$i = 1;
			
			foreach ($a_indexes as $a_index) {
				/* create new index command */
				$o_queryAlter = new \fPHP\Base\forestSQLQuery($o_glob->Base->{$o_glob->ActiveBase}->BaseGateway, \fPHP\Base\forestSQLQuery::ALTER, $s_table);
				
				$o_constraint = new \fPHP\Base\forestSQLConstraint($o_queryAlter);
					$o_constraint->Constraint = 'UNIQUE';
					$o_constraint->Name = $s_table . '_unqiue' . $i;
					$o_constraint->AlterOperation = 'ADD';
					
					foreach ($a_index as $s_index) {
						$o_constraint->Columns->Add(new forestString($s_index));
					}
					
				$o_queryAlter->Query->Constraints->Add($o_constraint);
				
				/* execute create command to add unique index for mongodb */
				$o_glob->Base->{$o_glob->ActiveBase}->FetchQuery($o_queryAlter, false, false);
				
				echo 'Created index[' . $s_table . '_unqiue' . $i . '] on table[' . $s_table . ']<br>';
				$i++;
			}
		}
	}
}
?>