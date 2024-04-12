<?php 

class jshoppingUpdate500{
	
	public function __construct(){
		$this->updatePayment();
		$this->updateProductCharactiristics();
		$this->updateConfig();
    }
	
	function updateProductCharactiristics(){
		$db = \JFactory::getDBO();
		$query = "select * from #__jshopping_products_extra_fields";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		foreach($list as $v){
			$this->_addNewFieldProductCharactiristic($v->id);
		}
		if (count($list)){
			$extfields = '';
			foreach($list as $v){
				$extfield .= '`extra_field_'.$v->id.'`,';
			}
			$query = "select ".$extfield." product_id from #__jshopping_products order by product_id";
			$db->setQuery($query);
			$products = $db->loadObjectList();
			foreach($products as $prod){
				$table = \JSFactory::getTable('producttofield');
				$table->bind((array)$prod);
				$table->store();
			}
		}
	}
	
	function _addNewFieldProductCharactiristic($id){
		$jshopConfig = \JSFactory::getConfig();
        $db = \JFactory::getDBO();
        $query = "ALTER TABLE `#__jshopping_products_to_extra_fields` ADD `extra_field_".(int)$id."` ".$jshopConfig->new_extra_field_type." NOT NULL";
        $db->setQuery($query);
        $db->execute();
	}
    
    function updatePayment(){
		$db = \JFactory::getDBO();
		$query = "select * from #__jshopping_payment_method";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		foreach($list as $v){
			if (substr($v->payment_params, 0, 1) != '{'){
				$newparams = json_encode($this->_parseOldStringToParams($v->payment_params));
				$query = "update #__jshopping_payment_method set payment_params='".$db->escape($newparams)."' where payment_id=".$v->payment_id;
				$db->setQuery($query);
				$db->execute();
			}
		}
	}
	
	function updateConfig(){
		$db = \JFactory::getDBO();
		$query = "select * from #__jshopping_config";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		foreach ($list as $v) {
			$config = \JSFactory::getTable('config');
			$config->bind((array)$v);
			$config->store();
		}
	}
	
	function _parseOldStringToParams($string){
        $params = explode("\n", $string);
		$newparams = [];
		foreach($params as $param){
            if ($param != '') {
				$pos = strpos($param, '=');
				$ext_param = array(0 => substr($param, 0, $pos), 1 => substr($param, $pos + 1));
				if (!$ext_param[0]) continue;
				$newparams[trim($ext_param[0])] = trim($ext_param[1]);
			}
        }
		return $newparams;
    }
}