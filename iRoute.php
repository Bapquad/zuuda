<?php
namespace Zuuda;

interface iRoute 
{
	
	public static function GetInstance();
	public static function GetAll();
	public static function Set( $pattern, $result );
	public static function Routing( $url );
	
}