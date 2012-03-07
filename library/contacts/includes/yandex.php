<?php
/**
 * @(#) yandex.php
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
		$this->reLastUrl = "";
		$this->id = "";
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
	
    # Login to get contacts	
	function login()
	{
		$this->_username=substr($this->_username,0,strpos($this->_username,"@"));
		$startUrl = "http://mail.yandex.ru/";
		$result = $this->get_curl($startUrl,"","");
        
        # getting the login form        
		$form = substr($result,strpos($result,"<form")+strlen("<form"));
        $form = substr($form,0,strpos($form,"</form>"));
        
        # getting login URL
        $loginUrl = substr($form,strpos($form,"action=\"")+strlen("action=\""));
		$loginUrl = substr($loginUrl,0,strpos($loginUrl,"\""));
        
		$strHidden = $this->hidden_fields($form);
		$postStr = $strHidden."&login=".$this->_username."&passwd=".urlencode($this->_password);
		$result = $this->get_curl($loginUrl,$postStr,$startUrl,true);
		if(strpos($result,"<input class=\"b-input__text\" id=\"b-domik-password11\" name=\"passwd\" type=\"password\"")===false)
		{
			$result = $this->get_curl($this->reLastUrl,"","");
			$this->id = substr($result,strpos($result,"abook?d=")+strlen("abook?d="));
			$this->id = substr($this->id,0,strpos($this->id,"\""));
			return true;
		}
		else
		{
			return false;
		}
	}
    # Get the content of address page
	function get_address_page()
	{
	
		/*$exportUrl = "http://mail.yandex.ru/classic/abook_export";
		$result = $this->get_curl($exportUrl,"","");
		$export = "http://mail.yandex.ru/classic/action_abook_export";
		$postStr = "tp=1&rus=0&submit=Экспортировать";
		$result = $this->get_curl($export,$postStr,$exportUrl);*/
		$addressUrl = "http://mail.yandex.ru/neo2/handlers/handlers.jsx?_h=abook-contacts";
		$paramString = "_handlers=abook-contacts&all=yes&_locale=en";
		$result = $this->get_curl($addressUrl,$paramString,"http://mail.yandex.ru/neo2/?ncrnd=4322#contacts");
		
		$contacts = substr($result,strpos($result,"<contacts>")+strlen("<contacts>"));
		$contacts = substr($contacts,0,strpos($contacts,"</contacts>"));
			 
		return $contacts;
	}
        
    # Parse gmail address page
	function parser($str)
	{
       $contacts = explode("</contact>",$str);
         foreach($contacts as $contact) {
		 	$Name=$Email=$fname=$mname=$lname=""; 
			$fname = substr($contact,strpos($contact,"first=\"")+strlen("first=\""));
			$fname = substr($fname,0,strpos($fname,"\""));
			
            $mname = substr($contact,strpos($contact,"middle=\"")+strlen("middle=\""));
			$mname = substr($mname,0,strpos($mname,"\""));
			
			$lname = substr($contact,strpos($contact,"last=\"")+strlen("last=\""));
			$lname = substr($lname,0,strpos($lname,"\""));
			
            $Name = trim($fname." ".$mname." ".$lname);
		
			$Email = substr($contact,strpos($contact,"<email")+strlen("<email"));
			$Email = substr($Email,0,strpos($Email,"</email>"));
			$Email = trim(substr($Email,strpos($Email,"\">")+strlen("\">")));
			
			if($Name =="" && $this->is_valid_email($Email)){
				$Name = ucfirst(substr($Email,0,strpos($Email,"@")));
			}
            if($this->is_valid_email($Email)) {
                $this->email_array[]=$Email;
                $this->name_array[] = $Name;
            }
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
							
	function get_curl($url,$postfileds="",$referrer="",$header="")
	{
		$agent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; (R1 1.6); .NET CLR 2.0.50727; .NET CLR 1.1.4322)";
		$code="";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		if($referrer!="")
			curl_setopt($ch, CURLOPT_REFERER, $referrer);
            
        $cookie = $this->ckbox->getCookieString($url);
        if (!empty($cookie)) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
		if($postfileds!="")
			{
				curl_setopt($ch, CURLOPT_POST, 1.1);
				curl_setopt($ch, CURLOPT_POSTFIELDS,$postfileds);
			}
		curl_setopt($ch, CURLOPT_HEADER, 1);
		$result = curl_exec($ch);
		$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
		curl_close ($ch);
		//echo ("url=$url<hr>PostFields=".htmlspecialchars($postfileds)."<hr>".nl2br(htmlspecialchars($result))."<hr>");
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
		if($code==302)
		{
			$n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
			$n_url=substr($n_url,0,strpos($n_url,"\n"));
			$this->reLastUrl = $n_url;
			//echo $n_url."  cookie".$this->cookie_path."<br>";
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