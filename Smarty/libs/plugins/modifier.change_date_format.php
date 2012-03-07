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
function smarty_modifier_change_date_format($current_date, $site_format)
{	
	if(empty($current_date) || $current_date < 0 || $current_date == '0000-00-00 00:00:00')
	{
		return '';
	}
	
	if(empty($site_format))
	  $site_format = 'dd.mm.yy';
	  
	$date_sep = '/';
	if(strpos($site_format, '.') !== false)
		 $date_sep = '.';
	else if(strpos($site_format, '-') !== false)
		$date_sep = '-';	

	$date = $current_date;
	$str_current_date = strtotime($current_date);
	if(empty($str_current_date) || $current_date== '0000-00-00' || date('Y-m-d', $str_current_date) == '0000-00-00' )
	{
		return '';//'00'.$date_sep.'00'.$date_sep.'00';
	}

 	$current_date = date('Y-m-d', $str_current_date);
	
	# split format by /

 	$format = split('[/.-]', $site_format);

	$split_current_date = split('[-]', $current_date);
	$format_of_date = Array();
	for($i=0;$i<count($format);$i++)
	{
		if($format[$i]=='dd')
			$format_of_date[$i]= 'd';
		else if($format[$i]=='mm')
			$format_of_date[$i]= 'm';
		else if($format[$i]=='yy')
			$format_of_date[$i]= 'y';
		else if($format[$i]=='yyyy')
			$format_of_date[$i]= 'Y';
	}
	if(count($format_of_date) >0)
	{
		$actual_date = $format_of_date[0].$date_sep.$format_of_date[1].$date_sep.$format_of_date[2];
		$date = date($actual_date, strtotime($current_date));
	}
	return $date;
}