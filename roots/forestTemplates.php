<?php
/* +--------------------------------+ */
/* |				    | */
/* | forestPHP V0.1.1 (0x1 0001B)   | */
/* |				    | */
/* +--------------------------------+ */

/*
 * + Description +
 * template class using to print standard data elements
 *
 * + Version Log +
 * Version	Developer	Date		Comment
 * 0.1.1 alpha	renatus		2019-08-15	added to framework	
 */

class forestTemplates {
	use forestData;
	
	/* Fields */

	const LANDINGPAGE = 'landingpage';
	
	private $Type;
	private $PlaceHolders;

	const LANDINGPAGETXT = <<< EOF
	%0
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