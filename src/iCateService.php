<?php 
namespace Zuuda;

interface iCateService 
{
	
	public static function GetInstance();
	public static function BootService( Application $app = NULL );
	public static function GetPath( $category, $item, $sp='/', $last=NULL );
	public static function GetParent( $category, $item );
	
}