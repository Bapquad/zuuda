<?php
namespace Zuuda;

interface iRouteService 
{
	
	public static function GetInstance();
	public static function BootService( Application $app = NULL );
	
}