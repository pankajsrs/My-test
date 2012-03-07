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
 * Name:     decode_text
 * Purpose:  Decode the encoded special characters text
 * ---------------------------------- 
 */
 function smarty_modifier_decode_text($str)
 {
	$str = str_replace("<-pls->", "+", $str);
	$str = str_replace("<-star->", "*", $str);
	$str = str_replace("<-tild->", "~",  $str);
	$str = str_replace("<-crt->", "^",  $str);

	return $str;
 }