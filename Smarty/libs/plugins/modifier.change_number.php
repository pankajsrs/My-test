<?php
/*
 * Smarty plugin
 * ----------------------------------
 * Type:     function
 * Name:     html_select_date
 * Purpose:  change number format
 * {$number|change_number_format:$format}
 * ---------------------------------- 
 */
function smarty_modifier_change_number($number, $num_format)
{
	
  @list($decimal_separator, $thousand_separator) = split('==', $num_format);
  if(empty($decimal_separator))
 		$decimal_separator='.';

  if(empty($thousand_separator))
 		$thousand_separator=' ';
  
  $number = number_format(round($number, 2), 2, $decimal_separator,$thousand_separator);
  return $number;
}