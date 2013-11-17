<?php $oPluginHtml=new plugin_html?>
<form action="" method="POST" >
<table class="tb_edit">
	
	<tr>
		<th>Utilisateur</th>
		<td><input name="login" value="<?php echo $this->oMember->login ?>" /><?php if($this->tMessage and isset($this->tMessage['login'])): echo implode(',',$this->tMessage['login']); endif;?></td>
	</tr>

	<tr>
		<th>Administrateur</th>
		<td><?php echo $oPluginHtml->getSelect('admin',$this->tJoinmodel_ouinon,$this->oMember->admin)?><?php if($this->tMessage and isset($this->tMessage['admin'])): echo implode(',',$this->tMessage['admin']); endif;?></td>
	</tr>

	<tr>
		<th>Affectation par d&eacute;faut</th>
		<td><input name="defaultAffectation" value="<?php echo $this->oMember->defaultAffectation ?>" /><?php if($this->tMessage and isset($this->tMessage['defaultAffectation'])): echo implode(',',$this->tMessage['defaultAffectation']); endif;?></td>
	</tr>

</table>

<input type="hidden" name="token" value="<?php echo $this->token?>" />
<?php if($this->tMessage and isset($this->tMessage['token'])): echo $this->tMessage['token']; endif;?>

<input type="submit" value="Modifier" /> <a href="<?php echo $this->getLink('member::list')?>">Annuler</a>
</form>

