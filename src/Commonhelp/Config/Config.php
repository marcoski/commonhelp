<?php

namespace Commonhelp\Config;

use Commonhelp\Util\Collections\ArrayCollection;
abstract class Config extends ArrayCollection{
	
	/**
	 * Magic method to have any kind of setters or getters.
	 *
	 * @param string $name      Getter/Setter name
	 * @param array  $arguments Method arguments
	 *
	 * @return mixed
	 */
	public function __call($name, array $arguments){
		if($this->isValidMethod($name)){
			$name = strtolower($name);
			$prefix = substr($name, 0, 3);
			$parameter = substr($name, 3);
			if ($prefix === 'set' && isset($arguments[0])) {
				$this->container[$parameter] = $arguments[0];
				return $this;
			} elseif ($prefix === 'get') {
				$default_value = isset($arguments[0]) ? $arguments[0] : null;
				return isset($this->container[$parameter]) ? $this->container[$parameter] : $default_value;
			}
		}else{
			throw new \RuntimeException('Invalid configuration method');
		}
	}
	
	abstract function isValidMethod($method);
}
