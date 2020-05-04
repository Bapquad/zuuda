<?php
namespace Zuuda;

use Exception;
use Zuuda\Session;
use Zuuda\RequestHeader;
use Zuuda\Pagination;

abstract class DataGrid implements iHTML, iData, iDataGridv1_0, iSection, iTemplate 
{
	private $_vars = array();			// Holds the widget datas.
	private $_name;						// Holds the widget name.
	private $_title;					// Holds the title of page.
	private $_column_data = array();	// Holds the column details.
	private $_model;					// Holds the model of widget.
	private $_tpl_path;					// Holds the template path.
	private $_option_data;				// Holds the options controls.
	private $_length;					// Holds the number of rows of list.
	private $_total_pages;				// Holds the number of pages.
	private $_current_page;				// Holds the current page.
	private $_page_printer;				// Holds the url paging page.
	private $_default_num_of_rows = 3;	// Holds the default number of rows.
	private $_keyword;					// Holds the keyword of search pharse.
	private $_filter;					// Holds the filter mode.
	private $_sort;						// Holds the sort value.
	private $_order;					// Holds the current field order.
	private $_data_grid;
	private $_paging;
	
	protected function __getVars() { return $this->_vars; }
	protected function __getName() { return $this->_name; }
	protected function __getTitle() { return $this->_title; }
	protected function __getColumnData() { return $this->_column_data; }
	protected function __getModel() { return $this->_model; }
	protected function __getTemplate() { return $this->_tpl_path; }
	protected function __getOptionData() { return $this->_option_data; }
	protected function __getLength() { return $this->_length; }
	protected function __getTotalPages() { return $this->_total_pages; }
	protected function __getCurrentPage() { return $this->_current_page; }
	protected function __getPagePrinter() { return $this->_page_printer; }
	protected function __getDefaultNumOfRows() { return $this->_default_num_of_rows; }
	protected function __getKeyword() { return $this->_keyword; }
	protected function __getFilter() { return $this->_filter; }
	protected function __getSort() { return $this->_sort; }
	protected function __getOrder() { return $this->_order; }
	protected function __getData() { return $this->_data_grid; }
	protected function __getPaging() { return $this->_paging; }
	
	protected function __setName( $value ) { $this->_name = $value; return $this; }
	protected function __setTitle( $value ) { $this->_title = $value; return $this; }
	protected function __setColumnData( $value ) { $this->_column_data = $value; return $this; }
	protected function __setModel( $value ) { $this->_model = $value; return $this; }
	protected function __setTemplate( $value ) { $this->_tpl_path = $value; return $this; }
	protected function __setOptionData( $value ) { $this->_option_data = $value; return $this; }
	protected function __setLength( $value ) { $this->_length = $value; return $this; }
	protected function __setTotalPages( $value ) { $this->_total_pages = $value; return $this; }
	protected function __setCurrentPage( $value ) { $this->_current_page = $value; return $this; }
	protected function __setDefaultNumOfRows( $value ) { $this->_default_num_of_rows = $value; return $this; }
	protected function __setKeyword( $value ) { $this->_keyword = $value; return $this; }
	protected function __setFilter( $value ) { $this->_filter = $value; return $this; }
	protected function __setSort( $value ) { $this->_sort = $value; return $this; }
	protected function __setOrder( $value ) { $this->_order = $value; return $this; }
	protected function __setData( $value ) { $this->_data_grid = $value; return $this->_data_grid; }
	protected function __setPaging( $value ) { $this->_paging = $value; return $this; }
	protected function __setPagePrinter( $pagePattern, $data = NULL ) 
	{
		$params = array();
		if( NULL!==$data )
			foreach( $data as $name => $value ) 
				$params[] = "$name=$value";
		
		if( count($params) ) 
			$pagePattern .= '?' . implode( '&', $params );
		$this->_page_printer = $pagePattern; 
		return $this;
	}
	
	/** Implements Interface iData */
	final public function SetModel( Model $model = NULL ) { return $this->__setModel( $model ); }
	final public function GetVars() { return $this->__getVars(); }
	final public function Assign() { return $this->__setVar( func_get_args(), func_num_args() ); }
	final public function Set() { return $this->__setVar( func_get_args(), func_num_args() ); }
	final public function Share() { return $this->__setVar( func_get_args(), func_num_args() ); }
	
