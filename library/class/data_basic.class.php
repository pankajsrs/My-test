<?php

	/* @Package : dataBasic 
	*  The package defines all the common operations of database like
	*  SELECT, INSERT, UPDATE, DELETE. It inherites data_manager.class for
	*  basic db operations.
	*  @Date : 09/10/2008
	*/

	class dataBasic
	{
	
		var $QueryTool; // to operate extended properties
		var $table_name;
		var $join_type;
		var $primary;
		var $order;
		var $stripSlash;
		# constructor of class
		function dataBasic()
		{
			$this->QueryTool = new dataBasic_DataManager(DB_NAME);
			# default order
			$this->order = ' ASC ';
			# default join type
			$this->join_type = ' AND ';
		}

		# Method to set the table_name
		function set_table($table_name)
		{
			if (empty($table_name))
			{
				return false;
			}
			$this->table_name = $table_name;
		}
		function get_table()
		{
			return $this->table_name;
		}
		# Method to set the primary
		function set_primary($primary_key)
		{
			$this->primary = $primary_key;
		}
		
		# Method to set the join type (AND/OR)
		function set_join_type($join_type)
		{
			$this->join_type = $join_type;
		}
		function set_order($order)
		{
			$this->order = $order;
		}
		
		function set_strip($status='Y')
		{
			$this->stripSlash = $status;
		}
		
		/** Method to insert record
		**  Before using this function it is recommended to call the set_table method
		**
		**  Example - Insert the record with values Arun, Active
		**  $data = Array ('field_name' => 'value', 'field_name' => 'value');
		**  $obj_bacis->set_table('xx');
		**  $obj_bacis->set_join_type(' AND '); /OR
		**  $obj_basic->update_data($data, $condition);
		**/
		function insert_data ($data)
		{
		 
			if(empty($this->table_name))
			{
				return false;
			}
			 
			$this->QueryTool->setTable($this->table_name);
			$inserted_id = $this->QueryTool->save ($data);
			if($inserted_id == 0)
			{
				return 0;
			}
			else
			{
				return $inserted_id;
			}
		}
		
		/** Method to update record
		**  Before using this function it is recommended to call set_table, set_join_type
		**  method
		**  @Param - $data : Array, $condition: Array
		**
		**  Example - Update the record where member_id is 1 with name=arun and status=active
		**  $data = Array ('name' => 'Arun', 'status' => 'Active');
		**  $condition = Array ('member_id' => 1);
		**  $obj_bacis->set_table('xx');
		**  $obj_bacis->set_join_type(' AND '); /OR
		**  $obj_basic->update_data($data, $condition);
		**/
		function update_data ($data, $condition)
		{
			if(empty($this->table_name))
			{
				return false;
			}
			if(empty($this->join_type))
			{
				$this->join_type = ' AND ';
			}
			$this->QueryTool->setTable($this->table_name);
			$this->QueryTool->setJoinType($this->join_type);
			$status = $this->QueryTool->save($data, $condition);
			if($status)
			{
				return true;
				
			}
			else
			{
				return false;
			}
		}
		
		/** delete the record from the table
		**  $condition - 'member_id=1' (Example)
		**/
		function delete_record($condition)
		{
			if(empty($this->table_name))
			{
				return false;
			}
			
			$this->QueryTool->setTable($this->table_name);
			$status = $this->QueryTool->remove($condition);
			if($status)
			{
				return true;
				
			}
			else
			{
				return false;
			}
		}
		
		/**
		** Method to get the data from only one table
		** @param: field_array - list of fields and their values to make the where clause,
		** $fileds - list of fields which need to be fetch, $start, $limit.
		** before using this function we need to call set_table()
		** set_primary()
		** @Return type - null/array
		**/

		function get_data($field_array, $fields='*', $start=null, $limit=null)
		{
			$where = "1=1 ";
			if(!empty($field_array))
			{
				foreach($field_array as $key=>$value)
				{
					if(!is_null($value))
						$where .= $this->join_type." ".trim($key)."='".trim(mysql_escape_string($value))."'";
				}
			}
			$this->QueryTool->setWhere($where);
			$this->QueryTool->setTable($this->table_name);
			$this->QueryTool->setPrimary($this->primary);
			$this->QueryTool->setOrder($this->order); #
			if(!is_null($start) && !is_null($limit))
			{
				$limit = $start.", ".$limit;
				$this->QueryTool->setLimit($limit);
			}
			
			$data = $this->QueryTool->get($fields);
			if(empty($data))
			{
				return null;
			}
			else
			{
				return $data;
			}
		}

		/**
		** Method to get the count of records from the table
		** @param: field_array - list of fields and their values to make the where clause,
		** before using this function we need to call set_table(), set_join_type() - AND/OR,
		** set_primary()
		** $field_array means condition array
		** @Return type - null/array
		**/

		function get_count($field_array)
		{
			
			
			$where = "1=1 ";
			if(!empty($field_array))
			{
				foreach($field_array as $key=>$value)
				{
					$where .= $this->join_type." ".trim($key)."='".trim(mysql_escape_string($value))."'";
				}
			
			}
					
			$sql = " SELECT COUNT(".$this->primary.") as record_count FROM ".$this->table_name." WHERE ".$where;
			$data = $this->QueryTool->getQueryData($sql);
			if(empty($data))
			{
				return null;
			}
			else
			{
				return $data[0]['record_count'];
			}
		}
		/**
		*  @Description : method to execute any query.
		*  @Parameter   : query as string, queryType as string
		*  @returnType  : true/false
		*/

		function run_query($query, $query_type)
		{
			
			return $this->QueryTool->runQuery($query, $query_type);
		}

		/**
		*  @Description : method to execute any query and get data.
		*  @Parameter   : query as string
		*  @returnType  : null/data
		*/
		function get_query_data($query)
		{
			
			return $this->QueryTool->getQueryData($query);
		}
		
		# chech the size of image
		function check_size($image_name, $image_size, $uploadType)
		{
				
				//2 mb
				$max_file_size = 1024*1024*2;
								
				if($image_name != "")
				{

					$size = $image_size;

					if($size > $max_file_size)
					{
						return false;
					} 
					else
					{
						return true;
					}
				}
				else
				{
					return true;
				}
		}
		 

		function begin_transaction()
		{
			/**
			** Set autocommit to 0 and start transaction.
			**/
			$sql = "SET autocommit=0";
			$this->run_query($sql, 'update');
			$sql = " BEGIN ";
			$this->run_query($sql, 'update');
		}

	 

		function rollback_transaction()
		{
			$sql = " ROLLBACK ";
			$this->run_query($sql, 'update');
		}
		function commit_transaction()
		{
			$sql = " COMMIT ";
			$this->run_query($sql, 'update');
		}
		function off_autocommit()
		{
			$sql = "SET autocommit=1";
			$this->run_query($sql, 'update');
		}
		
	 
	
		
	} # End of class

	# extends the methods of data manager (database access)
	class dataBasic_DataManager extends DataManager
	{
		// set primary table
		var $tableName  = "";
		var $primaryCol = "";
	}