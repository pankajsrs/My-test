<?php
/**
 * @(#) linkedin.php
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
		if(substr_count($this->_username, '@') > 1) {
            $this->_username=substr($this->_username,0,strrpos($this->_username,"@"));
        }
		$startUrl = "https://www.linkedin.com/secure/login";
		
		$result = $this->get_curl($startUrl);
		$form = substr($result,strpos($result,"<form")+strlen("<form"));
		$form = substr($form,0,strpos($form,"</form>"));
		$strHidden = $this->hidden_fields($form);
	
		$loginUrl = "https://www.linkedin.com/uas/login-submit";
		
		
		$postStr = $strHidden."&session_key=".$this->_username."&session_password=".urlencode($this->_password)."&session_login=&session_rikey=invalid key&session_login.x=0&session_login.y=0";
		
		$startUrl = "https://www.linkedin.com/secure/login";
		
		
		$result = $this->get_curl($loginUrl,$postStr,$startUrl,true);
        if(strpos($result,'<iframe src="https://www.google.com'))
		{
			$url = substr($result,strpos($result,'<iframe src="')+strlen('<iframe src="'));
			$url = substr($url,0,strpos($url,'"'));
			
			$form = substr($result,strpos($result,'<input type="hidden"')+strlen('<input type="hidden"'));
			$form = substr($form,0,strpos($form,"</form>"));
			
			$strHidden = $this->hidden_fields($form);
			
			$loginUrl = substr($result,strpos($result,'<form action="')+strlen('<form action="'));
			$loginUrl = substr($loginUrl,0,strpos($loginUrl,'"'));
			
			$result = $this->get_curl($url);
			$url1 = substr($result,strpos($result,'src="image?c=')+strlen('src="image?c='));
			$url1 = substr($url1,0,strpos($url1,'">'));
			
			$imgpath = str_replace("\\", "/", getcwd())."/temp/y_img_".time().".jpg";
			$url='https://www.google.com/recaptcha/api/image?c='.$url1; 
			$captchaImg = $url;
			$contents = file_get_contents($captchaImg);
			$this->writefile($imgpath, $contents);
			$capthce_text=$this->captcha($imgpath);
			@unlink($imgpath);
			
			$loginUrl = "https://www.linkedin.com/uas/captcha-submit";
			
			$postStr = 'recaptcha_challenge_field='.$url1.'&recaptcha_response_field='.str_replace(" ", "+",$capthce_text).'&origSourceAlias=0_7r5yezRXCiA_H0CRD8sf6DhOjTKUNps5xGTqeX8EEoi&origActionAlias=0_5tNjVJa7nyJTjBEQf9OL_PhOjTKUNps5xGTqeX8EEoi'.'&'.$strHidden.'&trk=guest_home_login';
			$startUrl = "https://www.linkedin.com/uas/login-submit";
			$result = $this->get_curl($loginUrl,$postStr,$startUrl,true);
			if(strpos($result,"Sign Out"))
                {
                    return true;
                }
                else
                {
                    return false;
                }
			}
		
		/* Start New Code */ 
		
				if(strpos($result,"Sign Out"))
                    {
                       return true;
                    }
				else
				{
					return false;
				} 
		/* End New Code */
		
		
	}
//Get the content of address page

	
	function get_address_page()
	{
		$addressUrl = "http://www.linkedin.com/connections?trk=hb_side_cnts";
		$result = $this->get_curl($addressUrl,"",$this->reLastUrl);
		
		$addressUrl = "http://www.linkedin.com/addressBookExport";
		$result = $this->get_curl($addressUrl,"",$addressUrl);
		
		$form = substr($result,strpos($result,'<form action="/addressBookExport"')+strlen('<form action="/addressBookExport"'));
		$form = substr($form,0,strpos($form,"</form>"));
		$strHidden = $this->hidden_fields($form);
		
		$postStr = $strHidden."&outputType=microsoft_outlook&exportNetwork=Export";
		$loginUrl="http://www.linkedin.com/addressBookExport";
		$startUrl="http://www.linkedin.com/addressBookExport";
		$result = $this->get_curl($loginUrl,$postStr,$startUrl,true);
		
		
		/* Captche start */
		    $url = substr($result,strpos($result,'<iframe src="')+strlen('<iframe src="'));
			$url = substr($url,0,strpos($url,'"'));
			
			$form = substr($result,strpos($result,'<input type="hidden"')+strlen('<input type="hidden"'));
			$form = substr($form,0,strpos($form,"</form>"));
			
			$strHidden = $this->hidden_fields($form);
			
			$loginUrl = substr($result,strpos($result,'<form action="')+strlen('<form action="'));
			$loginUrl = substr($loginUrl,0,strpos($loginUrl,'"'));
			
			$result = $this->get_curl($url);
			$url1 = substr($result,strpos($result,'src="image?c=')+strlen('src="image?c='));
			$url1 = substr($url1,0,strpos($url1,'">'));
			
			$strHidden =$strHidden.'&recaptcha_challenge_field='.$url1;
	
			$imgpath = str_replace("\\", "/", getcwd())."/temp/y_img_".time().".jpg";
			$url='https://www.google.com/recaptcha/api/image?c='.$url1; 
			$captchaImg = $url;
			$contents = file_get_contents($captchaImg);
			$this->writefile($imgpath, $contents);
			$capthce_text=$this->captcha($imgpath);
		    $strHidden=str_replace("manual_challenge",$capthce_text,$strHidden); 
			@unlink($imgpath);
		/* Captche End*/
		
		
		$loginUrl="http://www.linkedin.com/addressBookExport";
		$startUrl="http://www.linkedin.com/addressBookExport";
		$result = $this->get_curl($loginUrl,$strHidden,$startUrl,true);
		
		
		$startUrl="http://www.linkedin.com/addressBookExport?exportNetworkRedirect=&outputType=microsoft_outlook";
		$result = $this->get_curl($startUrl);
		if(strpos($result, "\r\n\r\n") !== FALSE) {
            if (!empty($result)) list(,$result) = explode("\r\n\r\n", $result, 2);
            else $result = '';
        }
				
		/* new code end */

		$jsessionid = substr($result,strpos($result,'csrfToken" value="')+strlen('csrfToken" value="'));
		$jsessionid = substr($jsessionid,0,strpos($jsessionid,'"'));
		return $result;
	}
