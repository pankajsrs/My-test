<?php
/**
 * @(#) lycos.php
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
include_once("Json2.php");

class ContactImporter {
	function ContactImporter($username, $password) {
		$this->_username = & $username;
		$this->_password = & $password;
		$this->name_array = array();
		$this->email_array = array();
		$this->addressUrl = "";
		$this->referer = "";
		$this->reLastUrl = "";
		$this->sessionid = "";
		$this->authtoken = "";
		$this->domain = "";
		$this->debug = true;
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
		$startUrl = "http://www.mail.lycos.com";
		$result = $this->get_curl($startUrl,"","");
		$form = substr($result,strpos($result,"<form method=\"post\" action=\"")+strlen("<form method=\"post\" action=\""));
		$loginUrl = substr($form,0,strpos($form,"\""));
		$loginUrl = $startUrl.$loginUrl;
		$form = substr($form,0,strpos($form,"</form>"));
		$strHidden = $this->hidden_fields($form);
		$postStr = $strHidden."&m_U=".$this->_username."&m_P=".urlencode($this->_password)."&login=Log+into+Lycos+Mail";
		$result = $this->get_curl($loginUrl,$postStr,$startUrl);
		if(strpos($result,"<input type='text' id='m_U' name='m_U'")===false)
		{
			$this->sessionid = substr($result,strpos($result,'epwd=')+strlen('epwd='));
			$this->sessionid = substr($this->sessionid,0,strpos($this->sessionid,'"'));
			
			return true;
		}
		else
		{
			return false;
		}
	}
    //Get the content of address page
	function get_address_page()
	{
    	$reff = "";
        $export = "https://webmail.lycos.com/webmail/driver?nimlet=login&uid=".rawurlencode($this->_username). "%40lycos.com&domainID=lycos.com&epwd=".$this->sessionid."&wtype=t&locale=en";
		$postStr = "";
		$result = $this->get_curl($export,$postStr,$reff,false);
		
		
		$reff = "";
		$export = "https://webmail.lycos.com/webmail/driver?nimlet=getpab";
		$postStr = "";
        $result = $this->get_curl($export,$postStr,$reff,false); 
		
		$result = substr($result,strpos($result,'{"'));
		 
		return $result;
	   	
	}

    //Parse lycos address page
	function parser($str)
	{
        $contacts = $str;
		$json = new Json();
		$contacts = (array)$json->decode($contacts);
		$contacts = (array)$contacts['res'];
		foreach($contacts['contacts'] as $contact) {
			$temp_email = $contact->email; 
			$temp_name = $contact->nickname; 
			if($this->is_valid_email($temp_email)){
				$this->name_array[] = $temp_name;
				$this->email_array[] = $temp_email;
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
				
	function get_curl($url,$postfileds="",$referrer="",$setheader="")
	{
		$agent = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50215; .NET CLR 2.0.50727; .NET CLR 3.0.04506.648; .NET CLR 3.5.21022; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 1.1.4322)";
		$code="";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		if($referrer!="")
			curl_setopt($ch, CURLOPT_REFERER, $referrer);
        if($setheader!="")
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/soap+xml; charset=utf-8'));
		}    
        // ---------------[modified to manage cookie]--------------------------
        $cookie = $this->ckbox->getCookieString($url);
        if (!empty($cookie)) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        //-------------------------------------------
       
		if($postfileds!="")
			{
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS,$postfileds);
			}
		// if($header!="")
			// {	
				curl_setopt($ch, CURLOPT_HEADER, 1);
			// }	
		
		//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
		//curl_setopt($ch, CURLOPT_PROXY, "localhost:8888");	
		
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
        //   echo "<pre>Cookies: ".print_r($this->ckbox, true)."</pre><hr>";
        /*------------------------------------------*/

		if($this->debug) {
		//echo ("url=$url<hr>code=$code<hr>PostFields=$postfileds<hr>".nl2br(htmlspecialchars($result))."<hr>");
		}
				
		if($code==302 || $code==301)
		{
			if(strpos($result,"Location:")!=FALSE){
				$n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
				$n_url=substr($n_url,0,strpos($n_url,"\n"));
				$this->reLastUrl = $n_url;
				$this->domain = substr($this->reLastUrl,0,strpos($this->reLastUrl,'/zimbra'));
				if(strpos($n_url,"http://")===FALSE){
					$n_url = $this->domain.$n_url;
				}
				if(strpos($this->reLastUrl,"authtoken")!==false){
					$this->authtoken = substr($this->reLastUrl,strpos($this->reLastUrl,"authtoken=")+strlen("authtoken="));
					$this->authtoken = trim($this->authtoken);
				}
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