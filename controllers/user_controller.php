<?php
set_time_limit(600);///Set time limit for script
require_once(LIBRARY_DIR."phpmailer/class.phpmailer.php");
require_once(MODEL_DIR.'index_model.php');	
class User extends Controller
{
	public $action;
	public $controller;
	#	constructor	of class
	function User()
	{
		global $obj_user,$obj_index;

		try
		{
			$obj_user = new UserModel();
			$obj_index = new IndexModel();

			$this->action	=	isset($_REQUEST['action'])?$_REQUEST['action']:'';
			$this->controller = isset($_REQUEST['controller'])?$_REQUEST['controller']:'';

			switch($this->action)
			{
				case 'index':
					$this->findFriendAction();
					break;
				case 'findfriend':
					$this->findFriendAction();
					break;
				case 'connectfriend':
					$this->connectFriendAction();
					break;
				case 'friendlanding':
					$this->friendLandingAction();
					break;
				case 'unsubscribe':
					$this->unsubscribeAction();
					break;
				case 'user-subscription':
					$this->userSubscriptionAction();
					break;

    			case 'connect-to-friends':
					$this->connectTofriendsAction();
					break;
					
				case 'remove':
					$this->remove();
					break;
					
				default :
					die('ACTION NAME NOT AVAILABLE');
			}
		}
		catch(Exception	$e)
		{
			trigger_error($e);
		}
	}

	function indexAction()
	{
		$this->findFriendAction();
	}

	function findFriendAction()
	{
		global $smarty, $obj_user, $obj_basic, $CONTACT_SERVICES;
		$data = Array();
		if(!isset($_SESSION['USER_DATA']))
		{
			$_SESSION['ERROR']['class'] = 'error';
			$_SESSION['ERROR']['message'] = "You are not authorized to access this page";
			header('Location: '.getURL('index/sign-up'));
			exit;
		}
		
		
		if(isset($_SESSION['FROM_SIGNUP']) && empty($_SESSION['ERROR']['message']))
		{
			$smarty->assign('flashMessage', Array('class' => 'done', 'message' => 'You have been signup with us successfully, Please check your mail for login details!'));
			
			$data['email'] = $_SESSION['USER_DATA']['email'];
			
		}
		@list($website, $ext) = split('[.]', str_replace("@", "", strstr($data['email'], '@')));
			
		if(isset($_SESSION['FROM_SIGNUP']) && !in_array($website, $CONTACT_SERVICES))
		{
			header('Location: '.getURL('page/thankyou'));
			exit;
		}
		$from_signup = isset($_SESSION['FROM_SIGNUP'])?$_SESSION['FROM_SIGNUP']:'y';
		$smarty->assign('from_signup', $from_signup);
		$smarty->assign('data', $data);
		
		if(isset($_SESSION['FROM_SIGNUP']))
			unset($_SESSION['FROM_SIGNUP']);

		$prefile_email = $_SESSION['USER_DATA']['email'];
		
		if(isset($_SESSION['USER_DATA']['email_from']))
			$prefile_email = $_SESSION['USER_DATA']['email_from'];
		
			$smarty->assign('prefill_email',$prefile_email );
			
		$smarty->display(TEMPLATE.'user/findfriend.html');
	}

