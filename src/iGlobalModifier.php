<?php 
namespace Zuuda;

interface iGlobalModifier 
{
	
	public static function GetInstance();
	public static function Get( $name ); 
	public static function Destroy( $name ); 
	public static function Unregister( $name ); 
	public static function Set( $name, $value ); 
	public static function Register( $name, $value ); 
	public static function GetAll(); 
	public static function Func( $name );
	public static function LoadUrl();
	
}