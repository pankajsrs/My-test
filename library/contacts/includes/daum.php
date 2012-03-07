<?php
/**
 * @(#) daum.php
 * @author Mamun mamun@improsys.com
 * @history
 *          created  : Mamun : Date : 18-11-2007
 *			updated  : Mamun : Date : 26-05-2008			
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

    function ContactImporter($user_id,$password)
    {
        $this->_username = & $user_id;
        $this->_password = & $password;
        $this->domain="";	
        $this->name_array = array();
        $this->email_array = array();
        $this->reLastUrl = "";
        $_new_dir = str_replace('\\', '/', getcwd()).'/';
        $this->cookie_path  = $_new_dir."temp/".$this->getRand().".txt";
        $this->filepath = $_new_dir."temp/".$this->getRand().".jpg";
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

    function random()
    {
        $test=mt_rand(65,90);
        $test3=mt_rand(97,122);
        $test1=mt_rand(65,90);
        $test4=mt_rand(48,57);
        $test5=mt_rand(48,57);
        $test6=mt_rand(65,90);
        $test7=mt_rand(10,30);
        $test8=mt_rand(150,259);
        return $test.$test3.$test1.$test4.$test5.$test7.$test8;
    }

    function login()
    {
        @unlink($this->cookie_path);
        $this->_username=substr($this->_username,0,strpos($this->_username,"@"));

        // 1-Get First Login Page http://www.daum.net/
        $homePage = "http://www.daum.net/";
        $result = $this->get_curl($homePage,"","",true);
        $loginForm = substr($result,strpos($result,"<form name=\"loginform\""));
        $loginForm = substr($loginForm,0,strpos($loginForm,"</form>"));
        $post = $this->hidden_array($loginForm);
        $post['id'] = $this->_username;
        $post['pw'] = $this->_password;
        $post['enpw'] = $this->_password;
        $post['login'] = "%EB%A1%9C%EA%B7%B8%EC%9D%B8";
        //$post['y'] = "6";
        $postStr="";
            foreach($post as $key=>$val)
            {
                if($postStr=="")
                    $postStr=$key."=".urlencode(stripslashes($val));
                else
                    $postStr.="&".$key."=".urlencode(stripslashes($val));
            }
        // 2- Post Login Data to Page  to login to gmail
        $loginUrl = substr($loginForm,strpos($loginForm,'action="')+strlen('action="'));
        $loginUrl = substr($loginUrl,0,strpos($loginUrl,'"'));
        $loginUrl = $loginUrl."?dummy".time();
        $result = $this->get_curl($loginUrl,$postStr,$homePage,true);
        if(strpos($result,'<p id="errorMsg">')===false)
            {
                $mailBox = "http://mail.daum.net/hanmail/Goto.daum?url=%2Fhanmail%2FIndex.daum%3Fdummy=-64225891&t__nil_loginbox=mail";
                $result = $this->get_curl($mailBox,"",$homePage,true);
                
                $url = substr($result,strpos($result,"Location: ")+strlen("Location: "));
                $url = substr($url,0,strpos($url,"\n"));
                $this->domain = substr($url,0,strpos($url,"/hanmail"));
                $result = $this->get_curl($url,"","");
                return true;
                
            }	
        else
            {
                @unlink($this->cookie_path);
                return false;
            }	
    }
    //Get the content of address page
    function get_address_page()
    {			
        $addressUrl = $this->domain."/hanmail/Index.daum?frame=addr&_top_hm=w_addr";
        $referr = $this->domain."/hanmail/CommonTab.daum?frame=mail";
        $addressPage = $this->get_curl($addressUrl,"",$referr,true);
        //$addressList = "http://addrbook.daum.net/plus/xmlhttp.do?command=addrListArr";
        $addressList = "http://mail.daum.net/hanmail/mail/GetAddrListFromNote.daum?suggestType=new_01";
        $referr = "http://addrbook.daum.net/plus/web.do";
        $address = $this->get_curl($addressList,"",$referr,true);
        if(strpos($address, "\r\n\r\n") !== FALSE) {
            list($headers, $address) = explode("\r\n\r\n", $address, 2);
        }
        $address = substr($address,strpos($address,'['));
        $address = substr($address,0,strpos($address,']'));
        
        
        return $address;
    }
    //Parse gmail address page	
    function parser($address)
    {
        
        $rows = explode("},",$address);
        for($i=0;$i<sizeof($rows);$i++)
        {
            $tr=substr($rows[$i],strpos($rows[$i],'"name":"')+strlen('"name":"')); 
            $tr_name=trim(substr($tr,0,strpos($tr,'",')));
            $tr_name = str_replace("#","",$tr_name);
            $tr=substr($rows[$i],strpos($rows[$i],'"addr":"')+strlen('"addr":"'));
            $tr_email=trim(substr($tr,0,strpos($tr,'",'))); 
            if($tr_name=="" && $tr_email!="")
            {
                $tr_name = substr($tr_email,0,strpos($tr_email,"@"));
            }
        if($tr_email!="" && !(in_array($tr_email,$this->email_array)))
        {
            $this->name_array[]=$tr_name;
            $this->email_array[]=$tr_email;
        }	
      }
      @unlink($this->cookie_file_path);
    }
    function hidden_array($str) {
        $str=str_replace("><",">\n<",$str);
        //$str=str_replace("\n\">","\">",$str);
        $post=array();
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
        if(strpos($valfield,$vstr)===false)
        {
            $vstr='value=';
            $value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
            $value=substr($value,0,strpos($value,">"));
        }
        else
        {
            $value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
            $value=substr($value,0,strpos($value,"\""));
        }
            $post[$name]=$value;
        }
    return $post;
    }
    
    function get_curl($url,$postfileds="",$referrer="",$header="",$header_array="") {
        $agent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)";
        $code="";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
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
        // if($header!="")
                curl_setopt($ch, CURLOPT_HEADER, 1);
        $result = curl_exec ($ch);
        $st=curl_error ($ch);
        $code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close ($ch);
        //echo ("url=$url<hr>PostFields=$postfileds<hr>Error=".$st."<hr>"."Code=".$code."<hr>".nl2br(htmlspecialchars($result))."<hr>");
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
        if($code==302) {
            $n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
            $n_url=substr($n_url,0,strpos($n_url,"\n"));
            $this->reLastUrl = trim($n_url);
            $result=$result."<br>".$this->get_curl(trim($n_url),"","",true);
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