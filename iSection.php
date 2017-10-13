<?php
namespace Zuuda;

interface iSection 
{
	public function rootName();
	public function SetTitle( $value );
	public function GetName();
	public function SetName( $section_name );
	public function SetTemplate( $section_tpl_name );
	public function SetLayout( $section_tpl_name );
	public function Render( $data, $aggrs );
}