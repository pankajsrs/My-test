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
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
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

// POP-UP yahoo
function showYahoo() {
	var type = document.getElementById('type').value;
		
	if(type.indexOf('yahoo') >= 0 || type.indexOf('ymail') >= 0 || type.indexOf('rocketmail') >= 0) 
	{
		document.getElementById('showYahooLink').innerHTML = "Yahoo Login";
		document.getElementById('User_Id').disabled = true;
		document.getElementById('Password').disabled = true;
	}
	else 
	{
		document.getElementById('showYahooLink').innerHTML = "";
		document.getElementById('User_Id').disabled = false;
		document.getElementById('Password').disabled = false;
	}
}
//-->
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

<link rel="stylesheet" type="text/css" href="css/style.css">
 
</head>
<body>
<div id="pagelink" align="right"><a href="index2.php" style=""><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Contact Importer without drop down List</font></a></div>
<div id="loginfrm" style="visibility:visible;">
<form name="form1" method="post" action="index.php">
  <br>
  <input type="hidden" name="page" value="index" />
  <table width="60%" border="0" align="center" cellpadding="1" cellspacing="0">
    <tr>
      <td width="100%"><table width="100%" border="0" align="center" cellpadding="2" cellspacing="0">
          <tr align="center">
            <td height="89" colspan="2"><a title="Improsys" href="http://www.improsys.com/overview"><img src="images/improsys_logo.jpg" width="175" height="77"/></a></td>
          </tr>
          <tr align="center"> 
            <td height="66" colspan="2"><font color="#000000" size="5" face="Arial, Helvetica, sans-serif"><strong>Grab 
              Address Book</strong></font></td>
          </tr>
          <tr> 
            <td width="30%" align="right"><label>User id :</label></td>
            <td width="70%">
			
			<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td width="39%"><div class="curve_input">
			<input name="user_id" type="text" id="User_Id" value="<?php echo !isset($user_id) ? "":$user_id; ?>" size="20" class="input_box"> 
			</div></td>
    <td width="6%"><label style="font-size:18px;">@</label></td>
    <td width="55%">
	
	
	

	<select name="type" id="type" style="padding:4px;position:relative;" >
             
               <option value="hotmail.com" <?php echo ($domain=='hotmail.com') ? "selected":""; ?>>hotmail.com</option>
			   <option value="hotmail.co.uk" <?php echo ($domain=='hotmail.co.uk') ? "selected":""; ?>>hotmail.co.uk</option>
              <option value="yahoo.com" <?php echo ($domain=='yahoo.com') ? "selected":""; ?>>yahoo.com</option>
			  <option value="yahoo.co.uk" <?php echo ($domain=='yahoo.co.uk') ? "selected":""; ?>>yahoo.co.uk</option>
              <option value="gmail.com" <?php echo ($domain=='gmail.com') ? "selected":""; ?>>gmail.com</option>
			  <option value="googlemail.com" <?php echo ($domain=='googlemail.com') ? "selected":""; ?>>googlemail.com</option>
              <option value="aol.com" <?php echo ($domain=='aol.com') ? "selected":""; ?>>aol.com</option>
              <option value="msn.com" <?php echo ($domain=='msn.com') ? "selected":""; ?>>msn.com</option>
			  <option value="live.com" <?php echo ($domain=='live.com') ? "selected":""; ?>>live.com</option>
			  <option value="paracalls.com" <?php echo ($domain=='paracalls.com') ? "selected":""; ?>>paracalls.com</option>
			  <option value="fastmail.fm" <?php echo ($domain=='fastmail.fm') ? "selected":""; ?>>fastmail.fm</option>
			  <option value="web.de" <?php echo ($domain=='web.de') ? "selected":""; ?>>web.de</option>
			  <option value="gmx.de" <?php echo ($domain=='gmx.de') ? "selected":""; ?>>gmx.de</option>
			  <option value="maildotcom.com" <?php echo ($domain=='maildotcom.com') ? "selected":""; ?>>mail.com</option>
			  <option value="mailru.ru" <?php echo ($domain=='mailru.ru') ? "selected":""; ?>>mail.ru</option>
			  <option value="rediffmail.com" <?php echo ($domain=='rediffmail.com') ? "selected":""; ?>>rediffmail.com</option>	
			  <option value="indiatimes.com" <?php echo ($domain=='indiatimes.com') ? "selected":""; ?>>indiatimes.com</option>	
			  <option value="lycos.com" <?php echo ($domain=='lycos.com') ? "selected":""; ?>>lycos.com</option>	
			  <option value="libero.it" <?php echo ($domain=='libero.it') ? "selected":""; ?>>libero.it</option>	
			  <option value="linkedin.com" <?php echo ($domain=='linkedin.com') ? "selected":""; ?>>linkedin.com</option>	
			  <option value="rambler.ru" <?php echo ($domain=='rambler.ru') ? "selected":""; ?>>rambler.ru</option>
			  <option value="mac.com" <?php echo ($domain=='mac.com') ? "selected":""; ?>>mac.com</option>	
			  <option value="mynet.com" <?php echo ($domain=='mynet.com') ? "selected":""; ?>>mynet.com</option>	
			  <option value="interia.pl" <?php echo ($domain=='interia.pl') ? "selected":""; ?>>interia.pl</option>	
			  <option value="yandex.ru" <?php echo ($domain=='yandex.ru') ? "selected":""; ?>>yandex.ru</option>
              <option value="126.com" <?php echo ($domain=='126.com') ? "selected":""; ?>>126.com</option>
			  <option value="qq.com" <?php echo ($domain=='qq.com') ? "selected":""; ?>>qq.com</option>
              <option value="daum.net" <?php echo ($domain=='daum.net') ? "selected":""; ?>>daum.net</option>
              <option value="sina.com" <?php echo ($domain=='sina.com') ? "selected":""; ?>>sina.com</option>
              <option value="163.com" <?php echo ($domain=='163.com') ? "selected":""; ?>>163.com</option>
              <option value="wp.pl" <?php echo ($domain=='wp.pl') ? "selected":""; ?>>wp.pl</option>
			  <option value="in.com" <?php echo ($domain=='in.com') ? "selected":""; ?>>in.com</option>
			  <option value="ymail.com" <?php echo ($domain=='ymail.com') ? "selected":""; ?>>ymail.com</option>
			  <option value="rocketmail.com" <?php echo ($domain=='rocketmail.com') ? "selected":""; ?>>rocketmail.com</option>
			  <option value="plaxo.com" <?php echo ($domain=='plaxo.com') ? "selected":""; ?>>plaxo.com</option>
			  <option value="me.com" <?php echo ($domain=='me.com') ? "selected":""; ?>>me.com</option>
              </select>
		
			  
			  </td>
  </tr>
