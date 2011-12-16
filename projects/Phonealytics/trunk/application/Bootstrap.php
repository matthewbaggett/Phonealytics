<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initConfig()
	{
	    $config = new Zend_Config($this->getOptions(), true);
	    
	    Zend_Registry::set('config', $config);
	    if(isset($config->session)){
	    	Zend_Session::setOptions($config->session->toArray());
	    }
	    return $config;
	}
	protected function _initRouter ()
	{
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->registerNamespace("Turbo_");
		$autoloader->registerNamespace("Boris_");
		$autoloader->registerNamespace("Snoopy_");
	    if (PHP_SAPI == 'cli')
	    {
	    	$this->bootstrap ('frontcontroller');
	        Zend_Controller_Front::getInstance()->setParam('disableOutputBuffering', true);
	    	$front = $this->getResource('frontcontroller');
	        $front->setRouter (new Turbo_Router_Cli());
	        $front->setRequest (new Zend_Controller_Request_Simple());
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

