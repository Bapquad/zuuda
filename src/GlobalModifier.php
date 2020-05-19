<?php 
namespace Zuuda;

class GlobalModifier implements iGlobalModifier  
{
	private static $this = '\Zuuda\GlobalModifier';
	public static function GetInstance() { return self::__getInstance(); }
	public static function Get( $name ) { return self::__getVar( $name ); }
	public static function Destroy( $name ) { return self::__destroy( $name ); }
	public static function Unregister( $name ) { return self::__destroy( $name ); }
	public static function Set( $name, $value ) { return self::__setVar( $name, $value ); }
	public static function Register( $name, $value ) { return self::__setVar( $name, $value ); }
	public static function GetAll() { return self::__getAll(); }
	public static function Func( $name ) { return self::__func( $name ); }
	public static function LoadUrl() { return self::__loadUrl(); }
	public static function Timezone() { return self::__timezone(); } 
	
	private function __construct() {}
	private function __clone() {}
	private static function __getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new GlobalModifier;
		}
		return $_instance;
	}
	
	private static function __setVar( $name, $value ) 
	{
		$GLOBALS[ $name ] = $value;
	}
	
	private static function __destroy( $name ) 
	{
		if( isset( $GLOBALS[ $name ] ) ) 
		{
			unset( $GLOBALS[ $name ] );
		}
	}
	
	private static function __getAll() 
	{
		return $GLOBALS;
	}
	
	private static function __getVar( $name ) 
	{
		if( isset( $GLOBALS[ $name ] ) ) 
		{
			return $GLOBALS[ $name ];
		}
		return array();
	}
	
	private static function __func( $name ) 
	{
		return !function_exists( $name );
	}
	
	private static function __loadUrl() 
	{
		global $router, $request_uri;
		if( isset($_GET['url']) ) 
		{
			$request_uri = (isset($_SERVER['REQUEST_URI']))?$_SERVER['REQUEST_URI']:$url;
			$url = $_GET['url'];
		}
		else  
		{
			$request_uri = (isset($_SERVER['REQUEST_URI']))?$_SERVER['REQUEST_URI']:'/';
			if(config::get('APP_PATH')!==PS) 
			{ 
				$request_uri = str_replace( config::get('APP_PATH'), '', $request_uri );
			} 
			$qmpos = stripos( $request_uri, '?' ); 
			if($qmpos) 
			{
				$url = substr( $request_uri, 1, $qmpos ); 
			} 
			else 
			{
				$url = ( PS===$request_uri ) ? $router['default']['url'] : substr( $request_uri, 1 ); 
			}
		}
		$GLOBALS['url'] = $url; 
		$GLOBALS['request_uri'] = $request_uri; 
		return $url; 
	} 
	
	private static function __timezone() 
	{ 
		global $configs; 
		if( isset($configs['LOCATE']['timezone']) ) 
		{ 
			$timezone = $configs['LOCATE']['timezone'];
		} 
		else 
		{ 
			$timezone = 'UTC'; 
		} 
		
		return date_default_timezone_set($timezone); 
	} 
	
}