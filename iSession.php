<?php
namespace Zuuda;

interface Session 
{
	
	public static function GetInstance();
	public static function Start();
	public static function Modify( $name, $var );
	public static function Register( $name, $var = NULL );
	public static function Unregister( $name );
	public static function GetData();
	public static function Get( $name = NULL );
	public static function Set( $name, $var );
	
}