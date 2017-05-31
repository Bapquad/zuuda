<?php

namespace Zuuda;

class cFile implements iFile 
{
	public static function GetInstance() { return self::_getInstance(); }
	public static function Get() { return self::_getInstance(); }
	public static function IsDir( $path ) { return self::_isDir( $path ); }
	public static function OpenDir( $path ) { return self::_openDir( $path ); }
	public static function ReadDir( $path ) { return self::_readDir( $path ); }
	public static function CloseDir( $path ) { return self::_closeDir( $path ); }
	public static function LookFile( $path, $file ) { return self::_lookFile( $path, $file ); }
	public static function LookDir( $path, $file = NULL ) { return self::_lookDir( $path, $file ); }
	
	public static function AssetPath( $path, $file = false ) { return _assetPath( $path, $file ); }
	
	public function __invoke( $name ) { return new FileInfo( $name ); }
	private function __clone(){}
	private function __construct(){} 
	private static function _getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new cFile;
		}
		return $_instance;
	}
	
	private static function _isDir( $path ) 
	{
		return is_dir( $path );
	}
	
	private static function _openDir( $path ) 
	{
		return opendir( $path );
	}
	
	private static function _readDir( $hl ) 
	{
		return readdir( $hl );
	}
	
	private static function _closeDir( $hl ) 
	{
		return closedir( $hl );
	}
	
	private static function _lookFile( $path, $file ) 
	{
		return self::_lookDir( $path, $file );
	}
	
	private static function _lookDir( $path, $file = NULL ) 
	{
		$rt = array();
		
		if( self::_isDir( $path ) ) 
		{
			if( $hl = self::_openDir( $path ) ) 
			{
				while( ( $name = self::_readDir( $hl ) ) !== false ) 
				{
					if( $name == '.' || $name == '..' || $name == 'empty' ) 
					{
						continue;
					}
					$rt = array_merge( $rt, (array) ( _correctPath( $path . ( ( is_null( $file ) ) ? $name : $name . PS . $file ) ) ) );
				}
			}
		}
		return $rt;
	}
	
}