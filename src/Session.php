<?php

namespace Zuuda;

class Session 
{
	
	static protected $_data = array();
	static protected $_inst;
	private static $this = '\Zuuda\Session';
	public static function Instance() { return self::__instance(); }
	public static function GetInstance() { return self::__instance(); }
	public static function Start() { return self::__start(); }
	public static function Modify( $name, $value ) { return self::__modify( $name, $value ); }
	public static function Register( $name, $value = NULL ) { return self::__register( $name, $value ); }
	public static function Unregister( $name ) { return self::__unregister( $name ); }
	public static function Remove( $name ) { return self::__unregister( $name ); }
	public static function GetData() { return self::__getData(); } 
	public static function GetAll() { return self::__getData(); }
	public static function Get( $name = NULL ) { return self::__getVar( $name ); }
	public static function Set( $name, $value ) { return self::__setVar( $name, $value ); } 
	public static function Has( $name ) { return self::__has( $name ); } 
	public static function Init() { return call_user_func_array(array(self::$this, '__register'), func_get_args()); }
	public static function Pull() { return call_user_func_array(array(self::$this, '__pull'), func_get_args()); } 
	public static function Push() { return call_user_func_array(array(self::$this, '__push'), array(func_get_args())); } 
	public static function Find() { return call_user_func_array(array(self::$this, '__find'), func_get_args()); } 
	public static function Edit() { return call_user_func_array(array(self::$this, '__edit'), array(func_get_args())); }
	public static function Merge() { return call_user_func_array(array(self::$this, '__edit'), array(func_get_args())); }
	public static function Earse() { return call_user_func_array(array(self::$this, '__earse'), array()); }
	public static function Empty() { return call_user_func_array(array(self::$this, '__empty'), array()); }
	public static function Commit() { return call_user_func_array(array(self::$this, '__commit'), array(func_get_args())); } 
	public static function Status() { return call_user_func_array(array(self::$this, '__status'), array()); }
	protected $_memd = array();
	protected $_memb = empc;
	protected $_memc = empc;
	
	final public function rootName() { return __CLASS__; }
	private function __construct() {}
	private function __clone() {}
	
	private static function __instance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			self::$_inst = $_instance = new Session;
		}
		return $_instance;
	}
	
	private static function __pull( $arg ) 
	{ 
		if( array_key_exists($arg, self::$_data) ) 
		{
			if( is_array(self::$_data[$arg]) ) 
			{
				self::$_inst->_memd = self::$_data[$arg]; 
			} 
			else 
			{ 
				self::$_inst->_memd = array(); 
			} 
		} 
		self::$_inst->_memb = $arg;
		return self::$_inst;
	} 
	
	private static function __find( $arg ) 
	{ 
		if( array_key_exists($arg, self::$_inst->_memd) ) 
		{
			self::$_inst->_memd = self::$_inst->_memd[$arg]; 
		} 
		else 
		{ 
			self::$_inst->_memd = array(); 
		}
		self::$_inst->_memc = $arg; 
		return self::$_inst;
	} 
	
	private static function __save( $args ) 
	{
		foreach( $args as $data ) 
		{ 
			self::$_inst->_memd = array_merge( self::$_inst->_memd, $data );
		} 
	}
	
	private static function __push( $args ) 
	{  
		self::$_inst->_memd = array();
		call_user_func_array(array(self::$this, '__save'), array($args)); 
		$item = self::$_inst->_memd; 
		self::$_inst->_memd = self::$_data[self::$_inst->_memb];
		array_push( self::$_inst->_memd, $item );
		return self::$_inst;
	} 
	
	private static function __edit( $args ) 
	{ 
		call_user_func_array(array(self::$this, '__save'), array($args));
		if( empc!==self::$_inst->_memc ) 
		{
			$item = self::$_inst->_memd; 
			self::$_inst->_memd = self::$_data[self::$_inst->_memb];
			self::$_inst->_memd[self::$_inst->_memc] = $item;
			self::$_inst->_memc = empc;
		}
		return self::$_inst;
	} 
	
	private static function __empty() 
	{ 
		self::$_inst->_memd = array(); 
		self::$_inst->_memc = empc; 
		return self::$_inst;
	} 
	
	private static function __earse() 
	{ 
		self::$_inst->_memd = NULL; 
		return self::$_inst;
	} 
	
	private static function __commit() 
	{
		if( empc!==self::$_inst->_memc ) 
		{ 
			if( NULL===self::$_inst->_memd ) 
			{ 
				$tmp = self::$_data[self::$_inst->_memb]; 
				if( array_key_exists(self::$_inst->_memc, $tmp) )
				{ 
					unset($tmp[self::$_inst->_memc]);
				} 
				self::$_inst->_memc = empc;
				self::$_inst->_memd = $tmp;
			} 
		} 
		if( empc!==self::$_inst->_memb ) 
		{
			if( NULL===self::$_inst->_memd ) 
				if( array_key_exists(self::$_inst->_memb, self::$_data) )
					call_user_func_array(array(self::$this, '__unregister'), array(self::$_inst->_memb)); 
			$rs = call_user_func_array(array(self::$this, '__setVar'), array(self::$_inst->_memb, self::$_inst->_memd)); 
		} 
		self::$_inst->_memd = array(); 
		self::$_inst->_memb = empc;
		return $rs; 
	} 
	
	private static function __status() 
	{ 
		self::$_data = $_SESSION;
		return self::$_data;
	} 
	
	private static function __start() 
	{
		session_start(); 
		self::$_data = $_SESSION;
	}
	
	private static function __modify( $name, $value ) 
	{
		if( array_key_exists( $name, self::$_data ) ) 
		{
			$_SESSION[$name] = $value;
			self::$_data[$name] = $value;
			return true;
		}
		return false;
	}

	private static function __register( $name, $value=array() ) 
	{
		if( !array_key_exists( $name, self::$_data ) ) 
		{
			$_SESSION[$name] = $value;
			self::$_data[$name] = $value;
		}
		return self::$_inst;
	}

	private static function __unregister( $name ) 
	{
		if( array_key_exists( $name, self::$_data ) ) 
		{
			unset( $_SESSION[$name] );
			unset( self::$_data[$name] );
			return true;
		}
		return false;
	}

	private static function __setVar( $name, $value ) 
	{
		return self::__modify( $name, $value );
	} 

	private static function __getVar( $name = NULL ) 
	{
		if( NULL===$name ) 
		{
			return self::$_data;
		}
		if( array_key_exists( $name, self::$_data ) ) 
		{
			return self::$_data[$name];
		}
		else 
		{
			return NULL;
		}
	}
	
	private static function __getData() 
	{
		return self::$_data;
	} 
	
	private static function __has( $name ) 
	{ 
		return array_key_exists( $name, self::$_data );
	} 
	
}