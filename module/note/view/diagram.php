<?php $tMonth=array('','Janvier','Fevrier','Mars','AVril','Mai','Juin','Juillet','Aout','Sept','Oct','Nov','Dec');
$iStartDay=-15;
$iEndDay=_root::getParam('limit',50);

$iTodayDate=(int)date('Ymd');

$tChargeDev=array();
$tNbChargeDev=array();

?>
<script>
function editLine(i){
	document.location.href='<?php echo _root::getLink('note::diagram',array('id'=>_root::getParam('id'),'action'=>'popup','line'=>''),0)?>'+i;
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
			//b.style.background='silver';
			a.checked=0;
		}else{
			//b.style.background='darkgreen';
			a.checked=1;
		}
	}
}
function checkRadio(sDate){
	var a=getById('input_'+sDate);
	if(a){
		a.checked=1;
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
.popup{
	border:2px solid gray;
	width:300px;
	padding:10px;
	position:absolute;
	background:white;
	left:40%;
	top:200px;
}
.popup p a{
	border:1px solid #bbb;
	display:block;
	background:#eee;
	text-decoration:none;
	text-align:center;
}
.selected td{
	border:1px solid orange;
}
</style>
<ul class="tabs">
	<li><a href="<?php echo _root::getLink('note::show',array('id'=>$this->oNote->id))?>">Current</a></li>
	<li><a href="<?php echo _root::getLink('note::history',array('id'=>$this->oNote->id))?>">Snapshots</a></li>
	<li class="selected"><a href="<?php echo _root::getLink('note::diagram',array('id'=>$this->oNote->id))?>">Planning</a></li>
	
	<?php if(_root::getAuth() and _root::getAuth()->getAccount() and _root::getAuth()->getAccount()->admin):?>
		<li><a href="<?php echo _root::getLink('note::admin')?>">Planning g&eacute;n&eacute;ral</a> </li>
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
		list($iStartDate,$iEndDate)=$this->oModuleNote->calculateListDate($sProject);
		$iCharge=$this->oModuleNote->calculCharge($sProject);
		$sDev=_root::getAuth()->getAccount()->login;
			
		$bEdit=0;
		$sAction=null;
		$bChecked=0;
		if(_root::getParam('line',-1)==$iLine){
			$sAction=_root::getParam('action');
			if($sAction!='popup'){
				$bEdit=1;
			}
		}
		
		 
		
		?>
		
		<tr class="line <?php if(_root::getParam('line',-1)==$iLine):?>selected<?php endif;?>" >
			
			<td>
				
				<?php if(_root::getParam('action')==null):?>
					<a href="<?php echo _root::getLink('note::diagram',array('id'=>_root::getParam('id'),'action'=>'popup','line'=>$iLine))?>" >EDIT</a>
				<?php elseif($sAction==startdate_enddate):?>
					<div  style="width:90px">
					<input type="submit" value="valid"/> 
					<a href="<?php echo _root::getLink('note::diagram',array('id'=>_root::getParam('id')))?>">cancell</a>
					</div>
				<?php elseif($sAction==startdate_charge):?>
					<div  style="width:150px;text-align:center">
					Charge: <input type="text" name="charge" style="width:50px"/><br />
					Affectation: <input type="text" name="affectation" style="width:50px" value="100"/>%<br />
					<input type="submit" value="valid"/> <a href="<?php echo _root::getLink('note::diagram',array('id'=>_root::getParam('id')))?>">cancel</a>
					</div>
				<?php elseif($sAction==hashtag_charge):?>
					<div  style="width:150px;text-align:center">
					hashtag <input type="text" name="hashtag" style="width:60px"/><br/>
					Charge: <input type="text" name="charge" style="width:50px"/><br />
					Affectation: <input type="text" name="affectation" style="width:50px" value="100"/>%<br />
					<input type="submit" value="valid"/> <a href="<?php echo _root::getLink('note::diagram',array('id'=>_root::getParam('id')))?>">cancel</a>
					</div>
				<?php endif;?>
					
				<?php if(_root::getParam('action')==hashtag_charge):?>
					<p style="margin:0px;text-align:right;"><input type="radio" name="hashtag_line" value="<?php echo $iLine?>"/></p>
				<?php endif;?>
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
					?>><div style="width:450px"><?php echo $this->oModuleNote->format($sProject)?></div></td>
			
			
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
				
				?>
				
				
				<?php if($sAction==startdate_enddate):?>
					<td onclick="checkUncheck('<?php echo $sInputCurrentDate?>')" id="td_<?php echo $sInputCurrentDate?>" class="<?php echo $sClass?>" style="font-size:8px;border:<?php echo $border?>px solid darkred"><input onclick="checkUncheck('<?php echo $sInputCurrentDate?>')" id="input_<?php echo $sInputCurrentDate?>" name="tDate[]" value="<?php echo $sInputCurrentDate?>" <?php if($sClass=='taskOn'):?>checked="checked"<?php endif;?> type="checkbox"/></td>
				<?php elseif($sAction==startdate_charge):?>
					<td onclick="checkRadio('<?php echo $sInputCurrentDate?>')" id="td_<?php echo $sInputCurrentDate?>" class="<?php echo $sClass?>" style="font-size:8px;border:<?php echo $border?>px solid darkred"><input id="input_<?php echo $sInputCurrentDate?>" name="sDate" value="<?php echo $sInputCurrentDate?>" <?php if($sClass=='taskOn' and !$bChecked):?>checked="checked"<?php $bChecked=1; endif;?> type="radio"/></td>
				<?php else:?>
				
				
					<td class="<?php echo $sClass?>" style="font-size:8px;border:<?php echo $border?>px solid darkred<?php echo $accolade?><?php echo $sLink?>">
						&nbsp;
					</td>
				
				<?php endif;?>
				
				
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

<?php if(_root::getParam('action')=='popup'):?>
<?php $tParam=array(
			'id'=>_root::getParam('id'),
			'line'=>_root::getParam('line'),
			'action'=>'popup',
);?>
<div class="popup">
	<p style="margin:0px;"><a style="border:0px;padding-right:4px;background:black;text-align:right;color:white" href="<?php echo _root::getLink('note::diagram',array('id'=>_root::getParam('id')))?>">Fermer</a></p>
	
	<h1>Choisissez</h1>
	<?php $tParam['action']=startdate_enddate;?>
	<p><a href="<?php echo _root::getLink('note::diagram',$tParam)?>">Date d&eacute;but &agrave; date de fin</a></p>
	<?php $tParam['action']=startdate_charge;?>
	<p><a href="<?php echo _root::getLink('note::diagram',$tParam)?>">Date d&eacute;but + charge</a></p>
	<?php $tParam['action']=hashtag_charge;?>
	<p><a href="<?php echo _root::getLink('note::diagram',$tParam)?>">Tache li&eacute;e + charge</a></p>
	
</div>
<?php endif;?>
