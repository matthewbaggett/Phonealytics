<?php

class ViewController extends Zend_Controller_Action
{
	protected $obj_user;
	protected $tbl_messages;
	protected $tbl_message_types;
	
	protected $arr_cache_all_messages = null;
	protected $arr_timespans;
	
	private $time_start;
	
 	public function init()
    {
        /* Initialize action controller here */
    	$this->time_start = microtime(true);
    	
    	//Include styling and our own js
    	$this->view->headLink()->appendStylesheet($this->view->baseUrl()."/css/usage.css");
    	$this->view->headScript()->appendFile($this->view->baseUrl()."/js/usage.js");
    	
    	//Include google api shit
    	$this->view->headScript()->appendFile("https://www.google.com/jsapi");
    	$this->view->headScript()->appendScript("google.load('visualization', '1.0', {'packages':['corechart']});");
    	
    	
    	$this->obj_user = Application_Model_User::getUserByName($this->getRequest()->getParam('user'));
    	if($this->obj_user == null){
    		$this->_redirect('/');
    	}
    	$this->view->assign('obj_user',$this->obj_user);
    	
    	$this->tbl_messages = new Application_Model_DbTable_Messages();
    	$this->tbl_message_types = new Application_Model_DbTable_MessageTypes();
    	
    	$this->arr_timespans = array(
    		'today'	=> strtotime('yesterday'),
			'last-week' => strtotime('1 week ago'),
			'last-month' => strtotime('1 month ago'),
			'last-year' => strtotime("1 year ago"),
			'all-time' => 0,
		);
    }	
    
    protected function _get_all_messages(){
    	if($this->arr_cache_all_messages === null){
    		$this->arr_cache_all_messages = $this->tbl_messages->fetchAll("intUserID = {$this->obj_user->intUserID}");
    	}
    	return $this->arr_cache_all_messages;
    }
    
	protected function _process_send_recieve_chart(){
    	$sql_get_type_totals = $this->tbl_messages->select(false);
    	$sql_get_type_totals->setIntegrityCheck(false); 
    	$sql_get_type_totals
    		->from(
    			'tblMessages',
    			array('COUNT(*) AS int_num_rows','intType')
    		)
    		->joinLeft(
    			'tblMessageTypes',
    			'tblMessageTypes.intMessageType = tblMessages.intType',
    			array('strLabel')
    		)
    		->group('intType')
    		->order('intType');
    	$sql_get_type_totals->where("tblMessages.intUserID = {$this->obj_user->intUserID}");    		
    	$arr_messages_data = array();
    	foreach($this->tbl_messages->fetchAll($sql_get_type_totals)->toArray() as $row){
    		if($row['strLabel']){
    			$arr_messages_data[$row['strLabel']] = $row['int_num_rows'];
    		}
    	}
    	$this->view->assign("arr_messages_data",$arr_messages_data);
	}
	
	protected function _process_most_frequent_conversations(){
		
		$arr_most_frequent = array();
		foreach($this->arr_timespans as $time_label => $oldest_time){
			$select = $this->tbl_messages->select(false);
			$select->setIntegrityCheck(false);
			$select->from(
				'tblMessages',
				array( 'COUNT(intMessageID) AS intNumConversations', 'intAddressID')
			);
			$select->joinLeft(
				'tblAddresses',
				'tblAddresses.intAddressID = tblMessages.intAddressID',
				array('strPublicName')
			);
			$select->where("tblMessages.intUserID = {$this->obj_user->intUserID}");
			$select->where("tblMessages.dtmRecieved >= '" . date("Y-m-d H:i:s",$oldest_time)."'");
			$select->group("intAddressID");
			$select->order("COUNT(intMessageID) DESC");
			$select->limit(20);
			//echo $select; exit;
			foreach($this->tbl_messages->fetchAll($select) as $obj_message){
				if(is_numeric($obj_message->strPublicName)){
					$str_public_name = "??? '" . substr($obj_message->strPublicName,-4,4);
				}else{
					$str_public_name = $obj_message->strPublicName;
				}
				$arr_most_frequent[$time_label][$str_public_name] = $obj_message->intNumConversations;
			}
		}
		$this->view->assign('arr_most_frequent',$arr_most_frequent);
	}
	
	protected function _process_swearyness(){
		$arr_all_messages = $this->_get_all_messages();
		$int_number_messages_sweary = 0;
		$int_number_messages_total = count($arr_all_messages->toArray());
		foreach($arr_all_messages as $obj_message){
			if($obj_message->isSweary()){
				$int_number_messages_sweary++;
			}
		}
		$arr_swearing_density = array();
		$arr_swearing_density['clean'] = $int_number_messages_total - $int_number_messages_sweary;
		$arr_swearing_density['sweary'] = $int_number_messages_sweary;
		$this->view->assign('arr_swearing_density',$arr_swearing_density);
	}
	
	protected function _process_get_last_sms(){
		$obj_last_sms = $this->tbl_messages->fetchRow("intUserID = {$this->obj_user->intUserID}",'dtmRecieved DESC');
		$this->view->assign("obj_last_sms",$obj_last_sms);
	}
	protected function _process_get_load_time(){
		$this->view->assign("str_load_time",microtime(true) - $this->time_start);
	}
	protected function _process_most_active(){
		$arr_all_messages = $this->_get_all_messages();
		$arr_activity_days = array(
			'Monday' => 0,
			'Tuesday' => 0,
			'Wednesday' => 0,
			'Thursday' => 0,
			'Friday' => 0,
			'Saturday' => 0,
			'Sunday' => 0,
		);
		$arr_activity_hours = array();
		for($i=0; $i < 24;$i++){
			$arr_activity_hours[str_pad($i,2,'0',STR_PAD_LEFT)] = 0;
		}
		foreach($arr_all_messages as $obj_message){
			$int_time = strtotime($obj_message->dtmRecieved);
			$int_hour = date("H",$int_time);
			$str_day = date("l",$int_time);
			$arr_activity_hours[$int_hour]++;
			$arr_activity_days[$str_day]++;
		}
		ksort($arr_activity_hours);
		#print_r($arr_activity_hours);exit;
		$this->view->assign('arr_activity_hours',$arr_activity_hours);
		$this->view->assign('arr_activity_days',$arr_activity_days);
	}
   

    public function indexAction()
    {
        
    }

    public function usageAction()
    {
    	$this->view->headTitle("Usage for {$this->obj_user->strUsername}");
    	
    	$this->_process_get_last_sms();
    	$this->_process_send_recieve_chart();
    	$this->_process_most_frequent_conversations();
    	$this->_process_swearyness();
    	$this->_process_most_active();
		$this->_process_get_load_time();
    }


}