//Parse gmail address page
	function parser($str)
	{
	    
		$rows=csv2array($str);
			
		for($i=0;$i<count($rows);$i++)
		{
		
			$emailAddress="";$firstName="";$lastName="";$tmpEmail="";$tmpFName="";$tmpLName="";$tmpName="";

			$tmpEmail = $rows[$i]['E-mail Address'];
			
			$tmpName = $rows[$i]['First Name'].$rows[$i]['Middle Name'].$rows[$i]['Last Name'];
		
			if($tmpName=="" && $tmpEmail!="")
			{
				$tmpName = substr($tmpEmail,0,strpos($tmpEmail,"@"));
			}
			if($tmpName!="" && $tmpEmail!="")
			{
				$this->email_array[] = $tmpEmail;
				$this->name_array[] = $tmpName;
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

    function get_curl($url,$postfileds="",$referrer="",$header="", $httph="")
    {
        $agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; (R1 1.6); .NET CLR 2.0.50727; .NET CLR 1.1.4322)';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        if($referrer!="") {
            curl_setopt($ch, CURLOPT_REFERER, $referrer);
        }
        if($httph!="") {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httph);
        }
        
        // ---------------[modified to manage cookie]--------------------------
        $cookie = $this->ckbox->getCookieString($url);
        if (!empty($cookie)) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        if($postfileds!="")
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$postfileds);
        }
        curl_setopt($ch, CURLOPT_HEADER, 1);
        
        $result = curl_exec ($ch);
        $code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close ($ch);
		//echo ("url=".htmlspecialchars($url)."<hr>PostFields=$postfileds<hr>".nl2br(htmlspecialchars($result))."<hr>");
        
        /*---------------[modified to manage cookie]--------------------------*/
        $body = $result;
		

        $headers = '';
        if(strpos($body, "\r\n\r\n") !== FALSE) {
            if (!empty($body)) list($headers,$body) = explode("\r\n\r\n", $body, 2);
            else $body = '';
        }

        $response_header_lines = preg_split("/\r?\n/", $headers);
        //echo "<pre>Headers: ".print_r($response_header_lines, true)."</pre>";

        foreach($response_header_lines as $r_index) {
            if (preg_match('/Set-Cookie\\s*:\\s*(.*)/ims',$r_index, $matches)) {
                $this->ckbox->addCookie( new Cookie($matches[1]));
            }
        }
        //echo "<pre>Cookies: ".print_r($this->ckbox, true)."</pre><hr>";
        /*------------------------------------------*/
        $this->last_url = $url;
        if($code==302)
        {
            $n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
            $n_url=substr($n_url,0,strpos($n_url,"\n"));
            $this->last_url = $n_url;
            $result=$result."<br>".$this->get_curl(trim($n_url),"","");
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

	function captcha($imgpath) {
		# Decaptchaer api
		$post = array(
			"function" => "picture2",
			"username" => DCaptcha_User,
			"password" => DCaptcha_Pass,
			"pict_to" => "0",
			"pict_type" => "0",
			"pict" => '@'.$imgpath);

		$result = $this->get_curl(DCaptcha_Host, $post);
		$result = explode('|', $result);
		$text = $result[count($result) - 1];
		#- Decaptchaer api
        return $text;
	}
	function writefile($filepath="",$data="") {
		touch($filepath);
		$fh = fopen($filepath, 'a') or die(print_r(error_get_last(), true));
		fwrite($fh, $data."\n");
		fclose($fh);
	}
   


function getcsvstr($csvstr, $delimiter = ',', $enclosure = '"', $escape = '\\') {
    $arr = explode($delimiter, $csvstr);
    foreach($arr as &$elem) {
        $elem = stripslashes(str_replace('"', '', $elem));
    }
    return $arr;
}

}
?>