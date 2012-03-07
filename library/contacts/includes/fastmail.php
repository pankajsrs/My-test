<?php
/**
 * @(#) fastmail.php
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
		$startUrl = "http://www.fastmail.fm/";
		$this->referer = $startUrl;
		$result = $this->get_curl($startUrl,"","");
		
		$LFrom = substr($result,strpos($result,"<form id=\"memail\""));
		$LFrom = substr($LFrom,0,strpos($LFrom,"</form>"));
		$loginUrl = substr($LFrom,strpos($LFrom,"action=\"")+strlen("action=\""));
		$loginUrl = substr($loginUrl,0,strpos($loginUrl,"\""));
		$postString = "MLS=LN-*&FLN-UserName=".$this->_username."&FLN-Password=".urlencode($this->_password)."&MSignal_LN-AU*=Login&FLN-ScreenSize=-1";
		$result = $this->get_curl($loginUrl,$postString,$this->referer,false);
				
		$error = "<div class=\"errorMessage\">Username not found or password incorrect. Please check that the username and/or password you have entered is correct</div>";
		if(strpos($result,$error)!=FALSE)	{
			return false;
		} else	{
			$nexturl = substr($result,strpos($result,"url=")+strlen("url="));
			$nexturl = substr($nexturl,0,strpos($nexturl,'"'));
			$result = $this->get_curl($nexturl,"",$this->referer,false);
		
			$ust = substr($nexturl,strpos($nexturl,"Ust=")+strlen("Ust="));
			$ust = substr($ust,0,strpos($ust,"&"));

			$this->addressUrl = "mail/?MLS=MB-*;SMB-CF=13448757;UDm=49;Ust=$ust;MSignal=AD-*S-1.N-1";
			$this->addressUrl = $startUrl.$this->addressUrl;
			$this->referer = $nexturl;
			return true;
		}
		
		
	}
//Get the content of address page
	function get_address_page()
	{
		$result = $this->get_curl($this->addressUrl,"",$this->referer);
		return $result;
	}

//Parse gmail address page
	function parser($str)
	{
		if(strpos($str,"<DIV CLASS=\"message\">No addresses</DIV>"))
		{
			
		}
		else
		{
			$addTable = substr($str,strpos($str,"<tbody >"));
			$addTable = substr($addTable,0,strpos($addTable,"</tbody>"));
			$rows = explode("<td class=\"chevron\"></td>",$addTable);
			for($i=1;$i<count($rows);$i++)	{
				$tmpName = $tmpEmail = "";
				$tmpName = substr($rows[$i],strpos($rows[$i],"contactName\">")+strlen("contactName\">"));
				$tmpName = substr($tmpName,0,strpos($tmpName,"</span>"));
				if(strpos($rows[$i],"message to this contact\">")!=FALSE){
					$tmpEmail = substr($rows[$i],strpos($rows[$i],"message to this contact\">")+strlen("message to this contact\">"));
					$tmpEmail = substr($tmpEmail,0,strpos($tmpEmail,"</a>"));
				}
				if($tmpName=="No name") {
					$tmpName=substr($tmpEmail,0,strpos($tmpEmail,"@"));
				}
				if($tmpName!="" && $tmpEmail!="" && $this->is_valid_email($tmpEmail)){
					$this->email_array[] = $tmpEmail;
					$this->name_array[] = $tmpName;
				}
			}
			
		}
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
        //-------------------------------------------

       	if($postfileds!="")
			{
				curl_setopt($ch, CURLOPT_POST, 1.1);
				curl_setopt($ch, CURLOPT_POSTFIELDS,$postfileds);
			}
		//  if($header!="") {	
				curl_setopt($ch, CURLOPT_HEADER, 1);
		//  }	
		$result = curl_exec ($ch);
		$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
		curl_close ($ch);
		//echo ("url=$url<hr>PostFields=$postfileds<hr>".nl2br(htmlspecialchars($result))."<hr>");
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
			$result=$result."<br/>".$this->get_curl(trim($n_url),"","",true);
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