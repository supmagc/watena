<?php

class Mapping extends Object {

	private $m_aData = array(
		'scheme' => 'http',
		'user' => null,
		'pass' => null,
		'host' => 'localhost',
		'port' => 80
	);

	
	public function __construct() {
		/*
		$sUrl = Request::protocol() . '://';
		if($_SERVER["SERVER_PORT"] != 80) {
			$sUrl .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}
		else {
			$sUrl .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		$this->m_oUrl = new Url($sUrl);
		*/
	}
	
	public static function init() {
		// Assure REQUEST_URI
		/*
		if(!isset($_SERVER['REQUEST_URI'])) {
			$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'],1 );
			if(isset($_SERVER['QUERY_STRING'])) {
				$_SERVER['REQUEST_URI'].='?'.$_SERVER['QUERY_STRING'];
			}
		}
		*/
		// Assure SERVER_PORT
		
		// Assure HTTP_USER_AGENT
	}
}

/** --------------------------------------------------------------------------------------------------------------------
 *
 */
function _cfg($option=null, $fallback=null) {
	static $_configs = array(/*
			'uri_hidden_script' => true;
	'uri_relative_public' => false;
	'uri_fallback_base' => 'http://localhost/application/';
	'uri_fallback_scheme' => 'http';
	'uri_fallback_user' => '';
	'uri_fallback_pass' => '';
	'uri_fallback_host' => 'localhost';
	'uri_fallback_port' => '';
	'uri_fallback_script' => 'index.php';
	'uri_separator_scheme' => '://';
	'uri_separator_auth' => ':';
	'uri_separator_host' =? '@';
	*/);

	if (isset($_configs[$option])) return $_configs[$option]; else return $fallback;
}
/** --------------------------------------------------------------------------------------------------------------------
 *
 */
class URI
{
	/** ----------------------------------------------------------------------------------------------------------------
	 *
	 */
	protected static $hidden_script;
	/** ----------------------------------------------------------------------------------------------------------------
	 *
	 */

