<?
/**
 * @(#) libero.php
 * @author Sumon sumon@improsys.com,Mamun mamun@improsys.com
 * @history
 *          created  : Mamun : Date : 04-02-2008
 *			
 * @version 1.0
 *
 * Copyright Improsys.
 *
 * All rights reserved.
 *
 * This software is the confidential and proprietary information
 * of Improsys. ("Confidential Information").
 * You shall not disclose such Confidential Information and shall use
 * it only in accordance with the terms of the license agreement
 * you entered into with Improsys.
 */



class ContactImporter
{
	function ContactImporter($username, $password)
	{
		$this->_username = & $username;
		$this->_password = & $password;
		$this->name_array = array();
		$this->email_array = array();
		$this->sid = "";
		$this->referer = "";
		$this->reLastUrl = "";
		$_new_dir = str_replace('\\', '/', getcwd()).'/';
		$this->cookie_path  = $_new_dir."/temp/".$this->getRand().".txt";
	}
	function getRand()
	{
		mt_srand((double)microtime()*100000);
		$test=mt_rand(65,90);
		$test3=mt_rand(97,122);
		$test1=mt_rand(65,90);
		$test4=mt_rand(48,57);
		$test5=mt_rand(48,57);
		$test6=mt_rand(65,90);
		$test7=mt_rand(97,122);
		$test8=mt_rand(97,122);
		$ram1=chr($test);
		$ram2=chr($test3);
		$ram3=chr($test4);
		$ram4=chr($test1);
		$ram5=chr($test5);
		$ram6=chr($test6);
		$ram7=chr($test7);
		$ram8=chr($test8);
		$fname="$ram1$ram2$ram3$ram4$ram5";
		return $fname;
	}
	
	function Timestamp()
	{
		$curdate=date("m-d-Y H:i:s");
		$curdate=split("[- :]",$curdate);
		$curdate=mktime($curdate[3],$curdate[4],$curdate[5],$curdate[0],$curdate[1],$curdate[2]);
		return $curdate;
	}
//Login to get contacts	
	function login()
	{
		@unlink($this->cookie_path);
		//$this->_username=substr($this->_username,0,strpos($this->_username,"@"));
		$startUrl = "http://mail.sohu.com/";
		$result = $this->get_curl($startUrl,$this->cookie_path,"","");
		$loginUrl = "http://passport.sohu.com/sso/login.jsp?userid=".urlencode($this->_username)."&password=".md5($this->_password)."&appid=1000&persistentcookie=0&s=".$this->Timestamp()."&b=1&w=1024&pwdtype=1";
		$result = $this->get_curl($loginUrl,$this->cookie_path,"",$startUrl,true);
		$url = "http://login.mail.sohu.com/servlet/LoginServlet";
		$result = $this->get_curl($url,$this->cookie_path,"","",true);
		if(strpos($result,">$this->_username<"))
		{
			$this->sid = substr($result,strpos($result,"addressbook?sid=")+strlen("addressbook?sid="));
			$this->sid = substr($this->sid,0,strpos($this->sid,"\""));
			return true;
		}
		else
		{
			@unlink($this->cookie_path);
			return false;
		}
	}
//Get the content of address page
	function get_address_page()
	{
		$addressUrl = substr($this->reLastUrl,0,strrpos($this->reLastUrl,"/"));
		$refer = $addressUrl."/addressbook";
		$addressUrl = $addressUrl."/addressbook?sid=".$this->sid;
		$result = $this->get_curl($addressUrl,$this->cookie_path,"",$refer);
		$form = substr($result,strpos($result,"<FORM METHOD=\"post\" ACTION=\"addressbook\""));
		$form = substr($form,0,strpos($form,"</FORM>"));
		$strHidden = $this->hidden_fields($form);
		$strHidden = str_replace("act=0","act=18",$strHidden);
		$exportUrl = $refer;
		$result = $this->get_curl($exportUrl,$this->cookie_path,$strHidden,$addressUrl);
		//$this->write_file($result,$this->cookie_path);
		return $result;
		//echo "<br>Url : ".htmlspecialchars($this->addressUrl)."<br>";
	}

//Parse gmail address page
	function parser($str)
	{
		$file = fopen ($this->cookie_path, "r");
		 while ($line = fgetcsv ($file, 1024,","))
		{
			if(!isset($th))
			{
			$th=$line;
			continue;
			}
			$temp_name=($line[4]=="") ? $line[3]:$line[0];

			$this->name_array[]=($line[0]=="" && $line[1]=="") ? $line[3]:(($line[0]!="" && $line[1]!="") ? $line[0]." ".$line[2]." ".$line[1]:$temp_name) ;

			$temp_email=($line[5]!="") ? $line[5]:((($line[12]!="")? $line[12]:(($line[13]!="") ? $line[13]:"")));
			if(strpos($temp_email,":")===false)
			$this->email_array[]=$temp_email;
			else
			{
			$temp_email=substr($temp_email,strrpos($temp_email,":")+1);

			$this->email_array[]=$temp_email;
			}
			
		}
	  fclose($file);
	  @unlink($this->cookie_path);
	}
	
