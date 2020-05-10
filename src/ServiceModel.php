<?php

namespace Zuuda;

class ServiceModel extends Model
{
	
	public function __construct() 
	{
		// ...
	}
	
	public function instance() 
	{
		parent::__construct();
		return $this; 
	}
	
}