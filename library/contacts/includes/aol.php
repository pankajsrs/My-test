<?php
/**
 * @(#) aol.php
 * @author Sumon sumon@improsys.com
 * @history
 *          created  : Sumon : Date : 05-30-2005
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

class ContactImporter{

  function ContactImporter($username, $password) {

        $this->_username =& $username;
        $this->_password =& $password;
		$this->uid="";
		$this->domain="";
		$this->version="";
		$this->name_array=array();
		$this->email_array=array();
		$this->tstamp="";
		$this->hdredirect="";
        $this->ckbox = new CookieContainer();
		// PUT your Server IP here
		$this->ip = array("Your_IP","Your_IP","Your_IP","Your_IP");
		// Enable line no 216
        $this->ip_in_use = $this->ip[mt_rand(0, 2)];

		}
    function getRand(){
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
		$this->_username=substr($this->_username,0,strpos($this->_username,"@"));
        
		$LOGINURL = "http://webmail.aol.com";
		$result = $this->get_curl($LOGINURL);
		
		if(!(strpos($result,'<iframe title="Screen Name Container"')===false))
		{
			$nextUrl=substr($result,strpos($result,'id="snsModule" src="')+strlen('id="snsModule" src="'));
			$nextUrl=substr($nextUrl,0,strpos($nextUrl,'"'));
			$result = $this->get_curl($nextUrl);
		}
		$this->version=substr($result,strpos($result,'Version=')+strlen("Version="));
		$this->version=substr($this->version,0,strpos($this->version,";"));
        
		$this->domain=substr($result,strpos($result,'domain=')+strlen("domain="));
		$this->domain=substr($this->domain,0,strpos($this->domain,";"));
        
		list($mm,$dd,$yyyy)=explode("-",gmdate("n-j-Y"));
		$this->tstamp=mktime(0,0,0,$mm,$dd,$yyyy);
		$this->tstamp+=(14*24*60*60);
	
		$value=substr($result,strpos($result,'usrd" value="')+strlen("usrd\" value=\""));
		$value=substr($value,0,strpos($value,'"'));
        
		$result=substr($result,strpos($result,'<form name="AOLLoginForm"'));
		$result=substr($result,0,strpos($result,'</form>'));
        
		$action=substr($result,strpos($result,'action="')+strlen('action="'));
		$action=substr($action,0,strpos($action,'"'));
        
		$hiddenfield = $this->hidden_fileds($result);
		$hiddenfield = str_replace("&tab=","&tab=aol", $hiddenfield);
        
		$POSTFIELDS = $hiddenfield.'&password='.urlencode($this->_password).'&loginId='.$this->_username;

        /*
        $header_array = array(
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: '.strlen($POSTFIELDS)
        );
        $this->ckbox->addCookie( new Cookie("=testcookie;"));
        */

		$result = $this->get_curl($action,$POSTFIELDS,$this->hdredirect /*,$header_array */);
		
		if(strpos($result,'<input type="password" name="password"')===false)
		{
			
			if(strpos($result,'uid')!=false)
			{
				$this->uid=substr($result,strpos($result,'&uid:')+strlen('&uid:'));
				$this->uid=substr($this->uid,0,strpos($this->uid,'&'));
			}	
					
			$hosttocheck=substr($result,strpos($result,"document.write(\"<script language='javascript' src='/")+strlen("document.write(\"<script language='javascript' src='/"));
			
			$this->version=substr($hosttocheck,0,strpos($hosttocheck,"/"));
			$hosttocheck=substr($hosttocheck,0,strpos($hosttocheck,'";'));
				
			if(!(strpos($result,'gPreferredHost = "')===false))		
			{
				$this->domain=substr($result,strpos($result,'gPreferredHost = "')+strlen('gPreferredHost = "'));
				$this->domain=substr($this->domain,0,strpos($this->domain,'";'));
			}
			else
			{
				$this->domain=substr($result,strpos($result,'var gErrorURL = "http://')+strlen('var gErrorURL = "http://'));
				$this->domain=substr($this->domain,0,strpos($this->domain,'/'));
			}
			if(!(strpos($result,'gProtocol = "')===false))
			{
				$protocol = substr($result,strpos($result,'gProtocol = "')+strlen('gProtocol = "'));
				$protocol = substr($protocol,0,strpos($protocol,'";'));
			}
			else
			{
				$protocol ="http";
			}
			
			$this->domain= $protocol."://".$this->domain;
			$url=substr($result,strpos($result,'gSuccessURL = "')+strlen('gSuccessURL = "'));
			$url=substr($url,0,strpos($url,'";'));
			$url=$this->domain.$url;
			$reffer=$url;
			$result=$this->get_curl($url,"","");
			$url=substr($result,strpos($result,'"ipt src=\"')+strlen('"ipt src=\"'));
			$url=substr($url,0,strpos($url,'\"'));
			$result=$this->get_curl($url,"",$reffer);
			if(strpos($result,'"UserUID":"')!=false)
			{
				$this->uid=substr($result,strpos($result,'"UserUID":"')+strlen('"UserUID":"'));
				$this->uid=substr($this->uid,0,strpos($this->uid,'"'));
			}
			return true;
		}
		else
		{
			return false;
		}
	}

    // Get the content of address page
    function get_address_page()
    {
        $action=$this->domain."/".$this->version."/aim/en-us/Lite/Today.aspx?AccessibilityRedirect=true";
        $result = $this->get_curl($action);
        $action=$this->domain."/".$this->version."/aim/en-us/Lite/ContactList.aspx?folder=Inbox&showUserFolders=False";
        $result = $this->get_curl($action);
        $action=$this->domain."/".$this->version."/aim/en-us/Lite/PrintContacts.aspx?user=".$this->uid;

        
        $result = $this->get_curl($action);
        $action=$this->domain."/".$this->version."/aim/en-us/Lite/addresslist-print.aspx?command=all&sort=FirstLastNick&sortDir=Ascending&nameFormat=FirstLastNick&user=".$this->uid;
        $result=substr($result,strpos($result,"listString"));
        $result = $this->get_curl($action);
        return $result;
    }


    function get_curl($url,$postfileds="",$referrer="",$header_array = "")
    {
        $agent = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 1.1.4322; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; OfficeLiveConnector.1.3; OfficeLivePatch.0.0)";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        if(!empty($header_array)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header_array);
        }
        
        if($referrer!="") {
            curl_setopt($ch, CURLOPT_REFERER, $referrer);
        }

        // ---------------[modified to manage cookie]--------------------------
        $cookie = $this->ckbox->getCookieString($url);
        if (!empty($cookie)) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        // echo "<pre>Cookies: ".print_r($this->ckbox, true)."</pre><hr>";

        if($postfileds!="")
        {
            curl_setopt($ch, CURLOPT_POST, 1.1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$postfileds);
        }
        // curl_setopt($ch, CURLOPT_INTERFACE, $this->ip_in_use);
                
        curl_setopt($ch, CURLOPT_HEADER, 1);	
        $result = curl_exec ($ch);
        $code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close ($ch);
        // echo ("url=$url<hr>PostFields=$postfileds<hr>".nl2br(htmlspecialchars($result))."<hr>");
        
        // ---------------[modified to manage cookie]--------------------------
        $body = $result;

        $headers = '';
        if (!empty($body)) list($headers,$body) = explode("\r\n\r\n",$body,2);
        else $body = '';

        $response_header_lines = preg_split("/\r?\n/", $headers);
        // echo "<pre>Response Headers: ".print_r($response_header_lines, true)."</pre>";

        foreach($response_header_lines as $r_index) {
            if (preg_match('/Set-Cookie\\s*:\\s*(.*)/ims',$r_index, $matches)) {
                $this->ckbox->addCookie( new Cookie($matches[1]));
            }
        }
        // echo "<pre>Set-Cookies: ".print_r($this->ckbox, true)."</pre><hr>";
        // -----------------------------------------

        if($code==302)
        {
            $n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
            $n_url=substr($n_url,0,strpos($n_url,"\n"));
            $this->hdredirect = trim($n_url);
            $result=$result."<br>".$this->get_curl(trim($n_url),"","");
            
        }
        
        return $result;
    }
