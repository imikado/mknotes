<div class="notes">
<?php $tNote= explode("\n",$this->content) ?>
<?php $bStart=0;?>
<?php $bArchive=0;?>
<?php foreach($tNote as $i => $sLine):?>

	<?php $bOk=0?>
	
	<?php if(substr($sLine,0,3)=='==='):?>
		<?php $bArchive=1;?>
	<?php endif;?>

	<?php if($bArchive):?>
		<p style="color:gray"><?php echo $sLine?></p>
	<?php elseif(substr($sLine,0,2)=='=='):?>
		<?php if($bStart):?></p><?php endif;?>
		<p style="font-weight:bold;border-bottom:1px solid black;margin-top:20px"><?php echo substr($sLine,2)?>
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
			
			echo $sText;
			?>
		<?php $bStart=1;?>
	<?php else:?>
		<br/><?php echo $sLine?>
	<?php endif;?>

<?php endforeach;?>

<?php if($bStart):?></p><?php endif;?>


</div>
