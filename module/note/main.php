<?php 
class module_note extends abstract_module{
	
	
	public function before(){
		$this->oLayout=new _layout('template1');
		
		//$this->oLayout->addModule('menu','menu::index');
	}

	public static function getOk(){
		return ' OK';
	}
	
	public function _index(){
	    //on considere que la page par defaut est la page de listage
	    $this->_list();
	}
	
	public function _list(){
		
		$tNote=model_note::getInstance()->findAll();
		
		$oView=new _view('note::list');
		$oView->tNote=$tNote;
		
		

		$this->oLayout->add('main',$oView);
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
			if(preg_match('/\[([0-9\/-]*)\]/',$sLine)){
				preg_match('/\[([0-9\/-]*)\]/',$sLine,$tMatchDate);
				list($sStartDate,$sEndDate)=explode('-',$tMatchDate[1]);
				
				$oStartDate=new plugin_date($sStartDate,'d/m/Y');
				$oEndDate=new plugin_date($sEndDate,'d/m/Y');
				
				$iStartDate=(int)$oStartDate->toString('Ymd');
				$iEndDate=(int)$oEndDate->toString('Ymd');
				
				if(!isset($tMinMax[$sKey]['min']) or $tMinMax[$sKey]['min'] > $iStartDate){
					$tMinMax[$sKey]['min']=$iStartDate;
				}
				
				if(!isset($tMinMax[$sKey]['max']) or $tMinMax[$sKey]['max'] < $iEndDate){
					$tMinMax[$sKey]['max']=$iEndDate;
				}
				
			}
		}
		
		plugin_debug::addSpy('tMinm',$tMinMax);
		
		$oView=new _view('note::diagram');
		$oView->oNote=$oNote;
		$oView->tProject=$tProject;
		$oView->tMinMax=$tMinMax;
		
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
		
		$tDate=_root::getParam('tDate');
		$sMaxDate=$tDate[ count($tDate)-1 ];
		
		$tNote= explode("\n",$oNote->content);
		//plugin_debug::addSpy('tNote',$tNote);
		foreach($tNote as $i => $sLine){
			if($i == _root::getParam('line')){
				
				$sLine=preg_replace('/\[([0-9\/-]*)\]/','',$sLine);
				$sLine.= ' ['.$tDate[0].'-'.$sMaxDate.']';
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

