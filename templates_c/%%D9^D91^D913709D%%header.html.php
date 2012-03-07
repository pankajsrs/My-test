<?php /* Smarty version 2.6.18, created on 2012-03-07 13:43:35
         compiled from Z:/raaser/templates//elements/header.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'Z:/raaser/templates//elements/header.html', 12, false),)), $this); ?>
<div id="header">
		<header>
			<h1>
				<a href="#" title="Freedom Pop - Free access for all">
					<img src="<?php echo $this->_tpl_vars['BASE_URL']; ?>
/public/images/logo.png" alt="Freedom Pop - Free access for all" />
				</a>
			</h1>
		</header>

</div><!-- #header //-->

<?php if (count($this->_tpl_vars['flashMessage']) > 0): ?>
<div class="msg <?php echo $this->_tpl_vars['flashMessage']['class']; ?>
" id="flashMessage" style="margin-top:20px;color:black;" ><?php echo $this->_tpl_vars['flashMessage']['message']; ?>
</div>
<?php endif; ?>