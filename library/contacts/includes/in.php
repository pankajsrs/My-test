<?php

/**
 * @(#) in.php
 * @author Hasan hasan@improsys.com
 * @history
 *          created  : Hasan : Date : 27-03-2009
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
        $this->editurl ="";
        $this->acurmbox ="";
        $this->live =false;
        $this->sid ="";
		$this->n ="";
		$this->mt ="";
        $this->hash ="";
        $this->mail_server ="";
		$this->reLastUrl = "";
		$this->prarray=array();
		$this->emarray=array();
		$this->name_array=array();
		$this->email_array=array();
		$this->dtls_page=array();
        $_new_dir = str_replace('\\', '/', getcwd()).'/';
		$this->cookie_path  = $_new_dir."/temp/".$this->getRand().".txt";  
		$this->mainUrl="";
		$this->newAccount=false;
        $this->ckbox = new CookieContainer();

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
	@unlink($this->cookie_path);
	$StartUrl = "http://mail.in.com/";
	$this->mail_server = $StartUrl;
	$result = $this->get_curl($StartUrl,"","", true);
	$form = substr($result,strpos($result,"<form name=\"frmloginverify\" method=\"POST\"")+strlen("<form name=\"frmloginverify\" method=\"POST\""));
	$form = substr($form,0,strpos($form,"</form>"));
	$loginUrl = substr($form, strpos($form,"action=\"") + strlen("action=\""), strpos($form,"\" >") - strlen("\" >"));
	$loginUrl = substr($loginUrl,0,strpos($loginUrl,"\" >"));
	$loginUrl = "http://mail.in.com".$loginUrl;
	$strHidden = substr($form, strpos($form,"f_sourceret\" value=\"") + strlen("f_sourceret\" value=\""));
	$strHidden = substr($strHidden,0,strpos($strHidden,"\">"));
	$postStr = "f_sourceret=".$strHidden."&f_id=".$this->_username."&f_pwd=".urlencode($this->_password);
	$result = $this->get_curl($loginUrl,$postStr,$StartUrl, true);
	$n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
	$n_url=substr($n_url,0,strpos($n_url,"\n"));
	$this->reLastUrl = "http://mail.in.com" . trim($n_url);
	$result = $this->get_curl($this->reLastUrl, $this->cookie_path, "", $StartUrl, true);
	$n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
	$n_url=substr($n_url,0,strpos($n_url,"\n"));
	$this->reLastUrl = "http://mail.in.com" . trim($n_url);
	$result = $this->get_curl($this->reLastUrl, $this->cookie_path, "", $StartUrl, true);
	return true;
}

function get_address_page()
{
	$addUrl = $this->mail_server."mails/getcontacts.php";
	$result = $this->get_curl($addUrl,"",$this->reLastUrl, true);
	$strresult = substr($result, strpos($result,"<tr onmouseover=\"this.className='cont_h'") + strlen("<tr onmouseover=\"this.className='cont_h'"));
	$strresult = substr($strresult,0,strpos($strresult,"</table>"));
	return $strresult;
}

function parser($str)
{
	$rows = explode("<tr onmouseover=\"this.className='cont_h'\"",$str);
	for($i=0;$i<count($rows);$i++)
	{
		$temp_name="";
		$temp_email="";
		
		$temp = substr($rows[$i],strpos($rows[$i],")\" title=\"")+strlen(")\" title=\""));
		$temp = substr($temp,0,strpos($temp,"</td>"));

		$cols = explode("\">",$temp);
		
		$temp_name=$cols[1];
		$temp_email=$cols[0];
		
		if(strpos($temp_email,"@")===false)
			{
				$temp_email = "";
			}
		if($temp_email!="" && $temp_name=="")
		{
			$temp_name=substr($temp_email,strpos($temp_email,"@"));
		}
		if($temp_email!="" && $temp_name!="")
		{
			$this->name_array[] = trim($temp_name);
			$this->email_array[] = trim($temp_email);
		}
	}
	
	  @unlink($this->cookie_path);

}
function get_curl($url,$postfileds="",$referrer="",$header="")
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
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS,$postfileds);
			}
		// if($header!="") {	
				curl_setopt($ch, CURLOPT_HEADER, 1);
		// }	
		$result = curl_exec ($ch);
		$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
		curl_close ($ch);
        
        // echo ("url=$url<hr>PostFields=$postfileds<hr>".nl2br(htmlspecialchars($result))."<hr>");        
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
			//echo $n_url."  cookie".$this->cookie_path."<br>";
			$result=$result."<br>".$this->get_curl(trim($n_url),"","",true);
		}
		return $result;
	}

function hidden_fields($str)
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
				$post="$name"."=".urlencode($value);
			else
				$post.="&".$name."=".urlencode($value);
		}
		echo($post);
		return $post;
	}


    function write_file($content,$filename)
	{
		if (!$handle = fopen($filename, 'w+')) {
			 print "Cannot open file ($filename)";
			 exit;
		}
		if (fwrite($handle, $content) === FALSE) {
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
