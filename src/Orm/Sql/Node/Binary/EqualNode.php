<?php

namespace Commonhelp\Orm\Sql;

use Commonhelp\Util\Expression\Operator\OperatorVisitor;
use Commonhelp\Util\Expression\Operator\SymbolExpression;
class EqualNode extends SymbolExpression{
	
	public function __construct(LitteralNode $left, LitteralNode $right){
		if($right->getValue() === null){
			$null = new LitteralNode('NULL');
			parent::__construct($left, $null, Node::IS);
		}else{
			parent::__construct($left, $right, OperatorVisitor::EQUAL);
		}
	}
}
