<?php 
namespace Zuuda;

class Flash implements iFlash 
{
	
	private static $this = '\Zuuda\Flash';
	final public function rootName() { return __CLASS__; }
	private function __construct() {}
	private function __clone() {}
	
	public static function Instance() { return self::__getInstance(); } 
	public static function GetInstance() { return self::__getInstance(); } 
	public static function Clear() { return self::__clear(); } 
	public static function Has() { return call_user_func_array(array(self::$this, '__has'), func_get_args()); } 
	
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
				$flash_exist = session::has('flash'); 
				if( $flash_exist ) 
				{ 
					$flashs = session::get('flash'); 
				}  
				else 
				{ 
					$flashs = array(); 
				} 
				
				$flashs[] = $name; 
				
				if( $flash_exist ) 
				{
					session::modify('flash', $flashs); 
				}
				else
				{
					session::register('flash', $flashs); 
				}
				
				if( session::has($name) ) 
				{
					session::modify($name, $args[0]); 
				} 
				else 
				{ 
					session::register($name, $args[0]); 
				} 
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
	
	private static function __has( $args ) 
	{
		if( session::has('flash') ) 
		{ 
			$flash = session::get('flash'); 
			return in_array($args, $flash); 
		} 
		return false;
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