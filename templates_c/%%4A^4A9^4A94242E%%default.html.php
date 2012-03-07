<?php /* Smarty version 2.6.18, created on 2012-03-07 13:43:35
         compiled from Z:/raaser/templates//layout/default.html */ ?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <title>Freedom Pop</title>

  <meta name="viewport" content="width=device-width">

   <link rel="stylesheet" media="screen,projection" type="text/css" href="<?php echo $this->_tpl_vars['public_url']; ?>
css/reset.css" />
   <link rel="stylesheet" media="screen,projection" type="text/css" href="<?php echo $this->_tpl_vars['public_url']; ?>
css/style.css" />

  
  <script type="text/javascript" src="<?php echo $this->_tpl_vars['public_url']; ?>
js/jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo $this->_tpl_vars['public_url']; ?>
js/modernizr-2.5.0.min.js"></script>  
  <script type="text/javascript" src="<?php echo $this->_tpl_vars['public_url']; ?>
js/cufon-yui.js"></script> 
  <script type="text/javascript" src="<?php echo $this->_tpl_vars['public_url']; ?>
js/HelviticaBold_700.font.js"></script>
 
 <script type="text/javascript" src="<?php echo $this->_tpl_vars['public_url']; ?>
js/Helvitica_400.font.js"></script>
   <script type="text/javascript" src="<?php echo $this->_tpl_vars['public_url']; ?>
js/jquery.ezmark.min.js"></script>
   <script type="text/javascript" src="<?php echo $this->_tpl_vars['public_url']; ?>
js/flexcroll.js"></script>
   
	<script type="text/javascript" src="<?php echo $this->_tpl_vars['public_url']; ?>
js/jquery.validationEngine.js"></script>
	<script type="text/javascript" src="<?php echo $this->_tpl_vars['public_url']; ?>
js/languages/jquery.validationEngine-en.js"></script>
	<link rel="stylesheet" media="screen,projection" type="text/css" href="<?php echo $this->_tpl_vars['public_url']; ?>
css/validationEngine.jquery.css" />

   <?php echo '
   <script type="text/javascript">
	Cufon.replace(\'#home-headlines-text h2\',{fontFamily: \'HelviticaBold\'}); // Works without a selector engine
	Cufon.replace(\'p.label\',{fontFamily: \'Helvitica\'}); // Works without a selector engine
	Cufon.replace(\'h5.cufontext\',{fontFamily: \'HelviticaBold\'}); // Works without a selector engine
  </script>
 
  <script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery(\'input[type="checkbox"]\').ezMark();
		});
  </script>	
 
 '; ?>

 


</head>
 


<body>

<input type="hidden" id="BASE_URL" value="<?php echo $this->_tpl_vars['BASE_URL']; ?>
"  />
<input type="hidden" id="PUBLIC_URL" value="<?php echo $this->_tpl_vars['public_url']; ?>
"  />

<div id="wrapper" class="homepage">

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template_path'])."/elements/header.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>


<?php echo $this->_tpl_vars['layout_content']; ?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template_path'])."/elements/footer.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

</div>
<!-- /main -->

<!-- Begin - Footer Comman Files -->
	<script type="text/javascript" src="<?php echo $this->_tpl_vars['public_url']; ?>
js/common.js"></script>
<!-- End - Footer Comman Files -->

</body>
<?php echo '<script type="text/javascript"> Cufon.now(); </script>'; ?>

</html>



