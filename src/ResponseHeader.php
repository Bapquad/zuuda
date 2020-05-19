<?php

namespace Zuuda;

class ResponseHeader extends Header
{
	
	private static $this = '\Zuuda\ResponseHeader';
	public static function GetContentType() { self::__getContentType(); }
	public static function Download( $export_name ) { self::__download( $export_name ); }
	public static function Location( $direct_url ) { self::__location( $direct_url ); }
	public static function ContentType( $type ) { self::__contentType( $type ); }
	public static function ContentTypeUTF8( $type ) { self::__contentTypeUtf8( $type ); }
	public static function DisplayHTML() { self::__displayHtml(); }
	public static function DisplayCSS() { self::__displayCss(); }
	public static function DisplayJS() { self::__displayJs(); }
	public static function DisplayJSON() { self::__displayJson(); }
	public static function DisplayJSON_P() { self::__displayJsonP(); }
	public static function DisplayJPG() { self::__displayJpg(); }
	public static function DisplayPNG() { self::__displayPng(); }
	public static function DisplayBMP() { self::__displayBmp(); }
	public static function DisplayGIF() { self::__displayGif(); }
	public static function DisplayText() { self::__displayText(); }
	public static function DisplayCode() { self::__displayCode(); }
	public static function Charset( $encode ) { self::__charset( $encode ); }
	public static function Request( $header_request ) { self::__request( $header_request ); }
	public static function Stream( $name ) { self::__stream( $name ); }
	final static public function GetHeaders() { return call_user_func_array(array(self::$this), array()); } 
	
	final static private function __getHeaders() 
	{
		$headersall = getallheaders();
		$headers = apache_response_headers(); 
		return $headers;
	}
	
}