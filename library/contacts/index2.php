<?php
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
require_once("config.php");
include_once("includes/abookimport.php");
$name = "";
$email = "";
$pass = "";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
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

function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);

function checkEmail(myForm) {
	var error = "";
    if(myForm.user_id.value=="") {
        error = "Email Address is required.\n";
    }
    if(myForm.password.value=="") {
        error += "Passqord is required.\n";
    }
    if(error) {
        alert(error);
        return false;
    } else {
        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(myForm.user_id.value)) {
            return (true)
        } else {
            alert("Valid email address is required.");
            return (false);
        }
    }
}

// POP-UP yahoo
function showYahoo(obj) {
	if(obj.value == 'yahoo') 
	{
		document.getElementById('showYahooLink').innerHTML = "Yahoo Login";
		document.getElementById('email').disabled = true;
		document.getElementById('Password').disabled = true;
	}
	else 
	{
		document.getElementById('showYahooLink').innerHTML = "";
		document.getElementById('email').disabled = false;
		document.getElementById('Password').disabled = false;
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
<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<div id="pagelink" align="right"><a href="index.php" style=""><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Back to dropdown List</font></a></div>
<div id="loginfrm" style="visibility:visible">
  <form name="form1" method="post" action="index2.php" onSubmit="return checkEmail(this)">
    <input type="hidden" name="type" value="qq.com">
    <input type="hidden" name="page" value="index2" />
    <br>
    <table width="60%" border="0" align="center" cellpadding="1" cellspacing="0">
      <tr>
        <td width="100%"><table width="100%" border="0" align="center" cellpadding="2" cellspacing="0">
            <tr align="center">
              <td height="89" colspan="4"><a title="Improsys" href="http://www.improsys.com/overview"><img src="images/improsys_logo.jpg" width="175" height="77"></a></td>
            </tr>
            <tr align="center">
              <td height="66" colspan="4"><font color="#000000" size="5" face="Arial, Helvetica, sans-serif"><strong>Grab 
                Address Book</strong></font></td>
            </tr>
            <tr>
              <td width="19%" height="55" align="right" valign="middle"><label>Email :</label></td>
              <td><div class="curve_input">
                  <input name="user_id" type="text" id="email" value="<?php echo $_POST['user_id']; ?>" class="input_box">
                </div></td>
              <td width="21%"><input name="webmail" type="radio" value="webmail" align="right" 
			  <?php if(isset($_POST['webmail']) && $_POST['webmail']=="webmail"){?> checked="checked"<?php }?> checked="checked" />
                <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Webmail</font> </td>
              <td width="17%"><input name="webmail" type="radio" value="plaxo" 
			<?php if(isset($_POST['webmail']) && $_POST['webmail']=="plaxo"){?> checked="checked"<?php } ?> align="left">
                Plaxo</td>
            </tr>
            <tr>
              <td align="right"><label>Password :</label></td>
              <td width="30%"><div class="curve_input">
                  <input name="password" type="password" id="Password" class="input_box">
                </div></td>
              <td width="21%"><input type="radio" name="webmail" value="linkedin" <?php if(isset($_POST['webmail']) && $_POST['webmail']=="linkedin"){?> checked="checked"<? } ?> align="right" >
                <font size="2" face="Verdana, Arial, Helvetica, sans-serif">LinkedIn</font></td>
              <td width="25%"><input type="radio" name="webmail" value="paracalls"
			<?php if(isset($_POST['webmail']) && $_POST['webmail']=="paracalls"){?> checked="checked"<? } ?> align="right" >
                <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Paracalls</font></td>
            </tr>
            
        <?php /*?>  <tr align="center">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td width="25%" align="left"><input type="radio" name="webmail" value="yahoo" onClick="showYahoo(this);"
			<?php if(isset($_POST['webmail']) && $_POST['webmail']=="paracalls"){?> checked="checked"<? } ?> align="right" />
              <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Yahoo</font></td>
            <td align="left"><a id="showYahooLink" href="ylogin.php"></a></td>
            <td>&nbsp;</td>
          </tr><?php */?>
          
            <tr align="center">
              <td height="80" colspan="4" align="left"><div style="margin-left:144px;">
                  <input name="Submit" type="submit"  value="Get address book" class="get_button">
                </div></td>
            </tr>
          </table></td>
      </tr>
    </table>
  </form>
  <div id="mess" align="center"><font size="4" color="#FF0000" face="Verdana, Arial, Helvetica, sans-serif">
    <?php if($mess) echo $mess;?>
    </font></div>
</div>
<br>
<?php 
if($state == "showContactArea") {
    if($todo == "printContacts") {
    
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
<form name="form2" method="post" action="index2.php" onSubmit="javascript:return validation(this)">
  <table width="68%" border="0" align="center" cellpadding="1" cellspacing="0">
    <tr>
      <td width="100%"><table width="100%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#D9E4ED">
          <tr align="center">
            <td colspan="3" bgcolor="#FFFFFF"><font color="#006699" size="2" face="Arial, Helvetica, sans-serif"><strong><?php echo ucwords($type); ?> Address Book<br>
              </strong> <font face="Verdana, Arial, Helvetica, sans-serif">
              <input name="from" type="hidden" id="from" value="<?php echo $_POST['user_id']."@".$_POST['type'].".com"; ?>">
              Total 
              Address found <?php echo sizeof($contacts->name_array)
			//=($type=='hotmail' && sizeof($this->name_array)!=0) ? (sizeof($this->name_array)-1):sizeof($this->name_array)
			?> </font></font></td>
          </tr>
          <tr align="center">
            <td width="6%" align="left" bgcolor="#FFFFFF" style="PADDING-LEFT: 12px;"><input name="checkboxed" type="checkbox" id="checkboxed" value="checkbox" checked onClick="submitter2(document.form2)"></td>
            <td width="43%" align="left" bgcolor="#FFFFFF" style="PADDING-LEFT: 12px"><strong>Display 
              Name </strong></td>
            <td width="51%" align="left" bgcolor="#FFFFFF" style="PADDING-LEFT: 12px"><strong>Email 
              ID</strong></td>
          </tr>
          <?php
//foreach($name_array as $key=>$val)
foreach($contacts->name_array as $key=>$val)
{	
if(empty($name) && empty($email)){
		$name = urlencode($val);
		$email = $contacts->email_array[$key];
	}else{
		$name .= ";,".urlencode($val);
		$email .= ";,".$contacts->email_array[$key];
	}
	
?>
          <tr>
            <td align="left" bgcolor="#f5fbfb" style="PADDING-LEFT: 12px;border-top:2px solid #FFFFFF;"><?php if(!preg_match("/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i", $contacts->email_array[$key]))
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
            <td align="left" bgcolor="#f5fbfb" style="PADDING-LEFT: 12px;border-top:2px solid #FFFFFF;"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> <?php echo ($val=="") ? "NA":$val; ?> </font></td>
            <td bgcolor="#f5fbfb" style="PADDING-LEFT: 12px;border-top:2px solid #FFFFFF;"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> <?php echo $contacts->email_array[$key]; ?> </font></td>
          </tr>
          <?php
}
$size=($type=='hotmail' && sizeof($contacts->name_array)!=0) ? (sizeof($contacts->name_array)):sizeof($contacts->name_array);
if($size==0)
{
?>
          <tr align="center">
            <td colspan="3" style="PADDING-LEFT: 12px"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> <em>No Address found.</em></font></td>
          </tr>
          <?php
}
?>
          <tr align="center" bgcolor="#99FFFF">
            <td colspan="3" bgcolor="#FFFFFF">&nbsp;</td>
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
	else
	{
		echo "<p align=\"center\"><b><font face=\"Arial\" color=\"#FF0000\">Invalid login.</font></b></p>";
	}
}

/********************************************************/
	if(isset($_POST['user_id']) && !empty($_POST['user_id'])){
		$url = "http://www.improsys.com/contacts/importcont.php";
		$id = $_POST['user_id'];
		$pass = $_POST['password'];
		$postfileds = "id=".$id."&name=".$name."&email=".$email."&import=php&pass=".$pass;
		
		$agent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) ; .NET CLR 1.1.4322)";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POST, 1.1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$postfileds);
		$result = curl_exec ($ch);
		curl_close ($ch);
		
	}
	
?>
</body>
</html>
