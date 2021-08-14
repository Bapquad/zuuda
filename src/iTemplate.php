<?php
namespace Zuuda;

interface iTemplate 
{
	public function rootName();
	public function GetVars();
	public function Assign();
	public function Set();
	public function Share();
	public function Render( $template, $args );
}