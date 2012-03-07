<?php /* Smarty version 2.6.18, created on 2012-03-07 13:43:35
         compiled from Z:/raaser/templates/user/findfriend.html */ ?>
<div id="wrapper" class="page">
<div id="home-headlines" class="headlines fl">
		<div id="home-headlines-text">
			<header>
				<hgroup>
					<h2>Get PRIORITY Access at Launch!</h2>
					
				</hgroup>
			</header>
		</div><!-- #home-headlines-text//-->
	</div><!-- #home-headlines //-->
	
	
	<div id="main-container" class="fl">
		<div id="content">
			<div id="page-heading">
				<header>
					<h3>Find friends already signed up for FreedomPop.<br />People connected to the most friends will get first priority for service at launch</h3>
				</header>
			</div><!-- #page-heading //-->
			
			<div id="find-friends-form-container">
  
   	<form action="<?php echo $this->_tpl_vars['BASE_URL']; ?>
user/connectfriend/" id="frm_findfriend" name="frm_findfriend" method="post">
  	
    <div id="form-container" class="fl">
					
						<div class="normal-field fl">
							<p class="label">Email Address</p>
							<p><?php echo $this->_tpl_vars['email_request']; ?>
<input type="text" name="user_id" id="User_Id"  value="<?php if ($this->_tpl_vars['email_from'] == ''): ?><?php echo $this->_tpl_vars['prefill_email']; ?>
<?php else: ?><?php echo $this->_tpl_vars['email_from']; ?>
<?php endif; ?>" class="input_box validate[required, custom[email]] normalfield"  /></p>
						</div>
						
						<div class="normal-field fl">
							<p class="label">Password</p>
							<p><input type="password" name="password" id="Password"  value="<?php echo $this->_tpl_vars['data']['password']; ?>
" class="input_box validate[required] normalfield"  /></p>
						</div>
						
					</div><!-- #form-container //-->
					
						<div class="normal-field submitfield fl">
							
                            <input type="submit"  value="" id="find_friends_button" />
						</div>	
				</form>
                
                </div><!-- #find-friends-form-container //-->
			
			<div id="bottom-content">
				<p>We don't store your email or password information or <br /> contact your friends without your permission.</p>
			
            <ul id="logos">
					<li><a href="#"><img src="<?php echo $this->_tpl_vars['BASE_URL']; ?>
/public/images/msn.png" alt="MSN" /></a></li>
					<li><a href="#"><img src="<?php echo $this->_tpl_vars['BASE_URL']; ?>
/public/images/yahoo.png" alt="Yahoo" /></a></li>
					<li><a href="#"><img src="<?php echo $this->_tpl_vars['BASE_URL']; ?>
/public/images/gmail.png" alt="Gmail" /></a></li>
					<li><a href="#"><img src="<?php echo $this->_tpl_vars['BASE_URL']; ?>
/public/images/aol.png" alt="AOL" /></a></li>
                    
			</ul>
				
				<div class="fix"></div>
    
    
    
    
    
 
        <?php if ($this->_tpl_vars['from_signup'] == 'y'): ?>   
        <a href="<?php echo $this->_tpl_vars['BASE_URL']; ?>
page/thankyou" id="skiplink" >skip</a>
       
		<?php endif; ?>
    
			 </div><!-- #bottom-content //-->
			</div>
			
			<div class="fix"></div>
		</div><!-- #content //-->
	</div><!-- #main-container //-->
<?php echo '
<script>
jQuery(document).ready(function() {
	jQuery(\'#frm_findfriend\').submit(function(){
		jQuery(this).find(\'input:text\').each(function(){
			jQuery(this).val(jQuery.trim(jQuery(this).val()));
	      });
	});
	jQuery("#frm_findfriend").validationEngine();
});
 
</script>
'; ?>

  