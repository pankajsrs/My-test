<?php
/**
 * @(#) rambler.php
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
		$this->addressUrl = "";
		$this->referer = "";
		$this->reLastUrl = "";
		$_new_dir = str_replace('\\', '/', getcwd()).'/';
		$this->cookie_path  = $_new_dir."/temp/".$this->getRand().".txt";
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
		$startUrl = "http://mail.rambler.ru";
		$result = $this->get_curl($startUrl,"","");
		
		$loginUrl ="http://id.rambler.ru/script/auth.cgi";
		
		$postStr="url=7&login=".$this->_username."&passw=".urlencode($this->_password)."&submit=%D0%92%D0%BE%D0%B9%D1%82%D0%B8+%D0%B2+%D0%BF%D0%BE%D1%87%D1%82%D1%83&long_session=on&back=http%3A%2F%2Fmail.rambler.ru%2Fmail%2Fstartpage.cgi&domain=rambler.ru";
		$result = $this->get_curl($loginUrl,$postStr,$startUrl,true);
		
		if(strpos($result,$this->_username."@rambler.ru")===false)
		{				
			return false;
		}
		else
		{	$this->addressUrl = substr($result,strpos($result,"id=\"addressbook-link\" href=\"")+strlen("id=\"addressbook-link\" href=\""));
			$this->addressUrl = substr($this->addressUrl,0,strpos($this->addressUrl,"\""));
			return true;	
		}
	}
//Get the content of address page
	function get_address_page()
	{
		$this->addressUrl = "http://newmail.rambler.ru".$this->addressUrl;
		$result = $this->get_curl($this->addressUrl,"",$this->reLastUrl);
		return $result;
		//echo "<br>Url : ".htmlspecialchars($this->addressUrl)."<br>";
	}

//Parse gmail address page
	function parser($str)
	{
		$addTable = substr($str,strpos($str,"<table id=\"mailbox-list\""));
		$addTable = substr($addTable,0,strpos($addTable,"</table>"));
		$rows = explode('<tr>',$addTable);
		for($i=1;$i<count($rows);$i++)
		{
			$name=$email="";
			$colums = explode("</td>",$rows[$i]);
			$name = trim(strip_tags(trim($colums[1])));
			$email = trim(strip_tags(trim($colums[4])));
			if($name == "" && $email!="")
			{
				$name = substr($email,0,strpos($email,"@"));
			}
			if(!(in_array(strip_tags(trim($email)),$this->email_array)))
			{
				if($this->is_valid_email($email))
				{
					$this->name_array[] = $name;
					$this->email_array[] = $email;
				}	
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
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		if($referrer!="")
			curl_setopt($ch, CURLOPT_REFERER, $referrer);
	
        // ---------------[modified to manage cookie]--------------------------
        $cookie = $this->ckbox->getCookieString($url);
        if (!empty($cookie)) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
       	if($postfileds!="")
			{
				curl_setopt($ch, CURLOPT_POST, 1.1);
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