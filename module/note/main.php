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
		
		$oView=new _view('note::edit');
		$oView->oNote=$oNote;
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
	
	public function _preview(){ 
		$this->oLayout=new _layout('preview');
		$sText=_root::getParam('text');
		
		$oView=$this->getViewProcessed($sText,0);
		
		
		$this->oLayout->add('main',$oView);
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
		
		$tId=model_note::getInstance()->getIdTab();
		$tColumn=model_note::getInstance()->getListColumn();
		foreach($tColumn as $sColumn){
			 $oPluginUpload=new plugin_upload($sColumn);
			if($oPluginUpload->isValid()){
				$sNewFileName=_root::getConfigVar('path.upload').$sColumn.'_'.date('Ymdhis');

				$oPluginUpload->saveAs($sNewFileName);
				$oNote->$sColumn=$oPluginUpload->getPath();
				continue;	
			}else  if( _root::getParam($sColumn,null) === null ){ 
				continue;
			}else if( in_array($sColumn,$tId)){
				 continue;
			}
			
			$oNote->$sColumn=_root::getParam($sColumn,null) ;
		}
		
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

