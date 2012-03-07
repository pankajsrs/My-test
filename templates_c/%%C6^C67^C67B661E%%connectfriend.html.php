<?php /* Smarty version 2.6.18, created on 2012-03-07 13:25:59
         compiled from Z:/raaser/templates/user/connectfriend.html */ ?>

<div id="wrapper" class="page foundfriends">
	
	
	<div id="home-headlines" class="headlines fl">
		<div id="home-headlines-text">
			<header>
				<hgroup>
					<?php if ($this->_tpl_vars['message_send'] != ''): ?>
  	                   <b><?php echo $this->_tpl_vars['message_send']; ?>
</b>
                    <?php endif; ?>
                    <h2>Connect with your  <?php echo $this->_tpl_vars['total']; ?>
 friends and reserve your priority position of <?php echo $this->_tpl_vars['priority']+1327; ?>
</h2> 
				</hgroup>
			</header>
		</div><!-- #home-headlines-text//-->
	</div><!-- #home-headlines //-->
	
	
	<div id="main-container" class="fl">
		<div id="content">
			<div id="page-heading">
				<header>
					<h3>Choose which friends you want to connect with on FreedomPop <br /> and click the "connect with selected friends" button</h3>
					<p>You'll improve your priority position for each FreedomPop friend you connect with. If you select someone <br /> 
					who is not here, they will be invited to join you. Every friend you connect with will improve your priority position.</p>
				</header>
			</div><!-- #page-heading //-->
			
			 <form action="<?php echo $this->_tpl_vars['BASE_URL']; ?>
user/connect-to-friends/" id="frm_connect" name="frm_connect" method="post">
			<div id="foundfriends-content" class="fl">
				<a onclick="check();"  style="cursor:pointer;" id="connect-selected-friend">Connect with selected friends</a>
				
				<div id="option-form-container" class="fl">
					<p class="selectall"><span class="option"><input type="checkbox"  name="select_checkbox" id="select_checkbox"  class="styled" /></span> SELECT ALL <span class="register-alredy">Already Registered!</span></p>
					
					<div id="select-container" class="flexcroll">
						<div id="option-content-container">
							
								<?php $_from = $this->_tpl_vars['list_contacts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['value']):
?>   	   		
		   	
         <p <?php if ($this->_tpl_vars['value']['is_registered'] == '1'): ?> class="colored" <?php endif; ?>> <span class="option"><input type="checkbox" name="list_contacts[]" value="<?php echo $this->_tpl_vars['value']['email']; ?>
---<?php echo $this->_tpl_vars['value']['name']; ?>
"  class="styled " id="ckd_<?php echo $this->_tpl_vars['key']; ?>
" /></span> 
		 <?php echo $this->_tpl_vars['value']['name']; ?>

		 <?php if ($this->_tpl_vars['value']['name'] != $this->_tpl_vars['value']['email']): ?>
		 [<?php echo $this->_tpl_vars['value']['email']; ?>
]
		 <?php endif; ?>
		 </p> 
           
	   	<?php endforeach; endif; unset($_from); ?>
        
         <input type="hidden" name="email_box" value="<?php echo $this->_tpl_vars['email_from']; ?>
">
        <input type="hidden" name="password_box" value="<?php echo $this->_tpl_vars['password_box']; ?>
">
							
						</div><!-- #option-content-container //-->	
					</div><!-- #select-container //-->
					
				</div><!-- #option-form-container //-->
				
				<?php if ($this->_tpl_vars['from_signup'] == 'y'): ?>
	   	<a href="<?php echo $this->_tpl_vars['BASE_URL']; ?>
page/thankyou" >SKIP</a>
	   	<?php endif; ?>   
			</div><!-- #foundfriends-content //-->
</form>	
			
			<div class="fix"></div>
		</div><!-- #content //-->
	</div><!-- #main-container //-->
	






<?php echo '
	<script>

//jQuery(\'#select_checkbox\').click(function() {

jQuery(\'#select_checkbox\').click(function() {
 
	jQuery(\'input:checkbox\').each(function() { 
		if(jQuery(\'#select_checkbox\').is(\':checked\'))
		{
			 if(jQuery(this).attr(\'name\') == \'list_contacts[]\' )
			 {
				jQuery(".ez-checkbox").addClass("ez-checked");
				jQuery(this).attr(\'checked\', true);
			 }
		}
		else
		{			
			 if(jQuery(this).attr(\'name\') == \'list_contacts[]\' )
			 {
				jQuery(".ez-checkbox").removeClass("ez-checked");
				jQuery(this).attr(\'checked\', false);
			 }
		}
	}); 
});
</script>
 <script>    
function check()
{
	var checked = false;
	jQuery(\'input:checkbox\').each(function() { 
		if(jQuery(this).attr(\'name\') == \'list_contacts[]\' && jQuery(this).is(\':checked\'))
		{
			checked = true; 
		}
	});
	if(!checked)
	{
		alert(\'Please select atleast one friend !\');
	}
	else
	{
		document.getElementById(\'frm_connect\').submit();
	}
}
</script>

'; ?>
	