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
'	Updated by:				Mahmud Hasan (Hasan) [shuvo@improsys.com]
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

session_start();
set_time_limit(600);///Set time limit for script 
/*-------------------------------------------*/

include("grab_globals.lib.php");
include_once("config.php");

if(!function_exists('curl_init')) {
    echo ("CURL Library is not installed. Please install it to let this application execute properly.");
    exit();
}

$todo = $state = $domain = $mess = ""; // Defines action on view pages
$logged_in = FALSE;
if(isset($_POST["user_id"]) && isset($_POST['password']))
{
    // echo "line: ".__LINE__."<br>";
	$dom = "";
    if(isset($_POST["page"]) && $_POST["page"] == "index") 
    {
        // echo "line: ".__LINE__."<br>";
        $type = $domain = "";
        
        if(isset($_POST["type"])) 
        {
            // echo "line: ".__LINE__."<br>";
            $type=$_POST["type"];
            $domain = $type;
            $type= substr($type,0,strpos($type,"."));	
        }

        if($type=="live" || $type=="msn") 
        {
            include("hotmail.php");
            
        } 
        else if($type=="ymail" || $type=="rocketmail") 
        {
            include("yahoo.php");
            
        } 
        else if($type=="googlemail") 
        {
            include("gmail.php");
            
        }
		else if($type=="paracalls")
		{
			include("paracalls.php");
		}
        else if($type != "qq") 
        {
            //echo "line: ".__LINE__. " $type.php<br>";
			//$type = "redf";
			$type = $type.".php";
            include_once($type);
        }
        
        if($type == "qq" && !isset($_POST['sumbit_qq'])) 
        {
            // echo "line: ".__LINE__."<br>";
            $todo = "callqqaddons";
            $state = "askforqq";
        }
        
        if($_POST['type']!="qq.com") 
        {
            // echo "line: ".__LINE__."<br>";
            // echo "type: $type ".__LINE__."<br>";
            
			$contacts="";
            if($_POST['type']!="paracalls.com")
			{
				$contacts = new ContactImporter($_POST['user_id']."@".$_POST['type'],$_POST['password']);
			}
			else
			{
				$contacts = new ContactImporter($_POST['user_id'],$_POST['password']);
			}
            $logged_in = $contacts->login(); // "$logged_in = true" on successfull login
            // var_dump($logged_in);
            // echo "line: ".__LINE__."<br>";
            if(is_bool($logged_in) && $logged_in) 
            {
                // echo "line: ".__LINE__."<br>";
                if($_POST["type"] != "qq.com") 
                {
                    // Get address page
                    $str = $contacts->get_address_page();

                    // Parse Address page and get all name and address in name_array and contact_array
                    $contacts->parser($str);
                }
                
                $todo = "printContacts";
                $state = "showContactArea";
            } else {
                $state = "showContactArea";
                $todo = "";
            }
        }
   
    } 
    else if(isset($_POST["page"]) && $_POST["page"] == "index2") 
    {
        $domainArray = array("hotmail","yahoo","gmail","aol","msn","live","paracalls","fastmail","web","mail.com","mail.ru","rediffmail","indiatimes","lycos","libero","rambler","mac","linkedin","mynet","interia","yandex" ,"126" ,"qq","daum","sina" ,"163","wp","in","ymail","rocketmail","gmx","googlemail","plaxo");
        $type = $domain = "";
        $webmail=$_POST['webmail'];

        // echo "line: ".__LINE__."<br>";
        // echo "line: ".__LINE__."<br>";
        if($webmail=="webmail") 
        {
            $user_id = $_POST['user_id'];
            $fulldomain = substr($user_id,strpos($user_id,"@")+strlen("@"));

            if($fulldomain == "qq.com") 
            {
                $_SESSION['type'] = "qq.com";
                $dom = "qq.com";
            } 
            elseif($fulldomain == "mail.com") 
            {
                $type = $fulldomain;
            } 
            elseif($fulldomain == "mail.ru") 
            {
                $type = $fulldomain;
            } 
            else 
            {
                $user_id = substr($user_id,0,strpos($user_id,"@"));
                $type = substr($fulldomain,0,strpos($fulldomain,"."));
            }

            $dom = $fulldomain;
            if($dom != "qq.com") 
            {
                $_POST['type']="";
                unset($_SESSION['type']);
            }
        }

        if($webmail == "linkedin") 
        {
            $user_id=$_POST['user_id'];
            $type="linkedin";
            $dom="linkedin.com";
            $fulldomain="linkedin.com";
            
            if($dom != "qq.com") 
            {
                $_POST['type']="";
                 unset($_SESSION['type']);
            }
        }
        if($webmail == "plaxo") 
        {
            $user_id=$_POST['user_id'];
            $type="plaxo";
            $dom="plaxo.com";
            $fulldomain="plaxo.com";
            
            if($dom != "qq.com") 
            {
                $_POST['type']="";
                 unset($_SESSION['type']);
            }
        }
		if($webmail == "paracalls") 
        {
            $user_id=$_POST['user_id'];
            $type="paracalls";
            $dom="paracalls.com";
            $fulldomain="paracalls.com";
            
            if($dom != "qq.com") 
            {
                $_POST['type']="";
                 unset($_SESSION['type']);
            }
        }
		/*else if($type=="paracalls")
		{
			include("paracalls.php");
		}*/
        if(isset($_SESSION['type'])) 
        {
            if(isset($_POST["type"])) 
            {
				
                $type = $_POST["type"];
                $domain = $type;
                $fulldomain = "qq.com";
                $dom = $type;
                $type = substr($type,0,strpos($type,"."));
            }
        }
		
        if($type=="live" || $type=="msn")
        {
            include("hotmail.php");
        }
        else if($type=="ymail" || $type=="rocketmail")
        {   
            include("yahoo.php");
			
			
        }
        elseif($type=="googlemail")
        {
            include("gmail.php");
        }
        else if($type=="mail.com") 
        {   
            include("maildotcom.php");
        }
        else if($type=="mail.ru") 
        {   
            include("mailru.php");
        }
		
        else if (in_array($type, $domainArray))
        {
            if($fulldomain == "linkedin.com")
            {
                $fulldomain = substr($user_id,strpos($user_id,"@")+strlen("@"));
                $user_id = substr($user_id,0,strpos($user_id,"@"));
            }
            if($type != "qq.com" && $type != "qq")
            {
                include("$type.php");
            }
        }
        else 
        {
            $mess = "This domain engine is not included with this Contact Importer package. Please contact Improsys.";
        }

        if($type == "qq" && !isset($_POST['sumbit_qq'])) 
        {
            $todo = "callqqaddons";
            $state = "askforqq";
        }
        
        if($dom != "qq.com" && in_array($type, $domainArray)) 
        {
            $contacts = new ContactImporter($_POST['user_id'],$_POST['password']);
            $logged_in = $contacts->login(); // $logged_in=true on successfull login
        
        
            if(is_bool($logged_in) && $logged_in) 
            {	
                if($_POST["type"] != "qq.com") 
                {
                    $str = $contacts->get_address_page(); // Get address page
                    $contacts->parser($str); // Parse Address page and get all name and address in name_array and contact_array
                }
                
                $todo = "printContacts";
                $state = "showContactArea";
            } else {
                $todo = "";
                $state = "showContactArea";
            
            }
        }
    }

    else if($_POST["type"]=="qq.com" && isset($_POST['name_arr']) && isset($_POST['email_arr']) && isset($_POST['sumbit_qq'])) 
    {
        include_once("qq.php");
        $contacts = new ContactImporter($_POST['user_id'],$_POST["password"]);
        $contacts->name_array = $_POST['name_arr'];
        $contacts->email_array = $_POST['email_arr'];
        $logged_in = false;
        if($_POST['login'] == "YES"){
            $logged_in = true;
			$todo = "printContacts";
		}	
        
        $domain = "qq.com";
        $state = "showContactArea";
    } 

// ob_implicit_flush(true);
} else {
    $_POST['user_id'] = "";
}

    if( $todo == "callqqaddons" && $state == "askforqq") {
    /* code for QQ.COM integration */

 ?>
        <html><body>
        <form name="frm_qq" id="frm_qq" action="qqaddons.php" method="post" >
            <input type="hidden" name="user_id" value="<?php echo $_POST["user_id"]; ?>">
            <input type="hidden" name="password" value="<?php echo $_POST['password']; ?>">
            <input type="hidden" name="type" value="qq.com">
        </form>
        <script>
            document.frm_qq.submit();
        </script>
        </body></html>
    
<?php } ?>

