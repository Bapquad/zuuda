<?php

namespace Zuuda; 

use Zuuda\Session;
use Zuuda\Fx;

class Error 
{ 

	private static $this = '\Zuuda\Error';
	final public function rootName() { return __CLASS__; }
	private function __construct() {}
	private function __clone() {} 
	public static function Instance() { return self::__getInstance(); } 
	public static function Handle() { return call_user_func_array(array(self::$this, '__handle'), array()); } 
	public static function Trigger() { return call_user_func_array(array(self::$this, '__trigger'), array(func_get_args())); } 
	
	final static public function __callStatic( $name, $args ) 
	{ 
		if( \Zuuda\Config::Get(DEVELOPMENT_ENVIRONMENT) && \Zuuda\Config::Get(DEVELOPER_WARNING) ) 
		{
			$errObj = current($args);
			$name = strtolower($name); 
			switch($name) 
			{ 
				case 'errhandle': 
					$traces = $errObj; 
					$out = '<ul class="error-lines" style="margin:0;padding:0">'; 
					foreach( $traces as $trace ) 
					{ 
						if(isset($trace['line'])) 
						{
							if(!session::has('developer.cert')) 
							{
								$rs = preg_match_all( '/(zuuda\\\src)/', $trace['file'], $matches ); 
								if($rs) continue;
							}
							$out .= '<li style="margin:.1rem 1rem;word-break: break-all;overflow-wrap: break-word;">'.$trace['file'].":".$trace['line']."</li>"; 
						}
					} 
					return $out.'</ul>';
				case 'exchandle': 
					$out = '<ul class="error-lines" style="margin:0;padding:0">'; 
					$out .= '<li style="margin:.1rem 1rem;word-break: break-all;overflow-wrap: break-word;">'.$errObj->getFile().":".$errObj->getLine()."</li>"; 
					return $out.'</ul>';
				case 'position':
					$traces = $errObj->getTrace(); 
					$out = '<ul class="error-lines" style="margin:0;padding:0">'; 
					foreach( $traces as $trace ) 
					{ 
						if(isset($trace['line'])) 
						{
							if(!session::has('developer.cert')) 
							{
								$rs = preg_match_all( '/(zuuda\\\src)/', $trace['file'], $matches ); 
								if($rs) continue;
							}
							$out .= '<li style="margin:.1rem 1rem;word-break: break-all;overflow-wrap: break-word;">'.$trace['file'].":".$trace['line']."</li>"; 
						}
					} 
					return $out.'</ul>';
				default:
					break;
			} 
		} 
	} 
	
	private static function __getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new Error;
		}
		return $_instance;
	} 
	
	private static function __handle() 
	{
		if(!session::has('developer.cert')&&!session::has('developer.cert.checked')) 
		{
			if(file_exists(ROOT_DIR.'developer.cert')) 
			{ 
				session::register('developer.cert', true); 
			} 
			session::register('developer.cert.checked', true); 
		}
		// trigger_error( "LOI ME ROI", E_USER_NOTICE );
		// dd(error_get_last());
		$old_exception_handler = fx::set_exception_handler('__exc_handler'); 
		$old_error_handler = fx::set_error_handler('__err_handler'); 
	} 
	
	private static function __trigger( $args ) 
	{ 
		$errMsg = $args[0];
		$errCode = (isset($args[1]))?$args[1]:1;
		if( 1===$errCode ) 
			trigger_error( $args[0], E_USER_ERROR );
		if( 2===$errCode ) 
			trigger_error( $args[0], E_USER_WARNING );
		if( 3===$errCode ) 
			trigger_error( $args[0], E_USER_NOTICE ); 
		return;
	} 
	
} 