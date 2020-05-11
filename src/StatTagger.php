<?php
namespace Zuuda;

class StatTagger implements iStatTagger 
{
	
	public static function GetInstance() { return self::__getInstance(); }
	public static function Tag( Model $source, Model $target, $code ) { return self::__tag( $source, $target, $code ); }
	
	private function __construct() {}
	private function __clone() {}
	private static function __getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new StatTagger;
		}
		return $_instance;
	}
	
	private static function __tag( Model $source, Model $target, $code ) 
	{
		
	}
	
}