<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
/*
 * Smarty plugin
 * ----------------------------------
 * Type:     modifie
 * Name:     isset
 * Purpose:  Decode the encoded special characters text
 * ---------------------------------- 
 */
 function smarty_modifier_isset($str)
 {
	if(isset($str)) return true;
	else return false;
 }