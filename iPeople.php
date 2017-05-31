<?php
namespace Zuuda;

interface iPeople 
{
	
	final public function GetAll();
	final public function GetOne( $data_id ); 
	final public function GetOnlineList(); 
	final public function GetOnline(); 
	final public function IsOne( $data_id = NULL ); 
	
}