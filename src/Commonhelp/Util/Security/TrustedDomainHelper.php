<?php
namespace Commonhelp\Util\Security;

use Commonhelp\App\Http\Request;
use Commonhelp\Config\Config;

class TrustedDomainHelper{
	
	/** @var SystemConfig */
	private $config;
	
	/**
	 * @param SecurityConfig $config
	 */
	function __construct(Config $config) {
		$this->config = $config;
	}
	
	/**
	 * Strips a potential port from a domain (in format domain:port)
	 * @param string $host
	 * @return string $host without appended port
	 */
	private function getDomainWithoutPort($host) {
		$pos = strrpos($host, ':');
		if ($pos !== false) {
			$port = substr($host, $pos + 1);
			if (is_numeric($port)) {
				$host = substr($host, 0, $pos);
			}
		}
		return $host;
	}
	
	/**
	 * Checks whether a domain is considered as trusted from the list
	 * of trusted domains. If no trusted domains have been configured, returns
	 * true.
	 * This is used to prevent Host Header Poisoning.
	 * @param string $domainWithPort
	 * @return bool true if the given domain is trusted or if no trusted domains
	 * have been configured
	 */
	public function isTrustedDomain($domainWithPort) {
		$domain = $this->getDomainWithoutPort($domainWithPort);
		// Read trusted domains from config
		$trustedList = $this->config->getTrusteddomains([]);
		if(!is_array($trustedList)) {
			return false;
		}
		// TODO: Workaround for older instances still with port applied. Remove for ownCloud 9.
		if(in_array($domainWithPort, $trustedList)) {
			return true;
		}
		// Always allow access from localhost
		if (preg_match(Request::REGEX_LOCALHOST, $domain) === 1) {
			return true;
		}
		return in_array($domain, $trustedList);
	}
}