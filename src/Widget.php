<?php

namespace Zuuda; 

use Zuuda\Fn;


class Widget extends Block 
{ 
	
	private static $this = '\Zuuda\Widget';
	public static function Use($class_name, $codeof=NULL) { return self::__use( $class_name, $codeof ); }
	private static function __use($n, $c) 
	{
		$infos = getSingleton('WidgetServiceLive');
		$dirName = explode(DS, $n);
		$module = $dirName[0];
		$_live = false;
		foreach( $infos as $info ) 
		{ 
			if(isset($info[$module])) 
			{ 
				if($c===$info[$module]['codeof']&&"0"!==$info[$module]['status']) 
				{ 
					return new Widget($n, $c); 
				} 
			} 
		} 
		return;
	} 
	
} 