	/** Implements Interface iDataGrid */
	final public function AddColumn( DataColumn $column ) { return $this->__addColumn( $column ); }
	final public function DeleteRow( $id ) { return $this->__deleteRow( $id ); }
	final public function Length() { return $this->__getLength(); }
	final public function SetLength( $value ) { return $this->__setLength( $value ); }
	final public function TotalPages() { return $this->__getTotalPages(); }
	final public function SetTotalPages( $value ) { return $this->__setTotalPages( $value ); }
	final public function SetCurrentPage( $value ) { return $this->__setCurrentPage( $value ); }
	final public function GetCurrentPage() { return $this->__getCurrentPage(); }
	final public function SetPagePrinter( $printer, $data=NULL ) { return $this->__setPagePrinter( $printer, $data ); }
	final public function GetPagePrinter() { return $this->__getPagePrinter(); }
	final public function EnablePagePrint() { return $this->__enablePagePrint(); }
	final public function DestroyPagePrint() { return $this->__destroyPagePrint(); }
	final public function EnableSectionOption( $data ) { return $this->__enableSectionOption( $data ); }
	final public function DestroySectionOption() { return $this->__destroySectionOption(); }
	final public function EnableOption() { return $this->__enableOption(); }
	final public function DestroyOption() { return $this->__destroyOption(); }
	final public function EnableScrumb() { return $this->__enableScrumb(); }
	final public function DestroyScrumb() { return $this->__destroyScrumb(); }
	final public function GetNumberRows() { return $this->__getNumberRows(); }
	final public function ChangeNumberRows( $value ) { return $this->__changeNumberRows( $value ); }
	final public function SetRowsNumberList( $list ) { return $this->__setRowsNumberList( $list ); }
	final public function DestroyRowsNumberList() { return $this->__destroyRowsNumberList(); }
	final public function DefaultRowsNumberList() { return $this->__defaultRowsNumberList(); }
	final public function SetDefaultNumOfRows( $value ) { return $this->__setDefaultNumOfRows( $value ); }
	final public function ResetOptionData() { return $this->__resetOptionData(); }
	final public function EnableKeyword( $field, $label, $opera ) { return $this->__enableKeyword( $field, $label, $opera ); }
	final public function DestroyKeyword() { return $this->__destroyKeyWord(); }
	final public function SetKeyword( $value ) { return $this->__setKeyword( $value ); }
	final public function GetPaging() { return $this->__getPaging(); }
	final public function SetPaging( $value = true ) { return $this->__setPaging( $value ); }
	final public function EnablePaging() { return $this->__setPaging( true ); }
	
	final public function SetChangeNumberRowsApi( $value ) { return $this->__setChangeNumberRowsApi( $value ); }
	final public function SetScrumbEditApi( $value ) { return $this->__setScrumbEditApi( $value ); }
	final public function SetScrumbDeleteApi( $value ) { return $this->__setScrumbDeleteApi( $value ); }
	final public function SetResetOptionApi( $value ) { return $this->__setResetOptionApi( $value ); }
	
	/** Implements Interface iSection */
	final public function SetTitle( $value ) { return $this->__setTitle( $value ); } 
	final public function GetName() { return $this->__getName(); }
	final public function SetName( $name ) { return $this->__setName( $name ); }
	final public function SetTemplate( $template ) { return $this->__setTemplate( $template ); }
	final public function SetLayout( $template ) { return $this->__setLayout( $template ); }
	final public function Render( $template = NULL, $data = NULL ) { return $this->__render( $template, $data ); }
	
	public function rootName() { return __CLASS__; }
	
	public function __construct( Model $model = NULL ) 
	{		
		// Iniializing the option data.
		$option_data = Session::get( 'grid' );
		$curr_area = get_class( $this );
		
		// Reset the option data when it has not exist or it be in the other area.
		if( is_null( $option_data ) ) 
		{
			Session::register( 'grid' );	// Initialize a grid session variable.
			$this->__resetOptionData();		// Default the option data.
		}
		else 
		{
			if( $option_data[ 'curr_area' ] != $curr_area ) 
			{
				$this->__resetOptionData();	// Back default option data.
			}
			else 
			{
				$this->__setOptionData( $option_data );
			}
		}
		
		// Save the primary model.
		$this->__setModel( $model );
		
		// Default it not use the session option menu.
		$this->__enableOption();
		$this->__destroySectionOption();
	}
	
	protected function buildData() { return $this->__buildData(); }
	
