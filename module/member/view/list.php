<table class="tb_list">
	<tr>
		
		<th>Utilisateur</th>

		<th>Administrateur</th>

		<th>Affectation par d&eacute;faut</th>

		<th></th>
	</tr>
	<?php if($this->tMember):?>
	<?php foreach($this->tMember as $oMember):?>
	<tr <?php echo plugin_tpl::alternate(array('','class="alt"'))?>>
		
		<td><?php echo $oMember->login ?></td>

		<td><?php if(isset($this->tJoinmodel_ouinon[$oMember->admin])){ echo $this->tJoinmodel_ouinon[$oMember->admin];}else{ echo $oMember->admin ;}?></td>

		<td><?php echo $oMember->defaultAffectation ?></td>

		<td>
			
			
<a href="<?php echo $this->getLink('member::edit',array(
										'id'=>$oMember->getId()
									) 
							)?>">Edit</a>
| 
<a href="<?php echo $this->getLink('member::show',array(
										'id'=>$oMember->getId()
									) 
							)?>">Show</a>

			
			
		</td>
	</tr>	
	<?php endforeach;?>
	<?php endif;?>
</table>



