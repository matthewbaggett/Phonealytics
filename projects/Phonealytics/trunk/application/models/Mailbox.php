<?php 
class Application_Model_Mailbox extends Zend_Db_Table_Row_Abstract{
	public function getMail(){
		if($this->strProtocol == 'imap'){
			$mail = new Zend_Mail_Storage_Imap(
				array(
					'host' => $this->strHost,
					'user' => $this->strUsername,
					'password' => $this->strPassword,
					'port' => $this->intPort,
					'ssl' => $this->bolSSL?'SSL':null,
				)
			);
		}elseif($this->strProtocol == 'pop3'){
			// TODO: POP3 support
			$mail = new Zend_Mail_Storage_Pop3();
			throw new exception("POP3 support isn't ready yet.");
		}else{
			throw new exception("An unknown protocol type was specified.");
		}
		$mail->selectFolder($this->strMailboxName);
		return $mail;
	}
}
