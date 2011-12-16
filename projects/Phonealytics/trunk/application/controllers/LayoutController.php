<?php
//require_once(dirname(__FILE__) . "/../modules/CMS/Models/Sitemap.)
class LayoutController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    	$arr_sitemap_nodes_main = CMS_Model_CMSSitemap::getSitemapByHandle('MAIN')->getSitemapNodes(6);
    	
    	$this->view->assign('arr_sitemap_nodes_main',$arr_sitemap_nodes_main);
    }

}

