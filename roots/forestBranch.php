<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.1.1 (0x1 00014)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * abstract class for all branches
 * core functionality for rendering and editing records which are managed in the twig object
 * sub record editing support as well
 * all functions can be overwritten for user specific use case
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.0 alpha	renatus		2019-08-04	first build
 * 0.1.1 alpha	renatus		2019-08-07	added view property and landing page function
 */

abstract class forestBranch {
	use forestData;

	/* Fields */
	
	const LIST = 'list';
	const DETAIL = 'detail';
	
	protected $Twig;
	protected $NextAction;
	protected $Filter;
	protected $StandardView;
	protected $KeepFilter;
	protected $OriginalView;
	
	/* Properties */
	
	/* Methods */
	
	abstract protected function initBranch();
	
	abstract protected function init();
	
	public function __construct() {
		$this->NextAction = new forestBool;
		$this->Filter = new forestBool;
		$this->KeepFilter = new forestBool;
		
		/* call initBranch method to set branch properties within forestBranch objects */
		$this->initBranch();
				
		if (!isset($this->StandardView)) {
			$this->StandardView = forestBranch::LIST;
		}
	
		$o_glob = forestGlobals::init();
		
		$this->OriginalView = $this->StandardView;
		$o_glob->OriginalView = $this->OriginalView;
		
		if (!$o_glob->FastProcessing) {
			/* init navigation object */
			$o_glob->Navigation->InitNavigation();
			
			$i_lastBranchId = 0;
			$i_lastActionId = 0;
			
			/* get using branch and action-id out of session */
			if ($o_glob->Security->SessionData->Exists('lastBranchId')) {
				$i_lastBranchId = $o_glob->Security->SessionData->{'lastBranchId'};
			}
			
			if ($o_glob->Security->SessionData->Exists('lastActionId')) {
				$i_lastActionId = $o_glob->Security->SessionData->{'lastActionId'};
			}
			
			$o_glob->URL->LastBranchId = $i_lastBranchId;
			$o_glob->URL->LastActionId = $i_lastActionId;
			
			/* save filters from last request if you may use them in the new request */
			if ($o_glob->Security->SessionData->Exists('filter')) {
				$o_glob->Security->SessionData->Add($o_glob->Security->SessionData->{'filter'}, 'last_filter');
			} else {
				$o_glob->Security->SessionData->Del('last_filter');
			}
			
			/* if branch or action-id changes in the new request we delete old filter options in user's session */
			if (($o_glob->URL->BranchId != $i_lastBranchId) || ($o_glob->URL->ActionId != $i_lastActionId)) {
				$o_glob->Security->SessionData->Del('filter');
			}
			
			/* save used branch and action-id in session */
			$o_glob->Security->SessionData->Add($o_glob->URL->BranchId, 'lastBranchId');
			$o_glob->Security->SessionData->Add($o_glob->URL->ActionId, 'lastActionId');
			
			/* if available get twig of branch object */
			if (!isset($this->Twig)) {
				$s_foo = $o_glob->URL->Branch . 'Twig';
				
				if (forestAutoLoad::IsReadable('./twigs/' . $s_foo . '.php')) {
					$this->Twig = new $s_foo;
				} else {
					$this->Twig = null;
					
					if (isset($this->Filter)) {
						$this->Filter->value = false;
					}
				}
			}
		}
		
		/* handle branch's action */
		$s_action = $o_glob->URL->Action;		
		
		if (!issetStr($s_action)) {
			$o_glob->URL->Action = 'init';
		}
		
		do {
			$this->NextAction->value = false;
			
			/* if standard action is 'init' we do not need to attach .'Action' to it */
			$s_action = ($o_glob->URL->Action == 'init') ? $o_glob->URL->Action : $o_glob->URL->Action . 'Action';
			$this->$s_action();
			
			if (!$o_glob->FastProcessing) {
				global $b_transaction_active;
				if ($b_transaction_active) {
					$o_glob->Base->{$o_glob->ActiveBase}->ManualCommit();
				}
			}
		} while ($this->NextAction->value);
	}
	
	/* individual branch-classes are calling this method to set next action */
	protected function SetNextAction($p_s_nextAction, $p_s_nextActionAfterReload = null) {
		if ($p_s_nextAction != null) {
			$o_glob = forestGlobals::init();
			$a_branchTree = $o_glob->BranchTree;
			
			if ($p_s_nextAction == 'RELOADBRANCH') {
				if ($p_s_nextActionAfterReload != null) {
					header('Location: '. forestLink::Link($o_glob->URL->Branch, $p_s_nextActionAfterReload));
				} else {
					header('Location: '. forestLink::Link($o_glob->URL->Branch));
				}
			}
			
			/* check if action really exists */
			if (!array_key_exists($p_s_nextAction, $a_branchTree['Id'][$o_glob->URL->BranchId]['actions']['Name'])) {
				throw new forestException('Action[%0] with BranchId[%1] could not be found', array($p_s_nextAction, $o_glob->URL->BranchId));
			}
			
			$o_glob->URL->Action = $p_s_nextAction;
			$o_glob->URL->ActionId = $a_branchTree['Id'][$o_glob->URL->BranchId]['actions']['Name'][$p_s_nextAction];
			
			$this->NextAction->value = true;
		}
	}
	
	/* generates landing page */
	protected function GenerateLandingPage() {
		$o_glob = forestGlobals::init();
		$s_landingPageNavigation = $o_glob->Navigation->RenderLandingPage();
		
		/* use template to render landing page */
		$o_glob->Templates->Add(new forestTemplates(forestTemplates::LANDINGPAGE, array($s_landingPageNavigation)), $o_glob->URL->Branch . 'LandingPage');
	}
}
?>