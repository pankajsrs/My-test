<?php /* Smarty version 2.6.18, created on 2012-03-07 13:22:54
         compiled from Z:/raaser/templates/page/contact-us.html */ ?>
<div id="wrapper" class="page termofuse">
	
	<div id="home-headlines" class="headlines fl">
		<div id="home-headlines-text">
			<header>
				<hgroup>
					<h2>Contact Us</h2>
					
				</hgroup>
			</header>
		</div><!-- #home-headlines-text//-->
	</div><!-- #home-headlines //-->
	
	
	<div id="main-container" class="fl">
		<div id="content">
			
				
				 <form action="<?php echo $this->_tpl_vars['BASE_URL']; ?>
page/contact-us" id="frm_get_updates" name="frm_get_updates" method="post">
  					<div id="form-container" class="fl" style="margin-left:250px; margin-top:30px" >
	 				     
                         <div class="normal-field fl">
                                                <p class="label">Your Name</p>
                                                <p><input type="text" name="your_name" id="your_name" value="<?php echo $this->_tpl_vars['data']['your_name']; ?>
" tabindex="1" class="input_box validate[required] normalfield"  />
                                    </p>
                        </div>
                        <br />
    	
	
                        <div class="normal-field fl">
                                                <p class="label">Your Email</p>
                                                <p><input type="text" name="email" id="email"  value="<?php echo $this->_tpl_vars['data']['email']; ?>
" tabindex="3" class="input_box validate[required, custom[email]] normalfield"  />
                                    </p>
                        </div>
                       
                       <br />
                       
                      <div class="normal-field last-field fl">
							<p class="label">Yuor Message</p>
							<p>
                             <textarea name="msg" id="msg" rows="10" cols="50" class="msgfield validate[required]">
                             </textarea></p>
						</div>
                        <br />
                        <div class="normal-field last-field fl" align="center">
							
							<p> <input type="submit" value="Send" name="contacr_us" id="contact_us" class="btnfield" />
                             
						</div>
                        
        </div>					
						
	</form>				              							
					
				<!-- #post-content //-->
			<!-- #article-container //-->
			<div class="fix"></div>
		</div><!-- #content //-->
	</div><!-- #main-container //-->
	
		
<?php echo '
<script>
jQuery(document).ready(function() {
	jQuery(\'#frm_get_updates\').submit(function(){
		jQuery(this).find(\'text, textarea\').each(function(){
			jQuery(this).val(jQuery.trim(jQuery(this).val()));
	      });
		  
	});
	jQuery("#frm_get_updates").validationEngine();
	 
});
 
</script>
'; ?>







  