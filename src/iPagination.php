<?php 
namespace Zuuda;

interface iPagination 
{
	
	public function AddClasses( $values );
	public function AlwaysShow();
	public function GetCanonicalUrl();
	public function GetPageParam();
	public function GetPageUrl();
	public function GetRelPrevNextLinkTags();
	public function Parse();
	public function SetClasses( $classes );
	public function SetClean();
	public function SetCrumbs( $crumbs );
	public function SetCurrent( $current );
	public function SetFull();
	public function SetKey( $key );
	public function SetNext( $next );
	public function SetPrevious( $previous );
	public function SetRpp( $rpp );
	public function SetPath( $path );
	public function SetTotal( $total ); 
	
}