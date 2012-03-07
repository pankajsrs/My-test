<?php

/**
 * @(#) hotmail.php
 * @author Sumon sumon@improsys.com
 * @history
 *          created  : Sumon : Date : 11-03-2004
 *	 last	updated  : Sumon : Date : 01 - 01-2007
 *			updated  : Mamun : Date : 12-18-2007
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
       	$this->mainUrl="";
		$this->ckbox = new CookieContainer();
    }
        
    function login()
    {
		$starttime = time();
        $StartUrl = "http://www.hotmail.com";
        $result = $this->get_curl($StartUrl,"","",true);

        if(!(strpos($this->_username,'@msn')===false))
        {
            $cookies="";
            $setcookies=substr($result,strpos($result,"Set-Cookie: ")+strlen("Set-Cookie: "));
            $cookies=substr($setcookies,0,strpos($setcookies,";")+1);
            $setcookies=substr($setcookies,strpos($setcookies,"Set-Cookie: ")+strlen("Set-Cookie: "));
            $cookies.=substr($setcookies,0,strpos($setcookies,";"));
            $cookies="Cookie: ".$cookies;
        }
        $LoginUrl = substr($result,strpos($result,"var srf_uPost='")+strlen("var srf_uPost='"));
        $LoginUrl = substr($LoginUrl,0,strpos($LoginUrl,"'"));

        if(!(strpos($this->_username,'@msn')===false))
        {
            $LoginUrl=str_replace('https://','https://msnia.',$LoginUrl);
        }
        $ppft=substr($result,strpos($result,'id="i0327" value="')+strlen('id="i0327" value="'));
        $ppft=substr($ppft,0,strpos($ppft,"\""));
        $RsPass = (get_magic_quotes_gpc()) ? stripslashes($this->_password) : $this->_password;
        $RsLogin = (get_magic_quotes_gpc()) ? stripslashes( $this->_username) :  $this->_username;

 
        $POSTFIELDS="PPFT=".urlencode($ppft)."&LoginOptions=3&login={$RsLogin}&passwd={$RsPass}&type=11&NewUser=1&MEST=&PPSX=P&idsbho=1&PwdPad=&sso=&i1=&i2=1&i3=3492&i4=&i12=1&i14=1100&i15=1149";

        $reffer = $StartUrl;
        if(strpos($this->_username,'@msn') !== false) {
            $POSTFIELDS .= "&i13=&i17=";
        }
        $header_array = array('Connection: Keep-Alive');
        
        $result= $this->get_curl($LoginUrl,$POSTFIELDS,$reffer,true,$header_array);
        
        if((strpos($result, 'top.location.replace(self.location.href') !== false)) {
            return false;
        }
        
        return true;
    }

    function get_address_page()
    {
        $addUrl ="https://snt107.mail.live.com/mail/PrintShell.aspx?type=contact&groupId=00000000-0000-0000-0000-000000000000&n=".time();
        $result = $this->get_curl($addUrl);
        
        if(strpos($result, 'window.location.replace("') !== false) {
            $n_url=substr($result,strpos($result,'window.location.replace("')
                                    + strlen('window.location.replace("'));
            $n_url=substr($n_url,0,strpos($n_url,'"'));
            $result = $this->get_curl(trim($n_url));
        }

        return $result;
    }

    function HexToHtml($str)
    {
        return str_replace("&#64;","@",$str);
    }
    
    function parser($str)
    {
        $contact_raw_array = explode('<div class="ContactsPrintPane cPrintContact BorderBottom">', $str);
        array_shift($contact_raw_array);
        
        foreach($contact_raw_array as $contact_raw) 
        {
            $email = $name = $store = '';
            
            # Process e-mail
            if(strpos($contact_raw,'Windows Live ID:</td>') !== false ) 
            {
                $email = $this->parse_between($contact_raw, "Windows Live ID:</td>", "</tr>");
                $email = trim(strip_tags($this->HexToHtml($email)));
            }
            else if (strpos($contact_raw,':</td>') !== false )
            {
                $contact_email_array = explode(':</td>', $contact_raw);
                
                
                foreach( $contact_email_array as $contact_email_string) 
                {
                    $store = $this->parse_between($contact_email_string, '<td class="Value" >', '</td>');
                    if (!empty($store) && strpos($store,'&#64;') !== false )
                    {
                        $email = trim(strip_tags($this->HexToHtml($store)));
                        break;
                    }
                }
            }
            
            # Process name
            if(strpos($contact_raw,':</td>') !== false )
            {
                $temp_name = '';
                
                $contact_name_array = explode(":</td>", $contact_raw);
                
                foreach($contact_name_array as $contact_name_string)
                {
                    $store = $this->parse_between($contact_name_string, '<td class="Value" >', '</td>');
                    if(!empty($store) && strpos($store,'&#64;') === false )
                    {
                        $name = trim(strip_tags($this->HexToHtml($store)));
                        break;
                    }
                    else
                    {
                        $temp_name = trim(strip_tags($this->HexToHtml($store)));
                    }
                }
                if(!$name)
                {
                    $name = $temp_name;
                }
            }
            
            // If name contact e-mail, pick first part before @
            if(strpos($name,'@') !== false ) {
                $name = substr($name, 0, strpos($name, "@"));
            }

            if($this->is_valid_email($email))
            {
                $this->name_array[] = $name;
                $this->email_array[] = $email;
            }
        }
        
    }
    
    function parse_between($input, $start, $end) {
        $sub_str = substr($input, strpos($input,$start) + strlen($start));
        $sub_str = substr($sub_str, 0, strpos($sub_str,$end));
        return $sub_str;
    }

    function get_curl($url,$postfileds="",$referrer="",$header="",$header_array="")
    {
        
        $agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        if($referrer!="")
        {
            curl_setopt($ch, CURLOPT_REFERER, $referrer);
        }
        if($header_array!="")
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header_array);
        }
            
        $cookie = $this->ckbox->getCookieString($url);
        if (!empty($cookie)) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }

        if($postfileds!="")
        {
            curl_setopt($ch, CURLOPT_POST, 1.1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$postfileds);
        }
		//curl_setopt($ch, CURLOPT_INTERFACE, "205.186.129.215");
        curl_setopt($ch, CURLOPT_HEADER, 1);

        //curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
        //curl_setopt($ch, CURLOPT_PROXY, "localhost:8888");
        
        $result = curl_exec ($ch);
        $code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close ($ch);
        // echo("url=$url<hr>PostFields=$postfileds<hr>".nl2br(htmlspecialchars($result))."<hr>");
        
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
        if($code==302 || $code==301) {
            $n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
            $n_url=substr($n_url,0,strpos($n_url,"\n"));
            if(strpos($n_url,"http://")===FALSE){
                $n_url = $this->mail_server.$n_url;
            }
            $this->reLastUrl = trim($n_url);
            $domain=substr($n_url,0,strpos($n_url,'/mail'));
            $result=$result."<im:br>".$this->get_curl(trim($n_url),"","",true);
        }
        return $result;
    }

    function is_valid_email($email) {
        $email = trim($email);
        if (!preg_match("/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i", $email)) {
            return false;
        }
        if($this->_username == $email) {
            return false;
        }
        return true;
    }

}
?>
