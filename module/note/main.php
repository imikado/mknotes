<?php 
class module_note extends abstract_module{
	
	protected $tMember;
	protected $tLink;
	protected $tHashtag;
	protected $tLinkHashtag;
	
	public function before(){
		$this->oLayout=new _layout('template1');
		
		$this->tMember=model_member::getInstance()->getSelect();
		
		define('startdate_enddate','startdate_enddate');
		define('startdate_charge','startdate_charge');
		define('hashtag_charge','hashtag_charge');
		
		//$this->oLayout->addModule('menu','menu::index');
	}

	public static function getOk(){
		return ' OK';
	}
	public static function getRun(){
		return ' RUN';
	}
	public static function getHide(){
		return ' HIDE';
	}
	
	public function _index(){
	    //on considere que la page par defaut est la page de listage
	    $this->_list();
	}
	
	public function _admin(){
		$this->oLayout=new _layout('template2');
		
		
		
		$tNote=model_note::getInstance()->findAllAdmin();
		
		$tContent=array();
		foreach($tNote as $oNote){
			
			if(!isset($this->tMember[$oNote->member_id])){
				continue;
			}
			
			$tLine=preg_split('/\n/',$oNote->content);
			foreach($tLine as $sLine){
				if(substr($sLine,0,3)=='==='){
					break;
				}
				
				
				if($sLine!='' ){
					$sLine.=' @'.$this->tMember[$oNote->member_id];
				}
				
				$tContent[]=$sLine;
				
				
			}
		}
		
		$tHashtag=array();
		$sHashtag=null;
		$tProject=array();
		$sProject=null;
		foreach($tContent as $sLine){
			if(trim($sLine)=='') continue;
			
			if(substr($sLine,0,2)=='==' and preg_match('/#([a-zA-Z]+)/',$sLine)){
				preg_match('/#([a-zA-Z]+)/',$sLine,$tMatch);
				
				$sHashtag=$tMatch[1];
				$sProject=null;
				
				$tHashtag[$sHashtag][]=$sLine;
				if(!isset($tProject[$sHashtag])){
					$sLine=preg_replace('/#'.$sHashtag.'/','<span style="color:darkred">#'.$sHashtag.'</span>',$sLine);
					$tProject[$sHashtag][]=$sLine;
				}
				
			}elseif(substr($sLine,0,2)=='==' ){
				$sProject=substr($sLine,2);
				$sHashtag=null;
				
				$tProject[$sProject][]=$sLine;
			}elseif($sHashtag!=''){
				$tProject[$sHashtag][]=$sLine;
			}elseif($sProject!=''){
				$tProject[$sProject][]=$sLine;	
			}
			
			
		}
		
		$tMinMax=array();
		$sKey=null;
		
		foreach($tContent as $sLine){
			if(substr($sLine,0,2)=='==' and preg_match('/#([a-zA-Z]+)/',$sLine)){
				preg_match('/#([a-zA-Z]+)/',$sLine,$tMatch);
				
				$sKey=$tMatch[1];
			}elseif(substr($sLine,0,2)=='=='){
				$sKey=substr($sLine,2);
			}
			
			
			list($iStartDate,$iEndDate)=$this->calculateListDate($sLine);
			
			if($iStartDate > 0){
				
				if(!isset($tMinMax[$sKey]['min']) or $tMinMax[$sKey]['min'] > $iStartDate){
					$tMinMax[$sKey]['min']=$iStartDate;
				}
				
				if(!isset($tMinMax[$sKey]['max']) or $tMinMax[$sKey]['max'] < $iEndDate){
					$tMinMax[$sKey]['max']=$iEndDate;
				}
				
			}
			
		}
		
		//----content
		$tContent=array();
		foreach($tProject as  $sProject0 => $tTask){
			foreach($tTask as $sLine){
				$tContent[]=$sLine;
			}
		}
		
		$this->processCalculDate($tContent);
		plugin_debug::addSpy('tLinkHashtag',$this->tLinkHashtag);
		
		$oView=new _view('note::diagramadmin');
		$oView->oNote=$oNote;
		$oView->tProject=$tContent;
		$oView->tMinMax=$tMinMax;
		$oView->oModuleNote=$this;
		$oView->tLinkHashtag=$this->tLinkHashtag;
		$oView->tHashtag=$this->tHashtag;
		
		$this->oLayout->add('main',$oView);
	}
	
