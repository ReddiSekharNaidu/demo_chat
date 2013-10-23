<?php
  class CardTypesTag extends AppModel{
        var $name = 'CardTypesTag';
        var $hasMany = array('CardTypesTag' => array(
                                            'className' => 'CardTypesTag',
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
