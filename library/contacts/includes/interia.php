<?php

/**
 * @(#) interia.php
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

include_once("WebUtils.php");

class ContactImporter
{
	function ContactImporter($username, $password)
	{
		$this->_username = & $username;
		$this->_password = & $password;
		$this->name_array = array();
		$this->email_array = array();
		$this->reLastUrl = "";
		$this->uid="";
		$this->ckbox = new CookieContainer();
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
	
		$this->_username=substr($this->_username,0,strpos($this->_username,"@"));
		$startUrl = "http://poczta.interia.pl/";
		$result = $this->get_curl($startUrl,"","");
		$loginUrl = substr($result,strpos($result,"action=\"")+strlen("action=\""));
		$loginUrl = substr($loginUrl,0,strpos($loginUrl,"\""));
		$loginUrl = trim($loginUrl);
		$postStr = "email=".$this->_username."%40interia.pl&pass=".$this->_password."&webmailSelect=classicMail&formSubmit.x=28&formSubmit.y=9";
		$cookies = "formdata=".$this->_username."@interia.pl%3B-1%3B-1%3BclassicMail; formSet=classicMail;";
		$result = $this->get_curl($loginUrl,$postStr,$startUrl,$cookies);
		//get the uid from the result;
		$this->uid=substr($result,strpos($result,"\"uid\":\"")+strlen("\"uid\":\""));
	    $this->uid=substr($this->uid,0,strpos($this->uid,"\""));
		$this->reLastUrl = "http://poczta.interia.pl/html/?uid=".$this->uid;
		
		$result = $this->get_curl($this->reLastUrl,"",$startUrl);
		if(strpos($result,$this->_username."@interia.pl"))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
//Get the content of address page
	function get_address_page()
	{
		$addressUrl = "http://poczta.interia.pl/html/getcontacts,uid,".$this->uid."?inpl_network_request=true";
		$result = $this->get_curl($addressUrl,"","http://poczta.interia.pl/html/","",true);
		return $result;
		
	}

//Parse gmail address page
	function parser($str)
	{
		$str = substr($str,strpos($str,"[{")+strlen("[{"));
		$str = substr($str,0,strpos($str,"}]"));
		$rows = explode("},",$str);
		for($i=0;$i<count($rows);$i++)
		{
			$tempName="";
			$colums = explode(",",$rows[$i]);
			
			$name = substr($colums[1],strrpos($colums[1],":\"")+strlen(":\""));
			$name = substr($name,0,strrpos($name,"\""));
			
			$fname = substr($colums[2],strrpos($colums[2],":\"")+strlen(":\""));
			$fname = substr($fname,0,strrpos($fname,"\""));
			
			$lname = substr($colums[3],strrpos($colums[3],":\"")+strlen(":\""));
			$lname = substr($lname,0,strrpos($lname,"\""));
			
			$nickname = substr($colums[4],strrpos($colums[4],":\"")+strlen(":\""));
			$nickname = substr($nickname,0,strrpos($nickname,"\""));
			
			$email = substr($colums[5],strrpos($colums[5],":\"")+strlen(":\""));
			$email = substr($email,0,strrpos($email,"\""));
			
			if($fname!="" && $lname!="")
			{
				$tempName = $fname." ".$lname;
			}
			elseif($name!="")
			{
				$tempName = $name;
			}
			elseif($nickname!="")
			{
				$tempName = $nickname;
			}
			elseif($fname!="")
			{
				$tempName = $fname;
			}
			elseif($lname!="")
			{
				$tempName = $lname;
			}
			if($tempName=="" && $email!="")
			{
				$tempName = substr($email,0,strpos($email,"@"));
			}
            if($this->is_valid_email($email)) {
				$this->email_array[] = $email;
				$this->name_array[] = $tempName;
            }
		}
	   @unlink($this->cookie_path);
	}
				
	function get_curl($url,$postfileds="",$referrer="",$cookies="",$header_array="")
	{
		$agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.10) Gecko/20100914 Firefox/3.6.10 GTB7.1";
		$code="";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		if($referrer!="")
			curl_setopt($ch, CURLOPT_REFERER, $referrer);
		if($header_array!="")
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('x-requested-with: XMLHttpRequest'));
		}
        
        $cookie = $this->ckbox->getCookieString($url);
		if($cookies!="") {
            $cookie .= "; ".$cookies;
		}
        if (!empty($cookie)) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        if($postfileds!="") {
            curl_setopt($ch, CURLOPT_POST, 1.1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$postfileds);
        }
        
		curl_setopt($ch, CURLOPT_HEADER, 1);
		
		$result = curl_exec ($ch);
		$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
		curl_close ($ch);
		// echo ("url=$url<hr>PostFields=$postfileds<hr>".nl2br(htmlspecialchars($result))."<hr>");
        
        $body = $result;
        $headers = '';
        if (!empty($body)) list($headers,$body) = explode("\r\n\r\n",$body,2);
        else $body = '';

        $response_header_lines = preg_split("/\r?\n/", $headers);
        // echo "<pre>Headers: ".print_r($response_header_lines, true)."</pre>";

        foreach($response_header_lines as $r_index) {
            if (preg_match('/Set-Cookie\\s*:\\s*(.*)/ims',$r_index, $matches)) {
                $this->ckbox->addCookie( new Cookie($matches[1]));
            }
        }
        //echo "<pre>Cookies: ".print_r($this->ckbox, true)."</pre><hr>";

		if($code==302) {
			$n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
			$n_url=substr($n_url,0,strpos($n_url,"\n"));
			$this->reLastUrl = $n_url;
			$this->uid=substr($n_url,strpos($n_url,"?uid=")+strlen("?uid="));
	    	//$this->uid=substr($this->uid,0,strpos($this->uid,"\""));
			if(strpos($n_url,"http://")===FALSE){
				$n_url = "http://poczta.interia.pl".$n_url;
			}
			$result=$result."<imp:br/>".$this->get_curl(trim($n_url),"","",true);
		}
		
		return $result;
	}
    
    function is_valid_email($email) {
        $email = trim($email);
        if (!preg_match("/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i", $email)) {
            return false;
        }
        return true;
    }
}
?>