<?php
/**
 * @(#) gmx.php
 * @author Sumon sumon@improsys.com
 * @history
 *          created  : Mamun : Date : 03-01-2010
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

class ContactImporter {

	function ContactImporter($username, $password) 
    {
        $this->_username =& $username;
        $this->_password =& $password;
        $this->email_array = array();
        $this->name_array = array();
		$this->addurl = "";
		$this->ckbox = new CookieContainer();
	}
	
	
	//Loging function for login to gmx.de
	function login() {
        $siteurl = "http://www.gmx.net/nossl";
		$response = $this->get_curl($siteurl);
		$loginfrm = substr($response,strpos($response,"<div class=\"loginbox\">")+strlen("<div class=\"loginbox\">"));
		$loginfrm = substr($loginfrm,0,strpos($loginfrm,"</form>"));
		$loginfrm = substr($loginfrm,strpos($loginfrm,"<form method=\"post\""));
		
		$loginurl = substr($loginfrm,strpos($loginfrm,"action=\"")+strlen("action=\""));
		$loginurl = substr($loginurl,0,strpos($loginurl,"\""));
		$hiddenfields = $this->hidden_fileds($loginfrm);
		$postfields = $hiddenfields."&id=".$this->_username."&p=".$this->_password."&jsenabled=true";
		$response = $this->get_curl($loginurl,$postfields,$siteurl,true);
		if(strpos($response,"<input type=\"password\"")===FALSE){
			$this->addurl = substr($response,strpos($response,"addressbook: decodeAmp(\"")+strlen("addressbook: decodeAmp(\""));
			$this->addurl = substr($this->addurl,0,strpos($this->addurl,"\""));
			return true;
		}else{
			return false;
		}
	}
	//Get the content of address page
	function get_address_page(){
		$response = $this->get_curl($this->addurl,"","",true);
		$sessionid = substr($response,strpos($response,"sessionId = \"")+strlen("sessionId = \""));
		$sessionid = substr($sessionid,0,strpos($sessionid,"\";"));
		
		$exporturl = "https://adressbuch.gmx.net/exportcontacts?session=".$sessionid;
		$response = $this->get_curl($exporturl,"","",true);
		
		$exportfrm = substr($response,strpos($response,"<form id=\"exportContacts\"")+strlen("<form id=\"exportContacts\""));
		$exportfrm = substr($exportfrm,0,strpos($exportfrm,"</form>"));
		$hiddenfields = $this->hidden_fileds($exportfrm);
		
		$postfields = $hiddenfields."&export=Exportieren&raw_format=csv_Outlook2003&language=eng";
		$export = "https://adressbuch.gmx.net/exportcontacts";
		$response = $this->get_curl($export,$postfields,$exporturl,true);
		return $response;
	}
            
	//Take out the name and email from gmx csv
	//Parse gmx csv
	function parser($str) {
        list($headers, $body) = explode("\r\n\r\n", $str, 2);
        $contacts = csv2array($body);
        
        foreach($contacts as $contact) {
			$temp_name = "";
			$temp_email = "";
                
            $temp_name = trim($contact["Title"]." ".$contact["First Name"]." ".$contact["Last Name"]);
            $temp_email = trim($contact["E-mail Address"]);
            if(!$this->is_valid_email($temp_email)) {
                $temp_email = trim($contact["E-mail 2 Address"]);
                if(!$this->is_valid_email($temp_email)) {
                    $temp_email = trim($contact["E-mail 3 Address"]);
                }
            }   
            if($this->is_valid_email($temp_email)) {
				$this->email_array[] = $temp_email;
				$this->name_array[] = $temp_name;
            }
		}
	}
	
	//Get hidden input fields
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

	//Get http response and content
	function get_curl($url,$postfileds="",$referrer="",$header="",$httph="")
	{
	$agent = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 1.1.4322; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; OfficeLiveConnector.1.3; OfficeLivePatch.0.0)";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_USERAGENT, $agent);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	if($referrer!="")
		curl_setopt($ch, CURLOPT_REFERER, $referrer);

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

	if($code==302)
	{
		$n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
		$n_url=substr($n_url,0,strpos($n_url,"\n"));
		if(strpos($n_url,"http://")===FALSE && strpos($n_url,"https://")===FALSE){
			$n_url = "http://service.gmx.net/de/cgi/".$n_url;
		}
		$result=$result."<br>".$this->get_curl(trim($n_url),"","",true);
		
	}
	
	return $result;
}
	//Generate random name	
	function getRand(){
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
    
    function is_valid_email($email) {
        $email = trim($email);
        if (!preg_match("/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i", $email)) {
            return false;
        }
        return true;
    }
	
}
?>