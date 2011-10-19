<?php

class CMS_ViewController extends Zend_Controller_Action
{

	protected $tbl_cms_page;
	
    public function init()
    {
    	$this->tbl_cms_page = new CMS_Model_DbTable_CMSPages();
    }

    public function indexAction()
    {
    	$path = $this->_request->getParam('page');
    	
        if($path){
        	$obj_page = CMS_Model_CMSPage::getPageByPath($path);
        	if($obj_page){
        		$this->view->assign('page', $obj_page);
        	}else{
        		throw new exception("Uhoh! Page does not exist!");
        	}
        }else{
       		throw new exception("Uhoh! You need to pass a path!");
        }

    }


}

