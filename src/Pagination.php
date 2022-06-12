<?php

namespace Zuuda;

use Exception;
use Zuuda\Text;

class Pagination implements iHTML, iPagination 
{
	private $_classies = array( 'pg-paging', 'pagination', 'pagination-sm' );		// Holds the style class.
	private $_crumbs = '5';										// Holds
	private $_rpp = '10';										// Holds the number of rows per page.
	private $_key = 'page';										// Holds the mark of page symbol.
	private $_path = '';										// Holds the Url link.
	private $_next = 'Next &raquo;';							// Holds the markup next page.
	private $_previous = '&laquo; Previous';					// Holds the markup previous page.
	private $_alwaysShow = false;								// Holds the flag for showing pagination.
	private $_clean = false;									// ...
	private $_current;											// Holds the current page.
	private $_total;											// Holds the total page.
	
	protected function __getClassList() { return $this->_classies; }
	protected function __getCrumbs() { return $this->_crumbs; }
	protected function __getRpp() { return $this->_rpp; }
	protected function __getKey() { return $this->_key; }
	protected function __getPath() { global $url; return ( empty( $this->_path ) ) ? $url : $this->_path; }
	protected function __getNext() { return $this->_next; }
	protected function __getPrevious() { return $this->_previous; }
	protected function __getAlwaysShow() { return $this->_alwaysShow; }
	protected function __getClean() { return $this->_clean; }
	protected function __getCurrent() { return $this->_current; }
	protected function __getTotal() { return $this->_total; }
	
	protected function __addClasses( $values ) { $this->_classies = array_merge( $this->_classies, (array) $values ); return $this; }
	protected function __setClasses( $values ) { $this->__addClasses( $values ); return $this; }
	protected function __setCrumbs( $value ) { $this->_crumbs = $value; return $this; }
	protected function __setRpp( $value ) { $this->_rpp = (int) $value; return $this; }
	protected function __setKey( $value ) { $this->_key = $value; return $this; }
	protected function __setPath( $value ) { $this->_path = $value; return $this; }
	protected function __setNext( $value ) { $this->_next = $value; return $this; }
	protected function __setPrevious( $value ) { $this->_previous = $value; return $this; }
	protected function __setAlwaysShow( $value ) { $this->_alwaysShow = $value; return $this; }
	protected function __setClean( $value = true ) { $this->_clean = $value; return $this; }
	protected function __setFull() { $this->__setClean( false ); return $this; }
	protected function __setCurrent( $value ) { $this->_current = $value; return $this; }
	protected function __setTotal( $value ) { $this->_total = $value; return $this; }
	
	final public function rootName() { return __CLASS__; }
	public function GetCanonicalUrl() { return $this->__getCanonicalUrl(); }
	public function GetPageParam() { return $this->__getPageParam(); }
	public function GetPageUrl( $page = NULL, $data = NULL ) { return $this->__getPageUrl( $page, $data ); }
	public function GetRelPrevNextLinkTags() { return $this->__getRelPrevNextLinkTags(); }
	public function Parse() { return $this->__parse(); }
	public function AddClasses( $values ) { return $this->__addClasses( $values ); }
	public function SetClasses( $classes ) { return $this->__setClasses( $classes ); }
	public function Show() { return $this->__setAlwaysShow( true ); }
	public function AlwaysShow() { return $this->__setAlwaysShow( true ); }
	public function SetClean() { return $this->__setClean(); }
	public function Clean() { return $this->__setClean(); }
	public function SetCrumbs( $crumbs ) { return $this->__setCrumbs( $crumbs ); }
	public function Crumbs( $crumbs ) { return $this->__setCrumbs( $crumbs ); }
	public function SetCurrent( $current ) { return $this->__setCurrent( $current ); }
	public function Current( $current ) { return $this->__setCurrent( $current ); }
	public function SetFull() { return $this->__setFull(); }
	public function SetKey( $key ) { return $this->__setKey( $key ); }
	public function SetNext( $next ) { return $this->__setNext( $next ); }
	public function SetPrevious( $previous ) { return $this->__setPrevious( $previous ); }
	public function SetPrev( $previous ) { return $this->__setPrevious( $previous ); }
	public function SetRpp( $rpp ) { return $this->__setRpp( $rpp ); }
	public function SetLimit( $rpp ) { return $this->__setRpp( $rpp ); }
	public function Limit( $rpp ) { return $this->__setRpp( $rpp ); }
	public function SetPath( $path ) { return $this->__setPath( $path ); }
	public function SetTotal( $total ) { return $this->__setTotal( $total ); }
	public function Total( $total ) { return $this->__setTotal( $total ); }
	
	public function __construct( $current = null, $total = null ) 
	{
		if( is_null( $current ) ) 
		{
			$current = 1;
		}
		$this->__setCurrent( $current );
		
		if( !is_null( $total ) ) 
		{
			$this->__setTotal( $total );
		}
		else 
		{
			$this->__setTotal( $current );
		}
	}
	
