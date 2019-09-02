<?php 
namespace Zuuda;

class GlobalModifier implements iGlobalModifier  
{
	
	public static function GetInstance() { return self::__getInstance(); }
	public static function Get( $name ) { return self::__getVar( $name ); }
	public static function Destroy( $name ) { return self::__destroy( $name ); }
	public static function Unregister( $name ) { return self::__destroy( $name ); }
	public static function Set( $name, $value ) { return self::__setVar( $name, $value ); }
	public static function Register( $name, $value ) { return self::__setVar( $name, $value ); }
	public static function GetAll() { return self::__getAll(); }
	public static function Func( $name ) { return self::__func( $name ); }
	public static function LoadUrl() { return self::__loadUrl(); }
	
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
		global $router;
		if( isset($_GET[ 'url' ]) ) 
		{
			$url = $_GET[ 'url' ];
		}
		else if( isset($_SERVER['REQUEST_URI']) ) 
		{
			$qmpos = stripos( $_SERVER['REQUEST_URI'], '?' ); 
			if($qmpos) 
			{
				$url = substr( $_SERVER['REQUEST_URI'], 1, $qmpos ); 
			} 
			else 
			{
				$url = ( PS===$_SERVER['REQUEST_URI'] ) ? 
					$router[ 'default' ][ 'url' ] 
					: 
					substr( $_SERVER['REQUEST_URI'], 1 ); 
			}
		}
		$GLOBALS[ 'url' ] = $url; 
		return $url; 
	}
	
}