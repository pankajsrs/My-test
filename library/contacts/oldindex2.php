<?php
error_reporting(E_ALL);

session_start();

$domainArray= array("hotmail","yahoo","gmail","aol","msn","live","fastmail","web","mail.com","mail.ru","rediff","indiatimes","lycos","libero","rambler","mac","linkedin","mynet","interia","yandex" ,"126" ,"qq","daum","sina" ,"163","wp","in","ymail","rocketmail","gmx","googlemail");
/*echo "<pre>";
print_r($domainArray);
echo "<pre/>";*/
/*
''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
'   File:	                oldindex2.php
'
'   Description:            This script Login to qq,126 and import address book like birhtdayalarm.com.
'
'   Written by:             Mahbub Hossain Sumon( sumon@improsys.com)
'
'	Updated by:				Md. Elme Focruzzaman Razi (Shuvo) [shuvo@improsys.com]
'
'   Languages:              PHP + CURL(Library)
'
'   Date Written:           July 26, 2004
'
'   Date Updated:           October 22, 2007
'
'   Version:            	V.2.0
'
'   Platform:               Windows 2000 / IIS / Netscape 7.1 / Linux / IE 4+
'
'   Copyright:              Improsys, Inc.
'
''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
*/
set_time_limit(600);///Set time limit for script 
include("includes/grab_globals.lib.php");

$type="";
$domain = "";
/*$test=$_POST["user_id"];
echo $test;*/
if(isset($_POST["user_id"]))
{       if(isset($_POST['webmail']))
            $webmail = $_POST['webmail'];
        else 
            $webmail = "";

        if($webmail=="webmail") 
		{ 
			$user_id=$_POST['user_id'];
			$fulldomain=substr($user_id,strpos($user_id,"@")+strlen("@"));
			//echo $fulldomain;
			if($fulldomain=="qq.com")
			{
			$_SESSION['type']="qq.com";
			$dom="qq.com";
			}
			elseif($fulldomain=="mail.com")
			$type=$fulldomain;
			elseif($fulldomain=="mail.ru")
			$type=$fulldomain;
			else
			 {
			$user_id=substr($user_id,0,strpos($user_id,"@"));
			//echo $user_id;
			$type=substr($fulldomain,0,strpos($fulldomain,"."));
			  }
			$dom=$fulldomain;
			if($dom!="qq.com")
			{
			$_POST['type']="";
			 unset($_SESSION['type']);
	
			}
		}
		
		if($webmail=="linkedin")
		
		{
			$user_id=$_POST['user_id'];
			$type="linkedin";
			$dom="linkedin.com";
			$fulldomain="linkedin.com";
			if($dom!="qq.com")
				{
				$_POST['type']="";
				 unset($_SESSION['type']);
		
				}
		
		
		}
		///echo  "userid:".$user_id."<br/>";
		//echo  "Password:".$_POST['password']."<br/>";	
		//echo "type:".$type;

}
if(isset($_SESSION['type']))
{
if(isset($_POST["type"]))
{
	$type=$_POST["type"];
	$domain = $type;
	$fulldomain="qq.com";
	$dom=$type;
	$type= substr($type,0,strpos($type,"."));
	/* echo  "userid:".$_POST['user_id']."<br/>";
		echo  "Password:".$_POST['password'];	*/
}
}
if(!function_exists('curl_init'))
{
	echo ("CURL Library is not installed. Please install it to let this application execute properly.");
	exit();
}
if(isset($user_id)||isset($_POST['uin']))
{    
    //echo $type;exit;
	if($type=="live" || $type=="msn"){
		include("includes/hotmail.php");
	}
	else if($type=="ymail" || $type=="rocketmail"){   
		include("includes/yahoo.php");
		//echo $type;
	}
	elseif($type=="googlemail"){
		include("includes/gmail.php");
	}
	else if($type=="mail.com"){   
		include("includes/maildotcom.php");
		//echo $type;
	}
	else if($type=="mail.ru"){   
		include("includes/mailru.php");
		//echo $type;
	}
	else if (in_array($type,$domainArray)){
	  //echo $type;
	    if($fulldomain=="linkedin.com")
			{
			
			$fulldomain=substr($user_id,strpos($user_id,"@")+strlen("@"));
			$user_id=substr($user_id,0,strpos($user_id,"@"));
			/*echo $fulldomain."<br/>";
			echo $user_id;
			exit;*/
			}
		include("includes/"."$type.php");
		 //echo $type;
	}
	else
	{
	$mess="The domain name does not exist.";
	}
}
ob_implicit_flush();
?>
<html>
<head>
<title>Hotmail/Yahoo/Gmail Address Book</title>
<script language="JavaScript" type="text/JavaScript">
<!--

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_validateForm() { //v4.0
  var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
  for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
    if (val) { nm=val.id; if ((val=val.value)!="") {
      if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
        if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
      } else if (test!='R') { num = parseFloat(val);
        if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' is required.\n'; }
  } if (errors) alert('The following error(s) occurred:\n'+errors);
  document.MM_returnValue = (errors == '');
}