	protected function __buildData() 
	{
		global $configs;
		$model = $this->__getModel();
		$columns = $this->__buildColumns()->__getColumnData();
		$default_num_rows = $this->__getDefaultNumOfRows();
		$option_data = $this->__getOptionData();
		$num_of_rows = $this->__getNumberRows();
		$current_page = $this->__getCurrentPage();
		$filter = $this->__getFilter();
		$keyword = $this->__getKeyword();
		$printer = $this->__getPagePrinter();
		$extra_printer = '';
		$sort = $this->__getSort();
		$order = $this->__getOrder();
		// Field of select.
		$bounds = array(); 
		foreach( $columns as $key => $column ) 
		{
			if( $column->getType() == COLLECTION_FIELDSET_TYPE ) 
			{
				$bounds[] = $column->getName();
			}
		}
		$model->bound( $bounds );
		
		if( !is_null( $filter ) && !is_null( $keyword ) ) 
		{
			$opera = strtolower( $filter[ 'opera' ] );
			$model->$opera( $filter[ 'field' ], '%'.$keyword.'%' );
			$extra_printer .= 'key={key}&';
		}
		
		if( !is_null( $order ) ) 
		{
			if( is_null( $sort ) ) 
			{
				$sort = 'asc';
				$this->__setSort( $sort );
			} 
			if( isset( $configs[ 'REQUEST_VARIABLES' ][ 'order' ] ) ) 
			{
				$extra_printer .= 'sort={sort}&';
				$extra_printer .= 'order={order}&';
			}
			$model->orderBy( $order, $sort );
		}

		// Building page printer.
		$printer .= ( ( $extra_printer != "" ) ? '?' : NULL) . substr( $extra_printer, 0, -1 );
		$this->__setPagePrinter( $printer );
		
		// Computing the total pages.
		if( !is_null( $option_data ) ) 
		{
			$num_of_rows = $option_data[ 'num_of_rows' ];
		}
		else 
		{
			$num_of_rows = $default_num_rows;
		} 
		$model->limit( $num_of_rows );
		$result = $model->page( $current_page )->limit( $num_of_rows )->search(); 
		$length = $model->total();
		$this->__setLength( $length );
		$total_pages = (int)ceil( $length/$num_of_rows );
		$this->__setTotalPages( $total_pages );
		
		// Check current page greater total pages.
		if( $current_page > $total_pages ) 
		{
			$current_page = ( $total_pages > 0 ) ? $total_pages : 1;
			$push_state = str_replace( '{page}', ( ( $current_page != 1 ) ? $current_page : NULL ), $printer );
			$push_state = str_replace( '{key}', $keyword, $push_state );
			$push_state = str_replace( '{sort}', $sort, $push_state );
			$push_state = str_replace( '{order}', $order, $push_state );
			$this->set( 'push_state', $push_state );
			$this->__setCurrentPage( $current_page );
		}
		
		if( $this->getPaging() === true ) 
		{
			$paging = new Pagination( $current_page, $length );
			// $paging->setPath( 'admin/stat/index/' );
			// $paging->setPath( '/?page={page}' );
			$paging->setPath( $printer );
			$paging->setRpp( $num_of_rows );
			$this->set( 'paging', $paging );
			$this->__setPaging( $paging );
		}

		// Final building the data
		return $this->__setData( $result );
	}
	
	protected function __buildColumns() 
	{
		$this->buildColumns();
		return $this;
	}
	
	protected function __addColumn( DataColumn $column ) 
	{
		global $configs;
		
		$columns = $this->__getColumnData();
		array_push( $columns, $column );
		$this->__setColumnData( $columns );
		if( $column->hasSort() ) 
		{
			if( isset( $configs[ 'REQUEST_VARIABLES' ][ 'order' ] ) ) 
			{
				$order = $configs[ 'REQUEST_VARIABLES' ][ 'order' ];
				if( $column->getName() == $order ) 
				{
					$sort = ( !isset( $configs[ 'REQUEST_VARIABLES' ][ 'sort' ] ) ) ? 'asc' : $configs[ 'REQUEST_VARIABLES' ][ 'sort' ];
					$this->__setOrder( $order );
					$this->__setSort( $sort );
				}
			}
			else if( $column->getDefaultSort() != NULL ) 
			{
				$this->__setOrder( $column->getName() );
				$this->__setSort( $column->getDefaultSort() );
			}
		}
		
		return $this;
	}
	
