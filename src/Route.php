<?php 
namespace Zuuda;

use Exception;
use ReflectionClass;
use Zuuda\RouteController;
use Zuuda\GlobalModifier;
use Zuuda\LocateService;
use Zuuda\Error;
use Zuuda\Text;
use Zuuda\Request;
use Zuuda\Response;
use Zuuda\Query;
use Zuuda\Session;
use Zuuda\Cookie;
use Zuuda\Flash;

class Route implements iRoute 
{
	private static $this = '\Zuuda\Route'; 
	private static $prefix = empc; 
	private static $inited = false;
	private static $headset = array(); 
	
	final public static function Instance() { return call_user_func_array(array(self::$this, '__instance'), array()); }
	final public static function GetInstance() { return call_user_func_array(array(self::$this, '__instance'), array()); }
	final public static function GetAll() { return self::__getAll(); }
	final public static function Set( $pattern, $result ) { return self::__setVar( $pattern, $result); }
	final public static function Routing( $url ) { return self::__routing( $url ); } 
	final public static function Get() { return call_user_func_array(array(self::$this, '__getVerb'), array(func_get_args())); } 
	final public static function Post() { return call_user_func_array(array(self::$this, '__postVerb'), array(func_get_args())); } 
	final public static function Put() { return call_user_func_array(array(self::$this, '__putVerb'), array(func_get_args())); } 
	final public static function Delete() { return call_user_func_array(array(self::$this, '__deleteVerb'), array(func_get_args())); } 
	final public static function Api() { return call_user_func_array(array(self::$this, '__apiType'), func_get_args()); } 
	final public static function Web() { return call_user_func_array(array(self::$this, '__webType'), func_get_args()); } 
	final public static function Group() { return call_user_func_array(array(self::$this, '__group'), array(func_get_args())); } 
	final public static function Fetch() { return call_user_func_array(array(self::$this, '__fetch'), array()); } 
	final public static function SecureMagicQuote() { return call_user_func_array(array(self::$this, '__secure_magic_quote'), array()); } 
	final public static function UnregisterGlobals() { return call_user_func_array(array(self::$this, '__unregister_global'), array()); } 
	final public static function SetReporting() { return call_user_func_array(array(self::$this, '__set_report'), array()); } 
	final public static function Share() { return call_user_func_array(array(self::$this, '__assign'), func_get_args()); } 
	final public static function Assign() { return call_user_func_array(array(self::$this, '__assign'), func_get_args()); } 
	
	private function __construct() {}
	private function __clone() {}
	private static function __instance() 
	{
		static $_instance; 
		if( is_null( $_instance ) ) 
		{
			$_instance = new Route;
		}
		return $_instance;
	} 
	
	final public static function __callStatic( $name, $arg ) 
	{ 
		self::$prefix .= $name.PS;
		current($arg)(self::instance());
	} 
	
	private static function __assign( $name, $value=NULL ) 
	{
		if( is_array($name) ) 
		{
			$data = item($name);
			$name = current($data); 
			$value = next($data); 
		}
		self::$headset[$name] = $value; 
		return true; 
	}
	
	private static function __headset( $data, $res ) 
	{
		foreach( $data as $name => $value ) 
		{
			$res->assign( $name, $value );
		} 
		return true; 
	}
	
	private static function __apply( $middlewares, $res ) 
	{
		if( $middlewares ) 
		{
			$req = query::instance(); 
			if(is_object($middlewares)) 
				$middlewares::handle($req, $res);
			else 
				foreach($middlewares as $middleware) 
					$middleware::handle($req, $res); 
		}
	}
	
	private static function __fetch() 
	{
		globalmodifier::set( '_cache', new Cache() ); 
		globalmodifier::set( '_irregularWords', array() ); 
		globalmodifier::set( '_inflect', new Inflection() ); 
		globalmodifier::set( '_post', array() ); 
		globalmodifier::set( '_put', array() ); 
		globalmodifier::set( '_delete', array() ); 
		globalmodifier::set( '_get', array() ); 
		globalmodifier::set( '_server', array() ); 
		globalmodifier::set( '_file', array() ); 
	}
	
	private static function __getAll() 
	{
		return $GLOBALS['router']['routings'];
	}
	
	private static function __setVar( $pattern, $result ) 
	{
		$GLOBALS['router']['routings'][$pattern] = $result; 
		return array( $pattern => $result ); 
	}
	
	private static function __routing( $url ) 
	{
		global $router;
		foreach ( $router['routings'] as $pattern => $result ) 
		{
			$pattern = '#^'.$pattern.'$#'; 
			if ( preg_match($pattern, $url) ) 
			{
				if( FALSE!==strpos($url, $result) || 
					FALSE!==strpos($result, $url) )
					return $result;
				else
					return preg_replace( $pattern, $result, $url );
			}
		} 
		return preg_replace( '#^/(.*)#', '$1', $url ); 
	} 
	
