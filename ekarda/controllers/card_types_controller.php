<?php
/**
	* @Class name:  CardTypesController
	* @description: Class for users cardtypes add,edit,delete,list
	* @author     : Chandresh Dashora
	* @since      : 21/06/2011
*/
class CardTypesController extends AppController {
	var $uses = array('CardType','CardTypesTag');
	var $name = 'CardTypes';
	var $components = array('File');
	/**
	* @function name:  superadmin_add
	* @description: This function used for adding new card type
	* @param      :
	* @return      :
	* @author     : Chandresh Dashora
	* @since      : 21/06/2011
	* @fun.type   : Functional
	*/
	function superadmin_add() {
		if(!empty($this->data)) {
			//set the CardType in modol
			$this->CardType->set($this->data);
			if($this->CardType->validates()) {
				if($this->data['CardType']['imgpath']['error'] == 0) {
					$tmp_image_name = $this->data['CardType']['imgpath']['name'];
					//rename the image name
					$image_name = $this->File->_rename_imagename($tmp_image_name);
					$new_image_path = CARDTYPE_IMAGE_RELATIVE_PATH.$image_name;
					$tmp_image_path = $this->data['CardType']['imgpath']['tmp_name'];
					//upload the image in card_type forder
					$status_image_upload = $this->File->_upload_image_or_file($tmp_image_path, $new_image_path);
					if($status_image_upload) {
						//when superadmin add card type user id = 0
						$this->data['CardType']['user_id'] = 0;
						$this->data['CardType']['imgpath'] = $image_name;
						//database save the card_type table
						if($this->CardType->save($this->data)) {
                            if($this->data['CardType']['tagName'] != "")
                            {
                                $conditions = array('CardType.title'=> $this->data['CardType']['title']);
                                $result = $this->CardType->find('first',array('conditions'=>$conditions));

                                $this->loadModel('CardTypesTag');
                                $tag_data['CardTypesTag']['card_type_id'] = $result['CardType']['id'];
                                $tag_data['CardTypesTag']['tag_name'] = $this->data['CardType']['tagName'];
                                $this->CardTypesTag->save($tag_data);
                            }
                            $this->Session->setFlash(RECORD_ADDED_SUCCESSFULLY);
                            $this->redirect(array('controller'=>'card_types', 'action'=>'index'));
						} else
							$this->Session->setFlash(RECORD_SAVE_FAIL);
					} else
						$this->Session->setFlash(RECORD_SAVE_FAIL);
				}
			}
		}
		$this->layout = "default-1.7.2";
	}
	/**
	* @function name:  superadmin_edit
	* @description: This function used for adding new card type
	* @param      : $id,$imgpath
	* @return     :
	* @author     : Chandresh Dashora
	* @since      : 21/06/2011
	* @fun.type   : Functional
	*/
	function superadmin_edit($id = null) {
		if(!empty($id) && $id != '') {
			$this->CardType->id = $id;
			if(isset($this->data) && !empty($this->data)) {
				if($this->CardType->checkUniqueCardtypetitle($this->data)) {
					$this->CardType->id = $id;
					if($this->data['CardType']['imgpath']['error'] == 0 || !empty($this->data['CardType']['title'])) {
						if($this->data['CardType']['imgpath']['error'] != 0) {
							unset($this->data['CardType']['imgpath']);
						} else {
							//delete the image
							$tmp_image_name = $this->data['CardType']['imgpath']['name'];
							//rename the image name
							$image_name = $this->File->_rename_imagename($tmp_image_name);
							$new_image_path = CARDTYPE_IMAGE_RELATIVE_PATH.$image_name;
							$tmp_image_path = $this->data['CardType']['imgpath']['tmp_name'];
							//upload the image in card_type forder
							$status_image_upload = $this->File->_upload_image_or_file($tmp_image_path, $new_image_path);
							$this->data['CardType']['imgpath'] = $image_name;
							if($status_image_upload) {
							} else {
								$this->Session->setFlash(RECORD_UPDATE_FAILED);
								$this->redirect(array('controller'=>'card_types', 'action'=>'index'));
								exit;
							}
						}
						$this->CardType->create();
						if($this->CardType->save($this->data)) {
                            if($this->data['CardType']['tagName'] != "")
                            {
                                $this->loadModel('CardTypesTag');
                                $conditions = array('CardTypesTag.card_type_id'=> $id);
                                $result = $this->CardTypesTag->find('first',array('conditions'=>$conditions));
                                $result['CardTypesTag']['tag_name'] = $this->data['CardType']['tagName'];
                                if($result['CardTypesTag']['id'] !="")
                                {
                                     $this->CardTypesTag->create();
                                     $this->CardTypesTag->save($result);
                                }else{
                                     $result['CardTypesTag']['card_type_id'] = $id;
                                     $this->CardTypesTag->save($result);
                                }
                            }
                            $this->Session->setFlash(RECORD_UPDATE_SUCCESSFULLY);
							$this->redirect(array('controller'=>'card_types', 'action'=>'index'));
						} else {
							$this->Session->setFlash(RECORD_UPDATE_FAILED);
							$this->redirect(array('controller'=>'card_types', 'action'=>'index'));
						}
					}
				} else {
					//error
					$this->Session->setFlash(CARDTYPE_ALREADY_USE);
					$this->redirect(array('controller'=>'card_types', 'action'=>'index'));
				}
			}
		} else {
			$this->redirect(array('controller'=>'card_types', 'action'=>'index'));
		}
	}
	/**
	* @function name:  superadmin_delete
	* @description: This function used for deleting card type
	* @param      : $id
	* @return     :
	* @author     :
	* @since      : 11/08/2011
	* @fun.type   : Functional
	*/
	function superadmin_delete($id = NULL) {
		if(isset($id)) {
			if($this->_count_myecard($id) <= 0) {
				// If we want the model associations, components, etc to be loaded
				$cardDesigns_obj = ClassRegistry::init("CardDesigns");
				$conditions = array("CardDesigns.card_type_id"=>$id);
				//Example usage with a model:
				$carddesign_result = $cardDesigns_obj->find('all', array('conditions'=>$conditions));
				foreach($carddesign_result as $carddesign_value) {
					$cardDesigns_obj->id = $carddesign_value['CardDesigns']['id'];
					$cartype_obj = $cardDesigns_obj->read();
					//set the variable path with image name
					$imange_path = CARDDESIGN_IMAGE_RELATIVE_PATH.$cartype_obj['CardDesigns']['imgpath'];
					//function is calling in app controller
					$image_delete_status = $this->File->_deleteFileImage($imange_path);
					$cardDesigns_obj->delete($cartype_obj['CardDesigns']['id'], true);
				}
				$delete_imange = $this->_delete_image_or_file($id);
				$this->CardType->recursive = - 1;
				if($this->CardType->delete($id, false)) {
                    $this->loadModel('CardTypesTag');
                    $conditions = array("CardTypesTag.card_type_id"=>$id);
                    $result = $this->CardTypesTag->find('first',array('conditions'=>$conditions));
                    if($result['CardTypesTag']['id'] !="")
                    {
                        $this->CardTypesTag->delete($result['CardTypesTag']['id'], false);
                    }
                    $this->set('msg', RECORD_DELETED_SUCCESSFULLY);
				} else {
                    $this->set('msg', CARDTYPE_IMAGE_DELETE_FAIL);
				}
			} else {
                $this->set('msg', CARDTYPE_ALREADY_USED);
			}
		} else {
            $this->set('msg', CARDTYPE_IMAGE_DELETE_FAIL);
		}
		$this->data['CardType']['user_id'] = 0;
		$this->set('card_type_obj', $this->_get_cardtype_listing($this->data, 'yes'));
		$this->set('updateMsgDiv', 'card_type_list');
		$this->set('elementMsgPath', CARD_TYPES.DS.'list_card_types');
		$this->set('div_msg_id', 'card_type_msg');
		$this->set('requireNotification', true);
		$this->viewPath = 'elements';
		$this->render('_msg-1.7.2');
	}
	//end function
	/**
	* @function name:  superadmin_index
	* @description: This function used for adding new cardtype name and image and listing the cardtypes.
	* @param      :
	* @return     :
	* @author     : Chandresh Dashora
	* @since      : 21/06/2011
	* @fun.type   : Functional
	*/
	function superadmin_index() {
		// Get Advertisers listing
		$this->data['CardType']['user_id'] = 0;
		//$card_type_obj = $this->_get_cardtype_listing($this->data, 'yes');
		$this->set('card_type_obj', $this->_get_cardtype_listing($this->data, 'yes'));
		$this->layout = "default-1.7.2";
	}
	function _get_cardtype_listing($cardtype_search_data = array(), $paging = NULL, $orderBy = NULL) {
		$conditions = array();
        $joins = array();
        $this->loadModel('CardTypesTag');
        $this->CardType->bindModel(array('hasMany'=>array('CardTypesTag'=>array('foreignKey'=>'card_type_id'))), false);
        $joins[] = array('table'=>'card_types_tags', 'foreignKey'=>'card_type_id', 'type'=>'LEFT', 'conditions'=>array('CardTypesTag.card_type_id = CardType.id'), 'alias'=>'CardTypesTag',);
        $fields = array('CardType.id', 'CardType.user_id', 'CardType.title', 'CardType.imgpath', 'CardType.created', 'CardType.modified', 'CardTypesTag.card_type_id', 'CardTypesTag.tag_name');
		// Set Order By
		if($orderBy == NULL) {
			$orderBy = 'CardType.id desc';
		}
		if($paging != NULL) {
			$pgLimit = PAGING_LIMIT;
			if($this->RequestHandler->isXml() && !empty($this->params['url']['n'])) {
				$pgLimit = $this->params['url']['n'];
			}
			$this->paginate = array('limit'=>$pgLimit, 'joins'=>$joins, 'conditions'=>$conditions, 'order'=>$orderBy,'fields'=>$fields);
			$cardtypes = $this->paginate('CardType');
		} else {
			$cardtypes = $this->CardType->find('all', array('joins'=>$joins, 'conditions'=>$conditions, 'order'=>$orderBy,'fields'=>$fields));
		}
		return $cardtypes;
	}
	function _delete_image_or_file($id) {
		$image_delete_status = false;
		$this->CardType->id = $id;
		$cartype_obj = $this->CardType->read();
		//set the variable path with image name
		$imange_path = CARDTYPE_IMAGE_RELATIVE_PATH.$cartype_obj['CardType']['imgpath'];
		//function is calling in app controller
		$image_delete_status = $this->File->_deleteFileImage($imange_path);
		return $image_delete_status;
	}
	/**
	* @Function : _count_myecard
	* @Param : None
	* @Return Value : count of myecard record
	* @Purpose: This function is for myecard avilable not delete cardtypes
	* @Author : Janak Kanani
	* @Created Date:
	* @Modified Date:
	*/
	function _count_myecard($card_type_id = NULL) {
		$myecard_Obj = ClassRegistry::init("Myecard");
		// create CardType model object
		return $myecard_Obj->find('count', array('conditions'=>array('Myecard.card_type_id'=>$card_type_id)));
	}
	//end function
}
