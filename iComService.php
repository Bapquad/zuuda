<?php 
namespace Zuuda;

interface iComService 
{
	
	public static function GetInstance(); 
	public static function BootService( Application $app = NULL );
	
}