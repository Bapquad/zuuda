<?php

namespace Zuuda; 

use Exception; 
use Zuuda\Auth; 

class Middleware 
{
	
	public static $this = "\Zuuda\Middleware"; 
	
	public static function Auth() { return call_user_func_array(array(self::$this, '__auth'), array(func_get_args())); } 
	public static function Instance() { return call_user_func_array(array(self::$this, '__instance'), array(func_get_args())); } 
	
	final public function rootName() { return __CLASS__; } 
	private function __construct() {} 
	private function __clone() {} 
	
	private static function __instance() 
	{ 
		static $_inst; 
		return $_inst ?: $_inst = new Middleware;
	} 
	
	private static function __auth( $args ) 
	{
		global $_CONFIG;
		try 
		{
			if( empty($args) ) 
			{ 
				if( null!==auth::app() ) 
				{
					if( $_CONFIG['APP_PATH']!==auth::app() ) 
					{ 
						throw new Exception("You haven't authorizied."); 
					} 
				} 
				else 
				{
					throw new Exception("You haven't authorizied."); 
				} 
			} 
		} 
		catch( Exception $e ) 
		{ 
			abort( 403 ); 
		} 
	}
	
}