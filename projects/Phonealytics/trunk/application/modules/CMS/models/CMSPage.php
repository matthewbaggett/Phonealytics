<?php 
class CMS_Model_CMSPage extends Zend_Db_Table_Row_Abstract{
	public function describe(){
		return $this->strPublicName;
	}
	public function getPageByPath($path){
		$tbl_cms_page = new CMS_Model_DbTable_CMSPages();
		return $tbl_cms_page->fetchRow(
        		sprintf("strPath = '%s'", $path)
        	);
        
	}
}