//-->

<!-- Begin

function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>
<script language="javascript" src="js/loginencription.js"></script>
		<script language="javascript">
				
		
// added by iran
	function valid_capture()

	{
		

				document.capture_submit.starttime.value = (new Date()).valueOf();
				var PublicKey = "CF87D7B4C864F4842F1D337491A48FFF54B73A17300E8E42FA365420393AC0346AE55D8AFAD975DFA175FAF0106CBA81AF1DDE4ACEC284DAC6ED9A0D8FEB1CC070733C58213EFFED46529C54CEA06D774E3CC7E073346AEBD6C66FC973F299EB74738E400B22B1E7CDC54E71AED059D228DFEB5B29C530FF341502AE56DDCFE9";
				var RSA = new RSAKey();
				RSA.setPublic(PublicKey, "10001");
				var PublicTs="1263710994";
				
				var Res = RSA.encrypt(document.capture_submit.pp.value + '\n' + document.capture_submit.ts.value + '\n');
				
				if (Res)
				{
					document.capture_submit.p.value = hex2b64(Res);
				}
				
				var MaskValue = "";
				for (var Loop = 0; Loop < document.capture_submit.pp.value.length; Loop++, MaskValue += "0");
				document.capture_submit.pp.value = MaskValue;		
			
	}
		
//
		
		function frmvalid()
		{
			var error="";
			if(document.getElementById("ct").value=="")
				error="Need to enter capture text";
			if(error!="")
			{
				alert(error);
				return false;
			}
			else 
				return true;
		}
	</script>
	<SCRIPT LANGUAGE="JavaScript">

<!-- This script and many more are available free online at -->
<!-- The JavaScript Source!! http://javascript.internet.com -->

