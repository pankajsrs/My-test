<?php
/**
 * @(#) sina.php
 * @author Sumon sumon@improsys.com
 * @history
 *          created  : Sumon : Date : 03-03-2005
 *			updated  : Mamun : Date : 11-18-2007
 *			updated  : Iran	 : Date : 05-13-2008

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
include_once("Json2.php");
 
class ContactImporter {
	
 function ContactImporter($username, $password)
 {
	$this->_username =& $username;
    $this->_password =& $password;
	$this->name_array=array();
	$this->email_array=array();
    $this->reffer = "";
	$_new_dir = str_replace('\\', '/', getcwd()).'/';

	$this->acurmbox = "";
	$this->response = "";
    $this->ckbox = new CookieContainer();
    $this->last_url = "";

 }

 
function login() {
    $str= $this->get_curl("http://mail.sina.com.cn/","","");
    $uname=substr($this->_username,0,strpos($this->_username,'@'));
	
	$export = "http://beacon.sina.com.cn/d.gif?&gUid_".floor(microtime(true)* 1000);
	$result = $this->get_curl($export,"",$reff,false); 
		
	$LOGINURL = "http://mail.sina.com.cn/cgi-bin/login.cgi";
	$POSTFIELDS = "u=".urlencode($uname)."&psw=".urlencode($this->_password)."&product=mail&".urlencode("µÇÂ¼")."=".urlencode("µÇ Â¼");
    $this->reffer = "http://mail.sina.com.cn/";
	$str = $this->get_curl($LOGINURL,$POSTFIELDS,$this->reffer,true);
    
    if(strpos($str, '<input name="u"') > 0)	{
        return false;
    } 
    else {
        $this->response = $str;
        $this->acurmbox = substr($str,strpos($str,"Location: http://")+strlen("Location: http://"));
        $this->acurmbox = substr($this->acurmbox,0,strpos($this->acurmbox,".sinamail"));
		
		if(strpos($this->response,'window.parent.location=')!==false)
		{
			$LOGINURL = substr($this->response,strpos($this->response,"window.parent.location='")+strlen("window.parent.location='"));
			$LOGINURL = substr($LOGINURL,0,strpos($LOGINURL,"'"));
			
			$this->response = $this->get_curl($LOGINURL,"","",true);
		}
		return true;
    }
}

 

//Get the content of address page
 function get_address_page()
 {
	if(strpos($this->last_url, "classic") === false) {
        $str="http://".$this->acurmbox.".sinamail.sina.com.cn/cgismarty/addr_export.php";
        $str = $this->get_curl($str,"",$this->reffer);
    }
    else {
        $str = html_entity_decode($this->response);
        $str = substr($str, strpos($str,"contacts:{"));
        $str = substr($str, 0, strpos($str,"\r\n"));
        $str[strlen($str)-1] = '';
    }

	return $str;
 }

//Parse address page
function parser($str) {
    if(strpos($this->last_url,"classic") === false) {
		
        list($headers, $body) = explode("\r\n\r\n", $str, 2);
        $contacts = csv2array($body, true);
        
        foreach($contacts as $contact) {
            if($this->is_valid_email($contact[3])) {
                $this->name_array[]=$contact[0];
                $this->email_array[]=$contact[3];
            }
        }
    }
    else {
		$strs = explode('},{',$str);
		foreach($strs as $str)
		{
			$name = substr($str,strpos($str,'"name":"')+strlen('"name":"'));
			$name = substr($name,0,strpos($name,'","'));
			$email = substr($str,strpos($str,'"email":"')+strlen('"email":"'));
			$email = substr($email,0,strpos($email,'","'));
			if($this->is_valid_email($email)) {
                $this->name_array[]=$name;
                $this->email_array[]=$email;
            }
		}

    }
}


function get_curl($url,$postfileds="",$referrer="",$header="")
 {
	$agent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	if($referrer!="")
		curl_setopt($ch, CURLOPT_REFERER, $referrer);
        
    $cookie = $this->ckbox->getCookieString($url);
    if (!empty($cookie)) {
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    }
        
	if($postfileds!="")
    {
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$postfileds);
    }
    curl_setopt($ch, CURLOPT_HEADER, 1);
		
	$result = curl_exec ($ch);
	$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
	curl_close ($ch);
	//echo("url=$url<hr>PostFields=$postfileds<hr>".nl2br(htmlspecialchars($result))."<hr>");	
    
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
    // echo "<pre>Cookies: ".print_r($this->ckbox, true)."</pre><hr>";
    /*------------------------------------------*/
    $this->last_url = $url;
    
	if($code==302) {
        $n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
        $n_url=substr($n_url,0,strpos($n_url,"\n"));
        
        if (strpos($result,"http") === false) {
            $host = substr($url,strpos($url,"://")+strlen("://"));
            $host = substr($host,0,strpos($host,"/"));
            
            $scheme = substr($url,0,strpos($url,"://"));
            $n_url = $scheme."://".$host.$n_url;
        }
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