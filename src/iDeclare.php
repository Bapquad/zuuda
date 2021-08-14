<?php
namespace Zuuda;

interface iDeclare 
{
	public function rootName();
	
	public function Assign();
	public function Set();
	public function Share(); 
	public function Render( $template, $args );
	
	public function IncludeMeta( $value );
	public function IncludeHtml( $value );
	public function IncludeCss( $value );
	public function IncludeJs( $value );
	
	public function PreloadMeta( $value );
	public function PreloadHtml( $value );
	public function PreloadCss( $value );
	public function PreloadJs( $value );
	
}