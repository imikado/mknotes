<table class="tb_list">
	<tr>
		
		<th>Limite par d&eacute;faut</th>

		<th>Limite par d&eacute;faut (planning g&eacute;n&eacute;ral)</th>

		<th></th>
	</tr>
	<?php if($this->tMember):?>
	<?php foreach($this->tMember as $oMember):?>
	<tr <?php echo plugin_tpl::alternate(array('','class="alt"'))?>>
		
		<td><?php echo $oMember->defaultLimitDiagram ?></td>

		<td><?php echo $oMember->defaultLimitDiagramAdmin ?></td>

		<td>
			
			
<a href="<?php echo $this->getLink('profil::edit',array(
										'id'=>$oMember->getId()
									) 
							)?>">Edit</a>

			
			
		</td>
	</tr>	
	<?php endforeach;?>
	<?php endif;?>
</table>



