<table class="tb_delete">
	
	<tr>
		<th>Utilisateur</th>
		<td><?php echo $this->oMember->login ?></td>
	</tr>

	<tr>
		<th>Administrateur</th>
		<td><?php echo $this->tJoinmodel_ouinon[$this->oMember->admin]?></td>
	</tr>

	<tr>
		<th>Charge par d&eacute;faut</th>
		<td><?php echo $this->oMember->defaultCharge ?></td>
	</tr>

</table>

<form action="" method="POST">
<input type="hidden" name="token" value="<?php echo $this->token?>" />
<?php if($this->tMessage and isset($this->tMessage['token'])): echo $this->tMessage['token']; endif;?>

<input type="submit" value="Confirmer la suppression" /> <a href="<?php echo $this->getLink('member::list')?>">Annuler</a>
</form>

