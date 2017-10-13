<?php 
namespace Zuuda;

interface iThemeService 
{
	
	public static function BootService();
	public static function ResetDefault( Model $model );
	public static function Reset( Model $model );
	public static function Install( Model $model, $theme_dir );
	public static function Load();
	public static function GetInstance();
	
}