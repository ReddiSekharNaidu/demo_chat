<?php 
class CardTemplatesController extends AppController{
    
    var $name = 'CardTemplates';
    var $uses = array('CardTemplate');
    
    /**
    * @function name:  superadmin_index
    * @description: This function used for view restaurant menu list
    *
    * @param      : -
    * @return     : -
    * @author     : 
    * @since      : 1/09/2011
    * @note		  : -
    */
    function superadmin_index(){
        $this->layout = 'default-1.7.2';
        $data=$this->CardTemplate->find('all');
        
        $this->set('result',$data); 
        $this->set('result', $this->superadmin_listing());
    }//end function
    
    function superadmin_listing() { 
        $this->paginate=array('limit' =>PAGE_LIMIT,'order' => array('CardTemplate.id'=>'DESC'));
        return $this->paginate('CardTemplate');
    }
    /**
    * @function name:  superadmin_add
    * @description: This function used for Add the data
    *
    * @param      : -
    * @return     : -
    * @author     : 
    * @since      : 1/09/2011
    * @note		  : -
    */	
    function superadmin_add($id=NULL){  
        $this->layout = 'default-1.7.2';
        if(!empty($this->data)) { 
            $this->CardTemplate->set($this->data);
            if($this->CardTemplate->Validates($this->data)) {
                $this->CardTemplate->id=$id;
                if($this->CardTemplate->save($this->data)) {
                    if($id!='' && $id!=0) {
                        $this->Session->setFlash(RECORD_UPDATE_SUCCESSFULLY);
                    } else {
                        $this->Session->setFlash(RECORD_ADDED_SUCCESSFULLY);
                    }
                    $this->data=array();
                    
                    $this->redirect(array('controller'=>'card_templates','action'=>'index'));
                } else {
                    $this->Session->setFlash(RECORD_SAVE_FAIL); 
                }
            }
        } else {
            $this->CardTemplate->id=$id;
            $this->data=$this->CardTemplate->read();
        }
    }

    /**
    * @function name:  superadmin_delete
    * @description: This function used for Edit the data
    *
    * @param      : -
    * @return     : -
    * @author     : 
    * @since      : 1/09/2011
    * @note		  : -
    */
    function superadmin_delete($id=0){
        if((int)$id>0) {
            $this->loadModel("CardDesign");
            $template_data=$this->CardDesign->find("count",array("conditions"=>array("card_template_id"=>$id)));
            if($template_data>0) {
                $this->Session->setFlash(TEMPLATE_IS_ALREADY_USED);
                $this->redirect(array('action'=>'superadmin_index','controller'=>'card_templates'));				 
            } else {
                $this->CardTemplate->delete($id,$cascade=true);
                $this->Session->setFlash(RECORD_DELETED_SUCCESSFULLY);
                $this->redirect(array('action'=>'superadmin_index','controller'=>'card_templates'));
            }
        } else {
            $this->redirect(array('action'=>'superadmin_index','controller'=>'card_templates'));
        }
    }
}