	public function _list(){
		
		$tNote=model_note::getInstance()->findAll();
		
		if($tNote){
			_root::redirect('note::show',array('id'=>$tNote[0]->id));
		}else{
			$this->_new();
		}
		
		
	}
	
	
	public function _new(){
		$oNote=new row_note;	
		$sContent='==Mon projet'."\n";
		$sContent.='-ma tache'."\n";
		$sContent.='--ma sous tache'."\n";
		
		$oNote->content=$sContent;
		$oNote->save();
		
		_root::redirect('note::index');
	}

	public function getViewProcessed($sContent,$bWrite=1){
		
		$oView=new _view('note::process');
		$oView->content=$sContent;
		$oView->bWrite=$bWrite;
		$oView->oModuleNote=$this;
		
		return $oView;
	}
	
	public function _edit(){
		$this->oLayout=new _layout('template2');
		
		$tMessage=$this->processSave();
		
		$oNote=model_note::getInstance()->findById( _root::getParam('id') );
		
		$sContent=null;
		$tContent=explode("\n",$oNote->content);
		foreach($tContent as $sLine){
			if(substr($sLine,0,3)=='==='){
				break;
			}
			
			$sContent.=$sLine."\n";
		}
		
		$oView=new _view('note::edit');
		$oView->oNote=$oNote;
		$oView->content=$sContent;
		$oView->tId=model_note::getInstance()->getIdTab();
		
		
		$oPluginXsrf=new plugin_xsrf();
		$oView->token=$oPluginXsrf->getToken();
		$oView->tMessage=$tMessage;
		
		$this->oLayout->add('main',$oView);
	}

	public function _help(){
		$oView=new _view('note::help');
		
		$this->oLayout->add('main',$oView);
	}
	
	public function _show(){
		$this->processSaveChecked();
		$this->processSaveUpdateLine();
		
		$oNote=model_note::getInstance()->findById( _root::getParam('id') );
		
		$oView=new _view('note::show');
		$oView->oNote=$oNote;
		$oView->oViewProcessed=$this->getViewProcessed($oNote->content);
		
		$this->oLayout->add('main',$oView);
	}
	
	public function _history(){
		$this->processSaveChecked();
		$this->processSaveUpdateLine();
		
		$oNote=model_note::getInstance()->findById( _root::getParam('id') );
		
		$oView=new _view('note::history');
		$oView->oNote=$oNote;
		$oView->oModuleNote=$this;
		
		$this->oLayout->add('main',$oView);
	}
	
	public function _archive(){
		$oNote=model_note::getInstance()->findById( _root::getParam('id') );
		
		$tCurrent=array();
		
		$tNote= explode("\n",$oNote->content);
		foreach($tNote as $i => $sLine){
			if(substr($sLine,0,3)=='==='){
				break;
			}
			
			$tCurrent[]=$sLine;

		}
		
		
		$oNote->content=implode("\n",$tCurrent)."\n".'===archive '.date('d/m/Y H\hi')."\n".implode("\n",$tNote);
		$oNote->save();
		
		_root::redirect('note::show',array('id'=>$oNote->id,'snapshot'=>'1'));
		
	}
	
