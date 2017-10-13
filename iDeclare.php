<?php
namespace Zuuda;

interface iDeclare 
{
	public function rootName();
	
	public function Assign( $name, $value );
	public function Set( $name, $value );
	public function Render( $template, $aggrs );
	
	public function IncludeMeta( $value );
	public function IncludeHtml( $value );
	public function IncludeCss( $value );
	public function IncludeJs( $value );
	
	public function PreloadMeta( $value );
	public function PreloadHtml( $value );
	public function PreloadCss( $value );
	public function PreloadJs( $value );
	
}