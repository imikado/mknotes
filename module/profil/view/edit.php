<?php $oPluginHtml=new plugin_html?>
<form action="" method="POST" >
<table class="tb_edit">
	
	<tr>
		<th>Limite par d&eacute;faut</th>
		<td><input name="defaultLimitDiagram" value="<?php echo $this->oMember->defaultLimitDiagram ?>" /><?php if($this->tMessage and isset($this->tMessage['defaultLimitDiagram'])): echo implode(',',$this->tMessage['defaultLimitDiagram']); endif;?></td>
	</tr>

	<?php if(_root::getAuth()->getAccount()->admin):?>

		<tr>
			<th>Limite par d&eacute;faut (planning g&eacute;n&eacute;ral)</th>
			<td><input name="defaultLimitDiagramAdmin" value="<?php echo $this->oMember->defaultLimitDiagramAdmin ?>" /><?php if($this->tMessage and isset($this->tMessage['defaultLimitDiagramAdmin'])): echo implode(',',$this->tMessage['defaultLimitDiagramAdmin']); endif;?></td>
		</tr>
	
	<?php endif;?>

</table>

<input type="hidden" name="token" value="<?php echo $this->token?>" />
<?php if($this->tMessage and isset($this->tMessage['token'])): echo $this->tMessage['token']; endif;?>

<input type="submit" value="Modifier" /> 
</form>