	private function processCalculDate($tContent){
		//$tContent=preg_split('/\n/',$sContent);
	
		foreach($tContent as $i => $sLine){
			if(preg_match('/#([a-zA-Z]+)/',$sLine) ){ 
				preg_match('/#([a-zA-Z]+)/',$sLine,$tMatch);
				
				$sHashtag=$tMatch[1];
				
				$this->tHashtag[$sHashtag]['text']=$sLine;
				$this->tHashtag[$sHashtag]['line']=$i;
				
				$this->processCalculDateForHashtag($sHashtag,$sLine);
			
			}
			if(preg_match('/\[([a-zA-Z0-9;%]*)\]/',$sLine)){
				preg_match('/\[([a-zA-Z0-9;%]*)\]/',$sLine,$tMatchDate);

				$iAffect=1;
				$tData=explode(';',$tMatchDate[1]);
				if(isset($tData[2])){
					$sAffect=str_replace('%','',$tData[2]);
					$iAffect=($sAffect/100);
				}
				
				$sHashtag=$tData[0];
				
				if(!isset($this->tLink[$sHashtag])){
					$this->processCalculDateForHashtag($sHashtag);
				}
				
				$this->tLinkHashtag[ $this->tLink[$sHashtag] ]['from']=$this->tHashtag[$sHashtag]['line'];
				$this->tLinkHashtag[ $this->tLink[$sHashtag] ]['to']=$i;
				
			}
			
		}
		
		
	
	}
	private function processCalculDateForHashtag($sHashtag){
		list($iStartDate,$iEndDate,$sEndDate)=$this->getListDate($this->tHashtag[$sHashtag]['text']);
		$this->tHashtag[$sHashtag]['startdate']=$iStartDate;
		$this->tHashtag[$sHashtag]['enddate']=$iEndDate;
		
		$this->tLink[$sHashtag]=$sEndDate;
	}
	
	private function getListDate($sProject){
		$iStartDate=0;
		$iEndDate=0;
		$sEndDate=null;
		if(preg_match('/\[([0-9\/-]*)\]/',$sProject)){
			preg_match('/\[([0-9\/-]*)\]/',$sProject,$tMatchDate);
			list($sStartDate,$sEndDate)=explode('-',$tMatchDate[1]);
			
			$oStartDate=new plugin_date($sStartDate,'d/m/Y');
			$oEndDate=new plugin_date($sEndDate,'d/m/Y');
			
			$iStartDate=(int)$oStartDate->toString('Ymd');
			$iEndDate=(int)$oEndDate->toString('Ymd');
			
			$sEndDate=$oEndDate->toString('d/m/Y');
		}elseif(preg_match('/\[([0-9\/;%]*)\]/',$sProject)){
			preg_match('/\[([0-9\/;%]*)\]/',$sProject,$tMatchDate);
			
			$iAffect=1;
			$tData=explode(';',$tMatchDate[1]);
			if(isset($tData[2])){
				$sAffect=str_replace('%','',$tData[2]);
				$iAffect=($sAffect/100);
			}
			
			$sStartDate=$tData[0];
			$iCharge=$tData[1];
			
			if($iAffect >0){			
				$iCharge=ceil( ($iCharge-1)/$iAffect);
			}
		
			$oStartDate=new plugin_date($sStartDate,'d/m/Y');
			$oEndDate=clone $oStartDate;
			$i=0;
			while($i<$iCharge){
				$oEndDate->addDay(1);
				if($oEndDate->getWeekDay()!=6 and $oEndDate->getWeekDay()!=0){
					$i++;
				}
			}
			
			$iStartDate=(int)$oStartDate->toString('Ymd');
			$iEndDate=(int)$oEndDate->toString('Ymd');
			
			$sEndDate=$oEndDate->toString('d/m/Y');
		
		}elseif(preg_match('/\[([a-zA-Z0-9;%]*)\]/',$sProject)){
			preg_match('/\[([a-zA-Z0-9;%]*)\]/',$sProject,$tMatchDate);
			
			$iAffect=1;
			$tData=explode(';',$tMatchDate[1]);
			if(isset($tData[2])){
				$sAffect=str_replace('%','',$tData[2]);
				$iAffect=($sAffect/100);
			}
			
			$sHashtag=$tData[0];
			
			if(!isset($this->tLink[$sHashtag])){
				$this->processCalculDateForHashtag($sHashtag);
			}
			
			$sStartDate=$this->tLink[$sHashtag];
			$iCharge=$tData[1];
			
			$iCharge=ceil( ($iCharge-1)/$iAffect);
			
			$oStartDate=new plugin_date($sStartDate,'d/m/Y');
			$oStartDate->addDay(1);
			$oEndDate=clone $oStartDate;
			$i=0;
			while($i<$iCharge){
				$oEndDate->addDay(1);
				if($oEndDate->getWeekDay()!=6 and $oEndDate->getWeekDay()!=0){
					$i++;
				}
			}
			
			$iStartDate=(int)$oStartDate->toString('Ymd');
			$iEndDate=(int)$oEndDate->toString('Ymd');
			
			$sEndDate=$oEndDate->toString('d/m/Y');
		}
		return array($iStartDate,$iEndDate,$sEndDate);
	}
	
	
	
