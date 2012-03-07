<?php

	/**
	* @ 
	* @Date -  28/09/2010
	**/
	
	class IndexModel
	{
		var $QueryTool; // to operate extended properties
		 
		
		# constructor of class
		function IndexModel()
		{
			$this->QueryTool = new IndexModelDataBasic(DB_NAME);
		}
		
			 
		function isEmailExist($email)
		{
			$query = " SELECT signup_id FROM signup WHERE email ='$email'   ";
			$query_data = $this->QueryTool->get_query_data($query);
			if(!empty($query_data))
			{
				return true;
			}
			
			return false;
		}
		
		function get_signup($email)
		{
			$query = " SELECT * FROM signup WHERE email ='$email'   ";
			$query_data = $this->QueryTool->get_query_data($query);
			
			if(!empty($query_data))
			{
				return $query_data;
				
			}
			
			return false;
		}
		
		
		function loginUser($email, $password)
		{
			$query = " SELECT * FROM signup WHERE email ='".mysql_escape_string($email)."'  AND password ='".mysql_escape_string($password)."'";
			
			$query_data = $this->QueryTool->get_query_data($query);
			if(!empty($query_data))
			{
				$_SESSION['USER_DATA'] = $query_data[0];
				$_SESSION['USER_DATA']['is_login'] = 'y';
				return true;
			}
		
			return false;
		}
		
		function isZipExist($zip_code)
		{
			$query = " SELECT zip_code FROM postcode WHERE  zip_code = '$zip_code'  ";
			$query_data = $this->QueryTool->get_query_data($query);
			if(!empty($query_data))
			{
				return true;
			}
		
			return false;
		}

		

		
	}# End of class
	

	# extends the methods of data basic
	class IndexModelDataBasic extends dataBasic
	{
		// set primary table
		var $tableName  = "";
		var $primaryCol = "";
	}