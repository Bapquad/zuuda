<?php
namespace Zuuda;

interface iLayout
{
	public function rootName();
	public function HeadAsset( $type, $value );
	public function ContentAsset( $type, $value );
	public function IncludeAsset( $assets );
	public function PreloadAsset();
	public function CustomAsset();
	public function IncludeJui( $value );
	public function PreloadJui( $value );
	public function RequireJui( $value );
	
	public function HeadHtml( $value );
	public function HeadStyle( $value );
	public function HeadScript( $value );
	public function PreloadStyle( $value );
	public function PreloadScript( $value );
	public function PreloadTag( $value );
	
	public function ContentHtml( $value );
	public function ContentStyle( $value );
	public function ContentScript( $value );
	
	public function IncludeStyle( $value );
	public function IncludeScript( $value );
	public function IncludeTag( $value );
	
	public function SetHeaderLayout( $layout );
	public function SetFooterLayout( $layout );
	public function SetMainLayout( $layout );
	public function RenderBlock( $block, $extraData );
}