	function connectFriendAction()
	{
		global $smarty, $obj_user, $obj_basic, $obj_index;
		$data = Array();
	 
		
		if(!isset($_POST['user_id']) || empty($_POST['user_id']))
		{
			$_SESSION['ERROR']['class'] = 'error';
			$_SESSION['ERROR']['message'] = "You are not authorized to access this page";
			header('Location: '.getURL('user/findfriend'));
			exit;
		}
		
		
		$name = trim($_SESSION['USER_DATA']['first_name'].' '.$_SESSION['USER_DATA']['last_name']);
		$email = $_POST['user_id'];
		$pass = $_POST['password'];
		list($email_name, $type) = explode("@", $email);
		$_POST["type"] = $type;
		$user_id = $_POST['user_id'] = $email_name;
		
		$type="";
		$domain = "";
		include(IMPROVESYS_DIR."includes/grab_globals.lib.php");
		include_once(IMPROVESYS_DIR."includes/csvutils.php");
		
		if(isset($_POST["type"]))
		{
			$type=$_POST["type"];
			$domain = $type;
			$type= substr($type,0,strpos($type,"."));
		}
		if(!function_exists('curl_init'))
		{
			$_SESSION['ERROR']['class'] = 'error';
			$_SESSION['ERROR']['message'] = "CURL Library is not installed. Please install it to let this application execute properly.";
			header('Location: '.getURL('user/findfriend'));
			exit;
		}
		
		if(isset($user_id)||isset($_POST['uin']))
		{
			if($type=="live" || $type=="msn")
			{
				$path = IMPROVESYS_DIR."includes/hotmail.php";
			}
			else if($type=="ymail" || $type=="rocketmail")
			{
				$path = IMPROVESYS_DIR."includes/yahoo.php";
			}
			else
			{
				$path = IMPROVESYS_DIR."includes/"."$type.php";
				
			}
			if(!file_exists($path))
			{
				$_SESSION['ERROR']['class'] = 'error';
				$_SESSION['ERROR']['message'] = "We doesn't support your email address";
				header('Location: '.getURL('user/findfriend'));
				exit;
			}
			
			include(IMPROVESYS_DIR."includes/"."$type.php");
		}
		
		$contacts=new ContactImporter($_POST['user_id']."@".$_POST['type'],$_POST['password']);
		/// $logged_in=true on successfull login
		$logged_in=$contacts->login();
		
		
		if(is_bool($logged_in) && $logged_in)
		{
			///Get address page
			//$str=get_address_page();
			$str=$contacts->get_address_page();
			//Parse Address page and get all name and address in name_array and contact_array
			//parser($str);
			$contacts->parser($str);
		}
		else if(is_bool($logged_in) && !$logged_in)
		{
			$error_message = "Please check your username or password.";
		}
  
		$list_contacts = Array();
		foreach($contacts->name_array as $key=>$val)
		{
			$val = $val=="" ? "NA":$val;
			$list_contacts[] = Array('name' => $val, 'email' => $contacts->email_array[$key]);
		} 
		
		 
		foreach($list_contacts as $key => $value)
		{
			$status  = $obj_index->isEmailExist($value['email']);
			$list_contacts[$key]['is_registered'] = $status;
		}
		 
		$from_signup = isset($_SESSION['FROM_SIGNUP'])?$_SESSION['FROM_SIGNUP']:'';
		$smarty->assign('from_signup', $from_signup);
		
		$priority = $obj_user->get_priority($_SESSION['USER_DATA']['signup_id']);
 
		$smarty->assign('priority', $priority[0]);
		$smarty->assign('total', count($list_contacts));
		
		if(empty($error_message) && empty($list_contacts))
		{
			$error_message = "You do not have any contacts in your address book.";
		}
		
		
		
		if(!empty($error_message))
		{
			$_SESSION['ERROR']['class'] = 'error';
			$_SESSION['ERROR']['message'] = $error_message;		
			$_SESSION['USER_DATA']['email_from'] = $email;		
		
			header('Location: '.getURL('user/findfriend'));
			exit;
		}
		
		
		$smarty->assign('email_from', $email);
		$smarty->assign('list_contacts', $list_contacts);
		$smarty->assign('password_box', trim($_POST['password']));
		$smarty->display(TEMPLATE.'user/connectfriend.html');
	 
	}
	