	public function _diagram(){
		$this->processDiagram();
		
		$this->oLayout=new _layout('template2');
		
		$oNote=model_note::getInstance()->findById( _root::getParam('id') );
		
		$tProject=$oNote->findListProject();
		
		
		
		$tMinMax=array();
		$sKey=null;
		foreach($tProject as $sLine){
			if(substr($sLine,0,2)=='=='){
				$sKey=substr($sLine,2);
			}
			
			list($iStartDate,$iEndDate)=$this->calculateListDate($sLine);
			
			if($iStartDate > 0){
				
				if(!isset($tMinMax[$sKey]['min']) or $tMinMax[$sKey]['min'] > $iStartDate){
					$tMinMax[$sKey]['min']=$iStartDate;
				}
				
				if(!isset($tMinMax[$sKey]['max']) or $tMinMax[$sKey]['max'] < $iEndDate){
					$tMinMax[$sKey]['max']=$iEndDate;
				}
				
			}
		}
		
		$this->processCalculDate($tProject);
		
		plugin_debug::addSpy('tLinkHashtag',$this->tLinkHashtag);
		
		$oView=new _view('note::diagram');
		$oView->oNote=$oNote;
		$oView->tProject=$tProject;
		$oView->tMinMax=$tMinMax;
		$oView->oModuleNote=$this;
		$oView->tLinkHashtag=$this->tLinkHashtag;
		$oView->tHashtag=$this->tHashtag;
		
		plugin_debug::addSpy('tHashtag',$this->tHashtag);
		
		$this->oLayout->add('main',$oView);
	}
	
	public function _preview(){ 
		$this->oLayout=new _layout('preview');
		$sText=_root::getParam('text');
		
		$oView=$this->getViewProcessed($sText,0);
		
		
		$this->oLayout->add('main',$oView);
	}
	
	
	
	private function processDiagram(){
		if(!_root::getRequest()->isPost() ){ //si ce n'est pas une requete POST on ne soumet pas
			return null;
		}
		
		$oNote=model_note::getInstance()->findById( _root::getParam('id',null) );
		
		$sAction=_root::getParam('action');
		
		$tNote= explode("\n",$oNote->content);
		
		if($sAction==startdate_enddate){
			$tDate=_root::getParam('tDate');
			$sMaxDate=$tDate[ count($tDate)-1 ];
		}else if($sAction == startdate_charge){
			$sDate=_root::getParam('sDate');
			$iCharge=_root::getParam('charge');
			$iAffectation=_root::getParam('affectation');
		}else if($sAction == hashtag_charge){
			$sHashtag=_root::getParam('hashtag');
			$iCharge=_root::getParam('charge');
			$iAffectation=_root::getParam('affectation');
			$ihashtag_line=_root::getParam('hashtag_line');
			
			foreach($tNote as $i => $sLine){
				if($i == $ihashtag_line){
					if(!preg_match('/#'.$sHashtag.'/',$sLine)){
						$sLine.=' #'.$sHashtag;
					}
				}
				$tNote[$i]=$sLine;
			}
		}
		
		//plugin_debug::addSpy('tNote',$tNote);
		foreach($tNote as $i => $sLine){
			if($i == _root::getParam('line')){
				
				if($sAction==startdate_enddate){
					$sLine=preg_replace('/\[([0-9\/-]*)\]/','',$sLine);
					$sLine.= ' ['.$tDate[0].'-'.$sMaxDate.']';
				}else if($sAction==startdate_charge){
					$sLine=preg_replace('/\[([0-9\/;%]*)\]/','',$sLine);
					$sLine.= ' ['.$sDate.';'.$iCharge.';'.$iAffectation.'%]';
				}else if($sAction==hashtag_charge){
					$sLine=preg_replace('/\[([a-zA-Z0-9\/;%]*)\]/','',$sLine);
					$sLine.= ' ['.$sHashtag.';'.$iCharge.';'.$iAffectation.'%]';
				}
				
			}
			$tNote[$i]=$sLine;
			
		}
		$oNote->content=implode("\n",$tNote);
		$oNote->save();
		
		_root::redirect('note::diagram',array('id'=>$oNote->id));
	}
	
