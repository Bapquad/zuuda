<?php

namespace Zuuda;

use Zuuda\cFile;

class Html implements iHTML 
{
	private static $this = '\Zuuda\Html';
	private static function __fetchTinyUrl($url) 
	{ 
		$ch = curl_init(); 
		$timeout = 5; 
		curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url[0]); 
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout); 
		$data = curl_exec($ch); 
		curl_close($ch); 
		return '<a href="'.$data.'" target = "_blank" >'.$data.'</a>'; 
	}

	public static function shortenUrls($data) {
		$data = preg_replace_callback('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', array(get_class($this), '_fetchTinyUrl'), $data);
		return $data;
	}

	public static function sanitize($data) {
		return mysql_real_escape_string($data);
	}

	public static function Instance() { return self::__getInstance(); }
	public static function GetInstance() { return self::__getInstance(); }
	public static function Link( $text, $path ) { return self::__link( $text, $path ); }
	public static function Asset( $file_path ) { return self::__assetPath( $file_path ); } 
	public static function AssetPath( $file_path ) { return self::__assetPath( $file_path ); } 
	public static function IncludeJs( $file_name ) { return self::__includeJs( $file_name ); }
	public static function IncludeCss( $file_name ) { return self::__includeCss( $file_name ); }
	public static function IncludeImg( $file_name, $alt_text ) { return self::__includeImg( $file_name, $alt_text ); }
	public static function IncludeGif( $file_name, $alt_text ) { return self::__includeGif( $file_name, $atl_text ); }
	public static function IncludePng( $file_name, $alt_text ) { return self::__includePng( $file_name, $alt_text ); }
	public static function IncludeJpg( $file_name, $alt_text ) { return self::__includeJpeg( $file_name, $alt_text ); }
	public static function IncludeJpeg( $file_name, $alt_text ) { return self::__includeJpeg( $file_name, $alt_text ); }
	public static function Write( $content ) { echo ( $content ); }
	public static function Assign( $name, $value, $template ) { return self::__assign( $name, $value, $template ); }
	public static function Json() { return call_user_func_array(array(self::$this, '__json'), func_get_args()); }
	
	private function __construct() {}
	private function __clone() {}
	private static function __getInstance() 
	{
		static $_instance;
		if( is_null( $_instance ) ) 
		{
			$_instance = new Html;
		}
		return $_instance;
	}
	
	private static function __json( $in ) 
	{
		return htmlentities(json_encode($in)); 
	}
	
	private static function __link( $text, $path ) 
	{
		return '<a href="'.$path.'">'.$text.'</a>';	
	} 

	private static function __assetPath( $file_path ) 
	{
		return __assetPath( $file_path );
	}
	
	private static function __includeJs( $file_name ) 
	{
		return '<script type="text/javascript" src="'.((preg_match('/(https)|(http):\/\//', $file_name))?$file_name:cFile::assetPath('js/'.$file_name.'.js')).'"></script>'."\n";
	}
	
	private static function __includeCss( $file_name ) 
	{
		return '<link rel="stylesheet" type="text/css" href="'.((preg_match('/(https)|(http):\/\//', $file_name))?$file_name:cFile::assetPath('skin/css/'.$file_name.'.css')).'" />'."\n";
	}
	
	private static function __includeImg( $file_name, $atl_text ) 
	{
		return '<img alt="'.$alt_text.'" src="'.((preg_match('/(https)|(http):\/\//', $file_name))?$file_name:cFile::assetPath($file_name)).'" />';
	}
	
	private static function __includeGif( $file_name, $alt_text ) 
	{
		return '<img alt="'.$alt_text.'" src="'.((preg_match('/(https)|(http):\/\//', $file_name))?$file_name:cFile::assetPath($file_name.'.gif')).'" />'; 
	}
	
	private static function __includePng( $file_name, $alt_text ) 
	{
		return '<img alt="'.$alt_text.'" src="'.((preg_match('/(https)|(http):\/\//', $file_name))?$file_name:cFile::assetPath($file_name.'.png')).'" />';
	}
	
	private static function __includeJpeg( $file_name, $alt_text ) 
	{
		return '<img alt="'.$alt_text.'" src="'.((preg_match('/(https)|(http):\/\//', $file_name))?$file_name:cFile::assetPath($file_name.'.jpg')).'" />';
	}
	
	private static function __assign( $name, $value, $template ) 
	{
		return str_replace( "{{" . $name . "}}", $value, $template );
	}
}