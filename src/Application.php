<?php
namespace Zuuda;

use ReflectionClass;
use Exception;
use Zuuda\Error;
use Zuuda\Fx;
use Zuuda\Text;

class Application 
{
	private static $_data = array();
	
	final public function rootName() { return __CLASS__; } 
	final static public function HasUrl() { return self::__hasUrl(); }
	final static public function GetUrl() { return self::__getUrl(); }
	final static public function SetUrl( $value ) { return self::__setUrl( $value ); }
	
	public static function Instance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new application;
		}
		return $_instance;
	}
	
	private static function __hasUrl() 
	{
		if( isset( self::$_data[ 'url' ] ) )
		{
			return true;
		}
		return false;
	}
	
	private static function __getUrl() { return ( NULL !== self::$_data[ 'url' ] ) ? self::$_data[ 'url' ] : NULL; }
	
	private static function __setUrl( $value ) 
	{
		self::$_data = array_merge
		(
			self::$_data, 
			array( 'url' => $value ) 
		);
		return $value;
	}
	
	private function __construct() {}
	private function __clone() {}

	private function __routeURL( $url ) 
	{
		if( config::has( 'COM' ) ) 
		{
			if( self::__hasUrl() ) 
			{
				return self::__getUrl(); 
			}
		}
		return Route::routing( $url ); 
	}
	
	private function __bootService() 
	{
		GlobalModifier::set( '_cache', new Cache() );
		GlobalModifier::set( '_irregularWords', array() );
		GlobalModifier::set( '_inflect', new Inflection() );
		GlobalModifier::set( '_post', array() );
		GlobalModifier::set( '_put', array() );
		GlobalModifier::set( '_delete', array() );
		GlobalModifier::set( '_get', array() );
		GlobalModifier::set( '_server', array() );
		GlobalModifier::set( '_file', array() );
		GlobalModifier::loadUrl();
		GlobalModifier::timezone(); 
	}
	
	private function __parseQuery( $query, $override = true ) 
	{
		global $_CONFIG;
		if( stripos( $query, '?' ) ) 
		{
			$query = explode( question, $query ); 
			$_CONFIG[ 'REQUEST_VARIABLES' ] = text::instance()->parseStr($query[1]); 
			
			if( array_key_exists( 'REQUEST_VARIABLES', $_CONFIG ) ) 
			{
				GlobalModifier::set( '_GET', $_CONFIG[ 'REQUEST_VARIABLES' ] );
				GlobalModifier::set( '_get', $_CONFIG[ 'REQUEST_VARIABLES' ] );
			}
		}
		else if( $override )
		{
			GlobalModifier::set( '_GET', array() );
			GlobalModifier::set( '_get', array() );
		} 
		return;
	}
	
	private function __bootParams() 
	{
		global $_CONFIG;
		$_CONFIG['QUERY_STRING'] = explode(PS, trim($this->__routeURL(singleton('Global')->get('url')), '?-_')); 
		$this->__parseQuery($GLOBALS['request_uri']);
		GlobalModifier::set( '_server', $_SERVER );
	}
	
	private function __bootServices( $serviceInst, $appInst = NULL ) 
	{
		return $serviceInst->bootService( $appInst );
	}

	private function __extractController() 
	{
		global $_CONFIG;
		global $router;
		$_extract = array();
		if(is_array($_CONFIG['QUERY_STRING'])) 
		{
			$module = $_CONFIG['QUERY_STRING'][0];
			array_push($_extract, array_shift($_CONFIG['QUERY_STRING']));
			$_CONFIG["MODULE"] = ucfirst($module); 
			$controller = (isset($_CONFIG['QUERY_STRING']) && isset($_CONFIG['QUERY_STRING'][0])) ? $_CONFIG['QUERY_STRING'][0] : NULL;
			if( NULL === $controller || empty_char === $controller ) 
			{
				$controller = 'index';
			} 
			$controller = ucfirst($controller); 
			array_push($_extract, array_shift($_CONFIG['QUERY_STRING']));
			$controller = explode('-', $controller);
			foreach($controller as $k =>  $c) 
			{ 
				$controller[$k] = ucfirst($c);
			} 
			$controller = implode( EMPTY_CHAR, $controller ); 
			$_CONFIG["CONTROLLER"] = preg_replace( '/[\-\_\s]/', '', $controller );
			$_CONFIG['ACTION'] = array_shift($_CONFIG['QUERY_STRING']);

			$_extract = $_CONFIG["MODULE"].BS.CTRLER_PRE.BS.$_CONFIG["CONTROLLER"].CONTROLLER;
			return $_extract;
		}
		return $router['default']['controller'];
	} 
	
	private function __release() 
	{
		Flash::clear();
	}

	static function Booting() 
	{
		static $_instance;
		if( NULL!==$_instance ) 
			return $_instance;	// NEEDLE HANDLE.
		Session::start();
		Cookie::start();
		Error::handle();
		$_instance = application::instance();
		$_instance->__bootService();
		if( Config::has('COM') ) 
		{
			$_instance->__bootServices( Comsite::getInstance(), $_instance ); 
			$_instance->__bootServices( BTShipnelService::getInstance() );
			$_instance->__bootServices( RouteService::getInstance(), $_instance );
			$_instance->__bootServices( ComService::getInstance(), $_instance );
			$_instance->__bootServices( ThemeService::getInstance() );
			$_instance->__bootServices( CateService::getInstance(), $_instance );
			$_instance->__bootServices( ExtensionInformationService::getInstance(), $_instance );
			$_instance->__bootServices( WidgetInformationService::getInstance(), $_instance );
		}
		$_instance->__bootServices( LocateService::getInstance(), $_instance ); 
		$_instance->__bootParams(); 
		return $_instance;
	} 
	
	static function Handling( Controller $dispatcher, String $patchAction ) 
	{
		global $_CONFIG; 
		$_instance = Application::Instance();
		call_user_func_array(array($dispatcher, "CheckMass"), array(strtolower($_SERVER['REQUEST_METHOD'])) ); 
		call_user_func_array(array($dispatcher, "BeforeAction"), $_CONFIG["QUERY_STRING"]);
		call_user_func_array(array($dispatcher, $patchAction), $_CONFIG["QUERY_STRING"]);
		call_user_func_array(array($dispatcher, "AfterAction"), $_CONFIG["QUERY_STRING"]); 
		call_user_func_array(array($dispatcher, "BeforeRender"), $_CONFIG["QUERY_STRING"]); 
		call_user_func_array(array($dispatcher, "FinalRender" ), $_CONFIG["QUERY_STRING"]);
		return $_instance;
	}
	
	public function SetReporting() 
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
		return $this;
	}

	public function SecureMagicQuotes() 
	{
		$_GET    = __stripSlashesDeep( $_GET );
		$_POST   = __stripSlashesDeep( $_POST );
		$_COOKIE = __stripSlashesDeep( $_COOKIE ); 
		return $this;
	}

	public function UnregisterGlobals() 
	{
		$registed = array
		(
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
		return $this;
	}

	public function Start() 
	{
		global $_CONFIG, $_get; 
		try 
		{
			$controller_class_name = $this->__extractController(); 
			$controller_class_file = __currentControllerFile(); 
			if( file_exists($controller_class_file) ) 
			{
				$ctrlRefl = new ReflectionClass($controller_class_name); 
				$ttrInjts = $ctrlRefl->getConstructor()->getParameters(); 
				$args = array();
				foreach( $ttrInjts as $key => $arg ) 
				{
					$propName = $arg->getClass()->name; 
					$args[] = new $propName;
				}
				$dispatch = (empty($args))?new $controller_class_name():$ctrlRefl->newInstanceArgs((array) $args);
				$action = $_CONFIG['ACTION']; 
				$_get = array_merge( $_get, self::__get_uri_var($action) ); 
				if( is_string($action) ) 
				{
					$action = explode( ';', $action );
					foreach( $action as $key => $value ) 
					{
						$action[$key] = strtoupper(substr($value, 0, 1)).substr($value, 1); 
					}
					$action = implode(EMPTY_CHAR, $action); 
				}
				$actionDelimeter = '*.*'; 
				$actionInjector = '/[\-\_\s]/';
				$action = explode( $actionDelimeter, preg_replace( $actionInjector, $actionDelimeter, $action ) );
				foreach( $action as $key => $word ) 
					$action[$key] = ucfirst($word); 
				$action = implode(EMPTY_CHAR, $action);
				$_CONFIG['BEFORE_RENDER_EVENT'] = $action.'Render'; 
				$action .= ACTION;
				if($action === 'Action') 
				{
					$_CONFIG['BEFORE_RENDER_EVENT'] = 'IndexRender'; 
					$action = 'IndexAction';
				}
				if( method_exists($dispatch, $action) ) 
				{
					Application::Handling($dispatch, $action); 
				}
				else
					throw new Exception("Ops! Your action <strong>$action</strong> is not found in <strong>$controller_class_name.php</strong>.");
			}
			else 
				throw new Exception("Ops! Your controller <strong>$controller_class_name</strong> is not found."); 
		}
		catch(Exception $e) 
		{
			if( config::get(DEVELOPER_WARNING) && config::get(DEVELOPMENT_ENVIRONMENT) ) 
			{
				abort( 404, '<b><i>[EXCEPTION ERROR]</i> </b>'.$e->getMessage().BL.error::position($e) ); 
			} 
			else 
			{
				abort( 404, "Ops! Your page hasn't found." ); 
			}
		}
		$this->__release(); 
		escape(); 
		return $this;
	}
	
	static private function __get_uri_var( &$in ) 
	{ 
		$out = array(); 
		if( false!==stripos($in, '?') ) 
		{
			$in = explode('?', $in); 
			$vars = $in[1]; 
			$out = text::instance()->parsestr($vars); 
			$in = current($in);
		} 
		return $out;
	} 
	
}