	private function processSaveChecked(){
		if(!_root::getRequest()->isPost() ){ //si ce n'est pas une requete POST on ne soumet pas
			return null;
		}elseif(_root::getParam('type')!='checked'){
			return null;
		}
		
		$oNote=model_note::getInstance()->findById( _root::getParam('id',null) );
		
		$tNote= explode("\n",$oNote->content);
		foreach($tNote as $i => $sLine){
			if($i == _root::getParam('line')){
				$checked=_root::getParam('checked');
				if($checked){
					$sLine.=self::getOk();
				}else{
					$sLine=str_replace(self::getOk(),'',$sLine);
				}
			}
			$tNote[$i]=$sLine;
			
		}
		$oNote->content=implode("\n",$tNote);
		$oNote->save();
		
	}
	private function processSaveUpdateLine(){
		if(!_root::getRequest()->isPost() ){ //si ce n'est pas une requete POST on ne soumet pas
			return null;
		}elseif(_root::getParam('type')!='updateLine'){
			return null;
		}
		
		$oNote=model_note::getInstance()->findById( _root::getParam('id',null) );
		
		$tNote= explode("\n",$oNote->content);
		foreach($tNote as $i => $sLine){
			if($i == _root::getParam('line')){
				$sLine=_root::getParam('content');
			}
			$tNote[$i]=$sLine;
			
		}
		$oNote->content=implode("\n",$tNote);
		$oNote->save();
		
	}
	
	
	public function _adminEditByMember(){
			
		$tNote=model_note::getInstance()->findAllByMember(_root::getParam('member_id'));
	
		if($tNote){
			_root::redirect('note::adminEdit',array('id'=>$tNote[0]->id));
		}
		
	}
	public function _adminEdit(){
		$this->oLayout=new _layout('template2');
		
		$tMessage=$this->processAdminSave();
		
		$oNote=model_note::getInstance()->findById( _root::getParam('id') );
		
		$sContent=null;
		$tContent=explode("\n",$oNote->content);
		foreach($tContent as $sLine){
			if(substr($sLine,0,3)=='==='){
				break;
			}
			
			$sContent.=$sLine."\n";
		}
		
		$oView=new _view('note::edit');
		$oView->oNote=$oNote;
		$oView->content=$sContent;
		$oView->tId=model_note::getInstance()->getIdTab();
		
		
		$oPluginXsrf=new plugin_xsrf();
		$oView->token=$oPluginXsrf->getToken();
		$oView->tMessage=$tMessage;
		
		$this->oLayout->add('main',$oView);
	}
	

