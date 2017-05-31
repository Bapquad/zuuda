<?php 
namespace Zuuda;

interface iRecordSet 
{
	public function IgnoreField( $name );
	public function ExcludeField( $name );
}