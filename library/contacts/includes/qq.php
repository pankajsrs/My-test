<?php
/**
 * @(#) qq.php
 * @author Shuvo shuvo@improsys.com
 * @history
 * created  : Shuvo : Date :22/10/2007 
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


 include_once("csvutils.php");
 include_once("WebUtils.php");
 include_once("JSON.php");


 class ContactImporter
 {

	 function ContactImporter($user_id,$password,$capture="",$p="",$starttime="", $csvn="",$pgv_info="",$pgv_pvid="",$tinfo="",$cem="",$r_cookie="",$ppp="")
	 	{
			$this->_username = & $user_id;
			$this->password = & $password;
			$this->pgv_info=$pgv_info;
			$this->pgv_pvid=$pgv_pvid;
			$this->tinfo=$tinfo;
			$this->cem=$cem;
			$this->r_cookie=$r_cookie;
			$this->sid="";
			$this->refreshUrl="";
			$this->randurl= "";
			$this->postArray="";
			$this->capture= & $capture;
	        $this->domain="";	
			$this->name_array = array();
			$this->email_array = array();
			$this->referer = "";
			$this->reLastUrl = "";
			$_new_dir = str_replace('\\', '/', getcwd()).'/';
			$this->cookie_path  = $_new_dir."temp/".$this->getRand().".txt";
			$this->filepath = "temp/".$this->getRand().".jpg";
			if($csvn === "") {
				$this->csvfilepath = "temp/tmp_".$this->getRand().".csv";
			} else {
				$this->csvfilepath = $csvn;
			}
            $this->ckbox = new CookieContainer();

	 	}
		
	function getcsvpath(){
		return $this->csvfilepath;
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

    function get_capture()
		{
			$csv = new CsvUtils($this->csvfilepath);
			$url="https://mail.qq.com/cgi-bin/loginpage?";
			$result=$this->get_curl($url);
								
			$str=substr($result,strpos($result,'<form name="form1"'));
			$str=substr($str,0,strpos($str,"</form"));
			$getts=substr($str,strpos($str,'name="ts"')+strlen('name="ts"'));
			$getts=substr($getts,strpos($getts,'value="')+strlen('value="'));
			$getts=substr($getts,0,strpos($getts,'"'));
			$csv->csv_set_value("ts", $getts);
			//save ts to use in capture_submit form in index.php;
			
			$this->randurl=substr($str,strpos($str,'action="')+strlen('action="'));
			$this->randurl=substr($this->randurl,0,strpos($this->randurl,'"'));
		
			$csv->csv_set_value("rnadurl", $this->randurl);
			$csv->csv_set_value("postarr", $str);
			
			$imgUrl=substr($result,strpos($result,"S('vfcode').src = '")+strlen("S('vfcode').src = '"));
			$imgUrl=substr($imgUrl,0,strpos($imgUrl,"&'"));
			$imgUrl = $imgUrl."&0.".$this->random();
			$result=$this->get_curl($imgUrl,"","https://mail.qq.com/cgi-bin/loginpage?");
            list($headers,$body) = explode("\r\n\r\n",$result,2);
            
			$this->write_file($body, $this->filepath);
            $ck = serialize($this->ckbox);
			
			$csv->csv_set_value("cookie", $ck);
			$csv->csv_set_value("imgpath", $this->filepath);
			$csv->csv_set_value("uname", $this->_username);
			$str_csv = $csv->csv_set_value("pass", $this->password);
			$csv->csv_write_file($str_csv);
			
		return $this->filepath;
		}
        
		function login() {
			$csv = new CsvUtils($this->csvfilepath);
			$ck = $csv->csv_get_value("cookie");
            $this->ckbox = unserialize($ck);
            
			$randurl = $csv->csv_get_value("rnadurl");
			$str=$csv->csv_get_value("postarr");
			$postArray=$this->hidden_fileds($str);
					
			/*if($_POST['ppp']!=""){
				$postArray['p']=$_POST['ppp'];
			}else{
				$postArray['p']=$_POST['p'];
			}*/
			$postArray['p'] = $csv->csv_get_value("pass");
			$postArray['uin'] = $csv->csv_get_value("uname");
			$postArray['pp']=$_POST['pp'];
			$postArray['ts']=$_POST['ts'];
			$postArray['starttime']=$_POST['starttime'];
			unset($postArray['\"checkisWebLogin\"']);
			unset($postArray['ppp']);
			$postArray['ppp']=$_POST['ppp'];
			$postfields="";
			
			foreach($postArray as $key=>$val)
			{	
				if($postfields=="")
					$postfields=$key."=".urlencode($val);
				else
					$postfields.="&".$key."=".urlencode($val);
			}
					
			$cookies = "$this->cem; $this->pgv_info; $this->pgv_pvid; pgv_flv=10.0; ";
			$dayofweek = date("w")*3;
			$postfields.="&checkisWebLogin=".$dayofweek."&aliastype=@qq.com&verifycode=".$this->capture;
			$loginurl=$randurl;
			$result=$this->get_curl($loginurl,$postfields,"https://mail.qq.com/cgi-bin/loginpage");
		
			if(strpos($result,"errmsg")!==false)
			{
				$error = substr($result,strpos($result,'errmsg : "')+strlen('errmsg : "'));
				$error = substr($result,0,strpos($result,'"'));
				
				if(strpos($error,"errtype=3")===FALSE && strpos($error,"errtype=2")===FALSE){
					$this->filepath = $csv->csv_get_value("imgpath");
					@unlink($this->csvfilepath);
					@unlink($this->filepath);
					return false;
					
					 
				}else{
					$this->filepath = $csv->csv_get_value("imgpath");
					@unlink($this->csvfilepath);
					@unlink($this->filepath);
					return $this->get_capture();
					
				}	
			}
			else
			{
				if(strpos($result,'?sid=')===FALSE){
					return false;
				}else{
					$this->domain=substr($result,strpos($result,'var urlHead="')+strlen('var urlHead="'));
					$this->domain=substr($this->domain,0,strpos($this->domain,"\";"));
					
					$this->sid=substr($result,strpos($result,'?sid=')+strlen('?sid='));
					$this->sid=substr($this->sid,0,strpos($this->sid,'";'));
					return true;
				}
			}
		}


	function get_address_page()
	{
		$csv = new CsvUtils($this->csvfilepath);
		$csvUrl=$this->domain."addr_export?sid=".urlencode($this->sid);
		$result = $this->get_curl($csvUrl);
		$this->filepath = $csv->csv_get_value("imgpath");
		@unlink($this->csvfilepath);
		@unlink($this->filepath);
		return $result;
	}

	function parser($result) {
        list($headers, $body) = explode("\r\n\r\n", $result, 2);
		if(strpos($headers,"filename=address.csv")===FALSE){	
			$this->name_array[]="";
			$this->email_array[]="";
		}else{
			$contacts = csv2array($body, true);
			foreach($contacts as $contact) {
			 
				if($this->is_valid_email($contact[1])) {
					$this->name_array[]=$contact[0];
					$this->email_array[]=$contact[1];
				}
			}
		}
		@unlink($this->csvfilepath);
	}

	function get_curl($url,$postfileds="",$referrer="",$header=true,$cookies="",$header_array="")
	{
		$agent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; BTRS28621; .NET CLR 2.0.50215; .NET CLR 2.0.50727; .NET CLR 3.0.04506.648; .NET CLR 3.5.21022; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 1.1.4322)";
	    $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		if($referrer!=""){
			curl_setopt($ch, CURLOPT_REFERER, $referrer);
		}	
		if($header_array!=""){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header_array);
		}
        // ---------------[modified to manage cookie]--------------------------
        $cookie = $this->ckbox->getCookieString($url);
		if($cookies!="") {
            $cookie .= "; ".$cookies;
		}
        if (!empty($cookie)) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        //-------------------------------------------
       	if($postfileds!=""){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$postfileds);
		}
		if($header == true){
			curl_setopt($ch, CURLOPT_HEADER, 1);
        }
		/*if($debug!="")
		{*/
			//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
			//curl_setopt($ch, CURLOPT_PROXY, "localhost:8888");
		/*}*/
		$result = curl_exec ($ch);
		$st=curl_error ($ch);
		$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
		curl_close ($ch);
		echo ("url=$url<hr>Error=".$st."<hr>"."Code=".$code."<hr>PostFields=$postfileds<hr>".nl2br(htmlspecialchars($result))."<hr>");
        /*---------------[modified to manage cookie]--------------------------*/
        $body = $result;

        $headers = '';
        if (!empty($body)) {
            list($headers,$body) = explode("\r\n\r\n",$body,2);
        } else { 
            $body = ''; 
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

	function write_file($content,$filename)
	{
		if (!$handle = fopen($filename, 'w+'))
		{
			 print "Cannot open file ($filename)";
			 exit;
		}
		// Write $somecontent to our opened file.
		if (!fwrite($handle, $content)) 
		{
			//print "Cannot write to file ($filename)";
			exit;
		}
		fclose($handle);
	}


	function hidden_fileds($str)
	{
		$post=array();
		$str=str_replace("> <","><",$str);
		preg_match_all ('/<input.*?hidden.*?>/i', $str, $matches,PREG_PATTERN_ORDER);
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
			if(strstr($valfield,'value="'))
			{
				$vstr='value="';
				$value=substr($valfield,strpos($valfield,$vstr)+strlen($vstr));
				$value=substr($value,0,strpos($value,"\""));
			}
			else
			{
				$value="";
			}
			$post[$name]=$value;
		
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