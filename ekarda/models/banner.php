<?php
class Banner extends AppModel 
{
	var $name = 'Banner';
	var $validate=array(
	                   'type'=>array(
					                 'required'=>array( 'rule' => VALID_NOT_EMPTY,
			                                              'message'=>PlEASE_SELECT_TYPE
													  )
									),
						
								   
					   'imagefile' => array(
										   'image' => array(
														    'rule' => array('extension', array('gif', 'jpg','jpeg', 'png'),
															 'message' =>INCORRECT_FILE_TYPE)
															 )
										 ),	
					   'script'=>array(
							           'required'=>array('rule'=>VALID_NOT_EMPTY,
											             'message'=>PlEASE_ENTER_BANNER_SCRIPT)
									   ),			  
	                    'banner_content'=>array(
				                            'required'=>array( 'rule'=>VALID_NOT_EMPTY,
											                   'message'=>PlEASE_ENTER_BANNER_CONTENT)
											    ),
						 
					    'redirection_url'=>array(
					                             'url'=>array('rule'=>'/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(([0-9]{1,5})?\/.*)?$/ix',
                                                               'message'=>PlEASE_ENTER_VALID_URL )
											     ),
												
						'position'=>array(
					                      'required'=>array('rule' => VALID_NOT_EMPTY,
										                    'message'=>PlEASE_SELECT_POSITION
												            )			 
										  )
									 
						);
 }
 ?>