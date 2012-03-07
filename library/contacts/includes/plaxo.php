<?php
/**
 * @(#) plaxo.php
 * @author Sumon sumon@improsys.com
 * @history
 *          created  : Mamun : Date : 08-05-2010
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
        $this->_username =& $username;
        $this->_password =& $password;
		$this->uid="";
		$this->domain="";
		$this->version="";
		$this->name_array=array();
		$this->email_array=array();
		$this->acurmbox="";
		$this->csv_path="";
		$this->ckbox = new CookieContainer();
	}
	function login(){
	
		if(strpos($this->_username,"@plaxo.com")===FALSE){
			$plaxo_user_id=$this->_username;
		}else{
			$plaxo_user_id=substr($this->_username,0,strrpos($this->_username,"@"));
		}
	
		// 1-Get First Login Page http://mail.yahoo.com
		// This page will set some cookies and later we will use these cookies to access inside pages .
		$starturl = "https://www.plaxo.com/signin?ntmp=1";
    	$result=$this->get_curl($starturl);
		$str=substr($result,strpos($result,"<form")+strlen("<form"));
		$str=substr($str,0,strpos($str,"</form>"));
		$strhiddens=$this->hidden_fileds($str);
		$plaxo_user_password=$this->_password;
		$postfields=$strhiddens."&signin.email=".urlencode($plaxo_user_id)."&signin.password=".urlencode(stripslashes($plaxo_user_password));
		///Get Login
		$result=$this->get_curl("https://www.plaxo.com/signin",$postfields,$starturl);

		if(strpos($result,'<input type="password" size=30 id="signin_password" name="signin.password"')===false){
		
			if(strpos($result,'.location.replace("')===FALSE){
				return false;
			}else{
				$home = substr($result,strpos($result,".location.replace('")+strlen(".location.replace('"));
				$home = substr($home,0,strpos($home,"');"));
				$home = trim($home);
				$result=$this->get_curl($home,"","https://www.plaxo.com/signin");
				return true;
			}
		
		}else{
			return false;
		}	
	}
	function get_address_page(){
		$exportUrl = "https://www.plaxo.com/export";
		$result = $this->get_curl($exportUrl);
		$str = substr($result,strpos($result,"<form name=form")+strlen("<form name=form"));
		$str = substr($str,0,strpos($str,"</form>"));
		$action = substr($str,strpos($str,"action=\"")+strlen("action=\""));
		$action = substr($action,0,strpos($action,"\""));
		$strhidden = $this->hidden_fileds($str);
		$export = "https://www.plaxo.com".$action;
		$postfields = $strhidden."&paths.0.checked=checked&type=E";
		$result=$this->get_curl($export,$postfields,"");
		return $result;
	}
	//Take out the name and email from gmx csv
	function parser($str) {
        list($headers, $body) = explode("\r\n\r\n", $str, 2);
        $contacts = csv2array($body);
        foreach($contacts as $contact) {
			$temp_name = "";
			$temp_email = "";
                
            $temp_name = trim($contact["Title"]." ".$contact["First Name"]." ".$contact["Middle Name"]." ".$contact["Last Name"]);
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
	
	function is_valid_email($email) {
        $email = trim($email);
        if (!preg_match("/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i", $email)) {
            return false;
        }
        return true;
    }
	
	//Get the hidden fields of a page
	function hidden_fileds($str)
	{
		$post="";
		$str=str_replace("<input","\n<input",$str);
		preg_match_all("/<input.*hidden.*?>/", $str, $matches,PREG_PATTERN_ORDER);
				
		foreach($matches[0] as $keyfield=>$valfield)
		{
			if(strstr($valfield,'name="'))
			{
				$nstr='name="';
				$name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
				$name=substr($name,0,strpos($name,"\""));
			}elseif(strstr($valfield,'name=')){
				$nstr='name=';
				$name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
				$name=substr($name,0,strpos($name," "));
			}
			if(strstr($valfield,'value="')){
				$vstr='value="';
				$value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
				$value=substr($value,0,strpos($value,'"'));
			}elseif(strstr($valfield,'value=')){
				$vstr='value=';
				$value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
				$value=substr($value,0,strpos($value,">"));
			}
			//$details[$name]=$value;
            if($post=="")
				$post = $name."=".$value;
			else
				$post .= "&".$name."=".$value;
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
    
	//echo "<pre>Headers: ".print_r($response_header_lines, true)."</pre>";

    foreach($response_header_lines as $r_index) {
        if (preg_match('/Set-Cookie\\s*:\\s*(.*)/ims',$r_index, $matches)) {
            $this->ckbox->addCookie( new Cookie($matches[1]));
        }
    }
    //echo "<pre>Cookies: ".print_r($this->ckbox, true)."</pre><hr>";
    /*------------------------------------------*/

	if($code==302)
	{
		$n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
		$n_url=substr($n_url,0,strpos($n_url,"\n"));
		//echo $n_url."  cookie".$this->cookie_path."<br>";
		$this->hdredirect = trim($n_url);
		$result=$result."<br>".$this->get_curl(trim($n_url),"","",true);
		
	}
	
	return $result;
}

}
 
?>