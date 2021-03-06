<?php

namespace Commonhelp\Orm\Sql;

use Commonhelp\Orm\Sql\Node\InsertNode;
use Commonhelp\Orm\Sql\Node\AttributeNode;
use Commonhelp\Orm\Sql\Node\LitteralNode;
use Commonhelp\Orm\Sql\Node\ValuesNode;

class InsertManager extends AstSqlManager{
	
	public function __construct(){
		parent::__construct();
		$this->ast = new InsertNode();
	}
	
	public function into(Sql $table){
		$this->ast['relation'] = new AttributeNode(null, $table);
		return $this;
	}
	
	public function columns(AttributeNode $column){
		$this->ast['columns'][] = $column;
	}
	
	public function insert($values){
		if(is_string($values)){
			$this->ast['values'] = new LitteralNode($values);
		}else if(is_array($values)){
			$this->ast['relation'] = current(current($values));
			$vals = array();
			foreach($values as $v){
				list($column, $value) = $v;
				$this->columns($column);
				$vals[] = $value;
			}
			
			$this->ast['values'] = $this->createValues($vals);
		}
	}
	
	public function select(SelectManager $select){
		$this->ast['select'] = $select;
	}
	
	protected function createValues($vals){
		return new ValuesNode($vals, $this->ast['columns']);
	}
	
}