	protected function __deleteRow( $id ) 
	{
		$this->__getModel()->delete( $id );
		return $this;
	}
	
	protected function __enablePagePrint() 
	{
		$this->set( 'use_pg_print', true );
		
		return $this;
	}
	
	protected function __destroyPagePrint() 
	{
		$this->set( 'use_pg_print', false );
		
		return $this;
	}
	
	protected function __enableSectionOption( $data ) 
	{
		$this->set( 'sod', $data );
		
		return $this;
	}
	
	protected function __destroySectionOption() 
	{
		$this->set( 'sod', NULL );
		
		return $this;
	}
	
	protected function __enableOption() 
	{
		$this->__enableScrumb();
		$this->__enableSelectedEdit();
		$this->__destroyKeyWord();
		$this->__defaultRowsNumberList();
		$this->__enablePagePrint();
		
		// Update option data.
		$this->__updateOptionData();
		
		$this->set( 'use_option', true );
		
		return $this;
	}
	
	protected function __destroyOption() 
	{
		$option_data = $this->__getOptionData();
		
		if( $option_data[ 'curr_area' ] == get_class( $this ) ) 
		{
			$this->__destroyOptionData();
		}
		
		return $this->set( 'use_option', false );
	}
	
	private function __destroyOptionData() 
	{
		Session::unregister( 'grid' );
		$this->__setOptionData( NULL );
		$this->__destroyScrumb();
		$this->__destroyPagePrint();
		$this->__destroyRowsNumberList();
		$this->__destroyKeyword();
		
		return $this;
	}
	
	private function __resetOptionData() 
	{
		$curr_area = get_class( $this );
		
		$this->__setOptionData( array( 
			'num_of_rows' 	=> $this->__getDefaultNumOfRows(),
			'curr_area'		=> $curr_area 
		));
		
		return $this->__updateOptionData();
	}
	
	private function __updateOptionData() 
	{
		$option_data = $this->__getOptionData();
		Session::set( 'grid', $option_data );
		if( !is_null( $option_data ) ) 
		{
			$this->set( 'num_of_rows', $option_data[ 'num_of_rows' ] );
		}
		
		return $this;
	} 
	
	final protected function __setVar( $args, $argsNum ) 
	{ 
		try 
		{
			if( 1==$argsNum ) 
			{
				dd($args);
				$mixed = current($args); 
				$mixed = each($mixed);
				$name = $mixed['key']; 
				$value = $mixed['value']; 
			} 
			else if( 1<$argsNum ) 
			{ 
				$name = $args[0]; 
				$value = $args[1];
			} 
			else 
			{ 
				throw new \Exception( "The functions of <b>Datagrid::Assign(), Datagrid::Set(), and Datagrid::Share()</b> must be has least one parameter." ); 
			}
			
			$this->_vars[ $name ] = $value; 
			return $this; 
		} 
		catch( \Exception $e ) 
		{ 
			abort( 500, $e->getMessage() );
		} 
	}
	
	protected function __setRowsNumberList( $list = NULL, $new = true ) 
	{
		try 
		{
			if( !is_array( $list ) && !is_null( $list ) ) 
			{
				throw new Exception( 'SetRowsNumberList method has not array value.' );
			}
		}
		catch( Exception $e ) 
		{
			echo $e->getMessage();
		}
		
		if( is_array( $list ) || is_null( $list ) ) 
		{
			$this->set( 'number_row_list', $list );
			if( is_array( $list ) && $new ) 
			{
				$option_data = $this->__getOptionData();
				if( !in_array( $option_data[ 'num_of_rows' ], $list ) ) 
				{
					$this->__changeNumberRows( $list[ 0 ] );
				}
			}
			if( is_null( $list ) ) 
			{
				return $this->__changeNumberRows( $this->__getDefaultNumOfRows() );
			}
		}
		return $this;
	}
	
	protected function __defaultRowsNumberList() 
	{
		return $this->__setRowsNumberList( array( 3, 5, 8 ), false );
	}
	
	protected function __destroyRowsNumberList() 
	{
		$this->set( 'num_of_rows', NULL );
		return $this->__setRowsNumberList();
	}
	
	protected function __changeNumberRows( $value ) 
	{
		$option_data = $this->__getOptionData();
		if( !is_null( $option_data ) ) 
		{
			$option_data[ 'num_of_rows' ] = $value;
			$this->__setOptionData( $option_data );
			return $this->__updateOptionData();
		}
		return $this;
	}
	
