<?php

class Application_Model_DbTable_Addresses extends Zend_Db_Table_Abstract
{

    protected $_name = 'tblAddresses';
	protected $_rowClass = 'Application_Model_Address';
    

}

