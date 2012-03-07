<?php
/*
 * Smarty plugin
 * ----------------------------------
 * Type:     function
 * Name:     format_contact_number
 * Purpose:  change contact number format
 * {$number|change_number_format:$format}
 * Date : 04/01/2012 by Mohit
 * ---------------------------------- 
 */
function smarty_modifier_format_contact_number($contact_number, $child_country='Norway', $parent_country='Norway')
{
	if(is_null($contact_number) || empty($contact_number))	return '';
 
	switch($child_country)
	{
		case '':
		case 'Norway':
			$number = get_norway_formated_number($contact_number, $parent_country);
			break;
		
		case 'India':
			$number = get_india_formated_number($contact_number, $parent_country);
			break;

		default:
			$number = get_other_formated_number($contact_number, $child_country, $parent_country);
			break;
	}
	if(empty($number))
		$number = $contact_number;

	return $number;
}
 