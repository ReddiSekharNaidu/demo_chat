<?php
class AdminDesignSetup extends AppModel{
	var $name = 'AdminDesignSetup';
	var $hasMany = array( 
						 'AdminSelectedDesign' => array(
										 	    'className'  	 => 'AdminSelectedDesign',
												'foreignKey' 	 => 'admin_design_setup_id',
											    'counterCache' => true,
												'dependent'	 => true
									   		  )				      
					  );
					  
/*?> var $validate = array(
						    'message_subject' => array(
													'custom'=>array(
													               'rule'=>array('custom','/[a-z+.0-9]$/i'),  //alphabets allowed only
                                                                    'message'=>ENTER_ALPHABET_AND_NUMBERIC_ONLY
																   ),	
								                    'required' => array(
																		'rule' => VALID_NOT_EMPTY,
																		'message' => PLEASE_ENTER_SUBJECT
																	)	
													
												  ),
												  
							 'salutation' => array(
													'custom'=>array(
													                'rule'=>array('custom','/^[a-z]+$/'),  //alphabets allowed only
                                                                    'message'=>ENTER_ALPHABET_ONLY
																   ),	
								                    'required' => array(
																		'rule' => VALID_NOT_EMPTY,
																		'message' => PLEASE_ENTER_SALUTATION
																	)	
													
												  ),
												  
												  
							 'card_message' => array(
													'custom'=>array(
													               'rule'=>array('custom','/[a-z+.0-9]$/i'),  //alphabets allowed only
                                                                    'message'=>ENTER_ALPHABET_AND_NUMBERIC_ONLY
																   ),	
								                    'required' => array(
																		'rule' => VALID_NOT_EMPTY,
																		'message' => PLEASE_ENTER_CARD_MESSAGE
																	)	
													
												  ),						  					  
							);			<?php */
					  
}//end class
?>