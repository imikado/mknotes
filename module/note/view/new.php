<?php $oPluginHtml=new plugin_html?>
<form action="" method="POST" >

<table class="tb_new">
	
	<tr>
		<th>content</th>
		<td><textarea name="content" ></textarea></td>
	</tr>

</table>

<input type="hidden" name="token" value="<?php echo $this->token?>" />
<?php if($this->tMessage and isset($this->tMessage['token'])): echo $this->tMessage['token']; endif;?>

<input type="submit" value="Ajouter" /> <a href="<?php echo $this->getLink('note::list')?>">Annuler</a>
</form>

