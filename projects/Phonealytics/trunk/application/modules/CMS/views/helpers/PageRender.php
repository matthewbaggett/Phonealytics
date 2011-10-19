<?php
class Zend_View_Helper_PageRender extends Zend_View_Helper_Abstract
{
    public function PageRender (CMS_Model_CMSPage $page){
    	$str_xsl_path = dirname(__FILE__). "../../../xsl/{$page->strTemplate}.xsl";
    	$str_xsl = file_get_contents($str_xsl_path);
    	$xslt = new XSLTProcessor();
   		$xslt->importStylesheet(new  SimpleXMLElement($str_xsl));
   		return $xslt->transformToXml(new SimpleXMLElement($page->strContent)); 
    }
}