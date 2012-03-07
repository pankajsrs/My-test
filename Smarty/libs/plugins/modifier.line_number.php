
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
function smarty_modifier_line_number($phone_number)
{	
	 echo splitPhoneNumber($phone_number);
}