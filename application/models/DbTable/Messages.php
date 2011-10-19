<?php

class Application_Model_DbTable_Messages extends Zend_Db_Table_Abstract
{

    protected $_name = 'tblMessages';
	protected $_rowClass = 'Application_Model_Message';
    

}

