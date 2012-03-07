<?php
include_once("WebUtils.php");
class ContactImporter
{
	function ContactImporter($username, $password)
	{
		$this->_username = & $username;
		$this->_password = & $password;
		$this->name_array = array();
		$this->email_array = array();
		/*$this->addressUrl = "";
		$this->referer = "";
		$this->reLastUrl = "";
		$_new_dir = str_replace('\\', '/', getcwd()).'/';
		$this->cookie_path  = $_new_dir."/temp/".$this->getRand().".txt";*/
        $this->ckbox = new CookieContainer();

	}
	
	/*function getRand()
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
	}*/
	
	function login()
	{
		#$url="http://www.paracalls.com";
		$loginurl="http://www.paracalls.com/index.php?";
		$poostval="action=logval&email=".urlencode($this->_username)."&pass=".urlencode($this->_password)."&keepMe=0";
		$result=$this->get_curl($loginurl,$poostval);
		if(strpos($result,'Logging in ...')===false)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	function get_address_page()
	{
		$addressUrl = "http://www.paracalls.com/contacts.php";
		
		$str=$this->get_curl($addressUrl);
		return $str;
	}
	
	function parser($str)
	{
		$xml=substr($str,strpos($str,'<contacts xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">')+strlen('<contacts xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'));
		$xml=substr($xml,0,strpos($xml,'</contacts>'));
		$contacts=explode('<contact>',$xml);
		foreach($contacts as $contact)
		{
			if($contact=="")
			{
				continue;
			}
			$temp_name=substr($contact,strpos($contact,'<Name><![CDATA[')+strlen('<Name><![CDATA['));
			$temp_name=substr($temp_name,0,strpos($temp_name,']]></Name>'));
			$temp_email=substr($contact,strpos($contact,'<Email><![CDATA[')+strlen('<Email><![CDATA['));
			$temp_email=substr($temp_email,0,strpos($temp_email,']]></Email>'));
			if($this->is_valid_email($temp_email))
			{
				$this->name_array[]=$temp_name;
				$this->email_array[]=$temp_email;
			}
		}
	}
	
	function is_valid_email($email) {
        $email = trim($email);
        if (!preg_match("/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i", $email)) {
            return false;
        }
        return true;
    }
	
	///Function for retrieving web pages
function get_curl($url,$postfileds="",$referrer="",$header="", $httph="")
{
	$agent = "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4) Gecko/20030624 Netscape/7.1 (ax)";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
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
    //-------------------------------------------
    /*
	if($cookie_path!="")
		{
			touch($cookie_path);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_path);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_path);
		} */
	if($postfileds!="")
	    {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$postfileds);
		}
	// if($header!="")
			// {
				curl_setopt($ch, CURLOPT_HEADER, 1);
			// }
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
		$result=$result."<br>".$this->get_curl(trim($n_url),"","");
	}

	return $result;
}

}

/*$c= new ContactImporter("shuvo@improsys.com","shuvo123");

if($c->login())
{
	$c->parser($c->get_address_page());
}*/
?>
