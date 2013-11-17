<?php 
class module_member extends abstract_module{
	
	public function before(){
		$this->oLayout=new _layout('template1');
		
		//$this->oLayout->addModule('menu','menu::index');
	}
	
	
	public function _index(){
	    //on considere que la page par defaut est la page de listage
	    $this->_list();
	}
	
	
	public function _list(){
		
		$tMember=model_member::getInstance()->findAll();
		
		$oView=new _view('member::list');
		$oView->tMember=$tMember;
		
				$oView->tJoinmodel_ouinon=model_ouinon::getInstance()->getSelect();
		
		$this->oLayout->add('main',$oView);
		 
	}

	
	
	
	
	public function _edit(){
		$tMessage=$this->processSave();
		
		$oMember=model_member::getInstance()->findById( _root::getParam('id') );
		$oMember=$this->fillRow($oMember);
		
		$oView=new _view('member::edit');
		$oView->oMember=$oMember;
		$oView->tId=model_member::getInstance()->getIdTab();
		
				$oView->tJoinmodel_ouinon=model_ouinon::getInstance()->getSelect();
		
		$oPluginXsrf=new plugin_xsrf();
		$oView->token=$oPluginXsrf->getToken();
		$oView->tMessage=$tMessage;
		
		$this->oLayout->add('main',$oView);
	}

	
	
	public function _show(){
		$oMember=model_member::getInstance()->findById( _root::getParam('id') );
		
		$oView=new _view('member::show');
		$oView->oMember=$oMember;
		
				$oView->tJoinmodel_ouinon=model_ouinon::getInstance()->getSelect();
		$this->oLayout->add('main',$oView);
	}

	
	
	
	
	private function fillRow($oMember){
		if(!_root::getRequest()->isPost() ){ //si ce n'est pas une requete POST on ne soumet pas
			return $oMember;
		}
		
		$tId=model_member::getInstance()->getIdTab();
		$tColumn=model_member::getInstance()->getListColumn();
		foreach($tColumn as $sColumn){
			if( _root::getParam($sColumn,null) === null ){ 
				continue;
			}else if( in_array($sColumn,$tId)){
				 continue;
			}
			
			$oMember->$sColumn=_root::getParam($sColumn,null) ;
		}
		return $oMember;
	}

	private function processSave(){
		if(!_root::getRequest()->isPost() ){ //si ce n'est pas une requete POST on ne soumet pas
			return null;
		}
		
		$oPluginXsrf=new plugin_xsrf();
		if(!$oPluginXsrf->checkToken( _root::getParam('token') ) ){ //on verifie que le token est valide
			return array('token'=>$oPluginXsrf->getMessage() );
		}
	
		$iId=_root::getParam('id',null);
		if($iId==null){
			$oMember=new row_member;	
		}else{
			$oMember=model_member::getInstance()->findById( _root::getParam('id',null) );
		}
		
		$tId=model_member::getInstance()->getIdTab();
		$tColumn=model_member::getInstance()->getListColumn();
		foreach($tColumn as $sColumn){
			 $oPluginUpload=new plugin_upload($sColumn);
			if($oPluginUpload->isValid()){
				$sNewFileName=_root::getConfigVar('path.upload').$sColumn.'_'.date('Ymdhis');

				$oPluginUpload->saveAs($sNewFileName);
				$oMember->$sColumn=$oPluginUpload->getPath();
				continue;	
			}else  if( _root::getParam($sColumn,null) === null ){ 
				continue;
			}else if( in_array($sColumn,$tId)){
				 continue;
			}
			
			$oMember->$sColumn=_root::getParam($sColumn,null) ;
		}
		
		if($oMember->save()){
			//une fois enregistre on redirige (vers la page liste)
			_root::redirect('member::list');
		}else{
			return $oMember->getListError();
		}
		
	}
	
	

	
	public function after(){
		$this->oLayout->show();
	}
	
	
}

