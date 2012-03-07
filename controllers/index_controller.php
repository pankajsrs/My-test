<?php
	require_once(LIBRARY_DIR."phpmailer/class.phpmailer.php");
	
	class Index extends Controller
	{
		public $action;
		public $controller;
		#	constructor	of class
		function Index()
		{
			global $obj_index;

			try
			{
				$obj_index = new IndexModel();
				 
				$this->action	=	isset($_REQUEST['action'])?$_REQUEST['action']:'';
				$this->controller = isset($_REQUEST['controller'])?$_REQUEST['controller']:'';
				
				if(isset($_SESSION['USER_DATA']) && in_array($this->action, Array('sign-in', 'sign-up')))
				{
					header('Location: '.getURL('user/findfriend'));
					exit;
				}
				
				switch($this->action)
				{ 	
					case 'index':		 
						$this->indexAction();
						break;
					case 'sign-up':
						$this->signUpAction();
						break;	
					case 'sign-in':
						$this->signInAction();
						break;								
					case 'logout':
						$this->logoutAction();
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
			global $smarty, $obj_index, $obj_basic;
			
			if(isset($_SESSION['USER_DATA']))
			{
				header('Location: '.getURL('user/findfriend'));
				exit;
			}
			header('Location: '.getURL('index/sign-up'));
			exit;
			
			
			$smarty->display(TEMPLATE.'index/index.html');
			
		}
	 
		function signUpAction()
		{
			global $smarty, $obj_index, $obj_basic;
			
			
			# form submit
			
			if(isset($_POST['email']))
			{
				$is_error = 1;
				$error_msg =  'Unable to send you email, Please try again!';
				
				$email = getParameter('email');
				$obj_basic->begin_transaction();
				/*if($_POST['secure'] != $_SESSION['security_number'])
				{
					$error_msg =  'Wrong capcha, please try again!';
				}
				else */
				if($obj_index->isEmailExist($email))
				{
					$error_msg =  'Email already in use, please try another!';
				}
				else if(!$obj_index->isZipExist(getParameter('postal_code')))
				{
					$error_msg =  'Please check the zip code, Zip code should be US base only!';
				}
				else
				{
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
					);				
					$signup_id = $obj_basic->insert_data($data);
					if($signup_id)
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
				}
				
				if(!$is_error)
				{
					$obj_index->loginUser($email, $password);
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
			
			
			$smarty->display(TEMPLATE.'index/sign-up.html');
			
		}
		
		
		
		
		function signInAction()
		{
			global $smarty, $obj_index, $obj_basic;
			
			if(isset($_POST['email']))
			{
				$email = getParameter('email');
				$password = getParameter('password');
				if($obj_index->loginUser($email, $password))
				{
					header('Location: '.getURL('user/findfriend'));
					exit;
				}
				$error_msg = "Wrong Email and/or Password, please try again!";
				$smarty->assign('flashMessage', Array('class' => 'error', 'message' => $error_msg));
			}
			
			
			$smarty->display(TEMPLATE.'index/sign-in.html');
			
		}
		
		function logoutAction()
		{
			session_unset();
			session_destroy();
			header('Location: '.getURL('index/sign-up'));
			exit;
		}
		 
	}