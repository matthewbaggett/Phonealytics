<?php 
class Application_Model_User extends Zend_Db_Table_Row_Abstract{
	public function getMailboxes(){
		$tbl_mailboxes = new Application_Model_DbTable_Mailboxes();
		return $tbl_mailboxes->fetchAll('intUserID = '.$this->intUserID);
	}
	static public function getUserByName($name){
		$tbl_users = new Application_Model_DbTable_Users();
		return $tbl_users->fetchRow(sprintf("strUsername = '%s'",filter_var($name,FILTER_SANITIZE_STRING)));
	}
}