	private static function __set_report() 
	{
		global $_CONFIG; 
		if ( $_CONFIG[ 'DEVELOPMENT_ENVIRONMENT' ] == true ) 
		{
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
		} 
		else 
		{
			error_reporting(E_ALL);
			ini_set('display_errors', 0);
			ini_set('log_errors', 1);
			ini_set('error_log', WEB_DIR.DS.'tmp'.DS.'logs'.DS.'error.log');
		}
	} 
	
	private static function __secure_magic_quote() 
	{
		$_GET    = __stripSlashesDeep( $_GET );
		$_POST   = __stripSlashesDeep( $_POST );
		$_COOKIE = __stripSlashesDeep( $_COOKIE ); 
	} 
	
	private static function __unregister_global() 
	{
		$registed = array (
			'_SESSION', 
			'_POST', 
			'_GET', 
			'_COOKIE', 
			'_REQUEST', 
			'_SERVER', 
			'_ENV', 
			'_FILES'
		);
		foreach( $registed as $value ) 
		{
			foreach ( GlobalModifier::get( $value ) as $key => $var ) 
			{
				if ( $var === GlobalModifier::get( $key ) ) 
				{
					GlobalModifier::destroy( $key );
				}
			}
		}
	}
	
	private static function __start() 
	{ 
		if( self::$inited ) return; 
		session::start();
		cookie::start(); 
		error::handle();
		self::fetch(); 
		self::__set_report(); 
		self::__secure_magic_quote(); 
		self::__unregister_global(); 
		LocateService::getInstance()->bootService();
		self::$inited = true;
	} 
	
	private static function __handctrl( $dispatcher, $action ) 
	{
		call_user_func_array(array($dispatcher, "BeforeAction"), array());
		call_user_func_array(array($dispatcher, $action), array());
		call_user_func_array(array($dispatcher, "AfterAction"), array()); 
		call_user_func_array(array($dispatcher, "BeforeRender"), array()); 
		call_user_func_array(array($dispatcher, "FinalRender" ), array());
	} 
	
	private static function __preware( $dispatch, $middlewares ) 
	{
		call_user_func_array(array(self::$this, '__headset'), array(self::$headset, $dispatch)); 
		call_user_func_array(array(self::$this, '__apply'), array(next($middlewares), $dispatch));
	}
	
	private static function __callback( $callback, $middlewares ) 
	{
		$controller_class_name = current($callback);
		$ctrlRefl = new ReflectionClass($controller_class_name); 
		$ttrInjts = $ctrlRefl->getConstructor()->getParameters(); 
		$args = array();
		foreach( $ttrInjts as $key => $arg ) 
		{
			$propName = $arg->getClass()->name; 
			$args[] = new $propName;
		}
		$dispatch = (empty($args))?new $controller_class_name():$ctrlRefl->newInstanceArgs((array) $args);
		call_user_func_array(array(self::$this, '__preware'), array($res, $middlewares));
		route::__handctrl($dispatch, next($callback).ACTION);
	}
	
	final public static function __group( $args ) 
	{ 
		$name = current($args); 
		self::$prefix .= PS.$name;
		if( !isset($_GET['url']) ) 
			$_GET['url'] = self::$prefix.PS; 
		if( $_GET['url']===self::$prefix ) 
			$_GET['url'] .= PS; 
		next($args)(self::instance());
		self::$prefix = empc;
	} 
	
	private static function __apiType( $callback ) 
	{
		self::$prefix = '/api';
		if( !isset($_GET['url']) ) 
			$_GET['url'] = PS; 
		if( $_GET['url']===self::$prefix ) 
			$_GET['url'] .= PS; 
		self::__start(); 
		response::instance()->cors();
		$callback(self::instance());
		self::$prefix = empc;
	} 
	
	private static function __webType( $callback ) 
	{
		self::$prefix = empc;
		self::__start();
		$callback(self::instance());
	} 
	
	private static function __parseUrl( $pattern, &$matches ) 
	{
		$rawpatt = '#:([\w\d]+)#';
		if(preg_match_all($rawpatt, self::$prefix.$pattern, $matches)) 
		{
			$matches = $matches[1]; 
			$pattern = preg_replace($rawpatt, '([\w\d]+)', $pattern); 
		}
		else 
		{
			$matches = array(); 
		} 
		return $pattern; 
	}
	
	private static function __getVerb( $args ) 
	{ 
		global $_get;
		if("GET"===$_SERVER["REQUEST_METHOD"]) 
		{
			$pattern = self::__parseUrl( current($args), $matches );
			$url = (isset($_GET['url']))?$_GET['url']:PS; 
			
			if( preg_match('#^'.self::$prefix.$pattern.'$#', $url, $request) )
			{
				unset($_GET['url']); 
				unset($request[0]);
				if( count($matches) ) 
				{
					$tmp = $request;
					$request = array(); 
					foreach( $tmp as $it ) 
					{
						$request[] = $it;
					}
					$request = array_combine($matches,$request);
				}
				$_get = array(); 
				$qp = stripos($_SERVER["REQUEST_URI"], question); 
				if( false!==$qp ) 
				{ 
					$urlstr = substr( $_SERVER["REQUEST_URI"], $qp+1 ); 
					$_get = text::instance()->parsestr($urlstr);  
				} 
				$_get = $_GET = array_merge($_get, $request);
				$res = RouteController::Instance();
				$callback = next($args); 
				if(is_callable( $callback )) 
				{
					call_user_func_array(array(self::$this, '__preware'), array($res, $args));
					$callback(query::instance(), $res);
					$res->finalRender();
				}
				else if( is_array($callback) ) 
				{
					route::__callback($callback, $args); 
				}
				self::__release();
			} 
		}
	} 
	
