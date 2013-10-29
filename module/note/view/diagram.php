<?php $tMonth=array('','Janvier','Fevrier','Mars','AVril','Mai','Juin','Juillet','Aout','Sept','Oct','Nov','Dec');
$iStartDay=-15;
$iEndDay=_root::getParam('limit',50);

$iTodayDate=(int)date('Ymd');
?>
<script>
function editLine(i){
	document.location.href='<?php echo _root::getLink('note::diagram',array('id'=>_root::getParam('id'),'line'=>''),0)?>'+i;
}
function switchLimit(){
	var a= getById('iLimit');
	if(a){
		var iLimit=a.value;
		document.location.href='<?php echo _root::getLink('note::diagram',array('id'=>_root::getParam('id'),'limit'=>''),0)?>'+iLimit;
	}
}
function checkUncheck(sDate){
	var a=getById('input_'+sDate);
	var b=getById('td_'+sDate);
	
	if(a && b){
		if(a.checked){
			b.style.background='silver';
			a.checked=0;
		}else{
			b.style.background='darkgreen';
			a.checked=1;
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
	<li><a href="<?php echo _root::getLink('note::show',array('id'=>$this->oNote->id))?>">Current</a></li>
	<li><a href="<?php echo _root::getLink('note::history',array('id'=>$this->oNote->id))?>">Snapshots</a></li>
	<li class="selected"><a href="<?php echo _root::getLink('note::diagram',array('id'=>$this->oNote->id))?>">Planning</a></li>
</ul>

<p style="text-align:right"><input style="text-align:right" size="3" type="text" id="iLimit" value="<?php echo $iEndDay?>"/><input onclick="switchLimit()" type="button" value="Switch limit"/></p>

<div style="margin:8px;width:1180px;overflow:auto">

<?php if(_root::getParam('line',-1) > -1):?>
<form action="" method="POST">
<?php endif;?>

	
<table style="margin:20px 0px;">
	
	<tr>
		<td rowspan="2"></td>
		
		<th rowspan="2"><div style="width:350px">Project</div></th>
		
		
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
	
	<?php foreach($this->tProject as $iLine => $sProject):?>
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
		$bEdit=0;
		if(_root::getParam('line',-1)==$iLine){
			$bEdit=1;
		}
		
		?>
		
		<tr class="line" <?php if($bEdit==1):?>style="cursor:pointer"<?php elseif($bEdit==0):?>onclick="editLine(<?php echo $iLine?>)"; style="cursor:pointer"<?php endif;?>>
			
			<td>
				<?php if($bEdit):?>
					<div  style="width:90px">
					<input type="submit" value="valid"/> 
					<a href="<?php echo _root::getLink('note::diagram',array('id'=>_root::getParam('id')))?>">cancell</a>
					</div>
				<?php endif;?>
			</td>
			
			<td class="empty" <?php 
				if(substr($sProject,0,2)=='=='):
					?>style="font-weight:bold;"<?php
					$sProject=substr($sProject,2);
				elseif(substr($sProject,0,3)=='---'): 
					?>style="padding-left:40px;"<?php
					$sProject=substr($sProject,3);
				elseif(substr($sProject,0,2)=='--'): 
					?>style="padding-left:25px;"<?php
					$sProject=substr($sProject,2);
				elseif(substr($sProject,0,1)=='-'): 
					?>style="padding-left:10px;"<?php
					$sProject=substr($sProject,1);
				endif;?>><?php echo $sProject?></td>
			
			
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
				if(isset($this->tMinMax[$sProject]) and $this->tMinMax[$sProject]['min'] <= $iCurrentDate and $this->tMinMax[$sProject]['max'] >= $iCurrentDate ){
					$accolade=';border-top:3px solid #569ea9';
				}
				
				?>
				
				
				<?php if($bEdit):?>
					<td onclick="checkUncheck('<?php echo $sInputCurrentDate?>')" id="td_<?php echo $sInputCurrentDate?>" class="<?php echo $sClass?>" style="font-size:8px;border:<?php echo $border?>px solid darkred"><input style="display:none" id="input_<?php echo $sInputCurrentDate?>" name="tDate[]" value="<?php echo $sInputCurrentDate?>" <?php if($sClass=='taskOn'):?>checked="checked"<?php endif;?> type="checkbox"/></td>
				<?php else:?>
				
				
					<td class="<?php echo $sClass?>" style="font-size:8px;border:<?php echo $border?>px solid darkred<?php echo $accolade?>">
						&nbsp;
					</td>
				
				<?php endif;?>
				
				
			<?php endfor;?>
			
			
		</tr>
	<?php endforeach;?>
</table>

<?php if(_root::getParam('line',-1) > -1):?>
</form>
<?php endif;?>

</div>
