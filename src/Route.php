<?php 
namespace Zuuda;

use Exception;
use ReflectionClass;
use Zuuda\RouteController;
use Zuuda\GlobalModifier;
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
	
	private static function __getAll() 
	{
		return $GLOBALS[ 'router' ][ 'routings' ];
	}
	
	private static function __setVar( $pattern, $result ) 
	{
		$GLOBALS[ 'router' ][ 'routings' ][ $pattern ] = $result; 
		return array( $pattern => $result );
	}
	
	private static function __routing( $url ) 
	{
		global $router;
		foreach ( $router[ 'routings' ] as $pattern => $result ) 
		{
			if ( preg_match($pattern, $url) ) 
			{
				if( FALSE!==strpos($url, $result) 
				 || FALSE!==strpos($result, $url) )
					return $result;
				else
					return preg_replace( $pattern, $result, $url );
			}
		} 
		return $url;
	} 
	
	final public static function __callStatic( $name, $arg ) 
	{ 
		self::$prefix .= $name.PS;
		current($arg)(self::instance());
	} 
	
	final public static function __group( $args ) 
	{ 
		$name = current($args); 
		self::$prefix .= $name.PS;
		next($args)(self::instance());
	} 
	
	private static function __start() 
	{ 
		session::start();
		cookie::start(); 
		error::handle();
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
	
	private static function __handctrl( $dispatcher, $action ) 
	{
		call_user_func_array(array($dispatcher, "BeforeAction"), array());
		call_user_func_array(array($dispatcher, $action), array());
		call_user_func_array(array($dispatcher, "AfterAction"), array()); 
		call_user_func_array(array($dispatcher, "BeforeRender"), array()); 
		call_user_func_array(array($dispatcher, "FinalRender" ), array());
	} 
	
	private static function __callback( $callback ) 
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
		route::__handctrl($dispatch, next($callback).ACTION);
	}
	
	private static function __apiType( $callback ) 
	{
		self::$prefix = 'api/';
		self::__start(); 
		response::instance()->cors();
		$callback(self::instance());
	} 
	
	private static function __webType( $callback ) 
	{
		self::$prefix = empc;
		self::__start();
		$callback(self::instance());
	} 
	
	private static function __getVerb( $args ) 
	{ 
		global $_get;
		if("GET"===$_SERVER["REQUEST_METHOD"]) 
		{
			$pattern = current($args);
			if( preg_match('#^'.self::$prefix.$pattern.'$#', $_GET['url'], $request) )
			{
				unset($_GET['url']); 
				unset($request[0]);
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
					$callback(query::instance(), $res);
					$res->finalRender();
				}
				else if( is_array($callback) ) 
				{
					route::__callback($callback); 
				}
				self::__release();
			} 
			else 
			{
				abort( 404 );
			}
		}
	} 
	
	private static function __postVerb( $args ) 
	{ 
		global $_get, $_post; 
		if("POST"===$_SERVER["REQUEST_METHOD"]) 
		{
			$pattern = current($args);
			if( preg_match('#^'.self::$prefix.$pattern.'$#', $_GET['url']) )
			{
				unset($_GET['url']);
				$_get = array(); 
				$qp = stripos($_SERVER["REQUEST_URI"], question); 
				if( false!==$qp ) 
				{ 
					$urlstr = substr( $_SERVER["REQUEST_URI"], $qp+1 ); 
					$_get = $_GET = text::instance()->parsestr($urlstr);  
				} 
				$res = RouteController::Instance();
				$callback = next($args); 
				if(is_callable( $callback )) 
				{
					$_post = $_POST;
					$callback(request::instance(), $res);
					$res->finalRender();
				}
				else if( is_array($callback) ) 
				{
					route::__callback($callback); 
				}
				self::__release();
			} 
			else 
			{
				abort( 404 );
			}
		}
	} 
	
	private static function __putVerb( $args ) 
	{ 
		global $_get, $_post; 
		if("PUT"===$_SERVER["REQUEST_METHOD"]) 
		{
			$pattern = current($args);
			if( preg_match('#^'.self::$prefix.$pattern.'$#', $_GET['url']) )
			{
				unset($_GET['url']);
				$_get = array(); 
				$qp = stripos($_SERVER["REQUEST_URI"], question); 
				if( false!==$qp ) 
				{ 
					$urlstr = substr( $_SERVER["REQUEST_URI"], $qp+1 ); 
					$_get = $_GET = text::instance()->parsestr($urlstr);  
				} 
				$_post = $_POST = array_merge($_get, file_get_contents("php://input")->jsondecode()); 
				$res = RouteController::Instance();
				$callback = next($args); 
				if(is_callable( $callback )) 
				{
					$callback(request::instance(), $res);
					$res->finalRender();
				}
				else if( is_array($callback) ) 
				{
					route::__callback($callback); 
				}
				self::__release();
			} 
			else 
			{
				abort( 404 );
			}
		}
	} 
	
	private static function __deleteVerb( $args ) 
	{ 
		global $_get;
		if("DELETE"===$_SERVER["REQUEST_METHOD"]) 
		{
			$pattern = current($args);
			if( preg_match('#^'.self::$prefix.$pattern.'$#', $_GET['url'], $request) )
			{
				unset($_GET['url']);
				unset($request[0]);
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
					$callback(query::instance(), $res);
					$res->finalRender();
				}
				else if( is_array($callback) ) 
				{
					route::__callback($callback); 
				}
				self::__release();
			} 
			else 
			{
				abort( 404 );
			}
		}
	} 
	
	private static function __release() 
	{
		flash::clear();
		escape();
	}
	
}