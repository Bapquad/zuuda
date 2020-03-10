<?php 
namespace Zuuda;
use Exception;

class Response
{
	
	private $_data; 
	private $_dispatcher;
	
	public static function Redirect( $uri ) { return __direct( $uri ); } 
	public static function View( Controller $dispatcher ) { return self::__view($dispatcher); } 
	public function With() { return call_user_func_array([$this, '__with'], array(func_get_args(), func_num_args())); } 
	public function Json( $data ) { return $this->__json($data); } 
	
	final public function SetDispatcher( Controller $dispatcher ) { $this->_dispatcher = $dispatcher; } 
	final public function rootName() { return __CLASS__; }
	private function __construct() {}
	private function __clone() {}
	
	private static function __getInstance() 
	{
		static $_instance;
		if( is_null($_instance) ) 
		{
			$_instance = new Response;
		}
		return $_instance;
	} 
	
	private function __with( $args, $argsNum ) 
	{ 
		try 
		{
			$oneArg = 1; 
			$twoArg = 2; 
			if( $oneArg===$argsNum ) 
			{
				$data = current($args); 
				if( is_string($data) ) 
				{
					return $this->_dispatcher->render( $data ); 
				} 
				else if( is_array($data) ) 
				{
					foreach($data as $key => $value ) 
						return $this->_dispatcher->assign( $key, $value );
				} 
				else 
				{
					throw new Exception( "Usage Response::with() is incorrect." ); 
				}
			} 
			else if( $twoArg===$argsNum ) 
			{
				return $this->_dispatcher( $args[0], $args[1] );
			}
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() ); 
		} 
	} 
	
	private function __json( $data ) 
	{
		return $this->_dispatcher->json( $data ); 
	} 
	
	private static function __view( $dispatcher ) 
	{
		$_instance = self::__getInstance(); 
		$_instance->setDispatcher( $dispatcher ); 
		return $_instance;
	} 
	
	private function __setDispatcher( $dispatcher ) 
	{
		$this->_dispatcher = $dispatcher; 
	}
	
}