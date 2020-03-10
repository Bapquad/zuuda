<?php 
namespace Zuuda;
use Exception;

class Request
{
	
	private $_data; 
	private static $class = '\Zuuda\Request';
	
	public static function All() { return self::__params(); } 
	public static function Has($name) { return self::__has($name); }
	public static function Params() { return self::__params(); } 
	public static function Body($name=NULL) { return self::__param($name, true); } 
	public static function Data($name=NULL) { return self::__param($name, true); } 
	public static function Get($name=NULL) { return self::__param($name); } 
	public static function Param($name=NULL) { return self::__param($name); } 
	public static function Input($name=NULL) { return self::__input($name); } 
	public static function Except() { return call_user_func_array([self::$class, '__except'], array(func_get_args(), func_num_args())); } 
	public static function Length() { return call_user_func_array([self::$class, '__length'], array(func_get_args(), func_num_args())); } 
	public static function Empty() { return call_user_func_array([self::$class, '__empty'], array(func_get_args(), func_num_args())); } 
	public static function Encrypt() { return call_user_func_array([self::$class, '__encrypt'], array(func_get_args(), func_num_args())); } 
	public static function Hash() { return call_user_func_array([self::$class, '__encrypt'], array(func_get_args(), func_num_args())); } 
	public static function Merge() { return call_user_func_array([self::$class, '__merge'], array(func_get_args(), func_num_args())); } 
	final public function rootName() { return __CLASS__; } 
	private function __construct() {} 
	private function __clone() {} 
	
	private static function __merge( $args, $argsNum ) 
	{ 
		global $_post; 
		try 
		{
			if( $argsNum ) 
			{
				$params = current( $args );
				if( is_array($params) ) 
					return array_merge($_post, $params); 
			}
			throw new Exception( "Usage <strong>Request::merge()</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 500, $e->getMessage() ); 
		} 
	} 
	
	private static function __encrypt( $args, $argsNum ) 
	{
		global $_post;
		$name = current($args); 
		if( isset($_post[$name]) ) 
		{
			if( !isset($configs['ENCRYPT']) || $configs['ENCRYPT']['request']==="MD5" ) 
				$out = md5($_post[$name]); 
			else if( $configs['ENCRYPT']['request']==="SHA-2" ) 
				$out = hash( "sha256", $_post[$name] ); 
			else if( $configs['ENCRYPT']['request']==="SHA-1" ) 
				$out = sha1( $_post[$name] );
			$_post[$name] = $out; 
			return $out;
		} 
		return NULL;
	}
	
	private static function __length( $args, $argsNum ) 
	{
		global $_post; 
		try 
		{
			if( zero===$argsNum ) 
				return count($_post); 
			else if( 1===$argsNum ) 
				return count($_post[current($args)]); 
			else 
				throw new Exception( "Usage <strong>Request::length()</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() ); 
		} 
	} 
	
	private static function __empty( $args, $argsNum ) 
	{
		global $_post; 
		try 
		{
			if( zero===$argsNum ) 
				return empty($_post); 
			else if( 1===$argsNum ) 
				return empty($_post[current($args)]);
			else 
				throw new Exception( "Usage <strong>Request::empty()</strong> is incorrect." ); 
		}
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() ); 
		} 
	}
	
	public static function __callStatic( $name, $args ) 
	{
		global $_post;
		try 
		{ 
			if( empty($args) ) 
				return $_post[$name]; 
			else 
				$value = current($args);
			$_post[$name] = $value; 
			return $value;	
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() ); 
		} 
	}
	
	private static function __except( $args, $argsNum ) 
	{
		global $_post;
		try 
		{
			$out = array(); 
			foreach($_post as $key => $value) 
				if( !in_array($key, $args) ) 
					$out[$key] = $value;
			return $out; 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() );
		} 
	}
	
	private static function __params() 
	{
		global $_post; 
		return $_post; 
	} 
	
	private static function __has( $name ) 
	{
		global $_post; 
		return isset($_post[$name]); 
	}
	
	private static function __param( $name=NULL, $body=false ) 
	{
		global $_post;
		try 
		{
			if( NULL===$name && false===$body ) 
				throw new Exception("Usage Request::param() is incorrect."); 
			if( isset($_post[$name]) ) 
			{
				return $_post[$name]; 
			}
			else 
			{
				if( $body )
					return $_post; 
				return NULL;
			}
		}
		catch( Exception $e ) 
		{
			abort( 400, $e->getMessage() ); 
		}
	} 
	
	private static function __input( $name=NULL ) 
	{
		global $_get;
		try 
		{
			if( NULL===$name ) 
				throw new Exception("Usage Request::input() is incorrect."); 
			if( isset($_get[$name]) ) 
				return $_get[$name]; 
			else 
				throw new Exception("There aren't input named {$name}"); 
		}
		catch( Exception $e ) 
		{
			abort( 400, $e->getMessage() ); 
		}
	} 

}