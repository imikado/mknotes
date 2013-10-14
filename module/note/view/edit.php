<script>
function preview(){
	var a=getById('contentfrom');
	if(a){
		var b=getById('previewtext');
		if(b){
			b.value=a.value;
			var c=(getById('previewform'));
			if(c){
				//alert(c.action);
				c.submit();
			}
		}
	}
}
</script>
<table>

<tr>
	<td><?php $oPluginHtml=new plugin_html?>
<form action="" method="POST" >
<textarea onKeyUp="preview()" id="contentfrom" style="width:550px;height:600px" name="content" ><?php echo $this->oNote->content ?></textarea>
	

<input type="hidden" name="token" value="<?php echo $this->token?>" />
<?php if($this->tMessage and isset($this->tMessage['token'])): echo $this->tMessage['token']; endif;?>

<p style="text-align:right"><input type="submit" value="Modifier" /> <a href="<?php echo $this->getLink('note::show',array('id'=>$this->oNote->id))?>">Annuler</a></p>
</form>

</td>
<td> 
<form style="display:none" id="previewform" action="<?php echo _root::getLink('note::preview')?>" target="previewframe" method="POST" ><textarea id="previewtext" name="text"></textarea></form>
<iframe id="previewframe" name="previewframe" style="width:600px;height:600px"></iframe>

</td></tr></table>

<script>preview();</script>
