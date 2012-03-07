<?php

/**
 ** @Package - Functions
 ** paging, dynamic drop down
 ** @Date - 20/08/2008
 **/

/**
 ** @ Method to create the paging
 ** @ Param - query string to pass eg (search.php?action=x&actio1=y), count of result,
 ** start, end, page
 ** (default will be 1), slab_start (slab_start=5 output will be <<prev 12345 next>>)
 **/
$secretPass = '/HKz(x[Sk7M-jJO;wgc0yV_$<znkY&|^!8Jveokuio5gDjky,5VOSd-?[Im|+tGj';

function create_paging($query_str, $count_of_result, $start, $end, $page, $slab_start)
{
	global $MAXRESULT;
	$numpages = 0;
	$SLABSIZE = 5;
	$prevlink = "<< ".tr('button','previous')."&nbsp;&nbsp;";
	$nextlink = "&nbsp;&nbsp;".tr('button','next')." >>";
	$pagelist = "";


	if(strpos($query_str,'?')>0){
		$and_or = "&";
	}
	else
	{
		$and_or = "?";
	}

	// find total number of  pages
	if ($count_of_result>0)
	{
		$numpages = ceil($count_of_result / $MAXRESULT);
	}
	if($numpages < 2) {
		$paging = "";
	}
	else
	{

		$pageslab = (floor(($page-1)/$SLABSIZE)+1)*$SLABSIZE;
		//$count_of_result = $SLABSIZE;

		#pageslab links formation
		for($i=$slab_start; ($i<=$pageslab && $i<=$numpages); $i++) {
			if($i == $page) {
				$pagelist = $pagelist."&nbsp;".$i."&nbsp;";
			}
			else {
				$pagelink =$query_str.$and_or."start=".(($i-1)*$MAXRESULT)."&end=".($i*$MAXRESULT)."&page=".$i."&slab_start=".$slab_start;
				$pagelist = $pagelist."&nbsp;<a href=".$pagelink.">".$i."</a>&nbsp;";
			}
		}
			
		#prevous link
		$txtslab_start = "&slab_start=".$slab_start;
		if($start != 0) {
			$txtpage = "&page=".($page-1);
			if($page == ($count_of_result-$SLABSIZE+1)) {
				$txtslab_start = "&slab_start=".($slab_start-$SLABSIZE);
			}
			$prevlink = $query_str.$and_or."start=".($start-$MAXRESULT)."&end=".$start.$txtpage.$txtslab_start;
			$prevlink = "<a href='".$prevlink."'><< ".tr('button','previous')."</a>&nbsp;&nbsp;";
		}
			
		#next link
		$txtslab_start = "&slab_start=".$slab_start;
		if($start+$MAXRESULT < $count_of_result) {
			$txtpage = "&page=".($page+1);
			if($page == $count_of_result) {
				$txtslab_start = "&slab_start=".($slab_start+$SLABSIZE);
			}
			$nextlink = $query_str.$and_or."start=".$end."&end=".($end+$MAXRESULT).$txtpage.$txtslab_start;
			$nextlink = "&nbsp;&nbsp;<a href='".$nextlink."'>".tr('button','next').">></a>";
		}
			
		#paging formation
			
		$paging = $prevlink.$pagelist.$nextlink;
	}

	return $paging;
}

/**
 ** @ Method to create the ajax paging
 ** @ Param - query string to pass eg (search.php?action=x&actio1=y), count of result,
 ** start, end, page
 ** (default will be 1), slab_start (slab_start=5 output will be <<prev 12345 next>>)
 **/
