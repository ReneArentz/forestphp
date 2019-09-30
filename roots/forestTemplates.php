<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.1.4 (0x1 0001B)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * template class using to print standard data elements
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.1 alpha	renatus		2019-08-15	added to framework
 * 0.1.2 alpha	renatus		2019-08-25	added listview and view	
 * 0.1.4 alpha	renatus		2019-09-28	added sublistview	
 */

class forestTemplates {
	use forestData;
	
	/* Fields */

	const LANDINGPAGE = 'landingpage';
	
	const LISTVIEW = 'listview';
	const LISTVIEWOPTIONSTOP = 'listviewoptionstop';
	const LISTVIEWOPTIONSDOWN = 'listviewoptionsdown';
	
	const VIEW = 'view';
	const VIEWOPTIONSTOP = 'viewoptionstop';
	const VIEWOPTIONSDOWN = 'viewoptionsdown';
	
	const SUBLISTVIEW = 'sublistview';
	const SUBLISTVIEWITEM = 'sublistviewitem';
	const SUBLISTVIEWITEMCONTENT = 'sublistviewitemcontent';
	
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
	<div style="margin-bottom: 10px;">
		<div class="row">
			<div class="col-sm-4">
				%0
			</div>
			<div class="col-sm-8 text-right">
				%1
			</div>
		</div>
		
		<div class="row row-eq-height" style="margin-top: 10px;">
			<div class="col-sm-4">
				%2
			</div>
			<div class="col-sm-8 row-eq-height-vertical-center">
				<div class="filter-terms">
				%3
				</div>
			</div>
		</div>
	</div>
	%4
EOF;

	const LISTVIEWOPTIONSDOWNTXT = <<< EOF
	<div>
		<div class="row">
			<div class="col-sm-4">
				%0
			</div>
			<div class="col-sm-8 text-right">
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
	%5
EOF;

	const VIEWOPTIONSTOPTXT = <<< EOF
	<div style="margin-bottom: 10px;">
		<div class="row">
			<div class="col-sm-4">
				%0
			</div>
			<div class="col-sm-8 text-right">
				%1
			</div>
		</div>
		
		<div class="row row-eq-height" style="margin-top: 10px;">
			<div class="col-sm-4">
				%2
			</div>
			<div class="col-sm-8 row-eq-height-vertical-center">
				<div class="filter-terms">
				%3
				</div>
			</div>
		</div>
	</div>
EOF;

	const VIEWOPTIONSDOWNTXT = <<< EOF
	<div>
		<div class="row">
			<div class="col-sm-4">
				%0
			</div>
			<div class="col-sm-8 text-right">
				%1
			</div>
		</div>
	</div>
EOF;

	const SUBLISTVIEWTXT = <<< EOF
<div class="panel-group" id="accordion">
	%0
</div> 
EOF;

	const SUBLISTVIEWITEMTXT = <<< EOF
	<div class="panel panel-default">
		<div class="panel-heading"><a data-toggle="collapse" data-parent="#accordion" href="#%0">
			<h4 class="panel-title">%1</h4>
		</a></div>
		<div id="%0" class="panel-collapse collapse%2"><div class="panel-body">
			%3
		</div></div>
	</div>
EOF;

	const SUBLISTVIEWITEMCONTENTTXT = <<< EOF
	%0
	<div class="table-responsive">
		<table class="table table-hover table-condensed">
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

	/* Properties */
	 
	public function getType() {
		return $this->Type->value;
	}
	
	/* Methods */
	
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
			
			case self::SUBLISTVIEW:
				$this->Type->value = self::SUBLISTVIEW;
			break;
			case self::SUBLISTVIEWITEM:
				$this->Type->value = self::SUBLISTVIEWITEM;
			break;
			case self::SUBLISTVIEWITEMCONTENT:
				$this->Type->value = self::SUBLISTVIEWITEMCONTENT;
			break;
			
			default:
				throw new forestException('Invalid template type[%0]', array($this->Type->value));
			break;
		}
	}
		
	public function __toString() {
		$s_foo = '';
		$s_pointer = strtoupper($this->Type->value) . 'TXT';
		$s_foo .= constant('forestTemplates::' . $s_pointer);
		
		if (count($this->PlaceHolders->value) > 0) {
			$s_foo = forestStringLib::sprintf2($s_foo, $this->PlaceHolders->value);
		}
		
		return $s_foo;
	}
}
?>