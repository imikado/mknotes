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
<style>
ul.tabs li{
display:inline;
border:1px solid gray;
padding:0px 6px;
background:#ddd;
}
ul.tabs {
	border-bottom:1px solid gray;
}
ul.tabs li.selected{
	border-bottom:1px solid white;
	background:white;
}
ul.tabs a{
	text-decoration:none;
}
</style>

<ul class="tabs">
	<li class="selected"><a href="<?php echo _root::getLink('note::show',array('id'=>$this->oNote->id))?>">Current</a></li>
	<li><a href="<?php echo _root::getLink('note::history',array('id'=>$this->oNote->id))?>">Snapshots</a></li>
	<li><a href="<?php echo _root::getLink('note::diagram',array('id'=>$this->oNote->id))?>">Planning</a></li>
	
	<?php if(_root::getAuth() and _root::getAuth()->getAccount() and _root::getAuth()->getAccount()->admin):?>
		<li><a href="<?php echo _root::getLink('note::admin')?>">Planning g&eacute;n&eacute;ral</a> </li>
	<?php endif;?> 
</ul>

<table>

<tr>
	<td><?php $oPluginHtml=new plugin_html?>
<form action="" method="POST" >
<textarea onKeyUp="preview()" id="contentfrom" style="width:550px;height:600px" name="content" ><?php echo $this->content ?></textarea>
	

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
