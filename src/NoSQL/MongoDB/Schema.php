<?php
namespace Zuuda\NoSQL\MongoDB;

use Exception; 

abstract class Schema
{
	
	public const Binary 		= "Binary"; 
	public const Decimal		= "Decimal128"; 
	public const Javascript		= "Javascript"; 
	public const MaxKey			= "MaxKey"; 
	public const MinKey			= "MinKey"; 
	public const ObjectId 		= "ObjectId"; 
	public const Regex			= "Regex"; 
	public const Timestamp		= "Timestamp"; 
	public const Datetime		= "UTCDateTime"; 
	public const Int32			= "Int32";
	public const Integer		= "Int32";
	public const Int64			= "Int64"; 
	public const String			= "String"; 
	public const Text			= "Text"; 
	public const Document		= "Document"; 
	public const Array			= "Array"; 
	
}
