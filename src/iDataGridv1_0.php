<?php 
namespace Zuuda;

interface iDataGridv1_0 
{
	/**
	 *	Manupopulate with columns.
	 *  Abstract:
	 *	1. AddColumn.
	*/
	public function BuildColumns();
	public function AddColumn( DataColumn $column );
	
	/**
	 *	Deleting a row.
	*/
	public function DeleteRow( $id );
	
	// public function EmptyData();
	public function Length();
	public function SetLength( $value );
	public function TotalPages();
	public function SetTotalPages( $value );
	public function SetCurrentPage( $value );
	public function GetCurrentPage();
	public function SetPagePrinter( $printer );
	public function GetPagePrinter();
	public function EnablePagePrint();
	public function DestroyPagePrint();

	public function EnableSectionOption( $data );
	public function DestroySectionOption();
	
	public function EnableOption();
	public function DestroyOption();
	
	public function EnableScrumb();
	public function DestroyScrumb();
	
	public function ChangeNumberRows( $value );
	public function GetNumberRows();
	public function SetRowsNumberList( $list );
	public function DestroyRowsNumberList();
	public function DefaultRowsNumberList();
	public function SetDefaultNumOfRows( $value );
	public function ResetOptionData();
	
	public function EnableKeyword( $field, $label, $opera );
	public function DestroyKeyword();
	public function SetKeyword( $value );
	public function GetPaging();
	public function SetPaging( $value = true );
	public function EnablePaging();
	
	public function SetChangeNumberRowsApi( $value );
	public function SetScrumbEditApi( $value );
	public function SetScrumbDeleteApi( $value );
	public function SetResetOptionApi( $value );
}