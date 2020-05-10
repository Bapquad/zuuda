<?php 
namespace Zuuda;

class Flash implements iFlash 
{
	
	public static function GetInstance() { return self::__getInstance(); } 
	public static function Clear() { return self::__clear(); }
	
	final public function rootName() { return __CLASS__; }
	private function __construct() {}
	private function __clone() {}
	
	private static function __getInstance() 
	{
		static $_instance;
		if( is_null($_instance) ) 
		{
			$_instance = new Flash;
		}
		return $_instance;
	} 
	
	public static function __callStatic( $name, $args ) 
	{
		try 
		{
			if( count($args) ) 
			{
				$flashs = session::get('flash'); 
				$flashs[] = $name; 
				session::modify('flash', $flashs); 
				session::modify($name, $args[0]); 
			} 
			else 
			{
				return session::get($name); 
			}
		} 
		catch( \Exception $e ) 
		{
			abort(500, $e->getMessage()); 
		}
	} 
	
	private static function __clear() 
	{
		try 
		{
			$flashs = session::get('flash'); 
			if( NULL!==$flashs ) 
			{ 
				foreach( $flashs as $flash ) 
				{ 
					session::unregister($flash);
				} 
			} 
			session::unregister('flash');
		} 
		catch( \Exception $e ) 
		{ 
			abort(500, $e->getMessage($e)); 
		} 
	}
	
}