//Take out the name and email from aol address page
//Parse aol address page
    function parser($str)
    {
        $str=substr($str,strpos($str,"</tr>"));
        $contacts=explode('<hr class="contactSeparator">',$str);
        $this->name_array=array();
        $this->email_array=array();
        foreach($contacts as $contacts)
        {
            $temp_name="";
            $temp_email="";
            if(!(strpos($contacts,'<span class="fullName">')===false))
            {
                $temp_name=substr($contacts,strpos($contacts,'<span class="fullName">')+strlen('<span class="fullName">'));
                $temp_name=substr($temp_name,0,strpos($temp_name,"</span>"));
            }
            if(!(strpos($contacts,'Email 1:</span> <span>')===false))
            {
                $temp_email=substr($contacts,strpos($contacts,'Email 1:</span> <span>')+strlen('Email 1:</span> <span>'));
                $temp_email=substr($temp_email,0,strpos($temp_email,"</span>"));
            }
            if($temp_name==""&&$temp_email!="")
            {
                $temp_name=substr($temp_email,0,strpos($temp_email,"@"));
            }
            if($temp_email != "" && !(in_array($temp_email,$this->email_array)))
            {
                $name[]=$temp_name;
                $email[]=$temp_email;
                $this->email_array[]=$temp_email;
                $this->name_array[]=$temp_name;
            }	
        }
    }

    function hidden_fileds($str)
    {
        $post="";
        
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
            $value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
            $value=substr($value,0,strpos($value,"\""));
            $details[$name]=$value;
            
            if($post=="")
                $post= $name."=".urlencode($value);
            else
                $post.="&".$name."=".urlencode($value);
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
