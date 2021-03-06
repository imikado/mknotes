<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>mknotes</title>
<link rel="stylesheet" type="text/css" href="css/main.css" media="screen" />
<script src="js/main.js" type="text/javascript"></script>
<link rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo _root::getLink('article::newsrss') ?>"/>
<META http-equiv="Content-Type" Content="text/html; charset=ISO-8859-1"> 
</head>
<body>

<div class="main">
	<p>
		<a href="<?php echo _root::getLink('note::index')?>">Accueil</a> |  
		
		<a href="<?php echo _root::getLink('note::help')?>">Aide</a> | 
		
		<?php if(_root::getAuth() and _root::getAuth()->getAccount() and _root::getAuth()->getAccount()->admin):?>
			<a href="<?php echo _root::getLink('member::index')?>">Gestion des utlisateurs</a> | 
		<?php endif;?>
		
		<a href="<?php echo _root::getLink('profil::index')?>">Pr&eacute;f&eacute;rences</a> | 
		
		<a href="<?php echo _root::getLink('auth::logout')?>">Logout</a> 
	
	</p>
	<div class="content">
		<?php echo $this->load('main') ?>
	</div>
</div>

</body>
</html>
