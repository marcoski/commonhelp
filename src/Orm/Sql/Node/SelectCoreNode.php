<?php

namespace Commonhelp\Orm\Sql;

class SelectCoreNode extends Node{

	public function __construct(){
		$this['source'] = new JoinSourceNode(null, null);	
		$this['set_quantifier'] = null;
	}
	
	public function from($value=null){
		if(null === $value){
			return $this['source']['left'];
		}
		
		$this['source']['left'] = $value;
	}
}
