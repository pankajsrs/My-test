<?php

include_once('application.php');

class Controller{
	public  $controller = '';
	public  $action = '';

	function Controller($controller, $action){
			global $smarty;
				
			$this->controller = $controller;
			$this->action = $action;
	
			$smarty->assign('site_title', ucwords($this->controller));
			 
			if (!file_exists(CONTROLLER_DIR.$this->controller.'_controller.php')) 
				die('Controller not avaialble as '.$this->controller.'_controller.php');
					
			if (!file_exists(MODEL_DIR.$this->controller.'_model.php'))
				die('Model not avaialble as  '.$this->controller.'_model.php');
		
		 	$controller_name = ucfirst($this->controller);
		 	 
		 	require_once(CONTROLLER_DIR.$this->controller.'_controller.php');
		 	require_once(MODEL_DIR.$this->controller.'_model.php');	

		 	ob_start();		 	
		 	$obj_cnt = new $controller_name();		 	
		 	$contents = ob_get_contents();
		 	ob_end_clean();
		 	
		 	$layout = isset($_SESSION['layout']) && !empty($_SESSION['layout'])?$_SESSION['layout']:'default';
		 	if (!file_exists(TEMPLATE."/layout/".$layout.'.html')) 
				die('Layout not avaialble as '.$layout.'.html');
			
		 	if(isset($_SESSION['layout'])) unset($_SESSION['layout']);
		 	$smarty->assign('layout_content', $contents);
		 	$smarty->display(TEMPLATE."/layout/".$layout.'.html');
		 	
	}
}