	function friendLandingAction()
	{
 
		global $smarty,$obj_user,$obj_basic,$obj_index;
		 $signupid = isset($_REQUEST['sid'])?$_REQUEST['sid']:'';
		 
		$email = getParameter('email');
		if(isset($_POST['save_friend']) && !empty($email))
		{	
			
			$is_error = 1;
			$error_msg =  'Unable to send email, please try again!';	
			
			if($obj_index->isEmailExist($email))
			{
				$error_msg =  'Email already in use, please try another!';
			}
			else
			{
				
				#friends signup
				$first_name = getParameter('first_name');
				$password = generate_random(7);
				$obj_basic->set_table('signup');
				$obj_basic->set_primary('signup_id');
				$data =Array(
					'first_name' => $first_name,
					'last_name' => getParameter('last_name'),
					'password' => $password,
					'email' => $email,
					'email_lower' => strtolower($email),
					'postal_code' => getParameter('postal_code'),
					'time_created' => date('Y-m-d H:i:s'),
					'opt_out' => '1',
					'status' => '0',
					'hear_from' => getParameter('how_you_hear'),
				);	
				
				
			    $signup_id = $obj_basic->insert_data($data);
				$login_detail = $obj_user->get_sender_detail($signupid);
				
				
				$decoded_id = $login_detail['signup_id']; 
				

				#insert ralation
				$obj_basic->set_table('contact');
				$obj_basic->set_primary('contact_id');
				$data_login =Array(
					'email' => $email,
					'first_name' => $first_name,
					'last_name' => getParameter('last_name'),
					'owner_id' => $decoded_id,
					'signup_id' => $signup_id,
				);	
				
				$obj_basic->insert_data($data_login);
		}
			
		
			if(isset($signup_id) && !empty($signup_id))
			{
				
				
				require_once(FUNCTION_DIR."html2text.php");						
				$senderEmail = ADMIN_MAIL;		
				
				$email_data = Array(
					'first_name' => $first_name,
					'email' => $email,
					'password' => $password,
				);
				$smarty->assign('email_data', $email_data);
				$body = $smarty->fetch(TEMPLATE.'emails/registration_successful.html');
				
				$receiverEmail = $email;
				$subject = "Registration Successful - Freedom Pop";

				$mail = new PHPMailer();					
				$mail->IsHTML(true);
				$mail->CharSet = "text/html; charset=UTF-8;";						
				$mail->From = $senderEmail;
				$mail->FromName = WEBSITE_NAME; // First name, last name
				$mail->AddAddress($email, $first_name);
				$mail->AddReplyTo($senderEmail);						
				$mail->Subject =  $subject;
				$mail->Body =  $body;
				$mail->AltBody = convert_html_to_text($body);
				if($mail->Send())
				{
					$obj_basic->commit_transaction();
					$obj_basic->off_autocommit();
					$is_error = '0';
				} 
			}
			if(!$is_error)
			{
				$obj_user->loginUser($email, $password);
				$_SESSION['FROM_SIGNUP'] = 'y';
				header('Location: '.getURL('user/findfriend'));
				exit;
			}
			else
			{		
				$obj_basic->rollback_transaction();
				$obj_basic->off_autocommit();
				
				$smarty->assign('flashMessage', Array('class' => 'error', 'message' => $error_msg));								
				$smarty->assign('data', $_POST);
			}
		}
		else
		{
			
			
		 
			
			$param = base64_decode($_REQUEST['param']); 
			
			list($name, $email) = split(",", $param);
			 
			$name = split(" ", $name);
			$fname = $name[0];
			$lname = $name[1];
			
			
			$login_detail = $obj_user->get_sender_detail($signupid);
		    $sid = $login_detail['signup_id'];
			 
			
			$smarty->assign('signupid', $signupid);

			
			if($login_detail)
			{
				
				$first_name = $login_detail['first_name'];
				$last_name = $login_detail['last_name'];
		
				$smarty->assign('first_name', $first_name);
				$smarty->assign('last_name', $last_name);
				
				$smarty->assign('fname', $fname);
				$smarty->assign('lname', $lname);
				$smarty->assign('email', $email);
			}
			else
			{
				$error = "Invalid link";	
				$smarty->assign('error', $error);
				$smarty->display(TEMPLATE.'page/link.html');
				return '';
			}	
			
		}
		$how_you_hear = Array(
					'Friend' => 'Friend',		
					'Search Engine' => 'Search Engine',
				);
		$smarty->assign('how_you_hear', $how_you_hear);
		$smarty->assign('signupid', $signupid);
		$smarty->display(TEMPLATE.'user/friendlanding.html');
	}
	
	
	function unsubscribeAction()
	{
		global $smarty,$obj_user,$obj_basic,$obj_index;
		
		if($_SESSION['USER_DATA']['signup_id'] && empty($_REQUEST['sid']))
		{
			$signup_id = isset($_SESSION['USER_DATA']['signup_id'])?$_SESSION['USER_DATA']['signup_id']:'';
		}
		elseif(isset($_REQUEST['sid']) && !empty($_REQUEST['sid']))
		{
			$signup_id = isset($_REQUEST['sid'])?$_REQUEST['sid']:'';
		}
		$obj_basic->set_table('notifications');
		
	    $condition = "MD5(signup_id)='$signup_id'";
		$data = $obj_user->get_from_notification($condition);
		
		if(!empty($data))
		{
			$id = $obj_basic->delete_record($condition);
			
			if($id)
			{
				$smarty->assign('success_message', 'You have Successfully Unsubscribe from all FreedomPop emails');	
			}
			else
			{
				$error_msg =  'Something went wrong';	
				$smarty->assign('flashMessage', Array('class' => 'error', 'message' => $error_msg));	
			}
		}
		else
		{
			$smarty->assign('success_message', 'You have no FreedomPop emails to Unsubscribe');	
		}
		
		$smarty->display(TEMPLATE.'user/unsubscribe.html');
	}
	
