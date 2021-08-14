<?php

namespace Zuuda;

use Exception; 

class Block extends App 
{
	
	private static $this = '\Zuuda\Block';
	private static function __use($n, $c) { return new Block($n, $c); } 
	public static function Use($class_name, $codeof=NULL) { return self::__use( $class_name, $codeof ); }
	final public function Instance() { return $this->__instance( func_get_args() ); } 
	
	final protected function __instance($args) 
	{ 
		try 
		{
			$_instance = parent::__instance($args); 
			if( NULL!== $_instance ) 
			{
				if( $this->_class->getParentClass()->getName()==='Zuuda\Section' ) 
				{ 
					$_instance->CodeOf($this->_codeof); 
				} 
				else 
				{
					throw new Exception("This class is not a <b>Block</b> class. Please try another class."); 
				} 
			}
			return $_instance; 
		} 
		catch( Exception $e ) 
		{ 
			abort( 500, $e->getMessage() . "<br /><br />".error::position($e) ); 
		} 
	} 
	
}