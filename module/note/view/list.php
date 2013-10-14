<?php $oModelNote=new module_note;?>
<table class="tb_list">
	<tr>
		
		<th style="width:500px">content</th>

		<th style="width:90px"></th>
	</tr>
	<?php if($this->tNote):?>
	<?php foreach($this->tNote as $oNote):?>
	<tr <?php echo plugin_tpl::alternate(array('','class="alt"'))?>>
		
		<td><?php echo $oModelNote->getViewProcessed($oNote->content,0)->show() ?></td>

		<td>
			
			


<a style="text-decoration:none;text-align:center;display:block;border:1px solid gray;background:#ddd" href="<?php echo $this->getLink('note::show',array(
										'id'=>$oNote->getId()
									) 
							)?>">Show</a>

			
			
		</td>
	</tr>	
	<?php endforeach;?>
	<?php endif;?>
</table>

<p><a href="<?php echo $this->getLink('note::new') ?>">New</a></p>