	function write_file($content,$filename)
	{
		// Let's make sure the file exists and is writable first.
		if (is_writable($filename)) 
		{
			// In our example we're opening $filename in append mode.
			// The file pointer is at the bottom of the file hence
			// that's where $somecontent will go when we fwrite() it.
			if (!$handle = fopen($filename, 'w+')) 
			{
			 	print "Cannot open file ($filename)";
			 	exit;
			}

			// Write $somecontent to our opened file.
			if (!fwrite($handle, $content)) 
			{
				print "Cannot write to file ($filename)";
				exit;
			}

			fclose($handle);

		} 
		else 
		{
			print "The file $filename is not writable";
		}

	}
	
	function hidden_fields($str)
			{
				$post="";$str=str_replace("><",">\n<",$str);
  				preg_match_all ("/<input.*hidden.*?>/i", $str, $matches,PREG_PATTERN_ORDER);
				foreach($matches[0] as $keyfield=>$valfield)
				{
				if(strstr($valfield,'name="'))
				{
				$nstr='name="';
				$name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
				$name=substr($name,0,strpos($name,"\""));
				}
				elseif(strstr($valfield,'name='))
				{
				$nstr='name=';
				$name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
				$name=substr($name,0,strpos($name," "));
				}
				else
				{
				$nstr='NAME="';
				$name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
				$name=substr($name,0,strpos($name,"\""));
				}
				if(strstr($valfield,'value="'))
				{
				$vstr='value="';
				$value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
				$value=substr($value,0,strpos($value,"\""));
				}
				elseif(strstr($valfield,'VALUE="'))
				{
				$vstr='VALUE="';
				$value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
				$value=substr($value,0,strpos($value,"\""));
				}
				else
				{
				$vstr='value=';
				$value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
				$value=substr($value,0,strpos($value,">"));
				}
				if($post=="")
				$post=$name."=".urlencode($value);
				else
				$post.="&".$name."=".urlencode($value);
				}
				return $post;
				}
				
	function get_curl($url,$cookie_path="",$postfileds="",$referrer="",$header="",$header_array="")
	{
		$agent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; (R1 1.6); .NET CLR 2.0.50727; .NET CLR 1.1.4322)";
		$code="";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		if($referrer!="")
			curl_setopt($ch, CURLOPT_REFERER, $referrer);
		if($header_array!="")
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header_array);
		}
		if($cookie_path!="")
        {
            touch($cookie_path);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_path);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_path);
        }
		if($postfileds!="")
			{
				curl_setopt($ch, CURLOPT_POST, 1.1);
				curl_setopt($ch, CURLOPT_POSTFIELDS,$postfileds);
			}
		if($header!="")
			{	
				curl_setopt($ch, CURLOPT_HEADER, 1);
			}	
		$result = curl_exec ($ch);
		$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
		curl_close ($ch);
		echo ("url=$url<hr>PostFields=$postfileds<hr>".nl2br(htmlspecialchars($result))."<hr>");
		if($code==302)
		{
			$n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
			$n_url=substr($n_url,0,strpos($n_url,"\n"));
			$this->reLastUrl = trim($n_url);
			//echo $n_url."  cookie".$this->cookie_path."<br>";
			$result=$result."<br>".$this->get_curl(trim($n_url),$this->cookie_path,"","",true);
		}
		return $result;
	}
}
?>