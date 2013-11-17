<?php class model_ouinon extends abstract_model{
	
	public static function getInstance(){
		return self::_getInstance(__CLASS__);
	}
	
	public function getSelect(){
		return array('non','oui');
	}
	
}
