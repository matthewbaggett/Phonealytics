<?php

class UpdateController extends Zend_Controller_Action
{
	const MAX_MAILS_PER_CHECK = 25;

	protected function __sanitiseName($name){
		$name_elem = explode(" ",$name);
		$name_elem = array_filter($name_elem);
		if(count($name_elem) == 1){
			return ucfirst($name);
		}else{
			return ucfirst($name_elem[0]). " " . strtoupper(substr(end($name_elem),0,1));
		}
	}
	
	protected function __getOrCreateAddress(Application_Model_User $obj_user, Application_Model_Mailbox $obj_mailbox, $name, $address){
		$tbl_addresses = new Application_Model_DbTable_Addresses();
		$match = $tbl_addresses->fetchRow(array("strName = '{$name}'"));
		if($match){
			return $match;
		}else{
			$insert_id = $tbl_addresses->insert(array(
				'intUserID' => $obj_user->intUserID,
				'intMailboxID' => $obj_mailbox->intMailboxID,
				'strAddress' => $address,
				'strName' => $name,
				'strPublicName' => $this->__sanitiseName($name),
			));
			return $tbl_addresses->fetchRow('intAddressID = ' . $insert_id);
		}
	}
	
	protected function __processMail(Application_Model_User $obj_user, Application_Model_Mailbox $obj_mailbox, 	Zend_Mail_Storage_Imap $mailbox, Zend_Mail_Message $mail){
		
		$address 			= $mail->getHeader('x-smssync-address','string');
		$message_id 		= $mail->getHeader('x-smssync-id','string');
		$timestamp_date		= $mail->getHeader('x-smssync-date','string');
		$thread 			= $mail->getHeader('x-smssync-thread','string');
		$type				= $mail->getHeader('x-smssync-type','string');
		$read				= $mail->getHeader('x-smssync-read','string');
		$status				= $mail->getHeader('x-smssync-status','string');
		$timestamp_backup	= $mail->getHeader('x-smssync-backup_time','string');
		$name				= $mail->getHeader('subject','string');
		
		if($mail->getHeader('content-transfer-encoding','string') == 'base64'){
			$message 			= base64_decode($mail->getContent());	
		}else{
			$message			= $mail->getContent();
		}

		// Parse the timestamp; its in milliseconds
		$timestamp_date 	= (int) ($timestamp_date / 1000);
		$timestamp_backup	= (int) strtotime($timestamp_backup);
		
		// Strip some characters from the subject to make the name
		$name				= trim(str_replace("SMS with","",$name));
		
		//Add things into the DB..
		$tbl_messages = new Application_Model_DbTable_Messages();
		
		$oAddress = $this->__getOrCreateAddress($obj_user,$obj_mailbox,$name,$address);
		
		//Move mail
		try{
			$mailbox->moveMessage($mail->key(), $obj_mailbox->strReadMailboxname);
		}catch(Exception $e){
			echo "Couldn't move mail: ".get_class($e)." {$e->getMessage()}\n";			
		}
		$oMessage = $tbl_messages->insert(array(
			'intMailboxID' 	=> $obj_mailbox->intMailboxID,
			'intUserID' 	=> $obj_user->intUserID,
			'intAddressID' 	=> $oAddress->intAddressID,
			'strMessage' 	=> $message,
			'intType' 		=> $type,
			'intRead' 		=> $read,
			'intStatus' 	=> $status,
			'intThread' 	=> $thread,
			'dtmRecieved' 	=> date("Y-m-d H:i:s",$timestamp_date),
			'dtmBackedup'	=> date("Y-m-d H:i:s",$timestamp_backup),
			'strHash' 		=> hash("SHA1",$message),
			'bolDeleted' 	=> 0
		));
		
	}
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function checkMailboxesAction()
    {
    	$this->_helper->viewRenderer->setNoRender (true);
    	
        $tbl_users = new Application_Model_DbTable_Users();
        $tbl_mailboxes = new Application_Model_DbTable_Mailboxes();
        echo "Checking mailboxes\n";
        foreach($tbl_users->fetchAll() as $obj_user){
        	echo " > {$obj_user->strUsername}\n";
        	foreach($obj_user->getMailboxes() as $obj_mailbox){
        		try{
        			$mails = $obj_mailbox->getMail();
        		}catch(Exception $e){
        			echo "Cannot connect to mailbox: {$e->getMessage()}\n";
        			
        			$mails = null;
        		}
        		echo "  > Opening mailbox {$obj_mailbox->intMailboxID}\n";
        		echo "   > There are {$mails->countMessages()} in this mailbox to process\n";
        		if($mails !== NULL){
	        		$i = 0;
	        		foreach($mails as $mail){
						$mails->noop();
	        			$i++;
	        			if($i <= self::MAX_MAILS_PER_CHECK){
	        				echo "   > {$i}: {$mail->subject}\n";
	        				$this->__processMail($obj_user, $obj_mailbox, $mails, $mail);
	        			}else{
	        				break 1;
	        			}
	        		}
        		}
        	}
        }
        exit;
    }


}



