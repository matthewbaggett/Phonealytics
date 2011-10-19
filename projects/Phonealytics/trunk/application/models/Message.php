<?php 
class Application_Model_Message extends Zend_Db_Table_Row_Abstract{
	
	protected function _trimArray($Input){
	 
	    if (!is_array($Input))
	        return trim($Input);
	 
	    return array_map(array($this,'_trimArray'), $Input);
	}
	
	protected function _trimWord($word){
		return ereg_replace("[^A-Za-z0-9 ]", "", $word );
	}
	
	protected function _get_badword_dictionary(){
		$str_file_badwords = getcwd() . "/../badwords.txt";
		if(!file_exists($str_file_badwords)){
			throw new Exception("Cannot find badwords file");
		}
		
		$str_bad_words = file_get_contents($str_file_badwords);
		$str_bad_words = str_replace("*","",$str_bad_words);
		$str_bad_words = strtolower($str_bad_words);
		//echo $str_bad_words; exit;
		$arr_bad_words =  explode("\n",$str_bad_words);
		return $this->_trimArray($arr_bad_words);
	}
	
	public function getAddress(){
		$tbl_addresses = new Application_Model_DbTable_Addresses();
		return $tbl_addresses->fetchRow("intAddressID = {$this->intAddressID}");
	}
	
	public function isSweary(){
		if(count($this->getSwearWords()) > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	public function getSwearWords(){
		$arr_found_badwords = array();
		$arr_badwords = $this->_get_badword_dictionary();
		#print_r($arr_badwords);
		$arr_words = explode(" ",$this->_trimWord($this->strMessage));
		$arr_words = array_filter($arr_words); 
		foreach($arr_words as $word){
			#echo "Is '$word' in ". implode(",", $arr_badwords)."\n";
			if(in_array(strtolower($word), $arr_badwords)){
				$arr_found_badwords[] = $word;
				#echo "yes.\n";
			}else{
				#echo "no.\n";
			}
		}
		#exit;
		return $arr_found_badwords;
	}
}
