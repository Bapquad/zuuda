<?php
namespace Zuuda;

interface iLocateService 
{
	
	public static function GetInstance();
	public static function BootService( Application $app = NULL );
	
}