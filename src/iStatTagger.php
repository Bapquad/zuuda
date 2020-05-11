<?php
namespace Zuuda;

interface iStatTagger 
{
	
	public static function GetInstance();
	public static function Tag( Model $source, Model $target, $code );
	
}