	protected function __getCanonicalUrl() 
	{
		$url = $this->__getPath();
		$page = (int) $this->__getCurrent();
		$key = "{{".$this->__getKey()."}}";
		
		if( stripos( $url, $key ) !== false ) 
		{
			if( $page !== 1 ) 
				return base(str_replace( $key, $page, $url ));
			return base(str_replace($key, '', $url));
		}
		else 
		{
			if( $page !== 1 ) 
				return base($url.$this->__getPageParam());
			return base($url);
		}
	}
	
	protected function __getPageParam( $page = NULL ) 
	{
		$key = $this->__getKey();
		
		if( is_null( $page ) ) 
		{
			$page = (int) $this->__getCurrent();
		}
		
		return '?'.$key.'='.((int) $page);
	}
	
	protected function __getPageUrl( $page = NULL, $data = NULL ) 
	{
		global $configs;
		$url = $this->__getPath();
		$key = $this->__getKey();
		$data = $data ?: array(); 
		$params = ( isset( $configs[ 'REQUEST_VARIABLES' ] ) ) ? $configs[ 'REQUEST_VARIABLES' ] : array();
		$params = array_merge($params, $data); 
		if( false===stripos( $url, "{{".$key."}}") ) 
		{
			foreach($params as $name => $value) 
			{ 
				$params[$name] = "{$name}={$value}";
			} 
			if( NULL===$page ) 
			{
				$pagec = (int) $this->__getCurrent();
				$params[$key] = $pagec;
			} 
			else 
			{
				$pattern = "{{".$key."}}";
				$params[$key] = "{$key}={$pattern}";
			}
			$href = base($url).'?'.implode('&', $params);
		}
		else 
		{
			unset($params[$key]);
			if( count($params) ) 
			{
				foreach($params as $name => $value) 
				{ 
					$params[$name] = "{$name}={$value}";
				} 
				$href = base($url.((false===stripos($url, '?'))?'?':'&').implode('&', $params)); 
			}
			else 
			{
				$href = base($url);
			}
		}
		$href = preg_replace(array( '/=$/', '/=&/' ), array( '', '&' ), $href); 
		if( !is_null($page) ) 
		{
			$pages = (int) ceil($this->__getTotal()/$this->__getRpp());
			$pagec = (int) $this->__getCurrent();
			$page = ($page>1)?$page:1; 
			$page = ($page<$pages)?$page:$pages;
			if( $pagec===$page ) 
				$href = "javascript:void(0)"; 
			else 
				$href = str_replace( "{{".$key."}}", $page, $href );
		}
		return $href;
	}
	
	protected function __getRelPrevNextLinkTags() 
	{
		global $configs;
		$key = "{{".$this->__getKey()."}}";
		$page = (int) $this->__getCurrent();
		$pages = (int) ceil($this->__getTotal()/$this->__getRpp());
		
		$href = $this->__getPageUrl();
		
		if( $page === 1 ) 
		{
			if( $pages > 1 ) 
			{
				$href = str_replace( $key, 2, $href );
				return array( '<link rel="next" href="'.base($href).'" />' );
			}
			return array();
		}
		
		$next_page_tags = array(
			'<link rel="prev" href="'.base((str_replace($key, $page - 1, $href))).'" />'
		);
		
		if( $pages > $page ) 
		{
			array_push(
				$next_page_tags, 
				'<link rel="next" href="'.base((str_replace($key, $page + 1, $href))).'" />'
			);
		}
		
		return $next_page_tags;
	}
	
	protected function __parse() 
	{
		global $configs;
		
		$classes = $this->__getClassList();
		$crumbs = $this->__getCrumbs();
		$rpp = $this->__getRpp();
		$key = $this->__getKey();
		$url = $this->__getPath();
		$next = $this->__getNext();
		$previous = $this->__getPrevious();
		$alwaysShow = $this->__getAlwaysShow();
		$clean = $this->__getClean();
		$current = $this->__getCurrent();
		$total = $this->__getTotal();

		$this->__check($current, $total);
		
		ob_start();
		include( ROOT_DIR.WIDGET_DIR.__correctPath(str_replace('zuuda\\', '', strtolower(__CLASS__)).DS.'render.inc.php') );
		$_response = ob_get_contents();
		ob_end_clean();
		return $_response;
	}
	
	protected function __check($current, $total) 
	{
		try 
		{
			if( is_null( $current ) ) 
			{
				throw new Exception( 'Phân trang(' . get_class( $this ) . ')::Chưa cài đặt trang hiện tại.' );
			}
			
			if( is_null( $total ) ) 
			{
				throw new Exception( 'Phân trang(' . get_class( $this ) . ')::Chưa cài đặt trang cuối.' );
			}
		} 
		catch(Exception $e) 
		{
			echo $e->getMessage(); 
		}
	}
}