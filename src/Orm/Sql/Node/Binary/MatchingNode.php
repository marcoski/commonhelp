<?php

namespace Commonhelp\Orm\Sql;

class MatchingNode extends BinaryNode{
	
	protected $escape;
	
	public function __construct(LitteralNode $left, LitteralNode $right, $escape=null){
		parent::__construct($left, $right);
		if(null !== $escape && is_string($escape)){
			$escape = new LitteralNode($escape);
		}
		$this->escape = $escape;
	}
	
	public function getEscape(){
		return $this->escape;
	}
	
}
