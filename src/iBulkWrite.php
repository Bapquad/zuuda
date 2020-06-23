<?php
namespace Zuuda; 

interface iBulkWrite 
{
	
	/**
	 * Returns the number of write operations added to MongoDB\Driver\BulkWrite object.
	 * 
	 * Params: (No params).
	 *
	 * Return: numeric
	 */
	public function Count();
	
	/**
	 * Add a delete operation to the bulk object of MongoDB\Driver\BulkWrite class.
	 * 
	 * Params:
	 * 	@filter - An empty predicate will match all documents in the collection.
	 *
	 * 	@deleteOptions - Optional, allow user specifies some options for delete operation.
	 *
	 * Return: Bulk.
	 */
	public function Delete( array $filters, array $deleteOptions );
	
	/**
	 * Add a insert operation to the bulk object of MongoDB\Driver\BulkWrite class.
	 *
	 * Params:
	 *	@document - A document to insert.
	 *
	 * Return: Bulk.
	 */
	public function Insert( array $document ); 
	
	/**
	 * Add an update operation to the MongoDB\Driver\BulkWrite object.
	 * 
	 * Params:
	 *	@filter - An empty predicate will match all documents in the collection.
	 *
	 *	@update - A document containing update operation such as the following objects 
	 *	| update operators ($set), 
	 *	| a replacement document (field:value), 
	 *	| a aggregate pipeline.
	 *
	 *	@updateOptions - Optional, allow user specifies some option for update operation.
	 *
	 * Return: Bulk.
	 */
	public function Update( array $filter, array $update, array $updateOptions ); 
	
	/**
	 * Release all the operation from the bulk.
	 *
	 * Params: (No params).
	 *
	 * Return: Bulk.
	 */
	public function Release(); 
	
}