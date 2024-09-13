<?php
/**
 * class for holding information about page limit of current page view
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestany.net>
 * @copyright   (c) 2024 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.1.0 stable
 * @link        https://forestany.net
 * @object-id   0x1 00011
 * @since       File available since Release 0.1.2 alpha
 * @deprecated  -
 *
 * @version log Version			Developer	Date		Comment
 * 				0.1.2 alpha		renea		2019-08-22	added to framework
 * 				0.9.0 beta		renea		2020-01-29	changes for bootstrap 4
 * 				1.1.0 stable	renea		2023-11-03	fix page count bug, if there are no records
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

class forestLimit {
	use \fPHP\Roots\forestData;
	
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
	
	/**
	 * constructor of forestLimit class
	 *
	 * @return null
	 *
	 * @access public
	 * @static no
	 */
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
	
	/**
	 * returns limit pagination
	 *
	 * @return string  html limit pagination element
	 *
	 * @throws forestException if error occurs
	 * @access public
	 * @static no
	 */
	public function __toString() {
		$o_glob = \fPHP\Roots\forestGlobals::init();
		
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
		
		if ($i_pages <= 0) {
			$i_pages = 0;
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
				$s_foo .= ' class="text-end"';
			}
		}
		
		$s_foo .= '>' . "\n";
		
		$s_foo .= '<ul class="pagination justify-content-end">' . "\n";
		
		/* calculate amout of pages */
		if (($this->Start->value + $this->Interval->value) > $this->Amount->value) {
			$amountPage = $this->Amount->value;
		} else {
			$amountPage = $this->Start->value + $this->Interval->value;
		}
		
		if (!$this->SmallDisplay->value) {
			/* no small display contains current page and amount of pages display */
			$s_foo .= '<li class="page-item d-none d-lg-flex"><span class="page-link">';
			$s_foo .= '<b>(' . (($this->Amount->value <= 0) ? 0 : ($this->Start->value + 1)) . '-' . $amountPage . ' / ' . $this->Amount->value . ')</b> - ';
			$s_foo .= '<b><i>' . $o_glob->GetTranslation('limitPage', 1) . ' ' . $i_page . '/' . (($i_pages == 0) ? 1 : $i_pages) . '</i></b>';
			$s_foo .= '</span></li>' . "\n";
		}
		
		/* paging only if we have elements */
		if ($i_pages > 0) {
			/* determine link action */
			$s_action = $o_glob->URL->Action;
			
			if ($o_glob->Security->SessionData->Exists('lastView')) {
				if ( ($o_glob->Security->SessionData->{'lastView'} == \fPHP\Branches\forestBranch::DETAIL) && ($o_glob->OriginalView != \fPHP\Branches\forestBranch::DETAIL) ) {
					$s_action = 'view';
				}
			}
			
			/* first page */
			$a_parameters['page'] = 1;
			$s_foo .= '<li class="page-item"><a class="page-link" href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, $s_action, $a_parameters) . '" title="' . $o_glob->GetTranslation('limitFirstPage', 1) . '"><span class="bi bi-skip-backward-fill"></span></a></li>' . "\n";
			
			
			if ($i_page > 1) {
				/* previous page */
				$a_parameters['page'] = $i_page - 1;
				$s_foo .= '<li class="page-item"><a class="page-link" id="a-button-limit-view-left" href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, $s_action, $a_parameters) . '" title="' . $o_glob->GetTranslation('limitPreviousPage', 1) . '"><span class="bi bi-caret-left-fill"></span></a></li>' . "\n";
			}
			
			for ($i = 1; $i <= $i_pages; $i++) {
				if ($i == $i_page) {
					/* current page */
					$s_foo .= '<li class="page-item active"><span class="page-link">'.$i.'</span></li>' . "\n";
				} elseif ( (($i <= $i_page + $this->SequencePages->value) && ($i > 0)) && (($i >= $i_page - $this->SequencePages->value) && ($i <= $i_pages)) ) {
					/* other pages greater than or lower than sequence value */
					$a_parameters['page'] = $i;
					$s_foo .= '<li class="page-item"><a class="page-link" href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, $s_action, $a_parameters) . '" title="' . $o_glob->GetTranslation('limitPage', 1) . '">'.$i.'</a></li>' . "\n";
				}
			}
			
			if ($i_page < $i_pages) {
				/* next page */
				$a_parameters['page'] = $i_page + 1;
				$s_foo .= '<li class="page-item"><a class="page-link" id="a-button-limit-view-right" href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, $s_action, $a_parameters) . '" title="' . $o_glob->GetTranslation('limitNextPage', 1) . '"><span class="bi bi-caret-right-fill"></span></a></li>' . "\n";
			}
			
			/* last page */
			$a_parameters['page'] = $i_pages;
			$s_foo .= '<li class="page-item"><a class="page-link" href="' . \fPHP\Helper\forestLink::Link($o_glob->URL->Branch, $s_action, $a_parameters) . '" title="' . $o_glob->GetTranslation('limitLastPage', 1) . '"><span class="bi bi-skip-forward-fill"></span></a></li>' . "\n";
		}

		$s_foo .= '</ul>' . "\n";
		
        $s_foo .= '</div>' . "\n";
		
		return $s_foo;
	}
}
?>