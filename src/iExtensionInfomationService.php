<?php 
namespace Zuuda;

interface iExtensionInfomationService 
{
	public static function GetInstance();
	public static function BootService( Application $app = NULL );
}