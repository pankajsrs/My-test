<?php
/*
 * Smarty plugin
 * ----------------------------------
 * Type:     function
 * Name:     html_select_date
 * Purpose:  change date format
 * {$date|change_date_format:$site_date_format}
 * ---------------------------------- 
 */
function smarty_modifier_redcolor($amount)
{
	if(round($amount, 0) <0)
	{
		$amount = '<font color="red">'.$amount."</font>";
	}
	return $amount;
}