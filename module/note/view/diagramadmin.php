<?php $tMonth=array('','Janvier','Fevrier','Mars','AVril','Mai','Juin','Juillet','Aout','Sept','Oct','Nov','Dec');
$iStartDay=-15;
$iEndDay=_root::getParam('limit',50);

$iTodayDate=(int)date('Ymd');
?>
<script>

function switchLimit(){
	var a= getById('iLimit');
	if(a){
		var iLimit=a.value;
		document.location.href='<?php echo _root::getLink('note::diagram',array('id'=>_root::getParam('id'),'limit'=>''),0)?>'+iLimit;
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
	<li><a href="<?php echo _root::getLink('note::show',array('id'=>$this->oNote->id))?>">Current</a></li>
	<li><a href="<?php echo _root::getLink('note::history',array('id'=>$this->oNote->id))?>">Snapshots</a></li>
	<li class="selected"><a href="<?php echo _root::getLink('note::diagram',array('id'=>$this->oNote->id))?>">Planning</a></li>
</ul>

<p style="text-align:right"><input style="text-align:right" size="3" type="text" id="iLimit" value="<?php echo $iEndDay?>"/><input onclick="switchLimit()" type="button" value="Switch limit"/></p>

<div style="margin:8px;width:1280px;overflow:auto">

<?php if(_root::getParam('line',-1) > -1):?>
<form action="" method="POST">
<?php endif;?>

	
<table style="margin:20px 0px;">
	
	<tr>
		<td rowspan="2"></td>
		
		<th rowspan="2"><div style="width:450px">Project</div></th>
		
		
		<?php $oCurrentDate=new plugin_date(date('Y-m-d'));?>
		<?php $oCurrentDate->addDay($iStartDay);?>
		
		<?php $colspan=0;?>
		<?php for($i=$iStartDay;$i<$iEndDay;$i++):?>
		
			<?php $lastMonth=$tMonth[ (int)$oCurrentDate->toString('m') ]?>
		
			<?php $oCurrentDate->addDay(1);?>
			
			
			<?php if($oCurrentDate->toString('d') == 1):?>
				<td style="border:1px solid gray" colspan="<?php echo $colspan?>">
					<?php echo $lastMonth?>
				</td>
				<?php $colspan=0;?>
			<?php endif;?>
			<?php $colspan++?>
		<?php endfor;?>
		
		<td style="border:1px solid gray" 	colspan="<?php echo $colspan?>">
			<?php echo $lastMonth?>
		</td>
		
	</tr>
	
	
	
	<tr>
		
		
		<?php $oCurrentDate=new plugin_date(date('Y-m-d'));?>
		<?php $oCurrentDate->addDay($iStartDay);?>
		
		<?php for($i=$iStartDay;$i<$iEndDay;$i++):?>
		
			
			<?php
			$style=null; 
			$oCurrentDate->addDay(1);
			$iCurrentDate=(int)$oCurrentDate->toString('Ymd');
			if($iTodayDate == $iCurrentDate){
				$style=';background:darkred;color:white;font-weight:bold';
			}
			?>
			
			<td style="font-size:9px;<?php echo $style?>">
				<?php echo $oCurrentDate->toString('d'); ?>
			</td>
			
			
		<?php endfor;?>
		
		
	</tr>
	
	
	<?php foreach($this->tProject as  $sProject0 => $tTask):?>
	
		<tr>
			<td style="font-size:2px">a&nbsp;</td>
		</tr>
	
		<?php foreach($tTask as $sProject):?>
			<?php 
			//$sProject0=substr($sProject0,2);
			
			$iStartDate=0;
			$iEndDate=0;
			if(preg_match('/\[([0-9\/-]*)\]/',$sProject)){
				preg_match('/\[([0-9\/-]*)\]/',$sProject,$tMatchDate);
				list($sStartDate,$sEndDate)=explode('-',$tMatchDate[1]);
				plugin_debug::addSpy('sStartDate',$sStartDate);
				plugin_debug::addSpy('sEndDate',$sEndDate);
				
				$oStartDate=new plugin_date($sStartDate,'d/m/Y');
				$oEndDate=new plugin_date($sEndDate,'d/m/Y');
				
				$iStartDate=(int)$oStartDate->toString('Ymd');
				$iEndDate=(int)$oEndDate->toString('Ymd');
			}
			$bEdit=0;
		
			
			if(isset($this->tMember)):
					foreach($this->tMember as $sLogin):
						$sProject=preg_replace('/@'.$sLogin.'/','<span style=";color:darkgreen">@'.$sLogin.'</span>',$sProject);
					endforeach;
				endif;
			
			$bProject=0;
			
			?>
			
			<tr class="line" >
				
				<td>
					
				</td>
				
				<td class="empty" <?php 
					if(substr($sProject,0,2)=='=='):
						?>style="background:#b9c3c6;font-size:12px;padding:3px;"<?php
						$sProject=substr($sProject,2);
						$bProject=1;
					elseif(substr($sProject,0,3)=='---'): 
						?>style="padding-left:40px;"<?php
						$sProject=substr($sProject,3);
					elseif(substr($sProject,0,2)=='--'): 
						?>style="padding-left:25px;"<?php
						$sProject=substr($sProject,2);
					elseif(substr($sProject,0,1)=='-'): 
						?>style="padding-left:10px;"<?php
						$sProject=substr($sProject,1);
					endif;
					?>><?php echo $sProject?></td>
				
				
				<?php $oCurrentDate=new plugin_date(date('Y-m-d'));?>
				<?php $oCurrentDate->addDay($iStartDay);?>
			
				<?php for($i=$iStartDay;$i<$iEndDay;$i++):?>
				
					<?php 
					$border=0;
					$sClass='empty';
					$oCurrentDate->addDay(1);
					$iCurrentDate=(int)$oCurrentDate->toString('Ymd');
					$sInputCurrentDate=$oCurrentDate->toString('d/m/Y');
					if($iStartDate <= $iCurrentDate and $iEndDate >= $iCurrentDate ):
						$sClass='taskOn';
					elseif($oCurrentDate->toString('w') == 6 or $oCurrentDate->toString('w') == 0):
						$sClass='weekend';
					endif;
					
					if($iTodayDate == $iCurrentDate){
						$border=2;
					}
					
					$accolade=null;
					if($bProject and isset($this->tMinMax[$sProject0]) and $this->tMinMax[$sProject0]['min'] <= $iCurrentDate and $this->tMinMax[$sProject0]['max'] >= $iCurrentDate ){
						$accolade=';border-top:4px solid #043f70';
					}
					
					?>
					
					
				
					
				
					<td class="<?php echo $sClass?>" style="font-size:8px;border:<?php echo $border?>px solid darkred<?php echo $accolade?>">
						&nbsp;
					</td>
					
				
					
					
				<?php endfor;?>
				
				
			</tr>
		<?php endforeach;?>
	<?php endforeach;?>
</table>

<?php if(_root::getParam('line',-1) > -1):?>
</form>
<?php endif;?>

</div>