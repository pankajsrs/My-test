<?php
/**
 * @(#) wp.php
 * @author Sumon sumon@improsys.com
 * @history
 *          created  : Md.Nuruzzaman Iran : Date : 09-09-2008
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
    $_new_dir = str_replace('\\', '/', getcwd()).'/';
    $this->cookie_path  = $_new_dir."temp/".$this->getRand().".txt";
    $this->fname="";
    $this->domain="";
    $this->sid = "";
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

    function login()
    {
        @unlink($this->cookie_path);
        $str= $this->get_curl("http://poczta.wp.pl/","","");
		$starturl="http://profil.wp.pl/login.html?url=http%3A%2F%2Fpoczta.wp.pl%2Findex.html%3Fflg%3D1&serwis=nowa_poczta_wp&ticaid=16937";
        $str= $this->get_curl($starturl,"","");
        $loginform=substr($str,strpos($str,"<form action="+strlen("<form action=")));
        $loginform=substr($loginform,0,strpos($loginform,"</form>"));
        $postloginfields=$this->hidden_fileds($loginform);
	    $uname=substr($this->_username,0,strpos($this->_username,'@'));
        $postloginfields .="&login_username=".$uname."&login_password=".$this->_password."&serwis=nowa_poczta_wp&subm=Zaloguj";
	    $referer="http://profil.wp.pl/login.html?url=http%3A%2F%2Fpoczta.wp.pl%2Findex.html%3Fflg%3D1&serwis=nowa_poczta_wp&ticaid=16937";
        $loginurl="http://profil.wp.pl/login.html";
        $str= $this->get_curl($loginurl,$postloginfields,$referer,"true");

        if(strpos($str,$uname) === false)
        {
            @unlink($this->cookie_path);
            return false;
        }	
        else
        {
            return true;
        }
    }



 //Get the content of address page
 function get_address_page()
 {
	$addressBookURL="http://ksiazka-adresowa.wp.pl/";
	$str=$this->get_curl($addressBookURL,"","","");
	return $str;
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
				$post="$name"."=".$value;
			else
				$post.="&".$name."=".$value;
		 }
 return $post;
 }

//Parse address page
    function parser($str)
	{
		$addressfield=substr($str,strpos($str,"<tr align=\"center\" style="));
		$addressfield=substr($addressfield,strpos($addressfield,"<input name="));
		$addressfield=substr($addressfield,0,strpos($addressfield,"<tr class=\"navigTD\""));

		$lines = explode("<input name=",$addressfield);
		array_shift($lines);
		
		foreach($lines as $str)
		{
			//Extract email
			if(strpos($str,"480,1);\">")===false)
			$email = "";
			else
			{
					$val = substr($str,strpos($str,"480,1);\">")+strlen("480,1);\">"));
					$email = substr($val,0,strpos($val,"</a>"));
					$email = str_replace('&nbsp;','',$email);
					
			}
			
			
			//Extract name
			$temfullname=substr($str,strpos($str,"<td align=\"left\" height=\"23\">")+strlen("<td align=\"left\" height=\"23\">"));
			$temfullname=substr($temfullname,0,strpos($temfullname,"</td>"));
			$temfullname=str_replace('&nbsp;','',$temfullname);
			
			if($email!="")
			{
				$tempdisplayname=substr($str,strpos($str,"</a></td><td>")+strlen("</a></td><td>"));
				$tempdisplayname=substr($tempdisplayname,0,strpos($tempdisplayname,"</td>"));
				$tempdisplayname=str_replace('&nbsp;','',$tempdisplayname);
			}
			//if email is null then the table containing display name will be "</td><td>"
			else
			{
				$tempdisplayname=substr($str,strpos($str,"</td><td>")+strlen("</td><td>"));
				$tempdisplayname=substr($tempdisplayname,strpos($tempdisplayname,"</td><td>")+strlen("</td><td>"));
				$tempdisplayname=substr($tempdisplayname,0,strpos($tempdisplayname,"</td>"));
				$tempdisplayname=str_replace('&nbsp;','',$tempdisplayname);
			}

			if($temfullname=="")
			{
				if($tempdisplayname=="")
				{
					if($email!="")
					{
					$name=substr($email,0,strpos($email,"@"));
					}
					else
						$name="";
				}
				else
				{
				$name=$tempdisplayname;
				}	
			}
			else
			$name=$temfullname;

			if( ($email!="") || ($name!="") )
			{
			$this->email_array[] = $email;
			$this->name_array[] = $name;
			}
		}	 

		@unlink($this->cookie_path);
		}

function get_curl($url,$postfileds="",$referrer="",$header="")
	{
	$agent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322)";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
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
	curl_close ($ch);
	// echo ("url=$url<hr>PostFields=$postfileds<hr>".nl2br(htmlspecialchars($result))."<hr>");
    /*---------------[modified to manage cookie]--------------------------*/
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
            $this->ckbox->addCookie( new Cookie($matches[1]));
        }
    }
    // echo "<pre>Cookies: ".print_r($this->ckbox, true)."</pre><hr>";
    /*------------------------------------------*/
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