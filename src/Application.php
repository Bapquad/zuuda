<?php
namespace Zuuda;

const SCRIPT_ASSET 	= 'script';
const STYLE_ASSET 	= 'style';
const HTML_ASSET 	= 'html';
const HEADER_LAYOUT = 'header';
const FOOTER_LAYOUT = 'footer';
const MAIN_LAYOUT 	= 'main'; 

use ReflectionClass;
use Exception;

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
		GlobalModifier::set( 'cache', new Cache() );
		GlobalModifier::set( 'irregularWords', array() );
		GlobalModifier::set( 'inflect', new Inflection() );
		GlobalModifier::set( 'html', Html::getInstance() );
		GlobalModifier::set( 'file', cFile::getInstance() );
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
		global $configs;
		$has_vars = stripos( $query, '?' );
		$exepos = $has_vars;
		if( $has_vars ) 
		{
			$has_vars = substr( $query, $has_vars + 1 ); 
			if( $has_vars ) 
			{
				$arr_vars = explode( '&', $has_vars );
				$configs[ 'REQUEST_VARIABLES' ] = array();
				foreach( $arr_vars as $key => $value ) 
				{
					$var = explode( '=', $value ); 
					if( isset( $configs[ 'REQUEST_VARIABLES' ][ $var[ 0 ] ] ) ) 
					{
						if( !is_array( $configs[ 'REQUEST_VARIABLES' ][ $var[ 0 ] ] ) ) 
						{
							$first_value = $configs[ 'REQUEST_VARIABLES' ][ $var[ 0 ] ];
							$configs[ 'REQUEST_VARIABLES' ][ $var[ 0 ] ] = array( $first_value );
						}
						array_push( $configs[ 'REQUEST_VARIABLES' ][ $var[ 0 ] ], urldecode( $var[ 1 ] ) );
					}
					else 
					{
						$configs[ 'REQUEST_VARIABLES' ][ $var[ 0 ] ] = isset( $var[ 1 ] ) ? urldecode( $var[ 1 ] ) : '';
					}
				}
			}
			
			if( array_key_exists( 'REQUEST_VARIABLES', $configs ) ) 
			{
				GlobalModifier::set( '_GET', $configs[ 'REQUEST_VARIABLES' ] );
				GlobalModifier::set( '_get', $configs[ 'REQUEST_VARIABLES' ] );
			}
		}
		else if( $override )
		{
			GlobalModifier::set( '_GET', array() );
			GlobalModifier::set( '_get', array() );
		} 
		return $exepos;
	}
	
	private function __bootParams() 
	{
		global $configs;
		$configs['QUERY_STRING'] = array_map( 'ucfirst' , explode( PS, trim($this->__routeURL(getSingleton('Global')->get('url')), '?-_')) ); 
		$this->__parseQuery($_SERVER[ "REQUEST_URI" ]);
		GlobalModifier::set( '_server', $_SERVER );
	}
	
	private function __bootServices( $serviceInst, $appInst = NULL ) 
	{
		if( Config::has( 'COM' ) ) 
		{
			return $serviceInst->bootService( $appInst );
		}
		return false;
	}

	private function __extractController() 
	{
		global $configs;
		global $router;
		$_extract = array();
		if(is_array($configs['QUERY_STRING'])) 
		{
			$module = $configs['QUERY_STRING'][0];
			array_push($_extract, array_shift($configs['QUERY_STRING']));
			$configs["MODULE"] = $module;
			$controller = (isset($configs['QUERY_STRING']) && isset($configs['QUERY_STRING'][0])) ? $configs['QUERY_STRING'][0] : NULL;
			if( NULL === $controller || empty_char === $controller ) 
			{
				$controller = 'Index';
			}
			array_push($_extract, array_shift($configs['QUERY_STRING']));
			$configs["CONTROLLER"] = preg_replace( '/[\-\_\s]/', '', $controller );
			$configs['ACTION'] = array_shift($configs['QUERY_STRING']);

			$_extract = $module.BS.CTRLER_PRE.BS.$configs["CONTROLLER"].CONTROLLER;

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
		$_instance = application::instance();
		$_instance->__bootService();
		$_instance->__bootServices( LocateService::getInstance(), $_instance ); 
		if( Config::has( 'COM' ) ) 
		{
			$_instance->__bootServices( BTShipnelService::getInstance() );
			$_instance->__bootServices( RouteService::getInstance(), $_instance );
			$_instance->__bootServices( ComService::getInstance(), $_instance );
			$_instance->__bootServices( ThemeService::getInstance() );
			$_instance->__bootServices( CateService::getInstance(), $_instance );
			$_instance->__bootServices( ExtensionInformationService::getInstance(), $_instance );
		}
		$_instance->__bootParams(); 
		return $_instance;
	} 
	
	static function Handling( Controller $dispatcher, String $action ) 
	{
		global $configs; 
		$_instance = Application::Instance();
		call_user_func_array(array($dispatcher, "CheckMass"), array(strtolower($_SERVER['REQUEST_METHOD'])) ); 
		call_user_func_array(array($dispatcher, "BeforeAction"), $configs["QUERY_STRING"]);
		call_user_func_array(array($dispatcher, $action), $configs["QUERY_STRING"]);
		call_user_func_array(array($dispatcher, "AfterAction"), $configs["QUERY_STRING"]); 
		call_user_func_array(array($dispatcher, "BeforeRender"), $configs["QUERY_STRING"]); 
		call_user_func_array(array($dispatcher, "FinalRender" ), $configs["QUERY_STRING"]);
		return $_instance;
	}
	
	public function SetReporting() 
	{
		global $configs; 
		if ( $configs[ 'DEVELOPMENT_ENVIRONMENT' ] == true ) 
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
		if( @get_magic_quotes_gpc() ) 
		{
			$_GET    = __stripSlashesDeep( $_GET );
			$_POST   = __stripSlashesDeep( $_POST );
			$_COOKIE = __stripSlashesDeep( $_COOKIE ); 
		} 
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
		global $configs, $_get; 
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
				$action = $configs['ACTION']; 
				$action = explode( ';', $action );
				foreach( $action as $key => $value ) 
				{
					$action[ $key ] = strtoupper( substr( $value, 0, 1 ) ).substr( $value, 1 );
				}
				$action = implode( EMPTY_CHAR, $action );
				$actionDelimeter = '|'; 
				$actionInjector = '/[\-\_\s]/';
				$action = explode( $actionDelimeter, preg_replace( $actionInjector, $actionDelimeter, $action ) );
				foreach( $action as $key => $word ) 
					$action[$key] = ucfirst($word); 
				$action = implode(EMPTY_CHAR, $action);
				$action .= ACTION;
				if($action === 'Action') $action = 'IndexAction';
				if((int)method_exists($dispatch, $action)) 
				{
					if( isset( $configs["QUERY_STRING"] ) ) 
					{
						$lim = count( $configs["QUERY_STRING"] );
						for( $i = 0; $i < $lim; $i++ ) 
						{
							$configs["QUERY_STRING"][ $i ] = strtolower( $configs["QUERY_STRING"][ $i ] );
						}
					} 
					Application::Handling($dispatch, $action);
				}
				else if( $configs[DEVELOPER_WARNING] )
					abort( 400, "Ops! Your action <strong>$action</strong> is not found in <strong>$controller_class_name.php</strong>." ); 
				else 
					abort( 404 ); 
			}
			else if( $configs[DEVELOPER_WARNING] ) 
				abort( 400, "Ops! Your controller <strong>$controller_class_name</strong> is not found." ); 
			else 
				abort( 404 ); 
		}
		catch(Exception $e) 
		{
			abort( 400 );
		}
		$this->__release(); 
		escape(); 
		return $this;
	}
}
