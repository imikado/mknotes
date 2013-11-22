<script>
var tItemStatus=new Array();

function hideDisplay(sIds){
	if(!tItemStatus[sIds]){
		tItemStatus[sIds]='display';
	}else if(tItemStatus[sIds]=='hide'){
		tItemStatus[sIds]='display';
	}else{
		tItemStatus[sIds]='hide';
	}
	
	if(tItemStatus[sIds]=='hide'){
		display(sIds);
	}else{
		hide(sIds);
	}
}

function hide(sIds){
	for(var i=0;i<tItem.length;i++){
		if(sIds.length == tItem[i].length){
		
		}else if(sIds == tItem[i].substr(0,sIds.length) && getById(tItem[i]) ){
			getById(tItem[i]).style.display='none';
		} 
	}
}
function display(sIds){
	for(var i=0;i<tItem.length;i++){
		if(sIds.length == tItem[i].length){
		
		}else if(sIds == tItem[i].substr(0,sIds.length) && getById(tItem[i]) ){
			getById(tItem[i]).style.display='block';
		} 
	}
}
var tItem=new Array();
var iItem=0;
function addItem(sId){
	tItem[iItem]=sId;
	iItem+=1;
}
</script>
<div class="notes">
<?php $tNote= explode("\n",$this->content) ?>
<?php $bStart=0;?>
<?php $bArchive=0;?>
<?php $level0=0;$level1=0;$level2=0;$level3=0; ?>
<?php $sScript=null;?>
<?php foreach($tNote as $i => $sLine):?>

	<?php $bOk=0?>
	
	<?php if(substr($sLine,0,3)=='==='):?>
		<?php break;?>
	<?php elseif(preg_match('/'.module_note::getHide().'/',$sLine)):?>
		<?php break;?>
	<?php endif;?>

	<?php if($bArchive):?>
		<p style="color:gray"><?php echo $sLine?></p>
	<?php elseif(substr($sLine,0,2)=='=='):?>
		<?php 
		$level0+=1;
		$level1=0;
		$level2=0;
		$level3=0;
		?>
		<?php if($bStart):?></p><?php endif;?>
		<p id="<?php echo $level0?>." style="font-weight:bold;border-bottom:1px solid black;margin-top:20px"><a href="#" onclick="hideDisplay('<?php echo $level0?>.')">[-]</a> 
		<?php echo $level0?>.<?php echo $this->oModuleNote->format(substr($sLine,2))?>
		<?php $bStart=1;?>
		
	<?php elseif(substr($sLine,0,3)=='---' or substr($sLine,0,2)=='--' or substr($sLine,0,1)=='-'):?>
		<?php if($bStart):?></p><?php endif;?>
		<p id="<?php 
			$sId=$level0.'.';
			 
			if(substr($sLine,0,3)=='---'):
				$level3+=1;
				$sId=$level0.'.'.$level1.'.'.$level2.'.'.$level3.'.';
			elseif(substr($sLine,0,2)=='--'):
				$level2+=1;
				$sId=$level0.'.'.$level1.'.'.$level2.'.';
			elseif(substr($sLine,0,1)=='-'):
				$level1+=1;
				$sId=$level0.'.'.$level1.'.';
			endif;
			
			echo $sId;
		?>" 
		
		 style="<?php
			if(substr($sLine,0,3)=='---'):
				$sText=substr($sLine,3);
				?>border:1px solid gray;margin-left:45px;margin-top:2px;background:#eee<?php
			elseif(substr($sLine,0,2)=='--'):
				$sText=substr($sLine,2);
				?>border:1px solid gray;margin-left:30px;margin-top:2px;background:#eee<?php
			elseif(substr($sLine,0,1)=='-'):
				$sText=substr($sLine,1);
				?>border:1px solid gray;margin-left:15px;background:#b3d1dc;margin-top:4px<?php
			endif;
			
			if(preg_match('/'.module_note::getOk().'/',$sLine)):
				?>;background:#66c673<?php
				$bOk=1;
			elseif(preg_match('/'.module_note::getRun().'/',$sLine)):
				?>;background:orange<?php
			endif;

			?>"><?php 
			
			if(substr($sLine,0,3)=='---'):
			elseif(substr($sLine,0,2)=='--'):
			elseif(substr($sLine,0,1)=='-'):
				?><a href="#" onclick="hideDisplay('<?php echo $sId?>')">[-]</a> <?php 
			endif;
			
			echo $sId;

			if($this->bWrite==1):
				?><span style="float:right">
					<a href="#" onclick="editLine(<?php echo $i?>)">[EDIT]</a>
					
					<input id="checkbox_<?php echo $i?>" onclick="doUndo(<?php echo $i?>)" <?php 
					if($bOk):
						?>checked="checked"<?php 
					endif;
					
					?> type="checkbox"/>
				</span>
				<?php
			endif;
			
			echo $this->oModuleNote->format($sText,$this->login);
			?>
		<?php $bStart=1;?>
		<?php $sScript.='addItem("'.$sId.'");';?>
	<?php else:?>
		<br/><?php echo $sLine?>
	<?php endif;?>

<?php endforeach;?>

<?php if($bStart):?></p><?php endif;?>


</div>
<script>
<?php echo $sScript?>
</script>