<!-- Begin
function checkEmail(myForm) {
	var error = "";
if(myForm.user_id.value=="")
  {
  	error = "Email Address is required.\n";
  }
if(myForm.password.value=="") 
  {
  error += "Passqord is required.\n";
  }   
if(error){
	alert(error);
	return false;
}else{
if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(myForm.user_id.value)){
	return (true)
}
else
  {
   alert("Valid email address is required.")
 	 return (false)
  }
}
    
	 
}
//  End -->
</script>
<style type="text/css">
<!--
.tdbcolor {
	background-color: #cccccc;
}
.bordcol {
	border:1px solid black;
}
.style4 {color: #FFFFFF}
-->
</style>
</head>
<body>
<?php
	$t="";
    if(isset($dom) && $dom=="qq.com")
    {
        if(isset($_POST["type"]))
        {
            if($_POST["type"]=="qq.com")
            {
                if(!isset($_POST["captext"]))
                    $t=$_POST["type"];
                else
                    $t="";
            }	
        }
    }
	if($t=="")
	{
?>
<div id="pagelink" align="right"><a href="oldindex.php" style=""><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Back to dropdown List</font></a></div>

<div id="loginfrm" style="visibility:visible">
<form name="form1" method="post" action="oldindex2.php" onSubmit="return checkEmail(this)">
<input type="hidden" name="type" value="qq.com">
  <br>
  <table width="44%" border="0" align="center" cellpadding="1" cellspacing="0" bgcolor="#F2F1F0">
    <tr>
      <td width="100%">
	  <table width="100%" border="0" align="center" cellpadding="2" cellspacing="0">
          <tr align="center" bgcolor="#99FFFF"> 
            <td colspan="3"><font color="#006699" size="2" face="Arial, Helvetica, sans-serif"><strong>Grab 
              Address Book</strong></font></td>
          </tr>
          <tr>
            <td width="19%" align="right" valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Email:</font></td>
            <td>
			<div style="float:left"><input name="user_id" type="text" id="email" value="<?php if(isset($_POST['user_id'])) {echo $_POST['user_id'];} ?>" style="width:200px;">
			</div>			</td>
            <td width="32%">
			  <input type="radio" name="webmail" value="webmail" checked="checked" align="right">
              <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Webmail</font>		   
			</td>
          </tr>
       
            
		  
          <tr> 
            <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">              Password:</font></td>
            <td width="49%"><input name="password" type="password" id="Password" style="width:200px;">  </td>
            <td width="32%">  <input type="radio" name="webmail" value="linkedin" align="right" >
              <font size="2" face="Verdana, Arial, Helvetica, sans-serif">LinkedIn</font></td>
          </tr>
          <tr align="center" bgcolor="#99FFFF"> 
            <td colspan="3"><input name="Submit" type="submit"  value="Get address book"></td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>
<div id="mess" align="center"><font size="4" color="#FF0000" face="Verdana, Arial, Helvetica, sans-serif"> <?php if(isset($mess) && $mess== true){ echo $mess;} ?></font></div>
</div>

<br>
<?php
}
	if(isset($_POST['type']))
	{
	if($_POST['type']=="qq.com" && !isset($_POST["captext"]))
	{
		/*
		$imgurl=get_capture();
		$pArray=$_SESSION["postarr"];
		*/
		$user_id=$_POST['user_id'];
		//echo $user_id;
		$user_id=substr($user_id,0,strpos($user_id,"@"));
		
		$imageurl=new ContactImporter($user_id,$_POST["password"]);
		$imgurl=$imageurl->get_capture();
		$csv = new CsvUtils($imageurl->getcsvpath());
		$pArray = $csv->csv_get_value("postarr");
		 $user_id=$csv->csv_get_value("uname");
		 $password=$csv->csv_get_value("pass");
		/* echo  "userid:".$user_id."<br/>";
		echo  "Password:".$password;*/
		//exit;
		//var_dump($pArray);

?>
		
    <div id="cap" style="visibility:hidden">
	<form name="capture_submit" method="post" action="oldindex2.php" onSubmit="return frmvalid()">
  <br>
  <table width="46%" border="0" align="center" cellpadding="1" cellspacing="0" bgcolor="#F2F1F0">
    <tr>
      <td width="100%">
      <table width="100%" border="0" align="center" cellpadding="2" cellspacing="0">
          <tr align="center" bgcolor="#99FFFF"> 
            <td colspan="2"><font color="#006699" size="2" face="Arial, Helvetica, sans-serif"><strong>Verification Text</strong></font></td>
          </tr>
		 
          <tr> 
            <td width="28%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Image Capture:</font></td>
            <td width="72%"><img src="<?php echo $imgurl; ?>"/> </div></td></tr>
			<input type="hidden" name="user_id" value="<?php echo $_POST['user_id']; ?>" /> 
			<input type="hidden" name="pp" value="<?php echo $csv->csv_get_value("pass"); ?>" />
			<input type="hidden" name="ts" value="<?php echo $csv->csv_get_value("ts"); ?>" />
			<input type="hidden" name="p" value="" />
			<input type="hidden" name="csvn" value="<?php echo $imageurl->getcsvpath(); ?>" />
			<input type="hidden" name="starttime" value="" />
			<input type="hidden" name="type" value="<?php echo $_POST["type"]; ?>"/>
			<script language="javascript" type="text/javascript">
			var curDate=new Date();
			var curMs=curDate.getUTCMilliseconds();
			var cem = Math.random();
			var tinfo = "tinfo="+((Math.random()* 2147483647)*curMs);		
			pvidtmp=(Math.round(Math.random()* 2147483647)*curMs)%10000000000;
			ssid="s"+(Math.round(Math.random()* 2147483647)*curMs)%10000000000;
			var pgv_info = "pgv_info=ssid="+ssid;
			var pgv_pvid="pgv_pvid="+pvidtmp; 	
			var r_cookie = "r_cookie="+curDate.getYear()%100+(curDate.getUTCMonth()+1)+curDate.getUTCDate()+curDate.getUTCMilliseconds()+Math.round(Math.random()*100000);
			
			document.write("<input type=\"hidden\" name=\"pgv_info\" value=\"" + pgv_info + "\">");
			document.write("<input type=\"hidden\" name=\"pgv_pvid\" value=\"" + pgv_pvid + "\">");
			document.write("<input type=\"hidden\" name=\"tinfo\" value=\"" + tinfo + "\">");
			document.write("<input type=\"hidden\" name=\"cem\" value=\"" + cem + "\">");
			document.write("<input type=\"hidden\" name=\"r_cookie\" value=\"" + r_cookie + "\">");
			</script>
           
          
          <tr> 
            <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Enter Text:</font></td>
            <td><input type="text" name="captext" id="ct" value=""></td>
          </tr>
          <tr align="center" bgcolor="#99FFFF"> 
            <td colspan="2"> <input type="submit" value="Submit" onClick="return valid_capture()"></td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>
</div>


<?php
	
	if(isset($_POST['user_id']) && isset($_POST['type']))
	{
		
		if($_POST['type']=="qq.com")
		{	
		
		$user_id=$_POST['user_id'];
		//echo $user_id;
		$user_id=substr($user_id,0,strpos($user_id,"@"));
			?>
            <script language="javascript" >
			
			qqSoecial();
			function qqSoecial()
			{	
				var re = /^\d+$/;
				var user="<?php echo $user_id; ?>";
				if(re.test(user)==true)
				{
					document.getElementById("cap").style.visibility="visible";
				//alert("this is numeric");
				}
				else
				{
					document.getElementById("cap").style.visibility="hidden";
				
					valid_capture();
					document.capture_submit.submit();
				
					//alert("this is text");
				}
			
		   }
		</script>
		<?php
		}
	}
?>


<?php
	}

}
if(isset($_POST['user_id'])||isset($_POST['uin']))
{
	//$name_array=array();///global variable contains all name found in address book
	// $logged_in=true on successfull login
	
	if($_POST['type']=="qq.com"  && isset($_POST["captext"]))
	{
        if(!array_key_exists("ppp", $_POST)) {
            $_POST['ppp'] = "";
        }
		
        $user_id=$_POST['user_id'];
		$user_id=substr($user_id,0,strpos($user_id,"@"));
		$contacts=new ContactImporter($user_id,$_POST["pp"],$_POST["captext"],$_POST["p"],$_POST["starttime"], $_POST["csvn"],$_POST['ppp']);
		
		$csv = new CsvUtils($contacts->getcsvpath());
		$pArray = $csv->csv_get_value("postarr");
		$logged_in=$contacts->login();
		
	}
	//elseif(isset($_POST['user_id'])&&!isset($_POST["captext"])) 
	
	
	
	
	elseif(!isset($_POST["captext"]) && $dom!="qq.com") 
	{
	//echo  "type".$type;
	//$contacts=new ContactImporter($_POST['user_id']."@".$_POST['type'].".com",$_POST['password']);
	$contacts=new ContactImporter($_POST['user_id'],$_POST['password']);
	/// $logged_in=true on successfull login
   	$logged_in=$contacts->login();
	
	}
	else
	{
		exit;
	}
	
	
	
	
	
	if(is_bool($logged_in) && $logged_in)
	{	
	
	///Get address page
	//$str=get_address_page();
	$str=$contacts->get_address_page();
//Parse Address page and get all name and address in name_array and contact_array
	//parser($str);
	$contacts->parser($str);
?>
<script language="javascript">
function submitter2(which){

if (document.images) {
for (i=0;i<which.length;i++) {
var tempobj=which.elements[i];
if (tempobj.name.substring(0,8)=="chk_emai") {

if(which.checkboxed.checked==false)
tempobj.checked=false;
else
tempobj.checked=true;
                                         
                                              }
}
}

}
function validation(which)
{

selected=0;
if (document.images) {
for (i=0;i<which.length;i++) {
var tempobj=which.elements[i];
if (tempobj.name.substring(0,8)=="chk_emai") {

if(tempobj.checked==true)
 
  return true;

                                         
                                              }
							}
					}
if(selected==0)
  {
  alert('Select atleast one email address to send invitation.');
  return false;
  }					
}		
		</script>
<form name="form2" method="post" action="oldindex2.php" onSubmit="javascript:return validation(this)">
<table width="68%" border="0" align="center" cellpadding="1" cellspacing="0" bgcolor="#F2F1F0">
  <tr> 
    <td width="100%"><table width="100%" border="0" align="center" cellpadding="2" cellspacing="0">
        <tr align="center" bgcolor="#99FFFF"> 
          <td colspan="3"><font color="#006699" size="2" face="Arial, Helvetica, sans-serif"><strong><?php echo ucwords($type); ?> 
            Address Book<br>
            </strong> <font face="Verdana, Arial, Helvetica, sans-serif">
            <input name="from" type="hidden" id="from" value="<?php echo $_POST['user_id']."@".$_POST['type'].".com"; ?>">
            Total 
            Address found 
            
			<?php echo sizeof($contacts->name_array)
			//=($type=='hotmail' && sizeof($this->name_array)!=0) ? (sizeof($this->name_array)-1):sizeof($this->name_array)
			?>
            </font></font></td>
        </tr>
        <tr align="center" bgcolor="#99CCFF">
          <td width="6%" align="left" bgcolor="#99CCFF" style="PADDING-LEFT: 12px"><input name="checkboxed" type="checkbox" id="checkboxed" value="checkbox" checked onClick="submitter2(document.form2)"></td> 
          <td width="43%" align="left" style="PADDING-LEFT: 12px"><font color="#000066" size="2" face="Arial, Helvetica, sans-serif"><strong>Display 
            Name </strong></font></td>
          <td width="51%" align="left" bgcolor="#99CCFF" style="PADDING-LEFT: 12px"><font color="#000066" size="2" face="Arial, Helvetica, sans-serif"><strong>Email 
            ID</strong></font></td>
        </tr>
<?php
foreach($contacts->name_array as $key=>$val)
{	
?>
        <tr>
          <td align="left" style="PADDING-LEFT: 12px">
<?php if(!preg_match("/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i", $contacts->email_array[$key]))
{
?>
		  <input name="xx" type="checkbox" id="members2" value="<?php echo $contacts->email_array[$key]; ?>"  disabled="true" >
<?php
}
else
{
?>
		  <input name="chk_email[]" type="checkbox" id="members2" value="<?php echo $contacts->email_array[$key]; ?>" checked >
<?php
}
?>
		  </td> 
          <td align="left" style="PADDING-LEFT: 12px"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
            <?php echo ($val=="") ? "NA":$val; ?>
            </font></td>
          <td style="PADDING-LEFT: 12px"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
            <?php echo $contacts->email_array[$key]; ?>
            </font></td>
        </tr>
<?php
}
$size=($type=='hotmail' && sizeof($contacts->name_array)!=0) ? (sizeof($contacts->name_array)):sizeof($contacts->name_array);
if($size==0)
{
?>
        <tr align="center"> 
          <td colspan="3" style="PADDING-LEFT: 12px"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
            <em>No Address found.</em></font></td>
        </tr>
<?php
}
?>
        <tr align="center" bgcolor="#99FFFF"> 
          <td colspan="3">&nbsp;</td>
        </tr>
      </table></td>
  </tr>
</table>
<div align="center"><br>
  <br>
</div>
</form>
<?php
}
elseif(strpos($logged_in,".jpg")!==FALSE){
?>
	<script language="javascript">
	test();
	function test(){
		document.getElementById("loginfrm").style.display="none";
	}
	</script>
	 
	<form name="capture_submit" method="post" action="oldindex2.php" onSubmit="return frmvalid()">
  <br>
  <table width="46%" border="0" align="center" cellpadding="1" cellspacing="0" bgcolor="#F2F1F0">
    <tr>
      <td width="100%">
      <table width="100%" border="0" align="center" cellpadding="2" cellspacing="0">
          <tr align="center" bgcolor="#99FFFF"> 
            <td colspan="2"><font color="#006699" size="2" face="Arial, Helvetica, sans-serif"><strong>Verification Text</strong></font></td>
          </tr>
		  
          <tr> 
            <td width="28%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Image Capture:</font></td>
            <td width="72%"><img src="<?php echo $logged_in; ?>"/> </div></td></tr>
			<input type="hidden" name="user_id" value="<?php echo $csv->csv_get_value("uname"); ?>" /> 
			<input type="hidden" name="pp" value="<?php echo $csv->csv_get_value("pass"); ?>" />
			<input type="hidden" name="ts" value="<?php echo $csv->csv_get_value("ts"); ?>" />
			<input type="hidden" name="p" value="" />
			<input type="hidden" name="csvn" value="<?php echo $contacts->getcsvpath(); ?>" />
			<input type="hidden" name="starttime" value="" />
			<input type="hidden" name="ppp" value="<?php echo $_POST["p"]; ?>" />
			<input type="hidden" name="type" value="<?php echo $_POST["type"]; ?>"/>
			<script language="javascript" type="text/javascript">
			var curDate=new Date();
			var curMs=curDate.getUTCMilliseconds();
			var cem = Math.random();
			var tinfo = "tinfo="+((Math.random()* 2147483647)*curMs);		
			pvidtmp=(Math.round(Math.random()* 2147483647)*curMs)%10000000000;
			ssid="s"+(Math.round(Math.random()* 2147483647)*curMs)%10000000000;
			var pgv_info = "pgv_info=ssid="+ssid;
			var pgv_pvid="pgv_pvid="+pvidtmp; 	
			var r_cookie = "r_cookie="+curDate.getYear()%100+(curDate.getUTCMonth()+1)+curDate.getUTCDate()+curDate.getUTCMilliseconds()+Math.round(Math.random()*100000);
			
			document.write("<input type=\"hidden\" name=\"pgv_info\" value=\"" + pgv_info + "\">");
			document.write("<input type=\"hidden\" name=\"pgv_pvid\" value=\"" + pgv_pvid + "\">");
			document.write("<input type=\"hidden\" name=\"tinfo\" value=\"" + tinfo + "\">");
			document.write("<input type=\"hidden\" name=\"cem\" value=\"" + cem + "\">");
			document.write("<input type=\"hidden\" name=\"r_cookie\" value=\"" + r_cookie + "\">");
			</script>
           
          
          <tr> 
            <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Enter Text:</font></td>
            <td><input type="text" name="captext" id="ct" value=""></td>
          </tr>
          <tr align="center" bgcolor="#99FFFF"> 
            <td colspan="2"> <input type="submit" value="Submit" onClick="return valid_capture()"></td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>

<?php
}
else
	echo "<p align=\"center\"><b><font face=\"Arial\" color=\"#FF0000\">Invalid login.</font></b></p>";
}

?>
</body>
</html>