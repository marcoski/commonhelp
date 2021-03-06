<?php

namespace Commonhelp\Orm\DataLayer;
use Commonhelp\Orm\Exception\DataLayerException;

use PDO;
use PDOException;
use Closure;
use Commonhelp\Util\Expression\AstManager;
use Commonhelp\Orm\Sql\InsertManager;

class PdoDataLayer extends DataLayerInterface{
	
	protected $pdo;
	protected $adaptee;
	
	protected $username;
	protected $password;
	protected $dsn;
	
	protected static $validTypes = array(
		'mysql',
		'sqlite'	
	);
	
	
	public function __construct(array $options = array()){
		if(empty($options)){
			$options['dsn'] = null;
			$options['username'] = '';
			$options['password'] = '';
		}
		if(!isset($options['dsn'])){
			$options['dsn'] = null;
		}
		if(!isset($options['username'])){
			$options['username'] = '';
		}
		if(!isset($options['password'])){
			$options['password'] = '';
		}
		
		$this->username = $options['username'];
		$this->password = $options['password'];
		$this->dsn = $options['dsn'];
		
	}
	
	public static function instance(array $options=array()){
		if(!static::$instance){
			static::$instance = new PdoDataLayer($options);
		}
		
		return static::$instance;
	}
	
	public function connect(array $options = array()){
		if(empty($options) && is_null($this->dsn)){
			throw new DataLayerException('DSN string must be provided to a database connection');
		}else if(!empty($options)){
			if(!isset($options['dsn'])){
				throw new DataLayerException('DSN string must be provided to a database connection');
			}
			if(!isset($options['username'])){
				$options['username'] = '';
			}
			if(!isset($options['password'])){
				$options['password'] = '';
			}
			$this->username = $options['username'];
			$this->password = $options['password'];
			$this->dsn = $options['dsn'];
		}
		$this->pdo = new PDO($this->dsn, $this->username, $this->password);
		/**
		 * MUST USE FULL NAMESPACE
		 *@see http://stackoverflow.com/questions/18337650/how-to-check-the-existence-of-a-namespace-in-php
		 */
		$adaptee = "Commonhelp\\Orm\\DataLayer\\DataAdaptee\\".ucfirst($this->getDriver().'DataAdaptee');
		$visitor = "Commonhelp\\Orm\\Sql\\Visitor\\".ucfirst($this->getDriver().'Visitor');
		$this->adaptee = new $adaptee();
		$this->visitor = new $visitor($this);
	}
	
	public static function getAvailableDriver(){
		return PDO::getAvailableDrivers();
	}
	
	public function quote($string){
		return $this->pdo->quote($string);
	}
	
	public function quoteColumnName($string){
		return $this->adaptee->quoteColumnName($string);
	}
	
	public function quoteTableName($string){
		return $this->adaptee->quoteTableName($string);
	}
	
	public function read(AstManager $manager){
		return $this->query($manager->toSql());
	}
	
	public function write(AstManager $manager){
		$this->query($manager->toSql());
		if($manager instanceof InsertManager){
			return $this->lastInsertId();
		}else{
			return true;
		}
	}
	
	public function close(){
		$this->pdo = null;
		$this->adaptee = null;
		$this->visitor = null;
	}
	
	public function beginTransaction(){
		
	}
	
	public function commit(){
		
	}
	
	public function rollback(){
		
	}
	
	public function transactional(Closure $func){
		
	}
	
	protected function lastInsertId($name = null){
		$this->pdo->lastInsertId($name);
	}
	
	protected function query($sql){
		$this->lastQuery = $sql;
		$stmnt = $this->pdo->prepare($sql);
		$stmnt->setFetchMode(PDO::FETCH_ASSOC);
		try{
			if(!$stmnt->execute()){
				throw new DataLayerException("Error executing query: {$sql}");
			}
		}catch(PDOException $e){
			throw new DataLayerException($e->getMessage());
		}
		
		return $stmnt->fetchAll();
	}
	
	protected function getDriver(){
		return $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
	}
	
	public static function isValidType($type){
		if($type === ''){
			return false;
		}
		return in_array($type, self::$validTypes);
	}
	
}
