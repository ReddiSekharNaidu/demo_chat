<?php
class CardType extends AppModel{

	var $name = 'CardType';
	var $hasMany = array('CardType' => array(
														'className' => 'CardType',
														'foreignKey' => false,
														'dependent'=> true
													)
											); 
	
	var $validate = array(
	
	'title' => array(
            			'required' => array(
											'rule' => '/.+/',
											'message' => CARDTYPE_NAME
											),
						'custom'=>array(
											'rule'=>array('custom','/[a-z+.0-9 ]$/i'), 
											'message'=>ENTER_ALPHABET_AND_NUMBERIC_ONLY
										   ),
						'unique' => array (
											'rule' => array('checkUniqueCardtypetitle'),														
											'message' => CARDTYPE_ALREADY_USE
										   )
            
        				),
						
	
	   'imgpath' => array(
	   					'required' => array(
											'rule' => '/.+/',
											'message' => CARDTYPE_IMAGE_NAME
											),
           									 'rule' => array('extension', array('png','jpg','jpeg','gif')),
            								 'message' => CARDTYPE_IMAGE_INVALID
        				)
	);
	function checkUniqueCardtypetitle($data1 = NULL){
	if(empty($this->data))
	{
		if(!empty($data1))
		{
			
			$this->data = $data1;
			$data1 ='';
		}
	}	
		$condArr = array('CardType.title' => $this->data['CardType']['title']);	
		if(isset($this->data['CardType']['id']) && !empty($this->data['CardType']['id'])) {
			$user_id = $this->data['CardType']['id'];			
			$condArr = array_merge(array('CardType.id != ' => $user_id ), $condArr);
		}	
    	return ($this->find('count', array('conditions' => $condArr)) == 0);
	}
	
}
?>