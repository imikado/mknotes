<?php $tMonth=array('','Janvier','Fevrier','Mars','AVril','Mai','Juin','Juillet','Aout','Sept','Oct','Nov','Dec');
$iStartDay=-15;
$iEndDay=50;

$iTodayDate=(int)date('Ymd');
?>
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

<table>
	
	<tr>
		<th rowspan="2">Project</th>
		<td rowspan="2"></td>
		
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
	
	<?php foreach($this->tProject as $sProject):?>
		<?php 
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
		?>
		
		<tr class="line">
			<td class="empty"><?php echo $sProject?></td>
			<td></td>
			
			<?php $oCurrentDate=new plugin_date(date('Y-m-d'));?>
			<?php $oCurrentDate->addDay($iStartDay);?>
		
			<?php for($i=$iStartDay;$i<$iEndDay;$i++):?>
			
				<?php 
				$border=0;
				$sClass='empty';
				$oCurrentDate->addDay(1);
				$iCurrentDate=(int)$oCurrentDate->toString('Ymd');
				if($iStartDate <= $iCurrentDate and $iEndDate >= $iCurrentDate ):
					$sClass='taskOn';
				elseif($oCurrentDate->toString('w') == 6 or $oCurrentDate->toString('w') == 0):
					$sClass='weekend';
				endif;
				
				if($iTodayDate == $iCurrentDate){
					$border=2;
				}
				?>
				
				<td class="<?php echo $sClass?>" style="font-size:8px;border:<?php echo $border?>px solid darkred">
					&nbsp;
				</td>
				
				
			<?php endfor;?>
			
			
		</tr>
	<?php endforeach;?>
</table>
