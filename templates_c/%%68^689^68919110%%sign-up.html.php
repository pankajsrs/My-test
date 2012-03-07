<?php /* Smarty version 2.6.18, created on 2012-03-07 13:22:29
         compiled from Z:/raaser/templates/index/sign-up.html */ ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['public_url']; ?>
js/easytooltip.js"></script>

<div id="cols" class="box">
 <hr class="noscreen" />

<div id="home-headlines" class="headlines fl">
		<div id="home-headlines-text">
			<header>
				<hgroup>
					<h2>The internet is a right, not a privilege -</h2>
					<h2 class="second-headline">Free mobile services for all!</h2>
				</hgroup>
			</header>
		</div><!-- #home-headlines-text//-->
	</div><!-- #home-headlines //-->
	
	<div id="main-container" class="fl">
		<div id="content" class="fl">
			<div id="page-heading">
				<header>
					<h3>Get early access and launch updates by providing information below.</h3>
				</header>
			</div><!-- #page-heading //-->
 
 <div id="content-left" class="fl">
 <form action="<?php echo $this->_tpl_vars['BASE_URL']; ?>
index/sign-up/" id="frm_get_updates" name="frm_get_updates" method="post">
  	<div id="form-container" class="fl">
	 <div class="small-text-field fl">
		<p class="label">First Name</p>
		<p><input type="text" name="first_name" id="first_name" value="<?php echo $this->_tpl_vars['data']['first_name']; ?>
" tabindex="1" class="input_box validate[required] smallfield"  /></p>
    </div>

	<div class="small-text-field last fl">
			<p class="label">Last Name</p>
			<p><input type="text" name="last_name" id="last_name"  value="<?php echo $this->_tpl_vars['data']['last_name']; ?>
" tabindex="2" class="input_box validate[required] smallfield"  /></p>
	</div>
    	
	<div class="normal-field fl" >
							<p class="label">Email Address</p>
							<p><input type="text" name="email" id="email"  value="<?php echo $this->_tpl_vars['data']['email']; ?>
" tabindex="3" class="input_box validate[required, custom[email]] normalfield"  />
            	</p>
	</div>
     
<div class="normal-field last-field fl">
							<p class="label">Zip Code</p>
							<p><input type="text" name="postal_code" id="postal_code" tabindex="4"  value="<?php echo $this->_tpl_vars['data']['postal_code']; ?>
" class="input_box validate[required, custom[onlyNumberSp, minSize[5], maxSize[5]] normalfield"  /></p>

						</div>		
       
		
         <!--<tr>
         	<td class="heading" >Captcha</td>
	        <td>
	         <input type="text" name="secure" value="" onfocus="javascript:this.value='';"  class="input_box" />
	         <br />
	         <span class="explain">click on the image to reload it</span>
	         <img  style="cursor:pointer;" src="<?php echo $this->_tpl_vars['BASE_URL']; ?>
page/captcha/" alt="Click to reload image" title="Click to reload image" id="captcha" onclick="javascript:reloadCaptcha()" />
             <br>
            ( Do Simple Math )
	     </td></tr>-->
       </div><!-- #form-container //-->
					
						<div class="normal-field submitfield fl" style="width:100%; text-align:center">
							<input type="submit"  value="" tabindex="5"  id="get_updates" />
						</div>	
				</form>
				
			</div><!-- #content-left //-->

 <div id="right-content" class="fr">
				<h5 class="cufontext">as seen in</h5>
				
				<div id="rounded-top" class="fl"></div>
				<div id="icons-container" class="fl">
					<ul id="icons">
						<li class="forbes"><a href="#"><img src="<?php echo $this->_tpl_vars['BASE_URL']; ?>
/public/images/forbes.jpg" alt="Forbes" title="" /></a></li>
						<li class="cnet"><a href="#"><img src="<?php echo $this->_tpl_vars['BASE_URL']; ?>
/public/images/cnet.jpg"  alt="CNET" title=""/></a></li>
						<li class="pc"><a href="#"><img src="<?php echo $this->_tpl_vars['BASE_URL']; ?>
/public/images/pc.jpg"  alt="PC" title=""/></a></li>
						<li class="bloomberg-business"><a href="#"><img src="<?php echo $this->_tpl_vars['BASE_URL']; ?>
/public/images/bloomberg-business.jpg"  alt="Blooberg Business" title=""/></a></li>
						<li class="techcrunch"><a href="#"><img src="<?php echo $this->_tpl_vars['BASE_URL']; ?>
/public/images/techcrunch.jpg"  alt="Tech Crunch" title=""/></a></li>
						<li class="gigaom"><a href="#"><img src="<?php echo $this->_tpl_vars['BASE_URL']; ?>
/public/images/gigaom.jpg"  alt="Gigaom" title=""/></a></li>
						<li class="bloomberg"><a href="#"><img src="<?php echo $this->_tpl_vars['BASE_URL']; ?>
/public/images/bloomberg.jpg"  alt="Bloomberg" title=""/></a></li>
					</ul>
				</div><!-- #icons-container //-->	
				
				<div id="rounded-bottom" class="fl"></div>
				
				<div class="fix"></div>
			</div><!-- #right-content //-->
			
			
			
			<div id="fine-print" class="fl">
				<p>By choosing "get updates", I certify I have read and agree to FreedomPop's <a href="<?php echo $this->_tpl_vars['BASE_URL']; ?>
page/privacy-policy" target="_blank" style="text-decoration: none;">privacy policy </a> and <a href="<?php echo $this->_tpl_vars['BASE_URL']; ?>
page/terms-of-use"   target="_blank"  style="text-decoration: none;" >terms of use. </a> </p>
			</div><!-- #fine-print//-->
			<div class="fix"></div>
		</div><!-- #content //-->
	</div><!-- #main-container //-->
	</div>

<?php echo '
<script>
jQuery(document).ready(function() {
	jQuery(\'#frm_get_updates\').submit(function(){
		jQuery(this).find(\'input:text\').each(function(){
			jQuery(this).val(jQuery.trim(jQuery(this).val()));
	      });
	});
	jQuery("#frm_get_updates").validationEngine();
	
	jQuery("a#link2").easyTooltip({
		tooltipId: "easyTooltip2",
		content: \'You will receive service updates, newsletters and important account information emails from FreedomPop\'
	});
});

function reloadCaptcha()
{
	document.getElementById(\'captcha\').src = document.getElementById(\'captcha\').src+ \'?\' +new Date();
}
</script>
'; ?>