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
	<li><a href="<?php echo _root::getLink('note::show',array('id'=>$this->oNote->id))?>">Current</a></li>
	<li class="selected"><a href="<?php echo _root::getLink('note::history',array('id'=>$this->oNote->id))?>">Snapshots</a></li>
</ul>

<div class="notes">
<?php $tNote= explode("\n",$this->oNote->content) ?>
<?php $bStart=0;?>
<?php $bArchive=0;?>
<?php foreach($tNote as $i => $sLine):?>

	<?php $bOk=0?>
	
	<?php if(substr($sLine,0,3)=='==='):?>
		<?php $bArchive=1;?>
	<?php endif;?>

	<?php if(!$bArchive):?>
		<?php continue;?>
	<?php elseif(substr($sLine,0,3)=='==='):?>
		<?php if($bStart):?></p><?php endif;?>
		<p style="background:#ddd;font-weight:bold;border-top:2px solid black;margin-top:40px"><?php echo substr($sLine,3)?>
		<?php $bStart=1;?>
	<?php elseif(substr($sLine,0,2)=='=='):?>
		<?php if($bStart):?></p><?php endif;?>
		<p style="font-weight:bold;border-bottom:1px solid gray;margin-top:20px"><?php echo substr($sLine,2)?>
		<?php $bStart=1;?>
	<?php elseif(substr($sLine,0,2)=='--' or substr($sLine,0,1)=='-'):?>
		<?php if($bStart):?></p><?php endif;?>
		<p style="<?php
			if(substr($sLine,0,2)=='--'):
				$sText=substr($sLine,2);
				?>border:1px solid gray;margin-left:30px;margin-top:2px;background:#eee<?php
			elseif(substr($sLine,0,1)=='-'):
				$sText=substr($sLine,1);
				?>border:1px solid gray;margin-left:15px;background:#cddde3;margin-top:4px<?php
			endif;
			
			if(preg_match('/'.module_note::getOk().'/',$sLine)):
				?>;background:#66c673<?php
				$bOk=1;
			endif;

			?>"><?php
			
			echo $sText;
			?>
		<?php $bStart=1;?>
	<?php else:?>
		<br/><?php echo $sLine?>
	<?php endif;?>

<?php endforeach;?>

<?php if($bStart):?></p><?php endif;?>

<p></p>
</div>




