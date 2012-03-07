<?php

/**
 *		@Package		: Data Manager
 *		@Description	: Methods to manipulate common database operations.
 *       These properties can be later inherited by other classes.
 *		@Date			: 09/10/2008
 */

class DataManager
{

	# var $aExtDet; will be defined in child class
	var $databaseName;
var $order;
var $limit;
var $where;
var $tableName;
var $primaryCol;
var $joinType;
var $stripSlash;
# set the database in constructor
function dataManager($db)
{
	$this->databaseName=$db;
	$LINK = mysql_connect(DB_HOST_NAME, DB_USER_NAME, DB_PASSWORD);
	if(!mysql_select_db($this->databaseName))
	{
		trigger_error("Error in db connection");
	}
	$this->setOrder('ASC');
	$_SESSION['debug'] = Array();
}
# set order of query. Default is ascending
function setOrder($order='ASC')
{
	$this->order = " ORDER BY ".$this->primaryCol." ".$order;
}

/** function set limit of result. Default is all
 *  pass comma seprated value (start, end)
 */

function setLimit($limit)
{
	$this->limit = 'Limit '.$limit;
}

# set where condition for the query
function setWhere($where)
{
		
	$this->where = " WHERE ".trim($where);
}

function setTable($tableName)
{
	$this->tableName = $tableName;
}

# Method to set the join type (AND/OR)
function setJoinType($join_type)
{
	$this->joinType = $join_type;
}

function setStrip($status='Y')
{
	$this->stripSlash = $status;
}
/**
 *  set primary col. it will be useful when operation will be
 *  conducted on table other than base table.
 */
function setPrimary($key)
{
	$this->primaryCol = $key;
}
/**
 *  @Description : return all rows from a single table
 *  @Return Type : Array
 */
function getAll()
{
		
	$sql = "SELECT * FROM ".$this->tableName." ".$this->where." ".$this->order." ".$this->limit;
	/**
	 * @Debug Code
	 *
	 */
	$time_start = getmicrotime();
	$result = @mysql_query($sql);
	/**
	 * @Debug Code
	 * @var unknown_type
	 */
	$time_end = getmicrotime();
	$time = $time_end - $time_start;
	set_debug($result, $sql, 'SELECT', $time);
	
	
	$rows = Array();
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		if(empty($this->stripSlash) || $this->stripSlash == 'Y')
		{
			$rows[] = stripslashes_deep($row);
		}
		else
		{
			$rows[] = $row;
		}

	}
	@mysql_free_result($result);
	if(empty($rows))
	{
		return null;
	}
	else
	{
		return $rows;
	}
}

/**
 *  @Description : return single row from a table
 *  @Return Type : Array
 */

function get($fieldName)
{
		
	$sql = "SELECT ".$fieldName." FROM ".$this->tableName." ".$this->where." ".$this->order." ".$this->limit;
	 
	/**
	 * @Debug Code
	 *
	 */
	$time_start = getmicrotime();
	$result = mysql_query($sql);
	/**
	 * @Debug Code
	 * @var unknown_type
	 */
	$time_end = getmicrotime();
	$time = $time_end - $time_start;
	set_debug($result, $sql, 'SELECT', $time);
	
	
	$rows = Array();
		
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		if(empty($this->stripSlash) || $this->stripSlash == 'Y')
		{
			$rows[] = stripslashes_deep($row);
		}
		else
		{
			$rows[] = $row;
		}
	}
	@mysql_free_result($result);
	if(empty($rows))
	{
		return null;
	}
	else
	{
		return $rows;
	}
}
/**
 * Saves the user data into database. Updates if id not empty,
 * else inserts new row.
 *
 * $userData = array (
 *      'filed1'  =>
 *      'filed2'  =>
 *		'filed3'  =>
 * );
 *
 * @param array $userData
 */