function create_ajax_paging($query_str, $count_of_result, $start, $end, $page, $slab_start)
{
	 
	 
	global $MAXRESULT;
	$numpages = 0;
	$SLABSIZE = 5;
	$prevlink = tr('button','previous')."&nbsp;&nbsp;";
	$nextlink = "&nbsp;&nbsp;".tr('button','next');
	$pagelist = "";


	if(strpos($query_str,'?')>0){
		$and_or = "&";
	}
	else
	{
		$and_or = "?";
	}


	// find total number of  pages
	if ($count_of_result>0)
	{
		$numpages = ceil($count_of_result / $MAXRESULT);
	}
	if($numpages < 2) {
		$paging = "";
	}
	else
	{
		$pageslab = (floor(($page-1)/$SLABSIZE)+1)*$SLABSIZE;
		//$count_of_result = $SLABSIZE;
		$slab_no = (floor($page/$SLABSIZE));
		#pageslab links formation
		for($i=$slab_start; ($i<=$pageslab && $i<=$numpages); $i++) {
			if($i == $page) {
				$pagelist = $pagelist."&nbsp;".$i."&nbsp;";
			}
			else {
				$pagelink =$query_str.$and_or."start=".(($i-1)*$MAXRESULT)."&end=".($i*$MAXRESULT)."&page=".$i."&slab_start=".$slab_start;
				$pagelist = $pagelist.'&nbsp;<a onclick="tck_pass_request( \''.$pagelink.'\')" style="cursor:pointer;">'.$i.'</a>&nbsp;';
			}
		}

		#previous link
		$txtslab_start = "&slab_start=".$slab_start;
		if($start != 0) {
			$txtpage = "&page=".($page-1);
			//	if($page == ($count_of_result-$SLABSIZE+1)) {
			if($page == $slab_start) {
				$txtslab_start = "&slab_start=".($slab_start-$SLABSIZE);
			}
			$prevlink = $query_str.$and_or."start=".($start-$MAXRESULT)."&end=".$start.$txtpage.$txtslab_start;
			$prevlink = '<a onclick="tck_pass_request(\''.$prevlink.'\')" style="cursor:pointer;">'.tr("button","previous").'</a>&nbsp;&nbsp;';
		}
			
		#next link
		$txtslab_start = "&slab_start=".$slab_start;
		if($start+$MAXRESULT < $count_of_result) {
			$txtpage = "&page=".($page+1);
			//if($page == $count_of_result) {
			if($page == $slab_no*$SLABSIZE) {
				$txtslab_start = "&slab_start=".($slab_start+$SLABSIZE);
			}
			$nextlink = $query_str.$and_or."start=".$end."&end=".($end+$MAXRESULT).$txtpage.$txtslab_start;
			$nextlink = '&nbsp;&nbsp;<a  onclick="tck_pass_request(\''.$nextlink.'\')" style="cursor:pointer;">'.tr("button","next").'</a>';
		}
			
		#paging formation
			
		$paging = $prevlink.$pagelist.$nextlink;
	}

	return $paging;
}

# get the currently running script name
function get_script_name()
{

	$running_script = '';

	# get the full path of running script
	$script_name = $_SERVER['SCRIPT_FILENAME'];

	$array_vars = split("/", $script_name);
	# get the count
	$num_element = count($array_vars);

	if ($num_element > 0)
	{
		$running_script =  trim($array_vars[$num_element-1]);
	}
	return $running_script;
}

#function to run curl script
function curlScript ($runfile,$method=0,$param=null)
{
	//echo "<br><b>Running Script : ".$runfile. "</b><br>";
	// INIT CURL
	$ch = curl_init();

	// SET URL FOR THE POST FORM LOGIN
	curl_setopt($ch, CURLOPT_URL, $runfile);

	// ENABLE HTTP POST
	curl_setopt ($ch, CURLOPT_POST, $method);

	if($method==1)
	{
		// SET POST PARAMETERS : FORM VALUES FOR EACH FIELD
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $params);
	}
	// IMITATE CLASSIC BROWSER'S BEHAVIOUR : HANDLE COOKIES
	curl_setopt ($ch, CURLOPT_COOKIEJAR, 'cookie.txt');

	# Setting CURLOPT_RETURNTRANSFER variable to 1 will force cURL
	# not to print out the results of its query.
	# Instead, it will return the results as a string return value
	# from curl_exec() instead of the usual true/false.
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

	// EXECUTE 1st REQUEST (FORM LOGIN)
	//$store = curl_exec ($ch);

	//print $store;
	// SET FILE TO DOWNLOAD
	curl_setopt($ch, CURLOPT_URL,$runfile);

	// EXECUTE 2nd REQUEST (FILE DOWNLOAD)
	$content = curl_exec ($ch);
		
	// CLOSE CURL
	curl_close ($ch);
	return $content;
}

# generate a random number
function generate_random($total_char = 5)
{
	$salt = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789";

	srand((double)microtime()*1000000);

	$string="";

	for ($i=0;$i<$total_char;$i++)
	{
		$string = $string . substr ($salt, rand() % strlen($salt), 1);

	}
	return $string;
}



function encode_text($str)
{
	$str = str_replace("+", "<-pls->", $str);
	$str = str_replace("*", "<-star->", $str);
	$str = str_replace("~", "<-tild->", $str);
	$str = str_replace("^", "<-crt->", $str);
	return urlencode($str);
}

 

