<?php
namespace Zuuda\NoSQL\MongoDB;

use MongoDB\Driver\BulkWrite; 
use MongoDB\BSON\ObjectId; 
use Zuuda\iBulkWrite as iBulk; 

class Bulk implements iBulk 
{
	
	private static $this = '\Zuuda\NoSQL\MongoDB\Bulk'; 
	private $_propsDeleteOp; 
	private $_propsUpdateOp; 
	private $_propsInsertOp; 
	private $_propsInsertedId;
	private $_result; 
	
	final public function Count() { return call_user_func_array(array($this, '__count'), array()); } 
	final public function Delete( array $filters, array $deleteOptions = array() ) { return call_user_func_array(array($this, '__delete'), func_get_args()); } 
	final public function Insert( array $document ) { return call_user_func_array(array($this, '__insert'), func_get_args()); } 
	final public function Update( array $filter, array $update, array $updateOptions = array() ) { return call_user_func_array(array($this, '__update'), func_get_args()); } 
	final public function Release() { return call_user_func_array(array($this, '__release'), array()); } 
	final public function Compute() { return call_user_func_array(array($this, '__compute'), array()); } 
	final public function Result() { return call_user_func_array(array($this, '__result'), func_get_args()); } 
	
	public function __construct() 
	{
		call_user_func_array(array($this, '__release'), array()); 
	} 
	
	final private function __count() 
	{
		$dc = count($this->_propsDeleteOp); 
		$uc = count($this->_propsUpdateOp); 
		$ic = count($this->_propsInsertOp); 
		return $dc+$uc+$ic; 
	} 
	
	final private function __delete( array $filters, array $deleteOptions = array() ) 
	{
		$input = array( $filters, $deleteOptions );
		$this->_propsDeleteOp[] = $input; 
		return $this; 
	} 
	
	final private function __insert( array $document ) 
	{
		$document["_id"] = new ObjectId; 
		$input = array( $document ); 
		$this->_propsInsertOp[] = $input; 
		return $this; 
	} 
	
	final private function __update( array $filters, array $update, array $updateOptions = array() ) 
	{
		$input = array( $filters, $update, $updateOptions ); 
		$this->_propsUpdateOp[] = $input; 
		return $this; 
	} 
	
	final private function __release() 
	{
		$this->_propsDeleteOp = array(); 
		
		if( NULL!==$this->_propsInsertOp ) 
		{
			$this->_propsInsertedId = array(); 
			foreach( $this->_propsInsertOp as $op ) 
			{
				$this->_propsInsertedId[] = $op[0]["_id"];
			} 
		}
		$this->_propsInsertOp = array(); 
		
		$this->_propsUpdateOp = array(); 
		return $this; 
	} 
	
	final private function __compute() 
	{
		$bulk = new BulkWrite; 
		
		foreach( $this->_propsDeleteOp as $op ) 
			call_user_func_array(array($bulk, 'delete'), $op); 
		
		foreach( $this->_propsInsertOp as $op ) 
			call_user_func_array(array($bulk, 'insert'), $op);
		
		foreach( $this->_propsUpdateOp as $op ) 
			call_user_func_array(array($bulk, 'update'), $op); 
		
		return $bulk;
	} 
	
	final private function __result( array $result = null ) 
	{ 
		if( NULL===$result ) 
			return array_merge( $this->_result, array('insertIds'=>$this->_propsInsertedId) ); 
		$this->_result = $result; 
		return $this; 
	}
	
}