	protected static $host_base;
	protected static $app_base;
	protected static $script_base;
	protected static $current_base;
	protected static $public_base;
	/** ----------------------------------------------------------------------------------------------------------------
	 *
	 */
	protected static $_segments;
	protected static $_parsers;
	/** ----------------------------------------------------------------------------------------------------------------
	 *
	 */
	public static function __init()
	{
		self::$hidden_script = _cfg('uri_hidden_script', false);

		if (self::$_segments = self::extract()) {
			self::_bases();
			self::_parsers();
		}
	}
	/** ----------------------------------------------------------------------------------------------------------------
	 *
	 */
	protected static function extract()
	{
		$script_uri = (isset($_SERVER['SCRIPT_URI'])) ? parse_url($_SERVER['SCRIPT_URI']) : array();
		if (empty($script_uri)) {
			$script_uri = parse_url(_cfg('uri_fallback_base', ''));
		}

		if (isset($_SERVER['PHP_SELF'])) {
			$script_path = preg_split('/[\/]/', $_SERVER['PHP_SELF'], -1, PREG_SPLIT_NO_EMPTY);
		} elseif (isset($_SERVER['REQUEST_URI']) && strlen($_SERVER['REQUEST_URI'])>2) {
			$script_path = preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']);
			$script_path = preg_split('/[\/]/', $script_path, -1, PREG_SPLIT_NO_EMPTY);
		} elseif (isset($script_uri['path'])) {
			$script_path = preg_split('/[\/]/', $script_uri['path'], -1, PREG_SPLIT_NO_EMPTY);
		} elseif (isset($_SERVER['SCRIPT_NAME'])) {
			$script_path = preg_split('/[\/]/', $_SERVER['SCRIPT_NAME'], -1, PREG_SPLIT_NO_EMPTY);
		} else {
			$script_path = array();
		}

		if (isset($_SERVER['SCRIPT_FILENAME'])) {
			$script_name = basename($_SERVER['SCRIPT_FILENAME']);
		} elseif (isset($_SERVER['SCRIPT_NAME'])) {
			$script_name = basename($_SERVER['SCRIPT_NAME']);
		} else {
			$script_name = _cfg('uri_fallback_script', '');
		}

		$script_split = (is_string($script_name)) ? array_search($script_name, $script_path, TRUE) : NULL;

		if (isset($_SERVER['REQUEST_SCHEME'])) {
			$uri['scheme'] = $_SERVER['REQUEST_SCHEME'];
		} elseif (isset($_SERVER['SERVER_PROTOCOL'])) {
			$uri['scheme'] = strtolower($_SERVER['SERVER_PROTOCOL']);
			$uri['scheme'] = substr($uri['Scheme'], 0, strpos($uri['Scheme'], '/'));
			$uri['scheme'] .= (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on') ? 's' : '';
		} elseif (isset($script_uri['scheme'])) {
			$uri['scheme'] = $script_uri['scheme'];
		} else {
			$uri['scheme'] = _cfg('uri_fallback_scheme', '');
		}

		if (isset($_SERVER['PHP_AUTH_USER'])) {
			$uri['user'] = $_SERVER['PHP_AUTH_USER'];
		} elseif (isset($script_uri['user'])) {
			$uri['user'] = $script_uri['user'];
		} else {
			$uri['user'] = _cfg('uri_fallback_user', '');
		}

		if (isset($_SERVER['PHP_AUTH_PW'])) {
			$uri['pass'] = $_SERVER['PHP_AUTH_PW'];
		} elseif (isset($script_uri['pass'])) {
			$uri['pass'] = $script_uri['pass'];
		} else {
			$uri['pass'] = _cfg('uri_fallback_pass', '');
		}

		if (isset($_SERVER['SERVER_NAME'])) {
			$uri['host'] = $_SERVER['SERVER_NAME'];
		} elseif (isset($_SERVER['HTTP_HOST'])) {
			$uri['host'] = $_SERVER['HTTP_HOST'];
		} elseif (isset($script_uri['host'])) {
			$uri['host'] = $script_uri['host'];
		} else {
			$uri['host'] = _cfg('uri_fallback_host', '');
		}

		if (isset($script_uri['port'])) {
			$uri['port'] = $script_uri['port'];
		} else {
			$uri['port'] = _cfg('uri_fallback_port', '');
		}

		if (is_numeric($script_split)) {
			$uri['path'] = implode('/', array_slice($script_path, 0, $script_split));
		} else {
			$uri['path'] = '';
		}

		if (is_string($script_name)) {
			$uri['script'] = $script_name;
		} else {
			$uri['script'] = '';
		}

		if (isset($_SERVER['PATH_INFO'])) {
			$uri['info'] = implode('/', preg_split('/[\/]/', $_SERVER['PATH_INFO'], -1, PREG_SPLIT_NO_EMPTY));
		} elseif (is_numeric($script_split)) {
			$uri['info'] = implode('/', array_slice($script_path, $script_split+1));
		} else {
			$uri['info'] = '';
		}

		return $uri;
	}
	/** ----------------------------------------------------------------------------------------------------------------
	 *
	 */
	protected static function _bases()
	{
		self::$host_base = self::compile('host');
		self::$app_base = self::compile('path');
		self::$script_base = self::$app_base.((self::$hidden_script) ? '' : '/'.self::$_segments['script']);
		self::$current_base = self::$script_base.((empty(self::$_segments['info'])) ? '' : '/'.self::$_segments['info']);

		if (_cfg('uri_relative_public', false) === true) self::$public_base = '/public';
		else self::$public_base = self::$app_base.'/public';

		return true;
	}
	/** ----------------------------------------------------------------------------------------------------------------
	 *
	 */
	protected static function _parsers()
	{
		if (!isset(self::$_Parsers)) {
			self::$_parsers['SR_Key'][] = '%hostbase%';
			self::$_parsers['SR_Data'][] =& self::$host_base;
			self::$_parsers['SR_Key'][] = '%appbase%';
			self::$_parsers['SR_Data'][] =& self::$app_base;
			self::$_parsers['SR_Key'][] = '%scriptbase%';
			self::$_parsers['SR_Data'][] =& self::$script_base;
			self::$_parsers['SR_Key'][] = '%currentbase%';
			self::$_parsers['SR_Data'][] =& self::$current_base;
			self::$_parsers['SR_Key'][] = '%publicbase%';
			self::$_parsers['SR_Data'][] =& self::$_segments['scheme'];
			self::$_parsers['SR_Key'][] = '%user%';
			self::$_parsers['SR_Data'][] =& self::$_segments['user'];
			self::$_parsers['SR_Key'][] = '%pass%';
			self::$_parsers['SR_Data'][] =& self::$_segments['pass'];
			self::$_parsers['SR_Key'][] = '%host%';
			self::$_parsers['SR_Data'][] =& self::$_segments['host'];
			self::$_parsers['SR_Key'][] = '%port%';
			self::$_parsers['SR_Data'][] =& self::$_segments['port'];
			self::$_parsers['SR_Key'][] = '%path%';
			self::$_parsers['SR_Data'][] =& self::$_segments['path'];
			self::$_parsers['SR_Key'][] = '%script%';
			self::$_parsers['SR_Data'][] =& self::$_segments['script'];
			self::$_parsers['SR_Key'][] = '%info%';
			self::$_parsers['SR_Data'][] =& self::$_segments['info'];
		} return true;
	}
	/** ----------------------------------------------------------------------------------------------------------------
	 *
	 */
	public static function compile($until=null)
	{
		$uri= '';
		$until = (is_string($until)) ? strtolower($until) : $until;
		if ($until === 'scheme') {
			return $uri .= (self::$_segments['scheme'] !== '') ? self::$_segments['scheme'].'://' : '';
		} else { $uri .= (self::$_segments['scheme'] !== '') ? self::$_segments['scheme'].'://' : ''; }
		if ($until === 'user') {
			return $uri .= (self::$_segments['user'] !== '') ? self::$_segments['user'].':' : '';
		} else { $uri .= (self::$_segments['user'] !== '') ? self::$_segments['user'] : ''; }
		$uri .= (self::$_segments['user'] !== '' || self::$_segments['pass'] !== '') ? ':' : '';
		if ($until === 'pass') {
			return $uri .= (self::$_segments['pass'] !== '') ? self::$_segments['pass'].'@' : '';
		} else { $uri .= (self::$_segments['pass'] !== '') ? self::$_segments['pass'] : ''; }
		$uri .= (self::$_segments['user'] !== '' || self::$_segments['pass'] !== '') ? '@' : '';
		if ($until === 'host') {
			return $uri .= (self::$_segments['host'] !== '') ? self::$_segments['host'] : '';
		} else { $uri .= (self::$_segments['host'] !== '') ? self::$_segments['host'] : ''; }
		if ($until === 'port') {
			return $uri .= (self::$_segments['port'] !== '') ? ':'.self::$_segments['port'] : '';
		} else { $uri .= (self::$_segments['port'] !== '') ? ':'.self::$_segments['port'] : ''; }
		if ($until === 'path') {
			return $uri .= (self::$_segments['path'] !== '') ? '/'.self::$_segments['path'] : '';
		} else { $uri .= (self::$_segments['path'] !== '') ? '/'.self::$_segments['path'] : ''; }
		if ($until === 'script') {
			return $uri .= (self::$_segments['script'] !== '') ? '/'.self::$_segments['script'] : '';
		} else { $uri .= (self::$_segments['script'] !== '') ? '/'.self::$_segments['script'] : ''; }
		if ($until === 'info') {
			return $uri .= (self::$_segments['info'] !== '') ? '/'.self::$_segments['info'] : '';
		} else { $uri .= (self::$_segments['info'] !== '') ? '/'.self::$_segments['info'] : ''; }
		return $uri;
	}
	/** ----------------------------------------------------------------------------------------------------------------
	 *
	 */
	public static function segment($name=null)
	{
		if (isset(self::$_segments[$name])) {
			return self::$_segments[$name];
		} return false;
	}
	/** ----------------------------------------------------------------------------------------------------------------
	 *
	 */
	public static function segments() {
		return self::$_segments;
	}
	/** ----------------------------------------------------------------------------------------------------------------
	 *
	 */
	public static function base($base='app')
	{
		switch ($base) {
			case 'host':
			case 'domain':
				return self::$host_base;
				break;
			case 'app':
			case 'base':
				return self::$app_base;
				break;
			case 'script':
			case 'index':
				return self::$script_base;
				break;
			case 'current':
			case 'this':
				return self::$current_base;
				break;
			case 'public':
			case 'web':
				return self::$public_base;
				break;
			case 'all':
				return array(
				'host'=>self::$host_base,
				'app'=>self::$app_base,
				'script'=>self::$script_base,
				'current'=>self::$current_base,
				'public'=>self::$public_base,
				);
				break;
		} return '';
	}
	/** ----------------------------------------------------------------------------------------------------------------
	 *
	 */
	public static function parse($string=null)
	{
		if (is_string($string)) {
			return str_replace(self::$_parsers['SR_Key'], self::$_parsers['SR_Data'], $string);
		} elseif (is_array($string)) {
			foreach ($string as $k => $v) {
				$parsed[$k] = self::$replace($v);
			} return $parsed;
		} return $string;
	}
} URI::__init();
?>