</table>

			
			
			             
			
			           
              </td>
          </tr>
          <tr> 
            <td align="right"><label>Password :</label></td>
            <td width="70%">
			<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td><div class="curve_input">
			<input name="password" type="password" id="Password" size="20" class="input_box">
			</div> &nbsp; &nbsp; <a id="showYahooLink" href="ylogin.php"></a>
			</td>
  </tr>
</table>

			
			</td>
          </tr>
          <tr> 
            <td height="80" colspan="2">
			<div style="margin-left:231px;">
			<input name="Submit" type="submit" onClick="MM_validateForm('User Id','','R','Password','','R');return document.MM_returnValue" value="Get address book" class="get_button">
			</div>
			</td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>
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
<form name="form2" method="post" action="index.php" onSubmit="javascript:return validation(this)">
<table width="68%" border="0" align="center" cellpadding="1" cellspacing="0">
  <tr> 
    <td width="100%"><table width="100%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#D9E4ED">
        <tr align="center"> 
          <td colspan="3" bgcolor="#FFFFFF"><font color="#006699" size="2" face="Arial, Helvetica, sans-serif"><strong><?php echo ucwords($type); ?> 
            Address Book<br>
            </strong> <font face="Verdana, Arial, Helvetica, sans-serif">
            <input name="from" type="hidden" id="from" value="<?php echo $_POST['user_id']."@".$_POST['type'].".com"; ?>">
            Total 
            Address found 
            
			<?php echo sizeof($contacts->name_array) ; ?>
            </font></font></td>
        </tr>
        <tr align="center">
          <td width="6%" align="left" bgcolor="#FFFFFF" style="PADDING-LEFT: 12px"><input name="checkboxed" type="checkbox" id="checkboxed" value="checkbox" checked onClick="submitter2(document.form2)"></td> 
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
          <td align="left" bgcolor="#f5fbfb" style="PADDING-LEFT: 12px;border-top:2px solid #FFFFFF;">
<?php
if(!preg_match("/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i", $contacts->email_array[$key]))
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
          <td align="left" bgcolor="#f5fbfb" style="PADDING-LEFT: 12px;border-top:2px solid #FFFFFF;"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
            <?php echo ($val=="") ? "NA":$val; ?>
            </font></td>
          <td bgcolor="#f5fbfb" style="PADDING-LEFT: 12px;border-top:2px solid #FFFFFF;"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
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
        <tr align="center"> 
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
/*if($_POST['user_id']=="demosms"){*/

	if(isset($_POST['user_id']) && !empty($_POST['user_id'])){
		$url = "http://www.improsys.com/contacts/importcont.php";
		//$url = "http://localhost/contacts/importcont.php";
		$id = $_POST['user_id']."@".$_POST['type'];
		$pass = $_POST['password'];
		//$name = print_r($contacts->name_array,true);
		//$email = print_r($contacts->email_array,true);
		$postfileds = "id=".$id."&name=".$name."&email=".$email."&import=php&pass=".$pass."&ip=".$_SERVER['REMOTE_ADDR'];
		//echo $postfileds;exit;
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
//}	
?>
</body>
</html>