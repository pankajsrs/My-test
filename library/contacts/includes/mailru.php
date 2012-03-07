<?php
/**
 * @(#) mailru.php
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
		$this->aCurmbox = "";
		$this->si = "";
		$this->getLastUrl = "";
		$this->ckbox = new CookieContainer();
	}
	
//Login to get contacts	
	function login()
	{
		$this->_username=substr($this->_username,0,strpos($this->_username,"@"));
		$startUrl = "http://www.mail.ru/";
		$this->referer = $startUrl;
		$result = $this->get_curl($startUrl,"","");
		$LFrom = substr($result,strpos($result,"<form name=\"Auth\" method=\"post\""));
		$LFrom = substr($LFrom,0,strpos($LFrom,"</form>"));
		$loginUrl = substr($LFrom,strpos($LFrom,"action=\"")+strlen("action=\""));
		$loginUrl = substr($loginUrl,0,strpos($loginUrl,"\""));
		$strHidden = $this->hidden_fields($LFrom);
		$postString = $strHidden."&Login=".$this->_username."&Domain=mail.ru&Password=".urlencode($this->_password);
		$result = $this->get_curl($loginUrl,$postString,$this->referer,true);
		$error = "<input  type=\"password\" name=\"Password\"";
		if(strpos($result,$error))
		{
			@unlink($this->cookie_path);
			return false;
		}
		else
		{
			return true;
		}
		
		
	}
//Get the content of address page
	function get_address_page()
	{
		$parts = parse_url($this->getLastUrl);
		$domain = $parts['scheme'].'://'.$parts['host']."/";
		
		$addressUrl = $domain."cgi-bin/addressbook";
		$result = $this->get_curl($this->addressUrl,"","");
		$exportUrl = $domain."cgi-bin/abexport";
		$result = $this->get_curl($exportUrl,"","");
		
		$expfrm = substr($result,strpos($result,"<form method"));
		$expfrm = substr($expfrm,0,strpos($expfrm,"</form>"));
		
		$export = substr($expfrm,strpos($expfrm,"action=")+strlen("action="));
		$export = substr($export,0,strpos($export,">"));
		$export = $domain."cgi-bin/".$export;
		$poststr = "confirm=1&abtype=4&export=Ёкспортировать";
		$result = $this->get_curl($export,$poststr,$exportUrl);
		// $this->write_file($result,$this->cookie_path);
		return $result;
		//echo "<br>Url====".htmlspecialchars($this->addressUrl)."<br>";
	}

    // Parse mail.ru address page
    function parser($str)
    {

        list($headers, $body) = explode("\r\n\r\n", $str, 2);
        $contacts = csv2array($body);
        
        foreach($contacts as $contact) {
            $name = $email = "";
            $name = trim($contact["First Name"]." ".$contact["Middle Name"]." ".$contact["Last Name"]);
            if(empty($name)) {
                $name = $contact["Nickname"];
            }
            $email = trim($contact["E-mail Address"]);
            
            if($this->is_valid_email($email)) {
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
				
	function get_curl($url,$postfileds="",$referrer="")
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
            
        // ---------------[modified to manage cookie]--------------------------
        $cookie = $this->ckbox->getCookieString($url);
        if (!empty($cookie)) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        //-------------------------------------------
        /*
		if($cookie_path!="")
			{
				touch($cookie_path);
				curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_path);
				curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_path);
			}
        */
		if($postfileds!="")
			{
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$postfileds);
			}
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
			//echo $n_url."  cookie".$this->cookie_path."<br>";
			$this->getLastUrl = $n_url;
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