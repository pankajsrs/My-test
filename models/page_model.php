<?php

	/**
	* @ 
	* @Date -  28/09/2010
	**/
	
	class PageModel
	{
		var $QueryTool; // to operate extended properties
		 
		
		# constructor of class
		function PageModel()
		{
			$this->QueryTool = new PageModelDataBasic(DB_NAME);
		}
		
	 
		
	}# End of class
	

	# extends the methods of data basic
	class PageModelDataBasic extends dataBasic
	{
		// set primary table
		var $tableName  = "";
		var $primaryCol = "";
	}