/*variables
#select		$oView->tJoinmember=member::getInstance()->getSelect();#fin_select
#uploadsave $oPluginUpload=new plugin_upload($sColumn);
			if($oPluginUpload->isValid()){
				$sNewFileName=_root::getConfigVar('path.upload').$sColumn.'_'.date('Ymdhis');

				$oPluginUpload->saveAs($sNewFileName);
				$oMember->$sColumn=$oPluginUpload->getPath();
				continue;	
			}else #fin_uploadsave

#methodList
	public function _list(){
		
		$tMember=model_member::getInstance()->findAll();
		
		$oView=new _view('member::list');
		$oView->tMember=$tMember;
		
				$oView->tJoinmodel_ouinon=model_ouinon::getInstance()->getSelect();
		
		$this->oLayout->add('main',$oView);
		 
	}
methodList#

#methodPaginationList
	public function _list(){
		
		$tMember=model_member::getInstance()->findAll();
		
		$oView=new _view('member::list');
		$oView->tMember=$tMember;
		
				$oView->tJoinmodel_ouinon=model_ouinon::getInstance()->getSelect();
		
		$oModulePagination=new module_pagination;
		$oModulePagination->setModuleAction('member::list');
		$oModulePagination->setParamPage('page');
		$oModulePagination->setLimit(5);
		$oModulePagination->setPage( _root::getParam('page') );
		$oModulePagination->setTab( $tMember );
		
		$oView->tMember=$oModulePagination->getPageElement();
		
		$this->oLayout->add('main',$oView);
		
		
		$oViewPagination=$oModulePagination->build();
		
		$this->oLayout->add('main',$oViewPagination);
		 
	}
methodPaginationList#
			
#methodNew
	public function _new(){
		$tMessage=$this->processSave();
	
		$oMember=new row_member;
		$oMember=$this->fillRow($oMember);
		
		$oView=new _view('member::new');
		$oView->oMember=$oMember;
		
				$oView->tJoinmodel_ouinon=model_ouinon::getInstance()->getSelect();
		
		$oPluginXsrf=new plugin_xsrf();
		$oView->token=$oPluginXsrf->getToken();
		$oView->tMessage=$tMessage;
		
		$this->oLayout->add('main',$oView);
	}
methodNew#
	
#methodEdit
	public function _edit(){
		$tMessage=$this->processSave();
		
		$oMember=model_member::getInstance()->findById( _root::getParam('id') );
		$oMember=$this->fillRow($oMember);
		
		$oView=new _view('member::edit');
		$oView->oMember=$oMember;
		$oView->tId=model_member::getInstance()->getIdTab();
		
				$oView->tJoinmodel_ouinon=model_ouinon::getInstance()->getSelect();
		
		$oPluginXsrf=new plugin_xsrf();
		$oView->token=$oPluginXsrf->getToken();
		$oView->tMessage=$tMessage;
		
		$this->oLayout->add('main',$oView);
	}
methodEdit#

#methodShow
	public function _show(){
		$oMember=model_member::getInstance()->findById( _root::getParam('id') );
		
		$oView=new _view('member::show');
		$oView->oMember=$oMember;
		
				$oView->tJoinmodel_ouinon=model_ouinon::getInstance()->getSelect();
		$this->oLayout->add('main',$oView);
	}
methodShow#
	
#methodDelete
	public function _delete(){
		$tMessage=$this->processDelete();

		$oMember=model_member::getInstance()->findById( _root::getParam('id') );
		
		$oView=new _view('member::delete');
		$oView->oMember=$oMember;
		
				$oView->tJoinmodel_ouinon=model_ouinon::getInstance()->getSelect();

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
	
		$oMember=model_member::getInstance()->findById( _root::getParam('id',null) );
				
		$oMember->delete();
		//une fois enregistre on redirige (vers la page liste)
		_root::redirect('member::list');
		
	}
methodProcessDelete#	
			
variables*/

