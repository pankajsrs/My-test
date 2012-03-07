<?php
 
	define('BASE_DIR', 'Z:/freedompop/');
	define('BASE_URL', 'http://localhost/freedompop/');
	define('SMARTY_DIR', 'Z:/freedompop/Smarty/libs/');
	# Database details	
	define('DB_HOST_NAME', 'localhost');
	define('DB_USER_NAME', 'root');
	define('DB_PASSWORD' , '');
	define('DB_NAME', 'freedompop');
	define('DEBUG', '0');
	 
	# define all global variables, constants
	define('WEBSITE_NAME', 'Freedom Pop');
	define('LIBRARY_DIR', BASE_DIR.'library/');
	define('CONTENT_DIR', LIBRARY_DIR.'content/');
	define('PUBLIC_DIR', BASE_DIR.'public/');
	define('PUBLIC_URL', BASE_URL.'public/');
	define('CLASS_DIR', LIBRARY_DIR.'class/');
	define('CONFIG_DIR', LIBRARY_DIR.'config/');
	define('FUNCTION_DIR', LIBRARY_DIR.'function/');
	define('JS_DIR', PUBLIC_DIR.'js/');
	define('CSS_URL', PUBLIC_URL.'css/');
	define('TEMPLATE', BASE_DIR.'templates/');
	define('TEMPLATE_C', BASE_DIR.'templates_c/');
	define('CATCHE', BASE_DIR.'cache/');
	define('IMAGE_PATH', PUBLIC_URL.'images/');
	
	define('IMPROVESYS_DIR', LIBRARY_DIR.'contacts/');
	
	define('MODEL_DIR', BASE_DIR.'models/');
	define('CONTROLLER_DIR', BASE_DIR.'controllers/');	
	define('ADMIN_MAIL', 'support@freedompop.com'); 

	$CONTACT_SERVICES = Array('gmail', "yahoo", "aol", "msn", "hotmail", "googlemail", "live", "paracalls", 
		'fastmail', 'web', 'gmx', 'maildotcom', 'mailru', 'rediffmail', 'indiatimes', 'lycos', 'libero', 'linkedin', 
		'rambler', 'mac', 'mynet', 'interia', 'yandex', '126', 'qq', 'daum', 'sina', '163', 'wp', 'in', 'ymail', 
		'rocketmail', 'plaxo', 'me');
