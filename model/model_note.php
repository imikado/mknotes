<?php
class model_note extends abstract_model{
	
	protected $sClassRow='row_note';
	
	protected $sTable='note';
	protected $sConfig='xmlExple';
	
	protected $tId=array('id');

	public static function getInstance(){
		return self::_getInstance(__CLASS__);
	}

	public function findById($uId){
		return $this->findOne('SELECT * FROM '.$this->sTable.' WHERE id=?',$uId );
	}
	public function findAll(){
		return $this->findMany('SELECT * FROM '.$this->sTable.' WHERE member_id=?',_root::getAuth()->getAccount()->id);
	}
	
	public function findAllAdmin(){
		return $this->findMany('SELECT * FROM '.$this->sTable.' ');
	}
	
}

class row_note extends abstract_row{
	
	protected $sClassModel='model_note';
	
	public function findListProject(){
		$tNote= explode("\n",$this->content);
		$tProject=array();
		foreach($tNote as $i => $sLine){
			if(substr($sLine,0,3)=='==='){
				break;
			}
			if(substr($sLine,0,2)=='--'){
				$tProject[$i]=$sLine;
			}elseif(substr($sLine,0,1)=='-'){
				$tProject[$i]=$sLine;
			}
			
			if(substr($sLine,0,2)=='=='){
				$tProject[$i]=$sLine;
			}
		}
		return $tProject;
	}
	
	public function findListOnlyProject(){
		$tNote= explode("\n",$this->content);
		$tProject=array();
		foreach($tNote as $i => $sLine){
			if(substr($sLine,0,3)=='==='){
				break;
			}
			
			if(substr($sLine,0,2)=='=='){
				$tProject[$i]=$sLine;
			}
		}
		return $tProject;
	}
	
	public function findListProjetWithTask(){
		$tNote= explode("\n",$this->content);
		$tProject=array();
		$sProject=null;
		foreach($tNote as $i => $sLine){
			if(substr($sLine,0,3)=='==='){
				break;
			}
			
			if(substr($sLine,0,2)=='=='){
				$sProject=substr($sProject,2);
			}else if($sProject!=''){
				$tProject[$sProject].=$sLine;
			}
		}
		return $tProject;
	}
	
	public function findListHashtagWithTask(){
		$tNote= explode("\n",$this->content);
		$tProject=array();
		$sProject=null;
		foreach($tNote as $i => $sLine){
			if(substr($sLine,0,3)=='==='){
				break;
			}
			
			if(substr($sLine,0,2)=='=='){
				if(preg_match('/#([a-zA-Z]*)')){
					preg_match('/#([a-zA-Z]*)',$tMatch);
					print_r($tMatch[1]);exit;
					$sProject=substr($sProject,2);
				}
			}else if($sProject!=''){
				$tProject[$sProject].=$sLine;
			}
		}
		return $tProject;
	}
	
	
	/*exemple jointure 
	public function findAuteur(){
		return model_auteur::getInstance()->findById($this->auteur_id);
	}
	*/
	/*exemple test validation*/
	private function getCheck(){
		$oPluginValid=new plugin_valid($this->getTab());
		/* renseigner vos check ici
		$oPluginValid->isEqual('champ','valeurB','Le champ n\est pas &eacute;gal &agrave; '.$valeurB);
		$oPluginValid->isNotEqual('champ','valeurB','Le champ est &eacute;gal &agrave; '.$valeurB);
		$oPluginValid->isUpperThan('champ','valeurB','Le champ n\est pas sup&eacute; &agrave; '.$valeurB);
		$oPluginValid->isUpperOrEqualThan('champ','valeurB','Le champ n\est pas sup&eacute; ou &eacute;gal &agrave; '.$valeurB);
		$oPluginValid->isLowerThan('champ','valeurB','Le champ n\est pas inf&eacute;rieur &agrave; '.$valeurB);
		$oPluginValid->isLowerOrEqualThan('champ','valeurB','Le champ n\est pas inf&eacute;rieur ou &eacute;gal &agrave; '.$valeurB);
		$oPluginValid->isEmpty('champ','Le champ n\'est pas vide');
		$oPluginValid->isNotEmpty('champ','Le champ ne doit pas &ecirc;tre vide');
		$oPluginValid->isEmailValid('champ','L\email est invalide');
		$oPluginValid->matchExpression('champ','/[0-9]/','Le champ n\'est pas au bon format');
		$oPluginValid->notMatchExpression('champ','/[a-zA-Z]/','Le champ ne doit pas &ecirc;tre a ce format');
		*/

		return $oPluginValid;
	}

	public function isValid(){
		return $this->getCheck()->isValid();
	}
	public function getListError(){
		return $this->getCheck()->getListError();
	}
	public function save(){
		if(!$this->isValid()){
			return false;
		}
		$this->member_id=_root::getAuth()->getAccount()->id;
		parent::save();
		return true;
	}

}
