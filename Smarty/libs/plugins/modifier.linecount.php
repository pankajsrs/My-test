<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty wordwrap modifier plugin
 *
 * Type:     modifier<br>
 * Name:     wordwrap<br>
 * Purpose:  wrap a string of text at a given length
 * @link http://smarty.php.net/manual/en/language.modifier.wordwrap.php
 *          wordwrap (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @param integer
 * @param string
 * @param boolean
 * @return string
 */
function smarty_modifier_linecount($string, $length="150")
{
    $lines = 0;
	$chara  = strlen($string);
	if($chara > 0) 
	{
		$lines = $chara/$length;
	}
	$lines =  ceil($lines);

	# Check the occurance of br
	$lines += substr_count(strtolower($string), "<br>");
	$lines += substr_count(strtolower($string), "<br />");
	return $lines;
}

?>
