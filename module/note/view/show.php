<script>
function doUndo(i){
	var a=getById('checkbox_'+i);
	if(a){
		var b=getById('inputLine');
		b.value=i;
		var c=getById('inputChecked');
		if(a.checked){
			c.value=1;
		}else{
			c.value=0;
		}
		var d=getById('formChecked');
		d.submit();
	}
}


var tContent=new Array();
<?php $tContent=explode("\n",$this->oNote->content);
foreach($tContent as $i => $sLine):
	?>tContent[ <?php echo $i?> ]='<?php echo str_replace("'","\'",html_entity_decode($sLine,ENT_QUOTES,'ISO-8859-1'))?>';
	<?php 
endforeach;
?>
function editLine(i){
	var a=getById('inputUpdateLine');
	if(a){
		a.value=i;
		
		var b=getById('inputUpdateContent');
		if(b){
			b.value=tContent[i];
			
			var c=getById('popupUpdateLine');
			if(c){
				c.style.display='block';
			}
		}
		
	}
	
	
}
</script>
<style>
.popup{
	border:2px solid gray;
	background:white;
	position:absolute;
	display:none;
	}
</style>

<?php echo $this->oViewProcessed->show()?>

<p style="text-align:right"> <a href="<?php echo $this->getLink('note::edit',array('id'=>$this->oNote->id))?>">Editer</a></p>

<form action="" method="POST" id="formChecked">
<input type="hidden" name="type" value="checked"/>
<input type="hidden" name="line" value="" id="inputLine"/>
<input type="hidden" name="checked" value="" id="inputChecked"/>
</form>

<div class="popup" id="popupUpdateLine">
<form action="" method="POST" id="formUpdateLine">
<input type="hidden" name="type" value="updateLine"/>
<input type="hidden" name="line" value="" id="inputUpdateLine"/>
<textarea id="inputUpdateContent" name="content" cols="1" style="width:600px"> </textarea>
<p><input type="submit" value="Save"/></p>
</form>
</div>

