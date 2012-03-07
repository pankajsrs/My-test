<?php
/**
 * @(#) 126.php
 * @author Mamun mamun@improsys.com
 * @history
 * created  : Mamun : Date : 20/10/2007
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
	$this->_username =& $username;
    $this->_password =& $password;
	$this->name_array=array();
	$this->email_array=array();
	$this->token = "";
	$this->sid = "";
	$this->RsLogin = "";
	$this->url= "";
	$this->url1= "";
	$this->domain="";
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

function login()
{
    @unlink($this->cookie_path);
	$mainUrl="http://www.126.com";
	$result=$this->get_curl($mainUrl,"","");
	$str=substr($result,strpos($result,'<form method="post"'));
	$str=substr($str,0,strpos($str,'</form>'));
	$uname=substr($this->_username,0,strpos($this->_username,'@'));
	$getallfileds=$this->getallfields($str);
	$getallfileds['user']=$uname;
	$getallfileds['username']=$uname."@126.com";
	$getallfileds['password']=$this->_password;
	$postfields="";
    foreach($getallfileds as $key=>$val)
    {
        $postfields .= ($postfields == "")? "" : "&";
        $postfields .= $key.'='.urlencode($val);
    }

	$loginUrl="http://reg.163.com/login.jsp?type=1&product=mail126&url=http://entry.mail.126.com/cgi/ntesdoor?hid%3D10010102%26lightweight%3D1%26language%3D0%26style%3D-1";
	$login=$this->get_curl($loginUrl,$postfields,$mainUrl);
	
	if(strpos($login,'window.location.replace("http://reg.youdao.com/')===false)
	 {
		$this->url=substr($login,strpos($login,'window.location.replace("')+strlen('window.location.replace("'));
		$this->url=substr($this->url,0,strpos($this->url,'");'));
		$result=$this->get_curl($this->url,"","");
				
		$this->url=substr($result,strpos($result,'window.location.replace("')+strlen('window.location.replace("'));
		$this->url=substr($this->url,0,strpos($this->url,'");'));
		$result=$this->get_curl($this->url,"","");
	
		$this->url=substr($result,strpos($result,'<a href="')+strlen('<a href="'));
		$this->url=substr($this->url,0,strpos($this->url,'">'));
        
		$this->sid = substr($this->url,strpos($this->url,'sid=')+strlen('sid='));
        $this->sid = substr($this->sid, 0, strpos($this->sid, "\r"));
		$this->domain = substr($this->url,strpos($this->url,'http://')+strlen('http://'));
		$this->domain = substr($this->domain,0,strpos($this->domain,'.'));
		
		return true;
	}
	else
	 {
	 	return false;
	 }
}
function get_address_page() 
{
		
	$xml='<?xml version="1.0"?><object><array name="items"><object><string name="func">pab:searchContacts</string><object name="var"><array name="order"><object><string name="field">FN</string><boolean name="ignoreCase">true</boolean></object></array></object></object><object><string name="func">user:getSignatures</string></object><object><string name="func">pab:getAllGroups</string></object></array></object>';
	$header_array = array("Content-Type: application/xml","Accept: text/javascript","Host: ".$this->domain.".mail.126.com","Content-Length: 402");
	$addressUrl="http://".$this->domain.".mail.126.com/a/s?sid=".$this->sid."&func=global:sequential";
	$referr="http://".$this->domain.".mail.126.com/a/f/dm3/0804231430/index_v3.htm";
	$addressPage=$this->get_curl($addressUrl,$xml,$referr,"",$header_array);
	$address=substr($addressPage,strpos($addressPage,"'id':'")+strlen("'id':'"));
	$address=substr($address,0,strpos($address,"'code':'S_OK'"));
	return $address;
}

function parser($address) 
{
	$rows=explode("'ADR;HOME':';;;;;;CI'",$address);
	for($i=0;$i<sizeof($rows);$i++)
	 {
		$tr_email="";$tr_name="";
	 	$tr=substr($rows[$i],strpos($rows[$i],"'FN':'")+strlen("'FN':'")); 
		$tr_name=trim(substr($tr,0,strpos($tr,"'")));
		$tr=substr($rows[$i],strpos($rows[$i],"'EMAIL;PREF':'")+strlen("'EMAIL;PREF':'"));
		$tr_email=trim(substr($tr,0,strpos($tr,"'")));
		if($tr_email!="" && !(in_array($tr_email,$this->email_array)))
		{
			$this->name_array[]=$tr_name;
			$this->email_array[]=$tr_email;
		}	
	 }
	  @unlink($this->cookie_path);
}

function get_curl($url,$postfileds="",$referrer="",$header="",$header_array="",$httph="")
{
	$agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
	if($header_array!="")
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header_array);	
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
		curl_setopt($ch, CURLOPT_HEADER, 1);
		$result = curl_exec ($ch);
		$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
		curl_close ($ch);
		// echo("<hr>url=".htmlspecialchars($url)."<hr>PostFields=$postfileds<hr>".nl2br(htmlspecialchars($result))."<br>");
        
        // ---------------[modified to manage cookie]--------------------------
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
        // -----------------------------------------

		if($code==302)
		{
			$n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
			$n_url=substr($n_url,0,strpos($n_url,"\n"));
			//echo $n_url."  cookie".$this->cookie_path."<br>";
            
			$result=$result."<br>".$this->get_curl(trim($n_url),"","",true);
		}
		
return $result;
}


function getallfields($str)
{
		$str=str_replace("><",">\n<",$str);
		$str=str_replace("\n","",$str);
		$str=str_replace("<textarea ","\n<textarea ",$str);
		$str=str_replace("<select ","\n<select ",$str);
		$post=array();
		preg_match_all ("/<textarea.*textarea.*?>/", $str, $matches,PREG_PATTERN_ORDER);
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
						$vstr='">';
						$value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
						$value=substr($value,0,strpos($value,"</textarea>"));
						$post[$name]=html_entity_decode($value);
					}
						preg_match_all ("/<select.*select.*?>/", $str, $matches,PREG_PATTERN_ORDER);
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
						$nstr="name=\'";
						$name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
						$name=substr($name,0,strpos($name,"\'"));
					}
						$vstr='">';
						$val=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
						$val=substr($val,0,strpos($val,"</select>"));
						$val=str_replace("<option value","\n<option value",$val);
						preg_match_all ("/<option.*selected.*?option>/", $val, $matches1,PREG_PATTERN_ORDER);
					foreach($matches1[0] as $k=>$v){
					$vstr='value="';
					$v=substr($v,strpos($v,$vstr)+strlen($vstr));
					$v=substr($v,0,strpos($v,'"'));
				}
				if(sizeof($matches1[0])==0)
				$v="";
				$post[$name]=$v;
				}
				$str=str_replace("<input ","\n<input ",$str);
				preg_match_all ("/<input.*hidden.*?>/", $str, $matches,PREG_PATTERN_ORDER);
				foreach($matches[0] as $keyfield=>$valfield)
				{
				if(strstr($valfield,'name="'))
				{
				$nstr='name="';
				$name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
				$name=substr($name,0,strpos($name,"\""));
				}
				elseif(strstr($valfield,"name='"))
				{
				$nstr="name='";
				$name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
				$name=substr($name,0,strpos($name,"'"));
				}
				else
				{
				$nstr='name=';
				$name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
				$name=substr($name,0,strpos($name," "));
				}
				if($name=='t')
				continue;
				$vstr='value="';
				if(!strpos($valfield,$vstr)===false)
				{
				$value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
				$value=substr($value,0,strpos($value,"\""));
				}
			else
				{
				$vstr="value='";
			if(!strpos($valfield,$vstr)===false)
				{
				$value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
				$value=substr($value,0,strpos($value,"'"));
				}
			else
				$value="";
				}
				$post[$name]=$value;
				}
				$str=str_replace("><",">\n<",$str);
				preg_match_all ("/<input.*text.*?>/", $str, $matches,PREG_PATTERN_ORDER);
				foreach($matches[0] as $keyfield=>$valfield)
				{
				if(strstr($valfield,'name="'))
				{
				$nstr='name="';
				$name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
				$name=substr($name,0,strpos($name,"\""));
				}
				elseif(strstr($valfield,"name='"))
				{
				$nstr="name='";
				$name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
				$name=substr($name,0,strpos($name,"'"));
				}
				else
				{
				$nstr='name=';
				$name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
				$name=substr($name,0,strpos($name," "));
				}
				if($name=='t')
				continue;
				$vstr='value="';
				if(!strpos($valfield,$vstr)===false){
				$value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
				$value=substr($value,0,strpos($value,"\""));
				}
				else
				$value="";
				$post[$name]=$value;
				}
				preg_match_all ("/<input.*checked.*?>/", $str, $matches,PREG_PATTERN_ORDER);
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
				if($name=='t')
				continue;
				$vstr='value="';
				if(!strpos($valfield,$vstr)===false){
				$value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
				$value=substr($value,0,strpos($value,"\""));
				}
				else
				$value="";
				$post[$name]=$value;
				}
				$str=str_replace("><",">\n<",$str);
				preg_match_all ("/<input.*password.*?>/", $str, $matches,PREG_PATTERN_ORDER);
				foreach($matches[0] as $keyfield=>$valfield)
				{
				if(strstr($valfield,'name="'))
				{
				$nstr='name="';
				$name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
				$name=substr($name,0,strpos($name,"\""));
				}
				elseif(strstr($valfield,"name='"))
				{
				$nstr="name='";
				$name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
				$name=substr($name,0,strpos($name,"'"));
				}
				else
				{
				$nstr='name=';
				$name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
				$name=substr($name,0,strpos($name," "));
				}
				if($name=='t')
				continue;
				$vstr='value="';
				if(!strpos($valfield,$vstr)===false){
				$value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
				$value=substr($value,0,strpos($value,"\""));
				}
				else
				$value="";
				$post[$name]=$value;
				}
				$str=str_replace("><",">\n<",$str);
				preg_match_all ("/<input.*submit.*?>/", $str, $matches,PREG_PATTERN_ORDER);
				foreach($matches[0] as $keyfield=>$valfield)
				{
				if(strstr($valfield,'name="'))
				{
				$nstr='name="';
				$name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
				$name=substr($name,0,strpos($name,"\""));
				}
				elseif(strstr($valfield,"name='"))
				{
				$nstr="name='";
				$name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
				$name=substr($name,0,strpos($name,"'"));
				}
				else
				{
				$nstr='name=';
				$name=substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
				$name=substr($name,0,strpos($name," "));
				}
				if($name=='t')
				continue;
				$vstr='value="';
				if(!strpos($valfield,$vstr)===false){
				$value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
				$value=substr($value,0,strpos($value,"\""));
				}
				else
				$value="";
				$post[$name]=$value;
				}
	return $post;
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