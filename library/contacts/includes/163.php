<?php
/**
 * @(#) 163.php
 * @author Sumon sumon@improsys.com
 * @history
 *          created  : Sumon : Date : 03-03-2005
 *			updated  : Mamun : Date : 11-18-2007
 *			updated  : Iran  : Date : 05-09-2008

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
	$str= $this->get_curl("http://mail.163.com","","");
	$uname=substr($this->_username,0,strpos($this->_username,'@'));
	$LOGINURL = "https://reg.163.com/logins.jsp?type=1&url=http://entry.mail.163.com/coremail/fcg/ntesdoor2?lightweight%3D1%26verifycookie%3D1%26language%3D-1%26style%3D-1";
	$POSTFIELDS = "verifycookie=1&username=".urlencode($uname)."&password=".urlencode($this->_password)."&style=-1&product=mail163&savelogin=&selType=-1&secure=on&%B5%C7%C2%BC%D3%CA%CF%E4=";
    $reffer = "http://mail.163.com";
	$str= $this->get_curl($LOGINURL,$POSTFIELDS,$reffer,true);
	if(strpos($str,$this->_username) === false)
	{
		@unlink($this->cookie_path);
		return false;
	}	
	else
   		{
			if(!strpos($str,"window.location.replace(") === false)
			{
				$next = substr($str,strpos($str,"window.location.replace(\"")+strlen("window.location.replace(\""));
				$next = substr($next,0,strpos($next,"\")"));
				$str= $this->get_curl($next,"","",true);
			}
			$this->domain=substr($this->reLastUrl,strpos($this->reLastUrl,"http://")+strlen("http://"));
			$this->domain=substr($this->domain,0,strpos($this->domain,'/'));
			$this->sid=substr($this->reLastUrl,strpos($this->reLastUrl,"?sid=")+strlen("?sid="));
			return true;
	 	}
 }



 //Get the content of address page
 function get_address_page()
 {

	$addressUrl = "http://".$this->domain."/a/s?sid=".$this->sid."&func=global:sequential";
	$referr = "http://".$this->domain."/a/j/js3/index.jsp?sid=".$this->sid;
	$posStr = '<?xml version="1.0"?><object><array name="items"><object><string name="func">pab:searchContacts</string><object name="var"><array name="order"><object><string name="field">FN</string><boolean name="ignoreCase">true</boolean></object></array></object></object><object><string name="func">user:getSignatures</string></object><object><string name="func">pab:getAllGroups</string></object></array></object>';
	$header_array = array('Accept: text/javascript','Content-Type: application/xml');
	$result=$this->get_curl($addressUrl,$posStr,$referr,"",$header_array);
	return $result;
 }
//Parse address From js 
function parser($str)
 {
 	$str = substr($str,strpos($str,"'var':[")+strlen("'var':["));
	$str = substr($str,0,strpos($str,"}]"));
	
 	$rows = explode("},",$str);
	for($i=0;$i<sizeof($rows);$i++)
	{
		$email = substr($rows[$i],strpos($rows[$i],"'EMAIL;PREF':'")+strlen("'EMAIL;PREF':'"));
		$email = substr($email,0,strpos($email,"'"));
		
		$name = substr($rows[$i],strpos($rows[$i],"'FN':'")+strlen("'FN':'"));
		$name = substr($name,0,strpos($name,"'"));
		
		$this->name_array[]=$name;
		$this->email_array[]=$email;
		
	}
	@unlink($this->cookie_path);
 }

function hidden_fileds($str)
 {
	$post="";
	preg_match_all ("/<input.*hidden.*?>/", $str, $matches,PREG_PATTERN_ORDER);
	foreach($matches[0] as $keyfield=>$valfield) {
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
function get_curl($url,$postfileds="",$referrer="",$header="",$header_array="")
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
		if($header_array!="")
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header_array);
		}
        
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
		// if($header!="")	{	
				curl_setopt($ch, CURLOPT_HEADER, 1);
		// }	
		$result = curl_exec ($ch);
		$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
		curl_close ($ch);
		// echo ("url=$url<hr>PostFields=".htmlspecialchars($postfileds)."<hr>".nl2br(htmlspecialchars($result))."<hr>");
        
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
			$this->reLastUrl = trim($n_url);
			//echo $n_url."  cookie".$this->cookie_path."<br>";
			$result=$result."<br>".$this->get_curl(trim($n_url),"","",true);
		}
		
		return $result;
	}
  
    function write_file($content,$filename) {
        if (!$handle = fopen($filename, 'w+')) {
             print "Cannot open file ($filename)";
             exit;
        }
        if (!fwrite($handle, $content)) {
            print "Cannot write to file ($filename)";
            exit;
        }
        fclose($handle);
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