function decode_text($request)
{
	foreach($request as $key => $value)
	{
		if(!isset($request[$key]))
			continue;
		if(!is_array($request[$key]))
		{
			$request[$key] = html_entity_decode(trim(urldecode($request[$key])), ENT_QUOTES);
		}
	}
	return $request;
}

 
/**
 ** Check if Request comes from AJAX or simple page load.
 ** @Param - Void
 ** @Return type - True/False
 **/

function is_ajax()
{

	return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
			($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));

}



/**
 ** @ Method to create breadcrumb.
 ** @ If request comes from left menu and if page is not home
 ** @ set its position to 2 other wise place it in last of array.
 ** @ The first position is always reserved for [home].
 ** @ Param - page_title (translated), page_name [for ref like home],
 ** @ link, leftmenu
 ** pass_request
 **/
function create_breadcrumb($request, $page_title, $index='0')
{
	$page_name = isset($request['pagename'])?trim($request['pagename']):'';
	$left_menu = isset($request['left_menu'])?trim($request['left_menu']):'N';
	$link = build_link($request);
	$index = exceptional_index($page_title, $index);
	$page_title = get_page_title($page_title);

	$bread_crumb = Array (
			'page_title' => $page_title,
			'page_name' => $page_name,
			'link' => $link,
	);

	if($index == '0')
	{
		unset($_SESSION['BREADCRUMB']);
		$index = 0;
	}
	else
	{
		if(isset($_SESSION['BREADCRUMB']))
		{
			for($i=0;$i<count($_SESSION['BREADCRUMB']); $i++)
			{
				if($i>=$index)
				{

					array_pop($_SESSION['BREADCRUMB']);
				}
			}
		}
	}

	$_SESSION['BREADCRUMB'][$index] = $bread_crumb;
}
/**
 ** @ Returns the breadcrumb stored in session.
 **/
function parse_bread_crumb()
{
	if(!isset($_SESSION['BREADCRUMB'])) return "";

	$length = count($_SESSION['BREADCRUMB']);
	$str = "";
	for($i=0;$i<$length;$i++)
	{
		if(isset($_SESSION['BREADCRUMB'][$i]))
		{
			$temp = $_SESSION['BREADCRUMB'][$i];
			$page_title = $temp['page_title'];
			$page_name = $temp['page_name'];
			$link = $temp['link'];
			$link_text = '';
			if($i>0)
			{
				$link_text = " / ";
			}
			//$str .= $link_text."<a style='cursor:pointer;' onclick=pass_request('controller.php?".$link."')>".$page_title."</a>";
			$dir = get_controller_name();
			$str .= $link_text.'<a  onclick="tck_pass_request( \''.$dir.$link.'\')" style="cursor:pointer;" >'.$page_title.'</a>';
		}
	}
	return $str;
}

function build_link($request)
{

	if(count($request)>0)
	{
		$notreq_arr = Array('undo_alert_msg', 'err', 'show_msg', 'request_path');
		$str = '';
		$i=0;
		foreach($request as $key=>$value)
		{
			if(in_array($key, $notreq_arr))
				continue;

			$seprator = '';
			if($i>0)
			{
				$seprator = "&";
			}
			if(is_array($value))
			{
				foreach($value as $key1=>$value1)
				{
					if($value1 != '' && !is_array($value1))
					{
						$value1 = urlencode($value1);
						$str .= '&'.$key.'[]'."=".$value1;
					}
				}
				continue;
			}
			if($value != '' && !is_array($value))
				$value = urlencode($value);

			$str .= $seprator.$key."=".$value;
			$i++;
		}
	}
	return $str;
}


function get_page_title($page_name)
{
	$page_title = '';
	switch($page_name)
	{
		case 'admin_dashboard':
		case 'partner_dashboard':
		case 'customer_dashboard':
			$page_title = tr('breadcrumb', 'home');
			break;

		default:
			$page_title = tr('breadcrumb', $page_name);
		break;


	}
	return $page_title;
}

function exceptional_index($page_title, $index)
{
	if($page_title == 'customer_detail' && $index == 2)
	{
		if(isset($_SESSION['BREADCRUMB']['2']['page_name'])
				&& $_SESSION['BREADCRUMB']['2']['page_name'] == 'list_subscription'	)
			$index = '3';
	}
	return $index;
}

