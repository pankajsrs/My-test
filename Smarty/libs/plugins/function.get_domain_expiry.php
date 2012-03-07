<?php
/*
 * Smarty plugin
 * ----------------------------------
 * Type:     function
 * Name:     get domain expiry
 * Purpose:  check the whois to get the domain expiry
 * 
 * ---------------------------------- 
 */
function smarty_function_get_domain_expiry($domain_name)
{
 	
	if(empty($domain)) return false;
	
	$whois = new SamsWhois();
	$server_name = $whois->whatserver($domain);
	if(strtolower($server_name) == 'finger.norid.no') {
	$server_name = 'whois.norid.no';
	}
	$port  = 43;
	$data = '';

	$fp = @fsockopen($server_name, $port,$errno, $errstr, 5);
	if( $fp )
	{
		@fputs($fp, $domain."\r\n");
		@socket_set_timeout($fp, 30);
		while( !@feof($fp) ){
			$data .= @fread($fp, 4096);
		}
		@fclose($fp);
		
	}

	if(!empty($data))
	{
		
		if($server_name != 'whois.norid.no')
		{
			// get expiration date
			preg_match('/[e|E]xpir.*?\n/', $data, $matches);
			$expiry = $matches[0];
			if(empty($expiry)) return false;
			$dates = split(":", $expiry);
			if(!isset($dates[1])) return false;
			$dates = split(" ", trim($dates[1]));
			$exp = strtotime(trim($dates[0]), time());
			return $exp = strftime("%Y-%m-%d", $exp);
		}
		else
		{
			// get expiration date
			preg_match('/[c|C]reat.*?\n/', $data, $matches);
			echo $expiry = $matches[0];
			if(empty($expiry)) return false;
			$dates = split(":", $expiry);
			if(!isset($dates[1])) return false;
			$dates = split(" ", trim($dates[1]));
			$exp = strtotime(trim($dates[0]), time());
			$exp = strftime("%Y-%m-%d", $exp);

			$cur_year = date('Y');
			$cur_month = date('m');
			list($y, $m, $d) = split("-", $exp);
			if($cur_month>=$m)
			{
				$exp = date('Y-m-d', @mktime(0,0,0,$m, $d,$cur_year+1 ));
			}
			else 
			{
				$exp = date('Y-m-d', @mktime(0,0,0,$m, $d, $cur_year ));
			}
			return $exp;
		}
	 
	}
	else
	{
		return false;
	}
}