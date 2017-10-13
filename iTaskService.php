<?php
namespace Zuuda;

interface iTaskService 
{
	
	public static function GetInstance();
	public static function BootService();
	public static function Task( Model $model );
	
}