function write_log($content)
{
	$file_name = LIBRARY_DIR."/log/log.txt";

	$error_log = "----------------------------------------------------\n";
	$error_log .= date('Y-m-d H:i:s')."\n";
	$error_log .= $content."\n";
	$error_log .= "----------------------------------------------------\n";


	if($handle = @fopen($file_name, 'a'))
	{
		@fwrite($handle,$error_log);
		@fclose($handle);
	}

}
 
function validate_ip_address($ip_address)
{
	if(preg_match("^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}^", $ip_address))
	{
		return true;
	}
	else
	{
		return false;
	}
}
 


function getParameter($value, $default='')
{
	if(isset($_REQUEST[$value]))
	{
		return html_entity_decode(urldecode(trim($_REQUEST[$value])), ENT_QUOTES);
	}
	return trim($default);
}

	

function get_ip_address()
{
	if (getenv('HTTP_X_FORWARDED_FOR')) {
		$ip_address = getenv('HTTP_X_FORWARDED_FOR');
	} else {
		$ip_address = getenv('REMOTE_ADDR');
	}
	return $ip_address;
}

function tr($group, $key)
{
	global $TRANSLATION;

	if(!empty($TRANSLATION) && !array_key_exists($group, $TRANSLATION))
	{
		# error indication
		return "<font color='red'>tr('".$group."', ".$key."')</font>";
	}
	else if( !empty($TRANSLATION) && !array_key_exists($key, $TRANSLATION[$group]))
	{
		# error indication
		return "<font color='red'>tr('".$group."', ".$key."')</font>";
	}
	else
	{
		return html_entity_decode($TRANSLATION[$group][$key]);
	}
}

function splitArrayIntoString($arr)
{
	if(count($arr) == 0)
		return '';

	foreach($arr as $key => $value)
		$arr[$key] = "'".$value."'";
		
	$string = implode(", ", $arr);

	return $string;
}

function replace_br_line($text)
{
	$text =  html_entity_decode($text, ENT_QUOTES);
	$text = str_ireplace("<br />", "\n", $text);
	$text = str_ireplace("<br>", "\n", $text);
	$text = str_ireplace("<br/>", "\n", $text);
	return trim(strip_tags($text));
}
	
function validate_domain_name($domain_name)
{
	if(!preg_match ("/^[a-z0-9][a-z0-9\-]+[a-z0-9](\.[a-z]{2,4})+$/i", $domain_name))
	{
		return false;
	}
	return true;
}

	
function getURL($url)
{
	return get_secure_url().$url;
}
function get_secure_url()
{
	$base_url = BASE_URL;
	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on')
		$base_url = BASE_URL_SECURE;
		
	return $base_url;
}
 
 
 
#encrypt url
function Encode($data,$pwd) 
{ 
    $pwd_length = strlen($pwd); 
    for ($i = 0; $i < 255; $i++) { 
        $key[$i] = ord(substr($pwd, ($i % $pwd_length)+1, 1)); 
        $counter[$i] = $i; 
    } 
    for ($i = 0; $i < 255; $i++) { 
        $x = ($x + $counter[$i] + $key[$i]) % 256; 
        $temp_swap = $counter[$i]; 
        $counter[$i] = $counter[$x]; 
        $counter[$x] = $temp_swap; 
    } 
    for ($i = 0; $i < strlen($data); $i++) { 
        $a = ($a + 1) % 256; 
        $j = ($j + $counter[$a]) % 256; 
        $temp = $counter[$a]; 
        $counter[$a] = $counter[$j]; 
        $counter[$j] = $temp; 
        $k = $counter[(($counter[$a] + $counter[$j]) % 256)]; 
        $Zcipher = ord(substr($data, $i, 1)) ^ $k; 
        $Zcrypt .= chr($Zcipher); 
    } 
    return $Zcrypt; 
} 

function hex2bin($hexdata) { 
    for ($i=0;$i< strlen($hexdata);$i+=2) { 
        $bindata.=chr( hexdec( substr($hexdata,$i,2))); 
    }   
    return $bindata; 
} 
/*** end Custom Encode/decode functions***/

// Custom encode Decode
	
	
function encodeString($encodeText)
{
	global $secretPass;
	$encoded = bin2hex(Encode($encodeText,$secretPass));
	return $encoded;
}


function decodeString($decodeText)
{
	global $secretPass;
	$decoded = Encode(hex2bin($decodeText),$secretPass); 
	return $decoded;
}

	