<?php
namespace Zuuda;

interface iConnService 
{
	
	public static function GetInstance();
	public static function BootService();
	public static function Connect();
	
}