	public function processSave(){
		if(!_root::getRequest()->isPost() ){ //si ce n'est pas une requete POST on ne soumet pas
			return null;
		}
		
		$oPluginXsrf=new plugin_xsrf();
		if(!$oPluginXsrf->checkToken( _root::getParam('token') ) ){ //on verifie que le token est valide
			return array('token'=>$oPluginXsrf->getMessage() );
		}
	
		$iId=_root::getParam('id',null);
		if($iId==null){
			$oNote=new row_note;	
		}else{
			$oNote=model_note::getInstance()->findById( _root::getParam('id',null) );
		}
		
		
		$tArchiveContent=array();
		$bArchive=0;
		$tNote= explode("\n",$oNote->content);
		foreach($tNote as $i => $sLine){
			if(substr($sLine,0,3)=='==='){
				$bArchive=1;
			}
			
			if($bArchive){
				$tArchiveContent[]=$sLine;
			}
		}
		
		$oNote->content=_root::getParam('content')."\n".implode("\n",$tArchiveContent);
		
		
		
		if($oNote->save()){
			//une fois enregistre on redirige (vers la page liste)
			
			
			_root::redirect('note::show',array('id'=>$oNote->id));
		}else{
			return $oNote->getListError();
		}
		
	}
	public function processAdminSave(){
		if(!_root::getRequest()->isPost() ){ //si ce n'est pas une requete POST on ne soumet pas
			return null;
		}
		
		$oPluginXsrf=new plugin_xsrf();
		if(!$oPluginXsrf->checkToken( _root::getParam('token') ) ){ //on verifie que le token est valide
			return array('token'=>$oPluginXsrf->getMessage() );
		}
	
		$iId=_root::getParam('id',null);
		if($iId==null){
			$oNote=new row_note;	
		}else{
			$oNote=model_note::getInstance()->findById( _root::getParam('id',null) );
		}
		
		
		$tArchiveContent=array();
		$bArchive=0;
		$tNote= explode("\n",$oNote->content);
		foreach($tNote as $i => $sLine){
			if(substr($sLine,0,3)=='==='){
				$bArchive=1;
			}
			
			if($bArchive){
				$tArchiveContent[]=$sLine;
			}
		}
		
		$oNote->content=_root::getParam('content')."\n".implode("\n",$tArchiveContent);
		
		
		
		if($oNote->save()){
			//une fois enregistre on redirige (vers la page liste)
			
			
			_root::redirect('note::admin');
		}else{
			return $oNote->getListError();
		}
		
	}
	
	
	//------------------- TOOLS
	
	public function format($sProject){
		foreach($this->tMember as $member_id => $sLogin){
			$sProject=preg_replace('/@'.$sLogin.'/','<a target="_blank" href="'._root::getLink('note::adminEditByMember',array('member_id'=>$member_id)).'" style=";color:darkgreen">@'.$sLogin.'</span>',$sProject);
		}
		if(preg_match('/#([a-zA-Z]+)/',$sProject) and !preg_match('/#quot/i',$sProject)){
			preg_match('/#([a-zA-Z]+)/',$sProject,$tMatch);
			$sProject=str_replace('#'.$tMatch[1],'<span style="color:darkred">'.'#'.$tMatch[1].'</span>',$sProject);
		}
		if(preg_match('/!([a-zA-Z]+)/',$sProject) and !preg_match('/#quot/i',$sProject)){
			preg_match('/!([a-zA-Z]+)/',$sProject,$tMatch);
			$sProject=str_replace('!'.$tMatch[1],'<span style="color:red">'.'!'.$tMatch[1].'</span>',$sProject);
		}
		
		
		if(preg_match('/\[([0-9\/-]*)\]/',$sProject)){
			preg_match('/\[([0-9\/\-]*)\]/',$sProject,$tMatchDate);
			$sProject=str_replace($tMatchDate[1],'<span style="color:#4a909a;font-weight:bold">'.str_replace('-',' au ',$tMatchDate[1]).'</span>',$sProject);
		}elseif(preg_match('/\[([0-9\/;%]*)\]/',$sProject)){
			preg_match('/\[([0-9\/;%]*)\]/',$sProject,$tMatchDate);
			
			$iAffect=1;
			$tData=explode(';',$tMatchDate[1]);
			if(isset($tData[2])){
				$sAffect=str_replace('%','',$tData[2]);
				$iAffect=$sAffect/100;
			}			
			
			$sStartDate=$tData[0];
			$iCharge=$tData[1];
			
			$sProject=str_replace($tMatchDate[1],'<span style="color:#4a909a;font-weight:bold">'.$sStartDate.' &nbsp;  '.$iCharge.' jour &nbsp; '.($iAffect*100).'%</span>',$sProject);
			
		}elseif(preg_match('/\[([a-zA-Z0-9;%]*)\]/',$sProject)){
			preg_match('/\[([a-zA-Z0-9;%]*)\]/',$sProject,$tMatchDate);
			
			$iAffect=1;
			$tData=explode(';',$tMatchDate[1]);
			if(isset($tData[2])){
				$sAffect=str_replace('%','',$tData[2]);
				$iAffect=($sAffect/100);
			}
			
			$sHashtag=$tData[0];
			
			$sStartDate=$this->tLink[$sHashtag];
			$iCharge=$tData[1];
				
			$sProject=str_replace($tMatchDate[1],'<span style="color:darkred">#'.$sHashtag.'</span><span style="color:#4a909a;font-weight:bold"> &nbsp;  '.$iCharge.' jour &nbsp; '.($iAffect*100).'%</span>',$sProject);
		}
		
		$sProject=str_replace(' OK',' <span style="font-weight:bold;background:#66c673;color:white">OK</span>',$sProject);
		$sProject=str_replace(' RUN',' <span style="font-weight:bold;background:orange;color:white">RUN</span>',$sProject);
		
		
		return $sProject;
		
	}
	
