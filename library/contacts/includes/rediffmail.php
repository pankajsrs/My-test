<?php
/**
 * @(#) rediff.php
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
include_once("csvutils.php");


class ContactImporter
{
	function ContactImporter($username, $password)
	{
		$this->_username = & $username;
		$this->_password = & $password;
		$this->name_array = array();
		$this->email_array = array();
		$this->addressUrl = "";
		$this->referer = "";
		$this->domain = "";
		$this->sessionid = "";
		$this->els = "";
	    $this->ckbox = new CookieContainer();
	}

	//Login to get contacts	
	function login()
	{
		
		$this->_username=substr($this->_username,0,strpos($this->_username,"@"));
		$loginUrl="http://mail.rediff.com/cgi-bin/login.cgi";
		$this->referer = $loginUrl;
		$postString = "login=".$this->_username."&passwd=".$this->_password."&FormName=existing&proceed=GO";
		$result = $this->get_curl($loginUrl,$postString,"http://www.rediff.com/");
		
		if(strpos($result,"window.location.replace"))
		{
			$redirectUrl = substr($result,strpos($result,"replace(\"")+strlen("replace(\""));
			$redirectUrl = substr($redirectUrl,0,strpos($redirectUrl,"\")"));
			$result = $this->get_curl($redirectUrl,"","",true);
			if(strpos($result,"<b>Go to Inbox &gt;&gt;</b>")!=FALSE){
				$redirectUrl = substr($result,strpos($result,"<div class=\"floatR\">")+strlen("<div class=\"floatR\">"));
				$redirectUrl = substr($redirectUrl,0,strpos($redirectUrl,"<div"));
				$redirectUrl = substr($redirectUrl,strpos($redirectUrl,"href=\"")+strlen("href=\""));
				$redirectUrl = substr($redirectUrl,0,strpos($redirectUrl,"\">"));
				$this->domain = substr($redirectUrl,strpos($redirectUrl,"http://")+strlen("http://"));
				$this->domain = substr($this->domain,0,strpos($this->domain,"/"));
				$result = $this->get_curl($redirectUrl,"","",true);
			}else{
				$redirectUrl = substr($result,strpos($result,"URL=")+strlen("URL="));
				$redirectUrl = substr($redirectUrl,0,strpos($redirectUrl,"\">"));
				$this->domain = substr($redirectUrl,strpos($redirectUrl,"http://")+strlen("http://"));
				$this->domain = substr($this->domain,0,strpos($this->domain,"/"));
				$result = $this->get_curl($redirectUrl,"","",true);
			}
		}
		
		if(strpos($result,$this->_username)!==false)
		{
					
			$this->sessionid = substr($result,strpos($result,"session_id='")+strlen("session_id='"));
			$this->sessionid = substr($this->sessionid,0,strpos($this->sessionid,"'"));
			if(strpos($result,"strELSKey = '")!=FALSE){
				$this->els = substr($result,strpos($result,"strELSKey = '")+strlen("strELSKey = '"));
				$this->els = substr($this->els,0,strpos($this->els,"'"));
			}else{
				$this->els = substr($result,strpos($result,"\"els\":'")+strlen("\"els\":'"));
				$this->els = substr($this->els,0,strpos($this->els,"'"));
			}
			return true;
		} else {
			return false;
		}

		
		
	}
    // Get the content of address page
	function get_address_page()
	{
		$this->addressUrl = "http://".$this->domain."/ajaxprism/exportaddrbook?service=moutlook";
		$postString = "els=".$this->els."&exporttype=moutlook";
		$result = $this->get_curl($this->addressUrl,$postString);
		return $result;
	}

    // Parse gmail address page
	function parser($str) {
        list($headers, $body) = explode("\r\n\r\n", $str, 2);
        $contacts = csv2array($body);
        
        foreach($contacts as $contact) {
            $name = $email = "";
            $name = trim($contact["First Name"]." ".$contact["Middle Name"]." ".$contact["Last Name"]);
            $email = trim($contact["E-mail Address"]);
            
            if($this->is_valid_email($email)) {
				if($name==""){
					$name = substr($email,0,strpos($email,"@"));
				}
                $this->email_array[] = $email;
                $this->name_array[] = $name;
            }
		}
	}
	
	function hidden_fields($str)
	{
		$post="";$str=str_replace("><",">\n<",$str);
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
		if(strpos($valfield,$vstr)===false){
		$vstr='value=';
		$value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
		$value=substr($value,0,strpos($value,">"));
		}
		else
		{
		$value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
		$value=substr($value,0,strpos($value,"\""));
		}
		if($post=="")
		$post=$name."=".urlencode($value);
		else
		$post.="&".$name."=".urlencode($value);
		}
		return $post;
	}
				
	function get_curl($url,$postfileds="",$referrer="",$header="",$debug="")
	{
		$agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727; .NET CLR 1.1.4322)";
		$ch = curl_init();
        
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		if($referrer!="")
			curl_setopt($ch, CURLOPT_REFERER, $referrer);
            
        // ---------------[modified to manage cookie]--------------------------

        $cookie = $this->ckbox->getCookieString($url);
       
        if (!empty($cookie)) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
            
        }
        
        //-------------------------------------------
       
        
		if($postfileds!="")	{
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$postfileds);
		}
		// if($header!="")	{
				curl_setopt($ch, CURLOPT_HEADER, 1);
		// }
		if($debug!="") {
			//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
			//curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:8888");
		}
		$result = curl_exec($ch);
		$st=curl_error ($ch);
		$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
		curl_close ($ch);
        //echo ("url=$url<hr>PostFields=$postfileds<hr>Error=".$st."<hr>"."Code=".$code."<hr>".nl2br(htmlspecialchars($result))."<hr>");
        
        // ---------------[modified to manage cookie]--------------------------
        $body = $result;

        $headers = '';
        if(strpos($body, "\r\n\r\n") !== FALSE) {
            if (!empty($body)) list($headers,$body) = explode("\r\n\r\n", $body, 2);
            else $body = '';
        }

        $response_header_lines = preg_split("/\r?\n/", $headers);
        // echo "<pre>Headers: ".print_r($response_header_lines, true)."</pre>";

        foreach($response_header_lines as $r_index) {
            if (preg_match('/Set-Cookie\\s*:\\s*(.*)/ims',$r_index, $matches)) {
                $this->ckbox->addCookie( new Cookie($matches[1]));
            }
        }
        // echo "<pre>Cookies: ".print_r($this->ckbox, true)."</pre>";
        // -----------------------------------------

		if($code==302)
		{
			$n_url = substr($result,strpos($result,"Location: ")+strlen("Location: "));
			$n_url = substr($n_url,0,strpos($n_url,"\n"));
			$this->domain = substr($n_url,strpos($n_url,"http://")+strlen("http://"));
			$this->domain = substr($this->domain,0,strpos($this->domain,"/"));
			$result=$result."<br>".$this->get_curl(trim($n_url),"","",true);
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