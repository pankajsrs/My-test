<?php
/**
 * @(#) web.php
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
		$this->aCurmbox = "";
		$this->si = "";
		$this->sid="";
		$this->server="";
		$this->protocol="";
		$this->homePage = "";
		$this->hostUrl="";
		$this->ckbox = new CookieContainer();
        $this->debug = false;

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
		$startUrl = "http://www.web.de";
		$this->referer = $startUrl;
		$result = $this->get_curl($startUrl,"","");
		
		$loginUrl = "https://login.web.de/intern/login/";
		$postString = "service=freemail&server=https://freemail.web.de&onerror=https://freemail.web.de/msg/temporaer.htm&onfail=https://freemail.web.de/msg/logonfailed.htm&rv_dologon=Login&password=".$this->_password."&username=".$this->_username;
		
		$result = $this->get_curl($loginUrl,$postString,$this->referer,true);
		if(strpos($result,"logonfailed.htm")!=false)
		{
			return false;
		}
		else
		{
			$nexturl = substr($result,strpos($result,'<A HREF="')+strlen('<A HREF="'));
			$nexturl = substr($nexturl,0,strpos($nexturl,'"'));
			$result = $this->get_curl($nexturl,"",$this->referer,true);
			
			$nexturl = substr($result,strpos($result,'Location: ')+strlen('Location: '));
			$nexturl = substr($nexturl,0,strpos($nexturl,'Content-Length:'));
			$result = $this->get_curl(trim($nexturl),"",$this->referer,true);
			
			$this->hostUrl=substr($nexturl,0,strpos($nexturl,'.web.de')+strlen('.web.de'));
			
			
			$nexturl = substr($result,strpos($result,'Location: ')+strlen('Location: '));
			$nexturl = substr($nexturl,0,strpos($nexturl,'Content-Length:'));
			$nexturl=$this->hostUrl.$nexturl;
			$result = $this->get_curl(trim($nexturl),"",$this->referer,true);
			
			$temp = substr($result,strpos($result,'href="/online/frame.htm'));
			$temp = substr($temp,strpos($temp,'href="')+strlen('href="'));
			$temp = substr($temp,0,strpos($temp,'"'));
			
			
			$nexturl=$this->hostUrl.$temp;
			$result = $this->get_curl(trim($nexturl),"",$this->referer,true);
			
			$this->aCurmbox = substr($nexturl,strpos($nexturl,"freemailng")+strlen("freemailng"));
			$this->aCurmbox = substr($this->aCurmbox,0,strpos($this->aCurmbox,"."));
			$this->si = substr($nexturl,strpos($nexturl,"?si=")+strlen("?si="));
			$this->si = substr($this->si,0,strpos($this->si,"&"));
			$this->homePage=$this->hostUrl."/online/startseite/?".$this->si;
			return true;
		}
	}
    // Get the content of address page
	function get_address_page()
	{
		$reff = $this->hostUrl."/online/menu.htm?si=".$this->si;
		$addressUrl = $this->hostUrl."/online/adressbuch/?si=".$this->si;
		$result = $this->get_curl($addressUrl,"",$reff,true);

		$nexturl = substr($result,strpos($result,'Location: ')+strlen('Location: '));
		$nexturl = substr($nexturl,0,strpos($nexturl,"\n"));
		$result = $this->get_curl(trim($nexturl),"",$this->homePage,true);
		
		$nexturl = substr($result,strpos($result,'Location: ')+strlen('Location: '));
		$nexturl = substr($nexturl,0,strpos($nexturl,"\n"));
        $nexturl = str_replace("%(partnerdata)", "register_url={$this->hostUrl}/intern/navigator/register/?si={$this->si}", $nexturl);
		$result = $this->get_curl(trim($nexturl), "", $this->homePage,true);
		
		$sid = substr($result,strpos($result,'session=') + strlen('session='));
		$sid = substr($sid,0,strpos($sid,'"'));
        
        $addressUrl = "https://adressbuch.web.de/exportcontacts";
		$reff = "https://adressbuch.web.de/exportcontacts?session={$sid}";
        $post_str = "what=PERSON&session={$sid}&language=eng&raw_format=csv_OutlookExpress&export=Exportieren";
        
		$result = $this->get_curl($addressUrl,$post_str,$reff);
		
		return $result;
	}

//Parse gmail address page
	function parser($str)
	{
        $csv = csv2array($str, false, ";");

		foreach($csv as $contact_data) {
			$tmpName = $tmpEmail = "";
            
            $tmpName .= isset($contact_data['First Name']) ? $contact_data['First Name'] : "";
            $tmpName .= isset($contact_data['Last Name']) ? " ".$contact_data['Last Name'] : "";
            $tmpName .= isset($contact_data['Nickname']) ? " ".$contact_data['Nickname'] : "";
            
            $tmpEmail .= isset($contact_data['E-mail Address']) ? $contact_data['E-mail Address'] : "";
            
            if(!empty($tmpEmail) && empty($tmpName)) {
                $tmpName = substr($tmpEmail, 0, strpos($tmpEmail,"@"));
            }
            if($this->is_valid_email($tmpEmail)) {
                $this->email_array[] = $tmpEmail;
                $this->name_array[] = $tmpName;
            }	
        }

	}
	
	function get_curl($url,$postfileds="",$referrer="",$header="")
	{
		$agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727; .NET CLR 1.1.4322; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; OfficeLiveConnector.1.3; OfficeLivePatch.0.0)";
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
        //-------------------------------------------
   
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
		$st=curl_error ($ch);
		curl_close ($ch);
        
        if($this->debug) {
            echo ("url=$url<hr>PostFields=$postfileds<hr>Error=".$st."<hr>"."Code=".$code."<hr>".nl2br(htmlspecialchars($result))."<hr>");
        }
        
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
                if(array_key_exists(1, $matches)) {
                    $this->ckbox->addCookie( new Cookie($matches[1]));
                }
            }
        }
        // echo "<pre>Cookies: ".print_r($this->ckbox, true)."</pre><hr>";
        
		/*if($code==302)
		{
			$n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
			$n_url=substr($n_url,0,strpos($n_url,"\n"));
			//echo $n_url."  cookie".$this->cookie_path."<br>";
			$result=$result."<br/>".$this->get_curl(trim($n_url),"","",true);
		}*/
		
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