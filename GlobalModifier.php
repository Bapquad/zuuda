<?php 
namespace Zuuda;

class GlobalModifier implements iGlobalModifier  
{
	
	public static function GetInstance() { return self::_getInstance(); }
	public static function Get( $name ) { return self::_get( $name ); }
	public static function Destroy( $name ) { return self::_destroy( $name ); }
	public static function Unregister( $name ) { return self::_destroy( $name ); }
	public static function Set( $name, $value ) { return self::_set( $name, $value ); }
	public static function Register( $name, $value ) { return self::_set( $name, $value ); }
	public static function GetAll() { return self::_getAll(); }
	public static function Func( $name ) { return self::_func( $name ); }
	public static function LoadUrl() { return self::_loadUrl(); }
	
	private function __construct() {}
	private function __clone() {}
	private static function _getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new GlobalModifier;
		}
		return $_instance;
	}
	
	private static function _set( $name, $value ) 
	{
		$GLOBALS[ $name ] = $value;
	}
	
	private static function _destroy( $name ) 
	{
		if( isset( $GLOBALS[ $name ] ) ) 
		{
			unset( $GLOBALS[ $name ] );
		}
	}
	
	private static function _getAll() 
	{
		return $GLOBALS;
	}
	
	private static function _get( $name ) 
	{
		if( isset( $GLOBALS[ $name ] ) ) 
		{
			return $GLOBALS[ $name ];
		}
		return array();
	}
	
	private static function _func( $name ) 
	{
		return !function_exists( $name );
	}
	
	private static function _loadUrl() 
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