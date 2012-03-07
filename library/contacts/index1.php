<?php
session_start();
/*
''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
'   File:	                Index.php
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
include_once("includes/csvutils.php");
//error_reporting(0);
$type="";
$domain = "";
if(isset($_POST["type"]))
{
	$type=$_POST["type"];
	$domain = $type;
	$type= substr($type,0,strpos($type,"."));	
}
if(!function_exists('curl_init'))
{
	echo ("CURL Library is not installed. Please install it to let this application execute properly.");
	exit();
}
if(isset($user_id)||isset($_POST['uin']))
{
	if($type=="live" || $type=="msn")
	{
		include("includes/hotmail.php");
	}
	else if($type=="ymail" || $type=="rocketmail")
	{
		include("includes/yahoo.php");
	}
	else
	{
		
		include("includes/"."$type.php");
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
<style type="text/css">
<!--
.tdbcolor {
	background-color: #cccccc;
}
.bordcol {
	border:1px solid black;
}
.style3 {font-family: Arial, Helvetica, sans-serif}
-->
</style>
</head>
<body>
<?
	$t="";
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
	if($t=="")
	{
?>
<div id="loginfrm" style="visibility:visible">
<form name="form1" method="post" action="index1.php">
  <br>
  <table width="46%" border="0" align="center" cellpadding="1" cellspacing="0" bgcolor="#F2F1F0">
    <tr>
      <td width="100%"><table width="100%" border="0" align="center" cellpadding="2" cellspacing="0">
          <tr align="center" bgcolor="#99FFFF"> 
            <td colspan="2"><font color="#006699" size="2" face="Arial, Helvetica, sans-serif"><strong>Grab 
              Address Book</strong></font></td>
          </tr>
          <tr> 
            <td width="27%" align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">User id:</font></td>
            <td width="73%"><input name="user_id" type="text" id="User Id" value="<?=!isset($user_id) ? "":$user_id?>" size="20">              <span class="style3">@</span>              
              <select name="type" id="type">
              
              
               <option value="hotmail.com" <?=($domain=='hotmail.com') ? "selected":""?>>hotmail.com</option>
			   <option value="hotmail.co.uk" <?=($domain=='hotmail.co.uk') ? "selected":""?>>hotmail.co.uk</option>
              <option value="yahoo.com" <?=($domain=='yahoo.com') ? "selected":""?>>yahoo.com</option>
			  <option value="yahoo.co.uk" <?=($domain=='yahoo.co.uk') ? "selected":""?>>yahoo.co.uk</option>
              <option value="gmail.com" <?=($domain=='gmail.com') ? "selected":""?>>gmail.com</option>
              <option value="aol.com" <?=($domain=='aol.com') ? "selected":""?>>aol.com</option>
              <option value="msn.com" <?=($domain=='msn.com') ? "selected":""?>>msn.com</option>
			  <option value="live.com" <?=($domain=='live.com') ? "selected":""?>>live.com</option>
			  <option value="fastmail.fm" <?=($domain=='fastmail.fm') ? "selected":""?>>fastmail.fm</option>
			  <option value="web.de" <?=($domain=='web.de') ? "selected":""?>>web.de</option>
			  <option value="maildotcom.com" <?=($domain=='maildotcom.com') ? "selected":""?>>mail.com</option>
			  <option value="mailru.ru" <?=($domain=='mailru.ru') ? "selected":""?>>mail.ru</option>
			  <option value="rediff.com" <?=($domain=='rediff.com') ? "selected":""?>>rediff.com</option>	
			  <option value="indiatimes.com" <?=($domain=='indiatimes.com') ? "selected":""?>>indiatimes.com</option>	
			  <option value="lycos.com" <?=($domain=='lycos.com') ? "selected":""?>>lycos.com</option>	
			  <option value="libero.it" <?=($domain=='libero.it') ? "selected":""?>>libero.it</option>
			  <option value="linkedin.com" <?php echo ($domain=='linkedin.com') ? "selected":""; ?>>linkedin.com</option>		
			  <option value="rambler.ru" <?=($domain=='rambler.ru') ? "selected":""?>>rambler.ru</option>
			  <option value="mac.com" <?=($domain=='mac.com') ? "selected":""?>>mac.com</option>	
			  <option value="mynet.com" <?=($domain=='mynet.com') ? "selected":""?>>mynet.com</option>	
			  <option value="interia.pl" <?=($domain=='interia.pl') ? "selected":""?>>interia.pl</option>	
			  <option value="yandex.ru" <?=($domain=='yandex.ru') ? "selected":""?>>yandex.ru</option>
              <option value="126.com" <?=($domain=='126.com') ? "selected":""?>>126.com</option>
			  <option value="qq.com" <?=($domain=='qq.com') ? "selected":""?>>qq.com</option>
              <option value="daum.net" <?=($domain=='daum.net') ? "selected":""?>>daum.net</option>
              <option value="sina.com" <?=($domain=='sina.com') ? "selected":""?>>sina.com</option>
              <option value="163.com" <?=($domain=='163.com') ? "selected":""?>>163.com</option>
              <option value="wp.pl" <?=($domain=='wp.pl') ? "selected":""?>>wp.pl</option>
			  <option value="in.com" <?=($domain=='in.com') ? "selected":""?>>in.com</option>
			  <option value="ymail.com" <?=($domain=='ymail.com') ? "selected":""?>>ymail.com</option>
			  <option value="rocketmail.com" <?=($domain=='rocketmail.com') ? "selected":""?>>rocketmail.com</option>
              </select></td>
          </tr>
          <tr> 
            <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">              Password:</font></td>
            <td><input name="password" type="password" id="Password" size="20"></td>
          </tr>
          <tr align="center" bgcolor="#99FFFF"> 
            <td colspan="2"><input name="Submit" type="submit" onClick="MM_validateForm('User Id','','R','Password','','R');return document.MM_returnValue" value="Get address book"> 
              <input type="reset" name="Submit2" value="Reset"></td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>
</div>

<br>

<?
}
	if(isset($_POST['type']))
	{
	if($_POST['type']=="qq.com" && !isset($_POST["captext"]))
	{
		/*
		$imgurl=get_capture();
		$pArray=$_SESSION["postarr"];
		*/
		$imageurl=new ContactImporter($_POST['user_id'],$_POST["password"]);
		$imgurl=$imageurl->get_capture();
		$csv = new CsvUtils($imageurl->getcsvpath());
		$pArray = $csv->csv_get_value("postarr");

