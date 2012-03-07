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
function smarty_modifier_mainorderstatus($order_id)
{ 
	global $obj_order;
	echo $obj_order->get_main_order_status($order_id);
}

?>