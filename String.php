<?php

namespace Zuuda;

class String 
{
	private $the_string;
	
	final public function rootName() { return __CLASS__; }

	public function __construct($value) 
	{
		$this->the_string = $value;
	}

	public function __toString() 
	{
		return $this->the_string;
	}

	public function StripSlashesDeep() 
	{
		stripSlashesDeep($this->the_string);
		return $this;
	}
}