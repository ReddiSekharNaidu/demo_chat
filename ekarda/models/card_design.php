<?php
class CardDesign extends AppModel
{

    var $name = 'CardDesign';
    var $belongsTo = array('CardType' => array(
        'className' => 'CardType',
        'foreignKey' => 'card_type_id'
    )
    );


    var $validate = array(

        'title' => array(


            'required' => array(
                'rule' => '/.+/',
                'message' => CARDDESIGN_NAME
            ),
            'custom' => array(
                'rule' => array('custom', '/[a-z+.0-9 ]$/i'), //alphabets allowed only
                'message' => ENTER_ALPHABET_AND_NUMBERIC_ONLY
            ),

            'unique' => array(
                'rule' => array('checkUniqueCarddesigntitle'),
                'message' => CARDDESIGN_ALREADY_USE
            )


        ),

        'card_type_id' => array(
            'required' => array(
                'rule' => '/.+/',
                'message' => CARDDESIGN_TYPE
            )

        ),

        'card_template_id' => array(
            'required' => array(
                'rule' => '/.+/',
                'message' => CARDDESIGN_TEMPLATE
            )

        ),

        'imgpath' => array(
            'required' => array(
                'rule' => '/.+/',
                'message' => CARDDESIGN_IMAGE
            ),
            'rule' => array('extension', array('png', 'jpg', 'jpeg', 'gif')),
            'message' => CARDDESIGN_IMAGE_INVALID
        ),
        'motif' => array(
            'ext' => array(
                'rule' => array('extension', array('dtm', '')),
                'allowEmpty' => true,
                'message' => CARDDESIGN_MOTIF_INVALID
            )
        ),

        'cardimgpath' => array(
            'required' => array(
                'rule' => '/.+/',
                'message' => CARDDESIGN_CARD
            ),
            'rule' => array('extension', array('png', 'jpg', 'jpeg', 'gif')),
            'message' => CARDDESIGN_IMAGE_INVALID
        ),


    );

    function checkUniqueCarddesigntitle($data1 = NULL)
    {
        if (empty($this->data)) {
            if (!empty($data1)) {

                $this->data = $data1;
                $data1 = '';
            }
        }
        $condArr = array('CardDesign.title' => $this->data['CardDesign']['title']);

        $condArr = am($condArr, array('CardDesign.card_type_id' => $this->data['CardDesign']['card_type_id']));
        if (isset($this->data['CardDesign']['id']) && !empty($this->data['CardDesign']['id'])) {
            $user_id = $this->data['CardDesign']['id'];
            $condArr = array_merge(array('CardDesign.id != ' => $user_id), $condArr);
        }
        return ($this->find('count', array('conditions' => $condArr)) == 0);
    }

    /**
     * @function name:   beforeSave
     * @description: Executes instantly after model data has beed validated successfully
     *
     * @param      :
     * @return     : -
     * @author     : Chris Natan
     * @date       : 6/30/2012
     * @note       : -
     */

    public function beforeSave($options) {
        $shouldHaveDefaultValue = array("is_custom_design");
        foreach ($shouldHaveDefaultValue as $field) {
            if (!isset($this->data[$this->name][$field])) {
                /* create default value  */
                $this->data[$this->name][$field] = 0;
            }
        }
        return true;
    }

}

?>