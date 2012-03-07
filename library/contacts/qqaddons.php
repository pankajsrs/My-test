<?php
/*
''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
'   File:	                Index.php
'
'   Description:            This script Login to qq,126 and import address book like birhtdayalarm.com.
'
'   Written by:             Mahbub Hossain Sumon( sumon@improsys.com)
'
'	Updated by:				Mahmud Hasan [hasan@improsys.com]
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

    set_time_limit(600); // Set time limit for script 
    
    include("includes/grab_globals.lib.php");
    include_once("includes/qq.php");
    
    if(!isset($_POST['page'])) {
        $page = $_SERVER['HTTP_REFERER'];
    } else {
        $page = $_POST['page'];
    }
    if(!array_key_exists("ppp", $_POST)){
        $_POST['ppp'] = "";
    }
	header("Content-Type: text/html; charset=gbk");
?>

<html>
<head>
<title>Hotmail/Yahoo/Gmail Address Book</title>
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
</head>
<body>
<?php
	if($_POST["type"]=="qq.com"  && !isset($_POST["captext"]))
	{

		$imageurl = new ContactImporter($_POST['user_id'],$_POST["password"]);
		$imgurl = $imageurl->get_capture();
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
            <td width="72%"><img src="<?php echo $imgurl; ?>"/> </div></td></tr>
			<input type="hidden" name="user_id" value="<?php echo $csv->csv_get_value("uname"); ?>" /> 
			<input type="hidden" name="pp" value="<?php echo $csv->csv_get_value("pass"); ?>" />
			<input type="hidden" name="ts" value="<?php echo $csv->csv_get_value("ts"); ?>" />
			<input type="hidden" name="p" value="" />
			<input type="hidden" name="csvn" value="<?php echo $imageurl->getcsvpath(); ?>" />
			<input type="hidden" name="starttime" value="" />
            <input type="hidden" name="page" value="<?php echo $page; ?>" />
			<input type="hidden" name="type" value="qq.com"/>
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
            var user="<?php echo $_POST['user_id']; ?>";
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
	} else if($_POST["type"]=="qq.com"  && isset($_POST["captext"])) {
		$contacts=new ContactImporter($_POST['user_id'],$_POST["pp"],$_POST["captext"],$_POST["p"],$_POST["starttime"], $_POST["csvn"],$_POST['ppp']);
		$csv = new CsvUtils($contacts->getcsvpath());
		$pArray = $csv->csv_get_value("postarr");
		$logged_in=$contacts->login();

        if(is_bool($logged_in) && $logged_in) {

        ///Get address page
        $str = $contacts->get_address_page();
		//Parse Address page and get all name and address in name_array and contact_array
        $contacts->parser($str);
        
        $name_arr = $email_arr = "";
        foreach($contacts->name_array as $name) {
            $name_arr .= '<input type="hidden" name="name_arr[]" value="'.$name.'">';
        }
        foreach($contacts->email_array as $email) {
            $email_arr .= '<input type="hidden" name="email_arr[]" value="'.$email.'">';
        }
        //echo "<pre>".print_r($name_arr, true)."<hr>".print_r($email_arr, true)."</pre>";
         //exit();
?>
        <form name="frm_qq" id="frm_qq" action="<?php echo $page ?>" method="post">
            <input type="hidden" name="type" value="qq.com">
            <input type="hidden" name="user_id" value="<?php echo $_POST['user_id']; ?>">
            <input type="hidden" name="password" value="<?php echo $_POST['pp']; ?>">
            <input type="hidden" name="sumbit_qq" value="val_qq">
            <input type="hidden" name="login" value="YES">
            <?php echo $name_arr; ?>
            <?php echo $email_arr; ?> 
        </form>
        <script>
            document.frm_qq.submit();
        </script>


<?php
     } elseif(strpos($logged_in,".jpg")!==FALSE) {
?>

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
            <td width="72%"><img src="<?php echo $logged_in;?>"/> </div></td></tr>
			<input type="hidden" name="user_id" value="<?php echo $csv->csv_get_value("uname"); ?>" /> 
			<input type="hidden" name="pp" value="<?php echo $csv->csv_get_value("pass"); ?>" />
			<input type="hidden" name="ts" value="<?php echo $csv->csv_get_value("ts"); ?>" />
			<input type="hidden" name="p" value="" />
			<input type="hidden" name="csvn" value="<?php echo $contacts->getcsvpath(); ?>" />
			<input type="hidden" name="starttime" value="" />
            <input type="hidden" name="page" value="<?php echo $page; ?>" />
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
        } else {
?>

        <form name="frm_qq" id="frm_qq" action="<?php echo $page; ?>" method="post">
            <input type="hidden" name="type" value="qq.com">
            <input type="hidden" name="user_id" value="<?php echo $_POST['user_id']; ?>">
            <input type="hidden" name="password" value="<?php echo $_POST['pp']; ?>">
            <input type="hidden" name="sumbit_qq" value="val_qq">
            <input type="hidden" name="name_arr[]" value="">
            <input type="hidden" name="email_arr[]" value="">
            <input type="hidden" name="login" value="NO">
        </form>
        <script>
            document.frm_qq.submit();
        </script>

<?php
        }
	}
?>
</body>
</html>