<?php

namespace Zuuda; 

use Zuuda\Fn;

class Widget extends Block 
{ 
	
	private static $class = '\Zuuda\Widget';
	public static function Use($class_name, $codeof=NULL) { return self::__use( $class_name, $codeof ); }
	private static function __use($n, $c) 
	{
		$dirName = explode(DS, $n);
		$driver = CODE.$c.DS.'Widgets'.DS.$dirName[0].DS.'driver'.DS.'widget.xml';
		$_live = false;
		if( file_exists($driver) ) 
		{
			$fp = fn::load_xml($driver); 
			if($fp->about->status->__toString()!=="0") 
			{ 
				$_live = true;
				return new Widget($n, $c); 
			} 
		} 
		return;
	} 
	
} 