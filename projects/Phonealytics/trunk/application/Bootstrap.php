<?php
require_once("routers/Cli.php");

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	protected function _initRouter ()
	{
	    if (PHP_SAPI == 'cli')
	    {
	    	$this->bootstrap ('frontcontroller');
	        Zend_Controller_Front::getInstance()->setParam('disableOutputBuffering', true);
	    	$front = $this->getResource('frontcontroller');
	        $front->setRouter (new Application_Router_Cli ());
	        $front->setRequest (new Zend_Controller_Request_Simple ());

	    }
	}
	protected function _initError ()
	{
	    if (PHP_SAPI == 'cli'){
	        $front = Zend_Controller_Front::getInstance();
			$front->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
			    'controller' => 'error',
			    'action'     => 'cli'
			)));
	    }
	}

}