	function userSubscriptionAction()
	{
		global $smarty,$obj_basic;
		
		if(!isset($_SESSION['USER_DATA']))
		{
			$_SESSION['ERROR']['class'] = 'error';
			$_SESSION['ERROR']['message'] = "You are not authorized to access this page";
			header('Location: '.getURL('index/sign-up'));
			exit;
		}
		if(isset($_POST['notification_button']) && !empty($_POST['notification_button']))
		{
			$notification = isset($_POST['notification'])?$_POST['notification']:'';
			$signup_id = isset($_SESSION['USER_DATA']['signup_id'])?$_SESSION['USER_DATA']['signup_id']:'';
			if(empty($notification))
			{
				$error_msg =  'Please Select atleast One Checkbox from the list';	
				$smarty->assign('flashMessage', Array('class' => 'error', 'message' => $error_msg));
			}
			
			if(!empty($signup_id))
			{
				#insert notifications
				$obj_basic->set_table('notifications');
				
				foreach($notification as $value)
				{
					if(!empty($value))
					{
						$data_login =Array(
							'signup_id' => $signup_id,
							'service' => $value,
							'status' => '1',
						);	
						$obj_basic->insert_data($data_login);
					}
				}
				$smarty->assign('success_message', 'You have Successfully saved your notification email');	
			}
			else
			{
				$error_msg =  'Something went wrong';	
				$smarty->assign('flashMessage', Array('class' => 'error', 'message' => $error_msg));	
			}
		}
		
		
		
		$smarty->display(TEMPLATE.'user/user-subscription.html');
	}
		
	function ers($ers)
	{
		if (!empty($ers))
		{
			$contents="";
			foreach ($ers as $key=>$error)
				$contents.="{$error}<br >";

			return $contents;
		}
	} 
	