	private static function __postVerb( $args ) 
	{ 
		global $_get, $_post; 
		if("POST"===$_SERVER["REQUEST_METHOD"]) 
		{
			$pattern = self::__parseUrl( current($args), $matches );
			$url = (isset($_GET['url']))?$_GET['url']:PS; 
			if( preg_match('#^'.self::$prefix.$pattern.'$#', $url, $request) ) 
			{
				unset($_GET['url']);
				unset($request[0]);
				if( count($matches) ) 
				{
					$tmp = $request;
					$request = array(); 
					foreach( $tmp as $it ) 
					{
						$request[] = $it;
					}
					$request = array_combine($matches,$request);
				}
				$_get = array(); 
				$qp = stripos($_SERVER["REQUEST_URI"], question); 
				if( false!==$qp ) 
				{ 
					$urlstr = substr( $_SERVER["REQUEST_URI"], $qp+1 ); 
					$_get = text::instance()->parsestr($urlstr);  
				} 
				$_get = $_GET = array_merge($_get, $request);
				$res = RouteController::Instance();
				$callback = next($args); 
				if(is_callable( $callback )) 
				{
					$_post = $_POST;
					call_user_func_array(array(self::$this, '__preware'), array($res, $args));
					$callback(request::instance(), $res);
					$res->finalRender();
				}
				else if( is_array($callback) ) 
				{
					route::__callback($callback, $args); 
				}
				self::__release();
			} 
		}
	} 
	
	private static function __putVerb( $args ) 
	{ 
		global $_get, $_post; 
		if("PUT"===$_SERVER["REQUEST_METHOD"]) 
		{
			$pattern = self::__parseUrl( current($args), $matches );
			$url = (isset($_GET['url']))?$_GET['url']:PS; 
			if( preg_match('#^'.self::$prefix.$pattern.'$#', $url, $request) ) 
			{
				unset($_GET['url']);
				unset($request[0]);
				if( count($matches) ) 
				{
					$tmp = $request;
					$request = array(); 
					foreach( $tmp as $it ) 
					{
						$request[] = $it;
					}
					$request = array_combine($matches,$request);
				}
				$_get = array(); 
				$qp = stripos($_SERVER["REQUEST_URI"], question); 
				if( false!==$qp ) 
				{ 
					$urlstr = substr( $_SERVER["REQUEST_URI"], $qp+1 ); 
					$_get = text::instance()->parsestr($urlstr);  
				} 
				$_get = $_GET = array_merge($_get, $request);
				$_post = $_POST = array_merge($_get, file_get_contents("php://input")->jsondecode()); 
				$res = RouteController::Instance();
				$callback = next($args); 
				if(is_callable( $callback )) 
				{
					call_user_func_array(array(self::$this, '__preware'), array($res, $args));
					$callback(request::instance(), $res);
					$res->finalRender();
				}
				else if( is_array($callback) ) 
				{
					route::__callback($callback, $args); 
				}
				self::__release();
			} 
		}
	} 
	
	private static function __deleteVerb( $args ) 
	{ 
		global $_get;
		if("DELETE"===$_SERVER["REQUEST_METHOD"]) 
		{
			$pattern = self::__parseUrl( current($args), $matches );
			$url = (isset($_GET['url']))?$_GET['url']:PS; 
			if( preg_match('#^'.self::$prefix.$pattern.'$#', $url, $request) )
			{
				unset($_GET['url']);
				unset($request[0]);
				if( count($matches) ) 
				{
					$tmp = $request;
					$request = array(); 
					foreach( $tmp as $it ) 
					{
						$request[] = $it;
					}
					$request = array_combine($matches,$request);
				}
				$_get = array(); 
				$qp = stripos($_SERVER["REQUEST_URI"], question); 
				if( false!==$qp ) 
				{ 
					$urlstr = substr( $_SERVER["REQUEST_URI"], $qp+1 ); 
					$_get = text::instance()->parsestr($urlstr);  
				} 
				$_get = $_GET = array_merge($_get, $request);
				$res = RouteController::Instance();
				$callback = next($args); 
				if(is_callable( $callback )) 
				{
					call_user_func_array(array(self::$this, '__preware'), array($res, $args));
					$callback(query::instance(), $res);
					$res->finalRender();
				}
				else if( is_array($callback) ) 
				{
					route::__callback($callback, $args); 
				}
				self::__release();
			} 
		}
	} 
	
	private static function __release() 
	{
		flash::clear();
		escape();
	}
	
}