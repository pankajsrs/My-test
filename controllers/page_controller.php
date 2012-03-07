<?php
	class Page extends Controller
	{
		public $action;
		public $controller;
		# constructor	of class
		function Page()
		{
			global $obj_page;

			try
			{
				$obj_page = new PageModel();
				 
				$this->action	=	isset($_REQUEST['action'])?$_REQUEST['action']:'';
				$this->controller = isset($_REQUEST['controller'])?$_REQUEST['controller']:'';
				 
				switch($this->action)
				{ 	
					case 'privacy-policy':
						$this->privacyPolicyAction();
						break;
					case 'terms-of-use':
						$this->termsOfUseAction();
						break;
					case 'thankyou':
						$this->thankyouAction();
						break;
					case 'contact-us':
							$this->contactusAction();
							break;
					
					case 'captcha':
							$this->captchaAction();
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
		
		 
		function privacyPolicyAction()
		{
			global $smarty, $obj_page, $obj_basic;
						 
			
			$smarty->display(TEMPLATE.'page/privacy-policy.html');
			
		}
		
		function contactusAction()
		{
			global $smarty, $obj_page, $obj_basic;
			
			if(isset($_REQUEST['contacr_us']))
			{
				require_once(FUNCTION_DIR."html2text.php");		
				require_once(LIBRARY_DIR."phpmailer/class.phpmailer.php");				
				$email = ADMIN_MAIL;		
				
				$senderEmail = $_REQUEST['email'];
				$email_data = Array(
					'name' => $_REQUEST['your_name'],
					'email' => $_REQUEST['email'],
					'msg' => $_REQUEST['msg'],
				);
				$smarty->assign('email_data', $email_data);
				
				$body = $smarty->fetch(TEMPLATE.'emails/contact_us.html');
				
				$receiverEmail = $email;
				$subject = "Contact Us - Freedom Pop";

				$mail = new PHPMailer();					
				$mail->IsHTML(true);
				$mail->CharSet = "text/html; charset=UTF-8;";						
				$mail->From = $senderEmail;
				$mail->FromName = WEBSITE_NAME; // First name, last name
				$mail->AddAddress($email, '');
				$mail->AddReplyTo($senderEmail);						
				$mail->Subject =  $subject;
				$mail->Body =  $body;
				$mail->AltBody = convert_html_to_text($body);
				
				if($mail->Send())
				{
					$_SESSION['ERROR']['class'] = 'done';
					$_SESSION['ERROR']['message'] = "You request has been submitted successfully!";
					header('Location: '.getURL('page/contact-us'));
					exit;
				} 
				else
				{
					$_SESSION['ERROR']['class'] = 'error';
					$_SESSION['ERROR']['message'] = "Unable to submit your request. Please try again!";
					header('Location: '.getURL('page/contact-us'));
					exit;
				}
			}
			
			$smarty->display(TEMPLATE.'page/contact-us.html');
			
			
			
		}
		
		
		function thankyouAction()
		{
			global $smarty, $obj_index, $obj_basic;
			/*
			if(!isset($_SESSION['FROM_SIGNUP']))
			{
				header('Location: '.getURL(''));
				exit;
			}*/
			unset($_SESSION['FROM_SIGNUP']);		
			$smarty->display(TEMPLATE.'page/thankyou.html');
		}
		
		
		function captchaAction()
		{
			$_SESSION['layout'] = 'ajax';
			/*===============================================================
			 General captcha settings
			===============================================================*/
			// captcha width
			$captcha_w = 150;
			// captcha height
			$captcha_h = 50;
			// minimum font size; each operation element changes size
			$min_font_size = 12;
			// maximum font size
			$max_font_size = 18;
			// rotation angle
			$angle = 20;
			// background grid size
			$bg_size = 13;
			// path to font - needed to display the operation elements
			$font_path = 'fonts/courbd.ttf';
			// array of possible operators
			$operators=array('+','-','*');
			// first number random value; keep it lower than $second_num
			$first_num = rand(1,5);
			// second number random value
			$second_num = rand(6,11);
		
			/*===============================================================
			 From here on you may leave the code intact unless you want
			or need to make it specific changes.
			===============================================================*/
		
			shuffle($operators);
			$expression = $second_num.$operators[0].$first_num;
			/*
			 operation result is stored in $session_var
			*/
			eval("\$session_var=".$second_num.$operators[0].$first_num.";");
			/*
			 save the operation result in session to make verifications
			*/
			$_SESSION['security_number'] = $session_var;
			/*
			 start the captcha image
			*/
			$img = imagecreate( $captcha_w, $captcha_h );
			/*
			 Some colors. Text is $black, background is $white, grid is $grey
			*/
			$black = imagecolorallocate($img,0,0,0);
			$white = imagecolorallocate($img,255,255,255);
			$grey = imagecolorallocate($img,215,215,215);
			/*
			 make the background white
			*/
			imagefill( $img, 0, 0, $white );
			/* the background grid lines - vertical lines */
			for ($t = $bg_size; $t<$captcha_w; $t+=$bg_size){
				imageline($img, $t, 0, $t, $captcha_h, $grey);
			}
			/* background grid - horizontal lines */
			for ($t = $bg_size; $t<$captcha_h; $t+=$bg_size){
				imageline($img, 0, $t, $captcha_w, $t, $grey);
			}
		
			/*
			 this determinates the available space for each operation element
			it's used to position each element on the image so that they don't overlap
			*/
			$item_space = $captcha_w/3;
		
			/* first number */
			imagettftext(
					$img,
					rand(
							$min_font_size,
							$max_font_size
					),
					rand( -$angle , $angle ),
					rand( 10, $item_space-20 ),
					rand( 25, $captcha_h-25 ),
					$black,
					$font_path,
					$second_num);
		
			/* operator */
			imagettftext(
					$img,
					rand(
							$min_font_size,
							$max_font_size
					),
					rand( -$angle, $angle ),
					rand( $item_space, 2*$item_space-20 ),
					rand( 25, $captcha_h-25 ),
					$black,
					$font_path,
					$operators[0]);
		
			/* second number */
			imagettftext(
					$img,
					rand(
							$min_font_size,
							$max_font_size
					),
					rand( -$angle, $angle ),
					rand( 2*$item_space, 3*$item_space-20),
					rand( 25, $captcha_h-25 ),
					$black,
					$font_path,
					$first_num);
		
			/* image is .jpg */
			header("Content-type:image/jpeg");
			/* name is secure.jpg */
			header("Content-Disposition:inline ; filename=secure.jpg");
			/* output image */
			imagejpeg($img);
		}
		
		function termsOfUseAction()
		{
			global $smarty, $obj_page, $obj_basic;
		
			
			$smarty->display(TEMPLATE.'page/terms-of-use.html');
			
		}
	}