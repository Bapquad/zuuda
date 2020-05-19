<?php

namespace Zuuda;

class cFile implements iFile 
{
	private static $this = '\Zuuda\cFile';
	public static function GetInstance() { return self::__getInstance(); }
	public static function Get() { return self::__getInstance(); }
	public static function IsDir( $path ) { return self::__isDir( $path ); }
	public static function IsEmptyDir( $path ) { return self::__isEmptyDir( $path ); }
	public static function OpenDir( $path ) { return self::__openDir( $path ); }
	public static function ReadDir( $path ) { return self::__readDir( $path ); }
	public static function CloseDir( $path ) { return self::__closeDir( $path ); }
	public static function ListAsset( $path ) { return self::__listAsset( $path ); }
	public static function ListFile( $path, $base=0 ) { return self::__listFile( $path, $base ); }
	public static function BaseList( $path ) { return self::__listFile($path, 1); }
	public static function FileList( $path ) { return self::__listFile($path, 1); }
	public static function LookFile( $path, $file ) { return self::__lookFile( $path, $file ); }
	public static function LookDir( $path, $file = NULL ) { return self::__lookDir( $path, $file ); } 
	public static function MakeDir( $path ) { return self::__make_dir($path); } 
	public static function RemoveDir( $path ) { return self::__remove_dir($path); } 
	public static function Remove( $path ) { return self::__remove($path); } 
	
	public static function AssetPath( $path, $file = true ) { return __assetPath( $path, $file ); }
	public static function BuildPath( $path, $file = true ) { return __assetPath( $path, $file, true ); }
	
	public function __invoke( $name ) { return new FileInfo( $name, true ); }
	private function __clone(){}
	private function __construct(){} 
	private static function __getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new cFile;
		}
		return $_instance;
	}
	
	private static function __isDir( $path ) 
	{
		return is_dir( $path );
	} 
	
	private static function __isEmptyDir( $path ) 
	{ 
		if( !is_readable($path) ) 
			return NULL; 
		if( !is_dir($path) ) 
			return true; 
		if( count(scandir($path)) == 2 ) 
			return true; 
	} 
	
	private static function __openDir( $path ) 
	{
		return opendir( $path );
	}
	
	private static function __readDir( $hl ) 
	{
		return readdir( $hl );
	}
	
	private static function __closeDir( $hl ) 
	{
		return closedir( $hl );
	} 
	
	private static function __readFileRecursive( $path, &$output, $base ) 
	{ 
		$hl = self::__openDir($path);
		$ig = array('.','..','.DS_Store','empty','.htaccess');
		while( $rsName = self::__readDir($hl) ) 
		{ 
			if( in_array($rsName, $ig) ) continue; 
			if( !self::__isDir($path.$rsName) ) 
			{
				if( $base ) 
				{ 
					$output[] = $rsName;
				} 
				else 
				{
					$fileName = $path.$rsName;
					$output[] = $fileName;
				}
				continue;
			}
			self::__readDirRecursive($rsName, $output); 
		} 
		self::__closeDir($hl); 
	} 
	
	private static function __readDirRecursive( $path, &$output ) 
	{ 
		$hl = self::__openDir($path);
		$ig = array('.','..','.DS_Store','empty','.htaccess');
		while( $rsName = self::__readDir($hl) ) 
		{ 
			if( !self::__isDir($path.$rsName) ) continue;
			if( in_array($rsName, $ig) ) continue; 
			$rsName = $path.$rsName.DS;
			$output[] = $rsName;
			self::__readDirRecursive($rsName, $output); 
		} 
		self::__closeDir($hl); 
	} 
	
	private static function __listFile( $path, $base=0 ) 
	{ 
		$result = [];
		if(self::__isDir($path)) 
		{ 
			self::__readFileRecursive( $path, $result, $base );
		} 
		return $result;
	} 
	
	private static function __lookFile( $path, $file ) 
	{
		$folders = [];
		$result = [];
		if(self::__isDir($path)) 
		{ 
			self::__readDirRecursive( $path, $folders );
		} 
		foreach($folders as $dir) 
		{
			$filePath = $dir.$file;
			if(file_exists($filePath)) 
			{ 
				$result[] = $filePath;
			} 
		}
		return $result;
	}
	
	private static function __lookDir( $path, $file = NULL ) 
	{
		$result = [];
		if(self::__isDir($path)) 
		{ 
			self::__readDirRecursive( $path, $result );
		} 
		return $result;
	} 
	
	private static function __make_dir( $path, &$force=NULL ) 
	{
		if( !is_dir($path) ) 
		{
			$parent_path = dirname($path); 
			if( is_dir($parent_path) ) 
			{
				return mkdir($path); 
			} 
			else 
			{ 
				if( is_array($force) ) 
				{
					$force[] = $parent_path;
					self::__make_dir($parent_path, $force); 
				} 
				else 
				{ 
					self::__make_dir($parent_path); 
				} 
				return mkdir($path); 
			} 
		}
		return false;
	}
	
	private static function __remove_dir( $path ) 
	{
		if( is_dir($path) && is_readable($path) ) 
			if( count(scandir($path)) == 2 ) 
				return rmdir($path); 
		return;
	}
	
	private static function __remove( $path ) 
	{ 
		if( file_exists($path) ) 
		{
			return unlink($path); 
		} 
		return; 
	} 
	
}