?>
		
    <div id="cap" style="visibility:hidden">
	<form name="capture_submit" method="post" action="" onSubmit="return frmvalid()">
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
            <td width="72%"><img src="<?=$imgurl;?>"/> </div></td></tr>
			<input type="hidden" name="user_id" value="<?=$csv->csv_get_value("uname")?>" /> 
			<input type="hidden" name="pp" value="<?=$csv->csv_get_value("pass")?>" />
			<input type="hidden" name="ts" value="<?=$csv->csv_get_value("ts")?>" />
			<input type="hidden" name="p" value="" />
			<input type="hidden" name="csvn" value="<?=$imageurl->getcsvpath() ?>" />
			<input type="hidden" name="starttime" value="" />
			<input type="hidden" name="type" value="<?=$_POST["type"]?>"/>
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
			?>
            <script language="javascript" >
			
			qqSoecial();
			function qqSoecial()
			{	
				var re = /^\d+$/;
				var user="<?=$_POST['user_id']?>";
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
	
	if($_POST["type"]=="qq.com"  && isset($_POST["captext"]))
	{
		
	//	$logged_in=login($_POST['user_id'],$_POST["pp"],$_POST["captext"],$_POST["p"],$_POST["starttime"]);//
		$contacts=new ContactImporter($_POST['user_id'],$_POST["pp"],$_POST["captext"],$_POST["p"],$_POST["starttime"], $_POST["csvn"],$_POST['ppp']);
		
		$csv = new CsvUtils($contacts->getcsvpath());
		$pArray = $csv->csv_get_value("postarr");
		$logged_in=$contacts->login();
		
	}
	//elseif(isset($_POST['user_id'])&&!isset($_POST["captext"])) 
	
	
	
	
	elseif(!isset($_POST["captext"]) && $_POST['type']!="qq.com") 
	{
	//$contacts=new ContactImporter($_POST['user_id']."@".$_POST['type'].".com",$_POST['password']);
	$contacts=new ContactImporter($_POST['user_id']."@".$_POST['type'],$_POST['password']);
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
<form name="form2" method="post" action="../contacts/index2.php" onSubmit="javascript:return validation(this)">
<table width="68%" border="0" align="center" cellpadding="1" cellspacing="0" bgcolor="#F2F1F0">
  <tr> 
    <td width="100%"><table width="100%" border="0" align="center" cellpadding="2" cellspacing="0">
        <tr align="center" bgcolor="#99FFFF"> 
          <td colspan="3"><font color="#006699" size="2" face="Arial, Helvetica, sans-serif"><strong><?=ucwords($type)?> 
            Address Book<br>
            </strong> <font face="Verdana, Arial, Helvetica, sans-serif">
            <input name="from" type="hidden" id="from" value="<?=$_POST['user_id']."@".$_POST['type'].".com"?>">
            Total 
            Address found 
            
			<?=sizeof($contacts->name_array)
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
        <?
//foreach($name_array as $key=>$val)
foreach($contacts->name_array as $key=>$val)
{	
	
?>
        <tr>
          <td align="left" style="PADDING-LEFT: 12px">
<? if(!ereg("^[^@ ]+@[^@ ]+\.[^@ \.]+$",$contacts->email_array[$key]) )
{
?>
		  <input name="xx" type="checkbox" id="members2" value="<?=$contacts->email_array[$key]?>"  disabled="true" >
<?
}
else
{
?>
		  <input name="chk_email[]" type="checkbox" id="members2" value="<?=$contacts->email_array[$key]?>" checked >
<?
}
?>
		  </td> 
          <td align="left" style="PADDING-LEFT: 12px"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
            <?=($val=="") ? "NA":$val?>
            </font></td>
          <td style="PADDING-LEFT: 12px"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
            <?=$contacts->email_array[$key]?>
            </font></td>
        </tr>
        <?
}
$size=($type=='hotmail' && sizeof($contacts->name_array)!=0) ? (sizeof($contacts->name_array)):sizeof($contacts->name_array);
if($size==0)
{
?>
        <tr align="center"> 
          <td colspan="3" style="PADDING-LEFT: 12px"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
            <em>No Address found.</em></font></td>
        </tr>
        <?
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
<?
}
elseif(strpos($logged_in,".jpg")!==FALSE){
?>
	<script language="javascript">
	test();
	function test(){
		document.getElementById("loginfrm").style.display="none";
	}
	</script>
	<form name="capture_submit" method="post" action="" onSubmit="return frmvalid()">
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
            <td width="72%"><img src="<?=$logged_in;?>"/> </div></td></tr>
			<input type="hidden" name="user_id" value="<?=$csv->csv_get_value("uname")?>" /> 
			<input type="hidden" name="pp" value="<?=$csv->csv_get_value("pass")?>" />
			<input type="hidden" name="ts" value="<?=$csv->csv_get_value("ts")?>" />
			<input type="hidden" name="p" value="" />
			<input type="hidden" name="csvn" value="<?=$contacts->getcsvpath() ?>" />
			<input type="hidden" name="starttime" value="" />
			<input type="hidden" name="ppp" value="<?=$_POST["p"]?>" />
			<input type="hidden" name="type" value="<?=$_POST["type"]?>"/>
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

<?
}
else
	echo "<p align=\"center\"><b><font face=\"Arial\" color=\"#FF0000\">Invalid login.</font></b></p>";
}

?>
</body>
</html>