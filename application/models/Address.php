<?php 
class Application_Model_Address extends Zend_Db_Table_Row_Abstract{
	public function describe(){
		return $this->strPublicName;
	}
}
