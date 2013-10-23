<?php
class CardDesignsTag extends AppModel{

	var $name = 'CardDesignsTag';
	var $hasMany = array('CardDesignsTag' => array(
														'className' => 'CardDesignsTag',
														'foreignKey' => false,
														'dependent'=> true
													)
											); 
	
	var $validate = array(
                'tag_name' => array(
                        'required' => array(
                                            'rule' => '/.+/',
                                            'message' => CARDTYPE_TAG
                                            )                        
                )
        );
	
}
?>