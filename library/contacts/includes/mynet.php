<?php
/**
 * @(#) mynet.php
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
		$this->reLastUrl = "";
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
		$startUrl = "http://uyeler.mynet.com/login/?loginRequestingURL=lmail&formname=eposta";
		$result = $this->get_curl($startUrl,"","");
		$form = substr($result,strpos($result,"<form method=post action=\"")+strlen("<form method=post action=\""));
		$loginUrl = substr($form,0,strpos($form,"\""));
		$form = substr($form,0,strpos($form,"</form>"));
		$strHidden = $this->hidden_fields($form);
		$strHidden = str_replace("&rememberstate=","&rememberstate=0",$strHidden);
		$postStr = $strHidden."&username=".$this->_username."&password=".urlencode($this->_password);
		$result = $this->get_curl($loginUrl,$postStr,$startUrl);
		
		if(strpos($result,"<form")===false)
		{
			$url = substr($result,strpos($result,"url=")+strlen("url="));
			$url = substr($url,0,strpos($url,"\">"));
			$result = $this->get_curl($url,"","",true);
			return true;	
		}else{
			return false;	
		}
	}
//Get the content of address page
	function get_address_page()
	{
		$refer = $this->reLastUrl;
		$url = "http://adresdefteri.mynet.com".$this->reLastUrl;
		$exportUrl = "http://adresdefteri.mynet.com/Exim/EximPage.aspx";
		$result =$this->get_curl($exportUrl,"",$url);
		$export = "http://adresdefteri.mynet.com/Exim/ExportFileDownload.aspx";
		$postStr = "format=microsoft_csv";
		$result =$this->get_curl($export,$postStr,$exportUrl);
		return $result;
	}

    // Parse gmail address page
	function parser($str) {
        list($headers, $body) = explode("\r\n\r\n", $str, 2);
        if(strpos($body, "\r\n\r\n") !== FALSE) {
            list($headers, $body) = explode("\r\n\r\n", $body, 2);
        }
        $contacts = csv2array($body);
        foreach($contacts as $contact) {
			$temp_email= trim($contact["E-mail Address"]);
			$temp_name = trim($contact["First Name"]." ".$contact["Last Name"]);
            if(empty($temp_name)) {
                $temp_name = trim($contact["E-mail Display Name"]);
            }

            if($this->is_valid_email($temp_email)) {
                $this->email_array[]=$temp_email;
                $this->name_array[] = $temp_name;
            }
		}
	}
	
	
	function hidden_fields($str)
			{
				$post="";
				$str=str_replace("><",">\n<",$str);
				$str=str_replace("\n","",$str);
				$str=str_replace("<input","\n<input",$str);
				preg_match_all ("/<input.*hidden.*?>/i", $str, $matches,PREG_PATTERN_ORDER);
				foreach($matches[0] as $keyfield=>$valfield)
				{
				if(strstr($valfield,'name='))
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
				$value = trim($value);
				}
				elseif(strstr($valfield,'value='))
				{
				$vstr='value=';
				$value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
				$value=substr(trim($value),0,strpos(trim($value),"/>"));
				$value = trim($value);
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
        //-------------------------------------------
	
              
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
		//echo ("url=$url<hr>PostFields=$postfileds<hr>".nl2br(htmlspecialchars($result))."<hr>");
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