function save($aData, $aCondition=null)
{
	# update
		
	if(!is_null($aCondition))
	{

		if(!is_array($aCondition))
		{
			return false;
		}
		$setFlag = "";
		$sql = "UPDATE ".$this->tableName." SET ";
		$i=0;
		foreach($aData as $key=>$value)
		{
			if($i==0)
			{
				$sql .=$key."='".mysql_escape_string(html_entity_decode($value, ENT_QUOTES))."'";
			}
			else
			{
				$sql .=", ".$key."='".mysql_escape_string(html_entity_decode($value, ENT_QUOTES))."'";
			}
			$i++;
		} # end for

		if(empty($this->joinType))
		{
			$this->joinType = ' AND ';
		}
		# execute update query
		$sWhere = '';
		$k=0;
		foreach($aCondition as $key=>$value)
		{
			if($k==0)
			{
				$sWhere .= $key."='".mysql_escape_string(html_entity_decode($value, ENT_QUOTES))."'";
			}
			else
			{
				$sWhere .= $this->joinType." ".$key."='".mysql_escape_string(html_entity_decode($value, ENT_QUOTES))."'";
			}
			$k++;
		}
		$sql .=" WHERE ".$sWhere;
		
		/**
		 * @Debug Code
		 *
		 */
		$time_start = getmicrotime();
		$result = mysql_query($sql);
		/**
		 * @Debug Code
		 * @var unknown_type
		 */
		$time_end = getmicrotime();
		$time = $time_end - $time_start;
		set_debug($result, $sql, 'UPDATE', $time);
		
		if($result)
		{
				
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		# insert

		$sql = "INSERT INTO ".$this->tableName;
	$sKey = "";
	$iValue = "";
	$i = 0;
	foreach($aData as $key=>$value)
	{
		if($i==0)
		{
			$sKey .=$key;
			$iValue .="'".mysql_escape_string(html_entity_decode($value, ENT_QUOTES))."'";
		}
		else
		{
			$sKey .=", ".$key;
			$iValue .=", '".mysql_escape_string(html_entity_decode($value, ENT_QUOTES))."'";
		}
		$i++;
	} # end for
		
	$sql .= " (".$sKey.") VALUES (".$iValue.")";
	
	/**
	 * @Debug Code
	 *
	 */
	$time_start = getmicrotime();
	$result = mysql_query($sql);
	/**
	 * @Debug Code
	 * @var unknown_type
	 */
	$time_end = getmicrotime();
	$time = $time_end - $time_start;
	set_debug($result, $sql, 'INSERT', $time);
	
	
	# if sucessfull
	if($result)
	{
		return mysql_insert_id();
	}
	else
	{
		return '0';
	}
	} # end if
}

/**
 *	@Description: Function to delete record from specified table based on condition
 *   @Param: string (condition) if input parameter missing the function will delete
 *
 */
function remove($condition=null)
{
	$sWhere = "";
	if(!is_null($condition))
	{
		$sWhere = $condition;
	}
		
	$sql = "DELETE FROM ".$this->tableName." WHERE ".$sWhere;

	/**
	 * @Debug Code
	 *
	 */
	$time_start = getmicrotime();
	$recordset = @mysql_query($sql);
	/**
	 * @Debug Code
	* @var unknown_type
	*/
	$time_end = getmicrotime();
	$time = $time_end - $time_start;
	set_debug($recordset, $sql, 'DELETE', $time);
		
		
	if($recordset)
	{
		return true;
	}
	else
	{
		return false;
	}
}
/**
 *  @Description : method to execute any query.
 *  @Parameter   : query as string, queryType as string
 *  @returnType  : true/false
 */

function runQuery($sQuery, $queryType)
{
	/**
	 * @Debug Code
	 *
	 */
	$time_start = getmicrotime();
	$recordset = mysql_query($sQuery);
	/**
	 * @Debug Code
	 * @var unknown_type
	 */
	$time_end = getmicrotime();
	$time = $time_end - $time_start;
	set_debug($recordset, $sQuery, $queryType, $time);
		
		
	if(strtoupper($queryType) == 'SELECT')
	{
		$num_rows = mysql_num_rows($recordset);
	}
	else
	{
		$num_rows = mysql_affected_rows();
	}
	if(strtoupper($queryType) == 'SELECT')
		mysql_free_result($recordset);

	return $num_rows;
}

/**
 *  @Description : method to execute any query and get data.
 *  @Parameter   : query as string
 *  @returnType  : null/data
 */
function getQueryData($sQuery)
{
	/**
	* @Debug Code
	*
	*/
	$time_start = getmicrotime();
	$result = mysql_query($sQuery);
	/**
	 * @Debug Code
	 * @var unknown_type
	 */
	$time_end = getmicrotime();
	$time = $time_end - $time_start;
	//echo $sQuery;
	if(mysql_errno())
		mail("rahul.srsinfo@gmail.com", time(), $sQuery. "==".mysql_error());
	
	set_debug($result, $sQuery, 'SELECT', $time);
		
	$rows = Array();
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		if(empty($this->stripSlash) || $this->stripSlash == 'Y')
		{
			$rows[] = stripslashes_deep($row);
		}
		else
		{
			$rows[] = $row;
		}
	}
	@mysql_free_result($result);
	if(empty($rows))
	{
		return null;
	}
	else
	{
		return $rows;
	}
}

} # End of class

function set_debug($rs, $sQuery, $queryType, $time)
{
	$idx = count($_SESSION['debug'])-1;
	if($queryType == 'SELECT')
	$_SESSION['debug'][$idx]['Num'] = mysql_num_rows($rs);
	
	if($queryType != 'SELECT')
		$_SESSION['debug'][$idx]['Affected'] = mysql_affected_rows();

	$_SESSION['debug'][$idx]['Query'] = $sQuery;
	$_SESSION['debug'][$idx]['Error'] = mysql_error();
	$_SESSION['debug'][$idx]['Took'] =  ceil($time);
}

function getmicrotime(){
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}

## stripslahes for array and single value
function stripslashes_deep($value)
{
	$value = is_array($value) ?
	array_map('stripslashes_deep', $value) :
	stripslashes(htmlentities($value, ENT_QUOTES));
	return $value;
}

?>