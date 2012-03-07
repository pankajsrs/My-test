<?php

	/* @Package : Smarty lib
	*  @Description : Load basic path to smarty variables.
	*/

	include_once(SMARTY_DIR.'Smarty.class.php');

	class SmartyConnect extends Smarty
	{
	   function SmartyConnect()
	   {
		  // Class Constructor.
		  // These automatically get set with each new instance.
		  $this->Smarty();
		  $this->template_dir = TEMPLATE;
		  $this->compile_dir = TEMPLATE_C;
 		  $this->caching = false;		  
	   }
	}
	
?>