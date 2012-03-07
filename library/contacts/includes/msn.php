<?php
include("XMLParser.php");
class ContactImporter{

  function ContactImporter($username, $password) 
  {
        $this->_username =& $username;
        $this->_password =& $password;
		$this->name_array=array();
		$this->email_array=array();
        $this->token = "";

  }

  function login()
  {
  		   $soapEnvelope = '<s:Envelope
			xmlns:s = "http://www.w3.org/2003/05/soap-envelope"
			xmlns:wsse = "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"
			xmlns:saml = "urn:oasis:names:tc:SAML:1.0:assertion"
			xmlns:wsp = "http://schemas.xmlsoap.org/ws/2004/09/policy"
			xmlns:wsu = "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd"
			xmlns:wsa = "http://www.w3.org/2005/08/addressing"
			xmlns:wssc ="http://schemas.xmlsoap.org/ws/2005/02/sc"
			xmlns:wst = "http://schemas.xmlsoap.org/ws/2005/02/trust">
			<s:Header>
				<wlid:ClientInfo xmlns:wlid ="http://schemas.microsoft.com/wlid">
					<wlid:ApplicationID>10</wlid:ApplicationID>
				</wlid:ClientInfo>
				<wsa:Action s:mustUnderstand = "1">http://schemas.xmlsoap.org/ws/2005/02/trust/RST/Issue</wsa:Action>
				<wsa:To s:mustUnderstand = "1">https://dev.login.live.com/wstlogin.srf</wsa:To>
				<wsse:Security>
					<wsse:UsernameToken wsu:Id = "user">
						<wsse:Username>'.$this->_username.'</wsse:Username>
						<wsse:Password>'.$this->_password.'</wsse:Password>
					</wsse:UsernameToken>
				</wsse:Security>
			</s:Header>
			<s:Body>
				<wst:RequestSecurityToken Id = "RST0">
					<wst:RequestType>http://schemas.xmlsoap.org/ws/2005/02/trust/Issue</wst:RequestType>
					<wsp:AppliesTo>
						<wsa:EndpointReference>
							<wsa:Address>http://live.com</wsa:Address>
						</wsa:EndpointReference>
					</wsp:AppliesTo>
					<wsp:PolicyReference URI = "MBI"></wsp:PolicyReference>
				</wst:RequestSecurityToken>
			</s:Body>
			</s:Envelope>';

	    $url = "https://dev.login.live.com/wstlogin.srf";
	    $header_array = array('Content-Type: application/soap+xml; charset=UTF-8');
	
	    $result=$this->get_curl($url,$soapEnvelope,$header_array);
	    $data=$result['data'];
	    $this->token=substr($data,strpos($data,'<wsse:BinarySecurityToken Id="Compact0">')+42);
	    $this->token=substr($this->token,0,strpos($this->token,'</wsse:BinarySecurityToken>'));

		if($this->token!="")
		    return true;
	    else
		    return false;
  }

  function get_address_page()
  {
	    $url="https://cumulus.services.live.com/".$this->_username."/LiveContacts";

	    $header_array=array('Authorization : WLID1.0 t="'.urlencode($this->token).'"');

	    $result=$this->get_curl($url,"",$header_array);

	    return $result['data'];
  }

  function get_curl($url,$postfileds="",$header_array="")
	{
		$curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
		if($header_array!="")
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header_array);
					
       /* if ($header) 
		curl_setopt($curl, CURLOPT_HEADER, 1);*/
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        if($postfileds!="")
   		{
   			curl_setopt($curl, CURLOPT_POST, 1.1);
   			curl_setopt($curl, CURLOPT_POSTFIELDS,$postfileds);
   		}
		$data = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
        curl_close($curl);
		if($http_code==302)
		{
			$n_url=substr($result,strpos($result,"Location: ")+strlen("Location: "));
			$n_url=substr($n_url,0,strpos($n_url,"\n"));
			//echo $n_url."  cookie".$this->cookie_path."<br>";
			$result=$result."<br>".$this->get_curl(trim($n_url),$this->cookie_path,"","",true);
		}
		$result['code']= $http_code;
		$result['data']= $data;
		echo ("url=".htmlspecialchars($url)."<hr>PostFields=$postfileds<hr>".nl2br(htmlspecialchars($result['data']))."<hr>");
		return $result;
		
	}
	
  function parser($str)
  {
	$xml_parser = new xml();
	$contacts = $xml_parser->parse($str);
	for($i=0;$i<sizeof($contacts['email']);$i++)
	{
	  if($contacts['email'][$i]=="" && $contacts['name'][$i]=="")
	  	{
	  		continue;
		}	
		$this->name_array[$i] = $contacts['name'][$i];
		$this->email_array[$i] = $contacts['email'][$i];
	}
	if($this->name_array=="")
	{
		$this->name_array=array(0);
	}
 }

}
?>

 
