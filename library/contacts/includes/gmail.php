<?php
/**
 * @(#) gmail.php
 * @author Sumon sumon@improsys.com
 * @history
 *          created : Sumon : Date : 03-03-2005
 *			modify	: Mamun : Date : 13-12-2007
 *			modify	: Mamun : Date : 15-12-2007
 *			modify	: Mamun : Date : 12-12-2010
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
        $this->_username = $username;
        $this->_password = $password;
        $this->acurmbox="";
        $this->name_array=array();
        $this->email_array=array();
        $this->fmc_array=array();
        $this->ik = "";
        $this->isNew = false;
        $this->debug = false;
        $this->ua = '';
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

    //  Login to Gmail.
    function login()
	{
        $body = '';
        $this->ua = "Google Talk";
        $url = "https://www.google.com/accounts/ClientAuth";
        $postfileds = sprintf("Email=%s&Passwd=%s&PersistentCookie=false&source=googletalk&accountType=HOSTED_OR_GOOGLE&skipvpage=true",
                            rawurlencode( $this->_username ), rawurlencode( $this->_password ));
        $result = $this->get_curl($url, $postfileds);
        if(strpos($result,'Error') !== false || strpos($result,'error') !== false)  return false;
        
        if (!empty($result)) {
            list( ,$body) = explode("\r\n\r\n",$result,2);
        }
        else {
            return false;
        }
        
        $postfileds = str_replace("\n", "&", $body);
        
        $url = "https://www.google.com/accounts/IssueAuthToken";
        $postfileds .= '&service=gaia&Session=true&skipvpage=true';
        $result = $this->get_curl($url, $postfileds);
        
        if (!empty($result)) {
            list( , $body) = explode("\r\n\r\n",$result,2);
        }
        else {
            return false;
        }
        
        $token = trim($body);
        
        $this->ua = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) ; .NET CLR 1.1.4322)";
        $url = 'https://www.google.com/accounts/TokenAuth?auth='.$token.'&service=mail&continue=https%3A%2F%2Fmail.google.com%2Fmail&source=googletalk';
        $resp = $this->get_curl($url, "", "",true);
        
        return true;
	}

    // Get the content of address page
	function get_address_page()
	{
		$reffer = "http://mail.google.com/mail/contacts/ui/ContactManager?js=RAW";
		$addressUrl = "https://mail.google.com/mail/contacts/data/contacts?thumb=true&groups=true&show=ALL&psort=Name&max=100000&out=js&rf=&jsx=true";
		$str=$this->get_curl($addressUrl,"",$reffer);
		return $str;
	}

	function get_curl($url,$postfileds="",$referrer="",$header="",$httph="")
	{
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->ua);

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
        $result = curl_exec ($ch);
		$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
		curl_close ($ch);

        if($this->debug) {
            echo ("url=$url<hr>PostFields=$postfileds<hr>");
            echo "<pre>Cookies: ".print_r($this->ckbox, true)."</pre><hr>";
            echo nl2br(htmlspecialchars($result))."<hr>";
        }
        $body = $result;

        $headers = '';
        if (!empty($body)) list($headers,$body) = explode("\r\n\r\n",$body,2);
        else $body = '';

        $response_header_lines = preg_split("/\r?\n/", $headers);
        
        if($this->debug) {
            echo "<pre>Headers: ".print_r($response_header_lines, true)."</pre><hr>";
        }
        
        foreach($response_header_lines as $r_index) {
            if (preg_match('/Set-Cookie\\s*:\\s*(.*)/ims',$r_index, $matches)) {
                $this->ckbox->addCookie( new Cookie($matches[1]));
            }
        }
        
		if($code==302 ||$code==301)
		{
			$n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
			$n_url=substr($n_url,0,strpos($n_url,"\n"));
            
            $this->ckbox->deleteCookieByName("GAUSR");
            
			//echo $n_url."  cookie".$this->cookie_path."<br>";
			$result=$result."<br>".$this->get_curl(trim($n_url),"","",true);
		}
		return $result;
	}


//Parse gmail address page
	function parser($str)
	{
		$str = substr($str,strpos($str,'"Contacts":[{"Addresses":'));
		$str = substr($str,0,strpos($str,'"Groups":[{"Count":'));
		$lines = explode('"Affinity":',$str);
		array_shift($lines);
		foreach($lines as $str)
		{
            $name = $email = "";
			//Extract email
            
			if(strpos($str,'"Address":"')===false)
			$email = "";
			else
			{
				if(strpos($str,"\"Address\":\"'")===false)
				{
					$val = substr($str,strpos($str,'"Address":"')+strlen('"Address":"'));
					$email = substr($val,0,strpos($val,'"'));
					$email = $this->UnitoChr($email);
				}
				else
				{
					$val = substr($str,strpos($str,"\"Address\":\"'")+strlen("\"Address\":\"'"));
					$email = substr($val,0,strpos($val,"'"));
					$email = $this->UnitoChr($email);
				}
			}
			//Extract name
			if(strpos($str,"\"Name\":")!==FALSE)
				{
					$name = substr($str,strpos($str,"\"Name\":\"")+strlen("\"Name\":\""));
					$name = substr($name,0,strpos($name,"\""));
					$name = $this->UnitoChr($name);
				}
			if(empty($name)){
				if(strpos($str,'"FullName":{"Unstructured":"')===false)
				{
					$name = substr($email,0,strpos($email,'@'));
					$name = $this->UnitoChr($name);
				}
				else
				{
					$val = substr($str,strpos($str,'"FullName":{"Unstructured":"')+strlen('"FullName":{"Unstructured":"'));
					$name = substr($val,0,strpos($val,'"'));
					$name = $this->UnitoChr($name);
				}
			}
			//Extract frequent mailed status
			if(strpos($str,'"Groups":[{"id":"^Freq"}]')===false)
				$fmc = false;
			else
				$fmc = true;
			if($this->is_valid_email($email)) {
				$this->email_array[] = $email;
				$this->name_array[] = str_replace('\\\\','\\',$name);
				$this->fmc_array[] = $fmc;
			}
		}
    }
    
    function is_valid_email($email) {
        $email = trim($email);
        if (!preg_match("/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i", $email)) {
            return false;
        }
        return true;
    }
	function UnitoChr($str){
		$replace_array = array("'",'"','=',"&");
		$find_array = array('\u0027','\u0022','\u003D','\u0026');
		$str = str_replace($find_array,$replace_array,$str);
		return $str;
	}
}

?>