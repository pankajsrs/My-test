<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty status modifier plugin
 *
 * Type:     modifier<br>
 * Name:     status<br>
 * Purpose:  change status
 * @link http://smarty.php.net/manual/en/language.modifier.wordwrap.php
 *          wordwrap (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @param integer
 * @param string
 * @param boolean
 * @return string
 */
function smarty_modifier_tr($val1, $val2, $key = 'common')
{
	echo tr($key, $val2);
}

?>
