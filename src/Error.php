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
			$out = empc; 
			switch($name) 
			{ 
				case 'errhandle': 
					if(file_exists(ROOT_DIR.'developer.cert')) 
					{
						$traces = $errObj; 
						$out1 = '<div class="error-detail"><pre>'.nl; 
						$out2 = 'Stack trace:'.nl;
						$lines = array();
						foreach( $traces as $key => $trace ) 
						{ 
							if(isset($trace['args']) ) 
							{
								$args = count($trace['args'])?'(...)':'()';
							} 
							else 
							{
								$trace['args'] = false; 
							}
							$func = $trace['function']?:NULL;
							$class = (isset($trace['class']))?$trace['class']:NULL;
							$type = (isset($trace['type']))?$trace['type']:NULL;
							$file = (isset($trace['file']))?$trace['file']:NULL;
							$line = (isset($trace['line']))?'('.$trace['line'].'): ':NULL;
							if( '__err_handler'===$func) 
							{
								$msg = 'Syntax error: '.$trace['args'][1].' in '.$trace['args'][2].':'.$trace['args'][3].nl;
							} 
							if( false===$trace['args'] ) 
							{ 
								$lines[count($lines)-1]['class'] = $class;
								$lines[count($lines)-1]['type'] = $type;
								$lines[count($lines)-1]['func'] = $func;
							} 
							else if( NULL===$line ) 
							{ 
								$lines[count($lines)-1]['class'] = $class;
								$lines[count($lines)-1]['type'] = $type;
								$lines[count($lines)-1]['func'] = $func;
								$lines[count($lines)-1]['args'] = $args;
							} 
							else 
							{
								$lines[] = array(
									'file' => $file, 
									'line' => $line, 
									'class'=> $class, 
									'type' => $type, 
									'func' => $func, 
									'args' => $args, 
								);
							}
						} 
						$line_txt = array(); 
						foreach( $lines as $key => $line ) 
						{
							if( !isset($line['file']) ) 
								continue;
							$line_txt[] = '#'.$key.space.$line['file'].$line['line'].$line['class'].$line['type'].$line['func'].$line['args'].nl;
						}
						$line_txt[] = "{main}".nl;
						$out = $out1.$msg.$out2.implode($line_txt).'</pre></div>';
					} 
					return $out;
				case 'exchandle': 
					if(file_exists(ROOT_DIR.'developer.cert')) 
					{
						$out = '<div class="error-detail"><pre>'.$errObj->__toString().'</pre></div>';
					}
				case 'position':
					if(file_exists(ROOT_DIR.'developer.cert')) 
					{
						$out = '<div class="error-detail"><pre>'.$errObj->__toString().'</pre></div>';
					} 
				default:
					break;
			} 
			return $out; 
		} 
	} 
	
	private static function __getInstance() 
	{
		static $_instance;
		if( is_null($_instance) ) 
		{
			$_instance = new Error;
		}
		return $_instance;
	} 
	
	private static function __handle() 
	{
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