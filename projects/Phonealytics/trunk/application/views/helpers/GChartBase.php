<?php
class Zend_View_Helper_GChartBase extends Zend_View_Helper_Abstract 
{
	protected $str_div;
	protected $str_function_name;
	
    protected function _getFunctionName(){
    	return str_replace("-","_",strtolower("draw_{$this->str_div}_chart"));
    }
}