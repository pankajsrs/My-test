<?php
/**
 * @(#) 126.php
 * @author Mamun mamun@improsys.com
 * @history
 * created  : Mamun : Date : 22/10/2010
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

include_once("JSON.php");
include_once("WebUtils.php");

class ContactImporter
{
	function ContactImporter($username, $password){
		$this->_username =& $username;
		$this->_password =& $password;
		$this->name_array=array();
		$this->email_array=array();
		$this->ckbox = new CookieContainer();
	}
	function login(){
		$startUrl = "https://auth.me.com/authenticate?service=mail";
		$loginUrl = "";
		$frm = "";
		$hiddenStr = "";
		$postStr = "";
		$response = "";
		$header_array = array();
		$response = $this->get_curl($startUrl);
		$frm = substr($response,strpos($response,"<form id=\"loginForm\"")+strlen("<form id=\"loginForm\""));
		$frm = substr($frm,0,strpos($frm,"</form>"));
		$loginUrl = substr($frm,strpos($frm,"action=\"")+strlen("action=\""));
		$loginUrl = substr($loginUrl,0,strpos($loginUrl,"\""));
		$hiddenStr = $this->hidden_fileds($frm);
		$postStr = $hiddenStr."&username=".$this->_username."&password=".$this->_password;
		$response = $this->get_curl($loginUrl,$postStr,$startUrl);
		$nextUrl = "http://www.me.com/mail/";
		$response = $this->get_curl($nextUrl);
		$nextUrl = "https://www.me.com/wm/preference";
		$header_array[0] = "Accept: application/json-rpc";
		$header_array[1] = "Content-Type: application/json-rpc";
		$response = $this->get_curl($nextUrl,'{"jsonrpc":"2.0","id":"0/0","method":"list"}',"https://www.me.com/mail/",$header_array);
		$setCookie = $this->ckbox->getCookieValue("isc-www.me.com");
		$header_array[2] = "x-mobileme-isc: ".$setCookie;
		$response = $this->get_curl($nextUrl,'{"jsonrpc":"2.0","id":"0/0","method":"list"}',"https://www.me.com/mail/",$header_array);
		
		
		if(strpos($response,"<input id=\"password\"")===FALSE){
			return true;
		}else{
			return false;
		}
	}
	function get_address_page(){
		$reff = "https://www.me.com/mail/message/en/#compose";
		$addressUrl = "https://www.me.com/wo/WebObjects/Contacts.woa/wa/ScriptAction/autoComplete";
		
		list($usec, $sec) = explode(" ",microtime()); 
		$milliseconds = (round(((float)$usec*1000)) + ((float)$sec*1000));
		$postStr = 'postBody={"requiredFields":"EmailAddress"}';
		$header_array = array();
		$setCookie = $this->ckbox->getCookieValue("isc-www.me.com");
		$header_array[0] = "Content-Type: application/json";
		$header_array[1] = "x-mobileme-isc: ".$setCookie;
		$header_array[2] = "x-mobileme-version: 1.0";
		$response = $this->get_curl($addressUrl,$postStr,$reff,$header_array);
		$response = substr($response,strpos($response,"{\""));
		return $response;		
	}
	function parser($address){
		$json = new Services_JSON();
		$contacts = (array)$json->decode($address);
			
		foreach($contacts['records'] as $contact){
			$name = "";
			$email = "";
			$contact = (array)$contact;
			$name = trim($contact['firstName']." ".$contact['lastName']);
			$emails = (array)$contact['emailAddresses'][0];
			if(isset($emails['work'])){
				$email = $emails['work'];
			}else{
				$email = $emails['home'];	
			}
			if($this->is_valid_email($email) && $name==""){
				$name = substr($email,0,strpos($email,"@"));
			}
			if($this->is_valid_email($email) && $name != ""){
				$this->name_array[] = $name;
				$this->email_array[] = $email;
			}	
		}
	}
	
	function get_curl($url,$postfileds="",$referrer="",$header_array="",$debug=false)
	{
		$agent = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		if($header_array!=""){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header_array);
		}		
			curl_setopt($ch, CURLOPT_USERAGENT, $agent);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		if($referrer!=""){
			curl_setopt($ch, CURLOPT_REFERER, $referrer);
		}
		// ---------------[modified to manage cookie]--------------------------
		$cookie = $this->ckbox->getCookieString($url);
		if (!empty($cookie)) {
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		}
		//-------------------------------------------
		if($postfileds!="")
			{
			curl_setopt($ch, CURLOPT_POST, 1.1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$postfileds);
			}
		// if($header!="")
			curl_setopt($ch, CURLOPT_HEADER, 1);
			if($debug)
			{
				curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
				curl_setopt($ch, CURLOPT_PROXY, "localhost:8888");
			}
			$result = curl_exec ($ch);
			$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
			curl_close ($ch);
			//echo ("<hr>url=".htmlspecialchars($url)."<hr>PostFields=$postfileds<hr>".nl2br(htmlspecialchars($result))."<br>");
			
			// ---------------[modified to manage cookie]--------------------------
			$body = $result;
	
			$headers = '';
			if (!empty($body)) { 
				if(strpos($body,"HTTP/1.1 200 Connection Established")!==FALSE){
					list(,$headers,$body) = explode("\r\n\r\n",$body,3);
				}else{
					list($headers,$body) = explode("\r\n\r\n",$body,2);
				}
			}else{ 
				$body = '';
			}
	
			$response_header_lines = preg_split("/\r?\n/", $headers);
			//echo "<pre>Headers: ".print_r($response_header_lines, true)."</pre>";
	
			foreach($response_header_lines as $r_index) {
				if (preg_match('/Set-Cookie\\s*:\\s*(.*)/ims',$r_index, $matches)) {
					$this->ckbox->addCookie( new Cookie($matches[1]));
				}
			}
			//echo "<pre>Cookies: ".print_r($this->ckbox, true)."</pre><hr>";
			// -----------------------------------------
	
			if($code==301 || $code==302)
			{
				$n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
				$n_url=substr($n_url,0,strpos($n_url,"\n"));
				//echo $n_url."  cookie".$this->cookie_path."<br>";
				$result=$result."<br>".$this->get_curl(trim($n_url));
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
			$post= $name."=".urlencode($value);
		else
			$post.="&".$name."=".urlencode($value);
		}
		
	return $post;
	}
}
?>