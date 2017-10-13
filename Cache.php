<?php

namespace Zuuda;

class Cache {
	
	final public function rootName() { return __CLASS__; }

	function get($fileName) {
		$fileName = _correctPath( ROOT.DS.'tmp'.DS.'cache'.DS.$fileName );
		if (file_exists($fileName)) {
			$handle = fopen($fileName, 'rb');
			$variable = fread($handle, filesize($fileName));
			fclose($handle);
			return unserialize($variable);
		} else {
			return null;
		}
	}
	
	function set($fileName,$variable) {
		$fileName = _correctPath( ROOT.DS.'tmp'.DS.'cache'.DS.$fileName );
		$handle = fopen($fileName, 'a');
		fwrite($handle, serialize($variable));
		fclose($handle);
	}

}
