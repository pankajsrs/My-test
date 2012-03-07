<?php
/**
 * @(#) yahoo.php
 * @author Sumon sumon@improsys.com
 * @history
 *          created  : Sumon : Date : 11-03-2004
 *			updated  : Sumon : Date:  03:10:2005
 *			updated  : Hasan : Date:  13:04:2011
 * @version 3.0
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


//---------**** Functions required to run index.php****------------//

include_once("JSON.php");
include_once("WebUtils.php");

class ContactImporter{

  function ContactImporter($username, $password) {

        $this->_username =& $username;
        $this->_password =& $password;
		$this->name_array=array();
		$this->email_array=array();
        $this->crumb = "";
        $this->totalContact = 0;
        $this->last_url = "";
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
    function login() {
        
		$imgpath = str_replace("\\", "/", getcwd())."/temp/y_img_".time().".jpg";

		$LOGINURL = "https://login.yahoo.com/config/login_verify2?&.src=ym";
		$yahoo=$this->get_curl($LOGINURL,"","");
		$str=substr($yahoo,strpos($yahoo,"<form"));
		$str=substr($str,0,strpos($str,"</form>"));
		$strhiddens=$this->hidden_fileds($str);

		$poststr = $strhiddens."&login=".urlencode($this->_username)."&passwd=".$this->_password;
        $poststr .= "&.save=&passwd_raw=";
        
        $poststr = str_replace(".lang=en", ".lang=en-US", $poststr);
        $poststr = str_replace(".done=http://mail.yahoo.com", ".done=http%3A%2F%2Fmail.yahoo.com", $poststr);
        
        $poststr = str_replace("c=&", "", $poststr);
        $poststr = str_replace("ivt=&", "", $poststr);
        $poststr = str_replace("sg=&", "", $poststr);
        
        $poststr = str_replace(".pd=ym_ver=0", ".pd=ym_ver%3D0%26c%3D%26ivt%3D%26sg%3D", $poststr);
        $poststr = str_replace(".ws=0", ".ws=1", $poststr);
        
        $header_array = array (
            "x-requested-with: XMLHttpRequest",
            "Accept-Encoding: ",
            "Content-Length: ".strlen($poststr)
            );
        
		$yahoo = $this->get_curl("https://login.yahoo.com/config/login",$poststr,$LOGINURL,$header_array);
        
        if(strpos($yahoo, "\r\n\r\n") !== FALSE) {
            if (!empty($yahoo)) list( , $body) = explode("\r\n\r\n", $yahoo, 2);
            else $body = '';
        }
        
        # If no CAPTCHA found
        if(strpos($body, "redirect") !== FALSE) {
            $redir_url = substr($body, strpos($body,'url" : "') + strlen('url" : "'));
            $redir_url = substr($redir_url, 0, strpos($redir_url,"\""));
            
            // $this->get_curl($redir_url);
            return true;
        }
        
        # If CAPTCHA found
        else if(strpos($body, "error") !== FALSE) {
            $url = 'https://login.yahoo.com/captcha/CaptchaWSProxyService.php?'.
            'action=createlazy&initial_view=&.intl=us&.lang=en-US&rnd='.floor(microtime(true)* 1000);
            
            $header_array = array (
                "x-requested-with: XMLHttpRequest",
                "Accept-Encoding: "
                );
            $yahoo = $this->get_curl($url, "", $LOGINURL, $header_array);

            # Get form and prepare post string
            $form = substr($yahoo, strpos($yahoo,'<Turnkey>') + strlen('<Turnkey>'));
            $form = substr($form, 0, strpos($form,"</Turnkey>"));
            $form = html_entity_decode($form);
            $img_post = $this->hidden_fileds($form);
            
            $poststr = str_replace(".cp=0", ".cp=1", $poststr);
            $poststr = str_replace(".save=", ".saveC=", $poststr);
            $poststr .= "&".$img_post."&captchaAnswer=";
            
            
            # Get image URL, pick the image, write into file, resolve capthca
            $img_url = substr($form, strpos($form,'src="') + strlen('src="'));
            $img_url = substr($img_url, 0, strpos($img_url,'"'));
            
			$contents = file_get_contents($img_url);
			$this->writefile($imgpath, $contents);
            
            $captcha_text = $this->captcha($imgpath);
            
            if(!$captcha_text) {
                return false;
            }
            
            $poststr .= $captcha_text;
            
            @unlink($imgpath);
            
            $header_array = array (
                "x-requested-with: XMLHttpRequest",
                "Accept-Encoding: ",
                "Content-Length: ".strlen($poststr)
                );
            
            $yahoo=$this->get_curl("https://login.yahoo.com/config/login",$poststr,$LOGINURL,$header_array);
                
            if(strpos($yahoo, "\r\n\r\n") !== FALSE) {
                if (!empty($yahoo)) list( , $body) = explode("\r\n\r\n", $yahoo, 2);
                else $body = '';
            }
            
            # If no CAPTCHA found
            if(strpos($body, "redirect") !== FALSE) {
                $redir_url = substr($body, strpos($body,'url" : "') + strlen('url" : "'));
                $redir_url = substr($redir_url, 0, strpos($redir_url,"\""));
                
                // $this->get_curl($redir_url);
                return true;
            }
            return false;
        }
    }
    
    function m_login($yahoo_user, $yahoo_pass) {
    
        $LOGINURL = "http://mlogin.yahoo.com/";
        $yahoo = $this->get_curl($LOGINURL);
        
        $str=substr($yahoo,strpos($yahoo,"<form"));

        $url = substr($str, strpos($str,'action="')+strlen('action="'));
        $url = substr($url, 0, strpos($url,'"'));
        
        $str=substr($str,0,strpos($str,"</form>"));
        
        $strhiddens = $this->hidden_fileds($str);
        $poststr=$strhiddens."&id=".urlencode($yahoo_user)."&password=".urlencode($yahoo_pass)."&__submit=Sign+In";
        
        $yahoo = $this->get_curl($url, $poststr, $LOGINURL);
        
        if(strpos($yahoo,'<input type="password"') === false) {
            $logout_url = substr($yahoo,stripos($yahoo,'http://mlogin.yahoo.com/w/login/logout')+strlen('http://mlogin.yahoo.com/w/login/logout'));
			$logout_url = substr($logout_url,0,stripos($logout_url,'">'));
			$logout_url = "http://mlogin.yahoo.com/w/login/logout".urldecode($logout_url);
		    $this->get_curl($logout_url);
			return true;
        }
        else{
            return false;
        }
    }
    
    function get_address_page() {
        # Now use yahoo functionality to export yahoo address as CSV
        
        $url = "http://address.mail.yahoo.com/";
        $result = $this->get_curl($url);

        $this->crumb = substr($result,strpos($result,"dotCrumb:") + strlen("dotCrumb:"));
        $this->crumb = substr($this->crumb,0,strpos($this->crumb,"',"));
        $this->crumb = str_replace(array("'", " "), "", $this->crumb);
        
        $this->totalContact = substr($result,strpos($result,"TotalABContacts = ") + strlen("TotalABContacts = "));
        $this->totalContact = (int)substr($this->totalContact,0,strpos($this->totalContact,";"));

        $result=substr($result,strpos($result,"InitialContacts = ") + strlen("InitialContacts = "));
        $result=substr($result,0,strpos($result,"]"));
        $result .= "]";
        
        return $result;
    }

    # Parse yahoo JSON / CSV
    function parser($str)
    {
        $contacts = $str;
        $total = 0;
        $temp_email = $temp_name = "";
        
        $json = new Services_JSON();
        $contacts = (array)$json->decode($contacts);

        foreach($contacts as $contact) {

            $contact = (array)$contact;
            if(array_key_exists("email", $contact)) {
                $temp_email = $contact['email'];
            }
            if($temp_email == "" && array_key_exists("msgrID", $contact)) {
                $temp_email = ($contact['msgrID'] == "") ? $contact['msgrID'] : $contact['msgrID']."@yahoo.com";
            }
            
            if($this->is_valid_email($temp_email)) {
                $this->email_array[] = $temp_email;
            } else {
                continue;                
            }
            if(array_key_exists("contactName", $contact)) {
                if($contact['contactName'] == "") {
                     $temp_name = substr($temp_email,0,strpos($temp_email,"@"));
                } else {
                    $temp_name = explode(", ", $contact['contactName']);
                    if(count($temp_name) > 1) {
                        $temp_name = $temp_name[1]." ".$temp_name[0];
                    } else {
                        $temp_name = $temp_name[0];
                    }
                    
                }
                $this->name_array[] = $temp_name;
            }
        }
        $total += count($contacts);
        $countLoop = 0;
        $cookie = array("x-requested-with: XMLHttpRequest");
        while($total < $this->totalContact) {

            $url = "http://address.mail.yahoo.com/?_src=&_partner=generic_intl&_crumb=".$this->crumb."&_intl=us&sortfield=1&sortorder=1&setsort=1&filtername=All%20Contacts&bucket=".++$countLoop."&scroll=1&VPC=social_list&.r=".time();
            $result = $this->get_curl($url,"","", $cookie);
            if (!empty($result)) {
                list($headers,$body) = explode("\r\n\r\n", $result, 2);
            }
            $contarr = (array)$json->decode($body);
            $total += (int)$contarr['response']->ResultSet->TotalContacts;
            $contacts = $contarr['response']->ResultSet->Contacts;

            if(!is_array($contacts)) {
                break;
            }
            foreach($contacts as $contact) {
                
                $contact = (array)$contact;
                
                if(array_key_exists("email", $contact)) {
                    $temp_email = $contact['email'];
                }
                if($temp_email == "" && array_key_exists("msgrID", $contact)) {
                    $temp_email = $contact['msgrID'] == ""? "" : $contact['msgrID']."@yahoo.com";
                }
                if($this->is_valid_email($temp_email)) {
                    $this->email_array[] = $temp_email;
                } else {
                    continue;                
                }
                
                if(array_key_exists("contactName", $contact)) {
                    if($contact['contactName'] == "") {
                        $temp_name = substr($temp_email,0,strpos($temp_email,"@"));
                    } else {
                        $temp_name = explode(", ", $contact['contactName']);
                        if(count($temp_name) > 1) {
                            $temp_name = $temp_name[1]." ".$temp_name[0];
                        } else {
                            $temp_name = $temp_name[0];
                        }
                    }
                    $this->name_array[] = $temp_name;
                }
            }        
        }
               
    }
    
    //Get the hidden fields of a page
    function hidden_fileds($str, $array=false) {
    
        $post="";
        $str = str_replace("><",">\n<",$str);
        preg_match_all ("/<input.*hidden.*?>/", $str, $matches,PREG_PATTERN_ORDER);
        foreach($matches[0] as $keyfield=>$valfield) {
            if(strstr($valfield,'name="')) {
                $nstr = 'name="';
                $name = substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
                $name = substr($name,0,strpos($name,"\""));
            }
            else {
                $nstr = 'name=';
                $name = substr($valfield,strpos($valfield,$nstr)+strlen($nstr));
                $name = substr($name,0,strpos($name," "));
            }
            
            $vstr = 'value="';
            $value = substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
            $value = substr($value,0,strpos($value,"\""));
            $details[$name] = $value;
            
            if(!$array) {
                if($post=="") { $post="$name"."=".$value; }
                else { $post.="&".$name."=".$value; }
            }
            else if($array) {
                $post[$name] = $value;
            }
        }
        return $post;
    }
		
    ///Function for retrieving web pages
    function get_curl($url,$postfileds="",$referrer="", $httph=array())
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

        if(!empty($httph)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httph);
        }
        
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
        // echo ("url=".htmlspecialchars($url)."<hr>PostFields=$postfileds<hr>".nl2br(htmlspecialchars($result))."<hr>");
        
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

        $this->last_url = $url;
        if($code==302)
        {
            $n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
            $n_url=substr($n_url,0,strpos($n_url,"\n"));
            $this->last_url = $n_url;
            $result = $result."<im:br>".$this->get_curl(trim($n_url));
        }

        return $result;
    }
	function writefile($filepath="",$data="") {
		touch($filepath);
		$fh = fopen($filepath, 'a') or die(print_r(error_get_last(), true));
		fwrite($fh, $data."\n");
		fclose($fh);
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
        if(count($result) < 3) {
            return false;
        }
		$text = $result[count($result) - 1];
		#- Decaptchaer api
        return $text;
	}

}


?>