<?php
/**
 * @(#) maildotcom.php
 * @author Sumon sumon@improsys.com,Mamun mamun@improsys.com
 * @history
 *          created  : Mamun : Date : 04-02-2008
 *			modify	 : Mamun : Date : 13-12-2010	  
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

include_once("includes/Json2.php");
include_once("WebUtils.php");

class ContactImporter
{
	function ContactImporter($username, $password)
	{
		$this->_username = & $username;
		$this->_password = & $password;
		$this->name_array = array();
		$this->email_array = array();
		$this->authToken = "";
		$this->clientVersion = "";
		$this->ckbox = new CookieContainer();
	}
    	
//Login to get contacts	
	function login()
	{   
		$this->_username=substr($this->_username,0,strpos($this->_username,"@"));
		$startUrl = "http://www.mail.com/int/";
		$result = $this->get_curl($startUrl,"","",true);
        
		
		$postString = "rdirurl=http%3A%2F%2Fwww.mail.com%2Fint%2F&login=".$this->_username."@mail.com&password=".urlencode($this->_password);
		$result = $this->get_curl("http://service.mail.com/login.html",$postString,$startUrl);

        if(strpos($result,"<input id=\"login1\"")===FALSE){
			if(strpos($result,"mailclient.predefined")===FALSE){
				return false;
			}else{
				if(strpos($result,"session : {")===FALSE && strpos($result,"version : {")===FALSE){
					return false;
				}else{
					$this->authToken = substr($result,strpos($result,"authToken      : \"")+strlen("authToken      : \""));
					$this->authToken = substr($this->authToken,0,strpos($this->authToken,"\""));
					
					$this->clientVersion = substr($result,strpos($result,"rms             : \"")+strlen("rms             : \""));
					$this->clientVersion = substr($this->clientVersion,0,strpos($this->clientVersion,"\""));
				
					return true;
				}
			}
        }else{
            return false;
        }
			
	}
//Get the content of address page
	function get_address_page()
	{
		$adrUrl = "http://service.mail.com/callgate-".$this->clientVersion."/coms8/PersonService/getAll?X-UI-JSON=javascript&X-UI-RequestId=13&Authorization=".$this->authToken."&nocache=".time();
        $postString = "{\"order\":{\"descending\":false,\"order\":\"ORDER_NAME\"},\"from\":0,\"to\":0,\"search\":{\"searchString\":\"\",\"position\":\"LIKE_ANY\",\"categoryIds\":null}}";
		$refer = "http://service.mail.com/mail.html#1";
		$result = $this->get_curl($adrUrl,$postString,$refer);	
		return $result;
	}

    //Parse gmail address page
	function parser($str) {
        list($headers, $body) = explode("\r\n\r\n", $str, 2);
        if(strpos($body, "\r\n\r\n") !== FALSE) {
            list($headers, $body) = explode("\r\n\r\n", $body, 2);
        }
        //Problem,
        $json = new Json();
        $contacts = (array)$json->decode($body);
        
		foreach($contacts['response'] as $contact){
			$contact = (array)$contact;
			$temp_name = $temp_email="";
            
            $temp_name = isset($contact['shortName']) ? $contact['shortName'] : "";
            
            if(isset($contact['email'][0])) {
                $temp_email = (array)$contact['email'][0];
                $temp_email = $temp_email['address'];
            }
            if(!empty($temp_email)) {
				$this->name_array[] = trim($temp_name);
				$this->email_array[] = trim($temp_email);
            }
		}
	}

	function hidden_field_array($str)
	{
		$post=array();
		$str=str_replace("> <","><",$str);
		preg_match_all ('/<input.*?hidden.*?>/i', $str, $matches,PREG_PATTERN_ORDER);
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
			if(strstr($valfield,'value="'))
			{
				$vstr='value="';
				$value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
				$value=substr($value,0,strpos($value,"\""));
			}
			else
			{
				$value="";
			}
			$post[$name]=$value;
		
		}
		return $post;
	}

					
	function get_curl($url,$postfileds="",$referrer="",$header="",$header_array="", $doRedirect=true)
	{
		$agent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 2.0.50727; .NET CLR 1.1.4322)";
		$code="";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		if($referrer!="")
			curl_setopt($ch, CURLOPT_REFERER, $referrer);
		if($header_array!=""){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header_array);
		}
        // ---------------[modified to manage cookie]--------------------------
        $cookie = $this->ckbox->getCookieString($url);
        if (!empty($cookie)) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
       if($postfileds!="")
			{
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS,$postfileds);
			}
		// if($header!="")
			// {	
				curl_setopt($ch, CURLOPT_HEADER, 1);
			// }	
		$result = curl_exec ($ch);
		$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
		curl_close ($ch);
		//echo ("url=$url<hr>PostFields=$postfileds<hr>".nl2br(htmlspecialchars($result))."<hr>");
        
        /*---------------[modified to manage cookie]--------------------------*/
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
        // echo "<pre>Cookies: ".print_r($this->ckbox, true)."</pre><hr>";
        /*------------------------------------------*/
        if($doRedirect){
            if($code==302)
            {
                $n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
                $n_url=substr($n_url,0,strpos($n_url,"\n"));
                //echo $n_url."  cookie".$this->cookie_path."<br>";
                $result=$result."<br>".$this->get_curl(trim($n_url),"","",true);
            }
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