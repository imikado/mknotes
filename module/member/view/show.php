<table class="tb_show">
	
	<tr>
		<th>Utilisateur</th>
		<td><?php echo $this->oMember->login ?></td>
	</tr>

	<tr>
		<th>Administrateur</th>
		<td><?php echo $this->tJoinmodel_ouinon[$this->oMember->admin]?></td>
	</tr>

	<tr>
		<th>Affectation par d&eacute;faut</th>
		<td><?php echo $this->oMember->defaultAffectation ?></td>
	</tr>

</table>
<p> <a href="<?php echo $this->getLink('member::list')?>">Retour</a></p>

