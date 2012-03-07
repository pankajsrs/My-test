<?php
	error_reporting(E_ALL & ~E_DEPRECATED);
	ini_set('display_errors', 1);
	
	session_set_cookie_params(8*60*60);
 	ini_set('memory_limit','64M');
	ini_set("session.save_handler","files");
	ini_set("session.hash_bits_per_character","4");
	ini_set("session.gc_probability","1");
	ini_set("session.gc_divisor","100");
	ini_set("session.bug_compat_42","On");
	ini_set("session.cookie_httponly","");
	ini_set('session.gc_maxlifetime', '28800'); 
	//For persistent login
	ini_set('session.save_path',BASE_DIR."tmp/");
	session_start();
	
	if(ob_get_length()) ob_clean();
	header('Expires : Mon, 26 Jul 2006 05:00:00 GMT');
	header('Last Modified :'.gmdate("D, d M y H:i:s").'GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: no-cache');
	header('Content-type: text/html; charset="ISO-8859-1"',true);
	header("Content-Transfer-Encoding: ISO-8859-1");
	
	
	
	try
	{
		global $type;
		# validate the url for any type of hack.
		validateUrl();
	

		# load common classes which will be used through out the project
		include_once(CLASS_DIR."data_manager.class.php");
		include_once(CLASS_DIR."data_basic.class.php");
		include_once(CLASS_DIR.'smconnect.class.php');
		include_once(FUNCTION_DIR.'common_functions.php');
		
		/**
		* create the object of datamanager. It will invoke the constructor
		* which is reponsible for db connection.
		**/
		$obj_manager = new DataManager(DB_NAME);
		$obj_basic = new dataBasic;
		 # create the object of smarty.
		$smarty = new SmartyConnect;
		 
		$user_data = isset($_SESSION['USER_DATA'])?$_SESSION['USER_DATA']:'';
	 	
		if(isset($_SESSION['ERROR']))
		{
			$smarty->assign('flashMessage', Array('class' => $_SESSION['ERROR']['class'], 'message' => $_SESSION['ERROR']['message']));
			unset($_SESSION['ERROR']);
		}
		
		$smarty->assign('user_data', $user_data);
		$smarty->assign('image_url', IMAGE_PATH);
		$smarty->assign('template_path', TEMPLATE);
		
		$smarty->assign('public_url', PUBLIC_URL);
		$smarty->assign('WEBSITE_NAME',WEBSITE_NAME);
		$smarty->assign('BASE_URL',BASE_URL); 
	}
	catch (Exception $e)
	{
		throw new Exception($e);
	}

	# Validate the url for any type of hack.
	function validateUrl()
	{
	
		if(isset($_REQUEST["includedir"]))
		{
			echo "Wrong Url";
			exit;
		}

		$qeryString = $_SERVER['QUERY_STRING'];
		if(strstr(strip_tags($qeryString), "includedir"))
		{
			echo "Wrong Url";
			exit;
		}

		if( preg_match(
		'/^(http|https|ftp|script|SELECT|UNION|UPDATE|AND|exe|exec|INSERT|tmp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,6}'
		.'((:[0-9]{1,5})?\/.*)?$/i' ,$qeryString))
		{
			echo "Wrong Url";
			exit;
		}
		
		foreach($_REQUEST as $key=>$value)
		{
			if(!is_array($value))
			{
				$_REQUEST[$key] = htmlentities(stripslashes(urldecode($value)), ENT_QUOTES);
			}
			else
			{
				foreach($value as $k=>$v)
				{
					if(!is_array($v))
					{
					$_REQUEST[$key][$k] = htmlentities(stripslashes(urldecode($v)), ENT_QUOTES);
					}
				}
			}
		}		
	}
	 