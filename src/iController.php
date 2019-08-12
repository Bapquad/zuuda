<?php 
namespace Zuuda;

interface iController 
{
	public function rootName();
	public function CheckMass( $query );
	public function BeforeAction( $query );
	public function AfterAction( $query );
}