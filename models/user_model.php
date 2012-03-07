<?php

	/**
	* @ 
	* @Date -  28/09/2010
	**/
	
	class UserModel
	{
		var $QueryTool; // to operate extended properties
		 
		
		# constructor of class
		function UserModel()
		{
			$this->QueryTool = new UserModelDataBasic(DB_NAME);
		}
		
		function get_sender_detail($signupid)
		{
			$query = "SELECT * FROM signup WHERE MD5(signup_id) = '".$signupid."'";
			$data = mysql_query($query);
			$result = mysql_fetch_array($data);;
			//$data = $this->QueryTool->getQueryData($query);
			if($result)
			{
				return $result;	
			}
			else
			{
				return false;
			}
			
		}
		
		#get total user connected
		function get_total_friends()
		{
			$query = "SELECT * FROM signup";
			$query_data = $this->QueryTool->get_query_data($query);
			$total_count  = count($query_data);
			return $total_count;	
		}
		
		function get_from_notification($condition)
		{
			$where = "WHERE 1  ";
			if(!empty($condition))
				$where.= " AND ".$condition;

			$query = "SELECT * FROM notifications $where";
			$query_data = $this->QueryTool->get_query_data($query);
			return $query_data;	
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
		
		function isEmailExist_lead($email)
		{
			$query = " SELECT lead_id FROM lead WHERE email ='$email'   ";
			$query_data = $this->QueryTool->get_query_data($query);
			if(!empty($query_data))
			{
				return true;
			}
			
			return false;
		}
	 function get_priority($signup_id)
	 {
		 
		$query = "SELECT COUNT(contact_id) AS cnt_contact, owner_id FROM contact WHERE 1  GROUP BY owner_id 
ORDER BY COUNT(contact_id)  DESC  ";	
		$query_data = $this->QueryTool->get_query_data($query);
		
		$query = "SELECT DISTINCT owner_id FROM contact";
		$count = $this->QueryTool->get_query_data($query);
		$count_total = count($count);
		
		
		$rank = 1;
		for($i=0;$i<count($query_data);$i++)
		{
			if($query_data[$i]['owner_id'] ==  $signup_id)
			{
				break;
			}
			else
			{
				$rank++	;	
			}
			
		}
		$data[0] = $rank;
		$data[1] = $count_total;
		return $data;
 
	}
	
	function get_lead_data($email)
	{
			$query = " SELECT signup_id FROM lead WHERE MD5(email) ='$email'   ";
			$query_data = $this->QueryTool->get_query_data($query);
			if(!empty($query_data))
			{
				return $query_data;
			}
	}
		
	}# End of class
	

	# extends the methods of data basic
	class UserModelDataBasic extends dataBasic
	{
		// set primary table
		var $tableName  = "";
		var $primaryCol = "";
	}