	#send mail to selected friends
	function connectTofriendsAction()
	{
		global $smarty,$obj_user,$obj_index,$obj_basic;

		#validation
		
		
		
		//print_r($_POST);exit;
		
	   //echo count($_POST); exit;
	 
	  // if(count($_POST) == 2)
	   
		
		if(empty($_POST['list_contacts']))
		{
			$error_msg =  'Please Select atleast One Contact from the list';	
			$smarty->assign('flashMessage', Array('class' => 'error', 'message' => $error_msg));
			
		}
		else
		{
 
			$first_name = $_SESSION['USER_DATA']['first_name'];
			$last_name = $_SESSION['USER_DATA']['last_name'];
		    $signup_id = $_SESSION['USER_DATA']['signup_id'];
			//$email_from = trim($_SESSION['email']);
			
			$smarty->assign('signup_id', md5($signup_id));
			//$smarty->assign('signup_id', $signup_id);
			
			$smarty->assign('first_name', $first_name);
			$smarty->assign('last_name', $last_name);
			
			$subject = "Invitation".' '.WEBSITE_NAME ;
			
			foreach($_POST['list_contacts'] as $value)
			{
				list($value, $name) = split("---", $value);
				
				
				#send invitation mail to frinds
				$mail = new PHPMailer();					
				$mail->IsHTML(true);
				$mail->CharSet = "text/html; charset=UTF-8;";						
				$mail->From = ADMIN_MAIL;
			    $mail->FromName = WEBSITE_NAME; // First name, last name
				$mail->AddReplyTo(ADMIN_MAIL);						
				$mail->Subject =  $subject;
				$mail->AddAddress($value);
				
				
				
				 if($obj_index->isEmailExist($value))
				 {
					
					$smarty->assign('signup', 'f');	
					$friend_data= $obj_index->get_signup($value);
					
					$smarty->assign('already_signup_id', md5($friend_data[0]['signup_id']));	
				 }
				
				$param = base64_encode($name .','.$value); 
				$smarty->assign('param', $param);
				
				$smarty->assign('email', md5($value));
				
				$body = $smarty->fetch(TEMPLATE.'emails/invitation.html');
				$mail->Body =  $body;
				
				
			 if($mail->Send())
				{					
					
					#check email exist in lead table or not
					if($obj_user->isEmailExist_lead($value))
					 {
						
						$obj_basic->set_table('lead');
						$data = array(
							'time_last_contact' => date('Y-m-d H:i:s'),
							'signup_id' => $signup_id
						);
						$condition = array('email' => $value);
						$obj_basic->update_data($data, $condition);
					 }
					 else
					 {
						#insert into lead table
						$obj_basic->set_table('lead');
						$obj_basic->set_primary('lead_id');
						
						$data_login =Array(
							'name' => $name,
							'email' => $value,
							'emails_sent' => '1',
							'opt_out' => '1',
							'signup_id' => $signup_id,
							'time_created' => date('Y-m-d H:i:s'),
							'time_last_contact' => date('Y-m-d H:i:s'),
						);		
					 $obj_basic->insert_data($data_login);
					 }
				}
				$mail->ClearAddresses();
				unset($mail);
			}
			$message_send = "Your invitation has been send";
			$smarty->assign('message_send', $message_send);
		}
		//$this->connectFriendAction();
		$smarty->display(TEMPLATE.'page/thankyou.html');

	}
	
	function remove()
	{
		global $obj_basic,$smarty,$obj_user;
		
		$signup_id = isset($_REQUEST['sid'])?$_REQUEST['sid']:'';
		$email = isset($_REQUEST['em'])?$_REQUEST['em']:'';
		$obj_basic->set_table('lead');
		$condition = "MD5(email) = '$email'";
		
		$data = $obj_user->get_lead_data($email);
		if(!empty($data))
		{
			$result = $obj_user->get_sender_detail( md5($data[0]['signup_id']));
			$smarty->assign('first_name', $result['first_name']);
			$smarty->assign('last_name', $result['last_name']);
		}
		else
		{
			$message = "Your contact  has already remove from Freedop pop";	
			$smarty->assign('message', $message);
		}
		
		$obj_basic->delete_record($condition);
		
		$smarty->display(TEMPLATE.'page/thankyou_no.html');
	}
}