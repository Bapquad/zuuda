<?php
namespace Zuuda;

interface iWidgetHost 
{
	public function AddWidget( $widget, $forceName = NULL );
	public function GetWidget( $name );
}