	protected function __getNumberRows() 
	{
		$option_data = $this->__getOptionData();
		
		if( !is_null( $option_data ) && isset( $option_data[ 'num_of_rows' ] ) ) 
		{
			return (int) $option_data[ 'num_of_rows' ];
		}
		return $this->__getDefaultNumOfRows();
	}
	
	protected function __enableScrumb() { return $this->set( 'use_scrumb', true ); }
	protected function __destroyScrumb() { return $this->set( 'use_scrumb', false ); }
	protected function __enableSelectedEdit() { return $this->set( 'selected_edit', true ); }
	protected function __destroySelectedEdit() { return $this->set( 'selected_edit', false ); }
	
	protected function __enableKeyword( $field, $label, $opera ) 
	{
		global $configs;
		
		$filter = array( 
			'field' => $field, 
			'label' => $label, 
			'opera' => $opera, 
		);
		
		$this->__setFilter( $filter );
		
		if( isset( $configs[ 'REQUEST_VARIABLES' ][ 'key' ] ) ) 
		{
			$keyword =  $configs[ 'REQUEST_VARIABLES' ][ 'key' ];
			$this->__setKeyword( $keyword );
		}
		
		return $this->set( 'key_search', true );
	}
	
	protected function __destroyKeyword() 
	{
		$this->__setFilter( NULL );
		
		return $this->set( 'key_search', false ); 
	}
	
	protected function __setChangeNumberRowsApi( $value ) 
	{
		return $this->set( 'change_number_rows_api', $value );
	}
	
	protected function __setScrumbEditApi( $value ) 
	{
		return $this->set( 'scrumb_edit_api', $value );
	}
	
	protected function __setScrumbDeleteApi( $value ) 
	{
		return $this->set( 'scrumb_delete_api', $value );
	}
	
	protected function __setResetOptionApi( $value ) 
	{
		return $this->set( 'reset_option_api', $value );
	}
	
	protected function __setLayout( $template ) { return $this->__setTemplate( $template ); }
	protected function __render( $template, $data ) 
	{
		$data_grid = $this->__getData();
		$columns = $this->__getColumnData();
		$printer = $this->__getPagePrinter();
		$length = $this->__getLength();
		$current_page = $this->__getCurrentPage();
		$total_pages = $this->__getTotalPages();
		$title = $this->__getTitle();
		$name = $this->__getName();
		$filter = $this->__getFilter();
		$keyword = $this->__getKeyword();
		$sort = $this->__getSort();
		$order = $this->__getOrder();
			
		if( !is_null( $template ) ) 
		{
			$this->setTemplate( $template );
		}
		
		$this->set( 'printer', $printer );				// Distributes page printer datas.
		$this->set( 'length', $length );				// Distributes the length value.
		$this->set( 'current_page', $current_page );	// Distributes the curr page value.
		$this->set( 'total_pages', $total_pages );		// Distributes the total pages value.
		$this->set( 'data', $data_grid );				// Distributes the data of grid.
		$this->set( 'columns', $columns );				// Distributes the column which grid has.
		$this->set( 'title', $title );					// Distributes the title.
		$this->set( 'key_filter', $filter );			// Distributes the key search filter.
		$this->set( 'keyword', $keyword );				// Distributes the keyword.
		$this->set( 'name', $name );					// Distributes the name.
		$this->set( 'sort', $sort );					// Distributes the sort, default null.
		$this->set( 'order', $order );					// Distributes the order, default null, if any column has sort, and in current order request, it returns it's name value.
		return $this->__renderLayout( $data );
	}
	
	protected function __renderLayout( $args = NULL ) 
	{
		global $configs;
		global $html, $file;
		
		$vars = $this->__getVars();
		$template = $this->__getTemplate();
		
		if( !is_null( $vars ) ) 
		{
			extract( $vars );
		}
		
		if( !is_null( $args ) && is_array( $args ) ) 
		{
			extract( $args );
		}
		
		if( is_null( $template ) ) 
		{
			$path = WIDGET_DIR . __correctPath( str_replace( 'zuuda\\', '', strtolower( __CLASS__ ) ) ) . DS . 'template' . '.tpl'; 
		}
		else 
		{
			$path = WIDGET_DIR . $template;
		}
		
		include( $path );
		
		return $this;
	}
}