<?php

/*
SOURCE=======================================================
<h2>{{$name}}</h2>
<h6>Futures</h6>
#if(true)
#foreach($features as $feature)
<p>{{$feature['name']}} has {{$feature['code']}}</p>
#endforeach
#for($i=1;$i<=6;$i++)
<h{{$i}}>Hello</h{{$i}}>
#endfor
#endif
#php 
#endphp
#include(file)

COMPILE=====================================================
<h2><?=$name?></h2>
<h6>Futures</h6>
<?php if(true): ?>
<?php foreach( $features as $feature ): ?>
<p><?=$feature['name']?> has <?=$feature['code']?></p>
<?php endforeach ?>
<?php for($i=1;$i<=6;i++): ?>
<h<?=$i?>>Hello</h<?=$i?>>
<?php endfor ?>
<?php endif ?>
<?php
?>
<?php include() ?>
*/
		
		
namespace Zuuda;

use Zuuda\Fx;
use Zuuda\Error;

class Tiny 
{ 

	private static $this = '\Zuuda\Tiny';
	private $_input;
	public function rootName() { return __CLASS__; } 
	final private function __construct() {} 
	final private function __clone() {} 
	final static public function Template() { return call_user_func_array(array(self::$this, '__instance'), func_get_args()); } 
	final public function Compile() { return call_user_func_array(array(self::$this, '__compile'), func_get_args()); } 
	
}