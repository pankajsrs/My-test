<?php

/**
 * @(#) libero.php
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
		$this->mailUrl = "";
		$this->referer = "";
	    $this->ckbox = new CookieContainer();
	}
		
//Login to get contacts	
	function login()
	{
		$startUrl = "https://login.libero.it/?service_id=old_email&ret_url=http%3A%2F%2Fwpop3.libero.it%2Femail.php";
		$result = $this->get_curl($startUrl,"","",true);
         //exit;
		$form = substr($result,strpos($result,"<form action="));
		$form = substr($form,0,strpos($form,"</form"));
		
		$this->dediurl=substr($form,strpos($form,"name=RET_URL value=\"")+strlen("name=RET_URL value=\""));
		$this->dediurl=substr($this->dediurl,strpos($this->dediurl,"http://")+strlen("http://"));
		$this->dediurl=substr($this->dediurl,0,strpos($this->dediurl,"."));
		$postStr = $this->hidden_fields($form);
		
		$username=substr($this->_username,0,strpos($this->_username,"@"));
		
		$this->_username=$username."@libero.it";
		$postStr="LOGINID=".$this->_username."&PASSWORD=".urlencode($this->_password)."&".$postStr;
		$loginUrl = "https://login.libero.it/logincheck.php";
		$result = $this->get_curl($loginUrl,$postStr,$startUrl,true);
		
		if(strpos($result,"Ciao, <B>$username</B>@libero.it"))
		{
			$this->mailUrl = substr($result,0,strpos($result,"Leggi Mail"));
			$this->mailUrl = substr($this->mailUrl,strpos($this->mailUrl,"<A HREF=\"/")+strlen("<A HREF=\"/"));
			$this->mailUrl = substr($this->mailUrl,0,strpos($this->mailUrl,"\""));
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
		$adrUrl="http://".$this->dediurl.".libero.it/".$this->mailUrl;
		$referer="http://".$this->dediurl.".libero.it/email.php";
		$result = $this->get_curl($adrUrl,"",$referer,true);
		//exit;
		$id = substr($this->mailUrl,strpos($this->mailUrl,"?ID=")+strlen("?ID="));
		$id = substr($id,0,strpos($id,"&"));
		$addressUrl = "http://".$this->dediurl.".libero.it/cgi-bin/abook.cgi";
		$referer="http://".$this->dediurl.".libero.it/cgi-bin/toolbar.cgi?ID=".$id;
		$postStr="ID=".$id."&Act_ABook=1&DIRECT=1&Template=&Language=&ab_list_mode=1&C_Folder=";
		$result = $this->get_curl($addressUrl,$postStr,$referer,true);
		
		$form = substr($result,strpos($result,"<form name=\"abookForm\""));
		$form = substr($form,0,strpos($form,"</form>"));
		$strHidden = $this->hidden_fields($form);
		$exportUrl = "http://".$this->dediurl.".libero.it/cgi-bin/abook.cgi";
		$strHidden = str_replace("&SUB_DUMMY=0","&AB_PATTERN=",$strHidden);
		$postStr = $strHidden."&Act_AB_Export=export";
		$result = $this->get_curl($exportUrl,$postStr,$addressUrl);
		//exit;
		$form = substr($result,strpos($result,"<form ACTION=\""));
		$form = substr($form,0,strpos($form,"</form>"));
		$strHidden = $this->hidden_fields($form);
		$strHidden = str_replace("&SUB_DUMMY=0","",$strHidden);
		$strHidden = str_replace("AB_Export_Type=Export_Ldif","AB_Export_Type=Export_Csv",$strHidden);
		$postStr = $strHidden."&Act_AB_Export=1&AB_PATTERN=&exp=";
		$result = $this->get_curl($exportUrl,$postStr,$addressUrl);
		
		$contacts = substr($result,strpos($result,"<a href=\"")+strlen("<a href=\""));
		$contacts = substr($contacts,0,strpos($contacts,"\""));
		$contacts ="http://".$this->dediurl.".libero.it".$contacts;
		$result = $this->get_curl($contacts,"",$addressUrl);
		return $result;
	}

    //Parse libero address page
	function parser($str)
	{
    
        list($headers, $body) = explode("\r\n\r\n", $str, 2);
        $contacts = csv2array($body);
        
         foreach($contacts as $contact) {
            $temp_name = $temp_email = "";
            
            $temp_name = trim($contact["First Name"]." ".$contact["Middle Name"]." ".$contact["Last Name"]." ".$contact["Nickname"]);            
            if(empty($temp_name)) {
                $temp_name = trim($contact["Name"]);
            }
            
            $temp_email = trim($contact["E-mail Address"]);
                
			if($this->is_valid_email($temp_email)) {
				$this->email_array[] = $temp_email;
				$this->name_array[] = $temp_name;
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
		curl_setopt($ch, CURLOPT_HEADER, 1);
		$result = curl_exec ($ch);
		$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
		//echo "_________code:".$code."<br>";
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
			//echo "<br>*************************".$n_url."***************************************<br>";
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