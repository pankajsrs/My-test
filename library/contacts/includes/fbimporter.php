<?php
/**
 * @(#) fbimporter.php
 * @author Sumon sumon@improsys.com
 * @history
 *          created  : Mamun : Date : 02-04-2010
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
    	$this->_username =& $username;
    	$this->_password =& $password;
		$this->name_array=array();
		$this->email_array=array();
		$this->startUrl = "http://touch.facebook.com/login.php";
		
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
	//Login to facebook
	function login()
	{
		$response = $this->get_curl($this->startUrl,$this->cookie_path,"","",true);
		$loginFrm = substr($response,strpos($response,"<form id=\"login_form\"")+strlen("<form id=\"login_form\""));
		$loginFrm = substr($loginFrm,0,strpos($loginFrm,"</form>"));
		$loginUrl = substr($loginFrm,strpos($loginFrm,"action=\"")+strlen("action=\""));
		$loginUrl = substr($loginUrl,0,strpos($loginUrl,"\""));
		$hiddenStr = $this->hidden_fileds($loginFrm);
		$poststr = "email=".$this->_username."&pass=".$this->_password."&next=".$hiddenStr;
		$response = $this->get_curl($loginUrl,$this->cookie_path,$poststr,"",true);
		
		if(strpos($response,"<form id=\"login_form\"")===FALSE){
			return true;
			
		}else{
			@unlink($this->cookie_path);
			return false;
		}
	}
	//Grap Address
	function get_address_page()
	{
		$s = 15;
		$stop = FALSE;
		$uids = array();
		while($stop===FALSE){
		
			$frndUrl = "http://touch.facebook.com/friends.php?v=all&s=$s";
			$refer = "http://touch.facebook.com/";
			$response = $this->get_curl($frndUrl,$this->cookie_path,"",$refer);
			if(strpos($response,"<h2>No friends.</h2>")===FALSE){
			
				$rows = explode("<div class=\"item_friend_network\"></div>",$response);
				for($i=0;$i<sizeof($rows)-1;$i++){
					$email = "";
					$name = "";
					$uid = "";
					
					$uid = substr($rows[$i],strpos($rows[$i],"profile.php?id=")+strlen("profile.php?id="));
					$uid = substr($uid,0,strpos($uid,"&"));
					$uid = trim($uid);
					$uids[] = $uid; 			
					//$prifileurl = "http://touch.facebook.com/profile.php?id=".$uid;
					$infourl = "http://touch.facebook.com/profile.php?id=".$uid."&v=info";
					
					//$response = $this->get_curl($prifileurl,$this->cookie_path,"",$refer,true);
					$name = substr($rows[$i],strpos($rows[$i],"\"item_friend_name\">")+strlen("\"item_friend_name\">"));
					$name = substr($name,0,strpos($name,"</div>"));
					$name = strip_tags($name);
					
					$response = $this->get_curl($infourl,$this->cookie_path,"",$refer);
					
					if(strpos($response,"Email:")!==FALSE){
						$email = substr($response,strpos($response,"Email:")+strlen("Email:"));
						$email = substr($email,0,strpos($email,"</div>"));
						if(strpos($email,"<img src=")===FALSE)
							$email = strip_tags($email);
						else
							$email = "";	
					}
					if(!empty($email)) {
						$this->email_array[] = $email;
						$this->name_array[] = $name;
					}
				}
				$s +=15; 
			}else{
				$stop = TRUE;
			}	
		}
		
		@unlink($this->cookie_path);
	}
	//Get the hidden fields of a page
	function hidden_fileds($str)
	{
		$post="";
		preg_match_all ("/<input.*hidden.*?>/", $str, $matches,PREG_PATTERN_ORDER);
		foreach($matches[0] as $keyfield=>$valfield)
		{
			if(strstr($valfield,'name="'))
			{
				$nstr='name="';
				$name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
				$name=substr($name,0,strpos($name,"\""));
			}
			else
			{
				$nstr='name=';
				$name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
				$name=substr($name,0,strpos($name," "));
			}
				$vstr='value="';
				$value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
				$value=substr($value,0,strpos($value,"\""));
				$details[$name]=$value;
			if($post=="")
				$post=$name."=".urlencode($value);
			else
				$post.="&".$name."=".urlencode($value);
		}
	
	return $post;
	}
	function get_curl($url,$cookie_path="",$postfileds="",$referrer="",$header="",$httph="")
	{
		$agent = "Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_0 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7A341 Safari/528.16";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
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
			curl_setopt($ch, CURLOPT_HEADER, 1);
		$result = curl_exec ($ch);
		$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
		curl_close ($ch);
		//echo ("url=$url<hr>PostFields=$postfileds<hr>".nl2br(htmlspecialchars($result))."<hr>");
		if($code==302 ||$code==301)
		{
			$n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
			$n_url=substr($n_url,0,strpos($n_url,"\n"));
			if(strpos($n_url,"http://")===FALSE){
				$n_url = "http://touch.facebook.com".$n_url;
			}
			$result=$result."<br>".$this->get_curl(trim($n_url),$this->cookie_path,"","",true);
		}
		return $result;
	}
} 
?>