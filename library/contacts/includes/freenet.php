<?
/**
 * @(#) freenet.php
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
		$this->reLastUrl = "";
		$this->id = "";
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
	
//Login to get contacts	
	function login()
	{
		@unlink($this->cookie_path);
		$this->_username=substr($this->_username,0,strpos($this->_username,"@"));
		$startUrl = "http://mail.freenet.de/dienste/emailoffice/index.html";
		$result = $this->get_curl($startUrl,$this->cookie_path,"","");
		$form = substr($result,strpos($result,"<form method=\"post\""));
		$form = substr($form,0,strpos($form,"</form>"));
		$loginUrl = substr($form,strpos($form,"action=\"")+strlen("action=\""));
		$loginUrl = substr($loginUrl,0,strpos($loginUrl,"\""));
		$strHidden = $this->hidden_fields($form);
		$postStr = $strHidden."&username=".$this->_username."&password=".urlencode($this->_password);
		$result = $this->get_curl($loginUrl,$this->cookie_path,$postStr,$startUrl);
		$this->reLastUrl = "http://e-tools.freenet.de/".$this->reLastUrl;
		$result = $this->get_curl($this->reLastUrl,$this->cookie_path,"",$startUrl,true);
		$result = $this->get_curl("http://office2.freenet.de/index.html",$this->cookie_path,"","",true);
		if(strpos($result,"<input name=\"username\"")===false)
		{
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
		$addressUrl = "http://office2.freenet.de/addresses/addr_list_view.html?sub_menu_id=4.3";
		$result = $this->get_curl($addressUrl,$this->cookie_path,"",$this->reLastUrl);
		exit;
		$exportUrl = "http://mail.yandex.ru/classic/abook_export";
		$result = $this->get_curl($exportUrl,$this->cookie_path,"","");
		$export = "http://mail.yandex.ru/classic/action_abook_export";
		$postStr = "tp=1&rus=0&submit=Экспортировать";
		$result = $this->get_curl($export,$this->cookie_path,$postStr,$exportUrl);
		$this->write_file($result,$this->cookie_path);
		return $result;
		//echo "<br>Url : ".htmlspecialchars($this->addressUrl)."<br>";
	}

//Parse gmail address page
	function parser($str)
	{
		$file = fopen ($this->cookie_path, "r");
		 while ($line = fgetcsv ($file, 1000,","))
		{
			if(!isset($th))
			{
			$th=$line;
			continue;
			}
			$temp_name=($line[8]=="") ? $line[2]:$line[8];

			$this->name_array[]=($line[0]=="" && $line[1]=="") ? $line[8]:(($line[0]!="" && $line[1]!="") ? $line[0]." ".$line[1]." ".$line[2]:$temp_name) ;

			$temp_email=($line[7]);
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
							
	function get_curl($url,$cookie_path="",$postfileds="",$referrer="",$header="")
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
			$this->reLastUrl = $n_url;
			//echo $n_url."  cookie".$this->cookie_path."<br>";
			$result=$result."<br>".$this->get_curl(trim($n_url),$this->cookie_path,"","",true);
		}
		
		return $result;
	}
}
?>