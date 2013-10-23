<?php

class CardTemplate extends AppModel {
    var $name = 'CardTemplate';
     
    var $validate=array(
        'name'=>array(
            'Unique' => array(
                'rule' => array('isUnique'), 
                'message' => TITLE_IS_ALREADY_EXIST
            ),
            'required' => array( 
                'rule' => VALID_NOT_EMPTY,
                'message' => PLEASE_ENTER_TITLE
            ),
            'custom' => array(
                'rule' => array('custom','/[a-z+]$/i'),
                'message' => ENTER_ALPHABET_ONLY
            ),
            'maxlength' => array(
                'rule' => array('maxlength', '30'),
                'message' => TITLE_IS_MAXLENGTH
            )
        ),
        'html_content' => array(
            'required' => array( 
                'rule' => VALID_NOT_EMPTY,
                'message' => PLEASE_ENTER_HTML_CONTENT
            )
        ),
        'text_content' => array(
            'required' => array( 
                'rule' => VALID_NOT_EMPTY,
                'message' => PLEASE_ENTER_TEXT_CONTENT
            )
        )
    );
}
