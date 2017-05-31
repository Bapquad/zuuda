<?php
namespace Zuuda;

interface iTemplate 
{
	public function rootName();
	public function GetVars();
	public function Assign( $name, $value );
	public function Set( $name, $value );
	public function Render( $template, $aggrs );
}