	public function calculateListDate($sProject){
		$iStartDate=0;
		$iEndDate=0;
		$sEndDate=null;
		if(preg_match('/\[([0-9\/-]*)\]/',$sProject)){
			preg_match('/\[([0-9\/-]*)\]/',$sProject,$tMatchDate);
			list($sStartDate,$sEndDate)=explode('-',$tMatchDate[1]);
			plugin_debug::addSpy('sStartDate',$sStartDate);
			plugin_debug::addSpy('sEndDate',$sEndDate);
			
			$oStartDate=new plugin_date($sStartDate,'d/m/Y');
			$oEndDate=new plugin_date($sEndDate,'d/m/Y');
			
			$iStartDate=(int)$oStartDate->toString('Ymd');
			$iEndDate=(int)$oEndDate->toString('Ymd');
			
			$sEndDate=$oEndDate->toString('d/m/Y');
		}elseif(preg_match('/\[([0-9\/;%]*)\]/',$sProject)){
			preg_match('/\[([0-9\/;%]*)\]/',$sProject,$tMatchDate);
			
			$iAffect=1;
			$tData=explode(';',$tMatchDate[1]);
			if(isset($tData[2])){
				$sAffect=str_replace('%','',$tData[2]);
				$iAffect=($sAffect/100);
			}
			
			
			$sStartDate=$tData[0];
			$iCharge=$tData[1];
			
			plugin_debug::addSpy('sStartDate',$sStartDate);
			
			if($iAffect >0){						
				$iCharge=ceil( ($iCharge-1)/$iAffect);
			}
			
			$oStartDate=new plugin_date($sStartDate,'d/m/Y');
			$oEndDate=clone $oStartDate;
			$i=0;
			while($i<$iCharge){
				$oEndDate->addDay(1);
				if($oEndDate->getWeekDay()!=6 and $oEndDate->getWeekDay()!=0){
					$i++;
				}
			}
			
			$iStartDate=(int)$oStartDate->toString('Ymd');
			$iEndDate=(int)$oEndDate->toString('Ymd');
			
			$sEndDate=$oEndDate->toString('d/m/Y');
		
		}elseif(preg_match('/\[([a-zA-Z0-9;%]*)\]/',$sProject)){
			preg_match('/\[([a-zA-Z0-9;%]*)\]/',$sProject,$tMatchDate);
			
			$iAffect=1;
			$tData=explode(';',$tMatchDate[1]);
			if(isset($tData[2])){
				$sAffect=str_replace('%','',$tData[2]);
				$iAffect=($sAffect/100);
			}
			
			$sHashtag=$tData[0];
			
			$sStartDate=$this->tLink[$sHashtag];
			$iCharge=$tData[1];
			
			plugin_debug::addSpy('sStartDate',$sStartDate);
			
			$iCharge=ceil( ($iCharge-1)/$iAffect);
			
			$oStartDate=new plugin_date($sStartDate,'d/m/Y');
			$oStartDate->addDay(1);
			$oEndDate=clone $oStartDate;
			$i=0;
			while($i<$iCharge){
				$oEndDate->addDay(1);
				if($oEndDate->getWeekDay()!=6 and $oEndDate->getWeekDay()!=0){
					$i++;
				}
			}
			
			$iStartDate=(int)$oStartDate->toString('Ymd');
			$iEndDate=(int)$oEndDate->toString('Ymd');
			
			$sEndDate=$oEndDate->toString('d/m/Y');
		}
		
		if(preg_match('/#([a-zA-Z]+)/',$sProject) and $sEndDate){
			
			preg_match('/#([a-zA-Z]+)/',$sProject,$tMatch);
			
			$sHashtag=$tMatch[1];
			$this->tLink[$sHashtag]=$sEndDate;
			
			plugin_debug::addSpy('tl',$this->tLink);
			
		}
		
		
		return array($iStartDate,$iEndDate);
	}
	
