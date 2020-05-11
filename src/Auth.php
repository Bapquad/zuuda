<?php

namespace Zuuda;

class Auth 
{
	
	private static $class = '\Zuuda\Auth';
	final public function rootName() { return __CLASS__; }
	private function __construct() {}
	private function __clone() {} 
	
	public static function User($prop=NULL) { return self::__user($prop); } 
	public static function Get($prop) { return self::__user($prop); } 
	public static function GetAll() { return self::__getAll(); }
	public static function Instance() { return self::__getInstance(); }
	public static function Body($prop) { return self::__user($prop); } 
	public static function Role() { return call_user_func_array([self::$class, '__role'], array(func_get_args())); } 
	public static function Apply() { return call_user_func_array([self::$class, '__role'], array(func_get_args())); } 
	
	public static function __callStatic( $name, $args ) 
	{
		try 
		{ 
			if( empty($args) ) 
				return self::__user($name); 
			else 
				throw new Exception( "Usage <strong>Auth::{$name}()</strong> is incorrect." ); 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() ); 
		} 
	}
	
	private static function __getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new Auth;
		}
		return $_instance;
	}
	
	private static function __user($prop) 
	{
		if($prop) 
		{
			$data = session::get(auth); 
			if( isset($data[$prop]) )
				return session::get(auth)[$prop]; 
			else
				return NULL;
		}
		return self::__getInstance(); 
	} 
	
	private static function __getAll() 
	{ 
		return session::get(auth);
	}
	
	public function __get( $prop ) // IMPORTANT: Must be public.
	{
		if($prop) 
		{
			$data = session::get(auth); 
			if( isset($data[$prop]) )
				return session::get(auth)[$prop]; 
			else
				return NULL;
		}
	} 
	
	private static function __login() 
	{
		global $configs;
		try 
		{
			if( isset($configs['AUTH']['login_url']) ) 
				redirect($configs['AUTH']['login_url']); 
			else
				throw new Exception( "You have not authorization." ); 
		} 
		catch( Exception $e ) 
		{
			abort( 403, $e->getMessage() );
		}
	}
	
	private static function __role( $args ) 
	{
		global $configs;
		if(!count($args)) 
		{
			if(is_null(session::get(auth))) 
				self::login();
		}
		if( isset($configs['AUTH']) ) 
		{
			if(session::get(auth)[$configs['AUTH']['apply_key']]!==current($args)) 
				self::login();
		} 
	}
	
}