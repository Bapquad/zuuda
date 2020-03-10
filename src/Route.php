<?php 
namespace Zuuda;

class Route implements iRoute 
{
	
	public static function GetInstance() { return sefl::__getInstance(); }
	public static function GetAll() { return self::__getAll(); }
	public static function Set( $pattern, $result ) { return self::__setVar( $pattern, $result); }
	public static function Routing( $url ) { return self::__routing( $url ); }
	
	private function __construct() {}
	private function __clone() {}
	private static function __getInstance() 
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
	
}