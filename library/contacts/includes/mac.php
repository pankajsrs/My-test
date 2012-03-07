<?php
/**
 * @(#) mac.php
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
		$this->cookie_path  = $_new_dir."temp/".$this->getRand().".txt";
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
	function login() {
		@unlink($this->cookie_path);
		//$this->_username=substr($this->_username,0,strpos($this->_username,"@"));
		$startUrl = "http://www.mac.com/WebObjects/Webmail.woa";
		$result = $this->get_curl($startUrl,"","");
		$form = substr($result,strpos($result,"<form method=\"post\" id=\"CALoginForm\" action=\"")+strlen("<form method=\"post\" id=\"CALoginForm\" action=\""));
		$loginUrl = substr($form,0,strpos($form,"\""));
		$form = substr($form,0,strpos($form,"</form>"));
		$strHidden = $this->hidden_fields($form);
		$postStr = $strHidden."&username=".$this->_username."&password=".urlencode($this->_password);
		$result = $this->get_curl($loginUrl,$postStr,$startUrl,true);
		if(strpos($result,$this->_username)) {
			$this->addressUrl = substr($result,strpos($result,"<img alt=\".Mac Mail\""));
			$this->addressUrl = substr($this->addressUrl,0,strpos($this->addressUrl,"<img alt=\"Address Book\""));
			
			$this->addressUrl = substr($this->addressUrl,strpos($this->addressUrl,"<a href=\"")+strlen("<a href=\""));
			$this->addressUrl = substr($this->addressUrl,0,strpos($this->addressUrl,"\""));
			return true;
		} else{
			@unlink($this->cookie_path);
			return false;	
		}
	}
//Get the content of address page
	function get_address_page()
	{
		$result = $this->get_curl($this->addressUrl,"",$this->reLastUrl);
		$contacts = "http://www.mac.com/WebObjects/AddressBook.woa/wa/ScriptAction/refreshContacts?currentBatchIndex=1&sortField=email";
		$result = $this->get_curl($contacts,"","");
		return $result;
		//echo "<br>Url : ".htmlspecialchars($this->addressUrl)."<br>";
	}

//Parse gmail address page
	function parser($str)
	{
		$pagination = substr($str,strpos($str,"totalBatches\":")+strlen("totalBatches\":"));
		$pagination = substr($pagination,0,strpos($pagination,","));
		for($i=0;$i<$pagination;$i++)
		{
			$contacts = "http://www.mac.com/WebObjects/AddressBook.woa/wa/ScriptAction/refreshContacts?currentBatchIndex=".($i+1)."&sortField=email";
			$str = $this->get_curl($contacts,"","");
			
			$addTable = substr($str,strpos($str,"{\"contactList\":\""));
			$addTable = substr($addTable,0,strpos($addTable,"\"}"));
			$rows = explode('<div class=\"contact sublist\"',$addTable);
			for($j=1;$j<count($rows);$j++)
			{
				$colums = explode('<\/div>',$rows[$j]);
				$tmpName = strip_tags(stripcslashes(trim(($colums[0]))));
				$tmpName = substr($tmpName,strpos($tmpName," ")+strlen(" "));
				$tmpName = str_replace("&nbsp;","",$tmpName);
				$tmpName = ltrim($tmpName);
				
				$tmpEmail = strip_tags(stripcslashes(trim($colums[1])));
				$tmpEmail = substr($tmpEmail,0,strpos($tmpEmail,"("));
				$tmpEmail = str_replace("&nbsp;","",$tmpEmail);
				$tmpEmail = ltrim($tmpEmail);
				if($tmpName=="" && $tmpEmail!="")
				{
					$tmpName = substr($tmpEmail,0,strpos($tmpEmail,"@"));
				}
				else
				{
					$tmpName = $tmpName;
				}
				if($tmpName!="" && $tmpEmail!="")
				{
					$this->email_array[] = $tmpEmail;
					$this->name_array[] = $tmpName;
				}	
			}
		}
		@unlink($this->cookie_path);
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
				if(strstr($valfield,'value="'))
				{
				$vstr='value="';
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
				
	function get_curl($url,$postfileds="",$referrer="",$header="",$header_array="")
	{
		$agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)";
		$code="";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		if($referrer!="")
			curl_setopt($ch, CURLOPT_REFERER, $referrer);
		if($header_array!="")
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/json'));
		}
        
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
				curl_setopt($ch, CURLOPT_POST, 1.1);
				curl_setopt($ch, CURLOPT_POSTFIELDS,$postfileds);
			}
		if($header!="")
			{	
				curl_setopt($ch, CURLOPT_HEADER, 1);
			}	
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
			$n_url=substr($result,strpos($result,"location: ")+strlen("location: "));
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