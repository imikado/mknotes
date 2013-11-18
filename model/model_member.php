<?php
class model_member extends abstract_model{
	
	protected $sClassRow='row_member';
	
	protected $sTable='member';
	protected $sConfig='xmlExple';
	
	protected $tId=array('id');

	public static function getInstance(){
		return self::_getInstance(__CLASS__);
	}

	public function findById($uId){
		return $this->findOne('SELECT * FROM '.$this->sTable.' WHERE id=?',$uId );
	}
	public function findAll(){
		return $this->findMany('SELECT * FROM '.$this->sTable);
	}
	
	public function getListAccount(){
  
       $tAccount=$this->findAll();
      
       $tLoginPassAccount=array();
      
       if($tAccount){
       foreach($tAccount as $oAccount){
       //on cree ici un tableau indexe par nom d'utilisateur et mot de pase
           $tLoginPassAccount[$oAccount->login][$oAccount->pass]=$oAccount;
       }
       }

       return $tLoginPassAccount;

   }
	public function hashPassword($sPassword){
		//utiliser ici la methode de votre choix pour hasher votre mot de passe
		return sha1('A2fereAF45Dds456'.$sPassword);
	}
	
	public function getSelect(){
		 $tAccount=$this->findAll();
		 
		 $tIndex=array();
		 foreach($tAccount as $oMember){
			 $tIndex[ $oMember->id ]= $oMember->login;
		 }
		 
		 return $tIndex;
	}
	
	public function getListIndexed(){
		$tAccount=$this->findAll();
		 $tIndex=array();
		 foreach($tAccount as $oMember){
			 $tIndex[ $oMember->login ]= $oMember;
		 }
		 
		 return $tIndex;
	}
	
	
}

class row_member extends abstract_row{
	
	protected $sClassModel='model_member';
	
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
		parent::save();
		return true;
	}

}
