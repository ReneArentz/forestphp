<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.4.0 (0x1 00011)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * class for holding information about page limit of current page view
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.2 alpha	renatus		2019-08-22	added to framework	
 */

class forestLimit {
	use forestData;
	
	/* Fields */
	
	private $Page;
	private $Start;
	private $Interval;
	private $Amount;
	private $DivClass;
	private $SmallDisplay;
	private $Align;
	private $SequencePages;
	
	/* Properties */
	
	/* Methods */
	
	public function __construct() {
		$this->Page = new forestInt;
		$this->Start = new forestInt;
		$this->Interval = new forestInt;
		$this->Amount = new forestInt;
		$this->DivClass = new forestString;
		$this->SmallDisplay = new forestBool;
		$this->Align = new forestList(array('left', 'center', 'right'), 'right');
		$this->SequencePages = new forestInt(2);
	}
	
	function __toString() {
		$o_glob = forestGlobals::init();
		
		/* calculate link and paging values */
		$a_parameters = $o_glob->URL->Parameters;
		unset($a_parameters['viewKey']);
		unset($a_parameters['editKey']);
		unset($a_parameters['deleteKey']);
		$i_page = $this->Page->value;
		$i_pages = ceil(intval($this->Amount->value) / intval($this->Interval->value));
		
		if ($i_page <= 0) {
			$i_page = 1;
		} else if ($i_page >= $i_pages) {
			$i_page = $i_pages;
		}
		
		/* start rendering limit element */
		$s_foo = '<div';
		
		if (issetStr($this->DivClass->value)) {
			$s_foo .= ' class="' . $this->DivClass->value . '"';
		}
		
		/* align options */
		if (issetStr($this->Align->value)) {
			if ($this->Align->value == 'center') {
				$s_foo .= ' class="text-center"';
			} else if ($this->Align->value == 'right') {
				$s_foo .= ' class="text-right"';
			}
		}
		
		$s_foo .= '>' . "\n";
		
		$s_foo .= '<ul class="pagination" style="margin: 0;">' . "\n";
		
		/* calculate amout of pages */
		if (($this->Start->value + $this->Interval->value) > $this->Amount->value) {
			$amountPage = $this->Amount->value;
		} else {
			$amountPage = $this->Start->value + $this->Interval->value;
		}
		
		if (!$this->SmallDisplay->value) {
			/* no small display contains current page and amount of pages display */
			$s_foo .= '<li class="page-item"><span class="page-link">';
			$s_foo .= '<b>(' . ($this->Start->value + 1) . '-' . $amountPage . ' / ' . $this->Amount->value . ')</b> - ';
			$s_foo .= '<b><i>' . $o_glob->GetTranslation('limitPage', 1) . ' ' . $i_page . '/' . $i_pages . '</i></b>';
			$s_foo .= '</span></li>' . "\n";
		}
		
		/* determine link action */
		$s_action = $o_glob->URL->Action;
		
		if ($o_glob->Security->SessionData->Exists('lastView')) {
			if ( ($o_glob->Security->SessionData->{'lastView'} == forestBranch::DETAIL) && ($o_glob->OriginalView != forestBranch::DETAIL) ) {
				$s_action = 'view';
			}
		}
		
		/* first page */
		$a_parameters['page'] = 1;
		$s_foo .= '<li class="page-item"><a class="page-link" href="' . forestLink::Link($o_glob->URL->Branch, $s_action, $a_parameters) . '" title="' . $o_glob->GetTranslation('limitFirstPage', 1) . '"><span class="glyphicon glyphicon-step-backward"></span></a></li>' . "\n";
		
		
		if ($i_page > 1) {
			/* previous page */
			$a_parameters['page'] = $i_page - 1;
			$s_foo .= '<li class="page-item"><a class="page-link" id="a-button-limit-view-left" href="' . forestLink::Link($o_glob->URL->Branch, $s_action, $a_parameters) . '" title="' . $o_glob->GetTranslation('limitPreviousPage', 1) . '"><span class="glyphicon glyphicon-triangle-left"></span></a></li>' . "\n";
		}
		
		for ($i = 1; $i <= $i_pages; $i++) {
			if ($i == $i_page) {
				/* current page */
				$s_foo .= '<li class="page-item active"><span class="page-link">'.$i.'</span></li>' . "\n";
			} elseif ( (($i <= $i_page + $this->SequencePages->value) && ($i > 0)) && (($i >= $i_page - $this->SequencePages->value) && ($i <= $i_pages)) ) {
				/* other pages greater than or lower than sequence value */
				$a_parameters['page'] = $i;
				$s_foo .= '<li class="page-item"><a class="page-link" href="' . forestLink::Link($o_glob->URL->Branch, $s_action, $a_parameters) . '" title="' . $o_glob->GetTranslation('limitPage', 1) . '">'.$i.'</a></li>' . "\n";
			}
		}
		
		if ($i_page < $i_pages) {
			/* next page */
			$a_parameters['page'] = $i_page + 1;
			$s_foo .= '<li class="page-item"><a class="page-link" id="a-button-limit-view-right" href="' . forestLink::Link($o_glob->URL->Branch, $s_action, $a_parameters) . '" title="' . $o_glob->GetTranslation('limitNextPage', 1) . '"><span class="glyphicon glyphicon-triangle-right"></span></a></li>' . "\n";
		}
		
		/* last page */
		$a_parameters['page'] = $i_pages;
		$s_foo .= '<li class="page-item"><a class="page-link" href="' . forestLink::Link($o_glob->URL->Branch, $s_action, $a_parameters) . '" title="' . $o_glob->GetTranslation('limitLastPage', 1) . '"><span class="glyphicon glyphicon-step-forward"></span></a></li>' . "\n";
		
		$s_foo .= '</ul>' . "\n";
		
        $s_foo .= '</div>' . "\n";
		
		return $s_foo;
	}
}
?>