	public function calculCharge($sProject){
		$iCharge=100;
		if(preg_match('/([0-9]*)%\]/',$sProject)){
			preg_match('/([0-9]*)%\]/',$sProject,$tMatch);
			
			$iCharge=(int)$tMatch[1];
		}
		return $iCharge;
	}
	public function getDev($sProject){
		if(preg_match('/@([a-zA-Z]*)/',$sProject)){
			preg_match('/@([a-zA-Z]*)/',$sProject,$tMatch);
			return $tMatch[1];
		}
	}
	public function getJalon($sProject){
		if(preg_match('/!([a-zA-Z]+)/',$sProject)){
			preg_match('/!([a-zA-Z]+)/',$sProject,$tMatch);
			return $tMatch[1];
		}
		return null;
	}
	

	
	public function after(){
		$this->oLayout->show();
	}
	
	
}

/*variables
#select		$oView->tJoinnote=note::getInstance()->getSelect();#fin_select
#uploadsave $oPluginUpload=new plugin_upload($sColumn);
			if($oPluginUpload->isValid()){
				$sNewFileName=_root::getConfigVar('path.upload').$sColumn.'_'.date('Ymdhis');

				$oPluginUpload->saveAs($sNewFileName);
				$oNote->$sColumn=$oPluginUpload->getPath();
				continue;	
			}else #fin_uploadsave
			
#methodNew
	public function _new(){
		$tMessage=$this->processSave();
	
		$oNote=new row_note;
		
		$oView=new _view('note::new');
		$oView->oNote=$oNote;
		
		
		
		$oPluginXsrf=new plugin_xsrf();
		$oView->token=$oPluginXsrf->getToken();
		$oView->tMessage=$tMessage;
		
		$this->oLayout->add('main',$oView);
	}
methodNew#
	
#methodEdit
	public function _edit(){
		$tMessage=$this->processSave();
		
		$oNote=model_note::getInstance()->findById( _root::getParam('id') );
		
		$oView=new _view('note::edit');
		$oView->oNote=$oNote;
		$oView->tId=model_note::getInstance()->getIdTab();
		
		
		
		$oPluginXsrf=new plugin_xsrf();
		$oView->token=$oPluginXsrf->getToken();
		$oView->tMessage=$tMessage;
		
		$this->oLayout->add('main',$oView);
	}
methodEdit#

#methodShow
	public function _show(){
		$oNote=model_note::getInstance()->findById( _root::getParam('id') );
		
		$oView=new _view('note::show');
		$oView->oNote=$oNote;
		
		
		$this->oLayout->add('main',$oView);
	}
methodShow#
	
#methodDelete
	public function _delete(){
		$tMessage=$this->processDelete();

		$oNote=model_note::getInstance()->findById( _root::getParam('id') );
		
		$oView=new _view('note::delete');
		$oView->oNote=$oNote;
		
		

		$oPluginXsrf=new plugin_xsrf();
		$oView->token=$oPluginXsrf->getToken();
		$oView->tMessage=$tMessage;
		
		$this->oLayout->add('main',$oView);
	}
methodDelete#	

#methodProcessDelete
	public function processDelete(){
		if(!_root::getRequest()->isPost() ){ //si ce n'est pas une requete POST on ne soumet pas
			return null;
		}
		
		$oPluginXsrf=new plugin_xsrf();
		if(!$oPluginXsrf->checkToken( _root::getParam('token') ) ){ //on verifie que le token est valide
			return array('token'=>$oPluginXsrf->getMessage() );
		}
	
		$oNote=model_note::getInstance()->findById( _root::getParam('id',null) );
				
		$oNote->delete();
		//une fois enregistre on redirige (vers la page liste)
		_root::redirect('note::list');
		
	}
methodProcessDelete#	
			
variables*/

