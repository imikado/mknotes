<?php $tMonth=array('','Janvier','Fevrier','Mars','AVril','Mai','Juin','Juillet','Aout','Sept','Oct','Nov','Dec');
$iStartDay=-15;
$iEndDay=_root::getParam('limit');
if($iEndDay==''){
	$iEndDay=_root::getAuth()->getAccount()->defaultLimitDiagramAdmin;
}
if($iEndDay==''){
	$iEndDay=50;
}

$iTodayDate=(int)date('Ymd');

$tChargeDev=array();
$tNbChargeDev=array();
?>
<script>

function switchLimit(){
	var a= getById('iLimit');
	if(a){
		var iLimit=a.value;
		document.location.href='<?php echo _root::getLink('note::admin',array('limit'=>''),0)?>'+iLimit;
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
	<li><a href="<?php echo _root::getLink('note::diagram',array('id'=>$this->oNote->id))?>">Planning</a></li>
	
	<?php if(_root::getAuth() and _root::getAuth()->getAccount() and _root::getAuth()->getAccount()->admin):?>
		<li class="selected"><a href="<?php echo _root::getLink('note::admin')?>">Planning g&eacute;n&eacute;ral</a> </li>
	<?php endif;?> 
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
	
	
	<?php foreach($this->tProject as $iLine => $sProject):?>
			<?php 
			//$sProject0=substr($sProject0,2);
			$sDev=$this->oModuleNote->getDev($sProject);
			list($iStartDate,$iEndDate)=$this->oModuleNote->calculateListDate($sProject,$sDev);
			$iCharge=$this->oModuleNote->calculCharge($sProject,$sDev);
			
			$sJalon=$this->oModuleNote->getJalon($sProject);
			
			$bEdit=0;
		
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
					?>><div style="width:450px"><?php echo $this->oModuleNote->format($sProject,$sDev)?></div></td>
				
				
				<?php $oCurrentDate=new plugin_date(date('Y-m-d'));?>
				<?php $oCurrentDate->addDay($iStartDay);?>
			
				<?php for($i=$iStartDay;$i<$iEndDay;$i++):?>
				
					<?php 
					$border=0;
					$sClass='empty';
					$oCurrentDate->addDay(1);
					$iCurrentDate=(int)$oCurrentDate->toString('Ymd');
					$sInputCurrentDate=$oCurrentDate->toString('d/m/Y');
					if($oCurrentDate->toString('w') == 6 or $oCurrentDate->toString('w') == 0):
						$sClass='weekend';
					elseif($iStartDate <= $iCurrentDate and $iEndDate >= $iCurrentDate ):
						$sClass='taskOn';
						
						if(!isset($tChargeDev[$iCurrentDate])){
							$tChargeDev[$iCurrentDate]=array();
						}
						if(!isset($tChargeDev[$iCurrentDate][$sDev])){
							$tChargeDev[$iCurrentDate][$sDev]=0;
						}
						
						
						$tChargeDev[$iCurrentDate][$sDev]+=$iCharge;
					endif;
					
					if($iTodayDate == $iCurrentDate){
						$border=2;
					}
					
					$accolade=null;
					if(isset($this->tMinMax[$sProject]) and $this->tMinMax[$sProject]['min'] <= $iCurrentDate and $this->tMinMax[$sProject]['max'] >= $iCurrentDate ){
						$accolade=';border-top:4px solid #043f70';
					}
					
					$sLink=null;
					if(isset($this->tLinkHashtag[$sInputCurrentDate]) and $iLine >= $this->tLinkHashtag[$sInputCurrentDate]['from'] and $iLine <= $this->tLinkHashtag[$sInputCurrentDate]['to']   ){
						$sLink=';border-right:2px dotted black';
					}
					
					$sJalonBurned=null;
					if($sClass=='taskOn' and $sJalon!='' and isset($this->tHashtag[$sJalon]) and $iCurrentDate >= $this->tHashtag[$sJalon]['startdate'] ){
						
						$sJalonBurned=';background:red';
					}
					
					?>
					
					
				
					
				
					<td class="<?php echo $sClass?>" style="font-size:8px;border:<?php echo $border?>px solid darkred<?php echo $accolade?><?php echo $sLink?><?php echo $sJalonBurned?>">
						&nbsp;
					</td>
					
				
					
					
				<?php endfor;?>
				
				
			</tr>
		
	<?php endforeach;?>
	
	<tr>
		<td></td>
		<th>Total</th>
		
		
		<?php $oCurrentDate=new plugin_date(date('Y-m-d'));?>
		<?php $oCurrentDate->addDay($iStartDay);?>
		<?php for($i=$iStartDay;$i<$iEndDay;$i++):?>
		
			
			<?php
			$style=null; 
			$oCurrentDate->addDay(1);
			$iCurrentDate=(int)$oCurrentDate->toString('Ymd');
			
			?>
			
			<td >
				<?php if(isset($tChargeDev[$iCurrentDate])):?>
				
					<?php foreach($tChargeDev[$iCurrentDate] as $sDev => $iCharge):?>
				
						<a style="font-size:9px;<?php if($iCharge > 100):?>color:red<?php else:?>color:green<?php endif;?>" href="#" title="@<?php echo $sDev?> <?php echo $iCharge?>%"><?php echo strtoupper(substr($sDev,0,1))?></a><br/>
						<?php if($iCharge > 100):?>
							<?php if(!isset($tNbChargeDev[$sDev])){ $tNbChargeDev[$sDev]=0; }?>
							<?php $tNbChargeDev[$sDev]+=1; ?>
						<?php endif;?>
					<?php endforeach;?>
				
				<?php endif;?>
			</td>
			
			
		<?php endfor;?>
		
	</tr>
</table>


<h2>Surcharge: (plus de 100%)</h2>
<?php foreach($tNbChargeDev as $sDev => $iNb):?>
<p><span style="color:darkgreen">@<?php echo $sDev?></span>: <?php echo $iNb?> jours</p>
<?php endforeach;?>


<?php if(_root::getParam('line',-1) > -1):?>
</form>
<?php endif;?>

</div>
