<?php /* Smarty version 2.6.18, created on 2012-03-07 13:23:11
         compiled from Z:/raaser/templates/emails/registration_successful.html */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Verification Email</title>
</head>
<body>
Hello <?php echo $this->_tpl_vars['email_data']['first_name']; ?>
,<br />
<br />
Thanks for signing up! <br /><br /><br />

Below is your login details,<br />
<strong>Email : </strong><?php echo $this->_tpl_vars['email_data']['email']; ?>
<br /> 
<strong>Password : </strong><?php echo $this->_tpl_vars['email_data']['password']; ?>
<br /> 
<br /> <br /> <br /> <br /> 



Regards,<br /> 
Freedom Pop
</body>
</html>